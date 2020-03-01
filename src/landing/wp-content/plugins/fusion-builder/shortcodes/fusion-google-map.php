<?php

if ( fusion_is_element_enabled( 'fusion_map' ) ) {

	if ( ! class_exists( 'FusionSC_GoogleMap' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_GoogleMap extends Fusion_Element {

			/**
			 * The Unique ID of this map.
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $map_id;

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
				add_filter( 'fusion_attr_google-map-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_map', array( $this, 'render' ) );
			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode paramters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {

				global $fusion_settings;

				if ( ! $fusion_settings->get( 'status_gmap' ) ) {
					return '';
				}

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'           => fusion_builder_default_visibility( 'string' ),
						'class'                    => '',
						'id'                       => '',
						'animation'                => 'no',
						'address'                  => '',
						'height'                   => '300px',
						'icon'                     => '',
						'infobox'                  => '',
						'infobox_background_color' => '',
						'infobox_content'          => '',
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

				$defaults['width']  = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
				$defaults['height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['height'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				$html = '';

				if ( $address ) {
					$addresses = explode( '|', $address );

					if ( $addresses ) {
						$this->args['address'] = $addresses;
					}

					$num_of_addresses = count( $addresses );

					$infobox_content_array = array();
					$infobox_content_array = ( ! in_array( $map_style, array( 'default', 'theme' ) ) && 'default' !== $infobox ) ? explode( '|', $infobox_content ) : array() ;

					$icon_array = array();
					if ( $icon ) {
						$icon_array = explode( '|', $icon );
					}

					if ( 'theme' == $map_style ) {
						$map_style                = 'custom';
						$icon                     = 'theme';
						$animation                = 'yes';
						$infobox                  = 'custom';
						$overlay_color            = $fusion_settings->get( 'primary_color' );
						$infobox_background_color = FusionBuilder::hex2rgb( $overlay_color );
						$infobox_background_color = 'rgba(' . $infobox_background_color[0] . ', ' . $infobox_background_color[1] . ', ' . $infobox_background_color[2] . ', 0.8)';
						$brightness_level         = Fusion_Color::new_color( $overlay_color )->brightness;

						$infobox_text_color = '#747474';
						if ( $brightness_level > 140 ) {
							$infobox_text_color = '#fff';
						}
					} elseif ( 'custom' == $map_style ) {
						if ( '0' == Fusion_Color::new_color( $overlay_color )->alpha ) {
							$overlay_color = '';
						}
					}

					// If only one custom icon is set, use it for all markers.
					if ( 'custom' == $map_style && $icon && 'theme' != $icon && $icon_array && count( $icon_array ) == 1 ) {
						$icon_url = $icon_array[0];
						for ( $i = 0; $i < $num_of_addresses; $i++ ) {
							$icon_array[ $i ] = $icon_url;
						}
					}

					if ( 'theme' == $icon && 'custom' == $map_style ) {
						for ( $i = 0; $i < $num_of_addresses; $i++ ) {
							$icon_array[ $i ] = plugins_url( 'images/avada_map_marker.png', dirname( __FILE__ ) );
						}
					}

					if ( wp_script_is( 'google-maps-api', 'registered' ) ) {
						wp_print_scripts( 'google-maps-api' );
						if ( wp_script_is( 'google-maps-infobox', 'registered' ) ) {
							wp_print_scripts( 'google-maps-infobox' );
						}
					}

					foreach ( $this->args['address'] as $add ) {

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
						if ( strpos( $this->args['address'][ $i ], 'latlng=' ) === 0 ) {
							$this->args['address'][ $i ] = $coordinates[ $i ]['address'];
						}
					}

					$this->args['infobox_content'] = $this->args['address'];
					if ( ! empty( $infobox_content_array ) ) {
						for ( $i = 0; $i < $num_of_addresses; $i++ ) {
							if ( ! array_key_exists( $i, $infobox_content_array ) ) {
								$infobox_content_array[ $i ] = $this->args['address'][ $i ];
							}
						}
						$this->args['infobox_content'] = $infobox_content_array;
					}

					$cached_addresses = get_option( 'fusion_map_addresses' );

					foreach ( $this->args['address'] as $key => $address ) {
						$json_addresses[] = array(
							'address'         => $address,
							'infobox_content' => html_entity_decode( $this->args['infobox_content'][ $key ] ),
						);

						if ( isset( $icon_array ) && array_key_exists( $key, $icon_array ) ) {
							$json_addresses[ $key ]['marker'] = $icon_array[ $key ];
						}

						if ( strpos( $address, strtolower( 'latlng=' ) ) !== false ) {
							$json_addresses[ $key ]['address']     = str_replace( 'latlng=', '', $address );
							$lat_lng                               = explode( ',', $json_addresses[ $key ]['address'] );
							$json_addresses[ $key ]['coordinates'] = true;
							$json_addresses[ $key ]['latitude']    = $lat_lng[0];
							$json_addresses[ $key ]['longitude']   = $lat_lng[1];
							$json_addresses[ $key ]['cache']       = false;

							if ( false !== strpos( $this->args['infobox_content'][ $key ], strtolower( 'latlng=' ) ) ) {
								$json_addresses[ $key ]['infobox_content'] = '';
							}

							if ( isset( $cached_addresses[ trim( $json_addresses[ $key ]['latitude'] . ',' . $json_addresses[ $key ]['longitude'] ) ] ) ) {
								$json_addresses[ $key ]['geocoded_address'] = $cached_addresses[ trim( $json_addresses[ $key ]['latitude'] . ',' . $json_addresses[ $key ]['longitude'] ) ]['address'];
								$json_addresses[ $key ]['cache'] = true;
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
					}

					$json_addresses = wp_json_encode( $json_addresses );

					$map_id       = uniqid( 'fusion_map_' ); // Generate a unique ID for this map.
					$this->map_id = $map_id;
					$overlay_color_hsl = array(
						'hue' => Fusion_Color::new_color( $overlay_color )->hue,
						'sat' => Fusion_Color::new_color( $overlay_color )->saturation,
						'lum' => Fusion_Color::new_color( $overlay_color )->lightness,
					);

					ob_start(); ?>
					<script type="text/javascript">
						var map_<?php echo $map_id; // WPCS: XSS ok. ?>;
						var markers = [];
						var counter = 0;
						function fusion_run_map_<?php echo $map_id ; // WPCS: XSS ok. ?>() {
							jQuery('#<?php echo $map_id; // WPCS: XSS ok. ?>').fusion_maps({
								addresses: <?php echo $json_addresses; // WPCS: XSS ok. ?>,
								animations: <?php echo ( 'yes' == $animation ) ? 'true' : 'false'; ?>,
								infobox_background_color: '<?php echo $infobox_background_color; // WPCS: XSS ok. ?>',
								infobox_styling: '<?php echo $infobox; // WPCS: XSS ok. ?>',
								infobox_text_color: '<?php echo $infobox_text_color; // WPCS: XSS ok. ?>',
								map_style: '<?php echo $map_style; // WPCS: XSS ok. ?>',
								map_type: '<?php echo $type; // WPCS: XSS ok. ?>',
								marker_icon: '<?php echo $icon; // WPCS: XSS ok. ?>',
								overlay_color: '<?php echo $overlay_color; // WPCS: XSS ok. ?>',
								overlay_color_hsl: <?php echo wp_json_encode( $overlay_color_hsl ); ?>,
								pan_control: <?php echo ( 'yes' == $zoom_pancontrol ) ? 'true' : 'false'; ?>,
								show_address: <?php echo ( 'yes' == $popup ) ? 'true' : 'false'; ?>,
								scale_control: <?php echo ( 'yes' == $scale ) ? 'true' : 'false'; ?>,
								scrollwheel: <?php echo ( 'yes' == $scrollwheel ) ? 'true' : 'false'; ?>,
								zoom: <?php echo $zoom; // WPCS: XSS ok. ?>,
								zoom_control: <?php echo ( 'yes' == $zoom_pancontrol ) ? 'true' : 'false'; ?>,
							});
						}

						google.maps.event.addDomListener(window, 'load', fusion_run_map_<?php echo $map_id; // WPCS: XSS ok. ?>);
					</script>
					<?php
					if ( $defaults['id'] ) {
						$html = ob_get_clean() . '<div id="' . $defaults['id'] . '"><div ' . FusionBuilder::attributes( 'google-map-shortcode' ) . '></div></div>';
					} else {
						$html = ob_get_clean() . '<div ' . FusionBuilder::attributes( 'google-map-shortcode' ) . '></div>';
					}
				}

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

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], array(
					'class' => 'shortcode-map fusion-google-map',
				) );

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				$attr['id'] = $this->map_id;

				$attr['style'] = 'height:' . $this->args['height'] . ';width:' . $this->args['width'] . ';';

				return $attr;

			}

			/**
			 * Gets the coordinates from an address.
			 *
			 * @access public
			 * @since 1.0
			 * @param string $address The address we want to geo-locate.
			 * @param bool   $force_refresh Whether we want to force-refresh the geolocating or not.
			 * @return string|array
			 */
			public function get_coordinates( $address, $force_refresh = false ) {

				global $fusion_settings;

				$key          = $fusion_settings->get( 'google_console_api_key' );
				$data         = '';
				$address_hash = md5( $address );
				$coordinates  = get_transient( $address_hash );

				if ( $force_refresh || false === $coordinates ) {

					$args = array( 'address' => rawurlencode( $address ), 'sensor' => 'false' );
					if ( 0 === strpos( $address, 'latlng=' ) ) {
						$args = array( 'latlng' => rawurlencode( substr( $address, 7 ) ), 'sensor' => 'false' );
					}

					$url = 'http://maps.googleapis.com/maps/api/geocode/json';
					if ( $key ) {
						$args['key'] = $key;
						$url = 'https://maps.googleapis.com/maps/api/geocode/json';
					}
					$url      = esc_url_raw( add_query_arg( $args, $url ) );
					$response = wp_remote_get( $url );

					if ( is_wp_error( $response ) ) {
						return;
					}

					$data = wp_remote_retrieve_body( $response );

					if ( is_wp_error( $data ) ) {
						return;
					}

					if ( 200 == $response['response']['code'] ) {

						$data = json_decode( $data );

						if ( 'OK' === $data->status ) {

							$coordinates = $data->results[0]->geometry->location;

							$cache_value['lat']     = $coordinates->lat;
							$cache_value['lng']     = $coordinates->lng;
							$cache_value['address'] = (string) $data->results[0]->formatted_address;

							// Cache coordinates for 3 months.
							set_transient( $address_hash, $cache_value, 3600 * 24 * 30 * 3 );
							$data = $cache_value;

						} elseif ( 'ZERO_RESULTS' === $data->status ) {
							return esc_attr__( 'No location found for the entered address.', 'fusion-builder' );
						} elseif ( 'INVALID_REQUEST' === $data->status ) {
							return esc_attr__( 'Invalid request. Did you enter an address?', 'fusion-builder' );
						} else {
							return esc_attr__( 'Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'fusion-builder' );
						}
					} else {
						return esc_attr__( 'Unable to contact Google API service.', 'fusion-builder' );
					}
				} else {
					// Return cached results.
					$data = $coordinates;
				}

				return $data;

			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query;

				$css[ $content_media_query ]['.fusion-google-map']['width'] = '100% !important';
				$css[ $three_twenty_six_fourty_media_query ]['.fusion-google-map']['width'] = '100% !important';
				$css[ $ipad_portrait_media_query ]['.fusion-google-map']['width'] = '100% !important';

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
				Fusion_Dynamic_JS::enqueue_script(
					'fusion-google-map',
					FUSION_LIBRARY_URL . '/assets/min/js/general/fusion-google-map.js',
					FUSION_LIBRARY_PATH . '/assets/min/js/general/fusion-google-map.js',
					array( 'jquery-fusion-maps' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_GoogleMap();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_google_map() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Google Map', 'fusion-builder' ),
		'shortcode'  => 'fusion_map',
		'icon'       => 'fusiona-map',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-google-map-preview.php',
		'preview_id' => 'fusion-builder-block-module-google-map-preview-template',
		'params'     => array(
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Address', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the address to the location you wish to display. Single address example: 775 New York Ave, Brooklyn, Kings, New York 11203. If the location is off, please try to use long/lat coordinates with latlng=. ex: latlng=12.381068,-1.492711. For multiple addresses, separate addresses by using the | symbol. ex: Address 1|Address 2|Address 3.', 'fusion-builder' ),
				'param_name'  => 'address',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Map Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of google map to display.', 'fusion-builder' ),
				'param_name'  => 'type',
				'value'       => array(
					'roadmap'   => esc_attr__( 'Roadmap', 'fusion-builder' ),
					'satellite' => esc_attr__( 'Satellite', 'fusion-builder' ),
					'hybrid'    => esc_attr__( 'Hybrid', 'fusion-builder' ),
					'terrain'   => esc_attr__( 'Terrain', 'fusion-builder' ),
				),
				'default' => 'roadmap',
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Map Dimensions', 'fusion-builder' ),
				'description'      => esc_attr__( 'Map dimensions in percentage, pixels or ems. NOTE: height does not accept percentage value.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'width'  => '100%',
					'height' => '300px',
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Zoom Level', 'fusion-builder' ),
				'description' => esc_attr__( 'Higher number will be more zoomed in.', 'fusion-builder' ),
				'param_name'  => 'zoom',
				'value'       => '14',
				'min'         => '1',
				'max'         => '25',
				'step'        => '1',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Scrollwheel on Map', 'fusion-builder' ),
				'description' => esc_attr__( "Enable zooming using a mouse's scroll wheel.", 'fusion-builder' ),
				'param_name'  => 'scrollwheel',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Scale Control on Map', 'fusion-builder' ),
				'description' => esc_attr__( 'Display the map scale.', 'fusion-builder' ),
				'param_name'  => 'scale',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Pan Control on Map', 'fusion-builder' ),
				'description' => esc_attr__( 'Displays pan control button.', 'fusion-builder' ),
				'param_name'  => 'zoom_pancontrol',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Address Pin Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to animate the address pins when the map first loads.', 'fusion-builder' ),
				'param_name'  => 'animation',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Tooltip by Default', 'fusion-builder' ),
				'description' => esc_attr__( 'Display or hide tooltip by default when the map first loads.', 'fusion-builder' ),
				'param_name'  => 'popup',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Select the Map Styling Switch', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose default styling for classic google map styles. Choose theme styling for our custom style. Choose custom styling to make your own with the advanced options below.', 'fusion-builder' ),
				'param_name'  => 'map_style',
				'value'       => array(
					'default' => esc_attr__( 'Default Styling', 'fusion-builder' ),
					'theme'   => esc_attr__( 'Theme Styling', 'fusion-builder' ),
					'custom'  => esc_attr__( 'Custom Styling', 'fusion-builder' ),
				),
				'default' => 'default',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Map Overlay Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Pick an overlaying color for the map. Works best with "roadmap" type.', 'fusion-builder' ),
				'param_name'  => 'overlay_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Infobox Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Type in custom info box content to replace address string. For multiple addresses, separate info box contents by using the | symbol. ex: InfoBox 1|InfoBox 2|InfoBox 3.', 'fusion-builder' ),
				'param_name'  => 'infobox_content',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Infobox Styling', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Choose between default or custom info box.', 'fusion-builder' ),
				'param_name'  => 'infobox',
				'value'       => array(
					'default' => esc_attr__( 'Default Infobox', 'fusion-builder' ),
					'custom'  => esc_attr__( 'Custom Infobox', 'fusion-builder' ),
				),
				'default'     => 'default',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Info Box Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Pick a color for the info box text.', 'fusion-builder' ),
				'param_name'  => 'infobox_text_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
						'operator' => '==',
					),
					array(
						'element'  => 'infobox',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Info Box Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Pick a color for the info box background.', 'fusion-builder' ),
				'param_name'  => 'infobox_background_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
						'operator' => '==',
					),
					array(
						'element'  => 'infobox',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Custom Marker Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom styling setting only. Use full image urls for custom marker icons or input "theme" for our custom marker. For multiple addresses, separate icons by using the | symbol or use one for all. ex: Icon 1|Icon 2|Icon 3.', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'map_style',
						'value'    => 'custom',
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
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_google_map' );
