<?php
/**
 * This file contains typography styles for WPCF plugin.
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
 * WPCF7 css classes that inherit Avada's body typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_wpcf7_body_typography( $typography_elements ) {
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$typography_elements['family'][] = '.wpcf7-form input[type="submit"]';
	}

	return $typography_elements;
}
add_filter( 'avada_body_typography_elements', 'avada_wpcf7_body_typography' );

/**
 * WPCF7 css classes that inherit Avada's button typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_wpcf7_button_typography( $typography_elements ) {
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$typography_elements['family'][] = '.wpcf7-form input[type="submit"]';
	}

	return $typography_elements;
}
add_filter( 'avada_button_typography_elements', 'avada_wpcf7_button_typography' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
