<?php
/**
 * Loads common Fusion libraries.
 *
 * @package Fusion-Library
 * @version 1.1
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

// Don't use a constant, we need this as a simple var.
$fusion_library_version = '1.1';

$current_dir = dirname( __FILE__ );

// Autoloader.
if ( ! class_exists( 'Fusion_Library_Autoloader' ) ) {
	include_once dirname( __FILE__ ) . '/inc/class-fusion-library-autoloader.php';
}
Fusion_Library_Autoloader::add_location( $current_dir, $fusion_library_version );
Fusion_Library_Autoloader::get_instance();

// Define the path.
// Will be used to load other files.
if ( ! defined( 'FUSION_LIBRARY_PATH' ) ) {
	$dirname = dirname( __FILE__ );
	$dirname = wp_normalize_path( $dirname );
	define( 'FUSION_LIBRARY_PATH', $dirname );
}

if ( ! defined( 'FUSION_LIBRARY_URL' ) ) {
	if ( defined( 'AVADA_VERSION' ) ) {
		$fusion_library_url = get_template_directory_uri() . '/includes/lib';
	} elseif ( defined( 'FUSION_BUILDER_PLUGIN_URL' ) ) {
		$fusion_library_url = FUSION_BUILDER_PLUGIN_URL . 'inc/lib';
	} else {
		$dir = dirname( __FILE__ );
		$dir = wp_normalize_path( $dir ); // Current directory.

		$dir_array = explode( '/', $dir );
		$path_length = count( $dir_array );
		if ( 4 < $path_length ) {
			for ( $i = 1; $i < 5; $i++ ) {
				unset( $dir_array[ $path_length - $i ] );
			}
			$wp_content_dir = implode( '/', $dir_array );
		} else {
			$wp_content_dir = wp_normalize_path( WP_CONTENT_DIR );
		}
		$wp_content_url = content_url();

		$link   = str_replace( $wp_content_dir, $wp_content_url, $dir );
		$scheme = ( ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) || is_ssl() ) ? 'https' : 'http';

		$fusion_library_url = set_url_scheme( $link, $scheme );
	}

	define( 'FUSION_LIBRARY_URL', $fusion_library_url );
}

// Font Awesome path.
if ( ! defined( 'FUSION_FA_PATH' ) ) {
	define( 'FUSION_FA_PATH', FUSION_LIBRARY_PATH . '/assets/fonts/fontawesome/font-awesome.css' );
}

// Include functions.
include_once FUSION_LIBRARY_PATH . '/inc/functions.php';
include_once FUSION_LIBRARY_PATH . '/inc/fusion-icon.php';
if ( file_exists( FUSION_LIBRARY_PATH . '/inc/wc-functions.php' ) ) {
	include_once FUSION_LIBRARY_PATH . '/inc/wc-functions.php';
}

// Set fusion_library global if not already set.
global $fusion_library;
if ( ! $fusion_library ) {
	$fusion_library = Fusion::get_instance();
}
