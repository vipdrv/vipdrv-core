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
 * Background settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_background( $sections ) {

	$settings = get_option( Avada::get_option_name() );

	$sections['background'] = array(
		'label'    => esc_html__( 'Background', 'Avada' ),
		'id'       => 'heading_background',
		'priority' => 11,
		'icon'     => 'el-icon-photo',
		'fields'   => array(
			'main_content_bg_subsection' => array(
				'label'       => esc_html__( 'Main Content Background', 'Avada' ),
				'description' => '',
				'id'          => 'main_content_bg_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'content_bg_color' => array(
						'label'       => esc_html__( 'Main Content Background Color', 'Avada' ),
						'description' => esc_html__( 'Controls the background color of the main content area which is everything below header and above footer.', 'Avada' ),
						'id'          => 'content_bg_color',
						'default'     => '#ffffff',
						'type'        => 'color-alpha',
					),
					'content_bg_image' => array(
						'label'       => esc_html__( 'Background Image For Main Content Area', 'Avada' ),
						'description' => esc_html__( 'Select an image to use for the main content area background.', 'Avada' ),
						'id'          => 'content_bg_image',
						'default'     => '',
						'mod'         => '',
						'type'        => 'media',
					),
					'content_bg_full' => array(
						'label'       => esc_html__( '100% Background Image', 'Avada' ),
						'description' => esc_html__( 'Turn on to have the main content background image display at 100% in width and height according to the window size.', 'Avada' ),
						'id'          => 'content_bg_full',
						'default'     => '0',
						'type'        => 'switch',
						'required'    => array(
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'  => '',
								),
							),
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'       => '',
									'id'        => '',
									'height'    => '',
									'width'     => '',
									'thumbnail' => '',
								),
							),
						),
					),
					'content_bg_repeat' => array(
						'label'       => esc_html__( 'Background Repeat', 'Avada' ),
						'description' => esc_html__( 'Controls how the background image repeats.', 'Avada' ),
						'id'          => 'content_bg_repeat',
						'default'     => 'no-repeat',
						'type'        => 'select',
						'choices'     => array(
							'repeat'    => esc_html__( 'Repeat All', 'Avada' ),
							'repeat-x'  => esc_html__( 'Repeat Horizontally', 'Avada' ),
							'repeat-y'  => esc_html__( 'Repeat Vertically', 'Avada' ),
							'no-repeat' => esc_html__( 'No Repeat', 'Avada' ),
						),
						'required'    => array(
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'  => '',
								),
							),
							array(
								'setting'  => 'content_bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'       => '',
									'id'        => '',
									'height'    => '',
									'width'     => '',
									'thumbnail' => '',
								),
							),
						),
					),
				),
			),
			'boxed_mode_bg_subsection' => array(
				'label'       => esc_html__( 'Boxed Mode Background', 'Avada' ),
				'description' => '',
				'id'          => 'boxed_mode_bg_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'boxed_mode_backgrounds_important_note_info' => ( '0' === Avada()->settings->get( 'dependencies_status' ) ) ? array() : array(
						'label'       => '',
						'description' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> The layout is set to "Wide" on the Layout tab. However, there are boxed mode options below that are still visible due to having a fusion page option dependency. Click the link in the option description to learn more.', 'Avada' ) . '</div>',
						'id'          => 'boxed_mode_backgrounds_important_note_info',
						'type'        => 'custom',
						'required'    => array(
							array(
								'setting'  => 'layout',
								'operator' => '!=',
								'value'    => 'Boxed',
							),
						),
					),
					'bg_image' => array(
						'label'           => esc_html__( 'Background Image For Outer Areas In Boxed Mode', 'Avada' ),
						'description'     => esc_html__( 'Select an image to use for the outer background area in boxed mode.', 'Avada' ),
						'id'              => 'bg_image',
						'default'         => '',
						'mod'             => '',
						'type'            => 'media',
						'required'        => array(
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
						),
					),
					'bg_full' => array(
						'label'           => esc_html__( '100% Background Image', 'Avada' ),
						'description'     => esc_html__( 'Turn on to have the outer boxed background image display at 100% in width and height according to the window size.', 'Avada' ),
						'id'              => 'bg_full',
						'default'         => '0',
						'type'            => 'switch',
						'required'        => array(
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'  => '',
								),
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'       => '',
									'id'        => '',
									'height'    => '',
									'width'     => '',
									'thumbnail' => '',
								),
							),
						),
					),
					'bg_repeat' => array(
						'label'           => esc_html__( 'Background Repeat', 'Avada' ),
						'description'     => esc_html__( 'Controls how the background image repeats.', 'Avada' ),
						'id'              => 'bg_repeat',
						'default'         => 'no-repeat',
						'type'            => 'select',
						'choices'         => array(
							'repeat'    => esc_html__( 'Repeat All', 'Avada' ),
							'repeat-x'  => esc_html__( 'Repeat Horizontally', 'Avada' ),
							'repeat-y'  => esc_html__( 'Repeat Vertically', 'Avada' ),
							'no-repeat' => esc_html__( 'No Repeat', 'Avada' ),
						),
						'required'    => array(
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => '',
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'  => '',
								),
							),
							array(
								'setting'  => 'bg_image',
								'operator' => '!=',
								'value'    => array(
									'url'       => '',
									'id'        => '',
									'height'    => '',
									'width'     => '',
									'thumbnail' => '',
								),
							),
						),
					),
					'bg_color' => array(
						'label'           => esc_html__( 'Background Color For Outer Areas In Boxed Mode', 'Avada' ),
						'description'     => esc_html__( 'Controls the background color of the outer background area in boxed mode.', 'Avada' ),
						'id'              => 'bg_color',
						'default'         => '#d7d6d6',
						'type'            => 'color-alpha',
						'required'        => array(
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
						),
					),
					'bg_pattern_option' => array(
						'label'           => esc_html__( 'Background Pattern', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display a pattern in the outer background area in boxed mode.', 'Avada' ),
						'id'              => 'bg_pattern_option',
						'default'         => '0',
						'type'            => 'switch',
						'required'        => array(
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
						),
					),
					'bg_pattern' => array(
						'label'           => esc_html__( 'Select a Background Pattern', 'Avada' ),
						'id'              => 'bg_pattern',
						'default'         => 'pattern1',
						'type'            => 'radio-image',
						'choices'         => array(
							'pattern1'  => Avada::$template_dir_url . '/assets/images/patterns/pattern1.png',
							'pattern2'  => Avada::$template_dir_url . '/assets/images/patterns/pattern2.png',
							'pattern3'  => Avada::$template_dir_url . '/assets/images/patterns/pattern3.png',
							'pattern4'  => Avada::$template_dir_url . '/assets/images/patterns/pattern4.png',
							'pattern5'  => Avada::$template_dir_url . '/assets/images/patterns/pattern5.png',
							'pattern6'  => Avada::$template_dir_url . '/assets/images/patterns/pattern6.png',
							'pattern7'  => Avada::$template_dir_url . '/assets/images/patterns/pattern7.png',
							'pattern8'  => Avada::$template_dir_url . '/assets/images/patterns/pattern8.png',
							'pattern9'  => Avada::$template_dir_url . '/assets/images/patterns/pattern9.png',
							'pattern10' => Avada::$template_dir_url . '/assets/images/patterns/pattern10.png',
							'pattern11' => Avada::$template_dir_url . '/assets/images/patterns/pattern11.png',
							'pattern12' => Avada::$template_dir_url . '/assets/images/patterns/pattern12.png',
							'pattern13' => Avada::$template_dir_url . '/assets/images/patterns/pattern13.png',
							'pattern14' => Avada::$template_dir_url . '/assets/images/patterns/pattern14.png',
							'pattern15' => Avada::$template_dir_url . '/assets/images/patterns/pattern15.png',
							'pattern16' => Avada::$template_dir_url . '/assets/images/patterns/pattern16.png',
							'pattern17' => Avada::$template_dir_url . '/assets/images/patterns/pattern17.png',
							'pattern18' => Avada::$template_dir_url . '/assets/images/patterns/pattern18.png',
							'pattern19' => Avada::$template_dir_url . '/assets/images/patterns/pattern19.png',
							'pattern20' => Avada::$template_dir_url . '/assets/images/patterns/pattern20.png',
							'pattern21' => Avada::$template_dir_url . '/assets/images/patterns/pattern21.png',
							'pattern22' => Avada::$template_dir_url . '/assets/images/patterns/pattern22.png',
						),
						'required'    => array(
							array(
								'setting'  => 'bg_pattern_option',
								'operator' => '==',
								'value'    => '1',
							),
							array(
								'setting'  => 'layout',
								'operator' => '==',
								'value'    => 'Boxed',
							),
						),
					),
				),
			),
		),
	);

	return $sections;

}
