<?php

if ( fusion_is_element_enabled( 'fusion_vimeo' ) ) {

	if ( ! class_exists( 'FusionSC_Vimeo' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Vimeo extends Fusion_Element {

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
				add_filter( 'fusion_attr_vimeo-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_vimeo-shortcode-video-sc', array( $this, 'video_sc_attr' ) );

				add_shortcode( 'fusion_vimeo', array( $this, 'render' ) );

			}

			/**
			 * Render the shortcode.
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
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'api_params'     => '',
						'autoplay'       => 'no',
						'alignment'      => '',
						'center'         => 'no',
						'height'         => 360,
						'id'             => '',
						'width'          => 600,
					), $args
				);

				$defaults['height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['height'], '' );
				$defaults['width']  = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], '' );

				extract( $defaults );

				$this->args = $defaults;

				// Make sure only the video ID is passed to the iFrame.
				$pattern = '/(?:https?:\/\/)?(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/';
				preg_match( $pattern, $id, $matches );
				if ( isset( $matches[3] ) ) {
					$id = $matches[3];
				}

				$html  = '<div ' . FusionBuilder::attributes( 'vimeo-shortcode' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'vimeo-shortcode-video-sc' ) . '>';
				$html .= '<iframe src="https://player.vimeo.com/video/' . $id . '?autoplay=0' . $api_params . '" width="' . $width . '" height="' . $height . '" allowfullscreen title="vimeo' . $id . '"></iframe>';
				$html .= '</div></div>';

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
					'class' => 'fusion-video fusion-vimeo',
				) );

				if ( 'yes' == $this->args['center'] ) {
					$attr['class'] .= ' center-video';
				} else {
					$attr['style'] = 'max-width:' . $this->args['width'] . 'px;max-height:' . $this->args['height'] . 'px;';
				}

				if ( '' !== $this->args['alignment'] ) {
					$attr['class'] .= ' fusion-align' . $this->args['alignment'];
					$attr['style'] .= ' width:100%';
				}

				if ( 'true' == $this->args['autoplay'] || 'yes' == $this->args['autoplay'] ) {
					$attr['data-autoplay'] = 1;
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				return $attr;

			}

			/**
			 * Builds the video shortcode attributes.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function video_sc_attr() {

				$attr = array(
					'class' => 'video-shortcode',
				);

				if ( 'yes' == $this->args['center'] ) {
					$attr['style'] = 'max-width:' . $this->args['width'] . 'px;max-height:' . $this->args['height'] . 'px;';
				}

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

				Fusion_Dynamic_JS::enqueue_script( 'fusion-video' );
			}
		}
	}

	new FusionSC_Vimeo();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_vimeo() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Vimeo', 'fusion-builder' ),
		'shortcode'  => 'fusion_vimeo',
		'icon'       => 'fusiona-vimeo2',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-vimeo-preview.php',
		'preview_id' => 'fusion-builder-block-module-vimeo-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Video ID', 'fusion-builder' ),
				'description' => esc_attr__( 'For example the Video ID for https://vimeo.com/75230326 is 75230326.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( "Select the video's alignment.", 'fusion-builder' ),
				'param_name'  => 'alignment',
				'default'     => '',
				'value'       => array(
					''       => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Dimensions', 'fusion-builder' ),
				'description'      => esc_attr__( 'In pixels but only enter a number, ex: 600.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'width'  => '600',
					'height' => '350',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Autoplay Video', 'fusion-builder' ),
				'description' => esc_attr__( 'Set to yes to make video autoplaying.', 'fusion-builder' ),
				'param_name'  => 'autoplay',
				'value'       => array(
					'false' => esc_attr__( 'No', 'fusion-builder' ),
					'true'  => esc_attr__( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'false',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Additional API Parameter', 'fusion-builder' ),
				'description' => esc_attr__( 'Use additional API parameter, for example &rel=0 to disable related videos.', 'fusion-builder' ),
				'param_name'  => 'api_params',
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
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_vimeo' );
