<?php

if ( fusion_is_element_enabled( 'fusion_dropcap' ) ) {

	if ( ! class_exists( 'FusionSC_Dropcap' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Dropcap extends Fusion_Element {

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
				add_filter( 'fusion_attr_dropcap-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_dropcap', array( $this, 'render' ) );

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
						'class'        => '',
						'id'           => '',
						'boxed'        => '',
						'boxed_radius' => '',
						'color'        => strtolower( $fusion_settings->get( 'dropcap_color' ) ),
					), $args
				);

				extract( $defaults );

				$this->args = $defaults;

				return '<span ' . FusionBuilder::attributes( 'dropcap-shortcode' ) . '>' . do_shortcode( $content ) . '</span>';

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
					'class' => 'fusion-dropcap dropcap',
					'style' => '',
				);

				if ( 'yes' == $this->args['boxed'] ) {
					$attr['class'] .= ' dropcap-boxed';

					if ( $this->args['boxed_radius'] || '0' === $this->args['boxed_radius'] ) {
						$this->args['boxed_radius'] = ( 'round' == $this->args['boxed_radius'] ) ? '50%' : $this->args['boxed_radius'];
						$attr['style'] = 'border-radius:' . $this->args['boxed_radius'] . ';';
					}

					$attr['style'] .= 'background-color:' . $this->args['color'] . ';';
				} else {
					$attr['style'] .= 'color:' . $this->args['color'] . ';';
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
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-dropcap' ), '.fusion-dropcap' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Dropcap settings.
			 */
			public function add_options() {

				return array(
					'dropcap_shortcode_section' => array(
						'label'       => esc_html__( 'Dropcap Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'dropcap_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'dropcap_color' => array(
								'label'       => esc_html__( 'Dropcap Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the dropcap text, or the dropcap box if a box is used.', 'fusion-builder' ),
								'id'          => 'dropcap_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
						),
					),
				);
			}
		}
	}

	new FusionSC_Dropcap();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_dropcap() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'              => esc_attr__( 'Dropcap', 'fusion-builder' ),
		'shortcode'         => 'fusion_dropcap',
		'generator_only'    => true,
		'icon'              => 'fusiona-font',
		'params'            => array(
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Dropcap Letter', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the letter to be used as dropcap.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'A',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the dropcap letter. Leave blank for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'dropcap_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Boxed Dropcap', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed dropcap.' ),
				'param_name'  => 'boxed',
				'value'       => array(
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Box Radius', 'fusion-builder' ),
				'param_name'  => 'boxed_radius',
				'value'       => '',
				'description' => esc_attr__( 'Choose the radius of the boxed dropcap. In pixels (px), ex: 1px, or "round".', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'boxed',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
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
add_action( 'fusion_builder_before_init', 'fusion_element_dropcap' );
