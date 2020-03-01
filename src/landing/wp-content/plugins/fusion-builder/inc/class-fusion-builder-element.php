<?php
/**
 * Builder Elements Class.
 *
 * @package Fusion-Library
 * @since 1.1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Builder Elements Class.
 *
 * @since 1.1.0
 */
abstract class Fusion_Element {

	/**
	 * FB options class object.
	 *
	 * @static
	 * @access protected
	 * @since 1.1.0
	 * @var object Fusion_Builder_Options
	 */
	protected static $fb_options;

	/**
	 * First add on or not.
	 *
	 * @static
	 * @access protected
	 * @since 1.1.0
	 * @var boolean
	 */
	protected static $first_addon = true;

	/**
	 * Dynamic CSS class object.
	 *
	 * @static
	 * @access protected
	 * @since 1.1.0
	 * @var bool
	 */
	protected static $dynamic_css_helpers;

	/**
	 * Options array.
	 * THis holds ALL OPTIONS from ALL ELEMENTS.
	 *
	 * @static
	 * @access protected
	 * @since 1.1.0
	 * @var array
	 */
	protected static $global_options = array();

	/**
	 * The class constructor
	 *
	 * @access public
	 */
	public function __construct() {
		global $dynamic_css_helpers;

		// Options class to add to.
		if ( ! self::$fb_options ) {
			self::$fb_options = Fusion_Builder_Options::get_instance();
		}

		// Check if class is in FB or FC.
		$is_core = ( false !== strpos( $this->get_dir(), wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR ) ) || ( ( defined( 'FUSION_CORE_PATH' ) && false !== strpos( $this->get_dir(), wp_normalize_path( FUSION_CORE_PATH ) ) ) ) );
		if ( $is_core ) {
			$element_options = array(
				'shortcode_styling' => array(
					'fields' => $this->add_options(),
				),
			);
		} else {
			$fields = $this->add_options();
			foreach ( $fields as $field_id => $field ) {
				$fields[ $field_id ]['highlight'] = esc_attr__( '3rd Party Element', 'fusion-builder' );
			}
			if ( self::$first_addon ) {
				self::$first_addon = false;
				$element_options = array(
					'fusion_builder_addons' => array(
						'label'    => esc_html__( 'Add-on Elements', 'fusion-builder' ),
						'id'       => 'fusion_builder_addons',
						'is_panel' => true,
						'priority' => 14,
						'icon'     => 'el-icon-cog',
						'fields'   => $fields,
					),
				);
			} else {
				$element_options = array(
					'fusion_builder_addons' => array(
						'fields' => $fields,
					),
				);
			}
		}
		self::$global_options = array_merge_recursive( self::$global_options, $element_options );
		self::$fb_options->add_options( $element_options );

		// Dynamic CSS class to add to.
		$dynamic_css_helpers->add_css( $this->add_styling() );

		// Dynamic JS script.
		$this->add_scripts();
	}

	/**
	 * Adds settings to element options panel.
	 *
	 * @access protected
	 * @since 1.1
	 */
	protected function add_options() {
		return array();
	}

	/**
	 * Checks location of child class.
	 *
	 * @access protected
	 * @since 1.1
	 */
	protected function get_dir() {
		$rc = new ReflectionClass( get_class( $this ) );
		return wp_normalize_path( dirname( $rc->getFileName() ) );
	}

	/**
	 * Adds scripts to the dynamic JS.
	 *
	 * @access protected
	 * @since 1.1.0
	 */
	protected function add_scripts() {
	}

	/**
	 * Adds dynamic stying to dynamic CSS.
	 *
	 * @access protected
	 * @since 1.1
	 */
	protected function add_styling() {
		return array();
	}

	/**
	 * Returns the $global_options property.
	 *
	 * @static
	 * @access public
	 * @since 1.1.0
	 * @return array
	 */
	public static function get_all_options() {
		return self::$global_options;
	}
}
