<?php
/**
 * Fusion-Builder Shortcode Element.
 *
 * @package Fusion-Core
 * @since 3.1.0
 */

if ( function_exists( 'fusion_is_element_enabled' ) && fusion_is_element_enabled( 'fusion_faq' ) ) {

	if ( ! class_exists( 'FusionSC_Faq' ) && class_exists( 'Fusion_Element' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-core
		 * @since 1.0
		 */
		class FusionSC_Faq extends Fusion_Element {

			/**
			 * FAQ counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $faq_counter = 1;

			/**
			 * An array of the shortcode arguments.
			 *
			 * @static
			 * @access public
			 * @since 1.0
			 * @var array
			 */
			public static $args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_shortcode( 'fusion_faq', array( $this, 'render' ) );
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

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'cats_slug'      => '',
						'exclude_cats'   => '',
						'featured_image' => $fusion_settings->get( 'faq_featured_image' ),
						'filters'        => $fusion_settings->get( 'faq_filters' ),
					), $args
				);

				$defaults['cat_slugs'] = $defaults['cats_slug'];

				// @codingStandardsIgnoreLine
				extract( $defaults );

				self::$args = $defaults;

				// Transform $cat_slugs to array.
				if ( $cat_slugs ) {
					$cat_slugs = preg_replace( '/\s+/', '', $cat_slugs );
					$cat_slugs = explode( ',', $cat_slugs );
				} else {
					$cat_slugs = array();
				}

				// Transform $cats_to_exclude to array.
				if ( $exclude_cats ) {
					$cats_to_exclude = preg_replace( '/\s+/', '', $exclude_cats );
					$cats_to_exclude = explode( ',' , $cats_to_exclude );
				} else {
					$cats_to_exclude = array();
				}

				// Initialize the query array.
				$args = array(
					'post_type'      => 'avada_faq',
					'posts_per_page' => -1,
					'has_password'   => false,
				);

				// Check if the are categories that should be excluded.
				if ( ! empty( $cats_to_exclude ) ) {

					// Exclude the correct cats from tax_query.
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'faq_category',
							'field'    => 'slug',
							'terms'    => $cats_to_exclude,
							'operator' => 'NOT IN',
						),
					);

					// Include the correct cats in tax_query.
					if ( ! empty( $cat_slugs ) ) {
						$args['tax_query']['relation'] = 'AND';
						$args['tax_query'][]           = array(
							'taxonomy' => 'faq_category',
							'field'    => 'slug',
							'terms'    => $cat_slugs,
							'operator' => 'IN',
						);
					}
				} else {
					// Include the cats from $cat_slugs in tax_query.
					if ( ! empty( $cat_slugs ) ) {
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'faq_category',
								'field'    => 'slug',
								'terms'    => $cat_slugs,
							),
						);
					}
				}

				$class = fusion_builder_visibility_atts( $hide_on_mobile, $class );

				$html = '<div class="fusion-faq-shortcode ' . $class . '">';

				// Setup the filters.
				$faq_terms = get_terms( 'faq_category' );

				// Check if we should display filters.
				if ( $faq_terms && 'no' !== $filters ) {

					$html .= '<ul class="fusion-filters clearfix">';

					// Check if the "All" filter should be displayed.
					$first_filter = true;
					if ( 'yes' === $filters ) {
						$html .= '<li class="fusion-filter fusion-filter-all fusion-active">';
						$html .= '<a data-filter="*" href="#">' . apply_filters( 'fusion_faq_all_filter_name', esc_html__( 'All', 'fusion-core' ) ) . '</a>';
						$html .= '</li>';
						$first_filter = false;
					}

					// Loop through the terms to setup all filters.
					foreach ( $faq_terms as $faq_term ) {
						// Only display filters of non excluded categories.
						if ( ! in_array( $faq_term->slug, $cats_to_exclude, true ) ) {
							// Check if current term is part of chosen terms, or if no terms at all have been chosen.
							if ( ( ! empty( $cat_slugs ) && in_array( $faq_term->slug, $cat_slugs, true ) ) || empty( $cat_slugs ) ) {
								// If the "All" filter is disabled, set the first real filter as active.
								if ( $first_filter ) {
									$html .= '<li class="fusion-filter fusion-active">';
									$html .= '<a data-filter=".' . urldecode( $faq_term->slug ) . '" href="#">' . $faq_term->name . '</a>';
									$html .= '</li>';
									$first_filter = false;
								} else {
									$html .= '<li class="fusion-filter fusion-hidden">';
									$html .= '<a data-filter=".' . urldecode( $faq_term->slug ) . '" href="#">' . $faq_term->name . '</a>';
									$html .= '</li>';
								}
							}
						}
					}

					$html .= '</ul>';
				} // End if().

				// Setup the posts.
				$faq_items = fusion_cached_query( $args );

				if ( ! $faq_items->have_posts() ) {
					return fusion_builder_placeholder( 'avada_faq', 'FAQ posts' );
				}

				$html .= '<div class="fusion-faqs-wrapper">';
				$html .= '<div class="accordian fusion-accordian">';
				$html .= '<div class="panel-group" id="accordian-' . $this->faq_counter . '">';

				$this_post_id = get_the_ID();

				while ( $faq_items->have_posts() ) :  $faq_items->the_post();

					// If used on a faq item itself, thzis is needed to prevent an infinite loop.
					if ( get_the_ID() === $this_post_id ) {
						continue;
					}

					// Get all terms of the post and it as classes; needed for filtering.
					$post_classes = '';
					$post_id = get_the_ID();
					$post_terms = get_the_terms( $post_id, 'faq_category' );
					if ( $post_terms ) {
						foreach ( $post_terms as $post_term ) {
							$post_classes .= urldecode( $post_term->slug ) . ' ';
						}
					}

					$html .= '<div class="fusion-panel panel-default fusion-faq-post ' . $post_classes . '">';
					// Get the rich snippets for the post.
					$html .= avada_render_rich_snippets_for_pages();

					$html .= '<div class="panel-heading">';
					$html .= '<h4 class="panel-title toggle">';
					$html .= '<a data-toggle="collapse" class="collapsed" data-parent="#accordian-' . $this->faq_counter . '" data-target="#collapse-' . $this->faq_counter . '-' . $post_id . '" href="#collapse-' . $this->faq_counter . '-' . $post_id . '">';
					$html .= '<div class="fusion-toggle-icon-wrapper"><i class="fa-fusion-box"></i></div>';
					$html .= '<div class="fusion-toggle-heading">' . get_the_title() . '</div>';
					$html .= '</a>';
					$html .= '</h4>';
					$html .= '</div>';

					$html .= '<div id="collapse-' . $this->faq_counter . '-' . $post_id . '" class="panel-collapse collapse">';
					$html .= '<div class="panel-body toggle-content post-content">';

					// Render the featured image of the post.
					if ( ( '1' === $featured_image || 'yes' === $featured_image ) && has_post_thumbnail() ) {
						$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

						if ( $featured_image_src[0] ) {
							$html .= '<div class="fusion-flexslider flexslider fusion-flexslider-loading post-slideshow fusion-post-slideshow">';
							$html .= '<ul class="slides">';
							$html .= '<li>';
							$html .= '<a href="' . $featured_image_src[0] . '" data-rel="iLightbox[gallery]" data-title="' . get_post_field( 'post_title', get_post_thumbnail_id() ) . '" data-caption="' . get_post_field( 'post_excerpt', get_post_thumbnail_id() ) . '">';
							$html .= '<span class="screen-reader-text">' . esc_attr__( 'View Larger Image', 'fusion-core' ) . '</span>';
							$html .= get_the_post_thumbnail( $post_id, 'blog-large' );
							$html .= '</a>';
							$html .= '</li>';
							$html .= '</ul>';
							$html .= '</div>';
						}
					}
					ob_start();
					the_content();
					$html .= ob_get_clean();
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';

				endwhile; // Loop through faq_items.
				wp_reset_postdata();

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

				$html .= '</div>';

				$this->faq_counter++;

				return $html;

			}

			/**
			 * Gets the query arguments.
			 *
			 * @access private
			 * @since 1.0
			 * @param array $term_slugs       The term slugs.
			 * @param array $terms_to_exclude The terms we wish to exclude.
			 */
			private function get_query_args( $term_slugs, $terms_to_exclude ) {

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections FAQ settings.
			 */
			public function add_options() {

				if ( ! class_exists( 'Fusion_Settings' ) ) {
					return;
				}

				$option_name = Fusion_Settings::get_option_name();

				return array(
					'faq_shortcode_section' => array(
						'label'       => esc_html__( 'FAQ Element', 'fusion-core' ),
						'description' => '',
						'id'          => 'faq_shortcode_section',
						'type'        => 'sub-section',
						'fields'      => array(
							'faq_featured_image' => array(
								'label'       => esc_html__( 'FAQ Featured Images', 'fusion-core' ),
								'description' => esc_html__( 'Turn on to display featured images.', 'fusion-core' ),
								'id'          => 'faq_featured_image',
								'default'     => '0',
								'type'        => 'switch',
								'option_name' => $option_name,
							),
							'faq_filters' => array(
								'label'       => esc_html__( 'FAQ Filters', 'fusion-core' ),
								'description' => esc_html__( 'Controls how the filters display for FAQs.', 'fusion-core' ),
								'id'          => 'faq_filters',
								'default'     => 'yes',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'yes'             => esc_html__( 'Show', 'fusion-core' ),
									'yes_without_all' => esc_html__( 'Show without "All"', 'fusion-core' ),
									'no'              => esc_html__( 'Hide', 'fusion-core' ),
								),
								'option_name' => $option_name,
							),
						),
					),
				);
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 3.1
			 * @return array
			 */
			public function add_styling() {
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_settings, $fusion_library;

				$css['global']['.fusion-filters .fusion-filter.fusion-active a']['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );
				$css['global']['.fusion-filters .fusion-filter.fusion-active a']['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				$css[ $content_media_query ]['.fusion-filters']['border-bottom'] = '0';
				$css[ $content_media_query ]['.fusion-filter']['float']          = 'none';
				$css[ $content_media_query ]['.fusion-filter']['margin']         = '0';
				$css[ $content_media_query ]['.fusion-filter']['border-bottom']  = '1px solid #E7E6E6';

				return $css;
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 3.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script(
					'avada-faqs',
					FusionCore_Plugin::$js_folder_url . '/avada-faqs.js',
					FusionCore_Plugin::$js_folder_path . '/avada-faqs.js',
					array( 'jquery', 'isotope', 'jquery-infinite-scroll' ),
					'1',
					true
				);
			}
		}
	} // End if().

	new FusionSC_Faq();
} // End if().

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_faq() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'FAQ', 'fusion-core' ),
		'shortcode'  => 'fusion_faq',
		'icon'       => 'fa fa-lg fa-info-circle',
		'preview'    => FUSION_CORE_PATH . '/shortcodes/previews/fusion-faq-preview.php',
		'preview_id' => 'fusion-builder-block-module-faq-preview-template',
		'params'     => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Filters', 'fusion-core' ),
				'description' => esc_attr__( 'Display the FAQ filters.', 'fusion-core' ),
				'param_name'  => 'filters',
				'value'       => array(
					''                => esc_attr__( 'Default', 'fusion-core' ),
					'yes'             => esc_attr__( 'Show', 'fusion-core' ),
					'yes-without-all' => __( 'Show without "All"', 'fusion-core' ),
					'no'              => esc_attr__( 'Hide', 'fusion-core' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Featured Images', 'fusion-core' ),
				'description' => esc_attr__( 'Display the FAQ featured images.', 'fusion-core' ),
				'param_name'  => 'featured_image',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-core' ),
					'yes' => esc_attr__( 'Yes', 'fusion-core' ),
					'no'  => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Categories', 'fusion-core' ),
				'description' => esc_attr__( 'Select categories to include or leave blank for all.', 'fusion-core' ),
				'param_name'  => 'cats_slug',
				'value'       => fusion_builder_shortcodes_categories( 'faq_category' ),
				'default'     => '',
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Exclude Categories', 'fusion-core' ),
				'description' => esc_attr__( 'Select categories to exclude.', 'fusion-core' ),
				'param_name'  => 'exclude_cats',
				'value'       => fusion_builder_shortcodes_categories( 'faq_category' ),
				'default'     => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-core' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-core' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-core' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-core' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-core' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-core' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-core' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-core' ),
			),
		),
	) );
}
add_action( 'wp_loaded', 'fusion_element_faq' );
