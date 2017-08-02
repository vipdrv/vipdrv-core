<?php
/**
 * Shortcodes helper functions.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $fusion_builder_elements, $fusion_builder_multi_elements, $fusion_builder_enabled_elements, $parallax_id;
$parallax_id = 1;

// Get builder options.
$fusion_builder_settings = get_option( 'fusion_builder_settings' );
$fusion_builder_enabled_elements = ( isset( $fusion_builder_settings['fusion_elements'] ) ) ? $fusion_builder_settings['fusion_elements'] : '';
$fusion_builder_enabled_elements = apply_filters( 'fusion_builder_enabled_elements', $fusion_builder_enabled_elements );

// Stores an array of all registered elements.
$fusion_builder_elements = array();

// Stores an array of all advanced elements.
$fusion_builder_multi_elements = array();

/**
 * Add an element to $fusion_builder_elements array.
 *
 * @param array $module The element we're loading.
 */
function fusion_builder_map( $module ) {
	global $fusion_builder_elements, $fusion_builder_enabled_elements, $fusion_builder_multi_elements, $all_fusion_builder_elements, $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$shortcode    = $module['shortcode'];
	$ignored_atts = array();

	if ( isset( $module['params'] ) ) {

		// Create an array of descriptions.
		foreach ( $module['params'] as $key => $param ) {

			// Allow filtering of description.
			if ( isset( $param['description'] ) ) {
				$builder_map = fusion_builder_map_descriptions( $shortcode, $param['param_name'] );
				$dynamic_description = '';
				if ( is_array( $builder_map ) ) {
					$setting = ( isset( $builder_map['theme-option'] ) && '' !== $builder_map['theme-option'] ) ? $builder_map['theme-option'] : '';
					$subset = ( isset( $builder_map['subset'] ) && '' !== $builder_map['subset'] ) ? $builder_map['subset'] : '';
					$type = ( isset( $builder_map['type'] ) && '' !== $builder_map['type'] ) ? $builder_map['type'] : '';
					$reset = ( ( isset( $builder_map['reset'] ) || 'range' === $type ) && '' !== $param['default'] ) ? $param['param_name'] : '';
					$dynamic_description = $fusion_settings->get_default_description( $setting, $subset, $type , $reset, $param );
					$dynamic_description = apply_filters( 'fusion_builder_option_dynamic_description', $dynamic_description, $shortcode, $param['param_name'] );
				}
				if ( 'hide_on_mobile' === $param['param_name'] ) {
					$link = '<a href="' . $fusion_settings->get_setting_link( 'visibility_small' ) . '" target="_blank" rel="noopener noreferrer">' . apply_filters( 'fusion_options_label', esc_html( 'Element Options', 'Fusion-Builder' ) ) . '</a>';
					$param['description'] = $param['description'] . sprintf( __( '  Each of the 3 sizes has a custom width setting on the Fusion Builder Elements tab in the %s.', 'fusion-builder' ), $link );
				}
				$param['description'] = apply_filters( 'fusion_builder_option_description', $param['description'] . $dynamic_description, $shortcode, $param['param_name'] );
			}

			// Allow filtering of default.
			$current_default = ( isset( $param['default'] ) ) ? $param['default'] : '';
			$new_default = apply_filters( 'fusion_builder_option_default', $current_default, $shortcode, $param['param_name'] );
			if ( '' !== $new_default ) {
				$param['default'] = $new_default;
			}

			// Allow filtering of value.
			$current_value = ( isset( $param['value'] ) ) ? $param['value'] : '';
			$new_value = apply_filters( 'fusion_builder_option_value', $current_value, $shortcode, $param['param_name'] );
			if ( '' !== $new_value ) {
				$param['value'] = $new_value;
			}

			// Allow filtering of dependency.
			$current_dependency = ( isset( $param['dependency'] ) ) ? $param['dependency'] : '';
			$current_dependency = fusion_builder_element_dependencies( $current_dependency, $shortcode, $param['param_name'] );
			$new_dependency = apply_filters( 'fusion_builder_option_dependency', $current_dependency, $shortcode, $param['param_name'] );
			if ( '' !== $new_dependency ) {
				$param['dependency'] = $new_dependency;
			}

			// Ignore attributes in the shortcode if 'remove_from_atts' is true.
			if ( isset( $param['remove_from_atts'] ) && true == $param['remove_from_atts'] ) {
				$ignored_atts[] = $param['param_name'];
			}

			// Set param key as param_name.
			$params[ $param['param_name'] ] = $param;
		}
		if ( '0' === $fusion_settings->get( 'dependencies_status' ) ) {
			foreach ( $params as $key => $value ) {
				if ( isset( $params[ $key ]['dependency'] ) && ! empty( $params[ $key ]['dependency'] ) ) {
					unset( $params[ $key ]['dependency'] );
				}
			}
		}
		$module['params'] = $params;
		$module['remove_from_atts'] = $ignored_atts;
	}

	// Create array of unfiltered elements.
	$all_fusion_builder_elements[ $shortcode ] = $module;

	// Add multi element to an array.
	if ( isset( $module['multi'] ) && 'multi_element_parent' === $module['multi'] && isset( $module['element_child'] ) ) {
		$fusion_builder_multi_elements[ $shortcode ] = $module['element_child'];
	}

	// Remove fusion slider element if disabled from theme options.
	if ( 'fusion_fusionslider' === $shortcode && ! $fusion_settings->get( 'status_fusion_slider' ) ) {
		unset( $all_fusion_builder_elements[ $shortcode ] );
	}
}

