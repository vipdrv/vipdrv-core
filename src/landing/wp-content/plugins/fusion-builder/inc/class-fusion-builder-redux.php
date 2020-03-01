<?php

class Fusion_Builder_Redux extends Fusion_FusionRedux {

	/**
	 * Initializes and triggers all other actions/hooks.
	 *
	 * @access public
	 */
	public function init_fusionredux() {

		add_filter( 'fusion_options_font_size_dimension_fields', array( $this, 'fusion_options_font_size_dimension_fields' ) );
		add_filter( 'fusion_options_sliders_not_in_pixels', array( $this, 'fusion_options_sliders_not_in_pixels' ) );
		add_filter( 'fusion_options_builder_soft_dependencies', array( $this, 'fusion_options_builder_soft_dependencies' ) );

		parent::init_fusionredux();
	}

	/**
	 * Adds options to be processes as font-sizes.
	 * Affects the field's sanitization call.
	 *
	 * @access public
	 * @since 1.1.0
	 * @param array $fields An array of fields.
	 * @return array
	 */
	public function fusion_options_font_size_dimension_fields( $fields ) {
		$extra_fields = array(
			'content_box_title_size',
			'content_box_icon_size',
			'counter_box_title_size',
			'counter_box_icon_size',
			'counter_box_body_size',
			'social_links_font_size',
		);
		return array_unique( array_merge( $fields, $extra_fields ) );
	}

	/**
	 * Sliders that are not in pixels.
	 *
	 * @access public
	 * @since 1.1.0
	 * @param array $fields An array of fields.
	 * @return array
	 */
	public function fusion_options_sliders_not_in_pixels( $fields ) {
		$extra_fields = array(
			'carousel_speed',
			'counter_box_speed',
			'testimonials_speed',
		);
		return array_unique( array_merge( $fields, $extra_fields ) );
	}

	/**
	 * Builder dependencies.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $dependencies An array of fields.
	 * @return array
	 */
	public function fusion_options_builder_soft_dependencies( $dependencies ) {
		return array_merge( $dependencies, array(
			'accordion_divider_line'                 => array( 'accordion_boxed_mode' ),
			'accordion_border_size'                  => array( 'accordion_boxed_mode' ),
			'accordian_border_color'                 => array( 'accordion_boxed_mode' ),
			'accordian_background_color'             => array( 'accordion_boxed_mode' ),
			'accordian_hover_color'                  => array( 'accordion_boxed_mode' ),
			'portfolio_archive_excerpt_length'       => array( 'portfolio_archive_content_length' ),
			'portfolio_archive_layout_padding'       => array( 'portfolio_archive_text_layout' ),
			'social_links_icon_color'                => array( 'social_links_color_type' ),
			'social_links_box_color'                 => array( 'social_links_boxed', 'social_links_color_type' ),
			'social_links_boxed_radius'              => array( 'social_links_boxed' ),
			'social_links_boxed_padding'             => array( 'social_links_boxed' ),
			'checklist_circle_color'                 => array( 'checklist_circle' ),
			'content_box_icon_circle_radius'         => array( 'content_box_icon_circle' ),
			'content_box_icon_bg_color'              => array( 'content_box_icon_circle' ),
			'content_box_icon_bg_inner_border_color' => array( 'content_box_icon_circle' ),
			'content_box_icon_bg_inner_border_size'  => array( 'content_box_icon_circle' ),
			'content_box_icon_bg_outer_border_color' => array( 'content_box_icon_circle' ),
			'content_box_icon_bg_outer_border_size'  => array( 'content_box_icon_circle' ),
			'portfolio_layout_padding'               => array( 'portfolio_text_layout' ),
			'portfolio_excerpt_length'               => array( 'portfolio_content_length' ),
		) );
	}

	/**
	 * Extra functionality on save.
	 *
	 * @access public
	 * @since 1.1
	 * @param array $data           The data.
	 * @param array $changed_values The changed values to save.
	 * @return void
	 */
	public function save_as_option( $data, $changed_values ) {
		update_option( 'fusion_cache_server_ip', $data['cache_server_ip'] );
	}
}
