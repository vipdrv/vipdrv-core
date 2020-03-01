<?php

if ( fusion_is_element_enabled( 'fusion_images' ) ) {

	if ( ! class_exists( 'FusionSC_ImageCarousel' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_ImageCarousel extends Fusion_Element {

			/**
			 * Image Carousels counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $image_carousel_counter = 1;

			/**
			 * The image data.
			 *
			 * @access private
			 * @since 1.0
			 * @var false|array
			 */
			private $image_data = false;

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
				add_filter( 'fusion_attr_image-carousel-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_image-carousel-shortcode-carousel', array( $this, 'carousel_attr' ) );
				add_filter( 'fusion_attr_image-carousel-shortcode-slide-link', array( $this, 'slide_link_attr' ) );
				add_filter( 'fusion_attr_fusion-image-wrapper', array( $this, 'image_wrapper' ) );

				/*
				TODO:
				add_filter( 'fusion_attr_fusion-nav-prev', array( $this, 'fusion_nav_prev' ) );
				add_filter( 'fusion_attr_fusion-nav-next', array( $this, 'fusion_nav_next' ) );
				*/

				add_shortcode( 'fusion_images', array( $this, 'render_parent' ) );
				add_shortcode( 'fusion_image', array( $this, 'render_child' ) );

				add_shortcode( 'fusion_clients', array( $this, 'render_parent' ) );
				add_shortcode( 'fusion_client', array( $this, 'render_child' ) );

			}

			/**
			 * Render the parent shortcode.
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
						'autoplay'       => 'no',
						'border'         => 'yes',
						'columns'        => '5',
						'column_spacing' => '13',
						'image_id'       => '',
						'lightbox'       => 'no',
						'mouse_scroll'   => 'no',
						'picture_size'   => 'fixed',
						'scroll_items'   => '',
						'show_nav'       => 'yes',
						'hover_type'     => 'none',
					),
					$args
				);

				$defaults['column_spacing'] = FusionBuilder::validate_shortcode_attr_value( $defaults['column_spacing'], '' );

				extract( $defaults );

				$this->parent_args = $defaults;

				$html  = '<div ' . FusionBuilder::attributes( 'image-carousel-shortcode' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'image-carousel-shortcode-carousel' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-carousel-positioner' ) . '>';

				// The main carousel.
				$html .= '<ul ' . FusionBuilder::attributes( 'fusion-carousel-holder' ) . '>';
				$html .= do_shortcode( $content );
				$html .= '</ul>';

				// Check if navigation should be shown.
				if ( 'yes' === $show_nav ) {
					$html .= '<div ' . FusionBuilder::attributes( 'fusion-carousel-nav' ) . '>';
					$html .= '<span ' . FusionBuilder::attributes( 'fusion-nav-prev' ) . '></span>';
					$html .= '<span ' . FusionBuilder::attributes( 'fusion-nav-next' ) . '></span>';
					$html .= '</div>';
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

				$this->image_carousel_counter++;

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
					'class' => 'fusion-image-carousel fusion-image-carousel-' . $this->parent_args['picture_size'],
				) );

				if ( 'yes' === $this->parent_args['lightbox'] ) {
					$attr['class'] .= ' lightbox-enabled';
				}

				if ( 'yes' === $this->parent_args['border'] ) {
					$attr['class'] .= ' fusion-carousel-border';
				}

				if ( $this->parent_args['class'] ) {
					$attr['class'] .= ' ' . $this->parent_args['class'];
				}

				if ( $this->parent_args['id'] ) {
					$attr['id'] = $this->parent_args['id'];
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

				$attr['class']            = 'fusion-carousel';
				$attr['data-autoplay']    = $this->parent_args['autoplay'];
				$attr['data-columns']     = $this->parent_args['columns'];
				$attr['data-itemmargin']  = $this->parent_args['column_spacing'];
				$attr['data-itemwidth']   = 180;
				$attr['data-touchscroll'] = $this->parent_args['mouse_scroll'];
				$attr['data-imagesize']   = $this->parent_args['picture_size'];
				$attr['data-scrollitems'] = $this->parent_args['scroll_items'];
				return $attr;

			}

			/**
			 * Render the child shortcode.
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args   Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string         HTML output.
			 */
			public function render_child( $args, $content = '' ) {
				global $fusion_library;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'alt'        => '',
						'image'      => '',
						'link'       => '',
						'linktarget' => '_self',
					), $args
				);

				extract( $defaults );

				$this->child_args = $defaults;

				$width = $height = '';

				$this->image_data = $fusion_library->images->get_attachment_data_from_url( $image );
				if ( $this->image_data ) {
					$image_id = $this->image_data['id'];
				}

				$image_size = 'full';
				if ( 'fixed' === $this->parent_args['picture_size'] ) {
					$image_size = 'portfolio-two';
					if ( 'six' === $this->parent_args['columns'] || 'five' === $this->parent_args['columns'] || 'four' === $this->parent_args['columns'] ) {
						$image_size = 'blog-medium';
					}
				}

				$output = '';
				if ( isset( $image_id ) ) {
					if ( $alt ) {
						$output = wp_get_attachment_image( $image_id, $image_size, false, array( 'alt' => $alt ) );
					} else {
						$output = wp_get_attachment_image( $image_id, $image_size );
					}
				} else {
					$output = '<img src="' . $image . '" alt="' . $alt . '"/>';
				}

				if ( 'no' === $this->parent_args['mouse_scroll'] && ( $link || 'yes' === $this->parent_args['lightbox'] ) ) {
					$output = '<a ' . FusionBuilder::attributes( 'image-carousel-shortcode-slide-link' ) . '>' . $output . '</a>';
				}

				return '<li ' . FusionBuilder::attributes( 'fusion-carousel-item' ) . '><div ' . FusionBuilder::attributes( 'fusion-carousel-item-wrapper' ) . '><div ' . FusionBuilder::attributes( 'fusion-image-wrapper' ) . '>' . $output . '</div></div></li>';
			}

			/**
			 * Builds the slide-link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function slide_link_attr() {

				$attr = array();

				if ( 'yes' === $this->parent_args['lightbox'] ) {

					if ( ! $this->child_args['link'] ) {
						$this->child_args['link'] = $this->child_args['image'];
					}

					$attr['data-rel'] = 'iLightbox[gallery_image_' . $this->image_carousel_counter . ']';

					if ( $this->image_data ) {
						$attr['data-caption'] = $this->image_data['caption'];
						$attr['data-title'] = $this->image_data['title'];
						$attr['aria-label'] = $this->image_data['title'];
					}
				}

				$attr['href'] = $this->child_args['link'];

				$attr['target'] = $this->child_args['linktarget'];
				if ( '_blank' === $this->child_args['linktarget'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}
				return $attr;

			}

			/**
			 * Builds the image-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function image_wrapper() {
				if ( $this->parent_args['hover_type'] ) {
					return array(
						'class' => 'fusion-image-wrapper hover-type-' . $this->parent_args['hover_type'],
					);
				}
				return array(
					'class' => 'fusion-image-wrapper',
				);
			}

			/**
			 * Builds the "previous" nav attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function fusion_nav_prev() {
				return array(
					'class' => 'fusion-nav-prev fusion-icon-left',
				);
			}

			/**
			 * Builds the "next" nav attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function fusion_nav_next() {
				return array(
					'class' => 'fusion-nav-next fusion-icon-right',
				);
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
					'.fusion-carousel .fusion-carousel-nav .fusion-nav-prev',
					'.fusion-carousel .fusion-carousel-nav .fusion-nav-next',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'carousel_nav_color' ) );

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'width' ) );

				preg_match_all( '!\d+!', $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ), $matches );
				$half_slider_nav_box_height = '' !== $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ? $matches[0][0] / 2 . $fusion_library->sanitize->get_unit( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) ) : '';

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = '-' . $half_slider_nav_box_height;

				$elements = array(
					'.fusion-carousel .fusion-carousel-nav .fusion-nav-prev:before',
					'.fusion-carousel .fusion-carousel-nav .fusion-nav-next:before',
				);

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_nav_box_dimensions', 'height' ) );

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = $fusion_library->sanitize->size( $fusion_settings->get( 'slider_arrow_size' ) );

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
				Fusion_Dynamic_JS::enqueue_script( 'fusion-carousel' );
			}
		}
	}

	new FusionSC_ImageCarousel();

}

/**
 * Map shortcode to Fusion Builder.
 */
function fusion_element_images() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Image Carousel', 'fusion-builder' ),
		'shortcode'     => 'fusion_images',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_image',
		'icon'          => 'fusiona-images',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-image-carousel-preview.php',
		'preview_id'    => 'fusion-builder-block-module-image-carousel-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_image link="" linktarget="_self" alt="" /]',
			),
			array(
				'type'             => 'multiple_upload',
				'heading'          => esc_attr__( 'Bulk Image Upload', 'fusion-builder' ),
				'description'      => __( 'This option allows you to select multiple images at once and they will populate into individual items. It saves time instead of adding one image at a time.', 'fusion-builder' ),
				'param_name'       => 'multiple_upload',
				'element_target'   => 'fusion_image',
				'param_target'     => 'image',
				'remove_from_atts' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Picture Size', 'fusion-builder' ),
				'description' => __( 'fixed = width and height will be fixed <br />auto = width and height will adjust to the image.', 'fusion-builder' ),
				'param_name'  => 'picture_size',
				'value'       => array(
					'fixed' => esc_attr__( 'Fixed', 'fusion-builder' ),
					'auto'  => esc_attr__( 'Auto', 'fusion-builder' ),
				),
				'default'     => 'fixed',
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
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Autoplay', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to autoplay the carousel.', 'fusion-builder' ),
				'param_name'  => 'autoplay',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Maximum Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the number of max columns to display.', 'fusion-builder' ),
				'param_name'  => 'columns',
				'value'       => '5',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Column Spacing', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the amount of spacing between items without "px". ex: 13.', 'fusion-builder' ),
				'param_name'  => 'column_spacing',
				'value'       => '13',
				'min'         => '0',
				'max'         => '300',
				'step'        => '1',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Scroll Items', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the amount of items to scroll. Leave empty to scroll number of visible items.', 'fusion-builder' ),
				'param_name'  => 'scroll_items',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Navigation', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show navigation buttons on the carousel.', 'fusion-builder' ),
				'param_name'  => 'show_nav',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Mouse Scroll', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to enable mouse drag control on the carousel. IMPORTANT: For easy draggability, when mouse scroll is activated, links will be disabled.', 'fusion-builder' ),
				'param_name'  => 'mouse_scroll',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Border', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to enable a border around the images.', 'fusion-builder' ),
				'param_name'  => 'border',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Image lightbox', 'fusion-builder' ),
				'description' => esc_attr__( 'Show image in lightbox.', 'fusion-builder' ),
				'param_name'  => 'lightbox',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => __( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_images' );

/**
 * Map shortcode to Fusion Builder.
 */
function fusion_element_fusion_image() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Image', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this textblock.', 'fusion-builder' ),
		'shortcode'         => 'fusion_image',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Image', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image to display.', 'fusion-builder' ),
				'param_name'  => 'image',
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
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Image Website Link', 'fusion-builder' ),
				'description' => esc_attr__( "Add the url to image's website. If lightbox option is enabled, you have to add the full image link to show it in the lightbox.", 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
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
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Image Alt Text', 'fusion-builder' ),
				'description' => esc_attr__( 'The alt attribute provides alternative information if an image cannot be viewed.', 'fusion-builder' ),
				'param_name'  => 'alt',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_fusion_image' );
