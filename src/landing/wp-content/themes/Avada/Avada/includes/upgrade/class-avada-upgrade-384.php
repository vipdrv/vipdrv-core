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
 * Handle migrations for Avada 3.8.4.
 *
 * @since 5.0.0
 */
class Avada_Upgrade_384 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @var string
	 */
	protected $version = '3.8.4';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.0.0
	 */
	protected function migration_process() {
		if ( 'done' !== get_option( 'avada_38_migrate' ) ) {
			$theme_version = get_option( 'avada_theme_version' );

			if ( '1.0.0' == $theme_version ) { // Child theme check failure.
				Avada()->init->set_theme_version();
			}

			if ( version_compare( $theme_version, '3.8', '>=' ) && version_compare( $theme_version, '3.8.5', '<' ) ) {
				$smof_data_to_decode = get_option( 'Avada_options' );

				$encoded_field_names = array( 'google_analytics', 'space_head', 'space_body', 'custom_css' );

				foreach ( $encoded_field_names as $field_name ) {
					$decoded_field_value = rawurldecode( $smof_data_to_decode[ $field_name ] );

					if ( $decoded_field_value ) {
						$smof_data_to_decode[ $field_name ] = $decoded_field_value;
					}
				}

				update_option( 'Avada_options', $smof_data_to_decode );
				update_option( 'avada_38_migrate', 'done' );
			}
		}
	}
}
