<?php
/**
 * Envato API class.
 *
 * @package Fusion_Updater
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Creates the Envato API connection.
 *
 * @class Fusion_Updater
 * @version 5.0.0
 * @since 5.0.0
 */
final class Fusion_Updater {

	/**
	 * The arguments that are used in the Fusion_Product_Registration class.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var array
	 */
	private $args = array();

	/**
	 * An instance of the Fusion_Product_Registration class.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var object Fusion_Product_Registration.
	 */
	private $registration;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param object $registration An instance of the Fusion_Product_Registration class.
	 */
	public function __construct( $registration ) {

		$this->registration = $registration;
		$this->args         = $registration->get_args();

		// Check for theme & plugin updates.
		add_filter( 'http_request_args', array( $this, 'update_check' ), 5, 2 );

		// Inject theme updates into the response array.
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_themes' ) );
		add_filter( 'pre_set_transient_update_themes', array( $this, 'update_themes' ) );

		// Inject plugin updates into the response array.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins' ) );
		add_filter( 'pre_set_transient_update_plugins', array( $this, 'update_plugins' ) );

		// Inject plugin information into the API calls.
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );

		// Deferred Download.
		add_action( 'upgrader_package_options', array( $this, 'maybe_deferred_download' ), 99 );

	}

	/**
	 * Deferred item download URL.
	 *
	 * @since 5.0.0
	 *
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
	 * @since 5.0.0
	 *
	 * @param  int   $id The item ID.
	 * @param  array $args The arguments passed to `wp_remote_get`.
	 * @return bool|array The HTTP response.
	 */
	public function download( $id, $args = array() ) {
		if ( empty( $id ) ) {
			return false;
		}

		$url = 'https://api.envato.com/v2/market/buyer/download?item_id=' . $id . '&shorten_url=true';
		$response = $this->registration->envato_api()->request( $url, $args );

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
	 * Inject update data for premium themes.
	 *
	 * @since 5.0.0
	 *
	 * @param object $transient The pre-saved value of the `update_themes` site transient.
	 * @return object
	 */
	public function update_themes( $transient ) {
		// Process Avada updates.
		if ( isset( $transient->checked ) && class_exists( 'Avada' ) ) {

			// Get the installed version of Avada.
			$current_avada_version = Avada::get_normalized_theme_version();

			// Get the themes from the Envato API.
			$themes = $this->registration->envato_api()->themes();

			// Get latest Avada version.
			$latest_avada = array(
				'id'      => '',
				'name'    => '',
				'url'     => '',
				'version' => '',
			);
			foreach ( $themes as $theme ) {
				if ( isset( $theme['name'] ) && 'avada' === strtolower( $theme['name'] ) ) {
					$latest_avada = $theme;
					break;
				}
			}

			if ( version_compare( $current_avada_version, $latest_avada['version'], '<' ) ) {
				$transient->response[ $latest_avada['name'] ] = array(
					'theme'       => $latest_avada['name'],
					'new_version' => $latest_avada['version'],
					'url'         => 'https://theme-fusion.com/avada-documentation/changelog.txt',
					'package'     => $this->deferred_download( $latest_avada['id'] ),
				);
			}
		}

		return $transient;
	}

	/**
	 * Inject update data for premium plugins.
	 *
	 * @since 1.0.0
	 * @param object $transient The pre-saved value of the `update_plugins` site transient.
	 * @return object
	 */
	public function update_plugins( $transient ) {

		// Get the array of arguments.
		$args = $this->registration->get_args();

		// Get an array of premium plugins from the Envato API.
		$premiums = $this->registration->envato_api()->plugins();

		// Loop available plugins.
		$plugins = get_plugins();
		foreach ( $plugins as $plugin_file => $plugin ) {
			// Process bundled plugin updates.
			if ( isset( $args['bundled'] ) && ! empty( $args['bundled'] ) ) {
				foreach ( $args['bundled'] as $bundled_plugin ) {
					if ( $plugin['Name'] === $bundled_plugin && isset( $args['bundled-versions'][ $bundled_plugin ] ) && version_compare( $plugin['Version'], $args['bundled-versions'][ $bundled_plugin ], '<' ) && class_exists( 'Avada' ) ) {
						$_plugin = array(
							'slug'        => dirname( $plugin_file ),
							'plugin'      => $plugin,
							'new_version' => $args['bundled-versions'][ $bundled_plugin ],
							'url'         => '',
							'package'     => Avada()->remote_install->get_package( $bundled_plugin ),
						);
						$transient->response[ $plugin_file ] = (object) $_plugin;
					}
				}
			}
			// Process premium plugin updates.
			foreach ( $premiums as $premium ) {
				if ( $plugin['Name'] === $premium['name'] && version_compare( $plugin['Version'], $premium['version'], '<' ) ) {
					$_plugin = array(
						'slug'        => dirname( $plugin_file ),
						'plugin'      => $plugin,
						'new_version' => $premium['version'],
						'url'         => $premium['url'],
						'package'     => $this->deferred_download( $premium['id'] ),
					);
					$transient->response[ $plugin_file ] = (object) $_plugin;
				}
			}
		}
		return $transient;
	}

	/**
	 * Disables requests to the wp.org repository for Avada.
	 *
	 * @since 5.0.0
	 *
	 * @param array  $request An array of HTTP request arguments.
	 * @param string $url The request URL.
	 * @return array
	 */
	public function update_check( $request, $url ) {

		// Theme update request.
		if ( false !== strpos( $url, '//api.wordpress.org/themes/update-check/1.1/' ) ) {

			// Decode JSON so we can manipulate the array.
			$data = json_decode( $request['body']['themes'] );

			// Remove Avada.
			unset( $data->themes->Avada );

			// Encode back into JSON and update the response.
			$request['body']['themes'] = wp_json_encode( $data );
		}
		return $request;
	}

	/**
	 * Defers building the API download url until the last responsible moment to limit file requests.
	 *
	 * Filter the package options before running an update.
	 *
	 * @since 5.0.0
	 *
	 * @param array $options {
	 *     Options used by the upgrader.
	 *
	 *     @type string $package                     Package for update.
	 *     @type string $destination                 Update location.
	 *     @type bool   $clear_destination           Clear the destination resource.
	 *     @type bool   $clear_working               Clear the working resource.
	 *     @type bool   $abort_if_destination_exists Abort if the Destination directory exists.
	 *     @type bool   $is_multi                    Whether the upgrader is running multiple times.
	 *     @type array  $hook_extra                  Extra hook arguments.
	 * }
	 */
	public function maybe_deferred_download( $options ) {
		$package = $options['package'];
		if ( false !== strrpos( $package, 'deferred_download' ) && false !== strrpos( $package, 'item_id' ) ) {
			parse_str( wp_parse_url( $package, PHP_URL_QUERY ), $vars );
			if ( $vars['item_id'] ) {
				$args = $this->set_bearer_args();
				$options['package'] = $this->download( $vars['item_id'], $args );
			}
		}
		return $options;
	}

	/**
	 * Returns the bearer arguments for a request with a single use API Token.
	 *
	 * @since 5.0.0
	 * @return array
	 */
	public function set_bearer_args() {
		$args = array();
		$token = $this->registration->get_token();
		if ( ! empty( $token ) ) {
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'User-Agent'    => 'WordPress - ThemeFusion',
				),
				'timeout' => 20,
			);
		}
		return $args;
	}

	/**
	 * Inject API data for premium plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $response Always false.
	 * @param string $action The API action being performed.
	 * @param object $args Plugin arguments.
	 * @return bool|object $response The plugin info or false.
	 */
	public function plugins_api( $response, $action, $args ) {
		// Process premium theme updates.
		if ( 'plugin_information' === $action && isset( $args->slug ) ) {
			$installed = $this->registration->envato_api()->plugins();
			foreach ( $installed as $slug => $plugin ) {
				if ( dirname( $slug ) === $args->slug ) {
					$response = new stdClass();
					$response->slug           = $args->slug;
					$response->plugin         = $slug;
					$response->plugin_name    = $plugin['name'];
					$response->name           = $plugin['name'];
					$response->version        = $plugin['version'];
					$response->author         = $plugin['author'];
					$response->homepage       = $plugin['url'];
					$response->requires       = $plugin['requires'];
					$response->tested         = $plugin['tested'];
					$response->downloaded     = $plugin['number_of_sales'];
					$response->last_updated   = $plugin['updated_at'];
					$response->sections       = array(
						'description' => $plugin['description'],
					);
					$response->banners['low'] = $plugin['landscape_url'];
					$response->rating         = $plugin['rating']['rating'] / 5 * 100;
					$response->num_ratings    = $plugin['rating']['count'];
					$response->download_link  = $this->deferred_download( $plugin['id'] );
					break;
				}
			}
		}
		return $response;
	}
}
