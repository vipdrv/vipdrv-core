<?php
/**
 * Handles redux in Avada.
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
 * Handle Redux in Avada.
 */
class Avada_AvadaRedux extends Fusion_FusionRedux {

	/**
	 * The class constructor
	 *
	 * @access public
	 * @param array $args The arguments we'll be passing-on to the object.
	 */
	public function __construct( $args = array() ) {

		parent::__construct( $args );

		add_action( 'wp_ajax_avada_reset_all_caches', array( $this, 'reset_caches_handler' ) );
		add_action( 'wp_ajax_nopriv_avada_reset_all_caches', array( $this, 'reset_caches_handler' ) );

	}

	/**
	 * Initializes and triggers all other actions/hooks.
	 *
	 * @access public
	 */
	public function init_fusionredux() {

		$this->args['sections'] = Avada::$options;

		add_action( 'admin_menu', array( $this, 'deprecated_adminpage_hook' ) );
		add_filter( 'fusion_redux_typography_font_groups', array( $this, 'fusion_redux_typography_font_groups' ) );
		add_filter( 'fusion_options_font_size_dimension_fields', array( $this, 'fusion_options_font_size_dimension_fields' ) );
		add_filter( 'fusion_options_sliders_not_in_pixels', array( $this, 'fusion_options_sliders_not_in_pixels' ) );
		add_filter( 'fusion_options_page_soft_dependencies', array( $this, 'fusion_options_page_soft_dependencies' ) );
		if ( class_exists( 'Fusion_Builder_Redux' ) ) {
			// Split to multiple lines for PHP 5.2 compatibility.
			$fusion_builder = FusionBuilder();
			$fusion_builder_options_panel = $fusion_builder->get_fusion_builder_options_panel();
			$fusion_builder_redux = $fusion_builder_options_panel->get_fusion_builder_redux();
			add_filter( 'fusion_options_page_soft_dependencies', array( $fusion_builder_redux, 'fusion_options_builder_soft_dependencies' ) );
		}
		parent::init_fusionredux();

		// Importing/switching color scheme.
		add_action( 'wp_ajax_custom_option_import', array( $this, 'reset_caches_handler' ) );

		// Custom color scheme ajax save.
		add_action( 'wp_ajax_custom_colors_ajax_save', array( $this, 'custom_colors_ajax_save' ) );

		// Custom color scheme ajax delete.
		add_action( 'wp_ajax_custom_colors_ajax_delete', array( $this, 'custom_colors_ajax_delete' ) );
	}

