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
 * Handle migrations for Avada 4.0.2.
 *
 * @since 5.0.0
 */
class Avada_Upgrade_402 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @var string
	 */
	protected $version = '4.0.2';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.0.0
	 */
	protected function migration_process() {

		$options = get_option( $this->option_name, array() );

		// Update social-media repeaters.
		$social_media = Avada()->settings->get( 'social_media_icons' );
		if ( isset( $social_media['redux_repeater_data'] ) ) {
			$social_media['avadaredux_repeater_data'] = $social_media['redux_repeater_data'];
			unset( $social_media['redux_repeater_data'] );
			$options['social_media_icons'] = $social_media;
			update_option( $this->option_name, $options );
		}

		// Update custom-fonts repeaters.
		$custom_fonts = Avada()->settings->get( 'custom_fonts' );
		if ( isset( $custom_fonts['redux_repeater_data'] ) ) {
			$custom_fonts['avadaredux_repeater_data'] = $custom_fonts['redux_repeater_data'];
			unset( $custom_fonts['redux_repeater_data'] );
			$options['custom_fonts'] = $custom_fonts;
			update_option( $this->option_name, $options );
		}

	}
}
