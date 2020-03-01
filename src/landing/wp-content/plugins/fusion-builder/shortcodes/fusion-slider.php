<?php

if ( fusion_is_element_enabled( 'fusion_slider' ) ) {

	if ( ! class_exists( 'FusionSC_Slider' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Slider extends Fusion_Element {

			/**
			 * Sliders counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $slider_counter = 1;

			/**
			 * Parent SC arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $parent_args;

			/**
			 * Child SC arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $child_args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_slider-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_slider-shortcode-slide-link', array( $this, 'slide_link_attr' ) );
				add_filter( 'fusion_attr_slider-shortcode-slide-li', array( $this, 'slide_li_attr' ) );
				add_filter( 'fusion_attr_slider-shortcode-slide-img', array( $this, 'slide_img_attr' ) );
				add_filter( 'fusion_attr_slider-shortcode-slide-img-wrapper', array( $this, 'slide_img_wrapper_attr' ) );

				add_shortcode( 'fusion_slider', array( $this, 'render_parent' ) );
				add_shortcode( 'fusion_slide', array( $this, 'render_child' ) );

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
			public function render_parent( $args, $content = '' ) {

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'height'         => '100%',
						'width'          => '100%',
						'hover_type'     => 'none',
					), $args
				);

				$defaults['width']  = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
				$defaults['height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['height'], 'px' );

				extract( $defaults );

				$this->parent_args = $defaults;

				$html = '<div ' . FusionBuilder::attributes( 'slider-shortcode' ) . '><ul ' . FusionBuilder::attributes( 'slides' ) . '>' . do_shortcode( $content ) . '</ul></div>';

				$this->slider_counter++;

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

				$attr = fusion_builder_visibility_atts( $this->parent_args['hide_on_mobile'], array(
					'class' => 'fusion-slider-sc flexslider', // FIXXXME had clearfix class; group mixin working?
				) );

				if ( $this->parent_args['hover_type'] ) {
					$attr['class'] .= ' flexslider-hover-type-' . $this->parent_args['hover_type'];
				}

				$attr['style'] = 'max-width:' . $this->parent_args['width'] . ';height:' . $this->parent_args['height'] . ';';

				if ( $this->parent_args['class'] ) {
					$attr['class'] .= ' ' . $this->parent_args['class'];
				}

				if ( $this->parent_args['id'] ) {
					$attr['id'] = $this->parent_args['id'];
				}

				return $attr;

			}

			/**
			 * Render the child shortcode
			 *
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child( $args, $content = '' ) {
				global $fusion_library;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'lightbox'   => 'no',
						'link'       => null,
						'linktarget' => '_self',
						'type'       => 'image',
					), $args
				);

				extract( $defaults );

				$this->child_args = $defaults;

				$this->child_args['alt']   = '';
				$this->child_args['title'] = '';
				$this->child_args['src']   = $src = str_replace( '&#215;', 'x', $content );

				if ( 'image' === $type ) {

					$image_id = $fusion_library->images->get_attachment_id_from_url( $src );
					if ( ! empty( $link ) && $link ) {
						$image_id = $fusion_library->images->get_attachment_id_from_url( $link );
					}

					if ( $image_id ) {
						$this->child_args['alt']   = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						$this->child_args['title'] = get_post_field( 'post_excerpt', $image_id );
					}
				}

				if ( $link && ! empty( $link ) && 'image' === $type ) {
					$this->child_args['link'] = $link;
				}

				$html = '<li ' . FusionBuilder::attributes( 'slider-shortcode-slide-li' ) . '>';

				if ( $link && ! empty( $link ) ) {
					$html .= '<a ' . FusionBuilder::attributes( 'slider-shortcode-slide-link' ) . '>';
				}

				if ( ! empty( $type ) && 'video' === $type ) {
					$html .= '<div ' . FusionBuilder::attributes( 'full-video' ) . '>' . do_shortcode( $content ) . '</div>';
				} else {
					$html .= '<span ' . FusionBuilder::attributes( 'slider-shortcode-slide-img-wrapper' ) . '><img role="presentation" ' . FusionBuilder::attributes( 'slider-shortcode-slide-img' ) . ' /></span>';
				}

				if ( $link && ! empty( $link ) ) {
					$html .= '</a>';
				}

				$html .= '</li>';

				return $html;

			}

			/**
			 * Builds the slider-link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slide_link_attr() {
				global $fusion_library;

				$attr = array();

				if ( 'yes' === $this->child_args['lightbox'] ) {
					$attr['class'] = 'lightbox-enabled';
					$attr['data-rel'] = 'prettyPhoto[gallery_slider_' . $this->slider_counter . ']';
				}
				$image_id = $fusion_library->images->get_attachment_id_from_url( $this->child_args['link'] );
				if ( isset( $image_id ) && $image_id ) {
					$attr['data-caption'] = get_post_field( 'post_excerpt', $image_id );
					$attr['data-title'] = get_post_field( 'post_title', $image_id );
				} elseif ( $fusion_library->images->get_attachment_id_from_url( $this->child_args['src'] ) ) {
					$image_id = $fusion_library->images->get_attachment_id_from_url( $this->child_args['src'] );
					$attr['aria-label'] = get_post_field( 'post_title', $image_id );
				}
				$attr['href'] = $this->child_args['link'];
				$attr['target'] = $this->child_args['linktarget'];

				if ( '_blank' == $attr['target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				$attr['title'] = $this->child_args['title'];

				return $attr;

			}

			/**
			 * Builds the slider-list-item attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slide_li_attr() {
				return array(
					'class' => ( 'video' === $this->child_args['type'] ) ? 'video' : 'image',
				);
			}

			/**
			 * Builds the slider image attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slide_img_attr() {
				return array(
					'src' => $this->child_args['src'],
					'alt' => $this->child_args['alt'],
				);
			}

			/**
			 * Builds the image-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slide_img_wrapper_attr() {
				if ( $this->parent_args['hover_type'] ) {
					return array(
						'class' => 'hover-type-' . $this->parent_args['hover_type'],
					);
				}
				return array();
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = array(
					'.fusion-flexslider .flex-direction-nav .flex-prev',
					'.fusion-flexslider .flex-direction-nav .flex-next',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'carousel_nav_color' ) );

				$elements = $dynamic_css_helpers->map_selector( $elements, ':hover' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'carousel_hover_color' ) );

				$elements = array(
					'.fusion-slider-sc .flex-direction-nav a',
				);

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'width' ) );

				preg_match_all( '!\d+!', $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ), $matches );
				$half_slider_nav_box_height = '' !== $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ? $matches[0][0] / 2 . $fusion_library->sanitize->get_unit( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ) : '';

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );

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
				Fusion_Dynamic_JS::enqueue_script( 'fusion-lightbox' );
				Fusion_Dynamic_JS::enqueue_script( 'fusion-flexslider' );
			}
		}
	}

	new FusionSC_Slider();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_slider() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Slider', 'fusion-builder' ),
		'shortcode'     => 'fusion_slider',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_slide',
		'icon'          => 'fusiona-uniF61C',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-slider-preview.php',
		'preview_id'    => 'fusion-builder-block-module-slider-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_slide type="image" link="" linktarget="_self" lightbox="no" /]',
			),
			array(
				'type'             => 'multiple_upload',
				'heading'          => esc_attr__( 'Bulk Image Upload', 'fusion-builder' ),
				'description'      => __( 'This option allows you to select multiple images at once and they will populate into individual items. It saves time instead of adding one image at a time.', 'fusion-builder' ),
				'param_name'       => 'multiple_upload',
				'element_target'   => 'fusion_slide',
				'param_target'     => 'image',
				'remove_from_atts' => true,
			),
			array(
				'type'        => 'select',
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
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Image Size Dimensions', 'fusion-builder' ),
				'description'      => esc_attr__( 'Dimensions in percentage (%) or pixels (px).', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'width'  => '100%',
					'height' => '100%',
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
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_slider' );

/**
 * Map shortcode to Fusion Builder.
 */
function fusion_element_slide() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Slide', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this textblock.', 'fusion-builder' ),
		'shortcode'         => 'fusion_slide',
		'option_dependency' => 'type',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Content', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
				'hidden'      => true,
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Slide Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a video or image slide.', 'fusion-builder' ),
				'param_name'  => 'type',
				'value'       => array(
					'image' => esc_attr__( 'Image', 'fusion-builder' ),
					'video' => esc_attr__( 'Video', 'fusion-builder' ),
				),
				'default'     => 'image',
			),
			array(
				'type'             => 'upload',
				'heading'          => esc_attr__( 'Image', 'fusion-builder' ),
				'description'      => esc_attr__( 'Upload an image to display.', 'fusion-builder' ),
				'param_name'       => 'image',
				'remove_from_atts' => true,
				'value'            => '',
				'dependency'       => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'             => 'textarea',
				'heading'          => esc_attr__( 'Video Element or Video Embed Code', 'fusion-builder' ),
				'description'      => __( 'Click the Youtube or Vimeo Element button below then enter your unique video ID, or copy and paste your video embed code. <p><a href="#" class="insert-slider-video" data-type="fusion_youtube">Add YouTube Video</a></p><p><a href="#" class="insert-slider-video" data-type="fusion_vimeo">Add Vimeo Video</a></p>.', 'fusion-builder' ),
				'param_name'       => 'video',
				'remove_from_atts' => true,
				'value'            => '',
				'dependency'       => array(
					array(
						'element'  => 'type',
						'value'    => 'video',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Full Image Link or External Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url of where the image will link to. If lightbox option is enabled, you have to add the full image link to show it in the lightbox.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Lighbox', 'fusion-builder' ),
				'description' => esc_attr__( 'Show image in Lightbox.', 'fusion-builder' ),
				'param_name'  => 'lightbox',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window <br />_blank = open in new window.', 'fusion-builder' ),
				'param_name'  => 'linktarget',
				'value'       => array(
					'_self'  => esc_attr__( '_self', 'fusion-builder' ),
					'_blank' => esc_attr__( '_blank', 'fusion-builder' ),
				),
				'default'     => '_self',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'lightbox',
						'value'    => 'no',
						'operator' => '==',
					),
				),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_slide' );
