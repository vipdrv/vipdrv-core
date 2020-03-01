<?php
/**
 * Tracks imported content.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Importer
 * @since      5.2
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Class Avada_Demo_Content_Tracker
 */
class Avada_Demo_Content_Tracker {

	/**
	 * Currently importing / removing demo_type.
	 *
	 * @access private
	 * @since 5.2
	 * @var string
	 */
	private $demo_type;

	/**
	 * Array of arrays. Key is demo_type, value is array containing demo backup.
	 *
	 * @access private
	 * @since 5.2
	 * @var mixed|void
	 */
	private $demo_history;

	/**
	 * The defaults.
	 *
	 * @access private
	 * @since 5.2
	 * @var array
	 */
	private $import_data_defaults;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $demo_type The demo type.
	 */
	public function __construct( $demo_type ) {

		$this->demo_type = $demo_type;

		$this->demo_history = get_option( 'fusion_demo_history', array() );

		$this->import_data_defaults = array(
			'post'            => array(),
			'page'            => array(),
			'avada_portfolio' => array(),
			'avada_faq'       => array(),
			'attachment'      => array(),
			'product'         => array(),
			'event'           => array(),
			'forum'           => array(),
			'sliders'         => array(),
			'widgets'         => array(),
			'theme_options'   => array(),
			'general_data'    => array(),
			'all'             => array(),
		);

		add_action( 'wxr_importer.processed.term', array( $this, 'add_term_to_stack' ), 10, 2 );
		add_action( 'fusion_slider_import_processed_term', array( $this, 'add_fusion_slider_term_to_stack' ), 10, 2 );
	}

	/**
	 * Saves import data.
	 * Import data is used to quickly get which parts of which demo are imported.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $import_stage Demo type.
	 * @param array  $import_type  The import type.
	 */
	public function save_import_stage_data( $import_stage, $import_type ) {

		$import_data = get_option( 'fusion_import_data', $this->import_data_defaults );

		$import_data[ $import_stage ] = $import_type;

		update_option( 'fusion_import_data', $import_data, false );
	}

	/**
	 * Gets import data for specific stage.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $import_stage The import stage.
	 * @return array
	 */
	public function get_import_stage_data( $import_stage ) {

		$import_data = get_option( 'fusion_import_data', $this->import_data_defaults );

		if ( empty( $import_data[ $import_stage ] ) ) {
			return array();
		}

		return $import_data[ $import_stage ];
	}

	/**
	 * Updates import data.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $import_stage The import stage.
	 */
	public function update_import_stage_data( $import_stage ) {

		$data   = $this->get_import_stage_data( $import_stage );
		$data[] = $this->demo_type;

		$this->save_import_stage_data( $import_stage, $data );
	}

	/**
	 * Resets the stage.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $stage The import stage.
	 */
	public function reset_stage( $stage ) {

		$data = $this->get_import_stage_data( $stage );
		$key  = array_search( $this->demo_type, $data );
		if ( is_array( $data ) && false !== $key ) {
			unset( $data[ $key ] );
		}
		$this->save_import_stage_data( $stage, $data );
	}

	/**
	 * Adds general data to demo history.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function set_general_data() {

		$this->demo_history[ $this->demo_type ]['blogname']           = get_option( 'blogname' );
		$this->demo_history[ $this->demo_type ]['page_on_front']      = get_option( 'page_on_front' );
		$this->demo_history[ $this->demo_type ]['show_on_front']      = get_option( 'show_on_front' );
		$this->demo_history[ $this->demo_type ]['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
	}

	/**
	 * Adds Theme Options backup to demo history.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function set_theme_options() {
		$this->demo_history[ $this->demo_type ]['theme_options'] = get_option( Avada::get_original_option_name() );
	}

	/**
	 * Adds widgets backup to demo history.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function set_widgets() {
		$this->demo_history[ $this->demo_type ]['widgets']          = $this->fetch_widgets();
		$this->demo_history[ $this->demo_type ]['sidebars_widgets'] = get_option( 'sidebars_widgets' );
		$this->demo_history[ $this->demo_type ]['sbg_sidebars']     = get_option( 'sbg_sidebars' );
	}

	/**
	 * Fetches the widgets so they can be added to the demo history.
	 *
	 * @access public
	 * @since 5.2
	 * @return array
	 */
	public function fetch_widgets() {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'widget_%'" );

		if ( is_wp_error( $results ) ) {
			$results = array();
		}
		return $results;
	}

