<?php
/**
 * Dynamic-JS loader - File Method.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles enqueueing files dynamically.
 */
final class Fusion_Dynamic_JS_File extends Fusion_Dynamic_JS_Compiler {

	/**
	 * The filename.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var false|string
	 */
	protected $filename;

	/**
	 * The Fusion_Filesystem instance of the $filename.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var null|object
	 */
	protected $file = null;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $dynamic_js An instance of the Fusion_Dynamic_JS object.
	 */
	public function __construct( $dynamic_js ) {

		parent::__construct( $dynamic_js );

		$this->filename = $this->get_filename();
		$this->file     = new Fusion_Filesystem( $this->filename );
		$no_file        = false;

		if ( ! file_exists( $this->file->get_path() ) ) {
			$url = $this->write_file();
			if ( ! $url ) {
				$no_file = true;
			}
		}

		if ( $no_file || ! self::js_file_is_readable() ) {
			new Fusion_Dynamic_JS_Separate( $dynamic_js );
			self::disable_dynamic_js();
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueues the file.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		// Get an array of external dependencies.
		$dependencies = array_unique( $this->get_external_dependencies() );
		// Enqueue the script.
		wp_enqueue_script( 'fusion-scripts', $this->file->get_url(), $dependencies, null, true );
	}

	/**
	 * Check if file is accessable.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function js_file_is_readable() {
		$upload_dir = wp_upload_dir();
		$file_path  = $upload_dir['basedir'] . '/' . $this->get_filename();

		if ( is_readable( $file_path ) ) {
			// Secondary check.
			$fusion_dynamic_js_readable = get_transient( 'fusion_dynamic_js_readable' );

			if ( false === $fusion_dynamic_js_readable ) {
				// Check for 403 / 500.
				$response = wp_safe_remote_get( $this->file->get_url( false ), array(
					'timeout' => 5,
				) );
				$response_code = wp_remote_retrieve_response_code( $response );

				if ( 200 !== $response_code ) {
					set_transient( 'fusion_dynamic_js_readable', 'no' );
					return false;
				}
				set_transient( 'fusion_dynamic_js_readable', 'yes' );
				return true;
			}
			return (bool) ( 'yes' === $fusion_dynamic_js_readable );
		}
		return false;
	}

	/**
	 * Disable Dynamic JS compiler.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function disable_dynamic_js() {
		$options = get_option( Fusion_Settings::get_option_name(), array() );
		$options['js_compiler'] = '0';

		update_option( Fusion_Settings::get_option_name(), $options );
		set_transient( 'fusion_dynamic_js_readable', 'no' );
	}

	/**
	 * Writes the styles to a file.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool Whether the file-write was successful or not.
	 */
	public function write_file() {

		// Get the compiled JS.
		$content = $this->get_compiled_js();

		// Attempt to write the file.
		return ( $this->file->write_file( $content ) );

	}

	/**
	 * Gets the filename.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_filename() {

		$filenames = get_transient( 'fusion_dynamic_js_filenames' );
		if ( ! is_array( $filenames ) ) {
			$filenames = array();
		}
		$fusion = Fusion::get_instance();
		$id     = (int) $fusion->get_page_id();
		// @codingStandardsIgnoreLine
		$id .= md5( $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $_SERVER['REQUEST_URI'] );
		if ( isset( $filenames[ $id ] ) ) {
			return "fusion-scripts/{$filenames[ $id ]}.js";
		}

		// Do not reorder files here to improve performace.
		$scripts = wp_json_encode( $this->get_scripts( false ) );
		$l10n    = wp_json_encode( $this->dynamic_js->get_localizations() );
		// Create a filename using md5() and combining the scripts array with localizations.
		$filename = md5( $scripts . $l10n );

		$filenames[ $id ] = $filename;
		set_transient( 'fusion_dynamic_js_filenames', $filenames, HOUR_IN_SECONDS );

		return "fusion-scripts/{$filename}.js";

	}

	/**
	 * Deletes all compiled JS files.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public static function delete_compiled_js() {

		// Get the upload directory for this site.
		$upload_dir = wp_upload_dir();

		// Init the filesystem.
		$wp_filesystem = Fusion_Helper::init_filesystem();

		// Delete the folder.
		return $wp_filesystem->delete( $upload_dir['basedir'] . '/fusion-scripts', true, 'd' );
	}

	/**
	 * Resets the cached filenames transient.
	 *
	 * @static
	 * @since 1.0.0
	 * @return bool
	 */
	public static function reset_cached_filenames() {

		return delete_transient( 'fusion_dynamic_js_filenames' );

	}

	/**
	 * Resets JS compiler transient.
	 *
	 * @static
	 * @since 1.0.0
	 * @return bool
	 */
	public static function delete_dynamic_js_transient() {

		return delete_transient( 'fusion_dynamic_js_readable' );

	}
}
