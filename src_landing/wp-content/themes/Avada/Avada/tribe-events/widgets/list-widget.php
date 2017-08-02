<?php
/**
 * Events List Widget Template
 * This is the template for the output of the events list widget.
 * All the items are turned on and off through the widget admin.
 * There is currently no default styling, which is needed.
 *
 * This view contains the filters required to create an effective events list widget view.
 *
 * You can recreate an ENTIRELY new events list widget view by doing a template override,
 * and placing a list-widget.php file in a tribe-events/widgets/ directory
 * within your theme directory, which will override the /views/widgets/list-widget.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters (TO-DO)
 *
 * @return string
 *
 * @package TribeEventsCalendar
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_plural = tribe_get_event_label_plural();

$posts = tribe_get_list_widget_events();
?>

<?php if ( $posts ) : // Check if any event posts are found. ?>
	<ol class="hfeed vcalendar">
		<?php // Setup the post data for each event. ?>
		<?php foreach ( $posts as $post ) : ?>
			<?php setup_postdata( $post ); ?>
			<li class="tribe-events-list-widget-events <?php tribe_events_event_classes() ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php echo tribe_event_featured_image( get_the_ID(), 'recent-works-thumbnail', false ); // WPCS: XSS ok. ?>
				<?php endif; ?>

				<div class="fusion-tribe-events-list-content">

					<?php do_action( 'tribe_events_list_widget_before_the_event_title' ); ?>
					<?php // Event Title. ?>
					<h4 class="entry-title summary">
						<a href="<?php echo esc_url( tribe_get_event_link() ); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h4>

					<?php do_action( 'tribe_events_list_widget_after_the_event_title' ); ?>
					<?php do_action( 'tribe_events_list_widget_before_the_meta' ) ?>

					<div class="duration">
						<?php echo tribe_events_event_schedule_details(); ?>
					</div>

					<?php do_action( 'tribe_events_list_widget_after_the_meta' ) ?>

				</div>
			</li>
		<?php endforeach; ?>
	</ol>

	<p class="tribe-events-widget-link">
		<a href="<?php echo esc_url( tribe_get_events_link() ); ?>" rel="bookmark"><?php printf( __( 'View All %s', 'the-events-calendar' ), $events_label_plural ); ?></a>
	</p>

<?php else : // No events were found. ?>
	<p><?php printf( __( 'There are no upcoming %s at this time.', 'the-events-calendar' ), strtolower( $events_label_plural ) ); // WPCS: XSS ok. ?></p>
<?php endif;

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
