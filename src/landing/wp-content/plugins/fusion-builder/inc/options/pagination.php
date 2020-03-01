<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Pagination settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_pagination( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['pagination'] = array(
		'label'       => esc_html__( 'Pagination', 'fusion-builder' ),
		'id'          => 'pagination',
		'is_panel'    => true,
		'priority'    => 27,
		'icon'        => ' el-icon-link',
		'fields'      => array(
			'pagination_important_note_info' => array(
				'label'       => '',
				'description' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> The options on this tab apply to all pagination throughout the site, including the 3rd party plugins that Avada has design integration with.', 'fusion-builder' ) . '</div>',
				'id'          => 'pagination_important_note_info',
				'type'        => 'custom',
			),
			'pagination_box_padding' => array(
				'label'       => esc_html__( 'Pagination Box Padding', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the padding inside the pagination box.', 'fusion-builder' ),
				'id'          => 'pagination_box_padding',
				'units'		  => false,
				'default'     => array(
					'width'   => '6px',
					'height'  => '2px',
				),
				'type'        => 'dimensions',
			),
			'pagination_text_display' => array(
				'label'       => esc_html__( 'Pagination Text Display', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the "Previous/Next" text.', 'fusion-builder' ),
				'id'          => 'pagination_text_display',
				'default'     => '1',
				'type'        => 'switch',
			),
			'pagination_font_size' => array(
				'label'       => esc_html__( 'Pagination Font Size', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the size of the pagination text.', 'fusion-builder' ),
				'id'          => 'pagination_font_size',
				'default'     => '12px',
				'type'        => 'dimension',
				'required'        => array(
					array(
						'setting'  => 'pagination_text_display',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		),
	);

	return $sections;

}
