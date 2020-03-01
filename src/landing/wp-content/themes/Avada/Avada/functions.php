<?php
/**
 * Extra files & functions are hooked here.
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package Avada
 * @subpackage Core
 * @since 1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

define( 'AVADA_VERSION', '5.2.1' );

/**
 * Include Fusion-Library.
 */
include_once wp_normalize_path( get_template_directory() . '/includes/lib/fusion-library.php' );

/**
 * Include the main Avada class.
 */
include_once wp_normalize_path( get_template_directory() . '/includes/class-avada.php' );

/**
 * Define basic properties in the Avada class.
 */
Avada::$template_dir_path   = wp_normalize_path( get_template_directory() );
Avada::$template_dir_url    = get_template_directory_uri();
Avada::$stylesheet_dir_path = wp_normalize_path( get_stylesheet_directory() );
Avada::$stylesheet_dir_url  = get_stylesheet_directory_uri();

/**
 * Include the autoloader.
 */
include_once Avada::$template_dir_path . '/includes/class-avada-autoload.php';

/**
 * Instantiate the autoloader.
 */
new Avada_Autoload();

/**
 * Must-use Plugins.
 */
include_once Avada::$template_dir_path . '/includes/plugins/multiple_sidebars.php';
require_once Avada::$template_dir_path . '/includes/plugins/post-link-plus.php';

/**
 * If Fusion-Builder is installed, add the options.
 */
if ( ( defined( 'FUSION_BUILDER_PLUGIN_DIR' ) && is_admin() ) || ! is_admin() ) {
	new Fusion_Builder_Redux_Options();
}

/**
 * Load Fusion functions and make them available for later usage.
 */
require_once Avada::$template_dir_path . '/includes/fusion-functions.php';

/**
 * Make sure the Fusion_Multilingual class has been instantiated.
 */
if ( ! property_exists( $fusion_library, 'multilingual' ) || ! $fusion_library->multilingual ) {
	$fusion_library->multilingual = new Fusion_Multilingual();
}

/**
 * Instantiates the Avada_Options class.
 */
function avada_init_options() {
	Avada::$options = Avada_Options::get_instance();
}

// When in the dashboard delay the instantiation of the Avada_Options class.
// This helps put all sidebars (both default & custom) in the Theme Options panel.
if ( is_admin() ) {
	// Has to be widgets_init hook, as it is called before init with priority 1.
	add_action( 'init', 'avada_init_options', 1 );
} else {
	avada_init_options();
}

/**
 * Instantiate Avada_Upgrade classes.
 * Don't instantiate the class when DOING_AJAX to avoid issues
 * with the WP HeartBeat API.
 */
if ( ! fusion_doing_ajax() ) {
	Avada_Upgrade::get_instance();
}

/**
 * Instantiates the Avada class.
 * Make sure the class is properly set-up.
 * The Avada class is a singleton
 * so we can directly access the one true Avada object using this function.
 *
 * @return object Avada
 */
// @codingStandardsIgnoreLine
function Avada() {
	return Avada::get_instance();
}

/**
 * Instantiate the Avada_Admin class.
 * We need this both in the front & back to make sure the admin menu is properly added.
 */
new Avada_Admin();

/**
 * Instantiate the Avada_Multiple_Featured_Images object.
 */
new Avada_Multiple_Featured_Images();

/**
 * Instantiate Avada_Sidebars.
 */
new Avada_Sidebars();

/**
 * Instantiate Avada_Portfolio.
 * This is only needed on the frontend, doesn't do anything for the dashboard.
 */
if ( ! is_admin() ) {
	new Avada_Portfolio();
}

/**
 * Instantiate Avada_Social_Icons.
 * This is only needed on the frontend, doesn't do anything for the dashboard.
 */
global $social_icons;
if ( ! is_admin() ) {
	$social_icons = new Avada_Social_Icons();
}

/**
 * Instantiate Avada_fonts.
 * Only do this while in the dashboard, not needed on the frontend.
 */
if ( is_admin() ) {
	new Avada_Fonts();
}

/**
 * Instantiate Avada_Scripts.
 */
new Avada_Scripts();

