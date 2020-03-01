<?php

/*
Plugin Name: Fusion Builder
Plugin URI: http://www.theme-fusion.com
Description: ThemeFusion Page Builder Plugin
Version: 1.2.1
Author: ThemeFusion
Author URI: http://www.theme-fusion.com
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Developer mode.
if ( ! defined( 'FUSION_BUILDER_DEV_MODE' ) ) {
	define( 'FUSION_BUILDER_DEV_MODE', false );
}

// Plugin version.
if ( ! defined( 'FUSION_BUILDER_VERSION' ) ) {
	define( 'FUSION_BUILDER_VERSION', '1.2.1' );
}
// Plugin Folder Path.
if ( ! defined( 'FUSION_BUILDER_PLUGIN_DIR' ) ) {
	define( 'FUSION_BUILDER_PLUGIN_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}
// Plugin Folder URL.
if ( ! defined( 'FUSION_BUILDER_PLUGIN_URL' ) ) {
	define( 'FUSION_BUILDER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
// Plugin Root File.
if ( ! defined( 'FUSION_BUILDER_PLUGIN_FILE' ) ) {
	define( 'FUSION_BUILDER_PLUGIN_FILE', wp_normalize_path( __FILE__ ) );
}

register_activation_hook( __FILE__, array( 'FusionBuilder', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'FusionBuilder', 'deactivation' ) );

if ( ! class_exists( 'FusionBuilder' ) ) :

	/**
	 * Main FusionBuilder Class.
	 *
	 * @since 1.0
	 */
	class FusionBuilder {

		/**
		 * The one, true instance of this object.
		 *
		 * @static
		 * @access private
		 * @since 1.0
		 * @var object
		 */
		private static $instance;

		/**
		 * An array of allowed post types.
		 *
		 * @access private
		 * @since 1.0
		 * @var array
		 */
		private $allowed_post_types;

		/**
		 * Fusion_Product_Registration
		 *
		 * @access public
		 * @var object Fusion_Product_Registration.
		 */
		public $registration;

		/**
		 * Fusion_Images.
		 *
		 * @access public
		 * @var object
		 */
		public $images;

		/**
		 * An array of body classes to be added.
		 *
		 * @access private
		 * @since 1.1
		 * @var array
		 */
		private $body_classes = array();

		/**
		 * Determine if we're currently upgrading/migration options.
		 *
		 * @static
		 * @access public
		 * @var bool
		 */
		public static $is_updating = false;

		/**
		 * The Fusion_Builder_Options_Panel object.
		 *
		 * @access private
		 * @since 1.1.0
		 * @var object
		 */
		private $fusion_builder_options_panel;

		/**
		 * The Fusion_Builder_Dynamic_CSS object.
		 *
		 * @access private
		 * @since 1.1.3
		 * @var object
		 */
		private $fusion_builder_dynamic_css;

		/**
		 * URL to the js files.
		 *
		 * @static
		 * @access public
		 * @since 1.1.3
		 * @var string
		 */
		public static $js_folder_url;

		/**
		 * Path to the js files.
		 *
		 * @static
		 * @access public
		 * @since 1.1.3
		 * @var string
		 */
		public static $js_folder_path;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 */
		public static function get_instance() {

			// @codingStandardsIgnoreLine
			global $wp_rich_edit, $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge;

			if ( ! isset( $wp_rich_edit ) ) {
				$wp_rich_edit = false;

				// @codingStandardsIgnoreLine
				if ( 'true' == @get_user_option( 'rich_editing' ) || ! @is_user_logged_in() ) { // default to 'true' for logged out users.
					if ( $is_safari ) {
						// @codingStandardsIgnoreLine
						$wp_rich_edit = ! wp_is_mobile() || ( preg_match( '!AppleWebKit/(\d+)!', $_SERVER['HTTP_USER_AGENT'], $match ) && intval( $match[1] ) >= 534 );
					// @codingStandardsIgnoreLine
					} elseif ( $is_gecko || $is_chrome || $is_IE || $is_edge || ( $is_opera && ! wp_is_mobile() ) ) {
						$wp_rich_edit = true;
					}
				}
			}

			if ( $wp_rich_edit ) {

				// If the single instance hasn't been set, set it now.
				if ( null == self::$instance ) {
					self::$instance = new self;
				}
			} else {
				add_action( 'edit_form_after_title', 'fusion_builder_add_notice_of_disabled_rich_editor' );
			}

			// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
			if ( null === self::$instance ) {
				self::$instance = new FusionBuilder();
			}
			return self::$instance;
		}

		/**
		 * Initializes the plugin by setting localization, hooks, filters,
		 * and administrative functions.
		 *
		 * @access private
		 * @since 1.0
		 */
		private function __construct() {
			if ( true == FUSION_BUILDER_DEV_MODE ) {
				$path = '';
			} else {
				$path = '/min';
			}

			self::$js_folder_url = FUSION_BUILDER_PLUGIN_URL . 'assets/js' . $path;
			self::$js_folder_path = FUSION_BUILDER_PLUGIN_DIR . 'assets/js' . $path;

			// Multilingual handling.
			$this->set_is_updating();
			$this->includes();
			$this->textdomain();
			$this->register_scripts();
			$this->init();
			if ( is_admin() && ! class_exists( 'Avada' ) ) {
				$this->registration = new Fusion_Product_Registration( array(
					'type' => 'plugin',
					'name' => 'Fusion Builder',
				) );
			}
			add_action( 'fusion_settings_construct', array( $this, 'add_options_to_fusion_settings' ) );

			$this->versions_compare();
		}

		/**
		 * Initializes the plugin by setting localization, hooks, filters,
		 * and administrative functions.
		 *
		 * @access public
		 * @since 1.0
		 */
		public function init() {

			if ( is_admin() ) {
				do_action( 'fusion_builder_before_init' );
			}

			// Load admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Load shortcode scripts & styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'shortcode_scripts' ) );

			// Display Fusion Builder wrapper.
			$options = get_option( 'fusion_builder_settings' );
			$enable_builder_ui = '1';
			if ( isset( $options['enable_builder_ui'] ) ) {
				$enable_builder_ui = $options['enable_builder_ui'];
			}

			if ( $enable_builder_ui ) {
				add_action( 'edit_form_after_title', array( $this, 'before_main_editor' ), 999 );
				add_action( 'edit_form_after_editor', array( $this, 'after_main_editor' ) );
			}

			// WP editor scripts.
			add_action( 'admin_print_footer_scripts', array( $this, 'enqueue_wp_editor_scripts' ) );

			// Add Page Builder meta box.
			add_action( 'add_meta_boxes', array( $this, 'add_builder_meta_box' ) );
			add_filter( 'wpseo_metabox_prio', array( $this, 'set_yoast_meta_box_priority' ) );

			// Page Builder Helper metaboxes.
			add_action( 'add_meta_boxes', array( $this, 'add_builder_helper_meta_box' ) );

			// Content filter.
			add_filter( 'the_content', array( $this, 'fix_builder_shortcodes' ) );
			add_filter( 'the_content', array( $this, 'fusion_calculate_columns' ), 0 );
			add_filter( 'widget_text', array( $this, 'fusion_calculate_columns' ), 1, 3 );
			add_filter( 'widget_display_callback', array( $this, 'fusion_disable_wpautop_in_widgets' ), 10, 3 );

			// Save Helper metaboxes.
			add_action( 'save_post', array( $this, 'metabox_settings_save_details' ), 10, 2 );

			// Builder mce button.
			add_filter( 'mce_external_plugins', array( $this, 'add_rich_plugins' ) );
			add_filter( 'mce_buttons', array( $this, 'register_rich_buttons' ) );

			// Fusion Builder menu icon.
			add_action( 'admin_head', array( $this, 'admin_styles' ) );

			// Enable shortcodes in text widgets.
			add_filter( 'widget_text', 'do_shortcode' );

			$this->body_classes = $this->body_classes( array() );
			add_filter( 'body_class', array( $this, 'body_class_filter' ) );

			// Replace next page shortcode.
			add_filter( 'the_posts', array( $this, 'next_page' ) );

			// Dynamic-css additions.
			add_filter( 'fusion_dynamic_css_final', array( $this, 'shortcode_styles_dynamic_css' ) );

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_settings_link' ) );
		}

		/**
		 * Helper function for PHP 5.2 compatibility in the next_page method.
		 *
		 * @access private
		 * @since 1.1.0
		 * @param mixed $p Posts.
		 */
		function next_page_helper( $p ) {

			if ( false !== strpos( $p->post_content, '[fusion_builder_next_page]' ) ) {
				$p->post_content = str_replace( '[fusion_builder_next_page]', '<!--nextpage-->', $p->post_content );
			}
			return $p;

		}

		/**
		 * Replace fusion_builder_next_page shortcode with <!--nextpage-->
		 *
		 * @access public
		 * @since 1.1
		 * @param array $posts The array of posts.
		 */
		public function next_page( $posts ) {

			$posts = array_map( array( $this, 'next_page_helper' ), $posts );
			return $posts;

		}

		/**
		 * Set WP editor settings.
		 *
		 * @access public
		 * @since 1.0
		 */
		public function enqueue_wp_editor_scripts() {
			global $typenow;

			if ( isset( $typenow ) && in_array( $typenow, $this->allowed_post_types(), true ) ) {

				if ( ! class_exists( '_WP_Editors' ) ) {
					require wp_normalize_path( ABSPATH . WPINC . '/class-wp-editor.php' );
				}

				$set = _WP_Editors::parse_settings( 'fusion_builder_editor', array() );

				if ( ! current_user_can( 'upload_files' ) ) {
					$set['media_buttons'] = false;
				}

				_WP_Editors::editor_settings( 'fusion_builder_editor', $set );
			}
		}

		/**
		 * Processes that must run when the plugin is activated.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 */
		public static function activation() {

			$installed_plugins = get_plugins();
			$keys = array_keys( get_plugins() );
			$fusion_core_key = '';
			$fusion_core_slug = 'fusion-core';
			$fusion_core_version = '';

			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $fusion_core_slug . '/|', $key ) ) {
					$fusion_core_key = $key;
				}
			}

			if ( $fusion_core_key ) {
				$fusion_core = $installed_plugins[ $fusion_core_key ];
				$fusion_core_version = $fusion_core['Version'];

				if ( version_compare( $fusion_core_version, '3.0', '<' ) ) {
					$message = '<style>#error-page > p{display:-webkit-flex;display:flex;}#error-page img {height: 120px;margin-right:25px;}.fb-heading{font-size: 1.17em; font-weight: bold; display: block; margin-bottom: 15px;}.fb-link{display: inline-block;margin-top:15px;}.fb-link:focus{outline:none;box-shadow:none;}</style>';
					$message .= '<img src="' . plugins_url( 'images/icons/fb_logo.svg', __FILE__ ) . '" />';
					$message .= '<span><span class="fb-heading">Fusion Builder could not be activated</span>';
					$message .= '<span>Fusion Builder can only be activated on installs that use Fusion Core 3.0 or higher. Click the link below to install/activate Fusion Core 3.0, then you can activate Fusion Builder.</span>';
					$message .= '<a class="fb-link" href="' . admin_url( 'admin.php?page=avada-plugins' ) . '">' . esc_attr__( 'Go to the Avada plugin installation page', 'fusion-builder' ) . '</a></span>';
					wp_die( $message ); // WPCS: XSS ok.
				}
			}
			// Delete the patcher caches.
			delete_site_transient( 'fusion_patcher_check_num' );

			if ( ! class_exists( 'Fusion_Cache' ) ) {
				include_once FUSION_BUILDER_PLUGIN_DIR . 'inc/lib/inc/class-fusion-cache.php';
			}

			// Auto activate elements.
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/helpers.php';
			if ( function_exists( 'fusion_builder_auto_activate_element' ) ) {
				fusion_builder_auto_activate_element( 'fusion_gallery' );
			}

			$fusion_cache = new Fusion_Cache();
			$fusion_cache->reset_all_caches();
		}

		/**
		 * Processes that must run when the plugin is deactivated.
		 *
		 * @static
		 * @access public
		 * @since 1.1
		 */
		public static function deactivation() {
			// Delete the patcher caches.
			delete_site_transient( 'fusion_patcher_check_num' );

			if ( ! class_exists( 'Fusion_Cache' ) ) {
				include_once FUSION_BUILDER_PLUGIN_DIR . 'inc/lib/inc/class-fusion-cache.php';
			}

			$fusion_cache = new Fusion_Cache();
			$fusion_cache->reset_all_caches();
		}

		/**
		 * Add TinyMCE rich editor button.
		 *
		 * @access public
		 * @since 1.0
		 * @param array $buttons The array of available buttons.
		 * @return array
		 */
		public function register_rich_buttons( $buttons ) {
			if ( is_array( $buttons ) ) {
				array_push( $buttons, 'fusion_button' );
			}

			return $buttons;
		}

		/**
		 * Add Fusion Builder menu icon.
		 *
		 * @access public
		 * @since 1.0
		 */
		public function admin_styles() {

			$font_url = FUSION_BUILDER_PLUGIN_URL . 'assets/fonts/fonts';
			$font_url = str_replace( array( 'http://', 'https://' ), '//', $font_url );
			?>
			<style type="text/css">
				@font-face {
					font-family: 'icomoon';
					src:url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.eot');
					src:url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.eot?#iefix') format('embedded-opentype'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.woff') format('woff'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.ttf') format('truetype'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.svg#icomoon') format('svg');
					font-weight: normal;
					font-style: normal;
				}
				<?php if ( current_user_can( 'edit_theme_options' ) && ! class_exists( 'Avada' ) ) : ?>
					.dashicons-fusiona-logo:before{
						content: "\e62d";
						font-family: 'icomoon';
						speak: none;
						font-style: normal;
						font-weight: normal;
						font-variant: normal;
						text-transform: none;
						line-height: 1;

						/* Better Font Rendering. */
						-webkit-font-smoothing: antialiased;
						-moz-osx-font-smoothing: grayscale;
					}
				<?php endif; ?>
			</style>
			<?php

		}

		/**
		 * Define TinyMCE rich editor js plugin.
		 *
		 * @access public
		 * @since 1.0
		 * @param array $plugin_array The plugins array.
		 * @return array.
		 */
		public function add_rich_plugins( $plugin_array ) {
			if ( is_admin() ) {
				$plugin_array['fusion_button'] = FUSION_BUILDER_PLUGIN_URL . 'js/fusion-plugin.js';
			}

			return $plugin_array;
		}

		/**
		 * Set global variables.
		 *
		 * @access public
		 * @since 1.0
		 */
		public function init_global_vars() {
			global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query, $small_media_query, $medium_media_query, $large_media_query, $six_columns_media_query, $five_columns_media_query, $four_columns_media_query, $three_columns_media_query, $two_columns_media_query, $one_column_media_query, $dynamic_css, $dynamic_css_helpers;

			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}

			$c_page_id = Fusion::$c_page_id;
			$dynamic_css = $this->fusion_builder_dynamic_css;
			$dynamic_css_helpers = $dynamic_css->get_helpers();

			$side_header_width = ( 'Top' === $fusion_settings->get( 'header_position' ) ) ? 0 : intval( $fusion_settings->get( 'side_header_width' ) );
			$content_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + intval( $fusion_settings->get( 'content_break_point' ) ) ) . 'px)';
			$six_fourty_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + 640 ) . 'px)';
			$content_min_media_query = '@media only screen and (min-width: ' . ( intval( $side_header_width ) + intval( $fusion_settings->get( 'content_break_point' ) ) ) . 'px)';

			$three_twenty_six_fourty_media_query = '@media only screen and (min-device-width: 320px) and (max-device-width: 640px)';
			$ipad_portrait_media_query = '@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait)';

			// Visible options for shortcodes.
			$small_media_query = '@media screen and (max-width: ' . intval( $fusion_settings->get( 'visibility_small' ) ) . 'px)';
			$medium_media_query = '@media screen and (min-width: ' . ( intval( $fusion_settings->get( 'visibility_small' ) ) + 1 ) . 'px) and (max-width: ' . intval( $fusion_settings->get( 'visibility_medium' ) ) . 'px)';
			$large_media_query = '@media screen and (min-width: ' . ( intval( $fusion_settings->get( 'visibility_medium' ) ) + 1 ) . 'px)';

			// # Grid System.
			$main_break_point = (int) $fusion_settings->get( 'grid_main_break_point' );
			if ( 640 < $main_break_point ) {
				$breakpoint_range = $main_break_point - 640;
			} else {
				$breakpoint_range = 360;
			}

			$breakpoint_interval = $breakpoint_range / 5;

			$six_columns_breakpoint   = $main_break_point + $side_header_width;
			$five_columns_breakpoint  = $six_columns_breakpoint - $breakpoint_interval;
			$four_columns_breakpoint  = $five_columns_breakpoint - $breakpoint_interval;
			$three_columns_breakpoint = $four_columns_breakpoint - $breakpoint_interval;
			$two_columns_breakpoint   = $three_columns_breakpoint - $breakpoint_interval;
			$one_column_breakpoint    = $two_columns_breakpoint - $breakpoint_interval;

			$six_columns_media_query   = '@media only screen and (min-width: ' . $five_columns_breakpoint . 'px) and (max-width: ' . $six_columns_breakpoint . 'px)';
			$five_columns_media_query  = '@media only screen and (min-width: ' . $four_columns_breakpoint . 'px) and (max-width: ' . $five_columns_breakpoint . 'px)';
			$four_columns_media_query  = '@media only screen and (min-width: ' . $three_columns_breakpoint . 'px) and (max-width: ' . $four_columns_breakpoint . 'px)';
			$three_columns_media_query = '@media only screen and (min-width: ' . $two_columns_breakpoint . 'px) and (max-width: ' . $three_columns_breakpoint . 'px)';
			$two_columns_media_query   = '@media only screen and (max-width: ' . $two_columns_breakpoint . 'px)';
			$one_column_media_query    = '@media only screen and (max-width: ' . $one_column_breakpoint . 'px)';

		}

		/**
		 * Find and include all shortcodes within shortcodes folder.
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function init_shortcodes() {
			foreach ( glob( FUSION_BUILDER_PLUGIN_DIR . 'shortcodes/*.php', GLOB_NOSORT ) as $filename ) {
				require_once $filename;
			}
		}

		/**
		 * Add helper meta box on allowed post types.
		 *
		 * @access public
		 * @since 1.0
		 * @param mixed $post The post (not used in this context).
		 */
		public function single_settings_meta_box( $post ) {
			global $typenow;

			wp_nonce_field( basename( __FILE__ ), 'fusion_settings_nonce' );

			if ( isset( $typenow ) && in_array( $typenow, $this->allowed_post_types(), true ) ) : ?>
				<p class="fusion_page_settings">
					<input type="text" id="fusion_use_builder" name="fusion_use_builder" value="<?php echo esc_attr( get_post_meta( $post->ID, 'fusion_builder_status', true ) ); ?>" />
				</p>
			<?php endif;

		}

		/**
		 * Add Helper MetaBox.
		 *
		 * @access public
		 * @since 1.0
		 */
		public function add_builder_helper_meta_box() {
			$screens = $this->allowed_post_types();
			add_meta_box( 'fusion_settings_meta_box', esc_attr__( 'Fusion Builder Settings', 'fusion-builder' ), array( $this, 'single_settings_meta_box' ), $screens, 'side', 'high' );
		}

		/**
		 * Save Helper MetaBox Settings.
		 *
		 * @access public
		 * @since 1.0
		 * @param int|string $post_id The post ID.
		 * @param object     $post    The post.
		 * @return int|void
		 */
		public function metabox_settings_save_details( $post_id, $post ) {
			global $pagenow;

			if ( 'post.php' !== $pagenow ) {
				return $post_id;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			$post_type = get_post_type_object( $post->post_type );
			if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
				return $post_id;
			}

			if ( ! isset( $_POST['fusion_settings_nonce'] ) || ! wp_verify_nonce( $_POST['fusion_settings_nonce'], basename( __FILE__ ) ) ) {
				return $post_id;
			}

			if ( isset( $_POST['fusion_use_builder'] ) ) {
				update_post_meta( $post_id, 'fusion_builder_status', sanitize_text_field( $_POST['fusion_use_builder'] ) );
			} else {
				delete_post_meta( $post_id, 'fusion_builder_status' );
			}

		}

		/**
		 * Fix shortcode content on front end by getting rid of random p tags.
		 *
		 * @access public
		 * @since 1.0
		 * @param string $content The content.
		 * return string          The content, modified.
		 */
		public function fix_builder_shortcodes( $content ) {

			if ( is_singular() && ( 'active' === get_post_meta( get_the_ID(), 'fusion_builder_status', true ) || 'yes' === get_post_meta( get_the_ID(), 'fusion_builder_converted', true ) ) ) {
				$content = fusion_builder_fix_shortcodes( $content );
			}
			return $content;
		}

		/**
		 * Count the columns and break up to rows.
		 *
		 * @access public
		 * @since 1.0
		 * @param string $content         The content.
		 * @param string $widget_instance The widget Instance.
		 * @param string $widget          The widget.
		 * @return $content
		 */
		public function fusion_calculate_columns( $content, $widget_instance = '', $widget = '' ) {

			global $global_column_array, $global_column_inner_array;
			$is_in_widget = false;
			$content_id = get_the_id();
			if ( is_object( $widget ) && isset( $widget->id ) ) {
				$content_id = $widget->id;
				$is_in_widget = true;
			}

			$needles = array(
				array(
					'row_opening' => '[fusion_builder_row]',
					'row_closing' => '[/fusion_builder_row]',
					'column_opening' => '[fusion_builder_column ',
				),
				array(
					'row_opening' => '[fusion_builder_row_inner]',
					'row_closing' => '[/fusion_builder_row_inner]',
					'column_opening' => '[fusion_builder_column_inner ',
				),
			);

			$column_opening_positions_index = array();
			$php_version = phpversion();

			foreach ( $needles as $needle ) {
				$column_array = array();
				$last_pos = -1;
				$positions = array();
				$row_index = -1;
				$row_shortcode_name_length = strlen( $needle['row_opening'] );
				$column_shortcode_name_length = strlen( $needle['column_opening'] );

				// Get all positions of [fusion_builder_row shortcode.
				while ( ( $last_pos = strpos( $content, $needle['row_opening'], $last_pos + 1 ) ) !== false ) {
					$positions[] = $last_pos;
				}

				// For each row.
				foreach ( $positions as $position ) {

					$row_closing_position = strpos( $content, $needle['row_closing'], $position );

					// Search within this range/row.
					$range = $row_closing_position - $position + 1;
					// Row content.
					$row_content = substr( $content, $position + strlen( $needle['row_opening'] ), $range );
					$original_row_content = $row_content;

					$row_last_pos = -1;
					$row_position_change = 0;
					$element_positions = array();
					$container_column_counter = 0;
					$column_index = 0;
					$row_index++;
					$element_position_change = 0;
					$last_column_was_full = false;

					while ( ( $row_last_pos = strpos( $row_content,  $needle['column_opening'], $row_last_pos + 1 ) ) !== false ) {
						$element_positions[] = $row_last_pos;
					}

					$number_of_elements = count( $element_positions );

					// Loop through each column.
					foreach ( $element_positions as $key => $element_position ) {
						$column_index++;

						// Get all parameters from column.
						$end_position = strlen( $row_content ) - 1;
						if ( isset( $element_position[ $key + 1 ] ) ) {
							$end_position = $element_position[ $key + 1 ];
						}

						if ( version_compare( $php_version, '5.3', '<' ) ) {
							$params = explode( ']', substr( $row_content, $element_position + $column_shortcode_name_length, $end_position ), 2 );
							$column_values = shortcode_parse_atts( $params[0] );
						} else {
							$column_values = shortcode_parse_atts( strstr( substr( $row_content, $element_position + $column_shortcode_name_length, $end_position ), ']', true ) );
						}

						// Check that type parameter is found, if so calculate row and set spacing to array.
						if ( isset( $column_values['type'] ) ) {
							$column_type = explode( '_', $column_values['type'] );
							$column_width = intval( $column_type[0] ) / intval( $column_type[1] );
							$container_column_counter += $column_width;
							$column_spacing = ( isset( $column_values['spacing'] ) ) ? $column_values['spacing'] : '4%';

							// First column.
							if ( 0 === $key ) {
								if ( 0 < $row_index && ! empty( $column_array[ $row_index - 1 ] ) ) {
									// Get column index of last column of last row.
									end( $column_array[ $row_index - 1 ] );
									$previous_row_last_column = key( $column_array[ $row_index - 1 ] );

									// Add "last" to the last column of previous row.
									if ( false !== strpos( $column_array[ $row_index - 1 ][ $previous_row_last_column ][1], 'first' ) ) {
										$column_array[ $row_index - 1 ][ $previous_row_last_column  ] = array( 'no', 'first_last' );
									} else {
										$column_array[ $row_index - 1 ][ $previous_row_last_column ] = array( 'no', 'last' );
									}
								}

								// If column is full width it is automatically first and last of row.
								if ( 1 == $column_width ) {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index ][ $column_index ] = array( $column_spacing, 'first' );
								}
							} elseif ( 0 == $container_column_counter - $column_width ) { // First column of a row.
								if ( 1 == $column_width ) {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index ][ $column_index ] = array( $column_spacing, 'first' );
								}
							} elseif ( 1 == $container_column_counter ) { // Column fills remaining space in the row exactly.
								// If column is full width it is automatically first and last of row.
								if ( 1 == $column_width ) {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'last' );
								}
							} elseif ( 1 < $container_column_counter ) { // Column overflows the current row.
								$container_column_counter = $column_width;
								$row_index++;

								// Get column index of last column of last row.
								end( $column_array[ $row_index - 1 ] );
								$previous_row_last_column = key( $column_array[ $row_index - 1 ] );

								// Add "last" to the last column of previous row.
								if ( false !== strpos( $column_array[ $row_index - 1 ][ $previous_row_last_column ][1], 'first' ) ) {
									$column_array[ $row_index - 1 ][ $previous_row_last_column  ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index - 1 ][ $previous_row_last_column ] = array( 'no', 'last' );
								}

								// If column is full width it is automatically first and last of row.
								if ( 1 == $column_width ) {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index ][ $column_index ] = array( $column_spacing, 'first' );
								}
							} elseif ( $number_of_elements - 1 === $key ) { // Last column.
								// If column is full width it is automatically first and last of row.
								if ( 1 == $column_width ) {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'first_last' );
								} else {
									$column_array[ $row_index ][ $column_index ] = array( 'no', 'last' );
								}
							} else {
								$column_array[ $row_index ][ $column_index ] = array( $column_spacing, 'default' );
							}
						}

						if ( '[fusion_builder_column ' == $needle['column_opening'] ) {
							$global_column_array[ $content_id ] = $column_array;
						}
						if ( '[fusion_builder_column_inner ' == $needle['column_opening'] ) {
							$global_column_inner_array[ $content_id ] = $column_array;
						}

						$column_opening_positions_index[] = array( $position + $element_position + $row_shortcode_name_length + $column_shortcode_name_length, $row_index . '_' . $column_index );

					}
				}
			}

			/*
			 * Make sure columns and inner columns are sorted correctly for index insertion.
			 * Use the start index on shortcode in the content string as order value.
			 */
			usort( $column_opening_positions_index, array( $this, 'column_opening_positions_index_substract' ) );

			// Add column index and if in widget also the widget ID to the column shortcodes.
			foreach ( array_reverse( $column_opening_positions_index ) as $position ) {
				if ( $is_in_widget ) {
					$content = substr_replace( $content, 'row_column_index="' . $position[1] . '" widget_id="' . $widget->id . '" ', $position[0], 0 );
				} else {
					$content = substr_replace( $content, 'row_column_index="' . $position[1] . '" ', $position[0], 0 );
				}
			}

			return $content;
		}

		/**
		 * Fixes line break issue for shortcodes in widgets.
		 *
		 * @access public
		 * @since  1.2
		 * @param  string $widget_instance The widget Instance.
		 * @param  string $widget          The widget.
		 * @param  Array  $args            The Args.
		 * @return $instance
		 */
		public function fusion_disable_wpautop_in_widgets( $widget_instance, $widget, $args ) {
			if ( isset( $widget_instance['text'] ) && false !== strpos( $widget_instance['text'], '[fusion_' ) ) {
				remove_filter( 'widget_text_content', 'wpautop' );
			}
			return $widget_instance;
		}

		/**
		 * Helper function that substracts values.
		 * Added for compatibility with older PHP versions.
		 *
		 * @access public
		 * @since 1.0.3
		 * @param array $a 1st value.
		 * @param array $b 2nd value.
		 * @return int
		 */
		public function column_opening_positions_index_substract( $a, $b ) {
			return $a[0] - $b[0];
		}

		/**
		 * Shortcode Scripts & Styles.
		 * Enqueues all necessary scripts and styles for shortcodes.
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function shortcode_scripts() {
			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}
			$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
			$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;

			// If we're compiling CSS then we don't need to enqueue separate files.
			// Look at the shortcode_styles_dynamic_css method.
			if ( 'off' !== $fusion_settings->get( 'css_cache_method' ) ) {
				return;
			}
			$shortcodes_css_filename = ( true == FUSION_BUILDER_DEV_MODE ) ? 'css/fusion-shortcodes.css' : 'css/fusion-shortcodes.min.css';
			wp_enqueue_style( 'fusion-builder-shortcodes', FUSION_BUILDER_PLUGIN_URL . $shortcodes_css_filename, array(), FUSION_BUILDER_VERSION );

			if ( fusion_library()->get_option( 'use_animate_css' ) ) {
				wp_enqueue_style( 'fusion-builder-animations', FUSION_BUILDER_PLUGIN_URL . 'animations.min.css', array(), FUSION_BUILDER_VERSION );
			}

			if ( fusion_library()->get_option( 'status_lightbox' ) ) {
				wp_enqueue_style( 'fusion-builder-ilightbox', FUSION_BUILDER_PLUGIN_URL . 'ilightbox.min.css', array(), FUSION_BUILDER_VERSION );
			}
			// Fusion Builder frontend js.
			// @codingStandardsIgnoreLine
			// wp_enqueue_script( 'fusion_builder_frontend', FUSION_BUILDER_PLUGIN_URL . 'js/fusion-builder-front.js', array( 'jquery' ), FUSION_BUILDER_VERSION, true );

			// Font-awesome CSS.
			if ( fusion_library()->get_option( 'status_fontawesome' ) ) {
				wp_enqueue_style( 'fusion-font-awesome', FUSION_BUILDER_PLUGIN_URL . 'inc/lib/assets/fonts/fontawesome/font-awesome.css', array(), FUSION_BUILDER_VERSION );
			}

		}

		/**
		 * Add shortcode styles in dynamic-css.
		 *
		 * @access public
		 * @since 1.1.5
		 * @param string $original_styles The compiled styles.
		 * @return string The compiled styles with the new ones appended.
		 */
		public function shortcode_styles_dynamic_css( $original_styles ) {

			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}

			$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
			$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;
			$styles = '';

			if ( 'off' !== $fusion_settings->get( 'css_cache_method' ) ) {

				$wp_filesystem = Fusion_Helper::init_filesystem();

				// Stylesheet ID: fusion-builder-shortcodes. @codingStandardsIgnoreLine
				$styles .= @file_get_contents( FUSION_BUILDER_PLUGIN_DIR . 'css/fusion-shortcodes.min.css' );

				// Stylesheet ID: fusion-builder-animations. @codingStandardsIgnoreLine
				if ( fusion_library()->get_option( 'use_animate_css' ) ) {
					$styles .= @file_get_contents( FUSION_BUILDER_PLUGIN_DIR . 'animations.min.css' );
				}

				// Stylesheet ID: fusion-builder-ilightbox. @codingStandardsIgnoreLine
				if ( fusion_library()->get_option( 'status_lightbox' ) ) {
					$ilightbox_styles = @file_get_contents( FUSION_BUILDER_PLUGIN_DIR . 'ilightbox.min.css' );
					$ilightbox_url    = set_url_scheme( FUSION_BUILDER_PLUGIN_URL . 'assets/images/' );
					$styles .= str_replace( 'url(assets/images/', 'url(' . $ilightbox_url, $ilightbox_styles );
				}

				// Stylesheet ID: fusion-font-awesome. @codingStandardsIgnoreLine
				if ( fusion_library()->get_option( 'status_fontawesome' ) ) {
					$font_awesome_styles = @file_get_contents( FUSION_BUILDER_PLUGIN_DIR . 'inc/lib/assets/fonts/fontawesome/font-awesome.min.css' );
					$font_awesome_url    = set_url_scheme( FUSION_BUILDER_PLUGIN_URL . 'inc/lib/assets/fonts/fontawesome/' );

					$styles .= str_replace( 'url(fontawesome-webfont', 'url(' . $font_awesome_url . 'fontawesome-webfont', $font_awesome_styles );
				}
			}
			return $styles . $original_styles;
		}

		/**
		 * Shortcode Scripts & Styles.
		 * Registers the FB library scripts used as dependency.
		 *
		 * @access public
		 * @since 1.1
		 * @return void
		 */
		public function register_scripts() {

			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}

			if ( fusion_library()->get_option( 'use_animate_css' ) ) {
				Fusion_Dynamic_JS::register_script(
					'fusion-animations',
					self::$js_folder_url . '/general/fusion-animations.js',
					self::$js_folder_path . '/general/fusion-animations.js',
					array( 'jquery', 'cssua', 'fusion-waypoints' ),
					'1',
					true
				);
			}
			Fusion_Dynamic_JS::localize_script(
				'fusion-animations',
				'fusionAnimationsVars',
				array(
					'disable_mobile_animate_css' => $fusion_settings->get( 'disable_mobile_animate_css' ),
				)
			);
			Fusion_Dynamic_JS::register_script(
				'jquery-count-to',
				self::$js_folder_url . '/library/jquery.countTo.js',
				self::$js_folder_path . '/library/jquery.countTo.js',
				array( 'jquery' ),
				'1',
				true
			);
			Fusion_Dynamic_JS::register_script(
				'jquery-count-down',
				self::$js_folder_url . '/library/jquery.countdown.js',
				self::$js_folder_path . '/library/jquery.countdown.js',
				array( 'jquery' ),
				'1.0',
				true
			);
			Fusion_Dynamic_JS::localize_script(
				'fusion-video',
				'fusionVideoVars',
				array(
					'status_vimeo' => $fusion_settings->get( 'status_vimeo' ),
				)
			);
			Fusion_Dynamic_JS::register_script(
				'fusion-video',
				self::$js_folder_url . '/general/fusion-video.js',
				self::$js_folder_path . '/general/fusion-video.js',
				array( 'jquery', 'froogaloop', 'fusion-video-general' ),
				'1',
				true
			);
		}

		/**
		 * Admin Scripts.
		 * Enqueues all necessary scripts in the WP Admin to run Fusion Builder.
		 *
		 * @access public
		 * @since 1.0
		 * @param string $hook Not used in the context of this function.
		 * @return void
		 */
		public function admin_scripts( $hook ) {
			global $typenow, $fusion_builder_elements, $fusion_builder_multi_elements, $pagenow;

			// Load Fusion builder importer js.
			// @codingStandardsIgnoreLine
			if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'fusion-builder-settings' == $_GET['page'] ) {
				wp_enqueue_script( 'fusion_builder_importer_js', FUSION_BUILDER_PLUGIN_URL . 'inc/importer/js/fusion-builer-importer.js', '', FUSION_BUILDER_VERSION, true );

				// Localize Scripts.
				wp_localize_script( 'fusion_builder_importer_js', 'fusionBuilderConfig', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'fusion_import_nonce' => wp_create_nonce( 'fusion_import_nonce' ),
				) );
			}

			// Load icons if Avada is not installed / active.
			if ( ! class_exists( 'Avada' ) ) {
				wp_enqueue_style( 'fusion-font-icomoon', FUSION_BUILDER_PLUGIN_URL . 'assets/fonts/icomoon.css', false, FUSION_BUILDER_VERSION, 'all' );
			}

			if ( ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && post_type_supports( $typenow, 'editor' ) ) {

				// TODO: has to be loaded for shortcode generator to work. Even if FB is disabled for this post type.
				// @codingStandardsIgnoreLine
				// if ( is_admin() && isset( $typenow ) && in_array( $typenow, $this->allowed_post_types(), true ) ) {

				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-button' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'underscore' );
				wp_enqueue_script( 'backbone' );
				wp_enqueue_script( 'jquery-color' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				// Code Mirror.
				wp_enqueue_script( 'fusion-builder-codemirror-js', FUSION_BUILDER_PLUGIN_URL . 'assets/js/codemirror/codemirror.js', array( 'jquery' ), FUSION_BUILDER_VERSION, true );
				// Code Mirror.
				wp_enqueue_style( 'fusion-builder-codemirror-css', FUSION_BUILDER_PLUGIN_URL . 'assets/js/codemirror/codemirror.css', array(), FUSION_BUILDER_VERSION, 'all' );

				// WP Editor.
				wp_enqueue_script( 'fusion-builder-wp-editor-js', FUSION_BUILDER_PLUGIN_URL . 'js/wp-editor.js', array( 'jquery' ), FUSION_BUILDER_VERSION, true );

				// ColorPicker Alpha Channel.
				wp_enqueue_script( 'wp-color-picker-alpha', FUSION_BUILDER_PLUGIN_URL . 'js/wp-color-picker-alpha.js', array( 'wp-color-picker', 'jquery-color' ), FUSION_BUILDER_VERSION );

				// Bootstrap date and time picker.
				wp_enqueue_script( 'bootstrap-datetimepicker', FUSION_BUILDER_PLUGIN_URL . 'js/bootstrap-datetimepicker.min.js', array( 'jquery' ), FUSION_BUILDER_VERSION );
				wp_enqueue_style( 'bootstrap-datetimepicker', FUSION_BUILDER_PLUGIN_URL . 'css/bootstrap-datetimepicker.css', array(), '5.0.0', 'all' );

				// The noUi Slider.
				wp_enqueue_style( 'avadaredux-nouislider-css', FUSION_BUILDER_PLUGIN_URL . 'css/nouislider.css', array(), '5.0.0', 'all' );

				wp_enqueue_script( 'avadaredux-nouislider-js', FUSION_BUILDER_PLUGIN_URL . 'js/nouislider.min.js', array( 'jquery' ), '8.5.1', true );

				wp_enqueue_script( 'wnumb-js', FUSION_BUILDER_PLUGIN_URL . 'js/wNumb.js', array( 'jquery' ), '1.0.2', true );

				// FontAwesome.
				wp_enqueue_style( 'fusion-font-awesome', FUSION_BUILDER_PLUGIN_URL . 'inc/lib/assets/fonts/fontawesome/font-awesome.css', false, FUSION_BUILDER_VERSION, 'all' );

				// Icomoon font.
				wp_enqueue_style( 'fusion-font-icomoon', FUSION_BUILDER_PLUGIN_URL . 'assets/fonts/icomoon.css', false, FUSION_BUILDER_VERSION, 'all' );
				wp_enqueue_style( 'fusion-chosen-css', FUSION_BUILDER_PLUGIN_URL . 'tinymce/css/chosen.css', false, FUSION_BUILDER_VERSION, 'all' );

				// Chosen js.
				wp_enqueue_script( 'fusion_builder_chosen_js', FUSION_BUILDER_PLUGIN_URL . 'js/chosen.jquery.min.js', '', FUSION_BUILDER_VERSION, true );

				// Developer mode is enabled.
				if ( true == FUSION_BUILDER_DEV_MODE ) {

					// Utility for underscore.js templates.
					wp_enqueue_script( 'fusion_builder_app_util_js', FUSION_BUILDER_PLUGIN_URL . 'js/util.js', array( 'jquery', 'jquery-ui-core', 'underscore', 'backbone' ), FUSION_BUILDER_VERSION, true );

					// Sticky builder header.
					wp_enqueue_script( 'fusion-sticky-header', FUSION_BUILDER_PLUGIN_URL . 'js/sticky-menu.js', array( 'jquery', 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION );

					// Backbone Models.
					wp_enqueue_script( 'fusion_builder_model_element', FUSION_BUILDER_PLUGIN_URL . 'js/models/model-element.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_model_view_manager', FUSION_BUILDER_PLUGIN_URL . 'js/models/model-view-manager.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					// Backbone Element Collection.
					wp_enqueue_script( 'fusion_builder_collection_element', FUSION_BUILDER_PLUGIN_URL . 'js/collections/collection-element.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					// Backbone Views.
					wp_enqueue_script( 'fusion_builder_view_element', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-element.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_model_view_element_preview', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-element-preview.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_elements_library', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-elements-library.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_generator_elements', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-generator-elements.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_container', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-container.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_blank_page', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-blank-page.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_row', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-row.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_row_nested', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-row-nested.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_column_nested', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-column-nested.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_column', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-column.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_modal', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-modal.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_next_page', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-next-page.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_element_settings', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-element-settings.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_multi_element_child_settings', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-multi-element-child-settings.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_multi_element_ui', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-multi-element-sortable-ui.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_multi_element_child_ui', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-multi-element-sortable-child.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					wp_enqueue_script( 'fusion_builder_view_column_library', FUSION_BUILDER_PLUGIN_URL . 'js/views/view-column-library.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					// Backbone App.
					wp_enqueue_script( 'fusion_builder_app_js', FUSION_BUILDER_PLUGIN_URL . 'js/app.js', array( 'jquery', 'jquery-ui-core', 'underscore', 'backbone', 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					// Shortcode Generator.
					wp_enqueue_script( 'fusion_builder_sc_generator', FUSION_BUILDER_PLUGIN_URL . 'js/fusion-shortcode-generator.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					 // History.
					wp_enqueue_script( 'fusion_builder_history', FUSION_BUILDER_PLUGIN_URL . 'js/fusion-history.js', array( 'fusion_builder_app_util_js' ), FUSION_BUILDER_VERSION, true );

					// Localize Scripts.
					wp_localize_script( 'fusion_builder_app_js', 'fusionBuilderConfig', array(
						'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
						'fusion_load_nonce'          => wp_create_nonce( 'fusion_load_nonce' ),
						'fontawesomeicons'           => fusion_get_icons_array(),
						'fusion_builder_plugin_dir'  => FUSION_BUILDER_PLUGIN_URL,
						'includes_url'               => includes_url(),
						'disable_encoding'           => get_option( 'avada_disable_encoding' ),
						'full_width'                 => apply_filters( 'fusion_builder_width_hundred_percent', '' ),
					) );

					// Localize scripts. Text strings.
					wp_localize_script( 'fusion_builder_app_js', 'fusionBuilderText', fusion_builder_textdomain_strings() );

					// Developer mode is disabled.
				} else {

					// Fusion Builder js.
					wp_enqueue_script( 'fusion_builder', FUSION_BUILDER_PLUGIN_URL . 'js/fusion-builder.js', array( 'jquery' ), FUSION_BUILDER_VERSION, true );

					// Localize Script.
					wp_localize_script( 'fusion_builder', 'fusionBuilderConfig', array(
						'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
						'fusion_load_nonce'          => wp_create_nonce( 'fusion_load_nonce' ),
						'fontawesomeicons'           => fusion_get_icons_array(),
						'fusion_builder_plugin_dir'  => FUSION_BUILDER_PLUGIN_URL,
						'includes_url'               => includes_url(),
						'disable_encoding'           => get_option( 'avada_disable_encoding' ),
						'full_width'                 => apply_filters( 'fusion_builder_width_hundred_percent', '' ),
					) );

					// Localize script. Text strings.
					wp_localize_script( 'fusion_builder', 'fusionBuilderText', fusion_builder_textdomain_strings() );

				}

				// Builder Styling.
				wp_enqueue_style( 'fusion_builder_css', FUSION_BUILDER_PLUGIN_URL . 'css/fusion-builder.css', array(), FUSION_BUILDER_VERSION );

				// Elements Preview.
				wp_enqueue_style( 'fusion_element_preview_css', FUSION_BUILDER_PLUGIN_URL . 'css/elements-preview.css', array(), FUSION_BUILDER_VERSION );

				// Filter disabled elements.
				$fusion_builder_elements = fusion_builder_filter_available_elements();

				// Create elements js object. Load element's js and css.
				if ( ! empty( $fusion_builder_elements ) ) {

					$fusion_builder_elements = apply_filters( 'fusion_builder_all_elements', $fusion_builder_elements );

					echo '<script>var fusionAllElements = ' . wp_json_encode( $fusion_builder_elements ) . ';</script>';

					// Load modules backend js and css.
					foreach ( $fusion_builder_elements as $module ) {
						// JS file.
						if ( ! empty( $module['admin_enqueue_js'] ) ) {
							wp_enqueue_script( $module['shortcode'], $module['admin_enqueue_js'], '', FUSION_BUILDER_VERSION, true );
						}

						// CSS file.
						if ( ! empty( $module['admin_enqueue_css'] ) ) {
							wp_enqueue_style( $module['shortcode'], $module['admin_enqueue_css'], array(), FUSION_BUILDER_VERSION );
						}

						// Preview template.
						if ( ! empty( $module['preview'] ) ) {
							require_once wp_normalize_path( $module['preview'] );
						}

						// Custom settings template.
						if ( ! empty( $module['custom_settings_template_file'] ) ) {
							require_once wp_normalize_path( $module['custom_settings_template_file'] );
						}
						// Custom settings view.
						if ( ! empty( $module['custom_settings_view_js'] ) ) {
							wp_enqueue_script( $module['shortcode'] . '_custom_settings_view', $module['custom_settings_view_js'], '', FUSION_BUILDER_VERSION, true );
						}
					}
				}

				// Multi Element object.
				if ( ! empty( $fusion_builder_multi_elements ) ) {
					echo '<script>var fusionMultiElements = ' . wp_json_encode( $fusion_builder_multi_elements ) . ';</script>';
				}

				// Builder admin scripts hook.
				do_action( 'fusion_builder_admin_scripts_hook' );

				// }
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			// Helper functions.
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/helpers.php';
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/class-fusion-builder-options.php';
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/class-fusion-builder-dynamic-css.php';

			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/class-fusion-builder-element.php';
			Fusion_Builder_Options::get_instance();
			$this->fusion_builder_dynamic_css = new Fusion_Builder_Dynamic_CSS();

			// Load globals media vars.
			$this->init_global_vars();

			do_action( 'fusion_builder_shortcodes_init' );

			// Load all shortcode elements.
			$this->init_shortcodes();
			// Shortcode related functions.
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/shortcodes.php';

			// Page layouts.
			require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/layouts.php';

			if ( is_admin() ) {
				// Importer/Exporter.
				require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/importer/importer.php';
				// Builder underscores templates.
				require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/templates.php';
				// Settings.
				require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/fusion-builder-admin.php';

				require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/class-fusion-builder-options-panel.php';

				if ( class_exists( 'Avada' ) ) {
					$this->fusion_builder_options_panel = new Fusion_Builder_Options_Panel();
				}
			}

			// WooCommerce.
			if ( class_exists( 'WooCommerce' ) ) {
				require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/woocommerce/woo-config.php';
			}
		}

		/**
		 * Fusion Builder wrapper.
		 *
		 * @access public
		 * @since 1.0
		 * @param object $post The post.
		 */
		public function before_main_editor( $post ) {
			global $typenow;

			if ( isset( $typenow ) && in_array( $typenow, $this->allowed_post_types() ) ) {

				$builder_active = 'active' === get_post_meta( $post->ID, 'fusion_builder_status', true ) ? true : false;

				$builder_enabled_data = '';
				$builder_settings = get_option( 'fusion_builder_settings' );
				if ( isset( $builder_settings['enable_builder_ui_by_default'] ) && $builder_settings['enable_builder_ui_by_default'] && 'active' !== get_post_meta( $post->ID, 'fusion_builder_status', true ) ) {
					$builder_enabled_data = ' data-enabled="1"';
				}

				$editor_label   = ( $builder_active ) ? esc_attr__( 'Use Default Editor', 'fusion-builder' ) : esc_attr__( 'Use Fusion Builder', 'fusion-builder' );
				$builder_hidden = ( $builder_active ) ? ' class="fusion_builder_hidden"' : '';
				$builder_active = ( $builder_active ) ? ' fusion_builder_is_active' : '';

				echo '<a href="#" id="fusion_toggle_builder" data-builder="' . esc_attr__( 'Use Fusion Builder', 'fusion-builder' ) . '" data-editor="' . esc_attr__( 'Use Default Editor', 'fusion-builder' ) . '"' . $builder_enabled_data . ' class="button button-primary button-large' . $builder_active . '">' . $editor_label . '</a><div id="fusion_main_editor_wrap"' . $builder_hidden . '>'; // WPCS: XSS ok.
			}
		}

		/**
		 * Fusion Builder wrapper.
		 *
		 * @package Fusion Builder
		 * @author Theme Fusion
		 */
		public function after_main_editor() {
			global $typenow;

			if ( isset( $typenow ) && in_array( $typenow, $this->allowed_post_types() ) ) {
				echo '</div>';
			}
		}
		/**
		 * Default post types.
		 *
		 * @package Fusion Builder
		 * @author Theme Fusion
		 * @since 1.0
		 */
		public static function default_post_types() {

			// Defaults.
			$post_types = array(
				'page',
				'post',
				'avada_faq',
				'avada_portfolio',
				'fusion_template',
			);
			// Allow theme developers to change default selection via filter.  Can also do so for Avada.
			return apply_filters( 'fusion_builder_default_post_types', $post_types );
		}
		/**
		 * Builder is displayed on the following post types.
		 *
		 * @package Fusion Builder
		 * @author Theme Fusion
		 */
		private function allowed_post_types() {

			$options = get_option( 'fusion_builder_settings', array() );

			if ( ! empty( $options ) && isset( $options['post_types'] ) ) {
				// If there are options saved, used them.
				$post_types = ( ' ' === $options['post_types'] ) ? array() : $options['post_types'];
				return apply_filters( 'fusion_builder_allowed_post_types', $post_types );
			} else {
				// Otherwise use defaults.
				return self::default_post_types();
			}

		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function textdomain() {

			// Get text domain.
			$domain = 'fusion-builder';

			// The "plugin_locale" filter is also used in load_plugin_textdomain().
			$user_locale = fusion_get_user_locale();
			$locale = apply_filters( 'plugin_locale', $user_locale, $domain );

			// Create path to custom language file.
			$custom_mo = WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo';

			if ( file_exists( $custom_mo ) ) {
				load_textdomain( $domain, $custom_mo );
			} else {
				load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}

		/**
		 * Add Page Builder MetaBox.
		 *
		 * @since 1.0
		 * @param  string $post_type  Post type slug.
		 * @return void
		 */
		public function add_builder_meta_box( $post_type ) {
			if ( post_type_supports( $post_type, 'editor' ) ) {
				add_meta_box( 'fusion_builder_layout', '<span class="fusion-builder-logo"></span><span class="fusion-builder-title">' . esc_attr__( 'Fusion Builder', 'fusion-builder' ) . '</span><a href="https://theme-fusion.com/support/documentation/fusion-builder-documentation/" target="_blank" rel="noopener noreferrer"><span class="fusion-builder-help dashicons dashicons-editor-help"></span></a>', 'fusion_pagebuilder_meta_box', null, 'normal', 'high' );
			}
		}

		/**
		 * Resets the meta box priority for Yoast SEO.
		 * Devs can override by using fusion_builder_yoast_meta_box_priority filter.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return string The meta box priority.
		 */
		public function set_yoast_meta_box_priority() {
			return apply_filters( 'fusion_builder_yoast_meta_box_priority', 'default' );
		}

		/**
		 * Function to apply attributes to HTML tags.
		 * Devs can override attributes in a child theme by using the correct slug.
		 *
		 * @since 1.0.0
		 * @access public
		 * @param  string $slug    Slug to refer to the HTML tag.
		 * @param  array  $attributes Attributes for HTML tag.
		 * @return string The string of all attributes.
		 */
		public static function attributes( $slug, $attributes = array() ) {

			$out = '';
			$attr = apply_filters( "fusion_attr_{$slug}", $attributes );

			if ( empty( $attr ) ) {
				$attr['class'] = $slug;
			}

			foreach ( $attr as $name => $value ) {
				if ( 'valueless_attribute' === $value ) {
					$out .= ' ' . esc_html( $name );
				} else if ( ! empty( $value ) || strlen( $value ) > 0 || is_bool( $value ) ) {
					$out .= ' ' . esc_html( $name ) . '="' . esc_attr( $value ) . '"';
				}
			}

			return trim( $out );
		}

		/**
		 * Function to get the default shortcode param values applied.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param  array $defaults Array of defaults.
		 * @param  array $args     Array with user set param values.
		 * @return array
		 */
		public static function set_shortcode_defaults( $defaults, $args ) {

			if ( ! $args ) {
				$args = array();
			}

			$args = shortcode_atts( $defaults, $args );

			foreach ( $args as $key => $value ) {
				if ( '' === $value || '|' === $value ) {
					$args[ $key ] = $defaults[ $key ];
				}
			}

			return $args;
		}

		/**
		 * Returns an array with the rgb values.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param string $hex The HEX color.
		 * @return array
		 */
		public static function hex2rgb( $hex ) {
			$hex = str_replace( '#', '', $hex );

			if ( 3 === strlen( $hex ) ) {
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			$rgb = array( $r, $g, $b );

			return $rgb;
		}

		/**
		 * Tweaks the icon names.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param string $icon The icon.
		 * @return string
		 */
		public static function font_awesome_name_handler( $icon ) {

			$old_icons['arrow']                  = 'angle-right';
			$old_icons['asterik']                = 'asterisk';
			$old_icons['cross']                  = 'times';
			$old_icons['ban-circle']             = 'ban';
			$old_icons['bar-chart']              = 'bar-chart-o';
			$old_icons['beaker']                 = 'flask';
			$old_icons['bell']                   = 'bell-o';
			$old_icons['bell-alt']               = 'bell';
			$old_icons['bitbucket-sign']         = 'bitbucket-square';
			$old_icons['bookmark-empty']         = 'bookmark-o';
			$old_icons['building']               = 'building-o';
			$old_icons['calendar-empty']         = 'calendar-o';
			$old_icons['check-empty']            = 'square-o';
			$old_icons['check-minus']            = 'minus-square-o';
			$old_icons['check-sign']             = 'check-square';
			$old_icons['check']                  = 'check-square-o';
			$old_icons['chevron-sign-down']      = 'chevron-circle-down';
			$old_icons['chevron-sign-left']      = 'chevron-circle-left';
			$old_icons['chevron-sign-right']     = 'chevron-circle-right';
			$old_icons['chevron-sign-up']        = 'chevron-circle-up';
			$old_icons['circle-arrow-down']      = 'arrow-circle-down';
			$old_icons['circle-arrow-left']      = 'arrow-circle-left';
			$old_icons['circle-arrow-right']     = 'arrow-circle-right';
			$old_icons['circle-arrow-up']        = 'arrow-circle-up';
			$old_icons['circle-blank']           = 'circle-o';
			$old_icons['cny']                    = 'rub';
			$old_icons['collapse-alt']           = 'minus-square-o';
			$old_icons['collapse-top']           = 'caret-square-o-up';
			$old_icons['collapse']               = 'caret-square-o-down';
			$old_icons['comment-alt']            = 'comment-o';
			$old_icons['comments-alt']           = 'comments-o';
			$old_icons['copy']                   = 'files-o';
			$old_icons['cut']                    = 'scissors';
			$old_icons['dashboard']              = 'tachometer';
			$old_icons['double-angle-down']      = 'angle-double-down';
			$old_icons['double-angle-left']      = 'angle-double-left';
			$old_icons['double-angle-right']     = 'angle-double-right';
			$old_icons['double-angle-up']        = 'angle-double-up';
			$old_icons['download']               = 'arrow-circle-o-down';
			$old_icons['download-alt']           = 'download';
			$old_icons['edit-sign']              = 'pencil-square';
			$old_icons['edit']                   = 'pencil-square-o';
			$old_icons['ellipsis-horizontal']    = 'ellipsis-h';
			$old_icons['ellipsis-vertical']      = 'ellipsis-v';
			$old_icons['envelope-alt']           = 'envelope-o';
			$old_icons['exclamation-sign']       = 'exclamation-circle';
			$old_icons['expand-alt']             = 'plus-square-o';
			$old_icons['expand']                 = 'caret-square-o-right';
			$old_icons['external-link-sign']     = 'external-link-square';
			$old_icons['eye-close']              = 'eye-slash';
			$old_icons['eye-open']               = 'eye';
			$old_icons['facebook-sign']          = 'facebook-square';
			$old_icons['facetime-video']         = 'video-camera';
			$old_icons['file-alt']               = 'file-o';
			$old_icons['file-text-alt']          = 'file-text-o';
			$old_icons['flag-alt']               = 'flag-o';
			$old_icons['folder-close-alt']       = 'folder-o';
			$old_icons['folder-close']           = 'folder';
			$old_icons['folder-open-alt']        = 'folder-open-o';
			$old_icons['food']                   = 'cutlery';
			$old_icons['frown']                  = 'frown-o';
			$old_icons['fullscreen']             = 'arrows-alt';
			$old_icons['github-sign']            = 'github-square';
			$old_icons['google-plus-sign']       = 'google-plus-square';
			$old_icons['group']                  = 'users';
			$old_icons['h-sign']                 = 'h-square';
			$old_icons['hand-down']              = 'hand-o-down';
			$old_icons['hand-left']              = 'hand-o-left';
			$old_icons['hand-right']             = 'hand-o-right';
			$old_icons['hand-up']                = 'hand-o-up';
			$old_icons['hdd']                    = 'hdd-o';
			$old_icons['heart-empty']            = 'heart-o';
			$old_icons['hospital']               = 'hospital-o';
			$old_icons['indent-left']            = 'outdent';
			$old_icons['indent-right']           = 'indent';
			$old_icons['info-sign']              = 'info-circle';
			$old_icons['keyboard']               = 'keyboard-o';
			$old_icons['legal']                  = 'gavel';
			$old_icons['lemon']                  = 'lemon-o';
			$old_icons['lightbulb']              = 'lightbulb-o';
			$old_icons['linkedin-sign']          = 'linkedin-square';
			$old_icons['meh']                    = 'meh-o';
			$old_icons['microphone-off']         = 'microphone-slash';
			$old_icons['minus-sign-alt']         = 'minus-square';
			$old_icons['minus-sign']             = 'minus-circle';
			$old_icons['mobile-phone']           = 'mobile';
			$old_icons['moon']                   = 'moon-o';
			$old_icons['move']                   = 'arrows';
			$old_icons['off']                    = 'power-off';
			$old_icons['ok-circle']              = 'check-circle-o';
			$old_icons['ok-sign']                = 'check-circle';
			$old_icons['ok']                     = 'check';
			$old_icons['paper-clip']             = 'paperclip';
			$old_icons['paste']                  = 'clipboard';
			$old_icons['phone-sign']             = 'phone-square';
			$old_icons['picture']                = 'picture-o';
			$old_icons['pinterest-sign']         = 'pinterest-square';
			$old_icons['play-circle']            = 'play-circle-o';
			$old_icons['play-sign']              = 'play-circle';
			$old_icons['plus-sign-alt']          = 'plus-square';
			$old_icons['plus-sign']              = 'plus-circle';
			$old_icons['pushpin']                = 'thumb-tack';
			$old_icons['question-sign']          = 'question-circle';
			$old_icons['remove-circle']          = 'times-circle-o';
			$old_icons['remove-sign']            = 'times-circle';
			$old_icons['remove']                 = 'times';
			$old_icons['reorder']                = 'bars';
			$old_icons['resize-full']            = 'expand';
			$old_icons['resize-horizontal']      = 'arrows-h';
			$old_icons['resize-small']           = 'compress';
			$old_icons['resize-vertical']        = 'arrows-v';
			$old_icons['rss-sign']               = 'rss-square';
			$old_icons['save']                   = 'floppy-o';
			$old_icons['screenshot']             = 'crosshairs';
			$old_icons['share-alt']              = 'share';
			$old_icons['share-sign']             = 'share-square';
			$old_icons['share']                  = 'share-square-o';
			$old_icons['sign-blank']             = 'square';
			$old_icons['signin']                 = 'sign-in';
			$old_icons['signout']                = 'sign-out';
			$old_icons['smile']                  = 'smile-o';
			$old_icons['sort-by-alphabet-alt']   = 'sort-alpha-desc';
			$old_icons['sort-by-alphabet']       = 'sort-alpha-asc';
			$old_icons['sort-by-attributes-alt'] = 'sort-amount-desc';
			$old_icons['sort-by-attributes']     = 'sort-amount-asc';
			$old_icons['sort-by-order-alt']      = 'sort-numeric-desc';
			$old_icons['sort-by-order']          = 'sort-numeric-asc';
			$old_icons['sort-down']              = 'sort-asc';
			$old_icons['sort-up']                = 'sort-desc';
			$old_icons['stackexchange']          = 'stack-overflow';
			$old_icons['star-empty']             = 'star-o';
			$old_icons['star-half-empty']        = 'star-half-o';
			$old_icons['sun']                    = 'sun-o';
			$old_icons['thumbs-down-alt']        = 'thumbs-o-down';
			$old_icons['thumbs-up-alt']          = 'thumbs-o-up';
			$old_icons['time']                   = 'clock-o';
			$old_icons['trash']                  = 'trash-o';
			$old_icons['tumblr-sign']            = 'tumblr-square';
			$old_icons['twitter-sign']           = 'twitter-square';
			$old_icons['unlink']                 = 'chain-broken';
			$old_icons['upload']                 = 'arrow-circle-o-up';
			$old_icons['upload-alt']             = 'upload';
			$old_icons['warning-sign']           = 'exclamation-triangle';
			$old_icons['xing-sign']              = 'xing-square';
			$old_icons['youtube-sign']           = 'youtube-square';
			$old_icons['zoom-in']                = 'search-plus';
			$old_icons['zoom-out']               = 'search-minus';

			if ( isset( $icon ) && ! empty( $icon ) ) {
				if ( 'icon-' === substr( $icon, 0, 5 ) || 'fa-' !== substr( $icon, 0, 3 ) ) {
					$icon = str_replace( 'icon-', 'fa-', $icon );

					if ( array_key_exists( str_replace( 'fa-', '', $icon ), $old_icons ) ) {
						$fa_icon = 'fa-' . $old_icons[ str_replace( 'fa-', '', $icon ) ];
					} else {
						if ( 'fa-' !== substr( $icon, 0, 3 ) ) {
							$fa_icon = 'fa-' . $icon;
						} else {
							$fa_icon = $icon;
						}
					}
				} elseif ( 'fa-' != substr( $icon, 0, 3 ) ) {
					$fa_icon = 'fa-' . $icon;
				} else {
					$fa_icon = $icon;
				}
			} else {
				$fa_icon = '';
			}

			return $fa_icon;
		}

		/**
		 * Function to return animation classes for shortcodes mainly.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param  array $args Animation type, direction and speed.
		 * @return array       Array with data attributes.
		 */
		public static function animations( $args = array() ) {
			$defaults = array(
				'type'      => '',
				'direction' => 'left',
				'speed'     => '0.1',
				'offset'    => 'bottom-in-view',
			);

			$args = wp_parse_args( $args, $defaults );

			$animation_attribues = array();

			if ( $args['type'] ) {

				$animation_attribues['animation_class'] = 'fusion-animated';

				if ( 'static' === $args['direction'] ) {
					$args['direction'] = '';
				}

				if ( ! in_array( $args['type'], array( 'bounce', 'flash', 'shake', 'rubberBand' ), true ) ) {
					$direction_suffix = 'In' . ucfirst( $args['direction'] );
					$args['type'] .= $direction_suffix;
				}

				$animation_attribues['data-animationType'] = $args['type'];

				if ( $args['speed'] ) {
					$animation_attribues['data-animationDuration'] = $args['speed'];
				}
			}

			if ( $args['offset'] ) {
				if ( 'top-into-view' === $args['offset'] ) {
					$offset = '100%';
				} elseif ( 'top-mid-of-view' === $args['offset'] ) {
					$offset = '50%';
				} else {
					$offset = $args['offset'];
				}
				$animation_attribues['data-animationOffset'] = $offset;
			}

			return $animation_attribues;
		}

		/**
		 * Strips the unit from a given value.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param  string $value The value with or without unit.
		 * @param  string $unit_to_strip The unit to be stripped.
		 * @return string   the value without a unit.
		 */
		public static function strip_unit( $value, $unit_to_strip = 'px' ) {
			$value_length = strlen( $value );
			$unit_length = strlen( $unit_to_strip );

			if ( $value_length > $unit_length && 0 === substr_compare( $value, $unit_to_strip, $unit_length * (-1), $unit_length ) ) {
				return substr( $value, 0, $value_length - $unit_length );
			} else {
				return $value;
			}
		}

		/**
		 * Get the regular expression to parse a single shortcode.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param string $tagname Not used in the context of this function.
		 * @return string
		 */
		public static function get_shortcode_regex( $tagname ) {
			// @codingStandardsIgnoreStart
			return
				  '/\\['                              // Opening bracket.
				. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
				. "($tagname)"                     // 2: Shortcode name.
				. '(?![\\w-])'                       // Not followed by word character or hyphen.
				. '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
				.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
				.     '(?:'
				.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
				.         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
				.     ')*?'
				. ')'
				. '(?:'
				.     '(\\/)'                        // 4: Self closing tag...
				.     '\\]'                          // ...and closing bracket.
				. '|'
				.     '\\]'                          // Closing bracket.
				.     '(?:'
				.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
				.             '[^\\[]*+'             // Not an opening bracket.
				.             '(?:'
				.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
				.                 '[^\\[]*+'         // Not an opening bracket.
				.             ')*+'
				.         ')'
				.         '\\[\\/\\2\\]'             // Closing shortcode tag.
				.     ')?'
				. ')'
				. '(\\]?)/';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]].
				// @codingStandardsIgnoreEnd
		}

		/**
		 * Get Registered Sidebars.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @return array
		 */
		public static function fusion_get_sidebars() {
			global $wp_registered_sidebars;

			$sidebars = array();

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$name = $sidebar['name'];
				$sidebars[ $sidebar_id ] = $name;
			}

			return $sidebars;
		}

		/**
		 * Validate shortcode attribute value.
		 *
		 * @static
		 * @access public
		 * @since 1.0
		 * @param string $value         The value.
		 * @param string $accepted_unit The accepted unit.
		 * @param string $bc_support    Return value even if invalid.
		 * @return value
		 */
		public static function validate_shortcode_attr_value( $value, $accepted_unit, $bc_support = true ) {

			$validated_value = '';

			if ( '' !== $value ) {
				$value           = trim( $value );
				$unit            = preg_replace( '/[\d-]+/', '', $value );
				$numerical_value = preg_replace( '/[a-z,%]/', '', $value );

				if ( empty( $accepted_unit ) ) {
					$validated_value = $numerical_value;

				} else {

					if ( empty( $unit ) ) {
						// Add unit if it's required.
						$validated_value = $numerical_value . $accepted_unit;
					} elseif ( $bc_support || $unit === $accepted_unit ) {
						// If unit was found use original value. BC support.
						$validated_value = $value;
					} else {
						$validated_value = false;
					}
				}
			}

			return $validated_value;
		}

		/**
		 * Adds the options in the Fusion_Settings class.
		 *
		 * @access public
		 * @since 1.1.0
		 */
		public function add_options_to_fusion_settings() {

			if ( ! function_exists( 'fusion_builder_settings' ) ) {
				require_once wp_normalize_path( 'inc/class-fusion-builder-options.php' );
			}

		}

		/**
		 * Gets the value of a page option.
		 *
		 * @static
		 * @access public
		 * @param  string  $theme_option Theme option ID.
		 * @param  string  $page_option  Page option ID.
		 * @param  integer $post_id      Post/Page ID.
		 * @since  1.0.1
		 * @return string                Theme option or page option value.
		 */
		public static function get_page_option( $theme_option, $page_option, $post_id ) {

			$value = '';

			// If Avada is installed, use it to get the theme-option.
			if ( class_exists( 'Avada' ) ) {
				$value = fusion_get_option( $theme_option, $page_option, $post_id );
			}

			return apply_filters( 'fusion_builder_get_page_option', $value );

		}

		/**
		 * Checks if we're in the migration page.
		 * It does that by checking _GET, and then sets the $is_updating property.
		 *
		 * @access public
		 * @since 1.1.0
		 */
		public function set_is_updating() {
			if ( ! self::$is_updating && $_GET && isset( $_GET['avada_update'] ) && '1' == $_GET['avada_update'] ) {
				self::$is_updating = true;
			}
		}

		/**
		 * Adds extra classes for the <body> element, using the 'body_class' filter.
		 * Documentation: https://codex.wordpress.org/Plugin_API/Filter_Reference/body_class
		 *
		 * @since 1.1
		 * @param  array $classes CSS classes.
		 * @return array The merged and extended body classes.
		 */
		public function body_class_filter( $classes ) {
			$classes = array_merge( $classes, $this->body_classes );

			return $classes;
		}

		/**
		 * Calculate any extra classes for the <body> element.
		 *
		 * @param  array $classes CSS classes.
		 * @return array The needed body classes.
		 */
		private function body_classes( $classes ) {
			$classes[] = 'fusion-image-hovers';

			return $classes;
		}

		/**
		 * Gets the fusion_builder_options_panel private property.
		 *
		 * @access public
		 * @since 1.1.0
		 * @return object
		 */
		public function get_fusion_builder_options_panel() {
			return $this->fusion_builder_options_panel;
		}

		/**
		 * Compares db and plugin versions and does stuff if needed.
		 *
		 * @access private
		 * @since 1.1.2
		 */
		private function versions_compare() {

			$db_version = get_option( 'fusion_builder_version', false );
			if ( ! $db_version || FUSION_BUILDER_VERSION !== $db_version ) {

				// Reset caches.
				$fusion_cache = new Fusion_Cache();
				$fusion_cache->reset_all_caches();

				// Update version in the database.
				update_option( 'fusion_builder_version', FUSION_BUILDER_VERSION );
			}
		}

		/**
		 * Compares db and plugin versions and does stuff if needed.
		 *
		 * @since 1.2.1
		 * @access private
		 * @param array $links The array of action links.
		 * @return Array The $links array plus the added settings link.
		 */
		public function add_action_settings_link( $links ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=fusion-builder-settings' ) . '">' . esc_html__( 'Settings', 'fusion-builder' ) . '</a>';

			return $links;
		}
	} // End FusionBuilder class.

endif; // End if class_exists check.

// @codingStandardsIgnoreStart
/**
 * Instantiates the FusionBuilder class.
 * Make sure the class is properly set-up.
 * The FusionBuilder class is a singleton
 * so we can directly access the one true FusionBuilder object using this function.
 *
 * @return object FusionBuilder
 */
function FusionBuilder() {
	return FusionBuilder::get_instance();
}
// @codingStandardsIgnoreEnd

/**
 * Instantiate FusionBuilder class.
 */
function fusion_builder_activate() {

	// Include Fusion-Library.
	include_once FUSION_BUILDER_PLUGIN_DIR . 'inc/lib/fusion-library.php';
	do_action( 'fb_library_loaded' );
	FusionBuilder::get_instance();
	require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/dynamic-css/dynamic_css.php';

	$fb_patcher = new Fusion_Patcher( array(
		'context'     => 'fusion-builder',
		'version'     => FUSION_BUILDER_VERSION,
		'name'        => 'Fusion-Builder',
		'parent_slug' => 'fusion-builder-options',
		'page_title'  => esc_attr__( 'Fusion Patcher', 'fusion-builder' ),
		'menu_title'  => esc_attr__( 'Fusion Patcher', 'fusion-builder' ),
		'classname'   => 'FusionBuilder',
	) );
}
add_action( 'after_setup_theme', 'fusion_builder_activate' );

/**
 * TODO: example of adding FB options section with filter.
 *
 * @param  array $options Sections added by filter.
 * @return array $options Blog settings.
 */
function fusion_builder_add_elements_options( $options ) {
	$options['elements'] = FUSION_BUILDER_PLUGIN_DIR . 'inc/options/elements.php';

	return $options;
}
// @codingStandardsIgnoreLine
// add_filter( 'fusion_builder_option_section' , 'fusion_builder_add_elements_options' );
