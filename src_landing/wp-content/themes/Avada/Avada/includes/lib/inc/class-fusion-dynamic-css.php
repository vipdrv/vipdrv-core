<?php
/**
 * Dynamic-CSS handler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle generating the dynamic CSS.
 *
 * @since 1.0.0
 */
class Fusion_Dynamic_CSS {

	/**
	 * The one, true instance of this object.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var null|object
	 */
	protected static $instance = null;

	/**
	 * The mode we'll be using (file/inline).
	 *
	 * @access public
	 * @since 1.0.0
	 * @var string
	 */
	public $mode;

	/**
	 * An object containing helper methods.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var null|object Fusion_Dynamic_CSS_Helpers
	 */
	private $helpers = null;

	/**
	 * An instance of the Fusion_Dynamic_CSS_Inline class.
	 * null if we're not using inline mode.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var null|object Fusion_Dynamic_CSS_Inline
	 */
	public $inline = null;

	/**
	 * An instance of the Fusion_Dynamic_CSS_File class.
	 * null if we're not using file mode.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var null|object Fusion_Dynamic_CSS_File
	 */
	protected $file = null;

	/**
	 * Needs update?
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @var bool
	 */
	public static $needs_update = false;

	/**
	 * Disable cache?
	 * Used in special cases, for example Avada is active but WPtouch plugin is used.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var bool
	 */
	protected static $disable_cache = null;

	/**
	 * An array of extra files that we want to add in our CSS.
	 *
	 * @static
	 * @access private
	 * @since 1.0.0
	 * @var array
	 */
	private static $extra_files = array();

