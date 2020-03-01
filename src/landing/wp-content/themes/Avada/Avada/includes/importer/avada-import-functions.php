<?php
/**
 * A collection of functions used by the importer.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Importer
 * @since      5.2
 */

/**
 * Don't resize images.
 * Returns an empty array.
 *
 * @since 5.2
 * @param array $sizes We don't really care in this context...
 * @return array
 */
function avada_filter_image_sizes( $sizes ) {
	return array();
}


/**
 * Parsing Widgets Function
 *
 * @since 5.2
 * @see http://wordpress.org/plugins/widget-settings-importexport/
 * @param string $widget_data The widget-data, JSON-formatted.
 */
function fusion_import_widget_data( $widget_data ) {
	$json_data = json_decode( $widget_data, true );

	$sidebar_data = $json_data[0];
	$widget_data = $json_data[1];

	foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
		$widgets[ $widget_data_title ] = array();
		foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
			if ( is_int( $widget_data_key ) ) {
				$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
			}
		}
	}
	unset( $widgets[''] );

	foreach ( $sidebar_data as $title => $sidebar ) {
		$count = count( $sidebar );
		for ( $i = 0; $i < $count; $i++ ) {
			$widget = array();
			$widget['type'] = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
			$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
			if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
				unset( $sidebar_data[ $title ][ $i ] );
			}
		}
		$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
	}

	foreach ( $widgets as $widget_title => $widget_value ) {
		foreach ( $widget_value as $widget_key => $widget_value ) {
			$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
		}
	}

	$sidebar_data = array( array_filter( $sidebar_data ), $widgets );

	fusion_parse_import_data( $sidebar_data );
}

/**
 * Import data.
 *
 * @since 5.2
 * @param array $import_array The array of data to be imported.
 */
function fusion_parse_import_data( $import_array ) {
	global $wp_registered_sidebars;
	$sidebars_data = $import_array[0];
	$widget_data = $import_array[1];
	$current_sidebars = get_option( 'sidebars_widgets' );
	$new_widgets = array();

	foreach ( $sidebars_data as $import_sidebar => $import_widgets ) {

		foreach ( $import_widgets as $import_widget ) {
			// If the sidebar exists.
			if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) {
				$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
				$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
				$current_widget_data = get_option( 'widget_' . $title );
				$new_widget_name = fusion_get_new_widget_name( $title, $index );
				$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

				if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
					while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
						$new_index++;
					}
				}
				$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
				if ( array_key_exists( $title, $new_widgets ) ) {
					if ( 'nav_menu' == $title & ! is_numeric( $index ) ) {
						$menu = wp_get_nav_menu_object( $index );
						$menu_id = $menu->term_id;
						$new_widgets[ $title ][ $new_index ] = $menu_id;
					} else {
						$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
					}
					$multiwidget = $new_widgets[ $title ]['_multiwidget'];
					unset( $new_widgets[ $title ]['_multiwidget'] );
					$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
				} else {
					if ( 'nav_menu' == $title & ! is_numeric( $index ) ) {
						$menu = wp_get_nav_menu_object( $index );
						$menu_id = $menu->term_id;
						$current_widget_data[ $new_index ] = $menu_id;
					} else {
						$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
					}
					$current_multiwidget = isset( $current_widget_data['_multiwidget'] ) ? $current_widget_data['_multiwidget'] : false;
					$new_multiwidget = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
					$multiwidget = ( $current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
					unset( $current_widget_data['_multiwidget'] );
					$current_widget_data['_multiwidget'] = $multiwidget;
					$new_widgets[ $title ] = $current_widget_data;
				}
			} // End if().
		} // End foreach().
	} // End foreach().

	if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
		update_option( 'sidebars_widgets', $current_sidebars );

		foreach ( $new_widgets as $title => $content ) {
			update_option( 'widget_' . $title, $content );
		}
		return true;
	}
	return false;
}

/**
 * Get the new widget name.
 *
 * @since 5.2
 * @param string $widget_name  The widget-name.
 * @param int    $widget_index The index of the widget.
 * @return array
 */
function fusion_get_new_widget_name( $widget_name, $widget_index ) {
	$current_sidebars = get_option( 'sidebars_widgets' );
	$all_widget_array = array();
	foreach ( $current_sidebars as $sidebar => $widgets ) {
		if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
			foreach ( $widgets as $widget ) {
				$all_widget_array[] = $widget;
			}
		}
	}
	while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
		$widget_index++;
	}
	$new_widget_name = $widget_name . '-' . $widget_index;
	return $new_widget_name;
}

/**
 * Rename sidebar.
 *
 * @since 5.2
 * @param string $name The name.
 * @return string
 */
function avada_name_to_class( $name ) {
	$class = str_replace( array( ' ', ',', '.', '"', "'", '/', '\\', '+', '=', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`', '<', '>', '?', '[', ']', '{', '}', '|', ':' ), '', $name );
	return $class;
}

/**
 * Replaces URLs.
 *
 * @since 5.2
 * @param array $matches The matches.
 * @return string
 */
function fusion_fs_importer_replace_url( $matches ) {
	// Get the uploads folder.
	$wp_upload_dir = wp_upload_dir();
	if ( is_array( $matches ) ) {
		foreach ( $matches as $key => $match ) {
			if ( false !== strpos( $match, 'wp-content/uploads/sites/' ) ) {
				$parts = explode( 'wp-content/uploads/sites/', $match );
				if ( isset( $parts[1] ) ) {
					$sub_parts = explode( '/', $parts[1] );
					unset( $sub_parts[0] );
					$parts[1] = implode( '/', $sub_parts );

					// append the url to the uploads url.
					$parts[0] = $wp_upload_dir['baseurl'];
					return implode( '/', $parts );
				}
			}
		}
	}
	return $matches;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
