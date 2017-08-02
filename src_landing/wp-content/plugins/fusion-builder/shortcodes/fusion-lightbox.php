<?php

if ( fusion_is_element_enabled( 'fusion_lightbox' ) ) {

	if ( ! class_exists( 'FusionSC_FusionLightbox' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FusionLightbox extends Fusion_Element {

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
				add_shortcode( 'fusion_lightbox', array( $this, 'render' ) );
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
				return $content;

			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script( 'fusion-lightbox' );
			}
		}
	}

	new FusionSC_FusionLightbox();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_lightbox() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Lightbox', 'fusion-builder' ),
		'shortcode'         => 'fusion_lightbox',
		'icon'              => 'fusiona-uniF602',
		'on_save'           => 'lightboxShortcodeFilter',
		'admin_enqueue_js'  => FUSION_BUILDER_PLUGIN_URL . 'shortcodes/js/fusion-lightbox.js',
		'params'            => array(
			array(
				'type'             => 'radio_button_set',
				'heading'          => esc_attr__( 'Content Type', 'fusion-builder' ),
				'description'      => esc_attr__( 'Select what you want to display in the lightbox.', 'fusion-builder' ),
				'param_name'       => 'type',
				'defaults'         => '',
				'value'            => array(
					'' => esc_attr__( 'Image', 'fusion-builder' ),
					'video' => esc_attr__( 'Video', 'fusion-builder' ),
				),
			),
			array(
				'type'             => 'upload',
				'heading'          => esc_attr__( 'Full Image', 'fusion-builder' ),
				'description'      => esc_attr__( 'Upload an image that will show up in the lightbox.', 'fusion-builder' ),
				'param_name'       => 'full_image',
				'value'            => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => '',
						'operator' => '==',
					),
				),
			),
			array(
				'type'             => 'textfield',
				'heading'          => esc_attr__( 'YouTube or Vimeo Video url', 'fusion-builder' ),
				'description'      => esc_attr__( 'Enter the full video url that will show up in the lightbox.', 'fusion-builder' ),
				'param_name'       => 'video_url',
				'value'            => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'             => 'upload',
				'heading'          => esc_attr__( 'Thumbnail Image', 'fusion-builder' ),
				'description'      => esc_attr__( 'Clicking this image will show lightbox.', 'fusion-builder' ),
				'param_name'       => 'thumbnail_image',
				'value'            => '',
			),
			array(
				'type'             => 'textfield',
				'heading'          => esc_attr__( 'Alt Text', 'fusion-builder' ),
				'param_name'       => 'alt_text',
				'value'            => '',
				'description'      => esc_attr__( 'The alt attribute provides alternative information if an image cannot be viewed.', 'fusion-builder' ),
			),
			array(
				'type'             => 'textfield',
				'heading'          => esc_attr__( 'Lightbox Description', 'fusion-builder' ),
				'param_name'       => 'description',
				'value'            => '',
				'description'      => esc_attr__( 'This will show up in the lightbox as a description below the image.', 'fusion-builder' ),
			),
			array(
				'type'             => 'textfield',
				'heading'          => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'param_name'       => 'class',
				'value'            => '',
				'description'      => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
			array(
				'type'             => 'textfield',
				'heading'          => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'param_name'       => 'id',
				'value'            => '',
				'description'      => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_lightbox' );
