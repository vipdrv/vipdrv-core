<?php

if ( fusion_is_element_enabled( 'fusion_tooltip' ) ) {

	if ( ! class_exists( 'FusionSC_Tooltip' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Tooltip extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @since 1.0
			 * @access protected
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
				add_filter( 'fusion_attr_tooltip-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_tooltip', array( $this, 'render' ) );

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

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'class'     => '',
						'id'        => '',
						'animation' => false,
						'delay'     => 0,
						'placement' => 'top',
						'title'     => 'none',
						'trigger'   => 'hover',
					), $args
				);

				extract( $defaults );

				$this->args = $defaults;

				$html = sprintf( '<span %s>%s</span>', FusionBuilder::attributes( 'tooltip-shortcode' ), do_shortcode( $content ) );

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
					'class' => 'fusion-tooltip tooltip-shortcode',
				);

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				$attr['data-animation'] = $this->args['animation'];
				$attr['data-delay']     = $this->args['delay'];
				$attr['data-placement'] = $this->args['placement'];
				$attr['data-title']     = $this->args['title'];
				$attr['data-toggle']    = 'tooltip';
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

				$elements = apply_filters( 'fusion_builder_element_classes', array( '.tooltip-shortcode' ), '.tooltip-shortcode' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

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
				Fusion_Dynamic_JS::enqueue_script( 'fusion-tooltip' );
			}
		}
	}

	new FusionSC_Tooltip();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_tooltip() {
	fusion_builder_map( array(
		'name'           => esc_attr__( 'Tooltip', 'fusion-builder' ),
		'shortcode'      => 'fusion_tooltip',
		'icon'           => 'fusiona-exclamation-sign',
		'generator_only' => true,
		'params'         => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the text that displays in the tooltip.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the text that will activate the tooltip hover.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Tooltip Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position.' ),
				'param_name'  => 'placement',
				'value'       => array(
					'top'    => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom' => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => 'top',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Tooltip Trigger', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose action to trigger the tooltip.', 'fusion-builder' ),
				'param_name'  => 'trigger',
				'value'       => array(
					'hover' => esc_attr__( 'Hover', 'fusion-builder' ),
					'click' => esc_attr__( 'Click', 'fusion-builder' ),
				),
				'default'     => 'hover',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_tooltip' );
