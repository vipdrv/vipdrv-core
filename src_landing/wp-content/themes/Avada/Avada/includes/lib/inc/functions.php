<?php
/**
 * A collections of functions.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Returns an instance of the Fusion class.
 *
 * @since 1.0.0
 */
function fusion_library() {
	return Fusion::get_instance();
}

if ( ! function_exists( 'fusion_get_option' ) ) {
	/**
	 * Get theme option or page option.
	 *
	 * @param  string  $theme_option Theme option ID.
	 * @param  string  $page_option  Page option ID.
	 * @param  integer $post_id      Post/Page ID.
	 * @return string                Theme option or page option value.
	 */
	function fusion_get_option( $theme_option, $page_option, $post_id ) {

		if ( $theme_option && $page_option && $post_id ) {
			$page_option  = strtolower( fusion_get_page_option( $page_option, $post_id ) );
			$theme_option = strtolower( fusion_library()->get_option( $theme_option ) );

			if ( 'default' !== $page_option && ! empty( $page_option ) ) {
				return $page_option;
			}
			return $theme_option;
		} elseif ( $theme_option && 0 === intval( $post_id ) ) {
			$theme_option = strtolower( fusion_library()->get_option( $theme_option ) );
			return $theme_option;
		}
		return false;

	}
}

if ( ! function_exists( 'fusion_get_page_option' ) ) {
	/**
	 * Get page option value.
	 *
	 * @param  string  $page_option ID of page option.
	 * @param  integer $post_id     Post/Page ID.
	 * @return string               Value of page option.
	 */
	function fusion_get_page_option( $page_option, $post_id ) {

		if ( $page_option && $post_id ) {
			if ( 0 === strpos( $page_option, 'pyre_' ) ) {
				$page_option = str_replace( 'pyre_', '', $page_option );
			}
			return get_post_meta( $post_id, 'pyre_' . $page_option, true );
		}
		return false;

	}
}

if ( ! function_exists( 'fusion_get_mismatch_option' ) ) {
	/**
	 * Get theme option or page option when mismatched.
	 *
	 * @param  string  $theme_option Theme option ID.
	 * @param  string  $page_option  Page option ID.
	 * @param  integer $post_id      Post/Page ID.
	 * @since  4.0
	 * @return string                Theme option or page option value.
	 */
	function fusion_get_mismatch_option( $theme_option, $page_option, $post_id ) {

		if ( $theme_option && $page_option && $post_id ) {
			$page_option  = strtolower( fusion_get_page_option( $page_option, $post_id ) );
			$theme_option = strtolower( fusion_library()->get_option( $theme_option ) );
			// @codingStandardsIgnoreLine
			$theme_option = ( 1 == $theme_option ) ? 0 : 1;

			if ( 'default' !== $page_option && ! empty( $page_option ) ) {
				return $page_option;
			}
			return $theme_option;
		}
		return false;

	}
}

