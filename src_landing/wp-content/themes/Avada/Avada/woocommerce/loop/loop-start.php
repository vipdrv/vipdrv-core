<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop;

// Reset according to sidebar or fullwidth pages.
if ( empty( $woocommerce_loop['columns'] ) ) {
	if ( is_shop() || is_product_category() || is_product_tag() || is_tax() ) {

		if ( is_shop() ) {
			$woocommerce_loop['columns'] = Avada()->settings->get( 'woocommerce_shop_page_columns' );
		}
		if ( is_product_category() ||
			is_product_tag() ||
			is_tax()
		) {
			$woocommerce_loop['columns'] = Avada()->settings->get( 'woocommerce_archive_page_columns' );
			$columns = Avada()->settings->get( 'woocommerce_archive_page_columns' );
		}
	}
}

$column_class = '';
if ( isset( $woocommerce_loop['columns'] ) ) {
	$column_class = ' products-' . $woocommerce_loop['columns'];
}
?>
<ul class="products clearfix<?php echo $column_class; ?>">
