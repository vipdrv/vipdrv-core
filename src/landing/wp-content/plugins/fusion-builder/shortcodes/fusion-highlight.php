<?php

if ( fusion_is_element_enabled( 'fusion_highlight' ) ) {

	if ( ! class_exists( 'FusionSC_Highlight' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Highlight extends Fusion_Element {

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
				add_filter( 'fusion_attr_highlight-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_highlight', array( $this, 'render' ) );
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
						'class'   => '',
						'id'      => '',
						'color'   => $fusion_settings->get( 'primary_color' ),
						'rounded' => 'no',
					), $args
				);

				extract( $defaults );

				$this->args = $defaults;

				return '<span ' . FusionBuilder::attributes( 'highlight-shortcode' ) . '>' . do_shortcode( $content ) . '</span>';

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
					'class' => 'fusion-highlight',
				);

				$brightness_level = Fusion_Color::new_color( $this->args['color'] )->brightness;

				$attr['class'] .= ( $brightness_level > 140 ) ? ' light' : ' dark';

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( 'yes' == $this->args['rounded'] ) {
					$attr['class'] .= ' rounded';
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				if ( 'black' == $this->args['color'] ) {
					$attr['class'] .= ' highlight2';
				} else {
					$attr['class'] .= ' highlight1';
				}

				$attr['style'] = 'background-color:' . $this->args['color'] . ';';

				return $attr;

			}
		}
	}

	new FusionSC_Highlight();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_highlight() {
	fusion_builder_map( array(
		'name'           => esc_attr__( 'Highlight', 'fusion-builder' ),
		'shortcode'      => 'fusion_highlight',
		'icon'           => 'fusiona-H',
		'generator_only' => true,
		'params'         => array(
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Highlight Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Pick a highlight color.', 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => '',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Highlight With Round Edges', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have rounded edges.', 'fusion-builder' ),
				'param_name'  => 'rounded',
				'value'       => array(
					'no'  => __( 'No', 'fusion-builder' ),
					'yes' => __( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some text to highlight.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_highlight' );
