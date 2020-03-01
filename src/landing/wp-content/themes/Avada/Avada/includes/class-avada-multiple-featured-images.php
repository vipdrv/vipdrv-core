<?php
/**
 * Handles multiple featured images.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles multiple featured images.
 */
class Avada_Multiple_Featured_Images {

	/**
	 * Constructor.
	 *
	 * @access  public
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'after_setup_theme', array( $this, 'generate' ) );
		}
	}

	/**
	 * Generates the multiple images.
	 *
	 * @access  public
	 */
	public function generate() {
		$post_types = array(
			'post',
			'page',
			'avada_portfolio',
		);

		$i = 2;

		while ( $i <= Avada()->settings->get( 'posts_slideshow_number' ) ) {

			foreach ( $post_types as $post_type ) {
				new Fusion_Featured_Image( array(
					'id'           => 'featured-image-' . $i,
					'post_type'    => $post_type,
					'name'         => sprintf( __( 'Featured image %s', 'Avada' ), $i ),
					'label_set'	   => sprintf( __( 'Set featured image %s', 'Avada' ), $i ),
					'label_remove' => sprintf( __( 'Remove featured image %s', 'Avada' ), $i ),
				) );
			}

			$i++;

		}

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