if ( ! function_exists( 'fusion_render_rich_snippets_for_pages' ) ) {
	/**
	 * Render the full meta data for blog archive and single layouts.
	 *
	 * @param  boolean $title_tag   Set to true to render title rich snippet.
	 * @param  bool    $author_tag  Set to true to render author rich snippet.
	 * @param  bool    $updated_tag Set to true to render updated rich snippet.
	 * @return string               HTML markup to display rich snippets.
	 */
	function fusion_render_rich_snippets_for_pages( $title_tag = true, $author_tag = true, $updated_tag = true ) {
		ob_start();
		include wp_normalize_path( locate_template( 'templates/pages-rich-snippets.php' ) );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'fusion_render_post_metadata' ) ) {
	/**
	 * Render the full meta data for blog archive and single layouts.
	 *
	 * @param string $layout    The blog layout (either single, standard, alternate or grid_timeline).
	 * @param string $settings HTML markup to display the date and post format box.
	 * @return  string
	 */
	function fusion_render_post_metadata( $layout, $settings = array() ) {

		$html     = '';
		$author   = '';
		$date     = '';
		$metadata = '';

		$settings = ( is_array( $settings ) ) ? $settings : array();

		$default_settings = array(
			'post_meta'          => fusion_library()->get_option( 'post_meta' ),
			'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
			'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
			'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
			'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
			'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
		);

		$settings = wp_parse_args( $settings, $default_settings );
		$post_meta = get_post_meta( get_queried_object_id(), 'pyre_post_meta', true );

		// Check if meta data is enabled.
		if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

			// For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
			if ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] ) {
				return '';
			}

			// Render author meta data.
			if ( $settings['post_meta_author'] ) {
				ob_start();
				the_author_posts_link();
				$author_post_link = ob_get_clean();

				// Check if rich snippets are enabled.
				if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) && fusion_library()->get_option( 'disable_rich_snippet_author' ) ) {
					$metadata .= sprintf( esc_html__( 'By %s', 'Avada' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
				} else {
					$metadata .= sprintf( esc_html__( 'By %s', 'Avada' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
				}
				$metadata .= '<span class="fusion-inline-sep">|</span>';
			} else { // If author meta data won't be visible, render just the invisible author rich snippet.
				$author .= fusion_render_rich_snippets_for_pages( false, true, false );
			}

			// Render the updated meta data or at least the rich snippet if enabled.
			if ( $settings['post_meta_date'] ) {
				$metadata .= fusion_render_rich_snippets_for_pages( false, false, true );

				$formatted_date = get_the_time( fusion_library()->get_option( 'date_format' ) );
				$date_markup = '<span>' . $formatted_date . '</span><span class="fusion-inline-sep">|</span>';
				$metadata .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
			} else {
				$date .= fusion_render_rich_snippets_for_pages( false, false, true );
			}

			// Render rest of meta data.
			// Render categories.
			if ( $settings['post_meta_cats'] ) {
				ob_start();
				the_category( ', ' );
				$categories = ob_get_clean();

				if ( $categories ) {
					$metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'Avada' ), $categories ) : $categories;
					$metadata .= '<span class="fusion-inline-sep">|</span>';
				}
			}

			// Render tags.
			if ( $settings['post_meta_tags'] ) {
				ob_start();
				the_tags( '' );
				$tags = ob_get_clean();

				if ( $tags ) {
					$metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'Avada' ), $tags ) . '</span><span class="fusion-inline-sep">|</span>';
				}
			}

			// Render comments.
			if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
				ob_start();
				comments_popup_link( esc_html__( '0 Comments', 'Avada' ), esc_html__( '1 Comment', 'Avada' ), esc_html__( '% Comments', 'Avada' ) );
				$comments = ob_get_clean();
				$metadata .= '<span class="fusion-comments">' . $comments . '</span>';
			}

			// Render the HTML wrappers for the different layouts.
			if ( $metadata ) {
				$metadata = $author . $date . $metadata;

				if ( 'single' === $layout ) {
					$html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
				} elseif ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) ) {
					$html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
				} else {
					$html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
				}
			} else {
				$html .= $author . $date;
			}
		} else {
			// Render author and updated rich snippets for grid and timeline layouts.
			if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) ) {
				$html .= fusion_render_rich_snippets_for_pages( false );
			}
		}// End if().

		return apply_filters( 'fusion_post_metadata_markup', $html );
	}
}// End if().

if ( ! function_exists( 'fusion_calc_color_brightness' ) ) {
	/**
	 * Convert Calculate the brightness of a color.
	 *
	 * @param  string $color Color (Hex) Code.
	 * @return integer brightness level.
	 */
	function fusion_calc_color_brightness( $color ) {

		// @codingStandardsIgnoreLine
		if ( in_array( strtolower( $color ), array( 'black', 'navy', 'purple', 'maroon', 'indigo', 'darkslategray', 'darkslateblue', 'darkolivegreen', 'darkgreen', 'darkblue' ) ) ) {
			$brightness_level = 0;
		} elseif ( strpos( $color, '#' ) === 0 ) {
			$color = fusion_hex2rgb( $color );

			$brightness_level = sqrt( pow( $color[0], 2 ) * 0.299 + pow( $color[1], 2 ) * 0.587 + pow( $color[2], 2 ) * 0.114 );
		} else {
			$brightness_level = 150;
		}

		return $brightness_level;
	}
}

