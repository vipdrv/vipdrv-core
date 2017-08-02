<?php

/**
 * Row shortcode.
 *
 * @param array  $atts    The attributes array.
 * @param string $content The content.
 * @return string
 */
function fusion_builder_row_inner( $atts, $content = '' ) {
	extract( shortcode_atts( array(
			'id'    => '',
			'class' => '',
		), $atts
	) );

	$id      = ( '' !== $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
	$class_2 = ( '' !== $class ) ? ' ' . esc_attr( $class ) : '';

	return '<div' . $id . ' class="fusion-builder-row fusion-builder-row-inner fusion-row ' . esc_attr( $class ) . $class_2 . '">' . do_shortcode( fusion_builder_fix_shortcodes( $content ) ) . '</div>';
}
add_shortcode( 'fusion_builder_row_inner', 'fusion_builder_row_inner' );


/**
 * Map Row shortcode to Fusion Builder
 */
function fusion_element_row_inner() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Nested Columns', 'fusion-builder' ),
		'shortcode'         => 'fusion_builder_row_inner',
		'hide_from_builder' => true,
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_row_inner' );
