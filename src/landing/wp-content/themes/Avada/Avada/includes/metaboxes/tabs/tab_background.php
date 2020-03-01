<?php
/**
 * Background Metabox options.
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

$this->radio_buttonset(
	'page_bg_layout',
	esc_attr__( 'Layout', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'wide'    => esc_attr__( 'Wide', 'Avada' ),
		'boxed'   => esc_attr__( 'Boxed', 'Avada' ),
	),
	sprintf( esc_attr__( 'Select boxed or wide layout. %s', 'Avada' ), Avada()->settings->get_default_description( 'layout', '', 'select' ) )
);

// Dependency check for boxed mode.
$boxed_dependency = array(
	array(
		'field'      => 'page_bg_layout',
		'value'      => 'wide',
		'comparison' => '!=',
	),
);
if ( 'Wide' == Avada()->settings->get( 'layout' ) ) {
	$boxed_dependency[] = array(
		'field'      => 'page_bg_layout',
		'value'      => 'default',
		'comparison' => '!=',
	);
}

$page_bg_color = Fusion_Color::new_color( array(
	'color' => Avada()->settings->get( 'bg_color' ),
	'fallback' => '#ffffff',
) );
$this->color(
	'page_bg_color',
	esc_attr__( 'Background Color', 'Avada' ),
	sprintf( esc_html__( 'Controls the background color for the outer background. Hex code, ex: #000. %s', 'Avada' ), Avada()->settings->get_default_description( 'bg_color' ) ),
	true,
	$boxed_dependency,
	$page_bg_color->color
);

$this->upload(
	'page_bg',
	esc_attr__( 'Background Image for Outer Area', 'Avada' ),
	sprintf( esc_attr__( 'Select an image to use for the outer background. %s', 'Avada' ), Avada()->settings->get_default_description( 'bg_image', 'thumbnail' ) ),
	$boxed_dependency
);

// Also add check for background image.
$boxed_dependency[] = array(
	'field'      => 'page_bg',
	'value'      => '',
	'comparison' => '!=',
);

$this->radio_buttonset(
	'page_bg_full',
	esc_attr__( '100% Background Image', 'Avada' ),
	array(
		'no'  => esc_attr__( 'No', 'Avada' ),
		'yes' => esc_attr__( 'Yes', 'Avada' ),
	),
	esc_html__( 'Choose to have the background image display at 100%.', 'Avada' ),
	$boxed_dependency
);

$this->select(
	'page_bg_repeat',
	esc_attr__( 'Background Repeat', 'Avada' ),
	array(
		'repeat'    => esc_attr__( 'Tile', 'Avada' ),
		'repeat-x'  => esc_attr__( 'Tile Horizontally', 'Avada' ),
		'repeat-y'  => esc_attr__( 'Tile Vertically', 'Avada' ),
		'no-repeat' => esc_attr__( 'No Repeat', 'Avada' ),
	),
	esc_html__( 'Select how the background image repeats.', 'Avada' ),
	$boxed_dependency
);

// Dependency check for wide mode.
$wide_dependency = array();

$content_bg_color = Fusion_Color::new_color( array(
	'color' => Avada()->settings->get( 'content_bg_color' ),
	'fallback' => '#ffffff',
) );
$this->color(
	'wide_page_bg_color',
	esc_attr__( 'Background Color', 'Avada' ),
	sprintf( esc_html__( 'Controls the background color for the main content area. Hex code, ex: #000. %s', 'Avada' ), Avada()->settings->get_default_description( 'content_bg_color' ) ),
	true,
	$wide_dependency,
	$content_bg_color->color
);

$this->upload(
	'wide_page_bg',
	esc_attr__( 'Background Image for Main Content Area', 'Avada' ),
	sprintf( esc_html__( 'Select an image to use for the main content area. %s', 'Avada' ), Avada()->settings->get_default_description( 'content_bg_image', 'thumbnail' ) ),
	$wide_dependency
);

// Also add check for background image.
$wide_dependency[] = array(
	'field'      => 'wide_page_bg',
	'value'      => '',
	'comparison' => '!=',
);

$this->radio_buttonset(
	'wide_page_bg_full',
	esc_html__( '100% Background Image', 'Avada' ),
	array(
		'no'  => esc_attr__( 'No', 'Avada' ),
		'yes' => esc_attr__( 'Yes', 'Avada' ),
	),
	esc_html__( 'Choose to have the background image display at 100%.', 'Avada' ),
	$wide_dependency
);

$this->select(
	'wide_page_bg_repeat',
	esc_attr__( 'Background Repeat', 'Avada' ),
	array(
		'repeat'    => esc_attr__( 'Tile', 'Avada' ),
		'repeat-x'  => esc_attr__( 'Tile Horizontally', 'Avada' ),
		'repeat-y'  => esc_attr__( 'Tile Vertically', 'Avada' ),
		'no-repeat' => esc_attr__( 'No Repeat', 'Avada' ),
	),
	esc_html__( 'Select how the background image repeats.', 'Avada' ),
	$wide_dependency
);

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
