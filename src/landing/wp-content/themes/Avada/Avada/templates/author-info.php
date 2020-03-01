<?php
/**
 * Author-info template.
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
?>
<?php
global $social_icons;

// Initialize needed variables.
$author             = get_user_by( 'id', get_query_var( 'author' ) );
$author_id          = $author->ID;
$author_name        = get_the_author_meta( 'display_name', $author_id );
$author_avatar      = get_avatar( get_the_author_meta( 'email', $author_id ), '82' );
$author_description = get_the_author_meta( 'description', $author_id );
$author_custom      = get_the_author_meta( 'author_custom', $author_id );

// If no description was added by user, add some default text and stats.
if ( empty( $author_description ) ) {
	$author_description  = esc_html__( 'This author has not yet filled in any details.', 'Avada' );
	$author_description .= '<br />' . sprintf( esc_html__( 'So far %1$s has created %2$s blog entries.', 'Avada' ), $author_name, count_user_posts( $author_id ) );
}
?>
<div class="fusion-author">
	<div class="fusion-author-avatar">
		<?php echo $author_avatar; // WPCS: XSS ok. ?>
	</div>
	<div class="fusion-author-info">
		<h3 class="fusion-author-title<?php echo ( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) && Avada()->settings->get( 'disable_rich_snippet_author' )  ) ? ' vcard' : ''; ?>">
			<?php printf(
				esc_html__( 'About %s', 'Avada' ),
				( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) && Avada()->settings->get( 'disable_rich_snippet_author' ) ) ? '<span class="fn">' . esc_attr( $author_name ) . '</span>' : esc_attr( $author_name )
			); ?>
			<?php // If user can edit his profile, offer a link for it. ?>
			<?php if ( current_user_can( 'edit_users' ) || get_current_user_id() == $author_id ) : ?>
				<span class="fusion-edit-profile">(<a href="<?php echo esc_url_raw( admin_url( 'profile.php?user_id=' . $author_id ) ); ?>"><?php esc_attr_e( 'Edit profile', 'Avada' ); ?></a>)</span>
			<?php endif; ?>
		</h3>
		<?php echo $author_description; // WPCS: XSS ok. ?>
	</div>

	<div style="clear:both;"></div>

	<div class="fusion-author-social clearfix">
		<div class="fusion-author-tagline">
			<?php if ( $author_custom ) : ?>
				<?php echo $author_custom; // WPCS: XSS ok. ?>
			<?php endif; ?>
		</div>

		<?php

		// Get the social icons for the author set on his profile page.
		$author_social_icon_options = array(
			'authorpage'        => 'yes',
			'author_id'         => $author_id,
			'position'          => 'author',
			'icon_colors'       => Avada()->settings->get( 'social_links_icon_color' ),
			'box_colors'        => Avada()->settings->get( 'social_links_box_color' ),
			'icon_boxed'        => Avada()->settings->get( 'social_links_boxed' ),
			'icon_boxed_radius' => Fusion_Sanitize::size( Avada()->settings->get( 'social_links_boxed_radius' ) ),
			'tooltip_placement' => Avada()->settings->get( 'social_links_tooltip_placement' ),
			'linktarget'        => Avada()->settings->get( 'social_icons_new' ),
		);

		echo Avada()->social_sharing->render_social_icons( $author_social_icon_options ); // WPCS: XSS ok.

		?>
	</div>
</div>
