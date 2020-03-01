<?php
/**
 * Contains all theme-specific functions.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if ( ! function_exists( 'avada_render_blog_post_content' ) ) {
	/**
	 * Get the post (excerpt).
	 *
	 * @return void Content is directly echoed.
	 */
	function avada_render_blog_post_content() {
		if ( is_search() && ! Avada()->settings->get( 'search_excerpt' ) ) {
			return;
		}
		echo fusion_get_post_content(); // WPCS: XSS ok.
	}
}
add_action( 'avada_blog_post_content', 'avada_render_blog_post_content', 10 );

if ( ! function_exists( 'avada_render_portfolio_post_content' ) ) {
	/**
	 * Get the portfolio post (excerpt).
	 *
	 * @param  int|string $page_id The page ID.
	 * @return void
	 */
	function avada_render_portfolio_post_content( $page_id ) {
		echo fusion_get_post_content( $page_id, 'portfolio' ); // WPCS: XSS ok.
	}
}
add_action( 'avada_portfolio_post_content', 'avada_render_portfolio_post_content', 10 );

if ( ! function_exists( 'avada_render_blog_post_date' ) ) {
	/**
	 * Render the HTML for the date box for large/medium alternate blog layouts.
	 *
	 * @return void
	 */
	function avada_render_blog_post_date() {
		get_template_part( 'templates/blog-post-date' );
	}
}
add_action( 'avada_blog_post_date_and_format', 'avada_render_blog_post_date', 10 );

if ( ! function_exists( 'avada_render_blog_post_format' ) ) {
	/**
	 * Render the HTML for the format box for large/medium alternate blog layouts.
	 *
	 * @return void
	 */
	function avada_render_blog_post_format() {
		get_template_part( 'templates/post-format-box' );
	}
}
add_action( 'avada_blog_post_date_and_format', 'avada_render_blog_post_format', 15 );

if ( ! function_exists( 'avada_render_author_info' ) ) {
	/**
	 * Output author information on the author archive page.
	 *
	 * @return void
	 */
	function avada_render_author_info() {
		get_template_part( 'templates/author-info' );
	}
}
add_action( 'avada_author_info', 'avada_render_author_info', 10 );

if ( ! function_exists( 'avada_render_footer_copyright_notice' ) ) {
	/**
	 * Output the footer copyright notice.
	 *
	 * @return void
	 */
	function avada_render_footer_copyright_notice() {
		get_template_part( 'templates/footer-copyright-notice' );
	}
}
add_action( 'avada_footer_copyright_content', 'avada_render_footer_copyright_notice', 10 );

if ( ! function_exists( 'avada_render_footer_social_icons' ) ) {
	/**
	 * Output the footer social icons.
	 *
	 * @return void
	 */
	function avada_render_footer_social_icons() {
		global $social_icons;

		// Render the social icons.
		if ( Avada()->settings->get( 'icons_footer' ) ) : ?>
			<div class="fusion-social-links-footer">
				<?php

				$footer_social_icon_options = array(
					'position'          => 'footer',
					'icon_colors'       => Avada()->settings->get( 'footer_social_links_icon_color' ),
					'box_colors'        => Avada()->settings->get( 'footer_social_links_box_color' ),
					'icon_boxed'        => Avada()->settings->get( 'footer_social_links_boxed' ),
					'icon_boxed_radius' => Fusion_Sanitize::size( Avada()->settings->get( 'footer_social_links_boxed_radius' ) ),
					'tooltip_placement' => Avada()->settings->get( 'footer_social_links_tooltip_placement' ),
					'linktarget'        => Avada()->settings->get( 'social_icons_new' ),
				);

				echo $social_icons->render_social_icons( $footer_social_icon_options ); // WPCS: XSS ok.
				?>
			</div>
		<?php endif;
	}
}
add_action( 'avada_footer_copyright_content', 'avada_render_footer_social_icons', 15 );

if ( ! function_exists( 'avada_render_placeholder_image' ) ) {
	/**
	 * Action to output a placeholder image.
	 *
	 * @param  string $featured_image_size     Size of the featured image that should be emulated.
	 *
	 * @return void
	 */
	function avada_render_placeholder_image( $featured_image_size = 'full' ) {
		global $_wp_additional_image_sizes;

		if ( in_array( $featured_image_size, array( 'full', 'fixed' ) ) ) {
			$height = apply_filters( 'avada_set_placeholder_image_height', '150' );
			$width  = '1500px';
		} else {
			// @codingStandardsIgnoreStart
			@$height = $_wp_additional_image_sizes[ $featured_image_size ]['height'];
			@$width  = $_wp_additional_image_sizes[ $featured_image_size ]['width'] . 'px';
			// @codingStandardsIgnoreEnd
		}
		?>
		 <div class="fusion-placeholder-image" data-origheight="<?php echo esc_attr( $height ); ?>" data-origwidth="<?php echo esc_attr( $width ); ?>" style="height:<?php echo esc_attr( $height ); ?>px;width:<?php echo esc_attr( $width ); ?>;"></div>
		<?php
	}
}
add_action( 'avada_placeholder_image', 'avada_render_placeholder_image', 10 );

if ( ! function_exists( 'avada_get_image_orientation_class' ) ) {
	/**
	 * Returns the image class according to aspect ratio.
	 *
	 * @param  array $attachment The attachment.
	 * @return string The image class.
	 */
	function avada_get_image_orientation_class( $attachment ) {

		$sixteen_to_nine_ratio = 1.77;

		if ( ! isset( $attachment[1] ) || ! isset( $attachment[2] ) || empty( $attachment[1] ) || empty( $attachment[2] ) ) {
			return 'fusion-image-grid';
		}

		// Landscape.
		if ( $attachment[1] / $attachment[2] > $sixteen_to_nine_ratio ) {
			return 'fusion-image-landscape';
		}

		// Portrait.
		if ( $attachment[2] / $attachment[1] > $sixteen_to_nine_ratio ) {
			return 'fusion-image-portrait';
		}
	}
}

if ( ! function_exists( 'avada_render_post_title' ) ) {
	/**
	 * Render the post title as linked h1 tag.
	 *
	 * @param  int|string $post_id      The post ID.
	 * @param  bool       $linked       If we want it linked.
	 * @param  string     $custom_title A Custom title.
	 * @param  string|int $custom_size  A custom size.
	 * @param  string|int $custom_link  A custom link.
	 * @return string                   The post title as linked h1 tag.
	 */
	function avada_render_post_title( $post_id = '', $linked = true, $custom_title = '', $custom_size = '2', $custom_link = '' ) {

		$entry_title_class = '';

		// Add the entry title class if rich snippets are enabled.
		if ( Avada()->settings->get( 'disable_date_rich_snippet_pages' ) && Avada()->settings->get( 'disable_rich_snippet_title' ) ) {
			$entry_title_class = ' class="entry-title fusion-post-title"';
		} else {
			$entry_title_class = ' class="fusion-post-title"';
		}

		// If we have a custom title, use it otherwise get post title.
		$title = ( $custom_title ) ? $custom_title : get_the_title( $post_id );
		$permalink = ( $custom_link ) ? $custom_link : get_permalink( $post_id );

		// If the post title should be linked at the markup.
		if ( $linked ) {
			$link_target = '';
			if ( 'yes' == fusion_get_page_option( 'link_icon_target', $post_id ) || 'yes' == fusion_get_page_option( 'post_links_target', $post_id ) ) {
				$link_target = ' target="_blank" rel="noopener noreferrer"';
			}
			$title = '<a href="' . $permalink . '"' . $link_target . '>' . $title . '</a>';
		}

		// Return the HTML markup of the post title.
		return '<h' . $custom_size . $entry_title_class . '>' . $title . '</h' . $custom_size . '>';

	}
} // End if().

