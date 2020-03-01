<?php
/**
 * Adds HTML after the product summary.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

?>
<div class="fusion-clearfix"></div>

<?php $nofollow = ( Avada()->settings->get( 'nofollow_social_links' ) ) ? ' rel="noopener noreferrer nofollow"' : ' rel="noopener noreferrer"'; ?>

<?php if ( Avada()->settings->get( 'woocommerce_social_links' ) ) : ?>

	<?php $facebook_url = 'https://m.facebook.com/sharer.php?u=' . get_permalink(); ?>
	<?php if ( ! avada_jetpack_is_mobile() ) : ?>
		<?php $facebook_url = 'http://www.facebook.com/sharer.php?m2w&s=100&p&#91;url&#93;=' . get_permalink() . '&p&#91;title&#93;=' . wp_strip_all_tags( get_the_title(), true ); ?>
	<?php endif; ?>

	<ul class="social-share clearfix">
		<li class="facebook">
			<a href="<?php echo esc_url_raw( $facebook_url ); ?>" target="_blank"<?php echo $nofollow; // WPCS: XSS ok. ?>>
				<i class="fontawesome-icon medium circle-yes fusion-icon-facebook"></i>
				<div class="fusion-woo-social-share-text">
					<span><?php esc_attr_e( 'Share On Facebook', 'Avada' ); ?></span>
				</div>
			</a>
		</li>
		<li class="twitter">
			<a href="https://twitter.com/share?text=<?php echo wp_strip_all_tags( get_the_title(), true ); // WPCS: XSS ok. ?>&amp;url=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank"<?php echo $nofollow; // WPCS: XSS ok. ?>>
				<i class="fontawesome-icon medium circle-yes fusion-icon-twitter"></i>
				<div class="fusion-woo-social-share-text">
					<span><?php esc_attr_e( 'Tweet This Product', 'Avada' ); ?></span>
				</div>
			</a>
		</li>
		<li class="pinterest">
			<?php $full_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?>
			<a href="http://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_permalink() ); ?>&amp;description=<?php echo rawurlencode( wp_strip_all_tags( get_the_title(), true ) ); ?>&amp;media=<?php echo rawurlencode( $full_image[0] ); ?>" target="_blank"<?php echo $nofollow; // WPCS: XSS ok. ?>>
				<i class="fontawesome-icon medium circle-yes fusion-icon-pinterest"></i>
				<div class="fusion-woo-social-share-text">
					<span><?php esc_attr_e( 'Pin This Product', 'Avada' ); ?></span>
				</div>
			</a>
		</li>
		<li class="email">
			<a href="mailto:?subject=<?php echo rawurlencode( html_entity_decode( wp_strip_all_tags( get_the_title(), true ), ENT_COMPAT, 'UTF-8' ) ); ?>&body=<?php echo esc_url_raw( get_permalink() ); ?>" target="_blank"<?php echo $nofollow; // WPCS: XSS ok. ?>>
				<i class="fontawesome-icon medium circle-yes fusion-icon-mail"></i>
				<div class="fusion-woo-social-share-text">
					<span><?php echo esc_attr_e( 'Mail This Product', 'Avada' ); ?></span>
				</div>
			</a>
		</li>
	</ul>
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
