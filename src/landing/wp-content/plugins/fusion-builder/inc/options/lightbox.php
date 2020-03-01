<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Lightbox
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_lightbox( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['lightbox'] = array(
		'label'    => esc_html__( 'Lightbox', 'fusion-builder' ),
		'id'       => 'heading_lightbox',
		'priority' => 21,
		'icon'     => 'el-icon-info-circle',
		'option_name' => $option_name,
		'fields'   => array(
			'status_lightbox' => array(
				'label'       => esc_html__( 'Lightbox', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to enable the lightbox throughout the theme.', 'fusion-builder' ),
				'id'          => 'status_lightbox',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
			),
			'status_lightbox_single' => array(
				'label'       => esc_html__( 'Lightbox On Single Post Pages', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to enable the lightbox on single blog and portfolio posts.', 'fusion-builder' ),
				'id'          => 'status_lightbox_single',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_behavior' => array(
				'label'       => esc_html__( 'Lightbox Behavior', 'fusion-builder' ),
				'description' => esc_html__( 'Controls what the lightbox displays for single blog and portfolio posts.', 'fusion-builder' ),
				'id'          => 'lightbox_behavior',
				'default'     => 'all',
				'type'        => 'select',
				'option_name' => $option_name,
				'choices'     => array(
					'all'        => esc_html__( 'First featured image of every post', 'fusion-builder' ),
					'individual' => esc_html__( 'Only featured images of individual post', 'fusion-builder' ),
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_skin' => array(
				'label'       => esc_html__( 'Lightbox Skin', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the lightbox skin design.', 'fusion-builder' ),
				'id'          => 'lightbox_skin',
				'default'     => 'metro-white',
				'type'        => 'select',
				'option_name' => $option_name,
				'choices'     => array(
					'light'       => esc_html__( 'Light', 'fusion-builder' ),
					'dark'        => esc_html__( 'Dark', 'fusion-builder' ),
					'mac'         => esc_html__( 'Mac', 'fusion-builder' ),
					'metro-black' => esc_html__( 'Metro Black', 'fusion-builder' ),
					'metro-white' => esc_html__( 'Metro White', 'fusion-builder' ),
					'parade'      => esc_html__( 'Parade', 'fusion-builder' ),
					'smooth'      => esc_html__( 'Smooth', 'fusion-builder' ),
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_path' => array(
				'label'       => esc_html__( 'Thumbnails Position', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the position of the lightbox thumbnails.', 'fusion-builder' ),
				'id'          => 'lightbox_path',
				'default'     => 'vertical',
				'type'        => 'radio-buttonset',
				'option_name' => $option_name,
				'choices'     => array(
					'vertical'   => esc_html__( 'Right', 'fusion-builder' ),
					'horizontal' => esc_html__( 'Bottom', 'fusion-builder' ),
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_animation_speed' => array(
				'label'       => esc_html__( 'Animation Speed', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the animation speed of the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_animation_speed',
				'default'     => 'Normal',
				'type'        => 'radio-buttonset',
				'option_name' => $option_name,
				'choices'     => array(
					'Fast'   => esc_html__( 'Fast', 'fusion-builder' ),
					'Slow'   => esc_html__( 'Slow', 'fusion-builder' ),
					'Normal' => esc_html__( 'Normal', 'fusion-builder' ),
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_arrows' => array(
				'label'       => esc_html__( 'Arrows', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display arrows in the lightbox', 'fusion-builder' ),
				'id'          => 'lightbox_arrows',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_gallery' => array(
				'label'       => esc_html__( 'Gallery Start/Stop Button', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the gallery start and stop button.', 'fusion-builder' ),
				'id'          => 'lightbox_gallery',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_autoplay' => array(
				'label'       => esc_html__( 'Autoplay the Lightbox Gallery', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to autoplay the lightbox gallery.', 'fusion-builder' ),
				'id'          => 'lightbox_autoplay',
				'default'     => '0',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_slideshow_speed' => array(
				'label'       => esc_html__( 'Slideshow Speed', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the slideshow speed if autoplay is turned on. ex: 1000 = 1 second.', 'fusion-builder' ),
				'id'          => 'lightbox_slideshow_speed',
				'default'     => '5000',
				'type'        => 'slider',
				'option_name' => $option_name,
				'choices'     => array(
					'min'  => '1000',
					'max'  => '20000',
					'step' => '50',
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_opacity' => array(
				'label'       => esc_html__( 'Background Opacity', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the opacity level for the background behind the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_opacity',
				'default'     => '0.9',
				'type'        => 'slider',
				'option_name' => $option_name,
				'choices'     => array(
					'min'  => '0.1',
					'max'  => '1',
					'step' => '0.01',
				),
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_title' => array(
				'label'       => esc_html__( 'Title', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the image title in the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_title',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_desc' => array(
				'label'       => esc_html__( 'Caption', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the image caption in the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_desc',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_social' => array(
				'label'       => esc_html__( 'Social Sharing', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display social sharing buttons on lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_social',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_deeplinking' => array(
				'label'       => esc_html__( 'Deeplinking', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to deeplink images in the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_deeplinking',
				'default'     => '1',
				'type'        => 'switch',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'lightbox_video_dimensions' => array(
				'label'       => esc_html__( 'Slideshow Video Dimensions', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the width and height for videos inside the lightbox.', 'fusion-builder' ),
				'id'          => 'lightbox_video_dimensions',
				'units'       => false,
				'default'     => array(
					'width'   => '1280px',
					'height'  => '720px',
				),
				'type'        => 'dimensions',
				'option_name' => $option_name,
				'required'    => array(
					array(
						'setting'  => 'status_lightbox',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		),
	);

	return $sections;

}