if ( ! function_exists( 'avada_get_portfolio_classes' ) ) {
	/**
	 * Determine the css classes need for portfolio page content container.
	 *
	 * @param  int|string $post_id The post ID.
	 * @return string The classes separated with space.
	 */
	function avada_get_portfolio_classes( $post_id = '' ) {

		$classes = 'fusion-portfolio';

		// Get the page template slug without .php suffix.
		$page_template = str_replace( '.php', '', get_page_template_slug( $post_id ) );

		// Add the text class, if a text layout is used.
		if ( strpos( $page_template, 'text' ) || strpos( $page_template, 'one' ) ) {
			$classes .= ' fusion-portfolio-text';
		}

		// If one column text layout is used, add special class.
		if ( strpos( $page_template, 'one' ) && ! strpos( $page_template, 'text' ) ) {
			$classes .= ' fusion-portfolio-one-nontext';
		}

		// For text layouts add the class for boxed/unboxed.
		if ( strpos( $page_template, 'text' ) ) {
			$classes .= ' fusion-portfolio-' . fusion_get_option( 'portfolio_text_layout', 'portfolio_text_layout', $post_id ) . ' ';
			$page_template = str_replace( '-text', '', $page_template );
		}

		// Add the column class.
		$page_template = str_replace( '-column', '', $page_template );
		return $classes . ' fusion-' . $page_template;

	}
} // End if().

if ( ! function_exists( 'avada_get_image_size_dimensions' ) ) {
	/**
	 * Get Image dimensions.
	 *
	 * @param  string $image_size The Image size (obviously).
	 * @return array
	 */
	function avada_get_image_size_dimensions( $image_size = 'full' ) {
		global $_wp_additional_image_sizes;

		if ( 'full' === $image_size ) {
			$image_dimension = array(
				'height' => 'auto',
				'width' => '100%',
			);
		} else {
			if ( 'portfolio-six' === $image_size ) {
				$image_size = 'portfolio-five';
			} elseif ( 'portfolio-four' === $image_size ) {
				$image_size = 'portfolio-three';
			}
			$image_dimension = array(
				'height' => $_wp_additional_image_sizes[ $image_size ]['height'] . 'px',
				'width' => $_wp_additional_image_sizes[ $image_size ]['width'] . 'px',
			);
		}

		return $image_dimension;
	}
}

if ( ! function_exists( 'avada_get_portfolio_image_size' ) ) {
	/**
	 * The portfolio Imge Size.
	 *
	 * @param  int $current_page_id The ID of the current page.
	 * @return string
	 */
	function avada_get_portfolio_image_size( $current_page_id ) {

		$custom_image_size = 'full';
		if ( is_page_template( 'portfolio-one-column-text.php' ) ) {
			$custom_image_size = 'portfolio-full';
		} elseif ( is_page_template( 'portfolio-one-column.php' ) ) {
			$custom_image_size = 'portfolio-one';
		} elseif ( is_page_template( 'portfolio-two-column.php' ) || is_page_template( 'portfolio-two-column-text.php' ) ) {
			$custom_image_size = 'portfolio-two';
		} elseif ( is_page_template( 'portfolio-three-column.php' ) || is_page_template( 'portfolio-three-column-text.php' ) ) {
			$custom_image_size = 'portfolio-three';
		} elseif ( is_page_template( 'portfolio-four-column.php' ) || is_page_template( 'portfolio-four-column-text.php' ) ) {
			$custom_image_size = 'portfolio-three';
		} elseif ( is_page_template( 'portfolio-five-column.php' ) || is_page_template( 'portfolio-five-column-text.php' ) ) {
			$custom_image_size = 'portfolio-five';
		} elseif ( is_page_template( 'portfolio-six-column.php' ) || is_page_template( 'portfolio-six-column-text.php' ) ) {
			$custom_image_size = 'portfolio-five';
		}

		$portfolio_featured_image_size = get_post_meta( $current_page_id, 'pyre_portfolio_featured_image_size', true );
		if ( 'default' === $portfolio_featured_image_size || ! $portfolio_featured_image_size ) {
			$featured_image_size = ( 'full' === Avada()->settings->get( 'portfolio_featured_image_size' ) ) ? 'full' : $custom_image_size;
		} elseif ( 'full' === $portfolio_featured_image_size ) {
			$featured_image_size = 'full';
		} else {
			$featured_image_size = $custom_image_size;
		}

		if ( is_page_template( 'portfolio-grid.php' ) ) {
			$featured_image_size = 'full';
		}

		return $featured_image_size;
	}
} // End if().

if ( ! function_exists( 'avada_get_blog_layout' ) ) {
	/**
	 * Get the blog layout for the current page template.
	 *
	 * @return string The correct layout name for the blog post class.
	 */
	function avada_get_blog_layout() {
		$theme_options_blog_var = '';

		if ( is_home() ) {
			$theme_options_blog_var = 'blog_layout';
		} elseif ( is_archive() || is_author() ) {
			$theme_options_blog_var = 'blog_archive_layout';
		} elseif ( is_search() ) {
			$theme_options_blog_var = 'search_layout';
		}

		return str_replace( ' ', '-', strtolower( Avada()->settings->get( $theme_options_blog_var ) ) );
	}
}

if ( ! function_exists( 'avada_render_social_sharing' ) ) {
	/**
	 * Renders social sharing links.
	 *
	 * @param string $post_type The post-type.
	 * @return void
	 */
	function avada_render_social_sharing( $post_type = 'post' ) {
		include wp_normalize_path( locate_template( 'templates/social-sharing.php' ) );
	}
}

if ( ! function_exists( 'avada_render_related_posts' ) ) {
	/**
	 * Render related posts carousel.
	 *
	 * @param  string $post_type The post type to determine correct related posts and headings.
	 * @return void              Directly includes the template file.
	 */
	function avada_render_related_posts( $post_type = 'post' ) {

		// Set the needed variables according to post type.
		if ( 'post' === $post_type ) {
			$theme_option_name = 'related_posts';
			$main_heading      = esc_html__( 'Related Posts', 'Avada' );
		} elseif ( 'avada_portfolio' === $post_type ) {
			$theme_option_name = 'portfolio_related_posts';
			$main_heading      = esc_html__( 'Related Projects', 'Avada' );
		} elseif ( 'avada_faq' === $post_type ) {
			$theme_option_name = 'faq_related_posts';
			$main_heading      = esc_html__( 'Related Faqs', 'Avada' );
		}

		// Check if related posts should be shown.
		if ( isset( $theme_option_name ) && ( 'yes' === fusion_get_option( $theme_option_name, 'related_posts', get_the_ID() ) || '1' == fusion_get_option( $theme_option_name, 'related_posts', get_the_ID() ) ) ) {
			$number_related_posts = Avada()->settings->get( 'number_related_posts' );
			$number_related_posts = ( '0' == $number_related_posts ) ? '-1' : $number_related_posts;
			if ( 'post' === $post_type ) {
				$related_posts = fusion_get_related_posts( get_the_ID(), $number_related_posts );
			} else {
				$related_posts = fusion_get_custom_posttype_related_posts( get_the_ID(), $number_related_posts, $post_type );
			}

			// If there are related posts, display them.
			if ( isset( $related_posts ) && $related_posts->have_posts() ) {
				include wp_normalize_path( locate_template( 'templates/related-posts.php' ) );
			}
		}
	}
} // End if().