if ( ! function_exists( 'fusion_hex2rgb' ) ) {
	/**
	 * Convert Hex Code to RGB.
	 *
	 * @param  string $hex Color Hex Code.
	 * @return array       RGB values.
	 */
	function fusion_hex2rgb( $hex ) {
		if ( false !== strpos( $hex,'rgb' ) ) {

			$rgb_part = strstr( $hex, '(' );
			$rgb_part = trim( $rgb_part, '(' );
			$rgb_part = rtrim( $rgb_part, ')' );
			$rgb_part = explode( ',', $rgb_part );

			$rgb = array( $rgb_part[0], $rgb_part[1], $rgb_part[2], $rgb_part[3] );

		} elseif ( 'transparent' === $hex ) {
			$rgb = array( '255', '255', '255', '0' );
		} else {

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
		}

		return $rgb; // Returns an array with the rgb values.
	}
}// End if().

if ( ! function_exists( 'fusion_render_first_featured_image_markup' ) ) {
	/**
	 * Render the full markup of the first featured image, incl. image wrapper and rollover.
	 *
	 * @param  string  $post_id                   ID of the current post.
	 * @param  string  $post_featured_image_size  Size of the featured image.
	 * @param  string  $post_permalink            Permalink of current post.
	 * @param  boolean $display_placeholder_image Set to true to show an image placeholder.
	 * @param  boolean $display_woo_price         Set to true to show WooCommerce prices.
	 * @param  boolean $display_woo_buttons       Set to true to show WooCommerce buttons.
	 * @param  boolean $display_post_categories   Set to yes to show post categories on rollover.
	 * @param  string  $display_post_title        Controls if the post title will be shown; "default": theme option setting; enable/disable otheriwse.
	 * @param  string  $type                      Type of element the featured image is for. "Related" for related posts is the only type in use so far.
	 * @param  string  $gallery_id                ID of a special gallery the rollover "zoom" link should be connected to for lightbox.
	 * @param  string  $display_rollover          yes|no|force_yes: no disables rollover; force_yes will force rollover even if the Theme Option is set to no.
	 * @param  bool    $display_woo_rating        Whether we want to display ratings or not.
	 * @param  aray    $attributes                Arry with attributes that will be added to the wrapper.
	 * @return string Full HTML markup of the first featured image.
	 */
	function fusion_render_first_featured_image_markup( $post_id, $post_featured_image_size = '', $post_permalink = '', $display_placeholder_image = false, $display_woo_price = false, $display_woo_buttons = false, $display_post_categories = 'default', $display_post_title = 'default', $type = '', $gallery_id = '', $display_rollover = 'yes', $display_woo_rating = false, $attributes = array() ) {
		// Add a class for fixed image size, to restrict the image rollovers to the image width.
		$image_size_class = ( 'full' !== $post_featured_image_size ) ? ' fusion-image-size-fixed' : '';
		$image_size_class = ( ( ! has_post_thumbnail( $post_id ) && get_post_meta( $post_id, 'pyre_video', true ) ) || ( is_home() && 'blog-large' === $post_featured_image_size ) ) ? '' : $image_size_class;

		ob_start();
		/* include wp_normalize_path( locate_template( 'templates/featured-image-first.php' ) ); */
		include wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/templates/featured-image-first.php' );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'avada_featured_images_lightbox' ) ) {
	/**
	 * The featured images lightbox.
	 *
	 * @param  int $post_id The post ID.
	 * @return string
	 */
	function avada_featured_images_lightbox( $post_id ) {

		global $fusion_settings;
		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}

		$html            = '';
		$video           = '';
		$featured_images = '';

		$video_url = get_post_meta( $post_id, 'pyre_video_url', true );

		if ( $video_url ) {
			$video = '<a href="' . $video_url . '" class="iLightbox[gallery' . $post_id . ']"></a>';
		}

		$i = 2;

		$posts_slideshow_number = $fusion_settings->get( 'posts_slideshow_number' );
		if ( ! is_numeric( $posts_slideshow_number ) ) {
			$posts_slideshow_number = 1;
		}

		while ( $i <= $posts_slideshow_number ) :

			$attachment_new_id = fusion_get_featured_image_id( 'featured-image-' . $i, get_post_type( $post_id ) );
			if ( $attachment_new_id ) {
				$attachment_image = wp_get_attachment_image_src( $attachment_new_id, 'full' );
				$full_image       = wp_get_attachment_image_src( $attachment_new_id, 'full' );
				$attachment_data  = wp_get_attachment_metadata( $attachment_new_id );
				$featured_images .= '<a href="' . $full_image[0] . '" data-rel="iLightbox[gallery' . $post_id . ']" title="' . get_post_field( 'post_title', $attachment_new_id ) . '" data-title="' . get_post_field( 'post_title', $attachment_new_id ) . '" data-caption="' . get_post_field( 'post_excerpt', $attachment_new_id ) . '"></a>';
			}
			$i++;

		endwhile;

		return $html . '<div class="fusion-portfolio-gallery-hidden">' . $video . $featured_images . '</div>';
	}
}// End if().

