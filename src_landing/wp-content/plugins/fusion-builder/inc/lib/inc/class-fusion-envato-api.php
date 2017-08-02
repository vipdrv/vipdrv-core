<?php
/**
 * Envato API class.
 *
 * @package Fusion_Envato_API
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Creates the Envato API connection.
 *
 * @class Fusion_Envato_API
 * @version 1.0.0
 * @since 1.0.0
 */
class Fusion_Envato_API {

	/**
	 * The Envato API personal token.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var string
	 */
	private $token;

	/**
	 * An instance of the Fusion_Product_Registration class.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var object Fusion_Product_Registration.
	 */
	private $registration;

	/**
	 * A dummy constructor to prevent this class from being loaded more than once.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $registration An instance of the Fusion_Product_Registration class.
	 */
	public function __construct( $registration ) {

		$this->registration = $registration;
		$this->token        = $this->registration->get_token();

	}

	/**
	 * You cannot clone this class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'fusion-builder' ), '1.0.0' );
	}

	/**
	 * You cannot unserialize instances of this class.
	 *
	 * @access public
	 * @since 1.0.0
	 * @codeCoverageIgnore
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'fusion-builder' ), '1.0.0' );
	}

	/**
	 * Sets the token.
	 *
	 * @access public
	 * @param string $token The token.
	 */
	public function set_token( $token ) {
		$this->token = $token;
	}

