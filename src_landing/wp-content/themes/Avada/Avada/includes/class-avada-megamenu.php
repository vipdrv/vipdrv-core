<?php
/**
 * Manipulate mega-menus.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.4.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

// Don't duplicate me!
if ( ! class_exists( 'Avada_Megamenu' ) ) {

	/**
	 * Class to manipulate menus.
	 */
	class Avada_Megamenu extends Avada_Megamenu_Framework {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {
			add_action( 'wp_update_nav_menu_item', array( $this, 'save_custom_menu_style_fields' ), 10, 3 );
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'add_menu_style_data_to_menu' ) );
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'add_custom_fields' ) );
		}

		/**
		 * Function to replace normal edit nav walker for fusion core mega menus.
		 *
		 * @return string Class name of new navwalker
		 */
		public function add_custom_fields() {
			return 'Avada_Nav_Walker_Megamenu';
		}

		/**
		 * Add the custom megamenu fields menu item data to fields in database.
		 *
		 * @access public
		 * @param string|int $menu_id         The menu ID.
		 * @param string|int $menu_item_db_id The menu ID from the db.
		 * @param array      $args            The arguments array.
		 * @return void
		 */
		public function save_custom_menu_style_fields( $menu_id, $menu_item_db_id, $args ) {

			$meta_data  = get_post_meta( $menu_item_db_id );
			$avada_meta = ! empty( $meta_data['_menu_item_fusion_megamenu'][0] ) ? maybe_unserialize( $meta_data['_menu_item_fusion_megamenu'][0] ) : array();

			$field_name_suffix = array( 'icon', 'icononly', 'modal', 'highlight-label', 'highlight-label-background', 'highlight-label-color', 'highlight-label-border-color' );
			if ( ! $args['menu-item-parent-id'] ) {
				$field_name_suffix = array( 'style', 'icon', 'icononly', 'modal', 'highlight-label', 'highlight-label-background', 'highlight-label-color', 'highlight-label-border-color' );
			}

			if ( Avada()->settings->get( 'disable_megamenu' ) ) {

				$megamenu_field_name_suffix = array( 'title', 'widgetarea', 'columnwidth', 'thumbnail', 'background-image' );

				if ( ! $args['menu-item-parent-id'] ) {
					$megamenu_field_name_suffix = array( 'status', 'width', 'columns', 'columnwidth', 'thumbnail', 'background-image' );
				}

				$field_name_suffix = array_merge( $field_name_suffix, $megamenu_field_name_suffix );
			}

			foreach ( $field_name_suffix as $key ) {
				if ( ! isset( $_REQUEST[ 'menu-item-fusion-megamenu-' . $key ][ $menu_item_db_id ] ) ) {
					$_REQUEST[ 'menu-item-fusion-megamenu-' . $key ][ $menu_item_db_id ] = '';
				}
				$avada_meta[ str_replace( '-', '_', $key ) ] = sanitize_text_field( wp_unslash( $_REQUEST[ 'menu-item-fusion-megamenu-' . $key ][ $menu_item_db_id ] ) );
			}

			update_post_meta( $menu_item_db_id, '_menu_item_fusion_megamenu', $avada_meta );
		}

		/**
		 * Add custom megamenu fields data to the menu.
		 *
		 * @access public
		 * @param object $menu_item A single menu item.
		 * @return object The menu item.
		 */
		public function add_menu_style_data_to_menu( $menu_item ) {

			$meta_data  = get_post_meta( $menu_item->ID );
			$avada_meta = ! empty( $meta_data['_menu_item_fusion_megamenu'][0] ) ? maybe_unserialize( $meta_data['_menu_item_fusion_megamenu'][0] ) : array();

			if ( ! $menu_item->menu_item_parent ) {
				$menu_item->fusion_menu_style = isset( $avada_meta['style'] ) ? $avada_meta['style'] : '';
			}

			$menu_item->fusion_menu_icononly  = isset( $avada_meta['icononly'] ) ? $avada_meta['icononly'] : '';
			$menu_item->fusion_megamenu_icon  = isset( $avada_meta['icon'] ) ? $avada_meta['icon'] : '';
			$menu_item->fusion_megamenu_modal = isset( $avada_meta['modal'] ) ? $avada_meta['modal'] : '';

			$menu_item->fusion_highlight_label               = isset( $avada_meta['highlight_label'] ) ? $avada_meta['highlight_label'] : '';
			$menu_item->fusion_highlight_label_background    = isset( $avada_meta['highlight_label_background'] ) ? $avada_meta['highlight_label_background'] : '';
			$menu_item->fusion_highlight_label_color         = isset( $avada_meta['highlight_label_color'] ) ? $avada_meta['highlight_label_color'] : '';
			$menu_item->fusion_highlight_label_border_color  = isset( $avada_meta['highlight_label_border_color'] ) ? $avada_meta['highlight_label_border_color'] : '';

			if ( Avada()->settings->get( 'disable_megamenu' ) ) {
				if ( ! $menu_item->menu_item_parent ) {
					$menu_item->fusion_megamenu_status  = isset( $avada_meta['status'] ) ? $avada_meta['status'] : 'disabled';
					$menu_item->fusion_megamenu_width   = isset( $avada_meta['width'] ) ? $avada_meta['width'] : '';
					$menu_item->fusion_megamenu_columns = isset( $avada_meta['columns'] ) ? $avada_meta['columns'] : '';
				} else {
					$menu_item->fusion_megamenu_title      = isset( $avada_meta['title'] ) ? $avada_meta['title'] : '';
					$menu_item->fusion_megamenu_widgetarea = isset( $avada_meta['widgetarea'] ) ? $avada_meta['widgetarea'] : '';
				}
				$menu_item->fusion_megamenu_columnwidth = isset( $avada_meta['columnwidth'] ) ? $avada_meta['columnwidth'] : '';
				$menu_item->fusion_megamenu_thumbnail   = isset( $avada_meta['thumbnail'] ) ? $avada_meta['thumbnail'] : '';
				$menu_item->fusion_megamenu_background_image   = isset( $avada_meta['background_image'] ) ? $avada_meta['background_image'] : '';
			}

			return $menu_item;

		}
	}
} // End if().

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
