<?php
/**
 * Handles Redux Addons.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if ( class_exists( 'Fusion_Redux_Addons' ) ) {
	return;
}

/**
 * Handle loading Redux Addons for Fusion.
 *
 * @since 1.0.0
 */
class Fusion_Redux_Addons {

	/**
	 * An array of our custom field types.
	 *
	 * @access public
	 * @var array
	 */
	public $field_types;

	/**
	 * An array of our custom extension.
	 *
	 * @access public
	 * @var array
	 */
	public $extensions;

	/**
	 * The path of the current file.
	 *
	 * @access public
	 * @var string
	 */
	public $path;

	/**
	 * The option-name.
	 *
	 * @access public
	 * @var string
	 */
	public $option_name = 'fusion';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $option_name The option-name.
	 */
	public function __construct( $option_name ) {

		$this->option_name = $option_name;

		// An array of all the custom fields we have.
		$this->field_types = array(
			'typography',
			'color_alpha',
			'spacing',
			'dimensions',
			'ace_editor',
		);
		// An array of all our extensions.
		$this->extensions = array(
			'search',
			'repeater',
			'accordion',
			'vendorsupport',
		);

		$this->path = dirname( __FILE__ );

		foreach ( $this->field_types as $field_type ) {
			add_action( 'fusionredux/' . $this->option_name . '/field/class/' . $field_type, array( $this, 'register_' . $field_type ) );
		}

		foreach ( $this->extensions as $extension ) {
			if ( class_exists( 'FusionRedux' ) ) {
				FusionRedux::setExtensions( $this->option_name, $this->path . '/extensions/' . $extension . '/extension_' . $extension . '.php' );
			}
		}
	}

	/**
	 * Register the custom typography field
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function register_typography() {
		return $this->path . '/custom-fields/typography/field_typography.php';
	}


	/**
	 * Register the custom ace_editor field
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function register_ace_editor() {
		return $this->path . '/custom-fields/ace_editor/field_ace_editor.php';
	}

	/**
	 * Register the custom color_alpha field
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function register_color_alpha() {
		return $this->path . '/custom-fields/color_alpha/field_color_alpha.php';
	}

	/**
	 * Register the custom spacing field
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function register_spacing() {
		return $this->path . '/custom-fields/spacing/field_spacing.php';
	}

	/**
	 * Register the custom dimensions field
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function register_dimensions() {
		return $this->path . '/custom-fields/dimensions/field_dimensions.php';
	}
}
