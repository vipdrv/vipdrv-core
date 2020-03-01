<?php
/**
 * Override core-WooCommerce functions.
 *
 * @author     ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Display cross-sell template.
 *
 * @param int    $posts_per_page Number of posts in the query.
 * @param int    $columns        Number of culumns.
 * @param string $orderby        Determines how the query will order the posts.
 * @param string $order          Determines how the query will order the posts.
 */
function woocommerce_cross_sell_display( $posts_per_page = 3, $columns = 3, $orderby = 'rand', $order = 'desc' ) {

	global $woocommerce_loop;

	$attributes = array(
		'posts_per_page' => $posts_per_page,
		'orderby'        => $orderby,
		'columns'        => $columns,
	);

	if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
		// Get visble cross sells then sort them at random.
		$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );

		// Handle orderby and limit results.
		$orderby        = apply_filters( 'woocommerce_cross_sells_orderby', $orderby );
		$cross_sells    = wc_products_array_orderby( $cross_sells, $orderby, $order );
		$posts_per_page = apply_filters( 'woocommerce_cross_sells_total', $posts_per_page );
		$cross_sells    = $posts_per_page > 0 ? array_slice( $cross_sells, 0, $posts_per_page ) : $cross_sells;

		$attributes['cross_sells'] = $cross_sells;
		$woocommerce_loop['columns'] = $columns;
	}

	wc_get_template( 'cart/cross-sells.php', $attributes );
}

/**
 * Gets the shipping calculator template.
 */
function woocommerce_shipping_calculator() {
	if ( ! is_cart() ) {
		wc_get_template( 'cart/shipping-calculator.php' );
	}
}

/**
 * Output the WooCommerce Breadcrumb.
 *
 * @since 5.2.1
 * @param array $args The arguments for WooCommerce Breadcrumbs.
 * @return void
 */
function woocommerce_breadcrumb( $args = array() ) {
	$args = wp_parse_args( $args, apply_filters( 'woocommerce_breadcrumb_defaults', array(
		'delimiter'   => '&nbsp;&#47;&nbsp;',
		'wrap_before' => '<nav class="woocommerce-breadcrumb">',
		'wrap_after'  => '</nav>',
		'before'      => '',
		'after'       => '',
		'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
	) ) );

	$breadcrumbs = new WC_Breadcrumb();

	if ( ! empty( $args['home'] ) ) {
		$breadcrumbs->add_crumb( $args['home'], apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) );
	}

	$args['breadcrumb'] = $breadcrumbs->generate();

	/* @hooked WC_Structured_Data::generate_breadcrumblist_data() - 10 */
	do_action( 'woocommerce_breadcrumb', $breadcrumbs, $args );
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
