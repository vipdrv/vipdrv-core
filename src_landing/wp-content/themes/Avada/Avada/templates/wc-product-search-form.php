<?php
/**
 * Product Search Form.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

?>

<form role="search" method="get" class="searchform" action="<?php echo esc_url_raw( home_url( '/' ) ); ?>">
	<div>
		<input type="text" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" class="s" placeholder="<?php esc_attr_e( 'Search...', 'Avada' ); ?>" />
		<input type="hidden" name="post_type" value="product" />
	</div>
</form>
