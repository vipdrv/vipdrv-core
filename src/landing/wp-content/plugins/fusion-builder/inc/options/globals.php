<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Element settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_globals( $sections ) {

	$option_name = Fusion_Settings::get_option_name();
	$settings    = get_option( $option_name, array() );

	$sections['globals'] = array(
		'label'    => esc_html__( 'Global Options', 'fusion-builder' ),
		'id'       => 'globals',
		'is_panel' => true,
		'priority' => 1,
		'icon'     => 'el-icon-cog',
		'fields'   => array(
			'primary_color' => array(
				'label'       => esc_html__( 'Primary Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the main highlight color throughout Fusion Builder elements.', 'fusion-builder' ),
				'id'          => 'primary_color',
				'default'     => '#a0ce4e',
				'type'        => 'color',
			),
			'featured_image_placeholder' => array(
				'label'       => esc_html__( 'Image Placeholders', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display a placeholder image for posts that do not have a featured image. This allows the post to display on portfolio archives and related posts/projects carousels.', 'fusion-builder' ),
				'id'          => 'featured_image_placeholder',
				'default'     => '1',
				'type'        => 'switch',
			),
			'gmap_api' => array(
				'label'           => esc_html__( 'Google Maps API Key', 'fusion-builder' ),
				'description'     => sprintf( esc_html__( 'Follow the steps in %s to get the API key. This key applies to both the contact page map and Fusion Builder google map element.', 'fusion-builder' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank" rel="noopener noreferrer">' . esc_html__( 'the Google docs', 'fusion-builder' ) . '</a>' ),
				'id'              => 'gmap_api',
				'default'         => '',
				'type'            => 'text',
				'active_callback' => array( 'Avada_Options_Conditionals', 'is_contact' ),
				'required'    => array(
					array(
						'setting'  => 'status_gmap',
						'operator' => '=',
						'value'    => '1',
					),
				),
			),
			'ec_hover_type' => array(
				'label'       => esc_html__( 'Events Featured Image Hover Type', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the hover type for event featured images.', 'fusion-builder' ),
				'id'          => 'ec_hover_type',
				'default'     => 'none',
				'type'        => 'select',
				'choices'     => array(
					'none'    => 'none',
					'zoomin'  => esc_html__( 'Zoom In', 'fusion-builder' ),
					'zoomout' => esc_html__( 'Zoom Out', 'fusion-builder' ),
					'liftup'  => esc_html__( 'Lift Up', 'fusion-builder' ),
				),
			),
			'status_gmap' => array(
				'label'       => esc_html__( 'Google Map Scripts', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to enable google map.', 'fusion-builder' ),
				'id'          => 'status_gmap',
				'default'     => '1',
				'type'        => 'switch',
			),
			'social_media_icons' => array(
				'label'      => esc_html__( 'Social Media Links', 'fusion-builder' ),
				'description' => esc_html__( 'Social media links use a repeater field and allow one network per field. Click the "Add" button to add additional fields.', 'fusion-builder' ),
				'id'         => 'social_media_icons',
				'default'    => array(),
				'type'       => 'repeater',
				'bind_title' => 'icon',
				'fields'     => array(
					'icon' => array(
						'type'        => 'select',
						'description' => esc_html__( 'Select a social network to automatically add its icon', 'fusion-builder' ),
						'default'     => 'none',
						'choices'     => Fusion_Data::fusion_social_icons( true, false ),
					),
					'url' => array(
						'type'        => 'text',
						'label'       => esc_html__( 'Link (URL)', 'fusion-builder' ),
						'description' => esc_html__( 'Insert your custom link here', 'fusion-builder' ),
						'default'     => '',
					),
					'custom_title' => array(
						'type'        => 'text',
						'label'       => esc_html__( 'Custom Icon Title', 'fusion-builder' ),
						'description' => esc_html__( 'Insert your custom link here', 'fusion-builder' ),
						'default'     => '',
					),
					'custom_source' => array(
						'type'        => 'media',
						'label'       => esc_html__( 'Link (URL) of the image you want to use as the icon', 'fusion-builder' ),
						'description' => esc_html__( 'Upload your custom icon', 'fusion-builder' ),
						'default'     => '',
					),
				),
			),
			'nofollow_social_links' => array(
				'label'       => esc_html__( 'Add "nofollow" to social links', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to add "nofollow" attribute to all social links.', 'fusion-builder' ),
				'id'          => 'nofollow_social_links',
				'default'     => '0',
				'type'        => 'switch',
			),
			'social_icons_new' => array(
				'label'       => esc_html__( 'Open Social Icons in a New Window', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to allow social icons to open in a new window.', 'fusion-builder' ),
				'id'          => 'social_icons_new',
				'default'     => '1',
				'type'        => 'switch',
			),
			'woocommerce_product_box_design' => array(
				'type'        => 'radio-buttonset',
				'label'       => esc_html__( 'WooCommerce Product Box Design', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the design of the product boxes.', 'fusion-builder' ),
				'id'          => 'woocommerce_product_box_design',
				'default'     => 'classic',
				'choices'     => array(
					'classic' => esc_html__( 'Classic', 'fusion-builder' ),
					'clean'   => esc_html__( 'Clean', 'fusion-builder' ),
				),
			),
			'alternate_date_format_day' => array(
				'label'       => esc_html__( 'Blog Alternate Layout Day Format', 'fusion-builder' ),
				'description' => __( 'Controls the day format for blog alternate layouts. <a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank" rel="noopener noreferrer">Formatting Date and Time</a>', 'fusion-builder' ),
				'id'          => 'alternate_date_format_day',
				'default'     => 'j',
				'type'        => 'text',
			),
			'blog_load_more_posts_button_bg_color' => array(
				'label'       => esc_html__( 'Load More Posts Button Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the background color of the load more button for ajax post loading. Also works with the blog element.', 'fusion-builder' ),
				'id'          => 'blog_load_more_posts_button_bg_color',
				'default'     => '#ebeaea',
				'type'        => 'color-alpha',
			),
			'alternate_date_format_month_year' => array(
				'label'       => esc_html__( 'Blog Alternate Layout Month and Year Format', 'fusion-builder' ),
				'description' => __( 'Controls the month and year format for blog alternate layouts. <a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank" rel="noopener noreferrer">Formatting Date and Time</a>', 'fusion-builder' ),
				'id'          => 'alternate_date_format_month_year',
				'default'     => 'm, Y',
				'type'        => 'text',
			),
			'timeline_date_format' => array(
				'label'       => esc_html__( 'Blog Timeline Layout Date Format', 'fusion-builder' ),
				'description' => __( 'Controls the timeline label format for blog timeline layouts. <a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank" rel="noopener noreferrer">Formatting Date</a>', 'fusion-builder' ),
				'id'          => 'timeline_date_format',
				'default'     => 'F Y',
				'type'        => 'text',
			),
			'date_format' => array(
				'label'       => esc_html__( 'Date Format', 'fusion-builder' ),
				'description' => __( 'Controls the date format for date meta data.  <a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank" rel="noopener noreferrer">Formatting Date and Time</a>', 'fusion-builder' ),
				'id'          => 'date_format',
				'default'     => 'F jS, Y',
				'type'        => 'text',
			),
			'disable_date_rich_snippet_pages' => array(
				'label'       => esc_html__( 'Rich Snippets', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to enable rich snippets data site wide.', 'fusion-builder' ),
				'id'          => 'disable_date_rich_snippet_pages',
				'default'     => '1',
				'type'        => 'switch',
			),
			'disable_rich_snippet_title' => array(
				'label'       => esc_html__( 'Rich Snippets Title', 'Avada' ),
				'description' => esc_html__( 'Turn on to enable title rich snippet data site wide.', 'Avada' ),
				'id'          => 'disable_rich_snippet_title',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'disable_date_rich_snippet_pages',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'disable_rich_snippet_author' => array(
				'label'       => esc_html__( 'Rich Snippets Author Info', 'Avada' ),
				'description' => esc_html__( 'Turn on to enable author rich snippet data site wide.', 'Avada' ),
				'id'          => 'disable_rich_snippet_author',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'disable_date_rich_snippet_pages',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'disable_rich_snippet_date' => array(
				'label'       => esc_html__( 'Rich Snippets Publish Date', 'Avada' ),
				'description' => esc_html__( 'Turn on to enable date rich snippet data site wide.', 'Avada' ),
				'id'          => 'disable_rich_snippet_date',
				'default'     => '1',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'disable_date_rich_snippet_pages',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'posts_slideshow_number' => array(
				'label'       => esc_html__( 'Posts Slideshow Images', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the number of featured image boxes for blog/portfolio posts.', 'fusion-builder' ),
				'id'          => 'posts_slideshow_number',
				'default'     => '5',
				'type'        => 'slider',
				'choices'     => array(
					'min'  => '1',
					'max'  => '30',
					'step' => '1',
				),
			),
			'disable_excerpts' => array(
				'label'       => esc_html__( 'Excerpt [...] Display', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to display the read more sign [...] on excerpts throughout the site.', 'fusion-builder' ),
				'id'          => 'disable_excerpts',
				'default'     => '1',
				'type'        => 'switch',
			),
			'link_read_more' => array(
				'label'       => esc_html__( 'Make [...] Link to Single Post Page', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to have the read more sign [...] on excerpts link to the single post page.', 'fusion-builder' ),
				'id'          => 'link_read_more',
				'default'     => '0',
				'type'        => 'switch',
				'required'    => array(
					array(
						'setting'  => 'disable_excerpts',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'timeline_bg_color' => array(
				'label'       => esc_html__( 'Grid Box Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the background color for the grid boxes.', 'fusion-builder' ),
				'id'          => 'timeline_bg_color',
				'default'     => 'rgba(255,255,255,0)',
				'type'        => 'color-alpha',
			),
			'timeline_color' => array(
				'label'       => esc_html__( 'Grid Element Color', 'fusion-builder' ),
				'description' => esc_html__( 'Controls the color of borders/divider lines/date box/timeline dots and arrows for the grid boxes.', 'fusion-builder' ),
				'id'          => 'timeline_color',
				'default'     => '#ebeaea',
				'type'        => 'color-alpha',
			),
			'disable_mobile_animate_css' => array(
				'label'       => esc_html__( 'CSS Animations on Mobiles', 'fusion-builder' ),
				'description' => esc_html__( 'Turn on to enable CSS animations on mobiles.', 'fusion-builder' ),
				'id'          => 'disable_mobile_animate_css',
				'default'     => '0',
				'type'        => 'switch',
			),
		),
	);

	return $sections;

}
