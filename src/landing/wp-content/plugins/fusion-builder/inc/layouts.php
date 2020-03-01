<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Register the layouts.
 */
function fusion_builder_register_layouts() {

	$labels = array(
		'name'               => _x( 'Fusion Templates', 'Layout type general name', 'fusion-builder' ),
		'singular_name'      => _x( 'Layout', 'Layout type singular name', 'fusion-builder' ),
		'add_new'            => _x( 'Add New', 'Layout item', 'fusion-builder' ),
		'add_new_item'       => esc_attr__( 'Add New Layout', 'fusion-builder' ),
		'edit_item'          => esc_attr__( 'Edit Layout', 'fusion-builder' ),
		'new_item'           => esc_attr__( 'New Layout', 'fusion-builder' ),
		'all_items'          => esc_attr__( 'All Layouts', 'fusion-builder' ),
		'view_item'          => esc_attr__( 'View Layout', 'fusion-builder' ),
		'search_items'       => esc_attr__( 'Search Layouts', 'fusion-builder' ),
		'not_found'          => esc_attr__( 'Nothing found', 'fusion-builder' ),
		'not_found_in_trash' => esc_attr__( 'Nothing found in Trash', 'fusion-builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'can_export'          => true,
		'query_var'           => true,
		'has_archive'         => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
		'hierarchical'        => false,
		'show_in_nav_menus'   => false,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'supports'            => array( 'title', 'editor', 'revisions' ),
	);

	register_post_type( 'fusion_template', apply_filters( 'fusion_layout_template_args', $args ) );

	$labels = array(
		'name'               => _x( 'Fusion Elements', 'element type general name', 'fusion-builder' ),
		'singular_name'      => _x( 'Element', 'Element type singular name', 'fusion-builder' ),
		'add_new'            => _x( 'Add New', 'Element item', 'fusion-builder' ),
		'add_new_item'       => esc_attr__( 'Add New Element', 'fusion-builder' ),
		'edit_item'          => esc_attr__( 'Edit Element', 'fusion-builder' ),
		'new_item'           => esc_attr__( 'New Element', 'fusion-builder' ),
		'all_items'          => esc_attr__( 'All Elements', 'fusion-builder' ),
		'view_item'          => esc_attr__( 'View Element', 'fusion-builder' ),
		'search_items'       => esc_attr__( 'Search Elements', 'fusion-builder' ),
		'not_found'          => esc_attr__( 'Nothing found', 'fusion-builder' ),
		'not_found_in_trash' => esc_attr__( 'Nothing found in Trash', 'fusion-builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'can_export'         => true,
		'query_var'          => false,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'revisions' ),
	);

	register_post_type( 'fusion_element', apply_filters( 'fusion_layout_element_args', $args ) );

	$labels = array(
		'name' => esc_attr__( 'Category', 'fusion-builder' ),
	);

	register_taxonomy( 'element_category', array( 'fusion_element' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name' => esc_attr__( 'Category', 'fusion-builder' ),
	);

	register_taxonomy( 'template_category', array( 'fusion_template' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );
}
add_action( 'admin_init', 'fusion_builder_register_layouts' );

/**
 * Delete custom template or element.
 */
function fusion_builder_delete_layout() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( isset( $_POST['fusion_layout_id'] ) && '' !== $_POST['fusion_layout_id'] ) {

		$layout_id = (int) $_POST['fusion_layout_id'];

		if ( '' === $layout_id ) {
			die( -1 );
		}

		wp_delete_post( $layout_id, true );
	}

	die();
}
add_action( 'wp_ajax_fusion_builder_delete_layout', 'fusion_builder_delete_layout' );

/**
 * Add custom template or element.
 *
 * @param string $post_type The post-type.
 * @param string $name      The post-title.
 * @param string $content   The post-content.
 * @param array  $meta      The post-meta.
 * @param array  $taxonomy  Taxonomies.
 * @param string $term      Term.
 */
function fusion_builder_create_layout( $post_type, $name, $content, $meta = array(), $taxonomy = array(), $term = '' ) {

	$layout = array(
		'post_title'   => sanitize_text_field( $name ),
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => $post_type,
	);

	$layout_id = wp_insert_post( $layout );

	if ( ! empty( $meta ) ) {
		foreach ( $meta as $meta_key => $meta_value ) {
			add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
		}
	}

	if ( '' !== $term ) {
		wp_insert_term( $term, $taxonomy );
		$term_id = term_exists( $term, $taxonomy );
		wp_set_post_terms( $layout_id, $term_id, $taxonomy );
	}

	do_action( 'fusion_builder_create_layout_after' );

	return $layout_id;
}


/**
 * Display library tab content.
 */
function fusion_builder_display_library_content() {
	global $post;
	$saved_post = $post;
	?>
	<div class="fusion_builder_modal_settings">
		<div class="fusion-builder-modal-top-container">
			<div class="fusion-builder-modal-close fusiona-plus2"></div>
			<h2 class="fusion-builder-settings-heading"><?php esc_attr_e( 'Library', 'fusion-builder' ); ?></h2>
			<ul class="fusion-tabs-menu">
				<?php if ( current_theme_supports( 'fusion-builder-demos' ) ) : ?>
					<li><a href="#fusion-builder-layouts-demos" id="fusion-builder-layouts-demos-trigger"><?php esc_attr_e( 'Demos', 'fusion-builder' ); ?></a></li>
				<?php endif; ?>
				<li><a href="#fusion-builder-layouts-templates" id="fusion-builder-layouts-templates-trigger"><?php esc_attr_e( 'Templates', 'fusion-builder' ); ?></a></li>
				<li><a href="#fusion-builder-layouts-sections" id="fusion-builder-layouts-sections-trigger"><?php esc_attr_e( 'Containers', 'fusion-builder' ); ?></a></li>
				<li><a href="#fusion-builder-layouts-columns" id="fusion-builder-layouts-columns-trigger"><?php esc_attr_e( 'Columns', 'fusion-builder' ); ?></a></li>
				<li><a href="#fusion-builder-layouts-elements" id="fusion-builder-layouts-elements-trigger"><?php esc_attr_e( 'Elements', 'fusion-builder' ); ?></a></li>
			</ul>
		</div>

		<div class="fusion-layout-tabs">
			<?php if ( current_theme_supports( 'fusion-builder-demos' ) ) : // Display demos tab. ?>
				<div id="fusion-builder-layouts-demos" class="fusion-builder-layouts-tab">
					<div class="fusion-builder-layouts-header">
						<?php $fusion_builder_demos = apply_filters( 'fusion_builder_get_demo_pages', array() ); ?>

						<div class="fusion-builder-layouts-header-fields">
							<?php if ( $fusion_builder_demos ) : ?>
								<?php asort( $fusion_builder_demos ); ?>
								<select class="fusion-builder-demo-select">
									<?php foreach ( $fusion_builder_demos as $key => $fusion_builder_demo ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $fusion_builder_demo['category'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
						</div>

						<div class="fusion-builder-layouts-header-info">
							<h2><?php echo apply_filters( 'fusion_builder_import_title', esc_html__( 'Select a demo to view the pages you can import', 'fusion-builder' ) ); // WPCS: XSS ok. ?></h2>
							<span class="fusion-builder-layout-info">
								<?php echo apply_filters( 'fusion_builder_import_message', esc_attr__( 'Select a demo and the pages that are available to import will display.', 'fusion-builder' ) ); // WPCS: XSS ok. ?>
							</span>
						</div>
					</div>

					<?php foreach ( $fusion_builder_demos as $key => $fusion_builder_demo ) : ?>

						<ul class="fusion-page-layouts demo-<?php echo esc_attr( $key ); ?>">

							<?php if ( isset( $fusion_builder_demo['pages'] ) && ! empty( $fusion_builder_demo['pages'] ) ) : ?>
								<?php asort( $fusion_builder_demo['pages'] ); ?>
								<?php foreach ( $fusion_builder_demo['pages'] as $page_key => $page ) : ?>
									<li class="fusion-page-layout" data-layout_id="<?php echo esc_attr( $page['name'] ); ?>">
										<h4 class="fusion-page-layout-title"><?php echo esc_html( ucwords( strtolower( $page['name'] ) ) ); ?></h4>
										<span class="fusion-layout-buttons">
											<a href="#" class="fusion-builder-demo-button-load" data-page-name="<?php echo esc_attr( $page_key ); ?>" data-demo-name="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">
												<?php esc_html_e( 'Load', 'fusion-builder' ); ?>
											</a>
										</span>
									</li>
								<?php endforeach; ?>
							<?php else : ?>
								<li><p><?php esc_html_e( 'There are no demos in your library', 'fusion-builder' ); ?></p></li>
							<?php endif; ?>

						</ul>

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

			<?php
			// Display containers tab.
			?>

			<div id="fusion-builder-layouts-sections" class="fusion-builder-layouts-tab">

				<div class="fusion-builder-layouts-header">
					<div class="fusion-builder-layouts-header-fields fusion-builder-layouts-header-element-fields"></div>
					<div class="fusion-builder-layouts-header-info">
						<h2><?php esc_attr_e( 'Saved Containers', 'fusion-builder' ); ?></h2>
						<span class="fusion-builder-layout-info"><?php esc_attr_e( 'Manage your saved containers. Containers cannot be inserted from the library window.', 'fusion-builder' ); ?></span>
					</div>
				</div>

				<?php
				// Query containers.
				$query = fusion_cached_query( array(
					'status'         => 'publish',
					'post_type'      => 'fusion_element',
					'posts_per_page' => '-1',
					'tax_query'      => array(
						array(
							'taxonomy' => 'element_category',
							'field'    => 'slug',
							'terms'    => 'sections',
						),
					),
				) );
				?>

				<?php if ( $query->have_posts() ) : ?>

					<ul class="fusion-page-layouts">

						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php global $post; ?>

							<li class="fusion-page-layout" data-layout_id="<?php echo get_the_ID(); ?>">
								<h4 class="fusion-page-layout-title"><?php echo get_the_title(); ?></h4>
								<span class="fusion-layout-buttons">
									<a href="#" class="fusion-builder-layout-button-delete"><?php esc_attr_e( 'Delete', 'fusion-builder' ); ?></a>
								</span>
							</li>
						<?php endwhile; ?>

					</ul>

				<?php else : ?>
					<ul class="fusion-page-layouts">
						<p class="fusion-empty-library-message"><?php esc_attr_e( 'There are no custom containers in your library', 'fusion-builder' ); ?></p>
					</ul>

				<?php endif; ?>

				<?php
				$post = $saved_post ? $saved_post : $post;
				wp_reset_postdata();
				?>

			</div>

			<?php
			// Display columns tab.
			?>

			<div id="fusion-builder-layouts-columns" class="fusion-builder-layouts-tab">

				<div class="fusion-builder-layouts-header">
					<div class="fusion-builder-layouts-header-fields fusion-builder-layouts-header-element-fields"></div>
					<div class="fusion-builder-layouts-header-info">
						<h2><?php esc_attr_e( 'Saved Columns', 'fusion-builder' ); ?></h2>
						<span class="fusion-builder-layout-info"><?php esc_attr_e( 'Manage your saved columns. Columns cannot be inserted from the library window and they must always go inside a container.', 'fusion-builder' ); ?></span>
					</div>
				</div>

				<?php
				// Query columns.
				$query = fusion_cached_query( array(
					'status'         => 'publish',
					'post_type'      => 'fusion_element',
					'posts_per_page' => '-1',
					'tax_query'      => array(
						array(
							'taxonomy' => 'element_category',
							'field'    => 'slug',
							'terms'    => 'columns',
						),
					),
				) );
				?>

				<?php if ( $query->have_posts() ) : ?>

					<ul class="fusion-page-layouts">

						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php global $post; ?>

							<li class="fusion-page-layout" data-layout_id="<?php echo get_the_ID(); ?>">
								<h4 class="fusion-page-layout-title"><?php echo get_the_title(); ?></h4>
								<span class="fusion-layout-buttons">
									<a href="#" class="fusion-builder-layout-button-delete"><?php esc_attr_e( 'Delete', 'fusion-builder' ); ?></a>
								</span>
							</li>
						<?php endwhile; ?>

					</ul>

				<?php else : ?>
					<ul class="fusion-page-layouts">
						<p class="fusion-empty-library-message"><?php esc_attr_e( 'There are no custom columns in your library', 'fusion-builder' ); ?></p>
					</ul>

				<?php endif; ?>

				<?php
				$post = $saved_post ? $saved_post : $post;
				wp_reset_postdata();
				?>

			</div>

			<?php
			// Display elements tab.
			?>

			<div id="fusion-builder-layouts-elements" class="fusion-builder-layouts-tab">

				<div class="fusion-builder-layouts-header">
					<div class="fusion-builder-layouts-header-fields fusion-builder-layouts-header-element-fields"></div>
					<div class="fusion-builder-layouts-header-info">
						<h2><?php esc_attr_e( 'Saved Elements', 'fusion-builder' ); ?></h2>
						<span class="fusion-builder-layout-info"><?php esc_attr_e( 'Manage your saved elements. Elements cannot be inserted from the library window and they must always go inside a column.', 'fusion-builder' ); ?></span>
					</div>
				</div>

				<?php
				// Query elements.
				$query = fusion_cached_query( array(
					'status'         => 'publish',
					'post_type'      => 'fusion_element',
					'posts_per_page' => '-1',
					'tax_query'      => array(
						array(
							'taxonomy' => 'element_category',
							'field'    => 'slug',
							'terms'    => 'elements',
						),
					),
				) );
				?>

				<?php if ( $query->have_posts() ) : ?>

					<ul class="fusion-page-layouts">

						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php global $post; ?>
							<?php $element_type = esc_attr( get_post_meta( $post->ID, '_fusion_element_type', true ) ); ?>

							<li class="fusion-page-layout" data-layout_type="<?php echo esc_attr( $element_type ); ?>" data-layout_id="<?php echo esc_attr( get_the_ID() ); ?>">
								<h4 class="fusion-page-layout-title"><?php echo get_the_title(); ?></h4>
								<span class="fusion-layout-buttons">
									<a href="#" class="fusion-builder-layout-button-delete"><?php esc_attr_e( 'Delete', 'fusion-builder' ); ?></a>
								</span>
							</li>
						<?php endwhile; ?>

					</ul>

				<?php else : ?>
					<ul class="fusion-page-layouts">
						<p class="fusion-empty-library-message"><?php esc_attr_e( 'There are no custom elements in your library', 'fusion-builder' ); ?></p>
					</ul>

				<?php endif; ?>

				<?php
				$post = $saved_post ? $saved_post : $post;
				wp_reset_postdata();
				?>

			</div>

			<?php
			// Display templates tab.
			?>
			<div id="fusion-builder-layouts-templates" class="fusion-builder-layouts-tab">
				<div class="fusion-builder-layouts-header">

					<div class="fusion-builder-layouts-header-fields">
						<a href="#" class="fusion-builder-layout-button-save"><?php esc_attr_e( 'Save Template', 'fusion-builder' ); ?></a>
						<input type="text" id="new_template_name" value="" placeholder="<?php esc_attr_e( 'Custom template name', 'fusion-builder' ); ?>" />
					</div>

					<div class="fusion-builder-layouts-header-info">
						<h2><?php esc_attr_e( 'Save current page layout as a template', 'fusion-builder' ); ?></h2>
						<span class="fusion-builder-layout-info"><?php esc_attr_e( 'Enter a name for your template and click the Save button. This will save the entire page layout, page template from the page attributes box, custom css and Fusion Page Options. IMPORTANT: when loading a saved template, everything will load except for Fusion Page Options. The only time Fusion Page Options will load is if you choose to "Replace All Content".', 'fusion-builder' ); ?></span>
					</div>

				</div>

				<?php
				// Query page templates.
				$query = fusion_cached_query( array(
					'status'         => 'publish',
					'post_type'      => 'fusion_template',
					'posts_per_page' => '-1',
				) );
				?>

				<ul class="fusion-page-layouts">

					<?php if ( $query->have_posts() ) : ?>

						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php global $post; ?>
							<li class="fusion-page-layout" data-layout_id="<?php echo get_the_ID(); ?>">
								<h4 class="fusion-page-layout-title"><?php echo get_the_title(); ?></h4>
								<span class="fusion-layout-buttons">
									<a href="javascript:void(0)" class="fusion-builder-layout-button-load-dialog">
										<?php printf(
											esc_html__( 'Load %s', 'fusion-builder' ),
											'<div class="fusion-builder-load-template-dialog-container"><div class="fusion-builder-load-template-dialog"><span class="fusion-builder-save-element-title">' . esc_html__( 'How To Load Template?', 'fusion-builder' ) . '</span><div class="fusion-builder-save-element-container"><span class="fusion-builder-layout-button-load" data-load-type="replace">' . esc_attr__( 'Replace all page content', 'fusion-builder' ) . '</span><span class="fusion-builder-layout-button-load" data-load-type="above">' . esc_attr__( 'Insert above current content', 'fusion-builder' ) . '</span><span class="fusion-builder-layout-button-load" data-load-type="below">' . esc_attr__( 'Insert below current content', 'fusion-builder' ) . '</span></div></div></div>'
										); ?>
									</a>
									<a href="<?php echo esc_url_raw( htmlspecialchars_decode( get_edit_post_link( $post->ID ) ) ); ?>" class="" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Edit', 'fusion-builder' ); ?></a>
									<a href="#" class="fusion-builder-layout-button-delete"><?php esc_html_e( 'Delete', 'fusion-builder' ); ?></a>
								</span>
							</li>
						<?php endwhile; ?>

					<?php else : ?>

						<p class="fusion-empty-library-message"><?php esc_attr_e( 'There are no custom templates in your library', 'fusion-builder' ); ?></p>

					<?php endif; ?>

					<?php wp_reset_postdata(); ?>

				</ul>

			</div>

		</div>

	</div>

	<?php
	if ( $saved_post ) {
		$post = $saved_post;
	}
}


/**
 * Load custom elements.
 */
function fusion_load_custom_elements() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( isset( $_POST['cat'] ) && '' !== $_POST['cat'] ) {

		$cat = $_POST['cat'];

		// Query elements.
		$query = fusion_cached_query( array(
			'status'         => 'publish',
			'post_type'      => 'fusion_element',
			'posts_per_page' => '-1',
			'tax_query'      => array(
				array(
					'taxonomy' => 'element_category',
					'field'    => 'slug',
					'terms'    => $cat,
				),
			),
		));

		if ( $query->have_posts() ) : ?>
			<ul class="fusion-builder-all-modules">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php global $post; ?>
					<?php $element_type = esc_attr( get_post_meta( $post->ID, '_fusion_element_type', true ) ); ?>
					<?php $element_type_class = ( isset( $element_type ) && '' != $element_type ) ? 'fusion-element-type-' . $element_type : ''; ?>

					<li class="fusion-page-layout fusion_builder_custom_<?php echo esc_attr( $cat ); ?>_load <?php echo esc_attr( $element_type_class ); ?>" data-layout_id="<?php echo get_the_ID(); ?>">
						<h4 class="fusion_module_title"><?php echo get_the_title(); ?></h4>
					</li>

				<?php endwhile; ?>

			</ul>

		<?php else : ?>

			<p class="fusion-empty-library-message"><?php esc_attr_e( 'There are no custom elements in your library', 'fusion-builder' ); ?></p>

		<?php endif; ?>
		<?php wp_reset_postdata();
	}

	die();
}
add_action( 'wp_ajax_fusion_load_custom_elements', 'fusion_load_custom_elements' );


/**
 * Load custom page layout.
 */
function fusion_builder_load_layout() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( ! isset( $_POST['fusion_layout_id'] ) && '' === $_POST['fusion_layout_id'] ) {
		die( -1 );
	}

	$data      = array();
	$layout_id = (int) $_POST['fusion_layout_id'];
	$layout    = get_post( $layout_id );

	if ( $layout ) {

		// Set page content.
		$data['post_content'] = $layout->post_content;

		// Set page template.
		if ( 'fusion_template' == get_post_type( $layout_id ) ) {

			$page_template = get_post_meta( $layout_id, '_wp_page_template', true );

			if ( isset( $page_template ) && ! empty( $page_template ) ) {
				$data['page_template'] = $page_template;
			}

			$custom_css = get_post_meta( $layout_id, '_fusion_builder_custom_css', true );

			$data['post_meta'] = get_post_meta( $layout_id );

			if ( isset( $custom_css ) && ! empty( $custom_css ) ) {
				$data['custom_css'] = $custom_css;
			}
		}
	}

	$json_data = wp_json_encode( $data );

	die( $json_data ); // XPCS: XSS ok.

}
add_action( 'wp_ajax_fusion_builder_load_layout', 'fusion_builder_load_layout' );


/**
 * Load custom page layout.
 */
function fusion_builder_load_demo() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( ! isset( $_POST['page_name'] ) && '' === $_POST['page_name'] ) {
		die( -1 );
	}

	if ( ! isset( $_POST['demo_name'] ) && '' === $_POST['demo_name'] ) {
		die( -1 );
	}

	if ( ! isset( $_POST['post_id'] ) && '' === $_POST['post_id'] ) {
		die( -1 );
	}

	$data      = array();
	$page_name = $_POST['page_name'];
	$demo_name = $_POST['demo_name'];
	$post_id   = (int) $_POST['post_id'];

	$fusion_builder_demos = apply_filters( 'fusion_builder_get_demo_pages', array() );

	if ( isset( $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ] ) && ! empty( $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ] ) ) {

		// Set page content.
		$data['post_content'] = $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ]['content'];

		// Set page template.
		$page_template = $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ]['page_template'];

		if ( isset( $page_template ) && ! empty( $page_template ) ) {
			$data['page_template'] = $page_template;
		}
	}

	if ( isset( $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ]['meta'] ) && ! empty( $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ]['meta'] ) ) {

		$data['meta'] = $fusion_builder_demos[ $demo_name ]['pages'][ $page_name ]['meta'];
	}

	$json_data = wp_json_encode( $data );

	die( $json_data ); // XPCS: XSS ok.

}
add_action( 'wp_ajax_fusion_builder_load_demo', 'fusion_builder_load_demo' );

