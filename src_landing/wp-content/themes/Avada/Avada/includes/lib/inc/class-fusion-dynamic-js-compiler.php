<?php
/**
 * Dynamic-JS loader - Compiler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles the scripts compiling.
 */
class Fusion_Dynamic_JS_Compiler {

	/**
	 * The Fusion_Dynamic_JS object.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var object
	 */
	protected $dynamic_js;

	/**
	 * An array of our scripts.
	 * Each script also lists its dependencies.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $scripts = array();

	/**
	 * Have the scripts been reordered already?
	 *
	 * @static
	 * @access private
	 * @since 1.0.0
	 * @var bool
	 */
	private static $reordered = false;

	/**
	 * An array of external dependencies.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $external_dependencies = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $dynamic_js An instance of the Fusion_Dynamic_JS object.
	 */
	public function __construct( $dynamic_js ) {

		$this->dynamic_js = $dynamic_js;

	}

	/**
	 * Gets the compiled JS.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_compiled_js() {

		$scripts = $this->get_scripts();
		$l10n    = $this->dynamic_js->get_localizations();

		$content = '';

		// Init the filesystem.
		$wp_filesystem = Fusion_Helper::init_filesystem();

		// Super-dependencies.
		foreach ( $scripts as $key => $script ) {

			if ( 'enqueue' === $script['action'] ) {
				if ( 'cssua' === $script['handle'] || 'modernizr' === $script['handle'] ) {
					$path = $script['path'] ;

					// Skip if the path is empty or file doesn't exist.
					if ( ! $path || ! file_exists( $path ) ) {
						continue;
					}
					// Add the contents of the JS file.
					$file_content = $wp_filesystem->get_contents( $path );
					// If it failed, try file_get_contents().
					if ( ! $file_content ) {
						// @codingStandardsIgnoreLine
						$file_content = @file_get_contents( $path );
					}
					$file_content = trim( $file_content );
					if ( ! empty( $file_content ) ) {
						// Sometimes minimized scripts omit the closing column at the end.
						// Check and add missing ';' here.
						if ( ';' !== substr( $file_content, -1 ) && '}' !== substr( $file_content, -1 ) && ')' !== substr( $file_content, -1 ) ) {
							$file_content .= ';';
						}
						$content .= $file_content;
						// Add a blank line after each script.
						$content .= PHP_EOL;
					}
					unset( $scripts[ $key ] );
				}
			}
		}

		// Add enqueued scripts.
		foreach ( $scripts as $script ) {
			// Only add enqueued scripts, not just registered ones.
			if ( 'enqueue' !== $script['action'] ) {
				continue;
			}

			// Localize scripts.
			foreach ( $l10n as $l10n_script ) {
				if ( $script['handle'] !== $l10n_script['handle'] ) {
					continue;
				}
				foreach ( (array) $l10n_script['data'] as $key => $value ) {
					if ( ! is_scalar( $value ) ) {
						continue;
					}
					$l10n_script['data'][ $key ] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
				}
				$value = wp_json_encode( $l10n_script['data'] );
				$content .= "var {$l10n_script['name']}={$value};";
			}

			$path = $script['path'] ;
			// Skip if the path is empty or file doesn't exist.
			if ( ! $path || ! file_exists( $path ) ) {
				continue;
			}
			// Add the contents of the JS file.
			$file_content = $wp_filesystem->get_contents( $path );
			if ( ! $file_content ) {
				// @codingStandardsIgnoreLine
				$file_content = @file_get_contents( $path );
			}
			$file_content = trim( $file_content );
			if ( ! empty( $file_content ) ) {
				// Sometimes minimized scripts omit the closing column at the end.
				// Check and add missing ';' here.
				if ( ';' !== substr( $file_content, -1 ) && '}' !== substr( $file_content, -1 ) && ')' !== substr( $file_content, -1 ) ) {
					$file_content .= ';';
				}
				$content .= $file_content;
				// Add a blank line after each script.
				$content .= PHP_EOL;
			}
		}// End foreach().
		return $content;
	}

	/**
	 * Reorder scripts based on their dependencies.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function reorder_scripts() {

		// Build an ordered array of our dependent scripts.
		$dependent_scripts = array();
		foreach ( self::$scripts as $key => $script ) {

			// Check if the script has dependencies.
			if ( isset( $script['deps'] ) && ! empty( $script['deps'] ) ) {
				foreach ( $script['deps'] as $dependency_key => $dependency ) {

					// Check if our dependencies exist.
					// If not, assume they are external dependencies.
					if ( false === $this->get_key_from_handle( $dependency ) ) {
						self::$external_dependencies[] = $dependency;
						unset( self::$scripts['deps'][ $dependency_key ] );
						continue;
					}

					// Make sure dependency is enqueued.
					self::$scripts[ $this->get_key_from_handle( $dependency ) ]['action'] = 'enqueue';

					// Inject item in array.
					if ( in_array( $dependency, $dependent_scripts, true ) ) {
						$dependent_key     = array_search( $dependency, $dependent_scripts, true );
						$dependent_scripts = $this->add_element( $dependent_scripts, $dependent_key, $dependency );
					}

					// Add the script to the end of the array if it doesn't exist.
					if ( ! in_array( $script['handle'], $dependent_scripts, true ) ) {
						$dependent_scripts[] = $script['handle'];
					}
					$dependent_scripts = array_unique( $dependent_scripts );
				}
			}
		}

		// Go through our dependent scripts and shuffle them in the self::$scripts array
		// so that the final array is ordered for dependencies handling.
		$dependent_scripts = array_reverse( $dependent_scripts );
		foreach ( $dependent_scripts as $dependent ) {
			$key    = $this->get_key_from_handle( $dependent );
			$script = self::$scripts[ $key ];
			self::$scripts[] = $script;
			unset( self::$scripts[ $key ] );
		}
	}

	/**
	 * Find the key of an item in the script array using the script's handle.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param string $handle The script's handle.
	 * @return int           The position of the script in self::$scripts.
	 */
	private function get_key_from_handle( $handle ) {

		foreach ( self::$scripts as $key => $script ) {
			if ( $handle === $script['handle'] ) {
				return $key;
			}
		}
		return false;
	}

	/**
	 * Add element in the middle of an array.
	 *
	 * @access public
	 * @access protected
	 * @since 1.0.0
	 * @param array $array     The array.
	 * @param int   $new_key   The position of the new item in the array.
	 * @param mixed $new_value The value of the item we're adding to the array.
	 * @return array
	 */
	protected function add_element( $array, $new_key, $new_value ) {
		$length    = count( $array );
		$new_array = array();
		// If we're adding as the last element it's easy.
		if ( $new_key >= $length ) {
			$array[] = $new_value;
			return $array;
		}

		// Loop the array and add the item where appropriate.
		foreach ( $array as $key => $value ) {
			if ( $key === $new_key ) {
				$new_array[] = $new_value;
				continue;
			}
			$new_array[] = $value;
		}
		return $new_array;
	}

	/**
	 * Get the scripts.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param bool $reorder Whether we want to reorder the scripts or not.
	 * @return array
	 */
	public function get_scripts( $reorder = true ) {

		$dynamic_js    = $this->dynamic_js;
		self::$scripts = $dynamic_js->get_scripts();

		if ( $reorder && ! self::$reordered ) {
			$this->reorder_scripts();
			self::$reordered = true;
		}

		return self::$scripts;
	}

	/**
	 * Get the array of external dependencies.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @return array
	 */
	public function get_external_dependencies() {
		return self::$external_dependencies;
	}
}
