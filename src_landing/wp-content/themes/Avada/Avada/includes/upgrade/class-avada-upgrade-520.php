<?php
/**
 * Upgrades Handler.
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
 * Handle migrations for Avada 5.2.0.
 *
 * @since 5.2.0
 */
class Avada_Upgrade_520 extends Avada_Upgrade_Abstract {

	/**
	 * The version.
	 *
	 * @access protected
	 * @since 5.2.0
	 * @var string
	 */
	protected $version = '5.2.0';

	/**
	 * The actual migration process.
	 *
	 * @access protected
	 * @since 5.2.0
	 * @return void
	 */
	protected function migration_process() {

		$this->update_menu_items();
		$this->update_portfolio_settings();
		$this->update_header_bg_color();

	}

	/**
	 * Update the menu entries to the new data structure.
	 *
	 * @access private
	 * @since 5.2.0
	 * @return void
	 */
	private function update_menu_items() {
		// Update menu item's meta fields to new format and remove old fields.
		$meta_keys = array(
			'status'      => '_menu_item_fusion_megamenu_status',
			'width'       => '_menu_item_fusion_megamenu_width',
			'columns'     => '_menu_item_fusion_megamenu_columns',
			'title'       => '_menu_item_fusion_megamenu_title',
			'widgetarea'  => '_menu_item_fusion_megamenu_widgetarea',
			'columnwidth' => '_menu_item_fusion_megamenu_columnwidth',
			'icon'        => '_menu_item_fusion_megamenu_icon',
			'modal'       => '_menu_item_fusion_megamenu_modal',
			'thumbnail'   => '_menu_item_fusion_megamenu_thumbnail',
			'style'       => '_menu_item_fusion_menu_style',
			'icononly'    => '_menu_item_fusion_menu_icononly',
		);

		$args = array(
			'posts_per_page' => 100,
			'post_type'      => 'nav_menu_item',
			'post_status'    => 'publish',
			'paged'          => 1,
		);

		while ( $posts = get_posts( $args ) ) {

			foreach ( $posts as $post ) {
				$old_meta = get_post_meta( $post->ID );
				$new_meta = array();

				foreach ( $meta_keys as $new_key => $old_key ) {
					$default = '';
					if ( 'status' === $new_key ) {
						$default = 'disabled';
					}

					$new_meta[ $new_key ] = isset( $old_meta[ $old_key ][0] ) ? $old_meta[ $old_key ][0] : $default;
				}

				update_post_meta( $post->ID, '_menu_item_fusion_megamenu', $new_meta );
			}

			$args['paged']++;
		}

		foreach ( $meta_keys as $new_key => $old_key ) {
			delete_post_meta_by_key( $old_key );
		}

	}

	/**
	 * Update portfolio TO settings to new structure and names.
	 *
	 * @access private
	 * @since 5.2.0
	 * @return void
	 */
	private function update_portfolio_settings() {
		$options = get_option( $this->option_name, array() );

		$portfolio_archive_layout = $options['portfolio_archive_layout'];
		$options['portfolio_archive_layout'] = 'grid';

		if ( false !== strpos( $portfolio_archive_layout, 'Two' ) ) {
			$options['portfolio_archive_columns'] = 2;
		} else if ( false !== strpos( $portfolio_archive_layout, 'Three' ) ) {
			$options['portfolio_archive_columns'] = 3;
		} else if ( false !== strpos( $portfolio_archive_layout, 'Four' ) ) {
			$options['portfolio_archive_columns'] = 4;
		} else if ( false !== strpos( $portfolio_archive_layout, 'Five' ) ) {
			$options['portfolio_archive_columns'] = 5;
		} else if ( false !== strpos( $portfolio_archive_layout, 'Six' ) ) {
			$options['portfolio_archive_columns'] = 6;
		} else {
			$options['portfolio_archive_columns'] = 1;
		}

		if ( false === strpos( $portfolio_archive_layout, 'Text' ) && 'Portfolio One Column' !== $portfolio_archive_layout ) {
			$options['portfolio_archive_text_layout'] = 'no_text';
		} elseif ( isset( $options['portfolio_text_layout'] ) ) {
			$options['portfolio_archive_text_layout'] = $options['portfolio_text_layout'];
		}

		if ( 'Portfolio One Column' === $portfolio_archive_layout ) {
			$options['portfolio_archive_one_column_text_position'] = 'floated';
		} else {
			$options['portfolio_archive_one_column_text_position'] = 'below';
		}

		$options_to_migrate = array(
			'portfolio_archive_featured_image_size'     => 'portfolio_featured_image_size',
			'portfolio_archive_column_spacing'          => 'portfolio_column_spacing',
			'portfolio_archive_items'                   => 'portfolio_items',
			'portfolio_excerpt_length'                  => 'excerpt_length_portfolio',
			'portfolio_archive_excerpt_length'          => 'excerpt_length_portfolio',
			'portfolio_archive_strip_html_excerpt'      => 'portfolio_strip_html_excerpt',
			'portfolio_archive_title_display'           => 'portfolio_title_display',
			'portfolio_archive_text_alignment'          => 'portfolio_text_alignment',
			'portfolio_archive_layout_padding'          => 'portfolio_layout_padding',
			'portfolio_load_more_posts_button_bg_color' => 'portfolio_load_more_posts_button_bg_color',
		);

		foreach ( $options_to_migrate as $new => $old ) {
			if ( isset( $options[ $old ] ) ) {
				$options[ $new ] = $options[ $old ];
			}
		}

		$portfolio_content_length = 'excerpt';
		if ( isset( $options['portfolio_content_length'] ) ) {
			$portfolio_content_length = strtolower( str_replace( ' ', '_', $options['portfolio_content_length'] ) );
		}
		$options['portfolio_content_length']         = $portfolio_content_length;
		$options['portfolio_archive_content_length'] = $portfolio_content_length;

		unset( $options['excerpt_length_portfolio'] );

		$portfolio_pagination_type = 'pagination';
		if ( isset( $options['grid_pagination_type'] ) ) {
			$portfolio_pagination_type = strtolower( str_replace( ' ', '_', $options['grid_pagination_type'] ) );
		}
		$options['portfolio_pagination_type']         = $portfolio_pagination_type;
		$options['portfolio_archive_pagination_type'] = $portfolio_pagination_type;
		unset( $options['grid_pagination_type'] );

		update_option( $this->option_name, $options );
	}

	/**
	 * Update the header bg color to #ffffff if it is set to transparent.
	 *
	 * @access private
	 * @since 5.2.0
	 * @return void
	 */
	private function update_header_bg_color() {
		$options = get_option( $this->option_name, array() );
		if ( 'transparent' === $options['header_bg_color'] ) {
			$options['header_bg_color'] = '#ffffff';
			update_option( $this->option_name, $options );
		}
	}
}
