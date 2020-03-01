<?php
/**
 * Various helper methods for Avada.
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
 * Various helper methods for Avada.
 *
 * @since 3.8
 */
class Avada_Helper {

	/**
	 * Return the value of an echo.
	 * example: Avada_Helper::get_echo( 'function' );
	 *
	 * @param  string       $function The function name.
	 * @param  string|array $args The arguments we want to pass to the function.
	 * @return  string
	 */
	public static function get_echo( $function, $args = '' ) {

		// Early exit if function does not exist.
		if ( ! function_exists( $function ) ) {
			return;
		}

		ob_start();
		$function( $args );
		$get_echo = ob_get_clean();
		return $get_echo;

	}

	/**
	 * Modify the slider name for compatibility.
	 *
	 * @static
	 * @access  public
	 * @param  string $name The slider name.
	 * @return  string
	 */
	public static function slider_name( $name ) {

		$type = '';

		switch ( $name ) {
			case 'layer':
				$type = 'slider';
				break;
			case 'flex':
				$type = 'wooslider';
				break;
			case 'rev':
				$type = 'revslider';
				break;
			case 'elastic':
				$type = 'elasticslider';
				break;
		}

		return $type;

	}

	/**
	 * Given a post ID returns the slider type used.
	 *
	 * @static
	 * @access  public
	 * @param  int $post_id The post ID.
	 * @return  string
	 */
	public static function get_slider_type( $post_id ) {
		return get_post_meta( $post_id, 'pyre_slider_type', true );
	}

	/**
	 * Convert percent width to pixels.
	 *
	 * @static
	 * @access  public
	 * @param  int|float|string $percent   The percentage.
	 * @param  int|string       $max_width The screen max-width.
	 * @return  int
	 */
	public static function percent_to_pixels( $percent, $max_width = 1920 ) {
		return intval( ( intval( $percent ) * $max_width ) / 100 );
	}

	/**
	 * Converts ems to pixels.
	 *
	 * @static
	 * @access  public
	 * @param  int|string $ems The number of ems.
	 * @param  int|string $font_size The base font-size for conversions.
	 * @return  int
	 */
	public static function ems_to_pixels( $ems, $font_size = 14 ) {
		return intval( Fusion_Sanitize::number( $ems ) * $font_size );
	}

	/**
	 * Merges 2 CSS values to pixels.
	 *
	 * @static
	 * @access  public
	 * @param array $values The CSS values we want to merge.
	 * @return  int In pixels.
	 */
	public static function merge_to_pixels( $values = array() ) {
		$final_value = 0;
		foreach ( $values as $value ) {
			if ( false !== strpos( $value, '%' ) ) {
				$value = self::percent_to_pixels( $value, 1600 );
			} elseif ( false !== strpos( $value, 'em' ) ) {
				$value = self::ems_to_pixels( $value );
			} else {
				$value = intval( $value );
			}
			$final_value = $final_value + $value;
		}
		return $final_value;
	}

	/**
	 * Converts a PHP version to 3-part.
	 *
	 * @static
	 * @access public
	 * @param  string $ver The verion number.
	 * @return string
	 */
	public static function normalize_version( $ver ) {
		if ( ! is_string( $ver ) ) {
			return $ver;
		}
		$ver_parts = explode( '.', $ver );
		$count     = count( $ver_parts );
		// Keep only the 1st 3 parts if longer.
		if ( 3 < $count ) {
			return absint( $ver_parts[0] ) . '.' . absint( $ver_parts[1] ) . '.' . absint( $ver_parts[2] );
		}
		// If a single digit, then append '.0.0'.
		if ( 1 === $count ) {
			return absint( $ver_parts[0] ) . '.0.0';
		}
		// If 2 digits, append '.0'.
		if ( 2 === $count ) {
			return absint( $ver_parts[0] ) . '.' . absint( $ver_parts[1] ) . '.0';
		}
		return $ver;
	}

