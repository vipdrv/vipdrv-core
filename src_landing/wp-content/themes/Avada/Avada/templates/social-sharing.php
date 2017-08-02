<?php
/**
 * Social-sharing template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

global $social_icons;

// $post_type is inherited from the avada_render_social_sharing() function.
$setting_name = ( 'post' == $post_type ) ? 'social_sharing_box' : $post_type . '_social_sharing_box';
$share_box = get_post_meta( get_the_ID(), 'pyre_share_box', true );

if ( ( Avada()->settings->get( $setting_name ) && 'no' !== $share_box ) || ( ! Avada()->settings->get( $setting_name ) && 'yes' === $share_box ) ) {

	$full_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

	$sharingbox_social_icon_options = array(
		'sharingbox'        => 'yes',
		'icon_colors'       => Avada()->settings->get( 'sharing_social_links_icon_color' ),
		'box_colors'        => Avada()->settings->get( 'sharing_social_links_box_color' ),
		'icon_boxed'        => Avada()->settings->get( 'sharing_social_links_boxed' ),
		'icon_boxed_radius' => Fusion_Sanitize::size( Avada()->settings->get( 'sharing_social_links_boxed_radius' ) ),
		'tooltip_placement' => Avada()->settings->get( 'sharing_social_links_tooltip_placement' ),
		'linktarget'        => Avada()->settings->get( 'social_icons_new' ),
		'title'             => wp_strip_all_tags( get_the_title( get_the_ID() ), true ),
		'description'       => Avada()->blog->get_content_stripped_and_excerpted( 55, get_the_content() ),
		'link'              => get_permalink( get_the_ID() ),
		'pinterest_image'   => ( $full_image ) ? $full_image[0] : '',
	);
	?>
	<div class="fusion-sharing-box fusion-single-sharing-box share-box">
		<h4><?php echo apply_filters( 'fusion_sharing_box_tagline', Avada()->settings->get( 'sharing_social_tagline' ) ); // WPCS: XSS ok. ?></h4>
		<?php echo Avada()->social_sharing->render_social_icons( $sharingbox_social_icon_options ); // WPCS: XSS ok. ?>
	</div>
	<?php
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
