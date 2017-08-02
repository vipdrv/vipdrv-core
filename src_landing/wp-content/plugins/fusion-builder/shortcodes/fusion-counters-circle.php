<?php

if ( fusion_is_element_enabled( 'fusion_counters_circle' ) ) {

	if ( ! class_exists( 'FusionSC_CountersCircle' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_CountersCircle extends Fusion_Element {

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
				add_filter( 'fusion_attr_counters-circle-shortcode', array( $this, 'parent_attr' ) );
				add_shortcode( 'fusion_counters_circle', array( $this, 'render_parent' ) );

				add_filter( 'fusion_attr_counter-circle-shortcode', array( $this, 'child_attr' ) );
				add_filter( 'fusion_attr_counter-circle-wrapper-shortcode', array( $this, 'child_wrapper_attr' ) );
				add_shortcode( 'fusion_counter_circle', array( $this, 'render_child' ) );

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

				global $fusion_settings;

				$defaults = shortcode_atts(
					array(
						'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
						'class'            => '',
						'id'               => '',
						'animation_offset' => $fusion_settings->get( 'animation_offset' ),
					), $args
				);

				extract( $defaults );

				$this->parent_args = $defaults;

				return '<div ' . FusionBuilder::attributes( 'counters-circle-shortcode' ) . '>' . do_shortcode( $content ) . '</div>';

			}

			/**
			 * Builds the parent attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function parent_attr() {

				$attr = fusion_builder_visibility_atts(
					$this->parent_args['hide_on_mobile'],
					array(
						'class' => 'fusion-counters-circle counters-circle',
					)
				);

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
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child( $args, $content = '' ) {

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'class'         => '',
						'id'            => '',
						'countdown'     => 'no',
						'filledcolor'   => strtolower( $fusion_settings->get( 'counter_filled_color' ) ),
						'unfilledcolor' => strtolower( $fusion_settings->get( 'counter_unfilled_color' ) ),
						'scales'        => 'no',
						'size'          => '220',
						'speed'         => '1500',
						'value'         => '1',
					), $args
				);

				$defaults['size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['size'], '' );

				extract( $defaults );

				$this->child_args = $defaults;

				$this->child_args['scales'] = false;
				if ( 'yes' == $scales ) {
					$this->child_args['scales'] = true;
				}

				$this->child_args['countdown'] = false;
				if ( 'yes' == $countdown ) {
					$this->child_args['countdown'] = true;
				}

				$output = '<div ' . FusionBuilder::attributes( 'counter-circle-shortcode' ) . '>' . do_shortcode( $content ) . '</div>';

				return '<div ' . FusionBuilder::attributes( 'counter-circle-wrapper-shortcode' ) . '>' . $output . '</div>';

			}

			/**
			 * Builds the child attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function child_attr() {

				$attr = array(
					'class' => 'fusion-counter-circle counter-circle counter-circle-content',
				);

				if ( $this->child_args['class'] ) {
					$attr['class'] .= ' ' . $this->child_args['class'];
				}

				if ( $this->child_args['id'] ) {
					$attr['id'] = $this->child_args['id'];
				}

				$multiplicator = $this->child_args['size'] / 220;
				$stroke_size   = 11 * $multiplicator;
				$font_size     = 50 * $multiplicator;

				$attr['data-percent']       = $this->child_args['value'];
				$attr['data-countdown']     = $this->child_args['countdown'];
				$attr['data-filledcolor']   = $this->child_args['filledcolor'];
				$attr['data-unfilledcolor'] = $this->child_args['unfilledcolor'];
				$attr['data-scale']         = $this->child_args['scales'];
				$attr['data-size']          = $this->child_args['size'];
				$attr['data-speed']         = $this->child_args['speed'];
				$attr['data-strokesize']    = $stroke_size;

				$attr['style'] = 'font-size:' . $font_size . 'px;height:' . $this->child_args['size'] . 'px;width:' . $this->child_args['size'] . 'px;line-height:' . $this->child_args['size'] . 'px;';

				return $attr;

			}

			/**
			 * Builds the child-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function child_wrapper_attr() {

				$attr = array(
					'class' => 'counter-circle-wrapper',
					'style' => 'height:' . $this->child_args['size'] . 'px;width:' . $this->child_args['size'] . 'px;line-height:' . $this->child_args['size'] . 'px;',
					'data-originalsize' => $this->child_args['size'],
				);

				if ( $this->parent_args['animation_offset'] ) {
					$animations = FusionBuilder::animations( array( 'offset' => $this->parent_args['animation_offset'] ) );
					$attr       = array_merge( $attr, $animations );
				}
				return $attr;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Counter Circle settings.
			 */
			public function add_options() {

				return array(
					'counters_circle_shortcode_section' => array(
						'label'       => esc_html__( 'Counter Circle Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'cc_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'counter_filled_color' => array(
								'label'       => esc_html__( 'Counter Circle Filled Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the filled circle.', 'fusion-builder' ),
								'id'          => 'counter_filled_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'counter_unfilled_color' => array(
								'label'       => esc_html__( 'Counter Circle Unfilled Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the unfilled circle.', 'fusion-builder' ),
								'id'          => 'counter_unfilled_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
						),
					),
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
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query;

				$css[ $six_fourty_media_query ]['.fusion-counters-circle .counter-circle-wrapper']['display']      = 'block';
				$css[ $six_fourty_media_query ]['.fusion-counters-circle .counter-circle-wrapper']['margin-right'] = 'auto';
				$css[ $six_fourty_media_query ]['.fusion-counters-circle .counter-circle-wrapper']['margin-left']  = 'auto';

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
					'fusion-counters-circle',
					FusionBuilder::$js_folder_url . '/general/fusion-counters-circle.js',
					FusionBuilder::$js_folder_path . '/general/fusion-counters-circle.js',
					array( 'jquery', 'fusion-animations', 'jquery-count-to', 'jquery-easy-pie-chart', 'jquery-appear' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_CountersCircle();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_counters_circle() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Counter Circles', 'fusion-builder' ),
		'shortcode'     => 'fusion_counters_circle',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_counter_circle',
		'icon'          => 'fusiona-clock',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_counter_circle value="50" filledcolor="" unfilledcolor="" size="220" scales="no" countdown="no" speed="1500"]' . esc_attr__( 'Your Content Goes Here', 'fusion-builder' ) . '[/fusion_counter_circle]',
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
add_action( 'fusion_builder_before_init', 'fusion_element_counters_circle' );

/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_counter_circle() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'              => esc_attr__( 'Counter Circle', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this block.', 'fusion-builder' ),
		'shortcode'         => 'fusion_counter_circle',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Area Percentage', 'fusion-builder' ),
				'description' => esc_attr__( 'From 1% to 100%.', 'fusion-builder' ),
				'param_name'  => 'value',
				'value'       => '50',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Color', 'fusion-builder' ),
				'param_name'  => 'filledcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'counter_filled_color' ),
				'description' => esc_attr__( 'Controls the color of the filled in area. ', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Unfilled Color', 'fusion-builder' ),
				'param_name'  => 'unfilledcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'counter_unfilled_color' ),
				'description' => esc_attr__( 'Controls the color of the unfilled in area. ', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Size of the Counter', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert size of the counter in px. ex: 220.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => '220',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Scales', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show a scale around circles.', 'fusion-builder' ),
				'param_name'  => 'scales',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Countdown', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to let the circle filling move counter clockwise.', 'fusion-builder' ),
				'param_name'  => 'countdown',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Animation Speed', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert animation speed in milliseconds.', 'fusion-builder' ),
				'param_name'  => 'speed',
				'value'       => '1500',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Circle Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert text for counter circle box, keep it short.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_counter_circle' );
