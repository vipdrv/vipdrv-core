<?php
/**
 * This file contains typography styles for GFForms plugin.
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
 * GFForms css classes that inherit Avada's body typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_gform_body_typography( $typography_elements ) {
	if ( class_exists( 'GFForms' ) ) {
		$typography_elements['family'][] = '.gform_wrapper .gform_button';
		$typography_elements['family'][] = '.gform_wrapper .button';
		$typography_elements['family'][] = '.gform_page_footer input[type="button"]';
		$typography_elements['family'][] = '.gform_wrapper label';
		$typography_elements['family'][] = '.gform_wrapper .gfield_description';
		$typography_elements['size'][]   = '.gform_wrapper label';
		$typography_elements['size'][]   = '.gform_wrapper .gfield_description';
	}

	return $typography_elements;
}
add_filter( 'avada_body_typography_elements', 'avada_gform_body_typography' );

/**
 * GFForms css classes that inherit Avada's button typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_gform_button_typography( $typography_elements ) {
	if ( class_exists( 'GFForms' ) ) {
		$typography_elements['family'][] = '.gform_wrapper .gform_button';
		$typography_elements['family'][] = '.gform_wrapper .button';
		$typography_elements['family'][] = '.gform_page_footer input[type="button"]';
	}

	return $typography_elements;
}
add_filter( 'avada_button_typography_elements', 'avada_gform_button_typography' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
