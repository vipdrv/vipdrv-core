<?php
/**
 * Contains all framework specific functions that are not part of a separate class
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
if ( ! function_exists( 'fusion_get_related_posts' ) ) {
	/**
	 * Get related posts by category
	 *
	 * @param  integer $post_id      Current post id.
	 * @param  integer $number_posts Number of posts to fetch.
	 * @return object                Object with posts info.
	 */
	function fusion_get_related_posts( $post_id, $number_posts = -1 ) {

		$args = '';

		if ( 0 == $number_posts ) {
			$query = new WP_Query();
			return $query;
		}

		$args = wp_parse_args( $args, array(
			'category__in'        => wp_get_post_categories( $post_id ),
			'ignore_sticky_posts' => 0,
			'posts_per_page'      => $number_posts,
			'post__not_in'        => array( $post_id ),
		) );

		// If placeholder images are disabled,
		// add the _thumbnail_id meta key to the query to only retrieve posts with featured images.
		if ( ! Avada()->settings->get( 'featured_image_placeholder' ) ) {
			$args['meta_key'] = '_thumbnail_id';
		}

		return fusion_cached_query( $args );

	}
}

if ( ! function_exists( 'fusion_get_custom_posttype_related_posts' ) ) {
	/**
	 * Get related posts by a custom post type category taxonomy.
	 *
	 * @param  integer $post_id      Current post id.
	 * @param  integer $number_posts Number of posts to fetch.
	 * @param  string  $post_type    The custom post type that should be used.
	 * @return object                Object with posts info.
	 */
	function fusion_get_custom_posttype_related_posts( $post_id, $number_posts = 8, $post_type = 'avada_portfolio' ) {

		$query = new WP_Query();

		$args = '';

		if ( 0 == $number_posts ) {
			return $query;
		}

		$post_type = str_replace( 'avada_', '', $post_type );

		$item_cats = get_the_terms( $post_id, $post_type . '_category' );

		$item_array = array();
		if ( $item_cats ) {
			foreach ( $item_cats as $item_cat ) {
				$item_array[] = $item_cat->term_id;
			}
		}

		if ( ! empty( $item_array ) ) {
			$args = wp_parse_args( $args, array(
				'ignore_sticky_posts' => 0,
				'posts_per_page'      => $number_posts,
				'post__not_in'        => array( $post_id ),
				'post_type'           => 'avada_' . $post_type,
				'tax_query'           => array(
					array(
						'field'    => 'id',
						'taxonomy' => $post_type . '_category',
						'terms'    => $item_array,
					),
				),
			) );

			// If placeholder images are disabled, add the _thumbnail_id meta key to the query to only retrieve posts with featured images.
			if ( ! Avada()->settings->get( 'featured_image_placeholder' ) ) {
				$args['meta_key'] = '_thumbnail_id';
			}

			$query = fusion_cached_query( $args );

		}

		return $query;
	}
} // End if().

if ( ! function_exists( 'fusion_attr' ) ) {
	/**
	 * Function to apply attributes to HTML tags.
	 * Devs can override attr in a child theme by using the correct slug
	 *
	 * @param  string $slug         Slug to refer to the HTML tag.
	 * @param  array  $attributes   Attributes for HTML tag.
	 * @return string               Attributes in attr='value' format.
	 */
	function fusion_attr( $slug, $attributes = array() ) {

		$out  = '';
		$attr = apply_filters( "fusion_attr_{$slug}", $attributes );

		if ( empty( $attr ) ) {
			$attr['class'] = $slug;
		}

		foreach ( $attr as $name => $value ) {
			$out .= ' ' . esc_html( $name );
			if ( ! empty( $value ) ) {
				$out .= '="' . esc_attr( $value ) . '"';
			}
		}

		return trim( $out );

	}
}

