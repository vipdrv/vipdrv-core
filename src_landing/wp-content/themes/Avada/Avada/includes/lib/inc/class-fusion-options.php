<?php
/**
 * Options handler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Gets the options from separate files and unites them.
 */
class Fusion_Options {

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
	public $sections = array();

	/**
	 * An array of our fields.
	 *
	 * @access protected
	 * @var array
	 */
	protected static $fields;

	/**
	 * The functions prefix.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var string
	 */
	protected $function_prefix = '';

	/**
	 * The filter.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var string
	 */
	protected $filter_name = '';

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		// Set the section-names.
		$this->section_names = $this->get_section_names();

		// Include the section files.
		$this->include_files();

		// Set the $sections.
		$this->set_sections();

		// Set the $fields.
		$this->set_fields();

	}

	/**
	 * Return an array of section names.
	 * To modify this array simply extend this class
	 * and use the get_section_names() method there.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_section_names() {
		return array();
	}

	/**
	 * Return the path of the folder that contains the options files.
	 * Override in a child class.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_folder_path() {
		return '';
	}

	/**
	 * Include required files.
	 *
	 * @access public
	 */
	public function include_files() {

		$folder_path = $this->get_folder_path();
		foreach ( $this->section_names as $section ) {
			include_once wp_normalize_path( $folder_path . '/' . $section . '.php' );
		}

	}

	/**
	 * Set the sections.
	 *
	 * @access public
	 */
	public function set_sections() {

		$sections = array();
		foreach ( $this->section_names as $section ) {
			$sections = call_user_func( $this->function_prefix . $section, $sections );
		}

		$this->sections = apply_filters( $this->filter_name, $sections );

	}

	/**
	 * Get a flat array of our fields.
	 * This will contain simply the field IDs and nothing more than that.
	 * We'll be using this to check if a setting belongs to Avada or not.
	 *
	 * @access public
	 * @return array
	 */
	public function fields_array() {

		// Get the options object.
		$options = $this->get_options();
		$fields  = array();

		// Start parsing sections.
		foreach ( $options->sections as $section ) {

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
				if ( ! in_array( $field['type'], array( 'sub-section', 'accordion' ), true ) ) {
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
				if ( isset( $field['type'] ) && in_array( $field['type'], array( 'sub-section', 'accordion' ), true ) ) {

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
	 * Get the options from the db.
	 * Override in a child-class.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_options() {
		return array();
	}

	/**
	 * Returns the static $fields property.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function get_option_fields() {
		return self::$fields;
	}
}