if ( ! function_exists( 'avada_render_rollover' ) ) {
	/**
	 * Output the image rollover
	 *
	 * @param  string  $post_id                    ID of the current post.
	 * @param  string  $post_permalink             Permalink of current post.
	 * @param  boolean $display_woo_price          Set to yes to show´woocommerce price tag for woo sliders.
	 * @param  boolean $display_woo_buttons        Set to yes to show the woocommerce "add to cart" and "show details" buttons.
	 * @param  string  $display_post_categories    Controls if the post categories will be shown; "deafult": theme option setting; enable/disable otheriwse.
	 * @param  string  $display_post_title         Controls if the post title will be shown; "deafult": theme option setting; enable/disable otheriwse.
	 * @param  string  $gallery_id                 ID of a special gallery the rollover "zoom" link should be connected to for lightbox.
	 * @param  bool    $display_woo_rating         Whether we want to display ratings or not.
	 * @return void
	 */
	function avada_render_rollover( $post_id, $post_permalink = '', $display_woo_price = false, $display_woo_buttons = false, $display_post_categories = 'default', $display_post_title = 'default', $gallery_id = '', $display_woo_rating = false ) {
		include wp_normalize_path( FUSION_LIBRARY_PATH . '/inc/templates/rollover.php' );
	}
}

add_action( 'avada_rollover', 'avada_render_rollover', 10, 8 );

