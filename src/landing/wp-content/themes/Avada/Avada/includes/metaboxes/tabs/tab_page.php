<?php
/**
 * Page Metabox options.
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

$this->dimension(
	array(
		'main_top_padding',
		'main_bottom_padding',
	),
	esc_attr__( 'Page Content Padding', 'Avada' ),
	sprintf( esc_html__( 'In pixels ex: 20px. %s', 'Avada' ), Avada()->settings->get_default_description( 'main_padding', array( 'top', 'bottom' ) ) )
);

$this->text(
	'hundredp_padding',
	esc_html__( '100% Width Padding', 'Avada' ),
	sprintf( esc_html__( 'Controls the left and right padding for page content when using 100&#37; site width, 100&#37; width page template or 100&#37; width post option. This does not affect Fusion Builder containers.  Enter value including any valid CSS unit, ex: 30px. %s', 'Avada' ), Avada()->settings->get_default_description( 'hundredp_padding' ) )
);

$screen = get_current_screen();

if ( 'page' === $screen->post_type ) {
	$this->radio_buttonset(
		'show_first_featured_image',
		esc_attr__( 'Disable First Featured Image', 'Avada' ),
		array(
			'no'  => esc_attr__( 'No', 'Avada' ),
			'yes' => esc_attr__( 'Yes', 'Avada' ),
		),
		esc_html__( 'Disable the 1st featured image on page.', 'Avada' )
	);
}

if ( 'tribe_events' === $screen->post_type ) {
	$this->radio_buttonset(
		'share_box',
		esc_attr__( 'Show Social Share Box', 'Avada' ),
		array(
			'default' => esc_attr__( 'Default', 'Avada' ),
			'yes'     => esc_attr__( 'Show', 'Avada' ),
			'no'      => esc_attr__( 'Hide', 'Avada' ),
		),
		sprintf( esc_html__( 'Choose to show or hide the social share box. %s', 'Avada' ), Avada()->settings->get_default_description( 'events_social_sharing_box' , '', 'showhide' ) )
	);
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
