<?php
/**
 * Fusion-Builder Shortcode Element.
 *
 * @package Fusion-Core
 * @since 3.1.0
 */

if ( function_exists( 'fusion_is_element_enabled' ) && fusion_is_element_enabled( 'fusion_portfolio' ) ) {

	if ( ! class_exists( 'FusionSC_Portfolio' ) && class_exists( 'Fusion_Element' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-core
		 * @since 1.0
		 */
		class FusionSC_Portfolio extends Fusion_Element {

			/**
			 * The column number (one/two/three etc).
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $column;

			/**
			 * The image size (eg: full, thumbnail etc).
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $image_size;

			/**
			 * The portfolio counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $portfolio_counter = 1;

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
			 * Regular size images check.
			 *
			 * @access private
			 * @since 1.0
			 * @var null|int|string
			 */
			private $regular_images_found = false;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {

				parent::__construct();
				add_action( 'fusion_portfolio_shortcode_content', array( $this, 'get_post_content' ) );

				// Element attributes.
				add_filter( 'fusion_attr_portfolio-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_portfolio-shortcode-portfolio-wrapper', array( $this, 'portfolio_wrapper_attr' ) );
				add_filter( 'fusion_attr_portfolio-shortcode-portfolio-content', array( $this, 'portfolio_content_attr' ) );
				add_filter( 'fusion_attr_portfolio-shortcode-carousel', array( $this, 'carousel_attr' ) );
				add_filter( 'fusion_attr_portfolio-shortcode-slideshow', array( $this, 'slideshow_attr' ) );
				add_filter( 'fusion_attr_portfolio-shortcode-filter-link', array( $this, 'filter_link_attr' ) );

				add_shortcode( 'fusion_portfolio', array( $this, 'render' ) );
				fusion_portfolio_scripts();

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

				global $fusion_settings, $fusion_library;

				$defaults = apply_filters(
					'fusion_portfolio_default_parameter',
					FusionBuilder::set_shortcode_defaults(
						array(
							'animation_direction'      => 'left',
							'animation_offset'         => $fusion_settings->get( 'animation_offset' ),
							'animation_speed'          => '',
							'animation_type'           => '',
							'autoplay'                 => 'no',
							'text_layout'              => 'unboxed',
							'pull_by'                  => '',
							'cat_slug'                 => '',
							'tag_slug'                 => '',
							'exclude_tags'             => '',
							'carousel_layout'          => 'title_on_rollover',
							'class'                    => '',
							'column_spacing'           => $fusion_settings->get( 'portfolio_column_spacing' ),
							'columns'                  => 3,
							'content_length'           => 'excerpt',
							'excerpt_length'           => $fusion_settings->get( 'portfolio_excerpt_length' ),
							'excerpt_words'            => '',  // Deprecated.
							'exclude_cats'             => '',
							'filters'                  => 'yes',
							'hide_on_mobile'           => fusion_builder_default_visibility( 'string' ),
							'id'                       => '',
							'layout'                   => 'carousel',
							'mouse_scroll'             => 'no',
							'number_posts'             => $fusion_settings->get( 'portfolio_items' ),
							'offset'                   => '',
							'one_column_text_position' => 'below',
							'pagination_type'          => 'none',
							'hide_url_params'          => 'off',
							'picture_size'             => $fusion_settings->get( 'portfolio_featured_image_size' ),
							'portfolio_layout_padding' => '',
							'portfolio_text_alignment' => 'left',
							'portfolio_title_display'  => 'all',
							'scroll_items'             => '',
							'show_nav'                 => 'yes',
							'strip_html'               => 'yes',

							'boxed_text'               => '', // Deprecated.
						),
						$args
					)
				);

				$defaults['column_spacing'] = FusionBuilder::validate_shortcode_attr_value( $defaults['column_spacing'], '' );

				if ( '0' === $defaults['column_spacing'] ) {
					$defaults['column_spacing'] = '0.0';
				}

				if ( '0' === $defaults['offset'] ) {
					$defaults['offset'] = '';
				}

				// Backwards compatibility for old param name.
				if ( 'grid' === $defaults['layout'] && ! isset( $args['text_layout'] ) ) {
					$defaults['boxed_text'] = 'no_text';
				}

				if ( $defaults['boxed_text'] ) {
					$defaults['text_layout'] = $defaults['boxed_text'];
				}

				if ( 'grid-with-excerpts' === $defaults['layout'] || 'grid-with-text' === $defaults['layout'] ) {
					$defaults['layout'] = 'grid';
				}

				if ( 'default' === $defaults['text_layout'] ) {
					$defaults['text_layout'] = $fusion_settings->get( 'portfolio_text_layout', false, 'unboxed' );
				}

				if ( 'full-content' === $defaults['content_length'] ) {
					$defaults['content_length'] = 'full_content';
				}

				if ( 'default' === $defaults['content_length'] ) {
					$defaults['content_length'] = $fusion_settings->get( 'portfolio_content_length', false, 'excerpt' );
				}

				if ( 'default' === $defaults['portfolio_title_display'] ) {
					$defaults['portfolio_title_display'] = $fusion_settings->get( 'portfolio_title_display', false, 'all' );
				}

				if ( 'default' === $defaults['portfolio_text_alignment'] ) {
					$defaults['portfolio_text_alignment'] = $fusion_settings->get( 'portfolio_text_alignment', false, 'left' );
				}

				if ( 'default' === $defaults['picture_size'] ) {
					$image_size = $fusion_settings->get( 'portfolio_featured_image_size' );
					if ( 'full' === $image_size ) {
						$defaults['picture_size'] = 'auto';
					} else {
						$defaults['picture_size'] = 'fixed';
					}
				}

				if ( 'masonry' === $defaults['layout'] ) {
					$defaults['picture_size'] = 'auto';
				}

				if ( 'default' === $defaults['pagination_type'] ) {
					$defaults['pagination_type'] = trim( str_replace( array( '_scroll', '_' ), array( '', '-' ), strtolower( $fusion_settings->get( 'portfolio_pagination_type', false, 'none' ) ) ), '-' );
				}

				if ( 'default' === $defaults['strip_html'] ) {
					$defaults['strip_html'] = $fusion_settings->get( 'portfolio_strip_html_excerpt', false, 'yes' );
				} else {
					$defaults['strip_html'] = ( 'yes' === $defaults['strip_html'] );
				}

				// @codingStandardsIgnoreLine
				extract( $defaults );

				self::$args = $defaults;

				// Set the image size for the slideshow.
				$this->set_image_size();

				// As $excerpt_words is deprecated, only use it when explicity set.
				if ( $excerpt_words || '0' === $excerpt_words ) {
					$excerpt_length = $excerpt_words;
				}

				// Transform $cat_slugs to array.
				$cat_slugs = array();
				if ( 'tag' !== $defaults['pull_by'] ) {
					if ( self::$args['cat_slug'] ) {
						$cat_slugs = preg_replace( '/\s+/', '', self::$args['cat_slug'] );
						$cat_slugs = explode( ',', self::$args['cat_slug'] );
					}
				}

				// Transform $tag_slugs to array.
				$tag_slugs = array();
				if ( 'category' !== $defaults['pull_by'] ) {
					if ( self::$args['tag_slug'] ) {
						$tag_slugs = preg_replace( '/\s+/', '', self::$args['tag_slug'] );
						$tag_slugs = explode( ',', self::$args['tag_slug'] );
					}
				}

				$title      = true;
				$categories = true;
				// Check the title and category display options.
				if ( self::$args['portfolio_title_display'] ) {
					$title_display = self::$args['portfolio_title_display'];
					$title         = ( 'all' === $title_display || 'title' === $title_display );
					$categories    = ( 'all' === $title_display || 'cats' === $title_display );
				}

				// Add styling for alignment and padding.
				$styling = '';
				if ( 'carousel' !== self::$args['layout'] && 'no_text' !== self::$args['text_layout'] ) {
					$layout_padding   = ( 'boxed' === self::$args['text_layout'] && '' !== self::$args['portfolio_layout_padding'] ) ? 'padding: ' . self::$args['portfolio_layout_padding'] . ';' : '';
					$layout_alignment = 'text-align: ' . self::$args['portfolio_text_alignment'] . ';';
					$styling         .= '<style type="text/css">.fusion-portfolio-wrapper#fusion-portfolio-' . $this->portfolio_counter . ' .fusion-portfolio-content{ ' . $layout_padding . ' ' . $layout_alignment . ' }</style>';
				}

				// Transform $cats_to_exclude to array.
				$cats_to_exclude = array();
				if ( 'tag' !== $defaults['pull_by'] ) {
					if ( self::$args['exclude_cats'] ) {
						$cats_to_exclude = preg_replace( '/\s+/', '', self::$args['exclude_cats'] );
						$cats_to_exclude = explode( ',' , self::$args['exclude_cats'] );
					}
				}

				// Transform exclude_tags to array.
				$tags_to_exclude = array();
				if ( 'category' !== $defaults['pull_by'] ) {
					if ( self::$args['exclude_tags'] ) {
						$tags_to_exclude = preg_replace( '/\s+/', '', self::$args['exclude_tags'] );
						$tags_to_exclude = explode( ',' , self::$args['exclude_tags'] );
					}
				}

				// Check if there is paged content.
				$paged = 1;
				if ( 'none' !== $pagination_type ) {
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					if ( is_front_page() ) {
						$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
					}
				}

				// Initialize the query array.
				$args = array(
					'post_type'      => 'avada_portfolio',
					'paged'          => $paged,
					'posts_per_page' => $number_posts,
					'has_password'   => false,
				);

				if ( $defaults['offset'] ) {
					$args['offset'] = $offset + ( $paged - 1 ) * $number_posts;
				}

				// Check if there are categories that should be excluded.
				if ( ! empty( $cats_to_exclude ) ) {

					// Exclude the correct cats from tax_query.
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'portfolio_category',
							'field'    => 'slug',
							'terms'    => $cats_to_exclude,
							'operator' => 'NOT IN',
						),
					);

					// Include the correct cats in tax_query.
					if ( ! empty( $cat_slugs ) ) {
						$args['tax_query']['relation'] = 'AND';
						$args['tax_query'][] = array(
							'taxonomy' => 'portfolio_category',
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
								'taxonomy' => 'portfolio_category',
								'field'    => 'slug',
								'terms'    => $cat_slugs,
							),
						);
					}
				}

				// Check if there are tags that should be excluded.
				if ( ! empty( $tags_to_exclude ) ) {

					// Exclude the correct cats from tax_query.
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'portfolio_tags',
							'field'    => 'slug',
							'terms'    => $tags_to_exclude,
							'operator' => 'NOT IN',
						),
					);

					// Include the correct cats in tax_query.
					if ( ! empty( $tag_slugs ) ) {
						$args['tax_query']['relation'] = 'AND';
						$args['tax_query'][] = array(
							'taxonomy' => 'portfolio_tags',
							'field'    => 'slug',
							'terms'    => $tag_slugs,
							'operator' => 'IN',
						);
					}
				} else {
					// Include the tags from $cat_slugs in tax_query.
					if ( ! empty( $tag_slugs ) ) {
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'portfolio_tags',
								'field'    => 'slug',
								'terms'    => $tag_slugs,
							),
						);
					}
				}

				wp_reset_postdata();

				$portfolio_query = fusion_cached_query( apply_filters( 'fusion_portfolio_query_args', $args ) );

				if ( ! $portfolio_query->have_posts() ) {
					$this->portfolio_counter++;
					return fusion_builder_placeholder( 'avada_portfolio', 'portfolio posts' );
				}

				$portfolio_posts = '';
				if ( is_array( $cat_slugs ) && 0 < count( $cat_slugs ) && function_exists( 'fusion_add_url_parameter' ) ) {
					$cat_ids = array();
					foreach ( $cat_slugs as $cat_slug ) {
						$cat_obj = get_term_by( 'slug', $cat_slug, 'portfolio_category' );
						if ( isset( $cat_obj->term_id ) ) {
							$cat_ids[] = $cat_obj->term_id;
						}
					}
					$cat_ids = implode( ',', $cat_ids );
				}

				// Set a gallery id for the lightbox triggers on rollovers.
				$gallery_id = '-rw-' . $this->portfolio_counter;

				// Loop through returned posts.
				// Setup the inner HTML for each elements.
				while ( $portfolio_query->have_posts() ) {
					$portfolio_query->the_post();

					// Only add post if it has a featured image, or a video, or if placeholders are activated.
					if ( has_post_thumbnail() || $fusion_settings->get( 'featured_image_placeholder' ) || fusion_get_page_option( 'video', get_the_ID() ) ) {

						// Reset vars.
						$rich_snippets = $post_classes = $title_terms = $image = $post_title = $post_terms = $separator = $post_content = $buttons = $learn_more_button = $view_project_button = $post_separator = $element_orientation_class = '';

						// For carousels we only need the image and a li wrapper.
						if ( 'carousel' === $layout ) {
							// Title on rollover layout.
							if ( 'title_on_rollover' === $carousel_layout ) {
								$show_title = 'default';
								// Title below image layout.
							} else {
								$show_title = 'disable';

								// Get the post title.
								$fusion_portfolio_carousel_title = '<h4 ' . FusionBuilder::attributes( 'fusion-carousel-title' ) . '><a href="' . get_permalink( get_the_ID() ) . '" target="_self">' . get_the_title() . '</a></h4>';
								$title_terms .= apply_filters( 'fusion_portfolio_carousel_title', $fusion_portfolio_carousel_title );

								// Get the terms.
								$carousel_terms = get_the_term_list( get_the_ID(), 'portfolio_category', '<div class="fusion-carousel-meta">', ', ', '</div>' );
								$title_terms .= apply_filters( 'fusion_portfolio_carousel_terms', $carousel_terms );
							}

							// Render the video set in page options if no featured image is present.
							if ( ! has_post_thumbnail() && fusion_get_page_option( 'video', get_the_ID() ) ) {
								// For the portfolio one column layout we need a fixed max-width.
								if ( '1' === $columns || 1 === $columns ) {
									$video_max_width = '540px';
									// For all other layouts get the calculated max-width from the image size.
								} else {
									$featured_image_size_dimensions = avada_get_image_size_dimensions( $this->image_size );
									$video_max_width = $featured_image_size_dimensions['width'];
								}

								$video = fusion_get_page_option( 'video', get_the_ID() );
								$video_markup = '<div class="fusion-image-wrapper fusion-video" style="max-width:' . $video_max_width . ';">' . $video . '</div>';
								$image = apply_filters( 'fusion_portfolio_item_video', $video_markup, $video, $video_max_width );

							} elseif ( $fusion_settings->get( 'featured_image_placeholder' ) || has_post_thumbnail() ) {
								// Get the post image.
								if ( 'full' === $this->image_size && class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
									Avada()->images->set_grid_image_meta( array(
										'layout' => 'portfolio_full',
										'columns' => $columns,
										'gutter_width' => $column_spacing,
									) );
								}
								$image = fusion_render_first_featured_image_markup( get_the_ID(), $this->image_size, get_permalink( get_the_ID() ), true, false, false, 'default', $show_title, '', $gallery_id );
								if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
									Avada()->images->set_grid_image_meta( array() );
								}
							}

							$portfolio_posts .= '<li ' . FusionBuilder::attributes( 'fusion-carousel-item' ) . '><div ' . FusionBuilder::attributes( 'fusion-carousel-item-wrapper' ) . '>' . avada_render_rich_snippets_for_pages() . $image . $title_terms . '</div></li>';

						} else {

							$permalink = get_permalink();
							if ( isset( $cat_ids ) && function_exists( 'fusion_add_url_parameter' ) && 'off' === self::$args['hide_url_params'] ) {
								$permalink = fusion_add_url_parameter( $permalink, 'portfolioCats', $cat_ids );

							}

							// Include the post categories or tags based on element option as css classes for later useage with filters.
							$post_categories = get_the_terms( get_the_ID(), 'portfolio_category' );
							$post_tags       = get_the_terms( get_the_ID(), 'portfolio_tags' );

							if ( 'tag' === $defaults['pull_by'] ) {
								if ( $post_tags ) {
									foreach ( $post_tags as $post_tag ) {
										$post_classes .= urldecode( $post_tag->slug ) . ' ';
									}
								}
							} else {
								if ( $post_categories ) {
									foreach ( $post_categories as $post_category ) {
										$post_classes .= urldecode( $post_category->slug ) . ' ';
									}
								}
							}

							// Add the col-spacing class if needed.
							if ( $column_spacing ) {
								$post_classes .= 'fusion-col-spacing';
							}

							// Render the video set in page options if no featured image is present.
							if ( ! has_post_thumbnail() && fusion_get_page_option( 'video', get_the_ID() ) ) {
								// For the portfolio one column layout we need a fixed max-width.
								if ( '1' === $columns || 1 === $columns ) {
									$video_max_width = '540px';
									// For all other layouts get the calculated max-width from the image size.
								} else {
									$featured_image_size_dimensions = avada_get_image_size_dimensions( $this->image_size );
									$video_max_width = $featured_image_size_dimensions['width'];
								}

								$video = fusion_get_page_option( 'video', get_the_ID() );
								$video_markup = '<div class="fusion-image-wrapper fusion-video" style="max-width:' . $video_max_width . ';">' . $video . '</div>';
								$image = apply_filters( 'fusion_portfolio_item_video', $video_markup, $video, $video_max_width );

							} elseif ( $fusion_settings->get( 'featured_image_placeholder' ) || has_post_thumbnail() ) {

								$responsive_images_columns = $columns;
								$masonry_attributes = array();
								$element_base_padding = 0.8;

								// Masonry layout.
								if ( 'masonry' === $layout ) {
									// Set image or placeholder and correct corresponding styling.
									if ( has_post_thumbnail() ) {
										$post_thumbnail_attachment = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
										$masonry_attribute_style = 'background-image:url(' . $post_thumbnail_attachment[0] . ');';
									} else {
										$post_thumbnail_attachment = array();
										$masonry_attribute_style = 'background-color:#f6f6f6;';
									}

									// Get the correct image orientation class.
									if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
										$element_orientation_class = Avada()->images->get_element_orientation_class( $post_thumbnail_attachment );
										$element_base_padding  = Avada()->images->get_element_base_padding( $element_orientation_class );
									}
									$post_classes .= ' ' . $element_orientation_class;

									$masonry_column_offset = ' - ' . ( (int) $column_spacing / 2 ) . 'px';
									if ( 'fusion-element-portrait' === $element_orientation_class ) {
										$masonry_column_offset = '';
									}

									$masonry_column_spacing = ( (int) $column_spacing ) . 'px';

									if ( 'no_text' !== $text_layout && 'boxed' === $text_layout &&
										class_exists( 'Fusion_Sanitize' ) && class_exists( 'Fusion_Color' ) &&
										'transparent' !== Fusion_Sanitize::color( $fusion_settings->get( 'timeline_color' ) ) &&
										'0' != Fusion_Color::new_color( $fusion_settings->get( 'timeline_color' ) )->alpha ) {

										$masonry_column_offset = ' - ' . ( (int) $column_spacing / 2 ) . 'px';
										if ( 'fusion-element-portrait' === $element_orientation_class ) {
											$masonry_column_offset = ' + 4px';
										}

										$masonry_column_spacing = ( (int) $column_spacing - 4 ) . 'px';
										if ( 'fusion-element-landscape' === $element_orientation_class ) {
											$masonry_column_spacing = ( (int) $column_spacing - 10 ) . 'px';
										}
									}

									// Calculate the correct size of the image wrapper container, based on orientation and column spacing.
									$masonry_attribute_style .= 'padding-top:calc((100% + ' . $masonry_column_spacing . ') * ' . $element_base_padding . $masonry_column_offset . ');';

									// Check if we have a landscape image, then it has to stretch over 2 cols.
									if ( 'fusion-element-landscape' === $element_orientation_class ) {
										$responsive_images_columns = $columns / 2;
									} else {
										$this->regular_images_found = true;
									}

									// Set the masonry attributes to use them in the first featured image function.
									$masonry_attributes = array(
										'class' => 'fusion-masonry-element-container',
										'style' => $masonry_attribute_style,
									);
								} // End if().

								// Get the post image.
								if ( 'full' === $this->image_size && class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
									Avada()->images->set_grid_image_meta( array(
										'layout' => 'portfolio_full',
										'columns' => $responsive_images_columns,
										'gutter_width' => $column_spacing,
									) );
								}
								$image = fusion_render_first_featured_image_markup( get_the_ID(), $this->image_size, $permalink, true, false, false, 'default', 'default', '', $gallery_id, 'yes', false, $masonry_attributes );
								if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
									Avada()->images->set_grid_image_meta( array() );
								}
							} // End if().

							// Additional content for layouts using text.
							if ( 'carousel' !== self::$args['layout'] && 'no_text' !== self::$args['text_layout'] ) {

								// Get the rich snippets, if enabled.
								$rich_snippets = avada_render_rich_snippets_for_pages( false );

								// Get the post title.
								if ( $title ) {
									$post_title = avada_render_post_title( get_the_ID(), true, false, '2', $permalink );
								}

								// Get the post terms.
								if ( $categories ) {
									$the_cats = get_the_term_list( get_the_ID(), 'portfolio_category', '', ', ', '' );
									if ( $the_cats ) {
										$post_terms = '<h4>' . $the_cats . '</h4>';
									}
								}

								// Get the post content.
								ob_start();
								/**
								 * The fusion_portfolio_shortcode_content hook.
								 *
								 * @hooked content - 10 (outputs the post content)
								 */
								do_action( 'fusion_portfolio_shortcode_content' );

								$stripped_content = ob_get_clean();

								// For boxed layouts add a content separator if there is a post content.
								if ( 'boxed' === $text_layout && $stripped_content && 'masonry' !== self::$args['layout'] ) {
									$separator_styles_array = explode( '|', $fusion_settings->get( 'separator_style_type' ) );
									$separator_styles = '';

									foreach ( $separator_styles_array as $separator_style ) {
										$separator_styles .= ' sep-' . $separator_style;
									}

									$separator = '<div class="fusion-content-sep' . $separator_styles . '"></div>';
								}

								// On one column layouts render the "Learn More" and "View Project" buttons.
								if ( ( '1' === $columns || 1 === $columns ) && 'masonry' !== self::$args['layout'] ) {
									$classes = 'fusion-button fusion-button-small fusion-button-default fusion-button-' . strtolower( $fusion_settings->get( 'button_shape' ) ) . ' fusion-button-' . strtolower( $fusion_settings->get( 'button_type' ) );

									// Add the "Learn More" button.
									$learn_more_button = '<a href="' . $permalink . '" ' . FusionBuilder::attributes( $classes ) . '>' . esc_attr__( 'Learn More', 'fusion-core' ) . '</a>';

									// If there is a project url, add the "View Project" button.
									$view_project_button = '';
									if ( fusion_get_page_option( 'project_url', get_the_ID() ) ) {
										$view_project_button = '<a href="' . fusion_get_page_option( 'project_url', get_the_ID() ) . '" ' . FusionBuilder::attributes( $classes ) . '>' . esc_attr__( 'View Project', 'fusion-core' ) . '</a>';
									}

									// Wrap buttons.
									$buttons = '<div ' . FusionBuilder::attributes( 'fusion-portfolio-buttons' ) . '>' . $learn_more_button . $view_project_button . '</div>';

								}

								// Put it all together.
								$post_content  = '<div ' . FusionBuilder::attributes( 'portfolio-shortcode-portfolio-content' ) . '>';
								$post_content .= apply_filters( 'fusion_portfolio_grid_title', $post_title );
								$post_content .= apply_filters( 'fusion_portfolio_grid_terms', $post_terms );
								$post_content .= apply_filters( 'fusion_portfolio_grid_separator', $separator );
								$post_content .= '<div ' . FusionBuilder::attributes( 'fusion-post-content' ) . '>';
								$post_content .= apply_filters( 'fusion_portfolio_grid_content', $stripped_content );
								$post_content .= apply_filters( 'fusion_portfolio_grid_buttons', $buttons, $learn_more_button, $view_project_button );
								$post_content .= '</div></div>';
							} else {
								// Get the rich snippets for grid layout without excerpts.
								$rich_snippets = avada_render_rich_snippets_for_pages();
							} // End if().

							// Post separator for one column grid layouts.
							if ( ( '1' === $columns || 1 === $columns ) && 'boxed' !== self::$args['text_layout'] && 'grid' === self::$args['layout'] ) {
								$post_separator = '<div class="fusion-clearfix"></div><div class="fusion-separator sep-double"></div>';
							}

							$portfolio_posts .= '<article ' . FusionBuilder::attributes( 'fusion-portfolio-post ' . $post_classes ) . '><div ' . FusionBuilder::attributes( 'fusion-portfolio-content-wrapper' ) . '>' . $rich_snippets . $image . $post_content . '</div>' . apply_filters( 'fusion_portfolio_grid_post_separator', $post_separator ) . '</article>';
						} // End if().
					} // End if().
				} // End while().

				wp_reset_postdata();

				// Wrap all the portfolio posts with the appropriate HTML markup.
				// Carousel layout.
				if ( 'carousel' === $layout ) {
					self::$args['data-pages'] = '';

					$main_carousel = '<ul ' . FusionBuilder::attributes( 'fusion-carousel-holder' ) . '>' . $portfolio_posts . '</ul>';

					// Check if navigation should be shown.
					$navigation = '';
					if ( 'yes' === $show_nav ) {
						$navigation = '<div ' . FusionBuilder::attributes( 'fusion-carousel-nav' ) . '><span ' . FusionBuilder::attributes( 'fusion-nav-prev' ) . '></span><span ' . FusionBuilder::attributes( 'fusion-nav-next' ) . '></span></div>';
					}

					$html = '<div ' . FusionBuilder::attributes( 'portfolio-shortcode' ) . '><div ' . FusionBuilder::attributes( 'portfolio-shortcode-carousel' ) . '><div ' . FusionBuilder::attributes( 'fusion-carousel-positioner' ) . '>' . $main_carousel . $navigation . '</div></div></div>';

					// Other layouts.
				} else {
					// Reset vars.
					$filter_wrapper = $filter = $styles = '';

					// Setup the filters, if enabled.
					$portfolio_categories = get_terms( 'portfolio_category' );
					$portfolio_tags       = get_terms( 'portfolio_tags' );

					// Check if filters should be displayed.
					if ( ( $portfolio_categories || $portfolio_tags ) && 'no' !== $filters ) {

						// Check if the "All" filter should be displayed.
						$first_filter = true;
						if ( 'yes-without-all' !== $filters ) {
							$filter = '<li role="menuitem" ' . FusionBuilder::attributes( 'fusion-filter fusion-filter-all fusion-active' ) . '><a ' . FusionBuilder::attributes( 'portfolio-shortcode-filter-link', array(
								'data-filter' => '*',
							) ) . '>' . esc_attr__( 'All', 'fusion-core' ) . '</a></li>';
							$first_filter = false;
						}

						if ( 'tag' === $defaults['pull_by'] ) {
							// Loop through tags.
							foreach ( $portfolio_tags as $portfolio_tag ) {
								// Only display filters of non excluded tags.
								if ( ! in_array( $portfolio_tag->slug, $tags_to_exclude, true ) ) {
									// Check if tags have been chosen.
									if ( ! empty( self::$args['tag_slug'] ) ) {

										// Only display filters for explicitly included tags.
										if ( in_array( urldecode( $portfolio_tag->slug ), $tag_slugs, true ) ) {
											// Set the first tag filter to active, if the all filter isn't shown.
											$active_class = '';
											if ( $first_filter ) {
												$active_class = ' fusion-active';
												$first_filter = false;
											}

											$filter .= '<li role="menuitem" ' . FusionBuilder::attributes( 'fusion-filter fusion-hidden' . $active_class ) . '><a ' . FusionBuilder::attributes( 'portfolio-shortcode-filter-link', array(
												'data-filter' => '.' . urldecode( $portfolio_tag->slug ),
											) ) . '>' . $portfolio_tag->name . '</a></li>';
										}
									} else {
										// Display all tags.
										// Set the first tag filter to active, if the all filter isn't shown.
										$active_class = '';
										if ( $first_filter ) {
											$active_class = ' fusion-active';
											$first_filter = false;
										}

										$filter .= '<li role="menuitem" ' . FusionBuilder::attributes( 'fusion-filter fusion-hidden' . $active_class ) . '><a ' . FusionBuilder::attributes( 'portfolio-shortcode-filter-link', array(
											'data-filter' => '.' . urldecode( $portfolio_tag->slug ),
										) ) . '>' . $portfolio_tag->name . '</a></li>';
									}
								}
							} // End foreach().
						} else {
							// Loop through categories.
							foreach ( $portfolio_categories as $portfolio_category ) {
								// Only display filters of non excluded categories.
								if ( ! in_array( $portfolio_category->slug, $cats_to_exclude, true ) ) {
									// Check if categories have been chosen.
									if ( ! empty( self::$args['cat_slug'] ) ) {

										// Only display filters for explicitly included categories.
										if ( in_array( urldecode( $portfolio_category->slug ), $cat_slugs, true ) ) {
											// Set the first category filter to active, if the all filter isn't shown.
											$active_class = '';
											if ( $first_filter ) {
												$active_class = ' fusion-active';
												$first_filter = false;
											}

											$filter .= '<li role="menuitem" ' . FusionBuilder::attributes( 'fusion-filter fusion-hidden' . $active_class ) . '><a ' . FusionBuilder::attributes( 'portfolio-shortcode-filter-link', array(
												'data-filter' => '.' . urldecode( $portfolio_category->slug ),
											) ) . '>' . $portfolio_category->name . '</a></li>';
										}
									} else {
										// Display all categories.
										// Set the first category filter to active, if the all filter isn't shown.
										$active_class = '';
										if ( $first_filter ) {
											$active_class = ' fusion-active';
											$first_filter = false;
										}

										$filter .= '<li role="menuitem" ' . FusionBuilder::attributes( 'fusion-filter fusion-hidden' . $active_class ) . '><a ' . FusionBuilder::attributes( 'portfolio-shortcode-filter-link', array(
											'data-filter' => '.' . urldecode( $portfolio_category->slug ),
										) ) . '>' . $portfolio_category->name . '</a></li>';
									}
								}
							} // End foreach().
						} // End if().

						// Wrap filters.
						$filter_wrapper = '<div role="menubar">';
						$filter_wrapper .= '<ul ' . FusionBuilder::attributes( 'fusion-filters' ) . ' role="menu" aria-label="filters">' . $filter . '</ul>';
						$filter_wrapper .= '</div>';

					} // End if().

					// For column spacing set needed css.
					if ( $column_spacing ) {
						$styles = '<style type="text/css">.fusion-portfolio-' . $this->portfolio_counter . ' .fusion-portfolio-wrapper .fusion-col-spacing{padding:' . ( $column_spacing / 2 ) . 'px;}</style>';
					}

					// Pagination.
					self::$args['data-pages'] = $portfolio_query->max_num_pages;
					$pagination = '';

					if ( 'none' !== $pagination_type && 1 < esc_attr( $portfolio_query->max_num_pages ) ) {

						// Pagination is set to "load more" button.
						if ( 'load-more-button' === $pagination_type && -1 !== intval( $number_posts ) ) {
							$pagination .= '<div class="fusion-load-more-button fusion-portfolio-button fusion-clearfix">' . apply_filters( 'avada_load_more_posts_name', esc_attr__( 'Load More Posts', 'fusion-core' ) ) . '</div>';
						}

						$infinite_pagination = false;
						if ( 'load-more-button' === $pagination_type || 'infinite' === $pagination_type ) {
							$infinite_pagination = true;
						}

						ob_start();
						fusion_pagination( $portfolio_query->max_num_pages, $range = 2, $portfolio_query, $infinite_pagination );
						$pagination .= ob_get_contents();
						ob_get_clean();
					}

					$portfolio_posts = '<article class="fusion-portfolio-post fusion-grid-sizer"></article>' . $portfolio_posts;

					// Put it all together.
					$html = $styling . '<div ' . FusionBuilder::attributes( 'portfolio-shortcode' ) . '>' . $filter_wrapper . $styles . '<div ' . FusionBuilder::attributes( 'portfolio-shortcode-portfolio-wrapper' ) . '>' . $portfolio_posts . '</div>' . $pagination . '</div>';

				} // End if().

				$this->portfolio_counter++;

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

				global $fusion_settings;

				$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], array(
					'class' => 'fusion-recent-works fusion-portfolio-element fusion-portfolio fusion-portfolio-' . $this->portfolio_counter . ' fusion-portfolio-' . self::$args['layout'] . ' fusion-portfolio-paging-' . self::$args['pagination_type'],
				) );

				$attr['data-id'] = '-rw-' . $this->portfolio_counter;

				// Add classes for carousel layout.
				if ( 'carousel' === self::$args['layout'] ) {
					$attr['class'] .= ' recent-works-carousel portfolio-carousel';
					if ( 'auto' === self::$args['picture_size'] ) {
						$attr['class'] .= ' picture-size-auto';
					}
				} else {
					// Add classes for grid and masonry layouts.
					$attr['class'] .= ' fusion-portfolio-' . $this->column . ' fusion-portfolio-' . self::$args['text_layout'];

					if ( ( 'grid' === self::$args['layout'] || 'masonry' === self::$args['layout'] ) && 'no_text' !== self::$args['text_layout'] ) {
						$attr['class'] .= ' fusion-portfolio-text';

						if ( '1' === self::$args['columns'] && 'floated' === self::$args['one_column_text_position'] ) {
							$attr['class'] .= ' fusion-portfolio-text-floated';
						}
					}

					$attr['data-columns'] = $this->column;
				}

				// Add class for no spacing.
				if ( in_array( self::$args['column_spacing'], array( 0, '0', '0px' ), true ) ) {
					$attr['class'] .= ' fusion-no-col-space';
				}

				// Add class if regular size images were found.
				if ( true == $this->regular_images_found ) {
					$attr['class'] .= ' fusion-masonry-has-vertical';
				}

				// Add class if rollover is enabled.
				if ( $fusion_settings->get( 'image_rollover' ) ) {
					$attr['class'] .= ' fusion-portfolio-rollover';
				}

				// Add custom class.
				if ( self::$args['class'] ) {
					$attr['class'] .= ' ' . self::$args['class'];
				}

				// Add custom id.
				if ( self::$args['id'] ) {
					$attr['id'] = self::$args['id'];
				}

				// Add animation classes.
				if ( self::$args['animation_type'] ) {
					$animations = FusionBuilder::animations( array(
						'type'      => self::$args['animation_type'],
						'direction' => self::$args['animation_direction'],
						'speed'     => self::$args['animation_speed'],
						'offset'    => self::$args['animation_offset'],
					) );

					$attr = array_merge( $attr, $animations );

					$attr['class'] .= ' ' . $attr['animation_class'];
					unset( $attr['animation_class'] );
				}

				return $attr;

			}

			/**
			 * Builds the portfolio-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function portfolio_wrapper_attr( $args ) {

				$attr = array(
					'class'            => 'fusion-portfolio-wrapper',
					'id'               => 'fusion-portfolio-' . $this->portfolio_counter,
					'data-picturesize' => self::$args['picture_size'],
				);

				$attr['data-pages'] = self::$args['data-pages'];

				if ( self::$args['column_spacing'] ) {
					$margin = ( -1 ) * self::$args['column_spacing'] / 2;
					$attr['style'] = 'margin:' . $margin . 'px;';
				}

				return $attr;

			}

			/**
			 * Builds the fusion-portfolio-content attributes array.
			 *
			 * @access public
			 * @since 1.3
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function portfolio_content_attr( $args ) {
				global $fusion_settings, $fusion_library;

				$attr = array(
					'class' => 'fusion-portfolio-content',
					'style' => '',
				);

				if ( 'masonry' === self::$args['layout'] ) {
					$masonry_content_padding = self::$args['column_spacing'] / 2;

					if ( 'boxed' === self::$args['text_layout'] ) {
						$attr['style'] .= 'bottom:0px;';
						$attr['style'] .= 'left:0px;';
						$attr['style'] .= 'right:0px;';
					} else {
						$attr['style'] .= 'padding:20px 0px;';
						$attr['style'] .= 'bottom:0px;';
						$attr['style'] .= 'left:0px;';
						$attr['style'] .= 'right:0px;';
					}

					$attr['style'] .= 'z-index:1;';
					$attr['style'] .= 'position:absolute;';
					$attr['style'] .= 'margin:0;';

					$color = Fusion_Color::new_color( $fusion_settings->get( 'timeline_bg_color' ) );
					$color_css = $color->to_css( 'rgba' );
					if ( 0 === $color->alpha ) {
						$color_css = $color->to_css( 'rgb' );
					}
					$attr['style'] .= 'background-color:' . $color_css . ';';
				}

				return $attr;

			}

			/**
			 * Builds the carousel attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function carousel_attr() {

				$attr = array(
					'class' => 'fusion-carousel',
				);

				if ( 'title_below_image' === self::$args['carousel_layout'] ) {
					$attr['data-metacontent'] = 'yes';
					$attr['class'] .= ' fusion-carousel-title-below-image';
				}

				if ( 'fixed' === self::$args['picture_size'] ) {
					$attr['class'] .= ' fusion-portfolio-carousel-fixed';
				}

				$attr['data-autoplay']    = self::$args['autoplay'];
				$attr['data-columns']     = self::$args['columns'];
				$attr['data-itemmargin']  = self::$args['column_spacing'];
				$attr['data-itemwidth']   = 180;
				$attr['data-touchscroll'] = self::$args['mouse_scroll'];
				$attr['data-imagesize']   = self::$args['picture_size'];
				$attr['data-scrollitems'] = self::$args['scroll_items'];

				return $attr;
			}

			/**
			 * Builds the filter-link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function filter_link_attr( $args ) {

				$attr = array(
					'href' => '#',
				);

				if ( $args['data-filter'] ) {
					$attr['data-filter'] = $args['data-filter'];
				}

				return $attr;

			}

			/**
			 * Set image size.
			 *
			 * @access public
			 * @since 1.0
			 * @return void
			 */
			public function set_image_size() {

				// Set columns object var to correct string.
				switch ( self::$args['columns'] ) {
					case 1:
						$this->column = 'one';
						break;
					case 2:
						$this->column = 'two';
						break;
					case 3:
						$this->column = 'three';
						break;
					case 4:
						$this->column = 'four';
						break;
					case 5:
						$this->column = 'five';
						break;
					case 6:
						$this->column = 'six';
						break;
				}

				// Set the image size according to picture size param and layout.
				$this->image_size = 'full';
				if ( 'fixed' === self::$args['picture_size'] ) {
					if ( 'carousel' === self::$args['layout'] ) {
						$this->image_size = 'portfolio-two';
						if ( 'six' === $this->column || 'five' === $this->column || 'four' === $this->column ) {
							$this->image_size = 'blog-medium';
						}
					} else {
						$this->image_size = 'portfolio-' . $this->column;
						if ( 'six' === $this->column ) {
							$this->image_size = 'portfolio-five';
						} elseif ( 'four' === $this->column ) {
							$this->image_size = 'portfolio-three';
						}
					}
				}
			}

			/**
			 * Echoes the post-content.
			 *
			 * @access public
			 * @since 1.0
			 * @return void
			 */
			public function get_post_content() {
				$excerpt = 'no';
				if ( 'excerpt' === strtolower( self::$args['content_length'] ) ) {
					$excerpt = 'yes';
				}

				echo fusion_get_post_content( '', $excerpt, self::$args['excerpt_length'], self::$args['strip_html'] ); // WPCS: XSS ok.
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
					'portfolio_shortcode_section' => array(
						'label'       => esc_html__( 'Portfolio Element', 'fusion-core' ),
						'description' => '',
						'id'          => 'portfolio_shortcode_section',
						'type'        => 'sub-section',
						'fields'      => array(
							'portfolio_featured_image_size' => array(
								'label'       => esc_html__( 'Portfolio Featured Image Size', 'Avada' ),
								'description' => esc_html__( 'Controls if the featured image size is fixed (cropped) or auto (full image ratio) for portfolio elements. IMPORTANT: Fixed works best with a standard 940px site width. Auto works best with larger site widths.', 'Avada' ),
								'id'          => 'portfolio_featured_image_size',
								'default'     => 'cropped',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'cropped' => esc_html__( 'Fixed', 'Avada' ),
									'full'    => esc_html__( 'Auto', 'Avada' ),
								),
							),
							'portfolio_column_spacing' => array(
								'label'       => esc_html__( 'Portfolio Column Spacing', 'Avada' ),
								'description' => esc_html__( 'Controls the column spacing for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_column_spacing',
								'default'     => '20',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '300',
									'step' => '1',
								),
							),
							'portfolio_items' => array(
								'label'       => esc_html__( 'Number of Portfolio Items Per Page', 'Avada' ),
								'description' => esc_html__( 'Controls the number of posts that display per page for portfolio elements. Set to -1 to display all. Set to 0 to use the number of posts from Settings > Reading.', 'Avada' ),
								'id'          => 'portfolio_items',
								'default'     => '10',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '-1',
									'max'  => '50',
									'step' => '1',
								),
							),
							'portfolio_text_layout' => array(
								'label'       => esc_html__( 'Portfolio Text Layout', 'Avada' ),
								'description' => esc_html__( 'Controls if the portfolio text content is displayed boxed or unboxed or is completely disabled for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_text_layout',
								'default'     => 'unboxed',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'no_text' => esc_html__( 'No Text', 'Avada' ),
									'boxed'   => esc_html__( 'Boxed', 'Avada' ),
									'unboxed' => esc_html__( 'Unboxed', 'Avada' ),
								),
							),
							'portfolio_content_length' => array(
								'label'       => esc_html__( 'Portfolio Content Display', 'Avada' ),
								'description' => esc_html__( 'Controls if the portfolio content displays an excerpt or full content for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_content_length',
								'default'     => 'excerpt',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'excerpt'      => esc_html__( 'Excerpt', 'Avada' ),
									'full_content' => esc_html__( 'Full Content', 'Avada' ),
								),
							),
							'portfolio_excerpt_length' => array(
								'label'       => esc_html__( 'Excerpt Length', 'Avada' ),
								'description' => esc_html__( 'Controls the number of words in the excerpts for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_excerpt_length',
								'default'     => '10',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '500',
									'step' => '1',
								),
								'required'    => array(
									array(
										'setting'  => 'portfolio_content_length',
										'operator' => '==',
										'value'    => 'excerpt',
									),
								),
							),
							'portfolio_strip_html_excerpt' => array(
								'label'       => esc_html__( 'Strip HTML from Excerpt', 'Avada' ),
								'description' => esc_html__( 'Turn on to strip HTML content from the excerpt for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_strip_html_excerpt',
								'default'     => '1',
								'type'        => 'switch',
							),
							'portfolio_title_display' => array(
								'label'       => esc_html__( 'Portfolio Title Display', 'Avada' ),
								'description' => esc_html__( 'Controls what displays with the portfolio post title for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_title_display',
								'default'     => 'all',
								'type'        => 'select',
								'choices'     => array(
									'all'     => esc_html__( 'Title and Categories', 'Avada' ),
									'title'   => esc_html__( 'Only Title', 'Avada' ),
									'cats'    => esc_html__( 'Only Categories', 'Avada' ),
									'none'    => esc_html__( 'None', 'Avada' ),
								),
							),
							'portfolio_text_alignment' => array(
								'label'       => esc_html__( 'Portfolio Text Alignment', 'Avada' ),
								'description' => esc_html__( 'Controls the alignment of the portfolio title, categories and excerpt text when using the Portfolio Text layouts in portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_text_alignment',
								'default'     => 'left',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'left'     => esc_html__( 'Left', 'Avada' ),
									'center'   => esc_html__( 'Center', 'Avada' ),
									'right'    => esc_html__( 'Right', 'Avada' ),
								),
							),
							'portfolio_layout_padding' => array(
								'label'       => esc_html__( 'Portfolio Text Layout Padding', 'Avada' ),
								'description' => esc_html__( 'Controls the padding for the portfolio text layout when using boxed mode in portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_layout_padding',
								'choices'     => array(
									'top'     => true,
									'bottom'  => true,
									'left'    => true,
									'right'   => true,
									'units'   => array( 'px', '%' ),
								),
								'default'     => array(
									'top'     => '25px',
									'bottom'  => '25px',
									'left'    => '25px',
									'right'   => '25px',
								),
								'type'        => 'spacing',
								'required'    => array(
									array(
										'setting'  => 'portfolio_text_layout',
										'operator' => '==',
										'value'    => 'boxed',
									),
								),
							),
							'portfolio_pagination_type' => array(
								'label'       => esc_html__( 'Pagination Type', 'Avada' ),
								'description' => esc_html__( 'Controls the pagination type for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_pagination_type',
								'default'     => 'pagination',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'pagination'       => esc_html__( 'Pagination', 'Avada' ),
									'infinite_scroll'  => esc_html__( 'Infinite Scroll', 'Avada' ),
									'load_more_button' => esc_html__( 'Load More Button', 'Avada' ),
								),
							),
							'portfolio_load_more_posts_button_bg_color' => array(
								'label'       => esc_html__( 'Load More Posts Button Color', 'Avada' ),
								'description' => esc_html__( 'Controls the background color of the load more button for ajax post loading for portfolio elements.', 'Avada' ),
								'id'          => 'portfolio_load_more_posts_button_bg_color',
								'default'     => '#ebeaea',
								'type'        => 'color-alpha',
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

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_settings, $dynamic_css_helpers, $fusion_library;

				$elements = array(
					'.fusion-portfolio .fusion-portfolio-boxed .fusion-portfolio-post-wrapper',
					'.fusion-portfolio .fusion-portfolio-boxed .fusion-content-sep',
					'.fusion-portfolio-one .fusion-portfolio-boxed .fusion-portfolio-post-wrapper',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'sep_color' ) );

				$css['global']['.fusion-filters .fusion-filter.fusion-active a']['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );
				$css['global']['.fusion-filters .fusion-filter.fusion-active a']['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				$css[ $content_media_query ]['.fusion-filters']['border-bottom'] = '0';
				$css[ $content_media_query ]['.fusion-filter']['float']          = 'none';
				$css[ $content_media_query ]['.fusion-filter']['margin']         = '0';
				$css[ $content_media_query ]['.fusion-filter']['border-bottom']  = '1px solid #E7E6E6';

				return $css;
			}
		}
	} // End if().

	new FusionSC_Portfolio();
} // End if().

/**
 * Sets the necessary scripts.
 *
 * @access public
 * @since 3.1
 * @return void
 */
function fusion_portfolio_scripts() {

	global $fusion_settings;

	Fusion_Dynamic_JS::localize_script(
		'avada-portfolio',
		'avadaPortfolioVars',
		array(
			'lightbox_behavior'     => $fusion_settings->get( 'lightbox_behavior' ),
			'infinite_finished_msg' => '<em>' . __( 'All items displayed.', 'fusion-core' ) . '</em>',
			'infinite_blog_text'    => '<em>' . __( 'Loading the next set of posts...', 'fusion-core' ) . '</em>',
			'content_break_point'   => intval( $fusion_settings->get( 'content_break_point' ) ),
		)
	);
	Fusion_Dynamic_JS::enqueue_script(
		'avada-portfolio',
		FusionCore_Plugin::$js_folder_url . '/avada-portfolio.js',
		FusionCore_Plugin::$js_folder_path . '/avada-portfolio.js',
		array( 'jquery', 'fusion-video-general', 'fusion-lightbox', 'images-loaded', 'packery' ),
		'1',
		true
	);
}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_portfolio() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Portfolio', 'fusion-core' ),
		'shortcode'  => 'fusion_portfolio',
		'icon'       => 'fusiona-insertpicture',
		'preview'    => FUSION_CORE_PATH . '/shortcodes/previews/fusion-portfolio-preview.php',
		'preview_id' => 'fusion-builder-block-module-portfolio-preview-template',
		'params'     => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Layout', 'fusion-core' ),
				'description' => esc_attr__( 'Select the layout for the element.', 'fusion-core' ),
				'param_name'  => 'layout',
				'value'       => array(
					'carousel'       => esc_attr__( 'Carousel', 'fusion-core' ),
					'grid'           => esc_attr__( 'Grid', 'fusion-core' ),
					'masonry'        => esc_attr__( 'Masonry', 'fusion-core' ),
				),
				'default'     => 'carousel',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Picture Size', 'fusion-core' ),
				'description' => __( 'fixed = width and height will be fixed <br />auto = width and height will adjust to the image.', 'fusion-core' ),
				'param_name'  => 'picture_size',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-core' ),
					'fixed'   => esc_attr__( 'Fixed', 'fusion-core' ),
					'auto'    => esc_attr__( 'Auto', 'fusion-core' ),
				),
				'default'     => 'default',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'masonry',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Layout', 'fusion-core' ),
				'description' => esc_attr__( 'Controls if the portfolio text content is displayed boxed or unboxed or is completely disabled.', 'fusion-core' ),
				'param_name'  => 'text_layout',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-core' ),
					'no_text' => esc_attr__( 'No Text', 'fusion-core' ),
					'boxed'   => esc_attr__( 'Boxed', 'fusion-core' ),
					'unboxed' => esc_attr__( 'Unboxed', 'fusion-core' ),
				),
				'default'     => 'default',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Columns', 'fusion-core' ),
				'description' => __( 'Select the number of columns to display. With Carousel layout this specifies the maximum amount of columns. <strong>IMPORTANT:</strong> Masonry layout does not work with 1 column.', 'fusion-core' ),
				'param_name'  => 'columns',
				'value'       => '3',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Position', 'fusion-core' ),
				'description' => __( 'Select if title, terms and excerpts should be displayed below or next to the featured images.', 'fusion-core' ),
				'param_name'  => 'one_column_text_position',
				'default'     => 'below',
				'value'       => array(
					'below'   => esc_attr__( 'Below image', 'fusion-core' ),
					'floated' => esc_attr__( 'Next to Image', 'fusion-core' ),
				),
				'dependency'  => array(
					array(
						'element'  => 'columns',
						'value'    => '1',
						'operator' => '==',
					),
					array(
						'element'  => 'layout',
						'value'    => 'grid',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Column Spacing', 'fusion-core' ),
				'description' => esc_attr__( 'Insert the amount of spacing between portfolio items without "px". ex: 7.', 'fusion-core' ),
				'param_name'  => 'column_spacing',
				'value'       => '20',
				'min'         => '0',
				'max'         => '300',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'portfolio_column_spacing' ),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Posts Per Page', 'fusion-core' ),
				'description' => esc_attr__( 'Select number of posts per page.  Set to -1 to display all. Set to 0 to use number of posts from Settings > Reading.', 'fusion-core' ),
				'param_name'  => 'number_posts',
				'value'       => '8',
				'min'         => '-1',
				'max'         => '25',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'portfolio_items' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Portfolio Title Display', 'fusion-core' ),
				'description' => esc_attr__( 'Controls what displays with the portfolio post title.', 'fusion-core' ),
				'param_name'  => 'portfolio_title_display',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-core' ),
					'all'     => esc_attr__( 'Title and Categories', 'fusion-core' ),
					'title'   => esc_attr__( 'Only Title', 'fusion-core' ),
					'cats'    => esc_attr__( 'Only Categories', 'fusion-core' ),
					'none'    => esc_attr__( 'None', 'fusion-core' ),
				),
				'default'     => 'all',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Portfolio Text Alignment', 'fusion-core' ),
				'description' => esc_attr__( 'Controls the alignment of the portfolio title, categories and excerpt text when using the Portfolio Text layouts.', 'fusion-core' ),
				'param_name'  => 'portfolio_text_alignment',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-core' ),
					'left'    => esc_attr__( 'Left', 'fusion-core' ),
					'center'  => esc_attr__( 'Center', 'fusion-core' ),
					'right'   => esc_attr__( 'Right', 'fusion-core' ),
				),
				'default'     => 'left',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'dimension',
				'heading'     => esc_attr__( 'Portfolio Text Layout Padding ', 'fusion-core' ),
				'description' => esc_attr__( 'Controls the padding for the portfolio text layout when using boxed mode. Enter values including any valid CSS unit, ex: 25px, 25px, 25px, 25px.', 'fusion-core' ),
				'param_name'  => 'portfolio_layout_padding',
				'dependency'  => array(
					array(
						'element'  => 'text_layout',
						'value'    => 'unboxed',
						'operator' => '!=',
					),
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Filters', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to show or hide the category filters.', 'fusion-core' ),
				'param_name'  => 'filters',
				'value'       => array(
					'yes'             => esc_attr__( 'Yes', 'fusion-core' ),
					'yes-without-all' => __( 'Yes without "All"', 'fusion-core' ),
					'no'              => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Pull Posts By', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show posts by category or tag.', 'fusion-builder' ),
				'param_name'  => 'pull_by',
				'default'     => 'category',
				'value'       => array(
					'category' => esc_attr__( 'Category', 'fusion-builder' ),
					'tag'      => esc_attr__( 'Tag', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Categories', 'fusion-core' ),
				'description' => esc_attr__( 'Select categories or leave blank for all.', 'fusion-core' ),
				'param_name'  => 'cat_slug',
				'value'       => fusion_builder_shortcodes_categories( 'portfolio_category' ),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'pull_by',
						'value'    => 'tag',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Exclude Categories', 'fusion-core' ),
				'description' => esc_attr__( 'Select categories to exclude.', 'fusion-core' ),
				'param_name'  => 'exclude_cats',
				'value'       => fusion_builder_shortcodes_categories( 'portfolio_category' ),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'pull_by',
						'value'    => 'tag',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Tags', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a tag or leave blank for all.', 'fusion-builder' ),
				'param_name'  => 'tag_slug',
				'value'       => function_exists( 'fusion_builder_shortcodes_tags' ) ? fusion_builder_shortcodes_tags( 'portfolio_tags' ) : array(),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'pull_by',
						'value'    => 'category',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'multiple_select',
				'heading'     => esc_attr__( 'Exclude Tags', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a tag to exclude.', 'fusion-builder' ),
				'param_name'  => 'exclude_tags',
				'value'       => function_exists( 'fusion_builder_shortcodes_tags' ) ? fusion_builder_shortcodes_tags( 'portfolio_tags' ) : array(),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'pull_by',
						'value'    => 'category',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Pagination Type', 'fusion-core' ),
				'description' => esc_attr__( 'Choose the type of pagination.', 'fusion-core' ),
				'param_name'  => 'pagination_type',
				'default'     => 'none',
				'value'       => array(
					'default'          => esc_attr__( 'Default', 'fusion-core' ),
					'pagination'       => esc_attr__( 'Pagination', 'fusion-core' ),
					'infinite'         => esc_attr__( 'Infinite Scrolling', 'fusion-core' ),
					'load-more-button' => esc_attr__( 'Load More Button', 'fusion-core' ),
					'none'             => esc_attr__( 'None', 'fusion-core' ),
				),
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Hide URL Parameter', 'fusion-core' ),
				'description' => esc_attr__( 'Turn on to remove portfolio category parameters in single post URLs. These are mainly used for single item pagination within selected categories.', 'fusion-core' ),
				'param_name'  => 'hide_url_params',
				'default'     => 'off',
				'value'       => array(
					'on'  => esc_attr__( 'On', 'fusion-core' ),
					'off' => esc_attr__( 'Off', 'fusion-core' ),
				),
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Post Offset', 'fusion-core' ),
				'description' => esc_attr__( 'The number of posts to skip. ex: 1.', 'fusion-core' ),
				'param_name'  => 'offset',
				'value'       => '0',
				'min'         => '0',
				'max'         => '25',
				'step'        => '1',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Display', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to display an excerpt or full content.', 'fusion-core' ),
				'param_name'  => 'content_length',
				'value'       => array(
					'default'      => esc_attr__( 'Default', 'fusion-core' ),
					'excerpt'      => esc_attr__( 'Excerpt', 'fusion-core' ),
					'full_content' => esc_attr__( 'Full Content', 'fusion-core' ),
				),
				'default'     => 'excerpt',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Excerpt Length', 'fusion-core' ),
				'description' => esc_attr__( 'Insert the number of words/characters you want to show in the excerpt.', 'fusion-core' ),
				'param_name'  => 'excerpt_length',
				'value'       => '10',
				'min'         => '0',
				'max'         => '500',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'excerpt_length_portfolio' ),
				'dependency'  => array(
					array(
						'element'  => 'content_length',
						'value'    => 'full_content',
						'operator' => '!=',
					),
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Strip HTML', 'fusion-core' ),
				'description' => esc_attr__( 'Strip HTML from the post excerpt.', 'fusion-core' ),
				'param_name'  => 'strip_html',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-core' ),
					'yes'     => esc_attr__( 'Yes', 'fusion-core' ),
					'no'      => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '!=',
					),
					array(
						'element'  => 'content_length',
						'value'    => 'full_content',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Carousel Layout', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to show titles on rollover image, or below image.', 'fusion-core' ),
				'param_name'  => 'carousel_layout',
				'value'       => array(
					'title_below_image' => esc_attr__( 'Title below image', 'fusion-core' ),
					'title_on_rollover' => esc_attr__( 'Title on rollover', 'fusion-core' ),
				),
				'default'     => 'title_on_rollover',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Carousel Scroll Items', 'fusion-core' ),
				'description' => esc_attr__( 'Insert the amount of items to scroll. Leave empty to scroll number of visible items.', 'fusion-core' ),
				'param_name'  => 'scroll_items',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Carousel Autoplay', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to autoplay the carousel.', 'fusion-core' ),
				'param_name'  => 'autoplay',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-core' ),
					'no'  => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Carousel Show Navigation', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to show navigation buttons on the carousel.', 'fusion-core' ),
				'param_name'  => 'show_nav',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-core' ),
					'no'  => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => 'yes',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Carousel Mouse Scroll', 'fusion-core' ),
				'description' => esc_attr__( 'Choose to enable mouse drag control on the carousel.', 'fusion-core' ),
				'param_name'  => 'mouse_scroll',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-core' ),
					'no'  => esc_attr__( 'No', 'fusion-core' ),
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'layout',
						'value'    => 'carousel',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-core' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-core' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-core' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-core' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-core' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					'down'   => esc_attr__( 'Top', 'fusion-core' ),
					'right'  => esc_attr__( 'Right', 'fusion-core' ),
					'up'     => esc_attr__( 'Bottom', 'fusion-core' ),
					'left'   => esc_attr__( 'Left', 'fusion-core' ),
					'static' => esc_attr__( 'Static', 'fusion-core' ),
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-core' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-core' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-core' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-core' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-core' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-core' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					''                => esc_attr__( 'Default', 'fusion-core' ),
					'top-into-view'   => esc_attr__( 'Top of element hits bottom of viewport', 'fusion-core' ),
					'top-mid-of-view' => esc_attr__( 'Top of element hits middle of viewport', 'fusion-core' ),
					'bottom-in-view'  => esc_attr__( 'Bottom of element enters viewport', 'fusion-core' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-core' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
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
add_action( 'wp_loaded', 'fusion_element_portfolio' );
