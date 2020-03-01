<?php
/**
 * Rollovers template.
 *
 * @package Fusion-Library
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php

global $product, $woocommerce;

// Set defaults for Fusion Builder ( Fusion Page Options ).
$image_rollover_icons = apply_filters( 'fusion_builder_image_rollover_icons', 'linkzoom', $post_id );

// Portfolio defaults.
$link_icon_target = apply_filters( 'fusion_builder_link_icon_target', '', $post_id );
$video_url        = apply_filters( 'fusion_builder_video_url', '', $post_id );

// Blog defaults.
$link_icon_url     = apply_filters( 'fusion_builder_link_icon_url', '', $post_id );
$post_links_target = apply_filters( 'fusion_builder_post_links_target', '', $post_id );

// Set defaults for Fusion Builder ( Theme Options ).
$cats_image_rollover  = apply_filters( 'fusion_builder_cats_image_rollover', false );
$title_image_rollover = apply_filters( 'fusion_builder_title_image_rollover', false );
// Portfolio defaults.
$portfolio_link_icon_target = apply_filters( 'fusion_builder_portfolio_link_icon_target', false );

// Retrieve the permalink if it is not set.
$post_permalink = ( ! $post_permalink ) ? get_permalink( $post_id ) : $post_permalink;

// Check if theme options are used as base or if there is an override for post categories.
if ( 'default' == $display_post_categories ) {
	$display_post_categories = fusion_library()->get_option( 'cats_image_rollover' );
} elseif ( 'enable' === $display_post_categories ) {
	$display_post_categories = true;
} elseif ( 'disable' === $display_post_categories ) {
	$display_post_categories = false;
} else {
	$display_post_categories = $cats_image_rollover;
}

// Check if theme options are used as base or if there is an override for post title.
if ( 'default' === $display_post_title ) {
	$display_post_title = fusion_library()->get_option( 'title_image_rollover' );
} elseif ( 'enable' === $display_post_title ) {
	$display_post_title = true;
} elseif ( 'disable' === $display_post_title ) {
	$display_post_title = false;
} else {
	$display_post_title = $title_image_rollover;
}

// Set the link and the link text on the link icon to a custom url if set in page options. @codingStandardsIgnoreLine
if ( null != $link_icon_url ) {
	$icon_permalink = $link_icon_url;
	$icon_permalink_title = $link_icon_url;
} else {
	$icon_permalink = $post_permalink;
	$icon_permalink_title = get_the_title( $post_id );
}

if ( '' === $image_rollover_icons || 'default' === $image_rollover_icons ) {
	if ( fusion_library()->get_option( 'link_image_rollover' ) && fusion_library()->get_option( 'zoom_image_rollover' ) ) { // Link + Zoom.
		$image_rollover_icons = 'linkzoom';
	} elseif ( fusion_library()->get_option( 'link_image_rollover' ) && ! fusion_library()->get_option( 'zoom_image_rollover' ) ) { // Link.
		$image_rollover_icons = 'link';
	} elseif ( ! fusion_library()->get_option( 'link_image_rollover' ) && fusion_library()->get_option( 'zoom_image_rollover' ) ) { // Zoom.
		$image_rollover_icons = 'zoom';
	} elseif ( ! fusion_library()->get_option( 'link_image_rollover' ) && ! fusion_library()->get_option( 'zoom_image_rollover' ) ) { // Link.
		$image_rollover_icons = 'no';
	} else {
		$image_rollover_icons = 'linkzoom';
	}
}

// Set the link target to blank if the option is set.
$link_target = ( 'yes' === $link_icon_target || 'yes' === $post_links_target || ( 'avada_portfolio' === get_post_type() && $portfolio_link_icon_target && 'default' === $link_icon_target ) ) ? ' target="_blank"' : '';
?>
<div class="fusion-rollover">
	<div class="fusion-rollover-content">

		<?php
		/**
		 * Check if rollover icons should be displayed.
		 */
		?>
		<?php if ( 'no' !== $image_rollover_icons && 'product' !== get_post_type( $post_id ) ) : ?>
			<?php
			/**
			 * If set, render the rollover link icon.
			 */
			?>
			<?php if ( 'zoom' !== $image_rollover_icons ) : ?>
				<a class="fusion-rollover-link" href="<?php echo esc_url_raw( $icon_permalink ); ?>"<?php echo $link_target; // WPCS: XSS ok. ?>><?php echo $icon_permalink_title; // WPCS: XSS ok. ?></a>
			<?php endif; ?>

			<?php
			/**
			 * If set, render the rollover zoom icon.
			 */
			?>
			<?php if ( 'link' !== $image_rollover_icons ) : ?>
				<?php $full_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); // Get the image data. ?>
				<?php
				$full_image = ( ! is_array( $full_image ) ) ? array(
					0 => '',
				) : $full_image;
				?>

				<?php
				/**
				 * If a video url is set in the post options, use it inside the lightbox.
				 */
				?>
				<?php if ( $video_url ) : ?>
					<?php $full_image[0] = $video_url; ?>
				<?php endif; ?>

				<?php
				/**
				 * If both icons will be shown, add a separator.
				 */
				?>
				<?php if ( ( 'linkzoom' === $image_rollover_icons || '' === $image_rollover_icons ) && $full_image[0] ) : ?>
					<div class="fusion-rollover-sep"></div>
				<?php endif; ?>

				<?php
				/**
				 * Render the rollover zoom icon if we have an image.
				 */
				?>
				<?php if ( $full_image[0] ) : ?>
					<?php
					/**
					 * Only show images of the clicked post.
					 * Otherwise, show the first image of every post on the archive page.
					 */
					$lightbox_content = ( 'individual' === fusion_library()->get_option( 'lightbox_behavior' ) ) ? avada_featured_images_lightbox( $post_id ) : '';
					$data_rel         = ( 'individual' === fusion_library()->get_option( 'lightbox_behavior' ) ) ? 'iLightbox[gallery' . $post_id . ']' : 'iLightbox[gallery' . $gallery_id . ']';
					?>
					<a class="fusion-rollover-gallery" href="<?php echo esc_url_raw( $full_image[0] ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-rel="<?php echo esc_attr( $data_rel ); ?>" data-title="<?php echo esc_attr( get_post_field( 'post_title', get_post_thumbnail_id( $post_id ) ) ); ?>" data-caption="<?php echo esc_attr( get_post_field( 'post_excerpt', get_post_thumbnail_id( $post_id ) ) ); ?>">
						<?php esc_html_e( 'Gallery', 'fusion-builder' ); ?>
					</a>
					<?php echo $lightbox_content; // WPCS: XSS ok. ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php $in_cart = false; ?>
		<?php if ( class_exists( 'WooCommerce' ) && $woocommerce->cart ) : ?>
			<?php $items_in_cart = array(); ?>
			<?php if ( $woocommerce->cart->get_cart() && is_array( $woocommerce->cart->get_cart() ) ) : ?>
				<?php foreach ( $woocommerce->cart->get_cart() as $cart ) : ?>
					<?php $items_in_cart[] = $cart['product_id']; ?>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php $id = get_the_ID(); ?>
			<?php // @codingStandardsIgnoreLine ?>
			<?php $in_cart = in_array( $id, $items_in_cart ); ?>
		<?php endif; ?>

		<?php if ( ! $in_cart ) : ?>
			<?php
			/**
			 * Check if we should render the post title on the rollover.
			 */
			?>
			<?php if ( $display_post_title ) : ?>
				<h4 class="fusion-rollover-title">
					<a href="<?php echo esc_url_raw( $icon_permalink ); ?>"<?php echo $link_target; // WPCS: XSS ok. ?>>
						<?php echo get_the_title( $post_id ); ?>
					</a>
				</h4>
			<?php endif; ?>

			<?php
			/**
			 * Check if we should render the post categories on the rollover.
			 */
			?>
			<?php if ( $display_post_categories ) : ?>
				<?php
				// Determine the correct taxonomy.
				$post_taxonomy = '';
				if ( 'post' === get_post_type( $post_id ) ) {
					$post_taxonomy = 'category';
				} elseif ( 'avada_portfolio' === get_post_type( $post_id ) ) {
					$post_taxonomy = 'portfolio_category';
				} elseif ( 'product' === get_post_type( $post_id ) ) {
					$post_taxonomy = 'product_cat';
				}
				?>
				<?php echo get_the_term_list( $post_id, $post_taxonomy, '<div class="fusion-rollover-categories">', ', ', '</div>' ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( class_exists( 'WooCommerce' ) && $woocommerce->cart && 'product' === get_post_type( $post_id ) ) : ?>
			<?php $icon_class = ( $in_cart ) ? 'fusion-icon-check-square-o' : 'fusion-icon-spinner'; ?>
			<div class="cart-loading">
				<a href="<?php echo esc_url_raw( $woocommerce->cart->get_cart_url() ); ?>">
					<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
					<div class="view-cart"><?php esc_html_e( 'View Cart', 'fusion-builder' ); ?></div>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( class_exists( 'WooCommerce' ) && $product && ( ( is_search() && ! $in_cart ) || ! is_search() ) ) : ?>
			<?php
			/**
			 * Check if we should render the woo product price.
			 */
			?>
			<?php if ( $display_woo_rating ) : ?>
				<?php fusion_wc_get_template( 'loop/rating.php' ); ?>
			<?php endif; ?>

			<?php
			/**
			 * Check if we should render the woo product price.
			 */
			?>
			<?php if ( $display_woo_price ) : ?>
				<?php fusion_wc_get_template( 'loop/price.php' ); ?>
			<?php endif; ?>

			<?php
			/**
			 * Check if we should render the woo "add to cart" and "details" buttons.
			 */
			?>
			<?php if ( $display_woo_buttons ) : ?>
				<div class="fusion-product-buttons">
					<?php
					/**
					 * The avada_woocommerce_buttons_on_rollover hook.
					 *
					 * @hooked FusionTemplateWoo::avada_woocommerce_template_loop_add_to_cart - 10 (outputs add to cart button)
					 * @hooked FusionTemplateWoo::avada_woocommerce_rollover_buttons_linebreak - 15 (outputs line break for the buttons, needed for clean version)
					 * @hooked FusionTemplateWoo::show_details_button - 20 (outputs the show details button)
					 */
					do_action( 'avada_woocommerce_buttons_on_rollover' );
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<a class="fusion-link-wrapper" href="<?php echo esc_url_raw( $icon_permalink ); ?>"<?php echo $link_target; // WPCS: XSS ok. ?> aria-label="<?php the_title(); ?>"></a>
	</div>
</div>
