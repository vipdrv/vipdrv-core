<?php
/**
 * Converts shortcode names from Fusion Core
 * to a format that Fusion Builder will accept.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Converts shortcode names from Fusion Core
 * to a format that Fusion Builder will accept.
 */
class Fusion_Builder_Migrate {

	/**
	 * Slug for the admin page.
	 *
	 * @static
	 * @access public
	 * @since 5.0.0
	 * @var string
	 */
	public static $slug = 'fusion_builder_migrate';

	/**
	 * The option name that will be used to store the array of unconverted posts.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var string
	 */
	private static $option_name = 'fusion_core_unconverted_posts';

	/**
	 * The last version Avada was running on.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var string
	 */
	private static $avada_database_version = '';

	/**
	 * The Avada Theme Options option name.
	 *
	 * @static
	 * @access private
	 * @since 5.1.0
	 * @var string
	 */
	private static $avada_option_name = '';

	/**
	 * The value of the $option_name setting.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var array
	 */
	private static $option = array();

	/**
	 * Whether we want to migrate posts or revert a migrations.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var bool
	 */
	private static $revert = false;

	/**
	 * Number of posts to be checked/converted per cycle.
	 *
	 * @static
	 * @access private
	 * @since 5.0.2
	 * @var integer
	 */
	private static $posts_per_page = 0;

	/**
	 * Number of posts that need converted.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var integer
	 */
	private static $posts_to_convert = 0;

	/**
	 * Number of slides that need converted.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var integer
	 */
	private static $slides_to_convert = 0;


	/**
	 * Number of widgets that need converted.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var integer
	 */
	private static $widgets_to_convert = 0;

	/**
	 * Number of theme-options that need converted.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var integer
	 */
	private static $theme_options_to_convert = 1;

	/**
	 * The post types that can have Fusion-Builder shortcodes
	 * from previous versions.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var array
	 */
	private $post_types = array();

	/**
	 * The current post-type.
	 * We're going though all the valid post-types during the migration.
	 * This var lets us know which post-type we're currently processing.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var string
	 */
	private $current_post_type = 'page';

	/**
	 * Total number of available posts, per post-type.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var array
	 */
	private $total_posts = array();

	/**
	 * Total number of posts available from all post-types.
	 * This is similar to $total_posts, the difference being that
	 * we're combining counts for all post-statuses here.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var array
	 */
	private $total_posts_count = array();

	/**
	 * From which post we'll start our query for this post-type.
	 * Please note this does not refer to a post-ID, but the query's offset.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var int
	 */
	private $from_offset = 0;

	/**
	 * To which post we'll start our query for this post-type.
	 * Please note this does not refer to a post-ID, but the query's offset.
	 * This is calculated by adding get_posts_per_page() to $from
	 * with a cap on the posts number actually available.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var int
	 */
	private $to_offset = 0;

	/**
	 * The posts to process to this step.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var mixed
	 */
	private $posts;

	/**
	 * Page title of migration page.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var string
	 */
	private $migration_page_title = '';

	/**
	 * Self enclosing shortcodes.
	 *
	 * @static
	 * @access  private
	 * @since 5.0.0
	 * @var  array
	 */
	private static $self_enclosing_shortcodes = array();

	/**
	 * Shortcodes that need conversion.
	 *
	 * @static
	 * @access  private
	 * @since 5.0.0
	 * @var  array
	 */
	private static $shortcodes_for_conversion = array();

	/**
	 * Columns that need conversion.
	 *
	 * @static
	 * @access  private
	 * @since 5.0.0
	 * @var  array
	 */
	private static $columns_for_conversion = array();

	/**
	 * From->To names for shortcodes
	 *
	 * @static
	 * @access  private
	 * @since 5.0.0
	 * @var  array
	 */
	private static $from_to_shortcode_names = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param string $avada_version     The Avada Theme Version.
	 * @param string $avada_option_name The option-name.
	 */
	public function __construct( $avada_version, $avada_option_name ) {

		if ( isset( $_GET['revert'] ) && '1' == $_GET['revert'] ) {
			self::$revert = true;
		}

		self::$avada_database_version = $avada_version;
		self::$avada_option_name = $avada_option_name;

		if ( self::needs_migration() ) {

			add_action( 'save_post', array( $this, 'save_post_actions' ), 999, 2 );

			// Initialize the object.
			add_action( 'init', array( $this, 'init' ), 20 );

			// Adds the admin page.
			add_action( 'admin_init', array( $this, 'render_migration_page' ) );
		}
	}

	/**
	 * Initializes the object.
	 *
	 * @access public
	 * @since 5.0.0
	 * @return void
	 */
	public function init() {
		$this->set_posts_per_page();
		$this->set_self_enclosing_shortcodes();
		$this->set_shortcodes_for_conversion();
		$this->set_columns_for_conversion();
		$this->remove_conflicting_shortcodes();
		$this->set_from_to_shortcode_names();

		$this->migration_page_title = esc_html__( 'Avada Shortcode Conversion', 'Avada' );
		if ( isset( $_GET['revert'] ) && '1' == $_GET['revert'] ) {
			$this->migration_page_title = esc_html__( 'Revert Avada Shortcode Conversion', 'Avada' );
		}

		// Set the $option value.
		self::$option = get_option( self::$option_name, array() );

		// Set number of posts that need conversion.
		self::$posts_to_convert = get_option( self::$option_name . '_number' );

		$this->post_types();
		$this->current_post_type();

		// Early exit if no step is defined.
		if ( ! isset( $_GET['step'] ) ) {
			return;
		}

		if ( 'query' === $_GET['step'] ) {
			$this->total_posts();
			$this->from_offset();
			$this->to_offset();
			$this->get_posts();
			$this->save_post_ids();
		} elseif ( 'convert' === $_GET['step'] ) {
			// Set number of slides that need conversion.
			$slides = wp_count_posts( 'slide' );
			self::$slides_to_convert = 0;
			if ( $slides ) {
				$published = ( isset( $slides->publish ) ) ? $slides->publish : 0;
				$pending = ( isset( $slides->pending ) ) ? $slides->pending : 0;
				$draft = ( isset( $slides->draft ) ) ? $slides->draft : 0;
				$future = ( isset( $slides->future ) ) ? $slides->future : 0;
				$private = ( isset( $slides->private ) ) ? $slides->private : 0;

				self::$slides_to_convert = $published + $pending + $draft + $future + $private + 2;
			}

			// Make widgets count as one batch of posts.
			self::$widgets_to_convert = $this->get_posts_per_page();

			if ( ! isset( $_GET['type'] ) ) {
				return;
			}

			if ( 'posts' === $_GET['type'] ) {
				$this->convert_posts();
			} elseif ( 'slides' === $_GET['type'] ) {
				$this->convert_shortcode_names_in_fusion_slider();
				$this->convert_shortcode_names_in_revslider();
				$this->convert_shortcode_names_in_layerslider();
			} elseif ( 'widgets' === $_GET['type'] ) {
				$this->convert_shortcode_names_in_widgets();
			} elseif ( 'theme_options' === $_GET['type'] ) {
				$this->convert_shortcode_names_in_theme_options();
			}
		} // End if().
	}

