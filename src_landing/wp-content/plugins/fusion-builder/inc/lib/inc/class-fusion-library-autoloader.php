<?php
/**
 * Fusion Library Autoloader.
 * Manages loading other class files.
 *
 * @package Fusion-Library
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The autoloader class.
 */
class Fusion_Library_Autoloader {

	/**
	 * If there are multiple locations for Fusion-Library,
	 * they are all added here.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $locations = array();

	/**
	 * An array of the class paths we've located.
	 *
	 * @access private
	 * @var array
	 */
	private $paths = array();

	/**
	 * Since Fusion-Library can be included multiple times
	 * we need this class to be a singleton.
	 * This var holds the one true instance of this object.
	 *
	 * @static
	 * @access private
	 * @var object
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		spl_autoload_register( array( $this, 'include_file' ) );
	}

	/**
	 * Gets the one true instance of this class.
	 *
	 * @access public
	 * @return object.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Include the file if found.
	 *
	 * @access private
	 * @param string $class_name The class-name we're looking for.
	 */
	private function include_file( $class_name ) {
		if ( ! isset( $this->paths[ $class_name ] ) ) {
			$this->paths[ $class_name ] = $this->locate_file( $class_name );
		}
		// Only process if file was found.
		// If it doesn't exist, then the locate_file() method returned false.
		if ( $this->paths[ $class_name ] ) {
			include_once $this->paths[ $class_name ];
		}
	}

	/**
	 * Locate the class file
	 *
	 * @access private
	 * @param string $class_name The class-name we're looking for.
	 * @return string|false      If false, file was not located.
	 */
	private function locate_file( $class_name ) {
		// Return false if the class does not start with "Fusion".
		if ( 0 !== stripos( $class_name, 'Fusion' ) ) {
			return false;
		}

		// Extrapolate the filename from the class-name.
		$filename = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

		// Go through all instances of the files in case of multiple installations
		// and add their version as key in the array.
		$paths = array();
		foreach ( self::$locations as $version => $location ) {
			$file = wp_normalize_path( $location . '/inc/' . $filename );
			if ( ! file_exists( $file ) ) {
				continue;
			}
			$paths[ $version ] = $file;
		}

		// Reorder versions to make sure newest is first.
		krsort( $paths );

		// This is a pseudo-loop.
		// We're not actually looping though all items here,
		// we're simply returning the 1st element of the array.
		// It only acts as a loop if the 1st element is empty.
		foreach ( $paths as $path ) {
			if ( $path ) {
				// Return 1st (newest version) path.
				return $path;
			}
		}
		return false;
	}

	/**
	 * Add a location to the self::$locations array.
	 *
	 * @static
	 * @access public
	 * @param string $path    The path to add.
	 * @param string $version The fusion-library version.
	 */
	public static function add_location( $path, $version ) {
		if ( ! isset( self::$locations[ $version ] ) ) {
			self::$locations[ $version ] = $path;
		}
	}
}
