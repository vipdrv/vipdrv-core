<?php
/**
 * Dynamic-CSS handler - Inline CSS.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle generating the dynamic CSS.
 *
 * @since 1.0.0
 */
class Fusion_Dynamic_CSS_Inline {

	/**
	 * An innstance of the Fusion_Dynamic_CSS object.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var object
	 */
	private $dynamic_css;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $dynamic_css An instance of Fusion_DYnamic_CSS.
	 */
	public function __construct( $dynamic_css ) {

		$this->dynamic_css = $dynamic_css;
		add_action( 'wp_head', array( $this, 'add_inline_css' ), 999 );

	}

	/**
	 * Add Inline CSS.
	 * We need this on because it has to be loaded after all other Avada CSS
	 * and W3TC can combine it correctly.
	 *
	 * @access public
	 * @return void
	 */
	public function add_inline_css() {

		$dynamic_css = $this->dynamic_css;
		echo '<style id="fusion-stylesheet-inline-css" type="text/css">';
		echo apply_filters( 'fusion_library_inline_dynamic_css', $dynamic_css->make_css() ); // WPCS: XSS ok.
		echo '</style>';

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
