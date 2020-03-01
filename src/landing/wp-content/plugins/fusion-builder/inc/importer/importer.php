<?php

/**
 * Import Fusion elements/templates
 */
function fusion_builder_importer() {

	check_ajax_referer( 'fusion_import_nonce', 'fusion_import_nonce' );

	if ( isset( $_FILES ) && '' != $_FILES[0] ) {

		$file = $_FILES[0]['tmp_name'];

		if ( current_user_can( 'manage_options' ) ) {

			// we are loading importers.
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true );
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

				if ( isset( $file ) && ! empty( $file ) ) {

					$logger = new WP_Importer_Logger_HTML();

					// It's important to disable 'prefill_existing_posts'.
					// In case GUID of importing post matches GUID of an existing post it won't be imported.
					$importer = new Fusion_WXR_Importer( array(
							'fetch_attachments'      => true,
							'prefill_existing_posts' => false,
						)
					);

					$importer->set_logger( $logger );

					add_filter( 'wp_import_post_terms', 'add_fb_element_terms', 10, 3 );

					// Import data.
					ob_start();
					$importer->import( $file );
					ob_end_clean();

					remove_filter( 'wp_import_post_terms', 'add_fb_element_terms', 10 );

				}

				exit;
			}
		}
	}

	die();
}
add_action( 'wp_ajax_fusion_builder_importer', 'fusion_builder_importer' );

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
function add_fb_element_terms( $terms, $post_id, $data ) {

	if ( ! empty( $terms ) ) {

		$term_ids = array();
		foreach ( $terms as $term ) {

			if ( ! term_exists( $term['slug'], $term['taxonomy'] ) ) {
				$t = wp_insert_term(
					$term['name'],
					$term['taxonomy'],
					array(
						'slug' => $term['slug'],
					)
				);
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
 * Export Fusion elements/templates
 */
function fusion_export_xml() {

	if ( isset( $_GET['page'] ) && 'fusion-builder-options' == $_GET['page'] ) {

		$action = filter_input( INPUT_GET, 'fusion_action', FILTER_SANITIZE_STRING );
		$post_type = filter_input( INPUT_GET, 'fusion_export_type', FILTER_SANITIZE_STRING );

		if ( 'export' == $action ) {

			if ( isset( $post_type ) && ! empty( $post_type ) ) {

				if ( current_user_can( 'export' ) ) {

					/** Load WordPress export API */
					require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/export.php' );

					$args = array( 'content' => $post_type );
					export_wp( $args );
					exit();
				}
			}
		}
	}
}
add_action( 'admin_init', 'fusion_export_xml' );

/**
 * Export Filename for elements/templates
 *
 * @param string $wp_filename Export file name.
 * @return string $wp_filename New export file name depends on the post type
 */
function fusion_export_filename( $wp_filename ) {

	if ( isset( $_GET['page'] ) && 'fusion-builder-options' == $_GET['page'] ) {

		$post_type = filter_input( INPUT_GET, 'fusion_export_type', FILTER_SANITIZE_STRING );
		$wp_filename = $post_type . '-' . $wp_filename;
		return $wp_filename;
	}
}
add_filter( 'export_wp_filename', 'fusion_export_filename' );
