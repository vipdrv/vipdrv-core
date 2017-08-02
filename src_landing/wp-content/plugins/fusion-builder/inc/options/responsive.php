<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Mobile settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_responsive( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['mobile'] = array(
		'label'    => esc_html__( 'Responsive', 'fusion-builder' ),
		'id'       => 'responsive',
		'priority' => 2,
		'icon'     => 'el-icon-resize-horizontal',
		'option_name' => $option_name,
		'fields'   => array(
			'responsive' => array(
				'label'       => esc_html__( 'Responsive Design', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to use the responsive design features. If set to off, the fixed layout is used.', 'fusion-builder' ),
				'id'          => 'responsive',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'choices'     => array(
					'on'  => esc_html__( 'On', 'fusion-builder' ),
					'off' => esc_html__( 'Off', 'fusion-builder' ),
				),
			),
			'grid_main_break_point' => array(
				'label'       => esc_html__( 'Grid Responsive Breakpoint', 'fusion-builder' ),
				'description' => esc_html__( 'Controls when grid layouts (blog/portfolio) start to break into smaller columns. Further breakpoints are auto calculated.', 'fusion-builder' ),
				'id'          => 'grid_main_break_point',
				'default'     => '1000',
				'type'        => 'slider',
				'option_name' => $option_name,
				'choices'     => array(
					'min'  => '360',
					'max'  => '2000',
					'step' => '1',
					'edit' => 'yes',
				),
				'required'    => array(
					array(
						'setting'  => 'responsive',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'content_break_point' => array(
				'label'       => esc_html__( 'Site Content Responsive Breakpoint', 'fusion-builder' ),
				'description' => esc_html__( 'Controls when the site content area changes to the mobile layout. This includes all content below the header including the footer.', 'fusion-builder' ),
				'id'          => 'content_break_point',
				'default'     => '800',
				'type'        => 'slider',
				'option_name' => $option_name,
				'choices'     => array(
					'min'  => '0',
					'max'  => '2000',
					'step' => '1',
					'edit' => 'yes',
				),
				'required'    => array(
					array(
						'setting'  => 'responsive',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'typography_responsive' => array(
				'label'       => esc_html__( 'Responsive Heading Typography', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on for headings to change font size responsively.', 'fusion-builder' ),
				'id'          => 'typography_responsive',
				'default'     => '0',
				'choices'     => array(
					'on'  => esc_html__( 'On', 'fusion-builder' ),
					'off' => esc_html__( 'Off', 'fusion-builder' ),
				),
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'responsive',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'typography_sensitivity' => array(
				'label'           => esc_html__( 'Responsive Typography Sensitivity', 'fusion-builder' ),
				'description'     => esc_html__( 'Values below 1 decrease rate of resizing, values above 1 increase rate of resizing.', 'fusion-builder' ),
				'id'              => 'typography_sensitivity',
				'default'         => '0.6',
				'type'            => 'slider',
				'required'        => array(
					array(
						'setting'  => 'responsive',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'typography_responsive',
						'operator' => '!=',
						'value'    => 0,
					),
				),
				'choices'         => array(
					'min'  => '0',
					'max'  => '2',
					'step' => '.01',
				),
			),
			'typography_factor' => array(
				'label'       => esc_html__( 'Minimum Font Size Factor', 'fusion-builder' ),
				'description' => esc_html__( 'Minimum font factor is used to determine the minimum distance between headings and body font by a multiplying value.', 'fusion-builder' ),
				'id'          => 'typography_factor',
				'default'     => '1.5',
				'type'        => 'slider',
				'option_name' => $option_name,
				'required'        => array(
					array(
						'setting'  => 'responsive',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'setting'  => 'typography_responsive',
						'operator' => '!=',
						'value'    => 0,
					),
				),
				'choices'     => array(
					'min'  => '0',
					'max'  => '4',
					'step' => '.01',
				),
			),
		),
	);

	return $sections;
}
