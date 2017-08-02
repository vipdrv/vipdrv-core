<?php
/**
 * The main import handler.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Importer
 * @since      5.2
 */

/**
 * Import a demo.
 */
class Avada_Demo_Import {

	/**
	 * The demo type.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $demo_type;

	/**
	 * Path to the XML file.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $theme_xml;

	/**
	 * Path to the theme-options file.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $theme_options_file;

	/**
	 * Path to the widgets file.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $widgets_file;

	/**
	 * Path to the Fusion-Slider file.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $fs_url;

	/**
	 * Whether we should fetch attachments or not.
	 *
	 * @access private
	 * @since 5.2
	 * @var bool
	 */
	private $fetch_attachments;

	/**
	 * Whether this is a WooCommerce site or not.
	 *
	 * @access private
	 * @since 5.2
	 * @var bool
	 */
	private $shop_demo;

	/**
	 * The sidebars.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $sidebars;

	/**
	 * The Homepage title.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $homepage_title;

	/**
	 * WooCommerce pages.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $woopages;

	/**
	 * Whether Fusion-Slider exists or not.
	 *
	 * @access private
	 * @since 5.2
	 * @var bool
	 */
	private $fs_exists;

	/**
	 * Avada_Importer_Data instance.
	 *
	 * @access private
	 * @since 5.2
	 * @var object
	 */
	private $importer_files;

	/**
	 * Avada_Demo_Content_Tracker instance.
	 *
	 * @access private
	 * @since 5.2
	 * @var object
	 */
	private $content_tracker;

	/**
	 * The content-types we'll be importing.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $import_content_types;

	/**
	 * An array of allowed post-types.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $allowed_post_types = array();

	/**
	 * An array of allowed taxonomies.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $allowed_taxonomies = array();

	/**
	 * Whether we want to import everything or not.
	 *
	 * @access private
	 * @since 5.2
	 * @var bool
	 */
	private $import_all;

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function __construct() {

		// Hook importer into admin init.
		add_action( 'wp_ajax_fusion_import_demo_data', array( $this, 'import_demo_stage' ) );
	}

