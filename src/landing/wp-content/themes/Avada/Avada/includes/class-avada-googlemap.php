<?php
/**
 * Handles google maps in Avada.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8.5
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles google maps in Avada.
 */
class Avada_GoogleMap {

	/**
	 * The Map ID.
	 *
	 * @access private
	 * @var string
	 */
	private $map_id;

	/**
	 * Arguments array.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $args;

	/**
	 * Initiate the shortcode
	 */
	public function __construct() {

		add_filter( 'fusion_attr_avada-google-map', array( $this, 'attr' ) );
		add_action( 'wp_ajax_fusion_cache_map', array( $this, 'fusion_cache_map' ) );
		add_action( 'wp_ajax_nopriv_fusion_cache_map', array( $this, 'fusion_cache_map' ) );
	}

	/**
	 * Function to get the default shortcode param values applied.
	 *
	 * @param  array $defaults  Array with user set param values.
	 * @param  array $args      Array with user set param values.
	 * @return array
	 */
	public static function set_shortcode_defaults( $defaults, $args ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			$args = array();
		}

		$args = shortcode_atts( $defaults, $args );

		foreach ( $args as $key => $value ) {
			if ( '' == $value ) {
				$args[ $key ] = $defaults[ $key ];
			}
		}

		return $args;

	}

	/**
	 * Calculates the brightness of a given color.
	 *
	 * @static
	 * @access  public
	 * @param  string $color The color.
	 * @return  int|float
	 */
	public static function calc_color_brightness( $color ) {

		if ( in_array( strtolower( $color ), array( 'black', 'navy', 'purple', 'maroon', 'indigo', 'darkslategray', 'darkslateblue', 'darkolivegreen', 'darkgreen', 'darkblue' ) ) ) {
			$brightness_level = 0;
		} elseif ( 0 === strpos( $color, '#' ) ) {
			$color = fusion_hex2rgb( $color );
			$brightness_level = sqrt( pow( $color[0], 2 ) * 0.299 + pow( $color[1], 2 ) * 0.587 + pow( $color[2], 2 ) * 0.114 );
		} else {
			$brightness_level = 150;
		}

		return $brightness_level;
	}

	/**
	 * Function to apply attributes to HTML tags.
	 * Devs can override attributes in a child theme by using the correct slug
	 *
	 * @param  string $slug	   Slug to refer to the HTML tag.
	 * @param  array  $attributes Attributes for HTML tag.
	 * @return string
	 */
	public static function attributes( $slug, $attributes = array() ) {

		$out  = '';
		$attr = apply_filters( "fusion_attr_{$slug}", $attributes );

		if ( empty( $attr ) ) {
			$attr['class'] = $slug;
		}

		foreach ( $attr as $name => $value ) {
			if ( empty( $value ) ) {
				$out .= ' ' . esc_html( $name );
			} else {
				$out .= ' ' . esc_html( $name ) . '="' . esc_attr( $value ) . '"';
			}
		}

		return trim( $out );

	} // end attr().

	/**
	 * Render the shortcode.
	 *
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string		   HTML output.
	 */
	function render_map( $args, $content = '' ) {

		if ( ! Avada()->settings->get( 'status_gmap' ) ) {
			return '';
		}

		$defaults = $this->set_shortcode_defaults(
			array(
				'class'                    => '',
				'id'                       => '',
				'animation'                => 'no',
				'address'                  => '',
				'address_pin'              => 'yes',
				'height'                   => '300px',
				'icon'                     => '',
				'infobox'                  => '',
				'infobox_background_color' => '',
				'infobox_content'		   => '',
				'infobox_text_color'       => '',
				'map_style'                => '',
				'overlay_color'            => '',
				'popup'                    => 'yes',
				'scale'                    => 'yes',
				'scrollwheel'              => 'yes',
				'type'                     => 'roadmap',
				'width'                    => '100%',
				'zoom'                     => '14',
				'zoom_pancontrol'          => 'yes',
			), $args
		);

		extract( $defaults );

		self::$args = $defaults;

		$html = '';

		if ( $address ) {
			$addresses       = explode( '|', $address );
			$infobox_content = ( ! in_array( $map_style, array( 'default', 'theme' ) ) && 'default' !== $infobox ) ? html_entity_decode( $infobox_content ) : '' ;

			$infobox_content_array = ( $infobox_content ) ? explode( '|', $infobox_content ) : '';
			$icon_array            = ( $icon && 'default' !== $infobox ) ? explode( '|', $icon ) : '';

			if ( ! empty( $addresses ) ) {
				self::$args['address'] = $addresses;
			}

			$num_of_addresses = count( $addresses );

			if ( $icon && false === strpos( $icon, '|' )  && 'default' !== $infobox ) {
				for ( $i = 0; $i < $num_of_addresses; $i++ ) {
					$icon_array[ $i ] = $icon;
				}
			}

			if ( 'theme' == $map_style ) {

				$map_style                = 'custom';
				$icon                     = 'theme';
				$animation                = 'yes';
				$infobox                  = 'custom';
				$infobox_background_color = fusion_hex2rgb( Avada()->settings->get( 'primary_color' ) );
				$infobox_background_color = 'rgba(' . $infobox_background_color[0] . ', ' . $infobox_background_color[1] . ', ' . $infobox_background_color[2] . ', 0.8)';
				$overlay_color            = Avada()->settings->get( 'primary_color' );
				$brightness_level         = $this->calc_color_brightness( Avada()->settings->get( 'primary_color' ) );
				$infobox_text_color       = ( $brightness_level > 140 ) ? '#fff' : '#747474';
			} elseif ( 'custom' == $map_style ) {
				$overlay_color = Avada()->settings->get( 'map_overlay_color' );
				$color_obj = Fusion_Color::new_color( $overlay_color );
				if ( '0' == $color_obj->alpha ) {
					$overlay_color = '';
				} elseif ( 1 > $color_obj->alpha ) {
					$lighter = $color_obj->get_new( 'lightness', $color->lightness + absint( 100 * ( 1 - $color_obj->alpha ) ) );
					$overlay_color = $lighter->to_css( 'hex' );
				}
			}

			if ( 'theme' == $icon && 'custom' == $map_style ) {
				for ( $i = 0; $i < $num_of_addresses; $i++ ) {
					$icon_array[ $i ] = Avada::$template_dir_url . '/assets/images/avada_map_marker.png';
				}
			}

			if ( wp_script_is( 'google-maps-api', 'registered' ) ) {
				wp_print_scripts( 'google-maps-api' );
				if ( wp_script_is( 'google-maps-infobox', 'registered' ) ) {
					wp_print_scripts( 'google-maps-infobox' );
				}
			}

			foreach ( self::$args['address'] as $add ) {
				$add     = trim( $add );
				$add_arr = explode( "\n", $add );
				$add_arr = array_filter( $add_arr, 'trim' );
				$add     = implode( '<br/>', $add_arr );
				$add     = str_replace( "\r", '', $add );
				$add     = str_replace( "\n", '', $add );

				$coordinates[]['address'] = $add;
			}

			if ( ! is_array( $coordinates ) ) {
				return;
			}

			for ( $i = 0; $i < $num_of_addresses; $i++ ) {
				if ( 0 === strpos( self::$args['address'][ $i ], 'latlng=' ) ) {
					self::$args['address'][ $i ] = $coordinates[ $i ]['address'];
				}
			}

			if ( is_array( $infobox_content_array ) && ! empty( $infobox_content_array ) ) {
				for ( $i = 0; $i < $num_of_addresses; $i++ ) {
					if ( ! array_key_exists( $i, $infobox_content_array ) ) {
						$infobox_content_array[ $i ] = self::$args['address'][ $i ];
					}
				}
				self::$args['infobox_content'] = $infobox_content_array;
			} else {
				self::$args['infobox_content'] = self::$args['address'];
			}

			$cached_addresses = get_option( 'fusion_map_addresses' );

			foreach ( self::$args['address'] as $key => $address ) {
				$json_addresses[] = array(
					'address'         => $address,
					'infobox_content' => self::$args['infobox_content'][ $key ],
				);

				if ( isset( $icon_array ) && is_array( $icon_array ) ) {
					$json_addresses[ $key ]['marker'] = $icon_array[ $key ];
				}

				if ( false !== strpos( $address, strtolower( 'latlng=' ) ) ) {
					$json_addresses[ $key ]['address']     = str_replace( 'latlng=', '', $address );
					$lat_lng                               = explode( ',', $json_addresses[ $key ]['address'] );
					$json_addresses[ $key ]['coordinates'] = true;
					$json_addresses[ $key ]['latitude']    = $lat_lng[0];
					$json_addresses[ $key ]['longitude']   = $lat_lng[1];
					$json_addresses[ $key ]['cache']       = false;

					if ( false !== strpos( self::$args['infobox_content'][ $key ], strtolower( 'latlng=' ) ) ) {
						$json_addresses[ $key ]['infobox_content'] = '';
					}

					if ( isset( $cached_addresses[ trim( $json_addresses[ $key ]['latitude'] . ',' . $json_addresses[ $key ]['longitude'] ) ] ) ) {
						$json_addresses[ $key ]['geocoded_address'] = $cached_addresses[ trim( $json_addresses[ $key ]['latitude'] . ',' . $json_addresses[ $key ]['longitude'] ) ]['address'];
						$json_addresses[ $key ]['cache']            = true;
					}
				} else {
					$json_addresses[ $key ]['coordinates'] = false;
					$json_addresses[ $key ]['cache']       = false;

					if ( isset( $cached_addresses[ trim( $json_addresses[ $key ]['address'] ) ] ) ) {
						$json_addresses[ $key ]['latitude']  = $cached_addresses[ trim( $json_addresses[ $key ]['address'] ) ]['latitude'];
						$json_addresses[ $key ]['longitude'] = $cached_addresses[ trim( $json_addresses[ $key ]['address'] ) ]['longitude'];
						$json_addresses[ $key ]['cache']     = true;
					}
				}
			} // End foreach().

			$json_addresses = wp_json_encode( $json_addresses );

			$map_id = uniqid( 'fusion_map_' ); // Generate a unique ID for this map.
			$this->map_id = $map_id;
			ob_start(); ?>
			<script type="text/javascript">
				var map_<?php echo esc_attr( $map_id ); ?>;
				var markers = [];
				var counter = 0;
				function fusion_run_map_<?php echo esc_attr( $map_id ); ?>() {
					jQuery('#<?php echo esc_attr( $map_id ); ?>').fusion_maps({
						<?php // @codingStandardsIgnoreLine ?>
						addresses: <?php echo $json_addresses; ?>,
						address_pin: <?php echo ( 'yes' == $address_pin ) ? 'true' : 'false'; ?>,
						animations: <?php echo ( 'yes' == $animation ) ? 'true' : 'false'; ?>,
						infobox_background_color: '<?php echo esc_attr( $infobox_background_color ); ?>',
						infobox_styling: '<?php echo esc_attr( $infobox ); ?>',
						infobox_text_color: '<?php echo esc_attr( $infobox_text_color ); ?>',
						map_style: '<?php echo esc_attr( $map_style ); ?>',
						map_type: '<?php echo esc_attr( $type ); ?>',
						marker_icon: '<?php echo esc_attr( $icon ); ?>',
						overlay_color: '<?php echo esc_attr( $overlay_color ); ?>',
						overlay_color_hsl: <?php echo wp_json_encode( fusion_rgb2hsl( $overlay_color ) ); ?>,
						pan_control: <?php echo ( 'yes' == $zoom_pancontrol ) ? 'true' : 'false'; ?>,
						show_address: <?php echo ( 'yes' == $popup ) ? 'true' : 'false'; ?>,
						scale_control: <?php echo ( 'yes' == $scale ) ? 'true' : 'false'; ?>,
						scrollwheel: <?php echo ( 'yes' == $scrollwheel ) ? 'true' : 'false'; ?>,
						zoom: <?php echo esc_attr( $zoom ); ?>,
						zoom_control: <?php echo ( 'yes' == $zoom_pancontrol ) ? 'true' : 'false'; ?>,
					});
				}

				google.maps.event.addDomListener(window, 'load', fusion_run_map_<?php echo esc_attr( $map_id ); ?>);
			</script>
			<?php
			if ( $defaults['id'] ) {
				$html = ob_get_clean() . '<div id="' . $defaults['id'] . '"><div ' . $this->attributes( 'avada-google-map' ) . '></div></div>';
			} else {
				$html = ob_get_clean() . '<div ' . $this->attributes( 'avada-google-map' ) . '></div>';
			}
		} // End if().

		return $html;

	}

	/**
	 * Modifies attributes.
	 *
	 * @access  public
	 * @return array
	 */
	public function attr() {

		$attr['class'] = 'shortcode-map fusion-google-map avada-google-map';

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		$attr['id'] = $this->map_id;

		$attr['style'] = 'height:' . self::$args['height'] . ';width:' . self::$args['width'] . ';';

		return $attr;

	}
	/**
	 * Caches google maps.
	 *
	 * @access  public
	 * @return null
	 */
	public function fusion_cache_map() {

		check_ajax_referer( 'avada_admin_ajax', 'security' );

		// Check that the user has the right permissions.
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$addresses_to_cache = get_option( 'fusion_map_addresses' );

		// @codingStandardsIgnoreLine
		$post_addresses = isset( $_POST['addresses'] ) ? wp_unslash( $_POST['addresses'] ) : array();
		foreach ( $post_addresses as $address ) {

			if ( isset( $address['latitude'] ) && isset( $address['longitude'] ) ) {
				$addresses_to_cache[ trim( $address['address'] ) ] = array(
					'address'   => trim( $address['address'] ),
					'latitude'  => esc_attr( $address['latitude'] ),
					'longitude' => esc_attr( $address['longitude'] ),
				);

				if ( isset( $address['geocoded_address'] ) && $address['geocoded_address'] ) {
					$addresses_to_cache[ trim( $address['address'] ) ]['address'] = $address['geocoded_address'];
				}
			}
		}
		update_option( 'fusion_map_addresses', $addresses_to_cache );

		wp_die();

	}
}
