<?php

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_builder_element_blank_page() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Blank Page', 'fusion-builder' ),
		'shortcode'         => 'fusion_builder_blank_page',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => '',
				'description' => '',
				'param_name'  => 'blank_page_content',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_builder_element_blank_page' );