	/**
	 * Check if we're in an events archive.
	 *
	 * @return bool
	 */
	public static function is_events_archive() {
		if ( is_post_type_archive( 'tribe_events' ) || ( self::tribe_is_event() && is_archive() ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if currently an admin post screen is viewed.
	 *
	 * @static
	 * @access public
	 * @since 5.0.0
	 * @param string $type The type of the post screen.
	 * @return bool true only on an admin post screen.
	 */
	public static function is_post_admin_screen( $type = null ) {
		global $pagenow;

		// If not in backend, return early.
		if ( ! is_admin() ) {
			return false;
		}

		// Edit post screen.
		if ( 'edit' === $type ) {
			return in_array( $pagenow, array( 'post.php' ) );
			// New post screen.
		} elseif ( 'new' === $type ) {
			return in_array( $pagenow, array( 'post-new.php' ) );
			// Edit or new post screen.
		} else {
			return in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ) );
		}

	}

	/**
	 * Instantiates the WordPress filesystem for use with Avada.
	 *
	 * @static
	 * @access public
	 * @return object
	 */
	public static function init_filesystem() {

		$credentials = array();

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

		if ( 'ftpext' === $method ) {
			// If defined, set it to that, Else, set to NULL.
			$credentials['hostname'] = defined( 'FTP_HOST' ) ? preg_replace( '|\w+://|', '', FTP_HOST ) : null;
			$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : null;
			$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : null;

			// Set FTP port.
			if ( strpos( $credentials['hostname'], ':' ) && null !== $credentials['hostname'] ) {
				list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
				if ( ! is_numeric( $credentials['port'] ) ) {
					unset( $credentials['port'] );
				}
			} else {
				unset( $credentials['port'] );
			}

			// Set connection type.
			if ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' === $method ) {
				$credentials['connection_type'] = 'ftps';
			} elseif ( ! array_filter( $credentials ) ) {
				$credentials['connection_type'] = null;
			} else {
				$credentials['connection_type'] = 'ftp';
			}
		}

		// The Wordpress filesystem.
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem( $credentials );
		}

		return $wp_filesystem;
	}

	/**
	 * Check if we're on a WooCommerce page.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function is_woocommerce() {

		if ( function_exists( 'is_woocommerce' ) ) {
			return (bool) is_woocommerce();
		}
		return false;

	}

	/**
	 * Check if we're on a bbPress page.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function is_bbpress() {

		if ( function_exists( 'is_bbpress' ) ) {
			return (bool) is_bbpress();
		}
		return false;

	}

	/**
	 * Check if we're on a bbPress forum archive.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function bbp_is_forum_archive() {

		if ( function_exists( 'bbp_is_forum_archive' ) ) {
			return (bool) bbp_is_forum_archive();
		}
		return false;

	}

	/**
	 * Check if we're on a bbPress topic archive.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function bbp_is_topic_archive() {

		if ( function_exists( 'bbp_is_topic_archive' ) ) {
			return (bool) bbp_is_topic_archive();
		}
		return false;

	}

	/**
	 * Check if we're on a bbPress user's home.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function bbp_is_user_home() {

		if ( function_exists( 'bbp_is_user_home' ) ) {
			return (bool) bbp_is_user_home();
		}
		return false;

	}

	/**
	 * Check if we're on a bbPress search-results page.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function bbp_is_search() {

		if ( function_exists( 'bbp_is_search' ) ) {
			return (bool) bbp_is_search();
		}
		return false;

	}

	/**
	 * Check if we're on a buddyPress page.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public static function is_buddypress() {

		if ( function_exists( 'is_buddypress' ) ) {
			return (bool) is_buddypress();
		}
		return false;

	}

	/**
	 * Check if we're on an Event page.
	 *
	 * @static
	 * @access public
	 * @since 5.1.0
	 * @param int|false $id The page ID.
	 * @return bool
	 */
	public static function tribe_is_event( $id = false ) {

		if ( function_exists( 'tribe_is_event' ) ) {
			if ( false === $id ) {
				return (bool) tribe_is_event();
			} else {
				return (bool) tribe_is_event( $id );
			}
		}
		return false;

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