if ( ! function_exists( 'fusion_pagination' ) ) {
	/**
	 * Number based pagination
	 *
	 * @param string  $pages           Maximum number of pages.
	 * @param integer $range           Our range.
	 * @param string  $current_query   The current query.
	 * @param bool    $infinite_scroll Whether we want infinite scroll or not.
	 * @return void
	 */
	function fusion_pagination( $pages = '', $range = 2, $current_query = '', $infinite_scroll = false ) {
		$showitems = ( $range * 2 ) + 1;

		if ( '' == $current_query ) {
			global $paged;
			if ( empty( $paged ) ) {
				$paged = 1;
			}
		} else {
			$paged = $current_query->query_vars['paged'];
		}

		if ( '' == $pages ) {
			if ( '' == $current_query ) {
				global $wp_query;
				$pages = $wp_query->max_num_pages;
				if ( ! $pages ) {
					$pages = 1;
				}
			} else {
				$pages = $current_query->max_num_pages;
			}
		}
		?>

		<?php if ( 1 != $pages ) : ?>
			<?php if ( $infinite_scroll || ( 'Pagination' != Avada()->settings->get( 'blog_pagination_type' ) && ( is_home() || is_search() || ( 'post' == get_post_type() && ( is_author() || is_archive() ) ) ) ) || ( 'pagination' !== Avada()->settings->get( 'portfolio_archive_pagination_type' ) && ( is_post_type_archive( 'avada_portfolio' ) || is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skills' )  || is_tax( 'portfolio_tags' ) ) ) ) : ?>
				<div class="fusion-infinite-scroll-trigger"></div>
				<div class='pagination infinite-scroll clearfix' style="display:none;">
			<?php else : ?>
				<div class='pagination clearfix'>
			<?php endif; ?>

			<?php if ( 1 < $paged ) : ?>
				<a class="pagination-prev" href="<?php echo esc_url_raw( get_pagenum_link( $paged - 1 ) ); ?>">
					<span class="page-prev"></span>
					<span class="page-text"><?php esc_html_e( 'Previous', 'Avada' ); ?></span>
				</a>
			<?php endif; ?>

			<?php for ( $i = 1; $i <= $pages; $i++ ) : ?>
				<?php if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) : ?>
					<?php if ( $paged == $i ) : ?>
						<span class="current"><?php echo (int) $i; ?></span>
					<?php else : ?>
						<a href="<?php echo esc_url_raw( get_pagenum_link( $i ) ); ?>" class="inactive"><?php echo (int) $i; ?></a>
					<?php endif; ?>
				<?php endif; ?>
			<?php endfor; ?>

			<?php if ( $paged < $pages ) : ?>
				<a class="pagination-next" href="<?php echo esc_url_raw( get_pagenum_link( $paged + 1 ) ); ?>">
					<span class="page-text"><?php esc_html_e( 'Next', 'Avada' ); ?></span>
					<span class="page-next"></span>
				</a>
			<?php endif; ?>

			</div>
			<?php
			// Needed for Theme check.
			ob_start();
			posts_nav_link();
			ob_get_clean();
			?>
		<?php endif;

	}
} // End if().

if ( ! function_exists( 'fusion_breadcrumbs' ) ) {
	/**
	 * Render the breadcrumbs with help of class-breadcrumbs.php.
	 *
	 * @return void
	 */
	function fusion_breadcrumbs() {
		$breadcrumbs = Avada_Breadcrumbs::get_instance();
		$breadcrumbs->get_breadcrumbs();
	}
}

if ( ! function_exists( 'fusion_strip_unit' ) ) {
	/**
	 * Strips the unit from a given value.
	 *
	 * @param  string $value The value with or without unit.
	 * @param  string $unit_to_strip The unit to be stripped.
	 * @return string	the value without a unit.
	 */
	function fusion_strip_unit( $value, $unit_to_strip = 'px' ) {
		$value_length = strlen( $value );
		$unit_length = strlen( $unit_to_strip );

		if ( $value_length > $unit_length &&
			 substr_compare( $value, $unit_to_strip, $unit_length * (-1), $unit_length ) === 0
		) {
			return substr( $value, 0, $value_length - $unit_length );
		} else {
			return $value;
		}
	}
}

add_filter( 'feed_link', 'fusion_feed_link', 1, 2 );
if ( ! function_exists( 'fusion_feed_link' ) ) {
	/**
	 * Replace default WP RSS feed link with theme option RSS feed link.
	 *
	 * @param  string $output Feed link.
	 * @param  string $feed   Feed type.
	 * @return string         Return modified feed link.
	 */
	function fusion_feed_link( $output, $feed ) {
		if ( Avada()->settings->get( 'rss_link' ) ) {
			$feed_url = Avada()->settings->get( 'rss_link' );

			$feed_array = array(
				'rss' => $feed_url,
				'rss2' => $feed_url,
				'atom' => $feed_url,
				'rdf' => $feed_url,
				'comments_rss2' => '',
			);
			$feed_array[ $feed ] = $feed_url;
			$output = $feed_array[ $feed ];
		}

		return $output;
	}
}


add_filter( 'the_excerpt_rss', 'fusion_feed_excerpt' );
if ( ! function_exists( 'fusion_feed_excerpt' ) ) {
	/**
	 * Modifies feed description, by extracting shortcode contents.
	 *
	 * @since  5.0.4
	 * @param  string $excerpt The post excerpt.
	 * @return string The modified post excerpt.
	 */
	function fusion_feed_excerpt( $excerpt ) {

		$excerpt = wp_strip_all_tags( fusion_get_post_content_excerpt( 55, true ) );

		return $excerpt;

	}
}

if ( ! function_exists( 'fusion_add_url_parameter' ) ) {
	/**
	 * Add paramater to current url.
	 *
	 * @param  string $url         URL to add param to.
	 * @param  string $param_name  Param name.
	 * @param  string $param_value Param value.
	 * @return array               params added to url data.
	 */
	function fusion_add_url_parameter( $url, $param_name, $param_value ) {
		 $url_data = wp_parse_url( $url );
		if ( ! isset( $url_data['query'] ) ) {
			$url_data['query'] = '';
		}

		$params = array();
		parse_str( $url_data['query'], $params );

		if ( is_array( $param_value ) ) {
			$param_value = $param_value[0];
		}

		$params[ $param_name ] = $param_value;

		if ( 'product_count' == $param_name ) {
			$params['paged'] = '1';
		}

		$url_data['query'] = http_build_query( $params );
		return fusion_build_url( $url_data );
	}
}

if ( ! function_exists( 'fusion_build_url' ) ) {
	/**
	 * Build final URL form $url_data returned from fusion_add_url_paramtere.
	 *
	 * @param  array $url_data  url data with custom params.
	 * @return string           fully formed url with custom params.
	 */
	function fusion_build_url( $url_data ) {
		$url = '';
		if ( isset( $url_data['host'] ) ) {
			$url .= $url_data['scheme'] . '://';
			if ( isset( $url_data['user'] ) ) {
				$url .= $url_data['user'];
				if ( isset( $url_data['pass'] ) ) {
					$url .= ':' . $url_data['pass'];
				}
				$url .= '@';
			}
			$url .= $url_data['host'];
			if ( isset( $url_data['port'] ) ) {
				$url .= ':' . $url_data['port'];
			}
		}

		if ( isset( $url_data['path'] ) ) {
			$url .= $url_data['path'];
		}

		if ( isset( $url_data['query'] ) ) {
			$url .= '?' . $url_data['query'];
		}

		if ( isset( $url_data['fragment'] ) ) {
			$url .= '#' . $url_data['fragment'];
		}

		return $url;
	}
} // End if().

if ( ! function_exists( 'fusion_color_luminance' ) ) {
	/**
	 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.
	 *
	 * @param string $hex     Colour as hexadecimal (with or without hash).
	 * @param float  $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() ).
	 * @return str            Lightened/Darkend colour as hexadecimal (with hash).
	 */
	function fusion_color_luminance( $hex, $percent ) {
		// Validate hex string.
		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
		$new_hex = '#';

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}

		// Convert to decimal and change luminosity.
		for ( $i = 0; $i < 3; $i++ ) {
			$dec = hexdec( substr( $hex, $i * 2, 2 ) );
			$dec = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}
}

if ( ! function_exists( 'fusion_adjust_brightness' ) ) {
	/**
	 * Adjusts brightness of the $hex and rgba colors.
	 *
	 * @param   string $color The hex or rgba value of a color.
	 * @param   int    $steps A value between -255 (darken) and 255 (lighten).
	 * @return  string        Returns hex color or rgba, depending on input.
	 */
	function fusion_adjust_brightness( $color, $steps ) {
		$color_obj = Fusion_Color::new_color( $color );
		$lightness = absint( round( $color_obj->lightness + ( $steps / 2.55 ) ) );
		$lightness = max( 0, min( $lightness, 100 ) );
		return $color_obj->get_new( 'lightness', $lightness )->to_css( $color_obj->mode );
	}
}

/**
 * Gets the brightness of a color.
 *
 * @param string $color The color.
 * @return int          Value between 0 and 255.
 */
function fusion_get_brightness( $color ) {
	$color_obj = Fusion_Color::new_color( $color );
	// Returns brightness value from 0 to 255.
	return intval( ( ( $color_obj->red * 299 ) + ( $color_obj->green * 587 ) + ( $color_obj->blue * 114 ) ) / 1000 );
}

if ( ! function_exists( 'fusion_rgb2hsl' ) ) {
	/**
	 * Convert RGB to HSL color model.
	 *
	 * @param  string $hex_color Color Hex Code of RGB color.
	 * @return array             HSL values.
	 */
	function fusion_rgb2hsl( $hex_color ) {

		$hex_color  = str_replace( '#', '', $hex_color );

		if ( strlen( $hex_color ) < 3 ) {
			str_pad( $hex_color, 3 - strlen( $hex_color ), '0' );
		}

		$add      = strlen( $hex_color ) == 6 ? 2 : 1;
		$aa       = 0;
		// @codingStandardsIgnoreLine
		$add_on   = 1 == $add ? ( $aa = 16 - 1 ) + 1 : 1;

		$red         = round( ( hexdec( substr( $hex_color, 0, $add ) ) * $add_on + $aa ) / 255, 6 );
		$green     = round( ( hexdec( substr( $hex_color, $add, $add ) ) * $add_on + $aa ) / 255, 6 );
		$blue       = round( ( hexdec( substr( $hex_color, ( $add + $add ) , $add ) ) * $add_on + $aa ) / 255, 6 );

		$hsl_color  = array(
			'hue' => 0,
			'sat' => 0,
			'lum' => 0,
		);

		$minimum     = min( $red, $green, $blue );
		$maximum     = max( $red, $green, $blue );

		$chroma   = $maximum - $minimum;

		$hsl_color['lum'] = ( $minimum + $maximum ) / 2;

		if ( 0 == $chroma ) {
			$hsl_color['lum'] = round( $hsl_color['lum'] * 100, 0 );

			return $hsl_color;
		}

		$range = $chroma * 6;

		$hsl_color['sat'] = $hsl_color['lum'] <= 0.5 ? $chroma / ( $hsl_color['lum'] * 2 ) : $chroma / ( 2 - ( $hsl_color['lum'] * 2 ) );

		if ( $red <= 0.004 ||
			$green <= 0.004 ||
			$blue <= 0.004
		) {
			$hsl_color['sat'] = 1;
		}

		if ( $maximum == $red ) {
			$hsl_color['hue'] = round( ( $blue > $green ? 1 - ( abs( $green - $blue ) / $range ) : ( $green - $blue ) / $range ) * 255, 0 );
		} elseif ( $maximum == $green ) {
			$hsl_color['hue'] = round( ( $red > $blue ? abs( 1 - ( 4 / 3 ) + ( abs( $blue - $red ) / $range ) ) : ( 1 / 3 ) + ( $blue - $red ) / $range ) * 255, 0 );
		} else {
			$hsl_color['hue'] = round( ( $green < $red ? 1 - 2 / 3 + abs( $red - $green ) / $range : 2 / 3 + ( $red - $green ) / $range ) * 255, 0 );
		}

		$hsl_color['sat'] = round( $hsl_color['sat'] * 100, 0 );
		$hsl_color['lum']  = round( $hsl_color['lum'] * 100, 0 );

		return $hsl_color;
	}
} // End if().

if ( ! function_exists( 'fusion_compress_css' ) ) {
	/**
	 * Compress CSS
	 *
	 * @param  string $minify CSS to compress.
	 * @return string         Compressed CSS.
	 */
	function fusion_compress_css( $minify ) {
		// Remove comments.
		$minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );

		// Remove tabs, spaces, newlines, etc.
		return str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $minify );
	}
}

