<?php
/**
 * Templates handler.
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

/**
 * Template handler.
 */
class Avada_Template {

	/**
	 * An array of body classes to be added.
	 *
	 * @access private
	 * @since 5.0.0
	 * @var array
	 */
	private $body_classes = array();

	/**
	 * The class constructor
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'init' ), 20 );

		add_filter( 'the_password_form', array( $this, 'the_password_form' ) );
	}

	/**
	 * Initialize the class.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->body_classes = $this->body_classes( array() );

		add_filter( 'body_class', array( $this, 'body_class_filter' ) );
	}

	/**
	 * Detect if we have a sidebar.
	 */
	public function has_sidebar() {
		// Get our extra body classes.
		return ( in_array( 'has-sidebar', $this->body_classes ) );
	}

	/**
	 * Detect if we have double sidebars.
	 */
	public function double_sidebars() {

		// Get our extra body classes.
		return ( in_array( 'double-sidebars', $this->body_classes ) );
	}

	/**
	 * Returns the sidebar-1 & sidebar-2 context.
	 *
	 * @param int $sidebar Sidebar 1 or 2 (values: 1/2).
	 * @return mixed
	 */
	private function sidebar_context( $sidebar = 1 ) {

		$c_page_id = Avada()->fusion_library->get_page_id();

		$sidebar_1 = get_post_meta( $c_page_id, 'sbg_selected_sidebar_replacement', true );
		$sidebar_2 = get_post_meta( $c_page_id, 'sbg_selected_sidebar_2_replacement', true );

		if ( is_single() && ! is_singular( 'avada_portfolio' ) && ! is_singular( 'product' ) && ! Avada_Helper::is_bbpress() && ! Avada_Helper::is_buddypress() && ! is_singular( 'tribe_events' ) && ! is_singular( 'tribe_organizer' ) && ! is_singular( 'tribe_venue' ) ) {

			if ( Avada()->settings->get( 'posts_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'posts_sidebar' ) ) ? array( Avada()->settings->get( 'posts_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'posts_sidebar_2' ) ) ? array( Avada()->settings->get( 'posts_sidebar_2' ) ) : '';
			}

			if ( class_exists( 'Tribe__Events__Main' ) && Avada_Helper::tribe_is_event( $c_page_id ) && Avada()->settings->get( 'pages_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'pages_sidebar' ) ) ? array( Avada()->settings->get( 'pages_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'pages_sidebar_2' ) ) ? array( Avada()->settings->get( 'pages_sidebar_2' ) ) : '';
			}
		} elseif ( is_singular( 'avada_portfolio' ) ) {

			if ( Avada()->settings->get( 'portfolio_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'portfolio_sidebar' ) ) ? array( Avada()->settings->get( 'portfolio_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'portfolio_sidebar_2' ) ) ? array( Avada()->settings->get( 'portfolio_sidebar_2' ) ) : '';
			}
		} elseif ( is_singular( 'product' ) || ( class_exists( 'WooCommerce' ) && is_shop() ) ) {

			if ( Avada()->settings->get( 'woo_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'woo_sidebar' ) ) ? array( Avada()->settings->get( 'woo_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'woo_sidebar_2' ) ) ? array( Avada()->settings->get( 'woo_sidebar_2' ) ) : '';
			}
		} elseif ( ( is_page() || is_page_template() ) && ( ! is_page_template( '100-width.php' ) && ! is_page_template( 'blank.php' ) ) ) {

			if ( Avada()->settings->get( 'pages_global_sidebar' ) ) {

				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'pages_sidebar' ) ) ? array( Avada()->settings->get( 'pages_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'pages_sidebar_2' ) ) ? array( Avada()->settings->get( 'pages_sidebar_2' ) ) : '';

			}
		} elseif ( is_singular( 'tribe_events' ) ) {

			if ( Avada()->settings->get( 'ec_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' !== Avada()->settings->get( 'ec_sidebar' ) ) ? array( Avada()->settings->get( 'ec_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' !== Avada()->settings->get( 'ec_sidebar_2' ) ) ? array( Avada()->settings->get( 'ec_sidebar_2' ) ) : '';
			}
		} elseif ( is_singular( 'tribe_venue' ) || is_singular( 'tribe_organizer' ) ) {

			$sidebar_1 = ( 'None' !== Avada()->settings->get( 'ec_sidebar' ) ) ? array( Avada()->settings->get( 'ec_sidebar' ) ) : '';
			$sidebar_2 = ( 'None' !== Avada()->settings->get( 'ec_sidebar_2' ) ) ? array( Avada()->settings->get( 'ec_sidebar_2' ) ) : '';
		} // End if().

		if ( is_home() ) {
			$sidebar_1 = Avada()->settings->get( 'blog_archive_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'blog_archive_sidebar_2' );
		}

		if ( is_archive() && ( ! Avada_Helper::is_buddypress() && ! Avada_Helper::is_bbpress() && ( class_exists( 'WooCommerce' ) && ! is_shop() ) || ! class_exists( 'WooCommerce' ) ) && ! is_post_type_archive( 'avada_portfolio' ) && ! is_tax( 'portfolio_category' ) && ! is_tax( 'portfolio_skills' )  && ! is_tax( 'portfolio_tags' ) && ! is_tax( 'product_cat' ) && ! is_tax( 'product_tag' ) ) {
			$sidebar_1 = Avada()->settings->get( 'blog_archive_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'blog_archive_sidebar_2' );
		}

		if ( is_post_type_archive( 'avada_portfolio' ) || is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skills' )  || is_tax( 'portfolio_tags' ) ) {
			$sidebar_1 = Avada()->settings->get( 'portfolio_archive_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'portfolio_archive_sidebar_2' );
		}

		if ( class_exists( 'WooCommerce' ) && ( ( Avada_Helper::is_woocommerce() && is_tax() ) || is_tax( 'product_brand' ) || is_tax( 'images_collections' ) || is_tax( 'shop_vendor' ) ) ) {
			$sidebar_1 = Avada()->settings->get( 'woocommerce_archive_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'woocommerce_archive_sidebar_2' );
		}

		if ( is_search() ) {
			$sidebar_1 = Avada()->settings->get( 'search_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'search_sidebar_2' );
		}

		if ( ( ( class_exists( 'bbPress' ) && Avada_Helper::is_bbpress() ) || Avada_Helper::is_buddypress() ) && ! ( class_exists( 'bbPress' ) && Avada_Helper::bbp_is_forum_archive() ) && ! ( class_exists( 'bbPress' ) && Avada_Helper::bbp_is_topic_archive() ) && ! ( class_exists( 'bbPress' ) && Avada_Helper::bbp_is_user_home() ) && ! ( class_exists( 'bbPress' ) && Avada_Helper::bbp_is_search() ) ) {
			$sidebar_1 = Avada()->settings->get( 'ppbress_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'ppbress_sidebar_2' );

			if ( Avada()->settings->get( 'bbpress_global_sidebar' ) ) {
				$sidebar_1 = Avada()->settings->get( 'ppbress_sidebar' );
				$sidebar_2 = Avada()->settings->get( 'ppbress_sidebar_2' );
			} else {
				$sidebar_1 = get_post_meta( $c_page_id, 'sbg_selected_sidebar_replacement', true );
				$sidebar_2 = get_post_meta( $c_page_id, 'sbg_selected_sidebar_2_replacement', true );
			}
		}

		if ( ( ( class_exists( 'bbPress' ) && Avada_Helper::is_bbpress() ) || Avada_Helper::is_buddypress() ) && ( class_exists( 'bbPress' ) && ( Avada_Helper::bbp_is_forum_archive() || Avada_Helper::bbp_is_topic_archive() || Avada_Helper::bbp_is_user_home() || Avada_Helper::bbp_is_search() ) ) ) {
			$sidebar_1 = Avada()->settings->get( 'ppbress_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'ppbress_sidebar_2' );
		}

		if ( class_exists( 'Tribe__Events__Main' ) && Avada_Helper::is_events_archive() ) {
			$sidebar_1 = Avada()->settings->get( 'ec_sidebar' );
			$sidebar_2 = Avada()->settings->get( 'ec_sidebar_2' );
		}

		if ( 1 == $sidebar ) {
			return $sidebar_1;
		} elseif ( 2 == $sidebar ) {
			return $sidebar_2;
		}

	}


	/**
	 * Adds extra classes for the <body> element, using the 'body_class' filter.
	 * Documentation: https://codex.wordpress.org/Plugin_API/Filter_Reference/body_class
	 *
	 * @since 5.0.0
	 *
	 * @param  array $classes CSS classes.
	 * @return array The merged and extended body classes.
	 */
	public function body_class_filter( $classes ) {
		$classes = array_merge( $classes, $this->body_classes );

		return $classes;
	}

	/**
	 * Calculate any extra classes for the <body> element.
	 *
	 * @param  array $classes CSS classes.
	 * @return array The needed body classes.
	 */
	private function body_classes( $classes ) {

		$sidebar_1 = $this->sidebar_context( 1 );
		$sidebar_2 = $this->sidebar_context( 2 );
		$c_page_id  = Avada()->fusion_library->get_page_id();

		$classes[] = 'fusion-body';

		if ( ! is_rtl() ) {
			$classes[] = 'ltr';
		}

		if ( is_page_template( 'blank.php' ) ) {
			$classes[] = 'body_blank';
		}

		if ( ! Avada()->settings->get( 'header_sticky_tablet' ) ) {
			$classes[] = 'no-tablet-sticky-header';
		}
		if ( ! Avada()->settings->get( 'header_sticky_mobile' ) ) {
			$classes[] = 'no-mobile-sticky-header';
		}
		if ( ! Avada()->settings->get( 'mobile_slidingbar_widgets' ) ) {
			$classes[] = 'no-mobile-slidingbar';
		}
		if ( ! Avada()->settings->get( 'status_totop' ) ) {
			$classes[] = 'no-totop';
		}
		if ( ! Avada()->settings->get( 'status_totop_mobile' ) ) {
			$classes[] = 'no-mobile-totop';
		}
		if ( 'horizontal' === Avada()->settings->get( 'woocommerce_product_tab_design' ) &&
			 ( is_singular( 'product' ) || class_exists( 'Woocommerce' ) && ( is_account_page() || is_checkout() ) )
		) {
			$classes[] = 'woo-tabs-horizontal';
		}

		if ( 'modern' === Avada()->settings->get( 'mobile_menu_design' ) ) {
			$classes[] = 'mobile-logo-pos-' . strtolower( Avada()->settings->get( 'logo_alignment' ) );
		}

		$page_bg_layout = get_post_meta( $c_page_id, 'pyre_page_bg_layout', true );
		if ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && 'default' === $page_bg_layout ) || 'boxed' === $page_bg_layout ) {
			$classes[] = 'layout-boxed-mode';
		} else {
			$classes[] = 'layout-wide-mode';
		}

		if ( is_array( $sidebar_1 ) && ! empty( $sidebar_1 ) && ( $sidebar_1[0] || '0' == $sidebar_1[0] ) && ! Avada_Helper::is_buddypress() && ! Avada_Helper::is_bbpress() && ! is_page_template( '100-width.php' ) && ! is_page_template( 'blank.php' ) && ( ! class_exists( 'WooCommerce' ) || ( class_exists( 'WooCommerce' ) && ! is_cart() && ! is_checkout() && ! is_account_page() && ! ( get_option( 'woocommerce_thanks_page_id' ) && is_page( get_option( 'woocommerce_thanks_page_id' ) ) ) ) ) ) {
			$classes[] = 'has-sidebar';
		}

		if ( is_array( $sidebar_1 ) && $sidebar_1[0] && is_array( $sidebar_2 ) && $sidebar_2[0] && ! Avada_Helper::is_buddypress() && ! Avada_Helper::is_bbpress() && ! is_page_template( '100-width.php' )  && ! is_page_template( 'blank.php' ) && ( ! class_exists( 'WooCommerce' ) || ( class_exists( 'WooCommerce' ) && ! is_cart() && ! is_checkout() && ! is_account_page() && ! ( get_option( 'woocommerce_thanks_page_id' ) && is_page( get_option( 'woocommerce_thanks_page_id' ) ) ) ) ) ) {
			$classes[] = 'double-sidebars';
		}

		if ( is_page_template( 'side-navigation.php' ) && 0 !== get_queried_object_id() ) {
			$classes[] = 'has-sidebar';

			if ( is_array( $sidebar_2 ) && $sidebar_2[0] ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_home() ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_archive() && ( ! ( class_exists( 'BuddyPress' ) && Avada_Helper::is_buddypress() ) && ! ( class_exists( 'bbPress' ) && Avada_Helper::is_bbpress() ) && ( class_exists( 'WooCommerce' ) && ! is_shop() ) || ! class_exists( 'WooCommerce' ) ) && ! is_tax( 'portfolio_category' ) && ! is_tax( 'portfolio_skills' )  && ! is_tax( 'portfolio_tags' ) && ! is_tax( 'product_cat' ) && ! is_tax( 'product_tag' ) ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skills' )  || is_tax( 'portfolio_tags' ) ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_search() ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( ( Avada_Helper::is_bbpress() || Avada_Helper::is_buddypress() ) && ! Avada_Helper::bbp_is_forum_archive() && ! Avada_Helper::bbp_is_topic_archive() && ! Avada_Helper::bbp_is_user_home() && ! Avada_Helper::bbp_is_search() ) {
			if ( Avada()->settings->get( 'bbpress_global_sidebar' ) ) {
				if ( 'None' !== $sidebar_1 ) {
					$classes[] = 'has-sidebar';
				}
				if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
					$classes[] = 'double-sidebars';
				}
			} else {
				if ( is_array( $sidebar_1 ) && $sidebar_1[0] ) {
					$classes[] = 'has-sidebar';
				}
				if ( is_array( $sidebar_1 ) && $sidebar_1[0] && is_array( $sidebar_2 ) && $sidebar_2[0] ) {
					$classes[] = 'double-sidebars';
				}
			}
		}

		if ( ( Avada_Helper::is_bbpress() || Avada_Helper::is_buddypress() ) && ( Avada_Helper::bbp_is_forum_archive() || Avada_Helper::bbp_is_topic_archive() || Avada_Helper::bbp_is_user_home() || Avada_Helper::bbp_is_search() ) ) {
			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( class_exists( 'Tribe__Events__Main' ) && Avada_Helper::is_events_archive() ) {
			$classes[] = 'tribe-filter-live';

			if ( 'None' !== $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' !== $sidebar_1 && 'None' !== $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( 'no' !== get_post_meta( $c_page_id, 'pyre_display_header', true ) ) {
			if ( 'Left' === Avada()->settings->get( 'header_position' ) || 'Right' === Avada()->settings->get( 'header_position' ) ) {
				$classes[] = 'side-header';
			} else {
				$classes[] = 'fusion-top-header';
			}
			if ( 'Left' === Avada()->settings->get( 'header_position' ) ) {
				$classes[] = 'side-header-left';
			} elseif ( 'Right' === Avada()->settings->get( 'header_position' ) ) {
				$classes[] = 'side-header-right';
			}
			$classes[] = 'menu-text-align-' . strtolower( Avada()->settings->get( 'menu_text_align' ) );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$classes[] = 'fusion-woo-product-design-' . Avada()->settings->get( 'woocommerce_product_box_design' );
		}

		$classes[] = 'mobile-menu-design-' . Avada()->settings->get( 'mobile_menu_design' );

		$classes[] = 'fusion-image-hovers';

		if ( Avada()->settings->get( 'pagination_text_display' ) ) {
			$classes[] = 'fusion-show-pagination-text';
		} else {
			$classes[] = 'fusion-hide-pagination-text';
		}

		return $classes;
	}

	/**
	 * The comment template.
	 *
	 * @access public
	 * @param string     $comment The comment.
	 * @param array      $args    The comment arguments.
	 * @param int|string $depth   The comment depth.
	 */
	public function comment_template( $comment, $args, $depth ) {
		?>
		<?php $add_below = ''; ?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<div class="the-comment">
				<div class="avatar"><?php echo get_avatar( $comment, 54 ); ?></div>
				<div class="comment-box">
					<div class="comment-author meta">
						<strong><?php echo get_comment_author_link(); ?></strong>
						<?php printf( esc_attr__( '%1$s at %2$s', 'Avada' ), get_comment_date(),  get_comment_time() ); ?><?php edit_comment_link( __( ' - Edit', 'Avada' ),'  ','' ); ?><?php comment_reply_link( array_merge( $args, array(
							'reply_text' => __( ' - Reply', 'Avada' ),
							'add_below' => 'comment',
							'depth' => $depth,
							'max_depth' => $args['max_depth'],
						) ) ); ?>
					</div>
					<div class="comment-text">
						<?php if ( '0' == $comment->comment_approved ) : ?>
							<em><?php esc_attr_e( 'Your comment is awaiting moderation.', 'Avada' ); ?></em>
							<br />
						<?php endif; ?>
						<?php comment_text() ?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * The password protected form template.
	 *
	 * @since 5.1
	 * @access public
	 * @param string $output The form HTML.
	 * @return string The changed output depth.
	 */
	public function the_password_form( $output ) {

		$output = str_replace( 'type="submit"', 'class="fusion-button button-default fusion-button-default-size" type="submit"', $output );

		return $output;
	}

	/**
	 * The title template.
	 *
	 * @access public
	 * @param string     $content       The content.
	 * @param int|string $size          The size.
	 * @param string     $content_align The content alignment.
	 */
	public function title_template( $content = '', $size = '2', $content_align = 'left' ) {
		$margin_top	    = Avada()->settings->get( 'title_margin', 'top' );
		$margin_bottom	= Avada()->settings->get( 'title_margin', 'bottom' );
		$sep_color      = Avada()->settings->get( 'title_border_color' );
		$style_type	    = Avada()->settings->get( 'title_style_type' );
		$size_array     = array(
			'1' => 'one',
			'2' => 'two',
			'3' => 'three',
			'4' => 'four',
			'5' => 'five',
			'6' => 'six',
		);
		$classes        = '';
		$styles         = '';
		$sep_styles     = '';

		$classes_array = explode( ' ', $style_type );
		foreach ( $classes_array as $class ) {
			$classes .= ' sep-' . $class;
		}

		if ( $margin_top ) {
			$styles .= sprintf( 'margin-top:%s;', Fusion_Sanitize::get_value_with_unit( $margin_top ) );
		}
		if ( $margin_bottom ) {
			$styles .= sprintf( 'margin-bottom:%s;', Fusion_Sanitize::get_value_with_unit( $margin_bottom ) );
		}

		if ( false !== strpos( $style_type, 'underline' ) || false !== strpos( $style_type, 'none' ) ) {

			if ( false !== strpos( $style_type, 'underline' ) && $sep_color ) {
				$styles .= 'border-bottom-color:' . $sep_color;
			} elseif ( false !== strpos( $style_type, 'none' ) ) {
				$classes .= ' fusion-sep-none';
			}
			?>
			<div class="fusion-title fusion-title-size-<?php echo esc_attr( $size_array[ $size ] ); ?><?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $styles ); ?>">
				<h<?php echo (int) $size; ?> class="title-heading-<?php echo esc_attr( $content_align ); ?>">
					<?php echo $content; // WPCS: XSS ok. ?>
				</h<?php echo (int) $size; ?>>
			</div>
			<?php
		} else {
			if ( 'right' === $content_align ) {
				?>
				<div class="fusion-title fusion-title-size-<?php echo esc_attr( $size_array[ $size ] ); ?><?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $styles ); ?>">
					<div class="title-sep-container">
						<div class="title-sep<?php echo esc_attr( $classes ); ?>"></div>
					</div>
					<h<?php echo (int) $size; ?> class="title-heading-<?php echo esc_attr( $content_align ); ?>">
						<?php echo $content; // WPCS: XSS ok. ?>
					</h<?php echo (int) $size; ?>>
				</div>
				<?php
			} elseif ( 'center' === $content_align ) {
				?>
				<div class="fusion-title fusion-title-center fusion-title-size-<?php echo esc_attr( $size_array[ $size ] ); ?><?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $styles ); ?>">
					<div class="title-sep-container title-sep-container-left">
						<div class="title-sep<?php echo esc_attr( $classes ); ?>"></div>
					</div>
					<h<?php echo (int) $size; ?> class="title-heading-<?php echo esc_attr( $content_align ); ?>">
						<?php echo $content; // WPCS: XSS ok. ?>
					</h<?php echo (int) $size; ?>>
					<div class="title-sep-container title-sep-container-right">
						<div class="title-sep<?php echo esc_attr( $classes ); ?>"></div>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="fusion-title fusion-title-size-<?php echo esc_attr( $size_array[ $size ] ); ?><?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( $styles ); ?>">
					<h<?php echo (int) $size; ?> class="title-heading-<?php echo esc_attr( $content_align ); ?>">
						<?php echo $content; // WPCS: XSS ok. ?>
					</h<?php echo (int) $size; ?>>
					<div class="title-sep-container">
						<div class="title-sep<?php echo esc_attr( $classes ); ?>"></div>
					</div>
				</div>
				<?php
			} // End if().
		} // End if().
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
