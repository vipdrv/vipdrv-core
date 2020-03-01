<?php
/**
 * Conditionals.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * WIP
 * These are conditionals that will be used
 * in a future implementation of the customizer.
 * We'll be using them to show/hide options depending on the context.
 */
class Avada_Options_Conditionals {

	/**
	 * Conditional check:
	 * Figure out if WooCommerce is installed.
	 * If WooCommerce is installed, then check that we're on a Woo template.
	 *
	 * @return  bool
	 */
	public static function is_woo() {
		// Return the result of the is_woocommerce() function (boolean).
		return Avada_Helper::is_woocommerce();
	}

	/**
	 * Conditional check:
	 * Figure out if bbPress is installed.
	 * If bbPress is installed, then check that we're on a bbPress template.
	 *
	 * @return  bool
	 */
	public static function is_bbpress() {
		// Return the result of the is_bbpress() function (boolean).
		return Avada_Helper::is_bbpress();
	}

	/**
	 * Conditional check:
	 * Figure out if we're on the blog page.
	 *
	 * @return  bool
	 */
	public static function is_blog() {
		if ( is_front_page() && is_home() ) { // Default homepage.
			return true;
		} elseif ( is_front_page() ) { // Static homepage.
			return false;
		} elseif ( is_home() ) { // Blog page.
			return true;
		}
		return false;
	}

	/**
	 * Conditional check:
	 * Figure out if we're on the contact page or not.
	 *
	 * @return  bool
	 */
	public static function is_contact() {
		if ( is_page_template( 'contact.php' ) ) {
			return true;
		}
		return false;
	}
}
