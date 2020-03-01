<?php

if ( fusion_is_element_enabled( 'fusion_widget_area' ) ) {

	if ( ! class_exists( 'FusionSC_WidgetArea' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_WidgetArea extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * Counter for widgets.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $widget_counter = 1;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_widget-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_widget_area', array( $this, 'render' ) );

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
				if ( ! $fusion_settings ) {
					$fusion_settings = Fusion_Settings::get_instance();
				}

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
						'class'            => '',
						'id'               => '',
						'background_color' => '',
						'name'             => '',
						'padding'          => '',
						'title_color'    => $fusion_settings->get( 'widget_area_title_color' ),
						'title_size'     => $fusion_settings->get( 'widget_area_title_size' ),
					), $args
				);

				$defaults['padding'] = FusionBuilder::validate_shortcode_attr_value( $defaults['padding'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				$html = '<div ' . FusionBuilder::attributes( 'widget-shortcode' ) . '>';
				$html .= self::get_styles();

				ob_start();
				// @codingStandardsIgnoreStart
				if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( $name ) ) {
					// All is good, dynamic_sidebar() already called the rendering.
				}
				// @codingStandardsIgnoreEnd
				$html .= ob_get_clean();

				$html .= '<div ' . FusionBuilder::attributes( 'fusion-additional-widget-content' ) . '>';
				$html .= do_shortcode( $content );
				$html .= '</div>';
				$html .= '</div>';

				$this->widget_counter++;

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
					'class' => 'fusion-widget-area fusion-widget-area-' . $this->widget_counter . ' fusion-content-widget-area',
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
			 * Gets the CSS styles.
			 *
			 * @access public
			 * @since 1.0
			 * @return string
			 */
			public function get_styles() {
				global $fusion_library;
				$styles = '';

				if ( $this->args['background_color'] ) {
					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' {background-color:' . $this->args['background_color'] . ';}';
				}

				if ( $this->args['padding'] ) {
					if ( strpos( $this->args['padding'], '%' ) === false && strpos( $this->args['padding'], 'px' ) === false ) {
						$this->args['padding'] = $this->args['padding'] . 'px';
					}

					$_padding = $fusion_library->sanitize->get_value_with_unit( $this->args['padding'] );
					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' {padding:' . $_padding . ';}';
				}

				if ( $this->args['title_color'] ) {

					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' .widget h4 {color:' . $this->args['title_color'] . ';}';
					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' .widget .heading h4 {color:' . $this->args['title_color'] . ';}';
				}

				if ( $this->args['title_size'] ) {

					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' .widget h4 {font-size:' . $this->args['title_size'] . ';}';
					$styles .= '.fusion-widget-area-' . $this->widget_counter . ' .widget .heading h4 {font-size:' . $this->args['title_size'] . ';}';
				}

				if ( $styles ) {
					$styles = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
				}

				return $styles;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Widget Area settings.
			 */
			public function add_options() {

				return array(
					'widget_area_shortcode_section' => array(
						'label'       => esc_html__( 'Widget Area Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'widget_area_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'widget_area_title_size' => array(
								'label'       => esc_html__( 'Widget Title Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the size of widget titles. In pixels.', 'fusion-builder' ),
								'id'          => 'widget_area_title_size',
								'default'     => apply_filters( 'fusion_builder_widget_area_title_size', '' ),
								'type'        => 'dimension',
							),
							'widget_area_title_color' => array(
								'label'       => esc_html__( 'Widget Title Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of widget titles.', 'fusion-builder' ),
								'id'          => 'widget_area_title_color',
								'default'     => apply_filters( 'fusion_builder_widget_area_title_color', '' ),
								'type'        => 'color-alpha',
							),
						),
					),
				);
			}
		}
	}

	new FusionSC_WidgetArea();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_widget_area() {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	fusion_builder_map( array(
		'name'      => esc_attr__( 'Widget Area', 'fusion-builder' ),
		'shortcode' => 'fusion_widget_area',
		'icon'      => 'fusiona-sidebar',
		'params'    => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Widget Area Name', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the name of the widget area to display.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => FusionBuilder::fusion_get_sidebars(),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Widget Title Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the size of widget titles. In pixels ex: 18px.', 'fusion-builder' ),
				'param_name'  => 'title_size',
				'value'       => '',
				'default'     => $fusion_settings->get( 'widget_area_title_size' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Widget Title Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of widget titles.', 'fusion-builder' ),
				'param_name'  => 'title_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'widget_area_title_color' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Backgound Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the widget area.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
			),
			array(
				'type'        => 'dimension',
				'heading'     => esc_attr__( 'Padding', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-builder' ),
				'param_name'  => 'padding',
				'value'       => '',
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

// Later hook to ensure the sidebars are set.
add_action( 'wp_loaded', 'fusion_element_widget_area' );