if ( ! function_exists( 'fusion_get_post_content' ) ) {
	/**
	 * Return the post content, either excerpted or in full length.
	 *
	 * @param  string  $page_id        The id of the current page or post.
	 * @param  string  $excerpt        Can be either 'blog' (for main blog page), 'portfolio' (for portfolio page template) or 'yes' (for shortcodes).
	 * @param  integer $excerpt_length Length of the excerpts.
	 * @param  boolean $strip_html     Can be used by shortcodes for a custom strip html setting.
	 * @return string Post content.
	 **/
	function fusion_get_post_content( $page_id = '', $excerpt = 'blog', $excerpt_length = 55, $strip_html = false ) {

		$content_excerpted = false;

		// Main blog page.
		if ( 'blog' === $excerpt ) {

			// Check if the content should be excerpted.
			if ( 'excerpt' === strtolower( fusion_library()->get_option( 'content_length' ) ) ) {
				$content_excerpted = true;

				// Get the excerpt length.
				$excerpt_length = fusion_library()->get_option( 'excerpt_length_blog' );
			}

			// Check if HTML should be stripped from contant.
			if ( fusion_library()->get_option( 'strip_html_excerpt' ) ) {
				$strip_html = true;
			}
		} elseif ( 'portfolio' === $excerpt ) {
			// Check if the content should be excerpted.
			$portfolio_excerpt_length = fusion_get_portfolio_excerpt_length( $page_id );
			if ( false !== $portfolio_excerpt_length ) {
				$excerpt_length = $portfolio_excerpt_length;
				$content_excerpted = true;
			}

			// Check if HTML should be stripped from contant.
			if ( fusion_library()->get_option( 'portfolio_strip_html_excerpt' ) ) {
				$strip_html = true;
			}
		} elseif ( 'yes' === $excerpt ) {
			$content_excerpted = true;
		}

		// Sermon specific additional content.
		if ( 'wpfc_sermon' === get_post_type( get_the_ID() ) && class_exists( 'Avada' ) ) {
			return Avada()->sermon_manager->get_sermon_content( true );
		}

		// Return excerpted content.
		if ( $content_excerpted ) {
			return fusion_get_post_content_excerpt( $excerpt_length, $strip_html );
		}

		// Return full content.
		ob_start();
		the_content();
		return ob_get_clean();

	}
}// End if().

if ( ! function_exists( 'fusion_get_post_content_excerpt' ) ) {
	/**
	 * Do the actual custom excerpting for of post/page content.
	 *
	 * @param  string  $limit      Maximum number of words or chars to be displayed in excerpt.
	 * @param  boolean $strip_html Set to TRUE to strip HTML tags from excerpt.
	 * @return string 				The custom excerpt.
	 **/
	function fusion_get_post_content_excerpt( $limit = 285, $strip_html ) {
		global $more;

		// Init variables, cast to correct types.
		$content        = '';
		$read_more      = '';
		$custom_excerpt = false;
		$limit          = intval( $limit );
		$strip_html     = filter_var( $strip_html, FILTER_VALIDATE_BOOLEAN );

		// If excerpt length is set to 0, return empty.
		if ( 0 === $limit ) {
			return $content;
		}

		$post = get_post( get_the_ID() );

		// Filter to set the default [...] read more to something arbritary.
		$read_more_text = apply_filters( 'fusion_blog_read_more_excerpt', '&#91;...&#93;' );

		// If read more for excerpts is not disabled.
		if ( fusion_library()->get_option( 'disable_excerpts' ) ) {
			// Check if the read more [...] should link to single post.
			if ( fusion_library()->get_option( 'link_read_more' ) ) {
				$read_more = ' <a href="' . get_permalink( get_the_ID() ) . '">' . $read_more_text . '</a>';
			} else {
				$read_more = ' ' . $read_more_text;
			}
		}

		// Construct the content.
		// Posts having a custom excerpt.
		if ( has_excerpt() ) {
			// WooCommerce products should use short description field, which is a custom excerpt.
			if ( 'product' === $post->post_type ) {
				$content = do_shortcode( $post->post_excerpt );

				// Strip tags, if needed.
				if ( $strip_html ) {
					$content = wp_strip_all_tags( $content, '<p>' );
				}
			} else { // All other posts with custom excerpt.
				$content = '<p>' . do_shortcode( get_the_excerpt() ) . '</p>';
			}
		} else { // All other posts (with and without <!--more--> tag in the contents).
			// HTML tags should be stripped.
			if ( $strip_html ) {
				$content = wp_strip_all_tags( get_the_content( '{{read_more_placeholder}}' ), '<p>' );

				// Strip out all attributes.
				$content = preg_replace( '/<(\w+)[^>]*>/', '<$1>', $content );
				$content = str_replace( '{{read_more_placeholder}}', $read_more, $content );

			} else { // HTML tags remain in excerpt.
				$content = get_the_content( $read_more );
			}

			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", 'fusion_extract_shortcode_contents', $content );

			// <!--more--> tag is used in the post.
			if ( false !== strpos( $post->post_content, '<!--more-->' ) ) {
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]&gt;', $content );

				if ( $strip_html ) {
					$content = do_shortcode( $content );
				}
			}
		}// End if().

		// Limit the contents to the $limit length.
		if ( ! has_excerpt() || 'product' === $post->post_type ) {
			// Check if the excerpting should be char or word based.
			if ( 'Characters' === fusion_library()->get_option( 'excerpt_base' ) ) {
				$content = mb_substr( $content, 0, $limit );
				$content .= $read_more;
			} else { // Excerpting is word based.
				$content = explode( ' ', $content, $limit + 1 );
				if ( count( $content ) > $limit ) {
					array_pop( $content );
					$content = implode( ' ', $content );
					$content .= $read_more;

				} else {
					$content = implode( ' ', $content );
				}
			}

			if ( $strip_html ) {
				$content = '<p>' . $content . '</p>';
			} else {
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]&gt;', $content );
			}

			$content = do_shortcode( $content );
		}

		return $content;
	}
}// End if().