	/**
	 * The main importer function.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function import_demo_stage() {

		check_ajax_referer( 'avada_demo_ajax', 'security' );

		if ( current_user_can( 'manage_options' ) ) {

			$import_stages = array();
			if ( isset( $_POST['importStages'] ) ) {
				// @codingStandardsIgnoreLine
				$import_stages = wp_unslash( $_POST['importStages'] );
			}

			$this->demo_type = 'classic';
			// @codingStandardsIgnoreLine
			if ( isset( $_POST['demoType'] ) && '' !== trim( wp_unslash( $_POST['demoType'] ) ) ) {
				// @codingStandardsIgnoreLine
				$this->demo_type = wp_unslash( $_POST['demoType'] );
			}

			$this->fetch_attachments = false;
			// @codingStandardsIgnoreLine
			if ( isset( $_POST['fetchAttachments'] ) && 'true' === trim( wp_unslash( $_POST['fetchAttachments'] ) ) ) {
				$this->fetch_attachments = true;
			}

			$this->import_content_types = array();
			if ( isset( $_POST['contentTypes'] ) && is_array( $_POST['contentTypes'] ) ) {
				// @codingStandardsIgnoreLine
				$this->import_content_types = wp_unslash( $_POST['contentTypes'] );
			}

			$this->import_all = false;
			// @codingStandardsIgnoreLine
			if ( isset( $_POST['allImport'] ) && 'true' === trim( wp_unslash( $_POST['allImport'] ) ) ) {
				$this->import_all = true;
			}

			// Include the remote file getter.
			if ( ! class_exists( 'Avada_Importer_Data' ) ) {
				include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/class-avada-importer-data.php' );
			}

			if ( ! class_exists( 'Avada_Demo_Content_Tracker' ) ) {
				include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/class-avada-demo-content-tracker.php' );
			}

			$this->importer_files  = new Avada_Importer_Data( $this->demo_type );
			$this->content_tracker = new Avada_Demo_Content_Tracker( $this->demo_type );

			$this->before_import_stage();

			if ( ! empty( $import_stages[0] ) && method_exists( $this, 'import_' . $import_stages[0] ) ) {

				if ( 'download' !== $import_stages[0] ) {
					$this->theme_xml          = $this->importer_files->get_path( 'avada.xml' );
					$this->theme_options_file = $this->importer_files->get_path( 'theme_options.json' );
					$this->widgets_file       = $this->importer_files->get_path( 'widget_data.json' );
					$this->fs_url             = $this->importer_files->get_path( 'fusion_slider.zip' );

					$this->shop_demo      = $this->importer_files->is_shop();
					$this->sidebars       = $this->importer_files->get_sidebars();
					$this->homepage_title = $this->importer_files->get_homepage_title();
					$this->woopages       = $this->importer_files->get_woopages();
					$this->fs_exists      = true;

					if ( 'landing_product' === $this->demo_type ) {
						$this->fs_exists = false;
					}

					if ( 'content' === $import_stages[0] ) {
						$this->before_content_import();

						foreach ( $this->import_content_types as $content_type ) {
							// Note import stage which is currently processed.
							$this->content_tracker->update_import_stage_data( $content_type );
						}
					} else {
						// Note import stage which is currently processed.
						$this->content_tracker->update_import_stage_data( $import_stages[0] );
					}
				}

				// Make import stage backup if needed.
				if ( method_exists( $this->content_tracker, 'set_' . $import_stages[0] ) ) {
					call_user_func( array( $this->content_tracker, 'set_' . $import_stages[0] ) );
				}

				call_user_func( array( $this, 'import_' . $import_stages[0] ) );

				// Menus are imported with the content.
				if ( 'content' === $import_stages[0] ) {
					$this->after_content_import();
				}
			} // End if().

			// We've just processed last import stage.
			if ( 1 === count( $import_stages ) ) {

				// $this->content_tracker->set_general_data();
				$this->after_import();

				// Reset all caches. Deletes demo data as well.
				avada_reset_all_cache();

				echo 'imported';
			} else {
				echo 'import partially completed: ' . $import_stages[0]; // WPCS: XSS ok.
			}
			// Save data after import, for example imported terms.
			$this->content_tracker->save_demo_history();

			exit;
		} // End if().
	}

	/**
	 * Just some stuff that needs to be set before any import stage is run.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function before_import_stage() {

		add_filter( 'intermediate_image_sizes_advanced', 'avada_filter_image_sizes' );

		if ( function_exists( 'ini_get' ) ) {
			if ( 300 < ini_get( 'max_execution_time' ) ) {
				@ini_set( 'max_execution_time', 300 );
			}
			if ( 512 < intval( ini_get( 'memory_limit' ) ) ) {
				@ini_set( 'memory_limit', '512M' );
			}
		}

	}

	/**
	 * Just some stuff that needs to be set after any import stage is run.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function after_import() {

		if ( true === $this->import_all ) {
			$this->assign_menus_to_locations();

			$this->content_tracker->update_import_stage_data( 'all' );
		}

	}

	/**
	 * Downloads demo package (zip) file.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_download() {

		// Get remote files and save locally.
		if ( ! $this->importer_files->remote_files_downloaded() ) {
			$this->importer_files->download_remote_files();
		}
	}

	/**
	 * This is called before 'content' import stages are run.
	 * Mostly used to add hooks which will filter allowed post types and taxonomies from avada.xml file.
	 *
	 * Currently 'content' import stages are: posts, pages, images, CPT.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function before_content_import() {

		add_filter( 'wxr_importer.pre_process.user', array( $this, 'skip_authors' ), 10, 2 );
		add_action( 'wxr_importer.processed.post', array( $this, 'add_fusion_demo_import_meta' ), 10, 5 );
		add_filter( 'import_post_meta_key', array( $this, 'skip_old_menu_meta' ), 10, 3 );

		if ( ! $this->import_all ) {

			if ( ! empty( $this->import_content_types ) ) {

				foreach ( $this->import_content_types as $content_type ) {

					if ( method_exists( $this, 'allow_import_' . $content_type ) ) {
						call_user_func( array( $this, 'allow_import_' . $content_type ) );
					}
				}
			}

			add_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_not_allowed_post_types' ), 10, 4 );
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_not_allowed_taxonomies' ), 10, 2 );
		} else {
			// Slides are imported separately, not from avada.xml file.
			add_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_slide_post_type' ), 10, 4 );
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_slide_taxonomy' ), 10, 2 );
		}
	}

	/**
	 * This is called after 'content' import stages are run.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function after_content_import() {

		remove_filter( 'wxr_importer.pre_process.user', array( $this, 'skip_authors' ), 10 );
		remove_action( 'wxr_importer.processed.post', array( $this, 'add_fusion_demo_import_meta' ), 10 );
		remove_filter( 'import_post_meta_key', array( $this, 'skip_old_menu_meta' ), 10 );

		if ( ! $this->import_all ) {
			remove_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_not_allowed_post_types' ), 10 );
			remove_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_not_allowed_taxonomies' ), 10 );
		} else {
			remove_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_slide_post_type' ), 10 );
			remove_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_slide_taxonomy' ), 10 );
		}
	}

	/**
	 * We don't want to import demo authors.
	 *
	 * @access public
	 * @since 5.2
	 * @param array $data User importer data.
	 * @param array $meta User meta.
	 * @return bool
	 */
	public function skip_authors( $data, $meta ) {
		return false;
	}

