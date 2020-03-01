<?php
/**
 * The template for slidingbars.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<div id="slidingbar-area" class="slidingbar-area fusion-widget-area<?php echo ( Avada()->settings->get( 'slidingbar_open_on_load' ) ) ? ' open_onload' : ''; ?>">
	<div id="slidingbar">
		<div class="fusion-row">
			<div class="fusion-columns row fusion-columns-<?php echo esc_attr( Avada()->settings->get( 'slidingbar_widgets_columns' ) ); ?> columns columns-<?php echo esc_attr( Avada()->settings->get( 'slidingbar_widgets_columns' ) ); ?>">
				<?php $column_width = ( Avada()->settings->get( 'slidingbar_widgets_columns' ) == '5' ) ? 2 : 12 / Avada()->settings->get( 'slidingbar_widgets_columns' ); ?>
				<?php
				/**
				 * Render as many widget columns as have been chosen in Theme Options.
				 */
				?>
				<?php for ( $i = 1; $i < 7; $i++ ) : ?>
					<?php if ( $i <= Avada()->settings->get( 'slidingbar_widgets_columns' ) ) : ?>
						<div class="fusion-column <?php echo ( Avada()->settings->get( 'slidingbar_widgets_columns' ) == $i ) ? 'fusion-column-last' : ''; ?>col-lg-<?php echo esc_attr( $column_width ); ?> col-md-<?php echo esc_attr( $column_width ); ?> col-sm-<?php echo esc_attr( $column_width ); ?>">
						<?php if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'avada-slidingbar-widget-' . $i ) ) : ?>
							<?php // All is good, dynamic_sidebar() already called the rendering. ?>
						<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endfor; ?>
				<div class="fusion-clearfix"></div>
			</div>
		</div>
	</div>
	<div class="sb-toggle-wrapper">
		<a class="sb-toggle" href="#"><span class="screen-reader-text"><?php esc_attr_e( 'Toggle SlidingBar Area', 'Avada' ); ?></span></a>
	</div>
</div>
<?php wp_reset_postdata();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
