<?php
/**
 * This file contains typography styles for The Events Calendar plugin.
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
 * The Events Calendar CSS classes that inherit Avada's body typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_events_calendar_body_typography( $typography_elements ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$typography_elements['size'][]   = '.tribe-events-loop .tribe-events-event-meta';
	}

	return $typography_elements;
}
add_filter( 'avada_body_typography_elements', 'avada_events_calendar_body_typography' );

/**
 * The Events Calendar css classes that inherit Avada's H3 typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_events_calendar_h3_typography( $typography_elements ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .fusion-events-single-title-content .tribe-events-schedule h3';
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .recurringinfo .event-is-recurring';
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .recurringinfo .tribe-events-divider';
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .recurringinfo .tribe-events-cost';
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .tribe-events-divider';
		$typography_elements['size'][]   = '.single-tribe_events .fusion-events-featured-image .tribe-events-cost';
		$typography_elements['family'][] = '.single-tribe_events .fusion-events-featured-image .recurringinfo .tribe-events-divider';
		$typography_elements['family'][] = '.single-tribe_events .fusion-events-featured-image .recurringinfo .tribe-events-cost';
		$typography_elements['family'][] = '.single-tribe_events .fusion-events-featured-image .tribe-events-divider';
		$typography_elements['family'][] = '.single-tribe_events .fusion-events-featured-image .tribe-events-cost';
	}

	return $typography_elements;
}
add_filter( 'avada_h3_typography_elements', 'avada_events_calendar_h3_typography' );

/**
 * The Events Calendar css classes that inherit Avada's H4 typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_events_calendar_h4_typography( $typography_elements ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$typography_elements['size'][]   = '.sidebar .tribe-events-single-section-title';
		$typography_elements['size'][]   = '#tribe_events_filters_wrapper .tribe-events-filters-label';
		$typography_elements['color'][]  = '.sidebar .tribe-events-single-section-title';
		$typography_elements['color'][]  = '#tribe_events_filters_wrapper .tribe-events-filters-label';
		$typography_elements['family'][] = '.sidebar .tribe-events-single-section-title';
		$typography_elements['family'][] = '#tribe_events_filters_wrapper .tribe-events-filters-label';
	}

	return $typography_elements;
}
add_filter( 'avada_h4_typography_elements', 'avada_events_calendar_h4_typography' );


/**
 * The Events Calendar css classes that inherit Avada's button typography settings.
 *
 * @param array $typography_elements An array of all typography elements.
 * @return array
 */
function avada_events_calendar_button_typography( $typography_elements ) {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$typography_elements['family'][] = '#tribe-events .tribe-events-tickets .add-to-cart .tribe-button';
	}

	return $typography_elements;
}
add_filter( 'avada_button_typography_elements', 'avada_events_calendar_button_typography' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
