<?php
/**
 * This file contains typography styles for bbPress plugin.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The bbPress css classes that inherit Avada's H2 typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_bbpress_h2_typography( $typography_elements ) {
	if ( class_exists( 'bbPress' ) ) {
		$typography_elements['size'][]   = '#bbpress-forums #bbp-user-wrapper h2.entry-title';
		$typography_elements['color'][]  = '#bbpress-forums #bbp-user-wrapper h2.entry-title';
		$typography_elements['family'][] = '#bbpress-forums #bbp-user-wrapper h2.entry-title';
	}

	return $typography_elements;
}
add_filter( 'avada_h2_typography_elements', 'avada_bbpress_h2_typography' );

/**
 * The bbPress css classes that inherit Avada's body typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_bbpress_body_typography( $typography_elements ) {
	if ( class_exists( 'bbPress' ) ) {
		$typography_elements['family'][] = '#bbp_user_edit_submit';
	}

	return $typography_elements;
}
add_filter( 'avada_body_typography_elements', 'avada_bbpress_body_typography' );

/**
 * The bbPress css classes that inherit Avada's button typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_bbpress_button_typography( $typography_elements ) {
	if ( class_exists( 'bbPress' ) ) {
		$typography_elements['family'][] = '.bbp-submit-wrapper .button';
		$typography_elements['family'][] = '#bbp_user_edit_submit';
	}

	return $typography_elements;
}
add_filter( 'avada_button_typography_elements', 'avada_bbpress_button_typography' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
