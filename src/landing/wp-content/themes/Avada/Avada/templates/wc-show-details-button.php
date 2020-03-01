<?php
/**
 * Show details button.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $product;
$add_styles = (bool) ( ( ! $product->is_purchasable() || ! $product->is_in_stock() ) && ! $product->is_type( 'external' ) );
?>
<a href="<?php echo esc_url_raw( get_permalink() ); ?>" class="show_details_button"<?php echo ( $add_styles ) ? ' style="float:none;max-width:none;text-align:center;"' : ''; ?>>
	<?php esc_attr_e( 'Details', 'Avada' ); ?>
</a>
