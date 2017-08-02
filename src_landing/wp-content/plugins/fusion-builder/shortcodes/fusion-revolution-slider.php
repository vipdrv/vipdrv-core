<?php

/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_revolution_slider() {
	if ( ! defined( 'RS_PLUGIN_PATH' ) ) {
		return;
	}
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Revolution Slider', 'fusion-builder' ),
		'shortcode'  => 'rev_slider',
		'icon'       => 'fusiona-air',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-revolution-slider-preview.php',
		'preview_id' => 'fusion-builder-block-module-revolution-slider-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Select Slider', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a slider group.', 'fusion-builder' ),
				'param_name'  => 'alias',
				'value'       => fusion_builder_get_revslider_slides(),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_revolution_slider' );
