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
 * Handle migrations for Avada 5.1.5.
 *
 * @since 5.1.5
 */
class Avada_Upgrade_515 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.1.5
	 * @var string
	 */
	protected $version = '5.1.5';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.1.5
	 */
	protected function migration_process() {
		return;
	}
}