	/**
	 * Adds import meta to demos.
	 *
	 * @access public
	 * @since 5.2
	 * @param int   $post_id  The Post ID.
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 */
	public function add_fusion_demo_import_meta( $post_id, $data, $meta, $comments, $terms ) {

		update_post_meta( $post_id, 'fusion_demo_import', $this->demo_type );
	}

	/**
	 * Allow importing a post.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_post() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'post' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'category', 'post_tag' ) );
	}

	/**
	 * Allow importing a page.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_page() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'page', 'fusion_element', 'fusion_template', 'wpcf7_contact_form' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'element_category' ) );
	}

	/**
	 * Allow importing a portfolio.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_avada_portfolio() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'avada_portfolio' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'portfolio_category', 'portfolio_skills', 'portfolio_tags' ) );
	}

	/**
	 * Allow importing an FAQ.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_avada_faq() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'avada_faq' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'faq_category' ) );
	}

	/**
	 * Allow importing a product.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_product() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'product', 'shop_order', 'shop_coupon' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'product_cat', 'product_tag', 'product_visibility', 'product_type' ) );
	}

	/**
	 * Allow importing an event.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_event() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'tribe_events', 'tribe_venue', 'tribe_organizer' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'tribe_events_cat' ) );
	}

	/**
	 * Allow importing a forum.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_forum() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'forum', 'topic', 'reply' ) );
		$this->allowed_taxonomies = array_merge( $this->allowed_taxonomies, array( 'topic-tag' ) );
	}

	/**
	 * Allow importing an attachment.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function allow_import_attachment() {

		$this->allowed_post_types = array_merge( $this->allowed_post_types, array( 'attachment' ) );
	}

	/**
	 * Main content importer method.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_content() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			define( 'WP_LOAD_IMPORTERS', true ); // We are loading importers.
		}

		if ( ! class_exists( 'WP_Importer' ) ) { // If main importer class doesn't exist.
			$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			include $wp_importer;
		}

		if ( ! class_exists( 'WXR_Importer' ) ) { // If WP importer doesn't exist.
			include wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/importer/class-logger.php' );
			include wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/importer/class-logger-html.php' );

			$wp_import = wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/importer/class-wxr-importer.php' );
			include $wp_import;
		}

		if ( ! class_exists( 'Fusion_WXR_Importer' ) ) {
			include wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/importer/class-fusion-wxr-importer.php' );
		}

		if ( class_exists( 'WP_Importer' ) && class_exists( 'WXR_Importer' ) && class_exists( 'Fusion_WXR_Importer' ) ) { // Check for main import class and wp import class.

			$logger = new WP_Importer_Logger_HTML();

			// It's important to disable 'prefill_existing_posts'.
			// In case GUID of importing post matches GUID of an existing post it won't be imported.
			$importer = new Fusion_WXR_Importer( array(
				'fetch_attachments'      => $this->fetch_attachments,
				'prefill_existing_posts' => false,
			) );
			$importer->set_logger( $logger );

			ob_start();
			$importer->import( $this->theme_xml );
			ob_end_clean();

			// Import WooCommerce if WooCommerce Exists.
			if ( class_exists( 'WooCommerce' ) && $this->shop_demo ) {
				foreach ( $this->woopages as $woo_page_name => $woo_page_title ) {
					// @codingStandardsIgnoreLine
					$woopage = get_page_by_title( $woo_page_title );
					if ( isset( $woopage ) && $woopage->ID ) {
						update_option( $woo_page_name, $woopage->ID ); // Front Page.
					}
				}
				// We no longer need to install pages.
				delete_option( '_wc_needs_pages' );
				delete_transient( '_wc_activation_redirect' );
			}
			// Flush rules after install.
			flush_rewrite_rules();
		}
	}

	/**
	 * Skips post-types that are not allowed.
	 *
	 * @access public
	 * @since 5.2
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 * @return bool|array
	 */
	public function skip_not_allowed_post_types( $data, $meta, $comments, $terms ) {

		if ( ! in_array( $data['post_type'], $this->allowed_post_types ) ) {
			return false;
		}
		return $data;
	}

