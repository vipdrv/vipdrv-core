<?php
/**
 * Titlebar template.
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
<div class="fusion-page-title-bar fusion-page-title-bar-<?php echo esc_attr( $content_type ); ?> fusion-page-title-bar-<?php echo esc_attr( $alignment ); ?>">
	<div class="fusion-page-title-row">
		<div class="fusion-page-title-wrapper">
			<div class="fusion-page-title-captions">

				<?php if ( $title ) : ?>
					<?php // Add entry-title for rich snippets. ?>
					<?php $entry_title_class = ( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) && Avada()->settings->get( 'disable_rich_snippet_title' ) ) ? 'entry-title' : ''; ?>
					<h1 class="<?php echo esc_attr( $entry_title_class ); ?>"><?php echo $title; // WPCS: XSS ok. ?></h1>

					<?php if ( $subtitle ) : ?>
						<h3><?php echo $subtitle; // WPCS: XSS ok. ?></h3>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'center' === $alignment ) : // Render secondary content on center layout. ?>
					<?php if ( 'none' !== fusion_get_option( 'page_title_bar_bs', 'page_title_breadcrumbs_search_bar', $post_id ) ) : ?>
						<div class="fusion-page-title-secondary">
							<?php echo $secondary_content; // WPCS: XSS ok. ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</div>

			<?php if ( 'center' !== $alignment ) : // Render secondary content on left/right layout. ?>
				<?php if ( 'none' !== fusion_get_option( 'page_title_bar_bs', 'page_title_breadcrumbs_search_bar', $post_id ) ) : ?>
					<div class="fusion-page-title-secondary">
						<?php echo $secondary_content; // WPCS: XSS ok. ?>
					</div>
				<?php endif; ?>
			<?php endif;?>

		</div>
	</div>
</div>
