<?php
/**
 * Dynamic-CSS helpers.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The Helpers object.
 */
class Fusion_Dynamic_CSS_Helpers {

	/**
	 * An array for dynamic css.
	 *
	 * @access public
	 * @var array
	 */
	public static $dynamic_css = array();

	/**
	 * Add to Dynamic CSS.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $css The existing CSS.
	 */
	public function add_css( $css ) {

		self::$dynamic_css = array_merge_recursive( self::$dynamic_css, $css );
	}

	/**
	 * Helper function.
	 * Merge and combine the CSS elements.
	 *
	 * @access public
	 * @param  string|array $elements An array of our elements.
	 *                                If we use a string then it is directly returned.
	 * @return  string
	 */
	public function implode( $elements = array() ) {

		if ( ! is_array( $elements ) ) {
			return $elements;
		}

		// Make sure our values are unique.
		$elements = array_unique( $elements );
		// Sort elements alphabetically.
		// This way all duplicate items will be merged in the final CSS array.
		sort( $elements );

		// Implode items and return the value.
		return implode( ',', $elements );

	}

	/**
	 * Maps elements from dynamic css to the selector.
	 *
	 * @access public
	 * @param  array  $elements The elements.
	 * @param  string $selector_after The selector after the element.
	 * @param  string $selector_before The selector before the element.
	 * @return  array
	 */
	public function map_selector( $elements, $selector_after = '', $selector_before = '' ) {
		$array = array();
		foreach ( $elements as $element ) {
			$array[] = $selector_before . $element . $selector_after;
		}
		return $array;
	}

	/**
	 * Get the array of dynamically-generated CSS and convert it to a string.
	 * Parses the array and adds quotation marks to font families and prefixes for browser-support.
	 *
	 * @access public
	 * @param  array $css The CSS array.
	 * @return  string
	 */
	public function parser( $css ) {
		// Prefixes.
		foreach ( $css as $media_query => $elements ) {
			foreach ( $elements as $element => $style_array ) {
				foreach ( $style_array as $property => $value ) {
					// Font family.
					if ( 'font-family' === $property ) {
						if ( false === strpos( $value, ',' ) && false === strpos( $value, "'" ) && false === strpos( $value, '"' ) ) {
							$value = "'" . $value . "'";
						}
						$css[ $media_query ][ $element ]['font-family'] = $value;
					} // End if().
					elseif ( 'border-radius' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-border-radius'] = $value;
					} // box-shadow.
					elseif ( 'box-shadow' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-box-shadow'] = $value;
						$css[ $media_query ][ $element ]['-moz-box-shadow']    = $value;
					} // box-sizing.
					elseif ( 'box-sizing' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-box-sizing'] = $value;
						$css[ $media_query ][ $element ]['-moz-box-sizing']    = $value;
					} // text-shadow.
					elseif ( 'text-shadow' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-text-shadow'] = $value;
						$css[ $media_query ][ $element ]['-moz-text-shadow']    = $value;
					} // transform.
					elseif ( 'transform' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-transform'] = $value;
						$css[ $media_query ][ $element ]['-moz-transform']    = $value;
						$css[ $media_query ][ $element ]['-ms-transform']     = $value;
						$css[ $media_query ][ $element ]['-o-transform']      = $value;
					} // background-size.
					elseif ( 'background-size' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-background-size'] = $value;
						$css[ $media_query ][ $element ]['-moz-background-size']    = $value;
						$css[ $media_query ][ $element ]['-ms-background-size']     = $value;
						$css[ $media_query ][ $element ]['-o-background-size']      = $value;
					} // transition.
					elseif ( 'transition' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-transition'] = $value;
						$css[ $media_query ][ $element ]['-moz-transition']    = $value;
						$css[ $media_query ][ $element ]['-ms-transition']     = $value;
						$css[ $media_query ][ $element ]['-o-transition']      = $value;
					} // transition-property.
					elseif ( 'transition-property' === $property ) {
						$css[ $media_query ][ $element ]['-webkit-transition-property'] = $value;
						$css[ $media_query ][ $element ]['-moz-transition-property']    = $value;
						$css[ $media_query ][ $element ]['-ms-transition-property']     = $value;
						$css[ $media_query ][ $element ]['-o-transition-property']      = $value;
					} // linear-gradient.
					elseif ( is_array( $value ) ) {
						foreach ( $value as $subvalue ) {
							if ( false !== strpos( $subvalue, 'linear-gradient' ) ) {
								$css[ $media_query ][ $element ][ $property ][] = '-webkit-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-moz-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-ms-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-o-' . $subvalue;
							} // End if().
							elseif ( 0 === stripos( $subvalue, 'calc' ) ) {
								$css[ $media_query ][ $element ][ $property ][] = '-webkit-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-moz-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-ms-' . $subvalue;
								$css[ $media_query ][ $element ][ $property ][] = '-o-' . $subvalue;
							}
						}
					}
				}// End foreach().
			}// End foreach().
		}// End foreach().

		/**
		 * Process the array of CSS properties and produce the final CSS.
		 */
		$final_css = '';
		foreach ( $css as $media_query => $styles ) {

			$final_css .= ( 'global' !== $media_query ) ? $media_query . '{' : '';

			foreach ( $styles as $style => $style_array ) {
				$final_css .= $style . '{';
				foreach ( $style_array as $property => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $sub_value ) {
							$final_css .= $property . ':' . $sub_value . ';';
						}
					} else {
						$final_css .= $property . ':' . $value . ';';
					}
				}
				$final_css .= '}';
			}

