<?php
/**
 * Dynamic-CSS handler - Inline CSS.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle generating the dynamic CSS.
 *
 * @since 1.0.0
 */
class Fusion_Dynamic_CSS_File {

	/**
	 * An innstance of the Fusion_Dynamic_CSS object.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var object
	 */
	private $dynamic_css;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param object $dynamic_css An instance of Fusion_DYnamic_CSS.
	 */
	public function __construct( $dynamic_css ) {

		$this->dynamic_css = $dynamic_css;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_css' ), 11 );

		// Make sure file mode dynamic CSS is not created in backend.
		if ( is_admin() ) {
			return;
		}

		$needs_update = $this->dynamic_css->needs_update();

		// No need to proceed any further if there's no need to update the CSS
		// and the file exists.
		if ( ! $needs_update && file_exists( $this->file( 'path' ) ) ) {
			return;
		}

		// If we got this far, we need to generate the file.
		// First try to check if the file is writable.
		// Then attempt to create the file.
		// Finally check if the file exists.
		// If all of the above tests succeed, then the file is properly created.
		if ( $this->can_write() ) {
			if ( $this->write_file() ) {
				if ( file_exists( $this->file( 'path' ) ) ) {
					return;
				}
			}
		}
		// If we got this far we need to fallback to inline mode.
		$dynamic_css->inline = new Fusion_Dynamic_CSS_Inline( $dynamic_css );
		$dynamic_css->mode   = 'inline';
	}

	/**
	 * Enqueue the dynamic CSS.
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_dynamic_css() {

		// Nothing to enquue if the file doesn't exist.
		// If that happens we've fallen back to inline mode (see the class's constructor).
		if ( ! file_exists( $this->file( 'path' ) ) ) {
			return;
		}

		$dependencies = apply_filters( 'fusion_dynamic_css_stylesheet_dependencies', array() );
		wp_enqueue_style( 'fusion-dynamic-css', $this->file( 'uri' ), $dependencies );

	}

	/**
	 * Gets the css path or url to the stylesheet.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $target path/url.
	 * @return string Path or url to the file depending on the $target var.
	 */
	public function file( $target = 'path' ) {

		// Get the blog ID.
		$blog_id = '';
		// If this is a multisite installation, append the blogid to the filename.
		if ( is_multisite() ) {
			$current_site = get_blog_details();
			if ( $current_site->blog_id > 1 ) {
				$blog_id = "_blog-{$current_site->blog_id}";
			}
		}

		$fusion_library = Fusion::get_instance();
		$page_id        = ( $fusion_library->get_page_id() ) ? $fusion_library->get_page_id() : 'global';

		// If WooCommerce is active and we are on archive, use global CSS not shop page, which is return by get_page_id.
		if ( class_exists( 'WooCommerce' ) && ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
			$page_id = 'global';
		}

		$file_name = "fusion-styles/fusion{$blog_id}-{$page_id}.css";

		if ( 'filename' === $target ) {
			return $file_name;
		}

		$file = new Fusion_Filesystem( $file_name );

		// Return the path or the URL
		// depending on the $target we have defined when calling this method.
		if ( 'path' === $target ) {
			return $file->get_path();
		}
		return $file->get_url();

	}

	/**
	 * Writes the file.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @return bool Whether the file is successfully created or not.
	 */
	protected function write_file() {

		// The CSS.
		$css      = $this->dynamic_css->make_css();
		$filename = $this->file( 'filename' );
		$file     = new Fusion_Filesystem( $filename );

		if ( false === $file->write_file( $css ) ) {
			return false;
		}

		/**
		 * Writing to the file succeeded.
		 * Update the opion in the db so that we know the css for this post
		 * has been successfully generated
		 * and then return true.
		 */
		$fusion_library = Fusion::get_instance();

		$page_id = ( $fusion_library->get_page_id() ) ? $fusion_library->get_page_id() : 'global';
		$option  = get_option( 'fusion_dynamic_css_posts', array() );

		// Update the 'fusion_dynamic_css_time' option.
		$option[ $page_id ] = true;
		update_option( 'fusion_dynamic_css_posts', $option );
		$this->dynamic_css->update_saved_time();

		// Clean-up transient.
		$page_id = ( $fusion_library->get_page_id() ) ? $fusion_library->get_page_id() : 'global';

		// If WooCommerce is active and we are on archive, use global CSS not shop page, which is return by get_page_id.
		if ( class_exists( 'WooCommerce' ) && ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
			$page_id = 'global';
		}

		delete_transient( 'fusion_dynamic_css_' . $page_id );

		return true;
	}

	/**
	 * Determines if the CSS file is writable.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return bool
	 */
	private function can_write() {

		$file = new Fusion_Filesystem( $this->file( 'filename' ) );
		return $file->is_writable();

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
