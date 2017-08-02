<?php
/**
 * Filesystem helper for patcher module.
 *
 * @package Fusion-Library
 * @subpackage Fusion-Patcher
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles writing patches to the filesystem.
 *
 * @since 1.0.0
 */
class Fusion_Patcher_Filesystem {

	/**
	 * Is this for the avada theme, or the fusion-core plugin?
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $target = 'avada';

	/**
	 * The remote source.
	 *
	 * @static
	 * @access public
	 * @var null|string
	 */
	public static $source = null;

	/**
	 * The path of the target.
	 *
	 * @static
	 * @access public
	 * @var null|string
	 */
	public static $destination = null;

	/**
	 * The patch ID.
	 *
	 * @static
	 * @access public
	 * @var null|int
	 */
	public static $patch_id = null;

	/**
	 * Whether the file-writing was successful or not.
	 *
	 * @access public
	 * @var bool
	 */
	public $status = false;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param string      $target      The context (avada/fusion-core/fusion-builder).
	 * @param string|null $source      The remote source.
	 * @param string|null $destination The destination path.
	 * @param string|null $patch_id    The patch ID.
	 */
	public function __construct( $target = 'avada', $source = null, $destination = null, $patch_id = null ) {
		if ( is_null( $source ) || is_null( $destination ) ) {
			return;
		}
		self::$target      = $target;
		self::$source      = $source;
		self::$destination = $destination;
		self::$patch_id    = $patch_id;
		// Instantiate the WordPress filesystem.
		Fusion_Helper::init_filesystem();
		// Write the source contents to the destination.
		$this->write_file();
	}

	/**
	 * Get remote contents
	 *
	 * @access public
	 * @param  string $url  The URL we're getting our data from.
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 */
	public function get_remote( $url ) {
		$args = array(
			'timeout'    => 30,
			'user-agent' => 'fusion-user-agent',
		);

		$response = wp_remote_get( $url, $args );
		if ( is_array( $response ) ) {
			return $response['body'];
		}
		// Add a message so that the user knows what happened.
		new Fusion_Patcher_Admin_Notices( 'no-patch-contents', esc_attr__( 'The Avada patch contents cannot be retrieved. Please contact your host to unblock the "https://gist.github.com/" domain.', 'fusion-builder' ) );
		return false;
	}

	/**
	 * Write our contents to the destination file.
	 *
	 * @access public
	 * @return bool Returns true if the process was successful, false otherwise.
	 */
	public function write_file() {
		$contents = $this->get_remote( self::$source );
		if ( ! $contents ) {
			// The remote file is empty.
			$this->status = false;
			// Add a message to users for debugging purposes.
			new Fusion_Patcher_Admin_Notices( 'patch-empty', esc_attr__( 'Patch empty.', 'fusion-builder' ) );
			return false;
		}

		$target = false;
		if ( 'avada' === self::$target ) {
			$target = Avada::$template_dir_path;
		} elseif ( 'fusion-core' === self::$target && defined( 'FUSION_CORE_PATH' ) ) {
			$target = FUSION_CORE_PATH;
		} elseif ( 'fusion-builder' === self::$target && defined( 'FUSION_BUILDER_PLUGIN_DIR' ) ) {
			$target = FUSION_BUILDER_PLUGIN_DIR;
		}

		global $wp_filesystem;

		// For FS_METHOD ftpext we need to change target paths
		// as FTP root dir might not be server's root dir.
		if ( 'ftpext' === $wp_filesystem->method ) {
			if ( 'avada' === self::$target ) {
				$path_array  = explode( '/', Avada::$template_dir_path );
				$target      = $wp_filesystem->wp_themes_dir() . $path_array[ count( $path_array ) -1 ];
			} elseif ( 'fusion-core' === self::$target && defined( 'FUSION_CORE_PATH' ) ) {
				$path_array  = explode( '/', FUSION_CORE_PATH );
				$target      = $wp_filesystem->wp_plugins_dir() . $path_array[ count( $path_array ) -2 ];
			} elseif ( 'fusion-builder' === self::$target && defined( 'FUSION_BUILDER_PLUGIN_DIR' ) ) {
				$path_array  = explode( '/', FUSION_CORE_PATH );
				$target      = $wp_filesystem->wp_plugins_dir() . $path_array[ count( $path_array ) -2 ];
			}
		}

		if ( false === $target ) {
			// Fail if target is not avada|fusion-core|fusion-builder.
			$this->status = false;
			// Add a message to users for debugging purposes.
			new Fusion_Patcher_Admin_Notices( 'invalid-patch-target', esc_attr__( 'Invalid Patch target.', 'fusion-builder' ) );
			return false;
		}

		// Build the path.
		$path = wp_normalize_path( $target . '/' . self::$destination );
		// Define constants if undefined.
		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}
		if ( ! defined( 'FS_CHMOD_FILE' ) ) {
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}
		// Try to put the contents in the file.
		$this->status = $wp_filesystem->put_contents( $path, $contents, FS_CHMOD_FILE );
		if ( ! $this->status ) {

			// The zip-file URL for this patch.
			$patch_url = add_query_arg( array(
				'action' => 'serve_patch',
				'id'     => self::$patch_id,
			), Fusion_Patcher_Client::$remote_patches_uri );

			// Add a message to users for debugging purposes.
			new Fusion_Patcher_Admin_Notices( 'write-permissions-' . self::$patch_id, sprintf( __( 'The patch could not be applied because of your specific server permissions. You have two options to remedy this. 1. <a %1$s>Download this zip file</a> which contains the files needed to fix this issue. Simply extract the zip file, and replace the files it contains with the same files on your server. DO NOT REPLACE THE ENTIRE FOLDER. 2. <a %2$s>Contact our support center</a>, submit a ticket and include your FTP credentials so one of our support experts can apply the fix for you. Once the fix is applied, click the "Dismiss Notices" button so this message is removed.', 'fusion-builder' ), 'target="_blank" href="' . $patch_url . '" style="color:#fff;text-decoration:underline;font-weight:bold;"', 'target="_blank" href="http://theme-fusion.com/support-ticket/" style="color:#fff;text-decoration:underline;font-weight:bold;"' ) );
		}
		return $this->status;
	}
}