	/**
	 * Enqueue additional scripts.
	 *
	 * @access public
	 */
	public function enqueue() {
		parent::enqueue();
		wp_enqueue_script( 'avada-redux-reset-caches', trailingslashit( Avada::$template_dir_url ) . 'assets/admin/js/avada-reset-caches.js' );
		wp_localize_script(
			'avada-redux-reset-caches',
			'avadaReduxResetCaches',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'confirm' => esc_attr__( 'Are you sure you want to delete all Fusion caches?', 'Avada' ),
				'success' => esc_attr__( 'All Fusion caches have been reset.', 'Avada' ),
			)
		);
	}


	/**
	 * Save the custom color scheme to an option
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function custom_colors_ajax_save() {

		global $wpdb;

		// Check that the user has the right permissions.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// @codingStandardsIgnoreLine
		if ( ! empty( $_POST['data'] ) ) {

			$existing_colors  = get_option( 'avada_custom_color_schemes', array() );

			// @codingStandardsIgnoreLine
			if ( 'import' !== $_POST['data']['type'] ) {
				$scheme        = array();
				// @codingStandardsIgnoreLine
				$scheme_colors = wp_unslash( $_POST['data']['values'] );
				// @codingStandardsIgnoreLine
				$scheme_name   = sanitize_text_field( wp_unslash( $_POST['data']['name'] ) );

				if ( defined( 'FUSION_BUILDER_PLUGIN_DIR' ) ) {
					$fb_options = get_option( 'fusion_options' );
					foreach ( $scheme_colors as $option => $value ) {
						if ( array_key_exists( $option, $fb_options ) ) {
							$scheme_colors[ $option ] = $fb_options[ $option ];
						}
					}
				}

				$scheme[] = array(
					'name'   => $scheme_name,
					'values' => $scheme_colors,
				);

				// Check if scheme trying to be saved already exists, if so unset and merge. @codingStandardsIgnoreLine
				if ( 'update' == $_POST['data']['type'] ) {
					// Remove existing saved version and and merge in.
					foreach ( $existing_colors as $key => $existing_color ) {
						if ( $existing_color['name'] == $scheme_name ) {
							unset( $existing_colors[ $key ] );
						}
					}
					$schemes = array_merge( $scheme, $existing_colors );
				} elseif ( is_array( $existing_colors ) ) {
					$schemes = array_merge( $scheme, $existing_colors );
				} else {
					$schemes = $scheme;
				}

				// Sanitize schemes.
				$schemes = $this->sanitize_color_schemes( $schemes );

				update_option( 'avada_custom_color_schemes', $schemes );
				echo wp_json_encode( array(
					'status' => 'success',
					'action' => '',
				) );

			} else {
				// @codingStandardsIgnoreLine (sanitization is below using the sanitize_color_schemes method).
				$schemes = stripslashes( stripcslashes( wp_unslash( $_POST['data']['values'] ) ) );
				$schemes = json_decode( $schemes, true );
				if ( is_array( $existing_colors ) ) {
					// Add imported schemes to existing set.
					$schemes = array_merge( $schemes, $existing_colors );
				}

				// Sanitize schemes.
				$schemes = $this->sanitize_color_schemes( $schemes );

				update_option( 'avada_custom_color_schemes', $schemes );

				echo wp_json_encode( array(
					'status' => 'success',
					'action' => '',
				) );
			} // End if().
		} // End if().
		die();
	}

	/**
	 * Delete the custom color schemes selected
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function custom_colors_ajax_delete() {

		global $wpdb;

		// Check that the user has the right permissions.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// @codingStandardsIgnoreLine
		if ( ! empty( $_POST['data'] ) && is_array( $_POST['data']['names'] ) ) {

			$existing_colors  = get_option( 'avada_custom_color_schemes', array() );

			// @codingStandardsIgnoreLine (sanitization follows on a per-name basis).
			$post_data_names = wp_unslash( $_POST['data']['names'] );
			foreach ( $post_data_names as $scheme_name ) {
				$scheme_name = sanitize_text_field( $scheme_name );
				// Remove from array of existing schemes.
				foreach ( $existing_colors as $key => $existing_color ) {
					if ( $existing_color['name'] == $scheme_name ) {
						unset( $existing_colors[ $key ] );
					}
				}
			}

			update_option( 'avada_custom_color_schemes', $existing_colors );

			echo wp_json_encode( array(
				'status' => 'success',
				'action' => '',
			) );

		}
		die();
	}

	/**
	 * Register the page and then unregister it.
	 * This allows the user to access the URL of the page,
	 * but without an actual menu for the page.
	 *
	 * @access public
	 */
	public function deprecated_adminpage_hook() {
		add_submenu_page( 'themes.php', __( 'Avada Options have moved!', 'Avada' ), __( 'Avada Options', 'Avada' ), 'edit_theme_options', 'optionsframework', array( $this, 'deprecated_adminpage' ) );
		remove_submenu_page( 'themes.php', 'optionsframework' );
	}

	/**
	 * Creates a countdown counter and then redirects the user to the new admin page.
	 * We're using this to accomodate users that perhaps have the page bookmarked.
	 * This way they won't get an error page but we'll gracefully migrate them to the new page.
	 *
	 * @access public
	 */
	public function deprecated_adminpage() {
		?>
		<script type="text/javascript">
			var count = 6;
			var redirect = "<?php echo esc_url_raw( admin_url( 'themes.php?page=fusion_options' ) ); ?>";

			function countDown(){
				var timer = document.getElementById("timer");
				if (count > 0){
					count--;
					timer.innerHTML = "<?php printf( esc_html__( 'Theme options have changed, redirecting you to the new page in %s seconds.', 'Avada' ), '" + count + "' ); ?>";
					setTimeout("countDown()", 1000);
				} else {
					window.location.href = redirect;
				}
			}
		</script>
		<span id="timer" style="font-size: 1.7em; padding: 100px; text-align: center; line-height: 10em;"><script type="text/javascript">countDown();</script></span>
		<?php
	}

	/**
	 * Add a "Custom Fonts" group.
	 *
	 * @access public
	 * @since 5.1
	 * @param array $font_groups An array of our font-groups.
	 * @return array
	 */
	public function fusion_redux_typography_font_groups( $font_groups ) {

		// Get Custom fonts.
		$options = get_option( Avada::get_option_name(), array() );

		if ( isset( $options['custom_fonts'] ) ) {

			$custom_fonts = $options['custom_fonts'];

			// Check if there's at least one custom font set.
			if ( isset( $custom_fonts['name'] ) && is_array( $custom_fonts['name'] ) && ! empty( $custom_fonts['name'][0] ) ) {

				// Add Custom Fonts group.
				$font_groups['customfonts'] = array(
					'text'     => __( 'Custom Fonts', 'fusionredux-framework' ),
					'children' => array(),
				);

				// Add custom fonts.
				foreach ( $custom_fonts['name'] as $key => $label ) {

					$font_groups['customfonts']['children'][] = array(
						'id'          => esc_attr( $label ),
						'text'        => esc_attr( $label ),
						'data-google' => 'false',
					);
				}
			}
		}
		return $font_groups;
	}

	/**
	 * Adds options to be processes as font-sizes.
	 * Affects the field's sanitization call.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $fields An array of fields.
	 * @return array
	 */
	public function fusion_options_font_size_dimension_fields( $fields ) {
		$extra_fields = array(
			'meta_font_size',
			'es_title_font_size',
			'es_caption_font_size',
			'ec_sidew_font_size',
			'image_rollover_icon_size',
			'pagination_font_size',
			'form_input_height',
			'copyright_font_size',
			'tagline_font_size',
			'header_sticky_nav_font_size',
			'page_title_font_size',
			'page_title_subheader_font_size',
			'breadcrumbs_font_size',
			'social_links_font_size',
			'sidew_font_size',
			'slider_arrow_size',
			'slidingbar_font_size',
			'header_social_links_font_size',
			'footer_social_links_font_size',
			'sharing_social_links_font_size',
			'post_titles_font_size',
			'post_titles_font_lh',
			'post_titles_extras_font_size',
			'woo_icon_font_size',
		);
		return array_unique( array_merge( $fields, $extra_fields ) );
	}

	/**
	 * Sliders that are not in pixels.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $fields An array of fields.
	 * @return array
	 */
	public function fusion_options_sliders_not_in_pixels( $fields ) {
		$extra_fields = array(
			'slidingbar_widgets_columns',
			'footer_widgets_columns',
			'blog_grid_columns',
			'excerpt_length_blog',
			'portfolio_archive_excerpt_length',
			'portfolio_archive_columns',
			'portfolio_archive_items',
			'portfolio_excerpt_length',
			'portfolio_items',
			'posts_slideshow_number',
			'slideshow_speed',
			'tfes_interval',
			'tfes_speed',
			'lightbox_slideshow_speed',
			'lightbox_opacity',
			'map_zoom_level',
			'search_results_per_page',
			'number_related_posts',
			'related_posts_columns',
			'related_posts_speed',
			'related_posts_swipe_items',
			'pw_jpeg_quality',
			'woo_items',
			'woocommerce_shop_page_columns',
			'woocommerce_related_columns',
			'woocommerce_archive_page_columns',
			'typography_sensitivity',
			'typography_factor',
		);
		return array_unique( array_merge( $fields, $extra_fields ) );
	}

	/**
	 * Page dependencies.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $dependencies An array of fields.
	 * @return array
	 */
	public function fusion_options_page_soft_dependencies( $dependencies ) {
		return array_merge( $dependencies, array(
			'page_title_bar_text'                        => array( 'page_title_bar' ),
			'page_title_100_width'                       => array( 'page_title_bar' ),
			'page_title_height'                          => array( 'page_title_bar' ),
			'page_title_mobile_height'                   => array( 'page_title_bar' ),
			'page_title_bg_color'                        => array( 'page_title_bar' ),
			'page_title_border_color'                    => array( 'page_title_bar' ),
			'page_title_font_size'                       => array( 'page_title_bar', 'page_title_bar_text' ),
			'page_title_line_height'                     => array( 'page_title_bar', 'page_title_bar_text' ),
			'page_title_color'                           => array( 'page_title_bar', 'page_title_bar_text' ),
			'page_title_subheader_font_size'             => array( 'page_title_bar', 'page_title_bar_text' ),
			'page_title_alignment'                       => array( 'page_title_bar' ),
			'page_title_bg'                              => array( 'page_title_bar' ),
			'page_title_bg_retina'                       => array( 'page_title_bg', 'page_title_bar' ),
			'page_title_bg_full'                         => array( 'page_title_bg', 'page_title_bar' ),
			'page_title_bg_parallax'                     => array( 'page_title_bar', 'page_title_bg' ),
			'page_title_fading'                          => array( 'page_title_bar' ),
			'breadcrumb_important_note_info'             => array( 'page_title_bar' ),
			'page_title_bar_bs'                          => array( 'page_title_bar' ),
			'breadcrumb_mobile'                          => array( 'page_title_bar' ),
			'breacrumb_prefix'                           => array( 'page_title_bar' ),
			'breadcrumb_separator'                       => array( 'page_title_bar' ),
			'breadcrumbs_font_size'                      => array( 'page_title_bar' ),
			'breadcrumbs_text_color'                     => array( 'page_title_bar' ),
			'breadcrumb_show_categories'                 => array( 'page_title_bar' ),
			'breadcrumb_show_post_type_archive'          => array( 'page_title_bar' ),
			'footer_widgets_columns'                     => array( 'footer_widgets' ),
			'footer_widgets_center_content'              => array( 'footer_widgets' ),
			'footer_copyright_center_content'            => array( 'footer_copyright' ),
			'footer_text'                                => array( 'footer_copyright' ),
			'footerw_bg_image'                           => array( 'footer_widgets' ),
			'footerw_bg_full'                            => array( 'footer_widgets' ),
			'footerw_bg_repeat'                          => array( 'footer_widgets' ),
			'footerw_bg_pos'                             => array( 'footer_widgets' ),
			'footer_100_width'                           => array( 'footer_widgets', 'footer_copyright' ),
			'footer_area_padding'                        => array( 'footer_widgets', 'footer_copyright' ),
			'footer_bg_color'                            => array( 'footer_widgets' ),
			'footer_border_size'                         => array( 'footer_widgets' ),
			'footer_border_color'                        => array( 'footer_widgets' ),
			'footer_divider_color'                       => array( 'footer_widgets' ),
			'copyright_padding'                          => array( 'footer_copyright' ),
			'copyright_bg_color'                         => array( 'footer_copyright' ),
			'copyright_border_size'                      => array( 'footer_copyright' ),
			'copyright_border_color'                     => array( 'footer_copyright' ),
			'footer_headings_typography'                 => array( 'footer_widgets', 'footer_copyright' ),
			'footer_text_color'                          => array( 'footer_widgets', 'footer_copyright' ),
			'footer_link_color'                          => array( 'footer_widgets', 'footer_copyright' ),
			'footer_link_color_hover'                    => array( 'footer_widgets', 'footer_copyright' ),
			'copyright_font_size'                        => array( 'footer_copyright' ),
			'boxed_mode_backgrounds_important_note_info' => array( 'layout' ),
			'bg_image'                                   => array( 'layout' ),
			'bg_color'                                   => array( 'layout' ),
			'bg_pattern_option'                          => array( 'layout' ),
			'bg_pattern'                                 => array( 'layout' ),
			'image_rollover_direction'                   => array( 'image_rollover' ),
			'image_rollover_icon_size'                   => array( 'image_rollover' ),
			'link_image_rollover'                        => array( 'image_rollover' ),
			'zoom_image_rollover'                        => array( 'image_rollover' ),
			'title_image_rollover'                       => array( 'image_rollover' ),
			'cats_image_rollover'                        => array( 'image_rollover' ),
			'icon_circle_image_rollover'                 => array( 'image_rollover' ),
			'image_gradient_top_color'                   => array( 'image_rollover' ),
			'image_gradient_bottom_color'                => array( 'image_rollover' ),
			'image_rollover_text_color'                  => array( 'image_rollover' ),
			'image_rollover_icon_color'                  => array( 'image_rollover' ),
		) );
	}

	/**
	 * Extra functionality on save.
	 *
	 * @access public
	 * @since 4.0
	 * @param array $data           The data.
	 * @param array $changed_values The changed values to save.
	 * @return void
	 */
	public function save_as_option( $data, $changed_values ) {
		update_option( 'avada_disable_encoding', $data['disable_code_block_encoding'] );
		// Delete migration option for 5.1.
		if ( isset( $data['site_width'] ) && false === strpos( $data['site_width'], 'calc' ) ) {
			delete_option( 'avada_510_site_width_calc' );
		}
	}

	/**
	 * Sanitizes color schemes.
	 *
	 * @since 5.1.0
	 * @param array $schemes The color schemes.
	 * @return array The color schemens, sanitized.
	 */
	private function sanitize_color_schemes( $schemes ) {

		if ( ! is_array( $schemes ) ) {
			return array();
		}
		$final_schemes = array();
		foreach ( $schemes as $scheme ) {
			// Sanitize the scheme name.
			if ( ! isset( $scheme['name'] ) ) {
				$scheme['name'] = '';
			}
			$scheme['name'] = esc_attr( $scheme['name'] );
			// Sanitize the scheme values.
			if ( ! isset( $scheme['values'] ) ) {
				$scheme['values'] = array();
			}
			$scheme_values = array();
			foreach ( $scheme['values'] as $key => $value ) {
				$key = sanitize_key( $key );
				// Color sanitization.
				$color_obj = Fusion_Color::new_color( $value );
				$scheme_values[ $key ] = $color_obj->toCSS( $color_obj->mode );
			}
			$final_schemes[] = array(
				'name'   => $scheme['name'],
				'values' => $scheme_values,
			);
		}
		return $final_schemes;
	}

	/**
	 * Handles resetting caches.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function reset_caches_handler() {
		if ( is_multisite() && is_main_site() ) {
			$sites = get_sites();
			foreach ( $sites as $site ) {
				// @codingStandardsIgnoreLine
				switch_to_blog( $site->blog_id );
				avada_reset_all_cache();
				restore_current_blog();
			}
			return;
		}
		avada_reset_all_cache();
	}
}
