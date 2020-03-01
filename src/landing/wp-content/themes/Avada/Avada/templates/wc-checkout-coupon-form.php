<?php
/**
 * WooCommerce Checkout Coupons Form.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $woocommerce;

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}
?>

<form class="woocommerce-content-box full-width checkout_coupon" method="post">

	<h2 class="promo-code-heading fusion-alignleft"><?php esc_attr_e( 'Have A Promotional Code?', 'Avada' ); ?></h2>

	<div class="coupon-contents fusion-alignright">
		<div class="form-row form-row-first fusion-alignleft coupon-input">
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value=""/>
		</div>

		<div class="form-row form-row-last fusion-alignleft coupon-button">
			<input type="submit" class="fusion-button button-default fusion-button-default-size button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"/>
		</div>

		<div class="clear"></div>
	</div>
</form>