	/**
	 * Skip non-allowed taxonomies.
	 *
	 * @access public
	 * @since 5.2
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @return bool|array
	 */
	public function skip_not_allowed_taxonomies( $data, $meta ) {

		if ( ! in_array( $data['taxonomy'], $this->allowed_taxonomies ) ) {
			return false;
		}
		return $data;
	}

	/**
	 * Skips 'slide' post type.
	 * This is used to skip importing 'slides' from avada.xml file.
	 *
	 * @access public
	 * @since 5.2
	 * @param array $data     The Post importer data.
	 * @param array $meta     The Post meta.
	 * @param array $comments The Post comments.
	 * @param array $terms    The Post terms.
	 * @return bool|array
	 */
	public function skip_slide_post_type( $data, $meta, $comments, $terms ) {

		if ( 'slide' === $data['post_type'] ) {
			return false;
		}
		return $data;
	}

	/**
	 * Skip 'slide-page' terms.
	 *
	 * @access public
	 * @since 5.2
	 * @param array $data The Post importer data.
	 * @param array $meta The Post meta.
	 * @return bool|array
	 */
	public function skip_slide_taxonomy( $data, $meta ) {

		if ( 'slide-page' === $data['taxonomy'] ) {
			return false;
		}
		return $data;
	}

	/**
	 * Used to skip importing old menu meta to 5.2+ installs.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $meta_key The meta key.
	 * @param int    $post_id  Post ID.
	 * @param object $post     Post object.
	 * @return bool|string
	 */
	public function skip_old_menu_meta( $meta_key, $post_id, $post ) {

		$meta_keys = array(
			'_menu_item_fusion_megamenu_status',
			'_menu_item_fusion_megamenu_width',
			'_menu_item_fusion_megamenu_columns',
			'_menu_item_fusion_megamenu_title',
			'_menu_item_fusion_megamenu_widgetarea',
			'_menu_item_fusion_megamenu_columnwidth',
			'_menu_item_fusion_megamenu_icon',
			'_menu_item_fusion_megamenu_modal',
			'_menu_item_fusion_megamenu_thumbnail',
			'_menu_item_fusion_menu_style',
			'_menu_item_fusion_menu_icononly',
		);

		if ( in_array( $meta_key, $meta_keys ) ) {
			return false;
		}

		return $meta_key;
	}

