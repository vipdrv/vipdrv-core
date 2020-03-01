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
 * Handle migrations for Avada 4.0.3.
 *
 * @since 5.0.3
 */
class Avada_Upgrade_503 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.3
	 * @var string
	 */
	protected $version = '5.0.3';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.0.3
	 */
	protected function migration_process() {

		$options = get_option( $this->option_name, array() );

		// Update the post title option.
		$portfolio_items = Avada()->settings->get( 'portfolio_items' );

		if ( '0' === $portfolio_items || 0 === $portfolio_items ) {
			$options['portfolio_items'] = '-1';
			update_option( $this->option_name, $options );
		}
	}
}
