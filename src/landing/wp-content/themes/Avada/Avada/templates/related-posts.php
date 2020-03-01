<?php
/**
 * Related-posts template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<section class="related-posts single-related-posts">
	<?php Avada()->template->title_template( $main_heading, '3' ); ?>

	<?php
	/**
	 * Get the correct image size.
	 */
	$featured_image_size = ( 'cropped' === Avada()->settings->get( 'related_posts_image_size' ) ) ? 'fixed' : 'full';
	$data_image_size     = ( 'cropped' === Avada()->settings->get( 'related_posts_image_size' ) ) ? 'fixed' : 'auto';
	?>

	<?php
	/**
	 * Set the meta content variable.
	 */
	$data_meta_content = ( 'title_on_rollover' === Avada()->settings->get( 'related_posts_layout' ) ) ? 'no' : 'yes';

	$additional_carousel_class = '';
	if ( 'title_below_image' === Avada()->settings->get( 'related_posts_layout' ) ) {
		$additional_carousel_class = ' fusion-carousel-title-below-image';
	}
	?>

	<?php
	/**
	 * Set the autoplay variable.
	 */
	$data_autoplay = ( Avada()->settings->get( 'related_posts_autoplay' ) ) ? 'yes' : 'no';
	?>

	<?php
	/**
	 * Set the touch scroll variable.
	 */
	$data_swipe = ( Avada()->settings->get( 'related_posts_swipe' ) ) ? 'yes' : 'no';
	?>

	<?php $carousel_item_css = ( count( $related_posts->posts ) < Avada()->settings->get( 'related_posts_columns' ) ) ? ' style="max-width: 300px;"' : ''; ?>
	<?php $related_posts_swipe_items = Avada()->settings->get( 'related_posts_swipe_items' ); ?>
	<?php $related_posts_swipe_items = ( 0 == $related_posts_swipe_items ) ? '' : $related_posts_swipe_items; ?>
	<div class="fusion-carousel<?php echo esc_attr( $additional_carousel_class ); ?>" data-imagesize="<?php echo esc_attr( $data_image_size ); ?>" data-metacontent="<?php echo esc_attr( $data_meta_content ); ?>" data-autoplay="<?php echo esc_attr( $data_autoplay ); ?>" data-touchscroll="<?php echo esc_attr( $data_swipe ); ?>" data-columns="<?php echo esc_attr( Avada()->settings->get( 'related_posts_columns' ) ); ?>" data-itemmargin="<?php echo intval( Avada()->settings->get( 'related_posts_column_spacing' ) ) . 'px'; ?>" data-itemwidth="180" data-touchscroll="yes" data-scrollitems="<?php echo esc_attr( $related_posts_swipe_items ); ?>">
		<div class="fusion-carousel-positioner">
			<ul class="fusion-carousel-holder">
				<?php
				/**
				 * Loop through related posts.
				 */
				?>
				<?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
					<?php $post_id = get_the_ID(); ?>
					<li class="fusion-carousel-item"<?php echo $carousel_item_css; // WPCS: XSS ok. ?>>
						<div class="fusion-carousel-item-wrapper">
							<?php
							if ( 'title_on_rollover' === Avada()->settings->get( 'related_posts_layout' ) ) {
								$display_post_title = 'default';
							} else {
								$display_post_title = 'disable';
							}
							if ( 'auto' === $data_image_size ) {
								Avada()->images->set_grid_image_meta( array(
									'layout' => 'related-posts',
									'columns' => Avada()->settings->get( 'related_posts_columns' ),
								) );
							}
							echo fusion_render_first_featured_image_markup( $post_id, $featured_image_size, get_permalink( $post_id ), true, false, false, 'disable', $display_post_title, 'related' ); // WPCS: XSS ok.
							Avada()->images->set_grid_image_meta( array() );
							?>
							<?php if ( 'title_below_image' === Avada()->settings->get( 'related_posts_layout' ) ) : // Title on rollover layout. ?>
								<?php
								/**
								 * Get the post title.
								 */
								?>
								<h4 class="fusion-carousel-title">
									<a href="<?php echo esc_url_raw( get_permalink( get_the_ID() ) ); ?>"_self><?php echo get_the_title(); ?></a>
								</h4>

								<div class="fusion-carousel-meta">
									<span class="fusion-date"><?php echo esc_attr( get_the_time( Avada()->settings->get( 'date_format' ), $post_id ) ); ?></span>

									<?php if ( comments_open( $post_id ) ) : ?>
										<span class="fusion-inline-sep">|</span>
										<span><?php comments_popup_link( __( '0 Comments', 'Avada' ), __( '1 Comment', 'Avada' ), __( '% Comments', 'Avada' ) ); ?></span>
									<?php endif; ?>
								</div><!-- fusion-carousel-meta -->
							<?php endif; ?>
						</div><!-- fusion-carousel-item-wrapper -->
					</li>
				<?php endwhile; ?>
			</ul><!-- fusion-carousel-holder -->
			<?php
			/**
			 * Add navigation if needed.
			 */
			?>
			<?php if ( Avada()->settings->get( 'related_posts_navigation' ) ) : ?>
				<div class="fusion-carousel-nav">
					<span class="fusion-nav-prev"></span>
					<span class="fusion-nav-next"></span>
				</div>
			<?php endif; ?>

		</div><!-- fusion-carousel-positioner -->
	</div><!-- fusion-carousel -->
</section><!-- related-posts -->

<?php wp_reset_postdata();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
