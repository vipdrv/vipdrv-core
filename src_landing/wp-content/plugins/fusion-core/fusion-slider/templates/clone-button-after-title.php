<?php
/**
 * Clone slide button template.
 *
 * @package Fusion-Slider
 * @subpackage Templates
 * @since 1.0.0
 */

?>
<div id="fusion-slide-clone">
	<?php // @codingStandardsIgnoreLine ?>
	<a href="<?php echo esc_url_raw( $this->get_slide_clone_link( $_GET['post'] ) ); ?>" class="button">
		<?php esc_attr_e( 'Clone this slide', 'fusion-core' ); ?>
	</a>
</div>
