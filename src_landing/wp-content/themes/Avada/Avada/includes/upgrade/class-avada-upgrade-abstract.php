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
 * The Avada_Upgrade_Version is meant to be extended
 * by version-specific upgrade classes.
 *
 * @since 5.0.0
 */
abstract class Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @var string
	 */
	protected $version = '';

	/**
	 * The option name as it was in the currently update version..
	 *
	 * @access protected
	 * @since 5.1.0
	 * @var string
	 */
	protected $option_name = '';

	/**
	 * The theme version as stored in the db.
	 *
	 * @access protected
	 * @var string
	 */
	protected $database_theme_version;

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @param bool $forced Whether we're forcing this update or not.
	 */
	public function __construct( $forced = false ) {

		// Set the database_theme_version.
		$this->database_theme_version = get_option( 'avada_version', true );

		if ( is_array( $this->database_theme_version ) ) {
			$this->database_theme_version = end( $this->database_theme_version );
		}
		// Make sure the version saved in the db is properly formatted.
		$this->database_theme_version = Avada_Helper::normalize_version( $this->database_theme_version );

		// Make sure the current version is properly formatted.
		$this->version = Avada_Helper::normalize_version( $this->version );

		$this->option_name = $this->set_option_name( $this->version );

		// Trigger the migration.
		$this->migration_process();

		// Set the version.
		$this->update_version();

	}

	/**
	 * Set the currect option name, how it is stored in the db of the currently converted version.
	 *
	 * @access protected
	 * @since 5.1.0
	 * @param string $version The theme version.
	 */
	protected function set_option_name( $version ) {

		if ( version_compare( $this->database_theme_version, '5.1.0', '>=' ) ) {
			$option_name = 'fusion_options';
		} elseif ( version_compare( $version, '4.0.0', '<' ) ) {
			$option_name = 'Avada_options';
		} elseif ( version_compare( $version, '5.1.0', '<=' ) ) {
			$option_name = 'avada_theme_options';
		} else {
			$option_name = 'fusion_options';
		}

		return $option_name;
	}

	/**
	 * Updates the version.
	 *
	 * @access protected
	 * @since 5.0.0
	 * @return void
	 */
	protected function update_version() {

		// Do not update the version in the db
		// if the current version is greater than the one we're updating to.
		if ( ! $this->database_theme_version || empty( $this->database_theme_version ) ) {
			return;
		}
		if ( version_compare( $this->database_theme_version, $this->version, '>=' ) ) {
			return;
		}

		update_option( 'avada_version', $this->version );

	}

	/**
	 * The actual migration process.
	 * Empty on the parent class, meant to be overriden in version-specific classes.
	 *
	 * @access protected
	 * @since 5.0.0
	 */
	abstract protected function migration_process();

}
