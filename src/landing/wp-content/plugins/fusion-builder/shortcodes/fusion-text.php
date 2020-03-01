<?php

if ( fusion_is_element_enabled( 'fusion_text' ) ) {

	if ( ! class_exists( 'FusionSC_FusionText' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FusionText extends Fusion_Element {

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
				add_shortcode( 'fusion_text', array( $this, 'render' ) );

				add_filter( 'fusion_text_content', 'shortcode_unautop' );
				add_filter( 'fusion_text_content', 'do_shortcode' );
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
				return apply_filters( 'fusion_text_content', wpautop( $content, false ) );
			}
		}
	}

	new FusionSC_FusionText();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_text() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Text Block', 'fusion-builder' ),
		'shortcode'       => 'fusion_text',
		'icon'            => 'fusiona-font',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-text-preview.php',
		'preview_id'      => 'fusion-builder-block-module-text-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this textblock.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Click edit button to change this text.', 'fusion-builder' ),
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_text' );
