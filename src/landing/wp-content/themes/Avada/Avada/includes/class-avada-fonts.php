<?php
/**
 * Fonts handling.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Fonts handling.
 */
class Avada_Fonts {

	/**
	 * Constructor.
	 *
	 * @access  public
	 */
	public function __construct() {
		add_filter( 'upload_mimes', array( $this, 'mime_types' ) );
	}

	/**
	 * Allow uploading font file types.
	 *
	 * @param array $mimes The mime types allowed.
	 * @access public
	 */
	public function mime_types( $mimes ) {

		$mimes['ttf']   = $this->get_mime( 'ttf' );
		$mimes['woff']  = $this->get_mime( 'woff' );
		$mimes['svg']   = $this->get_mime( 'svg' );
		$mimes['eot']   = $this->get_mime( 'eot' );
		$mimes['woff2'] = 'font/woff2';

		return $mimes;

	}

	/**
	 * Get the MIME type of the font-files
	 * by examining font-files included in the theme.
	 *
	 * @access private
	 * @since 5.2
	 * @param string $file_type The file-type we want to check.
	 * @return string
	 */
	private function get_mime( $file_type ) {

		$path = wp_normalize_path( get_template_directory() . '/assets/fonts/fusion-icon/fusion-icon.' . $file_type );
		if ( file_exists( $path ) && function_exists( 'mime_content_type' ) ) {
			return mime_content_type( $path );
		}
		return 'font/' . $file_type;

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
