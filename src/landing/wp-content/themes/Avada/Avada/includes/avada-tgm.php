<?php
/**
 * Plugins for TGM usage.
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
 * Gets all recommended and required plugins for use in TGM plugin.
 *
 * @since 5.1.6
 */
function avada_get_required_and_recommened_plugins() {
	if ( ! class_exists( 'Avada_Importer_Data' ) ) {
		include_once Avada::$template_dir_path . '/includes/importer/class-avada-importer-data.php';
	}

	$is_plugins_page = false;
	if ( ( isset( $_GET['page'] ) && 'avada-plugins' === $_GET['page'] ) ||
		 ( isset( $_GET['page'] ) && 'install-required-plugins' === $_GET['page'] ) ||
		 ( isset( $_SERVER['HTTP_REFERER'] ) && false !== strpos( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 'HTTP_REFERER' ) )
	) {
		$is_plugins_page = true;
	}

	// Get the bundled plugin information.
	$bundled_plugins = Avada()->get_bundled_plugins();

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		$bundled_plugins['fusion_core']['slug'] => array(
			'name'               => $bundled_plugins['fusion_core']['name'],
			'slug'               => $bundled_plugins['fusion_core']['slug'],
			'source'             => ( $is_plugins_page ) ? Avada()->remote_install->get_package( 'Fusion Core' ) : 'bundled',
			'required'           => true,
			'version'            => $bundled_plugins['fusion_core']['version'],
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
			'image_url'          => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/fusion_core.png',
			'Author'             => 'ThemeFusion',
			'AuthorURI'          => 'https://theme-fusion.com',
		),
		$bundled_plugins['fusion_builder']['slug'] => array(
			'name'               => $bundled_plugins['fusion_builder']['name'],
			'slug'               => $bundled_plugins['fusion_builder']['slug'],
			'source'             => ( $is_plugins_page ) ? Avada()->remote_install->get_package( 'Fusion Builder' ) : 'bundled',
			'required'           => true,
			'version'            => $bundled_plugins['fusion_builder']['version'],
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
			'image_url'          => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/fusion_builder.png',
			'Author'             => 'ThemeFusion',
			'AuthorURI'          => 'https://theme-fusion.com',
		),
		$bundled_plugins['layer_slider']['slug'] => array(
			'name'               => $bundled_plugins['layer_slider']['name'],
			'slug'               => $bundled_plugins['layer_slider']['slug'],
			'source'             => ( $is_plugins_page ) ? Avada()->remote_install->get_package( 'LayerSlider WP' ) : 'bundled',
			'required'           => false,
			'version'            => $bundled_plugins['layer_slider']['version'],
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
			'image_url'          => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/layer_slider.png',
			'Author'             => 'Kreatura Media',
			'AuthorURI'          => 'https://layerslider.kreaturamedia.com/',
		),
		$bundled_plugins['slider_revolution']['slug'] => array(
			'name'               => $bundled_plugins['slider_revolution']['name'],
			'slug'               => $bundled_plugins['slider_revolution']['slug'],
			'source'             => ( $is_plugins_page ) ? Avada()->remote_install->get_package( 'Revolution Slider' ) : 'bundled',
			'required'           => false,
			'version'            => $bundled_plugins['slider_revolution']['version'],
			'force_activation'   => false,
			'force_deactivation' => false,
			'external_url'       => '',
			'image_url'          => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/slider_revolution.png',
			'Author'             => 'ThemePunch',
			'AuthorURI'          => 'http://themepunch.com/',
		),
		'woocommerce' => array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => false,
			'image_url' => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/woocommerce.png',
		),
		'bbpress' => array(
			'name'      => 'bbPress',
			'slug'      => 'bbpress',
			'required'  => false,
			'image_url' => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/bbpress.png',
		),
		'the-events-calendar' => array(
			'name'      => 'The Events Calendar',
			'slug'      => 'the-events-calendar',
			'required'  => false,
			'image_url' => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/the_events_calendar.png',
		),
		'contact-form-7' => array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => false,
			'image_url' => Avada::$template_dir_url . '/assets/admin/images/plugin-thumbnails/contact_form_7.png',
		),
	);

	return $plugins;
}

/**
 * Require the installation of any required and/or recommended third-party plugins here.
 * See http://tgmpluginactivation.com/ for more details
 */