if ( ! function_exists( 'avada_page_title_bar' ) ) {
	/**
	 * Render the HTML markup of the page title bar.
	 *
	 * @param  string $title             Main title; page/post title or custom title set by user.
	 * @param  string $subtitle          Subtitle as custom user setting.
	 * @param  string $secondary_content HTML markup of the secondary content; breadcrumbs or search field.
	 * @return void
	 */
	function avada_page_title_bar( $title, $subtitle, $secondary_content ) {
		$post_id = get_queried_object_id();
		$alignment = '';

		// Check for the secondary content.
		$content_type = 'none';
		if ( false !== strpos( $secondary_content, 'searchform' ) ) {
			$content_type = 'search';
		} elseif ( '' != $secondary_content ) {
			$content_type = 'breadcrumbs';
		}

		// Check the position of page title.
		$page_title_text_alignment = get_post_meta( $post_id, 'pyre_page_title_text_alignment', true );
		if ( $page_title_text_alignment && 'default' !== $page_title_text_alignment ) {
			$alignment = $page_title_text_alignment;
		} elseif ( Avada()->settings->get( 'page_title_alignment' ) ) {
			$alignment = Avada()->settings->get( 'page_title_alignment' );
		}

		// Render the page title bar.
		include wp_normalize_path( locate_template( 'templates/title-bar.php' ) );
	}
}

if ( ! function_exists( 'avada_add_login_box_to_nav' ) ) {
	/**
	 * Add woocommerce cart to main navigation or top navigation.
	 *
	 * @param  string $items HTML for the main menu items.
	 * @param  array  $args  Arguments for the WP menu.
	 * @return string
	 */
	function avada_add_login_box_to_nav( $items, $args ) {

		$ubermenu = ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ubermenu_get_menu_instance_by_theme_location( $args->theme_location ) ) ? true : false; // Disable woo cart on ubermenu navigations.

		if ( $ubermenu ) {
			return $items;
		}
		if ( in_array( $args->theme_location, array( 'main_navigation', 'top_navigation', 'sticky_navigation' ) ) ) {
			$is_enabled = ( 'top_navigation' == $args->theme_location ) ? Avada()->settings->get( 'woocommerce_acc_link_top_nav' ) : Avada()->settings->get( 'woocommerce_acc_link_main_nav' );

			if ( class_exists( 'WooCommerce' ) && $is_enabled ) {
				$woo_account_page_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				$logout_link = wp_logout_url( get_permalink( fusion_wc_get_page_id( 'myaccount' ) ) );

				if ( $woo_account_page_link ) {
					$active_classes = '';
					if ( is_account_page() ) {
						$active_classes = ' current-menu-item current_page_item';
					}

					$items .= '<li class="fusion-custom-menu-item fusion-menu-login-box' . $active_classes . '">';

						// If chosen in Theme Options, display the caret icon, as the my account item alyways has a dropdown.
						$caret_icon = '';
					if ( Avada()->settings->get( 'menu_display_dropdown_indicator' ) && 'v6' != Avada()->settings->get( 'header_layout' ) ) {
						$caret_icon = '<span class="fusion-caret"><i class="fusion-dropdown-indicator"></i></span>';
					}

						$my_account_link_contents = ( 'Right' == Avada()->settings->get( 'header_position' ) ) ? $caret_icon . esc_html__( 'My Account', 'Avada' ) : esc_html__( 'My Account', 'Avada' );

						$items .= '<a href="' . $woo_account_page_link . '"><span class="menu-text">' . $my_account_link_contents . '</span>' . $caret_icon . '</a>';

					if ( ! is_user_logged_in() ) {
						$items .= '<div class="fusion-custom-menu-item-contents">';
						if ( isset( $_GET['login'] ) && 'failed' == $_GET['login'] ) {
							$items .= '<p class="fusion-menu-login-box-error">' . esc_html__( 'Login failed, please try again.', 'Avada' ) . '</p>';
						}
						$items .= '<form action="' . wp_login_url() . '" name="loginform" method="post">';
						$items .= '<p><input type="text" class="input-text" name="log" id="username" value="" placeholder="' . esc_html__( 'Username', 'Avada' ) . '" /></p>';
						$items .= '<p><input type="password" class="input-text" name="pwd" id="password" value="" placeholder="' . esc_html__( 'Password', 'Avada' ) . '" /></p>';
						$items .= '<p class="fusion-remember-checkbox"><label for="fusion-menu-login-box-rememberme"><input name="rememberme" type="checkbox" id="fusion-menu-login-box-rememberme" value="forever"> ' . esc_html__( 'Remember Me', 'Avada' ) . '</label></p>';
						$items .= '<input type="hidden" name="fusion_woo_login_box" value="true" />';
						$items .= '<p class="fusion-login-box-submit">';
						$items .= '<input type="submit" name="wp-submit" id="wp-submit" class="button button-small default comment-submit" value="' . esc_html__( 'Log In', 'Avada' ) . '">';
						$items .= '<input type="hidden" name="redirect" value="' . esc_url( ( isset( $_SERVER['HTTP_REFERER'] ) ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) . '">';
						$items .= '</p>';
						$items .= '</form>';
						$items .= '<a class="fusion-menu-login-box-register" href="' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '" title="' . esc_attr__( 'Register', 'Avada' ) . '">' . esc_attr__( 'Register', 'Avada' ) . '</a>';
						$items .= '</div>';
					} else {
						$items .= '<ul class="sub-menu">';
						$items .= '<li><a href="' . $logout_link . '">' . esc_html__( 'Logout', 'Avada' ) . '</a></li>';
						$items .= '</ul>';
					}
					$items .= '</li>';
				} // End if().
			} // End if().
		} // End if().
		return $items;
	}
} // End if().
add_filter( 'wp_nav_menu_items', 'avada_add_login_box_to_nav', 10, 3 );

if ( ! function_exists( 'avada_nav_woo_cart' ) ) {
	/**
	 * Woo Cart Dropdown for Main Nav or Top Nav.
	 *
	 * @param string $position The cart position.
	 * @return string HTML of Dropdown
	 */
	function avada_nav_woo_cart( $position = 'main' ) {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		$woo_cart_page_link       = WC()->cart->get_cart_url();
		$cart_link_active_class   = '';
		$cart_link_active_text    = '';
		$is_enabled               = false;
		$main_cart_class          = '';
		$cart_link_inactive_class = '';
		$cart_link_inactive_text  = '';
		$items                    = '';
		$cart_contents_count      = WC()->cart->get_cart_contents_count();

		if ( 'main' === $position ) {
			$is_enabled               = Avada()->settings->get( 'woocommerce_cart_link_main_nav' );
			$main_cart_class          = ' fusion-main-menu-cart';
			$cart_link_active_class   = 'fusion-main-menu-icon fusion-main-menu-icon-active';
			$cart_link_inactive_class = 'fusion-main-menu-icon';

			if ( Avada()->settings->get( 'woocommerce_cart_counter' ) ) {
				if ( $cart_contents_count ) {
					$cart_link_active_text = '<span class="fusion-widget-cart-number">' . $cart_contents_count . '</span>';
				}
				$main_cart_class      .= ' fusion-widget-cart-counter';
			} elseif ( $cart_contents_count ) {
				// If we're here, then ( Avada()->settings->get( 'woocommerce_cart_counter' ) ) is not true.
				$main_cart_class .= ' fusion-active-cart-icons';
			}
		} elseif ( 'secondary' === $position ) {
			$is_enabled               = Avada()->settings->get( 'woocommerce_cart_link_top_nav' );
			$main_cart_class          = ' fusion-secondary-menu-cart';
			$cart_link_active_class   = 'fusion-secondary-menu-icon';
			/* translators: Number of items. */
			$cart_link_active_text    = sprintf( esc_html__( '%s Item(s)', 'Avada' ), $cart_contents_count ) . ' <span class="fusion-woo-cart-separator">-</span> ' . WC()->cart->get_cart_subtotal();
			$cart_link_inactive_class = $cart_link_active_class;
			$cart_link_inactive_text  = esc_html__( 'Cart', 'Avada' );
		}

		$cart_link_markup = '<a class="' . $cart_link_active_class . '" href="' . $woo_cart_page_link . '" aria-hidden="true"><span class="menu-text" aria-label="' . esc_html__( 'View Cart', 'Avada' ) . '">' . $cart_link_active_text . '</span></a>';

		if ( $is_enabled ) {
			if ( is_cart() ) {
				$main_cart_class .= ' current-menu-item current_page_item';
			}

			$items = '<li class="fusion-custom-menu-item fusion-menu-cart' . $main_cart_class . '">';
			if ( $cart_contents_count ) {
				$checkout_link = wc_get_checkout_url();

				$items .= $cart_link_markup;

				$items .= '<div class="fusion-custom-menu-item-contents fusion-menu-cart-items">';
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_link = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					$thumbnail_id = ( $cart_item['variation_id'] && has_post_thumbnail( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : $cart_item['product_id'];

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$items .= '<div class="fusion-menu-cart-item">';
						$items .= '<a href="' . $product_link . '">';
							$items .= get_the_post_thumbnail( $thumbnail_id, 'recent-works-thumbnail' );
							// Check needed for pre Woo 2.7 versions only.
							$item_name = method_exists( $_product, 'get_name' ) ? $_product->get_name() : $cart_item['data']->post->post_title;
							$items .= '<div class="fusion-menu-cart-item-details">';
								$items .= '<span class="fusion-menu-cart-item-title">' . $item_name . '</span>';
								$items .= '<span class="fusion-menu-cart-item-quantity">' . $cart_item['quantity'] . ' x ' . WC()->cart->get_product_subtotal( $cart_item['data'], 1 ) . '</span>';
							$items .= '</div>';
						$items .= '</a>';
						$items .= '</div>';
					}
				}
				$items .= '<div class="fusion-menu-cart-checkout">';
				$items .= '<div class="fusion-menu-cart-link"><a href="' . $woo_cart_page_link . '">' . esc_html__( 'View Cart', 'Avada' ) . '</a></div>';
				$items .= '<div class="fusion-menu-cart-checkout-link"><a href="' . $checkout_link . '">' . esc_html__( 'Checkout', 'Avada' ) . '</a></div>';
				$items .= '</div>';
				$items .= '</div>';
			} else {
				$items .= '<a class="' . $cart_link_inactive_class . '" href="' . $woo_cart_page_link . '" aria-hidden="true"><span class="menu-text" aria-label="' . esc_html__( 'View Cart', 'Avada' ) . '">' . $cart_link_inactive_text . '</span></a>';
			}
			$items .= '</li>';
		} // End if().
		return $items;
	}
} // End if().

