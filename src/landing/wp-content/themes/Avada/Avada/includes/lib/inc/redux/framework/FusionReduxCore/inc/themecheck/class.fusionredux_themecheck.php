<?php
	/**
	 * FusionRedux ThemeCheck
	 *
	 * @package   FusionReduxFramework
	 * @author    Dovy <dovy@fusionredux.io>
	 * @license   GPL-3.0+
	 * @link      http://fusionredux.op
	 * @copyright 2015 FusionReduxFramework
	 */

	/**
	 * FusionRedux-ThemeCheck class
	 *
	 * @package FusionRedux_ThemeCheck
	 * @author  Dovy <dovy@fusionredux.io>
	 */
	// Don't duplicate me!
	if ( ! class_exists( 'FusionRedux_ThemeCheck' ) ) {
		class FusionRedux_ThemeCheck {

			/**
			 * Plugin version, used for cache-busting of style and script file references.
			 *
			 * @since   1.0.0
			 * @var     string
			 */
			protected $version = '1.0.0';

			/**
			 * Instance of this class.
			 *
			 * @since    1.0.0
			 * @var      object
			 */
			protected static $instance = null;

			/**
			 * Instance of the FusionRedux class.
			 *
			 * @since    1.0.0
			 * @var      object
			 */
			protected static $fusionredux = null;

			/**
			 * Details of the embedded FusionRedux class.
			 *
			 * @since    1.0.0
			 * @var      object
			 */
			protected static $fusionredux_details = null;

			/**
			 * Slug for various elements.
			 *
			 * @since   1.0.0
			 * @var     string
			 */
			protected $slug = 'fusionredux_themecheck';

			/**
			 * Initialize the plugin by setting localization, filters, and administration functions.
			 *
			 * @since     1.0.0
			 */
			private function __construct() {

				if ( ! class_exists( 'ThemeCheckMain' ) ) {
					return;
				}

				// Load admin style sheet and JavaScript.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

				add_action( 'themecheck_checks_loaded', array( $this, 'disable_checks' ) );
				add_action( 'themecheck_checks_loaded', array( $this, 'add_checks' ) );

			}

			/**
			 * Return an instance of this class.
			 *
			 * @since     1.0.0
			 * @return    object    A single instance of this class.
			 */
			public static function get_instance() {

				// If the single instance hasn't been set, set it now.
				if ( null == self::$instance ) {
					self::$instance = new self;
				}

				return self::$instance;
			}

			/**
			 * Return an instance of this class.
			 *
			 * @since     1.0.0
			 * @return    object    A single instance of this class.
			 */
			public static function get_fusionredux_instance() {

				// If the single instance hasn't been set, set it now.
				if ( null == self::$fusionredux && FusionReduxFramework::$_as_plugin ) {
					self::$fusionredux = new FusionReduxFramework();
					self::$fusionredux->init();
				}

				return self::$fusionredux;
			}

			/**
			 * Return the FusionRedux path info, if had.
			 *
			 * @since     1.0.0
			 * @return    object    A single instance of this class.
			 */
			public static function get_fusionredux_details( $php_files = array() ) {
				if ( self::$fusionredux_details === null ) {
					foreach ( $php_files as $php_key => $phpfile ) {
						if ( strpos( $phpfile, 'class' . ' FusionReduxFramework {' ) !== false ) {
							self::$fusionredux_details               = array(
								'filename' => strtolower( basename( $php_key ) ),
								'path'     => $php_key,
							);
							self::$fusionredux_details['dir']        = str_replace( basename( $php_key ), '', $php_key );
							self::$fusionredux_details['parent_dir'] = str_replace( basename( self::$fusionredux_details['dir'] ) . '/', '', self::$fusionredux_details['dir'] );
						}
					}
				}
				if ( self::$fusionredux_details === null ) {
					self::$fusionredux_details = false;
				}

				return self::$fusionredux_details;
			}

			/**
			 * Disable Theme-Check checks that aren't relevant for ThemeForest themes
			 *
			 * @since    1.0.0
			 */
			function disable_checks() {
				global $themechecks;

				//$checks_to_disable = array(
				//	'IncludeCheck',
				//	'I18NCheck',
				//	'AdminMenu',
				//	'Bad_Checks',
				//	'MalwareCheck',
				//	'Theme_Support',
				//	'CustomCheck',
				//	'EditorStyleCheck',
				//	'IframeCheck',
				//);
				//
				//foreach ( $themechecks as $keyindex => $check ) {
				//	if ( $check instanceof themecheck ) {
				//		$check_class = get_class( $check );
				//		if ( in_array( $check_class, $checks_to_disable ) ) {
				//			unset( $themechecks[$keyindex] );
				//		}
				//	}
				//}
			}

			/**
			 * Disable Theme-Check checks that aren't relevant for ThemeForest themes
			 *
			 * @since    1.0.0
			 */
			function add_checks() {
				global $themechecks;

				// load all the checks in the checks directory
				$dir = 'checks';
				foreach ( glob( dirname( __FILE__ ) . '/' . $dir . '/*.php' ) as $file ) {
					require_once wp_normalize_path( $file );
				}
			}

			/**
			 * Register and enqueue admin-specific style sheet.
			 *
			 * @since     1.0.1
			 */
			public function enqueue_admin_styles() {
				$screen = get_current_screen();
				if ( 'appearance_page_themecheck' == $screen->id ) {
					wp_enqueue_style( $this->slug . '-admin-styles', FusionReduxFramework::$_url . 'inc/themecheck/css/admin.css', array(), $this->version );
				}
			}

			/**
			 * Register and enqueue admin-specific JavaScript.
			 *
			 * @since     1.0.1
			 */
			public function enqueue_admin_scripts() {

				$screen = get_current_screen();

				if ( 'appearance_page_themecheck' == $screen->id ) {
					wp_enqueue_script( $this->slug . '-admin-script', FusionReduxFramework::$_url . 'inc/themecheck/js/admin.js', array( 'jquery' ), $this->version );

					if ( ! isset( $_POST['themename'] ) ) {

						$intro = '';
						$intro .= '<h2>FusionRedux Theme-Check</h2>';
						$intro .= '<p>Extra checks for FusionRedux to ensure you\'re ready for marketplace submission to marketplaces.</p>';

						$fusionredux_check_intro['text'] = $intro;

						wp_localize_script( $this->slug . '-admin-script', 'fusionredux_check_intro', $fusionredux_check_intro );

					}
				}

			}
		}

		FusionRedux_ThemeCheck::get_instance();
	}
