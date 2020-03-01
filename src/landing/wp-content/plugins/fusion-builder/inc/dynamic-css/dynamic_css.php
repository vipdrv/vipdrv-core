<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
/**
 * Format of the $css array:
 * $css['media-query']['element']['property'] = value
 *
 * If no media query is required then set it to 'global'
 *
 * If we want to add multiple values for the same property then we have to make it an array like this:
 * $css[media-query][element]['property'][] = value1
 * $css[media-query][element]['property'][] = value2
 *
 * Multiple values defined as an array above will be parsed separately.
 *
 * @param array $css The original CSS.
 */
function fusion_builder_dynamic_css_array( $css ) {

	global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query, $small_media_query, $medium_media_query, $large_media_query, $six_columns_media_query, $five_columns_media_query, $four_columns_media_query, $three_columns_media_query, $two_columns_media_query, $one_column_media_query;

	$css[ $small_media_query ]['.fusion-no-small-visibility']['display'] = 'none !important';
	$css[ $medium_media_query ]['.fusion-no-medium-visibility']['display'] = 'none !important';
	$css[ $large_media_query ]['.fusion-no-large-visibility']['display'] = 'none !important';

	return apply_filters( 'fusion_builder_dynamic_css_array', $css );

}
add_filter( 'fusion_dynamic_css_array', 'fusion_builder_dynamic_css_array' );
