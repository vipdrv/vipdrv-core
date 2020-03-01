<?php

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_layer_slider() {
	if ( ! defined( 'LS_PLUGIN_BASE' ) ) {
		return;
	}
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Layer Slider', 'fusion-builder' ),
		'shortcode'  => 'layerslider',
		'icon'       => 'fusiona-stack',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-layer-slider-preview.php',
		'preview_id' => 'fusion-builder-block-module-layer-slider-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Select Slider', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a slider group.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => fusion_builder_get_layerslider_slides(),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_layer_slider' );
