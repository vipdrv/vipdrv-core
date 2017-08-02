<?php
/**
 * Handles sidebars.
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
 * Handle sidebars.
 */
class Avada_Sidebars {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * Register our sidebars.
	 */
	public function widgets_init() {

		// Main Blog widget area.
		register_sidebar( array(
			'name'          => 'Blog Sidebar',
			'id'            => 'avada-blog-sidebar',
			'description'   => __( 'Default Sidebar of Avada', 'Avada' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<div class="heading"><h4 class="widget-title">',
			'after_title'   => '</h4></div>',
		) );

		// Footer widget areas.
		$columns = (int) Avada()->settings->get( 'footer_widgets_columns' ) + 1;

		if ( ! $columns || 1 === $columns ) {
			$columns = 5;
		}

		// Register he footer widgets.
		for ( $i = 1; $i < $columns; $i++ ) {

			register_sidebar( array(
				'name'          => sprintf( 'Footer Widget %s', $i ),
				'id'            => 'avada-footer-widget-' . $i,
				'before_widget' => '<section id="%1$s" class="fusion-footer-widget-column widget %2$s">',
				'after_widget'  => '<div style="clear:both;"></div></section>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			) );

		}

		// Sliding bar widget areas.
		$columns = (int) Avada()->settings->get( 'slidingbar_widgets_columns' ) + 1;

		if ( ! $columns || 1 === $columns ) {
			$columns = 5;
		}

		// Register the slidingbar widgets.
		for ( $i = 1; $i < $columns; $i++ ) {

			register_sidebar( array(
				'name'          => sprintf( 'Sliding Bar Widget %s', $i ),
				'id'            => 'avada-slidingbar-widget-' . $i,
				'before_widget' => '<section id="%1$s" class="fusion-slidingbar-widget-column widget %2$s">',
				'after_widget'  => '<div style="clear:both;"></div></section>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			) );

		}
	}

}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
