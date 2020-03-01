<?php
/**
 * Slideshows template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php

$layout = Avada()->settings->get( 'blog_layout' );
if ( is_archive() ) {
	$layout = Avada()->settings->get( 'blog_archive_layout' );
} elseif ( is_search() ) {
	if ( ! Avada()->settings->get( 'search_featured_images' ) ) {
		return;
	}
	$layout = Avada()->settings->get( 'search_layout' );
}

$featured_image_width  = get_post_meta( $post->ID, 'pyre_fimg_width', true );
$featured_image_height = get_post_meta( $post->ID, 'pyre_fimg_height', true );

?>

<?php if ( 'Grid' !== $layout && 'masonry' !== $layout && 'Timeline' !== $layout ) {
	$styles = '';

	if ( $featured_image_width && 'auto' !== $featured_image_width ) {
		$styles .= '#post-' . $post->ID . ' .fusion-post-slideshow { max-width:' . $featured_image_width . ' !important;}';
	}

	if ( $featured_image_height && 'auto' !== $featured_image_height ) {
		$styles .= '#post-' . $post->ID . ' .fusion-post-slideshow, #post-' . $post->ID . ' .fusion-post-slideshow .fusion-image-wrapper img { max-height:' . $featured_image_height . ' !important;}';
	}

	if ( $featured_image_width && 'auto' === $featured_image_width ) {
		$styles .= '#post-' . $post->ID . ' .fusion-post-slideshow .fusion-image-wrapper img {width:auto;}';
	}

	if ( $featured_image_height && 'auto' === $featured_image_height ) {
		$styles .= '#post-' . $post->ID . ' .fusion-post-slideshow .fusion-image-wrapper img {height:auto;}';
	}

	if ( $featured_image_height && $featured_image_width && 'auto' !== $featured_image_height && 'auto' !== $featured_image_width ) {
		$styles .= '@media only screen and (max-width: 479px){#post-' . $post->ID . ' .fusion-post-slideshow, #post-' . $post->ID . ' .fusion-post-slideshow .fusion-image-wrapper img{width:auto !important; height:auto !important; } }';
	}

	if ( $styles ) {
		echo '<style type="text/css">' . $styles . '</style>'; // WPCS: XSS ok.
	}
}

$permalink = get_permalink( $post->ID );

if ( is_archive() ) {
	$size = ( 'None' === Avada()->settings->get( 'blog_archive_sidebar' ) && 'None' === Avada()->settings->get( 'blog_archive_sidebar_2' ) ) ? 'full' : 'blog-large';
} else {
	$size = ( ! Avada()->template->has_sidebar() ) ? 'full' : 'blog-large';
}
$size = ( 'Medium' === $layout || 'Medium Alternate' === $layout ) ? 'blog-medium' : $size;
$size = ( $featured_image_height && $featured_image_width && 'auto' !== $featured_image_height && 'auto' !== $featured_image_width ) ? 'full' : $size;
$size = ( 'auto' === $featured_image_height || 'auto' === $featured_image_width ) ? 'full' : $size;
$size = ( 'Grid' === $layout || 'masonry' === $layout || 'Timeline' === $layout ) ? 'full' : $size;

$video = get_post_meta( get_the_ID(), 'pyre_video', true );
?>

<?php if ( ( has_post_thumbnail() || $video ) && ! post_password_required( get_the_ID() ) ) : ?>
	<?php $thumbnail_id = get_post_thumbnail_id(); ?>
	<div class="fusion-flexslider flexslider fusion-flexslider-loading fusion-post-slideshow">
		<ul class="slides">
			<?php if ( $video ) : ?>
				<li>
					<div class="full-video">
						<?php echo $video; // WPCS: XSS ok. ?>
					</div>
				</li>
			<?php endif; ?>
			<?php
			if ( 'Grid' === $layout || 'masonry' === $layout ) {

				// Masonry layout, get the element orientation class.
				$element_orientation_class = '';
				$responsive_images_columns = Avada()->settings->get( 'blog_grid_columns' );
				if ( 'masonry' === $layout && has_post_thumbnail() ) {
					$post_thumbnail_attachment = wp_get_attachment_image_src( $thumbnail_id, 'full' );
					$element_orientation_class = Avada()->images->get_element_orientation_class( $post_thumbnail_attachment );

					// Check if we have a landscape image, then it has to stretch over 2 cols.
					if ( 'fusion-element-landscape' === $element_orientation_class ) {
						$responsive_images_columns /= 2;
					}
				}

				Avada()->images->set_grid_image_meta( array(
					'layout' => strtolower( $layout ),
					'columns' => $responsive_images_columns,
					'gutter_width' => Avada()->settings->get( 'blog_grid_column_spacing' ),
				) );
			} elseif ( 'Timeline' === $layout ) {
				Avada()->images->set_grid_image_meta( array(
					'layout' => strtolower( $layout ),
					'columns' => '2',
				) );
			} elseif ( false !== strpos( $layout, 'large' ) && 'full' === $size ) {
				Avada()->images->set_grid_image_meta( array(
					'layout' => strtolower( $layout ),
					'columns' => '1',
				) );
			}
			?>
			<?php if ( has_post_thumbnail() ) : ?>
				<?php $full_image      = wp_get_attachment_image_src( $thumbnail_id, 'full' ); ?>
				<?php $attachment_data = wp_get_attachment_metadata( $thumbnail_id ); ?>
				<?php if ( is_search() ) : ?>
					<li><?php echo fusion_render_first_featured_image_markup( $post->ID, $size, $permalink, false, false, true ); // WPCS: XSS ok. ?></li>
				<?php else : ?>
					<li><?php echo fusion_render_first_featured_image_markup( $post->ID, $size, $permalink ); // WPCS: XSS ok. ?></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php $i = 2; ?>
			<?php while ( $i <= Avada()->settings->get( 'posts_slideshow_number' ) ) : ?>
				<?php $attachment_id = fusion_get_featured_image_id( 'featured-image-' . $i, 'post' ); ?>
				<?php if ( $attachment_id ) : ?>
					<?php $attachment_image = wp_get_attachment_image_src( $attachment_id, $size ); ?>
					<?php $full_image       = wp_get_attachment_image_src( $attachment_id, 'full' ); ?>
					<?php $attachment_data  = wp_get_attachment_metadata( $attachment_id ); ?>
					<?php if ( is_array( $attachment_data ) ) : ?>
						<li>
							<div class="fusion-image-wrapper">
								<a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>">
									<?php
									$image_markup = '<img src="' . $attachment_image[0] . '" alt="' . $attachment_data['image_meta']['title'] . '" class="wp-image-' . $attachment_id . '" role="presentation"/>';
									$image_markup = Avada()->images->edit_grid_image_src( $image_markup, get_the_ID(), $attachment_id, $size );

									if ( function_exists( 'wp_make_content_images_responsive' ) ) {
										echo wp_make_content_images_responsive( $image_markup ); // WPCS: XSS ok.
									} else {
										echo $image_markup; // WPCS: XSS ok.
									}
									?>
								</a>
								<a style="display:none;" href="<?php echo esc_url_raw( $full_image[0] ); ?>" data-rel="iLightbox[gallery<?php echo (int) $post->ID; ?>]"  title="<?php echo esc_attr( get_post_field( 'post_excerpt', $attachment_id ) ); ?>" data-title="<?php echo esc_attr( get_post_field( 'post_title', $attachment_id ) ); ?>" data-caption="<?php echo esc_attr( get_post_field( 'post_excerpt', $attachment_id ) ); ?>">
									<?php
									$alt_tag = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
									if ( $alt_tag ) : ?>
										<img style="display:none;" alt="<?php echo esc_attr( $alt_tag ); ?>" role="presentation" />
									<?php endif; ?>
								</a>
							</div>
						</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php $i++; ?>
			<?php endwhile; ?>
			<?php Avada()->images->set_grid_image_meta( array() ); ?>
		</ul>
	</div>
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
