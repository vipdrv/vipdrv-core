<?php
/**
 * WooCommerce compatibility functions.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.0.0
 */

if ( ! function_exists( 'fusion_wc_get_page_id' ) ) {
	/**
	 * The woocommerce_get_page_id function was deprecated in WooCommerce 2.7.
	 * This is a proxy function to ensure Avada works with all WC versions.
	 *
	 * @param string $page The page we want to find.
	 * @return int         The page ID.
	 */
	function fusion_wc_get_page_id( $page ) {
		if ( function_exists( 'wc_get_page_id' ) ) {
			return wc_get_page_id( $page );
		} elseif ( function_exists( 'woocommerce_get_page_id' ) ) {
			return woocommerce_get_page_id( $page );
		}
	}
}

if ( ! function_exists( 'fusion_wc_get_template' ) ) {
	/**
	 * The woocommerce_get_template function was deprecated in WooCommerce 2.7.
	 * This is a proxy function to ensure Avada works with all WC versions.
	 *
	 * @param mixed  $slug The template slug.
	 * @param string $name (default: '').
	 */
	function fusion_wc_get_template( $slug, $name = '' ) {
		if ( function_exists( 'wc_get_template' ) ) {
			wc_get_template( $slug, $name );
		} elseif ( function_exists( 'woocommerce_get_template' ) ) {
			woocommerce_get_template( $slug, $name );
		}
	}
}

if ( ! function_exists( 'fusion_wc_get_template_part' ) ) {
	/**
	 * The woocommerce_get_template_part function was deprecated in WooCommerce 2.7.
	 * This is a proxy function to ensure Avada works with all WC versions.
	 *
	 * @param mixed  $slug The template slug.
	 * @param string $name (default: '').
	 */
	function fusion_wc_get_template_part( $slug, $name = '' ) {
		if ( function_exists( 'wc_get_template_part' ) ) {
			wc_get_template_part( $slug, $name );
		} elseif ( function_exists( 'woocommerce_get_template_part' ) ) {
			woocommerce_get_template_part( $slug, $name );
		}
	}
}

if ( ! function_exists( 'fusion_get_product' ) ) {
	/**
	 * The get_product function was deprecated in WooCommerce 2.7.
	 * This is a proxy function to ensure Avada works with all WC versions.
	 *
	 * @param mixed $the_product Post object or post ID of the product.
	 * @param array $args        Previously used to pass arguments to the factory, e.g. to force a type.
	 * @return WC_Product|null
	 */
	function fusion_get_product( $the_product = false, $args = array() ) {
		if ( function_exists( 'wc_get_product' ) ) {
			return wc_get_product( $the_product, $args );
		} elseif ( function_exists( 'get_product' ) ) {
			return get_product( $the_product, $args );
		}
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