if ( ! function_exists( 'fusion_add_woo_cart_to_widget_html' ) ) {
	/**
	 * Adds cart HTML to widget.
	 *
	 * @return string The final HTML.
	 */
	function fusion_add_woo_cart_to_widget_html() {
		$items               = '';
		$cart_contents_count = WC()->cart->get_cart_contents_count();

		if ( class_exists( 'WooCommerce' ) ) {
			$counter = '';
			$class   = '';
			$items   = '';

			if ( Avada()->settings->get( 'woocommerce_cart_counter' ) ) {
				$counter = '<span class="fusion-widget-cart-number">' . $cart_contents_count . '</span>';
				$class   = 'fusion-widget-cart-counter';
			}

			if ( ! Avada()->settings->get( 'woocommerce_cart_counter' ) && $cart_contents_count ) {
				$class .= ' fusion-active-cart-icon';
			}

			$items .= '<li class="fusion-widget-cart ' . $class . '"><a href="' . get_permalink( get_option( 'woocommerce_cart_page_id' ) ) . '" class=""><span class="fusion-widget-cart-icon"></span>' . $counter . '</a></li>';
		}

		return $items;
	}
}

if ( ! function_exists( 'avada_add_woo_cart_to_nav' ) ) {
	/**
	 * Add woocommerce cart to main navigation or top navigation.
	 *
	 * @param  string $items HTML for the main menu items.
	 * @param  array  $args  Arguments for the WP menu.
	 * @return string
	 */
	function avada_add_woo_cart_to_nav( $items, $args ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $items;
		}
		global $woocommerce;

		$ubermenu = false;

		if ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ubermenu_get_menu_instance_by_theme_location( $args->theme_location ) ) {

			// Disable woo cart on ubermenu navigations.
			$ubermenu = true;
		}

		if ( 'v6' !== Avada()->settings->get( 'header_layout' ) ) {
			if ( false == $ubermenu && 'main_navigation' == $args->theme_location || 'sticky_navigation' == $args->theme_location ) {
				$items .= avada_nav_woo_cart( 'main' );
			} elseif ( false == $ubermenu && 'top_navigation' === $args->theme_location ) {
				$items .= avada_nav_woo_cart( 'secondary' );
			}
		}

		return $items;
	}
}
add_filter( 'wp_nav_menu_items', 'avada_add_woo_cart_to_nav', 10, 3 );

if ( ! function_exists( 'avada_add_search_to_main_nav' ) ) {
	/**
	 * Add search to the main navigation.
	 *
	 * @param  string $items HTML for the main menu items.
	 * @param  array  $args  Arguments for the WP menu.
	 * @return string
	 */
	function avada_add_search_to_main_nav( $items, $args ) {
		$ubermenu = false;

		if ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ubermenu_get_menu_instance_by_theme_location( $args->theme_location ) ) {

			// Disable woo cart on ubermenu navigations.
			$ubermenu = true;
		}

		if ( 'v6' != Avada()->settings->get( 'header_layout' ) && false == $ubermenu ) {
			if ( 'main_navigation' == $args->theme_location || 'sticky_navigation' == $args->theme_location ) {
				if ( Avada()->settings->get( 'main_nav_search_icon' ) ) {

					$items .= '<li class="fusion-custom-menu-item fusion-main-menu-search">';
						$items .= '<a class="fusion-main-menu-icon" aria-hidden="true"></a>';
						$items .= '<div class="fusion-custom-menu-item-contents">';
							$items .= get_search_form( false );
						$items .= '</div>';
					$items .= '</li>';
				}
			}
		}

		return $items;
	}
}
add_filter( 'wp_nav_menu_items', 'avada_add_search_to_main_nav', 20, 4 );

if ( ! function_exists( 'avada_update_featured_content_for_split_terms' ) ) {
	/**
	 * Updates post meta.
	 *
	 * @param  int    $old_term_id      The ID of the old taxonomy term.
	 * @param  int    $new_term_id      The ID of the new taxonomy term.
	 * @param  int    $term_taxonomy_id Deprecated.
	 * @param  string $taxonomy         The taxonomy.
	 */
	function avada_update_featured_content_for_split_terms( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
		if ( 'portfolio_category' == $taxonomy ) {
			$pages = get_pages();

			if ( $pages ) {
				foreach ( $pages as $page ) {
					$page_id        = $page->ID;
					$categories     = get_post_meta( $page_id, 'pyre_portfolio_category', true );
					$new_categories = array();
					if ( $categories ) {
						foreach ( $categories as $category ) {
							if ( '0' != $category ) {
								$new_categories[] = ( isset( $category ) && $old_term_id == $category ) ? $new_term_id : $category;
							} else {
								$new_categories[] = '0';
							}
						}

						update_post_meta( $page_id, 'pyre_portfolio_category', $new_categories );
					}
				}
			}
		}
	}
}
add_action( 'split_shared_term', 'avada_update_featured_content_for_split_terms', 10, 4 );

/**
 * Perform a HTTP HEAD or GET request.
 *
 * If $file_path is a writable filename, this will do a GET request and write
 * the file to that path.
 *
 * This is a re-implementation of the deprecated wp_get_http() function from WP Core,
 * but this time using the recommended WP_Http() class and the WordPress filesystem.
 *
 * @param string      $url       URL to fetch.
 * @param string|bool $file_path Optional. File path to write request to. Default false.
 * @param array       $args      Optional. Arguments to be passed-on to the request.
 * @return bool|string False on failure and string of headers if HEAD request.
 */
