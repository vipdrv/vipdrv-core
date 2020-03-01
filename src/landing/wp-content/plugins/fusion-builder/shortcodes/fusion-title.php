<?php

if ( fusion_is_element_enabled( 'fusion_title' ) ) {

	if ( ! class_exists( 'FusionSC_Title' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Title extends Fusion_Element {

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
				add_filter( 'fusion_attr_title-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_title-shortcode-heading', array( $this, 'heading_attr' ) );
				add_filter( 'fusion_attr_title-shortcode-sep', array( $this, 'sep_attr' ) );

				add_shortcode( 'fusion_title', array( $this, 'render' ) );

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

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'content_align'  => 'left',
						'margin_top'     => $fusion_settings->get( 'title_margin', 'top' ),
						'margin_bottom'  => $fusion_settings->get( 'title_margin', 'bottom' ),
						'sep_color'      => $fusion_settings->get( 'title_border_color' ),
						'size'           => 1,
						'style_tag'      => '',
						'style_type'     => $fusion_settings->get( 'title_style_type' ),
					), $args
				);

				$defaults['margin_top']    = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_top'], 'px' );
				$defaults['margin_bottom'] = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_bottom'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				if ( 1 === count( explode( ' ', $this->args['style_type'] ) ) ) {
					$style_type .= ' solid';
				}

				if ( ! $this->args['style_type'] || 'default' == $this->args['style_type'] ) {
					$this->args['style_type'] = $style_type = $fusion_settings->get( 'title_style_type' );
				}

				// Make sure the title text is not wrapped with an unattributed p tag.
				$content = preg_replace( '!^<p>(.*?)</p>$!i', '$1', trim( $content ) );

				if ( false !== strpos( $style_type, 'underline' ) || false !== strpos( $style_type, 'none' ) ) {

					$html = sprintf( '<div %s><h%s %s>%s</h%s></div>', FusionBuilder::attributes( 'title-shortcode' ), $size,
					FusionBuilder::attributes( 'title-shortcode-heading' ), do_shortcode( $content ), $size );

				} else {

					if ( 'right' == $this->args['content_align'] ) {

						$html = sprintf(
							'<div %s><div %s><div %s></div></div><h%s %s>%s</h%s></div>',
							FusionBuilder::attributes( 'title-shortcode' ),
							FusionBuilder::attributes( 'title-sep-container' ),
							FusionBuilder::attributes( 'title-shortcode-sep' ),
							$size,
							FusionBuilder::attributes( 'title-shortcode-heading' ),
							do_shortcode( $content ),
							$size
						);
					} elseif ( 'center' == $this->args['content_align'] ) {

						$html = sprintf(
							'<div %s><div %s><div %s></div></div><h%s %s>%s</h%s><div %s><div %s></div></div></div>',
							FusionBuilder::attributes( 'title-shortcode' ),
							FusionBuilder::attributes( 'title-sep-container title-sep-container-left' ),
							FusionBuilder::attributes( 'title-shortcode-sep' ), $size,
							FusionBuilder::attributes( 'title-shortcode-heading' ),
							do_shortcode( $content ),
							$size,
							FusionBuilder::attributes( 'title-sep-container title-sep-container-right' ),
							FusionBuilder::attributes( 'title-shortcode-sep' )
						);

					} else {

						$html = sprintf(
							'<div %s><h%s %s>%s</h%s><div %s><div %s></div></div></div>',
							FusionBuilder::attributes( 'title-shortcode' ),
							$size,
							FusionBuilder::attributes( 'title-shortcode-heading' ),
							do_shortcode( $content ),
							$size,
							FusionBuilder::attributes( 'title-sep-container' ),
							FusionBuilder::attributes( 'title-shortcode-sep' )
						);
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
					'class' => 'fusion-title title',
					'style' => '',
				) );

				if ( strpos( $this->args['style_type'], 'underline' ) !== false ) {
					$styles = explode( ' ', $this->args['style_type'] );

					foreach ( $styles as $style ) {
						$attr['class'] .= ' sep-' . $style;
					}

					if ( $this->args['sep_color'] ) {
						$attr['style'] = sprintf( 'border-bottom-color:%s;', $this->args['sep_color'] );
					}
				} elseif ( false !== strpos( $this->args['style_type'], 'none' ) ) {
					$attr['class'] .= ' fusion-sep-none';
				}

				if ( 'center' == $this->args['content_align'] ) {
					$attr['class'] .= ' fusion-title-center';
				}

				$title_size = 'two';
				if ( '1' == $this->args['size'] ) {
					$title_size = 'one';
				} else if ( '2' == $this->args['size'] ) {
					$title_size = 'two';
				} else if ( '3' == $this->args['size'] ) {
					$title_size = 'three';
				} else if ( '4' == $this->args['size'] ) {
					$title_size = 'four';
				} else if ( '5' == $this->args['size'] ) {
					$title_size = 'five';
				} else if ( '6' == $this->args['size'] ) {
					$title_size = 'six';
				}

				$attr['class'] .= ' fusion-title-size-' . $title_size;

				if ( $this->args['margin_top'] ) {
					$attr['style'] .= sprintf( 'margin-top:%s;', $this->args['margin_top'] );
				}

				if ( $this->args['margin_bottom'] ) {
					$attr['style'] .= sprintf( 'margin-bottom:%s;', $this->args['margin_bottom'] );
				}

				if ( '' === $this->args['margin_top'] && '' === $this->args['margin_bottom'] ) {
					$attr['style'] .= ' margin-top:0px; margin-bottom:0px';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Builds the heading attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function heading_attr() {

				$attr = array(
					'class' => 'title-heading-' . $this->args['content_align'],
				);

				if ( '' === $this->args['margin_top'] && '' === $this->args['margin_bottom'] ) {
					$attr['class'] .= ' fusion-default-margin';
				}

				if ( $this->args['style_tag'] ) {
					$attr['style'] = $this->args['style_tag'];
				}

				return $attr;

			}

			/**
			 * Builds the separator attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function sep_attr() {

				$attr = array(
					'class' => 'title-sep',
				);

				$styles = explode( ' ', $this->args['style_type'] );

				foreach ( $styles as $style ) {
					$attr['class'] .= ' sep-' . $style;
				}

				if ( $this->args['sep_color'] ) {
					$attr['style'] = sprintf( 'border-color:%s;', $this->args['sep_color'] );
				}

				return $attr;

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

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-title' ), '.fusion-title' );

				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $main_elements ) ]['margin-top']    = '0px !important';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $main_elements ) ]['margin-bottom'] = '20px !important';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $main_elements ) ]['margin-top']    = '0px !important';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $main_elements ) ]['margin-bottom'] = '20px !important';

				$elements = array_merge(
					$dynamic_css_helpers->map_selector( $main_elements, ' .title-sep' ),
					$dynamic_css_helpers->map_selector( $main_elements, '.sep-underline' )
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'title_border_color' ) );

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Title settings.
			 */
			public function add_options() {

				return array(
					'title_shortcode_section' => array(
						'label'       => esc_html__( 'Title Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'title_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'title_style_type' => array(
								'label'       => esc_html__( 'Title Separator', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the type of title separator that will display.', 'fusion-builder' ),
								'id'          => 'title_style_type',
								'default'     => 'double',
								'type'        => 'select',
								'choices'     => array(
									'single'           => esc_html__( 'Single', 'fusion-builder' ),
									'single solid'     => esc_html__( 'Single Solid', 'fusion-builder' ),
									'single dashed'    => esc_html__( 'Single Dashed', 'fusion-builder' ),
									'single dotted'    => esc_html__( 'Single Dotted', 'fusion-builder' ),
									'double'           => esc_html__( 'Double', 'fusion-builder' ),
									'double solid'     => esc_html__( 'Double Solid', 'fusion-builder' ),
									'double dashed'    => esc_html__( 'Double Dashed', 'fusion-builder' ),
									'double dotted'    => esc_html__( 'Double Dotted', 'fusion-builder' ),
									'underline'        => esc_html__( 'Underline', 'fusion-builder' ),
									'underline solid'  => esc_html__( 'Underline Solid', 'fusion-builder' ),
									'underline dashed' => esc_html__( 'Underline Dashed', 'fusion-builder' ),
									'underline dotted' => esc_html__( 'Underline Dotted', 'fusion-builder' ),
									'none'             => esc_html__( 'None', 'fusion-builder' ),
								),
							),
							'title_border_color' => array(
								'label'       => esc_html__( 'Title Separator Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the title separators.', 'fusion-builder' ),
								'id'          => 'title_border_color',
								'default'     => '#e0dede',
								'type'        => 'color-alpha',
							),
							'title_margin' => array(
								'label'       => esc_html__( 'Title Top/Bottom Margins', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the top/bottom margin of the titles. Leave empty to use corresponding heading margins.', 'fusion-builder' ),
								'id'          => 'title_margin',
								'default'     => array(
									'top'     => '0px',
									'bottom'  => '31px',
								),
								'type'        => 'spacing',
								'choices'     => array(
									'top'     => true,
									'bottom'  => true,
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

				Fusion_Dynamic_JS::enqueue_script(
					'fusion-title',
					FusionBuilder::$js_folder_url . '/general/fusion-title.js',
					FusionBuilder::$js_folder_path . '/general/fusion-title.js',
					array( 'jquery' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_Title();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_title() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'            => esc_attr__( 'Title', 'fusion-builder' ),
		'shortcode'       => 'fusion_title',
		'icon'            => 'fusiona-H',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-title-preview.php',
		'preview_id'      => 'fusion-builder-block-module-title-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the title text.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the title size, H1-H6.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => array(
					'1' => 'H1',
					'2' => 'H2',
					'3' => 'H3',
					'4' => 'H4',
					'5' => 'H5',
					'6' => 'H6',
				),
				'default' => '1',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Title Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to align the heading left or right.', 'fusion-builder' ),
				'param_name'  => 'content_align',
				'value'       => array(
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default' => 'left',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Separator', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the kind of the title separator you want to use.', 'fusion-builder' ),
				'param_name'  => 'style_type',
				'value'       => array(
					'default'          => esc_attr__( 'Default', 'fusion-builder' ),
					'single solid'     => esc_attr__( 'Single Solid', 'fusion-builder' ),
					'single dashed'    => esc_attr__( 'Single Dashed', 'fusion-builder' ),
					'single dotted'    => esc_attr__( 'Single Dotted', 'fusion-builder' ),
					'double solid'     => esc_attr__( 'Double Solid', 'fusion-builder' ),
					'double dashed'    => esc_attr__( 'Double Dashed', 'fusion-builder' ),
					'double dotted'    => esc_attr__( 'Double Dotted', 'fusion-builder' ),
					'underline solid'  => esc_attr__( 'Underline Solid', 'fusion-builder' ),
					'underline dashed' => esc_attr__( 'Underline Dashed', 'fusion-builder' ),
					'underline dotted' => esc_attr__( 'Underline Dotted', 'fusion-builder' ),
					'none'             => esc_attr__( 'None', 'fusion-builder' ),
				),
				'default' => 'default',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Separator Color', 'fusion-builder' ),
				'param_name'  => 'sep_color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the separator color. ', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design Options', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'style_type',
						'value'    => 'none',
						'operator' => '!=',
					),
				),
				'default'     => $fusion_settings->get( 'title_border_color' ),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'margin_top'    => '',
					'margin_bottom' => '',

				),
				'description'      => esc_attr__( 'Spacing above and below the title. In px, em or %, e.g. 10px.', 'fusion-builder' ),
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
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_title' );