	/**
	 * Query the Envato API.
	 *
	 * @access public
	 * @uses wp_remote_get() To perform an HTTP request.
	 * @since 1.0.0
	 * @param  string $url API request URL, including the request method, parameters, & file type.
	 * @param  array  $args The arguments passed to `wp_remote_get`.
	 * @return array  The HTTP response.
	 */
	public function request( $url, $args = array() ) {
		$defaults = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->token,
				'User-Agent' => 'WordPress - Fusion Library',
			),
			'timeout' => 20,
		);
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $this->token ) ) {
			return new WP_Error( 'api_token_error', __( 'An API token is required.', 'fusion-builder' ) );
		}

		// Make an API request.
		$response = wp_remote_get( esc_url_raw( $url ), $args );

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new WP_Error( $response_code, $response_message );
		}
		if ( 200 !== $response_code ) {
			return new WP_Error( $response_code, __( 'An unknown API error occurred.', 'fusion-builder' ) );
		}
		$return = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( null === $return ) {
			return new WP_Error( 'api_error', __( 'An unknown API error occurred.', 'fusion-builder' ) );
		}
		return $return;
	}

	/**
	 * Deferred item download URL.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int $id The item ID.
	 * @return string.
	 */
	public function deferred_download( $id ) {
		if ( empty( $id ) ) {
			return '';
		}

		$args = array(
			'deferred_download' => true,
			'item_id' => $id,
		);
		return add_query_arg( $args, esc_url( admin_url( 'admin.php?page=avada' ) ) );
	}

	/**
	 * Get the item download.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int   $id   The item ID.
	 * @param array $args The arguments passed to `wp_remote_get`.
	 * @return bool|array The HTTP response.
	 */
	public function download( $id, $args = array() ) {
		if ( empty( $id ) ) {
			return false;
		}

		$url = 'https://api.envato.com/v2/market/buyer/download?item_id=' . $id . '&shorten_url=true';
		$response = $this->request( $url, $args );

		// @todo Find out which errors could be returned & handle them in the UI.
		if ( is_wp_error( $response ) || empty( $response ) || ! empty( $response['error'] ) ) {
			return false;
		}

		if ( ! empty( $response['wordpress_theme'] ) ) {
			return $response['wordpress_theme'];
		}

		if ( ! empty( $response['wordpress_plugin'] ) ) {
			return $response['wordpress_plugin'];
		}

		return false;
	}

	/**
	 * Get an item by ID and type.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int   $id   The item ID.
	 * @param array $args The arguments passed to `wp_remote_get`.
	 * @return array      The HTTP response.
	 */
	public function item( $id, $args = array() ) {
		$url = 'https://api.envato.com/v3/market/catalog/item?id=' . $id;
		$response = $this->request( $url, $args );

		if ( is_wp_error( $response ) || empty( $response ) ) {
			return false;
		}

		if ( ! empty( $response['wordpress_theme_metadata'] ) ) {
			return $this->normalize_theme( $response );
		}

		if ( ! empty( $response['wordpress_plugin_metadata'] ) ) {
			return $this->normalize_plugin( $response );
		}
		return false;
	}

	/**
	 * Get the list of available themes.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args The arguments passed to `wp_remote_get`.
	 * @param int   $page The page number if one is necessary.
	 * @return array      The HTTP response.
	 */
	public function themes( $args = array(), $page = '' ) {
		$themes = array();

		$url  = 'https://api.envato.com/v3/market/buyer/list-purchases?filter_by=wordpress-themes';
		$url .= ( $page ) ? '&page=' . $page : '';

		$response = $this->request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( empty( $response ) || empty( $response['results'] ) ) {
			return $themes;
		}

		foreach ( $response['results'] as $theme ) {
			$themes[] = $this->normalize_theme( $theme['item'] );
		}

		return $themes;
	}

	/**
	 * Normalize a theme.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $theme An array of API request values.
	 * @return array       A normalized array of values.
	 */
	public function normalize_theme( $theme ) {
		return array(
			'id'            => $theme['id'],
			'name'          => ( ! empty( $theme['wordpress_theme_metadata']['theme_name'] ) ? $theme['wordpress_theme_metadata']['theme_name'] : '' ),
			'author'        => ( ! empty( $theme['wordpress_theme_metadata']['author_name'] ) ? $theme['wordpress_theme_metadata']['author_name'] : '' ),
			'version'       => ( ! empty( $theme['wordpress_theme_metadata']['version'] ) ? $theme['wordpress_theme_metadata']['version'] : '' ),
			'description'   => self::remove_non_unicode( $theme['wordpress_theme_metadata']['description'] ),
			'url'           => ( ! empty( $theme['url'] ) ? $theme['url'] : '' ),
			'author_url'    => ( ! empty( $theme['author_url'] ) ? $theme['author_url'] : '' ),
			'thumbnail_url' => ( ! empty( $theme['thumbnail_url'] ) ? $theme['thumbnail_url'] : '' ),
			'rating'        => ( ! empty( $theme['rating'] ) ? $theme['rating'] : '' ),
		);
	}

	/**
	 * Get the list of available plugins.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args The arguments passed to `wp_remote_get`.
	 * @param int   $page The page number if one is necessary.
	 * @return array      The HTTP response.
	 */
	public function plugins( $args = array(), $page = '' ) {
		$plugins = array();

		$url  = 'https://api.envato.com/v3/market/buyer/list-purchases?filter_by=wordpress-plugins';
		$url .= ( $page ) ? '&page=' . $page : '';

		$response = $this->request( $url, $args );

		if ( is_wp_error( $response ) || empty( $response ) || empty( $response['results'] ) ) {
			return $plugins;
		}

		foreach ( $response['results'] as $plugin ) {
			$plugins[] = $this->normalize_plugin( $plugin['item'] );
		}
		return $plugins;
	}

	/**
	 * Normalize a plugin.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param  array $plugin An array of API request values.
	 * @return array A normalized array of values.
	 */
	public function normalize_plugin( $plugin ) {
		$requires = null;
		$tested   = null;
		$versions = array();

		// Set the required and tested WordPress version numbers.
		foreach ( $plugin['attributes'] as $k => $v ) {
			if ( 'compatible-software' === $v['name'] ) {
				$v['value'] = (array) $v['value'];
				foreach ( $v['value'] as $version ) {
					$versions[] = str_replace( 'WordPress ', '', trim( $version ) );
				}
				if ( ! empty( $versions ) ) {
					$requires = $versions[ count( $versions ) - 1 ];
					$tested = $versions[0];
				}
				break;
			}
		}

		return array(
			'id'              => $plugin['id'],
			'name'            => ( ! empty( $plugin['wordpress_plugin_metadata']['plugin_name'] ) ? $plugin['wordpress_plugin_metadata']['plugin_name'] : '' ),
			'author'          => ( ! empty( $plugin['wordpress_plugin_metadata']['author'] ) ? $plugin['wordpress_plugin_metadata']['author'] : '' ),
			'version'         => ( ! empty( $plugin['wordpress_plugin_metadata']['version'] ) ? $plugin['wordpress_plugin_metadata']['version'] : '' ),
			'description'     => ( ! empty( $plugin['wordpress_plugin_metadata']['description'] ) ? self::remove_non_unicode( $plugin['wordpress_plugin_metadata']['description'] ) : '' ),
			'url'             => ( ! empty( $plugin['url'] ) ? $plugin['url'] : '' ),
			'author_url'      => ( ! empty( $plugin['author_url'] ) ? $plugin['author_url'] : '' ),
			'thumbnail_url'   => ( ! empty( $plugin['thumbnail_url'] ) ? $plugin['thumbnail_url'] : '' ),
			'landscape_url'   => ( ! empty( $plugin['previews']['landscape_preview']['landscape_url'] ) ? $plugin['previews']['landscape_preview']['landscape_url'] : '' ),
			'requires'        => $requires,
			'tested'          => $tested,
			'number_of_sales' => ( ! empty( $plugin['number_of_sales'] ) ? $plugin['number_of_sales'] : '' ),
			'updated_at'      => ( ! empty( $plugin['updated_at'] ) ? $plugin['updated_at'] : '' ),
			'rating'          => ( ! empty( $plugin['rating'] ) ? $plugin['rating'] : '' ),
		);
	}

	/**
	 * Remove all non unicode characters in a string
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $retval The string to fix.
	 * @return string
	 */
	static private function remove_non_unicode( $retval ) {
		return preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $retval );
	}

	/**
	 * Get the token scopes from the Envato API.
	 *
	 * @access public
	 * @since 1.0.6
	 * @param string $token A token to check.
	 * @return array
	 */
	public function get_token_scopes( $token = '' ) {
		if ( '' === $token ) {
			$token = $this->token;
		}
		// The arguments for the request.
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'User-Agent'    => 'WordPress - Fusion Library',
			),
			'timeout' => 20,
		);
		// Make the request to the Envato API.
		$whoami = (array) $this->request( 'https://api.envato.com/whoami', $args );

		if ( isset( $whoami['scopes'] ) && is_array( $whoami['scopes'] ) ) {
			return $whoami['scopes'];
		}
		return array();
	}

	/**
	 * Check if the token has all the scopes we need.
	 *
	 * @access public
	 * @since 1.0.6
	 * @param string $scopes The scopes that need to be checked.
	 * @return bool
	 */
	public function check_token_scopes( $scopes ) {

		$scopes_ok = false;
		if ( is_array( $scopes ) && ! empty( $scopes ) ) {
			$scopes_ok = true;

			// An array of the scopes we need.
			$needed_scopes = array(
				'purchase:download',
				'purchase:list',
				'purchase:verify',
			);
			// Check if all needed scopes exist.
			foreach ( $needed_scopes as $needed_scope ) {
				if ( ! in_array( $needed_scope, $scopes ) ) {
					$scopes_ok = false;
				}
			}
		}
		return (bool) $scopes_ok;
	}
}