function avada_wp_get_http( $url = false, $file_path = false, $args = array() ) {

	// No need to proceed if we don't have a $url or a $file_path.
	if ( ! $url || ! $file_path ) {
		return false;
	}

	$try_file_get_contents = false;

	// Make sure we normalize $file_path.
	$file_path = wp_normalize_path( $file_path );

	// Include the WP_Http class if it doesn't already exist.
	if ( ! class_exists( 'WP_Http' ) ) {
		include_once wp_normalize_path( ABSPATH . WPINC . '/class-http.php' );
	}
	// Inlude the wp_remote_get function if it doesn't already exist.
	if ( ! function_exists( 'wp_remote_get' ) ) {
		include_once wp_normalize_path( ABSPATH . WPINC . '/http.php' );
	}

	$args = wp_parse_args( $args, array(
		'timeout'    => 30,
		'user-agent' => 'avada-user-agent',
	) );
	$response = wp_remote_get( esc_url_raw( $url ), $args );
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );

	// Try file_get_contents if body is empty.
	if ( empty( $body ) ) {
		if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
			$body = @file_get_contents( $url );
		}
	}

	// Initialize the Wordpress filesystem.
	$wp_filesystem = Avada_Helper::init_filesystem();

	if ( ! defined( 'FS_CHMOD_DIR' ) ) {
		define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
	}
	if ( ! defined( 'FS_CHMOD_FILE' ) ) {
		define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
	}

	// Attempt to write the file.
	if ( ! $wp_filesystem->put_contents( $file_path, $body, FS_CHMOD_FILE ) ) {
		// If the attempt to write to the file failed, then fallback to fwrite.
		// @codingStandardsIgnoreStart
		@unlink( $file_path );
		$fp = @fopen( $file_path, 'w' );

		$written = @fwrite( $fp, $body );
		@fclose( $fp );
		// @codingStandardsIgnoreEnd
		if ( false === $written ) {
			return false;
		}
	}

	// If all went well, then return the headers of the request.
	if ( isset( $response['headers'] ) ) {
		$response['headers']['response'] = $response['response']['code'];
		return $response['headers'];
	}

	// If all else fails, then return false.
	return false;
}

add_action( 'wp_ajax_avada_slider_preview', 'avada_ajax_avada_slider_preview' );
add_action( 'wp_ajax_nopriv_avada_slider_preview', 'avada_ajax_avada_slider_preview' );
add_action( 'fusion_builder_before_content', 'avada_ajax_avada_slider_preview' );

/**
 * Add slider UI to FusionBuilder
 *
 * @since 5.0
 * @return void
 */
function avada_ajax_avada_slider_preview() {
	global $post;

	// @codingStandardsIgnoreLine
	$slider_type = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['slidertype'] ) ) : get_post_meta( $post->ID, 'pyre_slider_type', true );
	// @codingStandardsIgnoreLine
	$slider_demo = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['demoslider'] ) ) : get_post_meta( $post->ID, 'pyre_demo_slider', true );

	$slider_object = false;
	$slider_type_string = '';

	if ( 'layer' === $slider_type ) {
		// @codingStandardsIgnoreLine
		$slider = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['layerslider'] ) ) : get_post_meta( $post->ID, 'pyre_slider', true );
		$slider_type_string = 'LayerSlider';
		if ( class_exists( 'LS_Sliders' ) ) {
			$slider_object = LS_Sliders::find( $slider );
			$edit_link = admin_url( 'admin.php?page=layerslider&action=edit&id=' . $slider );
		}
	} elseif ( 'rev' === $slider_type ) {
		// @codingStandardsIgnoreLine
		$slider = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['revslider'] ) ) : get_post_meta( $post->ID, 'pyre_revslider', true );
		$slider_type_string = 'Revolution Slider';
		if ( class_exists( 'RevSlider' ) ) {
			$slider_object = new RevSlider();
			if ( $slider_object->isAliasExistsInDB( $slider ) ) {
				$slider_object->initByAlias( $slider );
				$edit_link = admin_url( 'admin.php?page=revslider&view=slider&id=' . $slider_object->getID() );
			}
		}
	} elseif ( 'flex' === $slider_type ) {
		// @codingStandardsIgnoreLine
		$slider = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['wooslider'] ) ) : get_post_meta( $post->ID, 'pyre_wooslider', true );
		$slider_type_string = 'Fusion Slider';
		$slider_object = get_term_by( 'slug', $slider, 'slide-page' );
		if ( is_object( $slider_object ) ) {
			$edit_link = admin_url( 'term.php?taxonomy=slide-page&tag_ID=' . $slider_object->term_id . '&post_type=slide' );
			$edit_slides_link = admin_url( 'edit.php?slide-page=' . $slider . '&post_type=slide' );
		}
	} elseif ( 'elastic' === $slider_type ) {
		// @codingStandardsIgnoreLine
		$slider = ( isset( $_POST['data'] ) ) ? sanitize_text_field( wp_unslash( $_POST['data']['elasticslider'] ) ) : get_post_meta( $post->ID, 'pyre_elasticslider', true );
		$slider_type_string = 'Elastic Slider';
		$slider_object = get_term_by( 'slug', $slider, 'themefusion_es_groups' );
		if ( is_object( $slider_object ) ) {
			$edit_link = admin_url( 'term.php?taxonomy=themefusion_es_groups&tag_ID=' . $slider_object->term_id . '&post_type=themefusion_elastic' );
			$edit_slides_link = admin_url( 'edit.php?themefusion_es_groups=' . $slider . '&post_type=themefusion_elastic' );
		}
	} // End if().

	// If there was a demo import, but now they have changed slider, delete the demo post meta.
	if ( isset( $slider_demo ) && ! empty( $slider_demo ) && isset( $slider ) && '0' !== $slider && is_object( $post ) ) {
		delete_post_meta( $post->ID, 'pyre_demo_slider', true );
	}
	?>

	<?php if ( isset( $slider ) && '0' !== $slider && ( is_object( $slider_object ) || is_array( $slider_object ) ) ) : ?>

		<?php // If there is a slider set and it can be found. ?>
		<div class="fusion-builder-slider-helper">
			<h2 class="fusion-builder-slider-type"><span class="fusion-module-icon fusiona-uniF61C"></span> <?php echo esc_attr( $slider_type_string ); ?></h2>
			<p><?php esc_attr_e( 'This Slider Is Assigned Via Fusion Page Options', 'Avada' ); ?></p>
			<?php /* translators: The slider ID. */ ?>
			<h4 class="fusion-builder-slider-id"><?php printf( esc_attr__( 'Slider ID: %s', 'Avada' ), esc_attr( $slider ) ); ?></h4>
			<a href="<?php echo esc_url_raw( $edit_link ); ?>" title="<?php esc_attr_e( 'Edit slider', 'Avada' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary">
				<?php esc_attr_e( 'Edit Slider', 'Avada' ); ?>
			</a>
			<?php if ( isset( $edit_slides_link ) ) : ?>
				<a href="<?php echo esc_url_raw( $edit_slides_link ); ?>" title="<?php esc_attr_e( 'Edit Slides', 'Avada' ); ?>" style="margin-left:10px" target="_blank" rel="noopener noreferrer" class="button button-primary">
					<?php esc_attr_e( 'Edit Slides', 'Avada' ); ?>
				</a>
			<?php endif; ?>
			<a href="#" id="avada-slider-remove" title="<?php esc_attr_e( 'Remove Slider', 'Avada' ); ?>" style="margin-left:10px" class="button button-primary">
				<?php esc_attr_e( 'Remove Slider', 'Avada' ); ?>
			</a>
		</div>

	<?php elseif ( isset( $slider_demo ) && ! empty( $slider_demo ) ) : ?>

		<?php // If there is not a found slider, but there is demo post meta. ?>
		<div class="fusion-builder-slider-helper">
			<h2 class="fusion-builder-slider-type"><span class="fusion-module-icon fusiona-uniF61C"></span> <?php echo esc_attr( $slider_type_string ); ?></h2>
			<p><?php esc_attr_e( 'This Slider Is Assigned Via Fusion Page Options', 'Avada' ); ?></p>
			<?php /* translators: The slider. */ ?>
			<h4 class="fusion-builder-slider-id"><?php printf( esc_html__( 'Slider "%s" cannot be found', 'Avada' ), esc_attr( $slider_demo ) ); ?></h4>
			<a href="https://theme-fusion.com/avada-doc/sliders/how-to-get-our-demo-sliders/" title="<?php esc_attr_e( 'Learn How To Import Sliders', 'Avada' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary">
				<?php esc_attr_e( 'Learn How To Import Sliders', 'Avada' ); ?>
			</a> <a href="#" id="avada-slider-remove" title="<?php esc_attr_e( 'Remove Slider', 'Avada' ); ?>" style="margin-left:10px" class="button button-primary"><?php esc_attr_e( 'Remove Slider', 'Avada' ); ?></a>
		</div>

	<?php endif;
}

