<?php
/**
 * WooCommerce thumbnail template (clean mode).
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $product, $woocommerce;

$items_in_cart = array();

if ( $woocommerce->cart && $woocommerce->cart->get_cart() && is_array( $woocommerce->cart->get_cart() ) ) {
	foreach ( $woocommerce->cart->get_cart() as $cart ) {
		$items_in_cart[] = $cart['product_id'];
	}
}

$id             = get_the_ID();
$in_cart        = in_array( $id, $items_in_cart );
$size           = 'shop_catalog';
$post_permalink = get_permalink();

?>
<div class="fusion-clean-product-image-wrapper <?php echo ( $in_cart ) ? 'fusion-item-in-cart' : ''; ?>">
	<?php echo fusion_render_first_featured_image_markup( $id, $size, $post_permalink, true, false, true, 'disable', 'disable', '', '', 'yes', true ); // WPCS: XSS ok. ?>
</div>
