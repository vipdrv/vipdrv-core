<?php

if ( fusion_is_element_enabled( 'fusion_postslider' ) ) {

	if ( ! class_exists( 'FusionSC_Flexslider' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Flexslider extends Fusion_Element {

			/**
			 * The flex counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $flex_counter = 1;

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_flexslider-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_flexslider-shortcode-slides-container', array( $this, 'slides_container_attr' ) );
				add_filter( 'fusion_attr_flexslider-shortcode-caption', array( $this, 'caption_attr' ) );
				add_filter( 'fusion_attr_flexslider-shortcode-title-container', array( $this, 'title_container_attr' ) );
				add_filter( 'fusion_attr_flexslider-shortcode-thumbnails', array( $this, 'thumbnails_attr' ) );

				add_shortcode( 'fusion_flexslider', array( $this, 'render' ) );
				add_shortcode( 'fusion_postslider', array( $this, 'render' ) );
			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'category'       => '',
						'excerpt'        => '35',
						// 'group'          => '', // Not yet used.
						'layout'         => 'attachments',
						'lightbox'       => 'yes',
						'limit'          => '3',
						'post_id'        => '',
					), $args
				);

				extract( $defaults );

				$this->args = $defaults;

				$thumbnails = '';

				$slider = '';
				if ( 'attachments' == $layout ) {
					$slider = $this->attachments();
					$thumbnails = $this->get_attachments_thumbnails();
				} elseif ( 'posts' == $layout ) {
					$slider = $this->posts();
				} elseif ( 'posts-with-excerpt' == $layout ) {
					$slider = $this->posts_excerpt();
				}

				$slides_html = '<ul ' . FusionBuilder::attributes( 'flexslider-shortcode-slides-container' ) . '>' . $slider . '</ul>';

				$html = '<div ' . FusionBuilder::attributes( 'flexslider-shortcode' ) . '>' . $slides_html . '</div>';

				if ( 'attachments' == $layout ) {
					$thumbnails_html = '';
					$html .= '<div ' . FusionBuilder::attributes( 'flexslider-shortcode-thumbnails' ) . '>' . $thumbnails_html . '</div>';
				}

				$this->flex_counter++;

				return $html;

			}

			/**
			 * The attachements HTML.
			 *
			 * @access public
			 * @since 1.0
			 * @return string The HTML.
			 */
			public function attachments() {

				$html = '';

				if ( ! $this->args['post_id'] ) {
					$this->args['post_id'] = get_the_ID();
				}

				$query = fusion_cached_get_posts( array(
					'post_type'      => 'attachment',
					'posts_per_page' => $this->args['limit'],
					'post_status'    => 'any',
					'post_parent'    => $this->args['post_id'],
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'post_mime_type' => 'image',
					'exclude'        => get_post_thumbnail_id(),
				) );

				if ( $query ) :

					foreach ( $query as $attachment ) :

						$image   = wp_get_attachment_url( $attachment->ID );
						$title   = get_post_field( 'post_title', $attachment->ID );
						$alt     = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
						$thumb   = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
						$caption = get_post_field( 'post_excerpt', $attachment->ID );

						$image_output = '<img src="' . $image . '" alt="' . $alt . '" />';
						$output = $image_output;

						if ( 'yes' == $this->args['lightbox'] ) {
							$output = '<a href="' . $image . '" data-title="' . $title . '" data-caption="' . $caption . '" title="' . $title . '" data-rel="prettyPhoto[flex_' . $this->flex_counter . ']">' . $image_output . '</a>';
						}

						$html .= '<li data-thumb="' . $thumb[0] . '">' . $output . '</li>';

					endforeach;

				endif;

				wp_reset_query();

				return $html;

			}

			/**
			 * Gets the attachment thumbnails HTML.
			 *
			 * @access public
			 * @since 1.0
			 * @return string HTML.
			 */
			public function get_attachments_thumbnails() {

				$html = '';

				if ( ! $this->args['post_id'] ) {
					$this->args['post_id'] = get_the_ID();
				}

				$query = fusion_cached_get_posts( array(
					'post_type'      => 'attachment',
					'posts_per_page' => $this->args['limit'],
					'post_status'    => 'any',
					'post_parent'    => $this->args['post_id'],
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'post_mime_type' => 'image',
					'exclude'        => get_post_thumbnail_id(),
				) );

				if ( $query ) :

					foreach ( $query as $attachment ) :

						$image = wp_get_attachment_url( $attachment->ID );
						$title = get_post_field( 'post_excerpt', $attachment->ID );
						$alt   = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
						$thumb = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );

						$image_output = '<img src="' . $thumb[0] . '" alt="' . $alt . '" />';
						$output = $image_output;

						$html .= '<li>' . $output . '</li>';

					endforeach;

				endif;

				wp_reset_query();

				return $html;

			}

			/**
			 * Get the posts HTML.
			 *
			 * @access public
			 * @since 1.0
			 * @return string HTML.
			 */
			public function posts() {

				$html = '';

				$args = array(
					'posts_per_page' => $this->args['limit'],
					'meta_query'     => array(
						array(
							'key' => '_thumbnail_id',
						),
					),
				);

				if ( $this->args['post_id'] ) {
					$post_ids = explode( ',', $this->args['post_id'] );
					$args['post__in'] = $post_ids;
				}

				if ( $this->args['category'] ) {
					$args['category_name'] = $this->args['category'];
				}

				$query = fusion_cached_query( $args );

				if ( $query->have_posts() ) :

					while ( $query->have_posts() ) :  $query->the_post();

						$image = wp_get_attachment_url( get_post_thumbnail_id() );
						$title = get_post_field( 'post_excerpt', get_post_thumbnail_id() );
						$alt   = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );

						$image_output = '<img src="' . $image . '" alt="' . $alt . '" />';
						$link_output  = '<a href="' . get_permalink() . '" aria-label="' . get_the_title() . '">' . $image_output . '</a>';
						$title        = '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
						$container    = '<div ' . FusionBuilder::attributes( 'flexslider-shortcode-title-container' ) . '>' . $title . '</div>';

						$html .= '<li>' . $link_output . $container . '</li>';

					endwhile;

				endif;

				wp_reset_query();

				return $html;

			}

			/**
			 * Get the post excerpts HTML.
			 *
			 * @access public
			 * @since 1.0
			 * @return string HTML.
			 */
			public function posts_excerpt() {

				$html = '';

				$args = array(
					'posts_per_page' => $this->args['limit'],
					'meta_query'     => array(
						array(
							'key' => '_thumbnail_id',
						),
					),
				);

				if ( $this->args['post_id'] ) {
					$post_ids = explode( ',', $this->args['post_id'] );
					$args['post__in'] = $post_ids;
				}

				if ( $this->args['category'] ) {
					$args['category_name'] = $this->args['category'];
				}

				$query = fusion_cached_query( $args );

				if ( $query->have_posts() ) :

					while ( $query->have_posts() ) :  $query->the_post();

						$image = wp_get_attachment_url( get_post_thumbnail_id() );
						$title = get_post_field( 'post_excerpt', get_post_thumbnail_id() );
						$alt   = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );

						$image_output = '<img src="' . $image . '" alt="' . $alt . '" />';
						$link_output  = '<a href="' . get_permalink() . '" aria-label="' . get_the_title() . '">' . $image_output . '</a>';
						$title        = '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
						$excerpt      = fusion_builder_get_post_content( '', 'yes', $this->args['excerpt'], true );
						$container    = '<div ' . FusionBuilder::attributes( 'flexslider-shortcode-title-container' ) . '><div ' . FusionBuilder::attributes( 'excerpt-container' ) . '>' . $title . $excerpt . '</div></div>';

						$html .= '<li>' . $link_output . $container . '</li>';

					endwhile;

				endif;

				wp_reset_query();

				return $html;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr['class'] = 'fusion-flexslider fusion-flexslider-loading flexslider-' . $this->args['layout'];

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( 'yes' == $this->args['lightbox'] && 'attachments' == $this->args['layout'] ) {
					$attr['class'] .= ' flexslider-lightbox';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Builds the slider-container attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slides_container_attr() {
				return array(
					'class' => 'slides',
				);
			}

			/**
			 * Builds the caption attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function caption_attr() {
				return array(
					'class' => 'flex-caption',
				);
			}

			/**
			 * Builds the title-container attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function title_container_attr() {
				return array(
					'class' => 'slide-excerpt',
				);
			}

			/**
			 * Builds the thumbnails attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function thumbnails_attr() {

				$attr = array(
					'class' => 'flexslider',
				);
				if ( 'attachments' == $this->args['layout'] ) {
					$attr['class'] .= ' fat';
				}
				return $attr;

			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = array(
					'.fusion-flexslider .flex-direction-nav .flex-prev',
					'.fusion-flexslider .flex-direction-nav .flex-next',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'carousel_nav_color' ) );

				$elements = $dynamic_css_helpers->map_selector( $elements, ':hover' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'carousel_hover_color' ) );

				$elements = array(
					'.fusion-flexslider .flex-direction-nav a',
					'.fusion-flexslider.flexslider-posts .flex-direction-nav a',
					'.fusion-flexslider.flexslider-posts-with-excerpt .flex-direction-nav a',
					'.fusion-flexslider.flexslider-attachments .flex-direction-nav a',
					'.fusion-slider-sc .flex-direction-nav a',
				);

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'width' ) );

				preg_match_all( '!\d+!', $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ), $matches );
				$half_slider_nav_box_height = '' !== $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ? $matches[0][0] / 2 . $fusion_library->sanitize->get_unit( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ) : '';

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );

				return $css;
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script( 'fusion-flexslider' );
			}
		}
	}

	new FusionSC_Flexslider();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_post_slider() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Post Slider', 'fusion-builder' ),
		'shortcode'  => 'fusion_postslider',
		'icon'       => 'fusiona-layers-alt',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-post-slider-preview.php',
		'preview_id' => 'fusion-builder-block-module-post-slider-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a layout style for Post Slider.', 'fusion-builder' ),
				'param_name'  => 'layout',
				'value'       => array(
					'posts'              => esc_attr__( 'Posts with Title', 'fusion-builder' ),
					'posts-with-excerpt' => esc_attr__( 'Posts with Title and Excerpt', 'fusion-builder' ),
					'attachments'        => esc_attr__( 'Attachment Layout, Only Images Attached to Post/Page', 'fusion-builder' ),
				),
				'default'     => 'attachments',
			),
			array(
				'type'             => 'uploadattachment',
				'heading'          => esc_attr__( 'Attach Images to Post/Page Gallery', 'fusion-builder' ),
				'description'      => esc_attr__( 'To add images to this post or page for attachments layout, navigate to "Upload Files" tab in media manager and upload new images.', 'fusion-builder' ),
				'param_name'       => 'upload_attachments',
				'value'            => '',
				'remove_from_atts' => true,
				'dependency'       => array(
					array(
						'element'  => 'layout',
						'value'    => 'attachments',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Excerpt Number of Words', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the number of words you want to show in the excerpt.', 'fusion-builder' ),
				'param_name'  => 'excerpt',
				'value'       => '35',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'posts-with-excerpt',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Category', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a category of posts to display.', 'fusion-builder' ),
				'param_name'  => 'category',
				'value'       => fusion_builder_shortcodes_categories( 'category', true, esc_attr__( 'All', 'fusion-builder' ) ),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'attachments',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Number of Slides', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the number of slides to display.', 'fusion-builder' ),
				'param_name'  => 'limit',
				'value'       => '3',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Lightbox on Click', 'fusion-builder' ),
				'description' => esc_attr__( 'Only works on attachment layout.', 'fusion-builder' ),
				'param_name'  => 'lightbox',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'attachments',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_post_slider' );
