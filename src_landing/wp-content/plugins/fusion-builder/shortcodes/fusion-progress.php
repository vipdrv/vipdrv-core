<?php

if ( fusion_is_element_enabled( 'fusion_progress' ) ) {

	if ( ! class_exists( 'FusionSC_Progressbar' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Progressbar extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args = array();

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_progressbar-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_progressbar-shortcode-bar', array( $this, 'bar_attr' ) );
				add_filter( 'fusion_attr_progressbar-shortcode-content', array( $this, 'content_attr' ) );
				add_filter( 'fusion_attr_progressbar-shortcode-span', array( $this, 'span_attr' ) );

				add_shortcode( 'fusion_progress', array( $this, 'render' ) );

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
						'animated_stripes'  => 'no',
						'filledcolor'       => '',
						'height'            => $fusion_settings->get( 'progressbar_height' ),
						'percentage'        => '70',
						'show_percentage'   => 'yes',
						'striped'           => 'no',
						'textcolor'         => '',
						'text_position'     => $fusion_settings->get( 'progressbar_text_position' ),
						'unfilledcolor'     => '',
						'unit'              => '',
						'filledbordercolor' => $fusion_settings->get( 'progressbar_filled_border_color' ),
						'filledbordersize'  => $fusion_settings->get( 'progressbar_filled_border_size' ),
					), $args
				);

				$defaults['filledbordersize'] = FusionBuilder::validate_shortcode_attr_value( $defaults['filledbordersize'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				if ( ! $filledcolor ) {
					$this->args['filledcolor'] = $fusion_settings->get( 'progressbar_filled_color' );
				}

				if ( ! $textcolor ) {
					$this->args['textcolor'] = $fusion_settings->get( 'progressbar_text_color' );
				}

				if ( ! $unfilledcolor ) {
					$this->args['unfilledcolor'] = $fusion_settings->get( 'progressbar_unfilled_color' );
				}

				$text = '<span ' . FusionBuilder::attributes( 'fusion-progressbar-text' ) . '>' . $content . '</span>';

				$value = '';
				if ( 'yes' == $show_percentage ) {
					$value = '<span ' . FusionBuilder::attributes( 'fusion-progressbar-value' ) . '>' . $percentage . $unit . '</span>';
				}

				$text_wrapper = '<span ' . FusionBuilder::attributes( 'progressbar-shortcode-span' ) . '>' . $text . ' ' . $value . '</span>';

				$bar = '<div ' . FusionBuilder::attributes( 'progressbar-shortcode-bar' ) . '><div ' . FusionBuilder::attributes( 'progressbar-shortcode-content' ) . '></div></div>';

				if ( 'above_bar' == $text_position ) {
					return '<div ' . FusionBuilder::attributes( 'progressbar-shortcode' ) . '>' . $text_wrapper . ' ' . $bar . '</div>';
				}

				return '<div ' . FusionBuilder::attributes( 'progressbar-shortcode' ) . '>' . $bar . ' ' . $text_wrapper . '</div>';

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = array(
					'class' => 'fusion-progressbar',
				);

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( 'above_bar' == $this->args['text_position'] ) {
					$attr['class'] .= ' fusion-progressbar-text-above-bar';
				} elseif ( 'below_bar' == $this->args['text_position'] ) {
					$attr['class'] .= ' fusion-progressbar-text-below-bar';
				} else {
					$attr['class'] .= ' fusion-progressbar-text-on-bar';
				}

				return $attr;

			}

			/**
			 * Builds the bar attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function bar_attr() {

				$attr = array(
					'style' => 'background-color:' . $this->args['unfilledcolor'] . ';',
					'class' => 'fusion-progressbar-bar progress-bar',
				);

				if ( $this->args['height'] ) {
					$attr['style'] .= 'height:' . $this->args['height'] . ';';
				}

				if ( 'yes' == $this->args['striped'] ) {
					$attr['class'] .= ' progress-striped';
				}

				if ( 'yes' == $this->args['animated_stripes'] ) {
					$attr['class'] .= ' active';
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
			 * Builds the content attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function content_attr() {

				$attr = array(
					'class' => 'progress progress-bar-content',
					'style' => 'width:0%;background-color:' . $this->args['filledcolor'] . ';',
				);

				if ( $this->args['filledbordersize'] && $this->args['filledbordercolor'] ) {
					$attr['style'] .= 'border: ' . $this->args['filledbordersize'] . ' solid ' . $this->args['filledbordercolor'] . ';';
				}

				$attr['role'] = 'progressbar';
				$attr['aria-valuemin'] = '0';
				$attr['aria-valuemax'] = '100';

				$attr['aria-valuenow'] = $this->args['percentage'];

				return $attr;

			}

			/**
			 * Builds the span attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function span_attr() {
				return array(
					'class' => 'progress-title',
					'style' => 'color:' . $this->args['textcolor'] . ';',
				);
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access protected
			 * @since 1.1
			 * @return array
			 */
			protected function add_styling() {

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-progressbar-bar' ), '.fusion-progressbar-bar' );

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .progress-bar-content' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'counter_filled_color' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color']     = $fusion_library->sanitize->color( $fusion_settings->get( 'counter_filled_color' ) );

				$css['global'][ $dynamic_css_helpers->implode( $main_elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'counter_unfilled_color' ) );
				$css['global'][ $dynamic_css_helpers->implode( $main_elements ) ]['border-color']     = $fusion_library->sanitize->color( $fusion_settings->get( 'counter_unfilled_color' ) );

				$css[ $content_media_query ]['.fusion-progressbar']['margin-bottom'] = '10px !important';
				$css[ $six_fourty_media_query ]['.fusion-progressbar']['margin-bottom'] = '10px !important';
				$css[ $three_twenty_six_fourty_media_query ]['.fusion-progressbar']['margin-bottom'] = '10px !important';
				$css[ $ipad_portrait_media_query ]['.fusion-progressbar']['margin-bottom'] = '10px !important';

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access protected
			 * @since 1.1
			 * @return array $sections Progress Bar settings.
			 */
			protected function add_options() {

				return array(
					'progress_shortcode_section' => array(
						'label'       => esc_html__( 'Progress Bar Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'progressbar_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'progressbar_height' => array(
								'label'       => esc_html__( 'Progress Bar Height', 'fusion-builder' ),
								'description' => esc_html__( 'Insert a height for the progress bar.', 'fusion-builder' ),
								'id'          => 'progressbar_height',
								'default'     => '37px',
								'type'        => 'dimension',
							),
							'progressbar_text_position' => array(
								'label'       => esc_html__( 'Text Position', 'fusion-builder' ),
								'description' => esc_html__( 'Select the position of the progress bar text. Choose "Default" for theme option selection.', 'fusion-builder' ),
								'id'          => 'progressbar_text_position',
								'default'     => 'on_bar',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'on_bar'    => esc_html__( 'On Bar', 'fusion-builder' ),
									'above_bar' => esc_html__( 'Above Bar', 'fusion-builder' ),
									'below_bar' => esc_html__( 'Below Bar', 'fusion-builder' ),
								),
							),
							'progressbar_filled_color' => array(
								'label'       => esc_html__( 'Progress Bar Filled Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the progress bar filled area.', 'fusion-builder' ),
								'id'          => 'progressbar_filled_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'progressbar_filled_border_color' => array(
								'label'       => esc_html__( 'Progress Bar Filled Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the progress bar filled area.', 'fusion-builder' ),
								'id'          => 'progressbar_filled_border_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
							'progressbar_filled_border_size' => array(
								'label'       => esc_html__( 'Progress Bar Filled Border Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border size of the progress bar filled area.', 'fusion-builder' ),
								'id'          => 'progressbar_filled_border_size',
								'default'     => '0',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '20',
									'step' => '1',
								),
							),
							'progressbar_unfilled_color' => array(
								'label'       => esc_html__( 'Progress Bar Unfilled Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the progress bar unfilled area.', 'fusion-builder' ),
								'id'          => 'progressbar_unfilled_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'progressbar_text_color' => array(
								'label'       => esc_html__( 'Progress Bar Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the progress bar text.', 'fusion-builder' ),
								'id'          => 'progressbar_text_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
						),
					),
				);
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access protected
			 * @since 1.1
			 * @return void
			 */
			protected function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script(
					'fusion-progress',
					FusionBuilder::$js_folder_url . '/general/fusion-progress.js',
					FusionBuilder::$js_folder_path . '/general/fusion-progress.js',
					array( 'jquery', 'jquery-waypoints', 'fusion-waypoints', 'jquery-appear' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_Progressbar();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_progress() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Progress Bar', 'fusion-builder' ),
		'shortcode'  => 'fusion_progress',
		'icon'       => 'fusiona-tasks',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-progress-preview.php',
		'preview_id' => 'fusion-builder-block-module-progress-preview-template',
		'params'     => array(
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Progress Bar Height', 'fusion-builder' ),
				'description'      => esc_attr__( 'Insert a height for the progress bar. Enter value including any valid CSS unit, ex: 10px. ', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'height' => '',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the position of the progress bar text. Choose "Default" for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'text_position',
				'value'       => array(
					''          => esc_attr__( 'Default', 'fusion-builder' ),
					'on_bar'    => esc_attr__( 'On Bar', 'fusion-builder' ),
					'above_bar' => esc_attr__( 'Above Bar', 'fusion-builder' ),
					'below_bar' => esc_attr__( 'Below Bar', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Percentage Value', 'fusion-builder' ),
				'description' => esc_attr__( 'Select if you want the filled area percentage value to be shown.', 'fusion-builder' ),
				'param_name'  => 'show_percentage',
				'value'       => array(
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Progress Bar Unit', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert a unit for the progress bar. ex %.', 'fusion-builder' ),
				'param_name'  => 'unit',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'show_percentage',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Area Percentage', 'fusion-builder' ),
				'description' => esc_attr__( 'From 1% to 100%.', 'fusion-builder' ),
				'param_name'  => 'percentage',
				'value'       => '70',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the filled in area. ', 'fusion-builder' ),
				'param_name'  => 'filledcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'progressbar_filled_color' ),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Border Size', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'param_name'  => 'filledbordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => $fusion_settings->get( 'progressbar_filled_border_size' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color of the filled in area. ', 'fusion-builder' ),
				'param_name'  => 'filledbordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'progressbar_filled_border_color' ),
				'dependency'  => array(
					array(
						'element'  => 'filledbordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Unfilled Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the unfilled in area. ', 'fusion-builder' ),
				'param_name'  => 'unfilledcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'progressbar_unfilled_color' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Striped Filling', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get the filled area striped.', 'fusion-builder' ),
				'param_name'  => 'striped',
				'value'       => array(
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Animated Stripes', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get the the stripes animated.', 'fusion-builder' ),
				'param_name'  => 'animated_stripes',
				'value'       => array(
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'striped',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Progess Bar Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Text will show up on progess bar.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the text color. ', 'fusion-builder' ),
				'param_name'  => 'textcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'progressbar_text_color' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_progress' );
