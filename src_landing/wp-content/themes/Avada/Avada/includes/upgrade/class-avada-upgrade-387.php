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
 * Handle migrations for Avada 3.8.7.
 *
 * @since 5.0.0
 */
class Avada_Upgrade_387 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @var string
	 */
	protected $version = '3.8.7';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.0.0
	 */
	protected function migration_process() {

		$options = get_option( $this->option_name, array() );

		// Increase the height of top menu dropdown for woo cart change #2006.
		if ( isset( $options['topmenu_dropwdown_width'] ) && intval( $options['topmenu_dropwdown_width'] ) <= 180 ) {
			$options['topmenu_dropwdown_width'] = '180px';
		}

		// Increase the height of top menu dropdown for woo cart change #2006.
		if ( isset( $options['dropdown_menu_width'] ) && intval( $options['dropdown_menu_width'] ) <= 180 ) {
			$options['dropdown_menu_width'] = '180px';
		}

		// Update the options with our modifications.
		update_option( $this->option_name, $options );

	}
}
