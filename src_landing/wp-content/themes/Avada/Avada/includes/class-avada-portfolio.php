<?php
/**
 * Portfolios main class.
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

/**
 * Portfolio tweaks for Avada.
 */
class Avada_Portfolio {

	/**
	 * The class constructor
	 */
	public function __construct() {
		add_filter( 'fusion_content_class', array( $this, 'set_portfolio_single_width' ) );
		add_filter( 'pre_get_posts', array( $this, 'set_post_filters' ) );
	}

	/**
	 * Modify the query (using the 'pre_get_posts' filter)
	 *
	 * @param  object $query The WP Query object.
	 * @return  object
	 */
	public function set_post_filters( $query ) {

		if ( ! is_admin() && $query->is_main_query() && ( $query->is_post_type_archive( 'avada_portfolio' ) || $query->is_tax( array( 'portfolio_category', 'portfolio_skills', 'portfolio_tags' ) ) ) ) {
			// If TO setting is set to 0, all items should show.
			$number_of_portfolio_items = Avada()->settings->get( 'portfolio_archive_items' );
			$query->set( 'posts_per_page', $number_of_portfolio_items );
		}

		return $query;

	}

	/**
	 * Set portfolio width and assign a class to the content div
	 *
	 * @param  array $classes The CSS classes.
	 * @return  array
	 */
	public function set_portfolio_single_width( $classes ) {
		if ( is_singular( 'avada_portfolio' ) ) {
			$portfolio_width = ( 'half' == fusion_get_option( 'portfolio_featured_image_width', 'width', Avada()->fusion_library->get_page_id() ) ) ? 'half' : 'full';
			if ( ! Avada()->settings->get( 'portfolio_featured_images' ) && 'half' == $portfolio_width ) {
				$portfolio_width = 'full';
			}

			$classes[] = 'portfolio-' . $portfolio_width;
		}

		return $classes;
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