if ( ! function_exists( 'fusion_extract_shortcode_contents' ) ) {
	/**
	 * Extract text contents from all shortcodes for usage in excerpts.
	 *
	 * @param array $m The text.
	 * @return string The shortcode contents
	 */
	function fusion_extract_shortcode_contents( $m ) {

		global $shortcode_tags;

		// Setup the array of all registered shortcodes.
		$shortcodes = array_keys( $shortcode_tags );
		$no_space_shortcodes = array( 'fusion_dropcap' );
		$omitted_shortcodes  = array( 'fusion_code', 'fusion_imageframe', 'fusion_slide' );

		// Extract contents from all shortcodes recursively. @codingStandardsIgnoreLine
		if ( in_array( $m[2], $shortcodes ) && ! in_array( $m[2], $omitted_shortcodes ) ) {
			$pattern = get_shortcode_regex();
			// Add space to the excerpt by shortcode, except for those who should stick together, like dropcap.
			$space = ' ';
			// @codingStandardsIgnoreLine
			if ( in_array( $m[2], $no_space_shortcodes ) ) {
				$space = '';
			}
			$content = preg_replace_callback( "/$pattern/s", 'fusion_extract_shortcode_contents', rtrim( $m[5] ) . $space );

			return $content;
		}

		// Allow [[foo]] syntax for escaping a tag.
		if ( '[' === $m[1] && ']' === $m[6] ) {
			return substr( $m[0], 1, -1 );
		}

		return $m[1] . $m[6];
	}
}// End if().

/**
 * Returns the excerpt length for portfolio posts.
 *
 * @since 4.0.0
 * @param  string $page_id        The id of the current page or post.
 * @return string/boolean The excerpt length for the post; false if full content should be shown.
 **/
function fusion_get_portfolio_excerpt_length( $page_id = '' ) {
	$excerpt_length = false;

	if ( 'excerpt' === fusion_get_option( 'portfolio_archive_content_length', 'portfolio_content_length', $page_id ) ) {
		// Determine the correct excerpt length.
		if ( fusion_get_page_option( 'portfolio_excerpt', $page_id ) ) {
			$excerpt_length = fusion_get_page_option( 'portfolio_excerpt', $page_id );
		} else {
			$excerpt_length = fusion_library()->get_option( 'portfolio_archive_excerpt_length' );
		}
	} elseif ( ! $page_id && 'excerpt' === fusion_library()->get_option( 'portfolio_archive_content_length' ) ) {
		$excerpt_length = fusion_library()->get_option( 'portfolio_archive_excerpt_length' );
	}

	return $excerpt_length;

}

if ( ! function_exists( 'fusion_link_pages' ) ) {
	/**
	 * Pages links.
	 */
	function fusion_link_pages() {
		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'Avada' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
		) );
	}
}

