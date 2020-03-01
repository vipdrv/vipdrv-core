<?php
/**
 * The main Fusion library object.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The main Fusion library object.
 */
class Fusion {

	/**
	 * The one, true instance of the object.
	 *
	 * @static
	 * @access public
	 * @var null|object
	 */
	public static $instance = null;

	/**
	 * The current page ID.
	 *
	 * @access public
	 * @var bool|int
	 */
	public static $c_page_id = false;

	/**
	 * An instance of the Fusion_Images class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var object Fusion_Images
	 */
	public $images;

	/**
	 * An instance of the Fusion_Multilingual class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var object Fusion_Multilingual
	 */
	public $multilingual;

	/**
	 * And instance of the Fusion_Scripts class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var object Fusion_Scripts
	 */
	public $scripts;

	/**
	 * And instance of the Fusion_Dynamic_JS class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var object Fusion_Dynamic_JS
	 */
	public $dynamic_js;

	/**
	 * The class constructor
	 */
	private function __construct() {
		add_action( 'wp', array( $this, 'set_page_id' ) );

		if ( ! defined( 'AVADA_VERSION' ) ) {
			$this->images       = new Fusion_Images();
		}
		$this->sanitize     = new Fusion_Sanitize();
		$this->multilingual = new Fusion_Multilingual();
		$this->scripts      = new Fusion_Scripts();
		$this->dynamic_js   = new Fusion_Dynamic_JS();

		if ( $this->supported_plugins_changed() && class_exists( 'Fusion_Cache' ) ) {
			$fusion_cache = new Fusion_Cache();
			$fusion_cache->reset_all_caches();
		}
	}

	/**
	 * Access the single instance of this class.
	 *
	 * @return Fusion
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Gets the current page ID.
	 *
	 * @return string The current page ID.
	 */
	public function get_page_id() {
		if ( ! self::$c_page_id ) {
			$this->set_page_id();
		}
		return self::$c_page_id;
	}

	/**
	 * Sets the current page ID.
	 *
	 * @uses self::c_page_id
	 */
	public function set_page_id() {
		if ( ! self::$c_page_id ) {
			self::$c_page_id = self::c_page_id();
		}
	}

	/**
	 * Gets the current page ID.
	 *
	 * @return bool|int
	 */
	private static function c_page_id() {

		if ( get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) && is_home() ) {
			return get_option( 'page_for_posts' );
		}

		$c_page_id = get_queried_object_id();

		// The woocommerce shop page.
		if ( ! is_admin() && class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
			return get_option( 'woocommerce_shop_page_id' );
		}
		// The homepage.
		if ( 'posts' === get_option( 'show_on_front' ) && is_home() ) {
			return $c_page_id;
		}
		if ( ! is_singular() ) {
			return false;
		}
		return $c_page_id;
	}
	/**
	 * Gets the value of a theme option.
	 *
	 * @static
	 * @access public
	 * @param string|null               $option  The option.
	 * @param string|false              $subset  The sub-option in case of an array.
	 * @param string|array|null|boolean $default The default fallback value.
	 */
	public function get_option( $option = null, $subset = false, $default = null ) {

		global $fusion_settings;
		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}
		return $fusion_settings->get( $option, $subset, $default );
	}

	/**
	 * Check if the supported plugins array has changed.
	 * If a supported plugin was activated or deactivated
	 * we should reset all caches.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return bool True if changed, false if unchanged.
	 */
	protected function supported_plugins_changed() {
		$classes_to_check = array(
			'WPCF7',
			'bbPress',
			'WooCommerce',
			'Tribe__Events__Main',
		);
		$constants_to_check = array(
			'LS_PLUGIN_VERSION',
			'RS_PLUGIN_PATH',
		);

		$supported_saved    = get_option( 'fusion_supported_plugins_active', array() );
		$supported_detected = array();
		foreach ( $classes_to_check as $class ) {
			if ( class_exists( $class ) ) {
				$supported_detected[] = $class;
			}
		}
		foreach ( $constants_to_check as $constant ) {
			if ( defined( $constant ) ) {
				$supported_detected[] = $constant;
			}
		}
		if ( $supported_detected !== $supported_saved ) {
			update_option( 'fusion_supported_plugins_active', $supported_detected );
			return true;
		}
		return false;
	}
}