/**
 * Save custom layout.
 */
function fusion_builder_save_layout() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( isset( $_POST['fusion_layout_name'] ) && '' !== $_POST['fusion_layout_name'] ) {

		$layout_name = $_POST['fusion_layout_name'];
		$taxonomy    = 'element_category';
		$term        = '';
		$meta        = '';
		$layout_type = '';

		if ( isset( $_POST['fusion_layout_post_type'] ) && '' !== $_POST['fusion_layout_post_type'] ) {

			$post_type = $_POST['fusion_layout_post_type'];

			if ( isset( $_POST['fusion_current_post_id'] ) && '' !== $_POST['fusion_current_post_id'] ) {
				$post_id = $_POST['fusion_current_post_id'];
			}

			if ( isset( $_POST['fusion_layout_element_type'] ) && '' !== $_POST['fusion_layout_element_type'] ) {
				$meta['_fusion_element_type'] = $_POST['fusion_layout_element_type'];
				$layout_type = ' fusion-element-type-' . $_POST['fusion_layout_element_type'];
			}

			if ( 'fusion_template' == $post_type ) {
				$meta['fusion_builder_status'] = 'active';

				// Save custom css.
				if ( isset( $_POST['fusion_custom_css'] ) && '' !== $_POST['fusion_custom_css'] ) {
					$meta['_fusion_builder_custom_css'] = $_POST['fusion_custom_css'];
				}

				// Save page template.
				if ( isset( $_POST['fusion_page_template'] ) && '' !== $_POST['fusion_page_template'] ) {
					$meta['_wp_page_template'] = $_POST['fusion_page_template'];
				}
			}

			// Add Fusion Options to meta data.
			if ( isset( $_POST['fusion_options'] ) && '' !== $_POST['fusion_options'] && is_array( $_POST['fusion_options'] ) ) {
				foreach ( $_POST['fusion_options'] as $option ) {
					$meta[ $option[0] ] = $option[1];
				}
			}
			// Post category.
			if ( isset( $_POST['fusion_layout_new_cat'] ) && '' !== $_POST['fusion_layout_new_cat'] ) {
				$term = $_POST['fusion_layout_new_cat'];
			}

			$new_layout_id = fusion_builder_create_layout( $post_type, $layout_name, $_POST['fusion_layout_content'], $meta, $taxonomy, $term );
			?>

			<?php if ( 'fusion_element' == $post_type ) : ?>

				<li class="fusion-page-layout<?php echo esc_attr( $layout_type ); ?>" data-layout_id="<?php echo esc_attr( $new_layout_id ); ?>">
					<h4 class="fusion-page-layout-title"><?php echo get_the_title( $new_layout_id ); ?></h4>
					<span class="fusion-layout-buttons">
						<a href="#" class="fusion-builder-layout-button-delete"><?php esc_html_e( 'Delete', 'fusion-builder' ); ?></a>
					</span>
				</li>

			<?php elseif ( 'fusion_template' == $post_type ) : ?>

				<li class="fusion-page-layout" data-layout_id="<?php echo esc_attr( $new_layout_id ); ?>">
					<h4 class="fusion-page-layout-title"><?php echo get_the_title( $new_layout_id ); // WPCS: XSS ok. ?></h4>
					<span class="fusion-layout-buttons">
						<a href="javascript:void(0)" class="fusion-builder-layout-button-load-dialog">
							<?php printf(
								esc_html__( 'Load %s', 'fusion-builder' ),
								'<div class="fusion-builder-load-template-dialog-container"><div class="fusion-builder-load-template-dialog"><span class="fusion-builder-save-element-title">' . esc_attr__( 'How To Load Template?', 'fusion-builder' ) . '</span><div class="fusion-builder-save-element-container"><span class="fusion-builder-layout-button-load" data-load-type="replace">' . esc_attr__( 'Replace all page content', 'fusion-builder' ) . '</span><span class="fusion-builder-layout-button-load" data-load-type="above">' . esc_attr__( 'Insert above current content', 'fusion-builder' ) . '</span><span class="fusion-builder-layout-button-load" data-load-type="below">' . esc_attr__( 'Insert below current content', 'fusion-builder' ) . '</span></div></div></div>'
							); ?>
						</a>
						<a href="<?php echo esc_url_raw( htmlspecialchars_decode( get_edit_post_link( $new_layout_id ) ) ); ?>" class="" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Edit', 'fusion-builder' ); ?></a>
						<a href="#" class="fusion-builder-layout-button-delete"><?php esc_html_e( 'Delete', 'fusion-builder' ); ?></a>
					</span>
				</li>
			<?php endif;
		}
	}

	die();
}
add_action( 'wp_ajax_fusion_builder_save_layout', 'fusion_builder_save_layout' );

/**
 * Get image URL from image ID.
 */
function fusion_builder_get_image_url() {

	check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

	if ( ! isset( $_POST['fusion_image_ids'] ) && '' === $_POST['fusion_image_ids'] ) {
		die( -1 );
	}

	$data      = array();
	$image_ids = $_POST['fusion_image_ids'];
	foreach ( $image_ids as $image_id ) {
		if ( '' !== $image_id ) {
			$image_url = wp_get_attachment_url( $image_id, 'thumbnail' );
			$image_html = '<div class="fusion-multi-image" data-image-id="' . $image_id . '">';
			$image_html .= '<img src="' . $image_url . '"/>';
			$image_html .= '<span class="fusion-multi-image-remove dashicons dashicons-no-alt"></span>';
			$image_html .= '</div>';
			$data['images'][] = $image_html;
		}
	}
	$json_data = wp_json_encode( $data );

	die( $json_data ); // WPCS: XSS ok.

}
add_action( 'wp_ajax_fusion_builder_get_image_url', 'fusion_builder_get_image_url' );