	/**
	 * Adds Revolution slider IDs to demo history.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $slider_id The slider-ID.
	 */
	public function add_rev_slider_to_stack( $slider_id ) {

		if ( ! isset( $this->demo_history[ $this->demo_type ]['rev_sliders'] ) ) {
			$this->demo_history[ $this->demo_type ]['rev_sliders'] = array();
		}
		$this->demo_history[ $this->demo_type ]['rev_sliders'][] = (int) $slider_id;
	}

	/**
	 * Adds Layer slider IDs to demo history.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $slider_id The slider-ID.
	 */
	public function add_layer_slider_to_stack( $slider_id ) {

		if ( ! isset( $this->demo_history[ $this->demo_type ]['layer_sliders'] ) ) {
			$this->demo_history[ $this->demo_type ]['layer_sliders'] = array();
		}
		$this->demo_history[ $this->demo_type ]['layer_sliders'][] = (int) $slider_id;
	}

	/**
	 * Adds imported terms to demo history.
	 *
	 * @access public
	 * @since 5.2
	 * @param int   $term_id Term id.
	 * @param array $data    Term data.
	 */
	public function add_term_to_stack( $term_id, $data ) {

		if ( ! isset( $this->demo_history[ $this->demo_type ]['terms'] ) ) {
			$this->demo_history[ $this->demo_type ]['terms'] = array();
		}
		$this->demo_history[ $this->demo_type ]['terms'][] = array(
			'term_id'  => $term_id,
			'taxonomy' => $data['taxonomy'],
		);
	}

	/**
	 * Adds Fusion Sliders to demo history.
	 *
	 * @access public
	 * @since 5.2
	 * @param int   $term_id The term-ID.
	 * @param array $term    The term.
	 */
	public function add_fusion_slider_term_to_stack( $term_id, $term ) {

		if ( ! isset( $this->demo_history[ $this->demo_type ]['fusion_sliders'] ) ) {
			$this->demo_history[ $this->demo_type ]['fusion_sliders'] = array();
		}
		$this->demo_history[ $this->demo_type ]['fusion_sliders'][] = array(
			'term_id'  => $term_id,
			'taxonomy' => $term['taxonomy'],
		);
	}

	/**
	 * Saves demo history.
	 *
	 * @access public
	 * @since 5.2
	 */
	public function save_demo_history() {
		update_option( 'fusion_demo_history', $this->demo_history, false );
	}

	/**
	 * Demo history getter.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $key The key.
	 * @return bool
	 */
	public function get( $key ) {

		if ( empty( $this->demo_history[ $this->demo_type ][ $key ] ) ) {
			return false;
		}
		return $this->demo_history[ $this->demo_type ][ $key ];
	}

	/**
	 * Demo history setter.
	 *
	 * @access public
	 * @since 5.2
	 * @param string $key   The key.
	 * @param mixed  $value The value.
	 */
	public function set( $key, $value ) {

		$this->demo_history[ $this->demo_type ][ $key ] = $value;
		$this->save_demo_history();
	}

	/**
	 * Removes all demo backup data
	 *
	 * @access public
	 * @since 5.2
	 */
	public function remove_demo() {

		if ( isset( $this->demo_history[ $this->demo_type ] ) ) {
			unset( $this->demo_history[ $this->demo_type ] );
		}
		$this->save_demo_history();
	}
}