			$final_css .= ( 'global' !== $media_query ) ? '}' : '';

		}

		return apply_filters( 'fusion_dynamic_css', $final_css );

	}

	/**
	 * Returns the dynamic CSS.
	 * If possible, it also caches the CSS using WordPress transients.
	 *
	 * @access public
	 * @return  string  the dynamically-generated CSS.
	 */
	public function dynamic_css_cached() {

		// Get the page ID.
		$fusion_library  = Fusion::get_instance();
		$settings        = Fusion_Settings::get_instance();
		$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
		$mode            = $dynamic_css_obj->get_mode();

		$cache = false;

		// Only cache if css_cache_method is set to 'db'.
		if ( 'inline' === $mode ) {
			$cache = true;
		}

		// If WP_DEBUG set to true, caching is off in TO or Avada is not active if being used (e.g. WP Touch), then do not cache.
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || 'off' === $settings->get( 'css_cache_method' ) || $dynamic_css_obj->is_cache_disabled() ) {
			$cache = false;
		}

		if ( $cache ) {
			// If we're compiling to file, and this is a fallback, 1hr caching, 1 day for db mode.
			$cache_time = ( 'db' === $settings->get( 'css_cache_method' ) ) ? DAY_IN_SECONDS : HOUR_IN_SECONDS;

			$c_page_id      = $fusion_library->get_page_id();
			$page_id        = ( $c_page_id > 0 ) ? $c_page_id : 'global';

			// If WooCommerce is active and we are on archive, use global CSS not shop page, which is return by get_page_id.
			if ( class_exists( 'WooCommerce' ) && ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
				$page_id = 'global';
			}
			$transient_name = 'fusion_dynamic_css_' . $page_id;

			// Check if the dynamic CSS needs updating.
			// If it does, then calculate the CSS and then update the transient.
			if ( $dynamic_css_obj->needs_update() ) {

				// Calculate the dynamic CSS.
				$dynamic_css_array = apply_filters( 'fusion_dynamic_css_array', self::$dynamic_css );
				$dynamic_css       = '/********* Compiled on ' . date( DATE_ATOM ) . " - Do not edit *********/\n" . $this->parser( $dynamic_css_array );

				// Set the transient for an hour.
				set_transient( $transient_name, $dynamic_css, $cache_time );

				$option  = get_option( 'fusion_dynamic_css_posts', array() );
				$option[ $page_id ] = true;
				update_option( 'fusion_dynamic_css_posts', $option );
			} else {

				// Check if the transient exists.
				// If it does not exist, then generate the CSS and update the transient.
				// @codingStandardsIgnoreLine
				if ( false === ( $dynamic_css = get_transient( $transient_name ) ) ) {

					// Calculate the dynamic CSS.
					$dynamic_css_array = apply_filters( 'fusion_dynamic_css_array', self::$dynamic_css );
					$dynamic_css       = $this->parser( $dynamic_css_array );

					// Set the transient for an hour.
					set_transient( $transient_name, $dynamic_css, $cache_time );
				}
			}
		} else {
			// Calculate the dynamic CSS.
			$dynamic_css_array = apply_filters( 'fusion_dynamic_css_array', self::$dynamic_css );
			$dynamic_css       = '/********* Compiled on ' . date( DATE_ATOM ) . " - Do not edit *********/\n" . $this->parser( $dynamic_css_array );
		}// End if().

		return apply_filters( 'fusion_dynamic_css_cached', $dynamic_css );

	}

	/**
	 * Combines google-fonts & fallback fonts.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param array $typo_array The typography setting as saved in the db.
	 * @return string
	 */
	public function combined_font_family( $typo_array = array() ) {

		$google_font    = isset( $typo_array['font-family'] ) ? $typo_array['font-family'] : false;
		$fallback_fonts = isset( $typo_array['font-backup'] ) ? $typo_array['font-backup'] : false;

		// Exit early by returning the fallback font
		// in case no google-font is defined.
		if ( false === $google_font ) {
			return $this->format_font_family( $fallback_fonts );
		}

		// Exit early returning the google font
		// in case no fallback font is defined.
		if ( false === $fallback_fonts || '' === $fallback_fonts ) {
			return $this->format_font_family( $google_font );
		}

		// Exit early returning the google (primary) font
		// in case google font is set to use standard font and it's the same as fallback font.
		if ( $google_font === $fallback_fonts ) {
			return $this->format_font_family( $google_font );
		}

		// Return the sum of the font-families properly formatted.
		return $this->format_font_family( $google_font . ', ' . $fallback_fonts );

	}

	/**
	 * Formats the font-family for CSS use.
	 *
	 * @access public
	 * @since 5.0.3
	 * @param string $family The font-family to use.
	 * @return string
	 */
	public function format_font_family( $family ) {

		// Make sure nothing malicious comes through.
		$family = wp_strip_all_tags( $family );

		// Remove quotes and double-quotes.
		// We'll add these back later if they are indeed needed.
		$family = str_replace( array( '"', "'" ), '', $family );

		if ( empty( $family ) ) {
			return '';
		}

		$families = array();
		// If multiple font-families, make sure each-one of them is sanitized separately.
		if ( false !== strpos( $family, ',' ) ) {
			$families = explode( ',', $family );
			foreach ( $families as $key => $value ) {
				$value = trim( $value );
				// Add quotes if needed.
				if ( false !== strpos( $value, ' ' ) ) {
					$value = '"' . $value . '"';
				}
				$families[ $key ] = $value;
			}
			$family = implode( ', ', $families );
		} else {
			// Add quotes if needed.
			if ( false !== strpos( $family, ' ' ) ) {
				$family = '"' . $family . '"';
			}
		}
		return $family;
	}
}
