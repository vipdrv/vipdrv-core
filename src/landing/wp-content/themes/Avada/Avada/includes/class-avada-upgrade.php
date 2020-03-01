<?php
/**
 * Handles upgrades.
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
 * Handle upgrades.
 */
class Avada_Upgrade {

	/**
	 * Instance.
	 *
	 * @static
	 * @access public
	 * @var null|object
	 */
	public static $instance = null;

	/**
	 * The theme version as stored in the db.
	 *
	 * @access private
	 * @var string
	 */
	private $database_theme_version;

	/**
	 * An array of previous versions.
	 *
	 * @access private
	 * @var array
	 */
	private $previous_theme_versions;

	/**
	 * The previouis version.
	 *
	 * @access private
	 * @var string
	 */
	private $previous_theme_version;

	/**
	 * The current version.
	 *
	 * @access private
	 * @var string
	 */
	private $current_theme_version;

	/**
	 * An array of all avada options.
	 *
	 * @access private
	 * @var array
	 */
	private $avada_options;

	/**
	 * The current User object.
	 *
	 * @access private
	 * @var object
	 */
	private $current_user;

	/**
	 * An array of all the already upgraded options.
	 *
	 * @access private
	 * @var array
	 */
	private static $upgraded_options = array();

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	protected function __construct() {

		$this->previous_theme_versions = get_option( 'avada_previous_version', array() );
		// Previous version only really needed, because through the upgrade loop, the database_theme_version will be altered.
		$this->previous_theme_version  = $this->get_previous_theme_version();
		$this->database_theme_version  = get_option( 'avada_version', false );
		$this->database_theme_version  = Avada_Helper::normalize_version( $this->database_theme_version );
		$this->current_theme_version   = Avada::get_theme_version();
		$this->current_theme_version   = Avada_Helper::normalize_version( $this->current_theme_version );

		// Check through all options names that were available for Theme Options in databse.
		$theme_options = get_option( Avada::get_option_name(), get_option( 'avada_theme_options', get_option( 'Avada_options', false ) ) );

		// If no old version is in database or there are no saved options,
		// this is a new install, nothing to do, but to copy version to db.
		if ( false === $this->database_theme_version || ! $theme_options ) {
			$this->fresh_installation();
			return;

			// If on front-end and user intervention necessary, do not continue.
		} elseif ( ! is_admin() && version_compare( $this->database_theme_version, '5.0.1', '<' ) ) {
			return;
		}

		// Each version is defined as an array( 'Version', 'Force-Instantiation' ).
		$versions = array(
			'385' => array( '3.8.5', false ),
			'387' => array( '3.8.7', false ),
			'390' => array( '3.9.0', false ),
			'392' => array( '3.9.2', false ),
			'400' => array( '4.0.0', true ),
			'402' => array( '4.0.2', false ),
			'403' => array( '4.0.3', false ),
			'500' => array( '5.0.0', true ),
			'503' => array( '5.0.3', false ),
			'510' => array( '5.1.0', false ),
			'516' => array( '5.1.6', false ),
			'520' => array( '5.2.0', false ),
			'521' => array( '5.2.1', false ),
		);

		$upgraded = false;
		foreach ( $versions as $key => $version ) {

			$classname = 'Avada_Upgrade_' . $key;

			if ( $this->database_theme_version && version_compare( $this->database_theme_version, $version[0], '<' ) ) {
				$upgraded = true;
				// Instantiate the class if migration is needed.
				new $classname();
			} elseif ( true === $version[1] ) {
				// Instantiate the class if force-instantiation is set to true.
				new $classname( true );
			}
		}

		if ( true === $upgraded ) {
			// Reset all Fusion caches.
			if ( ! class_exists( 'Fusion_Cache' ) ) {
				include_once Avada::$template_dir_path . '/includes/lib/inc/class-fusion-cache.php';
			}

			$fusion_cache = new Fusion_Cache();
			$fusion_cache->reset_all_caches();
		}

		/**
		 * Don't do anything when in the Customizer.
		 */
		global $wp_customize;
		if ( $wp_customize ) {
			return;
		}

		add_action( 'init', array( $this, 'update_installation' ) );

	}

