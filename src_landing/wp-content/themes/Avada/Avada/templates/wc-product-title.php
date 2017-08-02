<?php
/**
 * The product title.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

?>
<h3 class="product-title">
	<a href="<?php echo esc_url_raw( get_the_permalink() ); ?>">
		<?php echo get_the_title(); ?>
	</a>
</h3>
<div class="fusion-price-rating">
