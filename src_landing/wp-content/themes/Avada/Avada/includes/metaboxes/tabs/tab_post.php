<?php
/**
 * Post Metabox options.
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

$this->radio_buttonset(
	'show_first_featured_image',
	esc_attr__( 'Disable First Featured Image', 'Avada' ),
	array(
		'no'  => esc_attr__( 'No', 'Avada' ),
		'yes' => esc_attr__( 'Yes', 'Avada' ),
	),
	esc_html__( 'Disable the 1st featured image on single post pages.', 'Avada' )
);

$this->dimension(
	array(
		'fimg_width',
		'fimg_height',
	),
	esc_attr__( 'Featured Image Dimensions', 'Avada' ),
	esc_html__( 'In pixels or percentage, ex: 100% or 100px. Or Use "auto" for automatic resizing if you added either width or height.', 'Avada' ),
	array(
		array(
			'field'      => 'show_first_featured_image',
			'value'      => 'yes',
			'comparison' => '!=',
		),
	)
);

$this->radio_buttonset(
	'portfolio_width_100',
	esc_html__( 'Use 100% Width Page', 'Avada' ),
	array(
		'default' 	=> esc_attr__( 'Default', 'Avada' ),
		'no'  		=> esc_attr__( 'No', 'Avada' ),
		'yes' 		=> esc_attr__( 'Yes', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to set this post to 100&#37; browser width. %s', 'Avada' ), Avada()->settings->get_default_description( 'blog_width_100', '', 'yesno' ) )
);

$this->textarea(
	'video',
	esc_attr__( 'Video Embed Code', 'Avada' ),
	esc_html__( 'Insert Youtube or Vimeo embed code.', 'Avada' )
);

$screen = get_current_screen();
if ( 'avada_faq' !== $screen->post_type ) {
	$this->select(
		'image_rollover_icons',
		esc_attr__( 'Image Rollover Icons', 'Avada' ),
		array(
			'default'  => esc_attr__( 'Default', 'Avada' ),
			'linkzoom' => esc_html__( 'Link + Zoom', 'Avada' ),
			'link'     => esc_attr__( 'Link', 'Avada' ),
			'zoom'     => esc_attr__( 'Zoom', 'Avada' ),
			'no'       => esc_attr__( 'No Icons', 'Avada' ),
		),
		sprintf( esc_html__( 'Choose which icons display on this post. %s', 'Avada' ), Avada()->settings->get_default_description( 'image_rollover', '', 'rollover' ) )
	);

	// Dependency check for whether link icon is showing.
	$link_dependency = array(
		array(
			'field'      => 'image_rollover_icons',
			'value'      => 'zoom',
			'comparison' => '!=',
		),
		array(
			'field'      => 'image_rollover_icons',
			'value'      => 'no',
			'comparison' => '!=',
		),
	);
	if ( 0 == Avada()->settings->get( 'image_rollover' ) || 0 == Avada()->settings->get( 'link_image_rollover' ) ) {
		$link_dependency[] = array(
			'field'      => 'image_rollover_icons',
			'value'      => 'default',
			'comparison' => '!=',
		);
	}
	$this->text(
		'link_icon_url',
		esc_attr__( 'Link Icon URL', 'Avada' ),
		esc_attr__( 'Leave blank for post URL.', 'Avada' ),
		$link_dependency
	);

	$this->radio_buttonset(
		'post_links_target',
		esc_html__( 'Open Post Links In New Window', 'Avada' ),
		array(
			'no'  => esc_attr__( 'No', 'Avada' ),
			'yes' => esc_attr__( 'Yes', 'Avada' ),
		),
		esc_html__( 'Choose to open the single post page link in a new window.', 'Avada' )
	);
} // End if().

$this->radio_buttonset(
	'related_posts',
	esc_attr__( 'Show Related Posts', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide related posts on this post. %s', 'Avada' ), Avada()->settings->get_default_description( 'related_posts', '', 'showhide' ) )
);

$this->radio_buttonset(
	'share_box',
	esc_attr__( 'Show Social Share Box', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the social share box. %s', 'Avada' ), Avada()->settings->get_default_description( 'social_sharing_box', '', 'showhide' ) )
);

$this->radio_buttonset(
	'post_pagination',
	esc_html__( 'Show Previous/Next Pagination', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the post navigation. %s', 'Avada' ), Avada()->settings->get_default_description( 'blog_pn_nav', '', 'showhide' ) )
);

$this->radio_buttonset(
	'author_info',
	esc_attr__( 'Show Author Info Box', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the author info box. %s', 'Avada' ), Avada()->settings->get_default_description( 'author_info', '', 'showhide' ) )
);

$this->radio_buttonset(
	'post_meta',
	esc_html__( 'Show Post Meta', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the post meta. %s', 'Avada' ), Avada()->settings->get_default_description( 'post_meta', '', 'showhide' ) )
);

$this->radio_buttonset(
	'post_comments',
	esc_attr__( 'Show Comments', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_attr__( 'Choose to show or hide comments area. %s', 'Avada' ), Avada()->settings->get_default_description( 'blog_comments', '', 'showhide' ) )
);

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
