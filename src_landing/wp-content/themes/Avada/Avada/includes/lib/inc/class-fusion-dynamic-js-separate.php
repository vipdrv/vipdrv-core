<?php
/**
 * Dynamic-JS loader - Separate Scripts Method.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles enqueueing files dynamically.
 */
final class Fusion_Dynamic_JS_Separate {

	/**
	 * The Fusion_Dynamic_JS object.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var object
	 */
	protected $dynamic_js;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $dynamic_js An instance of the Fusion_Dynamic_JS object.
	 */
	public function __construct( $dynamic_js ) {

		$this->dynamic_js = $dynamic_js;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_separate_scripts' ) );
	}

	/**
	 * Enqueues separate scripts.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function enqueue_separate_scripts() {

		$wp_content_dir = untrailingslashit( wp_normalize_path( WP_CONTENT_DIR ) );
		$wp_content_url = content_url();

		foreach ( $this->dynamic_js->get_scripts() as $script ) {
			// Get URL in case we're using path.
			if ( 0 !== strpos( $script['url'], 'http' ) ) {
				$script['url'] = str_replace( $wp_content_dir, $wp_content_url, wp_normalize_path( $script['url'] ) );
			}

			// Strip protocols.
			$script['url']  = set_url_scheme( $script['url'] );

			wp_enqueue_script(
				$script['handle'],
				$script['url'],
				$script['deps'],
				$script['ver'],
				$script['in_footer']
			);
		}
		foreach ( $this->dynamic_js->get_localizations() as $l10n ) {
			wp_localize_script(
				$l10n['handle'],
				$l10n['name'],
				$l10n['data']
			);
		}
	}
}
