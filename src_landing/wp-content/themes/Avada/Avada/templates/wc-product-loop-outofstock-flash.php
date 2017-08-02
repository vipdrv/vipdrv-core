<?php
/**
 * Out of stock flash template
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $product;
?>

<?php if ( ! $product->is_in_stock() ) : ?>
	<div class="fusion-out-of-stock">
		<div class="fusion-position-text">
			<?php esc_attr_e( 'Out of stock', 'Avada' ); ?>
		</div>
	</div>
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
