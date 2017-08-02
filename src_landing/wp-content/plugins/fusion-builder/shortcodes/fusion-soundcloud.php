<?php

if ( fusion_is_element_enabled( 'fusion_soundcloud' ) ) {

	if ( ! class_exists( 'FusionSC_Soundcloud' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Soundcloud extends Fusion_Element {

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
				add_filter( 'fusion_attr_soundcloud-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_soundcloud', array( $this, 'render' ) );
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
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => 'fusion-soundcloud',
						'id'             => '',
						'auto_play'      => 'no',
						'color'          => 'ff7700',
						'comments'       => 'yes',
						'height'         => '',
						'layout'         => 'classic',
						'show_related'   => 'no',
						'show_reposts'   => 'no',
						'show_user'      => 'yes',
						'url'            => '',
						'width'          => '100%',
					), $args
				);

				$defaults['width']  = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
				$defaults['height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['height'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				$autoplay = ( 'yes' === $auto_play ) ? 'true' : 'false';
				$comments = ( 'yes' === $comments ) ? 'true' : 'false';

				if ( 'visual' === $layout ) {
					$visual = 'true';

					if ( ! $height ) {
						$height = '450';
					}
				} else {
					$visual = 'false';

					if ( ! $height ) {
						$height = '166';
					}
				}

				$height = (int) $height;

				$show_related = ( 'yes' === $show_related ) ? 'false' : 'true';
				$show_reposts = ( 'yes' === $show_reposts ) ? 'true' : 'false';
				$show_user    = ( 'yes' === $show_user ) ? 'true' : 'false';

				if ( $color ) {
					$color = str_replace( '#', '', $color );
				}

				return '<div ' . FusionBuilder::attributes( 'soundcloud-shortcode' ) . '><iframe scrolling="no" frameborder="no" width="' . $width . '" height="' . $height . '" src="https://w.soundcloud.com/player/?url=' . $url . '&amp;auto_play=' . $autoplay . '&amp;hide_related=' . $show_related . '&amp;show_comments=' . $comments . '&amp;show_user=' . $show_user . '&amp;show_reposts=' . $show_reposts . '&amp;visual=' . $visual . '&amp;color=' . $color . '" title="soundcloud"></iframe></div>';
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = array();

				if ( $this->args['class'] ) {
					$attr['class'] = $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				return $attr;

			}
		}
	}

	new FusionSC_Soundcloud();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_soundcloud() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Soundcloud', 'fusion-builder' ),
		'shortcode'  => 'fusion_soundcloud',
		'icon'       => 'fusiona-soundcloud',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-soundcloud-preview.php',
		'preview_id' => 'fusion-builder-block-module-soundcloud-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'SoundCloud Url', 'fusion-builder' ),
				'description' => esc_attr__( 'The SoundCloud url, ex: http://api.soundcloud.com/tracks/110813479.', 'fusion-builder' ),
				'param_name'  => 'url',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the layout of the soundcloud embed.', 'fusion-builder' ),
				'param_name'  => 'layout',
				'value'       => array(
					'classic' => esc_attr__( 'Classic', 'fusion-builder' ),
					'visual'  => esc_attr__( 'Visual', 'fusion-builder' ),
				),
				'default'     => 'classic',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Comments', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display comments.', 'fusion-builder' ),
				'param_name'  => 'comments',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Related', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display related items.', 'fusion-builder' ),
				'param_name'  => 'show_related',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show User', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display the user who posted the item.', 'fusion-builder' ),
				'param_name'  => 'show_user',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Autoplay', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to autoplay the track.', 'fusion-builder' ),
				'param_name'  => 'auto_play',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the color of the element.', 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => '#ff7700',
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Dimensions', 'fusion-builder' ),
				'description'      => esc_attr__( 'In pixels (px) or percentage (%).', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'width'  => '100%',
					'height' => '150px',
				),
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
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_soundcloud' );
