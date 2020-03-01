<?php

if ( fusion_is_element_enabled( 'fusion_one_page_text_link' ) ) {

	if ( ! class_exists( 'FusionSC_OnePageTextLink' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_OnePageTextLink extends Fusion_Element {

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
				add_filter( 'fusion_attr_one-page-text-link-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_one_page_text_link', array( $this, 'render' ) );

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
						'class' => '',
						'id'    => '',
						'link'  => '',
					), $args
				);

				extract( $defaults );

				$this->args = $defaults;

				return '<a ' . FusionBuilder::attributes( 'one-page-text-link-shortcode' ) . '>' . do_shortcode( $content ) . '</a>';

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
					'class' => 'fusion-one-page-text-link',
				);

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				$attr['href'] = $this->args['link'];

				return $attr;

			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
			}
		}
	}

	new FusionSC_OnePageTextLink();

}

/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_one_page_text_link() {
	fusion_builder_map( array(
		'name'           => esc_attr__( 'One Page Text Link', 'fusion-builder' ),
		'shortcode'      => 'fusion_one_page_text_link',
		'generator_only' => true,
		'icon'           => 'fusiona-external-link',
		'params'         => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name Of Anchor', 'fusion-builder' ),
				'description' => esc_attr__( 'Unique identifier of the anchor to scroll to on click.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Text or HTML code', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert text or HTML code here (e.g: HTML for image). This content will be used to trigger the scrolling to the anchor.', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_one_page_text_link' );