/**
 * Returns an avada user agent for use with premium plugin downloads.
 *
 * @since 5.0.2
 * @return string
 */
function avada_user_agent() {
	return 'avada-user-agent';
}

if ( function_exists( 'wp_cache_clean_cache' ) && ! function_exists( 'wp_cache_debug' ) ) {
	/**
	 * This is an additional function to avoid PHP Fatal issues with WP Super Cache
	 */
	function wp_cache_debug() {
	}
}

if ( class_exists( 'GFForms' ) ) {
	add_filter( 'after_setup_theme', 'avada_gravity_form_merge_tags', 10, 1 );
}
if ( ! function_exists( 'avada_gravity_form_merge_tags' ) ) {
	/**
	 * Gravity Form Merge Tags in Post Content
	 *
	 * @access  public
	 * @param array $args Array of bool auto_append_eid and encrypt_eid.
	 */
	function avada_gravity_form_merge_tags( $args = array() ) {

		include_once Avada::$template_dir_path . '/includes/class-avada-gravityforms-tags-merger.php';
		Avada_Gravity_Forms_Tags_Merger::get_instance( $args );

	}
}

/**
 * Backwards-compatibility for the avada_post_metadata_date filter.
 *
 * @since 5.1
 * @param string $value The date format.
 * @return string
 */
function apply_avada_post_metadata_date_filter( $value ) {
	return apply_filters( 'avada_post_metadata_date', $value );
}
add_filter( 'fusion_post_metadata_date', 'apply_avada_post_metadata_date_filter' );

/**
 * Backwards-compatibility for the avada_post_metadata_markup filter.
 *
 * @since 5.1
 * @param string $value HTML.
 * @return string
 */
function apply_avada_post_metadata_markup_filter( $value ) {
	return apply_filters( 'avada_post_metadata_markup', $value );
}
add_filter( 'fusion_post_metadata_markup', 'apply_avada_post_metadata_markup_filter' );

/**
 * Backwards-compatibility for the avada_post_metadata_markup filter.
 *
 * @since 5.1
 * @param string $value HTML.
 * @return string
 */
function apply_avada_blog_read_more_excerpt_filter( $value ) {
	return apply_filters( 'avada_blog_read_more_excerpt', $value );
}
add_filter( 'fusion_blog_read_more_excerpt', 'apply_avada_blog_read_more_excerpt_filter' );

/**
 * Add revslider styles.
 */
function avada_revslider_styles() {
	// @codingStandardsIgnoreStart
	global $wpdb, $revSliderVersion;
	$plugin_version = $revSliderVersion;
	// @codingStandardsIgnoreEnd

	$table_name = $wpdb->prefix . 'revslider_css';
	if ( shortcode_exists( 'rev_slider' ) && get_option( 'avada_revslider_version' ) !== $plugin_version ) {

		$old_styles = array( '.avada_huge_white_text', '.avada_huge_black_text', '.avada_big_black_text', '.avada_big_white_text', '.avada_big_black_text_center', '.avada_med_green_text', '.avada_small_gray_text', '.avada_small_white_text', '.avada_block_black', '.avada_block_green', '.avada_block_white', '.avada_block_white_trans' );

		foreach ( $old_styles as $handle ) {
			$wpdb->delete( $table_name, array(
				'handle' => '.tp-caption' . $handle,
			) );
		}

		$styles = array(
			'.tp-caption.avada_huge_white_text'       => '{"position":"absolute","color":"#ffffff","font-size":"130px","line-height":"45px","font-family":"museoslab500regular"}',
			'.tp-caption.avada_huge_black_text'       => '{"position":"absolute","color":"#000000","font-size":"130px","line-height":"45px","font-family":"museoslab500regular"}',
			'.tp-caption.avada_big_black_text'        => '{"position":"absolute","color":"#333333","font-size":"42px","line-height":"45px","font-family":"museoslab500regular"}',
			'.tp-caption.avada_big_white_text'        => '{"position":"absolute","color":"#fff","font-size":"42px","line-height":"45px","font-family":"museoslab500regular"}',
			'.tp-caption.avada_big_black_text_center' => '{"position":"absolute","color":"#333333","font-size":"38px","line-height":"45px","font-family":"museoslab500regular","text-align":"center"}',
			'.tp-caption.avada_med_green_text'        => '{"position":"absolute","color":"#A0CE4E","font-size":"24px","line-height":"24px","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
			'.tp-caption.avada_small_gray_text'       => '{"position":"absolute","color":"#747474","font-size":"13px","line-height":"20px","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
			'.tp-caption.avada_small_white_text'      => '{"position":"absolute","color":"#fff","font-size":"13px","line-height":"20px","font-family":"PTSansRegular, Arial, Helvetica, sans-serif","text-shadow":"0px 2px 5px rgba(0, 0, 0, 0.5)","font-weight":"700"}',
			'.tp-caption.avada_block_black'           => '{"position":"absolute","color":"#A0CE4E","text-shadow":"none","font-size":"22px","line-height":"34px","padding":["1px", "10px", "0px", "10px"],"margin":"0px","border-width":"0px","border-style":"none","background-color":"#000","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
			'.tp-caption.avada_block_green'           => '{"position":"absolute","color":"#000","text-shadow":"none","font-size":"22px","line-height":"34px","padding":["1px", "10px", "0px", "10px"],"margin":"0px","border-width":"0px","border-style":"none","background-color":"#A0CE4E","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
			'.tp-caption.avada_block_white'           => '{"position":"absolute","color":"#fff","text-shadow":"none","font-size":"22px","line-height":"34px","padding":["1px", "10px", "0px", "10px"],"margin":"0px","border-width":"0px","border-style":"none","background-color":"#000","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
			'.tp-caption.avada_block_white_trans'     => '{"position":"absolute","color":"#fff","text-shadow":"none","font-size":"22px","line-height":"34px","padding":["1px", "10px", "0px", "10px"],"margin":"0px","border-width":"0px","border-style":"none","background-color":"rgba(0, 0, 0, 0.6)","font-family":"PTSansRegular, Arial, Helvetica, sans-serif"}',
		);

		foreach ( $styles as $handle => $params ) {
			$query_id = md5( maybe_serialize( $params ) );
			$test = wp_cache_get( $query_id, 'avada_revslider_styles' );
			if ( false === $test ) {
				$test = $wpdb->get_var( $wpdb->prepare( 'SELECT handle FROM ' . $table_name . ' WHERE handle = %s', $handle ) );
				wp_cache_set( $query_id, $test, 'avada_revslider_styles' );
			}

			if ( $test != $handle ) {
				$wpdb->replace(
					$table_name,
					array(
						'handle' => $handle,
						'params' => $params,
						'settings' => '{"hover":"false","type":"text","version":"custom","translated":"5"}',
					),
					array(
						'%s',
						'%s',
						'%s',
					)
				);
			}
		}
		update_option( 'avada_revslider_version', $plugin_version );
	} // End if().
}

