<?php
/**
 * Fusion Builder helper functions.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Fix shortcode content. Remove p and br tags.
 *
 * @since 1.0
 * @param string $content The content.
 * @return string
 */
function fusion_builder_fix_shortcodes( $content ) {
	$replace_tags_from_to = array(
		'<p>[' => '[',
		']</p>' => ']',
		']<br />' => ']',
		"<br />\n[" => '[',
	);

	return strtr( $content, $replace_tags_from_to );
}

/**
 * Get video prodiver.
 *
 * @since 1.0
 * @param string $video_string The video as entered by the user.
 * @return array
 */
function fusion_builder_get_video_provider( $video_string ) {

	$video_string = trim( $video_string );

	// Check for YouTube.
	$video_id = false;
	if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video_string, $id ) ) {
		if ( count( $id > 1 ) ) {
			$video_id = $id[1];
		}
	} else if ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $video_string, $id ) ) {
		if ( count( $id > 1 ) ) {
			$video_id = $id[1];
		}
	} else if ( preg_match( '/youtube\.com\/v\/([^\&\?\/]+)/', $video_string, $id ) ) {
		if ( count( $id > 1 ) ) {
			$video_id = $id[1];
		}
	} else if ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $video_string, $id ) ) {
		if ( count( $id > 1 ) ) {
			$video_id = $id[1];
		}
	}

	if ( ! empty( $video_id ) ) {
		return array(
			'type' => 'youtube',
			'id'   => $video_id,
		);
	}

	// Check for Vimeo.
	if ( preg_match( '/vimeo\.com\/(\w*\/)*(\d+)/', $video_string, $id ) ) {
		if ( count( $id > 1 ) ) {
			$video_id = $id[ count( $id ) - 1 ];
		}
	}

	if ( ! empty( $video_id ) ) {
		return array(
			'type' => 'vimeo',
			'id'   => $video_id,
		);
	}

	// Non-URL form.
	if ( preg_match( '/^\d+$/', $video_string ) ) {
		return array(
			'type' => 'vimeo',
			'id'   => $video_string,
		);
	}

	return array(
		'type' => 'youtube',
		'id'   => $video_string,
	);
}

/**
 * Create animation data and class.
 *
 * @since 1.0
 * @param string $animation_type      The animation type.
 * @param string $animation_direction Animation direction.
 * @param string $animation_speed     The animation speed (in miliseconds).
 * @param string $animation_offset    The animation offset.
 */
function fusion_builder_animation_data( $animation_type = '', $animation_direction = '', $animation_speed = '', $animation_offset = '' ) {

	$animation = array();
	$animation['data'] = '';
	$animation['class'] = '';

	if ( ! empty( $animation_type ) ) {

		if ( ! in_array( $animation_type, array( 'bounce', 'flase', 'shake', 'rubberBand' ), true ) ) {
			$animation_type = sprintf( '%1$sIn%2$s', $animation_type, ucfirst( $animation_direction ) );
		}

		$animation['data'] .= ' data-animationType=' . esc_attr( str_replace( 'Static', '', $animation_type ) );
		$animation['data'] .= ' data-animationDuration=' . esc_attr( $animation_speed );
		$animation['class'] = ' fusion-animated';

		if ( $animation_offset ) {
			if ( 'top-into-view' === $animation_offset ) {
				$offset = '100%';
			} elseif ( 'top-mid-of-view' === $animation_offset ) {
				$offset = '50%';
			} else {
				$offset = $animation_offset;
			}
			$animation['data'] .= ' data-animationOffset=' . esc_attr( $offset );
		}
	}

	return $animation;
}

/**
 * List of available animation types.
 *
 * @since 1.0
 */
function fusion_builder_available_animations() {

	$animations = array(
		''           => esc_attr__( 'None', 'fusion-builder' ),
		'bounce'     => esc_attr__( 'Bounce', 'fusion-builder' ),
		'fade'       => esc_attr__( 'Fade', 'fusion-builder' ),
		'flash'      => esc_attr__( 'Flash', 'fusion-builder' ),
		'rubberBand' => esc_attr__( 'Rubberband', 'fusion-builder' ),
		'shake'      => esc_attr__( 'Shake', 'fusion-builder' ),
		'slide'      => esc_attr__( 'Slide', 'fusion-builder' ),
		'zoom'       => esc_attr__( 'Zoom', 'fusion-builder' ),
	);

	return $animations;
}

/**
 * Check value type ( % or px ).
 *
 * @since 1.0
 * @param string $value The value we'll be checking.
 * @return string
 */
function fusion_builder_check_value( $value ) {
	if ( strpos( $value, '%' ) === false && strpos( $value, 'px' ) === false ) {
		$value = $value . 'px';
	}
	return $value;
}

/**
 * Returns array of layerslider slide groups.
 *
 * @since 1.0
 * @return array slide keys array.
 */
function fusion_builder_get_layerslider_slides() {
	global $wpdb;
	$slides_array[] = 'Select a slider';
	// Table name.
	$table_name = $wpdb->prefix . 'layerslider';

	// Check if layer slider is active.
	if ( shortcode_exists( 'layerslider' ) ) {
		// Get sliders.
		$sliders = $wpdb->get_results( "SELECT * FROM $table_name WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY date_c ASC" );

		if ( ! empty( $sliders ) ) {
			foreach ( $sliders as $key => $item ) {
				$slides[ $item->id ] = '';
			}
		}

		if ( isset( $slides ) && $slides ) {
			foreach ( $sliders as $slide ) {
				$slides_array[ $slide->id ] = $slide->name . ' (#' . $slide->id . ')';
			}
		}
	}

	return $slides_array;
}

/**
 * Returns array of rev slider slide groups.
 *
 * @since 1.0
 * @return array slide keys array.
 */
function fusion_builder_get_revslider_slides() {
	$revsliders[] = 'Select a slider';
	$revsliders = array( '0' => 'Select a slider' );

	// Check if slider revolution is active.
	if ( shortcode_exists( 'rev_slider' ) ) {
		$slider_object = new RevSliderSlider();
		$sliders_array = $slider_object->getArrSliders();

		if ( $sliders_array ) {
			foreach ( $sliders_array as $slider ) {
				$revsliders[ $slider->getAlias() ] = $slider->getTitle();
			}
		}
	}

	return $revsliders;
}

/**
 * Taxonomies.
 *
 * @since 1.0
 * @param string $taxonomy           The taxonomy.
 * @param bool   $empty_choice       If this is an empty choice or not.
 * @param string $empty_choice_label The label for empty choices.
 * @return array
 */
function fusion_builder_shortcodes_categories( $taxonomy, $empty_choice = false, $empty_choice_label = false ) {

	if ( ! $empty_choice_label ) {
		$empty_choice_label = esc_attr__( 'Default', 'fusion-builder' );
	}
	$post_categories = array();

	if ( $empty_choice ) {
		$post_categories[ $empty_choice_label ] = '';
	}

	$get_categories = get_categories( 'hide_empty=0&taxonomy=' . $taxonomy );

	if ( ! is_wp_error( $get_categories ) ) {

		if ( $get_categories && is_array( $get_categories ) ) {
			foreach ( $get_categories as $cat ) {
				if ( array_key_exists( 'slug', $cat ) &&
					array_key_exists( 'name', $cat )
				) {
					$label = $cat->name . ( ( array_key_exists( 'count', $cat ) ) ? ' (' . $cat->count . ')' : '' );
					$post_categories[ urldecode( $cat->slug ) ] = $label;
				}
			}
		}

		if ( isset( $post_categories ) ) {
			return $post_categories;
		}
	}
}
/**
 * Taxonomy terms.
 *
 * @since 1.2
 * @param string $taxonomy           The taxonomy.
 * @param bool   $empty_choice       If this is an empty choice or not.
 * @param string $empty_choice_label The label for empty choices.
 * @return array
 */
function fusion_builder_shortcodes_tags( $taxonomy, $empty_choice = false, $empty_choice_label = false ) {

	if ( ! $empty_choice_label ) {
		$empty_choice_label = esc_attr__( 'Default', 'fusion-builder' );
	}
	$post_tags = array();

	if ( $empty_choice ) {
		$post_tags[ $empty_choice_label ] = '';
	}

	$get_terms = get_terms( $taxonomy , array( 'hide_empty' => true ) );

	if ( ! is_wp_error( $get_terms ) ) {

		if ( $get_terms && is_array( $get_terms ) ) {
			foreach ( $get_terms as $term ) {

					$label = $term->name . ( ( array_key_exists( 'count', $term ) ) ? ' (' . $term->count . ')' : '' );
					$post_tags[ urldecode( $term->slug ) ] = $label;

			}
		}

		if ( isset( $post_tags ) ) {
			return $post_tags;
		}
	}
}

/**
 * Column combinations.
 *
 * @since  1.0
 * @param  string $module module being triggered from.
 * @return string html output for column selection.
 */
