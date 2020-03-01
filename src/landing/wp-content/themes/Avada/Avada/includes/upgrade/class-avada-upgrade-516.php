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
 * Handle migrations for Avada 5.1.6.
 *
 * @since 5.1.6
 */
class Avada_Upgrade_516 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.1.6
	 * @var string
	 */
	protected $version = '5.1.6';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.1.6
	 */
	protected function migration_process() {
		$options = get_option( $this->option_name, array() );

		// Update portfolio archive layout, when set to grid.
		$portfolio_archive_layout = Avada()->settings->get( 'portfolio_archive_layout' );

		if ( 'Portfolio Grid' === $portfolio_archive_layout ) {
			$options['portfolio_archive_layout'] = 'Portfolio One Column';
			update_option( $this->option_name, $options );
		}
	}
}
