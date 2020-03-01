<?php
/**
 * Dynamic-JS loader.
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
final class Fusion_Dynamic_JS {

	/**
	 * An array of our scripts.
	 * Each script also lists its dependencies.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $scripts = array();

	/**
	 * An array of our wp_localize_script calls.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $localize_scripts = array();

	/**
	 * An instance of the Fusion_Dynamic_JS_File class.
	 * null if the class was not instantiated.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var null|object Fusion_Dynamic_JS_File
	 */
	public $file = null;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'get_header', array( $this, 'init' ) );
		add_action( 'save_post', array( 'Fusion_Dynamic_JS_File', 'reset_cached_filenames' ) );
		add_action( 'fusionredux/options/fusion_options/saved', array( 'Fusion_Dynamic_JS_File', 'delete_dynamic_js_transient' ) );

	}

	/**
	 * This is fired on 'wp'.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		// If JS compiler is disabled, or if WP_SCRIPT_DEBUG is set to true load separate files.
		$settings = Fusion_Settings::get_instance();
		if ( '0' === $settings->get( 'js_compiler' ) || ( defined( 'WP_SCRIPT_DEBUG' ) && WP_SCRIPT_DEBUG ) ) {
			new Fusion_Dynamic_JS_Separate( $this );
			return;
		}
		$this->file = new Fusion_Dynamic_JS_File( $this );

	}

	/**
	 * Registers a script.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string      $handle    The script's handle.
	 * @param string      $url       The URL to the script.
	 * @param string      $path      The path to the script.
	 * @param array       $deps      An array of dependencies.
	 * @param bool|string $ver       The script version.
	 * @param bool        $in_footer Whether the script should be in the footer or not.
	 */
	public static function register_script( $handle = '', $url = '', $path = '', $deps = array(), $ver = false, $in_footer = false ) {
		self::add_script( 'register', $handle, $url, $path, $deps, $ver, $in_footer );
	}

	/**
	 * Enqueues a script.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string      $handle    The script's handle.
	 * @param string      $url       The URL to the script.
	 * @param string      $path      The path to the script.
	 * @param array       $deps      An array of dependencies.
	 * @param bool|string $ver       The script version.
	 * @param bool        $in_footer Whether the script should be in the footer or not.
	 */
	public static function enqueue_script( $handle = '', $url = '', $path = '', $deps = array(), $ver = false, $in_footer = false ) {
		self::add_script( 'enqueue', $handle, $url, $path, $deps, $ver, $in_footer );
	}

	/**
	 * Deregisters a script.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string $handle The script's handle.
	 */
	public static function deregister_script( $handle ) {
		foreach ( self::$scripts as $key => $script ) {
			if ( $handle === $script['handle'] ) {
				unset( self::$scripts[ $key ] );
			}
		}
	}

	/**
	 * Dequeues a script.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string $handle The script's handle.
	 */
	public static function dequeue_script( $handle ) {
		foreach ( self::$scripts as $key => $script ) {
			if ( $handle === $script['handle'] ) {
				self::$scripts[ $key ]['action'] = 'register';
			}
		}
	}

	/**
	 * Add a script to the array.
	 *
	 * @static
	 * @access private
	 * @since 1.0.0
	 * @param string      $action    The action to take. Can be enqueue|register.
	 * @param string      $handle    The script's handle.
	 * @param string      $url       The URL to the script.
	 * @param string      $path      The path to the script.
	 * @param array       $deps      An array of dependencies.
	 * @param bool|string $ver       The script version.
	 * @param bool        $in_footer Whether the script should be in the footer or not.
	 */
	private static function add_script( $action = 'enqueue', $handle = '', $url = '', $path = '', $deps = array(), $ver = false, $in_footer = false ) {

		// Early exit if $handle is not defined.
		if ( ! $handle ) {
			return;
		}

		// Check if our script already exists in the array.
		foreach ( self::$scripts as $script ) {
			if ( $handle === $script['handle'] ) {
				if ( 'register' === $script['action'] ) {
					// We're enqueueing the script.
					if ( 'enqueue' === $action ) {
						$url       = ( '' === $url ) ? $script['url'] : $url;
						$path      = ( '' === $path ) ? $script['path'] : $path;
						$deps      = ( empty( $deps ) ) ? $script['deps'] : $deps;
						$ver       = ( false ) ? $script['ver'] : $ver;
						$in_footer = ( false ) ? $script['in_footer'] : $in_footer;
					} elseif ( 'register' === $action ) {
						return;
					}
				} elseif ( 'enqueue' === $script['action'] ) {
					// The script was previously enqueued.
					if ( 'enqueue' === $action ) {
						return;
					} elseif ( 'register' === $action ) {
						$action = 'enqueue';
					}
				}
			}
		}

		// If animations are disabled in TO, we have to delete the dependency from the $deps array.
		if ( ! fusion_library()->get_option( 'use_animate_css' ) ) {
			$key = array_search( 'fusion-animations', $deps );
			if ( false !== $key ) {
				unset( $deps[ $key ] );
			}
		}

		self::$scripts[] = array(
			'action'    => (string) $action,
			'handle'    => (string) $handle,
			'url'       => (string) $url,
			'path'      => (string) $path,
			'deps'      => (array) $deps,
			'ver'       => (string) $ver,
			'in_footer' => true,
		);

	}

	/**
	 * Localize scripts and add variables.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string $handle The script's handle.
	 * @param string $name   The variable name.
	 * @param array  $data   An array of data.
	 */
	public static function localize_script( $handle = '', $name = '', $data = array() ) {

		// Early exit if $handle or $name are not defined.
		if ( ! $handle || ! $name ) {
			return;
		}

		// Early exit if the script already exists in the array.
		foreach ( self::$localize_scripts as $script ) {
			if ( $handle === $script['handle'] && $name === $script['name'] ) {
				return;
			}
		}

		self::$localize_scripts[] = array(
			'handle'    => (string) $handle,
			'name'      => (string) $name,
			'data'      => (array) $data,
		);

	}

	/**
	 * Get the scripts.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param bool $reorder Whether we want to reorder the scripts or not.
	 * @return array
	 */
	public function get_scripts( $reorder = true ) {

		return self::$scripts;

	}

	/**
	 * Get the scripts.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array
	 */
	public function get_localizations() {
		return self::$localize_scripts;
	}

	/**
	 * Determine if the server is HTTP/2 or not.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public static function is_http2() {

		if ( isset( $_SERVER['SERVER_PROTOCOL'] ) ) {
			$ver = 1;
			$ver = ( isset( $_SERVER['SERVER_PROTOCOL'] ) ) ? str_replace( 'HTTP/', '', sanitize_text_field( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) ) ) : '1';
			if ( 2 <= intval( $ver ) ) {
				return true;
			}
		}
		return false;
	}
}
