<?php

if ( fusion_is_element_enabled( 'fusion_person' ) ) {

	if ( ! class_exists( 'FusionSC_Person' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Person extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * The person image data.
			 *
			 * @access private
			 * @since 1.0
			 * @var false|array
			 */
			private $person_image_data = false;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_person-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_person-shortcode-image-container', array( $this, 'image_container_attr' ) );
				add_filter( 'fusion_attr_person-shortcode-href', array( $this, 'href_attr' ) );
				add_filter( 'fusion_attr_person-shortcode-img', array( $this, 'img_attr' ) );
				add_filter( 'fusion_attr_person-shortcode-author', array( $this, 'author_attr' ) );
				add_filter( 'fusion_attr_person-shortcode-social-networks', array( $this, 'social_networks_attr' ) );
				add_filter( 'fusion_attr_person-shortcode-icon', array( $this, 'icon_attr' ) );
				add_filter( 'fusion_attr_person-desc', array( $this, 'desc_attr' ) );

				add_shortcode( 'fusion_person', array( $this, 'render' ) );

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
				global $fusion_library, $fusion_settings;

				$social_icon_order  = '';
				$social_media_icons = $fusion_settings->get( 'social_media_icons' );
				if ( is_array( $social_media_icons ) && isset( $social_media_icons['icon'] ) && is_array( $social_media_icons['icon'] ) ) {
					$social_icon_order = implode( '|', $social_media_icons['icon'] );
				}
				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'           => fusion_builder_default_visibility( 'string' ),
						'class'                    => '',
						'id'                       => '',
						'lightbox'                 => 'no',
						'linktarget'               => '_self',
						'name'                     => '',
						'social_icon_boxed'        => ( 1 == $fusion_settings->get( 'social_links_boxed' ) ) ? 'yes' : $fusion_settings->get( 'social_links_boxed' ),
						'social_icon_boxed_colors' => strtolower( $fusion_settings->get( 'social_links_box_color' ) ),
						'social_icon_boxed_radius' => $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_boxed_radius' ) ),
						'social_icon_color_type'   => '',
						'social_icon_colors'       => strtolower( $fusion_settings->get( 'social_links_icon_color' ) ),
						'social_icon_font_size'    => $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_font_size' ) ),
						'social_icon_order'        => $social_icon_order,
						'social_icon_padding'      => $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_boxed_padding' ) ),
						'social_icon_tooltip'      => strtolower( $fusion_settings->get( 'social_links_tooltip_placement' ) ),
						'pic_bordercolor'          => strtolower( $fusion_settings->get( 'person_border_color' ) ),
						'pic_borderradius'         => intval( $fusion_settings->get( 'person_border_radius' ) ) . 'px',
						'pic_bordersize'           => $fusion_settings->get( 'person_border_size' ),
						'pic_link'                 => '',
						'pic_style'                => 'none',
						'pic_style_color'          => strtolower( $fusion_settings->get( 'person_style_color' ) ),
						'show_custom'              => 'no',
						'picture'                  => '',
						'title'                    => '',
						'hover_type'               => 'none',
						'background_color'         => strtolower( $fusion_settings->get( 'person_background_color' ) ),
						'content_alignment'        => strtolower( $fusion_settings->get( 'person_alignment' ) ),
						'icon_position'            => strtolower( $fusion_settings->get( 'person_icon_position' ) ),
						'facebook'                 => '',
						'twitter'                  => '',
						'instagram'                => '',
						'linkedin'                 => '',
						'dribbble'                 => '',
						'rss'                      => '',
						'youtube'                  => '',
						'pinterest'                => '',
						'flickr'                   => '',
						'vimeo'                    => '',
						'tumblr'                   => '',
						'google'                   => '',
						'googleplus'               => '',
						'digg'                     => '',
						'blogger'                  => '',
						'skype'                    => '',
						'myspace'                  => '',
						'deviantart'               => '',
						'yahoo'                    => '',
						'reddit'                   => '',
						'forrst'                   => '',
						'paypal'                   => '',
						'dropbox'                  => '',
						'soundcloud'               => '',
						'vk'                       => '',
						'xing'                     => '',
						'yelp'                     => '',
						'spotify'                  => '',
						'email'                    => '',
					), $args
				);
				foreach ( $args as $key => $arg ) {
					if ( false !== strpos( $key, 'custom_' ) ) {
						$defaults[ $key ] = $arg;
					}
				}
				$defaults['pic_bordersize']           = FusionBuilder::validate_shortcode_attr_value( $defaults['pic_bordersize'], 'px' );
				$defaults['pic_borderradius']         = FusionBuilder::validate_shortcode_attr_value( $defaults['pic_borderradius'], 'px' );
				$defaults['social_icon_boxed_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['social_icon_boxed_radius'], 'px' );
				$defaults['social_icon_font_size']    = FusionBuilder::validate_shortcode_attr_value( $defaults['social_icon_font_size'], 'px' );
				$defaults['social_icon_padding']      = FusionBuilder::validate_shortcode_attr_value( $defaults['social_icon_padding'], 'px' );

				if ( '0px' != $defaults['pic_borderradius'] && ! empty( $defaults['pic_borderradius'] ) && 'bottomshadow' == $defaults['pic_style'] ) {
					$defaults['pic_style'] = 'none';
				}

				if ( 'round' == $defaults['pic_borderradius'] ) {
					$defaults['pic_borderradius'] = '50%';
				}

				extract( $defaults );

				$this->args = $defaults;

				$this->args['styles'] = '';

				$rgb = FusionBuilder::hex2rgb( $defaults['pic_style_color'] );

				if ( 'glow' == $pic_style ) {
					$this->args['styles'] .= "-moz-box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);-webkit-box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);";
				}

				if ( 'dropshadow' == $pic_style ) {
					$this->args['styles'] .= "-moz-box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);-webkit-box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);";
				}

				if ( $pic_borderradius ) {
					$this->args['styles'] .= '-webkit-border-radius:' . $this->args['pic_borderradius'] . ';-moz-border-radius:' . $this->args['pic_borderradius'] . ';border-radius:' . $this->args['pic_borderradius'] . ';';
				}

				$inner_content = $social_icons_content = $social_icons_content_top = $social_icons_content_bottom = '';

				if ( $picture ) {

					$this->person_image_data = $fusion_library->images->get_attachment_data_from_url( $picture );

					$picture = '<img ' . FusionBuilder::attributes( 'person-shortcode-img' ) . ' />';

					if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
						Avada()->images->set_grid_image_meta( array( 'layout' => 'large', 'columns' => '1' ) );
					}

					if ( function_exists( 'wp_make_content_images_responsive' ) ) {
						$picture = wp_make_content_images_responsive( $picture );
					}

					if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
						Avada()->images->set_grid_image_meta( array() );
					}

					if ( $pic_link ) {
						$picture = '<a ' . FusionBuilder::attributes( 'person-shortcode-href' ) . '>' . $picture . '</a>';
					}

					$picture = '<div ' . FusionBuilder::attributes( 'person-shortcode-image-wrapper' ) . '><div ' . FusionBuilder::attributes( 'person-shortcode-image-container' ) . '>' . $picture . '</div></div>';
				}

				if ( $name || $title || $content ) {

					$social_networks = fusion_builder_get_social_networks( $defaults );
					$social_networks = fusion_builder_sort_social_networks( $social_networks );
					$icons = fusion_builder_build_social_links( $social_networks, 'person-shortcode-icon', $defaults );
					if ( 0 < count( $social_networks ) ) {
						$social_icons_content_top  = '<div ' . FusionBuilder::attributes( 'person-shortcode-social-networks' ) . '>';
						$social_icons_content_top .= '<div ' . FusionBuilder::attributes( 'fusion-social-networks-wrapper' ) . '>' . $icons . '</div>';
						$social_icons_content_top .= '</div>';

						$social_icons_content_bottom  = '<div ' . FusionBuilder::attributes( 'person-shortcode-social-networks' ) . '>';
						$social_icons_content_bottom .= '<div ' . FusionBuilder::attributes( 'fusion-social-networks-wrapper' ) . '>' . $icons . '</div>';
						$social_icons_content_bottom .= '</div>';
					}

					if ( 'top' == $this->args['icon_position'] ) {
						$social_icons_content_bottom = '';
					}
					if ( 'bottom' == $this->args['icon_position'] ) {
						$social_icons_content_top = '';
					}

					$person_author_wrapper = '<div ' . FusionBuilder::attributes( 'person-author-wrapper' ) . '><span ' . FusionBuilder::attributes( 'person-name' ) . '>' . $name . '</span><span ' . FusionBuilder::attributes( 'person-title' ) . '>' . $title . '</span></div>';

					$person_author_content = $person_author_wrapper . $social_icons_content_top;
					if ( 'right' == $content_alignment ) {
						$person_author_content = $social_icons_content_top . $person_author_wrapper;
					}

					$inner_content .= '<div ' . FusionBuilder::attributes( 'person-desc' ) . '>';
					$inner_content .= '<div ' . FusionBuilder::attributes( 'person-shortcode-author' ) . '>' . $person_author_content . '</div>';
					$inner_content .= '<div ' . FusionBuilder::attributes( 'person-content fusion-clearfix' ) . '>' . do_shortcode( $content ) . '</div>';
					$inner_content .= $social_icons_content_bottom;
					$inner_content .= '</div>';

				}

				return '<div ' . FusionBuilder::attributes( 'person-shortcode' ) . '>' . $picture . $inner_content . '</div>';
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
					'class' => 'fusion-person person fusion-person-' . $this->args['content_alignment'] . ' fusion-person-icon-' . $this->args['icon_position'],
				) );

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Builds the image-container attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function image_container_attr() {

				$attr = array(
					'class' => 'person-image-container',
				);

				if ( $this->args['hover_type'] ) {
					$attr['class'] .= ' hover-type-' . $this->args['hover_type'];
				}

				if ( 'glow' == $this->args['pic_style'] ) {
					$attr['class'] .= ' glow';
				} elseif ( 'dropshadow' == $this->args['pic_style'] ) {
					$attr['class'] .= ' dropshadow';
				} elseif ( 'bottomshadow' == $this->args['pic_style'] ) {
					$attr['class'] .= ' element-bottomshadow';
				}

				$attr['style'] = $this->args['styles'];

				return $attr;

			}

			/**
			 * Builds the link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function href_attr() {

				$attr = array(
					'href' => $this->args['pic_link'],
				);

				if ( 'yes' == $this->args['lightbox'] ) {
					$attr['class'] = 'lightbox-shortcode';
					$attr['href']  = $this->args['picture'];
				} else {
					$attr['target'] = $this->args['linktarget'];
				}

				return $attr;

			}

			/**
			 * Builds the image attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function img_attr() {

				$attr = array(
					'class' => 'person-img img-responsive',
					'style' => '',
				);

				$image_width  = ! empty( $this->person_image_data['width'] ) ? $this->person_image_data['width'] : '';
				$image_height = ! empty( $this->person_image_data['height'] ) ? $this->person_image_data['height'] : '';
				$image_id     = ! empty( $this->person_image_data['id'] ) ? $this->person_image_data['id'] : '';

				if ( ! empty( $image_id ) ) {
					$attr['class'] .= esc_attr( ' wp-image-' . $image_id );
				}

				if ( ! empty( $image_width ) ) {
					$attr['width'] = esc_attr( $image_width );
				}

				if ( ! empty( $image_height ) ) {
					$attr['height'] = esc_attr( $image_height );
				}

				if ( $this->args['pic_borderradius'] ) {
					$attr['style'] .= '-webkit-border-radius:' . $this->args['pic_borderradius'] . ';-moz-border-radius:' . $this->args['pic_borderradius'] . ';border-radius:' . $this->args['pic_borderradius'] . ';';
				}

				if ( $this->args['pic_bordersize'] ) {
					$attr['style'] .= 'border:' . $this->args['pic_bordersize'] . ' solid ' . $this->args['pic_bordercolor'] . ';';
				}

				$attr['src'] = $this->args['picture'];
				$attr['alt'] = $this->args['name'];

				return $attr;

			}

			/**
			 * Builds the author attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function author_attr() {
				return array(
					'class' => 'person-author',
				);
			}

			/**
			 * Builds the description attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function desc_attr() {

				$attr = array(
					'class' => 'person-desc',
				);

				if ( $this->args['background_color'] && 'transparent' != $this->args['background_color'] && Fusion_Color::new_color( $this->args['background_color'] )->alpha ) {
					$attr['style']  = 'background-color:' . $this->args['background_color'] . ';padding:40px;margin-top:0;';
				}

				return $attr;
			}

			/**
			 * Builds the social-networks attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function social_networks_attr() {

				$attr = array(
					'class' => 'fusion-social-networks',
				);

				if ( 'yes' == $this->args['social_icon_boxed'] ) {
					$attr['class'] .= ' boxed-icons';
				}

				return $attr;

			}

			/**
			 * Builds the icon attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function icon_attr( $args ) {

				global $fusion_settings;

				$attr = array(
					'class' => 'fusion-social-network-icon fusion-tooltip fusion-' . $args['social_network'] . ' fusion-icon-' . $args['social_network'],
				);

				$attr['aria-label'] = 'fusion-' . $args['social_network'];

				$link   = $args['social_link'];
				$target = ( $fusion_settings->get( 'social_icons_new' ) ) ? '_blank' : '_self';

				if ( 'mail' == $args['social_network'] ) {
					$link   = 'mailto:' . str_replace( 'mailto:', '', antispambot( $args['social_link'] ) );
					$target = '_self';
				}

				$attr['href']   = $link;
				$attr['target'] = $target;

				if ( '_blank' == $attr['target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				if ( $fusion_settings->get( 'nofollow_social_links' ) ) {
					$attr['rel'] = 'nofollow';
				}

				$attr['style'] = '';

				if ( $args['icon_color'] ) {
					$attr['style'] = 'color:' . $args['icon_color'] . ';';
				}

				if ( 'yes' == $this->args['social_icon_boxed'] && $args['box_color'] ) {
					$attr['style'] .= 'background-color:' . $args['box_color'] . ';border-color:' . $args['box_color'] . ';';
				}

				if ( 'yes' == $this->args['social_icon_boxed'] && $this->args['social_icon_boxed_radius'] || '0' === $this->args['social_icon_boxed_radius'] ) {
					if ( 'round' == $this->args['social_icon_boxed_radius'] ) {
						$this->args['social_icon_boxed_radius'] = '50%';
					}
					$attr['style'] .= 'border-radius:' . $this->args['social_icon_boxed_radius'] . ';';
				}

				if ( $this->args['social_icon_font_size'] ) {
					$attr['style'] .= 'font-size:' . $this->args['social_icon_font_size'] . ';';
				}

				if ( 'yes' == $this->args['social_icon_boxed'] && $this->args['social_icon_padding'] ) {
					$attr['style'] .= 'padding:' . $this->args['social_icon_padding'] . ';';
				}

				$attr['data-placement'] = $this->args['social_icon_tooltip'];
				$tooltip = $args['social_network'];
				$tooltip = ( 'googleplus' === strtolower( $tooltip ) ) ? 'Google+' : $tooltip;
				$tooltip = ( 'youtube' === strtolower( $tooltip ) ) ? 'YouTube' : $tooltip;

				$attr['data-title'] = ucfirst( $tooltip );
				$attr['title']      = ucfirst( $tooltip );

				if ( 'none' != $this->args['social_icon_tooltip'] ) {
					$attr['data-toggle'] = 'tooltip';
				}

				return $attr;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Person settings.
			 */
			public function add_options() {

				return array(
					'person_shortcode_section' => array(
						'label'       => esc_html__( 'Person Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'person_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'person_shortcode_important_note_info' => array(
								'label'       => '',
								'description' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> The styling options for the social icons used in the person element are controlled through the options under the "Social Icon Elements" section on this tab.', 'fusion-builder' ) . '</div>',
								'id'          => 'person_shortcode_important_note_info',
								'type'        => 'custom',
							),
							'person_background_color' => array(
								'label'       => esc_html__( 'Person Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the background color of the person area.', 'fusion-builder' ),
								'id'          => 'person_background_color',
								'default'     => 'rgba(0,0,0,0)',
								'type'        => 'color-alpha',
							),
							'person_border_color' => array(
								'label'       => esc_html__( 'Person Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the person image.', 'fusion-builder' ),
								'id'          => 'person_border_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'person_border_size' => array(
								'label'       => esc_html__( 'Person Border Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border size of the person image.', 'fusion-builder' ),
								'id'          => 'person_border_size',
								'default'     => '0',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '50',
									'step' => '1',
								),
							),
							'person_border_radius' => array(
								'label'       => esc_html__( 'Person Border Radius', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border radius of the person image.', 'fusion-builder' ),
								'id'          => 'person_border_radius',
								'default'     => '0px',
								'type'        => 'dimension',
								'choices'     => array( 'px', '%' ),
							),
							'person_style_color' => array(
								'label'       => esc_html__( 'Person Style Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the style color for all style types except border.', 'fusion-builder' ),
								'id'          => 'person_style_color',
								'default'     => '#000000',
								'type'        => 'color-alpha',
							),
							'person_alignment' => array(
								'label'       => esc_html__( 'Person Content Alignment', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the alignment of the person content.', 'fusion-builder' ),
								'id'          => 'person_alignment',
								'default'     => 'Left',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Left'   => esc_html__( 'Left', 'fusion-builder' ),
									'Center' => esc_html__( 'Center', 'fusion-builder' ),
									'Right'  => esc_html__( 'Right', 'fusion-builder' ),
								),
							),
							'person_icon_position' => array(
								'label'       => esc_html__( 'Person Social Icon Position', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the position of the social icons.', 'fusion-builder' ),
								'id'          => 'person_icon_position',
								'default'     => 'Top',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Top'    => esc_html__( 'Top', 'fusion-builder' ),
									'Bottom' => esc_html__( 'Bottom', 'fusion-builder' ),
								),
							),
						),
					),
				);
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script( 'fusion-tooltip' );
			}
		}
	}

	new FusionSC_Person();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_person() {

	global $fusion_settings;

	$person_options = array(
		'name'       => esc_attr__( 'Person', 'fusion-builder' ),
		'shortcode'  => 'fusion_person',
		'icon'       => 'fusiona-user',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-person-preview.php',
		'preview_id' => 'fusion-builder-block-module-person-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the name of the person.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the title of the person', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Profile Description', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter the content to be displayed', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Picture', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image to display.', 'fusion-builder' ),
				'param_name'  => 'picture',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Picture Link URL', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the URL the picture will link to, ex: http://example.com.', 'fusion-builder' ),
				'param_name'  => 'pic_link',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window <br />_blank = open in new window', 'fusion-builder' ),
				'param_name'  => 'linktarget',
				'value'       => array(
					'_self'  => esc_attr__( '_self', 'fusion-builder' ),
					'_blank' => esc_attr__( '_blank', 'fusion-builder' ),
				),
				'default'     => '_self',
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Picture Style Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Selected the style type for the picture.', 'fusion-builder' ),
				'param_name'  => 'pic_style',
				'value'       => array(
					'none'         => esc_attr__( 'None', 'fusion-builder' ),
					'glow'         => esc_attr__( 'Glow', 'fusion-builder' ),
					'dropshadow'   => esc_attr__( 'Drop Shadow', 'fusion-builder' ),
					'bottomshadow' => esc_attr__( 'Bottom Shadow', 'fusion-builder' ),
				),
				'default'     => 'none',
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Hover Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the hover effect type.', 'fusion-builder' ),
				'param_name'  => 'hover_type',
				'value'       => array(
					'none'    => esc_attr__( 'None', 'fusion-builder' ),
					'zoomin'  => esc_attr__( 'Zoom In', 'fusion-builder' ),
					'zoomout' => esc_attr__( 'Zoom Out', 'fusion-builder' ),
					'liftup'  => esc_attr__( 'Lift Up', 'fusion-builder' ),
				),
				'default'     => 'none',
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color. Leave blank for theme option selection', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'person_background_color' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the alignment of content. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'content_alignment',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => '',
			),

			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Picture Style Color', 'fusion-builder' ),
				'description' => esc_attr__( 'For all style types except border. Controls the style color. ', 'fusion-builder' ),
				'param_name'  => 'pic_style_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'person_style_color' ),
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Picture Border Size', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'param_name'  => 'pic_bordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '50',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'person_border_size' ),
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Picture Border Color', 'fusion-builder' ),
				'description' => esc_attr__( "Controls the picture's border color. ", 'fusion-builder' ),
				'param_name'  => 'pic_bordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'person_border_color' ),
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'pic_bordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Picture Border Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the border radius of the person image. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'pic_borderradius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'picture',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icons Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the social icon position. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'icon_position',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'top'    => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom' => esc_attr__( 'Bottom', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Boxed Social Icons', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'social_icon_boxed',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Social Icon Box Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the border radius of the boxed icons. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'social_icon_boxed_radius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'social_icon_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icon Color Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color type of the social icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'social_icon_color_type',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'custom' => esc_attr__( 'Custom Colors', 'fusion-builder' ),
					'brand'  => esc_attr__( 'Brand Colors', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the color of social icons. Use one for all or separate by | symbol. ex: #AA0000|#00AA00|#0000AA. ', 'fusion-builder' ),
				'param_name'  => 'social_icon_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'social_icon_color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Box Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the box color of social icons. Use one for all or separate by | symbol. ex: #AA0000|#00AA00|#0000AA. ', 'fusion-builder' ),
				'param_name'  => 'social_icon_boxed_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'social_icon_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
					array(
						'element'  => 'social_icon_color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icon Tooltip Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position for tooltips. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'social_icon_tooltip',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'top'    => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom' => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'Right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'none'   => esc_attr__( 'None', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Blogger Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Blogger link.', 'fusion-builder' ),
				'param_name'  => 'blogger',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Deviantart Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Deviantart link.', 'fusion-builder' ),
				'param_name'  => 'deviantart',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Digg Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Digg link.', 'fusion-builder' ),
				'param_name'  => 'digg',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dribbble Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dribbble link.', 'fusion-builder' ),
				'param_name'  => 'dribbble',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dropbox Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dropbox link.', 'fusion-builder' ),
				'param_name'  => 'dropbox',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Facebook Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Facebook link.', 'fusion-builder' ),
				'param_name'  => 'facebook',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Flickr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Flickr link.', 'fusion-builder' ),
				'param_name'  => 'flickr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Forrst Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Forrst link.', 'fusion-builder' ),
				'param_name'  => 'forrst',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Google+ Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Google+ link.', 'fusion-builder' ),
				'param_name'  => 'googleplus',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Instagram Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Instagram link.', 'fusion-builder' ),
				'param_name'  => 'instagram',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'LinkedIn Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom LinkedIn link.', 'fusion-builder' ),
				'param_name'  => 'linkedin',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Myspace Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Myspace link.', 'fusion-builder' ),
				'param_name'  => 'myspace',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'PayPal Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom PayPal link.', 'fusion-builder' ),
				'param_name'  => 'paypal',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Pinterest Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Pinterest link.', 'fusion-builder' ),
				'param_name'  => 'pinterest',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Reddit Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Reddit link.', 'fusion-builder' ),
				'param_name'  => 'reddit',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'RSS Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom RSS link.', 'fusion-builder' ),
				'param_name'  => 'rss',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Skype Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Skype link.', 'fusion-builder' ),
				'param_name'  => 'skype',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'SoundCloud Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom SoundCloud link.', 'fusion-builder' ),
				'param_name'  => 'soundcloud',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Spotify Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Spotify link.', 'fusion-builder' ),
				'param_name'  => 'spotify',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Tumblr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Tumblr link.', 'fusion-builder' ),
				'param_name'  => 'tumblr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Twitter Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Twitter link.', 'fusion-builder' ),
				'param_name'  => 'twitter',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Vimeo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Vimeo link.', 'fusion-builder' ),
				'param_name'  => 'vimeo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'VK Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom VK link.', 'fusion-builder' ),
				'param_name'  => 'vk',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Xing Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Xing link.', 'fusion-builder' ),
				'param_name'  => 'xing',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yahoo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yahoo link.', 'fusion-builder' ),
				'param_name'  => 'yahoo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yelp Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yelp link.', 'fusion-builder' ),
				'param_name'  => 'yelp',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Youtube Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Youtube link.', 'fusion-builder' ),
				'param_name'  => 'youtube',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Email Address', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert an email address to display the email icon.', 'fusion-builder' ),
				'param_name'  => 'email',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Custom Social Icons', 'fusion-builder' ),
				'description' => esc_attr__( 'Show the custom social icons specified in Theme Options.', 'fusion-builder' ),
				'param_name'  => 'show_custom',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
		),
	);
	$custom_social_networks = fusion_builder_get_custom_social_networks();
	if ( is_array( $custom_social_networks ) ) {
		$custom_networks = array();
		foreach ( $custom_social_networks as $key => $custom_network ) {
			$person_options['params'][] = array(
				'type'        => 'textfield',
				'heading'     => sprintf( esc_attr__( 'Custom %s Link', 'fusion-builder' ), $key + 1 ),
				'description' => esc_attr__( 'Insert your custom social link.', 'fusion-builder' ),
				'param_name'  => 'custom_' . $key,
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'show_custom',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			);
		}
	}
	$person_options['params'][] = array(
		'type'        => 'checkbox_button_set',
		'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
		'param_name'  => 'hide_on_mobile',
		'value'       => fusion_builder_visibility_options( 'full' ),
		'default'     => fusion_builder_default_visibility( 'array' ),
		'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
	);
	$person_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
		'param_name'  => 'class',
		'value'       => '',
		'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
	);
	$person_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
		'param_name'  => 'id',
		'value'       => '',
		'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
	);

	fusion_builder_map( $person_options );
}
add_action( 'fusion_builder_before_init', 'fusion_element_person' );