if ( ! function_exists( 'avada_header_template' ) ) {
	/**
	 * Avada Header Template Function.
	 *
	 * @param  string $slider_position Show header below or above slider.
	 * @return void
	 */
	function avada_header_template( $slider_position = 'Below' ) {

		$page_id = get_queried_object_id();

		$reverse_position = ( 'Below' == $slider_position ) ? 'Above' : 'Below';

		$menu_text_align = '';

		$theme_option_slider_position = Avada()->settings->get( 'slider_position' );
		$page_option_slider_position  = fusion_get_page_option( 'slider_position', $page_id );

		if ( ( ! $theme_option_slider_position || ( $slider_position == $theme_option_slider_position && strtolower( $reverse_position ) != $page_option_slider_position ) || ( $theme_option_slider_position != $slider_position && strtolower( $slider_position ) == $page_option_slider_position ) ) && ! is_page_template( 'blank.php' ) && 'no' != fusion_get_page_option( 'display_header', $page_id ) && 'Top' == Avada()->settings->get( 'header_position' ) ) {
			$header_wrapper_class  = 'fusion-header-wrapper';
			$header_wrapper_class .= ( Avada()->settings->get( 'header_shadow' ) ) ? ' fusion-header-shadow' : '';

			/**
			 * The avada_before_header_wrapper hook.
			 */
			do_action( 'avada_before_header_wrapper' );

			$sticky_header_logo = Avada()->settings->get( 'sticky_header_logo' );
			$sticky_header_logo = ( is_array( $sticky_header_logo ) && isset( $sticky_header_logo['url'] ) && $sticky_header_logo['url'] ) ? true : false;
			$mobile_logo        = Avada()->settings->get( 'mobile_logo' );
			$mobile_logo        = ( is_array( $mobile_logo ) && isset( $mobile_logo['url'] ) && $mobile_logo['url'] ) ? true : false;

			$sticky_header_type2_layout = '';

			if ( in_array( Avada()->settings->get( 'header_layout' ), array( 'v4', 'v5' ) ) ) {
				$sticky_header_type2_layout = ( 'menu_and_logo' == Avada()->settings->get( 'header_sticky_type2_layout' ) ) ? ' fusion-sticky-menu-and-logo' : ' fusion-sticky-menu-only';
				$menu_text_align = 'fusion-header-menu-align-' . Avada()->settings->get( 'menu_text_align' );
			}
			?>

			<header class="<?php echo esc_attr( $header_wrapper_class ); ?>">
				<div class="<?php echo esc_attr( 'fusion-header-' . Avada()->settings->get( 'header_layout' ) . ' fusion-logo-' . strtolower( Avada()->settings->get( 'logo_alignment' ) ) . ' fusion-sticky-menu-' . has_nav_menu( 'sticky_navigation' ) . ' fusion-sticky-logo-' . $sticky_header_logo . ' fusion-mobile-logo-' . $mobile_logo . ' fusion-mobile-menu-design-' . strtolower( Avada()->settings->get( 'mobile_menu_design' ) ) . $sticky_header_type2_layout . ' ' . $menu_text_align ); ?>">
					<?php
					/**
					 * The avada_header hook.
					 *
					 * @hooked avada_secondary_header - 10.
					 * @hooked avada_header_1 - 20 (adds header content for header v1-v3).
					 * @hooked avada_header_2 - 20 (adds header content for header v4-v5).
					 */
					do_action( 'avada_header' );
					?>
				</div>
				<div class="fusion-clearfix"></div>
			</header>
			<?php
			/**
			 * The avada_after_header_wrapper hook.
			 */
			do_action( 'avada_after_header_wrapper' );
		} // End if().
	}
} // End if().

if ( ! function_exists( 'avada_side_header' ) ) {
	/**
	 * Avada Side Header Template Function.
	 *
	 * @return void
	 */
	function avada_side_header() {
		$queried_object_id = get_queried_object_id();

		if ( ! is_page_template( 'blank.php' ) && 'no' != get_post_meta( $queried_object_id, 'pyre_display_header', true ) ) {
			get_template_part( 'templates/side-header' );
		}
	}
}

if ( ! function_exists( 'avada_secondary_header' ) ) {
	/**
	 * Gets the header-secondary template if needed.
	 */
	function avada_secondary_header() {
		if ( ! in_array( Avada()->settings->get( 'header_layout' ), array( 'v2', 'v3', 'v4', 'v5' ) ) ) {
			return;
		}
		if ( 'Leave Empty' !== Avada()->settings->get( 'header_left_content' ) || 'Leave Empty' !== Avada()->settings->get( 'header_right_content' ) ) {
			get_template_part( 'templates/header-secondary' );
		}
	}
}
add_action( 'avada_header', 'avada_secondary_header', 10 );

if ( ! function_exists( 'avada_header_1' ) ) {
	/**
	 * Gets the header-1 template if needed.
	 */
	function avada_header_1() {
		if ( ! in_array( Avada()->settings->get( 'header_layout' ), array( 'v1', 'v2', 'v3' ) ) ) {
			return;
		}
		get_template_part( 'templates/header-1' );
	}
}
add_action( 'avada_header', 'avada_header_1', 20 );

if ( ! function_exists( 'avada_header_2' ) ) {
	/**
	 * Gets the header-2 template if needed.
	 */
	function avada_header_2() {
		if ( ! in_array( Avada()->settings->get( 'header_layout' ), array( 'v4', 'v5' ) ) ) {
			return;
		}
		get_template_part( 'templates/header-2' );
	}
}
add_action( 'avada_header', 'avada_header_2', 20 );

if ( ! function_exists( 'avada_header_3' ) ) {
	/**
	 * Getys the header-3 template if needed.
	 */
	function avada_header_3() {
		if ( 'v6' !== Avada()->settings->get( 'header_layout' ) ) {
			return;
		}
		get_template_part( 'templates/header-3' );
	}
}
add_action( 'avada_header', 'avada_header_3', 10 );

if ( ! function_exists( 'avada_header_4' ) ) {
	/**
	 * Gets the template part for the v7 header.
	 *
	 * @since 5.0
	 */
	function avada_header_4() {
		if ( 'v7' !== Avada()->settings->get( 'header_layout' ) ) {
			return;
		}
		get_template_part( 'templates/header-4' );
	}
}
add_action( 'avada_header', 'avada_header_4', 10 );

if ( ! function_exists( 'avada_secondary_main_menu' ) ) {
	/**
	 * Gets the secondary menu template if needed.
	 */
	function avada_secondary_main_menu() {
		if ( ! in_array( Avada()->settings->get( 'header_layout' ), array( 'v4', 'v5' ) ) ) {
			return;
		}
		get_template_part( 'templates/header-secondary-main-menu' );
	}
}
add_action( 'avada_header', 'avada_secondary_main_menu', 30 );

if ( ! function_exists( 'avada_logo' ) ) {
	/**
	 * Gets the logo template if needed.
	 */
	function avada_logo() {
		// No need to proceed any further if no logo is set.
		if ( '' === Avada()->settings->get( 'logo' ) && '' === Avada()->settings->get( 'logo_retina' ) ) {
			return;
		}
		get_template_part( 'templates/logo' );
	}
}

