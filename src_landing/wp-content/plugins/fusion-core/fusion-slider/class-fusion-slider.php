<?php
/**
 * Fusion-Slider main class.
 *
 * @package Fusion-Slider
 * @since 1.0.0
 */

if ( ! class_exists( 'Fusion_Slider' ) ) {
	/**
	 * The main Fusion_Slider class.
	 */
	class Fusion_Slider {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'init_post_type' ), 10 );
			add_action( 'wp', array( $this, 'init' ), 10 );
			add_action( 'wp_before_admin_bar_render', array( $this, 'fusion_admin_bar_render' ) );
			add_filter( 'themefusion_es_groups_row_actions', array( $this, 'remove_taxonomy_actions' ), 10, 1 );
			add_filter( 'slide-page_row_actions', array( $this, 'remove_taxonomy_actions' ), 10, 1 );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_menu', array( $this, 'reorder_admin_menu' ), 999 );

			// Add settings.
			add_action( 'slide-page_add_form_fields', array( $this, 'slider_add_new_meta_fields' ), 10, 2 );
			add_action( 'slide-page_edit_form_fields', array( $this, 'slider_edit_meta_fields' ), 10, 2 );
			add_action( 'edited_slide-page', array( $this, 'slider_save_taxonomy_custom_meta' ), 10, 2 );
			add_action( 'create_slide-page', array( $this, 'slider_save_taxonomy_custom_meta' ), 10, 2 );
			// Clone slide.
			add_action( 'admin_action_save_as_new_slide', array( $this, 'save_as_new_slide' ) );
			add_filter( 'post_row_actions',  array( $this, 'admin_clone_slide_button' ), 10, 2 );
			add_action( 'edit_form_after_title', array( $this, 'admin_clone_slide_button_after_title' ) );
			// Clone slider.
			add_filter( 'slide-page_row_actions', array( $this, 'admin_clone_slider_button' ), 10, 2 );
			add_action( 'slide-page_edit_form_fields', array( $this, 'admin_clone_slider_button_edit_form' ) );
			add_action( 'admin_action_clone_fusion_slider', array( $this, 'save_as_new_slider' ) );
		}


		/**
		 * Runs on wp_loaded.
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function init_post_type() {
			register_post_type(
				'slide',
				array(
					'public'              => true,
					'has_archive'         => false,
					'rewrite'             => array(
						'slug' => 'slide',
					),
					'supports'            => array( 'title', 'thumbnail' ),
					'can_export'          => true,
					'menu_position'       => 3333,
					'hierarchical'        => false,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'menu_icon'           => 'dashicons-fusiona-logo',
					'labels'              => array(
						'name'               => _x( 'Fusion Slides', 'Post Type General Name', 'fusion-core' ),
						'singular_name'      => _x( 'Fusion Slide', 'Post Type Singular Name', 'fusion-core' ),
						'menu_name'          => __( 'Fusion Slider', 'fusion-core' ),
						'parent_item_colon'  => __( 'Parent Slide:', 'fusion-core' ),
						'all_items'          => __( 'Add or Edit Slides', 'fusion-core' ),
						'view_item'          => __( 'View Slide', 'fusion-core' ),
						'add_new_item'       => __( 'Add New Slide', 'fusion-core' ),
						'add_new'            => __( 'Add New Slide', 'fusion-core' ),
						'edit_item'          => __( 'Edit Slide', 'fusion-core' ),
						'update_item'        => __( 'Update Slide', 'fusion-core' ),
						'search_items'       => __( 'Search Slide', 'fusion-core' ),
						'not_found'          => __( 'Not found', 'fusion-core' ),
						'not_found_in_trash' => __( 'Not found in Trash', 'fusion-core' ),
					),
				)
			);

			register_taxonomy('slide-page', 'slide',
				array(
					'hierarchical'      => true,
					'label'             => 'Slider',
					'query_var'         => true,
					'rewrite'           => true,
					'hierarchical'      => true,
					'show_in_nav_menus' => false,
					'show_tagcloud'     => false,
					'labels'            => array(
						'name'                       => __( 'Fusion Sliders', 'fusion-core' ),
						'singular_name'              => __( 'Fusion Slider', 'fusion-core' ),
						'menu_name'                  => __( 'Add or Edit Sliders', 'fusion-core' ),
						'all_items'                  => __( 'All Sliders', 'fusion-core' ),
						'parent_item_colon'          => __( 'Parent Slider:', 'fusion-core' ),
						'new_item_name'              => __( 'New Slider Name', 'fusion-core' ),
						'add_new_item'               => __( 'Add Slider', 'fusion-core' ),
						'edit_item'                  => __( 'Edit Slider', 'fusion-core' ),
						'update_item'                => __( 'Update Slider', 'fusion-core' ),
						'separate_items_with_commas' => __( 'Separate sliders with commas', 'fusion-core' ),
						'search_items'               => __( 'Search Sliders', 'fusion-core' ),
						'add_or_remove_items'        => __( 'Add or remove sliders', 'fusion-core' ),
						'choose_from_most_used'      => __( 'Choose from the most used sliders', 'fusion-core' ),
						'not_found'                  => __( 'Not Found', 'fusion-core' ),
					),
				)
			);
		}

		/**
		 * Runs on wp.
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function init() {
			global $post;

			if ( ! class_exists( 'Fusion' ) || ! class_exists( 'Fusion_Settings' ) ) {
				return;
			}

			global $fusion_settings, $fusion_library;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}
			if ( ! $fusion_library ) {
				$fusion_library = Fusion::get_instance();
			}

			if ( $fusion_settings->get( 'status_fusion_slider' ) ) {

				// Check if header is enabled.
				if ( ! is_page_template( 'blank.php' ) && is_object( $post ) && 'no' !== fusion_get_page_option( 'display_header', $post->ID ) ) {
					$dependencies = array( 'jquery', 'avada-header', 'modernizr', 'cssua', 'jquery-flexslider', 'fusion-flexslider','froogaloop', 'fusion-video-general', 'fusion-video-bg' );
				} else {
					$dependencies = array( 'jquery', 'modernizr', 'cssua', 'jquery-flexslider', 'fusion-flexslider','froogaloop', 'fusion-video-general', 'fusion-video-bg' );
				}

				if ( $fusion_settings->get( 'typography_responsive' ) ) {
					$dependencies[] = 'fusion-responsive-typography';
				}
				if ( function_exists( 'fusion_is_element_enabled' ) && fusion_is_element_enabled( 'fusion_title' ) ) {
					$dependencies[] = 'fusion-title';
				}
				Fusion_Dynamic_JS::enqueue_script(
					'avada-fusion-slider',
					FusionCore_Plugin::$js_folder_url . '/avada-fusion-slider.js',
					FusionCore_Plugin::$js_folder_path . '/avada-fusion-slider.js',
					$dependencies,
					'1',
					true
				);
				$slider_position = get_post_meta( $fusion_library->get_page_id(), 'pyre_slider_position', true );
				$header_bg_opacity = get_post_meta( $fusion_library->get_page_id(), 'pyre_header_bg_opacity', true );
				Fusion_Dynamic_JS::localize_script(
					'avada-fusion-slider',
					'avadaFusionSliderVars',
					array(
						'side_header_break_point' => (int) $fusion_settings->get( 'side_header_break_point' ),
						'slider_position'         => ( $slider_position && 'default' !== $slider_position ) ? $slider_position : strtolower( $fusion_settings->get( 'slider_position' ) ),
						'header_transparency'     => ( ( 1 !== Fusion_Color::new_color( $fusion_settings->get( 'header_bg_color' ) )->alpha && ! $header_bg_opacity && '0' !== $header_bg_opacity ) || ( $header_bg_opacity && 1 > $header_bg_opacity ) || '0' === $header_bg_opacity ) ? 1 : 0,
						'header_position'         => $fusion_settings->get( 'header_position' ),
						'content_break_point'     => intval( $fusion_settings->get( 'content_break_point' ) ),
						'status_vimeo'            => $fusion_settings->get( 'status_vimeo' ),
					)
				);
			} // End if().
		}

		/**
		 * Removes the 'view' in the admin bar.
		 *
		 * @access public
		 */
		public function fusion_admin_bar_render() {
			global $wp_admin_bar, $typenow;

			if ( 'slide' === $typenow || 'themefusion_elastic' === $typenow ) {
				$wp_admin_bar->remove_menu( 'view' );
			}
		}

		/**
		 * Removes the 'view' link in taxonomy page.
		 *
		 * @access public
		 * @param array $actions WordPress actions array for the taxonomy admin page.
		 * @return array $actions
		 */
		public function remove_taxonomy_actions( $actions ) {
			global $typenow;

			if ( 'slide' === $typenow || 'themefusion_elastic' === $typenow ) {
				unset( $actions['view'] );
			}
			return $actions;
		}
		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return    void
		 */
		function admin_init() {
			global $pagenow;

			$post_type = '';

			if ( isset( $_GET['post'] ) && $_GET['post'] ) {
				$post_type = get_post_type( $_GET['post'] );
			}

			if ( ( isset( $_GET['taxonomy'] ) && 'slide-page' === $_GET['taxonomy'] ) || ( isset( $_GET['post_type'] ) && 'slide' === $_GET['post_type'] ) || 'slide' === $post_type ) {
				wp_enqueue_script( 'fusion-slider', esc_url_raw( FusionCore_Plugin::$js_folder_url . '/fusion-slider.js' ), false, '1.0', true );
			}

			if ( isset( $_GET['page'] ) && 'fs_export_import' === $_GET['page'] ) {
				$this->export_sliders();
			}
		}

		/**
		 * Adds the submenu.
		 *
		 * @access public
		 */
		public function admin_menu() {
			global $submenu;
			unset( $submenu['edit.php?post_type=slide'][10] );

			add_submenu_page( 'edit.php?post_type=slide', __( 'Export / Import', 'fusion-core' ), __( 'Export / Import', 'fusion-core' ), 'manage_options', 'fs_export_import', array( $this, 'fs_export_import_settings' ) );
		}

		/**
		 * Reorders the admin menu.
		 *
		 * @access public
		 * @return array
		 */
		public function reorder_admin_menu() {
			global $menu;
			if ( isset( $menu[3333] ) ) {
				$menu['2.333333'] = $menu[3333];
				unset( $menu[3333] );
			}
			return $menu;
		}

		/**
		 * Add term page.
		 *
		 * @access public
		 */
		public function slider_add_new_meta_fields() {

			// This will add the custom meta field to the add new term page.
			include FUSION_CORE_PATH . '/fusion-slider/templates/add-new-meta-fields.php';

		}

		/**
		 * Edit term page.
		 *
		 * @access public
		 * @param object $term The term object.
		 */
		public function slider_edit_meta_fields( $term ) {
			// Put the term ID into a variable.
			$t_id = $term->term_id;

			// Retrieve the existing value(s) for this meta field. This returns an array.
			$term_meta = get_option( "taxonomy_$t_id" );

			if ( ! array_key_exists( 'typo_sensitivity', $term_meta ) ) {
				$term_meta['typo_sensitivity'] = '1';
			}

			if ( ! array_key_exists( 'typo_factor', $term_meta ) ) {
				$term_meta['typo_factor'] = '1.5';
			}

			if ( ! array_key_exists( 'nav_box_width', $term_meta ) ) {
				$term_meta['nav_box_width'] = '63px';
			}

			if ( ! array_key_exists( 'nav_box_height', $term_meta ) ) {
				$term_meta['nav_box_height'] = '63px';
			}

			if ( ! array_key_exists( 'nav_arrow_size', $term_meta ) ) {
				$term_meta['nav_arrow_size'] = '25px';
			}
			include FUSION_CORE_PATH . '/fusion-slider/templates/edit-meta-fields.php';

		}

		/**
		 * Save extra taxonomy fields callback function.
		 *
		 * @access public
		 * @param int $term_id The term ID.
		 */
		public function slider_save_taxonomy_custom_meta( $term_id ) {
			// @codingStandardsIgnoreLine
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id      = $term_id;
				$term_meta = get_option( 'taxonomy_' . $t_id );
				// @codingStandardsIgnoreLine
				$cat_keys  = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ) {
					// @codingStandardsIgnoreLine
					if ( isset( $_POST['term_meta'][ $key ] ) ) {
						// @codingStandardsIgnoreLine
						$term_meta[ $key ] = $_POST['term_meta'][ $key ];
					}
				}
				// Save the option array.
				update_option( "taxonomy_$t_id", $term_meta );
			}
		}

		/**
		 * Export/Import Settings Page.
		 *
		 * @access public
		 */
		public function fs_export_import_settings() {
			if ( $_FILES ) {
				// @codingStandardsIgnoreLine
				$this->import_sliders( $_FILES['import']['tmp_name'] );
			}
			include FUSION_CORE_PATH . '/fusion-slider/templates/export-import-settings.php';
		}

		/**
		 * Exports the sliders.
		 *
		 * @access public
		 */
		public function export_sliders() {

			// @codingStandardsIgnoreLine
			if ( isset( $_POST['export_button'] ) && $_POST['export_button'] ) {
				// Load Importer API.
				require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/export.php' );

				ob_start();
				export_wp( array(
					'content' => 'slide',
				) );
				$export = ob_get_contents();
				ob_get_clean();

				$terms = get_terms( 'slide-page', array(
					'hide_empty' => 1,
				) );

				foreach ( $terms as $term ) {
					$term_meta = get_option( 'taxonomy_' . $term->term_id );
					$export_terms[ $term->slug ] = $term_meta;
				}

				$json_export_terms = wp_json_encode( $export_terms );

				$upload_dir = wp_upload_dir();
				$base_dir = trailingslashit( $upload_dir['basedir'] );
				$fs_dir = $base_dir . 'fusion_slider/';
				wp_mkdir_p( $fs_dir );

				// @codingStandardsIgnoreLine
				$loop = new WP_Query( array( 'post_type' => 'slide', 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id' ) );

				while ( $loop->have_posts() ) { $loop->the_post();
					$post_image_id = get_post_thumbnail_id( get_the_ID() );
					$image_path = get_attached_file( $post_image_id );
					if ( isset( $image_path ) && $image_path ) {
						$ext = pathinfo( $image_path, PATHINFO_EXTENSION );
						$this->filesystem()->copy( $image_path, $fs_dir . $post_image_id . '.' . $ext, true );
					}
				}

				wp_reset_postdata();

				$url = wp_nonce_url( 'edit.php?post_type=slide&page=fs_export_import' );
				if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
					return; // Stop processing here.
				}

				if ( WP_Filesystem( $creds ) ) {
					global $wp_filesystem;

					if ( ! $wp_filesystem->put_contents( $fs_dir . 'sliders.xml', $export, FS_CHMOD_FILE ) || ! $wp_filesystem->put_contents( $fs_dir . 'settings.json', $json_export_terms, FS_CHMOD_FILE ) ) {
						echo 'Couldn\'t export sliders, make sure wp-content/uploads is writeable.';
					} else {
						// Initialize archive object.
						$zip = new ZipArchive;
						$zip->open( 'fusion_slider.zip', ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );

						foreach ( new DirectoryIterator( $fs_dir ) as $file ) {
							if ( $file->isDot() ) {
								continue;
							}

							$zip->addFile( $fs_dir . $file->getFilename(), $file->getFilename() );
						}

						$zip_file = $zip->filename;

						// Zip archive will be created only after closing object.
						$zip->close();

						header( 'X-Accel-Buffering: no' );
						header( 'Pragma: public' );
						header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
						header( 'Content-Length: ' . filesize( $zip_file ) );
						header( 'Content-Type: application/octet-stream' );
						header( 'Content-Disposition: attachment; filename="fusion_slider.zip"' );
						ob_clean();
						flush();
						readfile( $zip_file );

						foreach ( new DirectoryIterator( $fs_dir ) as $file ) {
							if ( $file->isDot() ) {
								continue;
							}

							$this->filesystem()->delete( $fs_dir . $file->getFilename() );
						}
					} // End if().
				} // End if().
			} // End if().
		}

		/**
		 * Imports sliders from a zip file.
		 *
		 * @access public
		 * @param string $zip_file The path to the zip file.
		 * @param string $demo_type Demo type, used when sliders are imported during demo import process.
		 */
		public function import_sliders( $zip_file = '', $demo_type = null ) {
			if ( isset( $zip_file ) && '' !== $zip_file ) {
				$upload_dir = wp_upload_dir();
				$base_dir   = trailingslashit( $upload_dir['basedir'] );
				$fs_dir     = $base_dir . 'fusion_slider_exports/';

				// Delete entire folder to ensure all it's content is removed.
				$this->filesystem()->delete( $fs_dir, true, 'd' );

				// Attempt to manually extract the zip file first. Required for fptext method.
				if ( class_exists( 'ZipArchive' ) ) {
					$zip = new ZipArchive;
					if ( true === $zip->open( $zip_file ) ) {
						$zip->extractTo( $fs_dir );
						$zip->close();
					}
				}

				unzip_file( $zip_file, $fs_dir );

				// Replace remote URLs with local ones.
				$sliders_xml = $this->filesystem()->get_contents( $fs_dir . 'sliders.xml' );

				// This is run when Avada demo content is imported.
				if ( null !== $demo_type ) {

					// Replace placeholders.
					$home_url = untrailingslashit( get_home_url() );

					// In 'classic' demo case 'avada-xml' should be used for replacements.
					$demo = $demo_type;
					if ( 'classic' === $demo ) {
						$demo = 'avada-xml';
					}
					$demo = str_replace( '_', '-', $demo );

					// Replace URLs.
					$sliders_xml = str_replace(
						array(
							'http://avada.theme-fusion.com/' . $demo,
							'https://avada.theme-fusion.com/' . $demo,
						),
						$home_url,
						$sliders_xml
					);

					// Make sure assets are still from the remote server.
					// We can use http instead of https here for performance reasons
					// since static assets don't require https anyway.
					$sliders_xml = str_replace(
						$home_url . '/wp-content/',
						'http://avada.theme-fusion.com/' . $demo . '/wp-content/',
						$sliders_xml
					);

				}

				$sliders_xml = preg_replace_callback( '/(?<=<wp:meta_value><!\[CDATA\[)(https?:\/\/avada.theme-fusion.com)+(.*?)(?=]]><)/', 'fusion_fs_importer_replace_url', $sliders_xml );
				$this->filesystem()->put_contents( $fs_dir . 'sliders.xml', $sliders_xml );

				if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
					define( 'WP_LOAD_IMPORTERS', true );
				}

				if ( ! class_exists( 'WP_Importer' ) ) { // If main importer class doesn't exist.
					$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
					include $wp_importer;
				}

				if ( ! class_exists( 'WXR_Importer' ) ) { // If WP importer doesn't exist.
					include FUSION_LIBRARY_PATH . '/inc/importer/class-logger.php';
					include FUSION_LIBRARY_PATH . '/inc/importer/class-logger-html.php';

					$wp_import = FUSION_LIBRARY_PATH . '/inc/importer/class-wxr-importer.php';
					include $wp_import;
				}

				if ( ! class_exists( 'Fusion_WXR_Importer' ) ) {
					include FUSION_LIBRARY_PATH . '/inc/importer/class-fusion-wxr-importer.php';
				}

				if ( class_exists( 'WP_Importer' ) && class_exists( 'WXR_Importer' ) && class_exists( 'Fusion_WXR_Importer' ) ) { // Check for main import class and wp import class.

					$xml = $fs_dir . 'sliders.xml';

					$logger = new WP_Importer_Logger_HTML();

					// It's important to disable 'prefill_existing_posts'.
					// In case GUID of importing post matches GUID of an existing post it won't be imported.
					$importer = new Fusion_WXR_Importer( array(
							'fetch_attachments'      => true,
							'prefill_existing_posts' => false,
						)
					);

					$importer->set_logger( $logger );

					add_filter( 'wp_import_post_terms', array( $this, 'add_slider_terms' ), 10, 3 );

					ob_start();
					$importer->import( $xml );
					ob_end_clean();

					remove_filter( 'wp_import_post_terms', array( $this, 'add_slider_terms' ), 10 );

					// @codingStandardsIgnoreLine
					$loop = new WP_Query( array( 'post_type' => 'slide', 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id' ) );

					if ( $loop->have_posts() ) {

						while ( $loop->have_posts() ) { $loop->the_post();
							$post_thumb_meta = get_post_meta( get_the_ID(), '_thumbnail_id', true );

							if ( isset( $post_thumb_meta ) && $post_thumb_meta ) {
								$thumbnail_ids[ $post_thumb_meta ] = get_the_ID();
							}
						}
					}
					wp_reset_postdata();

					foreach ( new DirectoryIterator( $fs_dir ) as $file ) {
						if ( $file->isDot() || '.DS_Store' === $file->getFilename() ) {
							continue;
						}

						$image_path = pathinfo( $fs_dir . $file->getFilename() );

						if ( 'xml' !== $image_path['extension'] && 'json' !== $image_path['extension'] ) {
							$filename          = $image_path['filename'];
							$new_file_basename = wp_unique_filename( $upload_dir['path'] . '/', $image_path['basename'] );
							$new_image_path    = $upload_dir['path'] . '/' . $new_file_basename;
							$new_image_url     = $upload_dir['url'] . '/' . $new_file_basename;
							$this->filesystem()->copy( $fs_dir . $file->getFilename(), $new_image_path, true );

							// Check the type of tile. We'll use this as the 'post_mime_type'.
							$filetype = wp_check_filetype( basename( $new_image_path ), null );

							// Prepare an array of post data for the attachment.
							$attachment = array(
								'guid'           => $new_image_url,
								'post_mime_type' => $filetype['type'],
								'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $new_image_path ) ),
								'post_content'   => '',
								'post_status'    => 'inherit',
							);

							// Insert the attachment.
							if ( isset( $thumbnail_ids[ $filename ] ) && $thumbnail_ids[ $filename ] ) {
								$attach_id = wp_insert_attachment( $attachment, $new_image_path, $thumbnail_ids[ $filename ] );

								// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
								require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/image.php' );

								// Generate the metadata for the attachment, and update the database record.
								$attach_data = wp_generate_attachment_metadata( $attach_id, $new_image_path );
								wp_update_attachment_metadata( $attach_id, $attach_data );

								set_post_thumbnail( $thumbnail_ids[ $filename ], $attach_id );

								do_action( 'fusion_slider_import_image_attached', $attach_id, $thumbnail_ids[ $filename ] );
							}
						}
					} // End foreach().

					$url = wp_nonce_url( 'edit.php?post_type=slide&page=fs_export_import' );
					if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
						return; // Stop processing here.
					}

					if ( WP_Filesystem( $creds ) ) {
						global $wp_filesystem;

						$settings = $wp_filesystem->get_contents( $fs_dir . 'settings.json' );

						$decode = json_decode( $settings, true );

						if ( is_array( $decode ) ) {
							foreach ( $decode as $slug => $settings ) {
								// @codingStandardsIgnoreLine
								$get_term = get_term_by( 'slug', $slug, 'slide-page' );

								if ( $get_term ) {
									update_option( 'taxonomy_' . $get_term->term_id, $settings );
								}
							}
						}
					}
				} // End if().
			} else {
				echo '<p>' . esc_attr__( 'No file to import.', 'fusion-core' ) . '</p>';
			} // End if().
		}

		/**
		 * Correcting importer bug which uses 'wp_set_post_terms' to set terms for all post types.
		 * This is used to create 'slide-page' term (if it doesn't exist) and set it to a 'slide' post.
		 *
		 * @param array $terms Post terms.
		 * @param int   $post_id Post ID.
		 * @param array $data Raw data imported for the post.
		 *
		 * @return mixed
		 */
		public function add_slider_terms( $terms, $post_id, $data ) {

			if ( ! empty( $terms ) ) {

				$term_ids = array();
				foreach ( $terms as $term ) {

					if ( ! term_exists( $term['slug'], $term['taxonomy'] ) ) {
						wp_insert_term(
							$term['name'],
							$term['taxonomy'],
							array(
								'slug' => $term['slug'],
							)
						);

						$t = get_term_by( 'slug', $term['slug'], $term['taxonomy'], ARRAY_A );
						do_action( 'fusion_slider_import_processed_term', $t['term_id'], $t );
					} else {
						$t = get_term_by( 'slug', $term['slug'], $term['taxonomy'], ARRAY_A );
					}

					$term_ids[ $term['taxonomy'] ][] = (int) $t['term_id'];
				}

				foreach ( $term_ids as $tax => $ids ) {
					wp_set_object_terms( $post_id, $ids, $tax );
				}
			}

			return $terms;
		}

		/**
		 * Clones the slide button.
		 *
		 * @access public
		 * @param array  $actions An array of actions.
		 * @param object $post    The post object.
		 */
		public function admin_clone_slide_button( $actions, $post ) {
			if ( current_user_can( 'manage_options' ) && 'slide' === $post->post_type ) {
				$actions['clone_slide'] = '<a href="' . $this->get_slide_clone_link( $post->ID ) . '" title="' . esc_attr( __( 'Clone this slide', 'fusion-core' ) ) . '">' . __( 'Clone', 'fusion-core' ) . '</a>';
			}
			return $actions;
		}

		/**
		 * Clones the slider button.
		 *
		 * @access public
		 * @param array  $actions An array of actions.
		 * @param object $term    The term object.
		 */
		public function admin_clone_slider_button( $actions, $term ) {
			$args = array(
				'slider_id'                  => $term->term_id,
				'_fusion_slider_clone_nonce' => wp_create_nonce( 'clone_slider' ),
				'action'                     => 'clone_fusion_slider',
			);

			$url = add_query_arg( $args, admin_url( 'edit-tags.php' ) );
			$actions['clone_slider'] = "<a href='{$url}' title='" . __( 'Clone this slider', 'fusion-core' ) . "'>" . __( 'Clone', 'fusion-core' ) . '</a>';

			return $actions;
		}

		/**
		 * Clones the slider button edit form.
		 *
		 * @access public
		 * @param object $term The term object.
		 */
		public function admin_clone_slider_button_edit_form( $term ) {

			if ( isset( $_GET['taxonomy'] ) && 'slide-page' === $_GET['taxonomy'] && current_user_can( 'manage_options' ) ) {

				$args = array(
					'slider_id'                  => $term->term_id,
					'_fusion_slider_clone_nonce' => wp_create_nonce( 'clone_slider' ),
					'action'                     => 'clone_fusion_slider',
				);

				$url = add_query_arg( $args, admin_url( 'edit-tags.php' ) );
				include FUSION_CORE_PATH . '/fusion-slider/templates/clone-button-edit-form.php';
			}
		}

		/**
		 * Clones the slider button after the title.
		 *
		 * @access public
		 * @param object $post The post object.
		 */
		public function admin_clone_slide_button_after_title( $post ) {
			if ( isset( $_GET['post'] ) && current_user_can( 'manage_options' ) && 'slide' === $post->post_type ) {
				include FUSION_CORE_PATH . '/fusion-slider/templates/clone-button-after-title.php';
			}
		}

		/**
		 * Saves a new slider.
		 *
		 * @access public
		 */
		public function save_as_new_slider() {
			if ( isset( $_REQUEST['_fusion_slider_clone_nonce'] ) && check_admin_referer( 'clone_slider', '_fusion_slider_clone_nonce' ) && current_user_can( 'manage_options' ) ) {

				// @codingStandardsIgnoreLine
				$term_id            = $_REQUEST['slider_id'];
				$term_tax           = 'slide-page';
				$original_term      = get_term( $term_id, $term_tax );
				$original_term_meta = get_option( 'taxonomy_' . $term_id );
				$new_term_name      = sprintf( esc_attr__( '%s ( Cloned )', 'fusion-core' ), $original_term->name );

				$term_details = array(
					'description' => $original_term->description,
					'slug'        => wp_unique_term_slug( $original_term->slug, $original_term ),
					'parent'      => $original_term->parent,
				);

				$new_term = wp_insert_term( $new_term_name, $term_tax, $term_details );

				if ( ! is_wp_error( $new_term ) ) {

					// Add slides (posts) to new slider (term).
					$posts = get_objects_in_term( $term_id, $term_tax );

					if ( ! is_wp_error( $posts ) ) {
						foreach ( $posts as $post_id ) {
							$result = wp_set_post_terms( $post_id, $new_term['term_id'], $term_tax, true );
						}
					}

					// Clone slider (term) meta.
					if ( isset( $original_term_meta ) ) {
						$t_id = $new_term['term_id'];
						update_option( "taxonomy_$t_id", $original_term_meta );
					}

					// Redirect to the all sliders screen.
					wp_safe_redirect( admin_url( 'edit-tags.php?taxonomy=slide-page&post_type=slide' ) );
				}
			} // End if().
		}

		/**
		 * Gets the link to clone a slide.
		 *
		 * @access public
		 * @param int $id The post-id.
		 * @return string
		 */
		public function get_slide_clone_link( $id = 0 ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$post = get_post( $id );
			if ( ! $post ) {
				return;
			}

			$args = array(
				'_fusion_slide_clone_nonce' => wp_create_nonce( 'clone_slide' ),
				'post'                      => $post->ID,
				'action'                    => 'save_as_new_slide',
			);

			$url = add_query_arg( $args, admin_url( 'admin.php' ) );

			return $url;
		}

		/**
		 * Saves a new slide.
		 *
		 * @access public
		 */
		public function save_as_new_slide() {

			if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'save_as_new_slide' === $_REQUEST['action'] ) ) ) {
				wp_die( esc_attr__( 'No slide to clone.', 'fusion-core' ) );
			}

			if ( isset( $_REQUEST['_fusion_slide_clone_nonce'] ) && check_admin_referer( 'clone_slide', '_fusion_slide_clone_nonce' ) && current_user_can( 'manage_options' ) ) {

				// Get the post being copied.
				$id   = ( isset( $_GET['post'] ) ? $_GET['post'] : $_POST['post'] );
				$post = get_post( $id );

				// Copy the post and insert it.
				if ( isset( $post ) && $post ) {
					$new_id = $this->clone_slide( $post );

					// Redirect to the all slides screen.
					wp_safe_redirect( admin_url( 'edit.php?post_type=' . $post->post_type ) );

					exit;

				} else {
					// @codingStandardsIgnoreLine
					wp_die( sprintf( esc_attr__( 'Cloninig failed. Post not found. ID: %s', 'fusion-core' ), htmlspecialchars( $id ) ) );
				}
			}
		}

		/**
		 * Clones a slide.
		 *
		 * @access public
		 * @param object $post The post object.
		 */
		public function clone_slide( $post ) {
			// Ignore revisions.
			if ( 'revision' === $post->post_type ) {
				return;
			}

			$post_meta_keys = get_post_custom_keys( $post->ID );

			$new_post = array(
				'menu_order'     => $post->menu_order,
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $post->post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_mime_type' => $post->post_mime_type,
				'post_parent'    => $new_post_parent = $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'publish',
				'post_title'     => sprintf( esc_attr__( '%s ( Cloned )', 'fusion-core' ), $post->post_title ),
				'post_type'      => $post->post_type,
			);

			// Add new slide post.
			$new_post_id = wp_insert_post( $new_post );

			// Set a proper slug.
			$post_name             = wp_unique_post_slug( $post->post_name, $new_post_id, 'publish', $post->post_type, $new_post_parent );
			$new_post              = array();
			$new_post['ID']        = $new_post_id;
			$new_post['post_name'] = $post_name;

			wp_update_post( $new_post );

			// Clone post meta.
			if ( ! empty( $post_meta_keys ) ) {

				foreach ( $post_meta_keys as $meta_key ) {
					$meta_values = get_post_custom_values( $meta_key, $post->ID );

					foreach ( $meta_values as $meta_value ) {
						$meta_value = maybe_unserialize( $meta_value );
						add_post_meta( $new_post_id, $meta_key, $meta_value );
					}
				}
			}

			return $new_post_id;
		}

		/**
		 * Renders a slider.
		 *
		 * @access public
		 * @param string $term The term slug.
		 */
		public static function render_fusion_slider( $term ) {

			global $fusion_settings;
			if ( ! $fusion_settings ) {
				$fusion_settings = Fusion_Settings::get_instance();
			}

			if ( $fusion_settings->get( 'status_fusion_slider' ) ) {
				// @codingStandardsIgnoreLine
				$term_details    = get_term_by( 'slug', $term, 'slide-page' );
				$slider_settings = array();

				if ( is_object( $term_details ) ) {
					$slider_settings = get_option( 'taxonomy_' . $term_details->term_id );
				}

				if ( ! isset( $slider_settings['typo_sensitivity'] ) ) {
					$slider_settings['typo_sensitivity'] = '0.6';
				}

				if ( ! isset( $slider_settings['typo_factor'] ) ) {
					$slider_settings['typo_factor'] = '1.5';
				}

				if ( ! isset( $slider_settings['slider_width'] ) || '' === $slider_settings['slider_width'] ) {
					$slider_settings['slider_width'] = '100%';
				}

				if ( ! isset( $slider_settings['slider_height'] ) || '' === $slider_settings['slider_height'] ) {
					$slider_settings['slider_height'] = '500px';
				}

				if ( ! isset( $slider_settings['full_screen'] ) ) {
					$slider_settings['full_screen'] = false;
				}

				if ( ! isset( $slider_settings['animation'] ) ) {
					$slider_settings['animation'] = true;
				}

				if ( ! isset( $slider_settings['nav_box_width'] ) ) {
					$slider_settings['nav_box_width'] = '63px';
				}

				if ( ! isset( $slider_settings['nav_box_height'] ) ) {
					$slider_settings['nav_box_height'] = '63px';
				}

				if ( ! isset( $slider_settings['nav_arrow_size'] ) ) {
					$slider_settings['nav_arrow_size'] = '25px';
				}

				$nav_box_height_half = '0';
				if ( $slider_settings['nav_box_height'] ) {
					$nav_box_height_half = intval( $slider_settings['nav_box_height'] ) / 2;
				}

				$slider_data = '';

				if ( $slider_settings ) {
					foreach ( $slider_settings as $slider_setting => $slider_setting_value ) {
						$slider_data .= 'data-' . $slider_setting . '="' . $slider_setting_value . '" ';
					}
				}

				$slider_class = '';

				if ( '100%' === $slider_settings['slider_width'] && ! $slider_settings['full_screen'] ) {
					$slider_class .= ' full-width-slider';
				} elseif ( '100%' !== $slider_settings['slider_width'] && ! $slider_settings['full_screen'] ) {
					$slider_class .= ' fixed-width-slider';
				}

				if ( isset( $slider_settings['slider_content_width'] ) && '' !== $slider_settings['slider_content_width'] ) {
					$content_max_width = 'max-width:' . $slider_settings['slider_content_width'];
				} else {
					$content_max_width = '';
				}

				$args = array(
					'post_type'        => 'slide',
					// @codingStandardsIgnoreLine
					'posts_per_page'   => -1,
					'suppress_filters' => 0,
				);
				$args['tax_query'][] = array(
					'taxonomy' => 'slide-page',
					'field'    => 'slug',
					'terms'    => $term,
				);

				$query = FusionCore_Plugin::fusion_core_cached_query( $args );

				if ( $query->have_posts() ) {
					include FUSION_CORE_PATH . '/fusion-slider/templates/slider.php';
				}

				wp_reset_postdata();
			} // End if().
		}

		/**
		 * Gets the $wp_filesystem.
		 *
		 * @access private
		 * @since 3.1
		 * @return object
		 */
		private function filesystem() {
			// The Wordpress filesystem.
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			return $wp_filesystem;
		}
	}

	$fusion_slider = new Fusion_Slider();
} // End if().