/**
 * Instantiate Avada_Layout_bbPress.
 * We only need to do this for the frontend, when bbPress is installed.
 */
if ( ! is_admin() && class_exists( 'bbPress' ) ) {
	new Avada_Layout_bbPress();
}

/**
 * Instantiate Avada_EventsCalendar
 * We only need to do this on the frontend if Events Calendar is installed
 */
if ( ! is_admin() && class_exists( 'Tribe__Events__Main' ) ) {
	new Avada_EventsCalendar();
}

/**
 * Conditionally Instantiate Avada_AvadaRedux.
 */
$load_avadaredux = false;
if ( is_admin() && isset( $_GET['page'] ) && 'avada_options' === $_GET['page'] ) {
	$load_avadaredux = true;
}
$http_referer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
if ( false !== strpos( $http_referer, 'avada_options' ) ) {
	$load_avadaredux = true;
}

if ( $load_avadaredux ) {
	new Avada_AvadaRedux( array(
		'is_language_all'      => Avada::get_language_is_all(),
		'option_name'          => Avada::get_option_name(),
		'original_option_name' => Avada::get_original_option_name(),
		'version'              => Avada()->get_theme_version(),
		'textdomain'           => 'Avada',
		'disable_dependencies' => (bool) ( '0' === Avada()->settings->get( 'dependencies_status' ) ),
		'display_name'         => 'Avada',
		'menu_title'           => __( 'Theme Options', 'Avada' ),
		'page_title'           => __( 'Theme Options', 'Avada' ),
		'global_variable'      => 'fusion_fusionredux_options',
		'page_parent'          => 'themes.php',
		'page_slug'            => 'avada_options',
		'menu_type'            => 'submenu',
		'page_permissions'     => 'edit_theme_options',
	) );
} elseif ( ! is_admin() ) {
	new Avada_Google_Fonts();
}

/*
 * Include the TGM configuration
 * We only need this while on the dashboard.
 */
if ( is_admin() ) {
	require_once Avada::$template_dir_path . '/includes/class-tgm-plugin-activation.php';
	require_once Avada::$template_dir_path . '/includes/avada-tgm.php';
}

/*
 * Include deprecated functions
 */
require_once Avada::$template_dir_path . '/includes/deprecated.php';

/**
 * Metaboxes
 */
if ( is_admin() ) {
	include_once Avada::$template_dir_path . '/includes/metaboxes/metaboxes.php';
}

/**
 * Instantiate the mega menu framework
 */
$mega_menu_framework = new Avada_Megamenu_Framework();

/**
 * Custom Functions
 */
get_template_part( 'includes/custom_functions' );
require_once Avada::$template_dir_path . '/includes/avada-functions.php';

/**
 * WPML Config
 */
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	include_once Avada::$template_dir_path . '/includes/plugins/wpml.php';
}

/**
 * Include the importer
 */
if ( is_admin() ) {
	include Avada::$template_dir_path . '/includes/importer/importer.php';
}

/**
 * Load Woocommerce Configuraion.
 */
if ( class_exists( 'WooCommerce' ) ) {
	include_once Avada::$template_dir_path . '/includes/wc-functions.php';
	global $avada_woocommerce;
	$avada_woocommerce = new Avada_Woocommerce();
}

/**
 * The dynamic CSS.
 */
require_once Avada::$template_dir_path . '/includes/dynamic_css.php';
require_once Avada::$template_dir_path . '/includes/dynamic_css_helpers.php';
global $avada_dynamic_css;
$avada_dynamic_css = new Avada_Dynamic_CSS();

// Load dynamic css for plugins.
foreach ( glob( Avada::$template_dir_path . '/includes/typography/*.php', GLOB_NOSORT ) as $filename ) {
	require_once wp_normalize_path( $filename );
}

/**
 * Set the $content_width global.
 */
global $content_width;
if ( ! is_admin() && ( ! isset( $content_width ) || empty( $content_width ) ) ) {
	$content_width = (int) Avada()->layout->get_content_width();
}

/**
 * Font-Awesome icon handler.
 * Adds compatibility with order versions of FA icon names.
 *
 * @param  string $icon The icon-name.
 * @return  string
 */