/**
 * Filter available elements with enabled elements
 */
function fusion_builder_filter_available_elements() {
	global $fusion_builder_enabled_elements, $all_fusion_builder_elements, $fusion_builder_multi_elements;

	// If settings page was not saved, all elements are enabled.
	if ( '' === $fusion_builder_enabled_elements ) {
		$fusion_builder_enabled_elements = array_keys( $all_fusion_builder_elements );
	} else {
		// Add required shortcodes to enabled elements array.
		$fusion_builder_enabled_elements[] = 'fusion_builder_container';
		$fusion_builder_enabled_elements[] = 'fusion_builder_row';
		$fusion_builder_enabled_elements[] = 'fusion_builder_row_inner';
		$fusion_builder_enabled_elements[] = 'fusion_builder_column_inner';
		$fusion_builder_enabled_elements[] = 'fusion_builder_column';
		$fusion_builder_enabled_elements[] = 'fusion_builder_blank_page';
		$fusion_builder_enabled_elements[] = 'fusion_builder_next_page';
	}

	foreach ( $all_fusion_builder_elements as $module ) {
		// Get shortcode name.
		$shortcode = $module['shortcode'];

		// Check if its a multi element child.
		$multi_parent = array_search( $shortcode, $fusion_builder_multi_elements );

		if ( $multi_parent ) {
			if ( in_array( $multi_parent, $fusion_builder_enabled_elements ) ) {
				$fusion_builder_enabled_elements[] = $shortcode;
			}
		}

		// Add available elements to an array.
		if ( in_array( $shortcode, $fusion_builder_enabled_elements ) ) {

			$fusion_builder_elements[ $shortcode ] = $module;

		} else {
			// If parent shortcode is removed, also make sure to remove child shortcode.
			if ( isset( $module['multi'] ) && 'multi_element_parent' === $module['multi'] && isset( $module['element_child'] ) ) {

				remove_shortcode( $module['element_child'] );

			}

			remove_shortcode( $shortcode );
		}
	}

	return $fusion_builder_elements;

}

/**
 * Enqueue element frontend assets.
 */
function fusion_load_element_frontend_assets() {
	global $all_fusion_builder_elements;

	$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
	$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;

	foreach ( $all_fusion_builder_elements as $module ) {

		// Load element front end js.
		if ( ! empty( $module['front_enqueue_js'] ) ) {
			wp_enqueue_script( $module['shortcode'], $module['front_enqueue_js'], '', FUSION_BUILDER_VERSION, true );
		}

		// If we're using a file compiler for CSS we don't need to enqueue separate files for shortcodes.
		// These are added in dynamic-css from the fusion_load_element_frontend_assets_dynamic_css function.
		if ( 'file' === $mode ) {
			return;
		}

		// Load element front end css.
		if ( ! empty( $module['front_enqueue_css'] ) ) {
			wp_enqueue_style( $module['shortcode'], $module['front_enqueue_css'], array(), FUSION_BUILDER_VERSION );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'fusion_load_element_frontend_assets' );

/**
 * Add element frontend css to dynamic-css if possible.
 *
 * @since 1.1.5
 * @param string $original_styles The dynamic-css styles.
 * @return string The dynamic-css styles with any additional stylesheets appended.
 */
function fusion_load_element_frontend_assets_dynamic_css( $original_styles ) {
	global $all_fusion_builder_elements;
	$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
	$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;
	$styles = '';

	if ( 'file' === $mode ) {
		$wp_filesystem = Fusion_Helper::init_filesystem();
		foreach ( $all_fusion_builder_elements as $module ) {
			// Load element front end css.
			if ( ! empty( $module['front_enqueue_css'] ) ) {
				$fb_url  = untrailingslashit( FUSION_BUILDER_PLUGIN_DIR );
				$fb_path = untrailingslashit( FUSION_BUILDER_PLUGIN_URL );
				$path    = str_replace( $fb_url, $fb_path, $module['front_enqueue_css'] );
				// @codingStandardsIgnoreLine
				$styles .= @file_get_contents( $module['front_enqueue_css'] );
			}
		}
	}
	return $styles . $original_styles;
}
add_filter( 'fusion_dynamic_css_final', 'fusion_load_element_frontend_assets_dynamic_css' );
