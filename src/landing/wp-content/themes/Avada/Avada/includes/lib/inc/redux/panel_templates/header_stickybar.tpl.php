<?php
/**
 * The template for the header sticky bar.
 * Override this template by specifying the path where it is stored (templates_path) in your FusionRedux config.
 *
 * @author        FusionRedux Framework
 * @package       FusionReduxFramework/Templates
 * @version:      3.5.7.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<div id="fusionredux-sticky">
	<div id="info_bar">

		<a href="javascript:void(0);" class="expand_options<?php echo esc_attr(( $this->parent->args['open_expanded'] ) ? ' expanded' : ''); ?>"<?php echo $this->parent->args['hide_expand'] ? ' style="display: none;"' : '' ?>>
			<span class="dashicons dashicons-editor-ul"></span><?php esc_attr_e( 'Expand Options', 'Avada' ); ?>
		</a>

		<div class="fusion-support-links">
			<a href="https://theme-fusion.com/support" target="_blank"><span class="dashicons dashicons-thumbs-up"></span><?php esc_attr_e( 'Support Center', 'Avada' ); ?></a>
		</div>

		<div class="fusionredux-action_bar">
			<span class="spinner"></span>
			<?php
			if ( false === $this->parent->args['hide_save'] ) {
				submit_button( esc_attr__( 'Save Changes', 'Avada' ), 'primary', 'fusionredux_save', false );
			}

			if ( false === $this->parent->args['hide_reset'] ) {
				submit_button( esc_attr__( 'Reset Section', 'Avada' ), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'fusionredux-defaults-section' ) );
				submit_button( esc_attr__( 'Reset All', 'Avada' ), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'fusionredux-defaults' ) );
			}
			?>
		</div>
		<div class="fusionredux-ajax-loading" alt="<?php esc_attr_e( 'Working...', 'Avada' ) ?>">&nbsp;</div>
		<div class="clear"></div>
	</div>

	<!-- Notification bar -->
	<div id="fusionredux_notification_bar">
		<?php $this->notification_bar(); ?>
	</div>


</div>
