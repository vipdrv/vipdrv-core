<?php
/**
 * Stock HTML.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $product;

// Availability.
$availability      = $product->get_availability();
$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . $availability['availability'] . '</p>';
?>
<div class="avada-availability">
	<?php echo apply_filters( 'woocommerce_get_stock_html', $availability_html, $product ); // WPCS: XSS ok. ?>
</div>
