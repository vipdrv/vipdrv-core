<?php
/**
 * Imports demo data from our remote server.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

/**
 * The class responsible for importing data remotely.
 */
class Avada_Importer_Data {

	/**
	 * The name of the demo we're trying to import
	 *
	 * @access protected
	 * @var string
	 */
	protected $demo;

	/**
	 * The demo data.
	 *
	 * @access protected
	 * @var array
	 */
	protected $demo_data = array();

	/**
	 * The root of the remote server where demos are off-loaded.
	 *
	 * @static
	 * @access protected
	 * @var string
	 */
	protected static $remote_server = 'https://updates.theme-fusion.com/avada_demo/?compressed=1';

	/**
	 * The path where we'll be writing our files.
	 *
	 * @access protected
	 * @var string
	 */
	protected $basedir = '';

	/**
	 * An array of files we want to get from the remote server.
	 *
	 * @access protected
	 * @var array
	 */
	protected $files = array();

	/**
	 * The class constructor
	 *
	 * @access public
	 * @param string|null $demo The demo name.
	 */
	public function __construct( $demo = null ) {

		// If no demo has been defined, early exit.
		if ( is_null( $demo ) ) {
			return;
		}

		// Set the demo's $demo property.
		$this->demo = $demo;

		$demos = self::get_data();

		// If the demo does not exist, early exit.
		if ( ! isset( $demos[ $demo ] ) ) {
			return;
		}
		$this->demo_data = $demos[ $demo ];

		// Where will we be saving our files?
		$upload_dir    = wp_upload_dir();
		$this->basedir = wp_normalize_path( $upload_dir['basedir'] . '/avada-demo-data' );
	}

	/**
	 * Gets the demos data from the remote server (or locally if remote is unreachable)
	 * decodes the JSON object and returns an array.
	 *
	 * @static
	 * @access public
	 * @since 5.0.0
	 * @return array
	 */
	public static function get_data() {

		$demos = get_transient( 'avada_demos' );
		// Reset demos if reset_transient=1.
		if ( isset( $_GET['reset_transient'] ) && '1' === $_GET['reset_transient'] ) {
			$demos = false;
		}
		// If the transient does not exist or we've reset it, continue to get the JSON.
		if ( false === $demos ) {
			// Get the local demos first.
			$demos = file_get_contents( Avada::$template_dir_path . '/includes/importer/demos.json' );
			$demos = json_decode( $demos, true );

			// Get the demo details from the remote server.
			$args = array(
				'user-agent' => 'avada-user-agent',
			);
			$remote_demos = wp_remote_retrieve_body( wp_remote_get( self::$remote_server, $args ) );
			$remote_demos = json_decode( $remote_demos, true );
			if ( ! empty( $remote_demos ) && $remote_demos && function_exists( 'json_last_error' ) && json_last_error() === JSON_ERROR_NONE ) {
				$demos = $remote_demos;
			}
			set_transient( 'avada_demos', $demos, WEEK_IN_SECONDS );
		}
		return $demos;
	}

	/**
	 * Create the necessary local folders if they don't already exist
	 *
	 * @access protected
	 */
	protected function mkdir() {

		if ( ! file_exists( $this->basedir ) ) {
			wp_mkdir_p( $this->basedir );
		}
		$demo_data_path = wp_normalize_path( $this->basedir . '/' . $this->demo . '_demo' );
		if ( ! file_exists( $demo_data_path ) ) {
			wp_mkdir_p( $demo_data_path );
		}
	}

	/**
	 * Checks if demo data.zip file is already downloaded
	 *
	 * @return bool
	 */
	public function remote_files_downloaded() {

		$folder_path = wp_normalize_path( $this->basedir . '/' . $this->demo . '_demo/' );

		if ( ! file_exists( $folder_path . 'data.zip' ) || DAY_IN_SECONDS < time() - filemtime( $folder_path . 'data.zip' ) ) {
			return false;
		} else {
			return true;
		}

	}

	/**
	 * Tries to create necessary folders and downloads demo data.
	 */
	public function download_remote_files() {

		// Attempt to create the necessary folders if they don't exist.
		$this->mkdir();

		// Get remote files and save them locally.
		$this->get_remote_files();
	}

	/**
	 * Ping the remote server
	 * Get the demo data
	 * Save the data locally
	 *
	 * @access protected
	 */
	protected function get_remote_files() {

		$folder_path = wp_normalize_path( $this->basedir . '/' . $this->demo . '_demo/' );

		$response = avada_wp_get_http( $this->demo_data['zipFile'], $folder_path . 'data.zip' );

		if ( ! $response ) {
			header( 'HTTP/1.0 408 Request Timeout' );
			die();
		}

		// Initialize the WordPress filesystem.
		$wp_filesystem = Fusion_Helper::init_filesystem();
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}

		// Attempt to manually extract the zip file first. Required for fptext method.
		if ( class_exists( 'ZipArchive' ) ) {
			$zip = new ZipArchive;
			if ( true === $zip->open( $folder_path . 'data.zip' ) ) {
				$zip->extractTo( $folder_path );
				$zip->close();
				$this->xml_replacements();
				return true;
			}
		}

		$unzipfile = unzip_file( $folder_path . 'data.zip', $folder_path );

