<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Rollovers settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_rollover( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['rollover'] = array(
		'label'       => esc_html__( 'Featured Image Rollover', 'fusion-builder' ),
		'id'          => 'rollover',
		'is_panel'    => true,
		'priority'    => 26,
		'icon'        => 'el-icon-photo',
		'fields'      => array(
			'image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the rollover graphic on blog and portfolio featured images.', 'fusion-builder' ),
				'id'          => 'image_rollover',
				'default'     => '1',
				'type'        => 'switch',
			),
			'image_rollover_direction' => array(
				'label'       => esc_html__( 'Image Rollover Direction', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the direction the rollover starts from.', 'fusion-builder' ),
				'id'          => 'image_rollover_direction',
				'default'     => 'left',
				'type'        => 'select',
				'choices'     => array(
					'fade'            => esc_html__( 'Fade', 'fusion-builder' ),
					'left'            => esc_html__( 'Left', 'fusion-builder' ),
					'right'           => esc_html__( 'Right', 'fusion-builder' ),
					'bottom'          => esc_html__( 'Bottom', 'fusion-builder' ),
					'top'             => esc_html__( 'Top', 'fusion-builder' ),
					'center_horiz'    => esc_html__( 'Center Horizontal', 'fusion-builder' ),
					'center_vertical' => esc_html__( 'Center Vertical', 'fusion-builder' ),
				),
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'image_rollover_icon_size' => array(
				'label'       => esc_html__( 'Image Rollover Icon Font Size', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the size of the rollover icons.', 'fusion-builder' ),
				'id'          => 'image_rollover_icon_size',
				'default'     => '15px',
				'type'        => 'dimension',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'link_image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover Link Icon', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the link icon in the image rollover.', 'fusion-builder' ),
				'id'          => 'link_image_rollover',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'zoom_image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover Zoom Icon', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the zoom icon in the image rollover.', 'fusion-builder' ),
				'id'          => 'zoom_image_rollover',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'title_image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover Title', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the post title in the image rollover.', 'fusion-builder' ),
				'id'          => 'title_image_rollover',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'cats_image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover Categories', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the post categories in the image rollover.', 'fusion-builder' ),
				'id'          => 'cats_image_rollover',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'icon_circle_image_rollover' => array(
				'label'       => esc_html__( 'Image Rollover Icon Circle', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the icon background circle in the image rollover.', 'fusion-builder' ),
				'id'          => 'icon_circle_image_rollover',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'image_gradient_top_color' => array(
				'label'       => esc_html__( 'Image Rollover Gradient Top Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the top color of the image rollover background.', 'fusion-builder' ),
				'id'          => 'image_gradient_top_color',
				'type'        => 'color-alpha',
				'default'     => 'rgba(160,206,78,0.8)',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'image_gradient_bottom_color' => array(
				'label'       => esc_html__( 'Image Rollover Gradient Bottom Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the bottom color of the image rollover background.', 'fusion-builder' ),
				'id'          => 'image_gradient_bottom_color',
				'default'     => '#a0ce4e',
				'type'        => 'color-alpha',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'image_rollover_text_color' => array(
				'label'       => esc_html__( 'Image Rollover Element Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the color of image rollover text and icon circular backgrounds.', 'fusion-builder' ),
				'id'          => 'image_rollover_text_color',
				'default'     => '#333333',
				'type'        => 'color-alpha',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'image_rollover_icon_color' => array(
				'label'       => esc_html__( 'Image Rollover Icon Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the color of the icons in the image rollover.', 'fusion-builder' ),
				'id'          => 'image_rollover_icon_color',
				'default'     => '#ffffff',
				'type'        => 'color-alpha',
				'required'    => array(
					array(
						'setting'  => 'image_rollover',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		),
	);

	return $sections;

}
