<?php
/**
 * Icon picker methods.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Icons handler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */
class Fusion_Icon {
	/**
	 * Associative Array of Icon Data.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $data = array();

	/**
	 * Iterator.
	 *
	 * @access private
	 * @since 1.0
	 * @var object Iterator
	 */
	private $iterator;

	/**
	 * Constructor.
	 *
	 * @param object $iterator The iterator class.
	 * @param string $class    Icon css class.
	 * @param string $unicode  Unicode character reference.
	 */
	public function __construct( $iterator, $class, $unicode ) {

		$this->iterator = $iterator;

		// Set Basic Data.
		$this->data['class'] = $class;
		$this->data['unicode'] = $unicode;
	}

	/**
	 * Simple getter.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $key The key we'll be looking for in the array.
	 */
	public function __get( $key ) {

		if ( strtolower( $key ) === 'name' ) {
			return $this->get_name( $this->__get( 'class' ) );
		}

		if ( is_array( $this->data ) && isset( $this->data[ $key ] ) ) {
			return $this->data[ $key ];
		}
	}

	/**
	 * Gets the icon name.
	 *
	 * @access private
	 * @since 1.0
	 * @param string $class The icon class.
	 * @return string
	 */
	private function get_name( $class ) {

		// Remove Prefix.
		$name = substr( $class, strlen( $this->iterator->getPrefix() ) + 1 );

		// Convert Hyphens to Spaces.
		$name = str_replace( '-', ' ', $name );

		// Capitalize Words.
		$name = ucwords( $name );

		// Show Directional Variants in Parenthesis.
		$directions = array(
			'/up$/i',
			'/down$/i',
			'
			/left$/i', '/right$/i',
		);
		$directions_format = array( '(Up)', '(Down)', '(Left)', '(Right)' );
		$name = preg_replace( $directions, $directions_format, $name );

		// Use Word "Outlined" in Place of "O".
		$outlined_variants = array( '/\so$/i', '/\so\s/i' );
		$name = preg_replace( $outlined_variants, ' Outlined ', $name );

		// Remove Trailing Characters.
		$name = trim( $name );

		return $name;
	}
}

/**
 * FontAwesome Iterator.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */
class Fusion_FAIterator extends ArrayIterator {
	/**
	 * FontAwesome CSS Prefix.
	 *
	 * @access public
	 * @since 1.0
	 * @var string
	 */
	private $prefix;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $path          Path to FontAwesome CSS.
	 * @param string $fa_css_prefix The prefix for icons.
	 */
	public function __construct( $path, $fa_css_prefix = 'fa' ) {

		$this->prefix = $fa_css_prefix;
		$wp_filesystem = Fusion_Helper::init_filesystem();
		$css = $wp_filesystem->get_contents( $path );
		// If it failed, try file_get_contents().
		if ( ! $css ) {
		  // @codingStandardsIgnoreLine
		  $css = @file_get_contents( $path );
		}

		$pattern = '/\.(' . $fa_css_prefix . '-(?:\w+(?:-)?)+):before\{\s*content:\s*"(.+)";*\s*}/U';

		preg_match_all( $pattern, $css, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			$icon = new Fusion_Icon( $this, $match[1], $match[2] );
			$this->addIcon( $icon );
		}
	}

	/**
	 * Adds the icon.
	 *
	 * @access private
	 * @since 1.0
	 * @param object $icon The icon.
	 */
	private function addIcon( $icon ) {

		$this->append( $icon );
	}

	/**
	 * Returns the prefix.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function getPrefix() {

		return (string) $this->prefix;
	}
}

if ( ! function_exists( 'fusion_get_icons_array' ) ) {
	/**
	 * Get an array of available icons.
	 *
	 * @return array
	 */
	function fusion_get_icons_array() {
		$icons = new Fusion_FAIterator( FUSION_FA_PATH );
		$icons_array = array();

		foreach ( $icons as $icon ) {
			$icons_array[ $icon->class ] = $icon->class;
		}
		return $icons_array;
	}
}