if ( ! function_exists( 'fusion_cached_query' ) ) {
	/**
	 * Returns a cached query.
	 * If the query is not cached then it caches it and returns the result.
	 *
	 * @param string|array $args Same as in WP_Query.
	 * @return object
	 */
	function fusion_cached_query( $args ) {
		$query_id   = md5( maybe_serialize( $args ) );
		$query = wp_cache_get( $query_id, 'fusion_library' );
		if ( false === $query ) {
			$query = new WP_Query( $args );
			wp_cache_set( $query_id, $query, 'fusion_library' );
		}
		return $query;
	}
}

if ( ! function_exists( 'fusion_cached_get_posts' ) ) {
	/**
	 * Returns a cached query.
	 * If the query is not cached then it caches it and returns the result.
	 *
	 * @param string|array $args Same as in WP_Query.
	 * @return array
	 */
	function fusion_cached_get_posts( $args ) {
		$query = fusion_cached_query( $args );
		return $query->posts;
	}
}

if ( ! function_exists( 'fusion_get_user_locale' ) ) {
	/**
	 * Retrieves the locale of a user.
	 * If using WordPress 4.7+ uses get_user_locale.
	 * If using WordPress 4.7- uses get_locale.
	 * If the user has a locale set to a non-empty string then it will be
	 * returned. Otherwise it returns the locale of get_locale().
	 *
	 * @since 5.1
	 * @uses get_user_locale
	 * @uses get_locale
	 * @param int|WP_User $user_id User's ID or a WP_User object. Defaults to current user.
	 * @return string The locale of the user.
	 */
	function fusion_get_user_locale( $user_id = 0 ) {
		if ( function_exists( 'get_user_locale' ) ) {
			return get_user_locale( $user_id );
		}
		return get_locale();
	}
}

if ( ! function_exists( 'array_replace_recursive' ) ) {
	/**
	 * Fallback for PHP 5.2.
	 * The array_replace_recursive function was introduced in PHP 5.3.
	 *
	 * @since 1.0.0
	 * @param array $array  The 1st array.
	 * @param array $array1 The 2nd array.
	 * @return array.
	 */
	function array_replace_recursive( $array, $array1 ) {
		// Handle the arguments, merge one by one.
		$args  = func_get_args();
		$array = $args[0];
		if ( ! is_array( $array ) ) {
			return $array;
		}
		$args_count = count( $args );
		for ( $i = 1; $i < $args_count; $i++ ) {
			if ( is_array( $args[ $i ] ) ) {
				$array = fusion_array_replace_recursive_recurse( $array, $args[ $i ] );
			}
		}
		return $array;
	}
}

/**
 * Helper function for the array_replace_recursive fallback for PHP 5.2.
 *
 * @since 1.0.0
 * @param array $array The 1st array.
 * @param array $array1 The 2nd array.
 * @return array.
 */
function fusion_array_replace_recursive_recurse( $array, $array1 ) {
	foreach ( $array1 as $key => $value ) {
		// Create new key in $array, if it is empty or not an array.
		if ( ! isset( $array[ $key ] ) || ( isset( $array[ $key ] ) && ! is_array( $array[ $key ] ) ) ) {
			$array[ $key ] = array();
		}
		// Overwrite the value in the base array.
		if ( is_array( $value ) ) {
			$value = fusion_array_replace_recursive_recurse( $array[ $key ], $value );
		}
		$array[ $key ] = $value;
	}
	return $array;
}

if ( ! function_exists( 'fusion_get_featured_image_id' ) ) {
	/**
	 * Gets the ID of the featured image.
	 *
	 * @since 1.1.0
	 * @param int|string $image_id  The image ID.
	 * @param string     $post_type The post-type.
	 * @param int|string $post_id   The post-ID.
	 * @return int
	 */
	function fusion_get_featured_image_id( $image_id, $post_type, $post_id = null ) {
		return Fusion_Featured_Image::get_featured_image_id( $image_id, $post_type, $post_id );
	}
}
