<?php
/**
 * Upgrades Handler.
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
 * Handle migrations for Avada 5.1.
 *
 * @since 5.1.0
 */
class Avada_Upgrade_510 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.1.0
	 * @var string
	 */
	protected $version = '5.1.0';

	/**
	 * An array of all available languages.
	 *
	 * @static
	 * @access  private
	 * @var  array
	 */
	private static $available_languages = array();

	/**
	 * New option name 5.1 and beyond.
	 *
	 * @static
	 * @access  private
	 * @var  array
	 */
	private static $new_option_name = 'fusion_options';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function migration_process() {

		$available_languages = Fusion_Multilingual::get_available_languages();
		self::$available_languages = ( ! empty( $available_languages ) ) ? $available_languages : array( '' );

		$this->copy_options();
		$this->rename_suboptions();
		$this->delete_options();
		$this->delete_extra_caches();
		$this->registration_options();
		$this->nav_menus();
		$this->migrate_css_caching_methods();
		$this->site_width_changes();
		$this->menu_height_changes();

	}

	/**
	 * Copy options to new db name.
	 *
	 * @since 5.1.0
	 * @access protected
	 */
	protected function copy_options() {
		$available_langs = self::$available_languages;
		$default_language = Fusion_Multilingual::get_default_language();

		$options = get_option( $this->option_name, array() );
		$options = $this->update_woocommerce_single_gallery_size_option( $options );
		$options = $this->update_repeater_field_names( $options );
		update_option( self::$new_option_name, $options );

		foreach ( $available_langs as $language ) {

			// Skip langs that are already done.
			if ( '' === $language ) {
				continue;
			}

			$options = get_option( $this->option_name . '_' . $language, array() );

			$options = $this->update_woocommerce_single_gallery_size_option( $options );
			$options = $this->update_repeater_field_names( $options );

			update_option( self::$new_option_name . '_' . $language, $options );
		}
	}

	/**
	 * Set the new gallery size option to the value of the Woo single shop image size.
	 *
	 * @since 5.1.0
	 * @access protected
	 * @param array $options The Theme Options array.
	 * @return array The updated Theme Options array.
	 */
	protected function update_woocommerce_single_gallery_size_option( $options ) {
		$shop_single_image_size = get_option( 'shop_single_image_size', true );

		if ( isset( $shop_single_image_size['width'] ) ) {
			$options['woocommerce_single_gallery_size'] = $shop_single_image_size['width'] . 'px';
		}

		return $options;
	}

	/**
	 * Change repeater field names.
	 *
	 * @since 5.1.0
	 * @access protected
	 * @param array $options The Theme Options array.
	 * @return array The updated Theme Options array.
	 */
	protected function update_repeater_field_names( $options ) {
		// Update social-media repeaters.
		if ( isset( $options['social_media_icons']['avadaredux_repeater_data'] ) ) {
			$options['social_media_icons']['fusionredux_repeater_data'] = $options['social_media_icons']['avadaredux_repeater_data'];
			unset( $options['social_media_icons']['avadaredux_repeater_data'] );
		}

		// Update custom-fonts repeaters.
		if ( isset( $options['custom_fonts']['avadaredux_repeater_data'] ) ) {
			$options['custom_fonts']['fusionredux_repeater_data'] = $options['custom_fonts']['avadaredux_repeater_data'];
			unset( $options['custom_fonts']['avadaredux_repeater_data'] );
		}

		return $options;
	}

	/**
	 * Migrate menus.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function nav_menus() {

		// Migrate old menus.
		$the_query = new WP_Query( array(
			'post_type' => 'nav_menu_item',
		) );
		$posts = $the_query->posts;
		$url = get_home_url();
		$url = str_replace( 'http://', '', $url );
		$url = str_replace( 'https://', '', $url );
		foreach ( $posts as $post ) {
			if ( property_exists( $post, 'guid' ) ) {
				if ( false !== strpos( $post->guid, 'fusion_home_url' ) ) {
					// Replace placeholders.
					$guid = str_replace( 'fusion_home_url', $url, $post->guid );
					update_post_meta( $post->ID, 'guid', $guid, $post->guid );
				}
			}
		}
	}

	/**
	 * Registration options migration.
	 *
	 * @access protected
	 * @since 1.0.0
	 */
	protected function registration_options() {

		global $wpdb;

		// Registration options migration.
		$avada_registration = get_option( 'avada_registration' );
		delete_option( 'avada_registration' );
		if ( is_array( $avada_registration ) && isset( $avada_registration['token'] ) && ! empty( $avada_registration['token'] ) ) {
			update_option( 'fusion_registration', array(
				'avada' => $avada_registration,
			) );
		}
		$avada_registered = get_option( 'avada_registered', false );
		delete_option( 'avada_registered' );
		if ( $avada_registered ) {
			update_option( 'fusion_registered', array(
				'avada' => true,
			) );
		}
		$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_fusion_envato_api_down%'";
		$wpdb->query( $sql );

	}

	/**
	 * Delete extra caches.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function delete_extra_caches() {

		// Remove avada_dynamic_css_* transients.
		// These will be auto-generated if needed as fusion_dynamic_css_*.
		global $wpdb;
		$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_avada_dynamic_css_%'";
		$wpdb->query( $sql );

		// Delete cached-css files.
		$upload_dir    = wp_upload_dir();
		$folder_path   = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'avada-styles';
		$wp_filesystem = Fusion_Helper::init_filesystem();
		$wp_filesystem->delete( $folder_path, true, 'd' );

	}

	/**
	 * Some options are no longer needed, delete them.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function delete_options() {

		// Remove the avada_dynamic_css_time option.
		// It will be re-generated automatically as fusion_dynamic_css_time.
		delete_option( 'avada_dynamic_css_time' );

		// Remove the avada_dynamic_css_posts option.
		// It will be re-generated automatically as fusion_dynamic_css_posts.
		delete_option( 'avada_dynamic_css_posts' );

		// Delete other options that are no longer used.
		delete_option( 'fusion_dynamic_css_compiler' );
		delete_option( 'fusion_dynamic_css_caching' );
		delete_option( 'fusion_cache_server_ip' );
		delete_option( 'fusion_dynamic_js_compiler' );

	}

	/**
	 * Migrate & combine CSS-Caching method options.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function migrate_css_caching_methods() {

		$avada_options = get_option( self::$new_option_name, array() );
		$method = 'off';
		if ( isset( $avada_options['dynamic_css_db_caching'] ) && '1' === $avada_options['dynamic_css_db_caching'] ) {
			$method = 'db';
		}
		if ( isset( $avada_options['dynamic_css_compiler'] ) && '1' === $avada_options['dynamic_css_compiler'] ) {
			$method = 'file';
		}
		$avada_options['css_cache_method'] = $method;
		unset( $avada_options['dynamic_css_compiler'] );
		unset( $avada_options['dynamic_css_db_caching'] );
		update_option( self::$new_option_name, $avada_options );

	}


	/**
	 * Rename sub-options.
	 *
	 * @access protected
	 * @since 5.1.0
	 */
	protected function rename_suboptions() {

		$renamed_options = array(
			'dev_mode' => 'js_compiler',
		);
		$available_langs = self::$available_languages;
		$default_language = Fusion_Multilingual::get_default_language();
		foreach ( $available_langs as $language ) {

			// English.
			if ( '' === $language || 'en' === $language ) {
				$options = get_option( self::$new_option_name );
				foreach ( $renamed_options as $old => $new ) {
					if ( ! isset( $options[ $old ] ) ) {
						continue;
					}
					if ( 'dev_mode' === $old ) {
						// Reverse option value.
						$options[ $new ] = ( '1' === $options[ $old ] ) ? '0' : '1';
					} else {
						$options[ $new ] = $options[ $old ];
					}
					unset( $options[ $old ] );
				}
				continue;
			}

			// Skip the main language if something other than English.
			// We've already handled that above.
			if ( 'en' !== $default_language && $default_language === $language ) {
				continue;
			}

			$options = get_option( self::$new_option_name . '_' . $language );
			foreach ( $renamed_options as $old => $new ) {
				if ( ! isset( $options[ $old ] ) ) {
					continue;
				}
				if ( 'dev_mode' === $old ) {
					// Reverse option value.
					$options[ $new ] = ( '1' === $options[ $old ] ) ? '0' : '1';
				} else {
					$options[ $new ] = $options[ $old ];
				}
				unset( $options[ $old ] );
			}
			// Copy options to the new language.
			update_option( self::$new_option_name . '_' . $language, $options );

		} // End foreach().
	}

	/**
	 * In 5.1 the site-width option implementation was refined.
	 * We need to make adjustments if on boxed mode with a side-header.
	 *
	 * @access protected
	 * @since 5.1
	 */
	public function site_width_changes() {
		$available_langs  = self::$available_languages;
		$default_language = Fusion_Multilingual::get_default_language();
		$combined_value   = false;

		foreach ( $available_langs as $language ) {

			// English.
			if ( '' === $language || 'en' === $language ) {
				$option_name = self::$new_option_name;
			} else {
				$option_name = self::$new_option_name . '_' . $language;
			}
			$avada_options = get_option( $option_name, array() );

			if ( isset( $avada_options['layout'] ) && 'boxed' === strtolower( $avada_options['layout'] ) && isset( $avada_options['header_position'] ) && ( 'left' === strtolower( $avada_options['header_position'] ) || 'right' === strtolower( $avada_options['header_position'] ) ) && isset( $avada_options['site_width'] ) && ! empty( $avada_options['site_width'] ) && isset( $avada_options['side_header_width'] ) && ! empty( $avada_options['side_header_width'] ) ) {
				$site_width_value  = $avada_options['site_width'];
				$side_header_value = $avada_options['side_header_width'];
				if ( is_numeric( $site_width_value ) ) {
					$site_width_value .= 'px';
				}
				if ( is_numeric( $side_header_value ) ) {
					$side_header_value .= 'px';
				}
				$combined_value = Fusion_Sanitize::add_css_values( array(
					$site_width_value,
					$side_header_value,
				) );

			}
			if ( isset( $avada_options['layout'] ) && 'boxed' === strtolower( $avada_options['layout'] ) ) {
				$combined_value = $combined_value ? $combined_value : $avada_options['site_width'];
				if ( false !== strpos( $combined_value, 'px' ) && false === strpos( $combined_value, 'calc' ) ) {
					$combined_value = Fusion_Sanitize::add_css_values( array(
						$combined_value,
						'60px',
					) );
				}
			}

			if ( false !== $combined_value ) {

				$avada_options['site_width'] = $combined_value;
				update_option( $option_name, $avada_options );

				if ( false !== strpos( $combined_value, 'calc(' ) ) {
					update_option( 'avada_510_site_width_calc' . $language, true );
				}
			}
		} // End foreach().
	}

	/**
	 * In 5.1 menu height in TO = height on front-end.
	 * We need to add the highlight bar width to the height.
	 *
	 * @access protected
	 * @since 5.1
	 */
	public function menu_height_changes() {
		$available_langs  = self::$available_languages;
		$default_language = Fusion_Multilingual::get_default_language();

		foreach ( $available_langs as $language ) {

			// English.
			if ( '' === $language || 'en' === $language ) {
				$option_name = self::$new_option_name;
			} else {
				$option_name = self::$new_option_name . '_' . $language;
			}
			$avada_options = get_option( $option_name, array() );

			if ( isset( $avada_options['nav_height'] ) && isset( $avada_options['nav_highlight_border'] ) && '0' !== $avada_options['nav_highlight_border'] ) {
				$combined_value = intval( $avada_options['nav_height'] ) + intval( $avada_options['nav_highlight_border'] );
				$avada_options['nav_height'] = $combined_value;
				update_option( $option_name, $avada_options );
			}
		}
	}
}
