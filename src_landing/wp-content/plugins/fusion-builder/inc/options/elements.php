<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Element settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_elements( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['shortcode_styling'] = array(
		'label'    => esc_html__( 'Fusion Builder Elements', 'fusion-builder' ),
		'id'       => 'fusion_builder_elements',
		'is_panel' => true,
		'priority' => 14,
		'icon'     => 'el-icon-cog',
		'fields'   => array(
			'animations_shortcode_section' => array(
				'label'       => esc_html__( 'Animations', 'fusion-builder' ),
				'description' => '',
				'id'          => 'shortcode_animations_accordion',
				'default'     => '',
				'type'        => 'accordion',
				'fields'      => array(
					'animation_offset' => array(
						'label'       => esc_html__( 'Animation Offset', 'fusion-builder' ),
						'description' => esc_html__( 'Controls when the animation should start.', 'fusion-builder' ),
						'id'          => 'animation_offset',
						'default'     => 'top-into-view',
						'type'        => 'select',
						'option_name' => $option_name,
						'choices'     => array(
							'top-into-view'   => esc_html__( 'Top of element hits bottom of viewport', 'fusion-builder' ),
							'top-mid-of-view' => esc_html__( 'Top of element hits middle of viewport', 'fusion-builder' ),
							'bottom-in-view'  => esc_html__( 'Bottom of element enters viewport', 'fusion-builder' ),
						),
					),
				),
			),
			'carousel_shortcode_section' => array(
				'label'       => esc_html__( 'Carousel Element', 'fusion-builder' ),
				'description' => '',
				'id'          => 'carousel_shortcode_section',
				'type'        => 'accordion',
				'fields'      => array(
					'carousel_nav_color' => array(
						'label'       => esc_html__( 'Carousel Navigation Box Color', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the color of the navigation box for carousel sliders.', 'fusion-builder' ),
						'id'          => 'carousel_nav_color',
						'default'     => 'rgba(0,0,0,0.6)',
						'type'        => 'color-alpha',
						'option_name' => $option_name,
					),
					'carousel_hover_color' => array(
						'label'       => esc_html__( 'Carousel Hover Navigation Box Color', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the color of the hover navigation box for carousel sliders.', 'fusion-builder' ),
						'id'          => 'carousel_hover_color',
						'default'     => 'rgba(0,0,0,0.7)',
						'type'        => 'color-alpha',
						'option_name' => $option_name,
					),
					'carousel_speed' => array(
						'label'       => esc_html__( 'Carousel Speed', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the speed of all carousel elements. ex: 1000 = 1 second.', 'fusion-builder' ),
						'id'          => 'carousel_speed',
						'default'     => '2500',
						'type'        => 'slider',
						'option_name' => $option_name,
						'choices'     => array(
							'min'  => '1000',
							'max'  => '20000',
							'step' => '250',
						),
					),
				),
			),
			'visibility_shortcode_section' => array(
				'label'       => esc_html__( 'Visibility Size Options', 'fusion-builder' ),
				'id'          => 'visibility_shortcode_section',
				'description' => '',
				'type'        => 'accordion',
				'fields'      => array(
					'visibility_small' => array(
						'label'       => esc_html__( 'Small Screen', 'fusion-builder' ),
						'description' => esc_html__( 'Controls when the small screen visibility should take effect.', 'fusion-builder' ),
						'id'          => 'visibility_small',
						'default'     => '640',
						'type'        => 'slider',
						'min'         => '0',
						'step'        => '1',
						'max'         => '2000',
						'option_name' => $option_name,
					),
					'visibility_medium' => array(
						'label'       => esc_html__( 'Medium Screen', 'fusion-builder' ),
						'description' => esc_html__( 'Controls when the medium screen visibility should take effect.', 'fusion-builder' ),
						'id'          => 'visibility_medium',
						'default'     => '1024',
						'type'        => 'slider',
						'min'         => '0',
						'step'        => '1',
						'max'         => '2000',
						'option_name' => $option_name,
					),
					'visibility_large' => array(
						'label'       => esc_html__( 'Large Screen', 'fusion-builder' ),
						'description' => esc_html__( 'Any screen larger than that which is defined as the medium screen will be counted as a large screen.', 'fusion-builder' ),
						'id'          => 'visibility_large',
						'full_width'  => false,
						'type'        => 'raw',
						'content'     => '<div id="fusion-visibility-large">' . ( ( isset( $settings['visibility_medium'] ) && ! empty( $settings['visibility_medium'] ) ) ? '> <span>' . $settings['visibility_medium'] . '</span>' : '> <span>1200</span>' ) . '</div>',
						'option_name' => $option_name,
					),
				),
			),
		),
	);

	return $sections;

}
