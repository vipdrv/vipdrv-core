<?php
/**
 * Avada Options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Portfolio settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_portfolio( $sections ) {

	$sections['portfolio'] = array(
		'label'    => esc_html__( 'Portfolio', 'Avada' ),
		'id'       => 'heading_portfolio',
		'priority' => 16,
		'icon'     => 'el-icon-th',
		'class'    => 'hidden-section-heading',
		'fields'   => array(
			'general_portfolio_options_subsection' => array(
				'label'       => esc_html__( 'General Portfolio', 'Avada' ),
				'description' => '',
				'id'          => 'general_portfolio_options_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'portfolio_archive_layout' => array(
						'label'       => esc_html__( 'Portfolio Archive Layout', 'Avada' ),
						'description' => esc_html__( 'Controls the layout for the portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_layout',
						'default'     => 'grid',
						'type'        => 'radio-buttonset',
						'choices' => array(
							'grid'    => esc_html__( 'Grid', 'Avada' ),
							'masonry' => esc_html__( 'Masonry', 'Avada' ),
						),
					),
					'portfolio_archive_featured_image_size' => array(
						'label'       => esc_html__( 'Portfolio Archive Featured Image Size', 'Avada' ),
						'description' => __( 'Controls if the featured image size is fixed (cropped) or auto (full image ratio) for portfolio archive pages. <strong>IMPORTANT:</strong> Fixed works best with a standard 940px site width. Auto works best with larger site widths.', 'Avada' ),
						'id'          => 'portfolio_archive_featured_image_size',
						'default'     => 'cropped',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'cropped' => esc_html__( 'Fixed', 'Avada' ),
							'full'    => esc_html__( 'Auto', 'Avada' ),
						),
						'required'    => array(
							array(
								'setting'  => 'portfolio_archive_layout',
								'operator' => '==',
								'value'    => 'grid',
							),
						),
					),
					'portfolio_archive_columns' => array(
						'label'       => esc_html__( 'Portfolio Archive Columns', 'Avada' ),
						'description' => __( 'Controls the amount of columns for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_columns',
						'default'     => 1,
						'type'        => 'slider',
						'choices'     => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
					'portfolio_archive_column_spacing' => array(
						'label'       => esc_html__( 'Portfolio Archive Column Spacing', 'Avada' ),
						'description' => esc_html__( 'Controls the column spacing for archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_column_spacing',
						'default'     => '20',
						'type'        => 'slider',
						'choices'     => array(
							'min'  => '0',
							'max'  => '300',
							'step' => '1',
						),
					),
					'portfolio_archive_one_column_text_position' => array(
						'label'       => esc_html__( 'Portfolio Archive Content Position', 'Avada' ),
						'description' => esc_html__( 'Select if title, terms and excerpts should be displayed below or next to the featured images.', 'Avada' ),
						'id'          => 'portfolio_archive_one_column_text_position',
						'default'     => 'below',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'below'   => esc_attr__( 'Below image', 'Avada' ),
							'floated' => esc_attr__( 'Next to Image', 'Avada' ),
						),
						'required'    => array(
							array(
								'setting'  => 'portfolio_archive_layout',
								'operator' => '==',
								'value'    => 'grid',
							),
							array(
								'setting'  => 'portfolio_archive_columns',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'portfolio_archive_items' => array(
						'label'       => esc_html__( 'Number of Portfolio Items Per Archive Page', 'Avada' ),
						'description' => esc_html__( 'Controls the number of posts that display per page for portfolio archive pages. Set to -1 to display all. Set to 0 to use the number of posts from Settings > Reading.', 'Avada' ),
						'id'          => 'portfolio_archive_items',
						'default'     => '10',
						'type'        => 'slider',
						'choices'     => array(
							'min'  => '-1',
							'max'  => '50',
							'step' => '1',
						),
					),
					'portfolio_archive_text_layout' => array(
						'label'       => esc_html__( 'Portfolio Archive Text Layout', 'Avada' ),
						'description' => esc_html__( 'Controls if the portfolio text content is displayed boxed or unboxed or is completely disabled for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_text_layout',
						'default'     => 'no_text',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'no_text' => esc_html__( 'No Text', 'Avada' ),
							'boxed'   => esc_html__( 'Boxed', 'Avada' ),
							'unboxed' => esc_html__( 'Unboxed', 'Avada' ),
						),
					),
					'portfolio_archive_content_length' => array(
						'label'       => esc_html__( 'Portfolio Archive Content Display', 'Avada' ),
						'description' => esc_html__( 'Controls if the portfolio content displays an excerpt or full content for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_content_length',
						'default'     => 'excerpt',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'excerpt'      => esc_html__( 'Excerpt', 'Avada' ),
							'full_content' => esc_html__( 'Full Content', 'Avada' ),
						),
					),
					'portfolio_archive_excerpt_length' => array(
						'label'       => esc_html__( 'Portfolio Archive Excerpt Length', 'Avada' ),
						'description' => esc_html__( 'Controls the number of words in the excerpts for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_excerpt_length',
						'default'     => '10',
						'type'        => 'slider',
						'choices'     => array(
							'min'  => '0',
							'max'  => '500',
							'step' => '1',
						),
						'required'    => array(
							array(
								'setting'  => 'portfolio_archive_content_length',
								'operator' => '==',
								'value'    => 'Excerpt',
							),
						),
					),
					'portfolio_archive_strip_html_excerpt' => array(
						'label'       => esc_html__( 'Strip HTML from Excerpt', 'Avada' ),
						'description' => esc_html__( 'Turn on to strip HTML content from the excerpt for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_strip_html_excerpt',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_archive_title_display' => array(
						'label'       => esc_html__( 'Portfolio Archive Title Display', 'Avada' ),
						'description' => esc_html__( 'Controls what displays with the portfolio post title for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_title_display',
						'default'     => 'all',
						'type'        => 'select',
						'choices'     => array(
							'all'     => esc_html__( 'Title and Categories', 'Avada' ),
							'title'   => esc_html__( 'Only Title', 'Avada' ),
							'cats'    => esc_html__( 'Only Categories', 'Avada' ),
							'none'    => esc_html__( 'None', 'Avada' ),
						),
					),
					'portfolio_archive_text_alignment' => array(
						'label'       => esc_html__( 'Portfolio Archive Text Alignment', 'Avada' ),
						'description' => esc_html__( 'Controls the alignment of the portfolio title, categories and excerpt text when using the Portfolio Text layouts in portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_text_alignment',
						'default'     => 'left',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'left'     => esc_html__( 'Left', 'Avada' ),
							'center'   => esc_html__( 'Center', 'Avada' ),
							'right'    => esc_html__( 'Right', 'Avada' ),
						),
					),
					'portfolio_archive_layout_padding' => array(
						'label'       => esc_html__( 'Portfolio Archive Text Layout Padding', 'Avada' ),
						'description' => esc_html__( 'Controls the padding for the portfolio text layout when using boxed mode in portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_layout_padding',
						'choices'     => array(
							'top'     => true,
							'bottom'  => true,
							'left'    => true,
							'right'   => true,
							'units'   => array( 'px', '%' ),
						),
						'default'     => array(
							'top'     => '25px',
							'bottom'  => '25px',
							'left'    => '25px',
							'right'   => '25px',
						),
						'type'        => 'spacing',
						'required'    => array(
							array(
								'setting'  => 'portfolio_archive_text_layout',
								'operator' => '==',
								'value'    => 'boxed',
							),
						),
					),
					'portfolio_archive_pagination_type' => array(
						'label'       => esc_html__( 'Portfolio Archive Pagination Type', 'Avada' ),
						'description' => esc_html__( 'Controls the pagination type for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_pagination_type',
						'default'     => 'pagination',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'pagination'       => esc_html__( 'Pagination', 'Avada' ),
							'infinite_scroll'  => esc_html__( 'Infinite Scroll', 'Avada' ),
							'load_more_button' => esc_html__( 'Load More Button', 'Avada' ),
						),
					),
					'portfolio_archive_load_more_posts_button_bg_color' => array(
						'label'       => esc_html__( 'Load More Posts Button Color', 'Avada' ),
						'description' => esc_html__( 'Controls the background color of the load more button for ajax post loading for portfolio archive pages.', 'Avada' ),
						'id'          => 'portfolio_archive_load_more_posts_button_bg_color',
						'default'     => '#ebeaea',
						'type'        => 'color-alpha',
					),
					'portfolio_slug' => array(
						'label'       => esc_html__( 'Portfolio Slug', 'Avada' ),
						'description' => esc_html__( 'The slug name cannot be the same name as a page name or the layout will break. This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'Avada' ),
						'id'          => 'portfolio_slug',
						'default'     => 'portfolio-items',
						'type'        => 'text',
					),
				),
			),
			'portfolio_single_post_page_options_subsection' => array(
				'label'       => esc_html__( 'Portfolio Single Post', 'Avada' ),
				'description' => '',
				'id'          => 'portfolio_single_post_page_options_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'portfolio_pn_nav' => array(
						'label'       => esc_html__( 'Previous/Next Pagination', 'Avada' ),
						'description' => esc_html__( 'Turn on to display the previous/next post pagination for single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_pn_nav',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_featured_images' => array(
						'label'       => esc_html__( 'Featured Image / Video on Single Post Page', 'Avada' ),
						'description' => esc_html__( 'Turn on to display featured images and videos on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_featured_images',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_disable_first_featured_image' => array(
						'label'       => esc_html__( 'First Featured Image', 'Avada' ),
						'description' => esc_html__( 'Turn on to display the 1st featured image on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_disable_first_featured_image',
						'default'     => '1',
						'type'        => 'switch',
						'required'    => array(
							array(
								'setting'  => 'portfolio_featured_images',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'portfolio_featured_image_width' => array(
						'label'       => esc_html__( 'Featured Image Column Size', 'Avada' ),
						'description' => esc_html__( 'Controls if the featured image is half or full width on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_featured_image_width',
						'default'     => 'full',
						'type'        => 'radio-buttonset',
						'choices'     => array(
							'full' => esc_html__( 'Full Width', 'Avada' ),
							'half' => esc_html__( 'Half Width', 'Avada' ),
						),
						'required'    => array(
							array(
								'setting'  => 'portfolio_featured_images',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'portfolio_width_100' => array(
						'label'       => esc_html__( '100% Width Page', 'Avada' ),
						'description' => esc_html__( 'Turn on to display portfolio posts at 100% browser width according to the window size. Turn off to follow site width.', 'Avada' ),
						'id'          => 'portfolio_width_100',
						'default'     => '0',
						'type'        => 'switch',
					),
					'portfolio_project_desc_title' => array(
						'label'       => esc_html__( 'Project Description Title', 'Avada' ),
						'description' => esc_html__( 'Turn on to show the project description title on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_project_desc_title',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_project_details' => array(
						'label'       => esc_html__( 'Project Details', 'Avada' ),
						'description' => esc_html__( 'Turn on to show the project details title and content on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_project_details',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_link_icon_target' => array(
						'label'       => esc_html__( 'Open Post Links In New Window', 'Avada' ),
						'description' => esc_html__( 'Turn on to open the single post page, project url and copyright url links in a new window..', 'Avada' ),
						'id'          => 'portfolio_link_icon_target',
						'default'     => '0',
						'type'        => 'switch',
					),
					'portfolio_comments' => array(
						'label'       => esc_html__( 'Comments', 'Avada' ),
						'description' => esc_html__( 'Turn on to display comments on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_comments',
						'default'     => '0',
						'type'        => 'switch',
					),
					'portfolio_author' => array(
						'label'       => esc_html__( 'Author', 'Avada' ),
						'description' => esc_html__( 'Turn on to display the author name on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_author',
						'default'     => '0',
						'type'        => 'switch',
					),
					'portfolio_social_sharing_box' => array(
						'label'       => esc_html__( 'Social Sharing Box', 'Avada' ),
						'description' => esc_html__( 'Turn on to display the social sharing box on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_social_sharing_box',
						'default'     => '1',
						'type'        => 'switch',
					),
					'portfolio_related_posts' => array(
						'label'       => esc_html__( 'Related Projects', 'Avada' ),
						'description' => esc_html__( 'Turn on to display related projects on single portfolio posts.', 'Avada' ),
						'id'          => 'portfolio_related_posts',
						'default'     => '1',
						'type'        => 'switch',
					),
				),
			),
		),
	);

	return $sections;

}
