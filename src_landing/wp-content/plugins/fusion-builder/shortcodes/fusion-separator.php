<?php

if ( fusion_is_element_enabled( 'fusion_separator' ) ) {

	if ( ! class_exists( 'FusionSC_Separator' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Separator extends Fusion_Element {

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
				add_filter( 'fusion_attr_separator-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_separator-shortcode-icon-wrapper', array( $this, 'icon_wrapper_attr' ) );
				add_filter( 'fusion_attr_separator-shortcode-icon', array( $this, 'icon_attr' ) );

				add_shortcode( 'fusion_separator', array( $this, 'render' ) );
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
						'hide_on_mobile'    => fusion_builder_default_visibility( 'string' ),
						'class'             => '',
						'id'                => '',
						'alignment'         => 'center',
						'bottom_margin'     => '',
						'border_size'       => $fusion_settings->get( 'separator_border_size' ),
						'icon'              => '',
						'icon_circle'       => $fusion_settings->get( 'separator_circle' ),
						'icon_circle_color' => '',
						'sep_color'         => $fusion_settings->get( 'sep_color' ),
						'style_type'        => 'none',
						'top_margin'        => '',
						'width'             => '',
						'bottom'            => '', // Deprecated.
						'color'             => '', // Deprecated.
						'style'             => '', // Deprecated.
						'top'               => '', // Deprecated.
					), $args
				);

				$defaults['border_size']   = FusionBuilder::validate_shortcode_attr_value( $defaults['border_size'], 'px' );
				$defaults['width']         = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
				$defaults['top_margin']    = FusionBuilder::validate_shortcode_attr_value( $defaults['top_margin'], 'px' );
				$defaults['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $defaults['bottom_margin'], 'px' );

				if ( '0' === $defaults['icon_circle'] ) {
					$defaults['icon_circle'] = 'no';
				}

				if ( $defaults['style'] ) {
					$defaults['style_type'] = $defaults['style'];
				} elseif ( 'default' === $defaults['style_type'] ) {
					$defaults['style_type'] = $fusion_settings->get( 'separator_style_type' );
				}

				extract( $defaults );

				$this->args = $defaults;

				$this->args['style_type'] = str_replace( ' ', '|', $style_type );

				if ( $bottom ) {
					$this->args['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $bottom, 'px' );
				}

				if ( $color ) {
					$this->args['sep_color'] = $color;
				}

				if ( $top ) {
					$this->args['top_margin'] = FusionBuilder::validate_shortcode_attr_value( $top, 'px' );

					if ( ! $bottom && 'none' != $defaults['style'] ) {
						$this->args['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $top, 'px' );
					}
				}

				$icon_insert = '';
				if ( $icon && 'none' !== $style_type ) {
					$icon_insert = '<span ' . FusionBuilder::attributes( 'separator-shortcode-icon-wrapper' ) . '><i ' . FusionBuilder::attributes( 'separator-shortcode-icon' ) . '></i></span>';
				}

				$html = '<div ' . FusionBuilder::attributes( 'fusion-sep-clear' ) . '></div><div ' . FusionBuilder::attributes( 'separator-shortcode' ) . '>' . $icon_insert . '</div>';

				if ( 'right' === $this->args['alignment'] ) {
					$html .= '<div ' . FusionBuilder::attributes( 'fusion-sep-clear' ) . '></div>';
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
					'class' => 'fusion-separator',
					'style' => '',
				) );

				if ( ! $this->args['width'] || '100%' == $this->args['width'] ) {
					$attr['class'] .= ' fusion-full-width-sep';
				}

				$styles = explode( '|', $this->args['style_type'] );

				if ( ! in_array( 'none', $styles ) && ! in_array( 'single', $styles ) && ! in_array( 'double', $styles ) && ! in_array( 'shadow', $styles ) ) {
					$styles[] .= 'single';
				}

				foreach ( $styles as $style ) {
					$attr['class'] .= ' sep-' . $style;
				}

				if ( $this->args['sep_color'] ) {
					if ( 'shadow' === $this->args['style_type'] ) {

						$shadow = 'background:radial-gradient(ellipse at 50% -50% , ' . $this->args['sep_color'] . ' 0px, rgba(255, 255, 255, 0) 80%) repeat scroll 0 0 rgba(0, 0, 0, 0);';

						$attr['style']  = $shadow;
						$attr['style'] .= str_replace( 'radial-gradient', '-webkit-radial-gradient', $shadow );
						$attr['style'] .= str_replace( 'radial-gradient', '-moz-radial-gradient', $shadow );
						$attr['style'] .= str_replace( 'radial-gradient', '-o-radial-gradient', $shadow );
					} elseif ( 'none' !== $this->args['style_type'] ) {

						$attr['style'] = 'border-color:' . $this->args['sep_color'] . ';';
					}
				}

				if ( in_array( 'single', $styles ) ) {
					$attr['style'] .= 'border-top-width:' . $this->args['border_size'] . ';';
				}

				if ( in_array( 'double', $styles ) ) {
					$attr['style'] .= 'border-top-width:' . $this->args['border_size'] . ';border-bottom-width:' . $this->args['border_size'] . ';';
				}

				if ( 'center' === $this->args['alignment'] ) {
					$attr['style'] .= 'margin-left: auto;margin-right: auto;';
				} elseif ( 'right' === $this->args['alignment'] ) {
					$attr['style'] .= 'float:right;';
					$attr['class'] .= ' fusion-clearfix';
				}

				$attr['style'] .= 'margin-top:' . $this->args['top_margin'] . ';';

				if ( $this->args['bottom_margin'] ) {
					$attr['style'] .= 'margin-bottom:' . $this->args['bottom_margin'] . ';';
				}

				if ( $this->args['width'] ) {
					$attr['style'] .= 'width:100%;max-width:' . $this->args['width'] . ';';
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
			 * Builds the icon-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_wrapper_attr() {

				$attr = array(
					'class' => 'icon-wrapper',
				);

				$circle_color = $this->args['sep_color'];
				if ( 'no' === $this->args['icon_circle'] ) {
					$circle_color = 'transparent';
				}

				$attr['style'] = 'border-color:' . $circle_color . ';';

				if ( $this->args['icon_circle_color'] ) {
					$attr['style'] .= 'background-color:' . $this->args['icon_circle_color'] . ';';
				}

				return $attr;

			}

			/**
			 * Builds the icon attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_attr() {
				return array(
					'class' => 'fa ' . FusionBuilder::font_awesome_name_handler( $this->args['icon'] ),
					'style' => 'color:' . $this->args['sep_color'] . ';',
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

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-separator' ), '.fusion-separator' );

				$elements = array_merge(
					$dynamic_css_helpers->map_selector( $main_elements, '.sep-single' ),
					$dynamic_css_helpers->map_selector( $main_elements, '.sep-double' ),
					$dynamic_css_helpers->map_selector( $main_elements, '.sep-dashed' ),
					$dynamic_css_helpers->map_selector( $main_elements, '.sep-dotted' )
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'sep_color' ) );

				// Content separator for blog.
				$elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-body .fusion-content-sep' ), '.fusion-body .fusion-content-sep' );

				$separator_style_type = $fusion_settings->get( 'separator_style_type' );
				$separator_border_size = $fusion_settings->get( 'separator_border_size' ) . 'px';
				if ( false !== strpos( $separator_style_type, 'none' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';
				} else if ( false !== strpos( $separator_style_type, 'single' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = 'auto';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom'] = 'none';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top-width'] = $separator_border_size;
				} else if ( false !== strpos( $separator_style_type, 'double' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top-width'] = $separator_border_size;
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-width'] = $separator_border_size;
				} else if ( false !== strpos( $separator_style_type, 'shadow' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '1px';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top'] = 'none';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom'] = 'none';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = sprintf( '-webkit-radial-gradient(ellipse at 50%% -50%% , %s 0px, rgba(255, 255, 255, 0) 80%%) repeat scroll 0 0 rgba(0, 0, 0, 0)',  $fusion_settings->get( 'sep_color' ) );
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = sprintf( 'radial-gradient(ellipse at 50%% -50%% , %s 0px, rgba(255, 255, 255, 0) 80%%) repeat scroll 0 0 rgba(0, 0, 0, 0)',  $fusion_settings->get( 'sep_color' ) );

					$elements = $dynamic_css_helpers->map_selector( $elements, ':after' );
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = '10px';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '6px';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = '-webkit-radial-gradient(ellipse at 50% -50% , rgba(0, 0, 0, 0.5) 0px, rgba(255, 255, 255, 0) 65%);';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = 'radial-gradient(ellipse at 50% -50% , rgba(0, 0, 0, 0.5) 0px, rgba(255, 255, 255, 0) 65%);';
				}

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Separator settings.
			 */
			public function add_options() {

				return array(
					'separator_shortcode_section' => array(
						'label'       => esc_html__( 'Separator Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'separator_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'style_type' => array(
								'label'       => esc_html__( 'Separator Style', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the line style of all separators, divider lines on portfolio archives, blog archives, product archives and more.', 'fusion-builder' ),
								'id'          => 'separator_style_type',
								'default'     => 'double',
								'type'        => 'select',
								'choices'       => array(
									'none'          => esc_attr__( 'No Style', 'fusion-builder' ),
									'single'        => esc_attr__( 'Single Border Solid', 'fusion-builder' ),
									'double'        => esc_attr__( 'Double Border Solid', 'fusion-builder' ),
									'single|dashed' => esc_attr__( 'Single Border Dashed', 'fusion-builder' ),
									'double|dashed' => esc_attr__( 'Double Border Dashed', 'fusion-builder' ),
									'single|dotted' => esc_attr__( 'Single Border Dotted', 'fusion-builder' ),
									'double|dotted' => esc_attr__( 'Double Border Dotted', 'fusion-builder' ),
									'shadow'        => esc_attr__( 'Shadow', 'fusion-builder' ),
								),
							),
							'sep_color' => array(
								'label'       => esc_html__( 'Separator Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of all separators, divider lines and borders for meta, previous & next, filters, archive pages, boxes around number pagination, sidebar widgets, accordion divider lines, counter boxes and more.', 'fusion-builder' ),
								'id'          => 'sep_color',
								'default'     => '#e0dede',
								'type'        => 'color-alpha',
							),
							'separator_circle' => array(
								'label'       => esc_html__( 'Separator Circle', 'fusion-builder' ),
								'description' => esc_html__( 'Turn on if you want to display a circle around the separator icon.', 'fusion-builder' ),
								'id'          => 'separator_circle',
								'default'     => '1',
								'type'        => 'switch',
							),
							'separator_border_size' => array(
								'label'       => esc_html__( 'Border Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border size of the separator.', 'fusion-builder' ),
								'id'          => 'separator_border_size',
								'default'     => '1',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '50',
									'step' => '1',
								),
							),
						),
					),
				);

				return $sections;

			}
		}
	}

	new FusionSC_Separator();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_separator() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Separator', 'fusion-builder' ),
		'shortcode'  => 'fusion_separator',
		'icon'       => 'fusiona-minus',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-separator-preview.php',
		'preview_id' => 'fusion-builder-block-module-separator-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Style', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the separator line style.', 'fusion-builder' ),
				'param_name'  => 'style_type',
				'value'       => array(
					'none'          => esc_attr__( 'No Style', 'fusion-builder' ),
					'single solid'  => esc_attr__( 'Single Border Solid', 'fusion-builder' ),
					'double solid'  => esc_attr__( 'Double Border Solid', 'fusion-builder' ),
					'single|dashed' => esc_attr__( 'Single Border Dashed', 'fusion-builder' ),
					'double|dashed' => esc_attr__( 'Double Border Dashed', 'fusion-builder' ),
					'single|dotted' => esc_attr__( 'Single Border Dotted', 'fusion-builder' ),
					'double|dotted' => esc_attr__( 'Double Border Dotted', 'fusion-builder' ),
					'shadow'        => esc_attr__( 'Shadow', 'fusion-builder' ),
					'default'       => esc_attr__( 'Default', 'fusion-builder' ),
				),
				'default'     => 'none',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Separator Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the separator color. ', 'fusion-builder' ),
				'param_name'  => 'sep_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'sep_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'top_margin'    => '',
					'bottom_margin' => '',

				),
				'description' => esc_attr__( 'Spacing above and below the separator. In px, em or %, e.g. 10px.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Border Size', 'fusion-builder' ),
				'param_name'  => 'border_size',
				'value'       => '',
				'min'         => '0',
				'max'         => '50',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'separator_border_size' ),
				'description' => esc_attr__( 'In pixels. ', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Circled Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have a circle in separator color around the icon.', 'fusion-builder' ),
				'param_name'  => 'icon_circle',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Circle Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the circle around the icon.', 'fusion-builder' ),
				'param_name'  => 'icon_circle_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'icon_circle_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Separator Width', 'fusion-builder' ),
				'param_name'       => 'dimensions_width',
				'value'            => array(
					'width' => '',
				),
				'description'      => esc_attr__( 'In pixels (px or %), ex: 1px, ex: 50%. Leave blank for full width.', 'fusion-builder' ),
				'group'            => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the separator alignment; only works when a width is specified.', 'fusion-builder' ),
				'param_name'  => 'alignment',
				'value'       => array(
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => 'center',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_separator' );