if ( ! function_exists( 'avada_main_menu' ) ) {
	/**
	 * The main menu.
	 *
	 * @param bool $flyout_menu Whether we want the flyout menu or not.
	 */
	function avada_main_menu( $flyout_menu = false ) {

		$menu_class = 'fusion-menu';
		if ( 'v7' === Avada()->settings->get( 'header_layout' ) ) {
			$menu_class .= ' fusion-middle-logo-ul';
		}

		$main_menu_args = array(
			'theme_location'  => 'main_navigation',
			'depth'           => 5,
			'menu_class'      => $menu_class,
			'items_wrap'      => '<ul role="menubar" id="%1$s" class="%2$s">%3$s</ul>',
			'fallback_cb'     => 'Avada_Nav_Walker::fallback',
			'walker'          => new Avada_Nav_Walker(),
			'container'       => false,
			'item_spacing'    => 'discard',
			'echo'            => false,
		);

		if ( $flyout_menu ) {
			$flyout_menu_args = array(
				'depth'     => 1,
				'container' => false,
			);

			$main_menu_args = wp_parse_args( $flyout_menu_args, $main_menu_args );

			$main_menu = wp_nav_menu( $main_menu_args );

			return $main_menu;

		} else {
			$uber_menu_class = '';
			if ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) ) {
				$uber_menu_class = ' fusion-ubermenu';
			}

			echo '<nav class="fusion-main-menu' . esc_attr( $uber_menu_class ) . '" aria-label="Main Menu">';
			echo wp_nav_menu( $main_menu_args );
			echo '</nav>';

			if ( has_nav_menu( 'sticky_navigation' ) && 'Top' === Avada()->settings->get( 'header_position' ) && ( ! function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) || ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ! ubermenu_get_menu_instance_by_theme_location( 'sticky_navigation' ) ) ) ) {

				$sticky_menu_args = array(
					'theme_location'  => 'sticky_navigation',
					'menu_id'		  => 'menu-main-menu-1',
					'walker'          => new Avada_Nav_Walker(),
					'item_spacing'    => 'discard',
				);

				$sticky_menu_args = wp_parse_args( $sticky_menu_args, $main_menu_args );

				echo '<nav class="fusion-main-menu fusion-sticky-menu" aria-label="Main Menu Sticky">';
				echo wp_nav_menu( $sticky_menu_args );
				echo '</nav>';
			}

			// Make sure mobile menu is not loaded when we use slideout menu or ubermenu.
			if ( ! function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) || ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ! ubermenu_get_menu_instance_by_theme_location( 'main_navigation' ) ) ) {
				if ( has_nav_menu( 'mobile_navigation' ) ) {
					$mobile_menu_args = array(
						'theme_location'  => 'mobile_navigation',
						'menu_class'		  => 'fusion-mobile-menu',
						'depth'           => 5,
						'walker'          => new Avada_Nav_Walker(),
						'item_spacing'    => 'discard',
						'container_class' => 'fusion-mobile-navigation',
					);
					echo wp_nav_menu( $mobile_menu_args );
				}

				avada_mobile_main_menu();
			}
		} // End if().
	}
} // End if().

if ( ! function_exists( 'avada_default_menu_fallback' ) ) {
	/**
	 * Return null.
	 *
	 * @param array $args Menu arguments. Irrelevant in this context.
	 * @return null
	 */
	function avada_default_menu_fallback( $args ) {
		return null;
	}
}

if ( ! function_exists( 'avada_contact_info' ) ) {
	/**
	 * Returns the markup for the contact-info area.
	 */
	function avada_contact_info() {
		$phone_number    = do_shortcode( Avada()->settings->get( 'header_number' ) );
		$email           = antispambot( Avada()->settings->get( 'header_email' ) );
		$header_position = Avada()->settings->get( 'header_position' );

		$html = '';

		if ( $phone_number || $email ) {
			$html .= '<div class="fusion-contact-info">';
			$html .= $phone_number;
			if ( $phone_number && $email ) {
				if ( 'Top' == $header_position ) {
					$html .= '<span class="fusion-header-separator">' . apply_filters( 'avada_header_separator', '|' ) . '</span>';
				} else {
					$html .= '<br />';
				}
			}
			if ( $email ) {
				$html .= sprintf( apply_filters( 'avada_header_contact_info_email', '<a href="mailto:%s">%s</a>' ), $email, $email );
			}
			$html .= '</div>';
		}
		return $html;
	}
}

if ( ! function_exists( 'avada_secondary_nav' ) ) {
	/**
	 * Retuerns the markup for nav menu.
	 */
	function avada_secondary_nav() {
		if ( has_nav_menu( 'top_navigation' ) ) {
			return wp_nav_menu( array(
				'theme_location'  => 'top_navigation',
				'depth'           => 5,
				'items_wrap'      => '<ul role="menubar" id="%1$s" class="%2$s">%3$s</ul>',
				'container'       => false,
				'fallback_cb'     => 'Avada_Nav_Walker::fallback',
				'walker'          => new Avada_Nav_Walker(),
				'echo'            => false,
				'item_spacing'    => 'discard',
			) );
		}
	}
}

if ( ! function_exists( 'avada_header_social_links' ) ) {
	/**
	 * Return the social links maekup.
	 *
	 * @return string
	 */
	function avada_header_social_links() {
		global $social_icons;

		$options = array(
			'position'          => 'header',
			'icon_colors'       => Avada()->settings->get( 'header_social_links_icon_color' ),
			'box_colors'        => Avada()->settings->get( 'header_social_links_box_color' ),
			'icon_boxed'        => Avada()->settings->get( 'header_social_links_boxed' ),
			'icon_boxed_radius' => Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_boxed_radius' ) ),
			'tooltip_placement' => Avada()->settings->get( 'header_social_links_tooltip_placement' ),
			'linktarget'        => Avada()->settings->get( 'social_icons_new' ),
		);

		$render_social_icons = $social_icons->render_social_icons( $options );
		$html = ( $render_social_icons ) ? '<div class="fusion-social-links-header">' . $render_social_icons . '</div>' : '';

		return $html;
	}
}

if ( ! function_exists( 'avada_secondary_header_content' ) ) {
	/**
	 * Get the secondary header content based on the content area.
	 *
	 * @param  string $content_area Secondary header content area from theme optins.
	 * @return string               Html for the content.
	 */
	function avada_secondary_header_content( $content_area ) {
		if ( Avada()->settings->get( $content_area ) == 'Contact Info' ) {
			return avada_contact_info();
		} elseif ( Avada()->settings->get( $content_area ) == 'Social Links' ) {
			return avada_header_social_links();
		} elseif ( Avada()->settings->get( $content_area ) == 'Navigation' ) {
			$mobile_menu_wrapper = '';
			if ( has_nav_menu( 'top_navigation' ) ) {
				$mobile_menu_wrapper = '<div class="fusion-mobile-nav-holder"></div>';
			}

			$secondary_menu = '<nav class="fusion-secondary-menu" role="navigation" aria-label="Secondary Menu">';
			$secondary_menu .= avada_secondary_nav();
			$secondary_menu .= '</nav>';

			return $secondary_menu . $mobile_menu_wrapper;
		}
	}
}

if ( ! function_exists( 'avada_header_content_3' ) ) {
	/**
	 * Renders the 3rd content in headers.
	 */
	function avada_header_content_3() {
		get_template_part( 'templates/header-content-3' );
	}
}
if ( 'Top' === Avada()->settings->get( 'header_position' ) ) {
	add_action( 'avada_logo_append', 'avada_header_content_3', 10 );
}


if ( ! function_exists( 'avada_header_banner' ) ) {
	/**
	 * Returns the header banner.
	 *
	 * @return string
	 */
	function avada_header_banner() {
		return '<div class="fusion-header-banner">' . do_shortcode( Avada()->settings->get( 'header_banner_code' ) ) . '</div>';
	}
}

if ( ! function_exists( 'avada_header_tagline' ) ) {
	/**
	 * Returns the headers tagline.
	 *
	 * @return string
	 */
	function avada_header_tagline() {
		return '<h3 class="fusion-header-tagline">' . do_shortcode( Avada()->settings->get( 'header_tagline' ) ) . '</h3>';
	}
}

if ( ! function_exists( 'avada_modern_menu' ) ) {
	/**
	 * Gets the menu-mobile-modern template part.
	 *
	 * @return string
	 */
	function avada_modern_menu() {
		ob_start();
		get_template_part( 'templates/menu-mobile-modern' );
		return ob_get_contents();
	}
}

if ( ! function_exists( 'avada_mobile_main_menu' ) ) {
	/**
	 * Gets the menu-mobile-main template part.
	 */
	function avada_mobile_main_menu() {
		get_template_part( 'templates/menu-mobile-main' );
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
