<?php
/**
 * Footer Metabox options.
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
	'display_footer',
	esc_attr__( 'Display Footer Widget Area', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the footer. %s', 'Avada' ), Avada()->settings->get_default_description( 'footer_widgets', '', 'yesno' ) )
);

$this->radio_buttonset(
	'display_copyright',
	esc_attr__( 'Display Copyright Area', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the copyright area. %s', 'Avada' ), Avada()->settings->get_default_description( 'footer_copyright', '', 'yesno' ) )
);

$this->radio_buttonset(
	'footer_100_width',
	esc_html__( '100% Footer Width', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to set footer width to 100&#37; of the browser width. Select "No" for site width. %s', 'Avada' ), Avada()->settings->get_default_description( 'footer_100_width', '', 'yesno' ) )
);

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
