<?php
/**
 * Avada Options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Shortcode-Styling settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_redux_shortcode_styling( $sections ) {

	$option_name = 'fusion_builder_options';

	if ( class_exists( 'FusionBuilder' ) ) {
		$sections['shortcode_styling'] = array(
			'label'    => esc_html__( 'Fusion Builder Elements', 'Avada' ),
			'id'       => 'fusion_builder_elements',
			'is_panel' => true,
			'priority' => 14,
			'icon'     => 'el-icon-cog',
			'fields'   => array(
				'shortcode_styling' => array(
						'label'       => '',
						'description' => '<div class="fusion-redux-important-notice">' . sprintf( __( '<strong>IMPORTANT NOTE:</strong> Fusion Builder Elements settigns are moved to Fusion Builder Elements options panel <a href="%s" target="_blank">here</a>.', 'Avada' ), admin_url( 'admin.php?page=fusion-element-options' ) ) . '</div>',
						'id'          => 'shortcode_styling',
						'type'        => 'custom',
						'option_name' => $option_name,
				),
			),
		);
	}

	return $sections;

}