	/**
	 * Make sure there's only 1 instance of this class running.
	 *
	 * @static
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Avada_Upgrade();
		}
		return self::$instance;
	}

	/**
	 * Get the previous theme version from database.
	 *
	 * @access public
	 * @return string The previous theme version.
	 */
	public function get_previous_theme_version() {
		if ( is_array( $this->previous_theme_versions ) && ! empty( $this->previous_theme_versions ) ) {
			$this->previous_theme_version = end( $this->previous_theme_versions );
			reset( $this->previous_theme_versions );
		} else {
			$this->previous_theme_version = $this->previous_theme_versions;
		}

		// Make sure the theme version has 3 digits.
		return Avada_Helper::normalize_version( $this->previous_theme_version );
	}

	/**
	 * Actions to run on a fresh installation.
	 */
	public function fresh_installation() {
		update_option( 'avada_version', $this->current_theme_version );
	}

	/**
	 * Actions to run on an update installation.
	 *
	 * @param  bool $skip400 Skips the migration to 4.0 if set to true.
	 */
	public function update_installation( $skip400 = false ) {
		global $current_user;

		$this->current_user = $current_user;

		$this->debug();

		if ( version_compare( $this->current_theme_version, $this->database_theme_version, '>' ) ) {
			// Delete the update notice dismiss flag, so that the flag is reset.
			if ( ! $skip400 ) {
				delete_user_meta( $this->current_user->ID, 'avada_pre_385_notice' );
				delete_user_meta( $this->current_user->ID, 'avada_update_notice' );

				// Delete the TGMPA update notice dismiss flag, so that the flag is reset.
				delete_user_meta( $this->current_user->ID, 'tgmpa_dismissed_notice_tgmpa' );
			}

			$this->update_version();

		}

		// Hook the dismiss notice functionality.
		if ( ! $skip400 ) {
			add_action( 'admin_init', array( $this, 'notices_action' ) );
		}

		// Show upgrade notices.
		if ( version_compare( $this->current_theme_version, '5.1.0', '<=' ) ) {
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
		}
	}

	/**
	 * Update the avada version in the database and reset flags.
	 */
	public function update_version() {
		if ( version_compare( $this->current_theme_version, $this->database_theme_version, '>' ) ) {
			// Update the stored theme versions.
			update_option( 'avada_version', $this->current_theme_version );
			if ( $this->previous_theme_versions ) {
				if ( is_array( $this->previous_theme_versions ) ) {
					$versions_array   = $this->previous_theme_versions;
					$versions_array[] = $this->database_theme_version;
				} else {
					$versions_array = array(
						$this->previous_theme_versions,
					);
				}
			} else {
				$versions_array = array(
					$this->database_theme_version,
				);
			}

			update_option( 'avada_previous_version', $versions_array );
		}
	}

