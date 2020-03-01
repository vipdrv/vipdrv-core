<?php
/**
 * Portfolio Metabox options.
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
	'width',
	esc_html__( 'Width (Content Columns for Featured Image)', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'full'    => esc_attr__( 'Full Width', 'Avada' ),
		'half'    => esc_attr__( 'Half Width', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose if the featured image is full or half width. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_featured_image_width', '', 'select' ) )
);

$this->radio_buttonset(
	'portfolio_width_100',
	esc_attr__( 'Use 100% Width Page', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to set this post to 100&#37; browser width. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_width_100', '', 'yesno' ) )
);

$this->radio_buttonset(
	'project_desc_title',
	esc_html__( 'Show Project Description Title', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the project description title. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_project_desc_title', '', 'yesno' ) )
);

$this->radio_buttonset(
	'project_details',
	esc_html__( 'Show Project Details', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the project details text. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_project_details', '', 'yesno' ) )
);

$this->radio_buttonset(
	'show_first_featured_image',
	esc_html__( 'Disable First Featured Image', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
	),
	sprintf( esc_html__( 'Disable the 1st featured image on single post pages. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_disable_first_featured_image', '', 'reverseyesno' ) )
);

// Dependency check for whether link icon is showing.
$featured_image_dependency = array(
	array(
		'field'      => 'show_first_featured_image',
		'value'      => 'yes',
		'comparison' => '!=',
	),
);
if ( 0 == Avada()->settings->get( 'portfolio_disable_first_featured_image' ) ) {
	$featured_image_dependency[] = array(
		'field'      => 'show_first_featured_image',
		'value'      => 'default',
		'comparison' => '!=',
	);
}
$this->dimension(
	array(
		'fimg_width',
		'fimg_height',
	),
	esc_attr__( 'Featured Image Dimensions', 'Avada' ),
	esc_html__( 'In pixels or percentage, ex: 100% or 100px. Or Use "auto" for automatic resizing if you added either width or height.', 'Avada' ),
	$featured_image_dependency
);

$this->textarea(
	'video',
	esc_attr__( 'Video Embed Code', 'Avada' ),
	esc_attr__( 'Insert Youtube or Vimeo embed code.', 'Avada' )
);

$this->text(
	'video_url',
	esc_attr__( 'Youtube/Vimeo Video URL for Lightbox', 'Avada' ),
	esc_attr__( 'Insert the video URL that will show in the lightbox.', 'Avada' )
);

$this->text(
	'project_url',
	esc_attr__( 'Project URL', 'Avada' ),
	esc_attr__( 'The URL the project text links to.', 'Avada' )
);

$this->text(
	'project_url_text',
	esc_attr__( 'Project URL Text', 'Avada' ),
	esc_html__( 'The custom project text that will link.', 'Avada' )
);

$this->text(
	'copy_url',
	esc_attr__( 'Copyright URL', 'Avada' ),
	esc_html__( 'The URL the copyright text links to.', 'Avada' )
);

$this->text(
	'copy_url_text',
	esc_attr__( 'Copyright URL Text', 'Avada' ),
	esc_html__( 'The custom copyright text that will link.', 'Avada' )
);

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
	sprintf( esc_html__( 'Choose which icons display on this post. %s', 'Avada' ), Avada()->settings->get_default_description( 'link_image_rollover', '', 'rollover' ) )
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
	'link_icon_target',
	esc_attr__( 'Open Post Links In New Window', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'no'      => esc_attr__( 'No', 'Avada' ),
		'yes'     => esc_attr__( 'Yes', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to open the single post page, project url and copyright url links in a new window. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_link_icon_target', '', 'yesno' ) )
);

$this->radio_buttonset(
	'related_posts',
	esc_attr__( 'Show Related Projects', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide related projects on this post. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_related_posts', '', 'showhide' ) )
);

$this->radio_buttonset(
	'share_box',
	esc_attr__( 'Show Social Share Box', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the social share box. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_social_sharing_box', '', 'showhide' ) )
);

$this->radio_buttonset(
	'post_pagination',
	esc_attr__( 'Show Previous/Next Pagination', 'Avada' ),
	array(
		'default' => esc_attr__( 'Default', 'Avada' ),
		'yes'     => esc_attr__( 'Show', 'Avada' ),
		'no'      => esc_attr__( 'Hide', 'Avada' ),
	),
	sprintf( esc_html__( 'Choose to show or hide the post navigation. %s', 'Avada' ), Avada()->settings->get_default_description( 'portfolio_pn_nav', '', 'showhide' ) )
);

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
