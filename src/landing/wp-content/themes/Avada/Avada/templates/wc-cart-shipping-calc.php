<?php
/**
 * WooCommerce Cart-Shipping calculation template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $woocommerce;

if ( get_option( 'woocommerce_enable_shipping_calc' ) === 'no' || ! WC()->cart->needs_shipping() ) {
	return;
}
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="woocommerce-shipping-calculator" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

	<h2><span href="#" class="shipping-calculator-button"><?php esc_attr_e( 'Calculate shipping', 'woocommerce' ); ?></span>
	</h2>

	<div class="avada-shipping-calculator-form">

		<p class="form-row form-row-wide">
			<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
				<option value=""><?php esc_html_e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
				<?php
				foreach ( WC()->countries->get_shipping_countries() as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
				}
				?>
			</select>
		</p>

		<div class="<?php if ( Avada()->settings->get( 'avada_styles_dropdowns' ) ) : ?>avada-select-parent fusion-layout-column fusion-one-half fusion-spacing-yes<?php endif; ?>">
			<?php
			$current_cc = WC()->customer->get_shipping_country();
			$current_r  = WC()->customer->get_shipping_state();
			$states     = WC()->countries->get_states( $current_cc );
			?>

			<?php if ( is_array( $states ) && empty( $states ) ) : // Hidden Input. ?>

				<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / county', 'woocommerce' ); ?>" />

			<?php elseif ( is_array( $states ) ) : // Dropdown Input. ?>

				<span>
					<select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / county', 'woocommerce' ); ?>">
						<option value=""><?php esc_html_e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
						<?php
						foreach ( $states as $ckey => $cvalue ) {
							echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
						}
						?>
					</select>
				</span>

			<?php else : // Standard Input. ?>

				<input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php esc_attr_e( 'State / county', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" />

			<?php endif; ?>
		</div>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

			<p class="form-row form-row-wide">
				<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php esc_attr_e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
			</p>

		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

			<div class="form-row form-row-wide fusion-layout-column fusion-one-half fusion-spacing-yes fusion-column-last">
				<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php esc_attr_e( 'Postcode / ZIP', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
			</div>

		<?php endif; ?>

		<p>
			<button type="submit" name="calc_shipping" value="1" class="fusion-button button-default fusion-button-default-size button"><?php esc_attr_e( 'Update totals', 'woocommerce' ); ?></button>
		</p>

		<?php wp_nonce_field( 'woocommerce-cart' ); ?>
	</div>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
