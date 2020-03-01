<?php
/**
 * The main theme class.
 * We're using this one to instantiate uther classes
 * and access the main theme objects.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The main theme class.
 */
class Avada {

	/**
	 * The template directory path.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $template_dir_path = '';

	/**
	 * The template directory URL.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $template_dir_url = '';

	/**
	 * The stylesheet directory path.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $stylesheet_dir_path = '';

	/**
	 * The stylesheet directory URL.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $stylesheet_dir_url = '';

	/**
	 * The one, true instance of the Avada object.
	 *
	 * @static
	 * @access public
	 * @var null|object
	 */
	public static $instance = null;

	/**
	 * The theme version.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $version = AVADA_VERSION;

	/**
	 * The original option name.
	 * This is the untainted option name, without using any languages.
	 * If you want the property including language, use $option_name instead.
	 *
	 * @static
	 * @access private
	 * @var string
	 */
	private static $original_option_name = 'fusion_options';

	/**
	 * The option name including the language suffix.
	 * If you want the option name without language, use $original_option_name.
	 *
	 * @static
	 * @access private
	 * @var string
	 */
	private static $option_name = '';

	/**
	 * The language we're using.
	 * This is used to modify $option_name.
	 * It is the language code prefixed with a '_'
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $lang = '';

	/**
	 * Determine if the language has been applied to the $option_name.
	 *
	 * @static
	 * @access public
	 * @var bool
	 */
	public static $lang_applied = false;

	/**
	 * Dertermine if the current language is set to "all".
	 *
	 * @static
	 * @access private
	 * @var bool
	 */
	private static $language_is_all = false;

	/**
	 * Determine if we're currently upgrading/migration options.
	 *
	 * @static
	 * @access public
	 * @var bool
	 */
	public static $is_updating  = false;

	/**
	 * Avada_Settings.
	 *
	 * @access public
	 * @var object
	 */
	public $settings;

	/**
	 * Avada_Options.
	 *
	 * @static
	 * @access public
	 * @var null|object
	 */
	public static $options = null;

	/**
	 * Bundled Plugins.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $bundled_plugins = array(
		'fusion_core' => array(
			'slug'    => 'fusion-core',
			'name'    => 'Fusion Core',
			'version' => '3.2.1',
		),
		'fusion_builder' => array(
			'slug'    => 'fusion-builder',
			'name'    => 'Fusion Builder',
			'version' => '1.2.1',
		),
		'layer_slider' => array(
			'slug'    => 'LayerSlider',
			'name'    => 'LayerSlider WP',
			'version' => '6.5.5',
		),
		'slider_revolution' => array(
			'slug'    => 'revslider',
			'name'    => 'Slider Revolution',
			'version' => '5.4.5.1',
		),
	);

	/**
	 * Fusion.
	 *
	 * @access public
	 * @var object
	 */
	public $fusion_library;

	/**
	 * Avada_Init.
	 *
	 * @access public
	 * @var object
	 */
	public $init;

	/**
	 * Avada_Social_Sharing.
	 *
	 * @access public
	 * @var object
	 */
	public $social_sharing;

	/**
	 * Avada_Template.
	 *
	 * @access public
	 * @var object
	 */
	public $template;

	/**
	 * Avada_Blog.
	 *
	 * @access public
	 * @var object
	 */
	public $blog;

	/**
	 * Avada_Images.
	 *
	 * @access public
	 * @var object
	 */
	public $images;

	/**
	 * Avada_Head.
	 *
	 * @access public
	 * @var object
	 */
	public $head;

	/**
	 * Avada_Layout.
	 *
	 * @access public
	 * @var object
	 */
	public $layout;

	/**
	 * Avada_GoogleMap.
	 *
	 * @access public
	 * @var object
	 */
	public $google_map;

	/**
	 * Avada_EventsCalendar.
	 *
	 * @access public
	 * @var object Avada_EventsCalendar.
	 */
	public $events_calendar;

	/**
	 * Avada_Remote_installer.
	 *
	 * @access public
	 * @var object Avada_Remote_installer.
	 */
	public $remote_install;

	/**
	 * Avada_Product_registration
	 *
	 * @access public
	 * @var object Avada_Product_registration.
	 */
	public $registration;

	/**
	 * Avada_Sermon_Manager
	 *
	 * @access public
	 * @var object Avada_Sermon_Manager
	 */
	public $sermon_manager;

	/**
	 * Access the single instance of this class.
	 *
	 * @return Avada
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Avada();
		}
		return self::$instance;
	}

	/**
	 * Shortcut method to get the settings.
	 */
	public static function settings() {
		return self::get_instance()->settings->get_all();
	}

