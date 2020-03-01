<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle generating the dynamic CSS.
 *
 * @since 1.1.0
 */
class Fusion_Builder_Dynamic_CSS extends Fusion_Dynamic_CSS {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		parent::get_instance();

		if ( ! class_exists( 'Avada' ) ) {
			add_filter( 'fusion_dynamic_css', array( $this, 'custom_css' ) );
		}

		add_filter( 'fusion_dynamic_css_array', array( $this, 'fusion_builder_dynamic_css' ) );
		add_action( 'fusionredux/options/fusion_options/saved', array( $this, 'reset_all_caches' ), 999 );
	}

	/**
	 * Appends the custom-css option to the dynamic-css.
	 *
	 * @access public
	 * @since 1.1.0
	 * @param string $css The final CSS.
	 * @return string
	 */
	public function custom_css( $css ) {

		// Append the user-entered dynamic CSS.
		$option = get_option( Fusion_Settings::get_option_name(), array() );
		if ( isset( $option['custom_css'] ) && ! empty( $option['custom_css'] ) ) {
			$css .= wp_strip_all_tags( $option['custom_css'] );
		}

		return $css;

	}

	/**
	 * Format of the $css array:
	 * $css['media-query']['element']['property'] = value
	 *
	 * If no media query is required then set it to 'global'
	 *
	 * If we want to add multiple values for the same property then we have to make it an array like this:
	 * $css[media-query][element]['property'][] = value1
	 * $css[media-query][element]['property'][] = value2
	 *
	 * Multiple values defined as an array above will be parsed separately.
	 *
	 * @param array $original_css The existing CSS.
	 */
	public function fusion_builder_dynamic_css( $original_css = array() ) {
		global $fusion_settings;

		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}

		$dynamic_css         = Fusion_Dynamic_CSS::get_instance();
		$dynamic_css_helpers = $dynamic_css->get_helpers();
		$css                 = array();

		$info_background_color    = '' !== $fusion_settings->get( 'info_bg_color' ) ? strtolower( $fusion_settings->get( 'info_bg_color' ) ) : '#ffffff';
		$info_accent_color        = fusion_auto_calculate_accent_color( $info_background_color );

		$danger_background_color  = '' !== $fusion_settings->get( 'danger_bg_color' ) ? strtolower( $fusion_settings->get( 'danger_bg_color' ) ) : '#f2dede';
		$danger_accent_color      = fusion_auto_calculate_accent_color( $danger_background_color );

		$success_background_color = '' !== $fusion_settings->get( 'success_bg_color' ) ? strtolower( $fusion_settings->get( 'success_bg_color' ) ) : '#dff0d8';
		$success_accent_color     = fusion_auto_calculate_accent_color( $success_background_color );

		$warning_background_color = '' !== $fusion_settings->get( 'warning_bg_color' ) ? strtolower( $fusion_settings->get( 'warning_bg_color' ) ) : '#fcf8e3';
		$warning_accent_color     = fusion_auto_calculate_accent_color( $warning_background_color );

		if ( defined( 'WPCF7_PLUGIN' ) ) {
			// CF7 error notice.
			$elements = array(
				'.wpcf7-form .wpcf7-mail-sent-ng',
				'.wpcf7-form .wpcf7-validation-errors',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $danger_background_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border'] = '1px solid ' . $danger_accent_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $danger_accent_color;

			// CF7 success notice.
			$css['global']['.wpcf7-form .wpcf7-mail-sent-ok']['background-color'] = $success_background_color;
			$css['global']['.wpcf7-form .wpcf7-mail-sent-ok']['border'] = '1px solid ' . $success_accent_color;
			$css['global']['.wpcf7-form .wpcf7-mail-sent-ok']['color'] = $success_accent_color;
		}

		if ( class_exists( 'WooCommerce' ) ) {
			// WooCommerce error notice.
			$css['global']['.woocommerce-error li']['background-color'] = $danger_background_color;
			$css['global']['.woocommerce-error li']['border'] = '1px solid ' . $danger_accent_color;
			$css['global']['.woocommerce-error li']['color'] = $danger_accent_color;

			// WooCommerce general notice.
			$elements = array(
				'.woocommerce-info',
				'.woocommerce-message',
			);

			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $info_background_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top'] = '1px solid ' . $info_accent_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom'] = '1px solid ' . $info_accent_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $info_accent_color;

		}

		if ( class_exists( 'bbPress' ) ) {
			// bbPress general notice.
			$elements = array(
				'div.bbp-template-notice',
				'div.indicator-hint',
			);

			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = $info_background_color;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border'] = '1px solid ' . $info_accent_color;

		}

		// General - Events info notice.
		$elements = array(
			'.fusion-alert.alert-info',
			'.tribe-events-notices.alert-info',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $info_background_color;
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $info_accent_color;
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $info_accent_color;

		// Error - Events error notice.
		$css['global']['.alert-danger']['background-color'] = $danger_background_color;
		$css['global']['.alert-danger']['border-color'] = $danger_accent_color;
		$css['global']['.alert-danger']['color'] = $danger_accent_color;

		// Success - Events success notice.
		$css['global']['.alert-success']['background-color'] = $success_background_color;
		$css['global']['.alert-success']['border-color'] = $success_accent_color;
		$css['global']['.alert-success']['color'] = $success_accent_color;

		// Warning - Events warning notice.
		$css['global']['.alert-warning']['background-color'] = $warning_background_color;
		$css['global']['.alert-warning']['border-color'] = $warning_accent_color;
		$css['global']['.alert-warning']['color'] = $warning_accent_color;

		$css = array_replace_recursive( $css, $original_css );

		return apply_filters( 'avada_dynamic_css_array', $css );

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