function avada_font_awesome_name_handler( $icon ) {
	$old_icons = Fusion_Data::old_icons();
	$fa_icon   = ( 'fa-' !== substr( $icon, 0, 3 ) ) ? 'fa-' . $icon : $icon;
	if ( 'icon-' === substr( $icon, 0, 5 ) || 'fa=' !== substr( $icon, 0, 3 ) ) {
		// Replace old prefix with new one.
		$icon    = str_replace( 'icon-', 'fa-', $icon );
		$fa_icon = ( 'fa-' !== substr( $icon, 0, 3 ) ) ? 'fa-' . $icon : $icon;
		if ( array_key_exists( str_replace( 'fa-', '', $icon ), $old_icons ) ) {
			$fa_icon = 'fa-' . $old_icons[ str_replace( 'fa-', '', $icon ) ];
		}
	}
	return $fa_icon;
}

/**
 * Adds a counter span element to links.
 *
 * @param string $links The links HTML string.
 */
function avada_cat_count_span( $links ) {
	preg_match_all( '#\((.*?)\)#', $links, $matches );
	if ( ! empty( $matches ) ) {
		$i = 0;
		foreach ( $matches[0] as $val ) {
			$links = str_replace( '</a> ' . $val, ' ' . $val . '</a>', $links );
			$links = str_replace( '</a>&nbsp;' . $val, ' ' . $val . '</a>', $links );
			$i++;
		}
	}
	return $links;
}
add_filter( 'get_archives_link', 'avada_cat_count_span' );
add_filter( 'wp_list_categories', 'avada_cat_count_span' );

/**
 * Modify admin CSS.
 */
function avada_custom_admin_styles() {
	echo '<style type="text/css">.widget input { border-color: #DFDFDF !important; }</style>';
}
add_action( 'admin_head', 'avada_custom_admin_styles' );

/**
 * Add admin messages.
 */
function avada_admin_notice() {
	?>

	<?php if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) : ?>
		<div id="low-php-version-error" class="notice notice-error">
			<?php /* translators: Link to WordPress requirements page. */ ?>
			<p><?php printf( esc_attr__( 'Your server runs on an old version of PHP 5.2. It is recommended you update to %s or greater.', 'Avada' ), '<a href="https://wordpress.org/about/requirements/" target="_blank">PHP 7</a>' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['imported'] ) && 'success' === $_GET['imported'] ) : ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><?php esc_attr_e( 'Sucessfully imported demo data!', 'Avada' ); ?></p>
		</div>
	<?php endif; ?>
	<?php
}
add_action( 'admin_notices', 'avada_admin_notice' );

/**
 * Ignore nag messages.
 */
function avada_nag_ignore() {
	global $current_user;
	$user_id = $current_user->ID;

	// If user clicks to ignore the notice, add that to their user meta.
	if ( isset( $_GET['fusion_richedit_nag_ignore'] ) && '0' == $_GET['fusion_richedit_nag_ignore'] ) {
		add_user_meta( $user_id, 'fusion_richedit_nag_ignore', 'true', true );
	}

	// If user clicks to ignore the notice, add that to their user meta.
	if ( isset( $_GET['avada_uber_nag_ignore'] ) && '0' == $_GET['avada_uber_nag_ignore'] ) {
		update_option( 'avada_ubermenu_notice', true );
		update_option( 'avada_ubermenu_notice_hidden', true );
		$referer = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		wp_safe_redirect( $referer );
	}
}
add_action( 'admin_init', 'avada_nag_ignore' );

if ( function_exists( 'rev_slider_shortcode' ) ) {
	add_action( 'admin_init', 'avada_disable_revslider_notice' );
	add_action( 'admin_init', 'avada_revslider_styles' );
}

/**
 * Disable revslider notice.
 */
function avada_disable_revslider_notice() {
	update_option( 'revslider-valid-notice', 'false' );
}

/**
 * Woo Products Shortcode Recode.
 *
 * @param  array $atts The attributes.
 * @return string
 */
