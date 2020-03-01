<?php

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_builder_element_next_page() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Next Page', 'fusion-builder' ),
		'shortcode'         => 'fusion_builder_next_page',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => '',
				'description' => '',
				'param_name'  => 'next_page_content',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_builder_element_next_page' );
