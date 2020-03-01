<?php

if ( fusion_is_element_enabled( 'fusion_menu_anchor' ) ) {

	if ( ! class_exists( 'FusionSC_MenuAnchor' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_MenuAnchor extends Fusion_Element {

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
				add_filter( 'fusion_attr_menu-anchor-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_menu_anchor', array( $this, 'render' ) );

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

				$defaults = shortcode_atts(
					array(
						'class' => '',
						'name'  => '',
					), $args
				);

				extract( $defaults );

				$this->args = $defaults ;

				return '<div ' . FusionBuilder::attributes( 'menu-anchor-shortcode' ) . '></div>';

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
					'class' => 'fusion-menu-anchor',
					'id'    => $this->args['name'],
				);

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
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
			}
		}
	}

	new FusionSC_MenuAnchor();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_menu_anchor() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Menu Anchor', 'fusion-builder' ),
		'shortcode'         => 'fusion_menu_anchor',
		'icon'              => 'fusiona-anchor',
		'preview'           => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-menu-anchor-preview.php',
		'preview_id'        => 'fusion-builder-block-module-menu-anchor-preview-template',
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => '',
				'description' => esc_attr__( 'This name will be the id you will have to use in your one page menu.', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_menu_anchor' );
