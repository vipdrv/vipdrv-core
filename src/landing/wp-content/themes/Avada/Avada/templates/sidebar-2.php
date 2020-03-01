<?php
/**
 * Sidebar-2 template.
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
$sticky_sidebar = in_array( 'fusion-sticky-sidebar', apply_filters( 'fusion_sidebar_2_class', array() ) );
?>
<aside id="sidebar-2" role="complementary" <?php Avada()->layout->add_class( 'sidebar_2_class' ); ?> <?php Avada()->layout->add_style( 'sidebar_2_style' ); ?> <?php Avada()->layout->add_data( 'sidebar_2_data' ); ?>>
	<?php if ( $sticky_sidebar ) : ?>
		<div class="fusion-sidebar-inner-content">
	<?php endif; ?>
		<?php if ( 'right' === Avada()->layout->sidebars['position'] ) : ?>
			<?php echo avada_display_sidenav( Avada()->fusion_library->get_page_id() ); // WPCS: XSS ok. ?>

			<?php if ( class_exists( 'Tribe__Events__Main' ) && is_singular( 'tribe_events' ) ) : ?>
				<?php do_action( 'tribe_events_single_event_before_the_meta' ); ?>
				<?php tribe_get_template_part( 'modules/meta' ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( isset( Avada()->layout->sidebars['sidebar_2'] ) && Avada()->layout->sidebars['sidebar_2'] ) : ?>
			<?php generated_dynamic_sidebar( Avada()->layout->sidebars['sidebar_2'] ); ?>
		<?php endif; ?>
	<?php if ( $sticky_sidebar ) : ?>
		</div>
	<?php endif; ?>
</aside>
