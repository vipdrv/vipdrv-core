<?php

if ( fusion_is_element_enabled( 'fusion_table' ) ) {

	if ( ! class_exists( 'FusionSC_FusionTable' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FusionTable extends Fusion_Element {

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
				add_shortcode( 'fusion_table', array( $this, 'render' ) );

				add_filter( 'fusion_table_content', 'shortcode_unautop' );
				add_filter( 'fusion_table_content', 'do_shortcode' );
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
				return apply_filters(
					'fusion_table_content',
					fusion_builder_fix_shortcodes( $content )
				);
			}
		}
	}

	new FusionSC_FusionTable();

}

/**
 * Map shortcode to Fusion Builder.
 */
function fusion_element_table() {
	fusion_builder_map( array(
		'name'             => __( 'Table', 'fusion-builder' ),
		'shortcode'        => 'fusion_table',
		'icon'             => 'fusiona-table',
		'allow_generator'  => true,
		'admin_enqueue_js' => FUSION_BUILDER_PLUGIN_URL . 'shortcodes/js/fusion-table.js',
		'params'           => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the table style.', 'fusion-builder' ),
				'param_name'  => 'fusion_table_type',
				'value'       => array(
					'1' => esc_attr__( 'Style 1', 'fusion-builder' ),
					'2' => esc_attr__( 'Style 2', 'fusion-builder' ),
				),
				'default'          => '1',
				'remove_from_atts' => true,
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Number of Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Select how many columns to display.', 'fusion-builder' ),
				'param_name'  => 'fusion_table_columns',
				'value'       => array(
					''  => esc_attr__( 'Select Columns', 'fusion-builder' ),
					'1' => esc_attr__( '1 Column', 'fusion-builder' ),
					'2' => esc_attr__( '2 Column', 'fusion-builder' ),
					'3' => esc_attr__( '3 Column', 'fusion-builder' ),
					'4' => esc_attr__( '4 Column', 'fusion-builder' ),
					'5' => esc_attr__( '5 Column', 'fusion-builder' ),
				),
				'default'          => '',
				'remove_from_atts' => true,
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Table', 'fusion-builder' ),
				'description' => esc_attr__( 'Table content will appear here.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_table' );
