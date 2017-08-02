<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Gets the options from separate files and unites them.
 */
class Fusion_Builder_Options {

	/**
	 * An array of section names.
	 * We'll be using those to load all other files containing the options.
	 *
	 * @access public
	 * @var array
	 */
	public $section_names = array();

	/**
	 * An array of our sections.
	 *
	 * @access public
	 * @var array
	 */
	public $sections      = array();

	/**
	 * An array of our fields.
	 *
	 * @access private
	 * @var array
	 */
	private static $fields;

	/**
	 * The class instance.
	 *
	 * @static
	 * @access private
	 * @var null|object
	 */
	private static $instance = null;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	private function __construct() {
		// TODO: check for FB
		// Avada::$is_updating = ( $_GET && isset( $_GET['avada_update'] ) && '1' == $_GET['avada_update'] ) ? true : false; // Coding standard Fix.
		/**
		 * The array of sections by ID.
		 * These are used in the filenames AND the function-names.
		 */
		$this->section_names = apply_filters( 'fusion_builder_option_section', array(
			'globals'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/globals.php',
			'elements'   => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/elements.php',
			'responsive' => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/responsive.php',
			'lightbox'   => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/lightbox.php',
			'slideshows' => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/slideshows.php',
			'custom_css' => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/custom_css.php',
			'advanced'   => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/dynamic_css_js.php',
			'rollover'   => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/rollover.php',
			'pagination' => FUSION_BUILDER_PLUGIN_DIR . 'inc/options/pagination.php',
		) );

		// Include the section files.
		$this->include_files();

		// Set the $sections.
		$this->set_sections();

		// Set the $fields.
		$this->set_fields();

		add_filter( 'fusion_settings_all_fields', array( __CLASS__, 'get_option_fields' ) );

	}

	/**
	 * Returns a single instance of the object (singleton).
	 *
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Fusion_Builder_Options();
		}
		return self::$instance;
	}

	/**
	 * Include required files.
	 *
	 * @access public
	 */
	public function include_files() {

		foreach ( $this->section_names as $section => $url ) {
			include_once wp_normalize_path( $url );
		}

	}

	/**
	 * Set the sections.
	 *
	 * @access public
	 */
	public function set_sections() {

		$sections = array();
		foreach ( $this->section_names as $section => $url ) {
			$sections = call_user_func( 'fusion_builder_options_section_' . $section, $sections );
		}

		$this->sections = apply_filters( 'fusion_builder_options_sections', $sections );

	}

	/**
	 * Set the sections.
	 *
	 * @access public
	 * @param array $section The section array.
	 */
	public function add_options( $section ) {
		if ( ! empty( $section ) ) {
			$this->sections = array_replace_recursive( $this->sections, $section );
			$this->set_fields();
			do_action( 'fusion_options_added' );
		}
	}

	/**
	 * Get a flat array of our fields.
	 * This will contain simply the field IDs and nothing more than that.
	 * We'll be using this to check if a setting belongs to Fusion Builder or not.
	 *
	 * @access public
	 * @return array
	 */
	public function fields_array() {

		// Get the options object.
		$fusion_builder_new_options = Avada::$options;
		$fields = array();

		// Start parsing sections.
		foreach ( $fusion_builder_new_options->sections as $section ) {

			// Make sure we have defined fields for this section.
			// No need to proceed otherwise.
			if ( ! isset( $section['fields'] ) ) {
				continue;
			}

			// Start parsing the fields inside the section.
			foreach ( $section['fields'] as $field ) {

				// Make sure a field-type has been defined.
				if ( ! isset( $field['type'] ) ) {
					continue;
				}

				// For normal fields, we'll just add the field ID to our array.
				if ( ! in_array( $field['type'], array( 'sub-section', 'accordion' ) ) ) {
					if ( isset( $field['id'] ) ) {
						$fields[] = $field['id'];
					}
				} else {

					// For sub-sections & accordions we'll have to parse the sub-fields and add them to our array.
					if ( ! isset( $field['fields'] ) ) {
						continue;
					}
					foreach ( $field['fields'] as $sub_field ) {
						if ( isset( $sub_field['id'] ) ) {
							$fields[] = $sub_field['id'];
						}
					}
				}
			}
		}
		return $fields;
	}

	/**
	 * Sets the fields.
	 *
	 * @access public
	 */
	public function set_fields() {

		// Start parsing the sections.
		foreach ( $this->sections as $section ) {
			if ( ! isset( $section['fields'] ) ) {
				continue;
			}

			// Start parsing the fields.
			foreach ( $section['fields'] as $field ) {
				if ( ! isset( $field['id'] ) ) {
					continue;
				}

				// This is a sub-section or an accordion.
				if ( isset( $field['type'] ) && in_array( $field['type'], array( 'sub-section', 'accordion' ) ) ) {

					// Start parsing the fields inside the sub-section/accordion.
					foreach ( $field['fields'] as $sub_field ) {
						if ( ! isset( $sub_field['id'] ) ) {
							continue;
						}
						self::$fields[ $sub_field['id'] ] = $sub_field;
					}
				} else {

					// This is not a section, continue processing.
					self::$fields[ $field['id'] ] = $field;
				}
			}
		}
	}

	/**
	 * Returns the static $fields property.
	 *
	 * @static
	 * @access public
	 * @param array $fields The existing fields.
	 * @return array
	 */
	public static function get_option_fields( $fields = array() ) {

		if ( ! is_array( self::$fields ) || ! self::$fields || empty( self::$fields ) ) {
			$instance = self::get_instance();
			$instance->set_fields();
		}

		return array_replace_recursive( $fields, self::$fields );

	}
}