	/**
	 * Notices that will show to users that upgrade
	 */
	public function upgrade_notice() {
		/* Check that the user hasn't already clicked to ignore the message */
		if ( $this->previous_theme_version && current_user_can( 'edit_theme_options' ) && ! get_user_meta( $this->current_user->ID, 'avada_update_notice', true ) ) {
			echo '<div class="updated error fusion-upgrade-notices">';
			if ( version_compare( $this->previous_theme_version, '3.8.5', '<' ) ) {
				?>
				<p><strong>The following important changes were made to Avada 3.8.5:</strong></p>
				<ol>
					<li><strong>CHANGED:</strong> Sidebar, Footer and Sliding Bar widget title HTML tag is changed from h3 to h4 for SEO improvements.</li>
					<li><strong>DEPRECATED:</strong> Icon Flip shortcode option was deprecated from flip boxes, content boxes and fontawesome shortcode. Alternatively, you can use the icon rotate option.</li>
				</ol>
				<?php
			}
			if ( version_compare( $this->previous_theme_version, '3.8.6', '<' ) ) {
				?>
				<p><strong>The following important changes were made to Avada 3.8.6:</strong></p>
				<ol>
					<li><strong>DEPRECATED:</strong> Fixed Mode for iPad will be deprecated in Avada 3.8.7. Fixed Mode will be moved into a plugin.</li>
					<li><strong>CHANGED:</strong> Titles for "Related Posts" and "Comments" on single post page are changed from H2 to H3 for SEO improvements.</li>
				</ol>
				<?php
			}
			if ( version_compare( $this->previous_theme_version, '3.8.7', '<' ) ) {
				?>
				<p><strong>The following important changes were made to Avada 3.8.7:</strong></p>
				<ol>
					<li><strong>REMOVED:</strong> Fixed Mode for iPad is removed as a theme option. Fixed Mode is moved into a free plugin. <a href="https://theme-fusion.com/avada-doc/fixed-mode-for-ipad-portrait/" target="_blank" rel="noopener noreferrer">Download</a>.</li>
					<li><strong>CHANGED:</strong> The left/right padding for the 100% Width Page Template &amp; 100% Full Width Container Now Applies To Mobile.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Header Content Options -> Side Header Responsive Breakpoint</em></strong> was replaced by <strong>Mobile Header Responsive Breakpoint</strong>. It can now be used to control the side header breakpoint as well as the mobile header break point for top headers.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Menu Options -> Menu Text Align</em></strong> will be followed by header 5. If your menu is no longer in center, please use that option to change the position of the menu.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Search Page -> Search Field Height</em></strong> was removed and combined with the new <strong>Form Input and Select Height</strong> option in the Extra tab. All form inputs and selects can be controlled with the new option.</li>
				</ol>
				<?php
			}
			if ( version_compare( $this->previous_theme_version, '3.9.0', '<' ) ) {
				?>
				<p><strong>The following important changes were made to Avada 3.9:</strong></p>
				<ol>
					<li><strong>CHANGED:</strong> The woo cart / my account dropdown width is now controlled by the dropdown width theme option for main and top menu.</li>
					<li><strong>CHANGED:</strong> The footer center option now allows each column to be fully centered.</li>
				</ol>
				<?php
			}
			if ( version_compare( $this->previous_theme_version, '5.1.0', '<' ) ) {
				?>
				<p><strong>Please view the important update information for Avada 5.1:</strong></p>

				You can view all update information here: <a href="http://theme-fusion.com/avada-doc/install-update/important-update-information/" target="_blank" rel="noopener noreferrer">Important Update Information</a>
				<?php
			}
			?>
			<p>
				<strong>
					<a href="http://theme-fusion.com/avada-documentation/changelog.txt" class="view-changelog button-primary" style="margin-top:1em;" target="_blank" rel="noopener noreferrer"><?php esc_attr_e( 'View Changelog', 'Avada' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'avada_update_notice', '1' ) ); ?>" class="dismiss-notice button-secondary" style="margin:1em 4px 0 4px;"><?php esc_attr_e( 'Dismiss this notice', 'Avada' ); ?></a>
				</strong>
			</p>
			</div>
			<?php
		} // End if().
	}

	/**
	 * Action to take when user clicks on notices button.
	 */
	public function notices_action() {
		// Set update notice dismissal, so that the notice is no longer shown.
		if ( isset( $_GET['avada_update_notice'] ) && sanitize_key( wp_unslash( $_GET['avada_update_notice'] ) ) ) {
			add_user_meta( $this->current_user->ID, 'avada_update_notice', '1', true );
		}
	}

	/**
	 * Debug helper.
	 *
	 * @param  string $setting The setting we're changing.
	 * @param  string $old_value The old value.
	 * @param  string $new_value The new value.
	 */
	private static function upgraded_options_row( $setting = '', $old_value = '', $new_value = '' ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $old_value !== $new_value && '' != $setting ) {
			self::$upgraded_options[ $setting ] = array(
				'old' => $old_value,
				'new' => $new_value,
			);
		}
	}

	/**
	 * Clears the twitter widget transients of the "old" style twitter widgets.
	 *
	 * @since 4.0.0
	 *
	 * @retun void
	 */
	protected static function clear_twitter_widget_transients() {
		global $wpdb;
		$tweet_transients = $wpdb->get_results( "SELECT option_name AS name, option_value AS value FROM $wpdb->options WHERE option_name LIKE '_transient_list_tweets_%'" );

		foreach ( $tweet_transients as $tweet_transient ) {
			delete_transient( str_replace( '_transient_', '', $tweet_transient->name ) );
		}
	}

	/**
	 * Debug helper.
	 *
	 * @param bool $debug_mode Turn debug on/off.
	 */
	private function debug( $debug_mode = false ) {
		if ( $debug_mode ) {
			global $current_user;

			delete_user_meta( $current_user->ID, 'avada_update_notice' );
			delete_option( 'avada_version' );
			update_option( 'avada_version', '5.1' );
			delete_option( 'avada_previous_version' );
			delete_option( Avada::get_option_name() );

			// @codingStandardsIgnoreStart
			var_dump( 'Current Version: ' . Avada::$version );
			var_dump( 'DB Version: ' . get_option( 'avada_version', false ) );
			var_dump( 'Previous Version: ' . get_option( 'avada_previous_version', array() ) );
			var_dump( 'Update Notice: ' . get_user_meta( $current_user->ID, 'avada_update_notice', true ) );
			// @codingStandardsEnd
		}

		return;
	}

}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
