<?php
/**
 * Before shop item buttons.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $post;
?>

<?php if ( isset( $_SERVER['QUERY_STRING'] ) ) : ?>
	<?php parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params ); ?>
	<?php if ( isset( $params['product_view'] ) ) : ?>
		<?php $product_view = $params['product_view']; ?>
		<?php if ( 'list' == $product_view ) : ?>
			<div class="product-excerpt product-<?php echo esc_attr( $product_view ); ?>">
				<div class="product-excerpt-container">
					<div class="post-content">
						<?php echo do_shortcode( $post->post_excerpt ); ?>
					</div>
				</div>
				<div class="product-buttons">
					<div class="product-buttons-container clearfix"> </div>
		<?php else : ?>
			<div class="product-buttons">
				<div class="product-buttons-container clearfix">
		<?php endif; ?>
	<?php else : ?>
		<div class="product-buttons">
			<div class="product-buttons-container clearfix">
	<?php endif; ?>
<?php else : ?>
	<div class="product-buttons">
		<div class="product-buttons-container clearfix">
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