function avada_register_required_and_recommended_plugins() {

	// Get all required and recommended plugins.
	$plugins = avada_get_required_and_recommened_plugins();

	// Change this to your theme text domain, used for internationalising strings.
	$theme_text_domain = 'Avada';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'        	=> $theme_text_domain,
		'default_path'  	=> '',
		'parent_slug' 		=> 'avada',
		'menu'            	=> 'avada-plugins',
		'has_notices'     	=> true,
		'is_automatic'    	=> true,
		'message'         	=> '',
		'strings'         	=> array(
			'page_title'                      => __( 'Install/Update Required Plugins', 'Avada' ),
			'menu_title'                      => __( 'Install Plugins', 'Avada' ),
			'installing'                      => __( 'Installing Plugin: %s', 'Avada' ), // %1$s = plugin name
			'oops'                            => __( 'Something went wrong with the plugin API.', 'Avada' ),
			'notice_can_install_required'     => _n_noop( 'Avada requires the following plugin installed: %1$s.', 'Avada requires the following plugins installed: %1$s.', 'Avada' ), // %1$s = plugin name(s)
			// @codingStandardsIgnoreLine
			'notice_can_install_recommended'  => _n_noop( str_replace( '{{system-status}}', admin_url( 'admin.php?page=avada-system-status' ), 'This theme recommends the following plugin installed or updated: %1$s.<br />IMPORTANT: If your hosting plan has low resources, activating additional plugins can lead to fatal "out of memory" errors. We recommend at least 128MB of memory. Check your resources on the <a href="{{system-status}}" target="_self">System Status</a> tab.' ), str_replace( '{{system-status}}', admin_url( 'admin.php?page=avada-system-status' ), 'This theme recommends the following plugins installed or updated: %1$s.<br />IMPORTANT: If your hosting plan has low resources, activating additional plugins can lead to fatal "out of memory" errors. We recommend at least 128MB of memory. Check your resources on the <a href="{{system-status}}" target="_self">System Status</a> tab.' ), 'Avada' ), // %1$s = plugin name(s)
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'Avada' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'Avada' ), // %1$s = plugin name(s)
			// @codingStandardsIgnoreLine
			'notice_can_activate_recommended' => _n_noop( str_replace( '{{system-status}}', admin_url( 'admin.php?page=avada-system-status' ), 'The following recommended plugin is currently inactive: %1$s.<br />IMPORTANT: If your hosting plan has low resources, activating additional plugins can lead to fatal "out of memory" errors. We recommend at least 128MB of memory. Check your resources on the <a href="{{system-status}}" target="_self">System Status</a> tab.' ), str_replace( '{{system-status}}', admin_url( 'admin.php?page=avada-system-status' ), 'The following recommended plugins are currently inactive: %1$s.<br />IMPORTANT: If your hosting plan has low resources, activating additional plugins can lead to fatal "out of memory" errors. We recommend at least 128MB of memory. Check your resources on the <a href="{{system-status}}" target="_self">System Status</a> tab.' ), 'Avada' ), // %1$s = plugin name(s)
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'Avada' ), // %1$s = plugin name(s)
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to ensure maximum compatibility with Avada: %1$s', 'The following plugins need to be updated to ensure maximum compatibility with Avada: %1$s', 'Avada' ), // %1$s = plugin name(s)
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'Avada' ), // %1$s = plugin name(s)
			'install_link'                    => _n_noop( 'Go Install Plugin', 'Go Install Plugins', 'Avada' ),
			'activate_link'                   => _n_noop( 'Go Activate Plugin', 'Go Activate Plugins', 'Avada' ),
			'return'                          => __( 'Return to Required Plugins Installer', 'Avada' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'Avada' ),
			'complete'                        => __( 'All plugins installed and activated successfully. %s', 'Avada' ), // %1$s = dashboard link
			'nag_type'                        => 'error',// Determines admin notice type - can only be 'updated' or 'error'
		),
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'avada_register_required_and_recommended_plugins' );

/**
 * Returns the user capability for showing the notices.
 *
 * @return string
 */
function avada_tgm_show_admin_notice_capability() {
	return 'edit_theme_options';
}
add_filter( 'tgmpa_show_admin_notice_capability', 'avada_tgm_show_admin_notice_capability' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
