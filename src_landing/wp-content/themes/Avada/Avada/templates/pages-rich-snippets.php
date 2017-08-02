<?php
/**
 * Rich snippets template.
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
<?php if ( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) ) : ?>

	<?php if ( $title_tag && Avada()->settings->get( 'disable_rich_snippet_title' ) ) : ?>
		<span class="entry-title rich-snippet-hidden">
			<?php echo get_the_title(); ?>
		</span>
	<?php endif; ?>

	<?php if ( $author_tag && Avada()->settings->get( 'disable_rich_snippet_author' ) ) : ?>
		<span class="vcard rich-snippet-hidden">
			<span class="fn">
				<?php the_author_posts_link(); ?>
			</span>
		</span>
	<?php endif; ?>

	<?php if ( $updated_tag && Avada()->settings->get( 'disable_rich_snippet_date' ) ) : ?>
		<span class="updated rich-snippet-hidden">
			<?php echo esc_attr( get_the_modified_time( 'c' ) ); ?>
		</span>
	<?php endif; ?>

<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
