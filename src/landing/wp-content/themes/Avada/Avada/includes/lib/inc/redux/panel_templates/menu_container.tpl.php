<?php
/**
 * The template for the menu container of the panel.
 *
 * Override this template by specifying the path where it is stored (templates_path) in your FusionRedux config.
 *
 * @author 	FusionRedux Framework
 * @package 	FusionReduxFramework/Templates
 * @version:    3.5.4
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<div class="fusionredux-sidebar">
	<div class="fusion-redux-sidebar-head">
		<div class="fusion-redux-logo"></div>
		<h2>
			<?php echo wp_kses_post( $this->parent->args['display_name'] ); ?>
			<span><?php echo wp_kses_post( $this->parent->args['display_version'] ); ?></span>
		</h2>
	</div>
	<ul class="fusionredux-group-menu">
<?php
		foreach ( $this->parent->sections as $k => $section ) {
			$title = isset ( $section[ 'title' ] ) ? $section[ 'title' ] : '';

			$skip_sec = false;
			foreach ( $this->parent->hidden_perm_sections as $num => $section_title ) {
				if ( $section_title == $title ) {
					$skip_sec = true;
				}
			}

			if ( isset ( $section[ 'customizer_only' ] ) && $section[ 'customizer_only' ] == true ) {
				continue;
			}

			if ( false == $skip_sec ) {
				echo $this->parent->section_menu ( $k, $section );
				$skip_sec = false;
			}
		}

		/**
		 * action 'fusionredux-page-after-sections-menu-{opt_name}'
		 *
		 * @param object $this FusionReduxFramework
		 */
		do_action ( "fusionredux-page-after-sections-menu-{$this->parent->args[ 'opt_name' ]}", $this );

		/**
		 * action 'fusionredux/page/{opt_name}/menu/after'
		 *
		 * @param object $this FusionReduxFramework
		 */
		do_action ( "fusionredux/page/{$this->parent->args[ 'opt_name' ]}/menu/after", $this );
?>
	</ul>
</div>
