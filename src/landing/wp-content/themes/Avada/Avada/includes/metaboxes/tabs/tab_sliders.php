<?php
/**
 * Sliders Metabox options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

$this->select(
	'slider_type',
	esc_attr__( 'Slider Type', 'Avada' ),
	array(
		'no'      => esc_attr__( 'No Slider', 'Avada' ),
		'layer'   => 'LayerSlider',
		'flex'    => 'Fusion Slider',
		'rev'     => 'Slider Revolution',
		'elastic' => 'Elastic Slider',
	),
	esc_html__( 'Select the type of slider that displays.', 'Avada' )
);

global $wpdb;
$slides_array[0] = esc_html__( 'Select a slider', 'Avada' );
if ( class_exists( 'LS_Sliders' ) ) {

	// Table name.
	$table_name = $wpdb->prefix . 'layerslider';

	// Get sliders.
	$sliders = $wpdb->get_results( "SELECT * FROM $table_name WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY date_c ASC" );

	if ( ! empty( $sliders ) ) {
		foreach ( $sliders as $key => $item ) {
			$slides[ $item->id ] = $item->name . ' (#' . $item->id . ')';
		}
	}

	if ( isset( $slides ) && ! empty( $slides ) ) {
		foreach ( $slides as $key => $val ) {
			$slides_array[ $key ] = $val;
		}
	}
}

$this->select(
	'slider',
	esc_attr__( 'Select LayerSlider', 'Avada' ),
	$slides_array,
	esc_html__( 'Select the unique name of the slider.', 'Avada' ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'layer',
			'comparison' => '==',
		),
	)
);

if ( method_exists( 'FusionCore_Plugin', 'get_fusion_sliders' ) ) {
	$slides_array = FusionCore_Plugin::get_fusion_sliders();

	$this->select(
		'wooslider',
		esc_attr__( 'Select Fusion Slider', 'Avada' ) ,
		$slides_array,
		esc_html__( 'Select the unique name of the slider.', 'Avada' ),
		array(
			array(
				'field'      => 'slider_type',
				'value'      => 'flex',
				'comparison' => '==',
			),
		)
	);
}

global $wpdb;
$revsliders[0] = esc_attr__( 'Select a slider', 'Avada' );

if ( function_exists( 'rev_slider_shortcode' ) ) {
	$slider_object = new RevSliderSlider();
	$sliders_array = $slider_object->getArrSliders();

	if ( $sliders_array ) {
		foreach ( $sliders_array as $slider ) {
			$revsliders[ $slider->getAlias() ] = $slider->getTitle() . ' (#' . $slider->getID() . ')';
		}
	}
}

$this->select(
	'revslider',
	esc_attr__( 'Select Slider Revolution Slider', 'Avada' ),
	$revsliders,
	esc_html__( 'Select the unique name of the slider.', 'Avada' ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'rev',
			'comparison' => '==',
		),
	)
);

$slides_array    = array();
$slides_array[0] = esc_html__( 'Select a slider', 'Avada' );
$slides          = get_terms( 'themefusion_es_groups' );
if ( $slides && ! isset( $slides->errors ) ) {
	$slides = maybe_unserialize( $slides );
	foreach ( $slides as $key => $val ) {
		$slides_array[ $val->slug ] = $val->name . ' (#' . $val->term_id . ')';
	}
}
$this->select(
	'elasticslider',
	esc_attr__( 'Select Elastic Slider', 'Avada' ),
	$slides_array,
	esc_html__( 'Select the unique name of the slider.', 'Avada' ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'elastic',
			'comparison' => '==',
		),
	)
);

$this->radio_buttonset(
	'slider_position',
	esc_attr__( 'Slider Position', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'below'   => esc_attr__( 'Below', 'Avada' ),
		'above'   => esc_attr__( 'Above', 'Avada' ),
	),
	sprintf( esc_html__( 'Select if the slider shows below or above the header. Only works for top header position. %s', 'Avada' ), Avada()->settings->get_default_description( 'slider_position', '', 'select' ) ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'no',
			'comparison' => '!=',
		),
	)
);

$this->radio_buttonset(
	'avada_rev_styles',
	esc_attr__( 'Disable Avada Styles For Slider Revolution', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to enable or disable Avada styles for Slider Revolution. %s', 'Avada' ), Avada()->settings->get_default_description( 'avada_rev_styles', '', 'reverseyesno' ) ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'rev',
			'comparison' => '==',
		),
	)
);

$this->upload(
	'fallback',
	esc_attr__( 'Slider Fallback Image', 'Avada' ),
	esc_html__( 'This image will override the slider on mobile devices.', 'Avada' ),
	array(
		array(
			'field'      => 'slider_type',
			'value'      => 'no',
			'comparison' => '!=',
		),
		array(
			'field'      => 'slider_type',
			'value'      => '',
			'comparison' => '!=',
		),
	)
);

// New hidden field for demo slider contents, to allow demo slider placeholder.
$this->hidden(
	'demo_slider',
	''
);
/* Omit closing PHP tag to avoid "Headers already sent" issues. */