function fusion_builder_column_layouts( $module = '' ) {

	$layouts = apply_filters( 'fusion_builder_column_layouts', array(
		array(
			'layout'   => array( '' ),
			'keywords' => esc_attr__( 'empty blank', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_1' ),
			'keywords' => esc_attr__( 'full one 1', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_2' ),
			'keywords' => esc_attr__( 'two half 2 1/2', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','1_3','1_3' ),
			'keywords' => esc_attr__( 'third thee 3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_4','1_4' ),
			'keywords' => esc_attr__( 'four fourth 4 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_3','1_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','2_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','3_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_4','1_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_4','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_2' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_2','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','4_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '4_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_5','2_5' ),
			'keywords' => esc_attr__( 'three fith two fifth 3/5 2/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_5','3_5' ),
			'keywords' => esc_attr__( 'two fifth three fifth 2/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','3_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','3_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_2' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','2_3','1_6' ),
			'keywords' => esc_attr__( 'one two six sixth 2/3 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','1_5','1_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one six sixth 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '5_6' ),
			'keywords' => esc_attr__( 'five sixth 5/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '4_5' ),
			'keywords' => esc_attr__( 'four fifth 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_4' ),
			'keywords' => esc_attr__( 'three fourth 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_3' ),
			'keywords' => esc_attr__( 'two third 2/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_5' ),
			'keywords' => esc_attr__( 'three fifth 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2' ),
			'keywords' => esc_attr__( 'one half two 1/2', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_5' ),
			'keywords' => esc_attr__( 'two fifth 2/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3' ),
			'keywords' => esc_attr__( 'one third three 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6' ),
			'keywords' => esc_attr__( 'one six sixth 1/6', 'fusion-builder' ),
		),
	) );

	// If being viewed on a section, remove empty from layout options.
	if ( ! isset( $module ) || 'container' !== $module ) {
		unset( $layouts[0] );
	}

	$html = '<ul class="fusion-builder-column-layouts fusion-builder-all-modules">';
	foreach ( $layouts as $layout ) {
		$html .= '<li data-layout="' . implode( ',', $layout['layout'] ) . '">';
		$html .= '<h4 class="fusion_module_title" style="display:none;">' . $layout['keywords'] . '</h4>';

		foreach ( $layout['layout'] as $size ) {
			$html .= '<div class="fusion_builder_layout_column fusion_builder_column_layout_' . $size . '">' . preg_replace( '/[_]+/', '/', $size ) . '</div>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';

	return $html;
}

/**
 * Nested column combinations.
 *
 * @since 1.0
 */
function fusion_builder_inner_column_layouts() {

	$layouts = apply_filters( 'fusion_builder_inner_column_layouts', array(

		array(
			'layout'   => array( '1_1' ),
			'keywords' => esc_attr__( 'full one 1', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_2' ),
			'keywords' => esc_attr__( 'two half 2 1/2', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','1_3','1_3' ),
			'keywords' => esc_attr__( 'third thee 3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_4','1_4' ),
			'keywords' => esc_attr__( 'four fourth 4 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_3','1_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','2_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','3_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_4','1_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_4','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_2' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_2','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','4_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '4_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_5','2_5' ),
			'keywords' => esc_attr__( 'three fith two fifth 3/5 2/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_5','3_5' ),
			'keywords' => esc_attr__( 'two fifth three fifth 2/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','3_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','3_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_2' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','2_3','1_6' ),
			'keywords' => esc_attr__( 'one two six sixth 2/3 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','1_5','1_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one six sixth 1/6', 'fusion-builder' ),
		),
	) );

	$html = '<ul class="fusion-builder-column-layouts fusion-builder-all-modules">';
	foreach ( $layouts as $layout ) {
		$html .= '<li data-layout="' . implode( ',', $layout['layout'] ) . '">';
		$html .= '<h4 class="fusion_module_title" style="display:none;">' . $layout['keywords'] . '</h4>';

		foreach ( $layout['layout'] as $size ) {
			$html .= '<div class="fusion_builder_layout_column fusion_builder_column_layout_' . $size . '">' . preg_replace( '/[_]+/', '/', $size ) . '</div>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';

	return $html;
}

/**
 * Column combinations.
 *
 * @since 1.0
 */
function fusion_builder_generator_column_layouts() {

	$layouts = apply_filters( 'fusion_builder_generators_column_layouts', array(
		array(
			'layout'   => array( '1_1' ),
			'keywords' => esc_attr__( 'full one 1', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_2' ),
			'keywords' => esc_attr__( 'two half 2 1/2', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','1_3','1_3' ),
			'keywords' => esc_attr__( 'third thee 3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_4','1_4' ),
			'keywords' => esc_attr__( 'four fourth 4 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_3','1_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_3','2_3' ),
			'keywords' => esc_attr__( 'two third 2/3 1/3', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','3_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_4','1_4' ),
			'keywords' => esc_attr__( 'one four fourth 1/4 3/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_4','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_4','1_2' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_4','1_2','1_4' ),
			'keywords' => esc_attr__( 'half one four fourth 1/2 1/4', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','4_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '4_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5 4/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '3_5','2_5' ),
			'keywords' => esc_attr__( 'three fith two fifth 3/5 2/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '2_5','3_5' ),
			'keywords' => esc_attr__( 'two fifth three fifth 2/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','3_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','3_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth three 1/5 3/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_2','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_2' ),
			'keywords' => esc_attr__( 'one half six sixth 1/2 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','2_3','1_6' ),
			'keywords' => esc_attr__( 'one two six sixth 2/3 1/6', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_5','1_5','1_5','1_5','1_5' ),
			'keywords' => esc_attr__( 'one five fifth 1/5', 'fusion-builder' ),
		),
		array(
			'layout'   => array( '1_6','1_6','1_6','1_6','1_6','1_6' ),
			'keywords' => esc_attr__( 'one six sixth 1/6', 'fusion-builder' ),
		),
	) );

	$html = '<ul class="fusion-builder-column-layouts">';

	foreach ( $layouts as $layout ) {
		$html .= '<li class="generator-column" data-layout="' . implode( ',', $layout['layout'] ) . '">';
		$html .= '<h4 class="fusion_module_title" style="display:none;">' . $layout['keywords'] . '</h4>';

		foreach ( $layout['layout'] as $size ) {
			$html .= '<div class="fusion_builder_layout_column fusion_builder_column_layout_' . $size . '">' . preg_replace( '/[_]+/', '/', $size ) . '</div>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';

	return $html;
}

/**
 * Save the metadata.
 *
 * @since 1.0
 * @param int    $post_id The poist-ID.
 * @param object $post    The Post object.
 */
function fusion_builder_save_meta( $post_id, $post ) {

	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['fusion_builder_nonce'] ) || ! wp_verify_nonce( $_POST['fusion_builder_nonce'], 'fusion_builder_template' ) ) {

		return $post_id;
	}

	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );

	// Check if the current user has permission to edit the post.
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	// If more than one set to an array.
	$names = array( '_fusion_builder_custom_css' );

	foreach ( $names as $name ) {

		// Get the posted data and sanitize it for use as an HTML class.
		if ( '_fusion_builder_custom_css' === $name ) {
			$new_meta_value = ( isset( $_POST[ $name ] ) ? $_POST[ $name ] : '' );
		} else {
			$new_meta_value = ( isset( $_POST[ $name ] ) ? sanitize_html_class( $_POST[ $name ] ) : '' );
		}

		// Get the meta key.
		$meta_key = $name;

		// Get the meta value of the custom field key.
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// If a new meta value was added and there was no previous value, add it.
		if ( $new_meta_value && '' == $meta_value ) {
			add_post_meta( $post_id, $meta_key, $new_meta_value, true ); } // If the new meta value does not match the old value, update it.
		elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value ); } // If there is no new meta value but an old value exists, delete it.
		elseif ( '' == $new_meta_value && $meta_value ) {
			delete_post_meta( $post_id, $meta_key, $meta_value ); }
	}
}
add_action( 'save_post', 'fusion_builder_save_meta', 10, 2 );

/**
 * Print custom CSS code.
 *
 * @since 1.0
 */
function fusion_builder_custom_css() {
	global $post;

	// Early exit if $post is not defined.
	if ( is_null( $post ) ) {
		return;
	}

	$saved_custom_css = get_post_meta( $post->ID, '_fusion_builder_custom_css', true );

	if ( isset( $saved_custom_css ) && '' != $saved_custom_css ) : ?>
		<style type="text/css"><?php echo stripslashes_deep( $saved_custom_css ); // WPCS: XSS ok. ?></style>
	<?php endif;

}
add_action( 'wp_head', 'fusion_builder_custom_css', 11 );

/**
 * Fusion builder text strings.
 *
 * @since 1.0
 */
function fusion_builder_textdomain_strings() {

	$text_strings = array(

		'custom_css'                                  => esc_attr__( 'Custom CSS', 'fusion-builder' ),
		'builder'                                     => esc_attr__( 'Builder', 'fusion-builder' ),
		'library'                                     => esc_attr__( 'Library', 'fusion-builder' ),
		'add_css_code_here'                           => esc_attr__( 'Add your CSS code here...', 'fusion-builder' ),
		'delete_page_layout'                          => esc_attr__( 'Delete page layout', 'fusion-builder' ),
		'undo'                                        => esc_attr__( 'Undo', 'fusion-builder' ),
		'redo'                                        => esc_attr__( 'Redo', 'fusion-builder' ),
		'save'                                        => esc_attr__( 'Save', 'fusion-builder' ),
		'delete_item'                                 => esc_attr__( 'Delete item', 'fusion-builder' ),
		'clone_item'                                  => esc_attr__( 'Clone item', 'fusion-builder' ),
		'edit_item'                                   => esc_attr__( 'Edit item', 'fusion-builder' ),
		'full_width_section'                          => esc_attr__( 'Container', 'fusion-builder' ),
		'section_settings'                            => esc_attr__( 'Container Settings', 'fusion-builder' ),
		'insert_section'                              => esc_attr__( 'Insert Container', 'fusion-builder' ),
		'clone_section'                               => esc_attr__( 'Clone Container', 'fusion-builder' ),
		'save_section'                                => esc_attr__( 'Save Container', 'fusion-builder' ),
		'delete_section'                              => esc_attr__( 'Delete Container', 'fusion-builder' ),
		'builder_sections'                            => esc_attr__( 'Builder Containers', 'fusion-builder' ),
		'click_to_toggle'                             => esc_attr__( 'Click to toggle', 'fusion-builder' ),
		'save_custom_section'                         => esc_attr__( 'Save Custom Container', 'fusion-builder' ),
		'save_custom_template'                        => esc_attr__( 'Save Custom Template', 'fusion-builder' ),
		'save_custom_section_info'                    => esc_attr__( 'Custom containers will be stored and managed on the Library tab', 'fusion-builder' ),
		'enter_name'                                  => esc_attr__( 'Enter Name...', 'fusion-builder' ),
		'column'                                      => esc_attr__( 'Column', 'fusion-builder' ),
		'columns'                                     => esc_attr__( 'Columns', 'fusion-builder' ),
		'resize_column'                               => esc_attr__( 'Resize column', 'fusion-builder' ),
		'resized_column'                              => esc_attr__( 'Resized Column to', 'fusion-builder' ),
		'column_library'                             => esc_attr__( 'Column settings', 'fusion-builder' ),
		'clone_column'                                => esc_attr__( 'Clone column', 'fusion-builder' ),
		'save_column'                                 => esc_attr__( 'Save column', 'fusion-builder' ),
		'delete_column'                               => esc_attr__( 'Delete column', 'fusion-builder' ),
		'delete_row'                                  => esc_attr__( 'Delete row', 'fusion-builder' ),
		'clone_column'                                => esc_attr__( 'Clone column', 'fusion-builder' ),
		'save_custom_column'                          => esc_attr__( 'Save Custom Column', 'fusion-builder' ),
		'save_custom_column_info'                     => esc_attr__( 'Custom elements will be stored and managed on the Library tab', 'fusion-builder' ),
		'add_element'                                 => esc_attr__( 'Add element', 'fusion-builder' ),
		'element'                                     => esc_attr__( 'Element', 'fusion-builder' ),
		'insert_columns'                              => esc_attr__( 'Insert Columns', 'fusion-builder' ),
		'search_elements'                             => esc_attr__( 'Search elements', 'fusion-builder' ),
		'builder_columns'                             => esc_attr__( 'Builder Columns', 'fusion-builder' ),
		'library_columns'                             => esc_attr__( 'Library Columns', 'fusion-builder' ),
		'library_sections'                            => esc_attr__( 'Library Containers', 'fusion-builder' ),
		'cancel'                                      => esc_attr__( 'Cancel', 'fusion-builder' ),
		'select_element'                              => esc_attr__( 'Select Element', 'fusion-builder' ),
		'builder_elements'                            => esc_attr__( 'Builder Elements', 'fusion-builder' ),
		'library_elements'                            => esc_attr__( 'Library Elements', 'fusion-builder' ),
		'inner_columns'                               => esc_attr__( 'Nested Columns', 'fusion-builder' ),
		'element_settings'                            => esc_attr__( 'Element Settings', 'fusion-builder' ),
		'clone_element'                               => esc_attr__( 'Clone Element', 'fusion-builder' ),
		'save_element'                                => esc_attr__( 'Save Element', 'fusion-builder' ),
		'delete_element'                              => esc_attr__( 'Delete Element', 'fusion-builder' ),
		'save_custom_element'                         => esc_attr__( 'Save Custom Element', 'fusion-builder' ),
		'save_custom_element_info'                    => esc_attr__( 'Custom elements will be stored and managed on the Library tab', 'fusion-builder' ),
		'add_edit_items'                              => esc_attr__( 'Add / Edit Items', 'fusion-builder' ),
		'sortable_items_info'                         => esc_attr__( 'Add or edit new items for this element.  Drag and drop them into the desired order.', 'fusion-builder' ),
		'delete_inner_columns'                        => esc_attr__( 'Delete inner columns', 'fusion-builder' ),
		'clone_inner_columns'                         => esc_attr__( 'Clone inner columns', 'fusion-builder' ),
		'save_inner_columns'                          => esc_attr__( 'Save inner columns', 'fusion-builder' ),
		'delete_inner_columns'                        => esc_attr__( 'Delete inner columns', 'fusion-builder' ),
		'save_nested_columns'                         => esc_attr__( 'Save Nested Columns', 'fusion-builder' ),
		'select_options_or_leave_blank_for_all'       => esc_attr__( 'Select Options or Leave Blank for All', 'fusion-builder' ),
		'select_categories_or_leave_blank_for_all'    => esc_attr__( 'Select Categories or Leave Blank for All', 'fusion-builder' ),
		'select_categories_or_leave_blank_for_none'   => esc_attr__( 'Select Categories or Leave Blank for None', 'fusion-builder' ),
		'please_enter_element_name'                   => esc_attr__( 'Please enter element name', 'fusion-builder' ),
		'are_you_sure_you_want_to_delete_this_layout' => esc_attr__( 'Are you sure you want to delete this layout ?', 'fusion-builder' ),
		'are_you_sure_you_want_to_delete_this'        => esc_attr__( 'Are you sure you want to delete this ?', 'fusion-builder' ),
		'please_enter_template_name'                  => esc_attr__( 'Please enter template name', 'fusion-builder' ),
		'save_page_layout'                            => esc_attr__( 'Save page layout', 'fusion-builder' ),
		'upload'                                      => esc_attr__( 'Upload', 'fusion-builder' ),
		'upload_image'                                => esc_attr__( 'Upload Image', 'fusion-builder' ),
		'attach_images'                               => esc_attr__( 'Attach Images to Gallery', 'fusion-builder' ),
		'insert'                                      => esc_attr__( 'Insert', 'fusion-builder' ),
		'pre_built_page'                              => esc_attr__( 'Pre-Built Page', 'fusion-builder' ),
		'to_get_started'                              => esc_attr__( 'To get started, add a Container, or add a pre-built page.', 'fusion-builder' ),
		'to_get_started_sub'                          => esc_attr__( 'The building process always starts with a container, then columns, then elements.', 'fusion-builder' ),
		'watch_the_video'                             => esc_attr__( 'Watch The Video!', 'fusion-builder' ),
		'edit_settings'                               => esc_attr__( 'Edit Settings', 'fusion-builder' ),
		'backward_history'                            => esc_attr__( 'Backward History', 'fusion-builder' ),
		'duplicate_content'                           => esc_attr__( 'Duplicate Content', 'fusion-builder' ),
		'forward_history'                             => esc_attr__( 'Forward History', 'fusion-builder' ),
		'save_custom_content'                         => esc_attr__( 'Save Custom Content', 'fusion-builder' ),
		'delete_content'                              => esc_attr__( 'Delete Content', 'fusion-builder' ),
		'add_content'                                 => esc_attr__( 'Add Content', 'fusion-builder' ),
		'additional_docs'                             => esc_attr__( 'Click the ? icon to view additional documentation', 'fusion-builder' ),
		'getting_started_video'                       => esc_attr__( 'Getting Started Video', 'fusion-builder' ),
		'icon_control_description'                    => esc_attr__( 'Icon Control Descriptions:', 'fusion-builder' ),
		'history'                                     => esc_attr__( 'History', 'fusion-builder' ),
		'collapse_sections'                           => esc_attr__( 'Collapse Sections', 'fusion-builder' ),
		'history_states'                              => esc_attr__( 'History States', 'fusion-builder' ),
		'empty'                                       => esc_attr__( 'Start', 'fusion-builder' ),
		'moved_column'                                => esc_attr__( 'Moved Column', 'fusion-builder' ),
		'added_custom_element'                        => esc_attr__( 'Added Custom Element: ', 'fusion-builder' ),
		'added_custom_column'                         => esc_attr__( 'Added Custom Column: ', 'fusion-builder' ),
		'added_columns'                               => esc_attr__( 'Added Columns', 'fusion-builder' ),
		'added_custom_section'                        => esc_attr__( 'Added Custom Container: ', 'fusion-builder' ),
		'deleted'                                     => esc_attr__( 'Deleted', 'fusion-builder' ),
		'cloned'                                      => esc_attr__( 'Cloned', 'fusion-builder' ),
		'moved'                                       => esc_attr__( 'Moved', 'fusion-builder' ),
		'edited'                                      => esc_attr__( 'Edited', 'fusion-builder' ),
		'added_nested_columns'                        => esc_attr__( 'Added Nested Columns', 'fusion-builder' ),
		'deleted_nested_columns'                      => esc_attr__( 'Deleted Nested Columns', 'fusion-builder' ),
		'moved_nested_column'                         => esc_attr__( 'Moved Nested Column', 'fusion-builder' ),
		'head_title'                                  => esc_attr__( 'Head Title', 'fusion-builder' ),
		'currency'                                    => esc_attr__( 'Currency', 'fusion-builder' ),
		'price'                                       => esc_attr__( 'Price', 'fusion-builder' ),
		'period'                                      => esc_attr__( 'Period', 'fusion-builder' ),
		'enter_text'                                  => esc_attr__( 'Enter Text', 'fusion-builder' ),
		'added'                                       => esc_attr__( 'Added', 'fusion-builder' ),
		'added_section'                               => esc_attr__( 'Added Container', 'fusion-builder' ),
		'cloned_nested_columns'                       => esc_attr__( 'Cloned Nested Columns', 'fusion-builder' ),
		'content_imported'                            => esc_attr__( 'Content Imported', 'fusion-builder' ),
		'table_intro'                                 => esc_attr__( 'Visually create your table below, add or subtract rows and columns', 'fusion-builder' ),
		'add_table_column'                            => esc_attr__( 'Add Column', 'fusion-builder' ),
		'add_table_row'                               => esc_attr__( 'Add Row', 'fusion-builder' ),
		'column_title'                                => esc_attr__( 'Column', 'fusion-builder' ),
		'standout_design'                             => esc_attr__( 'Standout', 'fusion-builder' ),
		'add_button'                                  => esc_attr__( 'Add Button', 'fusion-builder' ),
		'yes'                                         => esc_attr__( 'Yes', 'fusion-builder' ),
		'no'                                          => esc_attr__( 'No', 'fusion-builder' ),
		'table_options'                               => esc_attr__( 'Table Options', 'fusion-builder' ),
		'table'                                       => esc_attr__( 'Table', 'fusion-builder' ),
		'toggle_all_sections'                         => esc_attr__( 'Toggle All Containers', 'fusion-builder' ),
		'cloned_section'                              => esc_attr__( 'Cloned Container', 'fusion-builder' ),
		'deleted_section'                             => esc_attr__( 'Deleted Container', 'fusion-builder' ),
		'select_image'                                => esc_attr__( 'Select Image', 'fusion-builder' ),
		'select_images'                               => esc_attr__( 'Select Images', 'fusion-builder' ),
		'select_video'                                => esc_attr__( 'Select Video', 'fusion-builder' ),
		'empty_section'                               => esc_attr__( 'To Add Elements, You Must First Add a Column', 'fusion-builder' ),
		'empty_section_with_bg'                       => esc_attr__( 'This is an empty container with a background image. To add elements, you must first add a column', 'fusion-builder' ),
		'to_add_images'                               => esc_attr__( 'To add images to this post or page for attachments layout, navigate to "Upload Files" tab in media manager and upload new images.', 'fusion-builder' ),
		'importing_single_page'                       => esc_attr__( 'WARNING:
Importing a single demo page will remove all other page content, fusion page options and page template. Fusion Theme Options and demo images are not imported. Click OK to continue or cancel to stop.', 'fusion-builder' ),
		'content_error_title'                         => esc_attr__( 'Content Error', 'fusion-builder' ),
		'content_error_description'                   => sprintf( __( 'Your page content could not be converted. Most likely it was created with an earlier (pre 5.0) version of Avada. To update old content to Avada 5.0 or higher, you must go through <a href="%s" target="_blank">conversion</a>.', 'fusion-builder' ), 'https://theme-fusion.com/fb-doc/technical/converting-fusion-builder-pages/' ),
		'moved_container'                             => esc_attr__( 'Moved Container', 'fusion-builder' ),
		'currency_before'                             => esc_attr__( 'Before', 'fusion-builder' ),
		'currency_after'                              => esc_attr__( 'After', 'fusion-builder' ),
		'delete_nextpage'                             => esc_attr__( 'Delete Next Page Divider', 'fusion-builder' ),
		'nextpage'                                    => esc_attr__( 'Next Page', 'fusion-builder' ),
		'library_misc'                                => esc_attr__( 'Special', 'fusion-builder' ),
		'special_title'                               => esc_attr__( 'Special Items', 'fusion-builder' ),
		'special_description'                         => esc_attr__( 'The nextpage item allows you to break your page into several pages. Simply insert it onto the page, and automatic pagination will show on the frontend.', 'fusion-builder' ),
		'select_link'                                 => esc_attr__( 'Select Link', 'fusion-builder' ),
	);

	return $text_strings;
}

/**
 * Add shortcode generator toggle button to text editor.
 *
 * @since 1.0
 */
function fusion_builder_add_quicktags_button() {
	?>
	<?php if ( get_current_screen()->base == 'post' ) : ?>
		<script type="text/javascript" charset="utf-8">
			if ( typeof( QTags ) == 'function' ) {
				QTags.addButton( 'fusion_shortcodes_text_mode', ' ','', '', 'f' );
			}
		</script>
	<?php endif;
}
add_action( 'admin_print_footer_scripts', 'fusion_builder_add_quicktags_button' );

/**
 * Build Social Network Icons.
 *
 * @since 1.0
 * @param string|array $social_networks The social networks array.
 * @param string       $filter          The filter that will be used to build the attributes.
 * @param array        $defaults        Defaults array.
 * @param int          $i               Increment counter.
 * @return string
 */
function fusion_builder_build_social_links( $social_networks = '', $filter, $defaults, $i = 0 ) {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$use_brand_colors = false;
	$icons = '';
	$shortcode_defaults = array();

	if ( '' != $social_networks && is_array( $social_networks ) ) {

		// Add compatibility for different key names in shortcodes.
		foreach ( $defaults as $key => $value ) {
			// @codingStandardsIgnoreStart
			$key = ( 'social_icon_boxed'        === $key ) ? 'icons_boxed' : $key;
			$key = ( 'social_icon_colors'       === $key ) ? 'icon_colors' : $key;
			$key = ( 'social_icon_boxed_colors' === $key ) ? 'box_colors'  : $key;
			$key = ( 'social_icon_color_type'   === $key ) ? 'color_type'  : $key;
			// @codingStandardsIgnoreEnd

			$shortcode_defaults[ $key ] = $value;
		}

		extract( $shortcode_defaults );

		// Check for icon color type.
		if ( 'brand' == $color_type || ( '' == $color_type && 'brand' == $fusion_settings->get( 'social_links_color_type' ) ) ) {
			$use_brand_colors = true;

			$box_colors = Fusion_Data::fusion_social_icons( true, true );
			// Backwards compatibility for old social network names.
			$box_colors['googleplus'] = array( 'label' => 'Google+', 'color' => '#dc4e41' );
			$box_colors['mail']       = array( 'label' => esc_html__( 'Email Address', 'fusion-builder' ), 'color' => '#000000' );

		} else {

			// Custom social icon colors.
			$icon_colors = explode( '|', $icon_colors );
			$box_colors  = explode( '|', $box_colors );

			$num_of_icon_colors = count( $icon_colors );
			$num_of_box_colors  = count( $box_colors );

			$social_networks_count = count( $social_networks );

			for ( $k = 0; $k < $social_networks_count; $k++ ) {
				if ( 1 == $num_of_icon_colors ) {
					$icon_colors[ $k ] = $icon_colors[0];
				}
				if ( 1 == $num_of_box_colors ) {
					$box_colors[ $k ] = $box_colors[0];
				}
			}
		}

		// Process social networks.
		foreach ( $social_networks as $key => $value ) {

			foreach ( $value as $network => $link ) {

				if ( 'custom' == $network && is_array( $link ) ) {

					foreach ( $link as $custom_key => $url ) {

						if ( 'yes' == $icons_boxed ) {

							if ( true === $use_brand_colors ) {
								$custom_icon_box_color = ( $box_colors[ $network ]['color'] ) ? $box_colors[ $network ]['color'] : '';
							} else {
								$custom_icon_box_color = $i < count( $box_colors ) ? $box_colors[ $i ] : '';
							}
						} else {
							$custom_icon_box_color = '';
						}

						$social_media_icons = $fusion_settings->get( 'social_media_icons' );
						if ( ! is_array( $social_media_icons ) ) {
							$social_media_icons = array();
						}
						if ( ! isset( $social_media_icons['custom_title'] ) ) {
							$social_media_icons['custom_title'] = array();
						}
						if ( ! isset( $social_media_icons['custom_source'] ) ) {
							$social_media_icons['custom_source'] = array();
						}
						if ( ! isset( $social_media_icons['custom_title'][ $custom_key ] ) ) {
							$social_media_icons['custom_title'][ $custom_key ] = '';
						}
						if ( ! isset( $social_media_icons['custom_source'][ $custom_key ] ) ) {
							$social_media_icons['custom_source'][ $custom_key ] = '';
						}

						$icon_options = array(
							'social_network' => $social_media_icons['custom_title'][ $custom_key ],
							'social_link'    => $url,
							'icon_color'     => $i < count( $icon_colors ) ? $icon_colors[ $i ] : '',
							'box_color'      => $custom_icon_box_color,
						);

						$icons .= '<a ' . FusionBuilder::attributes( $filter, $icon_options ) . '>';
						$icons .= '<img';

						if ( isset( $social_media_icons['custom_source'][ $custom_key ]['url'] ) ) {
							$icons .= ' src="' . $social_media_icons['custom_source'][ $custom_key ]['url'] . '"';
						}
						if ( isset( $social_media_icons['custom_title'][ $custom_key ] ) && '' != $social_media_icons['custom_title'][ $custom_key ] ) {
							$icons .= ' alt="' . $social_media_icons['custom_title'][ $custom_key ] . '"';
						}
						if ( isset( $social_media_icons['custom_source'][ $custom_key ]['width'] ) && $social_media_icons['custom_source'][ $custom_key ]['width'] ) {
							$width = intval( $social_media_icons['custom_source'][ $custom_key ]['width'] );
							$icons .= ' width="' . $width . '"';
						}
						if ( isset( $social_media_icons['custom_source'][ $custom_key ]['height'] ) && $social_media_icons['custom_source'][ $custom_key ]['height'] ) {
							$height = intval( $social_media_icons['custom_source'][ $custom_key ]['height'] );
							$icons .= ' height="' . $height . '"';
						}
						$icons .= ' /></a>';
					}
				} else {

					if ( true == $use_brand_colors ) {
						$icon_options = array(
							'social_network' => $network,
							'social_link'    => $link,
							'icon_color'     => ( 'yes' == $icons_boxed ) ? '#ffffff' : $box_colors[ $network ]['color'],
							'box_color'      => ( 'yes' == $icons_boxed ) ? $box_colors[ $network ]['color'] : '',
						);

					} else {
						$icon_options = array(
						'social_network' => $network,
						'social_link'    => $link,
						'icon_color'     => $i < count( $icon_colors ) ? $icon_colors[ $i ] : '',
						'box_color'      => $i < count( $box_colors ) ? $box_colors[ $i ] : '',
						);
					}
					$icons .= '<a ' . FusionBuilder::attributes( $filter, $icon_options ) . '></a>';
				}
				$i++;
			}
		}
	}
	return $icons;
}

/**
 * Get Social Networks.
 *
 * @since 1.0
 * @param array $defaults The default values.
 * @return array
 */
function fusion_builder_get_social_networks( $defaults ) {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$social_links_array = array();

	if ( $defaults['facebook'] ) {
		$social_links_array['facebook'] = $defaults['facebook'];
	}
	if ( $defaults['twitter'] ) {
		$social_links_array['twitter'] = $defaults['twitter'];
	}
	if ( $defaults['instagram'] ) {
		$social_links_array['instagram'] = $defaults['instagram'];
	}
	if ( $defaults['linkedin'] ) {
		$social_links_array['linkedin'] = $defaults['linkedin'];
	}
	if ( $defaults['dribbble'] ) {
		$social_links_array['dribbble'] = $defaults['dribbble'];
	}
	if ( $defaults['rss'] ) {
		$social_links_array['rss'] = $defaults['rss'];
	}
	if ( $defaults['youtube'] ) {
		$social_links_array['youtube'] = $defaults['youtube'];
	}
	if ( $defaults['pinterest'] ) {
		$social_links_array['pinterest'] = $defaults['pinterest'];
	}
	if ( $defaults['flickr'] ) {
		$social_links_array['flickr'] = $defaults['flickr'];
	}
	if ( $defaults['vimeo'] ) {
		$social_links_array['vimeo'] = $defaults['vimeo'];
	}
	if ( $defaults['tumblr'] ) {
		$social_links_array['tumblr'] = $defaults['tumblr'];
	}
	if ( $defaults['googleplus'] ) {
		$social_links_array['googleplus'] = $defaults['googleplus'];
	}
	if ( $defaults['google'] ) {
		$social_links_array['googleplus'] = $defaults['google'];
	}
	if ( $defaults['digg'] ) {
		$social_links_array['digg'] = $defaults['digg'];
	}
	if ( $defaults['blogger'] ) {
		$social_links_array['blogger'] = $defaults['blogger'];
	}
	if ( $defaults['skype'] ) {
		$social_links_array['skype'] = $defaults['skype'];
	}
	if ( $defaults['myspace'] ) {
		$social_links_array['myspace'] = $defaults['myspace'];
	}
	if ( $defaults['deviantart'] ) {
		$social_links_array['deviantart'] = $defaults['deviantart'];
	}
	if ( $defaults['yahoo'] ) {
		$social_links_array['yahoo'] = $defaults['yahoo'];
	}
	if ( $defaults['reddit'] ) {
		$social_links_array['reddit'] = $defaults['reddit'];
	}
	if ( $defaults['forrst'] ) {
		$social_links_array['forrst'] = $defaults['forrst'];
	}
	if ( $defaults['paypal'] ) {
		$social_links_array['paypal'] = $defaults['paypal'];
	}
	if ( $defaults['dropbox'] ) {
		$social_links_array['dropbox'] = $defaults['dropbox'];
	}
	if ( $defaults['soundcloud'] ) {
		$social_links_array['soundcloud'] = $defaults['soundcloud'];
	}
	if ( $defaults['vk'] ) {
		$social_links_array['vk'] = $defaults['vk'];
	}
	if ( $defaults['xing'] ) {
		$social_links_array['xing'] = $defaults['xing'];
	}
	if ( $defaults['yelp'] ) {
		$social_links_array['yelp'] = $defaults['yelp'];
	}
	if ( $defaults['spotify'] ) {
		$social_links_array['spotify'] = $defaults['spotify'];
	}
	if ( $defaults['email'] ) {
		$social_links_array['mail'] = $defaults['email'];
	}
	if ( $defaults['show_custom'] && 'yes' === $defaults['show_custom'] ) {
		$social_links_array['custom'] = array();
		if ( is_array( $fusion_settings->get( 'social_media_icons', 'icon' ) ) ) {
			foreach ( $fusion_settings->get( 'social_media_icons', 'icon' ) as $key => $icon ) {
				$social_media_icons_url = $fusion_settings->get( 'social_media_icons', 'url' );
				if ( 'custom' == $icon && is_array( $social_media_icons_url ) && isset( $social_media_icons_url[ $key ] ) && ! empty( $social_media_icons_url[ $key ] ) ) {
					// Check if there is a default set for this, if so use that rather than TO link.
					if ( isset( $defaults[ 'custom_' . $key ] ) && ! empty( $defaults[ 'custom_' . $key ] ) ) {
						$social_links_array['custom'][ $key ] = $defaults[ 'custom_' . $key ];
					} else {
						$social_links_array['custom'][ $key ] = $social_media_icons_url[ $key ];
					}
				}
			}
		}
	}

	return $social_links_array;
}

/**
 * Sort Social Network Icons.
 *
 * @since 1.0
 * @param array $social_networks_original Original array of social networks.
 * @return array
 */
function fusion_builder_sort_social_networks( $social_networks_original ) {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$social_networks = array();
	$icon_order      = '';

	// Get social networks order from theme options.
	$social_media_icons = $fusion_settings->get( 'social_media_icons' );
	if ( isset( $social_media_icons['icon'] ) && is_array( $social_media_icons['icon'] ) ) {
		$icon_order = implode( '|', $social_media_icons['icon'] );
	}

	if ( ! is_array( $icon_order ) ) {
		$icon_order = explode( '|', $icon_order );
	}

	if ( is_array( $icon_order ) && ! empty( $icon_order ) ) {
		// First put the icons that exist in the theme options,
		// and order them using tha same order as in theme options.
		foreach ( $icon_order as $key => $value ) {

			// Backwards compatibility for old social network names.
			// @codingStandardsIgnoreStart
			$value = ( 'google' === $value ) ? 'googleplus' : $value;
			$value = ( 'gplus'  === $value ) ? 'googleplus' : $value;
			$value = ( 'email'  === $value ) ? 'mail'       : $value;
			// @codingStandardsIgnoreEnd

			// Check if social network from TO exists in shortcode.
			if ( ! isset( $social_networks_original[ $value ] ) ) {
				continue;
			}

			if ( 'custom' === $value ) {
				$social_networks[] = array( $value => array( $key => $social_networks_original[ $value ][ $key ] ) );
			} else {
				$social_networks[] = array( $value => $social_networks_original[ $value ] );
				unset( $social_networks_original[ $value ] );
			}
		}

		// Put any remaining icons after the ones from the theme options.
		foreach ( $social_networks_original as $name => $url ) {
			if ( 'custom' !== $name ) {
				$social_networks[] = array( $name => $url );
			}
		}
	}

	return $social_networks;
}

/**
 * Get Custom Social Networks.
 *
 * @since 1.0
 * @return array
 */
function fusion_builder_get_custom_social_networks() {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$social_links_array = array();
	$social_media_icons = $fusion_settings->get( 'social_media_icons' );
	if ( is_array( $social_media_icons ) && isset( $social_media_icons['icon'] ) && is_array( $social_media_icons['icon'] ) ) {
		foreach ( $social_media_icons['icon'] as $key => $icon ) {
			if ( 'custom' == $icon && isset( $social_media_icons['url'][ $key ] ) && ! empty( $social_media_icons['url'][ $key ] ) ) {
				$social_links_array[ $key ] = $social_media_icons['url'][ $key ];
			}
		}
	}
	return $social_links_array;
}

/**
 * Returns an array of visibility options.
 *
 * @since 1.0
 * @param string $type whether to return full array or values only.
 * @return array
 */
function fusion_builder_visibility_options( $type ) {

	$visibility_options = array(
		'small-visibility'  => esc_attr__( 'Small Screen', 'fusion-builder' ),
		'medium-visibility' => esc_attr__( 'Medium Screen', 'fusion-builder' ),
		'large-visibility'  => esc_attr__( 'Large Screen', 'fusion-builder' ),
		);
	if ( 'values' == $type ) {
		$visibility_options = array_keys( $visibility_options );
	}
	return $visibility_options;
}

/**
 * Returns an array of default visibility options.
 *
 * @since 1.0
 * @param  string $type either array or string to return.
 * @return string|array
 */
function fusion_builder_default_visibility( $type ) {

	$default_visibility = fusion_builder_visibility_options( 'values' );
	if ( 'string' == $type ) {
		$default_visibility = implode( ', ', $default_visibility );
	}
	return $default_visibility;
}

/**
 * Reverses the visibility selection and adds to attribute array.
 *
 * @since 1.0
 * @param string|array $selection Devices selected to be shown on.
 * @param array        $attr      Current attributes to add to.
 * @return array
 */
function fusion_builder_visibility_atts( $selection, $attr ) {
	$visibility_values = fusion_builder_visibility_options( 'values' );

	// If empty, show all.
	if ( empty( $selection ) ) {
		$selection = $visibility_values;
	}

	// If no is used, change that to all options selected, as fallback.
	if ( 'no' === $selection ) {
		$selection = $visibility_values;
	}

	// If yes is used, use all selections with mobile visibility removed.
	if ( 'yes' === $selection ) {
		if ( false !== ( $key = array_search( 'small-visibility', $visibility_values ) ) ) {
			unset( $visibility_values[ $key ] );
			$selection = $visibility_values;
		}
	}

	// Make sure the selection is an array.
	if ( ! is_array( $selection ) ) {
		$selection = explode( ',', str_replace( ' ', '', $selection ) );
	}

	$visibility_options = fusion_builder_visibility_options( 'values' );
	foreach ( $visibility_options as $visibility_option ) {
		if ( ! in_array( $visibility_option, $selection ) ) {
			if ( is_array( $attr ) ) {
				$attr['class'] .= ( ( $attr['class'] ) ? ' fusion-no-' . $visibility_option : 'fusion-no-' . $visibility_option );
			} else {
				$attr .= ( ( $attr ) ? ' fusion-no-' . $visibility_option : 'fusion-no-' . $visibility_option );
			}
		}
	}
	return $attr;
}
/**
 * Adds fallbacks for section attributes.
 *
 * @since 1.0
 * @param array $args Array of attributes.
 * @return array
 */
function fusion_section_deprecated_args( $args ) {

	$param_mapping = array(
		'backgroundposition'    => 'background_position',
		'backgroundattachment'  => 'background_parallax',
		'background_attachment' => 'background_parallax',
		'bordersize'            => 'border_size',
		'bordercolor'           => 'border_color',
		'borderstyle'           => 'border_style',
		'paddingtop'            => 'padding_top',
		'paddingbottom'         => 'padding_bottom',
		'paddingleft'           => 'padding_left',
		'paddingright'          => 'padding_right',
		'backgroundcolor'       => 'background_color',
		'backgroundimage'       => 'background_image',
		'backgroundrepeat'      => 'background_repeat',
		'paddingBottom'         => 'padding_bottom',
		'paddingTop'            => 'padding_top',
	);

	if ( ! is_array( $args ) ) {
		$args = array();
	}

	if ( ( array_key_exists( 'backgroundattachment', $args ) && 'scroll' == $args['backgroundattachment'] ) ||
		 ( array_key_exists( 'background_attachment', $args ) && 'scroll' == $args['background_attachment'] )
	) {
		$args['backgroundattachment'] = $args['background_attachment'] = 'none';
	}

	foreach ( $param_mapping as $old => $new ) {
		if ( ! isset( $args[ $new ] ) && isset( $args[ $old ] ) ) {
			$args[ $new ] = $args[ $old ];
			unset( $args[ $old ] );
		}
	}

	return $args;
}

/**
 * Creates placeholders for empty post type shortcodes.
 *
 * @since 1.0
 * @param string $post_type name of post type.
 * @param string $label label for post type.
 * @return string
 */
function fusion_builder_placeholder( $post_type, $label ) {
	if ( current_user_can( 'publish_posts' ) ) {
		$string = sprintf( esc_html__( 'Please add %s for them to display here.', 'fusion-builder' ), $label );
		$link = admin_url( 'post-new.php?post_type=' . $post_type );
		$html = '<a href="' . $link . '" class="fusion-builder-placeholder">' . $string . '</a>';
		return $html;
	}
}

/**
 * Sorts modules.
 *
 * @since 1.0.0
 * @param array $a Element settings.
 * @param array $b Element settings.
 */
function fusion_element_sort( $a, $b ) {
	return strnatcmp( $a['name'], $b['name'] );
}

/**
 * Returns a single side dimension.
 *
 * @since 1.0
 * @param string $dimensions current dimensions combined.
 * @param string $direction which side dimension to be retrieved.
 * @return string
 */
function fusion_builder_single_dimension( $dimensions, $direction ) {
	$dimensions = explode( ' ', $dimensions );
	if ( 4 === count( $dimensions ) ) {
		list( $top, $right, $bottom, $left ) = $dimensions;
	} elseif ( 3 === count( $dimensions ) ) {
		$top = $dimensions[0];
		$right = $left = $dimensions[1];
		$bottom = $dimensions[2];
	} elseif ( 2 === count( $dimensions ) ) {
		$top = $bottom = $dimensions[0];
		$right = $left = $dimensions[1];
	} else {
		$top = $right = $bottom = $left = $dimensions[0];
	}
	return ${ $direction };
}

/**
 * Adds admin notice when visual editor is disabled
 *
 * @since 1.0
 */
function fusion_builder_add_notice_of_disabled_rich_editor() {
	global $current_user;
	$user_id = $current_user->ID;

	$current_uri = $_SERVER['REQUEST_URI'];
	$uri_parts = parse_url( $current_uri );
	if ( ! isset( $uri_parts['query'] ) ) {
		$uri_parts['query'] = '';
	}
	$path = explode( '/', $uri_parts['path'] );
	$last = end( $path );
	$full_link = admin_url() . $last . '?' . $uri_parts['query'];

	// Check that the user hasn't already clicked to ignore the message.
	// @codingStandardsIgnoreLine
	if ( ! get_user_meta( $user_id, 'fusion_richedit_nag_ignore' ) ) {
		printf( '<div id="disabled-rich-editor" class="updated"><p>%s <a href="%s">%s</a><span class="dismiss" style="float:right;"><a href="%s&fusion_richedit_nag_ignore=0">%s</a></span></div>', esc_attr__( 'Note: The visual editor, which is necesarry for Fusion Builder to work, has been disabled in your profile settings.', 'fusion-builder' ), esc_url_raw( admin_url( 'profile.php' ) ), esc_attr__( 'Go to Profile', 'fusion-builder' ), esc_url_raw( $full_link ), esc_attr__( 'Hide Notice', 'fusion-builder' ) );
	}
}

/**
 * Auto activate Fusion Builder element. To be used by addon plugins.
 *
 * @since 1.0.4
 * @param string $shortcode Shortcode tag.
 */
function fusion_builder_auto_activate_element( $shortcode ) {
	$fusion_builder_settings = get_option( 'fusion_builder_settings' );

	if ( $fusion_builder_settings && isset( $fusion_builder_settings['fusion_elements'] ) && is_array( $fusion_builder_settings['fusion_elements'] ) ) {
		$fusion_builder_settings['fusion_elements'][] = $shortcode;

		update_option( 'fusion_builder_settings', $fusion_builder_settings );
	}
}

add_action( 'fusion_placeholder_image', 'fusion_render_placeholder_image', 10 );

if ( ! function_exists( 'fusion_render_placeholder_image' ) ) {
	/**
	 * Action to output a placeholder image.
	 *
	 * @param  string $featured_image_size     Size of the featured image that should be emulated.
	 *
	 * @return void
	 */
	function fusion_render_placeholder_image( $featured_image_size = 'full' ) {
		global $_wp_additional_image_sizes;

		if ( in_array( $featured_image_size, array( 'full', 'fixed' ) ) ) {
			$height = apply_filters( 'fusion_set_placeholder_image_height', '150' );
			$width  = '1500px';
		} else {
			@$height = $_wp_additional_image_sizes[ $featured_image_size ]['height'];
			@$width  = $_wp_additional_image_sizes[ $featured_image_size ]['width'] . 'px';
		}
		?>
		 <div class="fusion-placeholder-image" data-origheight="<?php echo esc_attr( $height ); ?>" data-origwidth="<?php echo esc_attr( $width ); ?>" style="height:<?php echo esc_attr( $height ); ?>px;width:<?php echo esc_attr( $width ); ?>;"></div>
		<?php
	}
}

/**
 * Returns equivalent global information for FB param.
 *
 * @since 1.0.5
 * @param string $shortcode Name of shortcode.
 * @param string $param     Param name in shortcode.
 * @return array|bool       Element option data.
 */
function fusion_builder_map_descriptions( $shortcode, $param ) {
	$shortcode_option_map = apply_filters( 'fusion_builder_map_descriptions', array() );

	// Alert.
	$shortcode_option_map['box_shadow']['fusion_alert'] = array( 'theme-option' => 'alert_box_shadow', 'type' => 'select' );
	$shortcode_option_map['border_size']['fusion_alert'] = array( 'theme-option' => 'alert_border_size', 'type' => 'range' );
	// Button.
	$shortcode_option_map['size']['fusion_button'] = array( 'theme-option' => 'button_size', 'type' => 'select' );
	$shortcode_option_map['stretch']['fusion_button'] = array( 'theme-option' => 'button_span', 'type' => 'select' );
	$shortcode_option_map['type']['fusion_button'] = array( 'theme-option' => 'button_type', 'type' => 'select' );
	$shortcode_option_map['shape']['fusion_button'] = array( 'theme-option' => 'button_shape', 'type' => 'select' );
	$shortcode_option_map['button_gradient_top_color']['fusion_button'] = array( 'theme-option' => 'button_gradient_top_color', 'reset' => true );
	$shortcode_option_map['button_gradient_bottom_color']['fusion_button'] = array( 'theme-option' => 'button_gradient_bottom_color', 'reset' => true );
	$shortcode_option_map['button_gradient_top_color_hover']['fusion_button'] = array( 'theme-option' => 'button_gradient_top_color_hover', 'reset' => true );
	$shortcode_option_map['button_gradient_bottom_color_hover']['fusion_button'] = array( 'theme-option' => 'button_gradient_bottom_color_hover', 'reset' => true );
	$shortcode_option_map['accent_color']['fusion_button'] = array( 'theme-option' => 'button_accent_color', 'reset' => true );
	$shortcode_option_map['accent_hover_color']['fusion_button'] = array( 'theme-option' => 'button_accent_hover_color', 'reset' => true );
	$shortcode_option_map['bevel_color']['fusion_button'] = array( 'theme-option' => 'button_bevel_color', 'reset' => true );
	$shortcode_option_map['border_width']['fusion_button'] = array( 'theme-option' => 'button_border_width', 'type' => 'range' );

	$shortcode_option_map['button_fullwidth']['fusion_login'] = array( 'theme-option' => 'button_span', 'type' => 'yesno' );
	$shortcode_option_map['button_fullwidth']['fusion_register'] = array( 'theme-option' => 'button_span', 'type' => 'yesno' );
	$shortcode_option_map['button_fullwidth']['fusion_lost_password'] = array( 'theme-option' => 'button_span', 'type' => 'yesno' );

	$shortcode_option_map['button_size']['fusion_tagline_box'] = array( 'theme-option' => 'button_size', 'type' => 'select' );
	$shortcode_option_map['button_type']['fusion_tagline_box'] = array( 'theme-option' => 'button_type', 'type' => 'select' );
	$shortcode_option_map['button_shape']['fusion_tagline_box'] = array( 'theme-option' => 'button_shape', 'type' => 'select' );

	// Checklist.
	$shortcode_option_map['iconcolor']['fusion_checklist'] = array( 'theme-option' => 'checklist_icons_color', 'reset' => true );
	$shortcode_option_map['circle']['fusion_checklist'] = array( 'theme-option' => 'checklist_circle', 'type' => 'yesno' );
	$shortcode_option_map['circlecolor']['fusion_checklist'] = array( 'theme-option' => 'checklist_circle_color', 'reset' => true );

	// Columns.
	$shortcode_option_map['dimension_margin']['fusion_builder_column'] = array( 'theme-option' => 'col_margin', 'subset' => array( 'top', 'bottom' ) );
	$shortcode_option_map['dimension_margin']['fusion_builder_column_inner'] = array( 'theme-option' => 'col_margin', 'subset' => array( 'top', 'bottom' ) );

	// Container.
	$shortcode_option_map['background_color']['fusion_builder_container'] = array( 'theme-option' => 'full_width_bg_color', 'reset' => true );
	$shortcode_option_map['border_size']['fusion_builder_container'] = array( 'theme-option' => 'full_width_border_size', 'type' => 'range' );
	$shortcode_option_map['border_color']['fusion_builder_container'] = array( 'theme-option' => 'full_width_border_color', 'reset' => true );

	// Content Box.
	$shortcode_option_map['backgroundcolor']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_bg_color', 'reset' => true );
	$shortcode_option_map['title_size']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_title_size' );
	$shortcode_option_map['title_color']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_title_color', 'reset' => true );
	$shortcode_option_map['body_color']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_body_color', 'reset' => true );
	$shortcode_option_map['icon_size']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_size', 'reset' => true );
	$shortcode_option_map['iconcolor']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_color', 'reset' => true );
	$shortcode_option_map['icon_circle']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_circle', 'type' => 'select' );
	$shortcode_option_map['icon_circle_radius']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_circle_radius' );
	$shortcode_option_map['circlecolor']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_bg_color', 'reset' => true );
	$shortcode_option_map['circlebordercolor']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_bg_inner_border_color', 'reset' => true );
	$shortcode_option_map['outercirclebordercolor']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_bg_outer_border_color', 'reset' => true );
	$shortcode_option_map['circlebordersize']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_bg_inner_border_size', 'type' => 'range' );
	$shortcode_option_map['outercirclebordersize']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_bg_outer_border_size' , 'type' => 'range' );
	$shortcode_option_map['icon_hover_type']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_icon_hover_type', 'type' => 'select' );
	$shortcode_option_map['hover_accent_color']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_hover_animation_accent_color', 'reset' => true );
	$shortcode_option_map['link_type']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_link_type', 'type' => 'select' );
	$shortcode_option_map['link_area']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_link_area', 'type' => 'select' );
	$shortcode_option_map['link_target']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_link_target', 'type' => 'select' );
	$shortcode_option_map['margin_top']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_margin', 'subset' => 'top' );
	$shortcode_option_map['margin_bottom']['fusion_content_boxes'] = array( 'theme-option' => 'content_box_margin', 'subset' => 'bottom' );

	$shortcode_option_map['backgroundcolor']['fusion_content_box'] = array( 'theme-option' => 'content_box_bg_color', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['iconcolor']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_color', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['icon_circle_radius']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_circle_radius', 'type' => 'child' );
	$shortcode_option_map['circlecolor']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_bg_color', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['circlebordercolor']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_bg_inner_border_color', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['outercirclebordercolor']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_bg_outer_border_color', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['circlebordersize']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_bg_inner_border_size', 'type' => 'child', 'reset' => true );
	$shortcode_option_map['outercirclebordersize']['fusion_content_box'] = array( 'theme-option' => 'content_box_icon_bg_outer_border_size', 'type' => 'child', 'reset' => true );

	// Countdown.
	$shortcode_option_map['timezone']['fusion_countdown'] = array( 'theme-option' => 'countdown_timezone', 'type' => 'select' );
	$shortcode_option_map['show_weeks']['fusion_countdown'] = array( 'theme-option' => 'countdown_show_weeks', 'type' => 'yesno' );
	$shortcode_option_map['background_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_background_color', 'reset' => true );
	$shortcode_option_map['background_image']['fusion_countdown'] = array( 'theme-option' => 'countdown_background_image', 'subset' => 'thumbnail' );
	$shortcode_option_map['background_repeat']['fusion_countdown'] = array( 'theme-option' => 'countdown_background_repeat' );
	$shortcode_option_map['background_position']['fusion_countdown'] = array( 'theme-option' => 'countdown_background_position' );
	$shortcode_option_map['counter_box_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_counter_box_color', 'reset' => true );
	$shortcode_option_map['counter_text_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_counter_text_color', 'reset' => true );
	$shortcode_option_map['heading_text_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_heading_text_color', 'reset' => true );
	$shortcode_option_map['subheading_text_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_subheading_text_color', 'reset' => true );
	$shortcode_option_map['link_text_color']['fusion_countdown'] = array( 'theme-option' => 'countdown_link_text_color', 'reset' => true );
	$shortcode_option_map['link_target']['fusion_countdown'] = array( 'theme-option' => 'countdown_link_target',  'type' => 'select' );

	// Counter box.
	$shortcode_option_map['color']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_color', 'reset' => true );
	$shortcode_option_map['title_size']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_title_size' );
	$shortcode_option_map['icon_size']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_icon_size' );
	$shortcode_option_map['body_color']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_body_color', 'reset' => true );
	$shortcode_option_map['body_size']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_body_size' );
	$shortcode_option_map['border_color']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_border_color', 'reset' => true );
	$shortcode_option_map['icon_top']['fusion_counters_box'] = array( 'theme-option' => 'counter_box_icon_top', 'type' => 'yesno' );

	// Counter Circle.
	$shortcode_option_map['filledcolor']['fusion_counter_circle'] = array( 'theme-option' => 'counter_filled_color', 'reset' => true );
	$shortcode_option_map['unfilledcolor']['fusion_counter_circle'] = array( 'theme-option' => 'counter_unfilled_color', 'reset' => true );

	// Dropcap.
	$shortcode_option_map['color']['fusion_dropcap'] = array( 'theme-option' => 'dropcap_color', 'shortcode' => 'fusion_dropcap', 'reset' => true );

	// Flipboxes.
	$shortcode_option_map['background_color_front']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_front_bg', 'reset' => true );
	$shortcode_option_map['title_front_color']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_front_heading', 'reset' => true );
	$shortcode_option_map['text_front_color']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_front_text', 'reset' => true );
	$shortcode_option_map['background_color_back']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_back_bg', 'reset' => true );
	$shortcode_option_map['title_back_color']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_back_heading', 'reset' => true );
	$shortcode_option_map['text_back_color']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_back_text', 'reset' => true );
	$shortcode_option_map['border_size']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_border_size', 'type' => 'range' );
	$shortcode_option_map['border_color']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_border_color' );
	$shortcode_option_map['border_radius']['fusion_flip_box'] = array( 'theme-option' => 'flip_boxes_border_radius' );

	// Icon Element.
	$shortcode_option_map['circlecolor']['fusion_fontawesome'] = array( 'theme-option' => 'icon_circle_color', 'reset' => true );
	$shortcode_option_map['circlebordercolor']['fusion_fontawesome'] = array( 'theme-option' => 'icon_border_color', 'reset' => true );
	$shortcode_option_map['iconcolor']['fusion_fontawesome'] = array( 'theme-option' => 'icon_color', 'reset' => true );

	// Image Frame.
	$shortcode_option_map['bordercolor']['fusion_imageframe'] = array( 'theme-option' => 'imgframe_border_color', 'reset' => true );
	$shortcode_option_map['bordersize']['fusion_imageframe'] = array( 'theme-option' => 'imageframe_border_size', 'type' => 'range' );
	$shortcode_option_map['borderradius']['fusion_imageframe'] = array( 'theme-option' => 'imageframe_border_radius' );
	$shortcode_option_map['stylecolor']['fusion_imageframe'] = array( 'theme-option' => 'imgframe_style_color', 'reset' => true );

	// Modal.
	$shortcode_option_map['background']['fusion_modal'] = array( 'theme-option' => 'modal_bg_color', 'reset' => true );
	$shortcode_option_map['border_color']['fusion_modal'] = array( 'theme-option' => 'modal_border_color', 'reset' => true );

	// Person.
	$shortcode_option_map['background_color']['fusion_person'] = array( 'theme-option' => 'person_background_color', 'reset' => true );
	$shortcode_option_map['pic_bordercolor']['fusion_person'] = array( 'theme-option' => 'person_border_color', 'reset' => true );
	$shortcode_option_map['pic_bordersize']['fusion_person'] = array( 'theme-option' => 'person_border_size', 'type' => 'range' );
	$shortcode_option_map['pic_borderradius']['fusion_person'] = array( 'theme-option' => 'person_border_radius' );
	$shortcode_option_map['pic_style_color']['fusion_person'] = array( 'theme-option' => 'person_style_color', 'reset' => true );
	$shortcode_option_map['content_alignment']['fusion_person'] = array( 'theme-option' => 'person_alignment', 'type' => 'select' );
	$shortcode_option_map['icon_position']['fusion_person'] = array( 'theme-option' => 'person_icon_position', 'type' => 'select' );

	// Popover.
	$shortcode_option_map['title_bg_color']['fusion_popover'] = array( 'theme-option' => 'popover_heading_bg_color', 'reset' => true );
	$shortcode_option_map['content_bg_color']['fusion_popover'] = array( 'theme-option' => 'popover_content_bg_color', 'reset' => true );
	$shortcode_option_map['bordercolor']['fusion_popover'] = array( 'theme-option' => 'popover_border_color', 'reset' => true );
	$shortcode_option_map['textcolor']['fusion_popover'] = array( 'theme-option' => 'popover_text_color', 'reset' => true );
	$shortcode_option_map['placement']['fusion_popover'] = array( 'theme-option' => 'popover_placement', 'type' => 'select' );

	// Portfolio.
	$shortcode_option_map['portfolio_layout_padding']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_layout_padding', 'subset' => array( 'top', 'right', 'bottom', 'left' ) );
	$shortcode_option_map['picture_size']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_featured_image_size', 'type' => 'select' );
	$shortcode_option_map['text_layout']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_text_layout', 'type' => 'select' );
	$shortcode_option_map['portfolio_text_alignment']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_text_alignment', 'type' => 'select' );
	$shortcode_option_map['column_spacing']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_column_spacing', 'type' => 'range' );
	$shortcode_option_map['number_posts']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_items', 'type' => 'range' );
	$shortcode_option_map['pagination_type']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_pagination_type', 'type' => 'select' );
	$shortcode_option_map['content_length']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_content_length', 'type' => 'select' );
	$shortcode_option_map['excerpt_length']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_excerpt_length', 'type' => 'range' );
	$shortcode_option_map['portfolio_title_display']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_title_display', 'type' => 'select' );
	$shortcode_option_map['strip_html']['fusion_portfolio'] = array( 'theme-option' => 'portfolio_strip_html_excerpt', 'type' => 'yesno' );

	// Pricing table.
	$shortcode_option_map['backgroundcolor']['fusion_pricing_table'] = array( 'theme-option' => 'pricing_bg_color', 'reset' => true );
	$shortcode_option_map['bordercolor']['fusion_pricing_table'] = array( 'theme-option' => 'pricing_border_color', 'reset' => true );
	$shortcode_option_map['dividercolor']['fusion_pricing_table'] = array( 'theme-option' => 'pricing_divider_color', 'reset' => true );

	// Progress bar.
	$shortcode_option_map['height']['fusion_progress'] = array( 'theme-option' => 'progressbar_height' );
	$shortcode_option_map['text_position']['fusion_progress'] = array( 'theme-option' => 'progressbar_text_position', 'type' => 'select' );
	$shortcode_option_map['filledcolor']['fusion_progress'] = array( 'theme-option' => 'progressbar_filled_color', 'reset' => true );
	$shortcode_option_map['filledbordercolor']['fusion_progress'] = array( 'theme-option' => 'progressbar_filled_border_color', 'reset' => true );
	$shortcode_option_map['filledbordersize']['fusion_progress'] = array( 'theme-option' => 'progressbar_filled_border_size', 'type' => 'range' );
	$shortcode_option_map['unfilledcolor']['fusion_progress'] = array( 'theme-option' => 'progressbar_unfilled_color', 'reset' => true );
	$shortcode_option_map['textcolor']['fusion_progress'] = array( 'theme-option' => 'progressbar_text_color', 'reset' => true );

	// Section Separator.
	$shortcode_option_map['backgroundcolor']['fusion_section_separator'] = array( 'theme-option' => 'section_sep_bg', 'reset' => true );
	$shortcode_option_map['bordersize']['fusion_section_separator'] = array( 'theme-option' => 'section_sep_border_size', 'type' => 'range' );
	$shortcode_option_map['bordercolor']['fusion_section_separator'] = array( 'theme-option' => 'section_sep_border_color', 'reset' => true );
	$shortcode_option_map['icon_color']['fusion_section_separator'] = array( 'theme-option' => 'icon_color', 'reset' => true );

	// Separator.
	$shortcode_option_map['border_size']['fusion_separator'] = array( 'theme-option' => 'separator_border_size', 'type' => 'range' );
	$shortcode_option_map['icon_circle']['fusion_separator'] = array( 'theme-option' => 'separator_circle', 'type' => 'yesno' );
	$shortcode_option_map['sep_color']['fusion_separator'] = array( 'theme-option' => 'sep_color', 'reset' => true );

	// Social Icons.
	$shortcode_option_map['color_type']['fusion_social_links'] = array( 'theme-option' => 'social_links_color_type', 'type' => 'select' );
	$shortcode_option_map['icon_colors']['fusion_social_links'] = array( 'theme-option' => 'social_links_icon_color' );
	$shortcode_option_map['icons_boxed']['fusion_social_links'] = array( 'theme-option' => 'social_links_boxed', 'type' => 'yesno' );
	$shortcode_option_map['box_colors']['fusion_social_links'] = array( 'theme-option' => 'social_links_box_color' );
	$shortcode_option_map['icons_boxed_radius']['fusion_social_links'] = array( 'theme-option' => 'social_links_boxed_radius' );
	$shortcode_option_map['tooltip_placement']['fusion_social_links'] = array( 'theme-option' => 'social_links_tooltip_placement', 'type' => 'select' );

	// Social Icons for Person.
	$shortcode_option_map['social_icon_font_size']['fusion_person'] = array( 'theme-option' => 'social_links_font_size' );
	$shortcode_option_map['social_icon_padding']['fusion_person'] = array( 'theme-option' => 'social_links_boxed_padding' );
	$shortcode_option_map['social_icon_color_type']['fusion_person'] = array( 'theme-option' => 'social_links_color_type', 'type' => 'select' );
	$shortcode_option_map['social_icon_colors']['fusion_person'] = array( 'theme-option' => 'social_links_icon_color' );
	$shortcode_option_map['social_icon_boxed']['fusion_person'] = array( 'theme-option' => 'social_links_boxed', 'type' => 'yesno' );
	$shortcode_option_map['social_icon_boxed_colors']['fusion_person'] = array( 'theme-option' => 'social_links_box_color' );
	$shortcode_option_map['social_icon_boxed_radius']['fusion_person'] = array( 'theme-option' => 'social_links_boxed_radius' );
	$shortcode_option_map['social_icon_tooltip']['fusion_person'] = array( 'theme-option' => 'social_links_tooltip_placement', 'type' => 'select' );

	// Sharing Box.
	$shortcode_option_map['title']['fusion_sharing'] = array( 'theme-option' => 'sharing_social_tagline' );
	$shortcode_option_map['backgroundcolor']['fusion_sharing'] = array( 'theme-option' => 'social_bg_color', 'reset' => true );
	$shortcode_option_map['icons_boxed']['fusion_sharing'] = array( 'theme-option' => 'social_links_boxed', 'type' => 'yesno' );
	$shortcode_option_map['icons_boxed_radius']['fusion_sharing'] = array( 'theme-option' => 'social_links_boxed_radius' );
	$shortcode_option_map['tagline_color']['fusion_sharing'] = array( 'theme-option' => 'sharing_box_tagline_text_color', 'reset' => true );
	$shortcode_option_map['tooltip_placement']['fusion_sharing'] = array( 'theme-option' => 'sharing_social_links_tooltip_placement', 'type' => 'select' );
	$shortcode_option_map['color_type']['fusion_sharing'] = array( 'theme-option' => 'social_links_color_type', 'type' => 'select' );
	$shortcode_option_map['icon_colors']['fusion_sharing'] = array( 'theme-option' => 'social_links_icon_color' );
	$shortcode_option_map['box_colors']['fusion_sharing'] = array( 'theme-option' => 'social_links_box_color' );

	// Tabs.
	$shortcode_option_map['backgroundcolor']['fusion_tabs'] = array( 'theme-option' => 'tabs_bg_color', 'shortcode' => 'fusion_tabs', 'reset' => true );
	$shortcode_option_map['inactivecolor']['fusion_tabs'] = array( 'theme-option' => 'tabs_inactive_color', 'shortcode' => 'fusion_tabs', 'reset' => true );
	$shortcode_option_map['bordercolor']['fusion_tabs'] = array( 'theme-option' => 'tabs_border_color', 'shortcode' => 'fusion_tabs', 'reset' => true );

	// Tagline.
	$shortcode_option_map['backgroundcolor']['fusion_tagline_box'] = array( 'theme-option' => 'tagline_bg', 'reset' => true );
	$shortcode_option_map['bordercolor']['fusion_tagline_box'] = array( 'theme-option' => 'tagline_border_color', 'reset' => true );
	$shortcode_option_map['margin_top']['fusion_tagline_box'] = array( 'theme-option' => 'tagline_margin', 'subset' => 'top' );
	$shortcode_option_map['margin_bottom']['fusion_tagline_box'] = array( 'theme-option' => 'tagline_margin', 'subset' => 'bottom' );

	// Testimonials.
	$shortcode_option_map['backgroundcolor']['fusion_testimonials'] = array( 'theme-option' => 'testimonial_bg_color', 'reset' => true );
	$shortcode_option_map['textcolor']['fusion_testimonials'] = array( 'theme-option' => 'testimonial_text_color', 'reset' => true );
	$shortcode_option_map['random']['fusion_testimonials'] = array( 'theme-option' => 'testimonials_random', 'type' => 'yesno' );

	// Title.
	$shortcode_option_map['style_type']['fusion_title'] = array( 'theme-option' => 'title_style_type', 'type' => 'select' );
	$shortcode_option_map['sep_color']['fusion_title'] = array( 'theme-option' => 'title_border_color', 'reset' => true );
	$shortcode_option_map['dimensions']['fusion_title'] = array( 'theme-option' => 'title_margin', 'subset' => array( 'top', 'bottom' ) );

	// Toggles.
	$shortcode_option_map['type']['fusion_accordion'] = array( 'theme-option' => 'accordion_type', 'type' => 'select' );
	$shortcode_option_map['divider_line']['fusion_accordion'] = array( 'theme-option' => 'accordion_divider_line', 'type' => 'yesno' );
	$shortcode_option_map['boxed_mode']['fusion_accordion'] = array( 'theme-option' => 'accordion_boxed_mode', 'type' => 'yesno' );
	$shortcode_option_map['border_size']['fusion_accordion'] = array( 'theme-option' => 'accordion_border_size', 'type' => 'range' );
	$shortcode_option_map['border_color']['fusion_accordion'] = array( 'theme-option' => 'accordian_border_color', 'reset' => true );
	$shortcode_option_map['background_color']['fusion_accordion'] = array( 'theme-option' => 'accordian_background_color', 'reset' => true );
	$shortcode_option_map['hover_color']['fusion_accordion'] = array( 'theme-option' => 'accordian_hover_color', 'reset' => true );

	// User Login Element.
	$shortcode_option_map['text_align']['fusion_login'] = array( 'theme-option' => 'user_login_text_align', 'type' => 'select' );
	$shortcode_option_map['form_background_color']['fusion_login'] = array( 'theme-option' => 'user_login_form_background_color', 'reset' => true );
	$shortcode_option_map['text_align']['fusion_register'] = array( 'theme-option' => 'user_login_text_align', 'type' => 'select' );
	$shortcode_option_map['form_background_color']['fusion_register'] = array( 'theme-option' => 'user_login_form_background_color', 'reset' => true );
	$shortcode_option_map['text_align']['fusion_lost_password'] = array( 'theme-option' => 'user_login_text_align', 'type' => 'select' );
	$shortcode_option_map['form_background_color']['fusion_lost_password'] = array( 'theme-option' => 'user_login_form_background_color', 'reset' => true );
	$shortcode_option_map['link_color']['fusion_login'] = array( 'theme-option' => 'link_color' );
	$shortcode_option_map['link_color']['fusion_register'] = array( 'theme-option' => 'link_color' );
	$shortcode_option_map['link_color']['fusion_lost_password'] = array( 'theme-option' => 'link_color' );

	// FAQs.
	$shortcode_option_map['featured_image']['fusion_faq'] = array( 'theme-option' => 'faq_featured_image', 'type' => 'yesno' );
	$shortcode_option_map['filters']['fusion_faq'] = array( 'theme-option' => 'faq_filters', 'type' => 'select' );

	// Widget Area Element.
	$shortcode_option_map['title_color']['fusion_widget_area'] = array( 'theme-option' => 'widget_area_title_color', 'reset' => true );
	$shortcode_option_map['title_size']['fusion_widget_area'] = array( 'theme-option' => 'widget_area_title_size' );

	// Gallery.
	$shortcode_option_map['picture_size']['fusion_gallery'] = array( 'theme-option' => 'gallery_picture_size', 'reset' => true, 'type' => 'select' );
	$shortcode_option_map['layout']['fusion_gallery'] = array( 'theme-option' => 'gallery_layout', 'reset' => true, 'type' => 'select' );
	$shortcode_option_map['columns']['fusion_gallery'] = array( 'theme-option' => 'gallery_columns', 'reset' => true );
	$shortcode_option_map['column_spacing']['fusion_gallery'] = array( 'theme-option' => 'gallery_column_spacing', 'reset' => true );
	$shortcode_option_map['lightbox_content']['fusion_gallery'] = array( 'theme-option' => 'gallery_lightbox_content', 'reset' => true, 'type' => 'select' );
	$shortcode_option_map['lightbox']['fusion_gallery'] = array( 'theme-option' => 'status_lightbox', 'type' => 'yesno' );
	$shortcode_option_map['hover_type']['fusion_gallery'] = array( 'theme-option' => 'gallery_hover_type', 'reset' => true, 'type' => 'select' );

	if ( 'animation_offset' === $param ) {
		return array( 'theme-option' => 'animation_offset', 'type' => 'select' );
	}

	if ( isset( $shortcode_option_map[ $param ][ $shortcode ] ) ) {
		return $shortcode_option_map[ $param ][ $shortcode ];
	}
	return false;
}

/**
 * Set builder element dependencies, for those which involve EO.
 *
 * @since  5.0.0
 * @param  array  $dependencies currently active dependencies.
 * @param  string $shortcode name of shortcode.
 * @param  string $option name of option.
 * @return array  dependency checks.
 */
function fusion_builder_element_dependencies( $dependencies, $shortcode, $option ) {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	$shortcode_option_map = array();

	// Portfolio.
	$shortcode_option_map['portfolio_layout_padding']['fusion_portfolio'][] = array(
		'check' => array(
			'element-option' => 'portfolio_text_layout',
			'value' => 'unboxed',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'text_layout',
			'value' => 'default',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['excerpt_length']['fusion_portfolio'][] = array(
		'check' => array(
			'element-option' => 'portfolio_content_length',
			'value' => 'Full Content',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'content_length',
			'value' => 'default',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['strip_html']['fusion_portfolio'][] = array(
		'check' => array(
			'element-option' => 'portfolio_content_length',
			'value' => 'Full Content',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'content_length',
			'value' => 'default',
			'operator' => '!=',
		),
	);

	// Progress.
	$shortcode_option_map['filledbordercolor']['fusion_progress'][] = array(
		'check' => array(
			'element-option' => 'progressbar_filled_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'filledbordersize',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Social links.
	$shortcode_option_map['icons_boxed_radius']['fusion_social_links'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icons_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['box_colors']['fusion_social_links'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icons_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['icon_colors']['fusion_social_links'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'color_type',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['box_colors']['fusion_social_links'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'color_type',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Sharing box.
	$shortcode_option_map['icons_boxed_radius']['fusion_sharing'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icons_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['box_colors']['fusion_sharing'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'color_type',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['box_colors']['fusion_sharing'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icons_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['icon_colors']['fusion_sharing'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'color_type',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Toggles.
	$shortcode_option_map['divider_line']['fusion_accordion'][] = array(
		'check' => array(
			'element-option' => 'accordion_boxed_mode',
			'value' => '1',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'boxed_mode',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['border_size']['fusion_accordion'][] = array(
		'check' => array(
			'element-option' => 'accordion_boxed_mode',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'boxed_mode',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['border_color']['fusion_accordion'][] = array(
		'check' => array(
			'element-option' => 'accordion_boxed_mode',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'boxed_mode',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['background_color']['fusion_accordion'][] = array(
		'check' => array(
			'element-option' => 'accordion_boxed_mode',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'boxed_mode',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['hover_color']['fusion_accordion'][] = array(
		'check' => array(
			'element-option' => 'accordion_boxed_mode',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'boxed_mode',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Checklist.
	$shortcode_option_map['circlecolor']['fusion_checklist'][] = array(
		'check' => array(
			'element-option' => 'checklist_circle',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'circle',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Imageframe.
	$shortcode_option_map['bordercolor']['fusion_imageframe'][] = array(
		'check' => array(
			'element-option' => 'imageframe_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'bordersize',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Button.
	$shortcode_option_map['bevel_color']['fusion_button'][] = array(
		'check' => array(
			'element-option' => 'button_type',
			'value' => 'Flat',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'type',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Person.
	$shortcode_option_map['social_icon_boxed_radius']['fusion_person'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'social_icon_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['social_icon_boxed_colors']['fusion_person'][] = array(
		'check' => array(
			'element-option' => 'social_links_boxed',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'social_icon_boxed',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['social_icon_boxed_colors']['fusion_person'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'social_icon_color_type',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['social_icon_colors']['fusion_person'][] = array(
		'check' => array(
			'element-option' => 'social_links_color_type',
			'value' => 'brand',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'social_icon_color_type',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Content boxes.
	$shortcode_option_map['circlebordercolor']['fusion_content_boxes'][] = array(
		'check' => array(
			'element-option' => 'content_box_icon_bg_inner_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'circlebordersize',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['outercirclebordercolor']['fusion_content_boxes'][] = array(
		'check' => array(
			'element-option' => 'content_box_icon_bg_outer_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'outercirclebordersize',
			'value' => '',
			'operator' => '!=',
		),
	);
	$boxed_content_boxes = array(
		'check' => array(
			'element-option' => 'content_box_icon_circle',
			'value' => 'no',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icon_circle',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['icon_circle_radius']['fusion_content_boxes'][] = $boxed_content_boxes;
	$shortcode_option_map['circlecolor']['fusion_content_boxes'][] = $boxed_content_boxes;
	$shortcode_option_map['circlebordercolor']['fusion_content_boxes'][] = $boxed_content_boxes;
	$shortcode_option_map['circlebordersize']['fusion_content_boxes'][] = $boxed_content_boxes;
	$shortcode_option_map['outercirclebordercolor']['fusion_content_boxes'][] = $boxed_content_boxes;
	$shortcode_option_map['outercirclebordersize']['fusion_content_boxes'][] = $boxed_content_boxes;

	$parent_boxed_content_boxes = array(
		'check' => array(
			'element-option' => 'content_box_icon_circle',
			'value' => 'no',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'parent_icon_circle',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['circlecolor']['fusion_content_box'][] = $parent_boxed_content_boxes;
	$shortcode_option_map['circlebordercolor']['fusion_content_box'][] = $parent_boxed_content_boxes;
	$shortcode_option_map['circlebordersize']['fusion_content_box'][] = $parent_boxed_content_boxes;
	$shortcode_option_map['outercirclebordercolor']['fusion_content_box'][] = $parent_boxed_content_boxes;
	$shortcode_option_map['outercirclebordersize']['fusion_content_box'][] = $parent_boxed_content_boxes;

	// Flip boxes.
	$shortcode_option_map['border_color']['fusion_flip_box'][] = array(
		'check' => array(
			'element-option' => 'flip_boxes_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'border_size',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Container.
	$shortcode_option_map['border_color']['fusion_builder_container'][] = array(
		'check' => array(
			'element-option' => 'full_width_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'border_size',
			'value' => '',
			'operator' => '!=',
		),
	);
	$shortcode_option_map['border_style']['fusion_builder_container'][] = array(
		'check' => array(
			'element-option' => 'full_width_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'border_size',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Section separator.
	$shortcode_option_map['bordercolor']['fusion_section_separator'][] = array(
		'check' => array(
			'element-option' => 'section_sep_border_size',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'bordersize',
			'value' => '',
			'operator' => '!=',
		),
	);

	// Separator.
	$shortcode_option_map['icon_circle_color']['fusion_separator'][] = array(
		'check' => array(
			'element-option' => 'separator_circle',
			'value' => '0',
			'operator' => '==',
		),
		'output' => array(
			'element' => 'icon_circle',
			'value' => '',
			'operator' => '!=',
		),
	);

	// If has TO related dependency, do checks.
	if ( isset( $shortcode_option_map[ $option ][ $shortcode ] ) && is_array( $shortcode_option_map[ $option ][ $shortcode ] ) ) {
		foreach ( $shortcode_option_map[ $option ][ $shortcode ] as $option_check ) {
			$option_value = $fusion_settings->get( $option_check['check']['element-option'] );
			$pass = false;

			// Check the result of check.
			if ( '==' == $option_check['check']['operator'] ) {
				$pass = ( $option_value == $option_check['check']['value'] ) ? true : false;
			}
			if ( '!=' == $option_check['check']['operator'] ) {
				$pass = ( $option_value != $option_check['check']['value'] ) ? true : false;
			}

			// If check passes then add dependency for checking.
			if ( $pass ) {
				$dependencies[] = $option_check['output'];
			}
		}
	}
	return $dependencies;
}

if ( ! function_exists( 'fusion_builder_render_rich_snippets_for_pages' ) ) {
	/**
	 * Render the full meta data for blog archive and single layouts.
	 *
	 * @param  boolean $title_tag   Set to true to render title rich snippet.
	 * @param  bool    $author_tag  Set to true to render author rich snippet.
	 * @param  bool    $updated_tag Set to true to render updated rich snippet.
	 * @return string               HTML markup to display rich snippets.
	 */
	function fusion_builder_render_rich_snippets_for_pages( $title_tag = true, $author_tag = true, $updated_tag = true ) {

		global $fusion_settings;
		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}

		ob_start();
		if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) ) : ?>

			<?php if ( $title_tag && $fusion_settings->get( 'disable_rich_snippet_title' ) ) : ?>
				<span class="entry-title" style="display: none;">
					<?php echo get_the_title(); ?>
				</span>
			<?php endif; ?>

			<?php if ( $author_tag && $fusion_settings->get( 'disable_rich_snippet_author' ) ) : ?>
				<span class="vcard" style="display: none;">
					<span class="fn">
						<?php the_author_posts_link(); ?>
					</span>
				</span>
			<?php endif; ?>

			<?php if ( $updated_tag && $fusion_settings->get( 'disable_rich_snippet_date' ) ) : ?>
				<span class="updated" style="display:none;">
					<?php echo get_the_modified_time( 'c' ); // WPCS: XSS ok. ?>
				</span>
			<?php endif; ?>

		<?php endif;
		return ob_get_clean();
	}
}

if ( ! function_exists( 'fusion_builder_get_post_content' ) ) {
	/**
	 * Return the post content, either excerpted or in full length.
	 *
	 * @param  string  $page_id        The id of the current page or post.
	 * @param  string  $excerpt        Can be either 'blog' (for main blog page), 'portfolio' (for portfolio page template) or 'yes' (for shortcodes).
	 * @param  integer $excerpt_length Length of the excerpts.
	 * @param  boolean $strip_html     Can be used by shortcodes for a custom strip html setting.
	 * @return string Post content.
	 **/
	function fusion_builder_get_post_content( $page_id = '', $excerpt = 'no', $excerpt_length = 55, $strip_html = false ) {
		$content_excerpted = false;

		if ( 'yes' === $excerpt ) {
			$content_excerpted = true;
		}

		// Return excerpted content.
		if ( $content_excerpted ) {
			return fusion_builder_get_post_content_excerpt( $excerpt_length, $strip_html );
		}

		// Return full content.
		ob_start();
		the_content();
		return ob_get_clean();
	}
}

if ( ! function_exists( 'fusion_builder_get_post_content_excerpt' ) ) {
	/**
	 * Do the actual custom excerpting for of post/page content.
	 *
	 * @param  string  $limit      Maximum number of words or chars to be displayed in excerpt.
	 * @param  boolean $strip_html Set to TRUE to strip HTML tags from excerpt.
	 * @return string 				The custom excerpt.
	 **/
	function fusion_builder_get_post_content_excerpt( $limit = 285, $strip_html ) {

		global $more, $fusion_settings;
		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}
		// Init variables, cast to correct types.
		$content        = '';
		$read_more      = '';
		$custom_excerpt = false;
		$limit          = intval( $limit );
		$strip_html     = filter_var( $strip_html, FILTER_VALIDATE_BOOLEAN );
		// If excerpt length is set to 0, return empty.
		if ( 0 === $limit ) {
			return $content;
		}
		$post = get_post( get_the_ID() );
		// Filter to set the default [...] read more to something arbritary.
		$read_more_text = apply_filters( 'fusion_blog_read_more_excerpt', '&#91;...&#93;' );

		// If read more for excerpts is not disabled.
		if ( $fusion_settings->get( 'disable_excerpts' ) ) {
			// Check if the read more [...] should link to single post.
			if ( $fusion_settings->get( 'link_read_more' ) ) {
				$read_more = ' <a href="' . get_permalink( get_the_ID() ) . '">' . $read_more_text . '</a>';
			} else {
				$read_more = ' ' . $read_more_text;
			}
		}

		// Construct the content.
		// Posts having a custom excerpt.
		if ( has_excerpt() ) {
			$content = '<p>' . do_shortcode( get_the_excerpt() ) . '</p>';
		} else { // All other posts (with and without <!--more--> tag in the contents).
			// HTML tags should be stripped.
			if ( $strip_html ) {
				$content = wp_strip_all_tags( get_the_content( '{{read_more_placeholder}}' ), '<p>' );
				// Strip out all attributes.
				$content = preg_replace( '/<(\w+)[^>]*>/', '<$1>', $content );
				$content = str_replace( '{{read_more_placeholder}}', $read_more, $content );
			} else { // HTML tags remain in excerpt.
				$content = get_the_content( $read_more );
			}
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", 'fusion_extract_shortcode_contents', $content );
			// <!--more--> tag is used in the post.
			if ( false !== strpos( $post->post_content, '<!--more-->' ) ) {
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]&gt;', $content );
				if ( $strip_html ) {
					$content = do_shortcode( $content );
				}
			}
		}
		// Limit the contents to the $limit length.
		if ( ! has_excerpt() ) {
			// Check if the excerpting should be char or word based.
			if ( 'Characters' === $fusion_settings->get( 'excerpt_base' ) ) {
				$content = mb_substr( $content, 0, $limit );
				$content .= $read_more;
			} else { // Excerpting is word based.
				$content = explode( ' ', $content, $limit + 1 );
				if ( count( $content ) > $limit ) {
					array_pop( $content );
					$content = implode( ' ', $content );
					$content .= $read_more;
				} else {
					$content = implode( ' ', $content );
				}
			}
			if ( $strip_html ) {
				$content = '<p>' . $content . '</p>';
			} else {
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]&gt;', $content );
			}
			$content = do_shortcode( $content );
		}
		return $content;
	}
}



if ( ! function_exists( 'fusion_builder_render_post_metadata' ) ) {
	/**
	 * Render the full meta data for blog archive and single layouts.
	 *
	 * @param string $layout    The blog layout (either single, standard, alternate or grid_timeline).
	 * @param string $settings HTML markup to display the date and post format box.
	 * @return  string
	 */
	function fusion_builder_render_post_metadata( $layout, $settings = array() ) {

		global $fusion_settings;
		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}

		$html = $author = $date = $metadata = '';

		$settings = ( is_array( $settings ) ) ? $settings : array();

		$default_settings = array(
			'post_meta'          => fusion_library()->get_option( 'post_meta' ),
			'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
			'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
			'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
			'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
			'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
		);

		$settings = wp_parse_args( $settings, $default_settings );
		$post_meta = get_post_meta( get_queried_object_id(), 'pyre_post_meta', true );

		// Check if meta data is enabled.
		if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

			// For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
			if ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] ) {
				return '';
			}

			// Render author meta data.
			if ( $settings['post_meta_author'] ) {
				ob_start();
				the_author_posts_link();
				$author_post_link = ob_get_clean();

				// Check if rich snippets are enabled.
				if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) && $fusion_settings->get( 'disable_rich_snippet_author' ) ) {
					$metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
				} else {
					$metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span>' . $author_post_link . '</span>' );
				}
				$metadata .= '<span class="fusion-inline-sep">|</span>';
			} else { // If author meta data won't be visible, render just the invisible author rich snippet.
				$author .= fusion_builder_render_rich_snippets_for_pages( false, true, false );
			}

			// Render the updated meta data or at least the rich snippet if enabled.
			if ( $settings['post_meta_date'] ) {
				$metadata .= fusion_builder_render_rich_snippets_for_pages( false, false, true );

				$formatted_date = get_the_time( $fusion_settings->get( 'date_format' ) );
				$date_markup = '<span>' . $formatted_date . '</span><span class="fusion-inline-sep">|</span>';
				$metadata .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
			} else {
				$date .= fusion_builder_render_rich_snippets_for_pages( false, false, true );
			}

			// Render rest of meta data.
			// Render categories.
			if ( $settings['post_meta_cats'] ) {
				ob_start();
				the_category( ', ' );
				$categories = ob_get_clean();

				if ( $categories ) {
					$metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'fusion-builder' ), $categories ) : $categories;
					$metadata .= '<span class="fusion-inline-sep">|</span>';
				}
			}

			// Render tags.
			if ( $settings['post_meta_tags'] ) {
				ob_start();
				the_tags( '' );
				$tags = ob_get_clean();

				if ( $tags ) {
					$metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'fusion-builder' ), $tags ) . '</span><span class="fusion-inline-sep">|</span>';
				}
			}

			// Render comments.
			if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
				ob_start();
				comments_popup_link( esc_html__( '0 Comments', 'fusion-builder' ), esc_html__( '1 Comment', 'fusion-builder' ), esc_html__( '% Comments', 'fusion-builder' ) );
				$comments = ob_get_clean();
				$metadata .= '<span class="fusion-comments">' . $comments . '</span>';
			}

			// Render the HTML wrappers for the different layouts.
			if ( $metadata ) {
				$metadata = $author . $date . $metadata;

				if ( 'single' === $layout ) {
					$html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
				} elseif ( in_array( $layout, array( 'alternate', 'grid_timeline' ), true ) ) {
					$html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
				} else {
					$html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
				}
			} else {
				$html .= $author . $date;
			}
		} else {
			// Render author and updated rich snippets for grid and timeline layouts.
			if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) ) {
				$html .= fusion_builder_render_rich_snippets_for_pages( false );
			}
		}

		return apply_filters( 'fusion_post_metadata_markup', $html );
	}
}

if ( ! function_exists( 'fusion_builder_update_element' ) ) {
	/**
	 * Update single element setting value.
	 *
	 * @param string       $element    Shortcode tag of element.
	 * @param string       $param_name Param name to be updated.
	 * @param string/array $values     Settings to be replaced / updated.
	 */
	function fusion_builder_update_element( $element, $param_name, $values ) {

		global $all_fusion_builder_elements;

		$element_settings = $all_fusion_builder_elements[ $element ]['params'];

		$settings = $element_settings[ $param_name ]['value'];

		if ( is_array( $values ) ) {
			$settings = array_merge( $settings, $values );
		} else {
			$settings = $values;
		}

		$all_fusion_builder_elements[ $element ]['params'][ $param_name ]['value'] = $settings;

	}
}

/**
 * Check if element is enabled or not.
 *
 * @param  string $element The element shortcode tag.
 * @return boolean
 */
function fusion_is_element_enabled( $element ) {

	$fusion_builder_settings = get_option( 'fusion_builder_settings', array() );
	if ( empty( $fusion_builder_settings ) || ! isset( $fusion_builder_settings['fusion_elements'] ) ) {
		return true;
	}
	// Set Fusion Builder enabled elements.
	$enabled_elements = $fusion_builder_settings['fusion_elements'];

	return (bool) ( empty( $enabled_elements ) || ( ! empty( $enabled_elements ) && in_array( $element, $enabled_elements ) ) );

}

if ( ! function_exists( 'fusion_get_fields_array' ) ) {
	/**
	 * Get a single fields array from sections.
	 *
	 * @since 1.1
	 * @param  object $sections Sections from redux.
	 * @return array
	 */
	function fusion_get_fields_array( $sections ) {

		$fields = array();
		foreach ( $sections->sections as $section ) {
			if ( ! isset( $section['fields'] ) ) {
				continue;
			}
			foreach ( $section['fields'] as $field ) {
				if ( ! isset( $field['type'] ) ) {
					continue;
				}
				if ( ! in_array( $field['type'], array( 'sub-section', 'accordion' ) ) ) {
					if ( isset( $field['id'] ) ) {
						$fields[] = $field['id'];
					}
				} else {
					if ( ! isset( $field['fields'] ) ) {
						continue;
					}
					foreach ( $field['fields'] as $sub_field ) {
						if ( isset( $sub_field['id'] ) ) {
							$fields[] = $sub_field['id'];
						}
					}
				}
			}
		}
		return $fields;
	}
}

if ( ! function_exists( 'fusion_auto_calculate_accent_color' ) ) {
	/**
	 * Auto calculate accent color, based on provided background color.
	 *
	 * @since 1.1.6
	 * @param  string $color color base value.
	 * @return string
	 */
	function fusion_auto_calculate_accent_color( $color ) {
		$color_obj = Fusion_Color::new_color( $color );

		if ( 0 < $color_obj->lightness ) { // Not black.
			if ( 50 < $color_obj->lightness ) {
				return $color_obj->getNew( 'lightness', $color_obj->lightness / 2 )->toCSS( 'rgba' );
			} else if ( 50 > $color_obj->lightness ) {
				return $color_obj->getNew( 'lightness', $color_obj->lightness * 2 )->toCSS( 'rgba' );
			}
		} else {
			return $color_obj->getNew( 'lightness', 70 )->toCSS( 'rgba' );
		}
	}
}