function avada_woo_product( $atts ) {
	global $woocommerce_loop;

	if ( empty( $atts ) ) {
		return;
	}

	$args = array(
		'post_type'       => 'product',
		'posts_per_page'  => 1,
		'no_found_rows'   => 1,
		'post_status'     => 'publish',
		'columns'         => 1,
	);

	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
		$args['meta_query'][] = array(
				'key'     => '_visibility',
				'value'   => array( 'catalog', 'visible' ),
				'compare' => 'IN',
			);
	} else {
		$args['tax_query'][] = WC()->query->get_tax_query();
	}

	if ( isset( $atts['sku'] ) && '' !== $atts['sku'] ) {
		$args['meta_query'][] = array(
			'key'     => '_sku',
			'value'   => $atts['sku'],
			'compare' => '=',
		);
	}

	if ( isset( $atts['id'] ) ) {
		$args['p'] = $atts['id'];
	}

	ob_start();

	if ( isset( $args['columns'] ) && 1 < $args['columns'] ) {
		$woocommerce_loop['columns'] = $args['columns'];
	}

	$products = fusion_cached_query( $args );

	if ( $products->have_posts() ) : ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php fusion_wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // End of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	<?php endif;

	wp_reset_postdata();

	return '<div class="woocommerce">' . ob_get_clean() . '</div>';
}

/**
 * Changes the default WooCommerce product shortcode
 * with a customized Avada version.
 */
function remove_product_shortcode() {
	if ( class_exists( 'WooCommerce' ) ) {
		// First remove the shortcode.
		remove_shortcode( 'product' );
		// Then recode it.
		add_shortcode( 'product', 'avada_woo_product' );
	}
}
add_action( 'wp_loaded', 'remove_product_shortcode' );

/**
 * Support email login on my account dropdown.
 */
// @codingStandardsIgnoreLine
if ( isset( $_POST['fusion_woo_login_box'] ) && 'true' === $_POST['fusion_woo_login_box'] ) {
	add_filter( 'authenticate', 'avada_email_login_auth', 10, 3 );
}

/**
 * Allow loging-in via email.
 *
 * @param  object $user     The user.
 * @param  string $username The username.
 * @param  string $password The password.
 */
function avada_email_login_auth( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) ) {
		return $user;
	}

	if ( ! empty( $username ) ) {
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status ) {
			$username = $user->user_login;
		}
	}

	return wp_authenticate_username_password( null, $username, $password );
}

/**
 * No redirect on woo my account dropdown login when it fails.
 */
// @codingStandardsIgnoreLine
if ( isset( $_POST['fusion_woo_login_box'] ) && 'true' === $_POST['fusion_woo_login_box'] ) {
	add_action( 'init', 'avada_load_login_redirect_support' );
}

/**
 * Tweaks the login redirect for WooCommerce.
 */
function avada_load_login_redirect_support() {
	if ( class_exists( 'WooCommerce' ) ) {

		// When on the my account page, do nothing.
		if ( ! empty( $_POST['login'] ) ) {
			if ( ! isset( $_POST['_wpnonce'] ) && empty( $_POST['_wpnonce'] ) ) {
				// @codingStandardsIgnoreLine
				$nonce = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
				if ( wp_verify_nonce( $nonce, 'woocommerce-login' ) ) {
					return;
				}
			}
		}

		add_action( 'login_redirect', 'avada_login_fail', 10, 3 );
	}
}

/**
 * Avada Login Fail Test.
 *
 * @param  string $url     The URL.
 * @param  string $raw_url The Raw URL.
 * @param  string $user    User.
 * @return string
 */
