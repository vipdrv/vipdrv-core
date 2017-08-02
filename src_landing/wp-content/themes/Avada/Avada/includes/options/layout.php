<?php
/**
 * Avada Options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Layout
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_layout( $sections ) {

	$settings = get_option( Avada::get_option_name(), array() );
	$language = Fusion_Multilingual::get_active_language();
	$avada_510_site_width_calc_option_name = 'avada_510_site_width_calc';
	$display_site_width_warning = false;
	if ( '' !== $language && 'en' !== $language ) {
		$avada_510_site_width_calc_option_name .= $language;
	}
	if ( get_option( $avada_510_site_width_calc_option_name, false ) ) {
		if ( isset( $settings['site_width'] ) && false !== strpos( $settings['site_width'], 'calc' ) ) {
			$display_site_width_warning = true;
		}
	}

	$sections['layout'] = array(
		'label'    => esc_html__( 'Layout', 'Avada' ),
		'id'       => 'heading_layout',
		'priority' => 1,
		'icon'     => 'el-icon-website',
		'fields'   => array(
			'layout' => array(
				'label'       => esc_html__( 'Layout', 'Avada' ),
				'description' => esc_html__( 'Controls the site layout.', 'Avada' ),
				'id'          => 'layout',
				'default'     => 'Wide',
				'type'        => 'radio-buttonset',
				'choices'     => array(
					'Boxed'   => esc_html__( 'Boxed', 'Avada' ),
					'Wide'    => esc_html__( 'Wide', 'Avada' ),
				),
			),
			'site_width' => array(
				'label'       => esc_html__( 'Site Width', 'Avada' ),
				'description' => esc_html__( 'Controls the overall site width.', 'Avada' ),
				'id'          => 'site_width',
				'default'     => '1100px',
				'type'        => 'dimension',
				'choices'     => array( 'px', '%' ),
				'transport'   => 'postMessage',
				'desc'        => ( $display_site_width_warning ) ? esc_attr__( 'The value was changed in Avada 5.1 to include both the site-width & side-header width, ex: calc(90% + 300px). Leave this as is, or update it with a single percentage, ex: 95%', 'Avada' ) : '',
			),
			'margin_offset' => array(
				'label'       => esc_html__( 'Boxed Mode Top/Bottom Offset', 'Avada' ),
				'description' => esc_html__( 'Controls the top/bottom offset of the boxed background.', 'Avada' ),
				'id'          => 'margin_offset',
				'choices'     => array(
					'top'     => true,
					'bottom'  => true,
					'units'   => array( 'px', '%' ),
				),
				'default'     => array(
					'top'     => '0px',
					'bottom'  => '0px',
				),
				'type'        => 'spacing',
				'required'    => array(
					array(
						'setting'  => 'layout',
						'operator' => '==',
						'value'    => 'Boxed',
					),
				),
			),
			'scroll_offset' => array(
				'label'       => esc_html__( 'Boxed Mode Offset Scroll Mode', 'Avada' ),
				'description' => esc_html__( 'Choose how the page will scroll. Framed scrolling will keep the offset in place, while Full scrolling removes the offset when scrolling the page.', 'Avada' ),
				'id'          => 'scroll_offset',
				'type'        => 'radio-buttonset',
				'choices'     => array(
					'framed'  => esc_html__( 'Framed Scrolling', 'Avada' ),
					'full'    => esc_html__( 'Full Scrolling', 'Avada' ),
				),
				'default'     => 'full',
				'required'    => array(
					array(
						'setting'  => 'layout',
						'operator' => '==',
						'value'    => 'Boxed',
					),
				),
			),
			'boxed_modal_shadow' => array(
				'label'       => esc_html__( 'Boxed Mode Shadow Type', 'Avada' ),
				'description' => esc_html__( 'Controls the type of shadow your boxed mode displays.', 'Avada' ),
				'id'          => 'boxed_modal_shadow',
				'default'     => 'None',
				'type'        => 'select',
				'choices'     => array(
					'None'    => esc_html__( 'No Shadow', 'Avada' ),
					'Light'   => esc_html__( 'Light Shadow', 'Avada' ),
					'Medium'  => esc_html__( 'Medium Shadow', 'Avada' ),
					'Hard'    => esc_html__( 'Hard Shadow', 'Avada' ),
				),
				'required'    => array(
					array(
						'setting'  => 'layout',
						'operator' => '==',
						'value'    => 'Boxed',
					),
				),
			),
			'main_padding' => array(
				'label'       => esc_html__( 'Page Content Padding', 'Avada' ),
				'description' => esc_html__( 'Controls the top/bottom padding for page content.', 'Avada' ),
				'id'          => 'main_padding',
				'choices'     => array(
					'top'     => true,
					'bottom'  => true,
					'units'   => array( 'px', '%' ),
				),
				'default'     => array(
					'top'     => '55px',
					'bottom'  => '40px',
				),
				'type'        => 'spacing',
			),
			'hundredp_padding' => array(
				'label'       => esc_html__( '100% Width Padding', 'Avada' ),
				'description' => esc_html__( 'Controls the left and right padding for page content when using 100% site width, 100% width page template or 100% width post option. This does not affect Fusion Builder containers. Enter value including any valid CSS unit, ex: 30px.', 'Avada' ),
				'id'          => 'hundredp_padding',
				'default'     => '30px',
				'type'        => 'dimension',
				'choices'     => array( 'px', '%' ),
			),
			'single_sidebar_layouts_info' => array(
				'label'           => esc_html__( 'Single Sidebar Layouts', 'Avada' ),
				'description'     => '',
				'id'              => 'single_sidebar_layouts_info',
				'type'            => 'info',
			),
			'sidebar_width' => array(
				'label'       => esc_html__( 'Single Sidebar Width', 'Avada' ),
				'description' => esc_html__( 'Controls the width of the sidebar when only one sidebar is present.', 'Avada' ),
				'id'          => 'sidebar_width',
				'default'     => '23%',
				'type'        => 'dimension',
				'choices'     => array( 'px', '%' ),
			),
			'dual_sidebar_layouts_info' => array(
				'label'           => esc_html__( 'Dual Sidebar Layouts', 'Avada' ),
				'description'     => '',
				'id'              => 'dual_sidebar_layouts_info',
				'type'            => 'info',
			),
			'sidebar_2_1_width' => array(
				'label'       => esc_html__( 'Dual Sidebar Width 1', 'Avada' ),
				'description' => esc_html__( 'Controls the width of sidebar 1 when dual sidebars are present.', 'Avada' ),
				'id'          => 'sidebar_2_1_width',
				'default'     => '21%',
				'type'        => 'dimension',
				'choices'     => array( 'px', '%' ),
			),
			'sidebar_2_2_width' => array(
				'label'       => esc_html__( 'Dual Sidebar Width 2', 'Avada' ),
				'description' => esc_html__( 'Controls the width of sidebar 2 when dual sidebars are present.', 'Avada' ),
				'id'          => 'sidebar_2_2_width',
				'default'     => '21%',
				'type'        => 'dimension',
				'choices'     => array( 'px', '%' ),
			),
		),
	);

	return $sections;

}
