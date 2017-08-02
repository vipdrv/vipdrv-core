<?php
/**
 * Header-3-content template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

if ( 'v4' !== Avada()->settings->get( 'header_layout' ) && Avada()->settings->get( 'header_position' ) == 'Top' ) {
	return;
}

$header_content_3 = Avada()->settings->get( 'header_v4_content' );
?>

<div class="fusion-header-content-3-wrapper">
	<?php if ( 'Tagline' === $header_content_3 ) : ?>
		<h3 class="fusion-header-tagline">
			<?php echo do_shortcode( Avada()->settings->get( 'header_tagline' ) ); ?>
		</h3>
	<?php elseif ( 'Tagline And Search' == $header_content_3 ) : ?>
		<?php if ( 'Top' === Avada()->settings->get( 'header_position' ) ) : ?>
			<?php if ( 'Right' == Avada()->settings->get( 'logo_alignment' ) ) : ?>
				<h3 class="fusion-header-tagline">
					<?php echo do_shortcode( Avada()->settings->get( 'header_tagline' ) ); ?>
				</h3>
				<div class="fusion-secondary-menu-search">
					<?php get_search_form( true ); ?>
				</div>
			<?php else : ?>
				<div class="fusion-secondary-menu-search">
					<?php get_search_form( true ); ?>
				</div>
				<h3 class="fusion-header-tagline">
					<?php echo do_shortcode( Avada()->settings->get( 'header_tagline' ) ); ?>
				</h3>
			<?php endif; ?>
		<?php else : ?>
			<h3 class="fusion-header-tagline">
				<?php echo do_shortcode( Avada()->settings->get( 'header_tagline' ) ); ?>
			</h3>
			<div class="fusion-secondary-menu-search">
				<?php get_search_form( true ); ?>
			</div>
		<?php endif; ?>
	<?php elseif ( 'Search' === $header_content_3 ) : ?>
		<div class="fusion-secondary-menu-search">
			<?php get_search_form( true ); ?>
		</div>
	<?php elseif ( 'Banner' === $header_content_3 ) : ?>
		<div class="fusion-header-banner">
			<?php echo do_shortcode( Avada()->settings->get( 'header_banner_code' ) ); ?>
		</div>
	<?php endif; ?>
</div>
