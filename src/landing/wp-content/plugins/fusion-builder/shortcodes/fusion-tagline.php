<?php

if ( fusion_is_element_enabled( 'fusion_tagline_box' ) ) {

	if ( ! class_exists( 'FusionSC_Tagline' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Tagline extends Fusion_Element {

			/**
			 * The tagline box counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $tagline_box_counter = 1;

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
				add_filter( 'fusion_attr_tagline-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_tagline-shortcode-reading-box', array( $this, 'reading_box_attr' ) );
				add_filter( 'fusion_attr_tagline-shortcode-button', array( $this, 'button_attr' ) );

				add_shortcode( 'fusion_tagline_box', array( $this, 'render' ) );

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

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
						'class'               => '',
						'id'                  => '',
						'backgroundcolor'     => $fusion_settings->get( 'tagline_bg' ),
						'border'              => '0px',
						'bordercolor'         => $fusion_settings->get( 'tagline_border_color' ),
						'button'              => '',
						'buttoncolor'         => 'default',
						'button_shape'        => $fusion_settings->get( 'button_shape' ),
						'button_size'         => $fusion_settings->get( 'button_size' ),
						'button_type'         => $fusion_settings->get( 'button_type' ),
						'content_alignment'   => 'left',
						'description'         => '',
						'highlightposition'   => 'left',
						'link'                => '',
						'linktarget'          => '_self',
						'margin_bottom'       => ( '' !== $fusion_settings->get( 'tagline_margin', 'bottom' ) ) ? $fusion_library->sanitize->size( $fusion_settings->get( 'tagline_margin', 'bottom' ) ) : '0px',
						'margin_top'          => ( '' !== $fusion_settings->get( 'tagline_margin', 'top' ) ) ? $fusion_library->sanitize->size( $fusion_settings->get( 'tagline_margin', 'top' ) ) : '0px',
						'modal'               => '',
						'shadow'              => 'no',
						'shadowopacity'       => '0.7',
						'title'               => '',
						'animation_type'      => '',
						'animation_direction' => 'left',
						'animation_speed'     => '',
						'animation_offset'    => $fusion_settings->get( 'animation_offset' ),
					), $args
				);
				$defaults = apply_filters( 'fusion_builder_default_args', $defaults, 'fusion_tagline_box' );

				$defaults['border'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border'], 'px' );

				if ( $defaults['modal'] ) {
					$defaults['link'] = '#';
				}

				$defaults['button_type'] = strtolower( $defaults['button_type'] );

				extract( $defaults );

				$this->args = $defaults;
				$desktop_button = $title_tag = $additional_content = '';

				$styles = apply_filters( 'fusion_builder_tagline_box_style', "<style type='text/css'>.reading-box-container-{$this->tagline_box_counter} .element-bottomshadow:before,.reading-box-container-{$this->tagline_box_counter} .element-bottomshadow:after{opacity:{$shadowopacity};}</style>", $defaults, $this->tagline_box_counter );

				if ( isset( $title ) && $title ) {
					$title_tag = '<h2>' . $title . '</h2>';
				}

				$addition_content_class = '';

				if ( isset( $description ) && $description ) {
					if ( isset( $title ) && $title ) {
						$addition_content_class = ' fusion-reading-box-additional';
					}

					$additional_content .= '<div class="reading-box-description' . $addition_content_class . '">' . $description . '</div>';
					$addition_content_class = '';
				} else {
					if ( isset( $title ) && $title ) {
						$addition_content_class = ' fusion-reading-box-additional';
					}
				}

				if ( $content ) {
					$additional_content .= '<div class="reading-box-additional' . $addition_content_class . '">' . do_shortcode( $content ) . '</div>';
				}

				if ( ( isset( $link ) && $link ) && ( isset( $button ) && $button ) && 'center' !== $this->args['content_alignment'] ) {

					$button_margin_class = '';
					if ( $additional_content ) {
						$button_margin_class = ' fusion-desktop-button-margin';
					}

					$this->args['button_class'] = ' fusion-desktop-button continue' . $button_margin_class;
					$desktop_button = '<a ' . FusionBuilder::attributes( 'tagline-shortcode-button' ) . '><span>' . $button . '</span></a>';
				}

				if ( $additional_content ) {
					$additional_content .= '<div class="fusion-clearfix"></div>';

					$additional_content = $desktop_button . $title_tag . $additional_content;
				} else if ( 'center' === $this->args['content_alignment'] ) {
					$additional_content = $title_tag;
				} else {
					$additional_content = '<div class="fusion-reading-box-flex">';
					if ( 'left' === $this->args['content_alignment'] ) {
						$additional_content .= $title_tag . $desktop_button;
					} else {
						$additional_content .= $desktop_button . $title_tag;
					}
					$additional_content .= '</div>';
				}

				if ( ( isset( $link ) && $link ) && ( isset( $button ) && $button ) ) {
					$this->args['button_class'] = ' fusion-mobile-button';
					$additional_content .= '<a ' . FusionBuilder::attributes( 'tagline-shortcode-button' ) . '><span>' . $button . '</span></a>';
				}

				$html = $styles . '<div ' . FusionBuilder::attributes( 'tagline-shortcode' ) . '><div ' . FusionBuilder::attributes( 'tagline-shortcode-reading-box' ) . '>' . $additional_content . '</div></div>';

				$this->tagline_box_counter++;

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
				global $fusion_library;

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], array(
					'class' => 'fusion-reading-box-container reading-box-container-' . $this->tagline_box_counter,
				) );

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

				$attr['style'] = '';

				if ( $this->args['margin_top'] || '0' === $this->args['margin_top'] ) {
					$attr['style'] .= 'margin-top:' . $fusion_library->sanitize->get_value_with_unit( $this->args['margin_top'] ) . ';';
				}

				if ( $this->args['margin_bottom'] || '0' === $this->args['margin_bottom'] ) {
					$attr['style'] .= 'margin-bottom:' . $fusion_library->sanitize->get_value_with_unit( $this->args['margin_bottom'] ) . ';';
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
			 * Builds the reading-box attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function reading_box_attr() {

				global $fusion_settings;

				$attr = array(
					'class' => 'reading-box',
				);

				if ( 'right' === $this->args['content_alignment'] ) {
					$attr['class'] .= ' reading-box-right';
				} elseif ( 'center' === $this->args['content_alignment'] ) {
					$attr['class'] .= ' reading-box-center';
				}

				if ( 'yes' === $this->args['shadow'] ) {
					$attr['class'] .= ' element-bottomshadow';
				}

				$attr['style']  = 'background-color:' . $this->args['backgroundcolor'] . ';';
				$attr['style'] .= 'border-width:' . $this->args['border'] . ';';
				$attr['style'] .= 'border-color:' . $this->args['bordercolor'] . ';';
				if ( 'none' !== $this->args['highlightposition'] ) {
					if ( str_replace( 'px', '', $this->args['border'] ) > 3 ) {
						$attr['style'] .= 'border-' . $this->args['highlightposition'] . '-width:' . $this->args['border'] . ';';
					} else {
						$attr['style'] .= 'border-' . $this->args['highlightposition'] . '-width:3px;';
					}
					$attr['style'] .= 'border-' . $this->args['highlightposition'] . '-color:' . $fusion_settings->get( 'primary_color' ) . ';';
				}
				$attr['style'] .= 'border-style:solid;';

				return $attr;
			}

			/**
			 * Builds the button attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function button_attr() {

				$attr = array(
					'class' => 'button fusion-button button-' . $this->args['buttoncolor'] . ' button-' . $this->args['button_shape'] . ' fusion-button-' . $this->args['button_size'] . ' button-' . $this->args['button_size'] . ' button-' . $this->args['button_type'] . $this->args['button_class'],
				);
				$attr['class'] = strtolower( $attr['class'] );

				if ( 'right' === $this->args['content_alignment'] ) {
					$attr['class'] .= ' continue-left';
				} elseif ( 'center' === $this->args['content_alignment'] ) {
					$attr['class'] .= ' continue-center';
				} else {
					$attr['class'] .= ' continue-right';
				}

				if ( 'flat' === $this->args['button_type'] ) {
					$attr['style'] = '-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;';
				}

				$attr['href'] = $this->args['link'];
				$attr['target'] = $this->args['linktarget'];

				if ( '_blank' == $attr['target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				if ( $this->args['modal'] ) {
					$attr['data-toggle'] = 'modal';
					$attr['data-target'] = '.' . $this->args['modal'];
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

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-reading-box-container' ), '.fusion-reading-box-container' );

				if ( 'yes' == $fusion_settings->get( 'button_span' ) ) {
					$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-desktop-button' );
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = 'auto';
				}

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .reading-box' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'tagline_bg' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				$css[ $content_media_query ]['.fusion-reading-box-container .fusion-reading-box-flex']['display'] = 'block';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-desktop-button' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';
				$css[ $content_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-mobile-button' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
				$css[ $content_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';

				$elements = $dynamic_css_helpers->map_selector( $elements, '.continue-center' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .continue-center' );
				$css[ $content_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .reading-box.reading-box-center' );
				$css[ $content_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .reading-box.reading-box-right' );
				$css[ $content_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'right';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .continue' );
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Tagline settings.
			 */
			public function add_options() {

				return array(
					'tagline_box_shortcode_section' => array(
						'label'       => esc_html__( 'Tagline Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'tagline_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'tagline_bg' => array(
								'label'       => esc_html__( 'Tagline Box Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the tagline box background.', 'fusion-builder' ),
								'id'          => 'tagline_bg',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'tagline_border_color' => array(
								'label'       => esc_html__( 'Tagline Box Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the tagline box.', 'fusion-builder' ),
								'id'          => 'tagline_border_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'tagline_margin' => array(
								'label'       => esc_html__( 'Tagline Top/Bottom Margins', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the top/bottom margin of the tagline box.', 'fusion-builder' ),
								'id'          => 'tagline_margin',
								'default'     => array(
									'top'     => '0px',
									'bottom'  => '84px',
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

				Fusion_Dynamic_JS::enqueue_script( 'fusion-button' );
			}
		}
	}

	new FusionSC_Tagline();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_tagline_box() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'            => esc_attr__( 'Tagline Box', 'fusion-builder' ),
		'shortcode'       => 'fusion_tagline_box',
		'icon'            => 'fusiona-list-alt',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-tagline-preview.php',
		'preview_id'      => 'fusion-builder-block-module-tagline-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color. ', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'tagline_bg' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Shadow', 'fusion-builder' ),
				'description' => esc_attr__( 'Show the shadow below the box.', 'fusion-builder' ),
				'param_name'  => 'shadow',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Shadow Opacity', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the opacity of the shadow.', 'fusion-builder' ),
				'param_name'  => 'shadowopacity',
				'value'       => array(
					'1'   => '1',
					'0.1' => '0.1',
					'0.2' => '0.2',
					'0.3' => '0.3',
					'0.4' => '0.4',
					'0.5' => '0.5',
					'0.6' => '0.6',
					'0.7' => '0.7',
					'0.8' => '0.8',
					'0.9' => '0.9',
				),
				'default'     => '0.7',
				'dependency'  => array(
					array(
						'element'  => 'shadow',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Border Size', 'fusion-builder' ),
				'param_name'  => 'border',
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'min'         => '0',
				'max'         => '20',
				'value'       => '1',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'tagline_border_color' ),
				'dependency'  => array(
					array(
						'element'  => 'border',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Highlight Border Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the highlight. This border highlight is from theme options primary color and does not take the color from border color above.', 'fusion-builder' ),
				'param_name'  => 'highlightposition',
				'value'       => array(
					'top'    => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom' => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'none'   => esc_attr__( 'None', 'fusion-builder' ),
				),
				'default'     => 'left',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how the content should be displayed.', 'fusion-builder' ),
				'param_name'  => 'content_alignment',
				'value'       => array(
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => 'left',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Link', 'fusion-builder' ),
				'description' => esc_attr__( 'The url the button will link to.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the text that will display in the button.', 'fusion-builder' ),
				'param_name'  => 'button',
				'value'       => '',
				'dependency'  => array(
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
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Modal Window Anchor', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the class name of the modal window you want to open on button click.', 'fusion-builder' ),
				'param_name'  => 'modal',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Size', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's size. Choose default for theme option selection.", 'fusion-builder' ),
				'param_name'  => 'button_size',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'small'  => esc_attr__( 'Small', 'fusion-builder' ),
					'medium' => esc_attr__( 'Medium', 'fusion-builder' ),
					'large'  => esc_attr__( 'Large', 'fusion-builder' ),
					'xlarge' => esc_attr__( 'XLarge', 'fusion-builder' ),
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Type', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's type. Choose default for theme option selection.", 'fusion-builder' ),
				'param_name'  => 'button_type',
				'value'       => array(
					''     => esc_attr__( 'Default', 'fusion-builder' ),
					'flat' => esc_attr__( 'Flat', 'fusion-builder' ),
					'3d'   => esc_attr__( '3D', 'fusion-builder' ),
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Shape', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's shape. Choose default for theme option selection.", 'fusion-builder' ),
				'param_name'  => 'button_shape',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'square' => esc_attr__( 'Square', 'fusion-builder' ),
					'pill'   => esc_attr__( 'Pill', 'fusion-builder' ),
					'round'  => esc_attr__( 'Round', 'fusion-builder' ),
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Button Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the button color. Default uses theme option selection.', 'fusion-builder' ),
				'param_name'  => 'buttoncolor',
				'value'       => array(
					'default'   => esc_attr__( 'Default', 'fusion-builder' ),
					'green'     => esc_attr__( 'Green', 'fusion-builder' ),
					'darkgreen' => esc_attr__( 'Dark Green', 'fusion-builder' ),
					'orange'    => esc_attr__( 'Orange', 'fusion-builder' ),
					'blue'      => esc_attr__( 'Blue', 'fusion-builder' ),
					'red'       => esc_attr__( 'Red', 'fusion-builder' ),
					'pink'      => esc_attr__( 'Pink', 'fusion-builder' ),
					'darkgray'  => esc_attr__( 'Dark Gray', 'fusion-builder' ),
					'lightgray' => esc_attr__( 'Light Gray', 'fusion-builder' ),
				),
				'default'     => 'default',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Tagline Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the title text.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Tagline Description', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the description text.', 'fusion-builder' ),
				'param_name'  => 'description',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Additional Content', 'fusion-builder' ),
				'description' => esc_attr__( 'This is additional content you can add to the tagline box. This will show below the title and description if one is used.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'description'      => esc_attr__( 'Spacing above and below the tagline. In px, em or %, e.g. 10px.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'margin_top'    => '',
					'margin_bottom' => '',
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
				'type'        => 'select',
				'heading'     => __( 'Speed of Animation', 'fusion-builder' ),
				'description' => __( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'value'       => array(
					'1'   => '1',
					'0.1' => '0.1',
					'0.2' => '0.2',
					'0.3' => '0.3',
					'0.4' => '0.4',
					'0.5' => '0.5',
					'0.6' => '0.6',
					'0.7' => '0.7',
					'0.8' => '0.8',
					'0.9' => '0.9',
				),
				'default'     => '0.3',
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
add_action( 'fusion_builder_before_init', 'fusion_element_tagline_box' );
