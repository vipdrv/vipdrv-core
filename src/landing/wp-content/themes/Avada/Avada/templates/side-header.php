<?php
/**
 * Side-header template.
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
<?php

// The avada_before_header_wrapper hook.
do_action( 'avada_before_header_wrapper' );

$sticky_header_logo = ( Avada()->settings->get( 'sticky_header_logo' ) ) ? true : false;
$mobile_logo        = ( Avada()->settings->get( 'mobile_logo' ) ) ? true : false;
?>

<div id="side-header-sticky"></div>
<div id="side-header" class="clearfix fusion-mobile-menu-design-<?php echo esc_attr( strtolower( Avada()->settings->get( 'mobile_menu_design' ) ) ); ?> fusion-sticky-logo-<?php echo esc_attr( $sticky_header_logo ); ?> fusion-mobile-logo-<?php echo esc_attr( $mobile_logo ); ?> fusion-sticky-menu-<?php echo esc_attr( has_nav_menu( 'sticky_navigation' ) ); ?><?php echo ( Avada()->settings->get( 'header_shadow' ) ) ? ' header-shadow' : ''; ?>">
	<div class="side-header-wrapper">
		<?php
		// The avada_header_inner_before hook.
		do_action( 'avada_header_inner_before' );
		?>
		<?php $mobile_logo = ( Avada()->settings->get( 'mobile_logo' ) ) ? true : false; ?>
		<div class="side-header-content fusion-logo-<?php echo esc_attr( strtolower( Avada()->settings->get( 'logo_alignment' ) ) ); ?> fusion-mobile-logo-<?php echo esc_attr( $mobile_logo ); ?>">
			<?php avada_logo(); ?>
		</div>
		<div class="fusion-main-menu-container fusion-logo-menu-<?php echo esc_attr( strtolower( Avada()->settings->get( 'logo_alignment' ) ) ); ?>">
			<?php avada_main_menu(); ?>
		</div>

		<?php if ( 'Tagline And Search' == Avada()->settings->get( 'header_v4_content' ) || 'Search' == Avada()->settings->get( 'header_v4_content' ) ) : ?>
			<div class="fusion-secondary-menu-search">
				<div class="fusion-secondary-menu-search-inner"><?php get_search_form(); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( 'Leave Empty' != Avada()->settings->get( 'header_left_content' ) || 'Leave Empty' != Avada()->settings->get( 'header_right_content' ) ) : ?>
			<?php $content_1 = avada_secondary_header_content( 'header_left_content' ); ?>
			<?php $content_2 = avada_secondary_header_content( 'header_right_content' ); ?>

			<div class="side-header-content side-header-content-1-2">
				<?php if ( $content_1 ) : ?>
					<div class="side-header-content-1 fusion-clearfix">
					<?php echo $content_1; // WPCS: XSS ok. ?>
					</div>
				<?php endif; ?>
				<?php if ( $content_2 ) : ?>
					<div class="side-header-content-2 fusion-clearfix">
					<?php echo $content_2; // WPCS: XSS ok. ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( 'None' != Avada()->settings->get( 'header_v4_content' ) ) : ?>
			<div class="side-header-content side-header-content-3">
				<?php avada_header_content_3(); ?>
			</div>
		<?php endif; ?>

		<?php
		// The avada_header_inner_after hook.
		do_action( 'avada_header_inner_after' );
		?>
	</div>
	<style>
	.side-header-styling-wrapper > div {
		display: none !important;
	}

	.side-header-styling-wrapper .side-header-background-image,
	.side-header-styling-wrapper .side-header-background-color,
	.side-header-styling-wrapper .side-header-border {
		display: block !important;
	}
	</style>
	<div class="side-header-styling-wrapper" style="overflow:hidden;">
		<div class="side-header-background-image"></div>
		<div class="side-header-background-color"></div>
		<div class="side-header-border"></div>
	</div>
</div>

<?php
// The avada_after_header_wrapper hook.
do_action( 'avada_after_header_wrapper' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
