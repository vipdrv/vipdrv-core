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
 * Handle migrations for Avada 5.2.1.
 *
 * @since 5.2.1
 */
class Avada_Upgrade_521 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.2.1
	 * @var string
	 */
	protected $version = '5.2.1';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.2.1
	 * @return void
	 */
	protected function migration_process() {

		$this->update_portfolio_settings();

	}

	/**
	 * Update portfolio archive items TO with the posts_per_page setting.
	 *
	 * @access private
	 * @since 5.2.1
	 * @return void
	 */
	private function update_portfolio_settings() {
		$options = get_option( $this->option_name, array() );

		$posts_per_page = get_option( 'posts_per_page' );

		$options['portfolio_archive_items'] = $posts_per_page;

		update_option( $this->option_name, $options );
	}
}
