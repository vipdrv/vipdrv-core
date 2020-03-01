<?php
/**
 * Handler for dynamic-css.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8.5
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle generating the dynamic CSS.
 */
class Avada_Dynamic_CSS {

	/**
	 * The Fusion_Dynamic_CSS object.
	 *
	 * @access protected
	 * @since 5.1.0
	 * @var object
	 */
	protected $fusion_dynamic_css;

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		$this->fusion_dynamic_css = Fusion_Dynamic_CSS::get_instance();
		add_action( 'wp', array( $this, 'init' ) );

	}

	/**
	 * Add extra actions.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		add_filter( 'fusion_dynamic_css_stylesheet_dependencies', array( $this, 'stylesheet_dependencies' ) );
		add_filter( 'fusion_dynamic_css_cached', array( $this, 'fusion_dynamic_css_cached' ), 999 );

		// Backwards-compatibility.
		add_filter( 'fusion_dynamic_css', array( $this, 'avada_dynamic_css_filter' ) );

	}

	/**
	 * Appends the custom-css option to the dynamic-css.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $css The final CSS.
	 * @return string
	 */
	public function fusion_dynamic_css_cached( $css ) {

		// Append the user-entered dynamic CSS.
		$option = get_option( Avada::get_option_name(), array() );
		if ( isset( $option['custom_css'] ) && ! empty( $option['custom_css'] ) ) {
			$css .= wp_strip_all_tags( $option['custom_css'] );
		}
		return $css;

	}

	/**
	 * Adds a dependency for the main stylesheet.
	 *
	 * @access public
	 * @since 5.1
	 * @param array $dependencies An array of dependencies for the stylesheet.
	 * @return array
	 */
	public function stylesheet_dependencies( $dependencies ) {

		$dependencies[] = 'avada-stylesheet';
		return $dependencies;

	}

	/**
	 * Backwards-compatibility:
	 * 'avada_dynamic_css' filter was replaced by 'fusion_dynamic_css'.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $css The final CSS.
	 * @return string
	 */
	public function avada_dynamic_css_filter( $css ) {
		return apply_filters( 'avada_dynamic_css', $css );
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
