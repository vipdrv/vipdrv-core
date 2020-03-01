<?php
/**
 * Handles the Events-Calendar implementation.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8.7
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handles the Events-Calendar implementation.
 */
class Avada_EventsCalendar {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'tribe_events_before_the_title', array( $this, 'before_the_title' ) );
		add_action( 'tribe_events_after_the_title', array( $this, 'after_the_title' ) );

		add_filter( 'tribe_events_mobile_breakpoint', array( $this, 'set_mobile_breakpoint' ) );
		add_action( 'tribe_events_bar_after_template', array( $this, 'add_clearfix' ) );

		add_filter( 'tribe_events_get_the_excerpt', array( $this, 'get_the_excerpt' ), 10, 2 );
	}

	/**
	 * Open the wrapper before the title.
	 *
	 * @access public
	 */
	public function before_the_title() {
		echo '<div class="fusion-events-before-title">';
	}

	/**
	 * Close the wrapper after the title.
	 *
	 * @access public
	 */
	public function after_the_title() {
		echo '</div>';
	}

	/**
	 * Removes arrows from the "previous" link.
	 *
	 * @access public
	 * @param string $anchor The HTML.
	 * @return string
	 */
	public function remove_arrow_from_prev_link( $anchor ) {
		return tribe_get_prev_event_link( '%title%' );
	}

	/**
	 * Removes arrows from the "next" link.
	 *
	 * @access public
	 * @param string $anchor The HTML.
	 * @return string
	 */
	public function remove_arrow_from_next_link( $anchor ) {
		return tribe_get_next_event_link( '%title%' );
	}

	/**
	 * Returns the mobile breakpoint.
	 *
	 * @access public
	 * @return int
	 */
	public function set_mobile_breakpoint() {
		return intval( Avada()->settings->get( 'content_break_point' ) );
	}

	/**
	 * Renders the title for single events.
	 *
	 * @access public
	 */
	public static function render_single_event_title() {
		$event_id = get_the_ID();
		?>
		<div class="fusion-events-single-title-content">
			<?php the_title( '<h2 class="tribe-events-single-event-title summary entry-title">', '</h2>' ); ?>

			<div class="tribe-events-schedule updated published tribe-clearfix">
				<?php echo tribe_events_event_schedule_details( $event_id, '<h3>', '</h3>' ); // WPCS: XSS ok. ?>
				<?php if ( tribe_get_cost() ) : ?>
					<span class="tribe-events-divider">|</span>
					<span class="tribe-events-cost"><?php echo tribe_get_cost( null, true ); // WPCS: XSS ok. ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Adds clearfix.
	 *
	 * @access public
	 */
	public function add_clearfix() {
		echo '<div class="clearfix"></div>';
	}

	/**
	 * Renders to correct excerpts on archive pages.
	 *
	 * @since 5.1.6
	 * @access public
	 * @param string $excerpt The post excerpt.
	 * @param object $post The post object.
	 * @return string The new excerpt.
	 */
	public function get_the_excerpt( $excerpt, $post ) {

		if ( 'tribe_events' === get_post_type() && is_archive() ) {
			return fusion_get_post_content( $post->ID, 'yes', apply_filters( 'excerpt_length', 55 ), true );
		}

		return $excerpt;
	}
}