if ( ! function_exists( 'fusion_get_attachment_data_by_url' ) ) {
	/**
	 * Get attachment data by URL.
	 *
	 * @param  string $image_url  The Image URL.
	 * @param  string $logo_field The logo field.
	 * @return array              Image Details.
	 */
	function fusion_get_attachment_data_by_url( $image_url, $logo_field = '' ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		if ( $attachment ) {
			return wp_get_attachment_metadata( $attachment[0] );
		}
		// Import the image to media library.
		$import_image = fusion_import_to_media_library( $image_url, $logo_field );
		if ( $import_image ) {
			return wp_get_attachment_metadata( $import_image );
		}
		return false;
	}
}

if ( ! function_exists( 'fusion_import_to_media_library' ) ) {
	/**
	 * Imports a file to the media library.
	 *
	 * @param string $url The file URL.
	 * @param string $theme_option If we're doing this for a theme option,
	 *                             specify which option that is to properly save the data.
	 */
	function fusion_import_to_media_library( $url, $theme_option = '' ) {

		// Gives us access to the download_url() and wp_handle_sideload() functions.
		require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );

		$timeout_seconds = 30;

		// Download file to temp dir.
		$temp_file = download_url( $url, $timeout_seconds );

		if ( ! is_wp_error( $temp_file ) ) {
			// Array based on $_FILE as seen in PHP file uploads.
			$file = array(
				'name' => basename( $url ),
				'type' => 'image/png',
				'tmp_name' => $temp_file,
				'error' => 0,
				'size' => filesize( $temp_file ),
			);

			$overrides = array(
				// Tells WordPress to not look for the POST form
				// fields that would normally be present, default is true,
				// we downloaded the file from a remote server, so there
				// will be no form fields.
				'test_form' => false,

				// Setting this to false lets WordPress allow empty files, not recommended.
				'test_size' => true,

				// A properly uploaded file will pass this test.
				// There should be no reason to override this one.
				'test_upload' => true,
			);

			// Move the temporary file into the uploads directory.
			$results = wp_handle_sideload( $file, $overrides );

			if ( ! empty( $results['error'] ) ) {
				return false;
			}
			$attachment = array(
				'guid'           => $results['url'],
				'post_mime_type' => $results['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $results['file'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			// Insert the attachment.
			$attach_id = wp_insert_attachment( $attachment, $results['file'] );

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attach_data = wp_generate_attachment_metadata( $attach_id, $results['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			if ( $theme_option ) {
				Avada()->settings->set( $theme_option, $results['url'] );
			}

			return $attach_id;
		} // End if().
		return false;
	}
} // End if().
/* Omit closing PHP tag to avoid "Headers already sent" issues. */
