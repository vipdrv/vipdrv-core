<?php
/**
 * Header Metabox options.
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
	'display_header',
	esc_attr__( 'Display Header', 'Avada' ),
	array(
		'yes' => esc_attr__( 'Yes', 'Avada' ),
		'no'  => esc_attr__( 'No', 'Avada' ),
	),
	esc_html__( 'Choose to show or hide the header.', 'Avada' )
);

$this->radio_buttonset(
	'header_100_width',
	esc_html__( '100% Header Width', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to set header width to 100&#37; of the browser width. Select "No" for site width. %s', 'Avada' ), Avada()->settings->get_default_description( 'header_100_width', '', 'yesno' ) ),
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
	)
);

$header_bg_color = Fusion_Color::new_color( array(
	'color' => Avada()->settings->get( 'header_bg_color' ),
	'fallback' => '#ffffff',
) );
$this->color(
	'header_bg_color',
	esc_html__( 'Background Color', 'Avada' ),
	sprintf( esc_html__( 'Controls the background color for the header. Hex code or rgba value, ex: #000. %s', 'Avada' ), Avada()->settings->get_default_description( 'header_bg_color' ) ),
	false,
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
	),
	$header_bg_color->color
);

$this->range(
	'header_bg_opacity',
	esc_attr__( 'Background Opacity', 'Avada' ),
	sprintf( esc_html__( 'Controls the opacity of the header background color. Ranges between 0 (transparent) and 1 (opaque). For top headers opacity set below 1 will remove the header height completely. For side headers opacity set below 1 will display a color overlay. %s', 'Avada' ), Avada()->settings->get_default_description( 'header_bg_opacity' ) ),
	'0',
	'1',
	'0.01',
	$header_bg_color->alpha,
	'',
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
	)
);

$this->upload(
	'header_bg',
	esc_attr__( 'Background Image', 'Avada' ),
	sprintf( esc_html__( 'Select an image for the header background. If left empty, the header background color will be used. For top headers the image displays on top of the header background color and will only display if header opacity is set to 1. For side headers the image displays behind the header background color so the header opacity must be set below 1 to see the image. %s', 'Avada' ), Avada()->settings->get_default_description( 'header_bg_image', 'thumbnail' ) ),
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
	)
);

$this->radio_buttonset(
	'header_bg_full',
	esc_html__( '100% Background Image', 'Avada' ),
	array(
		'no'  => esc_attr__( 'No', 'Avada' ),
		'yes' => esc_attr__( 'Yes', 'Avada' ),
	),
	esc_html__( 'Choose to have the background image display at 100%.', 'Avada' ),
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
		array(
			'field'      => 'header_bg',
			'value'      => '',
			'comparison' => '!=',
		),
	)
);

$this->select(
	'header_bg_repeat',
	esc_attr__( 'Background Repeat', 'Avada' ),
	array(
		'repeat'    => esc_attr__( 'Tile', 'Avada' ),
		'repeat-x'  => esc_attr__( 'Tile Horizontally', 'Avada' ),
		'repeat-y'  => esc_attr__( 'Tile Vertically', 'Avada' ),
		'no-repeat' => esc_attr__( 'No Repeat', 'Avada' ),
	),
	esc_html__( 'Select how the background image repeats.', 'Avada' ),
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
		array(
			'field'      => 'header_bg',
			'value'      => '',
			'comparison' => '!=',
		),
	)
);

$menus = get_terms( 'nav_menu', array(
	'hide_empty' => false,
) );
$menu_select['default'] = 'Default Menu';

foreach ( $menus as $menu ) {
	$menu_select[ $menu->term_id ] = $menu->name;
}

$this->select(
	'displayed_menu',
	esc_attr__( 'Main Navigation Menu', 'Avada' ),
	$menu_select,
	sprintf( esc_html__( 'Select which menu displays on this page. %s', 'Avada' ), Avada()->settings->get_default_description( 'main_navigation', '', 'menu' ) ),
	array(
		array(
			'field'      => 'display_header',
			'value'      => 'yes',
			'comparison' => '==',
		),
	)
);

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
