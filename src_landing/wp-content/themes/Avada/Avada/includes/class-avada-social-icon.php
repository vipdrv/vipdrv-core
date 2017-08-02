<?php
/**
 * Single social-icon handler.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Single social-icon handler.
 *
 * @since 4.0.0
 */
class Avada_Social_Icon {

	/**
	 * Array of our arguments for this icon.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $args = array();

	/**
	 * The prefix that we'll be using for all our icon classes.
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $iconfont_prefix = 'fusion-icon-';

	/**
	 * Creates the markup for a single icon.
	 *
	 * @static
	 * @access public
	 * @param array $args The arguments array.
	 * @return string
	 */
	public static function get_markup( $args ) {

		$icon_options = array(
			'class' => '',
			'style' => '',
		);
		if ( isset( $args['social_network'] ) ) {
			$icon_options['social_network'] = $args['social_network'];
		} elseif ( isset( $args['icon'] ) ) {
			$icon_options['social_network'] = $args['icon'];
		}
		$icon_options['social_link'] = '';
		if ( isset( $args['social_link'] ) ) {
			$icon_options['social_link'] = $args['social_link'];
		} elseif ( isset( $args['url'] ) ) {
			$icon_options['social_link'] = $args['url'];
		}
		if ( isset( $args['icon_color'] ) ) {
			$icon_options['icon_color'] = $args['icon_color'];
		}
		if ( isset( $args['box_color'] ) ) {
			$icon_options['box_color'] = $args['box_color'];
		}
		$icon_options['last'] = ( isset( $args['last'] ) ) ? $args['last'] : false;

		$icon_padding = Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_boxed_padding' ) );
		$custom = '';
		$is_custom_icon = ( isset( $args['custom_source'] ) && isset( $args['custom_title'] ) ) ? true : false;
		// This is a custom icon.
		if ( $is_custom_icon ) {
			// Get the position.
			$position = ( isset( self::$args['position'] ) && 'footer' === self::$args['position'] ) ? 'footer' : 'header';
			// Get the line-height.
			$line_height_option = ( 'header' == $position ) ? 'header_social_links_font_size' : 'footer_social_links_font_size';
			$line_height        = Fusion_Sanitize::size( Avada()->settings->get( $line_height_option ) );
			// Get the padding.
			$padding_option = ( 'header' === $position ) ? 'header_social_links_boxed_padding' : 'footer_social_links_boxed_padding';
			$icon_padding   = Fusion_Sanitize::size( Avada()->settings->get( $padding_option ) );
			// Calculate the max-height for the custom icon.
			$max_height = ( self::$args['icon_boxed'] ) ? 'calc(' . $line_height . ' + (2 * ' . $icon_padding . ') + 2px)' : $line_height;

			$custom = '<img src="' . $args['custom_source'] . '" style="width:auto;max-height:' . $max_height . ';" alt="' . $args['custom_title'] . '" />';

		}

		if ( 'custom' === substr( $icon_options['social_network'], 0, 7 ) ) {
			$icon_options['class'] .= 'custom ';
			$tooltip = str_replace( 'custom', '', $args['custom_title'] );
			// $icon_options['social_network'] = strtolower( $tooltip );
		} else {
			$tooltip = ucfirst( $icon_options['social_network'] );
		}

		$icon_options['social_network'] = ( 'email' == $icon_options['social_network'] ) ? 'mail' : $icon_options['social_network'];

		$icon_options['class'] .= 'fusion-social-network-icon fusion-tooltip fusion-' . $icon_options['social_network'] . ' ' . self::$iconfont_prefix . $icon_options['social_network'];
		$icon_options['class'] .= ( $args['last'] ) ? ' fusion-last-social-icon' : '';

		$icon_options['href'] = $icon_options['social_link'];

		if ( 'googleplus' == $icon_options['social_network'] && false !== strpos( $icon_options['social_link'], 'share?' ) ) {
			$icon_options['onclick'] = 'javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;';
		}

		if ( self::$args['linktarget'] ) {
			$icon_options['target'] = '_blank';
			$icon_options['rel'] = 'noopener noreferrer';
		}

		if ( 'mail' == $icon_options['social_network'] ) {

			if ( 'http' === substr( $icon_options['social_link'], 0, 4 ) ) {
				$icon_options['href'] = $icon_options['social_link'];
			} else {
				if ( false !== strpos( $icon_options['social_link'], 'body=' ) ) {
					$icon_options['href'] = 'mailto:' . str_replace( 'mailto:', '', $icon_options['social_link'] );
				} else {
					$icon_options['href']   = 'mailto:' . antispambot( str_replace( 'mailto:', '', $icon_options['social_link'] ) );
				}
			}

			$icon_options['target'] = '_self';
		}

		if ( Avada()->settings->get( 'nofollow_social_links' ) ) {
			$icon_options['rel'] = 'nofollow';
		}

		if ( $args['icon_color'] ) {
			$icon_options['style'] .= 'color:' . $args['icon_color'] . ';';
		}

		if ( $is_custom_icon ) {
			// We need a top offset for boxed mode, based on the padding.
			$top_offset = ( self::$args['icon_boxed'] ) ? 'top:-' . $icon_padding . ';' : '';
			$icon_options['style'] .= 'position:relative;' . $top_offset;
		}

		if ( ! $is_custom_icon && self::$args['icon_boxed'] && $args['box_color'] && ! is_array( $args['box_color'] ) ) {
			$icon_options['style'] .= 'background-color:' . $args['box_color'] . ';border-color:' . $args['box_color'] . ';';
		}

		if ( ! $is_custom_icon && self::$args['icon_boxed'] && ( self::$args['icon_boxed_radius'] || '0' === self::$args['icon_boxed_radius'] ) ) {
			self::$args['icon_boxed_radius'] = ( 'round' == self::$args['icon_boxed_radius'] ) ? '50%' : self::$args['icon_boxed_radius'];
			$icon_options['style'] .= 'border-radius:' . self::$args['icon_boxed_radius'] . ';';
		}

		if ( 'none' != strtolower( self::$args['tooltip_placement'] ) ) {
			$icon_options['data-placement'] = strtolower( self::$args['tooltip_placement'] );
			if ( 'Googleplus' == $tooltip ) {
				$tooltip = 'Google+';
			} elseif ( 'Youtube' === $tooltip ) {
				$tooltip = 'YouTube';
			}

			$icon_options['data-title']  = $tooltip;
			$icon_options['data-toggle'] = 'tooltip';
		}

		$icon_options['title'] = $tooltip;

		$icon_options = apply_filters( 'fusion_attr_social-icons-class-icon', $icon_options );

		$properties = '';
		$not_allowed_attributes = array( 'last', 'box_color', 'icon_color', 'social_link', 'social_network' );
		foreach ( $icon_options as $name => $value ) {
			if ( ! in_array( $name, $not_allowed_attributes ) ) {
				$properties .= ! empty( $value ) ? ' ' . esc_html( $name ) . '="' . esc_attr( $value ) . '"' : esc_html( " {$name}" );
			}
		}

		return '<a ' . $properties . '><span class="screen-reader-text">' . $tooltip . '</span>' . $custom . '</a>';

	}
}