function avada_login_fail( $url = '', $raw_url = '', $user = '' ) {
	if ( ! is_account_page() ) {

		if ( isset( $_SERVER ) && isset( $_SERVER['HTTP_REFERER'] ) && esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) {
			$referer_array = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );
			$parsed_url    = ( isset( $_SERVER['SERVER_PORT'] ) ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['SERVER_PORT'] ) ) ) : array(
				'host' => '80',
			);

			// Make sure it works ok for ports other than 80.
			$port = ( isset( $_SERVER['SERVER_PORT'] ) ) ? ':' . $parsed_url['host'] : ':80';
			$port = ( ':80' === $port ) ? '' : $port;

			// Make sure host doesn't have a trailing slash and append the port.
			$host = untrailingslashit( $referer_array['host'] ) . $port;

			// Make sure path has a slash at the beginning.
			$path = $referer_array['path'];
			if ( 0 !== strpos( $referer_array['path'], '/' ) ) {
				$path = '/' . $referer_array['path'];
			}

			// Combine the above to a $referer.
			if ( false !== strpos( $port, '443' ) ) {
				$referer = 'https://' . $host . $path;
			} else {
				$referer = '//' . $host . $path;
			}

			// If there's a valid referrer, and it's not the default log-in screen.
			if ( ! empty( $referer ) && ! strstr( $referer, 'wp-login' ) && ! strstr( $referer, 'wp-admin' ) ) {
				if ( is_wp_error( $user ) ) {
					// Let's append some information (login=failed) to the URL for the theme to use.
					wp_safe_redirect( add_query_arg( array(
						'login' => 'failed',
					), $referer ) );
				} else {
					wp_safe_redirect( $referer );
				}
				exit;
			}
		} // End if().
		return $url;
	} // End if().
}

/**
 * Show a shop page description on product archives.
 */
function woocommerce_product_archive_description() {
	if ( is_post_type_archive( 'product' ) && 0 == get_query_var( 'paged' ) ) {
		$shop_page = get_post( fusion_wc_get_page_id( 'shop' ) );
		if ( $shop_page ) {
			$description = apply_filters( 'the_content', $shop_page->post_content );
			if ( $description ) {
				echo '<div class="post-content">' . $description . '</div>'; // WPCS: XSS ok.
			}
		}
	}
}

/**
 * Layerslider API.
 */
function avada_layerslider_ready() {
	if ( class_exists( 'LS_Sources' ) ) {
		LS_Sources::addSkins( Avada::$template_dir_path . '/includes/ls-skins' );
	}
	if ( defined( 'LS_PLUGIN_BASE' ) ) {
		remove_action( 'after_plugin_row_' . LS_PLUGIN_BASE, 'layerslider_plugins_purchase_notice', 10, 3 );
	}
}
add_action( 'layerslider_ready', 'avada_layerslider_ready' );

/**
 * Istantiate the auto-patcher tool.
 */
global $avada_patcher;
$avada_patcher = new Fusion_Patcher( array(
	'context'     => 'avada',
	'version'     => Avada::get_theme_version(),
	'name'        => 'Avada',
	'parent_slug' => 'avada',
	'page_title'  => esc_attr__( 'Fusion Patcher', 'Avada' ),
	'menu_title'  => esc_attr__( 'Fusion Patcher', 'Avada' ),
	'classname'   => 'Avada',
	'bundled'     => array(
		'fusion-builder',
		'fusion-core',
	),
) );

/**
 * During updates sometimes there are changes that will break a site.
 * We're adding a maintenance page to make sure users don't see a broken site.
 * As soon as the update is complete the site automatically returns to normal mode.
 */
$maintenance   = false;
$users_message = esc_html__( 'Our site is currently undergoing scheduled maintenance. Please try again in a moment.', 'Avada' );
// Check if we're currently update Avada.
if ( Avada::$is_updating ) {
	$maintenance   = true;
	$admin_message = esc_html__( 'Currently updating the Avada Theme. Your site will be accessible once the update finishes', 'Avada' );
}

/**
 * Make sure that if the fusion-core plugin is activated,
 * it's at least version 2.0.
 */
if ( class_exists( 'FusionCore_Plugin' ) ) {
	$fc_version = FusionCore_Plugin::VERSION;
	if ( version_compare( $fc_version, '2.0', '<' ) ) {
		$maintenance   = true;
		/* translators: The "follow this link" link. */
		$admin_message = sprintf( esc_attr__( 'The Fusion-Core plugin needs to be updated before your site can exit maintenance mode. Please %s to update the plugin.', 'Avada' ), '<a href="' . admin_url( 'themes.php?page=install-required-plugins' ) . '" style="color:#0088cc;font-weight:bold;">' . esc_attr__( 'follow this link', 'Avada' ) . '</a>' );
	}
}

