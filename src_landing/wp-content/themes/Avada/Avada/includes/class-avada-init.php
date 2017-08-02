<?php
/**
 * Initializes Avada basic components.
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
 * Initializes Avada basic components.
 */
class Avada_Init {

	/**
	 * Constructor.
	 *
	 * @access  public
	 */
	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'set_builder_status' ), 10 );
		add_action( 'after_setup_theme', array( $this, 'add_theme_supports' ), 10 );
		add_action( 'after_setup_theme', array( $this, 'register_nav_menus' ) );
		add_action( 'after_setup_theme', array( $this, 'add_image_size' ) );

		if ( class_exists( 'BuddyPress' ) && ! Avada_Helper::is_buddypress() ) {
			add_action( 'init', array( $this, 'remove_buddypress_redirection' ), 5 );
		}

		if ( class_exists( 'GF_User_Registration_Bootstrap' ) ) {
			add_action( 'init', array( $this, 'change_gravity_user_registration_priority' ) );
		}

		add_action( 'widgets_init', array( $this, 'widget_init' ) );

		add_action( 'wp', array( $this, 'set_theme_version' ) );

		// Allow shortcodes in widget text.
		add_filter( 'widget_text', 'do_shortcode' );

		add_filter( 'wp_nav_menu_args', array( $this, 'main_menu_args' ), 5 );
		add_action( 'after_switch_theme', array( $this, 'theme_activation' ) );
		add_action( 'switch_theme', array( $this, 'theme_deactivation' ) );

		// Term meta migration for WordPress 4.4.
		add_action( 'avada_before_main_content', array( $this, 'youtube_flash_fix' ) );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

		// Remove post_format from preview link.
		add_filter( 'preview_post_link', array( $this, 'remove_post_format_from_link' ), 9999 );

		add_filter( 'wp_tag_cloud', array( $this, 'remove_font_size_from_tagcloud' ) );

		// Add contact methods for author page.
		add_filter( 'user_contactmethods', array( $this, 'modify_contact_methods' ) );

		if ( ! is_admin() ) {
			add_filter( 'pre_get_posts', array( $this, 'modify_search_filter' ) );
		}

		// Check if we've got a task to remove backup data.
		if ( false !== get_option( 'scheduled_avada_fusionbuilder_migration_cleanups', false ) ) {
			add_action( 'init', array( 'Fusion_Builder_Migrate', 'cleanup_backups' ) );
		}
	}

	/**
	 * Load the theme textdomain.
	 *
	 * @access  public
	 */
	public function load_textdomain() {

		// Path: wp-content/theme/languages/en_US.mo.
		// Path: wp-content/languages/themes/Avada-en_US.mo.
		$loaded = load_theme_textdomain( 'Avada', Avada::$template_dir_path . '/languages' );

		// Path: wp-content/theme/languages/Avada-en_US.mo.
		if ( ! $loaded ) {
			add_filter( 'theme_locale', array( $this, 'change_locale' ), 10, 2 );
			$loaded = load_theme_textdomain( 'Avada', Avada::$template_dir_path . '/languages' );

			// Path: wp-content/theme/languages/avada-en_US.mo.
			// Path: wp-content/languages/themes/avada-en_US.mo.
			if ( ! $loaded ) {
				remove_filter( 'theme_locale', array( $this, 'change_locale' ) );
				add_filter( 'theme_locale', array( $this, 'change_locale_lowercase' ), 10, 2 );
				$loaded = load_theme_textdomain( 'Avada', Avada::$template_dir_path . '/languages' );

				// Path: wp-content/languages/Avada-en_US.mo.
				if ( ! $loaded ) {
					remove_filter( 'theme_locale', array( $this, 'change_locale_lowercase' ) );
					add_filter( 'theme_locale', array( $this, 'change_locale' ), 10, 2 );
					$loaded = load_theme_textdomain( 'Avada', dirname( dirname( Avada::$template_dir_path ) ) . '/languages' );

					// Path: wp-content/languages/themes/avada/en_US.mo.
					if ( ! $loaded ) {
						remove_filter( 'theme_locale', array( $this, 'change_locale' ) );
						load_theme_textdomain( 'Avada', dirname( dirname( Avada::$template_dir_path ) ) . '/languages/themes/avada' );
					}
				}
			}
		}
	}

	/**
	 * Formats the locale.
	 *
	 * @access  public
	 * @param  string $locale The language locale.
	 * @param  string $domain The textdomain.
	 * @return  string
	 */
	public function change_locale( $locale, $domain ) {
		return $domain . '-' . $locale;
	}

	/**
	 * Formats the locale using lowercase characters.
	 *
	 * @access  public
	 * @param  string $locale The language locale.
	 * @param  string $domain The textdomain.
	 * @return  string
	 */
	public function change_locale_lowercase( $locale, $domain ) {
		return strtolower( $domain ) . '-' . $locale;
	}

	/**
	 * Conditionally add theme_support for fusion_builder.
	 *
	 * @access  public
	 */
	public function set_builder_status() {
		$builder_settings = get_option( 'fusion_builder_settings' );

		if ( isset( $builder_settings['enable_builder_ui'] ) && $builder_settings['enable_builder_ui'] ) {
			add_theme_support( 'fusion_builder' );
		}
	}

	/**
	 * Stores the theme version in the options table in the WordPress database.
	 *
	 * @access  public
	 */
	public function set_theme_version() {
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme_obj = wp_get_theme();
			$theme_version = $theme_obj->get( 'Version' );

			if ( $theme_obj->parent_theme ) {
				$template_dir  = basename( Avada::$template_dir_path );
				$theme_obj     = wp_get_theme( $template_dir );
				$theme_version = $theme_obj->get( 'Version' );
			}

			update_option( 'avada_theme_version', $theme_version );
		}

	}

	/**
	 * Add theme_supports.
	 *
	 * @access  public
	 */
	public function add_theme_supports() {

		// Default WP generated title support.
		add_theme_support( 'title-tag' );
		// Default RSS feed links.
		add_theme_support( 'automatic-feed-links' );
		// Default custom header.
		add_theme_support( 'custom-header' );
		// Default custom backgrounds.
		add_theme_support( 'custom-background' );
		// Woocommerce Support.
		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-slider' );

		if ( '1' === Avada()->settings->get( 'enable_woo_gallery_zoom' ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}

		if ( '1' !== Avada()->settings->get( 'disable_woo_gallery' ) ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}

		// Post Formats.
		add_theme_support( 'post-formats', array( 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat' ) );
		// Add post thumbnail functionality.
		add_theme_support( 'post-thumbnails' );

	}

	/**
	 * Add image sizes.
	 *
	 * @access  public
	 */
	public function add_image_size() {
		add_image_size( 'blog-large', 669, 272, true );
		add_image_size( 'blog-medium', 320, 202, true );
		add_image_size( 'recent-posts', 700, 441, true );
		add_image_size( 'recent-works-thumbnail', 66, 66, true );
		// Image sizes used for grid layouts.
		add_image_size( '200', 200, '', false );
		add_image_size( '400', 400, '', false );
		add_image_size( '600', 600, '', false );
		add_image_size( '800', 800, '', false );
		add_image_size( '1200', 1200, '', false );
	}

	/**
	 * Register navigation menus.
	 *
	 * @access  public
	 */
	public function register_nav_menus() {

		register_nav_menu( 'main_navigation', 'Main Navigation' );
		register_nav_menu( 'top_navigation', 'Top Navigation' );
		register_nav_menu( 'mobile_navigation', 'Mobile Navigation' );
		register_nav_menu( '404_pages', '404 Useful Pages' );
		register_nav_menu( 'sticky_navigation', 'Sticky Header Navigation' );

	}

	/**
	 * Theme activation actions.
	 *
	 * @access  public
	 */
	public function theme_activation() {

		update_option( 'shop_catalog_image_size',   array(
			'width' => 500,
			'height' => '',
			0,
		) );
		update_option( 'shop_single_image_size',    array(
			'width' => 700,
			'height' => '',
			0,
		) );
		update_option( 'shop_thumbnail_image_size', array(
			'width' => 120,
			'height' => '',
			0,
		) );
		// Delete the patcher caches.
		delete_site_transient( 'fusion_patcher_check_num' );
		// Delete compiled JS.
		avada_reset_all_cache();

	}

	/**
	 * Theme activation actions.
	 *
	 * @access  public
	 */
	public function theme_deactivation() {

		// Delete the patcher caches.
		delete_site_transient( 'fusion_patcher_check_num' );
		// Delete compiled JS.
		avada_reset_all_cache();

	}

	/*
	// WIP
	public function migrate_term_data() {
		$version = get_bloginfo( 'version' );
		$function_test = function_exists( 'add_term_meta' );

		if ( version_compare( $version, '4.4', '>=' ) && ! $function_test ) {}
	}
	*/

	/**
	 * Get the main menu arguments.
	 *
	 * @access public
	 * @param  array $args The arguments.
	 * @return  array The arguments modified.
	 */
	public function main_menu_args( $args ) {

		global $post;

		$c_page_id = Avada()->fusion_library->get_page_id();

		if ( get_post_meta( $c_page_id, 'pyre_displayed_menu', true ) &&
			'default' !== get_post_meta( $c_page_id, 'pyre_displayed_menu', true ) &&
			( 'main_navigation' === $args['theme_location'] || 'sticky_navigation' === $args['theme_location'] )
		) {
			$menu = get_post_meta( $c_page_id, 'pyre_displayed_menu', true );
			$args['menu'] = $menu;
		}

		return $args;

	}

	/**
	 * Inject some HTML to fix a youtube flash bug.
	 *
	 * @access  public
	 */
	public function youtube_flash_fix() {
		echo '<div class="fusion-youtube-flash-fix">&shy;<style type="text/css"> iframe { visibility: hidden; opacity: 0; } </style></div>';
	}

	/**
	 * Register widgets.
	 *
	 * @access  public
	 */
	public function widget_init() {

		register_widget( 'Fusion_Widget_Ad_125_125' );
		register_widget( 'Fusion_Widget_Contact_Info' );
		register_widget( 'Fusion_Widget_Tabs' );
		register_widget( 'Fusion_Widget_Recent_Works' );
		register_widget( 'Fusion_Widget_Tweets' );
		register_widget( 'Fusion_Widget_Flickr' );
		register_widget( 'Fusion_Widget_Social_Links' );
		register_widget( 'Fusion_Widget_Facebook_Page' );
		register_widget( 'Fusion_Widget_Menu' );

	}

	/**
	 * Removes the post format from links.
	 *
	 * @access  public
	 * @param  string $url The URL to process.
	 * @return  string The URL with post_format stripped.
	 */
	public function remove_post_format_from_link( $url ) {
		$url = remove_query_arg( 'post_format', $url );
		return $url;
	}

	/**
	 * Removes font-sizes from the tagclouds.
	 *
	 * @param string $tagcloud The markup of tagclouds.
	 * @return string
	 */
	public function remove_font_size_from_tagcloud( $tagcloud ) {
		return preg_replace( '/ style=(["\'])[^\1]*?\1/i', '', $tagcloud, -1 );
	}

	/**
	 * Modifies user contact methods and adds some more social networks.
	 *
	 * @param array $profile_fields The profile fields.
	 * @return array The profile fields with additional contact methods.
	 */
	public function modify_contact_methods( $profile_fields ) {
		// Add new fields.
		$profile_fields['author_email'] = 'Email (Author Page)';
		$profile_fields['author_facebook'] = 'Facebook (Author Page)';
		$profile_fields['author_twitter']  = 'Twitter (Author Page)';
		$profile_fields['author_linkedin'] = 'LinkedIn (Author Page)';
		$profile_fields['author_dribble']  = 'Dribble (Author Page)';
		$profile_fields['author_gplus']    = 'Google+ (Author Page)';
		$profile_fields['author_custom']   = 'Custom Message (Author Page)';

		return $profile_fields;
	}

	/**
	 * Removes the BuddyPress redirection actions.
	 *
	 * @access public
	 */
	public function remove_buddypress_redirection() {
		remove_action( 'bp_init', 'bp_core_wpsignup_redirect' );
	}

	/**
	 * Changes the hook priority of the GF_User_Registration->maybe_activate_user() function.
	 *
	 * @since 5.1
	 * @access public
	 * @return void
	 */
	public function change_gravity_user_registration_priority() {
		remove_action( 'wp', array( gf_user_registration(), 'maybe_activate_user' ) );
		add_action( 'wp', array( gf_user_registration(), 'maybe_activate_user' ), 999 );
	}


	/**
	 * Modifies the search filter.
	 *
	 * @param object $query The search query.
	 * @return object $query The modified search query.
	 */
	function modify_search_filter( $query ) {
		if ( is_search() && $query->is_search ) {
			if ( isset( $_GET ) && ( 2 < count( $_GET ) || ( 2 == count( $_GET ) && ! isset( $_GET['lang'] ) ) ) ) {
				return $query;
			}

			$search_content = Avada()->settings->get( 'search_content' );

			if ( 'all_post_types_no_pages' === $search_content ) {
				$query->set( 'post_type', array( 'post', 'avada_portfolio', 'product', 'tribe_events' ) );
			} elseif ( 'Only Posts' === $search_content ) {
				$query->set( 'post_type', 'post' );
			} elseif ( 'portfolio_items' === $search_content ) {
				$query->set( 'post_type', 'avada_portfolio' );
			} elseif ( 'Only Pages' === $search_content ) {
				$query->set( 'post_type', 'page' );
			} elseif ( 'woocommerce_products' === $search_content ) {
				$query->set( 'post_type', 'product' );
			} elseif ( 'tribe_events' === $search_content ) {
				$query->set( 'post_type', 'tribe_events' );
			}
		}
		return $query;
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