		if ( $unzipfile ) {
			$this->xml_replacements();
			return true;
		}

		return false;
	}

	/**
	 * Fixes menus paths in xml files.
	 *
	 * @access private
	 * @since 5.1.0
	 * @return bool
	 */
	private function xml_replacements() {

		// Get the files path.
		$xml_file_path  = $this->get_path( 'avada.xml' );
		$json_file_path = $this->get_path( 'widget-data.json' );

		// Initialize the filesystem.
		$wp_filesystem = Fusion_Helper::init_filesystem();

		// Get the files contents.
		$xml_content  = $wp_filesystem->get_contents( $xml_file_path );
		$json_content = $wp_filesystem->get_contents( $json_file_path );

		// Replace placeholders.
		$home_url = untrailingslashit( get_home_url() );

		// In 'classic' demo case 'avada-xml' should be used for replacements.
		$demo = $this->demo;
		if ( 'classic' === $demo ) {
			$demo = 'avada-xml';
		}
		$demo = str_replace( '_', '-', $demo );

		// Replace URLs.
		$xml_content = str_replace(
			array(
				'http://avada.theme-fusion.com/' . $demo,
				'https://avada.theme-fusion.com/' . $demo,
			),
			$home_url,
			$xml_content
		);
		$json_content = str_replace(
			array(
				str_replace( '/', '\\/', 'http://avada.theme-fusion.com/' . $demo ),
				str_replace( '/', '\\/', 'https://avada.theme-fusion.com/' . $demo ),
			),
			str_replace( '/', '\\/', $home_url ),
			$json_content
		);

		// Make sure assets are still from the remote server.
		// We can use http instead of https here for performance reasons
		// since static assets don't require https anyway.
		$xml_content = str_replace(
			$home_url . '/wp-content/',
			'http://avada.theme-fusion.com/' . $demo . '/wp-content/',
			$xml_content
		);

		// Take care of assets.
		$xml_content = preg_replace_callback( '/(?<=<wp:meta_value><!\[CDATA\[)(https?:\/\/avada.theme-fusion.com)+(.*?)(?=]]><)/', 'fusion_fs_importer_replace_url', $xml_content );

		// Replace URLs in the JSON file.
		$json_content = str_replace(
			str_replace( '/', '\\/', $home_url . '/wp-content/' ),
			str_replace( '/', '\\/', 'http://avada.theme-fusion.com/' . $demo . '/wp-content/' ),
			$json_content
		);

		// Write files.
		$xml_file_return  = $wp_filesystem->put_contents( $xml_file_path, $xml_content );
		$json_file_return = $wp_filesystem->put_contents( $json_file_path, $json_content );

		return ( $xml_file_return && $json_file_return );

	}

	/**
	 * Get the path to the locally-saved files.
	 *
	 * @access public
	 * @param string $file Example: "avada.xml", "widget_data.json".
	 * @return string      Absolute path.
	 */
	public function get_path( $file ) {

		if ( 'theme_options.json' === $file || 'widget_data.json' === $file || 'fusion_slider.zip' === $file ) {
			$file = str_replace( '_', '-', $file );
		}
		return wp_normalize_path( $this->basedir . '/' . $this->demo . '_demo/' . $file );

	}

	/**
	 * Get the $remote_server static property.
	 *
	 * @static
	 * @access public
	 * @return string
	 */
	public static function get_remote_server_url() {
		return self::$remote_server;
	}

	/**
	 * Get the revslider property of this object.
	 *
	 * @access public
	 * @return false|array
	 */
	public function get_revslider() {

		// Early exit if we don't have anything.
		if ( ! isset( $this->demo_data['revSliders'] ) || empty( $this->demo_data['revSliders'] ) ) {
			return array();
		}
		return $this->demo_data['revSliders'];

	}


	/**
	 * Get the layerslider property of this object.
	 *
	 * @access public
	 * @return false|array
	 */
	public function get_layerslider() {

		// Early exit if we don't have anything.
		if ( ! isset( $this->demo_data['layerSliders'] ) || empty( $this->demo_data['layerSliders'] ) ) {
			return false;
		}
		return $this->demo_data['layerSliders'];

	}

	/**
	 * Is this demo a shop demo or not?
	 *
	 * @access public
	 * @return bool
	 */
	public function is_shop() {
		if ( isset( $this->demo_data['shop'] ) && true === $this->demo_data['shop'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Get the sidebars data.
	 *
	 * @access public
	 * @return false|array
	 */
	public function get_sidebars() {
		if ( isset( $this->demo_data['sidebars'] ) && false != $this->demo_data['sidebars'] ) {
			return $this->demo_data['sidebars'];
		}
		return false;
	}

	/**
	 * Get the homepage title.
	 *
	 * @access public
	 * @return string
	 */
	public function get_homepage_title() {
		if ( isset( $this->demo_data['homeTitle'] ) ) {
			return $this->demo_data['homeTitle'];
		}
		return 'Home';
	}

	/**
	 * Get the woo pages.
	 *
	 * @access public
	 * @return false|array
	 */
	public function get_woopages() {
		if ( isset( $this->demo_data['woopages'] ) && false != $this->demo_data['woopages'] ) {
			return $this->demo_data['woopages'];
		}
		return false;
	}
}
