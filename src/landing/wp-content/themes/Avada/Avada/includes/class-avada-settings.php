<?php
/**
 * Settings handler.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Get & set setting values.
 */
class Avada_Settings extends Fusion_Settings {

	/**
	 * Access the single instance of the parent class.
	 *
	 * @return Fusion_Settings
	 */
	public static function get_instance() {
		if ( null === parent::$instance ) {
			parent::$instance = new parent();
		}
		return parent::$instance;
	}

}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
