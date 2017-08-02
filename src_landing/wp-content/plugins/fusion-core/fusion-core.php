<?php
/**
 * Plugin Name: Fusion Core
 * Plugin URI: http://theme-fusion.com
 * Description: ThemeFusion Core Plugin for ThemeFusion Themes
 * Version: 3.2.1
 * Author: ThemeFusion
 * Author URI: http://theme-fusion.com
 *
 * @package Fusion-Core
 * @subpackage Core
 */

// Plugin Folder Path.
if ( ! defined( 'FUSION_CORE_PATH' ) ) {
	define( 'FUSION_CORE_PATH', wp_normalize_path( dirname( __FILE__ ) ) );
}

// Plugin Folder URL.
if ( ! defined( 'FUSION_CORE_URL' ) ) {
	define( 'FUSION_CORE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! class_exists( 'FusionCore_Plugin' ) ) {
	/**
	 * The main fusion-core class.
	 */
	class FusionCore_Plugin {

		/**
		 * Plugin version, used for cache-busting of style and script file references.
		 *
		 * @since   1.0.0
		 * @var  string
		 */
		const VERSION = '3.2.1';

		/**
		 * Instance of the class.
		 *
		 * @static
		 * @access protected
		 * @since 1.0.0
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * JS folder URL.
		 *
		 * @static
		 * @access public
		 * @since 3.0.3
		 * @var string
		 */
		public static $js_folder_url;

		/**
		 * JS folder path.
		 *
		 * @static
		 * @access public
		 * @since 3.0.3
		 * @var string
		 */
		public static $js_folder_path;


		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @access private
		 * @since 1.0.0
		 */
		private function __construct() {
			self::$js_folder_url = FUSION_CORE_URL . 'js/min';
			self::$js_folder_path = FUSION_CORE_PATH . '/js/min';

			add_action( 'after_setup_theme', array( $this, 'load_fusion_core_text_domain' ) );
			add_action( 'after_setup_theme', array( $this, 'add_image_size' ) );

			// Load scripts & styles.
			if ( ! is_admin() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
				add_filter( 'fusion_dynamic_css_final', array( $this, 'scripts_dynamic_css' ) );
			}

			// Register custom post-types and taxonomies.
			add_action( 'init', array( $this, 'register_post_types' ) );

			// Admin menu tweaks.
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			// Provide single portfolio template via filter.
			add_filter( 'single_template', array( $this, 'portfolio_single_template' ) );

			// Check if Fusion Core has been updated.  Delay until after theme is available.
			add_action( 'after_setup_theme',  array( $this, 'versions_compare' ) );

		}

		/**
		 * Register the plugin text domain.
		 *
		 * @access public
		 * @return void
		 */
		public function load_fusion_core_text_domain() {
			load_plugin_textdomain( 'fusion-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @static
		 * @access public
		 * @since 1.0.0
		 * @return object  A single instance of the class.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set yet, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;

		}

		/**
		 * Gets the value of a theme option.
		 *
		 * @static
		 * @access public
		 * @since 3.0
		 * @param string|null  $option The option.
		 * @param string|false $subset The sub-option in case of an array.
		 */
		public static function get_option( $option = null, $subset = false ) {

			$value = '';
			// If Avada is installed, use it to get the theme-option.
			if ( class_exists( 'Avada' ) ) {
				$value = Avada()->settings->get( $option, $subset );
			}
			return apply_filters( 'fusion_core_get_option', $value, $option, $subset );

		}

		/**
		 * Returns a cached query.
		 * If the query is not cached then it caches it and returns the result.
		 *
		 * @static
		 * @access public
		 * @param string|array $args Same as in WP_Query.
		 * @return object
		 */
		public static function fusion_core_cached_query( $args ) {

			$query_id = md5( maybe_serialize( $args ) );
			$query    = wp_cache_get( $query_id, 'avada' );
			if ( false === $query ) {
				$query = new WP_Query( $args );
				wp_cache_set( $query_id, $query, 'avada' );
			}
			return $query;

		}

		/**
		 * Returns array of available fusion sliders.
		 *
		 * @access public
		 * @since 3.1.6
		 * @return array
		 */
		public static function get_fusion_sliders() {
			$slides_array    = array();
			$slides          = array();
			$slides          = get_terms( 'slide-page' );
			if ( $slides && ! isset( $slides->errors ) ) {
				$slides = maybe_unserialize( $slides );
				foreach ( $slides as $key => $val ) {
					$slides_array[ $val->slug ] = $val->name . ' (#' . $val->term_id . ')';
				}
			}
			return $slides_array;
		}

		/**
		 * Add image sizes.
		 *
		 * @access  public
		 */
		public function add_image_size() {
			add_image_size( 'portfolio-full', 940, 400, true );
			add_image_size( 'portfolio-one', 540, 272, true );
			add_image_size( 'portfolio-two', 460, 295, true );
			add_image_size( 'portfolio-three', 300, 214, true );
			add_image_size( 'portfolio-five', 177, 142, true );
		}

		/**
		 * Enqueues scripts.
		 *
		 * @access public
		 */
		public function scripts() {
			// If we're using a CSS to file compiler there's no need to enqueue separate file.
			// It will be added directly to the compiled CSS (@see scripts_dynamic_css method).
			if ( class_exists( 'Fusion_Dynamic_CSS' ) ) {
				$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
				$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;
				if ( 'file' === $mode ) {
					return;
				}
			}

			wp_enqueue_style( 'fusion-core-style', plugins_url( 'css/style.min.css', __FILE__ ) );
		}

		/**
		 * Adds styles to the compiled dynamic-css.
		 *
		 * @access public
		 * @since 3.1.5
		 * @param string $original_styles The compiled dynamic-css styles.
		 * @return string The dynamic-css with extra css apended if needed.
		 */
		public function scripts_dynamic_css( $original_styles ) {
			$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
			$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;
			$styles = '';

			if ( 'file' === $mode ) {
				$wp_filesystem = Fusion_Helper::init_filesystem();
				// Stylesheet ID: fusion-core-style.
				$styles .= $wp_filesystem->get_contents( FUSION_CORE_PATH . '/css/style.min.css' );
			}

			return $styles . $original_styles;
		}

		/**
		 * Register custom post types.
		 *
		 * @access public
		 * @since 3.1.0
		 */
		public function register_post_types() {

			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings_array = array(
					'portfolio_slug' => 'portfolio-items',
					'status_eslider' => '1',
				);
				if ( class_exists( 'Fusion_Settings' ) ) {
					$fusion_settings = Fusion_Settings::get_instance();

					$fusion_settings_array = array(
						'portfolio_slug' => $fusion_settings->get( 'portfolio_slug' ),
						'status_eslider' => $fusion_settings->get( 'status_eslider' ),
					);
				}
			} else {
				$fusion_settings_array = array(
					'portfolio_slug' => $fusion_settings->get( 'portfolio_slug' ),
					'status_eslider' => $fusion_settings->get( 'status_eslider' ),
				);
			}

			$permalinks = get_option( 'avada_permalinks' );

			// Portfolio.
			register_post_type(
				'avada_portfolio',
				array(
					'labels'      => array(
						'name'          => _x( 'Portfolio', 'Post Type General Name', 'fusion-core' ),
						'singular_name' => _x( 'Portfolio', 'Post Type Singular Name', 'fusion-core' ),
						'add_new_item'  => _x( 'Add New Portfolio Post', 'fusion-core' ),
						'edit_item'  => _x( 'Edit Portfolio Post', 'fusion-core' ),

					),
					'public'      => true,
					'has_archive' => true,
					'rewrite'     => array(
						'slug' => $fusion_settings_array['portfolio_slug'],
					),
					'supports'    => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
					'can_export'  => true,
				)
			);

			register_taxonomy(
				'portfolio_category',
				'avada_portfolio',
				array(
					'hierarchical' => true,
					'label'        => esc_attr__( 'Portfolio Categories', 'fusion-core' ),
					'query_var'    => true,
					'rewrite'      => array(
						'slug'       => empty( $permalinks['portfolio_category_base'] ) ? _x( 'portfolio_category', 'slug', 'fusion-core' ) : $permalinks['portfolio_category_base'],
						'with_front' => false,
					),
				)
			);

			register_taxonomy(
				'portfolio_skills',
				'avada_portfolio',
				array(
					'hierarchical' => true,
					'label'        => esc_attr__( 'Skills', 'fusion-core' ),
					'query_var'    => true,
					'labels'       => array(
						'add_new_item' => esc_attr__( 'Add New Skill', 'fusion-core' ),
					),
					'rewrite'      => array(
						'slug'       => empty( $permalinks['portfolio_skills_base'] ) ? _x( 'portfolio_skills', 'slug', 'fusion-core' ) : $permalinks['portfolio_skills_base'],
						'with_front' => false,
					),
				)
			);

			register_taxonomy(
				'portfolio_tags',
				'avada_portfolio',
				array(
					'hierarchical' => false,
					'label'        => esc_attr__( 'Tags', 'fusion-core' ),
					'query_var'    => true,
					'rewrite'      => array(
						'slug'       => empty( $permalinks['portfolio_tags_base'] ) ? _x( 'portfolio_tags', 'slug', 'fusion-core' ) : $permalinks['portfolio_tags_base'],
						'with_front' => false,
					),
				)
			);

			// FAQ.
			register_post_type(
				'avada_faq',
				array(
					'labels' => array(
						'name'          => _x( 'FAQs', 'Post Type General Name', 'fusion-core' ),
						'singular_name' => _x( 'FAQ', 'Post Type Singular Name', 'fusion-core' ),
						'add_new_item'  => _x( 'Add New FAQ Post', 'fusion-core' ),
						'edit_item'  => _x( 'Edit FAQ Post', 'fusion-core' ),
					),
					'public' => true,
					'has_archive' => true,
					'rewrite' => array(
						'slug' => 'faq-items',
					),
					'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
					'can_export' => true,
				)
			);

			register_taxonomy(
				'faq_category',
				'avada_faq',
				array(
					'hierarchical' => true,
					'label'        => 'FAQ Categories',
					'query_var'    => true,
					'rewrite'      => true,
				)
			);

			// Elastic Slider.
			if ( $fusion_settings_array['status_eslider'] ) {
				register_post_type(
					'themefusion_elastic',
					array(
						'public' => true,
						'has_archive' => false,
						'rewrite' => array(
							'slug' => 'elastic-slide',
						),
						'supports' => array( 'title', 'thumbnail' ),
						'can_export' => true,
						'menu_position' => 100,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'labels' => array(
							'name'               => _x( 'Elastic Sliders', 'Post Type General Name', 'fusion-core' ),
							'singular_name'      => _x( 'Elastic Slider', 'Post Type Singular Name', 'fusion-core' ),
							'menu_name'          => esc_attr__( 'Elastic Slider', 'fusion-core' ),
							'parent_item_colon'  => esc_attr__( 'Parent Slide:', 'fusion-core' ),
							'all_items'          => esc_attr__( 'Add or Edit Slides', 'fusion-core' ),
							'view_item'          => esc_attr__( 'View Slides', 'fusion-core' ),
							'add_new_item'       => esc_attr__( 'Add New Slide', 'fusion-core' ),
							'add_new'            => esc_attr__( 'Add New Slide', 'fusion-core' ),
							'edit_item'          => esc_attr__( 'Edit Slide', 'fusion-core' ),
							'update_item'        => esc_attr__( 'Update Slide', 'fusion-core' ),
							'search_items'       => esc_attr__( 'Search Slide', 'fusion-core' ),
							'not_found'          => esc_attr__( 'Not found', 'fusion-core' ),
							'not_found_in_trash' => esc_attr__( 'Not found in Trash', 'fusion-core' ),
						),
					)
				);

				register_taxonomy(
					'themefusion_es_groups',
					'themefusion_elastic',
					array(
						'hierarchical' => false,
						'query_var' => true,
						'rewrite' => true,
						'labels' => array(
							'name'                       => _x( 'Groups', 'Taxonomy General Name', 'fusion-core' ),
							'singular_name'              => _x( 'Group', 'Taxonomy Singular Name', 'fusion-core' ),
							'menu_name'                  => esc_attr__( 'Add or Edit Groups', 'fusion-core' ),
							'all_items'                  => esc_attr__( 'All Groups', 'fusion-core' ),
							'parent_item_colon'          => esc_attr__( 'Parent Group:', 'fusion-core' ),
							'new_item_name'              => esc_attr__( 'New Group Name', 'fusion-core' ),
							'add_new_item'               => esc_attr__( 'Add Groups', 'fusion-core' ),
							'edit_item'                  => esc_attr__( 'Edit Group', 'fusion-core' ),
							'update_item'                => esc_attr__( 'Update Group', 'fusion-core' ),
							'separate_items_with_commas' => esc_attr__( 'Separate groups with commas', 'fusion-core' ),
							'search_items'               => esc_attr__( 'Search Groups', 'fusion-core' ),
							'add_or_remove_items'        => esc_attr__( 'Add or remove groups', 'fusion-core' ),
							'choose_from_most_used'      => esc_attr__( 'Choose from the most used groups', 'fusion-core' ),
							'not_found'                  => esc_attr__( 'Not Found', 'fusion-core' ),
						),
					)
				);
			} // End if().

			// qTranslate and mqTranslate custom post type support.
			if ( function_exists( 'qtrans_getLanguage' ) ) {
				add_action( 'portfolio_category_add_form', 'qtrans_modifyTermFormFor' );
				add_action( 'portfolio_category_edit_form', 'qtrans_modifyTermFormFor' );
				add_action( 'portfolio_skills_add_form', 'qtrans_modifyTermFormFor' );
				add_action( 'portfolio_skills_edit_form', 'qtrans_modifyTermFormFor' );
				add_action( 'portfolio_tags_add_form', 'qtrans_modifyTermFormFor' );
				add_action( 'portfolio_tags_edit_form', 'qtrans_modifyTermFormFor' );
				add_action( 'faq_category_edit_form', 'qtrans_modifyTermFormFor' );
			}

			// Check if flushing permalinks required and flush them.
			$flush_permalinks = get_option( 'fusion_core_flush_permalinks' );
			if ( ! $flush_permalinks ) {
				flush_rewrite_rules();
				update_option( 'fusion_core_flush_permalinks', true );
			}
		}

		/**
		 * Elastic Slider admin menu.
		 *
		 * @access public
		 */
		public function admin_menu() {
			global $submenu;
			unset( $submenu['edit.php?post_type=themefusion_elastic'][10] );
		}

		/**
		 * Load single portfolio template from FC.
		 *
		 * @access public
		 * @since 3.1
		 * @param string $single_post_template The post template.
		 * @return string
		 */
		public function portfolio_single_template( $single_post_template ) {
			global $post;

			// Check the post-type.
			if ( 'avada_portfolio' !== $post->post_type ) {
				return $single_post_template;
			}

			// The filename of the template.
			$filename = 'single-avada_portfolio.php';

			// Include template file from the theme if it exists.
			if ( locate_template( 'single-avada_portfolio.php' ) ) {
				return locate_template( 'single-avada_portfolio.php' );
			}

			// Include template file from the plugin.
			$single_portfolio_template = wp_normalize_path( dirname( __FILE__ ) . '/templates/' . $filename );

			// Checks if the single post is portfolio.
			if ( file_exists( $single_portfolio_template ) ) {
				return $single_portfolio_template;
			}
			return $single_post_template;
		}

		/**
		 * Compares db and plugin versions and does stuff if needed.
		 *
		 * @access public
		 * @since 3.1.5
		 */
		public function versions_compare() {

			$db_version = get_option( 'fusion_core_version', false );

			if ( ! $db_version || self::VERSION !== $db_version ) {

				// Run activation related steps.
				delete_option( 'fusion_core_flush_permalinks' );

				if ( class_exists( 'Fusion_Cache' ) ) {
					$fusion_cache = new Fusion_Cache();
					$fusion_cache->reset_all_caches();
				}
				fusion_core_enable_elements();

				// Update version in the database.
				update_option( 'fusion_core_version', self::VERSION );
			}
		}
	}
} // End if().

// Load the instance of the plugin.
add_action( 'plugins_loaded', array( 'FusionCore_Plugin', 'get_instance' ) );

/**
 * Setup Fusion Slider.
 *
 * @since 3.1
 * @return void
 */
function setup_fusion_slider() {
	global $fusion_settings;
	if ( ! $fusion_settings && class_exists( 'Fusion_Settings' ) ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	if ( ! class_exists( 'Fusion_Settings' ) || '0' !== $fusion_settings->get( 'status_fusion_slider' ) ) {
		include_once FUSION_CORE_PATH . '/fusion-slider/class-fusion-slider.php';
	}
}
// Setup Fusion Slider.
add_action( 'after_setup_theme', 'setup_fusion_slider', 10 );

/**
 * Find and include all shortcodes within shortcodes folder.
 *
 * @since 3.1
 * @return void
 */
function fusion_init_shortcodes() {
	if ( class_exists( 'Avada' ) ) {
		foreach ( glob( plugin_dir_path( __FILE__ ) . '/shortcodes/*.php', GLOB_NOSORT ) as $filename ) {
			require_once wp_normalize_path( $filename );
		}
	}
}
// Load all shortcode elements.
add_action( 'fusion_builder_shortcodes_init', 'fusion_init_shortcodes' );

/**
 * Load portfolio archive template from FC.
 *
 * @access public
 * @since 3.1
 * @param string $archive_post_template The post template.
 * @return string
 */
function fusion_portfolio_archive_template( $archive_post_template ) {
	$archive_portfolio_template = dirname( __FILE__ ) . '/templates/archive-avada_portfolio.php';

	// Checks if the archive is portfolio.
	if ( is_post_type_archive( 'avada_portfolio' )
		|| is_tax( 'portfolio_category' )
		|| is_tax( 'portfolio_skills' )
		|| is_tax( 'portfolio_tags' ) ) {
		if ( file_exists( $archive_portfolio_template ) ) {
			fusion_portfolio_scripts();
			return $archive_portfolio_template;
		}
	}
	return $archive_post_template;
}

// Provide archive portfolio template via filter.
add_filter( 'archive_template', 'fusion_portfolio_archive_template' );

/**
 * Enable Fusion Builder elements on activation.
 *
 * @access public
 * @since 3.1
 * @return void
 */
function fusion_core_enable_elements() {
	if ( function_exists( 'fusion_builder_auto_activate_element' ) && version_compare( FUSION_BUILDER_VERSION , '1.0.6', '>' ) ) {
		fusion_builder_auto_activate_element( 'fusion_portfolio' );
		fusion_builder_auto_activate_element( 'fusion_faq' );
		fusion_builder_auto_activate_element( 'fusion_fusionslider' );
	}
}

register_activation_hook( __FILE__, 'fusion_core_activation' );
register_deactivation_hook( __FILE__, 'fusion_core_deactivation' );

/**
 * Runs on fusion core activation hook.
 */
function fusion_core_activation() {

	// Reset patcher on activation.
	fusion_core_reset_patcher_counter();

	// Enable fusion core elements on activation.
	fusion_core_enable_elements();
}

/**
 * Runs on fusion core deactivation hook.
 */
function fusion_core_deactivation() {
	// Reset patcher on deactivation.
	fusion_core_reset_patcher_counter();

	// Delete the option to flush rewrite rules after activation.
	delete_option( 'fusion_core_flush_permalinks' );
}

/**
 * Resets the patcher counters.
 */
function fusion_core_reset_patcher_counter() {
	delete_site_transient( 'fusion_patcher_check_num' );
}

/**
 * Instantiate the patcher class.
 */
function fusion_core_patcher_activation() {
	if ( class_exists( 'Fusion_Patcher' ) ) {
		new Fusion_Patcher( array(
			'context'     => 'fusion-core',
			'version'     => FusionCore_Plugin::VERSION,
			'name'        => 'Fusion-Core',
			'parent_slug' => 'avada',
			'page_title'  => esc_attr__( 'Fusion Patcher', 'fusion-core' ),
			'menu_title'  => esc_attr__( 'Fusion Patcher', 'fusion-core' ),
			'classname'   => 'FusionCore_Plugin',
		) );
	}
}
add_action( 'after_setup_theme', 'fusion_core_patcher_activation', 17 );
