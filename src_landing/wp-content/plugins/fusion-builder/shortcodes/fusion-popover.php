<?php

if ( fusion_is_element_enabled( 'fusion_popover' ) ) {

	if ( ! class_exists( 'FusionSC_Popover' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Popover extends Fusion_Element {

			/**
			 * The popover counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $popover_counter = 1;

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
				add_filter( 'fusion_attr_popover-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_popover', array( $this, 'render' ) );

			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args       Shortcode parameters.
			 * @param  string $sc_content Content between shortcode.
			 * @return string             HTML output.
			 */
			public function render( $args, $sc_content = '' ) {

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'class'            => '',
						'id'               => '',
						'animation'        => true,
						'content'          => '',
						'content_bg_color' => $fusion_settings->get( 'popover_content_bg_color' ),
						'delay'            => '50',
						'placement'        => strtolower( $fusion_settings->get( 'popover_placement' ) ),
						'title'            => '',
						'title_bg_color'   => $fusion_settings->get( 'popover_heading_bg_color' ),
						'bordercolor'      => $fusion_settings->get( 'popover_border_color' ),
						'textcolor'        => $fusion_settings->get( 'popover_text_color' ),
						'trigger'          => 'click',
					), $args
				);

				if ( 'default' === $defaults['placement'] ) {
					$defaults['placement'] = strtolower( $fusion_settings->get( 'popover_placement' ) );
				}

				extract( $defaults );

				$this->args = $defaults;

				$arrow_color = $content_bg_color;
				if ( 'bottom' === $placement ) {
					$arrow_color = $title_bg_color;
				}

				$styles  = '<style type="text/css">';
				$styles .= '.popover-' . $this->popover_counter . '.' . $placement . ' .arrow{border-' . $placement . '-color:' . $bordercolor . ';}';
				$styles .= '.popover-' . $this->popover_counter . '{border-color:' . $bordercolor . ';}';
				$styles .= '.popover-' . $this->popover_counter . ' .popover-title{background-color:' . $title_bg_color . ';color:' . $textcolor . ';border-color:' . $bordercolor . ';}';
				$styles .= '.popover-' . $this->popover_counter . ' .popover-content{background-color:' . $content_bg_color . ';color:' . $textcolor . ';}';
				$styles .= '.popover-' . $this->popover_counter . '.' . $placement . ' .arrow:after{border-' . $placement . '-color:' . $arrow_color . ';}';
				$styles .= '</style>';

				$html = '<span ' . FusionBuilder::attributes( 'popover-shortcode' ) . '>' . $styles . do_shortcode( $sc_content ) . '</span>';

				$this->popover_counter++;

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

				$attr = array(
					'class' => 'fusion-popover popover-' . $this->popover_counter,
				);

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				$attr['data-animation'] = $this->args['animation'];
				$attr['data-class']     = 'popover-' . $this->popover_counter;
				$attr['data-container'] = 'popover-' . $this->popover_counter;
				$attr['data-content']   = $this->args['content'];
				$attr['data-delay']     = $this->args['delay'];
				$attr['data-placement'] = strtolower( $this->args['placement'] );
				$attr['data-title']     = $this->args['title'];
				$attr['data-toggle']    = 'popover';
				$attr['data-trigger']   = $this->args['trigger'];

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

				global $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-popover' ), '.fusion-popover' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Popover settings.
			 */
			public function add_options() {

				return array(
					'popover_shortcode_section' => array(
						'label'       => esc_html__( 'Popover Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'popover_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'popover_heading_bg_color' => array(
								'label'       => esc_html__( 'Popover Heading Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the popover heading background.', 'fusion-builder' ),
								'id'          => 'popover_heading_bg_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
							'popover_content_bg_color' => array(
								'label'       => esc_html__( 'Popover Content Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of popover content background.', 'fusion-builder' ),
								'id'          => 'popover_content_bg_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
							'popover_border_color' => array(
								'label'       => esc_html__( 'Popover Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of popover box.', 'fusion-builder' ),
								'id'          => 'popover_border_color',
								'default'     => '#ebebeb',
								'type'        => 'color-alpha',
							),
							'popover_text_color' => array(
								'label'       => esc_html__( 'Popover Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the popover text.', 'fusion-builder' ),
								'id'          => 'popover_text_color',
								'default'     => '#747474',
								'type'        => 'color',
							),
							'popover_placement' => array(
								'label'       => esc_html__( 'Popover Position', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the position of the popover in reference to the triggering element.', 'fusion-builder' ),
								'id'          => 'popover_placement',
								'default'     => 'Top',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Top'    => esc_html__( 'Top', 'fusion-builder' ),
									'Right'  => esc_html__( 'Right', 'fusion-builder' ),
									'Bottom' => esc_html__( 'Bottom', 'fusion-builder' ),
									'Left'   => esc_html__( 'Left', 'fusion-builder' ),
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
				Fusion_Dynamic_JS::enqueue_script( 'fusion-popover' );
			}
		}
	}

	new FusionSC_Popover();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_popover() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'           => esc_attr__( 'Popover', 'fusion-builder' ),
		'shortcode'      => 'fusion_popover',
		'generator_only' => true,
		'icon'           => 'fusiona-uniF61C',
		'params'         => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Triggering Content', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
				'description' => esc_attr__( 'Content that will trigger the popover.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Popover Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Heading text of the popover.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Heading Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the popover heading. ', 'fusion-builder' ),
				'param_name'  => 'title_bg_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'popover_heading_bg_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Contents Inside Popover', 'fusion-builder' ),
				'description' => esc_attr__( 'Text to be displayed inside the popover.', 'fusion-builder' ),
				'param_name'  => 'content',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Content Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the popover content area. ', 'fusion-builder' ),
				'param_name'  => 'content_bg_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'popover_content_bg_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color of the of the popover box. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'popover_border_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Popover Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls all the text color inside the popover box. Leave blank for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'textcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'popover_text_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Popover Trigger Method', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose mouse action to trigger popover.' ),
				'param_name'  => 'trigger',
				'value'       => array(
					'hover' => esc_attr__( 'Hover', 'fusion-builder' ),
					'click' => esc_attr__( 'Click', 'fusion-builder' ),
				),
				'default'     => 'click',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Popover Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position of the popover. Choose default for theme option selection.' ),
				'param_name'  => 'placement',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-builder' ),
					'top'     => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom'  => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'    => esc_attr__( 'Left', 'fusion-builder' ),
					'right'   => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => 'default',
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
add_action( 'fusion_builder_before_init', 'fusion_element_popover' );