	/**
	 * Determines if we should run the migration or not.
	 *
	 * @static
	 * @access public
	 * @since 5.0.0
	 * @return bool
	 */
	public static function needs_migration() {

		// Convert later was clicked on the splash screen.
		if ( is_admin() && isset( $_GET[ self::$slug ] ) && '0' == $_GET[ self::$slug ] && isset( $_GET['migrate_later'] ) && '1' == $_GET['migrate_later'] ) {
			update_option( self::$option_name . '_converted', '1' );
			return false;
		}

		// Trigger migration on url visit.
		if ( is_admin() && isset( $_GET[ self::$slug ] ) && '1' == $_GET[ self::$slug ] ) {
			return true;
		}

		// Make sure that $avada_database_version is defined.
		if ( empty( self::$avada_database_version ) ) {
			self::$avada_database_version = get_option( 'avada_version', '' );
		}
		if ( is_array( self::$avada_database_version ) ) {
			self::$avada_database_version = end( self::$avada_database_version );
		}

		// If the 'avada_version' setting is empty, this is a fresh installation
		// so no migration is needed.
		if ( ! self::$avada_database_version || empty( self::$avada_database_version ) ) {
			return false;
		}

		if ( version_compare( self::$avada_database_version, '5.0.0', '<' ) && ! get_option( self::$option_name . '_converted' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Redirects to the next step.
	 *
	 * @access private
	 * @since 5.0.0
	 * @param bool $advance If true, redirection completes for slides and widgets.
	 * @return void;
	 */
	private function next_step_redirection( $advance = false ) {
		// Set the post-type if not already set.
		if ( ! isset( $_GET['step'] ) || ! in_array( wp_unslash( $_GET['step'] ), array( 'query', 'convert', 'done' ) ) ) {
			return;
		}

		$url = false;
		$query_finished = false;
		$posts_per_page = $this->get_posts_per_page();

		if ( 'query' === $_GET['step'] ) {
			$post_type = $this->current_post_type;
			// TO becomes FROM.
			$from = $this->to_offset;
			// If the "from" value is equal or greater than the max number of posts
			// available for that post type, then move on to the next post-type.
			if ( $from >= $this->total_posts_count[ $post_type ] ) {
				$current_post_type_key = array_search( $post_type, $this->post_types );
				if ( isset( $this->post_types[ $current_post_type_key + 1 ] ) ) {
					$post_type = $this->post_types[ $current_post_type_key + 1 ];
					$from = 0;
				} else {
					$query_finished = true;
					update_option( self::$option_name . '_number', absint( count( self::$option ) ) );
				}
			}
			// Calculate the TO var.
			$to = $from + $posts_per_page;
			if ( $to >= $this->total_posts_count[ $post_type ] ) {
				$to = $this->total_posts_count[ $post_type ];
			}

			// Calculate the redirection URL.
			$revert = ( self::$revert ) ? '&revert=1' : '';
			if ( ! $query_finished ) {
				$url = admin_url( 'index.php?' . self::$slug . '=1&step=query&from=' . $from . '&to=' . $to . '&post_type=' . $post_type . $revert );
			} else {
				$url = admin_url( 'index.php?' . self::$slug . '=1&step=convert&type=posts&size=' . $posts_per_page . $revert );
			}
		} elseif ( 'convert' === $_GET['step'] ) {
			$revert = ( self::$revert ) ? '&revert=1' : '';
			if ( isset( $_GET['type'] ) && 'posts' === $_GET['type'] ) {
				if ( empty( self::$option ) ) {
					// It is now safe to remove the option.
					delete_option( self::$option_name );
					delete_option( self::$option_name . '_number' );

					$url = admin_url( 'index.php?' . self::$slug . '=1&step=convert&type=slides' . $revert );
				} else {
					$url = admin_url( 'index.php?' . self::$slug . '=1&step=convert&type=posts&size=' . $posts_per_page . $revert );
				}
			} elseif ( 'slides' === $_GET['type'] && $advance ) {
				$url = admin_url( 'index.php?' . self::$slug . '=1&step=convert&type=widgets' . $revert );
			} elseif ( 'widgets' === $_GET['type'] && $advance ) {
				$url = admin_url( 'index.php?' . self::$slug . '=1&step=convert&type=theme_options' . $revert );
			} elseif ( 'theme_options' === $_GET['type'] && $advance ) {
				$url = admin_url( 'index.php?' . self::$slug . '=1&step=done' . $revert );
			}
		} elseif ( 'done' === $_GET['step'] ) {
			// Conversion is finished.
			update_option( self::$option_name . '_converted', '1' );

			// If builder is disabled, set post types in settings to prevent defaults being used.
			$builder_status = get_option( 'avada_disable_builder', '1' );
			if ( isset( $builder_status ) && '0' === $builder_status ) {
				$builder_settings = array(
					'post_types' => ' ',
				);
				update_option( 'fusion_builder_settings', $builder_settings );
			}
		} // End if().

		if ( $url ) {
			header( "Refresh:0; url=$url" );
		}
	}


	/**
	 * Sets the post-types we'll be checking against.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function post_types() {
		$post_types = apply_filters( 'fusion_builder_shortcode_migration_post_types', array(
			'page',
			'post',
			'avada_faq',
			'avada_portfolio',
			'product',
			'tribe_events',
		) );

		foreach ( $post_types as $key => $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				unset( $post_types[ $key ] );
			}
		}

		$this->post_types = $post_types;
	}

	/**
	 * Get the current post-type.
	 * Sets the object's $current_post_type.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function current_post_type() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}
		if ( $_GET && isset( $_GET['post_type'] ) ) {
			$this->current_post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) );
		}
	}

	/**
	 * Gets the amount of posts per page for each step.
	 *
	 * @access private
	 * @since 5.0.2
	 * @return int
	 */
	private function get_posts_per_page() {
		return self::$posts_per_page;
	}

	/**
	 * Calculates and set the amount of posts per page for each step.
	 *
	 * @access private
	 * @since 5.0.2
	 * @return void
	 */
	private function set_posts_per_page() {
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
			if ( '0' === $max_execution_time ) {
				$max_execution_time = 300;
			}
			$posts_per_page = round( $max_execution_time / 4 );
			self::$posts_per_page = min( 75, max( 15, $posts_per_page ) );
		} else {
			self::$posts_per_page = 15;
		}
	}

	/**
	 * Get the total number of steps.
	 * Sets the object's $total_posts.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function total_posts() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}
		// Set the $total_posts array.
		foreach ( $this->post_types as $post_type ) {
			$this->total_posts[ $post_type ] = false;
			if ( post_type_exists( $post_type ) ) {
				$posts_count = wp_count_posts( $post_type );
				$this->total_posts[ $post_type ] = $posts_count;
			}
		}

		// Calculate the total number of posts available
		// and set the $total_posts_count.
		foreach ( $this->total_posts as $post_type => $post_statuses ) {
			// Make sure it's not empty or false.
			if ( $post_statuses ) {
				// Convert to array.
				$post_statuses = (array) $post_statuses;
				// Make sure the entry exists in the array.
				if ( ! isset( $this->total_posts_count[ $post_type ] ) ) {
					$this->total_posts_count[ $post_type ] = 0;
				}
				// Add count for all post statuses.
				foreach ( $post_statuses as $post_status ) {
					$this->total_posts_count[ $post_type ] += $post_status;
				}
			}
		}
	}

	/**
	 * Set the object's $from_offset.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function from_offset() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}
		// No need to proceed if we don't have a post-type defined.
		if ( ! isset( $_GET['post_type'] ) ) {
			return;
		}

		if ( isset( $_GET['from'] ) ) {
			$this->from_offset = absint( $_GET['from'] );
		}
		// Get the post-count for this post-type.
		// We'll use this to add a cap.
		$post_type_count = isset( $this->total_posts_count[ $this->current_post_type ] ) ? $this->total_posts_count[ $this->current_post_type ] : 0;

		if ( $this->from_offset > absint( $post_type_count ) ) {
			$this->from_offset = absint( $post_type_count );
		}
	}

	/**
	 * Set the object's $to_offset.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function to_offset() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}
		// No need to proceed if we don't have a post-type defined.
		if ( ! isset( $_GET['post_type'] ) ) {
			return;
		}

		if ( isset( $_GET['to'] ) ) {
			$this->to_offset = absint( $_GET['to'] );
		}
		// Get the post-count for this post-type.
		// We'll use this to add a cap.
		$post_type_count = isset( $this->total_posts_count[ $this->current_post_type ] ) ? $this->total_posts_count[ $this->current_post_type ] : 0;

		if ( $this->to_offset > absint( $post_type_count ) ) {
			$this->to_offset = absint( $post_type_count );
		}
	}

	/**
	 * Queries the posts.
	 * Sets the object's $posts.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_posts() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}

		$args = array(
			'suppress_filters' => 1,
			'posts_per_page'   => $this->get_posts_per_page(),
			'offset'           => $this->from_offset,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => $this->current_post_type,
			'post_status'      => 'any',
		);

		$this->posts = fusion_cached_get_posts( $args );
	}

	/**
	 * Finds posts that need converting.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function save_post_ids() {
		if ( ! $this->posts ) {
			return;
		}
		$convert_post_ids = array();
		foreach ( $this->posts as $post ) {
			if ( self::$revert ) {
				$backed_up_content = get_post_meta( $post->ID, 'fusion_builder_content_backup', true );
				if ( false !== strpos( $backed_up_content, 'fusion_' ) ) {
					$convert_post_ids[] = $post->ID;
				}
			} else {
				$page_template = get_page_template_slug( $post->ID );
				$shortcodes_to_check_for = array_merge( self::$columns_for_conversion, self::$shortcodes_for_conversion );
				foreach ( $shortcodes_to_check_for as $sc ) {
					if ( false !== strpos( $post->post_content, $sc ) || false !== strpos( $post->post_excerpt, $sc ) || 'faqs.php' === $page_template || false !== strpos( $page_template, 'portfolio' ) ) {
						$convert_post_ids[] = $post->ID;
					}
				}
			}
		}
		if ( ! empty( $convert_post_ids ) ) {
			$this->add_posts_to_setting( $convert_post_ids );
		}
	}

	/**
	 * Adds posts that need conversion to the db option.
	 *
	 * @access private
	 * @since 5.0.0
	 * @param array $ids The post IDs.
	 * @return void
	 */
	private function add_posts_to_setting( $ids ) {
		foreach ( $ids as $id ) {
			if ( ! in_array( $id, self::$option, true ) ) {
				self::$option[] = $id;
			}
		}
		update_option( self::$option_name, self::$option );
	}

	/**
	 * Removes post-IDs from the array of posts that need conversion.
	 *
	 * @access private
	 * @since 5.0.0
	 * @param array $ids The post IDs.
	 * @return void
	 */
	private function remove_posts_from_setting( $ids ) {
		foreach ( $ids as $id ) {
			$key = array_search( $id, self::$option );
			if ( false !== $key ) {
				unset( $option[ $key ] );
			}
		}
		update_option( self::$option_name, self::$option );
	}

	/**
	 * Migrates the content.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_posts() {
		// No need to proceed if we're not in the migration page.
		if ( ! $_GET || ! isset( $_GET[ self::$slug ] ) || '1' !== $_GET[ self::$slug ] ) {
			return;
		}

		// Get a slice of the array.
		$posts_per_page = $this->get_posts_per_page();
		$slice = array_slice( self::$option, 0, $posts_per_page );

		foreach ( $slice as $post_id ) {

			if ( self::$revert ) {
				// Revert.
				$this->revert_post_contents( $post_id );
			} else {
				// Convert.
				$this->convert_post_contents( $post_id );
			}

			// Remove item from the array.
			$key = array_search( $post_id, self::$option );
			unset( self::$option[ $key ] );
		}

		// Update the option, removing unset values.
		update_option( self::$option_name, self::$option );
	}

	/**
	 * Renders the migration page.
	 *
	 * @access public
	 * @since 5.0.0
	 * @return void
	 */
	public function render_migration_page() {
		ob_start();
		$this->get_migration_page_template();
		exit;
	}

	/**
	 * The migration-page template.
	 *
	 * @access public
	 * @since 5.0.0
	 * @return void
	 */
	public function get_migration_page_template() {
		global $shortcode_tags;
		// Add script to redirect to next step.
		$this->next_step_redirection();
		?>
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title ><?php echo esc_attr( $this->migration_page_title ) ?></title >
				<?php do_action( 'admin_print_styles' ); ?>
				<?php do_action( 'admin_head' ); ?>
				<style>
					<?php $this->get_migration_page_styles(); ?>
				</style>
			</head>
			<?php $version = Avada::get_normalized_theme_version(); ?>
			<body class="avada-setup wp-core-ui">
				<div class="update-content">
					<div class="avada-logo">
						<img src="<?php echo esc_url_raw( Avada::$template_dir_url ); ?>/assets/images/logo_migration.png" alt="<?php esc_html_e( 'Avada Logo', 'Avada' ); ?>" width="453" height="95">
						<span class="avada-version">
							<span class="avada-version-inner"><?php echo esc_attr( $version ); ?></span>
						</span>
					</div>
					<div class="avada-content-wrapper">
						<div class="avada-welcome-msg">
							<?php $this->get_migration_page_welcome_message(); ?>
						</div>
						<div class="avada-setup-content">
							<?php $this->get_pre_action_heading_content(); ?>

							<h1 class="avada-current-action-heading" style="font-size:1.3em;">
								<?php $this->get_migration_page_current_action(); ?>
							</h1>
							<div class="avada-current-action-desc">
								<?php $this->get_migration_page_current_action_desc(); ?>
							</div>
							<div class="avada-progress-bar">
								<?php $this->get_migration_page_progress_bar(); ?>
							</div>
							<div class="avada-more-info">
								<?php $this->get_migration_page_more_info(); ?>
							</div>
							<div class="avada-trigger-buttons">
								<?php $this->get_migration_page_buttons(); ?>
							</div>
						</div>
						<div class="avada-footer"><a class="avada-themefusion-link" href="https://theme-fusion.com" target="_blank" rel="noopener noreferrer" title="ThemeFusion">ThemeFusion</a><span class="avada-separator">|</span><?php printf( esc_html__( 'Created with %s', 'Avada' ), '<span class="avada-heart"></span>' ); ?></div>
					</div>
				</div>
			</body>
		</html>
		<?php
	}

	/**
	 * Styles for the migration page.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_styles() {
		?>
		.avada-setup {
			padding: 2% 20%;
			background-color: #f2f2f2;
			font-family:'Roboto', sans-serif;
			font-weight:300;
			font-size: 1.1em;
		}
		.update-content {
			max-width: 1150px;
			margin: auto;
		}
		.avada-logo {
			margin-bottom: 25px;
			text-align: center;
		}
		.avada-logo img {
			max-width: 226.5px;
			height: auto;
			vertical-align: bottom;
		}
		.avada-version {
			vertical-align: bottom;
		}
		.avada-version-inner {
			display: inline-block;
			margin-left: 20px;
			padding: 5px 10px;
			background-color: #a0ce4e;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			color: #fff;
		}
		.avada-content-wrapper {
			-webkit-border-radius: 3px;
			border-radius: 3px;
			-webkit-box-shadow: 1px 1px 3px 1px rgba(0,0,0,.2);
			box-shadow: 1px 1px 3px 1px rgba(0,0,0,.2);
		}
		.avada-welcome-msg {
			padding: 25px 35px;
			line-height: 1.6em;
			background-color: #a0ce4e;
			color: #fff;
			font-style: italic;
			text-align: center;
		}
		.avada-migration-link {
			color: #fff;
		}
		.avada-setup-content {
			padding: 30px 10%;
			background: #fff;
		}

		.fusion-builder-migration-progress-bar {
			display: block;
			height: 20px;
			width: 100%;
			margin: 1em 0;
		}
		.fusion-builder-migration-progress-bar[value] {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			border: none;
			color: blue;
		}
		.fusion-builder-migration-progress-bar[value]::-webkit-progress-bar {
			background-color: #ccc;
			border-radius: 2px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25) inset;
		}
		.fusion-builder-migration-progress-bar[value]::-webkit-progress-value {
			background-color: #A0CE4E;
			-webkit-animation: animate-stripes 5s linear infinite;
			animation: animate-stripes 5s linear infinite;
		}
		progress[value]::-moz-progress-bar {
			background-color: #A0CE4E;
		}
		.fusion-builder-migration-progress-bar::-webkit-progress-bar,
		.fusion-builder-migration-progress-bar,
		.fusion-builder-migration-progress-bar[value] {
			-webkit-animation: animate-stripes 5s linear infinite;
			animation: animate-stripes 5s linear infinite;
		}
		@-webkit-keyframes animate-stripes {
			100% { background-position: -100px 0px; }
		}
		@keyframes animate-stripes {
			100% { background-position: -100px 0px; }
		}

		.tasks-list {
			padding: 0;
			list-style: none;
		}
		.tasks-list li .content {
			color: #333;
		}
		.tasks-list li {
			color: #f2f2f2;
		}
		.tasks-list li:before {
			vertical-align: middle;
			font-family: dashicons;
			content: "\f147";
			font-size: 1.4em
		}
		.tasks-list li.done {
			color: #a0ce4e;
		}
		.tasks-list li.doing {
			color: #000;
		}
		.avada-button {
			display: inline-block;
			margin: 1.3em 0 0 0;
			padding: 1em 2em;
			background-color: #a0ce4e;
			color: #fff;
			text-decoration: none;
			-webkit-border-radius: 3px;
			border-radius: 3px;
			-webkit-transition: all 0.3s;
			-moz-transition: all 0.3s;
			-ms-transition: all 0.3s;
			transition: all 0.3s;
		}
		.avada-button.needs-update {
			background-color: #ef5350;
		}
		.avada-button:hover {
			background-color: #96c346;
		}
		.avada-button.needs-update:hover {
			background-color: #f44336;
		}
		.deny-conversion {
			background-color: #333333;
		}
		.deny-conversion:hover {
			background-color: #555555;
		}
		.avada-footer {
			padding: 23px 35px;
			background: #f2f2f2;
			font-size: 0.8em;
			text-align: right;
		}
		.avada-themefusion-link {
			color: #000;
			text-decoration: none;
		}
		.avada-separator {
			padding: 0 10px;
		}
		.avada-heart {
			padding-left: 5px;
			vertical-align: middle;
		}
		.avada-heart:after {
			font-family: dashicons;
			content: "\f487";
		}

		.avada-overall-progress {
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
			-weblit-justify-content: space-between;
			justify-content: space-between;
			margin-bottom: 30px;
		}
		.avada-progress-badge {
			height: 7px;
			width: 33%;
			background-color: #F2F2F2;
		}
		.avada-progress-badge.avada-filled {
			background-color: #A0CE4E;
		}
		<?php
	}

	/**
	 * The welcome message for the migration page.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_welcome_message() {
		if ( ! self::$revert ) {
			printf( esc_html__( 'Avada 5.0 is an amazing update with new features, improvements and our brand new Fusion Builder. To enjoy Avada 5.0, conversion steps need to be performed. Please see below. Thank you for choosing Avada!', 'Avada' ),  esc_attr( Avada()->get_theme_version() ) );
		} else {
			esc_html_e( 'This is the reversion process. Please see below for further information.', 'Avada' );
		}
	}

	/**
	 * Renders pre action heading content.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_pre_action_heading_content() {
		$query_class   = '';
		$convert_class = '';
		$done_class    = '';
		if ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) {
			$query_class = ' avada-filled';
		} elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) {
			$query_class   = ' avada-filled';
			$convert_class = ' avada-filled';
		} elseif ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) {
			$query_class   = ' avada-filled';
			$convert_class = ' avada-filled';
			$done_class    = ' avada-filled';
		}
		?>
		<div class="avada-overall-progress">
			<div class="avada-progress-badge<?php echo esc_attr( $query_class ); ?>"></div>
			<div class="avada-progress-badge<?php echo esc_attr( $convert_class ); ?>"></div>
			<div class="avada-progress-badge<?php echo esc_attr( $done_class ); ?>"></div>
		</div>
		<?php
	}

	/**
	 * The current action message.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_current_action() {
		if ( ! self::$revert ) {
			if ( ! isset( $_GET['step'] ) ) {
				esc_attr_e( 'IMPORTANT: Shortcode Conversion For Fusion Builder', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) {
				esc_attr_e( 'Collect IDs of posts that need to be converted', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) {
				esc_attr_e( 'Convert posts, slides, widgets and theme-options to new shortcode syntax', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) {
				esc_attr_e( 'Congratulations! The conversion finished successfully!', 'Avada' );
			}
		} else {
			if ( ! isset( $_GET['step'] ) ) {
				esc_attr_e( 'IMPORTANT: Revert Shortcode Conversion For Fusion Builder', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) {
				esc_attr_e( 'Collect IDs of posts that need to be reverted', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) {
				esc_attr_e( 'Revert posts, slides, widgets and theme-options that were previously converted', 'Avada' );
			} elseif ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) {
				esc_attr_e( 'Congratulations! The reversion finished successfully!', 'Avada' );
			}
		}
	}

	/**
	 * The description of the current action.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_current_action_desc() {
		?>
		<?php if ( ! isset( $_GET['step'] ) ) : ?>
			<?php if ( ! self::$revert ) : ?>
				<p><?php esc_html_e( 'Our newly built Fusion Builder is amazing and up to 5x faster. It needs to convert your old shortcodes to the new syntax. This will ensure all shortcodes use unique names, so there will be no conflicts with other plugins.', 'Avada' ); ?></p>
				<p><?php esc_html_e( 'Avada will search through your posts and pages and collect IDs of all pages using the old shortcodes and convert them to our new syntax. A backup is created of those pages and posts, to ensure all your data is fully secure.', 'Avada' ); ?></p>
				<p><strong><?php esc_html_e( 'The process can take time, please be patient during conversion and DO NOT CLOSE THIS SCREEN!', 'Avada' ); ?></strong></p>
				<p><?php printf( esc_html__( 'If the migration is not performed, you won\'t be able to use Avada %s unless you manually trigger the conversion at a later time through the WP admin area.', 'Avada' ), esc_attr( Avada()->get_normalized_theme_version() ) ); ?></p>
				<p><?php printf( esc_attr__( 'If you don\'t want your pages converted then please delete the new Avada folder and copy the old Avada folder to your server. If you did not backup your previous Avada theme folder, you can %s.', 'Avada' ), '<a href="https://theme-fusion.com/forums/topic/downloading-avada-4-0-3/" target="_blank">download Avada 4.0.3 here</a>' ); ?></p>
				<p><strong><?php esc_html_e( 'We recommend doing a full database backup before proceeding with conversion.', 'Avada' ); ?></strong></p>
				<p><?php printf( esc_html__( 'By pressing the "Start Conversion" button below, you confirm that Avada should convert your posts and pages to the new shortcode syntax.', 'Avada' ), esc_attr( Avada()->get_theme_version() ) ); ?></p>
				<p><form><label><input id="confirm-reading" type="checkbox" name="confirm_reading" value="confirm_reading"><?php esc_html_e( 'I have read the above.', 'Avada' ); ?></label></form></p>
			<?php else : ?>
				<p><?php esc_html_e( 'You have already converted your shortcodes to the new Avada 5.0 structure, but have chosen to revert back to their previous state before you installed Avada 5.0. This process does not downgrade you to the previous version of Avada.', 'Avada' ); ?></p>
				<p><?php printf( esc_attr__( 'Once reversion is done, you can load a previous version of Avada onto your server. You can download %s here if you do not have a backup of your previous theme.', 'Avada' ), '<a href="https://theme-fusion.com/forums/topic/downloading-avada-4-0-3/">' . esc_attr__( 'Avada 4.0.3', 'Avada' ) . '</a>' ); ?></p>
				<p><strong><?php esc_html_e( 'The process can take time, please be patient during reversion and DO NOT CLOSE THIS SCREEN!', 'Avada' ); ?></strong></p>
				<p><?php printf( esc_html__( 'By pressing the "Start Reversion" button below, you confirm that Avada should revert your posts and pages to the previous syntax.', 'Avada' ), esc_attr( Avada()->get_theme_version() ) ); ?></p>
				<p><form><label><input id="confirm-reading" type="checkbox" name="confirm_reading" value="confirm_reading"><?php esc_html_e( 'I have read the above.', 'Avada' ); ?></label></form></p>
			<?php endif; ?>
		<?php elseif ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) : ?>
			<p>
			<?php printf(
				esc_attr__( 'Currently scanning posts of post type "%1$s" (items %2$s to %3$s of %4$s total).', 'Avada' ),
				esc_attr( $this->current_post_type ),
				absint( $this->from_offset ),
				absint( $this->to_offset ),
				absint( $this->total_posts_count[ $this->current_post_type ] )
			); ?>
			</p>
			<?php if ( ! self::$revert ) : ?>
				<p><?php printf( esc_attr__( 'Posts that need to be converted: %s', 'Avada' ), absint( count( self::$option ) ) ); ?></p>
			<?php else : ?>
				<p><?php printf( esc_attr__( 'Posts that need to be reverted: %s', 'Avada' ), absint( count( self::$option ) ) ); ?></p>
			<?php endif; ?>
		<?php elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) : ?>
			<?php
			$posts_per_page = $this->get_posts_per_page();
			if ( isset( $_GET['type'] ) && 'posts' === $_GET['type'] ) {
				$still_to_convert = count( self::$option ) + $posts_per_page + self::$slides_to_convert + self::$widgets_to_convert;
			} elseif ( isset( $_GET['type'] ) && 'slides' === $_GET['type'] ) {
				$still_to_convert = self::$slides_to_convert + self::$widgets_to_convert + self::$theme_options_to_convert;
			} elseif ( isset( $_GET['type'] ) && 'widgets' === $_GET['type'] ) {
				$still_to_convert = self::$widgets_to_convert + self::$theme_options_to_convert;
			} else {
				$still_to_convert = self::$theme_options_to_convert;
			}
			?>
			<?php if ( ! self::$revert ) : ?>
				<?php printf( esc_attr__( 'Remaining items to convert: %s', 'Avada' ), (int) $still_to_convert ); ?>
			<?php else : ?>
				<?php printf( esc_attr__( 'Remaining items to revert: %s', 'Avada' ), (int) $still_to_convert ); ?>
			<?php endif; ?>
		<?php elseif ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) : ?>
			<?php if ( ! self::$revert ) : ?>
				<?php esc_attr_e( 'All needed posts have been converted to the new Fusion Builder syntax. You can now update Fusion Core and install Fusion Builder plugin.', 'Avada' ); ?>
			<?php else : ?>
				<?php printf( esc_attr__( 'All needed posts have been reverted to the previous syntax. You can now roll-back to %s.', 'Avada' ), '<a href="https://theme-fusion.com/forums/topic/downloading-avada-4-0-3/" target="_blank">Avada 4.0.3</a>' ); ?>
			<?php endif; ?>
		<?php endif;
	}

	/**
	 * The progress-bar
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_progress_bar() {
		?>
		<?php if ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) : ?>
			<progress class="fusion-builder-migration-progress-bar" max="<?php echo absint( $this->total_posts_count[ $this->current_post_type ] ); ?>" value="<?php echo absint( $this->to_offset ); ?>"></progress>
		<?php elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) : ?>
			<?php
			$total = self::$posts_to_convert + self::$slides_to_convert + self::$widgets_to_convert;
			$posts_per_page = $this->get_posts_per_page();

			if ( isset( $_GET['type'] ) && 'posts' === $_GET['type'] ) {
				$still_to_convert = count( self::$option ) + $posts_per_page + self::$slides_to_convert + self::$widgets_to_convert;
			} elseif ( isset( $_GET['type'] ) && 'slides' === $_GET['type'] ) {
				$still_to_convert = self::$slides_to_convert + self::$widgets_to_convert;
			} else {
				$still_to_convert = self::$widgets_to_convert;
			}
			?>
			<progress class="fusion-builder-migration-progress-bar" max="100" value="<?php echo absint( ( 1 - $still_to_convert / $total ) * 100 ); ?>"></progress>
		<?php endif;
	}

	/**
	 * Extra info to be displayed.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_more_info() {
		if ( isset( $_GET['step'] ) && 'query' === $_GET['step'] ) : ?>
			<ul class="tasks-list">
				<?php foreach ( $this->post_types as $post_type ) : ?>
					<?php
					$li_class = '';
					if ( array_search( $post_type, $this->post_types ) < array_search( $this->current_post_type, $this->post_types ) ) {
						$li_class = 'done';
					} elseif ( array_search( $post_type, $this->post_types ) == array_search( $this->current_post_type, $this->post_types ) ) {
						$li_class = 'doing';
					}
					?>
					<li class="<?php echo esc_attr( $li_class ); ?>">
						<span class="content"><?php printf( esc_attr__( 'Scanning posts of post type "%1$s". Total posts: %2$s.', 'Avada' ), esc_attr( $post_type ), absint( $this->total_posts_count[ $post_type ] ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php elseif ( isset( $_GET['step'] ) && 'convert' === $_GET['step'] ) : ?>
			<?php
			$posts_li_class         = '';
			$slides_li_class        = '';
			$widgets_li_class       = '';
			$theme_options_li_class = '';
			if ( isset( $_GET['type'] ) && 'posts' === $_GET['type'] ) {
				$posts_li_class = 'doing';
			} elseif ( isset( $_GET['type'] ) && 'slides' === $_GET['type'] ) {
				$posts_li_class  = 'done';
				$slides_li_class = 'doing';
			} elseif ( isset( $_GET['type'] ) && 'widgets' === $_GET['type'] ) {
				$posts_li_class   = 'done';
				$slides_li_class  = 'done';
				$widgets_li_class = 'doing';
			} else {
				$posts_li_class         = 'done';
				$slides_li_class        = 'done';
				$widgets_li_class       = 'done';
				$theme_options_li_class = 'doing';
			}
			?>
			<ul class="tasks-list">
				<?php if ( ! self::$revert ) : ?>
					<li class="<?php echo esc_attr( $posts_li_class ); ?>"><span class="content"><?php esc_html_e( 'Converting posts.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $slides_li_class ); ?>"><span class="content"><?php esc_html_e( 'Converting slides.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $widgets_li_class ); ?>"><span class="content"><?php esc_html_e( 'Converting widgets.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $theme_options_li_class ); ?>"><span class="content"><?php esc_html_e( 'Converting theme options.' ,'Avada' ); ?></span></li>
				<?php else : ?>
					<li class="<?php echo esc_attr( $posts_li_class ); ?>"><span class="content"><?php esc_html_e( 'Reverting posts.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $slides_li_class ); ?>"><span class="content"><?php esc_html_e( 'Reverting slides.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $widgets_li_class ); ?>"><span class="content"><?php esc_html_e( 'Reverting widgets.' ,'Avada' ); ?></span></li>
					<li class="<?php echo esc_attr( $theme_options_li_class ); ?>"><span class="content"><?php esc_html_e( 'Reverting theme options.' ,'Avada' ); ?></span></li>
				<?php endif; ?>
			</ul>
		<?php endif;
	}

	/**
	 * Prints the migration page buttons.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function get_migration_page_buttons() {
		?>
		<?php if ( ! isset( $_GET['step'] ) ) : ?>
			<?php $revert = ( self::$revert ) ? '&revert=1' : ''; ?>
			<?php $posts_per_page = $this->get_posts_per_page(); ?>
			<a class="avada-button confirm-conversion" id="confirm-conversion" href="<?php echo esc_url_raw( admin_url( 'index.php?' . self::$slug . '=1&step=query&from=0&to=' . $posts_per_page . '&post_type=' . $this->post_types[0] . $revert ) ); ?>">
				<?php if ( self::$revert ) : ?>
					<?php esc_attr_e( 'Start Reversion', 'Avada' ); ?>
				<?php else : ?>
					<?php esc_attr_e( 'Start Conversion', 'Avada' ); ?>
				<?php endif; ?>
			</a>

			<a class="avada-button deny-conversion" href="<?php echo esc_url_raw( admin_url( 'index.php?' . self::$slug . '=0&migrate_later=1' ) ); ?>">
				<?php if ( self::$revert ) : ?>
					<?php esc_attr_e( 'Do Not Revert', 'Avada' ); ?>
				<?php else : ?>
					<?php esc_attr_e( 'Do Not Convert', 'Avada' ); ?>
				<?php endif; ?>
			</a>

			<script type="text/javascript">
				var confirmButton = document.getElementById( 'confirm-conversion' );
				confirmButton.addEventListener( 'click', checkConfirmation, false );
				function checkConfirmation( event ) {
					if ( document.getElementById( 'confirm-reading' ).checked ) {
						window.location.href = "<?php echo esc_url_raw( admin_url( 'index.php?' . self::$slug . '=1&step=query&from=0&to=' . $posts_per_page . '&post_type=' . $this->post_types[0] ) ); ?>";
					} else {
						event.preventDefault()
						alert( "<?php esc_html_e( 'Please confirm that you have read the instructions, by checking the box.', 'Avada' ); ?>" );
					}
				}
			</script>
		<?php elseif ( isset( $_GET['step'] ) && 'done' === $_GET['step'] ) : ?>
			<?php if ( self::$revert ) : ?>
				<a class="avada-button" href="<?php echo esc_url_raw( admin_url( 'index.php' ) ); ?>">
					<?php esc_attr_e( 'Take me to Admin', 'Avada' ); ?>
				</a>
			<?php else : ?>
				<?php
				$bundled_plugins = Avada::get_bundled_plugins();
				$fusion_core_latest = false;
				if ( class_exists( 'FusionCore_Plugin' ) ) {
					$fusion_core_latest = version_compare( FusionCore_Plugin::VERSION, $bundled_plugins['fusion_core']['version'], '>=' );
				}

				$fusion_builder_latest = false;
				if ( defined( 'FUSION_BUILDER_VERSION' ) ) {
					$fusion_builder_latest = version_compare( FUSION_BUILDER_VERSION, $bundled_plugins['fusion_builder']['version'], '>=' );
				}
				?>
				<?php if ( $fusion_core_latest && $fusion_builder_latest ) : ?>
					<a class="avada-button" href="<?php echo esc_url_raw( admin_url( 'index.php' ) ); ?>">
						<?php esc_attr_e( 'Take me to Admin', 'Avada' ); ?>
					</a>
				<?php else : ?>
					<a class="avada-button" href="<?php echo esc_url_raw( admin_url( 'admin.php?page=avada-plugins' ) ); ?>">
						<?php esc_attr_e( 'Update Required Plugins', 'Avada' ); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif;
	}

	/**
	 * Sets names of self enclosing shortcode.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function set_self_enclosing_shortcodes() {
		$self_enclosing_shortcodes = array(
			'fusion_fontawesome',
			'fusion_image',
			'fusion_layerslider',
			'fusion_menu_anchor',
			'fusion_rev_slider',
			'fusion_section_separator',
			'fusion_separator',
			'fusion_social_links',
			'fusion_soundcloud',
			'fusion_login',
			'fusion_register',
			'fusion_lost_password',
			'fusion_vimeo',
			'fusion_youtube',
		);

		self::$self_enclosing_shortcodes = $self_enclosing_shortcodes;
	}


	/**
	 * Sets shortcode names that need converted.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function set_shortcodes_for_conversion() {
		$shortcodes_for_conversion = array(
			// Elements.
			'alert',
			'blog',
			'button',
			'checklist',
			'li_item',
			'content_boxes',
			'content_box',
			'counters_box',
			'counter_box',
			'counters_circle',
			'counter_circle',
			'dropcap',
			'flexslider',
			'postslider',
			'flip_boxes',
			'flip_box',
			'fontawesome',
			'fusionslider',
			'map',
			'highlight',
			'images',
			'image',
			'clients',
			'client',
			'imageframe',
			'menu_anchor',
			'modal',
			'modal_text_link',
			'one_page_text_link',
			'person',
			'popover',
			'pricing_table',
			'pricing_row',
			'pricing_column',
			'pricing_price',
			'pricing_footer',
			'progress',
			'recent_posts',
			'recent_works',
			'section_separator',
			'separator',
			'sharing',
			'slider',
			'slide',
			'social_links',
			'soundcloud',
			'tabs',
			'tab',
			'tagline_box',
			'testimonials',
			'testimonial',
			'title',
			'accordian',
			'toggle',
			'tooltip',
			'vimeo',
			'featured_products_slider',
			'products_slider',
			'youtube',
		);

		self::$shortcodes_for_conversion = $shortcodes_for_conversion;
	}

	/**
	 * Sets column names that need converted.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function set_columns_for_conversion() {
		$columns_for_conversion = array(
			'five_sixth',
			'fullwidth',
			'four_fifth',
			'one_fifth',
			'one_fourth',
			'one_full',
			'one_half',
			'one_sixth',
			'one_third',
			'three_fifth',
			'three_fourth',
			'two_fifth',
			'two_third',
		);

		self::$columns_for_conversion = $columns_for_conversion;
	}

	/**
	 * Check for confliciting shortcodes and remove them.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function remove_conflicting_shortcodes() {
		global $shortcode_tags;

		foreach ( self::$shortcodes_for_conversion as $key => $shortcode ) {
			if ( isset( $shortcode_tags[ $shortcode ] ) && ( ! is_array( $shortcode_tags[ $shortcode ] ) || ( is_object( $shortcode_tags[ $shortcode ][0] ) && false === strpos( get_class( $shortcode_tags[ $shortcode ][0] ), 'FusionSC' ) ) ) ) {
				unset( self::$shortcodes_for_conversion[ $key ] );
			}
		}
	}

	/**
	 * Setup the from -> to array for shortcode names.
	 *
	 * @access private
	 * @since 5.0.0
	 * @return void
	 */
	private function set_from_to_shortcode_names() {
		$from_to_shortcode_names = array();

		foreach ( self::$shortcodes_for_conversion as $shortcode ) {
			if ( 'accordian' === $shortcode ) {
				$from_to_shortcode_names[ '[' . $shortcode . ' ' ] = '[fusion_accordion ';
				$from_to_shortcode_names[ '[' . $shortcode . ']' ] = '[fusion_accordion]';
				$from_to_shortcode_names[ '[/' . $shortcode . ']' ] = '[/fusion_accordion]';
			} elseif ( 'recent_works' === $shortcode ) {
				$from_to_shortcode_names[ '[' . $shortcode . ' ' ] = '[fusion_portfolio ';
				$from_to_shortcode_names[ '[' . $shortcode . ']' ] = '[fusion_portfolio]';
				$from_to_shortcode_names[ '[/' . $shortcode . ']' ] = '[/fusion_portfolio]';
			} elseif ( 'tabs' === $shortcode || 'tab' === $shortcode ) {
				$from_to_shortcode_names[ '[' . $shortcode . ' ' ] = '[fusion_old_' . $shortcode . ' ';
				$from_to_shortcode_names[ '[/' . $shortcode . ']' ] = '[/fusion_old_' . $shortcode . ']';
			} elseif ( 'vimeo' === $shortcode || 'youtube' === $shortcode ) {
				$from_to_shortcode_names[ '[' . $shortcode . ' ' ] = '[fusion_' . $shortcode . ' ';
				$from_to_shortcode_names[ '[/' . $shortcode . ']' ] = '';
			} else {
				$from_to_shortcode_names[ '[' . $shortcode . ' ' ] = '[fusion_' . $shortcode . ' ';
				$from_to_shortcode_names[ '[' . $shortcode . ']' ] = '[fusion_' . $shortcode . ']';
				$from_to_shortcode_names[ '[/' . $shortcode . ']' ] = '[/fusion_' . $shortcode . ']';
			}
		}

		self::$from_to_shortcode_names = $from_to_shortcode_names;
	}

	/**
	 * 1. backup post content as post meta "fusion_builder_content_backup".
	 * 2. replace old shortcodes with new ones.
	 * 3. insert fusion_builder_row where needed.
	 * 4. search page content for opening [ tags. if is not a section add appropriate shortcodes.
	 * 5. insert missing columns shortcode inside fusion_builder_row where needed.
	 * 6. run verification check. check opened & closed tags ( section, row, column ).
	 * 7. add completed post meta flag.
	 *
	 * @access private
	 * @since 5.0.0
	 * @param string $id A post ID.
	 * @return void
	 */
	private function convert_post_contents( $id = '' ) {
		if ( ! empty( $id ) ) {

			// Check for "converted" post meta.
			$content = get_post( $id );

			// If we're not on a page with content, early exit.
			if ( ! is_object( $content ) || ! property_exists( $content, 'post_content' ) ) {
				return;
			}
			$post_content  = $content->post_content;
			$post_excerpt  = $content->post_excerpt;
			$page_template = get_page_template_slug( $id );

			$contents = array();
			if ( ! empty( $post_content ) || 'faqs.php' === $page_template || false !== strpos( $page_template, 'portfolio' ) ) {
				$contents['post_content'] = $post_content;
			}
			if ( ! empty( $post_excerpt ) ) {
				$contents['post_excerpt'] = $post_excerpt;
			}
			$page_converted = get_post_meta( $id, 'fusion_builder_converted', true );
			$builder_status = get_post_meta( $id, 'fusion_builder_status', true );

			// Backup page content if it was not converted previously.
			if ( 'yes' !== $page_converted ) {
				// Backup page content as post meta.
				update_post_meta( $id, 'fusion_builder_content_backup', $post_content );
			}

			// Update post content.
			$updated_post = array(
				'ID'           => $id,
			);

			$string_from_to = array(
				// Fullwidth container.
				'[/fullwidth]'    => '[/fusion_builder_row][/fusion_builder_container]',

				// Columns.
				'[one_full'       => '[fusion_builder_column type="1_1"',
				'[/one_full]'     => '[/fusion_builder_column]',

				'[one_half'       => '[fusion_builder_column type="1_2"',
				'[/one_half]'     => '[/fusion_builder_column]',

				'[two_third'      => '[fusion_builder_column type="2_3"',
				'[/two_third]'    => '[/fusion_builder_column]',

				'[two_fifth'      => '[fusion_builder_column type="2_5"',
				'[/two_fifth]'    => '[/fusion_builder_column]',

				'[/one_third]'    => '[/fusion_builder_column]',
				'[one_third'      => '[fusion_builder_column type="1_3"',

				'[/five_sixth]'   => '[/fusion_builder_column]',
				'[five_sixth'     => '[fusion_builder_column type="5_6"',

				'[/four_fifth]'   => '[/fusion_builder_column]',
				'[four_fifth'     => '[fusion_builder_column type="4_5"',

				'[/one_fifth]'    => '[/fusion_builder_column]',
				'[one_fifth'      => '[fusion_builder_column type="1_5"',

				'[/one_fourth]'   => '[/fusion_builder_column]',
				'[one_fourth'     => '[fusion_builder_column type="1_4"',

				'[/three_fifth]'  => '[/fusion_builder_column]',
				'[three_fifth'    => '[fusion_builder_column type="3_5"',

				'[/three_fourth]' => '[/fusion_builder_column]',
				'[three_fourth'   => '[fusion_builder_column type="3_4"',

				'[/one_sixth]'    => '[/fusion_builder_column]',
				'[one_sixth'      => '[fusion_builder_column type="1_6"',
			);

			foreach ( $contents as $content_type => $content ) {
				// For Visual Composer only.
				$new_string_from_to = $string_from_to;
				if ( false !== strpos( $content, '[vc_row]' ) ) {
					unset( $new_string_from_to['[/fullwidth]'] );
				}

				// Replace old layout shortcodes with new ones.
				$content = strtr( $content, $new_string_from_to );

				// Replace old element shortcodes with new ones.
				$content = $this->convert_shortcode_names( $content );

				// Add trailing slashes to self enclosing shortcodes.
				$content = $this->add_shortcode_trailing_slash( $content );

				if ( 'post_content' === $content_type && false === strpos( $content, '[vc_row]' ) ) {

					// Pricing shortcode inside text shortcode fix.
					// TODO: expand to include woo, layer, etc ?
					$second_string_from_to = array(
						'[fusion_text][fusion_pricing_table'    => '[fusion_pricing_table',
						'[/fusion_pricing_table][/fusion_text]' => '[/fusion_pricing_table]',
					);
					$content = strtr( $content, $second_string_from_to );

					$needle = '[fullwidth';
					$last_pos = -1;
					$position_change = 0;
					$positions = array();

					// Get all positions of [fullwidth shortcode. @codingStandardsIgnoreLine
					while ( ( $last_pos = strpos( $content, $needle, $last_pos + 1 ) ) !== false ) {
						$positions[] = $last_pos;
					}

					foreach ( $positions as $position ) {

						// Fullwidth tag closing position.
						$section_close_position = strpos( $content, ']', $position + $position_change );

						// Insert [fusion_builder_row] shortcode.
						$content = substr_replace( $content, '][fusion_builder_row]', $section_close_position, 1 );

						// Change in position.
						$position_change = $position_change + 20;

					}

					// Replace old [fullwidth shortcode with new [fusion_builder_section.
					$third_string_from_to = array(
						'[fullwidth' => '[fusion_builder_container',
					);

					$content = strtr( $content, $third_string_from_to );

					// Convert outer elements and columns.
					$content = $this->convert_outside_elements( $content );

					// Convert rows.
					$content = $this->convert_rows( $content );
				} // End if().

				if ( 'faqs.php' === $page_template ) {
					$content .= $this->convert_faqs_template( $id );
				} elseif ( false !== strpos( $page_template, 'portfolio' ) ) {
					$content .= $this->convert_portfolio_template( $id, $page_template );
				}

				// Convert container paddings.
				if ( '100-width.php' === $page_template ) {
					$content = $this->convert_container_paddings( $content, $id );
				}

				// For each content type, add converted contents to update array.
				$updated_post[ $content_type ] = $content;
			} // End foreach().

			$updated = wp_update_post( add_magic_quotes( $updated_post ), true );

			if ( is_wp_error( $updated ) ) {
				$errors = $updated->get_error_messages();

				update_post_meta( $id, 'fusion_builder_converted', $errors );
			} else {
				update_post_meta( $id, 'fusion_builder_converted', 'yes' );
			}
		} // End if().
	}

	/**
	 * 1. Revert content
	 * 2. Remove extra post-meta that was added during migration.
	 *
	 * @access private
	 * @since 5.0.0
	 * @param string $id A post ID.
	 * @return void
	 */
	private function revert_post_contents( $id = '' ) {

		// Early exit if post-ID is not defined.
		if ( empty( $id ) ) {
			return;
		}

		// Get the backed-up content.
		$backed_up_content = get_post_meta( $id, 'fusion_builder_content_backup', true );
		// Do not procced if the post was not previously converted.
		$converted = get_post_meta( $id, 'fusion_builder_converted', true );
		if ( 'yes' !== $converted ) {
			return;
		}
		// Do not proceed if the backed-up content does not exist or is empty.
		if ( ! is_string( $backed_up_content ) || empty( $backed_up_content ) ) {
			return;
		}

		// Get the post object.
		$post = get_post( $id );
		$reverted_post = (array) $post;
		// Revert content.
		$reverted_post['post_content'] = $backed_up_content;
		// Update post.
		wp_update_post( $reverted_post, true );
		// Cleanup post-meta.
		delete_post_meta( $id, 'fusion_builder_content_backup' );
		delete_post_meta( $id, 'fusion_builder_converted' );

	}

	/**
	 * Adds / before closing bracket of self enclosing shortcodes.
	 *
	 * @since 5.0.0
	 * @param string $content Content of a specific post.
	 * @return string The updated post content.
	 */
	private function add_shortcode_trailing_slash( $content ) {
		$positions = array();
		$position_change = 0;
		$last_pos = -1;

		// Get all positions of self enclosing shortcode beginnings.
		if ( '' !== $content ) {
			foreach ( self::$self_enclosing_shortcodes as $key => $needle ) {
				// @codingStandardsIgnoreLine
				while ( ( $last_pos = strpos( $content, '[' . $needle, $last_pos + 1 ) ) !== false ) {

					$allowed_chars = array( ' ', ']' );

					if ( in_array( substr( $content, $last_pos + strlen( $needle ) + 1, 1 ), $allowed_chars ) ) {
						$positions[] = $last_pos;
					}
				}
			}

			// Sort to make sure we go through them in order.
			asort( $positions );

			foreach ( $positions as $position ) {
				// Get closing position.
				$section_close_position = strpos( $content, ']', $position + $position_change );

				// Insert /].
				if ( '/]' !== substr( $content, $section_close_position - 1, 2 ) ) {
					$content = substr_replace( $content, '/]', $section_close_position, 1 );
				}

				// Change in position.
				$position_change++;
			}
		}
		return $content;
	}

	/**
	 * Convert old Avada shortcode names to new Fusion Builder names.
	 *
	 * @since 5.0.0
	 * @param string $content Content of a specific post.
	 * @return string The updated post content.
	 */
	private function convert_shortcode_names( $content ) {
		$content = strtr( $content, self::$from_to_shortcode_names );

		// Also convert needed shortcode attributes.
		$content = $this->convert_shortcode_attributes( $content );

		return $content;
	}

	/**
	 * Convert old Avada shortcode attributes to new Fusion Builder attributes.
	 *
	 * @since 5.0.0
	 * @param string $content Content of a specific post.
	 * @return string The updated post content.
	 */
	private function convert_shortcode_attributes( $content ) {

		$string_from_to = array(
			'grid-with-excerpts'     => 'grid-with-text',
			'style_type="single"'    => 'style_type="single solid"',
			'style_type="double"'    => 'style_type="double solid"',
			'style_type="underline"' => 'style_type="underline solid"',
		);
		$content = strtr( $content, $string_from_to );

		return $content;
	}

	/**
	 * Convert FusionBuilder rows.
	 *
	 * @since 5.0.0
	 * @param string $content Content of a specific post.
	 * @return string The updated post content.
	 */
	private function convert_rows( $content ) {
		$needle = '[fusion_builder_row]';
		$last_pos = -1;
		$position_change = 0;
		$positions = array();

		// Get all positions of [fusion_builder_row shortcode. @codingStandardsIgnoreLine
		while ( ( $last_pos = strpos( $content, $needle, $last_pos + 1 ) ) !== false ) {
			$positions[] = $last_pos;
		}

		// For each row.
		foreach ( $positions as $position ) {

			$position = $position + $position_change;

			$row_closing_position = strpos( $content, '[/fusion_builder_row]', $position );

			// Search within this range/row.
			$range = $row_closing_position - $position + 1;
			// Row content.
			$row_content = substr( $content, $position + strlen( $needle ), $range );

			$original_row_content = $row_content;

			$element_needle = '[';
			$row_last_pos = -1;
			$row_position_change = 0;
			$element_positions = array();

			$main_column_opened = false;
			$inner_column_opened = false;
			$inner_columns_total_width = 0;
			$outside_column_element = false;
			$outside_column_element_close_position = 0;
			$element_position_change = 0;

			// Get all positions for shortcode opening tag "[". @codingStandardsIgnoreLine
			while ( ( $row_last_pos = strpos( $row_content, $element_needle, $row_last_pos + 1 ) ) !== false ) {
				$element_positions[] = $row_last_pos;
			}

			foreach ( $element_positions as $element_position ) {

				$shortcode_name         = substr( $row_content, $element_position + $element_position_change, 40 );
				$shortcode_name_space   = strtok( $shortcode_name, ' ' );
				$shortcode_name_bracket = strtok( $shortcode_name, ']' );
				if ( strlen( $shortcode_name_space ) < strlen( $shortcode_name_bracket ) ) {
					$shortcode_name = $shortcode_name_space;
				} else {
					$shortcode_name = $shortcode_name_bracket;
				}

				// If it's a column that is opened.
				if ( '[fusion_builder_column' == $shortcode_name ) {

					if ( $main_column_opened ) {
						if ( $outside_column_element && $outside_column_element_close_position && $element_position + $element_position_change > $outside_column_element_close_position ) {
							// Close column.
							$row_content = substr_replace( $row_content, '[/fusion_builder_column]', $element_position + $element_position_change, 0 );
							$outside_column_element = false;
							$element_position_change = $element_position_change + 24;
							$outside_column_element_close_position = 0;
						} else {
							$inner_row_container = '';
							$inner_row_container_position_change = 0;
							if ( ! $inner_column_opened ) {

								if ( 0 == $inner_columns_total_width ) {
									$inner_row_container = '[fusion_builder_row_inner]';
									$inner_row_container_position_change = 26;
								}

								$column_width = explode( '_', substr( $row_content, $element_position + $element_position_change + strlen( $shortcode_name ) + 7, 3 ) );
								$column_width = $column_width[0] / $column_width[1];
								$inner_columns_total_width += $column_width;
							}

							$row_content = substr_replace( $row_content, $inner_row_container . '[fusion_builder_column_inner', $element_position + $element_position_change, strlen( $shortcode_name ) );
							$element_position_change += $inner_row_container_position_change + 6;
							$outside_column_element_close_position += $inner_row_container_position_change + 6;

							$inner_column_opened = true;
						}
					}

					$main_column_opened = true;

					// If it's a column that is closed.
				} elseif ( '[/fusion_builder_column' == $shortcode_name ) {
					if ( $main_column_opened && $inner_column_opened ) {

						$inner_row_container = '';
						$inner_row_container_position_change = 0;

						if ( 1 <= $inner_columns_total_width ) {
							$inner_row_container = '[/fusion_builder_row_inner]';
							$inner_row_container_position_change = 27;

							$inner_columns_total_width = 0;
						}

						$row_content = substr_replace( $row_content, '[/fusion_builder_column_inner]' . $inner_row_container, $element_position + $element_position_change, strlen( $shortcode_name ) + 1 );

						$element_position_change += $inner_row_container_position_change + 6;
						$outside_column_element_close_position += $inner_row_container_position_change + 6;
						$inner_column_opened = false;
					} elseif ( $main_column_opened ) {
						$main_column_opened = false;
					}
				} elseif ( '[/fusion_builder_row' == $shortcode_name ) { // If end of row.
					if ( $main_column_opened ) {
						$row_content = substr_replace( $row_content, '[/fusion_builder_column]', $element_position + $element_position_change, 0 );

						$main_column_opened = false;
						$element_position_change = $element_position_change + 24;
					}
				} elseif ( '1' != strpos( $shortcode_name, '/' ) ) { // If it's an element opening tag.

					$set_outside_column_element_close_position = false;

					$shortcode_name         = substr( $row_content, $element_position + $element_position_change, 40 );
					$shortcode_name_space   = strtok( $shortcode_name, ' ' );
					$shortcode_name_bracket = strtok( $shortcode_name, ']' );
					if ( strlen( $shortcode_name_space ) < strlen( $shortcode_name_bracket ) ) {
						$shortcode_name = str_replace( '[', '', $shortcode_name_space );
					} else {
						$shortcode_name = str_replace( '[', '', $shortcode_name_bracket );
					}

					// This is an element, add column.
					if ( ! $main_column_opened ) {

						$column_open_tag = '[fusion_builder_column type="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" spacing="yes" background_image="" background_repeat="no-repeat" padding="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="no" center_content="no" min_height="none"]';

						$row_content = substr_replace( $row_content, $column_open_tag, $element_position + $element_position_change, 0 );

						// Change in position.
						$element_position_change = $element_position_change + strlen( $column_open_tag );

						$set_outside_column_element_close_position = true;

					} elseif ( $main_column_opened && $outside_column_element_close_position && $element_position + $element_position_change > $outside_column_element_close_position ) {

						$column_close_open_tag = '[/fusion_builder_column][fusion_builder_column type="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" spacing="yes" background_image="" background_repeat="no-repeat" padding="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="no" center_content="no" min_height="none"]';

						if ( 'fusion_button' === $shortcode_name || 'fusion_fontawesome' === $shortcode_name || 'fusion_imageframe' === $shortcode_name || 'fusion_separator' === $shortcode_name || 'fusion_text' === $shortcode_name ) {
							$column_close_open_tag = '';
						}
						$row_content = substr_replace( $row_content, $column_close_open_tag, $element_position + $element_position_change, 0 );

						// Change in position.
						$element_position_change = $element_position_change + strlen( $column_close_open_tag );

						$set_outside_column_element_close_position = true;
					}

					if ( $set_outside_column_element_close_position ) {

						$main_column_opened = true;
						$outside_column_element = true;

						$shortcode_name = str_replace( '[', '', $shortcode_name );

						if ( in_array( $shortcode_name, self::$self_enclosing_shortcodes ) ) {
							$outside_column_element_close_position = strpos( $row_content, '/]', $element_position + $element_position_change );
						} else {
							$outside_column_element_close_position = strpos( $row_content, '[/' . $shortcode_name . ']', $element_position + $element_position_change );
						}

						$set_outside_column_element_close_position = false;
					}
				} // End if().
			} // End foreach().

			// Replace unprocessed row content with processed one.
			$content = substr_replace( $content, $row_content, $position + 20, strlen( $original_row_content ) );

			// Get character difference between processed and unprocessed row content.
			$content_difference = strlen( $row_content ) - strlen( $original_row_content );
			$position_change = $position_change + $content_difference;

		} // End foreach().

		return $content;
	}

	/**
	 * Convert content outside of FusionBuilder rows.
	 *
	 * @since 5.0.0
	 * @param string $content Content of a specific post.
	 * @return string The updated post content.
	 */
	private function convert_outside_elements( $content ) {

		// Check for elements outside of fullwidth section.
		$element_needle = '[';
		$last_pos = -1;
		$element_position_change = 0;
		$element_positions = array();

		$section_opened = false;
		$column_opened = false;

		// Get all positions for shortcode opening tag "[". @codingStandardsIgnoreLine
		while ( ( $last_pos = strpos( $content, $element_needle, $last_pos + 1 ) ) !== false ) {
			$element_positions[] = $last_pos;
		}

		foreach ( $element_positions as $key => $element_position ) {

			$section_needle = '[fusion_builder_container';
			$check_for_section = substr( $content, $element_position + $element_position_change, strlen( $section_needle ) );

			// If it's a section that is opened.
			if ( $check_for_section == $section_needle ) {

				if ( true == $section_opened ) {
					// Close section.
					$close_section_tag = '[/fusion_builder_row][/fusion_builder_container]';

					$content = substr_replace( $content, $close_section_tag, $element_position + $element_position_change, 0 );

					$section_opened = false;
					$element_position_change = $element_position_change + strlen( $close_section_tag );

				}
				$section_opened = true;

				// If section is closed.
			} elseif ( '[/fusion_builder_containe' == $check_for_section ) {
				$section_opened = false;

				// This is an element. Add column.
			} else {
				if ( false == $section_opened ) {
					$shortcode_name         = substr( $content, $element_position + $element_position_change, 40 );
					$shortcode_name_space   = strtok( $shortcode_name, ' ' );
					$shortcode_name_bracket = strtok( $shortcode_name, ']' );
					if ( strlen( $shortcode_name_space ) < strlen( $shortcode_name_bracket ) ) {
						$shortcode_name = str_replace( '[', '', $shortcode_name_space );
					} else {
						$shortcode_name = str_replace( '[', '', $shortcode_name_bracket );
					}

					// Convert section separator with negative margins.
					if ( 'fusion_separator' == $shortcode_name ) {
						$section_top_margin = '';
						$section_bottom_margin = '';

						if ( isset( $element_positions[ $key + 1 ] ) ) {
							$end_position = $element_positions[ $key + 1 ];
						} else {
							$end_position = strlen( $content );
						}
						$separator_tag_length = $end_position - $element_position;
						$separator_tag = substr( $content, $element_position + $element_position_change, $separator_tag_length );

						preg_match( '/(top=["\'](.*?)["\'])/', $separator_tag, $top );
						preg_match( '/(bottom=["\'](.*?)["\'])/', $separator_tag, $bottom );
						preg_match( '/(top_margin=["\'](.*?)["\'])/', $separator_tag, $top_margin );
						preg_match( '/(bottom_margin=["\'](.*?)["\'])/', $separator_tag, $bottom_margin );
						preg_match( '/(style=["\'](.*?)["\'])/', $separator_tag, $style );

						if ( isset( $top_margin[2] ) ) {
							$section_top_margin = self::validate_shortcode_attr_value( $top_margin[2], 'px' );

							$separator_tag = str_replace( $top_margin[0], '',  $separator_tag );
						}

						if ( isset( $bottom_margin[2] ) ) {
							$section_bottom_margin = self::validate_shortcode_attr_value( $bottom_margin[2], 'px' );

							$separator_tag = str_replace( $bottom_margin[0], '',  $separator_tag );
						}

						if ( isset( $top[2] ) ) {
							$section_top_margin = $top[2];

							$separator_tag = str_replace( $top[0], '',  $separator_tag );

							if ( ! isset( $bottom[2] ) && isset( $stlye[2] ) && 'none' !== $style[2] ) {
								$section_bottom_margin = self::validate_shortcode_attr_value( $top_[2], 'px' );
							}
						}

						if ( isset( $bottom[2] ) ) {
							$section_bottom_margin = self::validate_shortcode_attr_value( $bottom[2], 'px' );

							$separator_tag = str_replace( $bottom[0], '',  $separator_tag );
						}

						$section_top_margin = ' margin_top="' . $section_top_margin . '"';
						$section_bottom_margin = ' margin_bottom="' . $section_bottom_margin . '"';

						// Open and close tags.
						$wrapped_shortcode = '[fusion_builder_container hundred_percent="yes" overflow="visible"' . $section_top_margin . $section_bottom_margin . ' background_color="rgba(255,255,255,0)"][fusion_builder_row]';
						$wrapped_shortcode .= $separator_tag;
						$wrapped_shortcode .= '[/fusion_builder_row][/fusion_builder_container]';

						$content = substr_replace( $content, '', $element_position + $element_position_change  , $separator_tag_length );

						$content = substr_replace( $content, $wrapped_shortcode, $element_position + $element_position_change, 0 );

						$section_opened = false;

						$element_position_change = $element_position_change + strlen( $wrapped_shortcode ) - $separator_tag_length;

					} else {

						// Open section.
						$open_section_tag = '[fusion_builder_container hundred_percent="yes" overflow="visible"][fusion_builder_row]';

						$content = substr_replace( $content, $open_section_tag, $element_position + $element_position_change, 0 );

						$section_opened = true;

						$element_position_change = $element_position_change + strlen( $open_section_tag );
					} // End if().
				} // End if().
			} // End if().
		} // End foreach().

		// Close section if it was not closed.
		if ( true == $section_opened ) {

			$content .= '[/fusion_builder_row][/fusion_builder_container]';
			$section_opened = false;
		}

		return $content;
	}

	/**
	 * Convert FAQ page template to FAQ shortcode element.
	 *
	 * @since 5.0.0
	 * @param string $id The page ID.
	 * @return string The faq shortcode.
	 */
	private function convert_faqs_template( $id ) {
		$section_open_tag  = '[fusion_builder_container hundred_percent="yes" overflow="visible"][fusion_builder_row]';
		$column_open_tag   = '[fusion_builder_column type="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" spacing="yes" background_image="" background_repeat="no-repeat" padding="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="no" center_content="no" min_height="none"]';
		$faq_tag           = '[fusion_faq filters="" featured_image="" cats_slug="" exclude_cats="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="" id="" /]';
		$column_close_tag  = '[/fusion_builder_column]';
		$section_close_tag = '[/fusion_builder_row][/fusion_builder_container]';

		$backup_options = get_option( 'avada_500_backup_page_templates_faqs', array() );
		if ( self::$revert ) {
			if ( in_array( $id, $backup_options ) ) {
				update_post_meta( $id, '_wp_page_template', 'faqs.php' );
			}
		} else {
			// Backup template.
			if ( ! in_array( $id, $backup_options ) ) {
				$backup_options[] = $id;
				update_option( 'avada_500_backup_page_templates_faqs', $backup_options );
			}
			// Reset the page template.
			update_post_meta( $id, '_wp_page_template', 'default' );
		}

		return $section_open_tag . $column_open_tag . $faq_tag . $column_close_tag . $section_close_tag;
	}

	/**
	 * Convert portfolio page templates to portfolio shortcode element.
	 *
	 * @since 5.0.0
	 * @param string $id            The page ID.
	 * @param string $template_name The page template.
	 * @return string The faq shortcode.
	 */
	private function convert_portfolio_template( $id, $template_name ) {
		// Wrapping tags.
		$section_open_tag  = '[fusion_builder_container hundred_percent="yes" overflow="visible"][fusion_builder_row]';
		$column_open_tag   = '[fusion_builder_column type="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" spacing="yes" background_image="" background_repeat="no-repeat" padding="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="no" center_content="no" min_height="none"]';
		$column_close_tag  = '[/fusion_builder_column]';
		$section_close_tag = '[/fusion_builder_row][/fusion_builder_container]';

		// Layout.
		$layout = 'grid';
		if ( false !== strpos( $template_name, 'text' ) ) {
			$layout = 'grid-with-text';
		}
		// Columns.
		$columns_mapping     = array(
			'one' => '1',
			'two' => '2',
			'three' => '3',
			'grid' => '3',
			'four' => '4',
			'five' => '5',
			'six' => '6',
		);
		$template_name_array = explode( '-', $template_name );
		$columns             = $columns_mapping[ $template_name_array[1] ];

		// Cat slugs.
		$term_ids = fusion_get_page_option( 'pyre_portfolio_category', $id );

		$cat_slugs = ''; // No categories selected.
		if ( is_array( $term_ids ) ) {
			// Multiple categories selected.
			if ( 1 !== count( $term_ids ) || '0' !== $term_ids[0] ) {
				$cat_slugs = array();

				// "All Categories" is selected together with others
				if ( isset( $term_ids[0] ) && '0' === $term_ids[0] ) {
					unset( $term_ids[0] );
					$term_ids = array_values( $term_ids );
				}

				foreach ( $term_ids as $term_id ) {
					$term = get_term( $term_id, 'portfolio_category' );
					$cat_slugs[] = $term->slug;
				}
				$cat_slugs = implode( ',', $cat_slugs );
			}
		}

		// Image size.
		$image_size = fusion_get_page_option( 'pyre_portfolio_featured_image_size', $id );
		if ( 'cropped' == $image_size ) {
			$image_size = 'fixed';
		} elseif ( 'full' == $image_size ) {
			$image_size = 'auto';
		}

		// Column spacing.
		$column_spacing = fusion_get_page_option( 'pyre_portfolio_column_spacing', $id );
		if ( ! $column_spacing ) {
			$column_spacing = Avada()->settings->get( 'portfolio_column_spacing' );
		}

		$excerpt_length = fusion_get_page_option( 'pyre_portfolio_excerpt', $id );
		if ( ! $excerpt_length ) {
			$excerpt_length = Avada()->settings->get( 'excerpt_length_portfolio' );
		}

		// If single grid, make sure to set to grid with text, text on side.
		if ( '1' === $columns && 'grid' === $layout ) {
			$layout = 'grid-with-text';
		}

		// Construct the portfolio shortcode.
		$portfolio_vars = array(
			'content_length'           => str_replace( '_', '-', fusion_get_page_option( 'pyre_portfolio_content_length', $id ) ),
			'layout'                   => $layout,
			'portfolio_title_display'  => fusion_get_page_option( 'pyre_portfolio_title_display', $id ),
			'portfolio_text_alignment' => fusion_get_page_option( 'pyre_portfolio_text_alignment', $id ),
			'portfolio_layout_padding' => fusion_get_page_option( 'pyre_portfolio_layout_padding', $id ),
			'excerpt_length'           => $excerpt_length,
			'cat_slug'                 => $cat_slugs,
			'filters'                  => str_replace( '_', '-', fusion_get_page_option( 'pyre_portfolio_filters', $id ) ),
			'boxed_text'               => fusion_get_page_option( 'pyre_portfolio_text_layout', $id ),
			'picture_size'             => $image_size,
			'columns'                  => $columns,
			'column_spacing'           => $column_spacing,
			'number_posts'             => Avada()->settings->get( 'portfolio_items' ),
			'strip_html'               => 'default',
			'pagination_type'          => 'default',
			'one_column_text_position' => ( 'portfolio-one-column.php' === $template_name ) ? 'floated' : 'below',
		);

		$portfolio_tag  = '[fusion_portfolio ';
		foreach ( $portfolio_vars as $attribute_name => $value ) {
			$portfolio_tag  .= $attribute_name . '="' . $value . '" ';
		}

		$portfolio_tag  .= '/]';

		$backup_options = get_option( 'avada_500_backup_page_templates_portfolio', array() );
		if ( self::$revert ) {
			foreach ( $backup_options as $page_id => $template_args ) {
				if ( $page_id != $id ) {
					continue;
				}
				foreach ( $template_args as $page_template => $pyre_portfolio_width_100 ) {
					update_post_meta( $page_id, '_wp_page_template', $page_template );
					if ( true === $pyre_portfolio_width_100 ) {
						update_post_meta( $page_id, 'pyre_portfolio_width_100', 'yes' );
					}
				}
			}
		} else {
			$page_template = get_page_template_slug( $id );

			// Backup the page template.
			if ( ! isset( $backup_options[ $id ] ) ) {
				$backup_options[ $id ] = array();
			}
			$pyre_portfolio_width_100 = fusion_get_page_option( 'pyre_portfolio_width_100', $id );
			$backup_options[ $id ][ $page_template ] = ( 'yes' === $pyre_portfolio_width_100 );
			update_option( 'avada_500_backup_page_templates_portfolio', $backup_options );

			// Set the page template correctly.
			$page_width = $pyre_portfolio_width_100;
			if ( 'yes' === $page_width ) {
				update_post_meta( $id, '_wp_page_template', '100-width.php' );
			} else {
				update_post_meta( $id, '_wp_page_template', 'default' );
			}
		}
		// Remove the 100% width setting, as it is not valid on normal pages.
		delete_post_meta( $id, 'pyre_portfolio_width_100' );

		return $section_open_tag . $column_open_tag . $portfolio_tag . $column_close_tag . $section_close_tag;
	}

	/**
	 * Convert Fusion Slider post meta.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_shortcode_names_in_fusion_slider() {
		$post_meta_to_convert = array( 'pyre_heading', 'pyre_caption', 'pyre_button_1', 'pyre_button_2' );
		$backup_data = get_option( 'avada_500_backup_fusion_slider_data', array() );

		$args = array(
			'post_type'      => 'slide',
			'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) : $query->the_post();
				global $post;

				if ( self::$revert ) {
					foreach ( $backup_data as $post_id => $post_data ) {
						foreach ( $post_data as $post_meta_name => $post_meta_content ) {
							update_post_meta( $post_id, $post_meta_name, $post_meta_content );
						}
					}
				} else {
					foreach ( $post_meta_to_convert as $post_meta_name ) {
						$post_meta_content = get_post_meta( $post->ID, $post_meta_name, true );

						// Convert element shortcodes.
						$converted_post_meta = $this->convert_shortcode_names( $post_meta_content );

						if ( $converted_post_meta != $post_meta_content ) {
							// Backup data.
							if ( ! isset( $backup_data[ $post->ID ] ) ) {
								$backup_data[ $post->ID ] = array();
							}
							$backup_data[ $post->ID ][ $post_meta_name ] = $post_meta_content;

							// Update converted post meta.
							update_post_meta( $post->ID, $post_meta_name, $converted_post_meta );
						}
					}
					update_option( 'avada_500_backup_fusion_slider_data', $backup_data );
				}

			endwhile;

			wp_reset_postdata();
		} // End if().

		$this->next_step_redirection( true );
	}

	/**
	 * Convert Slider Revolution text layers.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_shortcode_names_in_revslider() {
		if ( class_exists( 'RevSliderFront' ) ) {
			$slider_object = new RevSliderSlider();
			$sliders_array = $slider_object->getArrSliders();

			foreach ( $sliders_array as $slider ) {
				$slides = $slider->getSlidesFromGallery();

				foreach ( $slides as $slide ) {
					$layers = $slide->getLayers();

					// The backup option.
					$backup_options = get_option( 'avada_500_backup_revslider', array() );

					if ( self::$revert ) { // Revert options?
						if ( ! empty( $backup_options ) ) {
							foreach ( $backup_options as $key => $layer_text ) {
								$layers[ $key ]['text'] = $layer_text;
							}
						}
					} else { // Backup and convert options.
						foreach ( $layers as $key => $layer ) {
							// Convert data.
							$converted_data = $this->convert_shortcode_names( $layer['text'] );
							// Backup data.
							if ( $converted_data !== $layer['text'] ) {
								$backup_options[ $key ] = $layer['text'];
							}

							$layers[ $key ]['text'] = $converted_data;
						}
						update_option( 'avada_500_backup_revslider', $backup_options );
					}

					$slide->setLayersRaw( $layers );
					$slide->saveLayers();
				}
			}
		} // End if().
	}

	/**
	 * Convert Layer Slider data.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_shortcode_names_in_layerslider() {
		if ( class_exists( 'LS_Sliders' ) ) {
			// Find all sliders.
			$sliders = LS_Sliders::find();
			// The backup options.
			$backup_options = get_option( 'avada_500_backup_layerslider', array() );
			foreach ( $sliders as $slider ) {
				if ( self::$revert ) {
					if ( ! empty( $backup_options ) ) {
						foreach ( $backup_options as $id => $layer ) {
							foreach ( $layer as $layerkey => $sublayer ) {
								foreach ( $sublayer as $sublayerkey => $sublayercontent ) {
									$slider['data']['layers'][ $layerkey ]['sublayers'][ $sublayerkey ]['html'] = $sublayercontent;
								}
							}
						}
					}
				} else {
					// Loop through each slider, checking for layers and converting.
					if ( isset( $slider['data']['layers'] ) ) {
						foreach ( $slider['data']['layers'] as $layerkey => $layer ) {
							if ( isset( $layer['sublayers'] ) ) {
								foreach ( $layer['sublayers'] as $sublayerkey => $sublayer ) {
									if ( isset( $sublayer['media'] ) && ( 'text' == $sublayer['media'] || 'html' == $sublayer['media'] ) ) {
										// If the sub layer is of type text, then convert html contents or revert depending on the process.
										$converted_data = $this->convert_shortcode_names( $sublayer['html'] );
										// If there is a difference, then backup the data.
										if ( $converted_data !== $sublayer['html'] ) {
											if ( ! isset( $backup_options[ $slider['id'] ] ) ) {
												$backup_options[ $slider['id'] ] = array();
											}
											if ( ! isset( $backup_options[ $slider['id'] ][ $layerkey ] ) ) {
												$backup_options[ $slider['id'] ][ $layerkey ] = array();
											}
											$backup_options[ $slider['id'] ][ $layerkey ][ $sublayerkey ] = $sublayer['html'];
											update_option( 'avada_500_backup_layerslider', $backup_options );
										}
										// Update data.
										$slider['data']['layers'][ $layerkey ]['sublayers'][ $sublayerkey ]['html'] = $converted_data;
									}
								}
							}
						}
					}
					// Save the backed-up data.
					update_option( 'avada_500_backup_layerslider', $backup_options );
				} // End if().
				$id    = $slider['id'];
				$title = $slider['data']['properties']['title'];
				$data  = $slider['data'];
				$slug  = ( isset( $slider['data']['properties']['slug'] ) ) ? $slider['data']['properties']['slug'] : '';

				// Update slider with changed content.
				LS_Sliders::update( $id, $title, $data, $slug );
			} // End foreach().
		} // End if().
	}

	/**
	 * Convert content inside of text widgets.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_shortcode_names_in_widgets() {
		global $wp_registered_sidebars, $wp_registered_widgets;
		$sidebars_widgets = wp_get_sidebars_widgets();

		foreach ( $sidebars_widgets as $sidebar_id => $sidebar ) {

			if ( ! empty( $sidebar ) ) {
				foreach ( $sidebar as $widget_id ) {
					$option_name = false;
					if ( isset( $wp_registered_widgets[ $widget_id ] ) && isset( $wp_registered_widgets[ $widget_id ]['callback'] ) && isset( $wp_registered_widgets[ $widget_id ]['callback'][0] ) ) {
						$option_name = $wp_registered_widgets[ $widget_id ]['callback'][0]->option_name;
					}

					// Only change text widgets.
					if ( 'widget_text' === $option_name ) {

						if ( self::$revert ) {
							// Revert backup.
							$backed_up_options = get_option( 'avada_500_backup_widget_text', array() );
							if ( ! empty( $backed_up_options ) ) {
								update_option( 'widget_text', $backed_up_options );
								$this->next_step_redirection( true );
							}
						} else {
							// Convert.
							$key = $wp_registered_widgets[ $widget_id ]['params'][0]['number'];
							$widget_data = get_option( $option_name );
							if ( isset( $widget_data[ $key ]['text'] ) ) {
								$widget_content = $widget_data[ $key ]['text'];
							} else {
								$widget_content = '';
							}

							// Backup data.
							$backup_options = get_option( 'avada_500_backup_widget_text', array() );
							if ( empty( $backup_options ) ) {
								update_option( 'avada_500_backup_widget_text', $widget_data );
							}

							// Then convert shortcodes.
							$widget_content = $this->convert_shortcode_names( $widget_content );

							$string_from_to = array(
								// Fullwidth container.
								'[/fullwidth]'    => '[/fusion_builder_row][/fusion_builder_container]',

								// Columns.
								'[one_full'       => '[fusion_builder_column type="1_1"',
								'[/one_full]'     => '[/fusion_builder_column]',

								'[one_half'       => '[fusion_builder_column type="1_2"',
								'[/one_half]'     => '[/fusion_builder_column]',

								'[two_third'      => '[fusion_builder_column type="2_3"',
								'[/two_third]'    => '[/fusion_builder_column]',

								'[two_fifth'      => '[fusion_builder_column type="2_5"',
								'[/two_fifth]'    => '[/fusion_builder_column]',

								'[/one_third]'    => '[/fusion_builder_column]',
								'[one_third'      => '[fusion_builder_column type="1_3"',

								'[/five_sixth]'   => '[/fusion_builder_column]',
								'[five_sixth'     => '[fusion_builder_column type="5_6"',

								'[/four_fifth]'   => '[/fusion_builder_column]',
								'[four_fifth'     => '[fusion_builder_column type="4_5"',

								'[/one_fifth]'    => '[/fusion_builder_column]',
								'[one_fifth'      => '[fusion_builder_column type="1_5"',

								'[/one_fourth]'   => '[/fusion_builder_column]',
								'[one_fourth'     => '[fusion_builder_column type="1_4"',

								'[/three_fifth]'  => '[/fusion_builder_column]',
								'[three_fifth'    => '[fusion_builder_column type="3_5"',

								'[/three_fourth]' => '[/fusion_builder_column]',
								'[three_fourth'   => '[fusion_builder_column type="3_4"',

								'[/one_sixth]'    => '[/fusion_builder_column]',
								'[one_sixth'      => '[fusion_builder_column type="1_6"',
							);

							// Replace old layout shortcodes with new ones.
							$widget_content = strtr( $widget_content, $string_from_to );

							// Handle [fullwidth] shortcodes.
							$needle = '[fullwidth';
							$last_pos = -1;
							$position_change = 0;
							$positions = array();

							// Get all positions of [fullwidth shortcode. @codingStandardsIgnoreLine
							while ( false !== ( $last_pos = strpos( $widget_content, $needle, $last_pos + 1 ) ) ) {
								$positions[] = $last_pos;
							}

							foreach ( $positions as $position ) {

								// Fullwidth tag closing position.
								$section_close_position = strpos( $widget_content, ']', $position + $position_change );

								// Insert [fusion_builder_row] shortcode.
								$widget_content = substr_replace( $widget_content, '][fusion_builder_row]', $section_close_position, 1 );

								// Change in position.
								$position_change = $position_change + 20;

							}

							// Replace old [fullwidth shortcode with new [fusion_builder_section.
							$string_from_to = array(
								'[fullwidth' => '[fusion_builder_container',
							);

							$widget_content = strtr( $widget_content, $string_from_to );

							// Handle columns.
							$needle = 'fusion_builder_column';
							$last_pos = -1;
							$position_change = 0;
							$positions = array();
							$row_open = false;

							// Get all positions of opening and closing column tag. @codingStandardsIgnoreLine
							while ( ( $last_pos = strpos( $widget_content, $needle, $last_pos + 1 ) ) !== false ) {
								$positions[] = $last_pos;
							}

							// For each column, opening and closing tag.
							foreach ( $positions as $position ) {

								$position = $position + $position_change;

								// Check if opening or closing and if row is open yet.
								$tag_type = ( '[/' === substr( $widget_content, $position - 2, 2 ) ) ? 'closing' : 'opening';
								$column_next = strpos( substr( $widget_content, $position + 20, 30 ), '[fusion_builder_column' );
								$existing_row = strpos( substr( $widget_content, $position - 30 , 30 ), '[fusion_builder_row]' );
								$existing_row_closed = strpos( substr( $widget_content, $position + 20 , 30 ), '[/fusion_builder_row]' );

								if ( $existing_row ) {
									$row_open = true;
								}
								if ( 'opening' === $tag_type && ! $row_open ) {
									$row_open = true;
									$position_change = $position_change + 20;
									$widget_content = substr_replace( $widget_content, '[fusion_builder_row][fusion_builder_column', $position - 1, 22 );
								}
								if ( 'closing' === $tag_type && $row_open && ! $column_next && ! $existing_row_closed ) {
									$row_open = false;
									$position_change = $position_change + 21;
									$widget_content = substr_replace( $widget_content, '[/fusion_builder_column][/fusion_builder_row]', $position - 2, 24 );
								}
							}

							if ( isset( $widget_data[ $key ]['text'] ) ) {
								$widget_data[ $key ]['text'] = $widget_content;
							}

							update_option( $option_name, $widget_data );
						} // End if().
					} // End if().
				} // End foreach().
			} // End if().
		} // End foreach().

		$this->next_step_redirection( true );
	}

	/**
	 * Validate shortcode attribute value.
	 *
	 * @static
	 * @access private
	 * @since 1.0
	 * @param string $value         The value.
	 * @param string $accepted_unit The accepted unit.
	 * @return value
	 */
	private static function validate_shortcode_attr_value( $value, $accepted_unit ) {

		$validated_value = '';

		if ( '' !== $value ) {
			$value           = trim( $value );
			$unit            = preg_replace( '/[\d-]+/', '', $value );
			$numerical_value = preg_replace( '/[a-z,%]/', '', $value );

			if ( empty( $accepted_unit ) ) {
				$validated_value = $numerical_value;

			} else {

				if ( empty( $unit ) ) {
					// Add unit if it's required.
					$validated_value = $numerical_value . $accepted_unit;
				} else {
					// If unit was found use original value. BC support.
					$validated_value = $value;
				}
			}
		}

		return $validated_value;
	}

	/**
	 * Convert content inside theme options.
	 *
	 * @since 5.0.0
	 * @return void
	 */
	private function convert_shortcode_names_in_theme_options() {

		global $fusion_library;

		$option_name = self::$avada_option_name;
		$options     = get_option( $option_name, array() );

		// Revert options?
		if ( self::$revert ) {
			$backed_up_options = get_option( $option_name . '_500_backup', array() );

			if ( ! $backed_up_options && 'fusion_options' === $option_name ) {
				$backed_up_options = get_option( 'avada_theme_options_500_backup', false );
			}

			if ( ! empty( $backed_up_options ) ) {
				update_option( $option_name, $backed_up_options );
			}
		} else {
			// Backup options.
			update_option( $option_name . '_500_backup', $options );

			// Now that we got the options, we need to loop through them
			// and convert shortcodes in them.
			foreach ( $options as $key => $value ) {
				// Only convert options that are strings, not empty,
				// and contain the '[' character.
				if ( is_string( $value ) && ! empty( $value ) && false !== strpos( $value, '[' ) ) {
					$options[ $key ] = $this->convert_shortcode_names( $value );
				}
			}
			// Update the options.
			update_option( $option_name, $options );
		}

		// Is this a multilingual site?
		// If it is, then we need to convert options on a per-language basis.
		$available_languages = Fusion_Multilingual::get_available_languages();
		if ( ! empty( $available_languages ) ) {
			foreach ( $available_languages as $language ) {

				// The option name.
				$option_name = self::$avada_option_name . '_' . $language;

				// Get the options for that language.
				$options = get_option( $option_name, array() );

				if ( self::$revert ) {
					$backed_up_options = get_option( $option_name . '_500_backup', array() );
					if ( ! empty( $backed_up_options ) ) {
						update_option( $option_name, $backed_up_options );
					}
				} else {
					// Backup options.
					update_option( $option_name . '_500_backup', $options );

					// Now that we got the options, we need to loop through them
					// and convert shortcodes in them.
					foreach ( $options as $key => $value ) {
						// Only convert options that are strings, not empty,
						// and contain the '[' character.
						if ( is_string( $value ) && ! empty( $value ) && false !== strpos( $value, '[' ) ) {
							$options[ $key ] = $this->convert_shortcode_names( $value );
						}
					}
					// Update the options.
					update_option( $option_name, $options );
				}
			}
		}
		$this->next_step_redirection( true );
	}

	/**
	 * Convert container paddings.
	 *
	 * @since 5.0.0
	 * @param string $content The page contents.
	 * @param string $id The page id.
	 * @return string $content The page contents.
	 */
	private function convert_container_paddings( $content, $id ) {
		$needle          = '[fusion_builder_container';
		$last_pos        = -1;
		$position_change = 0;
		$positions       = array();

		// Get all positions of [fullwidth shortcode. @codingStandardsIgnoreLine
		while ( ( $last_pos = strpos( $content, $needle, $last_pos + 1 ) ) !== false ) {
			$positions[] = $last_pos;
		}

		foreach ( $positions as $position ) {

			// Fullwidth tag closing position.
			$section_close_position = strpos( $content, ']', $position + $position_change );

			// Shortcode attributes.
			$attributes = substr( $content, $position + $position_change, $section_close_position - $position + $position_change );

			foreach ( array( 'left', 'right' ) as $direction ) {
				if ( strpos( $attributes, 'padding' . $direction . '=' ) ) {
					$content = substr_replace( $content, 'padding_' . $direction . '=', $position + $position_change + strpos( $attributes, 'padding' . $direction . '=' ), strlen( 'padding' . $direction . '=' ) );
					$position_change = $position_change++;
				}
			}

			// If not 100% internal, check for 0px padding and convert to empty.
			if ( ! strpos( $attributes, 'hundred_percent="yes"' ) ) {
				foreach ( array( 'left', 'right' ) as $direction ) {
					if ( strpos( $attributes, 'padding_' . $direction . '="0px"' ) ) {
						$content = substr_replace( $content, 'padding_' . $direction . '=""   ', $position + $position_change + strpos( $attributes, 'padding_' . $direction . '="0px"' ), strlen( 'padding_' . $direction . '="0px"' ) );
					}
				}
			} else {
				// If it is 100% internal, look for empty, replace with either PO/TO left and right padding.
				foreach ( array( 'left', 'right' ) as $direction ) {
					$padding_value = ( fusion_get_page_option( 'pyre_hundredp_padding', $id ) ) ? fusion_get_page_option( 'pyre_hundredp_padding', $id ) : Avada()->settings->get( 'hundredp_padding' );

					// If no padding left present, then we need to add it.
					if ( ! strpos( $attributes, 'padding_' . $direction . '="' ) ) {
						$content = substr_replace( $content, ' padding_' . $direction . '="' . $padding_value . '" ', $section_close_position, 0 );
						$position_change = $position_change + strlen( 'padding_' . $direction . '=" ' . $padding_value . '" ' );

						// If there is a padding left, but its empty, we need to add value.
					} elseif ( strpos( $attributes, 'padding_' . $direction . '=""' ) ) {
						$content = substr_replace( $content, 'padding_' . $direction . '="' . $padding_value . '"', $position + $position_change + strpos( $attributes, 'padding_' . $direction . '=""' ), strlen( 'padding_' . $direction . '=""' ) );
						$position_change = $position_change + strlen( $padding_value );
					}
				}
			}
		} // End foreach().
		return $content;
	}

	/**
	 * Takes care of removing backup data.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param int $limit The number of posts to process.
	 */
	public static function cleanup_backups( $limit = 20 ) {

		// The step of the migration.
		// If on 0 then process options,
		// otherwise set it to the ID of the post we left off.
		$from = get_option( 'avada_migration_cleanup_id', 0 );

		if ( 0 >= $from ) {

			// Check the backup options.
			$backup_options = array(
				'avada_500_backup_page_templates_faqs',
				'avada_500_backup_page_templates_portfolio',
				'avada_500_backup_fusion_slider_data',
				'avada_500_backup_revslider',
				'avada_500_backup_layerslider',
				'avada_500_backup_layerslider',
				'avada_500_backup_widget_text',
				Avada::get_option_name() . '_500_backup',
			);

			// Delete backup options.
			foreach ( $backup_options as $option ) {
				delete_option( $option );
			}
			update_option( 'avada_migration_cleanup_id', 1 );
		} else {

			// The post types we'll need to check.
			$post_types = apply_filters( 'fusion_builder_shortcode_migration_post_types', array(
				'page',
				'post',
				'avada_faq',
				'avada_portfolio',
				'product',
				'tribe_events',
			) );
			foreach ( $post_types as $key => $post_type ) {
				if ( ! post_type_exists( $post_type ) ) {
					unset( $post_types[ $key ] );
				}
			}

			// Build the query array.
			$args = array(
				'posts_per_page' => $limit,
				'offset'         => $from,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => $post_types,
				'post_status'    => 'any',
			);

			// The query to get posts that meet our criteria.
			$posts = fusion_cached_get_posts( $args );

			if ( $posts ) {
				// Remove backup data for those posts.
				foreach ( $posts as $post ) {
					delete_post_meta( $post->ID, 'fusion_builder_content_backup' );
				}

				// Get the ID of the last post
				// and update the 'avada_migration_cleanup_id' option.
				$last_post = (array) end( $posts );
				if ( isset( $last_post['ID'] ) ) {
					// Update the value so that the next step starts where we left off.
					update_option( 'avada_migration_cleanup_id', ( absint( $last_post['ID'] ) + 1 ) );
				} else {
					// No post found, we're finished.
					delete_option( 'scheduled_avada_fusionbuilder_migration_cleanups' );
					delete_option( 'avada_migration_cleanup_id' );
				}
			} else {
				// No posts found, we're finished.
				delete_option( 'scheduled_avada_fusionbuilder_migration_cleanups' );
				delete_option( 'avada_migration_cleanup_id' );
			}
		} // End if().
	}

	/**
	 * WPML Hack.
	 *
	 * @param int     $pidd The post ID.
	 * @param WP_Post $post The post object.
	 */
	public function save_post_actions( $pidd, $post ) {

		global $wpml_root_page_actions, $wpml_post_translations;

		if ( $wpml_root_page_actions ) {
			remove_action( 'save_post', array( $wpml_root_page_actions, 'wpml_home_url_save_post_actions' ), 0, 2 );
		}
		if ( $wpml_post_translations ) {
			remove_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100, 2 );
		}
	}
}
/* Omit closing PHP tag to avoid "Headers already sent" issues. */
