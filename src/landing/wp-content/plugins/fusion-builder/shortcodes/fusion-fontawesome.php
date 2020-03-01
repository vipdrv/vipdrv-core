<?php

if ( fusion_is_element_enabled( 'fusion_fontawesome' ) ) {

	if ( ! class_exists( 'FusionSC_FontAwesome' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FontAwesome extends Fusion_Element {

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
				add_filter( 'fusion_attr_fontawesome-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_fontawesome', array( $this, 'render' ) );

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
						'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
						'class'               => '',
						'id'                  => '',
						'alignment'           => '',
						'circle'              => 'yes',
						'circlecolor'         => $fusion_settings->get( 'icon_circle_color' ),
						'circlebordercolor'   => $fusion_settings->get( 'icon_border_color' ),
						'flip'                => '',
						'icon'                => '',
						'iconcolor'           => $fusion_settings->get( 'icon_color' ),
						'rotate'              => '',
						'size'                => 'medium',
						'spin'                => 'no',
						'animation_type'      => '',
						'animation_direction' => 'down',
						'animation_speed'     => '0.1',
						'animation_offset'    => $fusion_settings->get( 'animation_offset' ),
					), $args
				);

				extract( $defaults );

				// Dertmine line-height and margin from font size.
				$defaults['font_size']            = FusionBuilder::validate_shortcode_attr_value( self::convert_deprecated_sizes( $defaults['size'] ), '' );
				$defaults['circle_yes_font_size'] = $defaults['font_size'] * 0.88;
				$defaults['line_height']          = $defaults['font_size'] * 1.76;
				$defaults['icon_margin']          = $defaults['font_size'] * 0.5;
				$defaults['icon_margin_position'] = ( is_rtl() ) ? 'left' : 'right';

				$this->args = $defaults;

				$html = '<i ' . FusionBuilder::attributes( 'fontawesome-shortcode' ) . '>' . do_shortcode( $content ) . '</i>';

				if ( $alignment ) {
					$html = '<div class="align' . $alignment . '">' . $html . '</div>';
				}

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
					'class' => 'fa fontawesome-icon ' . FusionBuilder::font_awesome_name_handler( $this->args['icon'] ) . ' circle-' . $this->args['circle'],
				) );

				$attr['style'] = '';

				if ( 'yes' == $this->args['circle'] ) {

					if ( $this->args['circlebordercolor'] ) {
						$attr['style'] .= 'border-color:' . $this->args['circlebordercolor'] . ';';
					}

					if ( $this->args['circlecolor'] ) {
						$attr['style'] .= 'background-color:' . $this->args['circlecolor'] . ';';
					}

					$attr['style'] .= 'font-size:' . $this->args['circle_yes_font_size'] . 'px;';

					$attr['style'] .= 'line-height:' . $this->args['line_height'] . 'px;height:' . $this->args['line_height'] . 'px;width:' . $this->args['line_height'] . 'px;';

				} else {
					$attr['style'] .= 'font-size:' . $this->args['font_size'] . 'px;';
				}

				if ( 'center' == $this->args['alignment'] ) {
					$attr['style'] .= 'margin-left:0;margin-right:0;';
				} else {
					$attr['style'] .= 'margin-' . $this->args['icon_margin_position'] . ':' . $this->args['icon_margin'] . 'px;';
				}

				if ( $this->args['iconcolor'] ) {
					$attr['style'] .= 'color:' . $this->args['iconcolor'] . ';';
				}

				if ( $this->args['rotate'] ) {
					$attr['class'] .= ' fa-rotate-' . $this->args['rotate'];
				}

				if ( 'yes' == $this->args['spin'] ) {
					$attr['class'] .= ' fa-spin';
				}

				if ( $this->args['flip'] ) {
					$attr['class'] .= ' fa-flip-' . $this->args['flip'];
				}

				if ( $this->args['animation_type'] ) {
					$animations = FusionBuilder::animations( array(
						'type'      => $this->args['animation_type'],
						'direction' => $this->args['animation_direction'],
						'speed'     => $this->args['animation_speed'],
						'offset'    => $this->args['animation_offset'],
					) );

					$attr = array_merge( $attr, $animations );

					$attr['class'] .= ' ' . $attr['animation_class'];
					unset( $attr['animation_class'] );
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
			 * Converts deprecated image sizes to their new names.
			 *
			 * @access public
			 * @since 1.0
			 * @param  string $size The name of the old image-size.
			 * @return string       The name of the new image-size.
			 */
			public function convert_deprecated_sizes( $size ) {
				switch ( $size ) {
					case 'small':
						$size = '10px';
						break;
					case 'medium':
						$size = '18px';
						break;
					case 'large':
						$size = '40px';
						break;
					default:
						break;
				}

				return $size;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Icon settings.
			 */
			public function add_options() {

				return array(
					'icon_shortcode_section' => array(
						'label'       => esc_html__( 'Icon Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'icon_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'icon_circle_color' => array(
								'label'       => esc_html__( 'Icon Circle Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the circle background.', 'fusion-builder' ),
								'id'          => 'icon_circle_color',
								'default'     => '#333333',
								'type'        => 'color-alpha',
							),
							'icon_border_color' => array(
								'label'       => esc_html__( 'Icon Circle Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the circle background.', 'fusion-builder' ),
								'id'          => 'icon_border_color',
								'default'     => '#333333',
								'type'        => 'color-alpha',
							),
							'icon_color' => array(
								'label'       => esc_html__( 'Icon Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the icon.', 'fusion-builder' ),
								'id'          => 'icon_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
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

				Fusion_Dynamic_JS::enqueue_script( 'fusion-animations' );
			}
		}
	}

	new FusionSC_FontAwesome();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_font_awesome() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Font Awesome Icon', 'fusion-builder' ),
		'shortcode'  => 'fusion_fontawesome',
		'icon'       => 'fusiona-flag',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-font-awesome-preview.php',
		'preview_id' => 'fusion-builder-block-module-font-awesome-preview-template',
		'params'     => array(
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Size of Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Set the size of the icon. In pixels (px), ex: 13px.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Flip Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to flip the icon.', 'fusion-builder' ),
				'param_name'  => 'flip',
				'value'       => array(
					''           => esc_attr__( 'None', 'fusion-builder' ),
					'horizontal' => esc_attr__( 'Horizontal', 'fusion-builder' ),
					'vertical'   => esc_attr__( 'Vertical', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Rotate Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to rotate the icon.', 'fusion-builder' ),
				'param_name'  => 'rotate',
				'value'       => array(
					''    => esc_attr__( 'None', 'fusion-builder' ),
					'90'  => '90',
					'180' => '180',
					'270' => '270',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Spinning Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to let the icon spin.', 'fusion-builder' ),
				'param_name'  => 'spin',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon in Circle', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display the icon in a circle.', 'fusion-builder' ),
				'param_name'  => 'circle',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the icon. ', 'fusion-builder' ),
				'param_name'  => 'iconcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'icon_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Circle Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the circle. ', 'fusion-builder' ),
				'param_name'  => 'circlecolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'icon_circle_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'circle',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Circle Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the circle border. ', 'fusion-builder' ),
				'param_name'  => 'circlebordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'icon_border_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'circle',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( "Select the icon's alignment.", 'fusion-builder' ),
				'param_name'  => 'alignment',
				'value'       => array(
					''       => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
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
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'value'       => array(
					'down'   => esc_attr__( 'Top', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'up'     => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'static' => esc_attr__( 'Static', 'fusion-builder' ),
				),
				'default'     => 'down',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'value'       => array(
					'1'   => '1',
					'0.1' => '0.1',
					'0.2' => '0.2',
					'0.3' => '0.3',
					'0.4' => '0.4',
					'0.5' => '0.5',
					'0.6' => '0.6',
					'0.7' => '0.7',
					'0.8' => '0.8',
					'0.9' => '0.9',
				),
				'default'     => '0.1',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
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
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_font_awesome' );
