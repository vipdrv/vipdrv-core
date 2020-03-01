<?php

if ( fusion_is_element_enabled( 'fusion_imageframe' ) ) {

	if ( ! class_exists( 'FusionSC_Imageframe' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Imageframe extends Fusion_Element {

			/**
			 * The image-frame counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $imageframe_counter = 1;

			/**
			 * The image data.
			 *
			 * @access private
			 * @since 1.0
			 * @var false|array
			 */
			private $image_data = false;

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
				add_filter( 'fusion_attr_imageframe-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_imageframe-shortcode-link', array( $this, 'link_attr' ) );

				add_shortcode( 'fusion_imageframe', array( $this, 'render' ) );

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

				global $fusion_library, $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'align'               => '',
						'alt'                 => '',
						'animation_direction' => 'left',
						'animation_offset'    => $fusion_settings->get( 'animation_offset' ),
						'animation_speed'     => '',
						'animation_type'      => '',
						'bordercolor'         => '',
						'borderradius'        => intval( $fusion_settings->get( 'imageframe_border_radius' ) ) . 'px',
						'bordersize'          => $fusion_settings->get( 'imageframe_border_size' ),
						'class'               => '',
						'gallery_id'          => '',
						'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
						'hover_type'          => 'none',
						'id'                  => '',
						'lightbox'            => 'no',
						'lightbox_image'      => '',
						'link'                => '',
						'linktarget'          => '_self',
						'style'               => '',
						'stylecolor'          => '',
						'image_id'            => '',
						'style_type'          => 'none',  // Deprecated.
					), $args
				);

				$defaults['borderradius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['borderradius'], 'px' );
				$defaults['bordersize']   = FusionBuilder::validate_shortcode_attr_value( $defaults['bordersize'], 'px' );

				if ( ! $defaults['style'] ) {
					$defaults['style'] = $defaults['style_type'];
				}

				if ( $defaults['borderradius'] && 'bottomshadow' === $defaults['style'] ) {
					$defaults['borderradius'] = '0';
				}

				if ( 'round' === $defaults['borderradius'] ) {
					$defaults['borderradius'] = '50%';
				}

				extract( $defaults );

				$this->args = $defaults;

				// Add the needed styles to the img tag.
				if ( ! $bordercolor ) {
					$bordercolor = $fusion_settings->get( 'imgframe_border_color' );
				}

				if ( ! $stylecolor ) {
					$stylecolor = $fusion_settings->get( 'imgframe_style_color' );
				}

				$rgb = FusionBuilder::hex2rgb( $stylecolor );
				$border_radius = $img_styles = '';

				if ( '0' != $borderradius && '0px' !== $borderradius ) {
					$border_radius .= "-webkit-border-radius:{$borderradius};-moz-border-radius:{$borderradius};border-radius:{$borderradius};";
				}

				if ( $border_radius ) {
					$img_styles = ' style="' . $border_radius . '"';
				}

				// Alt tag.
				$title = $alt_tag = $image_url = $image_id = $image_width = $image_height = '';

				preg_match( '/(src=["\'](.*?)["\'])/', $content, $src );

				if ( array_key_exists( '2', $src ) ) {
					$src = $src[2];
				} elseif ( false === strpos( $content, '<img' ) && $content ) {
					$src = $content;
				}

				if ( $src ) {

					$src = str_replace( '&#215;', 'x', $src );

					$image_url = $this->args['pic_link'] = $src;

					$lightbox_image = $this->args['pic_link'];
					if ( $this->args['lightbox_image'] ) {
						$lightbox_image = $this->args['lightbox_image'];
					}

					$this->image_data = $fusion_library->images->get_attachment_data_from_url( $this->args['pic_link'] );

					if ( $this->image_data ) {
						$image_width  = ( $this->image_data['width'] ) ? $this->image_data['width'] : '';
						$image_height = ( $this->image_data['height'] ) ? $this->image_data['height'] : '';
						$image_id     = $this->image_data['id'];
						$alt_tag      = ( $this->image_data['alt'] ) ? $this->image_data['alt'] : '';
						$title        = ( $this->image_data['title'] ) ? $this->image_data['title'] : '';
					}

					// For pre 5.0 shortcodes extract the alt tag.
					preg_match( '/(alt=["\'](.*?)["\'])/', $content, $legacy_alt );
					if ( array_key_exists( '2', $legacy_alt ) && '' !== $legacy_alt[2] ) {
						$alt_tag = $legacy_alt[2];
					} elseif ( $alt ) {
						$alt_tag = $alt;
					}

					if ( false !== strpos( $content, 'alt=""' ) && $alt_tag ) {
						$content = str_replace( 'alt=""', $alt_tag, $content );
					} elseif ( false === strpos( $content, 'alt' ) && $alt_tag ) {
						$content = str_replace( '/> ', $alt_tag . ' />', $content );
					}

					if ( 'no' === $lightbox && ! $link ) {
						$title = ' title="' . $title . '"';
					} else {
						$title = '';
					}

					$content = '<img src="' . $image_url . '" width="' . $image_width . '" height="' . $image_height . '" alt="' . $alt_tag . '"' . $title . ' />';
				}

				$img_classes = 'img-responsive';

				if ( ! empty( $image_id ) ) {
					$img_classes .= ' wp-image-' . $image_id;
				}

				// Get custom classes from the img tag.
				preg_match( '/(class=["\'](.*?)["\'])/', $content, $classes );

				if ( ! empty( $classes ) ) {
					$img_classes .= ' ' . $classes[2];
				}

				$img_classes = 'class="' . $img_classes . '"';

				// Add custom and responsive class and the needed styles to the img tag.
				if ( ! empty( $classes ) ) {
					$content = str_replace( $classes[0], $img_classes . $img_styles , $content );
				} else {
					$content = str_replace( '/>', $img_classes . $img_styles . '/>', $content );
				}

				if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
					Avada()->images->set_grid_image_meta( array( 'layout' => 'large', 'columns' => '1' ) );
				}

				if ( function_exists( 'wp_make_content_images_responsive' ) ) {
					$content = wp_make_content_images_responsive( $content );
				}

				if ( class_exists( 'Avada' ) && property_exists( Avada(), 'images' ) ) {
					Avada()->images->set_grid_image_meta( array() );
				}

				// Set the lightbox image to the dedicated link if it is set.
				if ( $lightbox_image ) {
					$this->args['pic_link'] = $lightbox_image;
				}

				$output = do_shortcode( $content );

				if ( 'yes' === $lightbox || $link ) {
					$output = '<a ' . FusionBuilder::attributes( 'imageframe-shortcode-link' ) . '>' . do_shortcode( $content ) . '</a>';
				}

				$html = '<span ' . FusionBuilder::attributes( 'imageframe-shortcode' ) . '>' . $output . '</span>';

				if ( 'liftup' === $hover_type ) {
					$liftup_classes = 'imageframe-liftup';
					$liftup_styles  = '';

					if ( 'left' === $align ) {
						$liftup_classes .= ' fusion-imageframe-liftup-left';
					} elseif ( 'right' === $align ) {
						$liftup_classes .= ' fusion-imageframe-liftup-right';
					}

					if ( $border_radius ) {
						$liftup_styles = '<style scoped="scoped">.imageframe-liftup.imageframe-' . $this->imageframe_counter . ':before{' . $border_radius . '}</style>';
						$liftup_classes .= ' imageframe-' . $this->imageframe_counter;
					}

					$html = '<div ' . FusionBuilder::attributes( $liftup_classes ) . '>' . $liftup_styles . $html . '</div>';
				}

				if ( 'center' === $align ) {
					$html = '<div ' . FusionBuilder::attributes( 'imageframe-align-center' ) . '>' . $html . '</div>';
				}

				$this->imageframe_counter++;

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

				$attr = array(
					'style' => '',
				);

				$bordercolor  = $this->args['bordercolor'];
				$stylecolor   = $this->args['stylecolor'];
				$bordersize   = $this->args['bordersize'];
				$borderradius = $this->args['borderradius'];
				$style        = $this->args['style'];

				// Add the needed styles to the img tag.
				if ( ! $bordercolor ) {
					$bordercolor = $fusion_settings->get( 'imgframe_border_color' );
				}

				if ( ! $stylecolor ) {
					$stylecolor = $fusion_settings->get( 'imgframe_style_color' );
				}

				$rgb = FusionBuilder::hex2rgb( $stylecolor );
				$img_styles = '';

				if ( '0' != $bordersize && '0px' !== $bordersize ) {
					$img_styles .= "border:{$bordersize} solid {$bordercolor};";
				}

				if ( '0' != $borderradius && '0px' !== $borderradius ) {
					$img_styles .= "-webkit-border-radius:{$borderradius};-moz-border-radius:{$borderradius};border-radius:{$borderradius};";

					if ( '50%' === $borderradius || 100 < intval( $borderradius ) ) {
							$img_styles .= '-webkit-mask-image: -webkit-radial-gradient(circle, white, black);';
					}
				}

				if ( 'glow' === $style ) {
					$img_styles .= "-moz-box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);-webkit-box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);box-shadow: 0 0 3px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);";
				} elseif ( 'dropshadow' === $style ) {
					$img_styles .= "-moz-box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);-webkit-box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);box-shadow: 2px 3px 7px rgba({$rgb[0]},{$rgb[1]},{$rgb[2]},.3);";
				}

				if ( $img_styles ) {
					$attr['style'] .= $img_styles;
				}

				$attr['class'] = 'fusion-imageframe imageframe-' . $this->args['style'] . ' imageframe-' . $this->imageframe_counter;

				if ( 'bottomshadow' === $this->args['style'] ) {
					$attr['class'] .= ' element-bottomshadow';
				}

				if ( 'liftup' !== $this->args['hover_type'] ) {
					if ( 'left' === $this->args['align'] ) {
						$attr['style'] .= 'margin-right:25px;float:left;';
					} elseif ( 'right' === $this->args['align'] ) {
						$attr['style'] .= 'margin-left:25px;float:right;';
					}

					$attr['class'] .= ' hover-type-' . $this->args['hover_type'];
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				if ( $this->args['animation_type'] ) {
					$animations = FusionBuilder::animations( array(
						'type'      => $this->args['animation_type'],
						'direction' => $this->args['animation_direction'],
						'speed'     => $this->args['animation_speed'],
						'offset'    => $this->args['animation_offset'],
					) );

					$attr = array_merge( $attr, $animations );

					$attr['class'] .= ' ' . $attr['animation_class'];
					unset( $attr['animation_class'] );
				}

				return fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

			}

			/**
			 * Builds the link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function link_attr() {

				$attr = array();

				if ( 'yes' === $this->args['lightbox'] ) {
					$attr['href']  = $this->args['pic_link'];
					$attr['class'] = 'fusion-lightbox';

					if ( $this->args['gallery_id'] || '0' === $this->args['gallery_id'] ) {
						$attr['data-rel'] = 'iLightbox[' . $this->args['gallery_id'] . ']';
					} else {
						$attr['data-rel'] = 'iLightbox[' . substr( md5( $this->args['pic_link'] ), 13 ) . ']';
					}

					if ( $this->image_data ) {
						$attr['data-caption'] = $this->image_data['caption'];
						$attr['data-title']   = $this->image_data['title'];
						$attr['title']        = $this->image_data['title'];
					}
				} elseif ( $this->args['link'] ) {
					$attr['class']  = 'fusion-no-lightbox';
					$attr['href']   = $this->args['link'];
					$attr['target'] = $this->args['linktarget'];
					$attr['aria-label'] = ( $this->image_data ) ? $this->image_data['title'] : '';
					if ( '_blank' === $this->args['linktarget'] ) {
						$attr['rel'] = 'noopener noreferrer';
					}
				}

				return $attr;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Image Frame settings.
			 */
			public function add_options() {

				return array(
					'imageframe_shortcode_section' => array(
						'label'       => esc_html__( 'Image Frame Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'imgf_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'imgframe_border_color' => array(
								'label'       => esc_html__( 'Image Frame Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the image frame.', 'fusion-builder' ),
								'id'          => 'imgframe_border_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'imageframe_border_size' => array(
								'label'       => esc_html__( 'Image Frame Border Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border size of the image frame.', 'fusion-builder' ),
								'id'          => 'imageframe_border_size',
								'default'     => '0',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '50',
									'step' => '1',
								),
							),
							'imageframe_border_radius' => array(
								'label'       => esc_html__( 'Image Frame Border Radius', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border radius of the image frame.', 'fusion-builder' ),
								'id'          => 'imageframe_border_radius',
								'default'     => '0px',
								'type'        => 'dimension',
								'choices'     => array( 'px', '%' ),
							),
							'imgframe_style_color' => array(
								'label'       => esc_html__( 'Image Frame Style Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the style color of the image frame. Only works for glow and drop shadow style.', 'fusion-builder' ),
								'id'          => 'imgframe_style_color',
								'default'     => '#000000',
								'type'        => 'color-alpha',
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
				Fusion_Dynamic_JS::enqueue_script( 'fusion-animations' );
				Fusion_Dynamic_JS::enqueue_script( 'fusion-lightbox' );
			}
		}
	}

	new FusionSC_Imageframe();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_imageframe() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Image Frame', 'fusion-builder' ),
		'shortcode'  => 'fusion_imageframe',
		'icon'       => 'fusiona-image',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-image-frame-preview.php',
		'preview_id' => 'fusion-builder-block-module-image-frame-preview-template',
		'params'     => array(
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Image', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image to display in the frame.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Image ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Image ID from Media Library.', 'fusion-builder' ),
				'param_name'  => 'image_id',
				'value'       => '',
				'hidden'      => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Frame Style Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the frame style type.', 'fusion-builder' ),
				'param_name'  => 'style_type',
				'value'       => array(
					'none'         => esc_attr__( 'None', 'fusion-builder' ),
					'glow'         => esc_attr__( 'Glow', 'fusion-builder' ),
					'dropshadow'   => esc_attr__( 'Drop Shadow', 'fusion-builder' ),
					'bottomshadow' => esc_attr__( 'Bottom Shadow', 'fusion-builder' ),
				),
				'default'     => 'none',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Style Color', 'fusion-builder' ),
				'description' => esc_attr__( 'For all style types except border. Controls the style color. ', 'fusion-builder' ),
				'param_name'  => 'stylecolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'imgframe_style_color' ),
				'dependency'  => array(
					array(
						'element'  => 'style_type',
						'value'    => 'none',
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
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Border Size', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'param_name'  => 'bordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '50',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'imageframe_border_size' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'imgframe_border_color' ),
				'dependency'  => array(
					array(
						'element'  => 'bordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the image frame border radius. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'borderradius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'style_type',
						'value'    => 'bottomshadow',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Align', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how to align the image.', 'fusion-builder' ),
				'param_name'  => 'align',
				'value'       => array(
					'none'   => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
				),
				'default'     => 'none',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Image lightbox', 'fusion-builder' ),
				'description' => esc_attr__( 'Show image in Lightbox.', 'fusion-builder' ),
				'param_name'  => 'lightbox',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Gallery ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Set a name for the lightbox gallery this image frame should belong to.', 'fusion-builder' ),
				'param_name'  => 'gallery_id',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'lightbox',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Lightbox Image', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image that will show up in the lightbox.', 'fusion-builder' ),
				'param_name'  => 'lightbox_image',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'lightbox',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Image Alt Text', 'fusion-builder' ),
				'description' => esc_attr__( 'The alt attribute provides alternative information if an image cannot be viewed.', 'fusion-builder' ),
				'param_name'  => 'alt',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Picture Link URL', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the URL the picture will link to, ex: http://example.com.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'lightbox',
						'value'    => 'yes',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window<br />_blank = open in new window.', 'fusion-builder' ),
				'param_name'  => 'linktarget',
				'value'       => array(
					'_self'  => esc_attr__( '_self', 'fusion-builder' ),
					'_blank' => esc_attr__( '_blank', 'fusion-builder' ),
				),
				'default'     => '_self',
				'dependency'  => array(
					array(
						'element'  => 'lightbox',
						'value'    => 'yes',
						'operator' => '!=',
					),
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					'down'   => esc_attr__( 'Top', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'up'     => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'static' => esc_attr__( 'Static', 'fusion-builder' ),
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
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
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
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
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					''                => esc_attr__( 'Default', 'fusion-builder' ),
					'top-into-view'   => esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ),
					'top-mid-of-view' => esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ),
					'bottom-in-view'  => esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_imageframe' );