	/**
	 * Assigns imported menus to correct locations.
	 * Called from 'import_content' method.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function assign_menus_to_locations() {

		// Set imported menus to registered theme locations
		$locations = get_theme_mod( 'nav_menu_locations' ); // Registered menu locations in theme.
		$menus     = wp_get_nav_menus(); // Registered menus.

		if ( $menus ) {
			if ( 'classic' === $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$opmenu = get_page_by_title( 'One Page Parallax' );
			} elseif ( 'landing_product' === $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$opmenu = get_page_by_title( 'Homepage' );
			} elseif ( 'technology' === $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$opmenu = get_page_by_title( 'Technology' );
			} elseif ( 'wedding' === $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$opmenu = get_page_by_title( 'Home' );
			} elseif ( 'resume' === $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$opmenu = get_page_by_title( 'Home' );
			}
			foreach ( $menus as $menu ) { // Assign menus to theme locations.
				if ( 'classic' === $this->demo_type ) {
					if ( 'Classic Main' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					} elseif ( 'Classic 404' === $menu->name ) {
						$locations['404_pages'] = $menu->term_id;
					} elseif ( 'Classic Top' === $menu->name ) {
						$locations['top_navigation'] = $menu->term_id;
					}

					// Assign One Page Menu.
					if ( isset( $opmenu ) && $opmenu->ID && 'Classic One Page' === $menu->name ) {
						update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
					}
				} elseif ( 'classic_shop' === $this->demo_type ) {
					if ( 'Classic Shop Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					} elseif ( 'Classic Shop Top Secondary Menu' == $menu->name ) {
						$locations['top_navigation'] = $menu->term_id;
					}
				} elseif ( 'landing_product' === $this->demo_type ) {
					// Assign One Page Menu.
					if ( isset( $opmenu ) && $opmenu->ID && 'Landing Product Landing Page Menu' === $menu->name ) {
						update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
					}

					if ( 'Landing Product Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					}
				} elseif ( 'resume' === $this->demo_type ) {
					// Assign One Page Menu.
					if ( isset( $opmenu ) && $opmenu->ID && 'Resume Homepage Menu' === $menu->name ) {
						update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
					}

					if ( 'Resume Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					}
				} elseif ( 'wedding' === $this->demo_type ) {
					// Assign One Page Menu.
					if ( isset( $opmenu ) && $opmenu->ID && 'Wedding Homepage Menu' === $menu->name ) {
						update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
					}

					if ( 'Wedding Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					}
				} elseif ( 'technology' === $this->demo_type ) {
					// Assign One Page Menu.
					if ( isset( $opmenu ) && $opmenu->ID && 'Technology Front Page Menu' === $menu->name ) {
						update_post_meta( $opmenu->ID, 'pyre_displayed_menu', $menu->term_id );
					}

					if ( 'Technology Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					}
				} else {
					if ( ucwords( str_replace( '_', ' ', $this->demo_type ) ) . ' Main Menu' === $menu->name ) {
						$locations['main_navigation'] = $menu->term_id;
					}
				} // End if().
			} // End foreach().
		} // End if().

		set_theme_mod( 'nav_menu_locations', $locations ); // Set menus to locations.

	}

	/**
	 * Imports Theme Options.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_theme_options() {

		$theme_options_json                         = file_get_contents( $this->theme_options_file );
		$theme_options                              = json_decode( $theme_options_json, true );
		$theme_options['logo_retina']               = '';
		$theme_options['sticky_header_logo_retina'] = '';
		$theme_options['mobile_logo_retina']        = '';
		$theme_options_db_name                      = Avada::get_original_option_name();
		update_option( $theme_options_db_name, $theme_options );
	}

	/**
	 * Imports widgets.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_widgets() {

		// Add sidebar widget areas.
		if ( false !== $this->sidebars ) {
			update_option( 'sbg_sidebars', $this->sidebars );

			foreach ( $this->sidebars as $sidebar ) {
				$sidebar_class = avada_name_to_class( $sidebar );
				register_sidebar( array(
					'name'          => $sidebar,
					'id'            => 'avada-custom-sidebar-' . strtolower( $sidebar_class ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<div class="heading"><h4 class="widget-title">',
					'after_title'   => '</h4></div>',
				) );
			}
		}

		// Add data to widgets.
		if ( isset( $this->widgets_file ) && $this->widgets_file ) {
			$widgets_json   = $this->widgets_file; // Widgets data file.
			$widgets_json   = file_get_contents( $widgets_json );
			$widget_data    = $widgets_json;
			$import_widgets = fusion_import_widget_data( $widget_data );
		}
	}

	/**
	 * Calls Fusion, Rev and Layer sliders import methods.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_sliders() {
		add_action( 'wxr_importer.processed.post', array( $this, 'add_fusion_demo_import_meta' ), 10, 5 );
		$this->import_fusion_sliders();
		remove_action( 'wxr_importer.processed.post', array( $this, 'add_fusion_demo_import_meta' ), 10 );

		$this->import_layer_sliders();
		$this->import_revolution_sliders();
	}

	/**
	 * Imports LayerSlider.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_layer_sliders() {
		global $wpdb;

		// Import Layerslider.
		if ( defined( 'LS_PLUGIN_VERSION' ) && file_exists( WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php' ) && false !== $this->importer_files->get_layerslider() ) {
			// Get importUtil.
			include WP_PLUGIN_DIR . '/LayerSlider/classes/class.ls.importutil.php';

			foreach ( $this->importer_files->get_layerslider() as $layer_file ) {
				// Finally import rev slider data files.
				$filepath = $this->importer_files->get_path( 'layersliders/' . $layer_file );
				$import = new LS_ImportUtil( $filepath );
			}

			// Get all sliders
			// Table name.
			$table_name = $wpdb->prefix . 'layerslider';

			// Get sliders.
			$sliders = $wpdb->get_results( "SELECT * FROM $table_name WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY date_c ASC" );
			$slides = array();
			if ( ! empty( $sliders ) ) {
				foreach ( $sliders as $key => $item ) {
					$slides[ $item->id ] = $item->name;

					$this->content_tracker->add_layer_slider_to_stack( $item->id );
				}
			}

			if ( $slides ) {
				foreach ( $slides as $key => $val ) {
					$slides_array[ $val ] = $key;
				}
			}

			// Assign LayerSlider.
			if ( 'classic' == $this->demo_type ) {
				// @codingStandardsIgnoreLine
				$lspage = get_page_by_title( 'Layer Slider' );
				if ( isset( $lspage ) && $lspage->ID && $slides_array['Avada Full Width'] ) {
					update_post_meta( $lspage->ID, 'pyre_slider', $slides_array['Avada Full Width'] );
				}
			}
		} // End if().

	}

	/**
	 * Imports revsliders.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_revolution_sliders() {

		// Import Revslider.
		if ( class_exists( 'UniteFunctionsRev' ) && false != $this->importer_files->get_revslider() ) { // If revslider is activated.

			$slider = new RevSlider();
			foreach ( $this->importer_files->get_revslider() as $rev_file ) {
				// Finally import rev slider data files.
				$filepath = $this->importer_files->get_path( 'revsliders/' . $rev_file );
				ob_start();
				$result = $slider->importSliderFromPost( true, false, $filepath );
				ob_clean();
				ob_end_clean();

				if ( true === $result['success'] ) {
					$this->content_tracker->add_rev_slider_to_stack( $result['sliderID'] );
				}
			}
		}
	}

	/**
	 * Import fusion-sliders.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_fusion_sliders() {

		// Fusion Sliders Import.
		if ( true === $this->fs_exists && class_exists( 'Fusion_Slider' ) && file_exists( $this->fs_url ) ) {

			add_action( 'fusion_slider_import_image_attached', array( $this, 'add_fusion_slider_demo_import_meta' ), 10, 2 );
			$fusion_slider = new Fusion_Slider();
			$fusion_slider->import_sliders( $this->fs_url, $this->demo_type );
			remove_action( 'fusion_slider_import_image_attached', array( $this, 'add_fusion_slider_demo_import_meta' ), 10 );
		}

	}

	/**
	 * Adds meta to fusion-sliders.
	 *
	 * @access public
	 * @since 5.2
	 * @param int $attachment_id The attachment-ID.
	 * @param int $post_id       The post-ID.
	 */
	public function add_fusion_slider_demo_import_meta( $attachment_id, $post_id ) {
		update_post_meta( $attachment_id, 'fusion_slider_demo_import', $this->demo_type );
	}

	/**
	 * Sets home page, site title and imports menus.
	 *
	 * @access private
	 * @since 5.2
	 */
	private function import_general_data() {

		// Menus are imported with the rest of the content.
		// Set reading options. @codingStandardsIgnoreLine
		$homepage = get_page_by_title( $this->homepage_title );
		if ( isset( $homepage ) && $homepage->ID ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $homepage->ID ); // Front Page.
		}

		// Import site title.
		$site_title = 'Avada ' . ucwords( str_replace( '_', ' ', $this->demo_type ) );
		update_option( 'blogname', $site_title );

		$this->content_tracker->set( 'general_data', 'imported' );

		update_option( 'avada_imported_demo', 'true' );
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