	/**
	 * The class constructor
	 *
	 * @access private
	 */
	private function __construct() {

		// Add a non-persistent cache group.
		wp_cache_add_non_persistent_groups( 'avada' );

		// Set static vars.
		if ( '' === self::$template_dir_path ) {
			self::$template_dir_path = wp_normalize_path( get_template_directory() );
		}
		if ( '' === self::$template_dir_url ) {
			self::$template_dir_url = get_template_directory_uri();
		}
		if ( '' === self::$stylesheet_dir_path ) {
			self::$stylesheet_dir_path = wp_normalize_path( get_stylesheet_directory() );
		}
		if ( '' === self::$stylesheet_dir_url ) {
			self::$stylesheet_dir_url = get_stylesheet_directory_uri();
		}

		$this->set_is_updating();

		// Multilingual handling.
		self::multilingual_options();
		// Make sure that $option_name is set.
		// This is run AFTER the multilingual option as a fallback.
		if ( empty( self::$option_name ) ) {
			self::$option_name = self::get_option_name();
		}

		// Instantiate secondary classes.
		$this->settings       = Avada_Settings::get_instance();
		$this->registration   = new Fusion_Product_Registration( array(
			'type'    => 'theme',
			'name'    => 'Avada',
			'bundled' => array(
				'Fusion Core',
				'Fusion Builder',
			),
			'bundled-versions' => array(
				'Fusion Core'    => self::$bundled_plugins['fusion_core']['version'],
				'Fusion Builder' => self::$bundled_plugins['fusion_builder']['version'],
			),
		) );
		$this->init           = new Avada_Init();
		$this->social_sharing = new Avada_Social_Sharing();
		$this->template       = new Avada_Template();
		$this->blog           = new Avada_Blog();
		$this->images         = new Avada_Images();
		$this->head           = new Avada_Head();
		$this->layout         = new Avada_Layout();
		$this->google_map     = new Avada_GoogleMap();
		$this->remote_install = new Avada_Remote_installer();
		$this->fusion_library = Fusion::get_instance();
		$this->sermon_manager = new Avada_Sermon_Manager();

		// Set the Fusion Library Image Class variable to the Avada one, to avoid duplication.
		global $fusion_library;
		if ( $fusion_library ) {
			$fusion_library->images = $this->images;
		}
	}



	/**
	 * Checks if we're in the migration page.
	 * It does that by checking _GET, and then sets the $is_updating property.
	 *
	 * @access public
	 */
	public function set_is_updating() {
		if ( ! self::$is_updating && $_GET && isset( $_GET['avada_update'] ) && '1' == $_GET['avada_update'] ) {
			self::$is_updating = true;
		}
	}

	/**
	 * Gets the theme version.
	 *
	 * @static
	 * @access public
	 * @since 5.0
	 * @return string
	 */
	public static function get_theme_version() {
		return self::$version;
	}

	/**
	 * Gets the normalized theme version.
	 *
	 * @static
	 * @access public
	 * @since 5.0
	 * @return string
	 */
	public static function get_normalized_theme_version() {
		$theme_version = self::$version;
		$theme_version_array = explode( '.', $theme_version );

		if ( isset( $theme_version_array[2] ) && '0' === $theme_version_array[2] ) {
			$theme_version = $theme_version_array[0] . '.' . $theme_version_array[1];
		}

		return $theme_version;
	}

	/**
	 * Gets the bundled plugins.
	 *
	 * @static
	 * @access public
	 * @since 5.0
	 * @return array Array of bundled plugins.
	 */
	public static function get_bundled_plugins() {
		return self::$bundled_plugins;
	}

	/**
	 * Sets the $lang property for this object.
	 * Languages are prefixed with a '_'
	 *
	 * If we're not currently performing a migration
	 * it also checks if the options for the current language are set.
	 * If they are not, then we will copy the options from the main language.
	 *
	 * @static
	 * @access public
	 */
	public static function multilingual_options() {

		global $fusion_library;
		$multilingual = $fusion_library->multilingual;

		// Set the self::$lang.
		$active_language = Fusion_Multilingual::get_active_language();
		if ( ! in_array( $active_language, array( '', 'en', 'all' ) ) ) {
			self::$lang = '_' . $active_language;
		}
		// Make sure the options are copied if needed.
		if ( ! in_array( self::$lang, array( '', 'en', 'all' ) ) && ! self::$lang_applied ) {
			// Set the $option_name property.
			self::$option_name = self::get_option_name();
			// Get the options without using a language (defaults).
			$original_options = get_option( self::$original_option_name, array() );
			// Get options with a language.
			$options = get_option( self::$original_option_name . self::$lang, array() );
			// If we're not currently performing a migration and the options are not set
			// then we must copy the default options to the new language.
			if ( ! self::$is_updating && ! empty( $original_options ) && empty( $options ) ) {
				update_option( self::$original_option_name . self::$lang, get_option( self::$original_option_name ) );
			}
			// Modify the option_name to include the language.
			self::$option_name  = self::$original_option_name . self::$lang;
			// Set $lang_applied to true. Makes sure we don't do the above more than once.
			self::$lang_applied = true;
		}
	}

	/**
	 * Get the private $option_name.
	 * If empty returns the original_option_name.
	 *
	 * @static
	 * @access public
	 * @return string
	 */
	public static function get_option_name() {
		if ( empty( self::$option_name ) ) {
			return self::$original_option_name;
		}
		return self::$option_name;
	}

	/**
	 * Get the private $original_option_name.
	 *
	 * @static
	 * @access public
	 * @return string
	 */
	public static function get_original_option_name() {
		return self::$original_option_name;
	}

	/**
	 * Change the private $option_name.
	 *
	 * @static
	 * @access public
	 * @param false|string $option_name The option name to use.
	 */
	public static function set_option_name( $option_name = false ) {
		if ( false !== $option_name && ! empty( $option_name ) ) {
			self::$option_name = $option_name;
		}
	}

	/**
	 * Change the private $language_is_all property.
	 *
	 * @static
	 * @access public
	 * @param bool $is_all Whether we're on the "all" language option or not.
	 * @return null|void
	 */
	public static function set_language_is_all( $is_all ) {
		if ( true === $is_all ) {
			self::$language_is_all = true;
			return;
		}
		self::$language_is_all = false;
	}

	/**
	 * Get the private $language_is_all property.
	 *
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function get_language_is_all() {
		return self::$language_is_all;
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
