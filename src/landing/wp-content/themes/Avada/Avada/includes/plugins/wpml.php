<?php
/**
 * WPML mods.
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

add_filter( 'get_next_post_plus_join', 'avada_get_next_post_plus_join' );

add_filter( 'get_next_post_plus_where', 'avada_get_next_post_plus_where' );

add_filter( 'get_previous_post_plus_join', 'avada_get_next_post_plus_join' );

add_filter( 'get_previous_post_plus_where', 'avada_get_next_post_plus_where' );

/**
 * Avada WPML helper function.
 *
 * @param string $join The query to run.
 * @return string
 */
function avada_get_next_post_plus_join( $join ) {
	global $wpdb;
	$join .= "LEFT JOIN {$wpdb->prefix}icl_translations as t on t.element_id = p.ID";
	return $join;
}

/**
 * Avada WPML helper function.
 *
 * @param string $where The query to run.
 * @return string
 */
function avada_get_next_post_plus_where( $where ) {
	global $sitepress;
	$where .= "AND t.language_code = '" . $sitepress->get_current_language() . "'";
	return $where;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
