<?php
/**
 * Upgrades Handler.
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

/**
 * Handle migrations for Avada 3.8.5.
 *
 * @since 5.0.0
 */
class Avada_Upgrade_385 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @var string
	 */
	protected $version = '3.8.5';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.0.0
	 */
	protected function migration_process() {

		$options = get_option( $this->option_name, array() );

		// We no longer have a less compiler.
		// Migrate the less_compiler option to the new dynamic_css_compiler option.
		if ( isset( $options['less_compiler'] ) ) {
			$options['dynamic_css_compiler'] = $options['less_compiler'];
		}

		// We added an independent theme option for content box icons.
		if ( isset( $options['icon_color'] ) ) {
			$options['content_box_icon_color'] = $options['icon_color'];
		}

		if ( isset( $options['icon_circle_color'] ) ) {
			$options['content_box_icon_bg_color'] = $options['icon_circle_color'];
		}

		if ( isset( $options['icon_border_color'] ) ) {
			$options['content_box_icon_bg_inner_border_color'] = $options['icon_border_color'];
		}
		if ( isset( $options['h2_font_size'] ) ) {
			$options['post_titles_font_size'] = $options['h2_font_size'];
			$options['post_titles_extras_font_size'] = $options['h2_font_size'];
		}
		if ( isset( $options['h2_font_lh'] ) ) {
			$options['post_titles_font_lh'] = $options['h2_font_lh'];
		}

		// Update the options with our modifications.
		update_option( $this->option_name, $options );

	}
}
