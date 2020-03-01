<?php
/**
 * Adds fusion-builder options in redux.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Adds fusion-builder options in Redux.
 */
class Fusion_Builder_Redux_Options {

	/**
	 * Are we using the Avada theme?
	 *
	 * @access private
	 * @var bool
	 */
	private $is_avada = false;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		// Include the options file.
		include_once wp_normalize_path( dirname( __FILE__ ) . '/options/shortcode_styling.php' );

		// Determine if we're using the Avada theme.
		add_action( 'after_setup_theme', array( $this, 'is_avada' ) );

		// Add options to Avada.
		add_filter( 'avada_options_sections', array( $this, 'add_sections' ), 50 );

	}

	/**
	 * Determines if we're using the Avada theme
	 * and sets the $is_avada property.
	 * This method is added in 'after_setup_theme'.
	 *
	 * @access public
	 */
	public function is_avada() {
		if ( class_exists( 'Avada' ) ) {
			$this->is_avada = true;
		}
	}

	/**
	 * Set the sections.
	 *
	 * @access public
	 * @param array $sections Our Redux sections.
	 * @return array
	 */
	public function add_sections( $sections = array() ) {
		global $fusion_builder_elements;

		// Simplify the array.
		if ( null !== $fusion_builder_elements ) {
			$simplified_array = array();
			foreach ( $fusion_builder_elements as $element ) {
				if ( isset( $element['shortcode'] ) && isset( $element['name'] ) ) {
					$simplified_array[ $element['shortcode'] ] = $element['name'];
				}
			}
			if ( ! empty( $simplified_array ) ) {
				$fusion_builder_elements = $simplified_array;
			}
		}

		// If we can't find the builder elements from the global var,
		// use a hard-coded array.
		if ( null === $fusion_builder_elements ) {
			$fusion_builder_elements = array(
				'fusion_alert'                    => 'Alert',
				'fusion_blog'                     => 'Blog',
				'fusion_button'                   => 'Button',
				'fusion_checklist'                => 'Checklist',
				'fusion_code'                     => 'Code Block',
				'fusion_content_boxes'            => 'Content Boxes',
				'fusion_countdown'                => 'Countdown',
				'fusion_counters_box'             => 'Counter Boxes',
				'fusion_counters_circle'          => 'Counter Circles',
				'fusion_events'                   => 'Events',
				'fusion_faq'                      => 'FAQ',
				'fusion_flip_boxes'               => 'Flip Boxes',
				'fusion_fontawesome'              => 'Font Awesome Icon',
				'fusion_fusionslider'             => 'Fusion Slider',
				'fusion_map'                      => 'Google Map',
				'fusion_images'                   => 'Image Carousel',
				'fusion_imageframe'               => 'Image Frame',
				'layerslider'                     => 'Layer Slider',
				'fusion_login'                    => 'User Login',
				'fusion_register'                 => 'User Register',
				'fusion_lost_password'            => 'User Lost Password',
				'fusion_modal'                    => 'Modal',
				'fusion_person'                   => 'Person',
				'fusion_postslider'               => 'Post Slider',
				'fusion_pricing_table'            => 'Pricing Table',
				'fusion_progress'                 => 'Progress Bar',
				'fusion_recent_posts'             => 'Recent Posts',
				'fusion_portfolio'                => 'Portfolio',
				'rev_slider'                      => 'Revolution Slider',
				'fusion_section_separator'        => 'Section Separator',
				'fusion_separator'                => 'Separator',
				'fusion_sharing'                  => 'Sharing Box',
				'fusion_slider'                   => 'Slider',
				'fusion_social_links'             => 'Social Links',
				'fusion_soundcloud'               => 'Soundcloud',
				'fusion_tabs'                     => 'Tabs',
				'fusion_tagline_box'              => 'Tagline Box',
				'fusion_testimonials'             => 'Testimonials',
				'fusion_text'                     => 'Text Block',
				'fusion_title'                    => 'Title',
				'fusion_accordian'                => 'Toggles',
				'fusion_vimeo'                    => 'Vimeo',
				'fusion_widget_area'              => 'Widget Area',
				'fusion_featured_products_slider' => 'Woo Featured',
				'fusion_products_slider'          => 'Woo Carousel',
				'fusion_woo_shortcodes'           => 'Woo Shortcodes',
				'fusion_youtube'                  => 'Youtube',
			);
		} // End if().

		$option_name = 'fusion_builder_options';
		if ( ! function_exists( 'fusion_builder_redux_shortcode_styling' ) ) {
			return $sections;
		}

		// Get the new options.
		return fusion_builder_redux_shortcode_styling( $sections );

	}
}