/**
 * If we're on maintenance mode, show the screen.
 */
if ( $maintenance ) {
	new Avada_Maintenance( true, $users_message, $admin_message );
}

/**
 * Class for adding Avada specific data to builder.
 * These only affect the dashboard so are not needed when in the front-end.
 */
if ( Avada_Helper::is_post_admin_screen() && defined( 'FUSION_BUILDER_PLUGIN_DIR' ) && ! fusion_doing_ajax() ) {
	Fusion_Builder_Filters::get_instance();
}

/**
 * Add Fusion Builder Demos support.
 */
add_theme_support( 'fusion-builder-demos' );

/**
 * We will use builder options in Avada, no need for FB to instantiate redux.
 */
add_theme_support( 'fusion-builder-options' );
add_filter( 'fusion_options_label', 'avada_set_options_label' );
add_filter( 'fusion_builder_options_url', 'avada_set_options_url' );


/**
 * Sets options label.
 *
 * @since 5.1
 * @param string $label Label name of options page.
 * @return string
 */
function avada_set_options_label( $label ) {
	return esc_html( 'Theme Options', 'Avada' );
}

/**
 * Set options page URL.
 *
 * @since 5.1
 * @param string $url URL to the options page.
 * @return string
 */
function avada_set_options_url( $url ) {
	return admin_url( 'themes.php?page=avada_options' );
}

if ( Avada()->registration->is_registered() && Avada_Helper::is_post_admin_screen() && defined( 'FUSION_BUILDER_PLUGIN_DIR' ) && ( ! fusion_doing_ajax() || isset( $_POST['page_name'] ) ) ) {
	$fusion_builder_demo_importer = new Fusion_Builder_Demos_Importer();
}

/**
 * Filter a sanitized key string.
 *
 * @since 5.0.2
 * @param string $key     Sanitized key.
 * @param string $raw_key The key prior to sanitization.
 * @return string
 */
function avada_auto_update( $key, $raw_key ) {
	return ( 'avada' === $key && 'Avada' === $raw_key ) ? $raw_key : $key;
}

/**
 * Check if doing an ajax theme update,
 * if so make sure Avada theme name is not changed to lowercase.
 */
if ( fusion_doing_ajax() && isset( $_POST['action'] ) && 'update-theme' === $_POST['action'] ) {
	add_filter( 'sanitize_key', 'avada_auto_update', 10, 2 );
}

require_once Avada::$template_dir_path . '/includes/plugins/jetpack/class-jetpack-user-agent.php';

/**
 * Make sure language-all works correctly.
 * Uses Fusion_Multilingual action.
 *
 * @since 5.1
 */
function avada_set_language_is_all() {
	Avada::set_language_is_all( true );
}
add_action( 'fusion_library_set_language_is_all', 'avada_set_language_is_all' );

/**
 * Include Fusion Builder shared options support.
 */
if ( class_exists( 'FusionBuilder' ) ) {
	include_once Avada::$template_dir_path . '/includes/fusion-shared-options.php';
}

/**
 * Reset all cache on theme activation.
 *
 * @since 5.1
 */
function avada_reset_all_cache() {
	// Reset fusion-caches.
	if ( ! class_exists( 'Fusion_Cache' ) ) {
		include_once Avada::$template_dir_path . '/includes/lib/inc/class-fusion-cache.php';
	}

	$fusion_cache = new Fusion_Cache();
	$fusion_cache->reset_all_caches();
}

/**
 * Wrapper function for wp_doing_ajax, which was introduced in WP 4.7.
 *
 * @since 5.1.5
 */
function fusion_doing_ajax() {
	if ( function_exists( 'wp_doing_ajax' ) ) {
		return wp_doing_ajax();
	}

	return defined( 'DOING_AJAX' ) && DOING_AJAX;
}

// WIP, please ignore below.
if ( 'true' === get_option( 'avada_imported_demo' ) ) {
	flush_rewrite_rules();
	update_option( 'avada_imported_demo', 'false' );
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
