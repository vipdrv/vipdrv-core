<?php
/**
 * Mobile modern menu template.
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
$c_page_id = Avada()->fusion_library->get_page_id();
$displayed_menu = get_post_meta( $c_page_id, 'pyre_displayed_menu', true );
?>
<?php if ( 'modern' === Avada()->settings->get( 'mobile_menu_design' ) && ( has_nav_menu( 'main_navigation' ) || ( $displayed_menu && '' !== $displayed_menu && 'default' !== $displayed_menu ) ) ) : ?>
	<?php $header_content_3 = Avada()->settings->get( 'header_v4_content' ); ?>
	<div class="fusion-mobile-menu-icons">
		<?php // Make sure mobile menu toggle is not loaded when ubermenu is used. ?>
		<?php if ( ! function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) || ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ! ubermenu_get_menu_instance_by_theme_location( 'main_navigation' ) ) ) : ?>
			<a href="#" class="fusion-icon fusion-icon-bars" aria-label="<?php esc_attr_e( 'Toggle mobile menu', 'Avada' ); ?>"></a>
		<?php endif; ?>

		<?php if ( ( 'v4' == Avada()->settings->get( 'header_layout' ) || 'Top' != Avada()->settings->get( 'header_position' ) )  && ( 'Tagline And Search' == $header_content_3 || 'Search' == $header_content_3 ) ) : ?>
			<a href="#" class="fusion-icon fusion-icon-search" aria-label="<?php esc_attr_e( 'Toggle mobile search', 'Avada' ); ?>"></a>
		<?php endif; ?>

		<?php if ( class_exists( 'WooCommerce' ) && Avada()->settings->get( 'woocommerce_cart_link_main_nav' ) ) : ?>
			<a href="<?php echo esc_url_raw( get_permalink( get_option( 'woocommerce_cart_page_id' ) ) ); ?>" class="fusion-icon fusion-icon-shopping-cart"  aria-label="<?php esc_attr_e( 'Toggle mobile cart', 'Avada' ); ?>"></a>
		<?php endif; ?>
	</div>
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