	/**
	 * Constructor.
	 *
	 * @access protected
	 * @since 1.0.0
	 */
	protected function __construct() {

		if ( null === $this->helpers ) {
			$this->helpers = $this->get_helpers();
		}

		add_action( 'wp', array( $this, 'init' ), 999 );

		// When a post is saved, reset its caches to force-regenerate the CSS.
		add_action( 'save_post', array( $this, 'reset_post_transient' ) );
		add_action( 'save_post', array( $this, 'post_update_option' ) );

		add_action( 'customize_save_after', array( $this, 'reset_all_caches' ) );
		add_filter( 'fusion_dynamic_css', array( $this, 'add_extra_files' ) );
		add_filter( 'fusion_dynamic_css', array( $this, 'icomoon_css' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_extra_files' ), 11 );

		add_action( 'wp', array( $this, 'maintenance' ) );

	}

	/**
	 * Gets the instance of this object.
	 *
	 * @static
	 * @since 1.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add extra actions.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		// Add options.
		$this->add_options();

		// Set the $needs_update property.
		$this->needs_update();

		// Set the $disable_cache property.
		$this->is_cache_disabled();

		// Set mode.
		$this->set_mode();

		if ( 'file' === $this->get_mode() ) {
			$this->file = new Fusion_Dynamic_CSS_File( $this );
			return;
		}
		$this->inline = new Fusion_Dynamic_CSS_Inline( $this );
	}

	/**
	 * Determine if we're using file mode or inline mode.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function set_mode() {

		$this->mode = 'inline';

		// Early exit if on the customizer.
		// This will force-using inline mode.
		global $wp_customize;
		if ( $wp_customize ) {
			return;
		}

		// Make sure Avada is active if being used.
		if ( $this->is_cache_disabled() ) {
			$this->mode = 'inline';
			return;
		}

		// Check if we're using file mode or inline mode.
		// This simply checks the css_cache_method options.
		$settings = Fusion_Settings::get_instance();
		if ( 'file' === $settings->get( 'css_cache_method' ) ) {
			$this->mode = 'file';
		}

		// Additional checks for file mode.
		if ( 'file' === $this->mode && self::$needs_update ) {

			// Only allow processing 1 file every 5 seconds.
			$current_time = (int) time();
			$last_time    = (int) get_option( 'fusion_dynamic_css_time' );
			if ( 5 > ( $current_time - $last_time ) ) {
				$this->mode = 'inline';
				return;
			}
		}
	}

	/**
	 * Gets the mode we're using.
	 *
	 * @access public
	 * @since 1.1.5
	 * @return string
	 */
	public function get_mode() {

		if ( ! $this->mode || null === $this->mode ) {
			$this->set_mode();
		}
		return $this->mode;

	}

	/**
	 * This function takes care of creating the CSS.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string The final CSS.
	 */
	public function make_css() {

		// Creates the content of the CSS file.
		// We're adding a warning at the top of the file to prevent users from editing it.
		// The warning is then followed by the actual CSS content.
		$content = $this->helpers->dynamic_css_cached();
		$content = apply_filters( 'fusion_dynamic_css_final', $content );

		// When using domain-mapping plugins we have to make sure that any references to the original domain
		// are replaced with references to the mapped domain.
		// We're also stripping protocols from these domains so that there are no issues with SSL certificates.
		if ( defined( 'DOMAIN_MAPPING' ) && DOMAIN_MAPPING ) {

			if ( function_exists( 'domain_mapping_siteurl' ) && function_exists( 'get_original_url' ) ) {

				// The mapped domain of the site.
				$mapped_domain = domain_mapping_siteurl( false );
				$mapped_domain = str_replace( array( 'https://', 'http://' ), '//', $mapped_domain );

				// The original domain of the site.
				$original_domain = get_original_url( 'siteurl' );
				$original_domain = str_replace( array( 'https://', 'http://' ), '//', $original_domain );

				// Replace original domain with mapped domain.
				$content = str_replace( $original_domain, $mapped_domain, $content );

			}
		}

		// Strip protocols. This helps avoid any issues with https sites.
		$content = str_replace( array( 'https://', 'http://' ), '//', $content );

		return $content;

	}

	/**
	 * Reset ALL CSS transient caches.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function reset_all_transients() {
		global $wpdb;
		$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_fusion_dynamic_css_%'";
		// @codingStandardsIgnoreLine
		$wpdb->query( $sql );
	}

	/**
	 * Reset the dynamic CSS transient for a post.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int $post_id The ID of the post that's being reset.
	 */
	public function reset_post_transient( $post_id ) {
		delete_transient( 'fusion_dynamic_css_' . $post_id );
	}

	/**
	 * Create settings.
	 *
	 * @access private
	 */
	private function add_options() {
		// The 'fusion_dynamic_css_posts' option will hold an array of posts that have had their css generated.
		// We can use that to keep track of which pages need their CSS to be recreated and which don't.
		add_option( 'fusion_dynamic_css_posts', array(), '', 'yes' );
		// The 'fusion_dynamic_css_time' option holds the time the file writer was last used.
		add_option( 'fusion_dynamic_css_time', time(), '', 'yes' );
	}

	/**
	 * Update the fusion_dynamic_css_posts option when a post is saved.
	 * This adds the current post's ID in the array of IDs that the 'fusion_dynamic_css_posts' option has.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function post_update_option( $post_id ) {
		$option = get_option( 'fusion_dynamic_css_posts', array() );
		$option[ $post_id ] = false;
		update_option( 'fusion_dynamic_css_posts', $option );
	}

	/**
	 * Update the fusion_dynamic_css_posts option when the theme options are saved.
	 * This basically empties the array of page IDs from the 'fusion_dynamic_css_posts' option.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function global_reset_option() {
		update_option( 'fusion_dynamic_css_posts', array() );
	}

	/**
	 * Do we need to update the CSS file?
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function needs_update() {

		global $fusion_library;

		// Get the 'fusion_dynamic_css_posts' option from the DB.
		$option = get_option( 'fusion_dynamic_css_posts', array() );
		// Get the current page ID.
		$page_id = 'global';
		if ( $fusion_library->get_page_id() ) {
			$page_id = $fusion_library->get_page_id();

			// If WooCommerce is active and we are on archive, use global CSS not shop page, which is return by get_page_id.
			if ( class_exists( 'WooCommerce' ) && ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
				$page_id = 'global';
			}
		}

		// If the current page ID exists in the array of pages defined in the 'fusion_dynamic_css_posts' option
		// then the page has already been compiled and we don't need to re-compile it.
		// If it's not in the array then it has not been compiled before so we need to update it.
		if ( ! isset( $option[ $page_id ] ) || ! $option[ $page_id ] ) {
			self::$needs_update = true;
		}

		return self::$needs_update;

	}


	/**
	 * There are special cases when cache should be disabled, for example: Avada is active but WPtouch plugin is used.
	 * In such case all CSS caching should be disabled (and mode set to "inline") in order not to cache CSS which is missing global styles.
	 * Here TextDomain is used, instead of theme Name, so cache is disabled even if a user renames theme.
	 *
	 * @access public
	 * @since 1.1
	 * @return bool
	 */
	public function is_cache_disabled() {

		if ( null === self::$disable_cache ) {
			$theme = wp_get_theme();
			self::$disable_cache = false;
			if ( 'Avada' === $theme->get( 'TextDomain' ) && ! class_exists( 'Avada' ) ) {
				self::$disable_cache = true;
			}
		}

		return self::$disable_cache;
	}

	/**
	 * Update the 'fusion_dynamic_css_time' option.
	 * This will save in the db the last time that the compiler has run.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function update_saved_time() {
		update_option( 'fusion_dynamic_css_time', time() );
	}

	/**
	 * This is just a facilitator that will allow us to reset everything.
	 * Its only job is calling the other methods from this class and reset parts of our caches.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function reset_all_caches() {
		$this->reset_all_transients();
		$this->global_reset_option();
	}

	/**
	 * Get an instance of the Fusion_Dynamic_CSS_Helpers object.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return object Fusion_Dynamic_CSS_Helpers
	 */
	public function get_helpers() {
		// Instantiate the Fusion_Dynamic_CSS_Helpers object.
		if ( null === $this->helpers ) {
			$this->helpers = new Fusion_Dynamic_CSS_Helpers();
		}
		return $this->helpers;
	}

	/**
	 * Makes adding files to the compiled CSS easier.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @param string $path The file path.
	 * @param string $url  The file URL.
	 */
	public static function enqueue_style( $path, $url = '' ) {

		if ( '' === $url ) {
			self::$extra_files[] = $path;
			return;
		}
		self::$extra_files[ $url ] = $path;

	}

	/**
	 * Enqueue extra files.
	 * This is used as a falback in case we can't get the contents of the CSS file.
	 *
	 * @access public
	 * @since 1.0.6
	 */
	public function enqueue_extra_files() {

		// Get the extra files we need to enqueue.
		$extra_assets = get_transient( 'fusion_dynamic_css_extra_files_to_enqueue' );
		$extra_assets = ( ! is_array( $extra_assets ) ) ? array() : $extra_assets;

		// No need to proceed if $extra_assets doesn't have anything.
		if ( empty( $extra_assets ) ) {
			return;
		}

		// If we got this far there are scripts to enqueue.
		foreach ( $extra_assets as $url ) {
			// Early exit if not a string.
			if ( ! is_string( $url ) || is_numeric( $url ) ) {
				continue;
			}
			// Make sure the URL is properly escaped.
			$url = esc_url_raw( $url );

			// The only thing we have available is the url,
			// so we'll simply use md5() to create a unique handle for the script.
			$handle = md5( $url );

			// Enqueue the style.
			wp_enqueue_style( $handle, $url );

		}
	}


	/**
	 * Adds our extra files to the final CSS.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $css The final CSS.
	 * @return string     The final CSS after our extra files have been added.
	 */
	public function add_extra_files( $css ) {
		$extra_files  = array_unique( self::$extra_files );
		$extra_assets = array();

		$wp_filesystem = Fusion_Helper::init_filesystem();
		$files_css  = '';

		foreach ( $extra_files as $url => $path ) {
			// Get the file contents.
			$file_contents = $wp_filesystem->get_contents( $path );
			// If it failed, try file_get_contents().
			if ( ! $file_contents ) {
				// @codingStandardsIgnoreLine
				$file_contents = @file_get_contents( $path );
			}
			if ( $file_contents ) {
				$files_css .= $file_contents;
			} else {
				$extra_assets[] = $url;
			}
		}
		if ( ! empty( $extra_assets ) ) {
			set_transient( 'fusion_dynamic_css_extra_files_to_enqueue', $extra_assets );
		}
		return $files_css . $css;

	}

	/**
	 * Clean up the CSS caches once every few days.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function maintenance() {

		$days = apply_filters( 'fusion_css_compiler_reset_days', 10 );

		// If expired equals false.
		if ( false === get_transient( 'fusion_css_cache_cleanup' ) ) {
			$this->reset_all_caches();

			// Delete cached-css files.
			$upload_dir    = wp_upload_dir();
			$folder_path   = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'fusion-styles';
			$wp_filesystem = Fusion_Helper::init_filesystem();
			$wp_filesystem->delete( $folder_path, true, 'd' );

			// See you again in a few days!
			set_transient( 'fusion_css_cache_cleanup', true, $days * DAY_IN_SECONDS );

		}
	}

	/**
	 * Adds icomoon CSS.
	 *
	 * @access public
	 * @since 1.0.2
	 * @param string $css The original CSS.
	 * @return string The original CSS with the webfont @font-face declaration appended.
	 */
	public function icomoon_css( $css ) {

		$font_url = untrailingslashit( FUSION_LIBRARY_URL ) . '/assets/fonts/icomoon';
		$font_url = set_url_scheme( $font_url );

		$css .= '@font-face {';
		$css .= 'font-family: "icomoon";';
		$css .= "src:url('{$font_url}/icomoon.eot');";
		$css .= "src:url('{$font_url}/icomoon.eot?#iefix') format('embedded-opentype'),";
		$css .= "url('{$font_url}/icomoon.woff') format('woff'),";
		$css .= "url('{$font_url}/icomoon.ttf') format('truetype'),";
		$css .= "url('{$font_url}/icomoon.svg#icomoon') format('svg');";
		$css .= 'font-weight: normal;';
		$css .= 'font-style: normal;';
		$css .= '}';

		return $css;

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
