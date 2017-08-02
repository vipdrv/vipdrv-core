<?php
/**
 * Dynamic-css.
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
 * Format of the $css array:
 * $css['media-query']['element']['property'] = value
 *
 * If no media query is required then set it to 'global'
 *
 * If we want to add multiple values for the same property then we have to make it an array like this:
 * $css[media-query][element]['property'][] = value1
 * $css[media-query][element]['property'][] = value2
 *
 * Multiple values defined as an array above will be parsed separately.
 *
 * @param array $original_css The existing CSS.
 */
function avada_dynamic_css_array( $original_css = array() ) {
	global $wp_version, $avada_dynamic_css_array_added;

	if ( true === $avada_dynamic_css_array_added ) {
		return $original_css;
	}

	$css = array();

	$c_page_id = Avada()->fusion_library->get_page_id();

	$dynamic_css = Fusion_Dynamic_CSS::get_instance();
	$dynamic_css_helpers = $dynamic_css->get_helpers();

	/**
	 * An array of all the elements that will be targeted from the body typography settings
	 */
	$body_typography_elements = apply_filters( 'avada_body_typography_elements', avada_get_body_typography_elements() );

	/**
	 * An array of all the elements that will be targeted from the nav typography settings
	 */
	$nav_typography_elements = array(
		'.side-nav li a',
		'.fusion-main-menu > ul > li > a',
	);
	$nav_typography_elements = $dynamic_css_helpers->implode( $nav_typography_elements );

	/**
	 * An array of all the elements that will be targeter from the h1_typography settings
	 */
	$h1_typography_elements = apply_filters( 'avada_h1_typography_elements', avada_get_h1_typography_elements() );

	/**
	 * An array of all the elements that will be targeter from the h2_typography settings
	 */
	$h2_typography_elements = apply_filters( 'avada_h2_typography_elements', avada_get_h2_typography_elements() );

	/**
	 * An array of all the elements that will be targeter from the h3_typography settings
	 */
	$h3_typography_elements = apply_filters( 'avada_h3_typography_elements', avada_get_h3_typography_elements() );

	/**
	 * An array of all the elements that will be targeter from the h4_typography settings
	 */
	$h4_typography_elements = apply_filters( 'avada_h4_typography_elements', avada_get_h4_typography_elements() );

	/**
	 * An array of all the elements that will be targeter from the h5_typography settings
	 */
	$h5_typography_elements = apply_filters( 'avada_h5_typography_elements', avada_get_h5_typography_elements() );

	/**
	 * An array of all the elements that will be targeter from the h6_typography settings
	 */
	$h6_typography_elements = apply_filters( 'avada_h6_typography_elements', avada_get_h6_typography_elements() );

	$footer_headings_typography_elements = array(
		'.fusion-footer-widget-area h3',
		'.fusion-footer-widget-area .widget-title',
		'#slidingbar-area h3',
		'#slidingbar-area .widget-title',
	);
	$footer_headings_typography_elements = $dynamic_css_helpers->implode( $footer_headings_typography_elements );

	// Set the correct paddings and negative margins for the "100% Width Left/Right Padding" option.
	$hundredplr_padding = Fusion_Sanitize::size( fusion_get_option( 'hundredp_padding', 'hundredp_padding', $c_page_id ) );
	$hundredplr_padding_value = Fusion_Sanitize::number( $hundredplr_padding );
	$hundredplr_padding_unit = Fusion_Sanitize::get_unit( $hundredplr_padding );

	$hundredplr_padding_negative_margin = '-' . $hundredplr_padding_value . $hundredplr_padding_unit;

	if ( '%' == $hundredplr_padding_unit ) {
		$fullwidth_max_width = 100 - 2 * $hundredplr_padding_value;
		$hundredplr_padding_negative_margin = '-' . $hundredplr_padding_value / $fullwidth_max_width * 100 . $hundredplr_padding_unit;
	}

	$link_color_elements = array(
		'body a',
		'body a:before',
		'body a:after',
		'.single-navigation a[rel="prev"]:before',
		'.single-navigation a[rel="next"]:after',
		'.project-content .project-info .project-info-box a',
		'.fusion-content-widget-area .widget li a',
		'.fusion-content-widget-area .widget .recentcomments',
		'.fusion-content-widget-area .widget_categories li',
		'#main .post h2 a',
		'.about-author .title a',
		'.shop_attributes tr th',
		'.fusion-rollover a',
		'.fusion-load-more-button',
	);
	if ( class_exists( 'bbPress' ) ) {
		$link_color_elements[] = '.bbp-forum-header a.bbp-forum-permalink';
		$link_color_elements[] = '.bbp-topic-header a.bbp-topic-permalink';
		$link_color_elements[] = '.bbp-reply-header a.bbp-reply-permalink';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$link_color_elements[] = '.fusion-woo-featured-products-slider .price .amount';
		$link_color_elements[] = '#main .product .product_title';
		$link_color_elements[] = '.shop_table.order_details tr th';
		$link_color_elements[] = '.widget_layered_nav li.chosen a';
		$link_color_elements[] = '.widget_layered_nav li.chosen a:before';
		$link_color_elements[] = '.widget_layered_nav_filters li.chosen a';
		$link_color_elements[] = '.widget_layered_nav_filters li.chosen a:before';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$link_color_elements[] = '#tribe-events-content .tribe-events-sub-nav li a';
		$link_color_elements[] = '.event-is-recurring';
	}
	$link_color_elements = $dynamic_css_helpers->implode( $link_color_elements );

	// Is the site width a percent value?
	$site_width_percent = ( false !== strpos( Avada()->settings->get( 'site_width' ), '%' ) ) ? true : false;

	$theme_info = wp_get_theme();
	if ( $theme_info->parent_theme ) {
		$template_dir = basename( Avada::$template_dir_path );
		$theme_info   = wp_get_theme( $template_dir );
	}

	$css['global'][ '.' . $theme_info->get( 'Name' ) . '_' . str_replace( '.', '', $theme_info->get( 'Version' ) ) ]['color'] = 'green';

	if ( ! Avada()->settings->get( 'responsive' ) ) {
		$css['global']['.ua-mobile #wrapper']['width']    = '100% !important';
		$css['global']['.ua-mobile #wrapper']['overflow'] = 'hidden !important';
	}

	$side_header_width = ( 'Top' == Avada()->settings->get( 'header_position' ) ) ? 0 : intval( Avada()->settings->get( 'side_header_width' ) );

	if ( version_compare( $wp_version, '4.3.1', '<=' ) ) {
		// Tweak the comment-form CSS for WordPress versions < 4.4.
		$css['global']['#comment-input']['margin-bottom'] = '13px';
	}

	if ( class_exists( 'WooCommerce' ) ) {

		if ( 'horizontal' == Avada()->settings->get( 'woocommerce_product_tab_design' ) ) {

			$css['global']['.woocommerce-tabs > .tabs']['width']         = '100%';
			$css['global']['.woocommerce-tabs > .tabs']['margin']        = '0px';
			$css['global']['.woocommerce-tabs > .tabs']['border-bottom'] = '1px solid #dddddd';

			$css['global']['.woocommerce-tabs > .tabs li']['float'] = 'left';

			$css['global']['.woocommerce-tabs > .tabs li a']['border']  = '1px solid transparent !important';
			$css['global']['.woocommerce-tabs > .tabs li a']['padding'] = '10px 20px';

			$css['global']['.woocommerce-tabs > .tabs .active']['border'] = '1px solid #dddddd';
			$css['global']['.woocommerce-tabs > .tabs .active']['border-bottom'] = 'none';
			$css['global']['.woocommerce-tabs > .tabs .active']['min-height'] = '40px';
			$css['global']['.woocommerce-tabs > .tabs .active']['margin-bottom'] = '-1px';

			$css['global']['.woocommerce-tabs > .tabs .active:hover a']['cursor'] = 'default';

			$css['global']['.woocommerce-tabs .entry-content']['float']      = 'left';
			$css['global']['.woocommerce-tabs .entry-content']['margin']     = '0px';
			$css['global']['.woocommerce-tabs .entry-content']['width']      = '100%';
			$css['global']['.woocommerce-tabs .entry-content']['border-top'] = 'none';

			// For WooCommerce 2.6+ my account page this is in the content min media query.
		}

		if ( '0' != Fusion_Color::new_color( Avada()->settings->get( 'timeline_bg_color' ) )->alpha ) {
			$css['global']['.products .product-list-view']['padding-left']  = '20px';
			$css['global']['.products .product-list-view']['padding-right'] = '20px';
		}

		$elements = array(
			'.fusion-item-in-cart .fusion-rollover-content .fusion-rollover-title',
			'.fusion-item-in-cart .fusion-rollover-content .fusion-rollover-categories',
			'.fusion-item-in-cart .fusion-rollover-content .price',
			'.fusion-carousel-title-below-image .fusion-item-in-cart .fusion-rollover-content .fusion-product-buttons',
			'.products .product .fusion-item-in-cart .fusion-rollover-content .fusion-product-buttons',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		if ( 'clean' == Avada()->settings->get( 'woocommerce_product_box_design' ) ) {
			$css['global']['.fusion-woo-product-design-clean .products .fusion-price-rating']['display'] = 'block';
			$css['global']['.fusion-woo-product-design-clean .products .fusion-rollover .star-rating span:before, .fusion-woo-product-design-clean .products .fusion-rollover .star-rating:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_icon_color' ) );
			$css['global']['.fusion-woo-product-design-clean .products .fusion-rollover-content .fusion-product-buttons, .fusion-woo-slider .fusion-product-buttons']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_text_color' ) );
			$css['global']['.fusion-woo-product-design-clean .products .fusion-rollover-content .fusion-product-buttons a, .fusion-woo-slider .fusion-product-buttons a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_text_color' ) );
			$css['global']['.fusion-woo-product-design-clean .products .fusion-rollover-content .fusion-product-buttons a, .fusion-woo-slider .fusion-product-buttons a']['letter-spacing'] = '1px';
			$css['global']['.fusion-woo-product-design-clean .products .fusion-rollover-content .fusion-rollover-linebreak, .fusion-woo-slider .fusion-product-buttons .fusion-rollover-linebreak']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_text_color' ) );
			$css['global']['.product-details-container .fusion-content-sep']['border'] = 'none';
		}

		// Set single product gallery width.
		if ( is_product() ) {
			$post_image = get_the_post_thumbnail( get_the_ID(), apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

			if ( $post_image ) {
				$css['global']['.product .images']['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'woocommerce_single_gallery_size' ) );
				$css['global']['.product .summary.entry-summary']['margin-left'] = 'calc( ' . $css['global']['.product .images']['width'] . ' + 30px)';

				if ( is_rtl() ) {
					$css['global']['.rtl .product .summary.entry-summary']['margin-right'] = 'calc( ' . $css['global']['.product .images']['width'] . ' + 30px)';
				}
			}
		}
	} // End if().

	$elements = array(
		'html',
		'body',
		'html body.custom-background',
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-tabs > .tabs .active a';
		$elements[] = '.woocommerce-MyAccount-navigation > ul .is-active a';
		$elements[] = '.woocommerce-checkout-nav .is-active a';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) );

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) );
	if ( 'Boxed' == Avada()->settings->get( 'layout' ) || 'boxed' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'bg_color' ) );
	}

	if ( ! $site_width_percent ) {

		$elements = array(
			'#main',
			'.fusion-secondary-header',
			'.sticky-header .sticky-shadow',
			'.tfs-slider .slide-content-container',
			'.header-v4 #small-nav',
			'.header-v5 #small-nav',
			'.fusion-footer-copyright-area',
			'.fusion-footer-widget-area',
			'#slidingbar',
			'.fusion-page-title-bar',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '30px';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '30px';
	}

	$css['global']['.width-100 .fusion-fullwidth']['margin-left']  = $hundredplr_padding_negative_margin;
	$css['global']['.width-100 .fusion-fullwidth']['margin-right'] = $hundredplr_padding_negative_margin;

	$elements = array(
		'a:hover',
		'.event-is-recurring:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$elements = array(
		'#slidingbar-area ul li a:hover',
		'#slidingbar-area .widget li.recentcomments:hover:before',
		'#slidingbar-area .fusion-accordian .panel-title a:hover',
		'.project-content .project-info .project-info-box a:hover',
		'#main .post h2 a:hover',
		'#main .about-author .title a:hover',
		'.fusion-footer-widget-area a:hover',
		'.slidingbar-area a:hover',
		'.slidingbar-area .widget li a:hover:before',
		'.fusion-copyright-notice a:hover',
		'.fusion-content-widget-area .widget_categories li a:hover',
		'.fusion-content-widget-area .widget li a:hover',
		'.fusion-date-and-formats .fusion-format-box i',
		'h5.toggle:hover a',
		'.content-box-percentage',
		'.more a:hover:after',
		'.fusion-read-more:hover:after',
		'.pagination-prev:hover:before',
		'.pagination-next:hover:after',
		'.single-navigation a[rel=prev]:hover:before',
		'.single-navigation a[rel=next]:hover:after',
		'.fusion-content-widget-area .widget li a:hover:before',
		'.fusion-content-widget-area .widget_nav_menu li a:hover:before',
		'.fusion-content-widget-area .widget_categories li a:hover:before',
		'.fusion-content-widget-area .widget .recentcomments:hover:before',
		'.fusion-content-widget-area .widget_recent_entries li a:hover:before',
		'.fusion-content-widget-area .widget_archive li a:hover:before',
		'.fusion-content-widget-area .widget_pages li a:hover:before',
		'.fusion-content-widget-area .widget_links li a:hover:before',
		'.side-nav .arrow:hover:after',
		'#wrapper .jtwt .jtwt_tweet a:hover',
		'.star-rating:before',
		'.star-rating span:before',
		'#wrapper .fusion-widget-area .current_page_item > a',
		'#wrapper .fusion-widget-area .current-menu-item > a',
		'#wrapper .fusion-widget-area .current_page_item > a:before',
		'#wrapper .fusion-widget-area .current-menu-item > a:before',
		'.side-nav ul > li.current_page_item > a',
		'.side-nav li.current_page_ancestor > a',
		'.price ins .amount',
		'.price > .amount',
	);
	if ( is_rtl() ) {
		$elements[] = '.rtl .more a:hover:before';
		$elements[] = '.rtl .fusion-read-more:hover:before';
	}
	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper span.ginput_total';
		$elements[] = '.gform_wrapper span.ginput_product_price';
		$elements[] = '.ginput_shipping_price';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.bbp-pagination .bbp-pagination-links .pagination-prev:hover:before';
		$elements[] = '.bbp-pagination .bbp-pagination-links .pagination-next:hover:after';
		$elements[] = '.bbp-topics-front ul.super-sticky a:hover';
		$elements[] = '.bbp-topics ul.super-sticky a:hover';
		$elements[] = '.bbp-topics ul.sticky a:hover';
		$elements[] = '.bbp-forum-content ul.sticky a:hover';

	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce .address .edit:hover:after';
		$elements[] = '.woocommerce-tabs .tabs a:hover .arrow:after';
		$elements[] = '.woocommerce-pagination .prev:hover';
		$elements[] = '.woocommerce-pagination .next:hover';
		$elements[] = '.woocommerce-pagination .prev:hover:before';
		$elements[] = '.woocommerce-pagination .next:hover:after';
		$elements[] = '.woocommerce-tabs .tabs li.active a';
		$elements[] = '.woocommerce-tabs .tabs li.active a .arrow:after';
		$elements[] = '.woocommerce-side-nav li.is-active a';
		$elements[] = '.woocommerce-side-nav li.is-active a:after';
		$elements[] = '.woocommerce-Pagination .woocommerce-Button:hover:before';
		$elements[] = '.woocommerce-Pagination .woocommerce-Button:hover:after';
		$elements[] = '.woocommerce-MyAccount-navigation ul li.is-active a';
		$elements[] = '.woocommerce-MyAccount-navigation ul li.is-active a:after';
		$elements[] = '.woocommerce-MyAccount-content .woocommerce-Addresses .edit:hover';
		$elements[] = '.woocommerce-MyAccount-content .woocommerce-Addresses .edit:hover:after';
		$elements[] = '.woocommerce-MyAccount-downloads .download-actions a:hover';
		$elements[] = '.woocommerce-MyAccount-downloads .download-actions a:hover:after';
		$elements[] = '.my_account_orders .woocommerce-orders-table__cell-order-actions a:hover:after';
		$elements[] = '.avada-order-details .shop_table.order_details tfoot tr:last-child .amount';
		$elements[] = '#wrapper .cart-checkout a:hover';
		$elements[] = '#wrapper .cart-checkout a:hover:before';
		$elements[] = '.widget_shopping_cart_content .total .amount';
		$elements[] = '.widget_layered_nav li a:hover:before';
		$elements[] = '.widget_product_categories li a:hover:before';
		$elements[] = '.my_account_orders .woocommerce-orders-table__cell-order-number a';
		$elements[] = '.shop_table .product-subtotal .amount';
		$elements[] = '.cart_totals .order-total .amount';
		$elements[] = '.checkout .shop_table tfoot .order-total .amount';
		$elements[] = '#final-order-details .mini-order-details tr:last-child .amount';
		$elements[] = '.fusion-carousel-title-below-image .fusion-carousel-meta .price .amount';
		$elements[] = '.widget_shopping_cart_content a:hover:before';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '.tribe-events-gmap:hover:before';
		$elements[] = '.tribe-events-gmap:hover:after';
		$elements[] = '.tribe-events-nav-previous a:hover:before, .tribe-events-nav-previous a:hover:after';
		$elements[] = '.tribe-events-nav-next a:hover:before, .tribe-events-nav-next a:hover:after';
		$elements[] = '#tribe-events-content .tribe-events-sub-nav li a:hover';
		$elements[] = '.tribe-mini-calendar-event .list-date .list-dayname';
		$elements[] = '#tribe_events_filters_wrapper .tribe_events_slider_val';
		$elements[] = '.tribe-events-tickets .tickets_price .amount';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$css['global']['.fusion-content-widget-area .fusion-image-wrapper .fusion-rollover .fusion-rollover-content a:hover']['color'] = '#333333';

	$elements = array( '.star-rating:before', '.star-rating span:before' );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$elements = array( '.tagcloud a:hover', '#slidingbar-area .tagcloud a:hover', '.fusion-body .fusion-footer-widget-area .tagcloud a:hover' );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']       = '#FFFFFF';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['text-shadow'] = 'none';

	$elements = array(
		'#wrapper .fusion-tabs-widget .tab-holder .tabs li.active a',
		'#wrapper .post-content blockquote',
		'.comment-text blockquote',
		'.progress-bar-content',
		'.pagination .current',
		'.pagination a.inactive:hover',
		'.fusion-hide-pagination-text .pagination-prev:hover',
		'.fusion-hide-pagination-text .pagination-next:hover',
		'#nav ul li > a:hover',
		'#sticky-nav ul li > a:hover',
		'.tagcloud a:hover',
	);
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.bbp-pagination .bbp-pagination-links .current';
		$elements[] = '.bbp-topic-pagination .page-numbers:hover';
		$elements[] = '#bbpress-forums div.bbp-topic-tags a:hover';
		$elements[] = '.fusion-hide-pagination-text .bbp-pagination .bbp-pagination-links .pagination-prev:hover';
		$elements[] = '.fusion-hide-pagination-text .bbp-pagination .bbp-pagination-links .pagination-next:hover';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-pagination .page-numbers.current';
		$elements[] = '.woocommerce-pagination .page-numbers:hover';
		$elements[] = '.woocommerce-pagination .current';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$css['global']['#wrapper .side-nav li.current_page_item a']['border-right-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
	$css['global']['#wrapper .side-nav li.current_page_item a']['border-left-color']  = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$elements = array(
		'ul.circle-yes li:before',
		'.circle-yes ul li:before',
		'.progress-bar-content',
		'.pagination .current',
		'.fusion-date-and-formats .fusion-date-box',
		'.table-2 table thead',
		'.tagcloud a:hover',
		'#toTop:hover',
		'#wrapper .search-table .search-button input[type="submit"]:hover',
		'ul.arrow li:before',
	);
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.bbp-pagination .bbp-pagination-links .current';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.onsale';
		$elements[] = '.woocommerce-pagination .current';
		$elements[] = '.woocommerce .social-share li a:hover i';
		$elements[] = '.price_slider_wrapper .ui-slider .ui-slider-range';
		$elements[] = 'p.demo_store';
		$elements[] = '.avada-myaccount-data .digital-downloads li:before';
		$elements[] = '.avada-thank-you .order_details li:before';
		$elements[] = '.fusion-content-widget-area .widget_layered_nav li.chosen';
		$elements[] = '.fusion-content-widget-area .widget_layered_nav_filters li.chosen';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '.tribe-events-calendar thead th';
		$elements[] = 'body #wrapper .tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-]';
		$elements[] = 'body #wrapper .tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-]>a';
		$elements[] = 'body #wrapper #tribe-events-content .tribe-events-calendar td.tribe-events-present.mobile-active:hover';
		$elements[] = 'body #wrapper #tribe-events-content .tribe-events-calendar .mobile-active:hover';
		$elements[] = 'body #wrapper .tribe-events-calendar .mobile-active div[id*=tribe-events-daynum-]';
		$elements[] = '#tribe-events-content .tribe-events-tooltip h4';
		$elements[] = '.tribe-events-list-separator-month';
		$elements[] = '.tribe-mini-calendar-event .list-date';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	if ( class_exists( 'bbPress' ) ) {
		$elements = array(
			'.bbp-topics-front ul.super-sticky',
			'.bbp-topics ul.super-sticky',
			'.bbp-topics ul.sticky',
			'.bbp-forum-content ul.sticky',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = '#ffffe8';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity']          = '1';
	}

	if ( Avada()->settings->get( 'slidingbar_widgets' ) ) {

		$css['global']['#slidingbar']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_bg_color' ) );

		$css['global']['.sb-toggle-wrapper']['border-top-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_bg_color' ) );

		$css['global']['#wrapper #slidingbar-area .fusion-tabs-widget .tab-holder .tabs li']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_bg_color' ) );

		if ( Avada()->settings->get( 'slidingbar_top_border' ) ) {

			$css['global']['#slidingbar-area']['border-bottom'][] = '3px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_bg_color' ) );

			$css['global']['.fusion-header-wrapper']['margin-top']   = '3px';
			$css['global']['.admin-bar p.demo_store']['padding-top'] = '13px';

		}

		if ( ( ( 'Boxed' == Avada()->settings->get( 'layout' ) && 'default' == get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) || 'boxed' == get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) && 'Top' != Avada()->settings->get( 'header_position' ) ) {
			$elements = array(
				'.side-header-right #slidingbar-area',
				'.side-header-left #slidingbar-area',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = 'auto';
		}
	}

	$elements = array(
		'.fusion-separator .icon-wrapper',
		'html',
		'body',
		'#sliders-container',
		'#fusion-gmap-container',
	);

	if ( 'Boxed' != Avada()->settings->get( 'layout' ) ) {
		$elements[] = '#wrapper';
		$elements[] = '#main';
	} elseif ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) {
		$elements[] = '.above-footer-wrapper';
		$css['global']['#main']['background-color'] = 'transparent';
	} else {
		$elements[] = '#main';
	}

	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.bbp-arrow';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-tabs > .tabs .active a';
		$elements[] = '.woocommerce-MyAccount-navigation > ul .is-active a';
		$elements[] = '.woocommerce-checkout-nav .is-active a';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) );

	$css['global']['.fusion-footer-widget-area']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_bg_color' ) );

	$css['global']['#wrapper .fusion-footer-widget-area .fusion-tabs-widget .tab-holder .tabs li']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_bg_color' ) );

	$css['global']['.fusion-footer-widget-area']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_border_color' ) );
	$css['global']['.fusion-footer-widget-area']['border-top-width'] = intval( Avada()->settings->get( 'footer_border_size' ) ) . 'px';

	$css['global']['.fusion-footer-copyright-area']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'copyright_bg_color' ) );
	$css['global']['.fusion-footer-copyright-area']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'copyright_border_color' ) );
	$css['global']['.fusion-footer-copyright-area']['border-top-width'] = intval( Avada()->settings->get( 'copyright_border_size' ) ) . 'px';

	$css['global']['.fusion-copyright-notice']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'copyright_text_color' ) );
	$css['global']['.fusion-copyright-notice a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'copyright_link_color' ) );
	$css['global']['.fusion-footer-copyright-area a:hover']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'copyright_link_color_hover' ) );

	$css['global']['.fusion-image-wrapper .fusion-rollover']['background-image'][] = 'linear-gradient(top, ' . Fusion_Sanitize::color( Avada()->settings->get( 'image_gradient_top_color' ) ) . ' 0%, ' . Fusion_Sanitize::color( Avada()->settings->get( 'image_gradient_bottom_color' ) ) . ' 100%)';
	$css['global']['.fusion-image-wrapper .fusion-rollover']['background-image'][] = '-webkit-gradient(linear, left top, left bottom, color-stop(0, ' . Fusion_Sanitize::color( Avada()->settings->get( 'image_gradient_top_color' ) ) . '), color-stop(1, ' . Fusion_Sanitize::color( Avada()->settings->get( 'image_gradient_bottom_color' ) ) . '))';
	$css['global']['.fusion-image-wrapper .fusion-rollover']['background-image'][] = 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'image_gradient_top_color' ) )->to_css( 'hex' ) . ', endColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'image_gradient_bottom_color' ) )->to_css( 'hex' ) . '), progid: DXImageTransform.Microsoft.Alpha(Opacity=0)';

	$css['global']['.no-cssgradients .fusion-image-wrapper .fusion-rollover']['background'] = Fusion_Color::new_color( Avada()->settings->get( 'image_gradient_top_color' ) )->to_css( 'hex' );

	$css['global']['.fusion-image-wrapper:hover .fusion-rollover']['filter'] = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'image_gradient_top_color' ) )->to_css( 'hex' ) . ', endColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'image_gradient_bottom_color' ) )->to_css( 'hex' ) . '), progid: DXImageTransform.Microsoft.Alpha(Opacity=100)';

	$elements = array(
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-link',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-gallery',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_text_color' ) );

	$elements = array(
		'.fusion-rollover .fusion-rollover-content .fusion-rollover-title',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-title a',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories a',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content a',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .price *',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a:before',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_text_color' ) );

	$css['global']['.fusion-page-title-bar']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'page_title_border_color' ) );

	if ( '0' == Fusion_Color::new_color( Avada()->settings->get( 'page_title_border_color' ) )->alpha ) {
		$css['global']['.fusion-page-title-bar']['border'] = 'none';
	}

	if ( '' != Avada()->settings->get( 'footerw_bg_image', 'url' ) ) {

		$css['global']['.fusion-footer-widget-area']['background-image']    = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'footerw_bg_image', 'url' ) ) . '")';
		$css['global']['.fusion-footer-widget-area']['background-repeat']   = esc_attr( Avada()->settings->get( 'footerw_bg_repeat' ) );
		$css['global']['.fusion-footer-widget-area']['background-position'] = esc_attr( Avada()->settings->get( 'footerw_bg_pos' ) );

		if ( Avada()->settings->get( 'footerw_bg_full' ) ) {

			$css['global']['.fusion-footer-widget-area']['background-attachment'] = 'scroll';
			$css['global']['.fusion-footer-widget-area']['background-position']   = 'center center';
			$css['global']['.fusion-footer-widget-area']['background-size']       = 'cover';

		}
	}

	$css['global'][ $footer_headings_typography_elements ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'footer_headings_typography' ) );
	$css['global'][ $footer_headings_typography_elements ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'footer_headings_typography', 'font-size' ) );
	$css['global'][ $footer_headings_typography_elements ]['font-weight']    = intval( Avada()->settings->get( 'footer_headings_typography', 'font-weight' ) );
	$css['global'][ $footer_headings_typography_elements ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'footer_headings_typography', 'line-height' ) );
	$css['global'][ $footer_headings_typography_elements ]['letter-spacing'] = round( Avada()->settings->get( 'footer_headings_typography', 'letter-spacing' ) ) . 'px';

	$font_style = Avada()->settings->get( 'footer_headings_typography', 'font-style' );
	if ( ! empty( $font_style ) ) {
		$css['global'][ $footer_headings_typography_elements ]['font-style'] = esc_attr( Avada()->settings->get( 'footer_headings_typography', 'font-style' ) );
	}

	if ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_area_bg_parallax', 'footer_sticky_with_parallax_bg_image' ) ) ) {
		$css['global']['.fusion-footer-widget-area']['background-attachment'] = 'fixed';
		$css['global']['.fusion-footer-widget-area']['background-position']   = 'top center';
	}

	if ( 'footer_parallax_effect' == Avada()->settings->get( 'footer_special_effects' ) ) {
		$elements = array(
			'#sliders-container',
			'#fusion-gmap-container',
			'.fusion-page-title-bar',
			'#main',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position']  = 'relative';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['z-index']   = '1';
	}

	if ( 0 != intval( Avada()->settings->get( 'footer_sticky_height' ) ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
		$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']     = '100%';
		$css['global']['.above-footer-wrapper']['min-height']    = '100%';
		$css['global']['.above-footer-wrapper']['margin-bottom'] = (int) Avada()->settings->get( 'footer_sticky_height' ) * ( -1 ) . 'px';
		$css['global']['.above-footer-wrapper:after']['content'] = '""';
		$css['global']['.above-footer-wrapper:after']['display'] = 'block';
		$css['global']['.above-footer-wrapper:after']['height']  = intval( Avada()->settings->get( 'footer_sticky_height' ) ) . 'px';
		$css['global']['.fusion-footer']['height']               = intval( Avada()->settings->get( 'footer_sticky_height' ) ) . 'px';
	}

	$css['global']['.fusion-footer-widget-area']['padding-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'footer_area_padding', 'top' ) );
	$css['global']['.fusion-footer-widget-area']['padding-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'footer_area_padding', 'bottom' ) );

	$elements = array(
		'.fusion-footer-widget-area > .fusion-row',
		'.fusion-footer-copyright-area > .fusion-row',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = Fusion_Sanitize::size( Avada()->settings->get( 'footer_area_padding', 'left' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = Fusion_Sanitize::size( Avada()->settings->get( 'footer_area_padding', 'right' ) );

	if ( Avada()->settings->get( 'footer_100_width' ) ) {
		$elements = array(
			'.layout-wide-mode .fusion-footer-widget-area > .fusion-row',
			'.layout-wide-mode .fusion-footer-copyright-area > .fusion-row',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = '100% !important';
	}

	$css['global']['.fusion-footer-copyright-area']['padding-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'copyright_padding', 'top' ) );
	$css['global']['.fusion-footer-copyright-area']['padding-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'copyright_padding', 'bottom' ) );

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$css['global']['.tribe-events-single .related-posts .fusion-title .title-sep']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_border_color' ), Avada()->settings->get_default( 'ec_border_color' ) );
	}

	if ( isset( $body_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'body_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'body_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'body_typography', 'font-style' ) );
		}
	}
	if ( isset( $body_typography_elements['line-height'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['line-height'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'body_typography', 'line-height' ) );
	}
	if ( isset( $body_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'body_typography', 'font-size' ) );
	}
	if ( isset( $body_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['color'] ) ]['color']          = Fusion_Sanitize::color( Avada()->settings->get( 'body_typography', 'color' ) );
	}

	$elements = array(
		'.avada-container h3',
		'.review blockquote div strong',
		'.fusion-footer-widget-area h3',
		'#slidingbar-area h3',
		'.project-content .project-info h4',
		'.fusion-load-more-button',
		'.comment-form input[type="submit"]',
		'.ticket-selector-submit-btn[type="submit"]',
	);
	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper .gform_button';
		$elements[] = '.gform_wrapper .button';
		$elements[] = '.gform_page_footer input[type="button"]';
	}
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form input[type="submit"]';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbp_user_edit_submit';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce .single_add_to_cart_button';
		$elements[] = '.woocommerce button.button';
		$elements[] = '.woocommerce .avada-shipping-calculator-form .button';
		$elements[] = '.woocommerce .cart-collaterals .checkout-button';
		$elements[] = '.woocommerce .checkout #place_order';
		$elements[] = '.woocommerce .checkout_coupon .button';
		$elements[] = '.woocommerce .login .button';
		$elements[] = '.woocommerce .register .button';
		$elements[] = '.woocommerce .avada-order-details .order-again .button';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form .tribe-bar-submit input[type=submit]';
		$elements[] = '#tribe-events .tribe-events-button';
		$elements[] = '#tribe_events_filter_control #tribe_events_filters_toggle';
		$elements[] = '#tribe_events_filter_control #tribe_events_filters_reset';
		$elements[] = '#tribe-events .tribe-events-tickets .add-to-cart .tribe-button';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-weight'] = 'bold';

	$elements = array(
		'.meta .fusion-date',
		'.review blockquote q',
		'.post-content blockquote',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-style'] = 'italic';

	$elements = array(
		'.fusion-page-title-bar .fusion-breadcrumbs',
		'.fusion-page-title-bar .fusion-breadcrumbs li',
		'.fusion-page-title-bar .fusion-breadcrumbs li a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'breadcrumbs_font_size' ) );

	$css['global']['#wrapper .side-nav li a']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'side_nav_font_size' ) );

	$elements = array(
		'.sidebar .widget h4'
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'sidew_font_size' ) );
	$css['global'][ $nav_typography_elements ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'nav_typography' ) );
	$elements = array(
		'.fusion-main-menu-cart .fusion-widget-cart-number',
		'.fusion-flyout-cart-wrapper .fusion-widget-cart-number',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-family'] = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'nav_typography' ) );
	$css['global'][ $nav_typography_elements ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );
	$css['global'][ $nav_typography_elements ]['font-weight']    = intval( Avada()->settings->get( 'nav_typography', 'font-weight' ) );
	$css['global'][ $nav_typography_elements ]['letter-spacing'] = round( Avada()->settings->get( 'nav_typography', 'letter-spacing' ) ) . 'px';

	$css['global']['.fusion-menu-highlight-label']['border-radius'] = Fusion_Sanitize::size( Avada()->settings->get( 'main_nav_highlight_radius' ) );

	$font_style = Avada()->settings->get( 'nav_typography', 'font-style' );
	if ( ! empty( $font_style ) ) {
		$css['global'][ $nav_typography_elements ]['font-style'] = esc_attr( Avada()->settings->get( 'nav_typography', 'font-style' ) );
	}

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements = array(
			'.single-tribe_events .sidebar .widget h4',
			'.single-tribe_events .sidebar .tribe-events-single-section-title',
			'.single-tribe_events .sidebar .tribe-events-tickets-title',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidew_font_size' ) );

		$elements = array(
			'.single-tribe_events .sidebar',
			'.single-tribe_events .tribe-events-event-meta',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = intval( Avada()->settings->get( 'ec_text_font_size' ) ) . 'px';
	}

	$elements = array(
		'#slidingbar-area h3',
		'#slidingbar-area .widget-title',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'slidingbar_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'slidingbar_font_size' ) );

	$css['global']['.fusion-copyright-notice']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'copyright_font_size' ) );

	$elements = array(
		'#main .fusion-row',
		'.fusion-footer-widget-area .fusion-row',
		'#slidingbar-area .fusion-row',
		'.fusion-footer-copyright-area .fusion-row',
		'.fusion-page-title-row',
		'.tfs-slider .slide-content-container .slide-content',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );

	$elements = array(
		'#wrapper #main .post > h2.fusion-post-title',
		'#wrapper #main .post > .fusion-post-title-meta-wrap > h2.fusion-post-title',
		'#wrapper #main .fusion-post-content > .blog-shortcode-post-title',
		'#wrapper #main .fusion-post-content > h2.fusion-post-title',
		'#wrapper #main .fusion-portfolio-content > h2.fusion-post-title',
		'#wrapper .fusion-events-shortcode .fusion-events-meta h2',
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.single-product .product .product_title';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'post_titles_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'post_titles_font_lh' ) );

	$elements = array(
		'#wrapper #main .about-author .fusion-title h3',
		'#wrapper #main #comments .fusion-title h3',
		'#wrapper #main #respond .fusion-title h3',
		'#wrapper #main .related-posts .fusion-title h3',
		'#wrapper #main .related.products .fusion-title h3',
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.single-product .woocommerce-tabs h3';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'post_titles_extras_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1.5';

	$css['global']['.ei-title h2']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'es_title_font_size' ) );
	$css['global']['.ei-title h2']['line-height'] = '1.5';

	$css['global']['.ei-title h3']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'es_caption_font_size' ) );
	$css['global']['.ei-title h3']['line-height'] = '1.5';

	$elements = array(
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories a',
		'.fusion-recent-posts .columns .column .meta',
		'.fusion-carousel-meta',
		'.fusion-single-line-meta',
		'#wrapper .fusion-events-shortcode .fusion-events-meta h4',
	);
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums li.bbp-body ul.forum .bbp-forum-freshness';
		$elements[] = '#bbpress-forums li.bbp-body ul.topic .bbp-topic-freshness';
		$elements[] = '#bbpress-forums .bbp-forum-info .bbp-forum-content';
		$elements[] = '#bbpress-forums p.bbp-topic-meta';
		$elements[] = '.bbp-pagination-count';
		$elements[] = '#bbpress-forums div.bbp-topic-author .fusion-reply-id';
		$elements[] = '#bbpress-forums div.bbp-reply-author .fusion-reply-id';
		$elements[] = '#bbpress-forums .bbp-reply-header .bbp-meta';
		$elements[] = '#bbpress-forums span.bbp-admin-links a';
		$elements[] = '#bbpress-forums span.bbp-admin-links';
		$elements[] = '#bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log';
		$elements[] = '#bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log';
		$elements[] = '#bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'meta_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1.5';

	$elements = array(
		'.fusion-meta',
		'.fusion-meta-info',
		'.fusion-recent-posts .columns .column .meta',
		'.post .single-line-meta',
		'.fusion-carousel-meta',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'meta_font_size' ) );

	$elements = array(
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a',
		'.product-buttons a',
		'.fusion-rollover-linebreak',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'woo_icon_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1.5';

	// Make sure px vales have enough space before main text.
	$woo_icon_font_size = Avada()->settings->get( 'woo_icon_font_size' );
	if ( Fusion_Sanitize::get_unit( Avada()->settings->get( 'woo_icon_font_size' ) ) == 'px' ) {
		preg_match_all( '!\d+!', Avada()->settings->get( 'woo_icon_font_size' ), $matches );
		$woo_icon_font_size = $matches[0][0] + 2 . 'px';
	}

	if ( is_rtl() ) {
		$elements = array(
			'.rtl .fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a',
			'.rtl .product-buttons a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right']   = $woo_icon_font_size;

		$elements = array(
			'.rtl .fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a:before',
			'.rtl .product-buttons a:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']   = '-' . $woo_icon_font_size;
	} else {
		$elements = array(
			'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a',
			'.product-buttons a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']   = $woo_icon_font_size;

		$elements = array(
			'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-product-buttons a:before',
			'.product-buttons a:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '-' . $woo_icon_font_size;
	}

	$elements = array(
		'.pagination',
		'.page-links',
		'.pagination .pagination-next',
		'.pagination .pagination-prev',
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-pagination';
		$elements[] = '.woocommerce-pagination .next';
		$elements[] = '.woocommerce-pagination .prev';
	}

	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.bbp-pagination .bbp-pagination-links';
		$elements[] = '.bbp-pagination .bbp-pagination-links .pagination-prev';
		$elements[] = '.bbp-pagination .bbp-pagination-links .pagination-next';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'pagination_font_size' ) );

	$elements = array( '.fusion-page-title-bar h1', '.fusion-page-title-bar h3' );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'page_title_color' ) );

	$css['global'][ $dynamic_css_helpers->implode( $link_color_elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'link_color' ) );

	if ( class_exists( 'bbPress' ) ) {
		$link_color_rgb   = fusion_hex2rgb( Fusion_Sanitize::color( Avada()->settings->get( 'link_color' ) ) );
		$link_color_hover = 'rgba(' . $link_color_rgb[0] . ',' . $link_color_rgb[1] . ',' . $link_color_rgb[2] . ',0.8)';

		$css['global']['#bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a:hover']['color'] = $link_color_hover;
	}

	$css['global']['body #toTop:before']['color'] = '#fff';

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements = array(
			'.single-tribe_events .sidebar a',
			'.single-tribe_events .sidebar a:before',
			'.single-tribe_events .sidebar a:after',
			'.single-tribe_events .fusion-content-widget-area .widget li a',
			'.single-tribe_events .fusion-content-widget-area .widget li a:before',
			'.single-tribe_events .fusion-content-widget-area .widget li a:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_link_color' ) );

		$elements = array(
			'.single-tribe_events .sidebar a:hover',
			'.single-tribe_events .sidebar a:hover:before',
			'.single-tribe_events .sidebar a:hover:after',
			'.single-tribe_events .fusion-content-widget-area .widget li a:hover',
			'.single-tribe_events .fusion-content-widget-area .widget li a:hover:before',
			'.single-tribe_events .fusion-content-widget-area .widget li a:hover:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
	}

	$elements = array(
		'.fusion-page-title-bar .fusion-breadcrumbs',
		'.fusion-page-title-bar .fusion-breadcrumbs a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'breadcrumbs_text_color' ) );

	$elements = array(
		'#slidingbar-area h3',
		'#slidingbar-area .fusion-title > *',
		'#slidingbar-area .widget-title',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_headings_color' ) );

	if ( Avada()->settings->get( 'slidingbar_widgets' ) ) {
		$elements = array(
			'#slidingbar-area',
			'#slidingbar-area .widget_nav_menu li',
			'#slidingbar-area .widget_categories li',
			'#slidingbar-area .widget_product_categories li',
			'#slidingbar-area .widget_meta li',
			'#slidingbar-area .widget li.recentcomments',
			'#slidingbar-area .widget_recent_entries li',
			'#slidingbar-area .widget_archive li',
			'#slidingbar-area .widget_pages li',
			'#slidingbar-area .widget_links li',
			'#slidingbar-area .widget_layered_nav li',
			'#slidingbar-area .fusion-column',
			'#slidingbar-area .jtwt',
			'#slidingbar-area .jtwt .jtwt_tweet',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_text_color' ) );

		$elements = array(
			'.slidingbar-area a',
			'.slidingbar-area .widget li a:before',
			' #slidingbar-area .jtwt .jtwt_tweet a',
			'#wrapper #slidingbar-area .fusion-tabs-widget .tab-holder .tabs li a',
			'#slidingbar-area .fusion-accordian .panel-title a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_link_color' ) );
	}

	$elements = array(
		'.sidebar .widget h4',
		'.sidebar .widget .heading h4',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'sidebar_heading_color' ) );

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements = array(
			'.single-tribe_events .sidebar .widget h4',
			'.single-tribe_events .sidebar .widget .heading h4',
			'.single-tribe_events .sidebar .tribe-events-single-section-title',
			'.single-tribe_events .sidebar .tribe-events-tickets-title',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_heading_color' ) );

		$elements = array(
			'.single-tribe_events .sidebar'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_text_color' ) );

		$elements = array(
			'.single-tribe_events .fusion-content-widget-area .widget_nav_menu li',
			'.single-tribe_events .fusion-content-widget-area .widget_meta li',
			'.single-tribe_events .fusion-content-widget-area .widget_recent_entries li',
			'.single-tribe_events .fusion-content-widget-area .widget_archive li',
			'.single-tribe_events .fusion-content-widget-area .widget_pages li',
			'.single-tribe_events .fusion-content-widget-area .widget_links li',
			'.single-tribe_events .fusion-content-widget-area .widget li a',
			'.single-tribe_events .fusion-content-widget-area .widget .recentcomments',
			'.single-tribe_events .fusion-content-widget-area .widget_categories li',
			'.single-tribe_events #wrapper .fusion-tabs-widget .tab-holder',
			'.single-tribe_events .sidebar .tagcloud a',
			'.single-tribe_events .sidebar .tribe-events-meta-group dd',
			'.single-tribe_events .sidebar .tribe-mini-calendar-event',
			'.single-tribe_events .sidebar .tribe-events-list-widget ol li',
			'.single-tribe_events .sidebar .tribe-events-venue-widget li',
			'.single-tribe_events .sidebar .tribe-events-tickets td',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_divider_color' ) );
	}

	$elements = array(
		'.sidebar .widget .widget-title',
		'.sidebar .widget .heading .widget-title',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'sidebar_widget_bg_color' ) );

	if ( '0' != Fusion_Color::new_color( Avada()->settings->get( 'sidebar_widget_bg_color' ) )->alpha ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '9px 15px';
	}

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements = array(
			'.single-tribe_events .sidebar .widget .widget-title',
			'.single-tribe_events .sidebar .widget .heading .widget-title',
			'.single-tribe_events .sidebar .tribe-events-single-section-title',
			'.single-tribe_events .sidebar .tribe-events-tickets-title',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_widget_bg_color' ) );

		if ( '0' != Fusion_Color::new_color( Avada()->settings->get( 'ec_sidebar_widget_bg_color' ) )->alpha ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '9px 15px';
		}
	}

	$elements = array(
		'.fusion-footer-widget-area h3',
		'.fusion-footer-widget-area .widget-title',
		'.fusion-footer-widget-column .product-title',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_headings_typography', 'color' ) );

	$elements = array(
		'.fusion-footer-widget-area',
		'.fusion-footer-widget-area .widget_nav_menu li',
		'.fusion-footer-widget-area .widget_categories li',
		'.fusion-footer-widget-area .widget_product_categories li',
		'.fusion-footer-widget-area .widget_meta li',
		'.fusion-footer-widget-area .widget li.recentcomments',
		'.fusion-footer-widget-area .widget_recent_entries li',
		'.fusion-footer-widget-area .widget_archive li',
		'.fusion-footer-widget-area .widget_pages li',
		'.fusion-footer-widget-area .widget_links li',
		'.fusion-footer-widget-area .widget_layered_nav li',
		'.fusion-footer-widget-area article.col',
		'.fusion-footer-widget-area .jtwt',
		'.fusion-footer-widget-area .jtwt .jtwt_tweet',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_text_color' ) );

	$elements = array(
		'.fusion-footer-widget-area a',
		'.fusion-footer-widget-area .widget li a:before',
		'.fusion-footer-widget-area .jtwt .jtwt_tweet a',
		'#wrapper .fusion-footer-widget-area .fusion-tabs-widget .tab-holder .tabs li a',
		'.fusion-footer-widget-area .fusion-tabs-widget .tab-holder .news-list li .post-holder a',
		'.fusion-footer-widget-area .fusion-accordian .panel-title a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_link_color' ) );

	$elements = array(
		'.fusion-footer-widget-area ul li a:hover',
		'.fusion-footer-widget-area .widget a:hover',
		'.fusion-footer-widget-area .widget li a:hover:before',
		'.fusion-footer-widget-area .widget li.recentcomments:hover:before',
		'.fusion-footer-widget-area .fusion-tabs-widget .tab-holder .news-list li .post-holder a:hover',
		'.fusion-footer-widget-area .fusion-accordian .panel-title a:hover',
		'#wrapper .fusion-footer-widget-area .jtwt .jtwt_tweet a:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_link_color_hover' ) );

	$elements = array(
		'.fusion-footer-widget-area .tagcloud a:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_link_color_hover' ) );

	$elements = array(
		'#wrapper .fusion-footer-widget-area .fusion-tabs-widget .tab-holder .tabs li.active a',
		'#wrapper .fusion-footer-widget-area .fusion-tabs.classic .nav-tabs > li.active .tab-link',
		'#wrapper .fusion-footer-widget-area .fusion-tabs.classic .nav-tabs > li.active .tab-link:focus',
		'#wrapper .fusion-footer-widget-area .fusion-tabs.classic .nav-tabs > li.active .tab-link:hover',
		'#wrapper .fusion-footer-widget-area .fusion-tabs.vertical-tabs.classic .nav-tabs > li.active .tab-link',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_link_color_hover' ) );

	$elements = array(
		'.fusion-footer-widget-area .fusion-accordian .panel-title a:hover .fa-fusion-box'
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_link_color_hover' ) ) . ' !important';

	$css['global']['.ei-title h2']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'es_title_color' ) );
	$css['global']['.ei-title h3']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'es_caption_color' ) );

	$css['global']['.fusion-load-more-button.fusion-blog-button']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'blog_load_more_posts_button_bg_color' ) );
	$css['global']['.fusion-load-more-button.fusion-blog-button:hover']['background-color'] = Fusion_Color::new_color( Avada()->settings->get( 'blog_load_more_posts_button_bg_color' ) )->get_new( 'alpha', '0.8' )->to_css( 'rgba' );

	$button_brightness = fusion_calc_color_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'blog_load_more_posts_button_bg_color' ) ) );
	$text_color        = ( 140 < $button_brightness ) ? '#333' : '#fff';
	$elements = array(
		'.fusion-load-more-button.fusion-blog-button',
		'.fusion-load-more-button.fusion-blog-button:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $text_color;

	$css['global']['.fusion-load-more-button.fusion-portfolio-button']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'portfolio_load_more_posts_button_bg_color' ) );
	$css['global']['.fusion-load-more-button.fusion-portfolio-button:hover']['background-color'] = Fusion_Color::new_color( Avada()->settings->get( 'portfolio_load_more_posts_button_bg_color' ) )->get_new( 'alpha', '0.8' )->to_css( 'rgba' );

	$button_brightness = fusion_calc_color_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'portfolio_load_more_posts_button_bg_color' ) ) );
	$text_color        = ( 140 < $button_brightness ) ? '#333' : '#fff';
	$elements = array(
		'.fusion-load-more-button.fusion-portfolio-button',
		'.fusion-load-more-button.fusion-portfolio-button:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $text_color;

	if ( class_exists( 'WooCommerce' ) ) {
		$elements = array( '.quantity .minus', '.quantity .plus, .fusion-body .tribe-events-tickets .quantity .minus, .fusion-body .tribe-events-tickets .quantity .plus' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'qty_bg_color' ) );

		$elements = array( '.quantity .minus:hover', '.quantity .plus:hover, .fusion-body .tribe-events-tickets .quantity .minus:hover, .fusion-body .tribe-events-tickets .quantity .plus:hover' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'qty_bg_hover_color' ) );

		$css['global']['.woocommerce .social-share li a:hover i']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
	}

	$css['global']['.sb-toggle-wrapper .sb-toggle:after']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_toggle_icon_color' ) );

	if ( Avada()->settings->get( 'slidingbar_widgets' ) ) {
		$elements = array(
			'#slidingbar-area .widget_nav_menu li',
			'#slidingbar-area .widget_categories li',
			'#slidingbar-area .widget_product_categories li',
			'#slidingbar-area .widget_meta li',
			'#slidingbar-area .widget li.recentcomments',
			'#slidingbar-area .widget_recent_entries ul li',
			'#slidingbar-area .widget_archive li',
			'#slidingbar-area .widget_pages li',
			'#slidingbar-area .widget_links li',
			'#slidingbar-area .widget_layered_nav li',
			'#slidingbar-area .widget_product_categories li',
			'#slidingbar-area .product_list_widget li',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_divider_color' ) );

		$elements = array(
			'#slidingbar-area .tagcloud a',
			'#wrapper #slidingbar-area .fusion-tabs-widget .tab-holder',
			'#wrapper #slidingbar-area .fusion-tabs-widget .tab-holder .news-list li',
			'#slidingbar-area .fusion-accordian .fusion-panel',
		);

		if ( class_exists( 'bbPress' ) ) {
			$elements[] = '#slidingbar-area .bbp-pagination .bbp-pagination-links a.inactive';
			$elements[] = '#slidingbar-area .bbp-topic-pagination .page-numbers';
			$elements[] = '#slidingbar-area .widget.widget.widget_display_replies ul li';
			$elements[] = '#slidingbar-area .widget.widget_display_topics ul li';
			$elements[] = '#slidingbar-area .widget.widget_display_views ul li';
			$elements[] = '#slidingbar-area .widget.widget_display_stats dt';
			$elements[] = '#slidingbar-area .widget.widget_display_stats dd';
		}

		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$elements[] = '#slidingbar-area .tribe-mini-calendar-event';
			$elements[] = '#slidingbar-area .tribe-events-list-widget ol li';
			$elements[] = '#slidingbar-area .tribe-events-venue-widget li';
		}
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'slidingbar_divider_color' ) );
	} // End if().

	$elements = array(
		'.fusion-footer-widget-area .widget_nav_menu li',
		'.fusion-footer-widget-area .widget_categories li',
		'.fusion-footer-widget-area .product_list_widget li',
		'.fusion-footer-widget-area .widget_meta li',
		'.fusion-footer-widget-area .widget li.recentcomments',
		'.fusion-footer-widget-area .widget_recent_entries li',
		'.fusion-footer-widget-area .widget_archive li',
		'.fusion-footer-widget-area .widget_pages li',
		'.fusion-footer-widget-area .widget_links li',
		'.fusion-footer-widget-area .widget_layered_nav li',
		'.fusion-footer-widget-area .widget_product_categories li',
		'.fusion-footer-widget-area ul li',
		'.fusion-footer-widget-area .tagcloud a',
		'#wrapper .fusion-footer-widget-area .fusion-tabs-widget .tab-holder',
		'#wrapper .fusion-footer-widget-area .fusion-tabs-widget .tab-holder .news-list li',
		'.fusion-footer-widget-area .fusion-accordian .fusion-panel',
	);

	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '.fusion-footer-widget-area .bbp-pagination .bbp-pagination-links a.inactive';
		$elements[] = '.fusion-footer-widget-area .bbp-topic-pagination .page-numbers';
		$elements[] = '.fusion-footer-widget-area .widget.widget.widget_display_replies ul li';
		$elements[] = '.fusion-footer-widget-area .widget.widget_display_forums ul li';
		$elements[] = '.fusion-footer-widget-area .widget.widget_display_topics ul li';
		$elements[] = '.fusion-footer-widget-area .widget.widget_display_views ul li';
		$elements[] = '.fusion-footer-widget-area .widget.widget_display_stats dt';
		$elements[] = '.fusion-footer-widget-area .widget.widget_display_stats dd';
	}

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '.fusion-footer-widget-area .tribe-mini-calendar-event';
		$elements[] = '.fusion-footer-widget-area .tribe-events-list-widget ol li';
		$elements[] = '.fusion-footer-widget-area .tribe-events-venue-widget li';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'footer_divider_color' ) );

	$elements = array(
		'.input-text',
		'input[type="text"]',
		'input[type="password"]',
		'input[type="email"]',
		'textarea',
		'input.s',
		'#comment-input input',
		'#comment-textarea textarea',
		'.comment-form-comment textarea',
		'.post-password-form label input[type="password"]',
		'.main-nav-search-form input',
		'.search-page-search-form input',
		'.chzn-container-single .chzn-single',
		'.chzn-container .chzn-drop',
		'.avada-select-parent select',
		'.avada-select .select2-container .select2-choice',
		'.avada-select .select2-container .select2-choice2',
		'select',
		'#wrapper .search-table .search-field input',
	);
	if ( defined( 'ICL_SITEPRESS_VERSION' || class_exists( 'SitePress' ) ) ) {
		$elements[] = '#lang_sel_click a.lang_sel_sel';
		$elements[] = '#lang_sel_click ul ul a';
		$elements[] = '#lang_sel_click ul ul a:visited';
		$elements[] = '#lang_sel_click a';
		$elements[] = '#lang_sel_click a:visited';
	}
	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper .gfield input[type="text"]';
		$elements[] = '.gform_wrapper .gfield input[type="email"]';
		$elements[] = '.gform_wrapper .gfield input[type="tel"]';
		$elements[] = '.gform_wrapper .gfield input[type="url"]';
		$elements[] = '.gform_wrapper .gfield input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"] input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"]';
		$elements[] = '.gform_wrapper .gfield_select[multiple=multiple]';
		$elements[] = '.gform_wrapper .gfield select';
		$elements[] = '.gform_wrapper .gfield textarea';
	}
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form .wpcf7-text';
		$elements[] = '.wpcf7-form .wpcf7-quiz';
		$elements[] = '.wpcf7-form .wpcf7-number';
		$elements[] = '.wpcf7-form textarea';
		$elements[] = '.wpcf7-form .wpcf7-select';
		$elements[] = '.wpcf7-captchar';
		$elements[] = '.wpcf7-form .wpcf7-date';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums .bbp-search-form #bbp_search';
		$elements[] = '.bbp-reply-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form input#bbp_topic_title';
		$elements[] = '.bbp-topic-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form select#bbp_stick_topic_select';
		$elements[] = '.bbp-topic-form select#bbp_topic_status_select';
		$elements[] = '#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content';
		$elements[] = '.bbp-login-form input';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) );

	$elements = array(
		'.avada-select-parent .select-arrow',
		'#wrapper .select-arrow',
		'.fusion-modal-content .select-arrow',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) );

	$elements = array(
		'.input-text',
		'input[type="text"]',
		'input[type="email"]',
		'textarea',
		'input.s',
		'input.s .placeholder',
		'#comment-input input',
		'#comment-textarea textarea',
		'#comment-input .placeholder',
		'#comment-textarea .placeholder',
		'.comment-form-comment textarea',
		'.post-password-form label input[type="password"]',
		'.avada-select .select2-container .select2-choice',
		'.avada-select .select2-container .select2-choice2',
		'select',
		'.main-nav-search-form input',
		'.search-page-search-form input',
		'.chzn-container-single .chzn-single',
		'.chzn-container .chzn-drop',
		'.avada-select-parent select',
		'#wrapper .search-table .search-field input',
	);
	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper .gfield input[type="text"]';
		$elements[] = '.gform_wrapper .gfield input[type="email"]';
		$elements[] = '.gform_wrapper .gfield input[type="tel"]';
		$elements[] = '.gform_wrapper .gfield input[type="url"]';
		$elements[] = '.gform_wrapper .gfield input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"] input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"]';
		$elements[] = '.gform_wrapper .gfield_select[multiple=multiple]';
		$elements[] = '.gform_wrapper .gfield select';
		$elements[] = '.gform_wrapper .gfield textarea';
	}
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form .wpcf7-text';
		$elements[] = '.wpcf7-form .wpcf7-quiz';
		$elements[] = '.wpcf7-form .wpcf7-number';
		$elements[] = '.wpcf7-form textarea';
		$elements[] = '.wpcf7-form .wpcf7-select';
		$elements[] = '.wpcf7-select-parent .select-arrow';
		$elements[] = '.wpcf7-captchar';
		$elements[] = '.wpcf7-form .wpcf7-date';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums .bbp-search-form #bbp_search';
		$elements[] = '.bbp-reply-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form input#bbp_topic_title';
		$elements[] = '.bbp-topic-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form select#bbp_stick_topic_select';
		$elements[] = '.bbp-topic-form select#bbp_topic_status_select';
		$elements[] = '#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content';
		$elements[] = '.bbp-login-form input';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$elements = array(
		'input#s::-webkit-input-placeholder',
		'#comment-input input::-webkit-input-placeholder',
		'.post-password-form label input[type="password"]::-webkit-input-placeholder',
		'#comment-textarea textarea::-webkit-input-placeholder',
		'.comment-form-comment textarea::-webkit-input-placeholder',
		'.input-text::-webkit-input-placeholder',
		'input::-webkit-input-placeholder',
		'.searchform .s::-webkit-input-placeholder',
	);
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form textarea::-webkit-input-placeholder';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]::-webkit-input-placeholder';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]::-webkit-input-placeholder';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$elements = array(
		'input#s:-moz-placeholder',
		'#comment-input input:-moz-placeholder',
		'.post-password-form label input[type="password"]:-moz-placeholder',
		'#comment-textarea textarea:-moz-placeholder',
		'.comment-form-comment textarea:-moz-placeholder',
		'.input-text:-moz-placeholder',
		'input:-moz-placeholder',
		'.searchform .s:-moz-placeholder',
	);
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form textarea::-moz-input-placeholder';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]:-moz-placeholder';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]:-moz-placeholder';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$elements = array(
		'input#s::-moz-placeholder',
		'#comment-input input::-moz-placeholder',
		'.post-password-form label input[type="password"]::-moz-placeholder',
		'#comment-textarea textarea::-moz-placeholder',
		'.comment-form-comment textarea::-moz-placeholder',
		'.input-text::-moz-placeholder',
		'input::-moz-placeholder',
		'.searchform .s::-moz-placeholder',
	);
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]::-moz-placeholder';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]::-moz-placeholder';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$elements = array(
		'input#s:-ms-input-placeholder',
		'#comment-input input:-ms-input-placeholder',
		'.post-password-form label input[type="password"]::-ms-input-placeholder',
		'#comment-textarea textarea:-ms-input-placeholder',
		'.comment-form-comment textarea:-ms-input-placeholder',
		'.input-text:-ms-input-placeholder',
		'input:-ms-input-placeholder',
		'.searchform .s:-ms-input-placeholder',
	);
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]::-ms-input-placeholder';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]::-ms-input-placeholder';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$elements = array(
		'.input-text',
		'input[type="text"]',
		'input[type="email"]',
		'textarea',
		'input.s',
		'#comment-input input',
		'#comment-textarea textarea',
		'.comment-form-comment textarea',
		'.post-password-form label input[type="password"]',
		'.gravity-select-parent .select-arrow',
		'.select-arrow',
		'.main-nav-search-form input',
		'.search-page-search-form input',
		'.chzn-container-single .chzn-single',
		'.chzn-container .chzn-drop',
		'.avada-select-parent select',
		'.avada-select-parent .select-arrow',
		'select',
		'#wrapper .search-table .search-field input',
		'.avada-select .select2-container .select2-choice',
		'.avada-select .select2-container .select2-choice .select2-arrow',
		'.avada-select .select2-container .select2-choice2 .select2-arrow',
	);
	if ( defined( 'ICL_SITEPRESS_VERSION' || class_exists( 'SitePress' ) ) ) {
		$elements[] = '#lang_sel_click a.lang_sel_sel';
		$elements[] = '#lang_sel_click ul ul a';
		$elements[] = '#lang_sel_click ul ul a:visited';
		$elements[] = '#lang_sel_click a';
		$elements[] = '#lang_sel_click a:visited';
	}
	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper .gfield input[type="text"]';
		$elements[] = '.gform_wrapper .gfield input[type="email"]';
		$elements[] = '.gform_wrapper .gfield input[type="tel"]';
		$elements[] = '.gform_wrapper .gfield input[type="url"]';
		$elements[] = '.gform_wrapper .gfield input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"] input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"]';
		$elements[] = '.gform_wrapper .gfield_select[multiple=multiple]';
		$elements[] = '.gform_wrapper .gfield select';
		$elements[] = '.gform_wrapper .gfield textarea';
	}
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form .wpcf7-text';
		$elements[] = '.wpcf7-form .wpcf7-quiz';
		$elements[] = '.wpcf7-form .wpcf7-number';
		$elements[] = '.wpcf7-form textarea';
		$elements[] = '.wpcf7-form .wpcf7-select';
		$elements[] = '.wpcf7-select-parent .select-arrow';
		$elements[] = '.wpcf7-captchar';
		$elements[] = '.wpcf7-form .wpcf7-date';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums .quicktags-toolbar';
		$elements[] = '#bbpress-forums .bbp-search-form #bbp_search';
		$elements[] = '.bbp-reply-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form input#bbp_topic_title';
		$elements[] = '.bbp-topic-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form select#bbp_stick_topic_select';
		$elements[] = '.bbp-topic-form select#bbp_topic_status_select';
		$elements[] = '#bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content';
		$elements[] = '#wp-bbp_topic_content-editor-container';
		$elements[] = '#wp-bbp_reply_content-editor-container';
		$elements[] = '.bbp-login-form input';
		$elements[] = '#bbpress-forums .wp-editor-container';
		$elements[] = '#wp-bbp_topic_content-editor-container';
		$elements[] = '#wp-bbp_reply_content-editor-container';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-checkout .select2-drop-active';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_border_color' ) );

	$elements = array(
		'.input-text:not(textarea)',
		'input[type="text"]',
		'input[type="password"]',
		'input[type="email"]',
		'input.s',
		'#comment-input input',
		'.post-password-form label input[type="password"]',
		'.main-nav-search-form input',
		'.search-page-search-form input',
		'.chzn-container-single .chzn-single',
		'.chzn-container .chzn-drop',
		'select',
		'.searchform .search-table .search-field input',
		'.avada-select-parent select',
		'.avada-select .select2-container .select2-choice',
		'.fusion-login-box .fusion-login-form input[type=text]',
		'.fusion-login-box .fusion-login-form input[type=password]',
	);

	if ( class_exists( 'GFForms' ) ) {
		$elements[] = '.gform_wrapper .gfield input[type="text"]';
		$elements[] = '.gform_wrapper .gfield input[type="email"]';
		$elements[] = '.gform_wrapper .gfield input[type="tel"]';
		$elements[] = '.gform_wrapper .gfield input[type="url"]';
		$elements[] = '.gform_wrapper .gfield input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"] input[type="number"]';
		$elements[] = '.gform_wrapper .gfield input[type="password"]';
		$elements[] = '.gform_wrapper .gfield_select[multiple=multiple]';
		$elements[] = '.gform_wrapper .gfield select';
	}
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.wpcf7-form .wpcf7-text';
		$elements[] = '.wpcf7-form .wpcf7-quiz';
		$elements[] = '.wpcf7-form .wpcf7-number';
		$elements[] = '.wpcf7-form .wpcf7-select';
		$elements[] = '.wpcf7-captchar';
		$elements[] = '.wpcf7-form .wpcf7-date';
	}
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums .bbp-search-form #bbp_search';
		$elements[] = '.bbp-reply-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form input#bbp_topic_title';
		$elements[] = '.bbp-topic-form input#bbp_topic_tags';
		$elements[] = '.bbp-topic-form select#bbp_stick_topic_select';
		$elements[] = '.bbp-topic-form select#bbp_topic_status_select';
		$elements[] = '.bbp-login-form input';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.avada-shipping-calculator-form .avada-select-parent select';
		$elements[] = '.shipping-calculator-form .avada-select-parent select';
		$elements[] = '.cart-collaterals .form-row input';
		$elements[] = '.cart-collaterals .avada-select-parent input';
		$elements[] = '.cart-collaterals .woocommerce-shipping-calculator #calc_shipping_postcode';
		$elements[] = '.coupon .input-text';
		$elements[] = '.checkout .input-text:not(textarea)';
		$elements[] = '.woocommerce-checkout .select2-drop-active';
	}
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements[] = '#tribe-bar-form input[type=text]';
		$elements[] = '.tribe-bar-disabled #tribe-bar-form .tribe-bar-filters input[type=text]';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-top'] = '0';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '0';

	$elements = array(
		'.avada-select .select2-container .select2-choice .select2-arrow',
		'.avada-select .select2-container .select2-choice2 .select2-arrow',
		'.searchform .search-table .search-button input[type="submit"]',
	);

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']      = Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']       = Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) );

	$css['global']['.select2-container .select2-choice > .select2-chosen']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) );

	$elements = array( '.select-arrow', '.select2-arrow' );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_border_color' ) );

	if ( class_exists( 'GFForms' ) ) {
		$css['global']['.gfield_time_ampm .gravity-select-parent']['width'] = 'auto !important';
		$css['global']['.gfield_time_ampm .gravity-select-parent select']['min-width'] = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'form_input_height' ) ) . ' * 2) !important';
	}

	$height_fraction = intval( Avada()->settings->get( 'form_input_height' ) ) / 35;
	if ( 1 < $height_fraction ) {
		$css['global']['.fusion-main-menu .fusion-main-menu-search .fusion-custom-menu-item-contents']['width'] = 250 + 50 * $height_fraction . 'px';
	}

	if ( ! Avada()->settings->get( 'avada_styles_dropdowns' ) ) {

		$css['global']['select']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_border_color' ) );
		$css['global']['select']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );
		$css['global']['select']['border']           = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'form_border_color' ) );
		$css['global']['select']['font-size']        = '13px';
		$css['global']['select']['height']           = '35px';
		$css['global']['select']['text-indent']      = '5px';
		$css['global']['select']['width']            = '100%';

		$css['global']['select::-webkit-input-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );
		$css['global']['select:-moz-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );
	}

	$css['global']['.fusion-page-title-bar .fusion-page-title-row h1']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'page_title_font_size' ) );
	$css['global']['.fusion-page-title-bar .fusion-page-title-row h1']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'page_title_line_height' ) );

	$css['global']['.fusion-page-title-bar h3']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'page_title_subheader_font_size' ) );
	$css['global']['.fusion-page-title-bar h3']['line-height'] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'page_title_subheader_font_size' ) ), '12px' ) );

	if ( false !== strpos( Avada()->settings->get( 'site_width' ), 'px' ) ) {
		$margin      = '80px';
		$half_margin = '40px';
	} else {
		$margin      = '6%';
		$half_margin = '3%';
	}

	/**
	 * Portfolio Styling Options
	 */

	// Portfolio Text Alignment / portfolio_text_alignment.
	$elements = array(
		'.fusion-portfolio-content-wrapper .fusion-portfolio-content'
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = Avada()->settings->get( 'portfolio_text_alignment' );

	// Portfolio Text Layout Padding / portfolio_layout_padding.
	$elements = array(
		'.fusion-portfolio-boxed .fusion-portfolio-content'
	);
	$padding = Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_archive_layout_padding', 'top' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_archive_layout_padding', 'right' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_archive_layout_padding', 'bottom' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_archive_layout_padding', 'left' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = $padding;

	$elements = array(
		'.fusion-portfolio-boxed.fusion-portfolio-element .fusion-portfolio-content'
	);
	$padding = Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_layout_padding', 'top' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_layout_padding', 'right' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_layout_padding', 'bottom' ) );
	$padding .= ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'portfolio_layout_padding', 'left' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = $padding;

	// Stop page type class overriding shortcode.
	$css['global']['.fusion-portfolio-boxed .fusion-portfolio-unboxed .fusion-portfolio-content']['padding'] = '0px';
	$css['global']['.fusion-portfolio-boxed .fusion-portfolio-unboxed .fusion-portfolio-content']['margin'] = '20px 0px';
	$css['global']['.fusion-portfolio-boxed .fusion-portfolio-unboxed .fusion-portfolio-content-wrapper']['border'] = 'none';
	/**
	 * Single-sidebar Layouts
	 */
	$sidebar_width = Fusion_Sanitize::size( Avada()->settings->get( 'sidebar_width' ) );
	if ( false === strpos( $sidebar_width, 'px' ) && false === strpos( $sidebar_width, '%' ) ) {
		$sidebar_width = ( 100 > intval( $sidebar_width ) ) ? intval( $sidebar_width ) . '%' : intval( $sidebar_width ) . 'px';
	}
	$css['global']['body.has-sidebar #content']['width']       = Fusion_Sanitize::add_css_values( array( '100%', '-' . $sidebar_width, '-' . $margin ) );
	$css['global']['body.has-sidebar #main .sidebar']['width'] = $sidebar_width;
	/**
	 * Double-Sidebar layouts
	 */
	$sidebar_2_1_width = Fusion_Sanitize::size( Avada()->settings->get( 'sidebar_2_1_width' ) );
	if ( false === strpos( $sidebar_2_1_width, 'px' ) && false === strpos( $sidebar_2_1_width, '%' ) ) {
		$sidebar_2_1_width = ( 100 > intval( $sidebar_2_1_width ) ) ? intval( $sidebar_2_1_width ) . '%' : intval( $sidebar_2_1_width ) . 'px';
	}
	$sidebar_2_2_width = Fusion_Sanitize::size( Avada()->settings->get( 'sidebar_2_2_width' ) );
	if ( false === strpos( $sidebar_2_2_width, 'px' ) && false === strpos( $sidebar_2_2_width, '%' ) ) {
		$sidebar_2_2_width = ( 100 > intval( $sidebar_2_2_width ) ) ? intval( $sidebar_2_2_width ) . '%' : intval( $sidebar_2_2_width ) . 'px';
	}
	$css['global']['body.has-sidebar.double-sidebars #content']['width']               = Fusion_Sanitize::add_css_values( array( '100%', '-' . $sidebar_2_1_width, '-' . $sidebar_2_2_width, '-' . $margin ) );
	$css['global']['body.has-sidebar.double-sidebars #content']['margin-left']         = Fusion_Sanitize::add_css_values( array( $sidebar_2_1_width, $half_margin ) );
	$css['global']['body.has-sidebar.double-sidebars #main #sidebar']['width']         = $sidebar_2_1_width;
	$css['global']['body.has-sidebar.double-sidebars #main #sidebar']['margin-left']   = Fusion_Sanitize::add_css_values( array( $half_margin, '-100%', $sidebar_2_2_width ) );
	$css['global']['body.has-sidebar.double-sidebars #main #sidebar-2']['width']       = $sidebar_2_2_width;
	$css['global']['body.has-sidebar.double-sidebars #main #sidebar-2']['margin-left'] = $half_margin;

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$sidebar_width = Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidebar_width' ) );
		if ( false !== strpos( $sidebar_width, 'px' ) && false !== strpos( $sidebar_width, '%' ) ) {
			$sidebar_width = ( 100 > intval( $sidebar_width ) ) ? intval( $sidebar_width ) . '%' : intval( $sidebar_width ) . 'px';
		}
		if ( tribe_get_option( 'tribeEventsTemplate', 'default' ) != '100-width.php' ) {
			$css['global']['.single-tribe_events #content']['width'] = Fusion_Sanitize::add_css_values( array( '100%', '-' . $sidebar_width, '-' . $margin ) );
			$css['global']['.single-tribe_events #main .sidebar']['width'] = $sidebar_width;
		}
		/**
		 * Single-sidebar Layouts
		 */
		$css['global']['body.has-sidebar.single-tribe_events #content']['width']       = Fusion_Sanitize::add_css_values( array( '100%', '-' . $sidebar_width, '-' . $margin ) );
		$css['global']['body.has-sidebar.single-tribe_events #main .sidebar']['width'] = $sidebar_width;
		/**
		 * Double-Sidebar layouts
		 */
		$sidebar_2_1_width = Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidebar_2_1_width' ) );
		if ( false === strpos( $sidebar_2_1_width, 'px' ) && false === strpos( $sidebar_2_1_width, '%' ) ) {
			$sidebar_2_1_width = ( 100 > intval( $sidebar_2_1_width ) ) ? intval( $sidebar_2_1_width ) . '%' : intval( $sidebar_2_1_width ) . 'px';
		}
		$sidebar_2_2_width = Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidebar_2_2_width' ) );
		if ( false === strpos( $sidebar_2_2_width, 'px' ) && false === strpos( $sidebar_2_2_width, '%' ) ) {
			$sidebar_2_2_width = ( 100 > intval( $sidebar_2_2_width ) ) ? intval( $sidebar_2_2_width ) . '%' : intval( $sidebar_2_2_width ) . 'px';
		}
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #content']['width']               = Fusion_Sanitize::add_css_values( array( '100%', '-' . $sidebar_2_1_width, '-' . $sidebar_2_2_width, '-' . $margin ) );
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #content']['margin-left']         = Fusion_Sanitize::add_css_values( array( $sidebar_2_1_width, $half_margin ) );
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #main #sidebar']['width']         = $sidebar_2_1_width;
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #main #sidebar']['margin-left']   = Fusion_Sanitize::add_css_values( array( $half_margin, '-100%', $sidebar_2_2_width ) );
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #main #sidebar-2']['width']       = $sidebar_2_2_width;
		$css['global']['body.has-sidebar.double-sidebars.single-tribe_events #main #sidebar-2']['margin-left'] = $half_margin;
	}

	$elements = array(
		'#main .sidebar:not( .fusion-sticky-sidebar )',
		'#main .fusion-sticky-sidebar .fusion-sidebar-inner-content',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'sidebar_bg_color' ) );
	$css['global']['#main .sidebar']['padding']                                      = Fusion_Sanitize::size( Avada()->settings->get( 'sidebar_padding' ) );
	$css['global']['#main .sidebar.fusion-sticky-sidebar']['padding']                = '0px';

	$sticky_padding = str_replace( '%', 'vw', Fusion_Sanitize::size( Avada()->settings->get( 'sidebar_padding' ) ) );
	$css['global']['#main .sidebar.fusion-sticky-sidebar .fusion-sidebar-inner-content']['padding'] = $sticky_padding;

	if ( class_exists( 'Tribe__Events__Main' ) ) {

		$elements = array(
			'.single-tribe_events #main .sidebar:not( .fusion-sticky-sidebar )',
			'.single-tribe_events #main .fusion-sticky-sidebar .fusion-sidebar-inner-content',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color']       = Fusion_Sanitize::color( Avada()->settings->get( 'ec_sidebar_bg_color' ) );
		$css['global']['.single-tribe_events #main .sidebar']['padding']                       = Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidebar_padding' ) );
		$css['global']['.single-tribe_events #main .sidebar.fusion-sticky-sidebar']['padding'] = '0px';

		$sticky_padding = str_replace( '%', 'vw', Fusion_Sanitize::size( Avada()->settings->get( 'ec_sidebar_padding' ) ) );
		$css['global']['.single-tribe_events #main .sidebar.fusion-sticky-sidebar .fusion-sidebar-inner-content']['padding'] = $sticky_padding;
	}

	$css['global']['body .fusion-content-widget-area .fusion-tabs-widget .tab-hold .tabs li a:hover']['border-bottom'] = '0';

	$elements = array(
		'body .fusion-content-widget-area .fusion-tabs-widget .tab-hold .tabs li.active a',
		'body .fusion-content-widget-area .fusion-tabs-widget .tab-holder .tabs li.active a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom']    = '0';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$css['global']['.fusion-single-sharing-box']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'social_bg_color' ) );
	if ( 'transparent' == Avada()->settings->get( 'social_bg_color' ) || 0 == Fusion_Color::new_color( Avada()->settings->get( 'social_bg_color' ) )->alpha ) {
		$css['global']['.fusion-single-sharing-box']['padding'] = '0';
	}

	// Blog Archive Masonry content positioning.
	$masonry_content_position = Avada()->settings->get( 'blog_grid_column_spacing' ) / 2;
	$masonry_color = Fusion_Color::new_color( Avada()->settings->get( 'timeline_bg_color' ) );
	$masonry_css = $masonry_color->to_css( 'rgba' );
	if ( 0 === $masonry_color->alpha ) {
		$masonry_css = $masonry_color->to_css( 'rgb' );
	}
	$css['global']['.fusion-blog-layout-masonry .fusion-post-content-wrapper']['background-color'] = $masonry_css;

	// Portfolio Archive Masonry content positioning.
	$masonry_content_position = Avada()->settings->get( 'portfolio_archive_column_spacing' ) / 2;
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['bottom'] = '0px';
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['left'] = '0px';
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['right'] = '0px';
	if ( $masonry_content_position ) {
		$margin = ( -1 ) * $masonry_content_position;
		$css['global']['.fusion-portfolio-layout-masonry']['margin'] = $margin . 'px;';
	}
	if ( 'boxed' !== Avada()->settings->get( 'portfolio_archive_text_layout' ) ) {
		$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['padding'] = '20px 0';
	}
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['z-index'] = '1';
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['position'] = 'absolute';
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['margin'] = '0';
	$css['global']['.fusion-portfolio-layout-masonry .fusion-portfolio-content']['background-color'] = $masonry_css;

	$elements = array(
		'.fusion-blog-layout-grid .post .fusion-post-wrapper',
		'.fusion-blog-layout-timeline .post',
		'.fusion-portfolio.fusion-portfolio-boxed .fusion-portfolio-content-wrapper',
		'.products li.product',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'timeline_bg_color' ) );

	$elements = array(
		'.fusion-blog-layout-grid .post .flexslider',
		'.fusion-blog-layout-grid .post .fusion-post-wrapper',
		'.fusion-blog-layout-grid .post .fusion-content-sep',
		'.product .fusion-content-sep',
		'.products li',
		'.product-buttons',
		'.product-buttons-container',
		'.fusion-blog-layout-timeline .fusion-timeline-line',
		'.fusion-blog-timeline-layout .post',
		'.fusion-blog-timeline-layout .post .fusion-content-sep',
		'.fusion-blog-timeline-layout .post .flexslider',
		'.fusion-blog-layout-timeline .post',
		'.fusion-blog-layout-timeline .post .fusion-content-sep',
		'.fusion-portfolio.fusion-portfolio-boxed .fusion-portfolio-content-wrapper',
		'.fusion-portfolio.fusion-portfolio-boxed .fusion-content-sep',
		'.fusion-blog-layout-timeline .post .flexslider',
		'.fusion-blog-layout-timeline .fusion-timeline-date',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'timeline_color' ) );

	if ( 'transparent' == Fusion_Sanitize::color( Avada()->settings->get( 'timeline_color' ) ) || '0' == Fusion_Color::new_color( Avada()->settings->get( 'timeline_color' ) )->alpha ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border'] = 'none';
	}

	$elements = array(
		'.fusion-blog-layout-timeline .fusion-timeline-circle',
		'.fusion-blog-layout-timeline .fusion-timeline-date',
		'.fusion-blog-timeline-layout .fusion-timeline-circle',
		'.fusion-blog-timeline-layout .fusion-timeline-date',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'timeline_color' ) );

	$elements = array(
		'.fusion-timeline-icon',
		'.fusion-timeline-arrow:before',
		'.fusion-blog-timeline-layout .fusion-timeline-icon',
		'.fusion-blog-timeline-layout .fusion-timeline-arrow:before',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'timeline_color' ) );

	$elements = array(
		'div.indicator-hint'
	);
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums li.bbp-header';
		$elements[] = '#bbpress-forums div.bbp-reply-header';
		$elements[] = '#bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a';
		$elements[] = 'div.bbp-template-notice';
		$elements[] = '#bbpress-forums .bbp-search-results .bbp-forum-header';
		$elements[] = '#bbpress-forums .bbp-search-results .bbp-topic-header';

	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = Fusion_Sanitize::color( Avada()->settings->get( 'bbp_forum_header_bg' ) );

	if ( class_exists( 'bbPress' ) ) {
		$elements = array(
			'#bbpress-forums .forum-titles li',
			'span.bbp-admin-links',
			'span.bbp-admin-links a',
			'.bbp-forum-header a.bbp-forum-permalink',
			'.bbp-reply-header a.bbp-reply-permalink',
			'.bbp-topic-header a.bbp-topic-permalink',
			'.bbp-search-results .bbp-forum-header .bbp-forum-title h3',
			'.bbp-search-results .bbp-forum-header .bbp-forum-title a',
			'.bbp-search-results .bbp-forum-header .bbp-forum-post-date',
			'.bbp-search-results .bbp-forum-header .bbp-forum-permalink:before',
			'.bbp-search-results .bbp-reply-header .bbp-topic-title-meta',
			'.bbp-search-results .bbp-reply-header .bbp-topic-title-meta a',
			'.bbp-search-results .bbp-reply-header .bbp-reply-to',
			'.bbp-search-results .bbp-reply-header .bbp-reply-to a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'bbp_forum_header_font_color' ) );

		$css['global']['#bbpress-forums .bbp-replies div.even']['background'] = 'transparent';
	}
	$elements = array( 'div.indicator-hint' );
	if ( class_exists( 'bbPress' ) ) {
		$elements[] = '#bbpress-forums ul.bbp-lead-topic';
		$elements[] = '#bbpress-forums ul.bbp-topics';
		$elements[] = '#bbpress-forums ul.bbp-forums';
		$elements[] = '#bbpress-forums ul.bbp-replies';
		$elements[] = '#bbpress-forums ul.bbp-search-results';
		$elements[] = '#bbpress-forums li.bbp-body ul.forum';
		$elements[] = '#bbpress-forums li.bbp-body ul.topic';
		$elements[] = '#bbpress-forums div.bbp-reply-content';
		$elements[] = '#bbpress-forums div.bbp-reply-header';
		$elements[] = '#bbpress-forums div.bbp-reply-author .bbp-reply-post-date';
		$elements[] = '#bbpress-forums div.bbp-topic-tags a';
		$elements[] = '#bbpress-forums #bbp-single-user-details';
		$elements[] = 'div.bbp-template-notice';
		$elements[] = '.bbp-arrow';
		$elements[] = '#bbpress-forums .bbp-search-results .bbp-forum-content';
		$elements[] = '#bbpress-forums .bbp-search-results .bbp-topic-content';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'bbp_forum_border_color' ) );

	if ( 'Dark' == Avada()->settings->get( 'scheme_type' ) ) {

		$css['global']['.fusion-rollover .price .amount']['color'] = '#333333';
		$css['global']['.error_page .oops']['color'] = '#2F2F30';
		$css['global']['.meta li']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'body_typography', 'color' ) );

		if ( class_exists( 'bbPress' ) ) {
			$elements = array( '.bbp-arrow', '#bbpress-forums .quicktags-toolbar' );
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) );
		}

		$css['global']['#toTop']['background-color'] = '#111111';

		$css['global']['.chzn-container-single .chzn-single']['background-image'] = 'none';
		$css['global']['.chzn-container-single .chzn-single']['box-shadow']       = 'none';

		$elements = array( '.catalog-ordering a', '.order-dropdown > li:after', '.order-dropdown ul li a' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

		$elements = array(
			'.order-dropdown li',
			'.order-dropdown .current-li',
			'.order-dropdown > li:after',
			'.order-dropdown ul li a',
			'.catalog-ordering .order li a',
			'.order-dropdown li',
			'.order-dropdown .current-li',
			'.order-dropdown ul',
			'.order-dropdown ul li a',
			'.catalog-ordering .order li a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) );

		$elements = array(
			'.order-dropdown li:hover',
			'.order-dropdown .current-li:hover',
			'.order-dropdown ul li a:hover',
			'.catalog-ordering .order li a:hover',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = '#29292A';

		if ( class_exists( 'bbPress' ) ) {

			$elements = array(
				'.bbp-topics-front ul.super-sticky',
				'.bbp-topics ul.super-sticky',
				'.bbp-topics ul.sticky',
				'.bbp-forum-content ul.sticky',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = '#3E3E3E';

			$elements = array(
				'.bbp-topics-front ul.super-sticky a',
				'.bbp-topics ul.super-sticky a',
				'.bbp-topics ul.sticky a',
				'.bbp-forum-content ul.sticky a',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = '#FFFFFF';

		}

		$elements = array(
			'.pagination-prev:before',
			'.pagination-next:after',
		);
		if ( class_exists( 'WooCommerce' ) ) {
			$elements[] = '.woocommerce-pagination .prev:before';
			$elements[] = '.woocommerce-pagination .next:after';
		}
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = '#747474';

		$elements = array( '.table-1 table', '.tkt-slctr-tbl-wrap-dv table' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color']   = '#313132';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow']         = '0 1px 3px rgba(0, 0, 0, 0.08), inset 0 0 0 1px rgba(62, 62, 62, 0.5)';

		$elements = array(
			'.table-1 table th',
			'.tkt-slctr-tbl-wrap-dv table th',
			'.table-1 tbody tr:nth-child(2n)',
			'.tkt-slctr-tbl-wrap-dv tbody tr:nth-child(2n)',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = '#212122';

	} // End if().

	if ( Avada()->settings->get( 'blog_grid_column_spacing' ) || '0' === Avada()->settings->get( 'blog_grid_column_spacing' ) ) {

		$css['global']['#posts-container .fusion-blog-layout-grid']['margin'] = '-' . intval( Avada()->settings->get( 'blog_grid_column_spacing' ) / 2 ) . 'px -' . intval( Avada()->settings->get( 'blog_grid_column_spacing' ) / 2 ) . 'px 0 -' . intval( Avada()->settings->get( 'blog_grid_column_spacing' ) / 2 ) . 'px';

		$css['global']['#posts-container .fusion-blog-layout-grid .fusion-post-grid']['padding'] = intval( Avada()->settings->get( 'blog_grid_column_spacing' ) / 2 ) . 'px';

	}

	$css['global']['.quicktags-toolbar input']['background'][]     = 'linear-gradient(to top, ' . Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) ) . ', ' . Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) ) . ' ) #3E3E3E';
	$css['global']['.quicktags-toolbar input']['background-image'] = '-webkit-gradient( linear, left top, left bottom, color-stop(0, ' . Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) ) . '), color-stop(1, ' . Fusion_Sanitize::color( Avada()->settings->get( 'content_bg_color' ) ) . '))';
	$css['global']['.quicktags-toolbar input']['filter']           = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'form_bg_color' ) )->to_css( 'hex' ) . ', endColorstr=' . Fusion_Color::new_color( Avada()->settings->get( 'content_bg_color' ) )->to_css( 'hex' ) . '), progid: DXImageTransform.Microsoft.Alpha(Opacity=0)';
	$css['global']['.quicktags-toolbar input']['border']           = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'form_border_color' ) );
	$css['global']['.quicktags-toolbar input']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'form_text_color' ) );

	$css['global']['.quicktags-toolbar input:hover']['background'] = Fusion_Sanitize::color( Avada()->settings->get( 'form_bg_color' ) );

	if ( ! Avada()->settings->get( 'image_rollover' ) ) {
		$css['global']['.fusion-rollover']['display'] = 'none';

		$css['global']['.fusion-masonry-element-container.fusion-image-wrapper > a']['position'] = 'absolute';
		$css['global']['.fusion-masonry-element-container.fusion-image-wrapper > a']['top'] = '0';
		$css['global']['.fusion-masonry-element-container.fusion-image-wrapper > a']['bottom'] = '0';
		$css['global']['.fusion-masonry-element-container.fusion-image-wrapper > a']['left'] = '0';
		$css['global']['.fusion-masonry-element-container.fusion-image-wrapper > a']['right'] = '0';
	}

	if ( 'left' != Avada()->settings->get( 'image_rollover_direction' ) ) {

		switch ( Avada()->settings->get( 'image_rollover_direction' ) ) {

			case 'fade' :
				$image_rollover_direction_value = 'translateY(0%)';
				$image_rollover_direction_hover_value = '';

				$css['global']['.fusion-image-wrapper .fusion-rollover']['transition'] = 'opacity 0.5s ease-in-out';
				break;
			case 'right' :
				$image_rollover_direction_value       = 'translateX(100%)';
				$image_rollover_direction_hover_value = '';
				break;
			case 'bottom' :
				$image_rollover_direction_value       = 'translateY(100%)';
				$image_rollover_direction_hover_value = 'translateY(0%)';
				break;
			case 'top' :
				$image_rollover_direction_value       = 'translateY(-100%)';
				$image_rollover_direction_hover_value = 'translateY(0%)';
				break;
			case 'center_horiz' :
				$image_rollover_direction_value       = 'scaleX(0)';
				$image_rollover_direction_hover_value = 'scaleX(1)';
				break;
			case 'center_vertical' :
				$image_rollover_direction_value       = 'scaleY(0)';
				$image_rollover_direction_hover_value = 'scaleY(1)';
				break;
			default:
				$image_rollover_direction_value       = 'scaleY(0)';
				$image_rollover_direction_hover_value = 'scaleY(1)';
				break;
		}

		$css['global']['.fusion-image-wrapper .fusion-rollover']['transform'] = $image_rollover_direction_value;

		if ( '' != $image_rollover_direction_hover_value ) {
			$css['global']['.fusion-image-wrapper:hover .fusion-rollover']['transform'] = $image_rollover_direction_hover_value;
		}
	} // End if().

	$css['global']['.ei-slider']['width']  = Fusion_Sanitize::size( Avada()->settings->get( 'tfes_dimensions', 'width' ) );
	$css['global']['.ei-slider']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'tfes_dimensions', 'height' ) );

	$css['global']['.isotope .isotope-item']['transition-property'] = 'top, left, opacity';

	if ( class_exists( 'WooCommerce' ) ) {
		if ( Avada()->settings->get( 'woocommerce_one_page_checkout' ) ) {

			$elements = array(
				'.woocommerce .checkout #customer_details .col-1',
				'.woocommerce .checkout #customer_details .col-2',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-sizing']    = 'border-box';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border']        = '1px solid';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['overflow']      = 'hidden';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding']       = '30px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '30px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'left';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']         = '48%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '4%';

			if ( is_rtl() ) {

				$elements = array(
					'.rtl .woocommerce form.checkout #customer_details .col-1',
					'.rtl .woocommerce form.checkout #customer_details .col-2',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'right';

				$css['global']['.rtl .woocommerce form.checkout #customer_details .col-1']['margin-left']  = '4%';
				$css['global']['.rtl .woocommerce form.checkout #customer_details .col-1']['margin-right'] = 0;

			}

			$css['global']['.woocommerce form.checkout #customer_details div:last-child']['margin-right'] = '0';

			$css['global']['.woocommerce form.checkout .avada-checkout-no-shipping #customer_details .col-1']['width']        = '100%';
			$css['global']['.woocommerce form.checkout .avada-checkout-no-shipping #customer_details .col-1']['margin-right'] = '0';
			$css['global']['.woocommerce form.checkout .avada-checkout-no-shipping #customer_details .col-2']['display']      = 'none';

		} else {

			$elements = array(
				'.woocommerce form.checkout .col-2',
				'.woocommerce form.checkout #order_review_heading',
				'.woocommerce form.checkout #order_review',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		} // End if().
	} // End if().

	if ( Avada()->settings->get( 'page_title_100_width' ) ) {
		$css['global']['.layout-wide-mode .fusion-page-title-row']['max-width'] = '100%';

		if ( Avada()->settings->get( 'header_100_width' ) ) {
			$css['global']['.layout-wide-mode .fusion-page-title-row']['padding-left']  = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );
			$css['global']['.layout-wide-mode .fusion-page-title-row']['padding-right'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'right' ) );
		}
	}

	$elements = array(
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-link',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-gallery',
	);
	if ( ! Avada()->settings->get( 'icon_circle_image_rollover' ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = 'transparent';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']            = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'image_rollover_icon_size' ) ) . ' * 1.5)';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']           = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'image_rollover_icon_size' ) ) . ' * 1.5)';
	} else {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']      = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'image_rollover_icon_size' ) ) . ' * 2.41)';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'image_rollover_icon_size' ) ) . ' * 2.41)';
	}

	$elements = array(
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-link:before',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-gallery:before',
	);
	if ( Avada()->settings->get( 'image_rollover_icon_size' ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'image_rollover_icon_size' ) );
		if ( ! Avada()->settings->get( 'icon_circle_image_rollover' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1.5';
		} else {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '2.41';
		}
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'image_rollover_icon_color' ) );

	/**
	 * Headings.
	 */

	// H1.
	if ( isset( $h1_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h1_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h1_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h1_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h1_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h1_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h1_typography', 'font-style' ) );
		}
	}
	if ( isset( $h1_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['size'] ) ]['font-size']        = Fusion_Sanitize::size( Avada()->settings->get( 'h1_typography', 'font-size' ) );
	}
	if ( isset( $h1_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h1_typography_elements['color'] ) ]['color']           = Fusion_Sanitize::color( Avada()->settings->get( 'h1_typography', 'color' ) );
	}

	// H2.
	if ( isset( $h2_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h2_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h2_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h2_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h2_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h2_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h2_typography', 'font-style' ) );
		}
	}
	if ( isset( $h2_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'h2_typography', 'font-size' ) );
	}
	if ( isset( $h2_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h2_typography_elements['color'] ) ]['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'h2_typography', 'color' ) );
	}

	// H3.
	if ( isset( $h3_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h3_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h3_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h3_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h3_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h3_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h3_typography', 'font-style' ) );
		}
	}
	if ( isset( $h3_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'h3_typography', 'font-size' ) );
	}
	if ( isset( $h3_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h3_typography_elements['color'] ) ]['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'h3_typography', 'color' ) );
	}

	// H4.
	if ( isset( $h4_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h4_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h4_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h4_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h4_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h4_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h4_typography', 'font-style' ) );
		}
	}
	if ( isset( $h4_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'h4_typography', 'font-size' ) );
	}
	if ( isset( $h4_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['color'] ) ]['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'h4_typography', 'color' ) );
	}
	if ( isset( $h4_typography_elements['line-height'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h4_typography_elements['line-height'] ) ]['line-height']         = Fusion_Sanitize::size( Avada()->settings->get( 'h4_typography', 'line-height' ) );
	}

	// H5.
	if ( isset( $h5_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h5_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h5_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h5_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h5_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h5_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h5_typography', 'font-style' ) );
		}
	}
	if ( isset( $h5_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'h5_typography', 'font-size' ) );
	}
	if ( isset( $h5_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h5_typography_elements['color'] ) ]['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'h5_typography', 'color' ) );
	}

	// H6.
	if ( isset( $h6_typography_elements['family'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['family'] ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'h6_typography' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['family'] ) ]['font-weight']    = intval( Avada()->settings->get( 'h6_typography', 'font-weight' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['family'] ) ]['line-height']    = Fusion_Sanitize::size( Avada()->settings->get( 'h6_typography', 'line-height' ) );
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['family'] ) ]['letter-spacing'] = round( Avada()->settings->get( 'h6_typography', 'letter-spacing' ) ) . 'px';

		$font_style = Avada()->settings->get( 'h6_typography', 'font-style' );
		if ( ! empty( $font_style ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'h6_typography', 'font-style' ) );
		}
	}
	if ( isset( $h6_typography_elements['size'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['size'] ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'h6_typography', 'font-size' ) );
	}
	if ( isset( $h6_typography_elements['color'] ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $h6_typography_elements['color'] ) ]['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'h6_typography', 'color' ) );
	}

	$css['global'][ $dynamic_css_helpers->implode( array( 'h1', '.fusion-title-size-one' ) ) ]['margin-top']      = Fusion_Sanitize::size( Avada()->settings->get( 'h1_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h1', '.fusion-title-size-one' ) ) ]['margin-bottom']   = Fusion_Sanitize::size( Avada()->settings->get( 'h1_typography', 'margin-bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h2', '.fusion-title-size-two' ) ) ]['margin-top']      = Fusion_Sanitize::size( Avada()->settings->get( 'h2_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h2', '.fusion-title-size-two' ) ) ]['margin-bottom']   = Fusion_Sanitize::size( Avada()->settings->get( 'h2_typography', 'margin-bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h3', '.fusion-title-size-three' ) ) ]['margin-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'h3_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h3', '.fusion-title-size-three' ) ) ]['margin-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'h3_typography', 'margin-bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h4', '.fusion-title-size-four' ) ) ]['margin-top']     = Fusion_Sanitize::size( Avada()->settings->get( 'h4_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h4', '.fusion-title-size-four' ) ) ]['margin-bottom']  = Fusion_Sanitize::size( Avada()->settings->get( 'h4_typography', 'margin-bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h5', '.fusion-title-size-five' ) ) ]['margin-top']     = Fusion_Sanitize::size( Avada()->settings->get( 'h5_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h5', '.fusion-title-size-five' ) ) ]['margin-bottom']  = Fusion_Sanitize::size( Avada()->settings->get( 'h5_typography', 'margin-bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h6', '.fusion-title-size-six' ) ) ]['margin-top']      = Fusion_Sanitize::size( Avada()->settings->get( 'h6_typography', 'margin-top' ) );
	$css['global'][ $dynamic_css_helpers->implode( array( 'h6', '.fusion-title-size-six' ) ) ]['margin-bottom']   = Fusion_Sanitize::size( Avada()->settings->get( 'h6_typography', 'margin-bottom' ) );

	if ( class_exists( 'WooCommerce' ) ) {
		$css['global']['.woocommerce-Address-title']['margin-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'h3_typography', 'margin-bottom' ) );
	}

	/**
	 * HEADER IS NUMBER 5
	 */

	/**
	 * Header Styles
	 */
	$elements = array(
		'.fusion-header .fusion-logo',
		'#side-header .fusion-logo',
	);

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'logo_margin', 'top' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = Fusion_Sanitize::size( Avada()->settings->get( 'logo_margin', 'right' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'logo_margin', 'bottom' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = Fusion_Sanitize::size( Avada()->settings->get( 'logo_margin', 'left' ) );

	if ( Avada()->settings->get( 'header_shadow' ) ) {

		$elements = array(
			'.fusion-header-shadow:after',
			'body.side-header-left #side-header.header-shadow .side-header-border:after',
			'body.side-header-right #side-header.header-shadow .side-header-border:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content']        = '""';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['z-index']        = '99996';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position']       = 'absolute';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top']            = '0';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']         = '100%';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['pointer-events'] = 'none';

		$css['global']['.fusion-header-shadow:after']['left'] = '0';
		$css['global']['.fusion-header-shadow:after']['width'] = '100%';

		$css['global']['body.side-header-left #side-header.header-shadow .side-header-border:after']['box-shadow'] = '10px 0 15px -5px rgba(0, 0, 0, 0.14)';
		$css['global']['body.side-header-left #side-header.header-shadow .side-header-border:after']['right'] = '0';
		$css['global']['body.side-header-left #side-header.header-shadow .side-header-border:after']['width'] = '25px';

		$css['global']['body.side-header-right #side-header.header-shadow .side-header-border:before']['box-shadow'] = '-10px 0 15px -5px rgba(0, 0, 0, 0.14)';
		$css['global']['body.side-header-right #side-header.header-shadow .side-header-border:before']['left'] = '0';
		$css['global']['body.side-header-right #side-header.header-shadow .side-header-border:before']['width'] = '25px';

		$elements = array(
			'.fusion-header-shadow .fusion-mobile-menu-design-classic',
			'.fusion-header-shadow .fusion-mobile-menu-design-modern',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = '0px 10px 50px -2px rgba(0, 0, 0, 0.14)';

		$elements = array(
			'.fusion-is-sticky:before',
			'.fusion-is-sticky:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

	} // End if().

	$css['global']['.fusion-header-wrapper .fusion-row']['padding-left']  = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );
	$css['global']['.fusion-header-wrapper .fusion-row']['padding-right'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'right' ) );
	$css['global']['.fusion-header-wrapper .fusion-row']['max-width']     = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );

	$elements = array(
		'.fusion-header-v2 .fusion-header',
		'.fusion-header-v3 .fusion-header',
		'.fusion-header-v4 .fusion-header',
		'.fusion-header-v5 .fusion-header',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$css['global']['#side-header .fusion-secondary-menu-search-inner']['border-top-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$css['global']['.fusion-header .fusion-row']['padding-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'top' ) );
	$css['global']['.fusion-header .fusion-row']['padding-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'bottom' ) );

	$css['global']['.fusion-secondary-header']['background-color']    = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_bg_color' ) );
	$css['global']['.fusion-secondary-header']['font-size']           = Fusion_Sanitize::size( Avada()->settings->get( 'snav_font_size' ) );
	$css['global']['.fusion-secondary-header']['color']               = Fusion_Sanitize::color( Avada()->settings->get( 'snav_color' ) );
	$css['global']['.fusion-secondary-header']['border-bottom-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$elements = array(
		'.fusion-secondary-header a',
		'.fusion-secondary-header a:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'snav_color' ) );

	$css['global']['.fusion-header-v2 .fusion-secondary-header']['border-top-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	$css['global']['.fusion-mobile-menu-design-modern .fusion-secondary-header .fusion-alignleft']['border-bottom-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$css['global']['.fusion-header-tagline']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'tagline_font_size' ) );
	$css['global']['.fusion-header-tagline']['color']     = Fusion_Sanitize::color( Avada()->settings->get( 'tagline_font_color' ) );

	$elements = array(
		'.fusion-secondary-main-menu',
		'.fusion-mobile-menu-sep',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$css['global']['#side-header']['width']          = intval( $side_header_width ) . 'px';
	$css['global']['#side-header .side-header-background-image']['width']	= intval( $side_header_width ) . 'px';
	$css['global']['#side-header .side-header-background-color']['width']	= intval( $side_header_width ) . 'px';
	$css['global']['#side-header .side-header-border']['width']	= intval( $side_header_width ) . 'px';

	$css['global']['.side-header-wrapper']['padding-top']    = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'top' ) );
	$css['global']['.side-header-wrapper']['padding-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'bottom' ) );
	$css['global']['#side-header .side-header-border']['border-color']   = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );

	$css['global']['#side-header .side-header-content']['padding-left']  = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );
	$css['global']['#side-header .side-header-content']['padding-right'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );

	$css['global']['#side-header .fusion-main-menu > ul > li > a']['padding-left']               = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );
	$css['global']['#side-header .fusion-main-menu > ul > li > a']['padding-right']              = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'right' ) );
	$css['global']['.side-header-left .fusion-main-menu > ul > li > a > .fusion-caret']['right'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'right' ) );
	$css['global']['.side-header-right .fusion-main-menu > ul > li > a > .fusion-caret']['left'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'left' ) );
	$css['global']['#side-header .fusion-main-menu > ul > li > a']['border-top-color']           = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );
	$css['global']['#side-header .fusion-main-menu > ul > li > a']['border-bottom-color']        = Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) );
	$css['global']['#side-header .fusion-main-menu > ul > li > a']['text-align']                 = esc_attr( Avada()->settings->get( 'menu_text_align' ) );

	if ( class_exists( 'SitePress' ) ) {
		$wpml_text_align = 'center';
		if ( 'left' === esc_attr( Avada()->settings->get( 'menu_text_align' ) ) ) {
			$wpml_text_align = ( is_rtl() ) ? 'flex-end' : 'flex-start';
		} elseif ( 'right' === esc_attr( Avada()->settings->get( 'menu_text_align' ) ) ) {
			$wpml_text_align = ( is_rtl() ) ? 'flex-start' : 'flex-end';
		}
		$css['global']['#side-header .fusion-main-menu .wpml-ls-item > a, #side-header .fusion-main-menu .wpml-ls-item .menu-text']['justify-content'] = $wpml_text_align;
	}

	$elements = array(
		'#side-header .fusion-main-menu > ul > li.current-menu-ancestor > a',
		'#side-header .fusion-main-menu > ul > li.current-menu-item > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']              = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-right-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-left-color']  = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	$css['global']['body.side-header-left #side-header .fusion-main-menu > ul > li > ul']['left'] = intval( $side_header_width - 1 ) . 'px';

	$css['global']['body.side-header-left #side-header .fusion-main-menu .fusion-custom-menu-item-contents']['top']  = '0';
	$css['global']['body.side-header-left #side-header .fusion-main-menu .fusion-custom-menu-item-contents']['left'] = intval( $side_header_width - 1 ) . 'px';

	$css['global']['#side-header .fusion-main-menu .fusion-main-menu-search .fusion-custom-menu-item-contents']['border-top-width'] = '1px';
	$css['global']['#side-header .fusion-main-menu .fusion-main-menu-search .fusion-custom-menu-item-contents']['border-top-style'] = 'solid';

	$elements = array(
		'#side-header .side-header-content-1',
		'#side-header .side-header-content-2',
		'#side-header .fusion-secondary-menu > ul > li > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']     = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'snav_font_size' ) );
	$bar_width = ( 'bar' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) ) ? intval( Avada()->settings->get( 'nav_highlight_border' ) ) . 'px' : '0px';

	if ( 'bar' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) && 'Top' === Avada()->settings->get( 'header_position' ) ) {
		$css['global']['.fusion-main-menu > ul > li > a']['box-sizing'] = 'border-box';
	}

	$elements = array(
		'.side-header-left #side-header .fusion-main-menu > ul > li.current-menu-ancestor > a',
		'.side-header-left #side-header .fusion-main-menu > ul > li.current-menu-item > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-right-width'] = $bar_width;

	$elements = array(
		'.side-header-right #side-header .fusion-main-menu > ul > li.current-menu-ancestor > a',
		'.side-header-right #side-header .fusion-main-menu > ul > li.current-menu-item > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-left-width'] = $bar_width;

	$elements = array(
		'.side-header-right #side-header .fusion-main-menu ul .fusion-dropdown-menu .sub-menu li ul',
		'.side-header-right #side-header .fusion-main-menu ul .fusion-dropdown-menu .sub-menu',
		'.side-header-right #side-header .fusion-main-menu ul .fusion-menu-login-box .sub-menu',
		'.side-header-right #side-header .fusion-main-menu .fusion-menu-cart-items',
		'.side-header-right #side-header .fusion-main-menu .fusion-menu-login-box .fusion-custom-menu-item-contents',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = '-' . intval( Avada()->settings->get( 'dropdown_menu_width' ) ) . 'px';

	$css['global']['.side-header-right #side-header .fusion-main-menu-search .fusion-custom-menu-item-contents']['left'] = '-250px';

	/**
	 * Main Menu Styles
	 */
	// Main Menu Padding.
	$css['global']['.fusion-main-menu > ul > li']['padding-right'] = intval( Avada()->settings->get( 'nav_padding' ) ) . 'px';
	if ( is_rtl() ) {
		$css['global']['.rtl .fusion-main-menu .fusion-last-menu-item']['padding-right'] = intval( Avada()->settings->get( 'nav_padding' ) ) . 'px';
	}

	// Bar highlight style.
	if ( 0 != intval( Avada()->settings->get( 'nav_highlight_border' ) ) && 'bar' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) ) {
		$css['global']['.fusion-main-menu > ul > li > a']['border-top'] = intval( Avada()->settings->get( 'nav_highlight_border' ) ) . 'px solid transparent';
	}

	// Arrow highlight style.
	if ( 'arrow' === Avada()->settings->get( 'menu_highlight_style' ) ) {
		$css['global']['.fusion-main-menu, .fusion-main-menu .fusion-dropdown-menu']['overflow'] = 'visible';

	}

	if ( 'Top' != Avada()->settings->get( 'header_position' ) || 'v6' != Avada()->settings->get( 'header_layout' ) ) {
		$bar_width = ( 'bar' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) ) ? intval( Avada()->settings->get( 'nav_highlight_border' ) ) : 0;
		$css['global']['.fusion-main-menu > ul > li > a']['height'] = intval( Avada()->settings->get( 'nav_height' ) ) . 'px';
		$css['global']['.fusion-main-menu > ul > li > a']['line-height'] = ( intval( Avada()->settings->get( 'nav_height' ) ) - $bar_width ) . 'px';
	}

	$css['global']['.fusion-megamenu-icon img']['max-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );

	// Main menu icon styling.
	$direction_opposite = array(
		'left'   => 'right',
		'right'  => 'left',
		'top'    => 'bottom',
		'bottom' => 'top',
	);
	$icon_spacing_direction = 'padding-' . $direction_opposite[ Avada()->settings->get( 'menu_icon_position' ) ];
	$icon_spacing_value = '0.45em';

	// Line height reset for links which are flex links.
	if ( 'Top' === Avada()->settings->get( 'header_position' ) && Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
		$css['global']['.fusion-header-wrapper:not(.fusion-is-sticky) .fusion-main-menu > ul > li > a.fusion-flex-link']['line-height'] = '1 !important';
		$css['global']['.fusion-is-sticky .fusion-main-menu>ul>li>a.fusion-flex-link']['display'] = 'block';
	} else {
		$css['global']['.fusion-header-wrapper .fusion-main-menu > ul > li > a.fusion-flex-link']['line-height'] = '1 !important';
	}

	if ( 'top' === Avada()->settings->get( 'menu_icon_position' ) || 'bottom' === Avada()->settings->get( 'menu_icon_position' ) ) {
		$icon_spacing_value = '0.35em';
		if ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
			$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a .fusion-megamenu-icon']['display'] = 'none';
		} else {
			$css['global']['.fusion-header-wrapper .fusion-main-menu > ul > li > a.fusion-flex-link']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) ) . '!important';
		}
	} elseif ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
		// Resize side icons for shrinking enabled.
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a > .fusion-megamenu-icon']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_sticky_nav_font_size' ) );
	}

	if ( 'Top' === Avada()->settings->get( 'header_position' ) || 'center' === Avada()->settings->get( 'menu_text_align' ) ) {
		$css['global']['.fusion-main-menu a.fusion-flex-link']['justify-content'] = 'center';
		$css['global']['.fusion-main-menu a.fusion-flex-link']['-webkit-justify-content'] = 'center';
		$css['global']['.fusion-main-menu a.fusion-flex-link']['-ms-flex-pack'] = 'center';
	}
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-icon']['width'] = 'auto';
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-icon'][ $icon_spacing_direction ] = $icon_spacing_value;
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-icon']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_icon_size' ) ) . 'px';
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-icon']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_icon_color' ) );

	$elements = array(
		'.fusion-main-menu > ul > li > a:hover > .fusion-megamenu-icon',
		'.fusion-main-menu .current_page_item > a > .fusion-megamenu-icon',
		'.fusion-main-menu .current-menu-item > a > .fusion-megamenu-icon',
		'.fusion-main-menu .current-menu-item > a > .fusion-megamenu-icon',
		'.fusion-main-menu .current-menu-parent > a > .fusion-megamenu-icon',
		'.fusion-main-menu .current-menu-ancestor > a > .fusion-megamenu-icon',
		'.fusion-is-sticky .fusion-main-menu > ul > li > a:hover > .fusion-megamenu-icon',
		'.fusion-is-sticky .fusion-main-menu .current_page_item > a > .fusion-megamenu-icon',
		'.fusion-is-sticky .fusion-main-menu .current-menu-item > a > .fusion-megamenu-icon',
		'.fusion-is-sticky .fusion-main-menu .current-menu-parent > a > .fusion-megamenu-icon',
		'.fusion-is-sticky .fusion-main-menu .current-menu-ancestor > a > .fusion-megamenu-icon',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_icon_hover_color' ) );

	// If sideheader and top/bottom, we need to adjust line height.
	if ( 'Top' !== Avada()->settings->get( 'header_position' ) && ( 'top' === Avada()->settings->get( 'menu_icon_position' ) || 'bottom' === Avada()->settings->get( 'menu_icon_position' ) ) ) {
		$menu_height = Fusion_Sanitize::size( Avada()->settings->get( 'nav_height' ) ) . 'px';
		$menu_font_size = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );
		$css['global']['.fusion-main-menu .fusion-flex-link']['line-height'] = '1';
		$css['global']['.fusion-main-menu .fusion-flex-link']['padding'] = 'calc( ( ' . $menu_height . ' - ' . $menu_font_size . ') / 2 ) 0';
	}

	// Side header icon alignment.
	if ( 'Top' !== Avada()->settings->get( 'header_position' ) ) {
		$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['line-height'] = '1';
		if ( 'right' === Avada()->settings->get( 'menu_text_align' ) ) {
			if ( is_rtl() ) {
				$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['justify-content'] = 'flex-start';
			} else {
				$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['justify-content'] = 'flex-end';
			}
			if ( 'left' !== Avada()->settings->get( 'menu_icon_position' ) && 'right' !== Avada()->settings->get( 'menu_icon_position' ) ) {
				if ( ! is_rtl() ) {
					$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['align-items'] = 'flex-end';
				} else {
					$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['align-items'] = 'flex-start';
				}
			}
		}
		if ( 'left' === Avada()->settings->get( 'menu_text_align' ) ) {
			if ( is_rtl() ) {
				$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['justify-content'] = 'flex-end';
			} else {
				$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['justify-content'] = 'flex-start';
			}
			if ( 'left' !== Avada()->settings->get( 'menu_icon_position' ) && 'right' !== Avada()->settings->get( 'menu_icon_position' ) ) {
				if ( ! is_rtl() ) {
					$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['align-items'] = 'flex-start';
				} else {
					$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link']['align-items'] = 'flex-end';
				}
			}
		}
	}

	// Re-size mega menu thumbnail image.
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-image > img']['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_thumbnail_size', 'width' ) );
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-image > img']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_thumbnail_size', 'height' ) );
	$css['global']['.fusion-main-menu > ul > li > a > .fusion-megamenu-image > img']['max-height'] = 'none';
	$elements = array(
		'.fusion-main-menu > ul > li > a',
		'.fusion-main-menu .fusion-widget-cart-counter > a:before',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	// Main menu description.
	if ( 'v4' === Avada()->settings->get( 'header_layout' ) || 'v5' === Avada()->settings->get( 'header_layout' ) ) {
		$css['global']['.fusion-header-wrapper:not(.fusion-is-sticky) .fusion-main-menu > ul > li > a.fusion-flex-link.fusion-has-description']['line-height'] = '1';
	} else {
		$css['global']['.fusion-main-menu > ul > li > a.fusion-flex-link.fusion-has-description']['line-height'] = '1';
	}
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['display'] = 'block';
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['margin-top'] = '0.6em';
	$menu_font_size = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );
	$unit = preg_replace( '/\d+/u', '', $menu_font_size );
	$description_font_size = ( intval( $menu_font_size ) * 0.8 ) . $unit;
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['font-size'] = $description_font_size ;
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['font-family'] = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['font-weight'] = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['letter-spacing'] = round( Avada()->settings->get( 'body_typography', 'letter-spacing' ) ) . 'px';

	$text_color = Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) ) )->get_new( 'alpha', '0.65' )->to_css( 'rgba' );

	$css['global']['.fusion-main-menu > ul > li > a .fusion-menu-description']['color'] = $text_color;

	$hover_color = Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) ) )->get_new( 'alpha', '0.65' )->to_css( 'rgba' );
	$elements = array(
		'.fusion-main-menu > ul > li > a:hover .fusion-menu-description',
		'.fusion-body .fusion-main-menu .current_page_item > a .fusion-menu-description',
		'.fusion-body .fusion-main-menu .current-menu-item > a .fusion-menu-description',
		'.fusion-body .fusion-main-menu .current-menu-parent > a .fusion-menu-description',
		'.fusion-body .fusion-main-menu .current-menu-ancestor > a .fusion-menu-description',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $hover_color;

	// Hide description on sticky header and mobile menu.
	if ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a .fusion-menu-description']['display'] = 'none';
	}
	$css['global']['.fusion-mobile-nav-holder .fusion-menu-description']['display'] = 'none !important';

	$font_style = Avada()->settings->get( 'body_typography', 'font-style' );
	if ( ! empty( $font_style ) ) {
		$css['global'][ $dynamic_css_helpers->implode( $body_typography_elements['family'] ) ]['font-style'] = esc_attr( Avada()->settings->get( 'body_typography', 'font-style' ) );
	}

	$elements = array(
		'.fusion-main-menu > ul > li > a:hover',
		'.fusion-main-menu .fusion-widget-cart-counter > a:hover:before',
		'.fusion-is-sticky .fusion-main-menu > ul > li > a:hover',
		'.fusion-is-sticky .fusion-main-menu .fusion-widget-cart-counter > a:hover:before',
		'.fusion-is-sticky .fusion-main-menu > ul > li.current-menu-item > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global']['.fusion-main-menu > ul > li > a:hover']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global']['.fusion-main-menu > ul > .fusion-menu-item-button > a:hover']['border-color'] = 'transparent';
	$css['global']['.fusion-widget-cart-number']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global']['.fusion-widget-cart-counter a:hover:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global']['.fusion-widget-cart-number']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$css['global']['#side-header .fusion-main-menu > ul > li > a']['height'] = 'auto';
	$css['global']['#side-header .fusion-main-menu > ul > li > a']['min-height'] = intval( Avada()->settings->get( 'nav_height' ) ) . 'px';

	$css['global']['.ua-ie-11 #side-header .fusion-main-menu > ul > li > .fusion-flex-link']['height'] = intval( Avada()->settings->get( 'nav_height' ) ) . 'px';
	$css['global']['.ua-ie-11 #side-header .fusion-main-menu > ul > li > .fusion-flex-link']['box-sizing'] = 'content-box';

	$elements = array(
		'.fusion-body .fusion-main-menu .current_page_item > a',
		'.fusion-body .fusion-main-menu .current-menu-item > a',
		'.fusion-body .fusion-main-menu .current-menu-item:not(.fusion-main-menu-cart) > a:before',
		'.fusion-body .fusion-main-menu .current-menu-item > a',
		'.fusion-body .fusion-main-menu .current-menu-parent > a',
		'.fusion-body .fusion-main-menu .current-menu-ancestor > a',
	);

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']        = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	// Transparent border on button menu link or cart menu link.
	$elements = array(
		'.fusion-body .fusion-main-menu .current_page_item.fusion-menu-item-button > a',
		'.fusion-body .fusion-main-menu .current-menu-item.fusion-menu-item-button > a',
		'.fusion-body .fusion-main-menu .current-menu-item.fusion-menu-item-button > a',
		'.fusion-body .fusion-main-menu .current_page_item.fusion-menu-cart > a',
		'.fusion-body .fusion-main-menu .current-menu-item.fusion-menu-cart > a',
		'.fusion-body .fusion-main-menu .current-menu-item.fusion-menu-cart > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = 'transparent';

	// Arrow highlight.
	if ( 'arrow' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) ) {

		// Drop down svg.
		$css['global']['.fusion-dropdown-svg']['visibility'] = 'hidden';
		$css['global']['.fusion-dropdown-svg']['opacity'] = '0';
		$css['global']['.fusion-dropdown-svg']['pointer-events'] = 'none';
		$css['global']['.fusion-dropdown-svg']['transition'] = 'opacity .2s ease-in';
		$css['global']['.fusion-dropdown-menu .fusion-button']['position'] = 'static';

		$css['global']['li:hover .fusion-dropdown-svg']['visibility'] = 'visible';
		$css['global']['li:hover .fusion-dropdown-svg']['opacity'] = '1';
		$css['global']['li:hover .fusion-dropdown-svg']['pointer-events'] = 'auto';

		// Hover.
		$css['global']['.fusion-dropdown-svg svg path']['transition'] = 'fill .2s ease-in-out';
		$css['global']['.fusion-dropdown-svg.fusion-svg-active path']['fill'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_bg_hover_color' ) ) . '!important';

		// Mobile.
		$elements = array(
			'.fusion-mobile-nav-holder .fusion-dropdown-svg',
			'.fusion-mobile-nav-holder .fusion-arrow-svg',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$elements = array(
			'.fusion-arrow-highlight .fusion-arrow-svg'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity'] = '0';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = '0px';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['visibility'] = 'hidden';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'absolute';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['overflow'] = 'hidden';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';

		if ( 'Top' === Avada()->settings->get( 'header_position' ) ) {

			// Transition for sticky header color transition v4 and v5 not included.
			if ( 'v4' !== Avada()->settings->get( 'header_layout' ) && 'v5' !== Avada()->settings->get( 'header_layout' ) ) {
				$css['global']['.fusion-arrow-highlight .fusion-arrow-svg svg path']['transition'] = 'fill .25s ease-in-out';
			}

			$css['global']['.fusion-logo-link, .fusion-main-menu > ul']['line-height'] = '1';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateX( -50% );';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = '50%';

			// Sticky Header Shadow.
			$shadow_svg = array(
				'.fusion-sticky-shadow .fusion-arrow-highlight .fusion-arrow-svg svg',
				'.fusion-is-sticky .fusion-sticky-menu-only .fusion-arrow-highlight .fusion-arrow-svg svg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $shadow_svg ) ]['-webkit-filter'] = 'drop-shadow( 0px 1px 3px rgba(0,0,0,.117647) )';
			$css['global'][ $dynamic_css_helpers->implode( $shadow_svg ) ]['filter'] = 'drop-shadow( 0px 1px 3px rgba(0,0,0,.117647) )';

			$shadow_svg = array(
				'.fusion-sticky-shadow .fusion-arrow-highlight .fusion-arrow-svg',
				'.fusion-is-sticky .fusion-sticky-menu-only .fusion-arrow-highlight .fusion-arrow-svg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $shadow_svg ) ]['box-sizing'] = 'content-box';
			$css['global'][ $dynamic_css_helpers->implode( $shadow_svg ) ]['padding-bottom'] = '3px';
			$css['global'][ $dynamic_css_helpers->implode( $shadow_svg ) ]['margin-bottom'] = '-3px';

			// If header border is being use and is not transparent.
			$header_2_3_border = ( 'v2' === Avada()->settings->get( 'header_layout' ) || 'v3' === Avada()->settings->get( 'header_layout' ) );
			$header_4_5_border = ( ( 'v4' === Avada()->settings->get( 'header_layout' ) || 'v5' === Avada()->settings->get( 'header_layout' ) ) && 1 === Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'header_bg_color' ) ) )->alpha );
			if ( ( $header_2_3_border || $header_4_5_border ) && 0 !== Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) ) )->alpha ) {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['bottom'] = '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
				$css['global']['.fusion-arrow-svg svg']['margin-top'] = '-1px';
				$css['global']['.fusion-arrow-svg svg']['display'] = 'block';
			} elseif ( ( $header_2_3_border || $header_4_5_border ) && 0 === Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) ) )->alpha ) {
				$css['global']['.fusion-arrow-svg svg']['margin-top'] = '1px';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['bottom'] = '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );

				if ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
					$css['global']['.fusion-is-sticky .fusion-arrow-svg svg']['margin-top'] = '0px';
				}
			} else {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['bottom'] = '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			}
			if ( Avada()->settings->get( 'header_sticky' ) ) {
				$css['global']['.fusion-is-sticky .fusion-arrow-svg svg path']['fill'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_sticky_bg_color' ) );
				$css['global']['.fusion-is-sticky .fusion-arrow-svg svg path']['stroke'] = 'transparent';
			}

			// Arrow drop-down for top menu.
			$css['global']['.fusion-dropdown-svg']['transform'] = 'translate( -50%, 200% )';
			$css['global']['.fusion-dropdown-svg']['position'] = 'absolute';
			$css['global']['.fusion-dropdown-svg']['left'] = '50%';
			$css['global']['.fusion-dropdown-svg']['bottom'] = '-10px';
			$css['global']['.fusion-dropdown-svg']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['z-index'] = '100';
			$css['global']['.fusion-dropdown-svg']['-webkit-filter'] = 'drop-shadow( 0px -2px 1px rgba( 0, 0, 0, 0.03 ) );';
			$css['global']['.fusion-dropdown-svg']['filter'] = 'drop-shadow( 0px -2px 1px rgba( 0, 0, 0, 0.03 ));';

			$transparent_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) ) ) * 2 + 10 ) . 'px';

			// Top level spacer.
			$elements = array(
				'.fusion-main-menu > ul > .menu-item-has-children:hover:before',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'absolute';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['bottom'] = '-' . $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = 'transparent';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';

			// Depper level spacer.
			$elements = array(
				'.fusion-main-menu > ul .sub-menu .menu-item-has-children:hover:before',
				'.fusion-main-menu > ul .sub-menu .menu-item-has-children:hover:after',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'absolute';
			$css['global']['.fusion-main-menu > ul .sub-menu .menu-item-has-children:hover:before']['right'] = '-5px';
			$css['global']['.fusion-main-menu > ul .sub-menu .menu-item-has-children:hover:after']['left'] = '-5px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = 'transparent';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '5px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '0';

			// Deeper levels.
			$css['global']['.fusion-main-menu > ul > .fusion-dropdown-menu .sub-menu .sub-menu']['top'] = '0px';

			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-left'] = '5px';
			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu.fusion-switched-side']['margin-left'] = '-5px';

			$css['global']['.rtl .fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-right'] = '5px';
			$css['global']['.rtl .fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu.fusion-switched-side']['margin-right'] = '-5px';

			$elements = array(
				'.fusion-main-menu .fusion-dropdown-menu > .sub-menu',
				'.fusion-main-menu .fusion-megamenu-wrapper',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = $transparent_spacing;

			$elements = array(
				'.fusion-main-menu .fusion-dropdown-menu .sub-menu',
				'.fusion-main-menu  .fusion-megamenu-holder',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top-width'] = '0px';

		} elseif ( 'Left' === Avada()->settings->get( 'header_position' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY( -50% )';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['right'] = Fusion_Sanitize::add_css_values( array( '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ), '2px' ) );
			if ( 0 === Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) ) )->alpha ) {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['right'] = Fusion_Sanitize::add_css_values( array( '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ), '1px' ) );
			}
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '50%';
			$css['global']['.fusion-arrow-svg svg']['margin-left'] = '-1px';

			// Arrow drop-down for left header.
			$css['global']['.fusion-dropdown-svg']['transform'] = 'translate( 200%, -50% )';
			$css['global']['.fusion-dropdown-svg']['position'] = 'absolute';
			$css['global']['.fusion-dropdown-svg']['top'] = '50%';
			$css['global']['.fusion-dropdown-svg']['right'] = '-4px';
			$css['global']['.fusion-dropdown-svg']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['z-index'] = '100';

			$transparent_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ) ) * 2 + 5 ) . 'px';
			$elements = array(
				'.fusion-main-menu .menu-item-has-children:hover:before',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'absolute';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['right'] = '-' . $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '0';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = 'transparent';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';

			$elements = array(
				'.fusion-main-menu .fusion-dropdown-menu > .sub-menu',
				'.fusion-main-menu .fusion-megamenu-wrapper',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = $transparent_spacing;

			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-left'] = '5px';
			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-right'] = '5px';

		} elseif ( 'Right' === Avada()->settings->get( 'header_position' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY( -50% )';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '1';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = Fusion_Sanitize::add_css_values( array( '-' . Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ), '2px' ) );
			if ( 0 === Fusion_Color::new_color( Fusion_Sanitize::color( Avada()->settings->get( 'header_border_color' ) ) )->alpha ) {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ), '1px' ) );
			}
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '50%';
			$css['global']['.fusion-arrow-svg svg']['margin-right'] = '-1px';

			// Arrow drop-down for right header.
			$css['global']['.fusion-dropdown-svg']['transform'] = 'translate( -200%, -50% )';
			$css['global']['.fusion-dropdown-svg']['position'] = 'absolute';
			$css['global']['.fusion-dropdown-svg']['top'] = '50%';
			$css['global']['.fusion-dropdown-svg']['left'] = '-5px';
			$css['global']['.fusion-dropdown-svg']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'height' ) );
			$css['global']['.fusion-dropdown-svg']['z-index'] = '100';

			$transparent_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ) ) * 2 + 5 ) . 'px';
			$megamanu_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'menu_arrow_size', 'width' ) ) ) * 2 + 4 ) . 'px';
			$elements = array(
				'.fusion-main-menu .menu-item-has-children:hover:before',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'absolute';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = '-' . $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = $transparent_spacing;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '0';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = 'transparent';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';

			$css['global']['.fusion-main-menu .fusion-dropdown-menu > .sub-menu']['margin-left'] = '-' . $transparent_spacing;
			$css['global']['.fusion-main-menu .fusion-megamenu-wrapper']['margin-left'] = '-' . $transparent_spacing;
			$css['global']['.rtl .fusion-main-menu .fusion-megamenu-wrapper']['margin-left'] = '-' . $megamanu_spacing;
			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-left'] = '-5px';
			$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu .sub-menu']['margin-right'] = '-5px';
		} // End if().

		$elements = array(
			'.fusion-main-menu > ul > li > a.fusion-arrow-highlight:hover .fusion-arrow-svg',
			'.fusion-main-menu > ul > li:hover > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-main-menu .current_page_item > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-main-menu .current-menu-item > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-main-menu .current-menu-parent > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-main-menu .current-menu-ancestor > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-is-sticky .fusion-main-menu > ul > li > a.fusion-arrow-highlight:hover .fusion-arrow-svg',
			'.fusion-is-sticky .fusion-main-menu .current_page_item > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-is-sticky .fusion-main-menu .current-menu-item > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-is-sticky .fusion-main-menu .current-menu-parent > a.fusion-arrow-highlight .fusion-arrow-svg',
			'.fusion-is-sticky .fusion-main-menu .current-menu-ancestor > a.fusion-arrow-highlight .fusion-arrow-svg',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity'] = '1';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['visibility'] = 'visible';
		$css['global']['.fusion-main-menu > ul > li > a.fusion-arrow-highlight']['overflow'] = 'visible !important';
		if ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
			$css['global']['.fusion-header']['overflow'] = 'visible !important';
		}
	} // End if().

	// Background hover.
	if ( 'background' === Avada()->settings->get( 'menu_highlight_style' ) && 'v6' !== Avada()->settings->get( 'header_layout' ) ) {
		$elements = array(
			'.fusion-main-menu > ul > li:not(.fusion-middle-logo-menu-logo) > a:hover',
			'.fusion-main-menu > ul > li:not(.fusion-middle-logo-menu-logo):hover > a',
			'.fusion-main-menu > ul > li.current_page_item > a',
			'.fusion-main-menu > ul > li.current-menu-item > a',
			'.fusion-main-menu > ul > li.current-menu-parent > a',
			'.fusion-main-menu > ul > li.current-menu-ancestor > a',
			'.fusion-is-sticky .fusion-main-menu > ul > li:not(.fusion-middle-logo-menu-logo) > a:hover',
			'.fusion-is-sticky .fusion-main-menu > ul > li.current_page_item > a',
			'.fusion-is-sticky .fusion-main-menu > ul > li.current-menu-item > a',
			'.fusion-is-sticky .fusion-main-menu > ul > li.current-menu-parent > a',
			'.fusion-is-sticky .fusion-main-menu > ul > li.current-menu-ancestor > a',
		);

		if ( 'Top' === Avada()->settings->get( 'header_position' ) ) {
			$css['global']['.fusion-logo-link, .fusion-main-menu > ul']['line-height'] = '1';
		}

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color']  = Fusion_Sanitize::color( Avada()->settings->get( 'menu_highlight_background' ) );

		$css['global']['.fusion-body .fusion-main-menu > ul.fusion-menu > li.fusion-menu-item-button > a']['background-color'] = 'transparent';

		$css['global']['.fusion-main-menu > ul > li, .fusion-is-sticky .fusion-main-menu > ul > li']['padding-right'] = '0px';

		$half_menu_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'nav_padding' ) ) ) / 2 ) . 'px';
		$css['global']['.fusion-main-menu > ul > li > a']['padding-left'] = $half_menu_spacing;
		$css['global']['.fusion-main-menu > ul > li > a']['padding-right'] = $half_menu_spacing;

		$half_menu_spacing = ( intval( Fusion_Sanitize::size( Avada()->settings->get( 'header_sticky_nav_padding' ) ) ) / 2 ) . 'px';
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a']['padding-left'] = $half_menu_spacing;
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a']['padding-right'] = $half_menu_spacing;
		$css['global']['#side-header .fusion-main-menu > ul > li']['border-right'] = '1px solid transparent';
	}

	$css['global']['.fusion-main-menu > ul > .fusion-menu-item-button > a']['border-color'] = 'transparent';

	$elements = array(
		'.fusion-main-menu .fusion-main-menu-icon:after',
		'.fusion-main-menu .fusion-widget-cart-counter > a:before',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']  = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$elements = array(
		'.fusion-main-menu .fusion-menu-cart-link a:hover',
		'.fusion-main-menu .fusion-menu-cart-checkout-link a:hover',
		'.fusion-main-menu .fusion-menu-cart-link a:hover:before',
		'.fusion-main-menu .fusion-menu-cart-checkout-link a:hover:before',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']  = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	$elements = array(
		'.fusion-main-menu .fusion-main-menu-icon:after',
		'.fusion-main-menu .fusion-widget-cart-counter > a:before',
		'.fusion-main-menu .fusion-widget-cart-counter > a .fusion-widget-cart-number',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']  = Fusion_Sanitize::size( Avada()->settings->get( 'nav_font_size' ) );

	if ( Avada()->settings->get( 'main_nav_icon_circle' ) ) {
		$elements = array(
			'.fusion-main-menu .fusion-main-menu-icon:after',
			'.fusion-main-menu .fusion-widget-cart-counter > a:before',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border']  = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

		preg_match_all( '!\d+!', Avada()->settings->get( 'nav_font_size' ), $matches );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = $matches[0][0] * 0.35 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'nav_font_size' ) );
	}

	$css['global']['.fusion-body .fusion-main-menu .fusion-main-menu-icon']['border-color'] = 'transparent';

	$elements = array(
		'.fusion-main-menu .fusion-main-menu-icon:hover:after',
		'.fusion-is-sticky .fusion-main-menu .fusion-main-menu-icon:hover:after',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	if ( Avada()->settings->get( 'main_nav_icon_circle' ) ) {
		$css['global']['.fusion-main-menu .fusion-main-menu-icon:hover:after']['border'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
		$css['global']['.fusion-main-menu .fusion-widget-cart-counter > a:hover:before']['border'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	}

	$elements = array(
		'.fusion-main-menu .fusion-main-menu-search-open .fusion-main-menu-icon:after',
		'.fusion-main-menu .fusion-main-menu-icon-active:after',
		'.woocommerce-cart .fusion-main-menu-cart .fusion-main-menu-icon:after',
		'.woocommerce-cart .fusion-main-menu-cart .fusion-main-menu-icon:before',
		'.fusion-body .fusion-main-menu .fusion-widget-cart-counter .fusion-main-menu-icon-active:before',
		'.fusion-is-sticky .fusion-main-menu .fusion-menu .fusion-main-menu-icon-active:after',
		'.fusion-is-sticky .fusion-main-menu .fusion-main-menu-search-open .fusion-main-menu-icon:after',
		'.fusion-is-sticky .fusion-main-menu .fusion-main-menu-icon-active:after',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	if ( Avada()->settings->get( 'main_nav_icon_circle' ) ) {
		$elements = array(
			'.fusion-main-menu .fusion-main-menu-search-open .fusion-main-menu-icon:after',
			'.fusion-main-menu .fusion-main-menu-icon-active:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
	}

	$css['global']['.fusion-main-menu .sub-menu']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_bg_color' ) );
	$css['global']['.fusion-main-menu .sub-menu']['width']            = intval( Avada()->settings->get( 'dropdown_menu_width' ) ) . 'px';

	if ( 'bar' === Avada()->settings->get( 'menu_highlight_style' ) ) {
		$css['global']['.fusion-main-menu .sub-menu']['border-top'] = '3px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );
		$css['global']['.fusion-main-menu .sub-menu ul']['top']  = '-3px';
	}
	$css['global']['.fusion-main-menu .sub-menu']['font-family']      = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global']['.fusion-main-menu .sub-menu']['font-weight']      = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
	if ( Avada()->settings->get( 'megamenu_shadow' ) ) {
		$css['global']['.fusion-main-menu .sub-menu']['box-shadow']   = '1px 1px 30px rgba(0, 0, 0, 0.06)';
	}

	$css['global']['.fusion-main-menu .sub-menu ul']['left'] = intval( Avada()->settings->get( 'dropdown_menu_width' ) ) . 'px';

	if ( Avada()->settings->get( 'mainmenu_dropdown_display_divider' ) ) {
		$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu li a']['border-bottom'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );
	} else {
		$css['global']['.fusion-main-menu .fusion-dropdown-menu .sub-menu li a']['border-bottom'] = 'none';
	}
	$elements = array(
		'.fusion-main-menu .sub-menu li a',
		'.fusion-main-menu .fusion-dropdown-menu .sub-menu li a',
		'.fusion-megamenu-wrapper li .fusion-megamenu-title-disabled',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = intval( Avada()->settings->get( 'mainmenu_dropdown_vertical_padding' ) ) . 'px';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = intval( Avada()->settings->get( 'mainmenu_dropdown_vertical_padding' ) ) . 'px';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']          = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-weight']    = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']      = Fusion_Sanitize::size( Avada()->settings->get( 'nav_dropdown_font_size' ) );

	$css['global']['.fusion-main-menu .fusion-main-menu-cart']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'nav_dropdown_font_size' ) );

	$css['global']['.fusion-main-menu .sub-menu li a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_bg_hover_color' ) );

	if ( class_exists( 'WooCommerce' ) ) {
		$css['global']['.fusion-main-menu .fusion-menu-login-box-register']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );
	}

	$elements = array(
		'.fusion-main-menu .sub-menu .current_page_item > a',
		'.fusion-main-menu .sub-menu .current-menu-item > a',
		'.fusion-main-menu .sub-menu .current-menu-parent > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_bg_hover_color' ) );

	$css['global']['.fusion-main-menu .fusion-custom-menu-item-contents']['font-family'] = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global']['.fusion-main-menu .fusion-custom-menu-item-contents']['font-weight'] = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );

	$elements = array(
		'.fusion-main-menu .fusion-main-menu-search .fusion-custom-menu-item-contents',
		'.fusion-main-menu .fusion-main-menu-cart .fusion-custom-menu-item-contents',
		'.fusion-main-menu .fusion-menu-login-box .fusion-custom-menu-item-contents',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_bg_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );

	if ( 'transparent' == Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) ) || 0 == Fusion_Color::new_color( Avada()->settings->get( 'menu_sub_sep_color' ) )->alpha ) {
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border'] = '0';
	}

	if ( is_rtl() ) {
		$elements = array(
			'.rtl .fusion-header-v1 .fusion-main-menu > ul > li',
			'.rtl .fusion-header-v2 .fusion-main-menu > ul > li',
			'.rtl .fusion-header-v3 .fusion-main-menu > ul > li',
			'.rtl .fusion-header-v7 .fusion-main-menu > ul > li',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0';
		if ( 0 != Avada()->settings->get( 'nav_padding' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left'] = intval( Avada()->settings->get( 'nav_padding' ) ) . 'px';
		}

		$css['global']['.rtl.fusion-top-header .fusion-main-menu .sub-menu ul']['left']  = 'auto';
		$css['global']['.rtl.fusion-top-header .fusion-main-menu .sub-menu ul']['right'] = intval( Avada()->settings->get( 'dropdown_menu_width' ) ) . 'px';

	}

	/**
	 * Flyout Menu Styles
	 */
	$css['global']['.fusion-header-v6 .fusion-header-v6-content .fusion-flyout-menu-icons']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-header-v6-content .fusion-widget-cart-number']['min-width']  = Fusion_Sanitize::size( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );

	$icon_font_size = Fusion_Sanitize::number( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );

	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-menu-toggle']['height'] = $icon_font_size * 0.9 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-menu-toggle']['width'] = $icon_font_size * 1.5 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-search-toggle .fusion-toggle-icon']['height'] = $icon_font_size * 0.9 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-search-toggle .fusion-toggle-icon']['width'] = $icon_font_size * 0.9 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-toggle-icon-line']['height'] = round( $icon_font_size * 0.1 ) . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-body .fusion-header-v6.fusion-flyout-search-active .fusion-flyout-menu-icons .fusion-flyout-search-toggle .fusion-toggle-icon-line']['height'] = $icon_font_size * 0.1 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-toggle-icon-line']['width'] = $icon_font_size * 1.5 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );

	$css['global']['.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu-icons .fusion-flyout-menu-toggle .fusion-toggle-icon-line']['width'] = $icon_font_size * 0.9 / 0.75 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );
	$css['global']['.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-menu-icons .fusion-flyout-search-toggle .fusion-toggle-icon-line']['width'] = $icon_font_size * 0.9 / 0.75 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'flyout_menu_icon_font_size' ) );

	$elements = array(
		'.fusion-header-v6 .fusion-header-v6-content .fusion-flyout-menu-icons .fusion-flyout-cart-wrapper',
		'.fusion-header-v6 .fusion-header-v6-content .fusion-flyout-menu-icons .fusion-flyout-search-toggle',
		'.fusion-header-v6 .fusion-header-v6-content .fusion-flyout-menu-icons .fusion-flyout-menu-toggle',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = sprintf( '0 %spx',  round( Avada()->settings->get( 'nav_padding' ) / 2 ) );

	$css['global']['.fusion-header-v6 .fusion-header-v6-content .fusion-flyout-menu-icons']['margin'] = sprintf( '0 -%spx',  Avada()->settings->get( 'nav_padding' ) / 2 );

	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-icon:before']['color'] = Avada()->settings->get( 'flyout_menu_icon_color' );
	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-icon:hover:before']['color'] = Avada()->settings->get( 'flyout_menu_icon_hover_color' );

	if ( Avada()->settings->get( 'main_nav_icon_circle' ) ) {

		$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-icon:before']['border']  = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'flyout_menu_icon_color' ) );
		$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-icon:hover:before']['border']  = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'flyout_menu_icon_hover_color' ) );
		$css['global']['.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-icon:before']['border']  = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
		$css['global']['.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-icon:hover:before']['border']  = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

		$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-icon:before']['padding'] = $icon_font_size * 0.35 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'nav_font_size' ) );
	}

	$css['global']['.fusion-header-v6 .fusion-flyout-menu-icons .fusion-toggle-icon-line']['background-color'] = Avada()->settings->get( 'flyout_menu_icon_color' );

	$elements = array(
		'.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-menu-toggle:hover .fusion-toggle-icon-line',
		'.fusion-header-v6 .fusion-flyout-menu-icons .fusion-flyout-search-toggle:hover .fusion-toggle-icon-line',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Avada()->settings->get( 'flyout_menu_icon_hover_color' );

	$css['global']['.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-icon:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-icon:hover:before']['color'] = Avada()->settings->get( 'menu_hover_first_color' );

	$css['global']['.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-toggle-icon-line']['background-color'] = Avada()->settings->get( 'menu_first_color' );

	$elements = array(
		'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-flyout-menu-toggle:hover .fusion-toggle-icon-line',
		'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-icons .fusion-flyout-search-toggle:hover .fusion-toggle-icon-line',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Avada()->settings->get( 'menu_hover_first_color' );

	$css['global']['.fusion-header-v6 .fusion-flyout-menu-bg']['background-color'] = Avada()->settings->get( 'flyout_menu_background_color' );

	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s']['font-family'] = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'nav_typography' ) );

	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s::-webkit-input-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s::-moz-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s:-moz-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );
	$css['global']['#wrapper .fusion-header-v6 .fusion-flyout-search .searchform .s:-ms-input-placeholder']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$css['global']['.fusion-header-v6 .fusion-flyout-menu .fusion-menu li']['padding'] = Fusion_Sanitize::number( Avada()->settings->get( 'nav_font_size' ) ) . Fusion_Sanitize::get_unit( Avada()->settings->get( 'nav_font_size' ) ) . ' 0';

	switch ( Avada()->settings->get( 'flyout_menu_direction' ) ) {

		case 'fade' :
			$elements = array(
				'.fusion-header-v6 .fusion-flyout-menu',
				'.fusion-header-v6 .fusion-flyout-search',
				'.fusion-header-v6 .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '-1000%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transition'] = 'opacity 0.4s ease 0s, top 0s ease 0.4s';

			$elements = array(
				'.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu',
				'.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-search',
				'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '0';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transition'] = 'opacity 0.4s ease 0s, top 0s ease 0s';

			break;
		case 'left' :
			$elements = array(
				'.fusion-header-v6 .fusion-flyout-menu',
				'.fusion-header-v6 .fusion-flyout-search',
				'.fusion-header-v6 .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateX(-100%)';

			$elements = array(
				'.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu',
				'.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-search',
				'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateX(0%)';
			break;

		case 'right' :
			$elements = array(
				'.fusion-header-v6 .fusion-flyout-menu',
				'.fusion-header-v6 .fusion-flyout-search',
				'.fusion-header-v6 .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateX(100%)';

			$elements = array(
				'.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu',
				'.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-search',
				'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateX(0%)';
			break;

		case 'bottom' :
			$elements = array(
				'.fusion-header-v6 .fusion-flyout-menu',
				'.fusion-header-v6 .fusion-flyout-search',
				'.fusion-header-v6 .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY(100%)';

			$elements = array(
				'.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu',
				'.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-search',
				'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY(0%)';
			break;
		case 'top' :
			$elements = array(
				'.fusion-header-v6 .fusion-flyout-menu',
				'.fusion-header-v6 .fusion-flyout-search',
				'.fusion-header-v6 .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY(-100%)';

			$elements = array(
				'.fusion-header-v6.fusion-flyout-menu-active .fusion-flyout-menu',
				'.fusion-header-v6.fusion-flyout-search-active .fusion-flyout-search',
				'.fusion-header-v6.fusion-flyout-active .fusion-flyout-menu-bg',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transform'] = 'translateY(0%)';
			break;
	} // End switch().

	/**
	 * Secondary Menu Styles
	 */

	$css['global']['.fusion-secondary-menu > ul > li']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_first_border_color' ) );

	if ( 0 != Fusion_Sanitize::number( Avada()->settings->get( 'sec_menu_lh' ) ) ) {
		$css['global']['.fusion-secondary-menu > ul > li > a']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'sec_menu_lh' ) );
	}

	$css['global']['.fusion-secondary-menu .sub-menu, .fusion-secondary-menu .fusion-custom-menu-item-contents']['width'] = intval( Avada()->settings->get( 'topmenu_dropwdown_width' ) ) . 'px';
	$css['global']['.fusion-secondary-menu .fusion-secondary-menu-icon']['min-width'] = intval( Avada()->settings->get( 'topmenu_dropwdown_width' ) ) . 'px';
	$css['global']['.fusion-secondary-menu .sub-menu']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_sub_bg_color' ) );
	$css['global']['.fusion-secondary-menu .sub-menu']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_sep_color' ) );

	$css['global']['.fusion-secondary-menu .sub-menu a']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_sep_color' ) );
	$css['global']['.fusion-secondary-menu .sub-menu a']['color']        = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_color' ) );

	$css['global']['.fusion-secondary-menu .sub-menu a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_bg_hover_color' ) );
	$css['global']['.fusion-secondary-menu .sub-menu a:hover']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_hover_color' ) );

	$css['global']['.fusion-secondary-menu > ul > li > .sub-menu .sub-menu']['left'] = intval( Avada()->settings->get( 'topmenu_dropwdown_width' ) ) - 2 . 'px';

	$css['global']['.fusion-secondary-menu .fusion-custom-menu-item-contents']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_sub_bg_color' ) );
	$css['global']['.fusion-secondary-menu .fusion-custom-menu-item-contents']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_sep_color' ) );
	$css['global']['.fusion-secondary-menu .fusion-custom-menu-item-contents']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_color' ) );

	$elements = array(
		'.fusion-secondary-menu .fusion-secondary-menu-icon',
		'.fusion-secondary-menu .fusion-secondary-menu-icon:hover',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$css['global']['.fusion-secondary-menu .fusion-menu-cart-items a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_color' ) );

	$css['global']['.fusion-secondary-menu .fusion-menu-cart-item a']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_sep_color' ) );

	$css['global']['.fusion-secondary-menu .fusion-menu-cart-item a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_bg_hover_color' ) );
	$css['global']['.fusion-secondary-menu .fusion-menu-cart-item a:hover']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_hover_color' ) );

	if ( class_exists( 'WooCommerce' ) ) {
		$css['global']['.fusion-secondary-menu .fusion-menu-cart-checkout']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_cart_bg_color' ) );

		$css['global']['.fusion-secondary-menu .fusion-menu-cart-checkout a:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_color' ) );

		$elements = array(
			'.fusion-secondary-menu .fusion-menu-cart-checkout a:hover',
			'.fusion-secondary-menu .fusion-menu-cart-checkout a:hover:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_top_menu_sub_hover_color' ) );
	}

	$css['global']['.fusion-secondary-menu-icon']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_cart_bg_color' ) );
	$css['global']['.fusion-secondary-menu-icon']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$elements = array(
		'.fusion-secondary-menu-icon:before',
		'.fusion-secondary-menu-icon:after',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	if ( is_rtl() ) {
		$css['global']['.rtl .fusion-secondary-menu > ul > li:first-child']['border-left'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'header_top_first_border_color' ) );

		$css['global']['.rtl .fusion-secondary-menu > ul > li > .sub-menu .sub-menu']['left']  = 'auto';
		$css['global']['.rtl .fusion-secondary-menu > ul > li > .sub-menu .sub-menu']['right'] = intval( Avada()->settings->get( 'topmenu_dropwdown_width' ) ) . 'px';
	}

	if ( 0 != Fusion_Sanitize::number( Avada()->settings->get( 'sec_menu_lh' ) ) ) {
		$css['global']['.fusion-contact-info']['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'sec_menu_lh' ) );
	}

	/**
	 * Common Menu Styles
	 */

	if ( class_exists( 'WooCommerce' ) ) {
		$css['global']['.fusion-menu-cart-items']['width']   = intval( Avada()->settings->get( 'dropdown_menu_width' ) ) . 'px';

		$css['global']['.fusion-menu-cart-items']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'woo_icon_font_size' ) );
		$css['global']['.fusion-menu-cart-items']['line-height'] = '1.5';

		$css['global']['.fusion-menu-cart-items a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );

		$css['global']['.fusion-menu-cart-item a']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );

		$css['global']['.fusion-menu-cart-item a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_bg_hover_color' ) );

		$css['global']['.fusion-menu-cart-checkout']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_cart_bg_color' ) );

		$css['global']['.fusion-menu-cart-checkout a:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );

		$elements = array(
			'.fusion-menu-cart-checkout a:hover',
			'.fusion-menu-cart-checkout a:hover:before',
		);
		$elements['global']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
	}

	/**
	 * Megamenu Styles
	 */

	$css['global']['.fusion-megamenu-holder']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_hover_first_color' ) );

	$css['global']['.fusion-megamenu-holder']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_bg_color' ) );
	if ( Avada()->settings->get( 'megamenu_shadow' ) ) {
		$css['global']['.fusion-megamenu-holder']['box-shadow'] = '1px 1px 30px rgba(0, 0, 0, 0.06)';
	}

	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );
	$css['global']['.rtl .fusion-megamenu-wrapper .fusion-megamenu-submenu:last-child']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );

	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu .sub-menu a']['padding-top']    = intval( Avada()->settings->get( 'megamenu_item_vertical_padding' ) ) . 'px';
	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu .sub-menu a']['padding-bottom'] = intval( Avada()->settings->get( 'megamenu_item_vertical_padding' ) ) . 'px';
	if ( Avada()->settings->get( 'megamenu_item_display_divider' ) ) {
		$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu .sub-menu a']['border-bottom'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );
		$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a']['border-bottom'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );
		$css['global']['#side-header .fusion-main-menu > ul .sub-menu > li:last-child > a']['border-bottom'] = '1px solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_sep_color' ) );
		$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu']['padding-bottom'] = '0';
		$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu-notitle']['padding-top'] = '0';
	}

	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_bg_hover_color' ) );
	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );
	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover']['font-family']      = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover']['font-weight']      = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
	$css['global']['.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover']['font-size']        = Fusion_Sanitize::size( Avada()->settings->get( 'nav_dropdown_font_size' ) );

	$css['global']['.fusion-megamenu-title']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'megamenu_title_size' ) );
	$css['global']['.fusion-megamenu-title']['color']     = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$css['global']['.fusion-megamenu-title a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_first_color' ) );

	$css['global']['.fusion-megamenu-bullet']['border-left-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );

	$css['global']['.fusion-megamenu-widgets-container']['color']       = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );
	$css['global']['.fusion-megamenu-widgets-container']['font-family'] = $dynamic_css_helpers->combined_font_family( Avada()->settings->get( 'body_typography' ) );
	$css['global']['.fusion-megamenu-widgets-container']['font-weight'] = intval( Avada()->settings->get( 'body_typography', 'font-weight' ) );
	$css['global']['.fusion-megamenu-widgets-container']['font-size']   = Fusion_Sanitize::size( Avada()->settings->get( 'nav_dropdown_font_size' ) );

	if ( is_rtl() ) {
		$css['global']['.rtl .fusion-megamenu-bullet']['border-right-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_sub_color' ) );
		$css['global']['.rtl .fusion-megamenu-wrapper .fusion-megamenu-submenu .sub-menu ul']['right'] = 'auto';
	}

	/**
	 * Sticky Header Styles
	 */
	$elements = array(
		'.fusion-header-wrapper.fusion-is-sticky .fusion-header',
		'.fusion-header-wrapper.fusion-is-sticky .fusion-secondary-main-menu',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_sticky_bg_color' ) );

	$elements = array(
		'.no-rgba .fusion-header-wrapper.fusion-is-sticky .fusion-header',
		'.no-rgba .fusion-header-wrapper.fusion-is-sticky .fusion-secondary-main-menu',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_sticky_bg_color' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity']          = Fusion_Color::new_color( Avada()->settings->get( 'header_sticky_bg_color' ) )->alpha;
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['filter']           = 'progid: DXImageTransform.Microsoft.Alpha(Opacity=' . ( Fusion_Color::new_color( Avada()->settings->get( 'header_sticky_bg_color' ) )->alpha * 100 ) . ')';
	if ( 'background' !== Avada()->settings->get( 'menu_highlight_style' ) ) {
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li']['padding-right'] = intval( Avada()->settings->get( 'header_sticky_nav_padding' ) ) . 'px';
	}
	$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li:last-child']['padding-right'] = '0';

	$elements = array(
		'.fusion-is-sticky .fusion-main-menu .fusion-main-menu-icon:after',
		'.fusion-is-sticky .fusion-main-menu .fusion-widget-cart-counter > a:before',
		'.fusion-is-sticky .fusion-main-menu > ul > li > a',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_sticky_menu_color' ) );

	if ( 0 != intval( Avada()->settings->get( 'header_sticky_nav_padding' ) ) ) {
		$css['global']['.rtl .fusion-is-sticky .fusion-main-menu > ul > li:last-child']['padding-right'] = intval( Avada()->settings->get( 'header_sticky_nav_padding' ) ) . 'px';
	} else {
		$css['global']['.rtl .fusion-is-sticky .fusion-main-menu > ul > li:last-child']['padding-right'] = intval( Avada()->settings->get( 'nav_padding' ) ) . 'px';
	}

	if ( Avada()->settings->get( 'header_layout' ) != 'v6' ) {
		$css['global']['.fusion-is-sticky .fusion-main-menu > ul > li > a']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_sticky_nav_font_size' ) );
	}

	if ( is_rtl() ) {
		$elements = array(
			'.rtl .fusion-is-sticky .fusion-header-v1 .fusion-main-menu > ul > li',
			'.rtl .fusion-is-sticky .fusion-header-v2 .fusion-main-menu > ul > li',
			'.rtl .fusion-is-sticky .fusion-header-v3 .fusion-main-menu > ul > li',
			'.rtl .fusion-is-sticky .fusion-header-v7 .fusion-main-menu > ul > li',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0';
		$css['global']['.rtl .fusion-is-sticky .fusion-main-menu > ul > li']['padding-left'] = intval( Avada()->settings->get( 'header_sticky_nav_padding' ) ) . 'px';
		$css['global']['.rtl .fusion-is-sticky .fusion-main-menu > ul > li:last-child']['padding-left'] = '0';
	}

	/**
	 * Mobile Menu Styles
	 */

	$css['global']['.fusion-mobile-selector']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_background_color' ) );
	$css['global']['.fusion-mobile-selector']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_border_color' ) );
	$css['global']['.fusion-mobile-selector']['font-size']        = Fusion_Sanitize::size( Avada()->settings->get( 'mobile_menu_font_size' ) );
	$css['global']['.fusion-mobile-selector']['height']           = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';
	$css['global']['.fusion-mobile-selector']['line-height']      = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';
	$css['global']['.fusion-mobile-selector']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_font_color' ) );

	$elements = array(
		'.fusion-body .fusion-mobile-nav-holder .fusion-selector-down',
	);
	if ( is_rtl() ) {
		$elements[] = '.rtl .fusion-mobile-nav-holder .fusion-selector-down';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']       = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) - 2 ) . 'px';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height']  = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) - 2 ) . 'px';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_border_color' ) );

	$elements = array(
		'.fusion-selector-down:before',
	);
	if ( is_rtl() ) {
		$elements[] = '.rtl .fusion-selector-down:before';
	}
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_toggle_color' ) );

	if ( false !== strpos( Avada()->settings->get( 'mobile_menu_font_size' ), 'px' ) && 35 < intval( Avada()->settings->get( 'mobile_menu_font_size' ) ) ) {
		$css['global']['.fusion-selector-down']['font-size'] = '30px';
	}

	$elements = array(
		'.fusion-mobile-nav-holder > ul',
		'.fusion-mobile-menu-design-modern .fusion-mobile-nav-holder > ul',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_border_color' ) );

	$css['global']['.fusion-mobile-nav-item .fusion-open-submenu']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_font_color' ) );
	$css['global']['.fusion-mobile-nav-item a']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_font_color' ) );
	$css['global']['.fusion-mobile-nav-item a']['font-size']        = Fusion_Sanitize::size( Avada()->settings->get( 'mobile_menu_font_size' ) );
	$css['global']['.fusion-mobile-nav-item a']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_background_color' ) );
	$css['global']['.fusion-mobile-nav-item a']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_border_color' ) );
	$css['global']['.fusion-mobile-nav-item a']['height']           = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';
	$css['global']['.fusion-mobile-nav-item a']['line-height']      = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';

	$css['global']['.fusion-mobile-nav-item a:hover']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_hover_color' ) );

	$css['global']['.fusion-mobile-nav-item a:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_font_color' ) );

	$css['global']['.fusion-mobile-current-nav-item > a']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_hover_color' ) );

	$css['global']['.fusion-mobile-menu-icons']['margin-top'] = intval( Avada()->settings->get( 'mobile_menu_icons_top_margin' ) ) . 'px';

	$css['global']['.fusion-mobile-menu-icons a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_toggle_color' ) );

	$css['global']['.fusion-mobile-menu-icons a:before']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_menu_toggle_color' ) );

	$css['global']['.fusion-open-submenu']['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'mobile_menu_font_size' ) );

	$css['global']['.fusion-open-submenu']['height']      = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';
	$css['global']['.fusion-open-submenu']['line-height'] = intval( Avada()->settings->get( 'mobile_menu_nav_height' ) ) . 'px';

	if ( false !== strpos( Avada()->settings->get( 'mobile_menu_font_size' ), 'px' ) && 30 < intval( Avada()->settings->get( 'mobile_menu_font_size' ) ) ) {
		$css['global']['.fusion-open-submenu']['font-size'] = '20px';
	}

	$css['global']['.fusion-open-submenu:hover']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

	/**
	 * Social Links.
	 */
	$social_bg_color_alpha = Fusion_Color::new_color( Avada()->settings->get( 'social_bg_color' ) )->alpha;
	if ( 0 == $social_bg_color_alpha ) {
		$css['global']['.fusion-sharing-box']['padding-left']  = '0';
		$css['global']['.fusion-sharing-box']['padding-right'] = '0';
	}
	$css['global']['.fusion-sharing-box h4']['color']         = Fusion_Sanitize::color( Avada()->settings->get( 'sharing_box_tagline_text_color' ) );

	$css['global']['.fusion-social-links-header .fusion-social-networks a']['font-size']           = Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_font_size' ) );
	$css['global']['.fusion-social-links-header .fusion-social-networks.boxed-icons a']['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_boxed_padding' ) );
	$css['global']['.fusion-social-links-header .fusion-social-networks.boxed-icons a']['width']   = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_font_size' ) ) . ' + (2 * ' . Fusion_Sanitize::size( Avada()->settings->get( 'header_social_links_boxed_padding' ) ) . ') + 2px)';
	$css['global']['.fusion-social-links-footer .fusion-social-networks a']['font-size']           = Fusion_Sanitize::size( Avada()->settings->get( 'footer_social_links_font_size' ) );
	$css['global']['.fusion-social-links-footer .fusion-social-networks.boxed-icons a']['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'footer_social_links_boxed_padding' ) );
	$css['global']['.fusion-social-links-footer .fusion-social-networks.boxed-icons a']['width']   = 'calc(' . Fusion_Sanitize::size( Avada()->settings->get( 'footer_social_links_font_size' ) ) . ' + (2 * ' . Fusion_Sanitize::size( Avada()->settings->get( 'footer_social_links_boxed_padding' ) ) . ') + 2px)';

	/**
	 * Single Post Slideshow.
	 */
	if ( Avada()->settings->get( 'slideshow_smooth_height' ) || ( get_post_meta( 'auto' == $c_page_id, 'pyre_fimg_width', true ) && 'half' == get_post_meta( $c_page_id, 'pyre_width', true ) ) ) {
		$css['global']['.fusion-flexslider.fusion-post-slideshow']['overflow'] = 'hidden';
	}

	if ( class_exists( 'WooCommerce' ) ) {

		/**
		 * Woocommerce - Dynamic Styling.
		 */

		$css['global']['.order-dropdown']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		$css['global']['.order-dropdown > li:after']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_border_color' ) );

		$elements = array(
			'.order-dropdown a',
			'.order-dropdown a:hover',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		$elements = array(
			'.order-dropdown .current-li',
			'.order-dropdown ul li a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_bg_color' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_border_color' ) );

		$css['global']['.order-dropdown ul li a:hover']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		$css['global']['.order-dropdown ul li a:hover']['background-color'] = Fusion_Sanitize::color( fusion_color_luminance( Avada()->settings->get( 'woo_dropdown_bg_color' ), 0.1 ) );

		$css['global']['.catalog-ordering .order li a']['color']            = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );
		$css['global']['.catalog-ordering .order li a']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_bg_color' ) );
		$css['global']['.catalog-ordering .order li a']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_border_color' ) );

		$css['global']['.fusion-grid-list-view']['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_border_color' ) );

		$css['global']['.fusion-grid-list-view li']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_bg_color' ) );
		$css['global']['.fusion-grid-list-view li']['border-color']     = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_border_color' ) );

		$css['global']['.fusion-grid-list-view a']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		$css['global']['.fusion-grid-list-view li:hover']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		$css['global']['.fusion-grid-list-view li:hover']['background-color'] = Fusion_Sanitize::color( fusion_color_luminance( Avada()->settings->get( 'woo_dropdown_bg_color' ), 0.1 ) );

		$css['global']['.fusion-grid-list-view li.active-view']['background-color'] = Fusion_Sanitize::color( fusion_color_luminance( Avada()->settings->get( 'woo_dropdown_bg_color' ), 0.1 ) );

		$css['global']['.fusion-grid-list-view li.active-view a i']['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'woo_dropdown_text_color' ) );

		if ( is_rtl() ) {
			$woo_message_direction = 'right';
		} else {
			$woo_message_direction = 'left';
		}
		$elements = array(
			'.woocommerce-message:before',
			'.woocommerce-info:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ][ 'margin-' . $woo_message_direction ] = Fusion_Sanitize::add_css_values( array( '-' . Fusion_Sanitize::size( Avada()->settings->get( 'body_typography', 'font-size' ) ), '-3px' ) );

		$elements = array(
			'.woocommerce-message',
			'.woocommerce-info',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ][ 'padding-' . $woo_message_direction ] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'body_typography', 'font-size' ) ), '3px' ) );

	} // End if().

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$elements = array(
			'.tribe-grid-allday .tribe-events-week-allday-single, .tribe-grid-allday .tribe-events-week-allday-single:hover, .tribe-grid-body .tribe-events-week-hourly-single',
			'.datepicker.dropdown-menu .datepicker-days table tr td.active:hover',
		);
		$color = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
		$rgb = fusion_hex2rgb( $color );
		$rgba = 'rgba( ' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',0.7)';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $rgba;

		$elements = array(
			'.fusion-tribe-primary-info .tribe-events-list-event-title a',
			'.fusion-events-single-title-content',
			'.fusion-tribe-primary-info .tribe-events-list-event-title a',
			'.datepicker.dropdown-menu table tr td.day',
			'.datepicker.dropdown-menu table tr td span.month',
			'.tribe-events-venue-widget .tribe-venue-widget-thumbnail .tribe-venue-widget-venue-name',
			".tribe-mini-calendar div[id*='daynum-'] a, .tribe-mini-calendar div[id*='daynum-'] span",
		);
		$color = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
		$rgb = fusion_hex2rgb( $color );
		$rgba = 'rgba( ' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',0.85)';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $rgba;

		$elements = array(
			'.tribe-events-list .tribe-events-event-cost',
			'.tribe-events-list .tribe-events-event-cost span',
			'.fusion-tribe-events-headline',
			'#tribe-events .tribe-events-day .tribe-events-day-time-slot h5',
			'.tribe-mobile-day-date',
			'.datepicker.dropdown-menu table thead tr:first-child',
			'.datepicker.dropdown-menu table thead tr:first-child th:hover',
			'.datepicker.dropdown-menu .datepicker-days table tr td.active',
			'.datepicker.dropdown-menu .datepicker-days table tr td:hover',
			'.tribe-grid-header',
			'.datepicker.dropdown-menu table tr td span.month.active',
			'.datepicker.dropdown-menu table tr td span.month:hover',
			'.tribe-grid-body .tribe-events-week-hourly-single:hover',
			'.tribe-events-venue-widget .tribe-venue-widget-venue-name',
			'.tribe-mini-calendar .tribe-mini-calendar-nav td',
			".tribe-mini-calendar div[id*='daynum-'] a:hover",
			'.tribe-mini-calendar td.tribe-events-has-events:hover a',
			'.fusion-body .tribe-mini-calendar td.tribe-events-has-events:hover a:hover',
			'.fusion-body .tribe-mini-calendar td.tribe-events-has-events a:hover',
			'.tribe-mini-calendar td.tribe-events-has-events.tribe-events-present a:hover',
			'.tribe-mini-calendar td.tribe-events-has-events.tribe-mini-calendar-today a:hover',
			".tribe-mini-calendar .tribe-mini-calendar-today div[id*='daynum-'] a",
			".tribe-mini-calendar .tribe-mini-calendar-today div[id*='daynum-'] a",
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

		$elements = array(
			'.tribe-grid-header',
			'.tribe-events-grid .tribe-grid-header .tribe-grid-content-wrap .column',
			'.tribe-grid-allday .tribe-events-week-allday-single, .tribe-grid-allday .tribe-events-week-allday-single:hover, .tribe-grid-body .tribe-events-week-hourly-single, .tribe-grid-body .tribe-events-week-hourly-single:hover',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );

		$elements = array(
			'.tribe-events-calendar thead th',
			'.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-]',
			'.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-]>a',
			'.tribe-events-calendar div[id*=tribe-events-daynum-]',
			'.tribe-events-calendar div[id*=tribe-events-daynum-] a',
			'.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-]',
			'.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-]>a',
			'#tribe-events-content .tribe-events-tooltip h4',
			'.tribe-events-list-separator-month',
			'.fusion-tribe-primary-info .tribe-events-list-event-title',
			'.fusion-tribe-primary-info .tribe-events-list-event-title a',
			'.tribe-events-list .tribe-events-event-cost',
			'#tribe-events .fusion-tribe-events-headline h3',
			'#tribe-events .fusion-tribe-events-headline h3 a',
			'#tribe-events .tribe-events-day .tribe-events-day-time-slot h5',
			'.tribe-mobile-day .tribe-mobile-day-date',
			'.datepicker.dropdown-menu table thead tr:first-child',
			'.datepicker.dropdown-menu table tr td.day',
			'.fusion-events-single-title-content h2',
			'.fusion-events-single-title-content h3',
			'.fusion-events-single-title-content span',
			'.tribe-grid-header',
			'.tribe-grid-allday .tribe-events-week-allday-single, .tribe-grid-allday .tribe-events-week-allday-single:hover, .tribe-grid-body .tribe-events-week-hourly-single, .tribe-grid-body .tribe-events-week-hourly-single:hover',
			'.datepicker.dropdown-menu .datepicker-days table tr td.active:hover',
			'.datepicker.dropdown-menu table tr td span.month',
			'.datepicker.dropdown-menu table tr td span.month.active:hover',
			'.recurringinfo',
			'.fusion-events-featured-image .event-is-recurring',
			'.fusion-events-featured-image .event-is-recurring:hover',
			'.fusion-events-featured-image .event-is-recurring a',
			'.single-tribe_events .fusion-events-featured-image .recurringinfo .tribe-events-divider',
			'.tribe-events-venue-widget .tribe-venue-widget-venue-name, .tribe-events-venue-widget .tribe-venue-widget-venue-name a, #slidingbar-area .tribe-events-venue-widget .tribe-venue-widget-venue-name a',
			'.tribe-events-venue-widget .tribe-venue-widget-venue-name, .tribe-events-venue-widget .tribe-venue-widget-venue-name a:hover, #slidingbar-area .tribe-events-venue-widget .tribe-venue-widget-venue-name a:hover',
			'.tribe-mini-calendar .tribe-mini-calendar-nav td',
			".tribe-mini-calendar div[id*='daynum-'] a, .tribe-mini-calendar div[id*='daynum-'] span",
			"#slidingbar-area .tribe-mini-calendar div[id*='daynum-'] a",
			".tribe-mini-calendar div[id*='daynum-'] a:hover",
			'.tribe-mini-calendar .tribe-events-has-events:hover',
			'.tribe-mini-calendar .tribe-events-has-events:hover a',
			'.tribe-mini-calendar .tribe-events-has-events:hover a:hover',
			'.tribe-mini-calendar .tribe-events-has-events a:hover',
			'.tribe-mini-calendar .tribe-events-has-events.tribe-events-present a:hover',
			'.tribe-mini-calendar td.tribe-events-has-events.tribe-mini-calendar-today a:hover',
			'.tribe-mini-calendar .tribe-events-has-events.tribe-mini-calendar-today a',
			'.tribe-mini-calendar .tribe-events-has-events.tribe-mini-calendar-today a',
			".tribe-mini-calendar .tribe-events-othermonth.tribe-mini-calendar-today div[id*='daynum-'] a",
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_overlay_text_color' ) );

		$elements = array(
			'#tribe-events .tribe-events-list .tribe-events-event-meta .author > div',
			'.fusion-body #tribe-events .tribe-events-list .tribe-events-event-meta .author > div:last-child',
			'.events-list #tribe-events-footer, .single-tribe_events #tribe-events-footer, #tribe-events #tribe-events-footer',
			'.tribe-grid-allday',
			'.tribe-events-grid .tribe-grid-content-wrap .column',
			'.tribe-week-grid-block div',
			'#tribe-events #tribe-geo-results .type-tribe_events:last-child',
			'.events-archive.events-gridview #tribe-events-content table .type-tribe_events',
			'.tribe-events-viewmore',
			'.fusion-events-before-title h2',
			'#tribe-events .tribe-events-list .type-tribe_events',
			'#tribe-events .tribe-events-list-separator-month+.type-tribe_events.tribe-events-first',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_border_color' ) );

		$elements = array(
			'.tribe-bar-views-inner',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a',
			'#tribe_events_filters_wrapper .tribe-events-filters-group-heading',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_bg_color' ) );

		$elements = array(
			'#tribe_events_filters_wrapper .tribe-events-filters-group-heading',
			'.tribe-events-filter-group',
			'.tribe-events-filter-group:after',
			'#tribe_events_filters_wrapper .tribe-events-filter-group label',
			'.tribe-events-filters-horizontal .tribe-events-filter-group:before',
			'.tribe-events-filters-horizontal .tribe-events-filter-group:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-color'] = fusion_adjust_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_bg_color' ) ), -25 );

		$elements = array(
			'#tribe-bar-form',
			'#tribe-events-bar:before',
			'#tribe-events-bar:after',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper.tribe-events-filters-horizontal:before',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper.tribe-events-filters-horizontal:after',
			'#tribe-bar-collapse-toggle',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:hover',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option.tribe-bar-active a:hover',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper.tribe-events-filters-horizontal',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper.tribe-events-filters-vertical .tribe-events-filters-content',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper:before',
			'#tribe-events-content-wrapper #tribe_events_filters_wrapper:after',
			'.tribe-events-filter-group.tribe-events-filter-autocomplete',
			'.tribe-events-filter-group.tribe-events-filter-multiselect',
			'.tribe-events-filter-group.tribe-events-filter-range',
			'.tribe-events-filter-group.tribe-events-filter-select',
			'#tribe_events_filters_wrapper .tribe-events-filters-group-heading:hover',
			'#tribe_events_filters_wrapper .tribe-events-filter-group label',
			'#tribe_events_filters_wrapper .closed .tribe-events-filters-group-heading:hover',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = fusion_adjust_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_bg_color' ) ), 10 );

		$elements = array(
			'.tribe-events-filters-horizontal .tribe-events-filter-group'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = fusion_adjust_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_bg_color' ) ), -25 );

		$elements = array(
			'.tribe-events-filter-group:after'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-color'] = fusion_adjust_brightness( Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_bg_color' ) ), 10 );

		$elements = array(
			'#tribe-bar-form label',
			'.tribe-bar-disabled #tribe-bar-form label',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option a:hover',
			'#tribe-bar-views .tribe-bar-views-list .tribe-bar-views-option.tribe-bar-active a:hover',
			'#tribe_events_filters_wrapper .tribe-events-filters-label',
			'#tribe_events_filters_wrapper .tribe-events-filters-group-heading',
			'#tribe_events_filters_wrapper .tribe-events-filters-group-heading:after',
			'#tribe_events_filters_wrapper .tribe-events-filters-content > label',
			'#tribe_events_filters_wrapper label span',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_bar_text_color' ) );

		$elements = array(
			'.tribe-events-calendar div[id*=tribe-events-daynum-]',
			'.tribe-events-calendar div[id*=tribe-events-daynum-] a',
			'.tribe-events-grid .tribe-grid-header .tribe-week-today',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_calendar_heading_bg_color' ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth',
			'.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-]',
			'.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-]>a',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( fusion_adjust_brightness( Avada()->settings->get( 'ec_calendar_heading_bg_color' ), 40 ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_calendar_bg_color' ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( fusion_adjust_brightness( Avada()->settings->get( 'ec_calendar_bg_color' ), 80 ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( fusion_adjust_brightness( Avada()->settings->get( 'ec_calendar_bg_color' ), 80 ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td',
			'#tribe-events-content table.tribe-events-calendar',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_border_color' ) );

		$elements = array(
			'#tribe-events-content .tribe-events-calendar td:hover',
			'.tribe-week-today',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( fusion_adjust_brightness( Avada()->settings->get( 'ec_calendar_bg_color' ), 60 ) );

		$elements = array(
			'.tribe-grid-allday',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( fusion_adjust_brightness( Avada()->settings->get( 'ec_calendar_bg_color' ), 70 ) );

		$elements = array(
			'.recurring-info-tooltip',
			'#tribe-events-content .tribe-events-tooltip',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_tooltip_bg_color' ) );

		$elements = array(
			'.tribe-events-tooltip:before',
			'.tribe-events-right .tribe-events-tooltip:before',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-top-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_tooltip_bg_color' ) );

		$elements = array(
			'.tribe-grid-body .tribe-events-tooltip:before',
			'.tribe-grid-body .tribe-events-tooltip:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-right-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_tooltip_bg_color' ) );

		$elements = array(
			'.tribe-grid-body .tribe-events-right .tribe-events-tooltip:before',
			'.tribe-grid-body .tribe-events-right .tribe-events-tooltip:after',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-left-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_tooltip_bg_color' ) );

		$elements = array(
			'#tribe-events-content .tribe-events-tooltip'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'ec_tooltip_body_color' ) );

	} // End if().

	// Non-responsive mode.
	if ( ! Avada()->settings->get( 'responsive' ) ) {

		if ( 'Top' == Avada()->settings->get( 'header_position' ) ) {
			$elements = array( 'html', 'body' );
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['overflow-x'] = 'hidden';
		} else {
			$css['global']['.ua-mobile #wrapper']['width'] = 'auto !important';
		}

		$media_query = '@media screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';
		$css[ $media_query ]['.fusion-fullwidth']['background-attachment'] = 'scroll !important';
		$css[ $media_query ]['.fusion-fullwidth .fullwidth-faded']['background-attachment'] = 'scroll !important';
		$css[ $media_query ]['.no-mobile-totop .to-top-container']['display'] = 'none';
		$css[ $media_query ]['.no-mobile-slidingbar #slidingbar-area']['display'] = 'none';
		$css[ $media_query ]['.fusion-main-menu > ul > li']['padding-right'] = intval( Avada()->settings->get( 'mobile_nav_padding' ) ) . 'px';

		$media_query = '@media screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) - 18 ) . 'px)';
		$elements = array( 'body.admin-bar #wrapper #slidingbar-area', '.admin-bar p.demo_store' );
		$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '46px';
		$css[ $media_query ]['body.body_blank.admin-bar']['top'] = '45px';
		$css[ $media_query ]['html #wpadminbar']['z-index']  = '99999 !important';
		$css[ $media_query ]['html #wpadminbar']['position'] = 'fixed !important';

	}

	// Responsive mode.
	if ( Avada()->settings->get( 'responsive' ) ) {
		/*
		Media Queries
			00 Side Width / Layout Responsive Styles
				# General Styles
				# Grid System
			01 Side Header Responsive Styles
			02 Top Header Responsive Styles
			03 Mobile Menu Responsive Styles
			04 @media only screen and ( max-width: $content_break_point )
				# Layout
				# General Styles
				# Page Title Bar
				# Blog Layouts
				# Author Page - Info
				# Shortcodes
				# Events Calendar
				# Woocommerce
				# Not restructured mobile.css styles
			05 @media only screen and ( min-width: $content_break_point )
				# Shortcodes
			06 @media only screen and ( max-width: 640px )
				# Layout
				# General Styles
				# Page Title Bar
				# Blog Layouts
				# Footer Styles
				# Filters
				# Not restructured mobile.css styles
			07 @media only screen and ( min-device-width: 320px ) and ( max-device-width: 640px )
			08 @media only screen and ( max-width: 480px )
			09 media.css CSS
			10 iPad Landscape Responsive Styles
				# Footer Styles
			11 iPad Portrait Responsive Styles
				# Layout
				# Footer Styles
		*/

		$side_header_width = ( 'Top' == Avada()->settings->get( 'header_position' ) ) ? 0 : intval( Avada()->settings->get( 'side_header_width' ) );

		// # Grid System.
		$main_break_point = (int) Avada()->settings->get( 'grid_main_break_point' );
		if ( 640 < $main_break_point ) {
			$breakpoint_range = $main_break_point - 640;
		} else {
			$breakpoint_range = 360;
		}

		$breakpoint_interval = $breakpoint_range / 5;

		$six_columns_breakpoint   = $main_break_point + $side_header_width;
		$five_columns_breakpoint  = $six_columns_breakpoint - $breakpoint_interval;
		$four_columns_breakpoint  = $five_columns_breakpoint - $breakpoint_interval;
		$three_columns_breakpoint = $four_columns_breakpoint - $breakpoint_interval;
		$two_columns_breakpoint   = $three_columns_breakpoint - $breakpoint_interval;
		$one_column_breakpoint    = $two_columns_breakpoint - $breakpoint_interval;

		$six_columns_media_query   = '@media only screen and (min-width: ' . $five_columns_breakpoint . 'px) and (max-width: ' . $six_columns_breakpoint . 'px)';
		$five_columns_media_query  = '@media only screen and (min-width: ' . $four_columns_breakpoint . 'px) and (max-width: ' . $five_columns_breakpoint . 'px)';
		$four_columns_media_query  = '@media only screen and (min-width: ' . $three_columns_breakpoint . 'px) and (max-width: ' . $four_columns_breakpoint . 'px)';
		$three_columns_media_query = '@media only screen and (min-width: ' . $two_columns_breakpoint . 'px) and (max-width: ' . $three_columns_breakpoint . 'px)';
		$two_columns_media_query   = '@media only screen and (max-width: ' . $two_columns_breakpoint . 'px)';
		$one_column_media_query    = '@media only screen and (max-width: ' . $one_column_breakpoint . 'px)';

		$ipad_portrait_media_query = '@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait)';

		// Six Column Breakpoint.
		$elements = array(
			'.grid-layout-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $six_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '20% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $six_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '40% !important';

		$elements = array(
			'.fusion-blog-layout-grid-5 .fusion-post-grid',
			'.fusion-portfolio-five .fusion-portfolio-post',
			'.fusion-grid-5 .fusion-grid-column',
		);
		$css[ $six_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '25% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $six_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '50% !important';

		// Five Column Breakpoint.
		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $five_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '20% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $five_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '40% !important';

		$elements = array(
			'.fusion-blog-layout-grid-5 .fusion-post-grid',
			'.fusion-blog-layout-grid-4 .fusion-post-grid',
			'.fusion-portfolio-five .fusion-portfolio-post',
			'.fusion-portfolio-four .fusion-portfolio-post',
			'.fusion-grid-5 .fusion-grid-column',
			'.fusion-grid-4 .fusion-grid-column',
		);
		$css[ $five_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '33.3333333333% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $five_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '66% !important';

		// Four Column Breakpoint.
		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $four_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '25% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $four_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '50% !important';

		$elements = array(
			'.fusion-blog-layout-grid-5 .fusion-post-grid',
			'.fusion-blog-layout-grid-4 .fusion-post-grid',
			'.fusion-blog-layout-grid-3 .fusion-post-grid',
			'.fusion-portfolio-five .fusion-portfolio-post',
			'.fusion-portfolio-four .fusion-portfolio-post',
			'.fusion-portfolio-three .fusion-portfolio-post',
			'.fusion-grid-5 .fusion-grid-column',
			'.fusion-grid-4 .fusion-grid-column',
			'.fusion-grid-3 .fusion-grid-column',
		);
		$css[ $four_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $four_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100% !important';

		// Three Column Breakpoint.
		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $three_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '33.33% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $three_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '66% !important';

		$elements = array(
			'.fusion-blog-layout-grid-5 .fusion-post-grid',
			'.fusion-blog-layout-grid-4 .fusion-post-grid',
			'.fusion-blog-layout-grid-3 .fusion-post-grid',
			'.fusion-portfolio-five .fusion-portfolio-post',
			'.fusion-portfolio-four .fusion-portfolio-post',
			'.fusion-portfolio-three .fusion-portfolio-post',
			'.fusion-portfolio-masonry .fusion-portfolio-post',
			'.fusion-grid-5 .fusion-grid-column',
			'.fusion-grid-4 .fusion-grid-column',
			'.fusion-grid-3 .fusion-grid-column',
		);
		$css[ $three_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $three_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100% !important';

		// Two Column Breakpoint.
		$elements = array(
			'.fusion-blog-layout-grid .fusion-post-grid',
			'.fusion-portfolio-post',
			'.fusion-grid-column',
		);
		$css[ $two_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';
		$css[ $two_columns_media_query ]['.fusion-portfolio-grid:not(.fusion-portfolio-text) .fusion-portfolio-post .fusion-image-wrapper']['display'] = 'block';
		$css[ $two_columns_media_query ]['.fusion-portfolio-grid:not(.fusion-portfolio-text) .fusion-portfolio-post .fusion-image-wrapper']['text-align'] = 'center';

		$elements_landscape = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape .fusion-masonry-element-container' );
		$css[ $two_columns_media_query ][ $dynamic_css_helpers->implode( $elements_landscape ) ]['padding-top'] = '65% !important';

		$elements_grid = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-grid .fusion-masonry-element-container' );
		$css[ $two_columns_media_query ][ $dynamic_css_helpers->implode( $elements_grid ) ]['padding-top'] = '100% !important';

		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $two_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $two_columns_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100% !important';

		// One Column Breakpoint.
		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $one_column_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';

		$elements_landscape = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape .fusion-masonry-element-container' );
		$css[ $one_column_media_query ][ $dynamic_css_helpers->implode( $elements_landscape ) ]['padding-top'] = '65% !important';

		$elements_grid = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-grid .fusion-masonry-element-container' );
		$css[ $one_column_media_query ][ $dynamic_css_helpers->implode( $elements_grid ) ]['padding-top'] = '100% !important';

		// Portrait Column Breakpoint for iPad.
		$elements = array(
			'.fusion-blog-layout-grid-6 .fusion-post-grid',
			'.fusion-portfolio-six .fusion-portfolio-post',
			'.fusion-grid-6 .fusion-grid-column',
		);
		$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '33.3333333333% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '66% !important';

		$elements = array(
			'.fusion-blog-layout-grid-5 .fusion-post-grid',
			'.fusion-blog-layout-grid-4 .fusion-post-grid',
			'.fusion-blog-layout-grid-3 .fusion-post-grid',
			'.fusion-portfolio-five .fusion-portfolio-post',
			'.fusion-portfolio-four .fusion-portfolio-post',
			'.fusion-portfolio-three .fusion-portfolio-post',
			'.fusion-portfolio-masonry .fusion-portfolio-post',
			'.fusion-grid-5 .fusion-grid-column',
			'.fusion-grid-4 .fusion-grid-column',
			'.fusion-grid-3 .fusion-grid-column',
		);
		$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';

		$elements = $dynamic_css_helpers->map_selector( $elements, '.fusion-element-landscape' );
		$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100% !important';

		/**
		 * Side Header Only Responsive Styles.
		 */
		$side_header_media_query = '@media only screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';
		$side_header_min_media_query = '@media only screen and (min-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';

		if ( Avada()->settings->get( 'logo_background' ) && 'v4' !== Avada()->settings->get( 'header_layout' ) && 'v5' !== Avada()->settings->get( 'header_layout' ) && 'Top' === Avada()->settings->get( 'header_position' ) ) {
			$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'logo_background_color' ) );
			if ( 'v7' !== Avada()->settings->get( 'header_layout' ) ) {
				$alignment = ( 'Center' === Avada()->settings->get( 'logo_alignment' ) ) ? 'left' : Avada()->settings->get( 'logo_alignment' );
				$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['float'] = $alignment;
				if ( Avada()->settings->get( 'header_100_width' ) ) {
					$css[ $side_header_min_media_query ]['.fusion-header'][ 'padding-' . $alignment ] = '0px';
				}
			}
			$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['display'] = 'inline-flex';
			$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['position'] = 'relative';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['content'] = '""';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['pointer-events'] = 'none';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['position'] = 'absolute';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['top'] = '-' . Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'top' ) );
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['left'] = '0';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['width'] = '100%';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['height'] = '100%';
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['border-top'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'top' ) ) . ' solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'logo_background_color' ) );
			$css[ $side_header_min_media_query ]['.fusion-header-wrapper .fusion-header .fusion-logo-background:after']['border-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'header_padding', 'bottom' ) ) . ' solid ' . Fusion_Sanitize::color( Avada()->settings->get( 'logo_background_color' ) );
			if ( Avada()->settings->get( 'header_sticky_shrinkage' ) ) {
				$css[ $side_header_min_media_query ]['.fusion-header-wrapper.fusion-is-sticky .fusion-header .fusion-logo-background:after']['transition'] = 'border-width 0.25s ease-in-out';
				$css[ $side_header_min_media_query ]['.fusion-header-wrapper.fusion-is-sticky .fusion-header .fusion-logo-background:after']['border-bottom-width'] = '0px';
			}
			if ( 'v6' === Avada()->settings->get( 'header_layout' ) && 'Right' === $alignment ) {
				$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['order'] = '2';
			}
			if ( 'v7' === Avada()->settings->get( 'header_layout' ) && 'background' !== Avada()->settings->get( 'menu_highlight_style' ) ) {
				$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['padding-right'] = '0px';
				$css[ $side_header_min_media_query ]['.fusion-header .fusion-logo-background']['margin-right'] = intval( Avada()->settings->get( 'nav_padding' ) ) . 'px';
				$css[ $side_header_min_media_query ]['.fusion-is-sticky .fusion-header .fusion-logo-background']['padding-right'] = '0px';
				$css[ $side_header_min_media_query ]['.fusion-is-sticky .fusion-header .fusion-logo-background']['margin-right'] = intval( Avada()->settings->get( 'header_sticky_nav_padding' ) ) . 'px';
			}
		}

		$css[ $side_header_min_media_query ]['.fusion-icon-only-link .menu-text']['display'] = 'none';
		$css[ $side_header_min_media_query ]['.fusion-main-menu > ul > li > a.fusion-icon-only-link > .fusion-megamenu-icon']['padding'] = '0px';

		if ( 'Top' !== Avada()->settings->get( 'header_position' ) && Avada()->settings->get( 'logo_background' ) ) {
			$elements = array(
				'.side-header-content.fusion-logo-center',
				'.side-header-content.fusion-logo-left',
				'.side-header-content.fusion-logo-right',
			);
			$css[ $side_header_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'logo_background_color' ) );
		}

		if ( 'Boxed' == Avada()->settings->get( 'layout' ) || 'boxed' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {
			$css[ $side_header_media_query ]['body.side-header #wrapper']['margin-left']  = 'auto !important';
			$css[ $side_header_media_query ]['body.side-header #wrapper']['margin-right'] = 'auto !important';
		} else {
			$css[ $side_header_media_query ]['body.side-header #wrapper']['margin-left']  = '0 !important';
			$css[ $side_header_media_query ]['body.side-header #wrapper']['margin-right'] = '0 !important';
		}

		$elements = array(
			'#side-header',
			'.side-header-background-color',
		);
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_header_bg_color' ) );
		$css[ $side_header_media_query ]['.layout-boxed-mode .side-header-wrapper']['background-color'] = 'transparent';
		$css[ $side_header_media_query ]['#side-header']['transition'] = 'background-color 0.25s ease-in-out';
		$css[ $side_header_media_query ]['#side-header.fusion-is-sticky']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'header_sticky_bg_color' ) );

		$css[ $side_header_media_query ]['#side-header']['position'] = 'static';
		$css[ $side_header_media_query ]['#side-header']['height']   = 'auto';
		$css[ $side_header_media_query ]['#side-header']['width']    = '100% !important';
		$css[ $side_header_media_query ]['#side-header']['padding']  = '20px 30px !important';
		$css[ $side_header_media_query ]['#side-header']['margin']   = '0 !important';

		$css[ $side_header_media_query ]['#side-header .side-header-styling-wrapper']['display']   = 'none';

		$css[ $side_header_media_query ]['#side-header .side-header-wrapper']['padding-top'] = '0';
		$css[ $side_header_media_query ]['#side-header .side-header-wrapper']['padding-bottom'] = '0';
		$css[ $side_header_media_query ]['#side-header .side-header-wrapper']['position'] = 'relative';

		$elements = array(
			'#side-header .header-social',
			'#side-header .header-v4-content',
		);
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $side_header_media_query ]['#side-header .fusion-logo']['margin'] = '0 !important';
		$css[ $side_header_media_query ]['#side-header .fusion-logo']['float']  = 'left';

		$css[ $side_header_media_query ]['#side-header .side-header-content']['padding'] = '0 !important';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-classic .fusion-logo']['float']      = 'none';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-classic .fusion-logo']['text-align'] = 'center';

		$elements = array(
			'body.side-header #wrapper #side-header.header-shadow .side-header-border:after',
			'body #wrapper .header-shadow:after',
		);
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position']   = 'static';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'none';

		$elements = array(
			'#side-header .fusion-main-menu',
			'#side-header .side-header-content-1-2',
			'#side-header .side-header-content-3',
		);
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $side_header_media_query ]['#side-header .fusion-logo']['margin'] = '0';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-classic .fusion-main-menu-container .fusion-mobile-nav-holder']['display']    = 'block';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-classic .fusion-main-menu-container .fusion-mobile-nav-holder']['margin-top'] = '20px';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-classic .fusion-main-menu-container .fusion-mobile-sticky-nav-holder']['display'] = 'none';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo']['float']  = 'left';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo']['margin'] = '0';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-left']['float'] = 'left';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-right']['float'] = 'right';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-center']['float'] = 'left';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-mobile-menu-icons']['display'] = 'block';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-right .fusion-mobile-menu-icons']['float'] = 'left';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-right .fusion-mobile-menu-icons']['position'] = 'static';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-right .fusion-mobile-menu-icons a']['float'] = 'left';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-right .fusion-mobile-menu-icons :first-child']['margin-left'] = '0';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-left .fusion-mobile-menu-icons']['float'] = 'right';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-logo-menu-left .fusion-mobile-menu-icons a:last-child']['margin-left'] = '0';

		$elements = array(
			'#side-header.fusion-mobile-menu-design-modern .fusion-main-menu-container .fusion-mobile-nav-holder',
			'#side-header.fusion-mobile-menu-design-modern .side-header-wrapper > .fusion-secondary-menu-search',
		);

		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = '20px';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']    = '-30px';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']   = '-30px';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom']  = '-20px';

		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-main-menu-container .fusion-mobile-nav-holder > ul']['display']       = 'block';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-main-menu-container .fusion-mobile-nav-holder > ul']['border-right']  = '0';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-main-menu-container .fusion-mobile-nav-holder > ul']['border-left']   = '0';
		$css[ $side_header_media_query ]['#side-header.fusion-mobile-menu-design-modern .fusion-main-menu-container .fusion-mobile-nav-holder > ul']['border-bottom'] = '0';

		$css[ $side_header_media_query ]['#side-header.fusion-is-sticky.fusion-sticky-menu-1 .fusion-mobile-nav-holder']['display'] = 'none';

		$css[ $side_header_media_query ]['#side-header.fusion-is-sticky.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder']['display'] = 'none';

		if ( ( ( 'Boxed' == Avada()->settings->get( 'layout' ) && 'wide' !== get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) || 'boxed' == get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) && 'Top' != Avada()->settings->get( 'header_position' ) ) {

			if ( 'Right' == Avada()->settings->get( 'header_position' ) ) {

				$css[ $side_header_min_media_query ]['body.side-header-right #side-header']['position'] = 'absolute';
				$css[ $side_header_min_media_query ]['body.side-header-right #side-header']['top']      = '0';

				// $css[ $side_header_min_media_query ]['body.side-header-right #side-header .side-header-wrapper']['position'] = 'fixed';
				$css[ $side_header_min_media_query ]['body.side-header-right #side-header .side-header-wrapper']['width']    = intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

			}
		}

		$elements = array(
					'.width-100 .fusion-section-separator-with-offset',
				);
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = 'calc( (100vw - 100% ) / -2 ) !important';
		$css[ $side_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = 'calc( (100vw - 100% ) / -2 ) !important';

		/*
		Top Header Only Responsive Styles.
		*/
		$mobile_header_media_query = '@media only screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';
		$mobile_header_min_media_query = '@media only screen and (min-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-header']['padding'] = '0px';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-header .fusion-row']['padding-left']  = '0px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-header .fusion-row']['padding-right'] = '0px';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header']['max-width']  = '100%';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header']['text-align'] = 'center';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header']['margin-top'] = '10px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header']['margin-bottom'] = '8px';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header a']['margin-right']  = '20px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-social-links-header a']['margin-bottom'] = '5px';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-alignleft']['border-bottom'] = '1px solid transparent';

		$elements = array(
			'.fusion-mobile-menu-design-modern .fusion-alignleft',
			'.fusion-mobile-menu-design-modern .fusion-alignright',
		);
		$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']      = '100%';
		$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none';
		$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']    = 'block';

		$elements = array(
			'.fusion-body .fusion-mobile-menu-design-modern .fusion-secondary-header .fusion-alignleft',
			'.fusion-body .fusion-mobile-menu-design-modern .fusion-secondary-header .fusion-alignright',
		);
		$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu > ul > li']['display'] = 'inline-block';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu > ul > li']['vertical-align'] = 'middle';
		$css[ $mobile_header_media_query ]['.fusion-body .fusion-mobile-menu-design-modern .fusion-secondary-menu > ul > li']['float'] = 'none';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu > ul > li']['text-align'] = 'left';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-cart']['border-right'] = '0';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon']['background-color'] = 'transparent';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon']['padding-left']     = '10px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon']['padding-right']    = '7px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon']['min-width']        = '100%';

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon:after']['display'] = 'none';

		$elements = array(
			'.fusion-mobile-menu-design-modern .fusion-secondary-menu .fusion-secondary-menu-icon',
			'.fusion-mobile-menu-design-modern .fusion-secondary-menu .fusion-secondary-menu-icon:hover',
			'.fusion-mobile-menu-design-modern .fusion-secondary-menu-icon:before',
		);
		$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( Avada()->settings->get( 'snav_color' ) );

		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-header-tagline']['margin-top']  = '10px';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-header-tagline']['float']       = 'none';
		$css[ $mobile_header_media_query ]['.fusion-mobile-menu-design-modern .fusion-header-tagline']['line-height'] = '24px';

		if ( ( ( 1 > Fusion_Color::new_color( Avada()->settings->get( 'header_bg_color' ) )->alpha && ! get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) || ( '' != get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) && 1 > get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) ) && ! is_search() && ! is_404() && ! is_author() && ( ! is_archive() || ( class_exists( 'WooCommerce' ) && is_shop() ) ) ) {

			$elements = array(
				'.fusion-header',
				'.fusion-secondary-header',
			);
			$css[ $mobile_header_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-top'] = 'none';

			$elements = array(
				'.fusion-header-v1 .fusion-header',
				'.fusion-secondary-main-menu',
			);
			$css[ $mobile_header_min_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border'] = 'none';

			if ( 'boxed' === fusion_get_option( 'layout', 'page_bg_layout', $c_page_id ) ) {
				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['position'] = 'absolute';
				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['z-index']  = '10000';

				if ( $site_width_percent ) {
					$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
				} else {
					$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['width']		= '100%';
					$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['max-width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
				}
			} else {

				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['position'] = 'absolute';
				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['left']     = '0';
				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['right']    = '0';
				$css[ $mobile_header_min_media_query ]['.fusion-header-wrapper']['z-index']  = '10000';

			}
		}

		/*
		Mobile Menu Responsive Styles
		*/
		$mobile_menu_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + intval( Avada()->settings->get( 'side_header_break_point' ) ) ) . 'px)';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-header']['padding-left'] = '0 !important';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern .fusion-secondary-header']['padding-right'] = '0 !important';

		$css[ $mobile_menu_media_query ]['.fusion-header .fusion-row']['padding-left']  = '0';
		$css[ $mobile_menu_media_query ]['.fusion-header .fusion-row']['padding-right'] = '0';

		$elements = array(
			'.fusion-header-wrapper .fusion-header',
			'.fusion-header-wrapper .fusion-secondary-main-menu',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'mobile_header_bg_color' ) );

		$secondary_header_bg_color_obj = Fusion_Color::new_color( Avada()->settings->get( 'header_top_bg_color' ) );
		$secondary_header_bg_color_rgb = array(
			$secondary_header_bg_color_obj->red,
			$secondary_header_bg_color_obj->green,
			$secondary_header_bg_color_obj->blue,
		);
		$css[ $mobile_menu_media_query ]['.fusion-secondary-header']['background-color'] = 'rgb(' . $secondary_header_bg_color_obj->red . ',' . $secondary_header_bg_color_obj->green . ',' . $secondary_header_bg_color_obj->blue . ')';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-row']['padding-left']  = '0';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-row']['padding-right'] = '0';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-row']['max-width']     = '100%';

		$elements = array(
			'.fusion-footer-widget-area > .fusion-row',
			'.fusion-footer-copyright-area > .fusion-row',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0';

		$css[ $mobile_menu_media_query ]['.fusion-secondary-header .fusion-row']['display'] = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-secondary-header .fusion-alignleft']['margin-right'] = '0';
		$css[ $mobile_menu_media_query ]['.fusion-secondary-header .fusion-alignright']['margin-left'] = '0';
		$css[ $mobile_menu_media_query ]['body.fusion-body .fusion-secondary-header .fusion-alignright > *']['float'] = 'none';
		$css[ $mobile_menu_media_query ]['body.fusion-body .fusion-secondary-header .fusion-alignright .fusion-social-links-header .boxed-icons']['margin-bottom'] = '5px';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-header',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-header',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-header',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-header',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = '20px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '20px';

		$css[ $mobile_menu_media_query ]['.fusion-header-v4 .fusion-logo']['display']  = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-header-v4.fusion-mobile-menu-design-modern .fusion-logo .fusion-logo-link']['max-width'] = '75%';
		$css[ $mobile_menu_media_query ]['.fusion-header-v4.fusion-mobile-menu-design-modern .fusion-mobile-menu-icons']['position'] = 'absolute';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-logo a',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-logo a',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-logo a',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-logo a',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']     = '0 !important';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-main-menu',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-main-menu',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-main-menu',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-main-menu > ul > li',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-main-menu > ul .fusion-middle-logo-menu-logo']['display'] = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-sticky-menu > ul .fusion-middle-logo-menu-logo']['display'] = 'none';
		$css[ $mobile_menu_media_query ]['.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-main-menu > ul .fusion-middle-logo-menu-logo']['display'] = 'none';
		$css[ $mobile_menu_media_query ]['.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-sticky-menu > ul .fusion-middle-logo-menu-logo']['display'] = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-main-menu']['display'] = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-main-menu']['max-width'] = 'auto';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-logo']['padding'] = '0';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-mobile-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']    = 'block';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = '20px';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic .fusion-secondary-header']['padding'] = '10px';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic .fusion-secondary-header .fusion-mobile-nav-holder']['margin-top'] = '0';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-header',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-header',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = '20px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '20px';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-secondary-main-menu',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-secondary-main-menu',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = '6px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '6px';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-main-menu',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-main-menu',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic .fusion-main-menu.fusion-ubermenu']['display'] = 'block';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-mobile-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-logo',
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-logo a',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-logo a',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']     = '0 !important';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .searchform',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .searchform',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']    = 'block';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']      = '100%';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']     = '0';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = '13px';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .search-table',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .search-table',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-logo a']['float'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-header-banner']['margin-top'] = '10px';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-secondary-main-menu .searchform']['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic .fusion-alignleft']['margin-bottom'] = '10px';

		$elements = array(
			'.fusion-mobile-menu-design-classic .fusion-alignleft',
			'.fusion-mobile-menu-design-classic .fusion-alignright',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']       = 'none';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']       = '100%';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = 'normal';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']     = 'block';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-contact-info']['text-align']  = 'center';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-contact-info']['line-height'] = 'normal';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-secondary-menu']['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-social-links-header']['max-width']     = '100%';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-social-links-header']['margin-top']    = '5px';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-social-links-header']['text-align']    = 'center';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-social-links-header a']['margin-bottom'] = '5px';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['float']        = 'none';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['text-align']   = 'center';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['margin-top']   = '10px';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['line-height']  = '24px';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['margin-left']  = 'auto';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-tagline']['margin-right'] = 'auto';

		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['float']      = 'none';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['text-align'] = 'center';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['margin']     = '0 auto';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['width']      = '100%';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['margin-top'] = '20px';
		$css[ $mobile_menu_media_query ]['.fusion-header-wrapper .fusion-mobile-menu-design-classic .fusion-header-banner']['clear']      = 'both';

		$elements = array(
			'.fusion-mobile-menu-design-modern .ubermenu-responsive-toggle',
			'.fusion-mobile-menu-design-modern .ubermenu-sticky-toggle-wrapper',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear'] = 'both';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu > ul > li',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu > ul .fusion-middle-logo-menu-logo']['display'] = 'block';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu']['display'] = 'block';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-sticky-menu']['display'] = 'none';
		if ( has_nav_menu( 'sticky_navigation' ) ) {
			$css[ $mobile_menu_media_query ]['.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu']['display'] = 'none';
			$css[ $mobile_menu_media_query ]['.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-sticky-menu']['display'] = 'block';
		}

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern .fusion-main-menu.fusion-ubermenu']['display'] = 'block';

		if ( 'Right' === Avada()->settings->get( 'logo_alignment' ) ) {
			$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu']['float'] = 'right';
		} else {
			$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu']['float'] = 'left';
		}
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-main-menu']['width'] = 'auto';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-header',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-header',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-header',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-header',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-header',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-header',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']    = '20px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '20px';

		$elements = $dynamic_css_helpers->map_selector( $elements, ' .fusion-row' );
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-logo',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin'] = '0 !important';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-logo']['padding'] = '0';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .modern-mobile-menu-expanded .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .modern-mobile-menu-expanded .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .modern-mobile-menu-expanded .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .modern-mobile-menu-expanded .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .modern-mobile-menu-expanded .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .modern-mobile-menu-expanded .fusion-logo',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '20px !important';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-mobile-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']   = '20px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '-30px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '-30px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = Fusion_Sanitize::add_css_values( array( '-20px', '-' . Fusion_Sanitize::get_value_with_unit( Avada()->settings->get( 'header_padding', 'bottom' ) ) ) );

		$elements = $dynamic_css_helpers->map_selector( $elements, ' > ul' );
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-mobile-sticky-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-mobile-menu-icons',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-mobile-menu-icons',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-mobile-menu-icons',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-mobile-menu-icons',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-mobile-menu-icons',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-mobile-menu-icons',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-logo a']['float'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-logo .searchform']['float']   = 'none';
		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-logo .searchform']['display'] = 'none';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-header-banner']['margin-top'] = '10px';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-logo']['float'] = 'left';

		if ( is_rtl() ) {
			$css[ $mobile_menu_media_query ]['.rtl .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-logo']['float'] = 'right';

			$css[ $mobile_menu_media_query ]['.rtl .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-mobile-menu-icons']['float'] = 'left';

			$css[ $mobile_menu_media_query ]['.rtl .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-mobile-menu-icons a']['float']        = 'left';
			$css[ $mobile_menu_media_query ]['.rtl .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-mobile-menu-icons a']['margin-left']  = '0';
			$css[ $mobile_menu_media_query ]['.rtl .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-logo-center .fusion-mobile-menu-icons a']['margin-right'] = '15px';
		}

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-mobile-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-mobile-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top']   = '0';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '-30px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '-30px';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '0';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-secondary-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-secondary-main-menu',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'static';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border']   = '0';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-secondary-main-menu .fusion-mobile-nav-holder > ul',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-secondary-main-menu .fusion-mobile-nav-holder > ul',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border'] = '0';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-secondary-main-menu .searchform',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-secondary-main-menu .searchform',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';

		$elements = array(
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-sticky-header-wrapper',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-sticky-header-wrapper',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'fixed';
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']    = '100%';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-logo-right.fusion-header-v4 .fusion-logo',
			'.fusion-mobile-menu-design-modern.fusion-logo-right.fusion-header-v5 .fusion-logo',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'right';

		$elements = array(
			'.fusion-mobile-menu-design-modern.fusion-sticky-menu-only.fusion-header-v4 .fusion-secondary-main-menu',
			'.fusion-mobile-menu-design-modern.fusion-sticky-menu-only.fusion-header-v5 .fusion-secondary-main-menu',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'static';

		$elements = array(
			'.fusion-mobile-menu-design-classic.fusion-header-v1 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v2 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v3 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v4 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v5 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-classic.fusion-header-v7 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v1 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v2 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v3 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v4 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v5 .fusion-mobile-sticky-nav-holder',
			'.fusion-mobile-menu-design-modern.fusion-header-v7 .fusion-mobile-sticky-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$elements = array(
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v1.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v2.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v3.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v4.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v5.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v7.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v1.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v2.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v3.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v4.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v5.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-modern.fusion-header-v7.fusion-sticky-menu-1 .fusion-mobile-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$elements = array(
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v1.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v2.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v3.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v4.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder',
			'.fusion-is-sticky .fusion-mobile-menu-design-classic.fusion-header-v5.fusion-sticky-menu-1 .fusion-mobile-sticky-nav-holder',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

		$css[ $mobile_menu_media_query ]['.fusion-mobile-menu-design-classic .fusion-mobile-nav-holder .fusion-secondary-menu-icon']['text-align'] = 'inherit';

		$elements = array(
			'.fusion-mobile-menu-design-classic .fusion-mobile-nav-holder .fusion-secondary-menu-icon:before',
			'.fusion-mobile-menu-design-classic .fusion-mobile-nav-holder .fusion-secondary-menu-icon:after',
		);
		$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		if ( class_exists( 'SitePress' ) ) {
			$css[ $mobile_menu_media_query ]['.fusion-mobile-nav-holder li.fusion-mobile-nav-item .wpml-ls-item a::before']['display'] = 'none';
			$elements = array(
				'.fusion-mobile-nav-holder .wpml-ls-item .menu-text',
				'.wpml-ls-item .menu-text, .wpml-ls-item .sub-menu a > span',
				'.fusion-mobile-nav-holder .wpml-ls-item > a',
			);
			$wpml_mobile_text_align = 'center';
			if ( 'left' === esc_attr( Avada()->settings->get( 'mobile_menu_text_align' ) ) ) {
				$wpml_mobile_text_align = ( is_rtl() ) ? 'flex-end' : 'flex-start';
			} elseif ( 'right' === esc_attr( Avada()->settings->get( 'mobile_menu_text_align' ) ) ) {
				$wpml_mobile_text_align = ( is_rtl() ) ? 'flex-start' : 'flex-end';
			}
			$css[ $mobile_menu_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['justify-content'] = $wpml_mobile_text_align;
			$css[ $mobile_menu_media_query ]['.fusion-mobile-nav-holder .wpml-ls-native']['padding'] = '0 5px';
		}

		/*
		@media only screen and ( max-width: $content_break_point )
		*/
		$content_break_point_media_query = '@media only screen and (max-width: ' . intval( Avada()->settings->get( 'content_break_point' ) ) . 'px)';
		$content_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + intval( Avada()->settings->get( 'content_break_point' ) ) ) . 'px)';
		$content_min_media_query = '@media only screen and (min-width: ' . ( intval( $side_header_width ) + intval( Avada()->settings->get( 'content_break_point' ) ) ) . 'px)';

		// # Layout
		if ( Avada()->settings->get( 'smooth_scrolling' ) ) {
			if ( Avada()->settings->get( 'responsive' ) ) {
				$css[ $content_min_media_query ]['.no-overflow-y body']['padding-right'] = '9px';
				$css[ $content_min_media_query ]['.no-overflow-y #slidingbar-area']['right'] = '9px';
			}
		}

		if ( ! Avada()->settings->get( 'breadcrumb_mobile' ) ) {
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar .fusion-breadcrumbs']['display'] = 'none';
		}

		$css[ $content_media_query ]['.no-overflow-y']['overflow-y'] = 'visible !important';

		// Column breakpoints, content/sidebar moved to new sidebar breakpoint.
		$css[ $content_media_query ]['.fusion-layout-column']['margin-left']  = '0 !important';
		$css[ $content_media_query ]['.fusion-layout-column']['margin-right'] = '0 !important';

		$elements = array(
			'.fusion-layout-column:nth-child(5n)',
			'.fusion-layout-column:nth-child(4n)',
			'.fusion-layout-column:nth-child(3n)',
			'.fusion-layout-column:nth-child(2n)',
		);

		$css[ $content_media_query ]['.fusion-layout-column.fusion-spacing-no']['margin-bottom'] = '0';
		$css[ $content_media_query ]['.fusion-body .fusion-layout-column']['width'] = '100% !important';

		$elements = array(
			'.fusion-columns-5 .fusion-column:first-child',
			'.fusion-columns-4 .fusion-column:first-child',
			'.fusion-columns-3 .fusion-column:first-child',
			'.fusion-columns-2 .fusion-column:first-child',
			'.fusion-columns-1 .fusion-column:first-child',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = '0';

		$css[ $content_media_query ]['.fusion-columns .fusion-column']['width'] 	   = '100% !important';
		$css[ $content_media_query ]['.fusion-columns .fusion-column']['float']      = 'none';
		$css[ $content_media_query ]['.fusion-columns .fusion-column:not(.fusion-column-last)']['margin']     = '0 0 50px';
		$css[ $content_media_query ]['.fusion-columns .fusion-column']['box-sizing'] = 'border-box';

		if ( is_rtl() ) {
			$css[ $content_media_query ]['.rtl .fusion-column']['float'] = 'none';
		}

		$elements = array(
			'.col-sm-12',
			'.col-sm-6',
			'.col-sm-4',
			'.col-sm-3',
			'.col-sm-2',
			'.fusion-columns-5 .col-lg-2',
			'.fusion-columns-5 .col-md-2',
			'.fusion-columns-5 .col-sm-2',
			'.avada-container .columns .col',
			'.footer-area .fusion-columns .fusion-column',
			'#slidingbar-area .columns .col',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';

		if ( get_post_meta( $c_page_id, 'pyre_fallback', true ) ) {
			$css[ $content_media_query ]['#sliders-container']['display'] = 'none';
			$css[ $content_media_query ]['#fallback-slide']['display'] = 'block';
		}

		// # General Styles
		$css[ $content_media_query ]['.fusion-portfolio-text-floated .fusion-portfolio-content-wrapper']['display'] = 'block';
		$css[ $content_media_query ]['.fusion-portfolio-text-floated .fusion-image-wrapper']['max-width'] = 'none';

		// Mobile Logo.
		if ( Avada()->settings->get( 'mobile_logo', 'url' ) ) {
			$elements = array(
				'.fusion-mobile-logo-1 .fusion-standard-logo',
				'#side-header .fusion-mobile-logo-1 .fusion-standard-logo',
			);
			$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

			$elements = array(
				'.fusion-mobile-logo-1 .fusion-mobile-logo-1x',
				'#side-header .fusion-mobile-logo-1 .fusion-mobile-logo-1x',
			);
			$css[ $mobile_header_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';
		}

		$css[ $content_media_query ]['.fusion-secondary-menu-icon']['min-width'] = '100%';

		// # Page Title Bar
		if ( 'auto' != Avada()->settings->get( 'page_title_mobile_height' ) ) {

			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-top']    = '5px';
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-bottom'] = '5px';
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['min-height']     = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'page_title_mobile_height' ) ), '-10px' ) );
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['height']         = 'auto';

		} else {

			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-top']    = '10px';
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-bottom'] = '10px';
			$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['height']         = 'auto';
			$css[ $content_media_query ]['.fusion-page-title-row']['height'] = 'auto';

		}

		$css[ $content_media_query ]['.fusion-page-title-wrapper']['display'] = 'block';

		$elements = array(
			'.fusion-page-title-bar-left .fusion-page-title-captions',
			'.fusion-page-title-bar-right .fusion-page-title-captions',
			'.fusion-page-title-bar-left .fusion-page-title-secondary',
			'.fusion-page-title-bar-right .fusion-page-title-secondary',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']     = 'block';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']       = 'none';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']       = '100%';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = 'normal';

		$css[ $content_media_query ]['.fusion-page-title-bar-left .fusion-page-title-secondary']['text-align'] = 'left';
		$css[ $content_media_query ]['.fusion-page-title-bar .fusion-page-title-secondary']['margin'] = '2px 0 0 0';

		$css[ $content_media_query ]['.fusion-page-title-bar-left .searchform']['display']   = 'block';
		$css[ $content_media_query ]['.fusion-page-title-bar-left .searchform']['max-width'] = '100%';

		$css[ $content_media_query ]['.fusion-page-title-bar-right .fusion-page-title-secondary']['text-align'] = 'right';

		$css[ $content_media_query ]['.fusion-page-title-bar-right .searchform']['max-width'] = '100%';

		if ( 'auto' !== Avada()->settings->get( 'page_title_mobile_height' ) ) {

			$css[ $content_media_query ]['.fusion-page-title-row']['display']     = 'flex';
			$css[ $content_media_query ]['.fusion-page-title-row']['align-items'] = 'center';
			$css[ $content_media_query ]['.fusion-page-title-row']['width']       = '100%';
			$css[ $content_media_query ]['.fusion-page-title-row']['min-height']  = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'page_title_mobile_height' ) ), '-20px' ) );

			$css[ $content_media_query ]['.fusion-page-title-bar-center .fusion-page-title-row']['width'] = 'auto';

			$css[ $content_media_query ]['.fusion-page-title-captions']['width']        = '100%';
		}

		$css[ $content_media_query ]['.fusion-contact-info']['padding']     = '1em 30px 0px 30px';
		$css[ $content_media_query ]['.fusion-contact-info']['line-height'] = '1.5em';

		if ( get_post_meta( $c_page_id, 'pyre_page_title_mobile_height', true ) ) {

			if ( 'auto' !== get_post_meta( $c_page_id, 'pyre_page_title_mobile_height', true ) ) {

				$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['height'] = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_page_title_mobile_height', true ) );

				$css[ $content_media_query ]['.fusion-page-title-row']['display']     = 'flex';
				$css[ $content_media_query ]['.fusion-page-title-row']['align-items'] = 'center';

				$css[ $content_media_query ]['.fusion-page-title-captions']['width']        = '100%';

			} else {

				$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-top']    = '10px';
				$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['padding-bottom'] = '10px';
				$css[ $content_media_query ]['.fusion-body .fusion-page-title-bar']['height']         = 'auto';

			}
		}

		// # Blog Layouts.
		// Blog medium alternate layout.
		$elements = array(
			'.fusion-body .fusion-blog-layout-medium-alternate .fusion-post-content',
			'.fusion-body .fusion-blog-layout-medium-alternate .has-post-thumbnail .fusion-post-content',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']       = 'none';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear']       = 'both';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']      = '0';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top'] = '20px';

		// # Author Page - Info.
		$css[ $content_media_query ]['.fusion-author .fusion-social-networks']['display'] = 'block';
		$css[ $content_media_query ]['.fusion-body .fusion-author .fusion-social-networks']['text-align'] = 'center';
		$css[ $content_media_query ]['.fusion-author .fusion-social-networks']['margin-top'] = '10px';

		$css[ $content_media_query ]['.fusion-author-tagline']['display']      = 'block';
		$css[ $content_media_query ]['.fusion-author-tagline']['float']      = 'none';
		$css[ $content_media_query ]['.fusion-author-tagline']['text-align'] = 'center';
		$css[ $content_media_query ]['.fusion-author-tagline']['max-width']  = '100%';

		// # Events Calendar
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$css[ $content_media_query ]['.tribe-events-single ul.tribe-related-events li']['margin-right'] = '0';
			$css[ $content_media_query ]['.tribe-events-single ul.tribe-related-events li']['width'] = '100%';
			$css[ $content_break_point_media_query ]['.tribe-bar-collapse #tribe-bar-collapse-toggle']['width'] = '59% !important';
		}

		$sidebar_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + intval( Avada()->settings->get( 'sidebar_break_point' ) ) ) . 'px)';

		$css[ $sidebar_media_query ]['#content']['width']       = '100% !important';
		$css[ $sidebar_media_query ]['#content']['margin-left'] = '0px !important';
		$css[ $sidebar_media_query ]['.sidebar']['width']       = '100% !important';
		$css[ $sidebar_media_query ]['.sidebar']['float']       = 'none !important';
		$css[ $sidebar_media_query ]['.sidebar']['margin-left'] = '0 !important';
		$css[ $sidebar_media_query ]['.sidebar']['clear']       = 'both';

		$css[ $sidebar_media_query ]['body.has-sidebar #content .fusion-fullwidth:last-child']['margin-bottom']     = '0px !important';
		$css[ $sidebar_media_query ]['body.has-sidebar #content .fusion-layout-column:last-child']['margin-bottom'] = '0px !important';
		$css[ $sidebar_media_query ]['body.has-sidebar #content']['margin-bottom']                                  = '50px !important';
		$css[ $sidebar_media_query ]['body.has-sidebar.double-sidebars .sidebar:last-child']['margin-top']          = '50px !important';

		$retina_media_query = '@media only screen and (max-width: ' . ( intval( Avada()->settings->get( 'side_header_break_point' ) ) ) . 'px) and (-webkit-min-device-pixel-ratio: 1.5), only screen and (max-width: ' . ( intval( Avada()->settings->get( 'side_header_break_point' ) ) ) . 'px) and (min-resolution: 144dpi), only screen and (max-width: ' . ( intval( Avada()->settings->get( 'side_header_break_point' ) ) ) . 'px) and (min-resolution: 1.5dppx)';

		$elements = array(
			'.fusion-mobile-logo-1 .fusion-mobile-logo-1x',
			'#side-header .fusion-mobile-logo-1 .fusion-mobile-logo-1x',
		);
		$css[ $retina_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$elements = array(
			'.fusion-mobile-logo-1 .fusion-mobile-logo-2x',
			'#side-header .fusion-mobile-logo-1 .fusion-mobile-logo-2x',
		);
		$css[ $retina_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';

		// # WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			if ( 'horizontal' == Avada()->settings->get( 'woocommerce_product_tab_design' ) ) {

				$elements = array(
					'#wrapper .woocommerce-tabs .tabs',
					'#wrapper .woocommerce-tabs .panel',
				);

				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'none';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = 'auto';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']        = '100% !important';

				$elements = array(
					'.woocommerce-tabs .tabs',
					'.woocommerce-side-nav',
					'.woocommerce-MyAccount-navigation',
				);
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '25px';

				$css[ $content_media_query ]['.woocommerce-tabs > .tabs']['border'] = 'none';
				$css[ $content_media_query ]['.woocommerce-tabs > .wc-tab']['border-top'] = '1px solid';

				$css[ $content_media_query ]['.woocommerce-tabs > .tabs .active']['border-top'] = 'none';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs .active']['border-left'] = 'none';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs .active']['border-right'] = 'none';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs .active a']['background-color'] = 'transparent';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs li']['float'] = 'none';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs li']['border-bottom'] = '1px solid';
				$css[ $content_media_query ]['.woocommerce-tabs > .tabs li a']['padding'] = '10px 0';
			}
		}

		// # Not restructured mobile.css styles
		$css[ $content_media_query ]['#wrapper']['width'] = 'auto !important';

		$css[ $content_media_query ]['.create-block-format-context']['display'] = 'none';

		$css[ $content_media_query ]['.review']['float'] = 'none';
		$css[ $content_media_query ]['.review']['width'] = '100%';

		$elements = array(
			'.fusion-copyright-notice',
			'.fusion-body .fusion-social-links-footer',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']    = 'block';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

		$css[ $content_media_query ]['.fusion-social-links-footer']['width'] = 'auto';

		$css[ $content_media_query ]['.fusion-social-links-footer .fusion-social-networks']['display']    = 'inline-block';
		$css[ $content_media_query ]['.fusion-social-links-footer .fusion-social-networks']['float']      = 'none';
		$css[ $content_media_query ]['.fusion-social-links-footer .fusion-social-networks']['margin-top'] = '0';

		$css[ $content_media_query ]['.fusion-copyright-notice']['padding'] = '0 0 15px';

		$elements = array(
			'.fusion-copyright-notice:after',
			'.fusion-social-networks:after',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear']   = 'both';

		$elements = array(
			'.fusion-social-networks li',
			'.fusion-copyright-notice li',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';

		$css[ $content_media_query ]['.tfs-slider .fusion-title']['margin-bottom'] = '0 !important';

		$css[ $content_media_query ]['#main .cart-empty']['float']         = 'none';
		$css[ $content_media_query ]['#main .cart-empty']['text-align']    = 'center';
		$css[ $content_media_query ]['#main .cart-empty']['border-top-width'] = '1px';
		$css[ $content_media_query ]['#main .cart-empty']['border-top-style'] = 'solid';
		$css[ $content_media_query ]['#main .cart-empty']['border-bottom'] = 'none';
		$css[ $content_media_query ]['#main .cart-empty']['width']         = '100%';
		$css[ $content_media_query ]['#main .cart-empty']['line-height']   = 'normal !important';
		$css[ $content_media_query ]['#main .cart-empty']['height']        = 'auto !important';
		$css[ $content_media_query ]['#main .cart-empty']['margin-bottom'] = '10px';
		$css[ $content_media_query ]['#main .cart-empty']['padding-top']   = '10px';

		$css[ $content_media_query ]['#main .return-to-shop']['float']          = 'none';
		$css[ $content_media_query ]['#main .return-to-shop']['border-top']     = 'none';
		$css[ $content_media_query ]['#main .return-to-shop']['border-bottom-width'] = '1px';
		$css[ $content_media_query ]['#main .return-to-shop']['border-bottom-style'] = 'solid';
		$css[ $content_media_query ]['#main .return-to-shop']['width']          = '100%';
		$css[ $content_media_query ]['#main .return-to-shop']['text-align']     = 'center';
		$css[ $content_media_query ]['#main .return-to-shop']['line-height']    = 'normal !important';
		$css[ $content_media_query ]['#main .return-to-shop']['height']         = 'auto !important';
		$css[ $content_media_query ]['#main .return-to-shop']['padding-bottom'] = '10px';

		if ( class_exists( 'WooCommerce' ) ) {

			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['-webkit-justify-content'] = 'center';
			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['-ms-justify-content'] = 'center';
			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['justify-content'] = 'center';
			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['-webkit-flex-wrap'] = 'wrap';
			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['-ms-flex-wrap'] = 'wrap';
			$css[ $content_media_query ]['.woocommerce .checkout_coupon']['flex-wrap'] = 'wrap';

			$css[ $content_media_query ]['.woocommerce .checkout_coupon .promo-code-heading']['margin-bottom'] = '5px';

			$css[ $content_media_query ]['.woocommerce .checkout_coupon .coupon-contents']['margin']  = '0';
		}

		$css[ $content_media_query ]['#content.full-width']['margin-bottom'] = '0';

		$css[ $content_media_query ]['.sidebar .social_links .social li']['width']        = 'auto';
		$css[ $content_media_query ]['.sidebar .social_links .social li']['margin-right'] = '5px';

		$css[ $content_media_query ]['#comment-input']['margin-bottom'] = '0';

		$css[ $content_media_query ]['#comment-input input']['width']         = '100%';
		$css[ $content_media_query ]['#comment-input input']['float']         = 'none !important';
		$css[ $content_media_query ]['#comment-input input']['margin-bottom'] = '10px';

		$css[ $content_media_query ]['#comment-textarea textarea']['width'] = '100%';

		$css[ $content_media_query ]['.widget.facebook_like iframe']['width']     = '100% !important';
		$css[ $content_media_query ]['.widget.facebook_like iframe']['max-width'] = 'none !important';

		$css[ $content_media_query ]['.pagination']['margin-top'] = '40px';

		$css[ $content_media_query ]['.portfolio-one .portfolio-item .image']['float']         = 'none';
		$css[ $content_media_query ]['.portfolio-one .portfolio-item .image']['width']         = 'auto';
		$css[ $content_media_query ]['.portfolio-one .portfolio-item .image']['height']        = 'auto';
		$css[ $content_media_query ]['.portfolio-one .portfolio-item .image']['margin-bottom'] = '20px';

		$css[ $content_media_query ]['h5.toggle span.toggle-title']['width'] = '80%';

		$css[ $content_media_query ]['.share-box']['height'] = 'auto';

		$css[ $content_media_query ]['#wrapper .share-box h4']['float']       = 'none';
		$css[ $content_media_query ]['#wrapper .share-box h4']['line-height'] = '20px !important';
		$css[ $content_media_query ]['#wrapper .share-box h4']['margin-top']  = '0';
		$css[ $content_media_query ]['#wrapper .share-box h4']['padding']     = '0';

		$css[ $content_media_query ]['.share-box ul']['float']          = 'none';
		$css[ $content_media_query ]['.share-box ul']['overflow']       = 'hidden';
		$css[ $content_media_query ]['.share-box ul']['padding']        = '0 25px';
		$css[ $content_media_query ]['.share-box ul']['padding-bottom'] = '15px';
		$css[ $content_media_query ]['.share-box ul']['margin-top']     = '0px';

		$css[ $content_media_query ]['.project-content .project-description']['float'] = 'none !important';

		$css[ $content_media_query ]['.single-avada_portfolio .portfolio-half .project-content .project-description h3']['margin-top'] = '24px';

		$css[ $content_media_query ]['.project-content .fusion-project-description-details']['margin-bottom'] = '50px';

		$elements = array(
			'.project-content .project-description',
			'.project-content .project-info',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';

		$css[ $content_media_query ]['.portfolio-half .flexslider']['width'] = '100% !important';

		$css[ $content_media_query ]['.portfolio-half .project-content']['width'] = '100% !important';

		$css[ $content_media_query ]['#style_selector']['display'] = 'none';

		$elements = array(
			'.ls-avada .ls-nav-prev',
			'.ls-avada .ls-nav-next',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

		$css[ $content_media_query ]['#footer .social-networks']['width']    = '100%';
		$css[ $content_media_query ]['#footer .social-networks']['margin']   = '0 auto';
		$css[ $content_media_query ]['#footer .social-networks']['position'] = 'relative';
		$css[ $content_media_query ]['#footer .social-networks']['left']     = '-11px';

		$css[ $content_media_query ]['.tab-holder .tabs']['height'] = 'auto !important';
		$css[ $content_media_query ]['.tab-holder .tabs']['width']  = '100% !important';

		$css[ $content_media_query ]['.shortcode-tabs .tab-hold .tabs li']['width'] = '100% !important';

		$elements = array(
			'body .shortcode-tabs .tab-hold .tabs li',
			'body.dark .sidebar .tab-hold .tabs li',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-right'] = 'none !important';

		$css[ $content_media_query ]['.error-message']['line-height'] = '170px';
		$css[ $content_media_query ]['.error-message']['margin-top']  = '20px';

		$css[ $content_media_query ]['.error_page .useful_links']['width']        = '100%';
		$css[ $content_media_query ]['.error-page .useful_links']['padding-left'] = '0';

		$css[ $content_media_query ]['.fusion-google-map']['width']         = '100% !important';

		$css[ $content_media_query ]['.social_links_shortcode .social li']['width'] = '10% !important';

		$css[ $content_media_query ]['#wrapper .ei-slider']['width'] = '100% !important';

		$css[ $content_media_query ]['#wrapper .ei-slider']['height'] = '200px !important';

		$css[ $content_media_query ]['.popup']['display'] = 'none !important';

		$css[ $content_media_query ]['.share-box .social-networks']['text-align'] = 'left';

		if ( class_exists( 'WooCommerce' ) ) {
			$css[ $content_media_query ]['.fusion-body .products li']['width'] = '225px';

			$elements = array(
				'.products li',
				'#wrapper .catalog-ordering > ul',
				'#main .products li:nth-child(3n)',
				'#main .products li:nth-child(4n)',
				'#main .has-sidebar .products li',
				'.avada-myaccount-data .addresses .col-1',
				'.avada-myaccount-data .addresses .col-2',
				'.woocommerce-MyAccount-content .addresses .col-1',
				'.woocommerce-MyAccount-content .addresses .col-2',
				'.avada-customer-details .addresses .col-1',
				'.avada-customer-details .addresses .col-2',
			);

			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'none !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = 'auto !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto !important';

			$elements = array(
				'.avada-myaccount-data .addresses .col-1',
				'.avada-myaccount-data .addresses .col-2',
				'.woocommerce-MyAccount-content .addresses .col-1',
				'.woocommerce-MyAccount-content .addresses .col-2',
				'.avada-customer-details .addresses .col-1',
				'.avada-customer-details .addresses .col-2',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin'] = '0 !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100%';

			$css[ $content_media_query ]['#wrapper .catalog-ordering']['margin-bottom'] = '50px';

			$css[ $content_media_query ]['#wrapper .orderby-order-container']['display'] = 'flex';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['align-items'] = 'center';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['justify-content'] = 'space-between';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['margin-bottom'] = '10px';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['margin']        = '0 auto 10px auto';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['width']         = '225px';
			$css[ $content_media_query ]['#wrapper .orderby-order-container']['float']         = 'none';

			$css[ $content_media_query ]['#wrapper .order-dropdown > li:hover > ul']['display']  = 'block';
			$css[ $content_media_query ]['#wrapper .order-dropdown > li:hover > ul']['position'] = 'relative';
			$css[ $content_media_query ]['#wrapper .order-dropdown > li:hover > ul']['top']      = '0';

			$css[ $content_media_query ]['#wrapper .orderby.order-dropdown']['width']       	 = '176px';
			$css[ $content_media_query ]['#wrapper .orderby.order-dropdown']['margin']       	 = '0';
			$css[ $content_media_query ]['#wrapper .orderby.order-dropdown li a']['max-width'] = '100%';
			$css[ $content_media_query ]['#wrapper .orderby.order-dropdown']['z-index']        = '101';

			$css[ $content_media_query ]['#wrapper .sort-count.order-dropdown']['display'] = 'block';
			$css[ $content_media_query ]['#wrapper .sort-count.order-dropdown']['width'] = '225px';

			$css[ $content_media_query ]['#wrapper .sort-count.order-dropdown ul a']['width'] = '225px';

			$css[ $content_media_query ]['#wrapper .catalog-ordering .order']['margin'] = '0';

			$css[ $content_media_query ]['.catalog-ordering .fusion-grid-list-view']['display'] = 'block';
			$css[ $content_media_query ]['.catalog-ordering .fusion-grid-list-view']['width'] = '78px';

			$elements = array(
				'.woocommerce #customer_login .login .form-row',
				'.woocommerce #customer_login .login .lost_password',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';

			$elements = array(
				'.woocommerce #customer_login .login .inline',
				'.woocommerce #customer_login .login .lost_password',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']     = 'block';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = '0';

			$css[ $content_media_query ]['.avada-myaccount-data .my_account_orders .woocommerce-orders-table__cell-order-number']['padding-right'] = '8px';
			$css[ $content_media_query ]['.avada-myaccount-data .my_account_orders .woocommerce-orders-table__cell-order-actions']['padding-left'] = '8px';

			$css[ $content_media_query ]['.woocommerce-MyAccount-content .my_account_orders .woocommerce-orders-table__cell-order-number']['padding-right'] = '8px';
			$css[ $content_media_query ]['.woocommerce-MyAccount-content .my_account_orders .woocommerce-orders-table__cell-order-actions']['padding-left'] = '8px';

			$css[ $content_media_query ]['.shop_table .product-name']['width'] = '35%';

			$css[ $content_media_query ]['form.checkout .shop_table tfoot th']['padding-right'] = '20px';

			$css[ $content_media_query ]['form.checkout .shop_table tfoot th']['width'] = '60%';
			$css[ $content_media_query ]['form.checkout .shop_table tfoot td']['width'] = '40%';

			$css[ $content_media_query ]['.cart-collaterals']['-webkit-flex-direction'] = 'column';
			$css[ $content_media_query ]['.cart-collaterals']['flex-direction'] = 'column';

			$elements = array(
				'#wrapper .product .images',
				'.avada-single-product-gallery-wrapper',
				'#wrapper .product .summary.entry-summary',
				'#wrapper .woocommerce-tabs .tabs',
				'#wrapper .woocommerce-tabs .panel',
				'#wrapper .woocommerce-side-nav',
				'#wrapper .woocommerce-content-box',
				'.fusion-body .cart-collaterals .cart_totals',
				'.fusion-body .woocommerce-MyAccount-navigation',
				'.fusion-body .woocommerce-MyAccount-content',
				'#wrapper .shipping-coupon',
				'#wrapper .cart-totals-buttons',
				'#wrapper #customer_login .col-1',
				'#wrapper #customer_login .col-2',
				'#wrapper .woocommerce form.checkout #customer_details .col-1',
				'#wrapper .woocommerce form.checkout #customer_details .col-2',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'none';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = 'auto';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']        = '100% !important';

			if ( Avada()->settings->get( 'disable_woo_gallery' ) ) {
				$css[ $content_media_query ]['.product .entry-summary .summary-container']['margin-top']  = '20px';
			}

			$elements = array(
				'#customer_login .col-1',
				'.coupon',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '30px';

			$css[ $content_media_query ]['.shop_table .product-thumbnail']['float'] = 'none';

			$css[ $content_media_query ]['.product-info']['margin-left'] = '0';
			$css[ $content_media_query ]['.product-info']['margin-top']  = '10px';

			$css[ $content_media_query ]['.product .entry-summary div .price']['float'] = 'none';

			$css[ $content_media_query ]['.product .entry-summary .woocommerce-product-rating']['float']       = 'none';
			$css[ $content_media_query ]['.product .entry-summary .woocommerce-product-rating']['margin-left'] = '0';

			$elements = array(
				'.woocommerce-tabs .tabs',
				'.woocommerce-side-nav',
				'.woocommerce-MyAccount-navigation',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '25px';

			$css[ $content_media_query ]['.woocommerce-tabs .panel']['width']   = '91% !important';
			$css[ $content_media_query ]['.woocommerce-tabs .panel']['padding'] = '4% !important';

			$css[ $content_media_query ]['#reviews li .avatar']['display'] = 'none';

			$css[ $content_media_query ]['#reviews li .comment-text']['width']       = '90% !important';
			$css[ $content_media_query ]['#reviews li .comment-text']['margin-left'] = '0 !important';
			$css[ $content_media_query ]['#reviews li .comment-text']['padding']     = '5% !important';

			$css[ $content_media_query ]['html .woocommerce .woocommerce-container .social-share']['display'] = 'block';
			$css[ $content_media_query ]['.woocommerce-container .social-share']['overflow'] = 'hidden';

			$css[ $content_media_query ]['.woocommerce-container .social-share li']['display']       = 'block';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['float']         = 'left';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['margin']        = '0 auto';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['border-right']  = '0 !important';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['border-left']   = '0 !important';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['padding-left']  = '0 !important';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['padding-right'] = '0 !important';
			$css[ $content_media_query ]['.woocommerce-container .social-share li']['width']         = '50%';

			$css[ $content_media_query ]['.has-sidebar .woocommerce-container .social-share li']['width'] = '50%';

			$css[ $content_media_query ]['.avada-myaccount-user']['display'] = 'block';
			$css[ $content_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['width']   = '100%';
			$css[ $content_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['display'] = 'block';
			$css[ $content_media_query ]['.avada-myaccount-user .username .not-user']['display'] = 'inline';
			$css[ $content_media_query ]['.fusion-body .avada-myaccount-user .username .not-user']['padding'] = '0 5px;';
			$css[ $content_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['padding'] = '5px 0px';

			$css[ $content_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['border-right'] = 0;

			$css[ $content_media_query ]['.avada-myaccount-user']['padding-top']       = '10px';
			$css[ $content_media_query ]['.avada-myaccount-user']['padding-bottom']    = '10px';

			if ( is_rtl() ) {
				$css[ $content_media_query ]['.rtl .avada-myaccount-user .avada-myaccount-user-column']['border-left'] = '0';
			}

			$elements = array(
				'.shop_table .product-thumbnail img',
				'.shop_table .product-thumbnail .product-info',
				'.shop_table .product-thumbnail .product-info p',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']   = '100%';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']  = '0 !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '0';

			$css[ $content_media_query ]['.shop_table .product-thumbnail']['padding'] = '10px 0px';

			$css[ $content_media_query ]['.product .images']['margin-bottom'] = '30px';

			$css[ $content_media_query ]['#customer_login_box .button']['float']         = 'left';
			$css[ $content_media_query ]['#customer_login_box .button']['margin-bottom'] = '15px';

			$css[ $content_media_query ]['#customer_login_box .remember-box']['clear']   = 'both';
			$css[ $content_media_query ]['#customer_login_box .remember-box']['display'] = 'block';
			$css[ $content_media_query ]['#customer_login_box .remember-box']['padding'] = '0';
			$css[ $content_media_query ]['#customer_login_box .remember-box']['width']   = '125px';
			$css[ $content_media_query ]['#customer_login_box .remember-box']['float']   = 'left';

			$css[ $content_media_query ]['#customer_login_box .lost_password']['float'] = 'left';

		} // End if().

		if ( defined( 'WPCF7_PLUGIN' ) ) {

			$elements = array(
				'.wpcf7-form .wpcf7-text',
				'.wpcf7-form .wpcf7-quiz',
				'.wpcf7-form .wpcf7-number',
				'.wpcf7-form textarea',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']      = '100% !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['box-sizing'] = 'border-box';

		}

		if ( class_exists( 'GFForms' ) ) {
			$elements = array(
				'.gform_wrapper .right_label input.medium',
				'.gform_wrapper .right_label select.medium',
				'.gform_wrapper .left_label input.medium',
				'.gform_wrapper .left_label select.medium',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '35% !important';
		}

		$elements = array(
			'.product .images #slider .flex-direction-nav',
			'.product .images #carousel .flex-direction-nav',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

		if ( class_exists( 'WooCommerce' ) ) {
			$elements = array(
				'.avada-myaccount-user .avada-myaccount-user-column',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0 !important';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0 !important';
		}

		$css[ $content_media_query ]['.fullwidth-box']['background-attachment'] = 'scroll !important';
		$css[ $content_media_query ]['.fullwidth-box .fullwidth-faded']['background-attachment'] = 'scroll !important';

		$css[ $content_media_query ]['#toTop']['bottom']        = '30px';
		$css[ $content_media_query ]['#toTop']['border-radius'] = '4px';
		$css[ $content_media_query ]['#toTop']['height']        = '40px';
		$css[ $content_media_query ]['#toTop']['z-index']       = '10000';

		$css[ $content_media_query ]['#toTop:before']['line-height'] = '38px';

		$css[ $content_media_query ]['#toTop:hover']['background-color'] = '#333333';

		$css[ $content_media_query ]['.no-mobile-totop .to-top-container']['display'] = 'none';

		$css[ $content_media_query ]['.no-mobile-slidingbar #slidingbar-area']['display'] = 'none';

		$css[ $content_media_query ]['.no-mobile-slidingbar.mobile-logo-pos-left .mobile-menu-icons']['margin-right'] = '0';

		if ( is_rtl() ) {
			$css[ $content_media_query ]['.rtl.no-mobile-slidingbar.mobile-logo-pos-right .mobile-menu-icons']['margin-left'] = '0';
		}

		$css[ $content_media_query ]['.tfs-slider .slide-content-container .btn']['min-height']    = '0 !important';
		$css[ $content_media_query ]['.tfs-slider .slide-content-container .btn']['padding-left']  = '30px';
		$css[ $content_media_query ]['.tfs-slider .slide-content-container .btn']['padding-right'] = '30px !important';
		$css[ $content_media_query ]['.tfs-slider .slide-content-container .btn']['height']        = '26px !important';
		$css[ $content_media_query ]['.tfs-slider .slide-content-container .btn']['line-height']   = '26px !important';

		$css[ $content_media_query ]['.fusion-soundcloud iframe']['width'] = '100%';

		$elements = array(
			'.ua-mobile .fusion-page-title-bar',
			'.ua-mobile .footer-area',
			'.ua-mobile body',
			'.ua-mobile #main',
		);
		$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'scroll !important';

		if ( class_exists( 'RevSliderFront' ) ) {
			$css[ $content_media_query ]['.fusion-revslider-mobile-padding']['padding-left']  = '30px !important';
			$css[ $content_media_query ]['.fusion-revslider-mobile-padding']['padding-right'] = '30px !important';
		}

		// # Events Calendar
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			if ( ! is_rtl() ) {
				$css[ $content_media_query ]['.tribe-events-single ul.tribe-related-events .tribe-related-events-thumbnail']['float'] = 'left';
				$css[ $content_media_query ]['.tribe-events-single ul.tribe-related-events li .tribe-related-event-info']['padding-left'] = '10px';
				$css[ $content_media_query ]['.tribe-events-single ul.tribe-related-events li .tribe-related-event-info']['padding-right'] = '0';
			}

			if ( ( Avada()->settings->get( 'main_padding', 'top' ) || Avada()->settings->get( 'main_padding', 'top' ) == '0' ) && ! get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) && get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) != '0' ) {
				$css['global']['.tribe-mobile #main']['padding-top'] = Fusion_Sanitize::size( Avada()->settings->get( 'main_padding', 'top' ) );
			} elseif ( get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) ) {
				$css['global']['.tribe-mobile #main']['padding-top'] = get_post_meta( $c_page_id, 'pyre_main_top_padding', true );
			} else {
				$css['global']['.tribe-mobile #main']['padding-top'] = '55px !important';
			}

			// Filter.
			$elements = array(
				'#tribe-events-bar #tribe-bar-views .tribe-bar-views-inner label',
				'#tribe-events-bar #tribe-bar-views .tribe-bar-views-inner .tribe-bar-views-option a',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left'] = '15px';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '15px';

			$elements = array(
				'#tribe-events-bar .tribe-bar-filters .tribe-bar-date-filter',
				'#tribe-events-bar .tribe-bar-filters .tribe-bar-search-filter',
				'#tribe-events-bar .tribe-bar-filters .tribe-bar-geoloc-filter',
				'#tribe-events-bar .tribe-bar-filters .tribe-bar-submit',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left'] = '0';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-top'] = '15px';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-bottom'] = '15px';

			// Title and Navigation.
			$css[ $content_media_query ]['#tribe-events-content #tribe-events-header']['margin-bottom'] = '30px';

			$elements = array(
				'.tribe-events-list .fusion-events-before-title',
				'.tribe-events-month .fusion-events-before-title',
				'.tribe-events-week .fusion-events-before-title',
				'.tribe-events-day .fusion-events-before-title',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height'] = '100px';
			$css[ $content_media_query ]['.tribe-events-list.tribe-events-map .fusion-events-before-title']['height'] = 'auto';

			$css[ $content_media_query ]['#tribe-events-content #tribe-events-header .tribe-events-sub-nav li']['margin-top'] = '-40px';

			// Events Archive.
			// List View.
			$css[ $content_media_query ]['.tribe-events-loop .tribe-events-event-meta']['padding'] = '0';
			$css[ $content_media_query ]['#tribe-events .tribe-events-list .tribe-events-event-meta .author > div']['display'] = 'block';
			$css[ $content_media_query ]['#tribe-events .tribe-events-list .tribe-events-event-meta .author > div']['border-right'] = 'none';
			$css[ $content_media_query ]['#tribe-events .tribe-events-list .tribe-events-event-meta .author > div']['width'] = '100%';

			$elements = array(
				'#tribe-events .tribe-events-list .fusion-tribe-primary-info',
				'#tribe-events .tribe-events-list .fusion-tribe-secondary-info',
				'#tribe-events .tribe-events-list .fusion-tribe-no-featured-image .fusion-tribe-events-headline',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';

			$elements = array(
				'.tribe-events-list .tribe-events-venue-details',
				'.tribe-events-list .time-details',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin'] = '0';

			// Month View.
			$css[ $content_media_query ]['.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"] > a']['background'] = 'none';

			// Photo View.
			$css[ $content_media_query ]['.tribe-events-list .time-details']['padding'] = '0';

			// Single Event Page.
			$elements = array(
				'.fusion-events-featured-image .fusion-events-single-title-content h2',
				'.fusion-events-featured-image .fusion-events-single-title-content .tribe-events-schedule',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';

			$elements = array(
				'#tribe-events .tribe-events-list .type-tribe_events .tribe-events-event-image'
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

			$elements = array(
				'#tribe-events .tribe-events-list .type-tribe_events .fusion-tribe-events-event-image-responsive'
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

			$css[ $content_media_query ]['table.tribe-events-tickets > tbody tr']['display'] = 'block';
			$css[ $content_media_query ]['table.tribe-events-tickets > tbody tr']['padding'] = '0';
			$css[ $content_media_query ]['table.tribe-events-tickets > tbody tr']['border'] = 'none';

			$css[ $content_media_query ]['table.tribe-events-tickets-rsvp .tribe-tickets-table']['width'] = '100%';

			$elements = array(
				'table.tribe-events-tickets tr .woocommerce',
				'table.tribe-events-tickets tr .tribe-ticket.quantity',
				'table.tribe-events-tickets tr .tickets_name',
				'table.tribe-events-tickets tr .tickets_description',
				'table.tribe-events-tickets tr .tickets_price',
				'table.tribe-events-tickets tr td.add-to-cart',
				'table.tribe-events-tickets tr td.tribe-tickets-attendees',
				'table.tribe-events-tickets .tribe-tickets-attendees-list-optout > td',
			);
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-top'] = 'none';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-left'] = 'none';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-right'] = 'none';
			$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom'] = '1px solid';

			$css[ $content_media_query ]['table.tribe-events-tickets tr td.add-to-cart']['border-bottom'] = 'none';

			$css[ $content_media_query ]['.fusion-body .tribe-events-tickets-rsvp .quantity input']['padding'] = '0';

		} // End if().

		if ( class_exists( 'WooCommerce' ) ) {
			// $css[ $content_media_query ]['.fusion-woo-slider .fusion-carousel-title-on-rollover .fusion-rollover-title']['display'] = 'none';
			$css[ $content_media_query ]['.fusion-woo-slider .fusion-carousel-title-on-rollover .fusion-rollover-categories']['display'] = 'none';
			$css[ $content_media_query ]['.fusion-woo-slider .fusion-carousel-title-on-rollover .price']['display'] = 'none';
		}

		/*
		@media only screen and ( min-width: $content_break_point )
		*/
		$content_min_media_query = '@media only screen and (min-width: ' . ( intval( $side_header_width ) + intval( Avada()->settings->get( 'content_break_point' ) ) ) . 'px)';

		// # WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			if ( 'horizontal' == Avada()->settings->get( 'woocommerce_product_tab_design' ) ) {

				$elements = array(
					'.woocommerce-MyAccount-navigation > ul',
					'.woocommerce-checkout-nav',
				);
				foreach ( $elements as $selector ) {
					$css[ $content_min_media_query ][ $selector ]['width'] = '100%';
					if ( is_rtl() ) {
						$css[ $content_min_media_query ][ $selector . ' li' ]['float'] = 'right';
					} else {
						$css[ $content_min_media_query ][ $selector . ' li' ]['float'] = 'left';
					}
					$css[ $content_min_media_query ][ $selector . ' li a' ]['border']      = '1px solid transparent !important';
					$css[ $content_min_media_query ][ $selector . ' li a' ]['padding']     = '10px 20px';
					$css[ $content_min_media_query ][ $selector . ' li a' ]['height']      = 'auto';
					$css[ $content_min_media_query ][ $selector . ' li a' ]['line-height'] = 'normal';
					$css[ $content_min_media_query ][ $selector . ' li a:after' ]['display'] = 'none';
					$css[ $content_min_media_query ][ $selector . ' .is-active' ]['border']        = '1px solid #dddddd';
					$css[ $content_min_media_query ][ $selector . ' .is-active' ]['border-bottom'] = 'none';
					$css[ $content_min_media_query ][ $selector . ' .is-active' ]['min-height']    = '40px';
					$css[ $content_min_media_query ][ $selector . ' .is-active' ]['margin-bottom'] = '-1px';
					$css[ $content_min_media_query ][ $selector . ' .is-active:hover a' ]['cursor'] = 'default';
				}

				$css['global']['.woocommerce-content-box.avada-checkout']['margin'] = '0';
				$css['global']['.woocommerce-content-box.avada-checkout']['clear'] = 'both';
				$css['global']['.woocommerce-MyAccount-navigation']['width'] = '100%';
				$css['global']['.fusion-body .woocommerce-MyAccount-content']['margin'] = '0';
			}
		}

		/*
		@media only screen and and (max-device-width : 740px) and (orientation : landscape)
		*/
		$seven_fourty_media_query = '@media only screen and (max-device-width : 740px) and (orientation : landscape)';

		// # Footer Styles.
		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$css[ $seven_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$css[ $seven_fourty_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$css[ $seven_fourty_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$css[ $seven_fourty_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$css[ $seven_fourty_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		/*
		@media only screen and ( max-width: 640px )
		*/
		$six_fourty_media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + 640 ) . 'px)';

		// # Page Title Bar
		$css[ $six_fourty_media_query ]['.fusion-body .fusion-page-title-bar']['max-height'] = 'none';

		$css[ $six_fourty_media_query ]['.fusion-body .fusion-page-title-bar h1']['margin'] = '0';

		// # Blog Layouts.
		// Blog general styles.
		$elements = array(
			'.fusion-blog-layout-large .fusion-meta-info .fusion-alignleft',
			'.fusion-blog-layout-medium .fusion-meta-info .fusion-alignleft',
			'.fusion-blog-layout-large .fusion-meta-info .fusion-alignright',
			'.fusion-blog-layout-medium .fusion-meta-info .fusion-alignright',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']  = '0';
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']   = '100%';

		// Blog medium layout.
		$css[ $six_fourty_media_query ]['.fusion-body .fusion-blog-layout-medium .fusion-post-slideshow']['float']  = 'none';
		$css[ $six_fourty_media_query ]['.fusion-body .fusion-blog-layout-medium .fusion-post-slideshow']['margin'] = '0 0 20px 0';
		$css[ $six_fourty_media_query ]['.fusion-body .fusion-blog-layout-medium .fusion-post-slideshow']['height'] = 'auto';
		$css[ $six_fourty_media_query ]['.fusion-body .fusion-blog-layout-medium .fusion-post-slideshow']['width']  = 'auto';

		// Blog large alternate layout.
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-large-alternate .fusion-date-and-formats']['margin-bottom'] = '55px';

		$css[ $six_fourty_media_query ]['.fusion-body .fusion-blog-layout-large-alternate .fusion-post-content']['margin'] = '0';

		// Blog medium alternate layout.
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-medium-alternate .has-post-thumbnail .fusion-post-slideshow']['display']      = 'inline-block';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-medium-alternate .has-post-thumbnail .fusion-post-slideshow']['float']        = 'none';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-medium-alternate .has-post-thumbnail .fusion-post-slideshow']['margin-right'] = '0';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-medium-alternate .has-post-thumbnail .fusion-post-slideshow']['max-width']    = '197px';

		// Blog grid layout.
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-grid .fusion-post-grid']['position'] = 'static';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-grid .fusion-post-grid']['width']    = '100%';

		// # Footer Styles
		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$css[ $six_fourty_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$css[ $six_fourty_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$css[ $six_fourty_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$css[ $six_fourty_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		// # Not restructured mobile.css styles
		$elements = array(
			'.wooslider-direction-nav',
			'.wooslider-pauseplay',
			'.flex-direction-nav',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css[ $six_fourty_media_query ]['.share-box ul li']['margin-bottom'] = '10px';
		$css[ $six_fourty_media_query ]['.share-box ul li']['margin-right']  = '15px';

		$css[ $six_fourty_media_query ]['.buttons a']['margin-right'] = '5px';

		$elements = array(
			'.ls-avada .ls-nav-prev',
			'.ls-avada .ls-nav-next',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

		$css[ $six_fourty_media_query ]['#wrapper .ei-slider']['width']  = '100% !important';
		$css[ $six_fourty_media_query ]['#wrapper .ei-slider']['height'] = '200px !important';

		$css[ $six_fourty_media_query ]['.page-template-contact-php .fusion-google-map']['height'] = '270px !important';

		$css[ $six_fourty_media_query ]['.share-box .social-networks li']['margin-right'] = '20px !important';

		$css[ $six_fourty_media_query ]['.timeline-icon']['display'] = 'none !important';

		$css[ $six_fourty_media_query ]['.timeline-layout']['padding-top'] = '0 !important';

		$css[ $six_fourty_media_query ]['.post-content .wooslider .wooslider-control-thumbs']['margin-top'] = '-10px';

		$css[ $six_fourty_media_query ]['body .wooslider .overlay-full.layout-text-left .slide-excerpt']['padding'] = '20px !important';

		$css[ $six_fourty_media_query ]['.social_links_shortcode li']['height'] = '40px !important';

		$css[ $six_fourty_media_query ]['.products-slider .es-nav span']['transform'] = 'scale(0.5) !important';

		if ( class_exists( 'WooCommerce' ) ) {

			$css[ $six_fourty_media_query ]['.shop_table .product-quantity']['display'] = 'none';

			$css[ $six_fourty_media_query ]['.shop_table .filler-td']['display'] = 'none';

			$css[ $six_fourty_media_query ]['.my_account_orders .woocommerce-orders-table__cell-order-status']['display'] = 'none';
			$css[ $six_fourty_media_query ]['.my_account_orders .woocommerce-orders-table__cell-order-date']['display'] = 'none';

			$css[ $six_fourty_media_query ]['.woocommerce-MyAccount-downloads .download-remaining']['display'] = 'none';
			$css[ $six_fourty_media_query ]['.woocommerce-MyAccount-downloads .download-expires']['display'] = 'none';

			$css[ $six_fourty_media_query ]['.my_account_orders .woocommerce-orders-table__cell-order-number time']['display']     = 'block !important';
			$css[ $six_fourty_media_query ]['.my_account_orders .woocommerce-orders-table__cell-order-number time']['font-size']   = '10px';
			$css[ $six_fourty_media_query ]['.my_account_orders .woocommerce-orders-table__cell-order-number time']['line-height'] = 'normal';
		}

		$media_query = '@media only screen and (min-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';

		if ( class_exists( 'bbPress' ) ) {

			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-avatar img.avatar']['width']  = '80px !important';
			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-avatar img.avatar']['height'] = '80px !important';

			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-avatar']['width'] = '80px !important';

			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-navigation']['margin-left'] = '110px !important';

			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-navigation .first-col']['width'] = '47% !important';

			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-navigation .second-col']['margin-left'] = '53% !important';
			$css[ $six_fourty_media_query ]['#bbpress-forums #bbp-single-user-details #bbp-user-navigation .second-col']['width']       = '47% !important';

		}
		$css[ $six_fourty_media_query ]['.portfolio-masonry .portfolio-item']['width'] = '100% !important';
		$elements = array(
			'.table-1 table',
			'.tkt-slctr-tbl-wrap-dv table',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-collapse'] = 'collapse';
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-spacing']  = '0';
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']           = '100%';

		$elements = array(
			'.table-1 td',
			'.table-1 th',
			'.tkt-slctr-tbl-wrap-dv td',
			'.tkt-slctr-tbl-wrap-dv th',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['white-space'] = 'nowrap';

		$css[ $six_fourty_media_query ]['.table-2 table']['border-collapse'] = 'collapse';
		$css[ $six_fourty_media_query ]['.table-2 table']['border-spacing']  = '0';
		$css[ $six_fourty_media_query ]['.table-2 table']['width']           = '100%';

		$elements = array(
			'.table-2 td',
			'.table-2 th',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['white-space'] = 'nowrap';

		$elements = array(
			'.page-title-bar',
			'.footer-area',
			'body',
			'#main',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'scroll !important';

		$css[ $six_fourty_media_query ]['.tfs-slider[data-animation="slide"]']['height'] = 'auto !important';

		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['display']       = 'block';
		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['float']         = 'none';
		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['line-height']   = '20px !important';
		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['margin-top']    = '0';
		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['padding']       = '0';
		$css[ $six_fourty_media_query ]['#wrapper .share-box h4']['margin-bottom'] = '10px';

		$css[ $six_fourty_media_query ]['.fusion-sharing-box .fusion-social-networks']['float']      = 'none';
		$css[ $six_fourty_media_query ]['.fusion-sharing-box .fusion-social-networks']['display']    = 'block';
		$css[ $six_fourty_media_query ]['.fusion-sharing-box .fusion-social-networks']['width']      = '100%';
		$css[ $six_fourty_media_query ]['.fusion-sharing-box .fusion-social-networks']['text-align'] = 'left';

		$css[ $six_fourty_media_query ]['#content']['width']        = '100% !important';
		$css[ $six_fourty_media_query ]['#content']['margin-left'] = '0px !important';

		$css[ $six_fourty_media_query ]['.sidebar']['width']       = '100% !important';
		$css[ $six_fourty_media_query ]['.sidebar']['float']       = 'none !important';
		$css[ $six_fourty_media_query ]['.sidebar']['margin-left'] = '0 !important';
		$css[ $six_fourty_media_query ]['.sidebar']['clear']       = 'both';

		$css[ $six_fourty_media_query ]['.fusion-hide-on-mobile']['display'] = 'none';

		// Blog timeline layout.
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-timeline']['padding-top'] = '0';

		$css[ $six_fourty_media_query ]['.fusion-blog-layout-timeline .fusion-post-timeline']['float'] = 'none';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-timeline .fusion-post-timeline']['width'] = '100%';

		$css[ $six_fourty_media_query ]['.fusion-blog-layout-timeline .fusion-timeline-date']['margin-bottom'] = '0';
		$css[ $six_fourty_media_query ]['.fusion-blog-layout-timeline .fusion-timeline-date']['margin-top']    = '2px';

		$elements = array(
			'.fusion-timeline-icon',
			'.fusion-timeline-line',
			'.fusion-timeline-circle',
			'.fusion-timeline-arrow',
		);
		$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		if ( class_exists( 'WooCommerce' ) ) {
			if ( 'clean' == Avada()->settings->get( 'woocommerce_product_box_design' ) ) {
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons']['height'] = 'auto';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons']['margin-top'] = '0';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons *']['display'] = 'block';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons *']['text-align'] = 'center';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons *']['float'] = 'none !important';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons *']['max-width'] = '100%';
				$css[ $six_fourty_media_query ]['.fusion-woo-slider .fusion-clean-product-image-wrapper .fusion-product-buttons *']['margin-top'] = '0';
			}

			$css[ $six_fourty_media_query ]['.group_table .quantity']['width'] = '78px';
			$css[ $six_fourty_media_query ]['.group_table td.label']['font-size'] = '12px';
			$css[ $six_fourty_media_query ]['.group_table .price .woocommerce-Price-amount']['font-size'] = '14px';
		}

		/*
		@media only screen and ( max-width: 480px )
		*/
		if ( class_exists( 'bbPress' ) ) {
			$four_eigthy_media_query = '@media only screen and (max-width: 480px)';

			$css[ $four_eigthy_media_query ]['#bbpress-forums .bbp-body div.bbp-reply-author']['width'] = '71% !important';
			$css[ $four_eigthy_media_query ]['.bbp-arrow']['display'] = 'none';
			$css[ $four_eigthy_media_query ]['div.bbp-submit-wrapper']['float'] = 'right !important';
		}

		if ( class_exists( 'GFForms' ) ) {
			$four_eigthy_media_query = '@media all and (max-width: 480px), all and (max-device-width: 480px)';

			$elements = array(
				'body.fusion-body .gform_wrapper .ginput_container',
				'body.fusion-body .gform_wrapper div.ginput_complex',
				'body.fusion-body .gform_wrapper div.gf_page_steps',
				'body.fusion-body .gform_wrapper div.gf_page_steps div',
				'body.fusion-body .gform_wrapper .ginput_container input.small',
				'body.fusion-body .gform_wrapper .ginput_container input.medium',
				'body.fusion-body .gform_wrapper .ginput_container input.large',
				'body.fusion-body .gform_wrapper .ginput_container select.small',
				'body.fusion-body .gform_wrapper .ginput_container select.medium',
				'body.fusion-body .gform_wrapper .ginput_container select.large',
				'body.fusion-body .gform_wrapper .ginput_container textarea.small',
				'body.fusion-body .gform_wrapper .ginput_container textarea.medium',
				'body.fusion-body .gform_wrapper .ginput_container textarea.large',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="text"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="url"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="email"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="tel"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="number"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_right input[type="password"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="text"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="url"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="email"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="tel"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="number"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_left input[type="password"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="text"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="url"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="email"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="tel"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="number"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full input[type="password"]',
				'body.fusion-body .gform_wrapper .ginput_complex .ginput_full select',
				'body.fusion-body .gform_wrapper input.gform_button.button',
				'body.fusion-body .gform_wrapper input[type="submit"]',
				'body.fusion-body .gform_wrapper .gfield_time_hour input',
				'body.fusion-body .gform_wrapper .gfield_time_minute input',
				'body.fusion-body .gform_wrapper .gfield_date_month input',
				'body.fusion-body .gform_wrapper .gfield_date_day input',
				'body.fusion-body .gform_wrapper .gfield_date_year input',
				'.gfield_time_ampm .gravity-select-parent',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="text"]',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="url"]',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="email"]',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="tel"]',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="number"]',
				'body.fusion-body .gform_wrapper .ginput_complex input[type="password"]',
				'body.fusion-body .gform_wrapper .ginput_complex .gravity-select-parent',
				'body.fusion-body .gravity-select-parent',
			);
			$css[ $four_eigthy_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';
			$elements = array(
				'.gform_wrapper .gform_page_footer input[type="button"]',
				'.gform_wrapper .gform_button',
				'.gform_wrapper .button',
			);
			$css[ $four_eigthy_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['-webkit-box-sizing'] = 'border-box';
			$css[ $four_eigthy_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['box-sizing']         = 'border-box';

		} // End if().

		/*
		@media only screen and (min-device-width: 320px) and (max-device-width: 640px)
		*/
		$three_twenty_six_fourty_media_query = '@media only screen and (min-device-width: 320px) and (max-device-width: 640px)';

		// # Layout
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper']['width']      = 'auto !important';
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper']['overflow-x'] = 'hidden !important';

		$css[ $three_twenty_six_fourty_media_query ]['.fusion-columns .fusion-column']['float']      = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['.fusion-columns .fusion-column']['width']      = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['.fusion-columns .fusion-column']['margin']     = '0 0 50px';
		$css[ $three_twenty_six_fourty_media_query ]['.fusion-columns .fusion-column']['box-sizing'] = 'border-box';

		$elements = array(
			'.footer-area .fusion-columns .fusion-column',
			'#slidingbar-area .fusion-columns .fusion-column',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'left';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '98% !important';

		$css[ $three_twenty_six_fourty_media_query ]['.fullwidth-box']['background-attachment'] = 'scroll !important';
		$css[ $three_twenty_six_fourty_media_query ]['.fullwidth-box .fullwidth-faded']['background-attachment'] = 'scroll !important';
		$css[ $three_twenty_six_fourty_media_query ]['.no-mobile-totop .to-top-container']['display'] = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['.no-mobile-slidingbar #slidingbar-area']['display'] = 'none';

		$css[ $three_twenty_six_fourty_media_query ]['.review']['float'] = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['.review']['width'] = '100%';

		$elements = array(
			'.social-networks',
			'.copyright',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding']    = '0 0 15px';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

		$elements = array(
			'.copyright:after',
			'.social-networks:after',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['content'] = '""';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear']   = 'both';

		$elements = array(
			'.social-networks li',
			'.copyright li',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'inline-block';

		$css[ $three_twenty_six_fourty_media_query ]['.continue']['display'] = 'none';

		$css[ $three_twenty_six_fourty_media_query ]['.mobile-button']['display'] = 'block !important';
		$css[ $three_twenty_six_fourty_media_query ]['.mobile-button']['float']   = 'none';

		$css[ $three_twenty_six_fourty_media_query ]['.title']['margin-top']    = '0px !important';
		$css[ $three_twenty_six_fourty_media_query ]['.title']['margin-bottom'] = '20px !important';

		$css[ $three_twenty_six_fourty_media_query ]['#content']['width']         = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['#content']['float']         = 'none !important';
		$css[ $three_twenty_six_fourty_media_query ]['#content']['margin-left']   = '0 !important';
		$css[ $three_twenty_six_fourty_media_query ]['#content']['margin-bottom'] = '50px';

		$css[ $three_twenty_six_fourty_media_query ]['#content.full-width']['margin-bottom'] = '0';

		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['width'] = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['float'] = 'none !important';

		$css[ $three_twenty_six_fourty_media_query ]['.sidebar .social_links .social li']['width']        = 'auto';
		$css[ $three_twenty_six_fourty_media_query ]['.sidebar .social_links .social li']['margin-right'] = '5px';

		$css[ $three_twenty_six_fourty_media_query ]['#comment-input']['margin-bottom'] = '0';

		$css[ $three_twenty_six_fourty_media_query ]['.widget.facebook_like iframe']['width']     = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['.widget.facebook_like iframe']['max-width'] = 'none !important';

		$css[ $three_twenty_six_fourty_media_query ]['.pagination']['margin-top'] = '40px';

		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-one .portfolio-item .image']['float']         = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-one .portfolio-item .image']['width']         = 'auto';
		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-one .portfolio-item .image']['height']        = 'auto';
		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-one .portfolio-item .image']['margin-bottom'] = '20px';

		$css[ $three_twenty_six_fourty_media_query ]['h5.toggle span.toggle-title']['width'] = '80%';

		$css[ $three_twenty_six_fourty_media_query ]['.share-box']['height'] = 'auto';

		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .share-box h4']['float']       = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .share-box h4']['line-height'] = '20px !important';
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .share-box h4']['margin-top']  = '0';
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .share-box h4']['padding']     = '0';

		$css[ $three_twenty_six_fourty_media_query ]['.share-box ul']['float']          = 'none';
		$css[ $three_twenty_six_fourty_media_query ]['.share-box ul']['overflow']       = 'hidden';
		$css[ $three_twenty_six_fourty_media_query ]['.share-box ul']['padding']        = '0 25px';
		$css[ $three_twenty_six_fourty_media_query ]['.share-box ul']['padding-bottom'] = '25px';
		$css[ $three_twenty_six_fourty_media_query ]['.share-box ul']['margin-top']     = '0px';

		$css[ $three_twenty_six_fourty_media_query ]['.project-content .project-description']['float'] = 'none !important';

		$css[ $three_twenty_six_fourty_media_query ]['.project-content .fusion-project-description-details']['margin-bottom'] = '50px';

		$elements = array(
			'.project-content .project-description',
			'.project-content .project-info',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';

		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-half .flexslider']['width'] = '100% !important';

		$css[ $three_twenty_six_fourty_media_query ]['.portfolio-half .project-content']['width'] = '100% !important';

		$css[ $three_twenty_six_fourty_media_query ]['#style_selector']['display'] = 'none';

		$elements = array(
			'.ls-avada .ls-nav-prev',
			'.ls-avada .ls-nav-next',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

		$css[ $three_twenty_six_fourty_media_query ]['#footer .social-networks']['width']    = '100%';
		$css[ $three_twenty_six_fourty_media_query ]['#footer .social-networks']['margin']   = '0 auto';
		$css[ $three_twenty_six_fourty_media_query ]['#footer .social-networks']['position'] = 'relative';
		$css[ $three_twenty_six_fourty_media_query ]['#footer .social-networks']['left']     = '-11px';

		$css[ $three_twenty_six_fourty_media_query ]['.recent-works-items a']['max-width'] = '64px';

		$elements = array(
			'.footer-area .flickr_badge_image img',
			'#slidingbar-area .flickr_badge_image img',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = '64px';
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding']   = '3px !important';

		$css[ $three_twenty_six_fourty_media_query ]['.tab-holder .tabs']['height'] = 'auto !important';
		$css[ $three_twenty_six_fourty_media_query ]['.tab-holder .tabs']['width']  = '100% !important';

		$css[ $three_twenty_six_fourty_media_query ]['.shortcode-tabs .tab-hold .tabs li']['width'] = '100% !important';

		$elements = array(
			'body .shortcode-tabs .tab-hold .tabs li',
			'body.dark .sidebar .tab-hold .tabs li',
		);
		$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-right'] = 'none !important';

		$css[ $three_twenty_six_fourty_media_query ]['.error_page .useful_links']['width']        = '100%';
		$css[ $three_twenty_six_fourty_media_query ]['.error_page .useful_links']['padding-left'] = '0';

		$css[ $three_twenty_six_fourty_media_query ]['.fusion-google-map']['width']         = '100% !important';

		$css[ $three_twenty_six_fourty_media_query ]['.social_links_shortcode .social li']['width'] = '10% !important';

		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .ei-slider']['width']  = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['#wrapper .ei-slider']['height'] = '200px !important';

		$css[ $three_twenty_six_fourty_media_query ]['.share-box .social-networks']['text-align'] = 'left';

		$css[ $three_twenty_six_fourty_media_query ]['#content']['width']       = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['#content']['margin-left'] = '0px !important';

		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['width']       = '100% !important';
		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['float']       = 'none !important';
		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['margin-left'] = '0 !important';
		$css[ $three_twenty_six_fourty_media_query ]['.sidebar']['clear']       = 'both';
		$css[ $three_twenty_six_fourty_media_query ]['#cloudsCandy']['height']	= '40px';
		$css[ $three_twenty_six_fourty_media_query ]['#cloudsCandy>path:nth-child(2n+2)']['opacity']	= '0';

		/*
		Media queries - media.css CSS - to be split to the corresponding sections above
		*/

		if ( class_exists( 'WooCommerce' ) ) {

			$media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + 965 ) . 'px)';

			$css[ $media_query ]['.avada-coupon-fields']['display'] = 'block';
			$css[ $media_query ]['.cart-collaterals .shipping-coupon .coupon #avada_coupon_code']['width'] = '100%';
			$css[ $media_query ]['.coupon .input-text']['width'] = '100%';

			$css[ $media_query ]['.coupon .button']['margin-top'] = '20px';

			$media_query = '@media only screen and (max-width: ' . ( intval( $side_header_width ) + 900 ) . 'px)';

			$elements = array(
				'.woocommerce #customer_login .login .form-row',
				'.woocommerce #customer_login .login .lost_password',
			);
			$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';

			$elements = array(
				'.woocommerce #customer_login .login .inline',
				'.woocommerce #customer_login .login .lost_password',
			);
			$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']      = 'block';
			$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = '0';
			$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = '0';

			$css[ $media_query ]['.woocommerce #customer_login .login .button']['margin-bottom'] = '10px';

		}

		$media_query = '@media only screen and (min-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';

		$css[ $media_query ]['body.side-header-right.layout-boxed-mode #side-header']['position'] = 'absolute';
		$css[ $media_query ]['body.side-header-right.layout-boxed-mode #side-header']['top']      = '0';

		$css[ $media_query ]['body.side-header-right.layout-boxed-mode #side-header .side-header-wrapper']['position'] = 'absolute';

		$media_query = '@media screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) - 18 ) . 'px)';

		$elements = array(
			'body.admin-bar #wrapper #slidingbar-area',
			'body.layout-boxed-mode.side-header-right #slidingbar-area',
			'.admin-bar p.demo_store',
		);
		$css[ $media_query ][ $dynamic_css_helpers->implode( $elements ) ]['top'] = '46px';
		$css[ $media_query ]['body.body_blank.admin-bar']['top'] = '45px';
		$css[ $media_query ]['html #wpadminbar']['z-index']  = '99999 !important';
		$css[ $media_query ]['html #wpadminbar']['position'] = 'fixed !important';

		$media_query = '@media screen and (max-width: ' . ( intval( Avada()->settings->get( 'side_header_break_point' ) ) - 32 ) . 'px)';
		$css[ $media_query ]['.fusion-tabs.vertical-tabs .tab-pane']['max-width'] = 'none !important';

		$media_query = '@media only screen and (min-device-width: 768px) and (max-device-width: 1024px)';
		$css[ $media_query ]['#wrapper .ei-slider']['width'] = '100%';

		$media_query = '@media only screen and (min-device-width: 320px) and (max-device-width: 480px)';
		$css[ $media_query ]['#wrapper .ei-slider']['width'] = '100%';

		/*
		Landscape Responsive Styles - iPad
		*/
		$ipad_landscape_media_query = '@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape)';

		// #Layout
		$css[ $ipad_landscape_media_query ]['.fullwidth-box']['background-attachment'] = 'scroll !important';
		$css[ $ipad_landscape_media_query ]['.fullwidth-box .fullwidth-faded']['background-attachment'] = 'scroll !important';

		$css[ $ipad_landscape_media_query ]['.fusion-main-menu > ul > li']['padding-right'] = intval( Avada()->settings->get( 'mobile_nav_padding' ) ) . 'px';

		// # Footer Styles
		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$css[ $ipad_landscape_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$css[ $ipad_landscape_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		if ( 'footer_area_bg_parallax' == Avada()->settings->get( 'footer_special_effects' ) ) {

			$css[ $ipad_landscape_media_query ]['.fusion-footer-widget-area']['background-attachment'] = 'static';
			$css[ $ipad_landscape_media_query ]['.fusion-footer-widget-area']['margin']   = '0';

			$css[ $ipad_landscape_media_query ]['#main']['margin-bottom']   = '0';
		}

		$css[ $ipad_landscape_media_query ]['#wrapper .ei-slider']['width'] = '100%';
		$elements = array(
			'.fullwidth-box',
			'.page-title-bar',
			'.fusion-footer-widget-area',
			'body',
			'#main',
		);
		$css[ $ipad_landscape_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'scroll !important';
		if ( Avada()->settings->get( 'footerw_bg_image' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_parallax_effect', 'footer_area_bg_parallax', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$css[ $ipad_landscape_media_query ]['.fusion-body #wrapper']['background-color'] = 'transparent';
		}

		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$css[ $ipad_landscape_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$css[ $ipad_landscape_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$css[ $ipad_landscape_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		if ( Avada()->settings->get( 'footer_special_effects' ) == 'footer_area_bg_parallax' ) {
			$css[ $ipad_landscape_media_query ]['.fusion-footer-widget-area']['background-attachment'] = 'static';
			$css[ $ipad_landscape_media_query ]['.fusion-footer-widget-area']['margin']   = '0';

			$css[ $ipad_landscape_media_query ]['#main']['margin-bottom']   = '0';
		}

		/*
		Portrait Responsive Styles - iPad
		*/
		$ipad_portrait_media_query = '@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: portrait)';

		if ( Avada()->settings->get( 'footerw_bg_image' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_parallax_effect', 'footer_area_bg_parallax', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$css[ $ipad_portrait_media_query ]['.fusion-body #wrapper']['background-color'] = 'transparent';
		}

		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$css[ $ipad_portrait_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$css[ $ipad_portrait_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$css[ $ipad_portrait_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$css[ $ipad_portrait_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		if ( 'footer_area_bg_parallax' == Avada()->settings->get( 'footer_special_effects' ) ) {
			$css[ $ipad_portrait_media_query ]['.fusion-footer-widget-area']['background-attachment'] = 'static';
			$css[ $ipad_portrait_media_query ]['.fusion-footer-widget-area']['margin']   = '0';

			$css[ $ipad_portrait_media_query ]['#main']['margin-bottom']   = '0';
		}

		// # Layout
		$elements = array(
			'.fusion-columns-5 .fusion-column:first-child',
			'.fusion-columns-4 .fusion-column:first-child',
			'.fusion-columns-3 .fusion-column:first-child',
			'.fusion-columns-2 .fusion-column:first-child',
			'.fusion-columns-1 .fusion-column:first-child',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = '0';

		$elements = array(
			'.fusion-column:nth-child(5n)',
			'.fusion-column:nth-child(4n)',
			'.fusion-column:nth-child(3n)',
			'.fusion-column:nth-child(2n)',
			'.fusion-column',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper']['width']      = 'auto !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.create-block-format-context']['display'] = 'none';

		$ipad_portrait[ $ipad_portrait_media_query ]['.columns .col']['float']      = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.columns .col']['width']      = '100% !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.columns .col']['margin']     = '0 0 20px';
		$ipad_portrait[ $ipad_portrait_media_query ]['.columns .col']['box-sizing'] = 'border-box';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fullwidth-box']['background-attachment'] = 'scroll !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fullwidth-box .fullwidth-faded']['background-attachment'] = 'scroll !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-main-menu > ul > li']['padding-right'] = intval( Avada()->settings->get( 'mobile_nav_padding' ) ) . 'px';

		if ( ! Avada()->settings->get( 'breadcrumb_mobile' ) ) {
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .fusion-page-title-bar .fusion-breadcrumbs']['display'] = 'none';
		}

		// # Footer Styles
		if ( Avada()->settings->get( 'footer_sticky_height' ) && ( in_array( Avada()->settings->get( 'footer_special_effects' ), array( 'footer_sticky', 'footer_sticky_with_parallax_bg_image' ) ) ) ) {
			$elements = array( 'html', 'body', '#boxed-wrapper', '#wrapper' );
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']     = 'auto';
			$ipad_portrait[ $ipad_portrait_media_query ]['.above-footer-wrapper']['min-height']    = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.above-footer-wrapper']['margin-bottom'] = '0';
			$ipad_portrait[ $ipad_portrait_media_query ]['.above-footer-wrapper:after']['height']  = 'auto';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-footer']['height']               = 'auto';
		}

		if ( 'footer_area_bg_parallax' == Avada()->settings->get( 'footer_special_effects' ) ) {
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-footer-widget-area']['background-attachment'] = 'static';
			$css[ $ipad_portrait_media_query ]['.fusion-footer-widget-area']['margin']   = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['#main']['margin-bottom']   = '0';
		}

		$ipad_portrait[ $ipad_portrait_media_query ]['.review']['float'] = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.review']['width'] = '100%';

		$elements = array(
			'.fusion-social-networks',
			'.fusion-social-links-footer',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']    = 'block';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-links-footer']['width'] = 'auto';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-links-footer .fusion-social-networks']['display'] = 'inline-block';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-links-footer .fusion-social-networks']['float']   = 'none';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks']['padding'] = '0 0 15px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-author .fusion-author-ssocial .fusion-author-tagline']['float']      = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-author .fusion-author-ssocial .fusion-author-tagline']['text-align'] = 'center';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-author .fusion-author-ssocial .fusion-author-tagline']['max-width']  = '100%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-author .fusion-author-ssocial .fusion-social-networks']['text-align'] = 'center';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-author .fusion-author-ssocial .fusion-social-networks .fusion-social-network-icon:first-child']['margin-left'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks:after']['content'] = '""';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks:after']['display'] = 'block';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks:after']['clear']   = 'both';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks li']['float']   = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-social-networks li']['display'] = 'inline-block';

		if ( class_exists( 'WooCommerce' ) ) {

			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['float']         = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['text-align']    = 'center';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['border-top']    = '1px solid';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['border-bottom'] = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['width']         = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['line-height']   = 'normal !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['height']        = 'auto !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['margin-bottom'] = '10px';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .cart-empty']['padding-top']   = '10px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['float']          = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['border-top']     = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['border-bottom']  = '1px solid';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['width']          = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['text-align']     = 'center';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['line-height']    = 'normal !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['height']         = 'auto !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#main .return-to-shop']['padding-bottom'] = '10px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .promo-code-heading']['display']       = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .promo-code-heading']['margin-bottom'] = '10px !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .promo-code-heading']['float']         = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .promo-code-heading']['text-align']    = 'center';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-contents']['display'] = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-contents']['float']   = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-contents']['margin']  = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['display']       = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['width']         = 'auto !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['float']         = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['text-align']    = 'center';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['margin-right']  = '0';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-input']['margin-bottom'] = '10px !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-button']['display']      = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-button']['margin-right'] = '0';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-button']['float']        = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce .checkout_coupon .coupon-button']['text-align']   = 'center';

		} // End if().

		// Page Title Bar.
		if ( 'auto' !== Avada()->settings->get( 'page_title_mobile_height' ) ) {

			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .fusion-page-title-bar']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'page_title_mobile_height' ) );

		} else {

			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .fusion-page-title-bar']['padding-top']    = '10px';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .fusion-page-title-bar']['padding-bottom'] = '10px';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .fusion-page-title-bar']['height']         = 'auto';

		}

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-wrapper']['display'] = 'block';

		$elements = array(
			'.fusion-page-title-bar-left .fusion-page-title-captions',
			'.fusion-page-title-bar-right .fusion-page-title-captions',
			'.fusion-page-title-bar-left .fusion-page-title-secondary',
			'.fusion-page-title-bar-right .fusion-page-title-secondary',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']     = 'block';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']       = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']       = '100%';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = 'normal';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar-left .fusion-page-title-secondary']['text-align'] = 'left';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar .fusion-page-title-secondary']['margin'] = '2px 0 0 0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar-left .searchform']['display']   = 'block';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar-left .searchform']['max-width'] = '100%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar-right .fusion-page-title-secondary']['text-align'] = 'right';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-bar-right .searchform']['max-width'] = '100%';

		if ( 'auto' != Avada()->settings->get( 'page_title_mobile_height' ) ) {

			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-row']['display']    = 'table';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-row']['width']      = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-row']['height']     = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-row']['min-height'] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'page_title_mobile_height' ) ), '-20px' ) );

			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-wrapper']['display']        = 'table-cell';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-page-title-wrapper']['vertical-align'] = 'middle';

		}

		$ipad_portrait[ $ipad_portrait_media_query ]['.products .product-list-view']['width']     = '100% !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.products .product-list-view']['min-width'] = '100% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.sidebar .social_links .social li']['width']        = 'auto';
		$ipad_portrait[ $ipad_portrait_media_query ]['.sidebar .social_links .social li']['margin-right'] = '5px';

		$ipad_portrait[ $ipad_portrait_media_query ]['#comment-input']['margin-bottom'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['#comment-input input']['width']         = '90%';
		$ipad_portrait[ $ipad_portrait_media_query ]['#comment-input input']['float']         = 'none !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['#comment-input input']['margin-bottom'] = '10px';

		$ipad_portrait[ $ipad_portrait_media_query ]['#comment-textarea textarea']['width'] = '90%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.pagination']['margin-top'] = '40px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-one .portfolio-item .image']['float']         = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-one .portfolio-item .image']['width']         = 'auto';
		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-one .portfolio-item .image']['height']        = 'auto';
		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-one .portfolio-item .image']['margin-bottom'] = '20px';

		$ipad_portrait[ $ipad_portrait_media_query ]['h5.toggle span.toggle-title']['width'] = '80%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box']['height'] = 'auto';

		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .share-box h4']['float']       = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .share-box h4']['line-height'] = '20px !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .share-box h4']['padding']     = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box ul']['float']          = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box ul']['overflow']       = 'hidden';
		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box ul']['padding']        = '0 25px';
		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box ul']['padding-bottom'] = '15px';
		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box ul']['margin-top']     = '0px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.project-content .project-description']['float'] = 'none !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.project-content .fusion-project-description-details']['margin-bottom'] = '50px';

		$elements = array(
			'.project-content .project-description',
			'.project-content .project-info',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-half .flexslider']['width'] = '100%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.portfolio-half .project-content']['width'] = '100% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['#style_selector']['display'] = 'none';

		$elements = array(
			'.portfolio-tabs',
			'.faq-tabs',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['height']              = 'auto';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-width'] = '1px';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom-style'] = 'solid';

		$elements = array(
			'.portfolio-tabs li',
			'.faq-tabs li',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'left';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '30px';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-bottom'] = '0';

		$elements = array(
			'.ls-avada .ls-nav-prev',
			'.ls-avada .ls-nav-next',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

		$elements = array(
			'nav#nav',
			'nav#sticky-nav',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['#footer .social-networks']['width']    = '100%';
		$ipad_portrait[ $ipad_portrait_media_query ]['#footer .social-networks']['margin']   = '0 auto';
		$ipad_portrait[ $ipad_portrait_media_query ]['#footer .social-networks']['position'] = 'relative';
		$ipad_portrait[ $ipad_portrait_media_query ]['#footer .social-networks']['left']     = '-11px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.tab-holder .tabs']['height'] = 'auto !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.tab-holder .tabs']['width']  = '100% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.shortcode-tabs .tab-hold .tabs li']['width'] = '100% !important';

		$elements = array(
			'body .shortcode-tabs .tab-hold .tabs li',
			'body.dark .sidebar .tab-hold .tabs li',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['border-right'] = 'none !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.error-message']['line-height'] = '170px';
		$ipad_portrait[ $ipad_portrait_media_query ]['.error-message']['margin-top']  = '20px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.error_page .useful_links']['width']        = '100%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.error_page .useful_links']['padding-left'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-google-map']['width']         = '100% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.social_links_shortcode .social li']['width'] = '10% !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .ei-slider']['width']  = '100% !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .ei-slider']['height'] = '200px !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-blog-layout-medium-alternate .fusion-post-content']['float']      = 'none';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-blog-layout-medium-alternate .fusion-post-content']['width']      = '100% !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-blog-layout-medium-alternate .fusion-post-content']['margin-top'] = '20px';

		$ipad_portrait[ $ipad_portrait_media_query ]['.popup']['display'] = 'none !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.share-box .social-networks']['text-align'] = 'left';

		if ( class_exists( 'WooCommerce' ) ) {

			$elements = array(
				'.catalog-ordering .order',
				'.avada-myaccount-data .addresses .col-1',
				'.avada-myaccount-data .addresses .col-2',
				'.avada-customer-details .addresses .col-1',
				'.avada-customer-details .addresses .col-2',
				'#wrapper .catalog-ordering > .fusion-grid-list-view',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'none !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = 'auto !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto !important';

			$elements = array(
				'.avada-myaccount-data .addresses .col-1',
				'.avada-myaccount-data .addresses .col-2',
				'.avada-customer-details .addresses .col-1',
				'.avada-customer-details .addresses .col-2',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin'] = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']  = '100%';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .catalog-ordering']['margin-bottom'] = '50px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['display'] = 'flex';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['align-items'] = 'center';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['justify-content'] = 'space-between';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['margin-bottom'] = '10px';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['margin']        = '0 auto 10px auto';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['width']         = '225px';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby-order-container']['float']         = 'none';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .order-dropdown > li:hover > ul']['display']  = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .order-dropdown > li:hover > ul']['position'] = 'relative';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .order-dropdown > li:hover > ul']['top']      = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .orderby.order-dropdown']['width']        = '176px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .sort-count.order-dropdown']['display'] = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .sort-count.order-dropdown']['width'] = '225px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .sort-count.order-dropdown ul a']['width'] = '225px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .catalog-ordering .order']['margin'] = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['.catalog-ordering .fusion-grid-list-view']['display'] = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.catalog-ordering .fusion-grid-list-view']['width'] = '78px';

			$elements = array(
				'.products-2 li:nth-child(2n+1)',
				'.products-3 li:nth-child(3n+1)',
				'.products-4 li:nth-child(4n+1)',
				'.products-5 li:nth-child(5n+1)',
				'.products-6 li:nth-child(6n+1)',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear'] = 'none !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['#main .products li:nth-child(3n+1)']['clear'] = 'both !important';

			$elements = array(
				'.products li',
				'#main .products li:nth-child(3n)',
				'#main .products li:nth-child(4n)',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']        = '32.3% !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'left !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = '1% !important';

			$elements = array(
				'.woocommerce #customer_login .login .form-row',
				'.woocommerce #customer_login .login .lost_password',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';

			$elements = array(
				'.woocommerce #customer_login .login .inline',
				'.woocommerce #customer_login .login .lost_password',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display']     = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-data .my_account_orders .woocommerce-orders-table__cell-order-number']['padding-right'] = '8px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-data .my_account_orders .woocommerce-orders-table__cell-order-actions']['padding-left'] = '8px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.shop_table .product-name']['width'] = '35%';

			$elements = array(
				'#wrapper .woocommerce-side-nav',
				'#wrapper .woocommerce-content-box',
				'.fusion-body .woocommerce-MyAccount-navigation',
				'.fusion-body .woocommerce-MyAccount-content',
				'#wrapper .shipping-coupon',
				'#wrapper .cart_totals',
				'#wrapper #customer_login .col-1',
				'#wrapper #customer_login .col-2',
				'#wrapper .woocommerce form.checkout #customer_details .col-1',
				'#wrapper .woocommerce form.checkout #customer_details .col-2',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']        = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = 'auto';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']        = '100% !important';

			$elements = array(
				'#customer_login .col-1',
				'.coupon',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '20px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.shop_table .product-thumbnail']['float'] = 'none';

			$ipad_portrait[ $ipad_portrait_media_query ]['.product-info']['margin-left'] = '0';
			$ipad_portrait[ $ipad_portrait_media_query ]['.product-info']['margin-top']  = '10px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.product .entry-summary div .price']['float'] = 'none';

			$ipad_portrait[ $ipad_portrait_media_query ]['.product .entry-summary .woocommerce-product-rating']['float']       = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['.product .entry-summary .woocommerce-product-rating']['margin-left'] = '0';

			$elements = array(
				'.woocommerce-tabs .tabs',
				'.woocommerce-side-nav',
				'.woocommerce-MyAccount-navigation',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '25px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-tabs .panel']['width']   = '91% !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-tabs .panel']['padding'] = '4% !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['#reviews li .avatar']['display'] = 'none';

			$ipad_portrait[ $ipad_portrait_media_query ]['#reviews li .comment-text']['width']       = '90% !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#reviews li .comment-text']['margin-left'] = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#reviews li .comment-text']['padding']     = '5% !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share']['overflow'] = 'hidden';

			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['display']       = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['float']         = 'left';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['margin']        = '0 auto';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['border-right']  = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['border-left']   = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['padding-left']  = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['padding-right'] = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['.woocommerce-container .social-share li']['width']         = '25%';

			$ipad_portrait[ $ipad_portrait_media_query ]['.has-sidebar .woocommerce-container .social-share li']['width'] = '50%';

			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user']['display']           = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['width']      = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['display']    = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user .username .not-user']['display'] = 'inline';
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-body .avada-myaccount-user .username .not-user']['padding'] = '0 5px;';
			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['padding']    = '5px 0px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user .avada-myaccount-user-column']['border-right'] = 0;

			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user']['padding-top']       = '10px';
			$ipad_portrait[ $ipad_portrait_media_query ]['.avada-myaccount-user']['padding-bottom']    = '10px';

			if ( is_rtl() ) {
				$ipad_portrait[ $ipad_portrait_media_query ]['.rtl .avada-myaccount-user .avada-myaccount-user-column']['border-left'] = '0';
			}

			$elements = array(
				'.shop_table .product-thumbnail img',
				'.shop_table .product-thumbnail .product-info',
				'.shop_table .product-thumbnail .product-info p',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']   = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']   = '100%';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin']  = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '0';

			$ipad_portrait[ $ipad_portrait_media_query ]['.shop_table .product-thumbnail']['padding'] = '10px 0px';

			$ipad_portrait[ $ipad_portrait_media_query ]['.product .images']['margin-bottom'] = '30px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .button']['float']         = 'left';
			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .button']['margin-bottom'] = '15px';

			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .remember-box']['clear']   = 'both';
			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .remember-box']['display'] = 'block';
			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .remember-box']['padding'] = '0';
			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .remember-box']['width']   = '125px';
			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .remember-box']['float']   = 'left';

			$ipad_portrait[ $ipad_portrait_media_query ]['#customer_login_box .lost_password']['float'] = 'left';

			$elements = array(
				'#wrapper .product .images',
				'#wrapper .product .summary.entry-summary',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'left !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .avada-single-product-gallery-wrapper .avada-product-gallery']['float'] = 'none !important';

			if ( Avada()->settings->get( 'disable_woo_gallery' ) ) {
				$ipad_portrait[ $ipad_portrait_media_query ]['.product .entry-summary .summary-container']['margin-top']  = '20px';
			}

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .product .summary.entry-summary']['width']       = '48% !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .product .summary.entry-summary']['margin-left'] = '2% !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .woocommerce-tabs .tabs']['width'] = '24% !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .woocommerce-tabs .tabs']['float'] = 'left !important';

			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .woocommerce-tabs .panel']['float']   = 'right !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .woocommerce-tabs .panel']['width']   = '70% !important';
			$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .woocommerce-tabs .panel']['padding'] = '4% !important';

			$elements = array(
				'.product .images #slider .flex-direction-nav',
				'.product .images #carousel .flex-direction-nav',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none !important';

			$elements = array(
				'.avada-myaccount-user .avada-myaccount-user-column',
			);
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0 !important';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0 !important';

		} // End if().

		$ipad_portrait[ $ipad_portrait_media_query ]['body #small-nav']['visibility'] = 'visible !important';

		$elements = array();
		if ( class_exists( 'GFForms' ) ) {
			$elements[] = '.gform_wrapper .ginput_complex .ginput_left';
			$elements[] = '.gform_wrapper .ginput_complex .ginput_right';
			$elements[] = '.gform_wrapper .gfield input[type="text"]';
			$elements[] = '.gform_wrapper .gfield textarea';
		}
		if ( defined( 'WPCF7_PLUGIN' ) ) {
			$elements[] = '.wpcf7-form .wpcf7-text';
			$elements[] = '.wpcf7-form .wpcf7-quiz';
			$elements[] = '.wpcf7-form .wpcf7-number';
			$elements[] = '.wpcf7-form textarea';
		}

		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']      = 'none !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']      = '100% !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['box-sizing'] = 'border-box';

		$ipad_portrait[ $ipad_portrait_media_query ]['#nav-uber #megaMenu']['width'] = '100%';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fullwidth-box']['background-attachment'] = 'scroll';

		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop']['bottom']        = '30px';
		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop']['border-radius'] = '4px';
		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop']['height']        = '40px';
		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop']['z-index']       = '10000';

		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop:before']['line-height'] = '38px';

		$ipad_portrait[ $ipad_portrait_media_query ]['#toTop:hover']['background-color'] = '#333333';

		$ipad_portrait[ $ipad_portrait_media_query ]['.no-mobile-totop .to-top-container']['display'] = 'none';

		$ipad_portrait[ $ipad_portrait_media_query ]['.no-mobile-slidingbar #slidingbar-area']['display'] = 'none';

		$ipad_portrait[ $ipad_portrait_media_query ]['.tfs-slider .slide-content-container .btn']['min-height']    = '0 !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.tfs-slider .slide-content-container .btn']['padding-left']  = '20px';
		$ipad_portrait[ $ipad_portrait_media_query ]['.tfs-slider .slide-content-container .btn']['padding-right'] = '20px !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.tfs-slider .slide-content-container .btn']['height']        = '26px !important';
		$ipad_portrait[ $ipad_portrait_media_query ]['.tfs-slider .slide-content-container .btn']['line-height']   = '26px !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-soundcloud iframe']['width'] = '100%';

		$elements = array(
			'.fusion-columns-2 .fusion-column',
			'.fusion-columns-2 .fusion-flip-box-wrapper',
			'.fusion-columns-4 .fusion-column',
			'.fusion-columns-4 .fusion-flip-box-wrapper',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '50% !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'left !important';

		$elements = array(
			'.fusion-columns-2 .fusion-column:nth-of-type(3n)',
			'.fusion-columns-4 .fusion-column:nth-of-type(3n)',
			'.fusion-columns-2 .fusion-flip-box-wrapper:nth-of-type(3n)',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear'] = 'both';

		$elements = array(
			'.fusion-columns-3 .fusion-column',
			'.fusion-columns-3 .fusion-flip-box-wrapper',
			'.fusion-columns-5 .fusion-column',
			'.fusion-columns-5 .fusion-flip-box-wrapper',
			'.fusion-columns-6 .fusion-column',
			'.fusion-columns-6 .fusion-flip-box-wrapper',
			'.fusion-columns-5 .col-lg-2',
			'.fusion-columns-5 .col-md-2',
			'.fusion-columns-5 .col-sm-2',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '33.33% !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'left !important';

		$elements = array(
			'.fusion-columns-3 .fusion-column:nth-of-type(4n)',
			'.fusion-columns-3 .fusion-flip-box-wrapper:nth-of-type(4n)',
			'.fusion-columns-5 .fusion-column:nth-of-type(4n)',
			'.fusion-columns-5 .fusion-flip-box-wrapper:nth-of-type(4n)',
			'.fusion-columns-6 .fusion-column:nth-of-type(4n)',
			'.fusion-columns-6 .fusion-flip-box-wrapper:nth-of-type(4n)',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['clear'] = 'both';

		$elements = array(
			'.footer-area .fusion-column',
			'#slidingbar .fusion-column',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '40px';

		$elements = array(
			'.fusion-layout-column.fusion-one-sixth',
			'.fusion-layout-column.fusion-five-sixth',
			'.fusion-layout-column.fusion-one-fifth',
			'.fusion-layout-column.fusion-two-fifth',
			'.fusion-layout-column.fusion-three-fifth',
			'.fusion-layout-column.fusion-four-fifth',
			'.fusion-layout-column.fusion-one-fourth',
			'.fusion-layout-column.fusion-three-fourth',
			'.fusion-layout-column.fusion-one-third',
			'.fusion-layout-column.fusion-two-third',
			'.fusion-layout-column.fusion-one-half',
		);

		if ( is_rtl() ) {
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position']      = 'relative';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'right';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '4%';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '0%';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '20px';
		} else {
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['position']      = 'relative';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'left';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-right']  = '4%';
			$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '20px';
		}

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-sixth']['width']    = '13.3333%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-five-sixth']['width']   = '82.6666%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-fifth']['width']    = '16.8%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-two-fifth']['width']    = '37.6%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-three-fifth']['width']  = '58.4%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-four-fifth']['width']   = '79.2%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-fourth']['width']   = '22%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-three-fourth']['width'] = '74%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-third']['width']    = '30.6666%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-two-third']['width']    = '65.3333%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-half']['width']     = '48%';

		// No spacing Columns.
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-spacing-no']['margin-left']  = '0';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-spacing-no']['margin-right'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-sixth.fusion-spacing-no']['width']    = '16.6666666667%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-five-sixth.fusion-spacing-no']['width']   = '83.333333333%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-fifth.fusion-spacing-no']['width']    = '20%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-two-fifth.fusion-spacing-no']['width']    = '40%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-three-fifth.fusion-spacing-no']['width']  = '60%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-four-fifth.fusion-spacing-no']['width']   = '80%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-fourth.fusion-spacing-no']['width']   = '25%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-three-fourth.fusion-spacing-no']['width'] = '75%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-third.fusion-spacing-no']['width']    = '33.33333333%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-two-third.fusion-spacing-no']['width']    = '66.66666667%';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-half.fusion-spacing-no']['width']     = '50%';

		if ( is_rtl() ) {
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-column-last']['clear'] = 'left';
		} else {
			$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-column-last']['clear'] = 'right';
		}
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-one-full']['clear'] = 'both';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-column-last']['zoom']         = '1';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-column-last']['margin-left']  = '0';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-layout-column.fusion-column-last']['margin-right'] = '0';

		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-column.fusion-spacing-no']['margin-bottom'] = '0';
		$ipad_portrait[ $ipad_portrait_media_query ]['.fusion-column.fusion-spacing-no']['width']         = '100% !important';

		$elements = array(
			'.ua-mobile .page-title-bar',
			'.ua-mobile .fusion-footer-widget-area',
			'.ua-mobile body',
			'.ua-mobile #main',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'scroll !important';

		if ( get_post_meta( $c_page_id, 'pyre_fallback', true ) ) {
			$ipad_portrait[ $ipad_portrait_media_query ]['#sliders-container']['display'] = 'none';
			$ipad_portrait[ $ipad_portrait_media_query ]['#fallback-slide']['display'] = 'block';

		}

		$elements = array(
			'.fusion-secondary-header .fusion-row',
			'.fusion-header .fusion-row',
			'.footer-area > .fusion-row',
			'#footer > .fusion-row',
			'#header-sticky .fusion-row',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0px !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0px !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['.error-message']['font-size'] = '130px';

		$elements = array(
			'.fusion-secondary-header .fusion-row',
			'.fusion-header .fusion-row',
			'.footer-area > .fusion-row',
			'#footer > .fusion-row',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0px !important';
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0px !important';

		$ipad_portrait[ $ipad_portrait_media_query ]['#wrapper .ei-slider']['width'] = '100%';
		$elements = array(
			'.fullwidth-box',
			'.page-title-bar',
			'.fusion-footer-widget-area',
			'body',
			'#main',
		);
		$ipad_portrait[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'scroll !important';

		// Filter for editing the iPad Portrait Media Query Styles.
		$ipad_portrait = apply_filters( 'avada_ipad_portrait_styles', $ipad_portrait );
		$css = array_merge( $css, $ipad_portrait );

		// End iPad Portrait Media Query Styles.
	} // End if().

	if ( ! Avada()->settings->get( 'responsive' ) ) {

		$css['global']['.ua-mobile #wrapper']['width']         = '100% !important';
		$css['global']['.ua-mobile #wrapper']['overflow']      = 'hidden !important';
		$css['global']['.ua-mobile #slidingbar-area']['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
		$css['global']['.ua-mobile #slidingbar-area']['left']  = '0';

	}

	// WPML Flag positioning on the main menu when header is on the Left/Right.
	if ( class_exists( 'SitePress' ) && 'Top' !== Avada()->settings->get( 'header_position' ) ) {
		$css['global']['.fusion-main-menu > ul > li > a .iclflag']['margin-top'] = '14px !important';
	}

	/**
	 * IE11
	 */
	$elements = array(
		'.ua-ie-11 .avada-select-parent .select-arrow',
		'.ua-ie-11 .select-arrow',
	);
	if ( defined( 'WPCF7_PLUGIN' ) ) {
		$elements[] = '.ua-ie-11 .wpcf7-select-parent .select-arrow';
	}

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']      = '33px';
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '33px';

	$css['global']['.ua-ie-11 .gravity-select-parent .select-arrow']['height']      = '24px';
	$css['global']['.ua-ie-11 .gravity-select-parent .select-arrow']['line-height'] = '24px';

	if ( class_exists( 'GFForms' ) ) {
		$elements = array(
			'.ua-ie-11 #wrapper .gf_browser_ie.gform_wrapper .button',
			'.ua-ie-11 #wrapper .gf_browser_ie.gform_wrapper .gform_footer input.button',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '0 20px';
	}

	$css['global']['.ua-ie-11 .fusion-flip-box .flip-box-front']['backface-visibility'] = 'visible';
	$css['global']['.ua-ie-11 .fusion-flip-box .flip-box-back']['backface-visibility'] = 'visible';

	$css[ $media_query ]['.ua-ie-11 .fusion-imageframe, .ua-ie-11 .imageframe-align-center']['font-size']   = '0px';
	$css[ $media_query ]['.ua-ie-11 .fusion-imageframe, .ua-ie-11 .imageframe-align-center']['line-height'] = 'normal';

	if ( $site_width_percent ) {

		$elements = array(
			'.fusion-secondary-header',
			'.header-v4 #small-nav',
			'.header-v5 #small-nav',
			'#main',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = '0px';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = '0px';

		if ( '100%' == Avada()->settings->get( 'site_width' ) ) {
			$elements = array(
				'#slidingbar .fusion-row',
				'#sliders-container .tfs-slider .slide-content-container',
				'#main .fusion-row',
				'.fusion-page-title-bar',
				'.fusion-header',
				'.fusion-footer-widget-area',
				'.fusion-footer-copyright-area',
				'.fusion-secondary-header',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']  = $hundredplr_padding;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right'] = $hundredplr_padding;
		}

		$elements = array(
			'.width-100 .fullwidth-box',
			'.width-100 .fullwidth-box .fusion-row .fusion-full-width-sep',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = $hundredplr_padding_negative_margin;
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = $hundredplr_padding_negative_margin;

		$css['global']['#main.width-100 > .fusion-row']['padding-left']  = '0';
		$css['global']['#main.width-100 > .fusion-row']['padding-right'] = '0';

	} // End if().

	if ( 'Boxed' === Avada()->settings->get( 'layout' ) || 'boxed' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {
		$elements_with_html = array(
			'html',
			'body',
		);
		$elements_without_html = array(
			'body',
		);
		if ( 'framed' === Avada()->settings->get( 'scroll_offset' ) ) {
			$elements_with_html = array(
				'.fusion-top-frame',
				'.fusion-sides-frame',
				'.fusion-bottom-frame',
			);
			$elements_without_html = array(
				'.fusion-top-frame',
				'.fusion-sides-frame',
				'.fusion-bottom-frame',
			);
		}
		$elements = $elements_with_html;
		$background_color = ( get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) ) ? get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) : Fusion_Sanitize::color( Avada()->settings->get( 'bg_color' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( $background_color );

		$alpha = 1;
		if ( class_exists( 'Fusion_Color' ) ) {
			$alpha = Fusion_Color::new_color( $background_color )->alpha;
		}

		if ( 1 > $alpha ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-blend-mode'] = 'overlay';
		}

		$elements = $elements_without_html;
		if ( get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) {

			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) . '")';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-repeat'] = get_post_meta( $c_page_id, 'pyre_page_bg_repeat', true );

			if ( 'yes' === get_post_meta( $c_page_id, 'pyre_page_bg_full', true ) ) {

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'fixed';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position']   = 'center center';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-size']       = 'cover';

			}
		} elseif ( '' != Avada()->settings->get( 'bg_image', 'url' ) ) {

			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'bg_image', 'url' ) ) . '")';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-repeat'] = esc_attr( Avada()->settings->get( 'bg_repeat' ) );

			if ( Avada()->settings->get( 'bg_full' ) ) {

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'fixed';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position']   = 'center center';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-size']       = 'cover';

			}
		}

		if ( Avada()->settings->get( 'bg_pattern_option' ) && Avada()->settings->get( 'bg_pattern' ) && ! ( get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) || get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) ) {

			$elements = $elements_with_html;
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( Avada::$template_dir_url . '/assets/images/patterns/' . esc_attr( Avada()->settings->get( 'bg_pattern' ) ) . '.png' ) . '")';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-repeat'] = 'repeat';

		}

		$elements = array(
			'#wrapper',
			'.fusion-footer-parallax',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin']    = '0 auto';
		if ( 'Top' !== Avada()->settings->get( 'header_position' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) ), '-' . intval( Avada()->settings->get( 'side_header_width' ) ) . 'px' ) );
		} else {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
		}

		$css['global']['.wrapper_blank']['display'] = 'block';

		if ( Avada()->settings->get( 'responsive' ) && $site_width_percent ) {

			$elements = array(
				'#main .fusion-row',
				'.fusion-footer-widget-area .fusion-row',
				'#slidingbar-area .fusion-row',
				'.fusion-footer-copyright-area .fusion-row',
				'.fusion-page-title-row',
				'.fusion-secondary-header .fusion-row',
				'#small-nav .fusion-row',
				'.fusion-header .fusion-row',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width']      = 'none';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-left']   = '10px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding-right']  = '10px';

		}
	} // End if().

	if ( 'Wide' === Avada()->settings->get( 'layout' ) && 'boxed' !== get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {

		$css['global']['#wrapper']['width']     = '100%';
		$css['global']['#wrapper']['max-width'] = 'none';

	} else {

		$css['global']['body']['margin-top'] = Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'top' ) ) . ' !important';
		$css['global']['body']['margin-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'bottom' ) ) . ' !important';

		// Boxed framed scrolling.
		if ( 'framed' === Avada()->settings->get( 'scroll_offset' ) ) {
			$elements = array(
				'.fusion-top-frame',
				'.fusion-sides-frame',
				'.fusion-bottom-frame',
				'.fusion-boxed-shadow',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position'] = 'fixed';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['z-index']  = '99996';

			$site_width    = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
			$margin_top    = '%' === Fusion_Sanitize::get_unit( Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'top' ) ) ) ? intval( Avada()->settings->get( 'margin_offset', 'top' ) ) . 'vh' : Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'top' ) );
			$margin_bottom = '%' === Fusion_Sanitize::get_unit( Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'bottom' ) ) ) ? intval( Avada()->settings->get( 'margin_offset', 'bottom' ) ) . 'vh' : Fusion_Sanitize::size( Avada()->settings->get( 'margin_offset', 'bottom' ) );

			$css['global']['body']['margin-top'] = $margin_top . ' !important';
			$css['global']['body']['margin-bottom'] = $margin_bottom . ' !important';

			$elements = array(
				'.fusion-top-frame',
				'.fusion-bottom-frame',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left']  = '0';
			$css['global']['.fusion-top-frame']['top']                            = '0';
			$css['global']['.fusion-top-frame']['height']                         = $margin_top;
			$css['global']['.fusion-bottom-frame']['height']                      = $margin_bottom;
			$css['global']['.fusion-bottom-frame']['bottom']                      = '0';

			$css['global']['.fusion-sides-frame']['z-index']                      = '0';
			$css['global']['.fusion-sides-frame']['height']                       = '100%';
			$css['global']['.fusion-sides-frame']['width']                        = '100%';
			$css['global']['.fusion-sides-frame']['left']                         = '0';
			$css['global']['.fusion-sides-frame']['top']                          = '0';
			$css['global']['.fusion-sides-frame']['pointer-events']               = 'none';

			$css['global']['.fusion-boxed-shadow']['width']                     = $site_width;
			$css['global']['.fusion-boxed-shadow, body #side-header']['height'] = 'calc( 100vh - ' . $margin_top . ' - ' . $margin_bottom . ' )';
			$css['global']['.fusion-boxed-shadow']['top']                       = $margin_top;
			$css['global']['.fusion-boxed-shadow']['left']                      = 'calc( ( 100% - ' . $site_width . ' ) / 2 )';
			$css['global']['.fusion-boxed-shadow']['pointer-events']            = 'none';

			$mobile_wordpress = '@media screen and (max-width: 782px)';
			$elements = array(
				'.admin-bar .fusion-top-frame',
				'.admin-bar .fusion-sides-frame',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top']                             = '32px';
			$css[ $mobile_wordpress ][ $dynamic_css_helpers->implode( $elements ) ]['top']                  = '46px';
			$css['global']['.admin-bar .fusion-boxed-shadow, .admin-bar #side-header']['height']            = 'calc( 100vh - ' . $margin_top . ' - ' . $margin_bottom . ' - 32px )';
			$css[ $mobile_wordpress ]['.admin-bar .fusion-boxed-shadow, .admin-bar #side-header']['height'] = 'calc( 100vh - ' . $margin_top . ' - ' . $margin_bottom . ' - 46px )';
			$css['global']['.admin-bar .fusion-boxed-shadow']['top']                                        = 'calc( ' . $margin_top . ' + 32px )';
			$css[ $mobile_wordpress ]['.admin-bar .fusion-boxed-shadow']['top']                             = 'calc( ' . $margin_top . ' + 46px )';

			$css['global']['.fusion-side-header-stuck']['position'] = 'fixed !important';

			$css['global']['.fusion-page-title-bar, #main']['position'] = 'relative';

			$side_header_media_query = '@media screen and (max-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';
			$css[ $side_header_media_query ]['body #wrapper #side-header']['height']   = 'auto';
			$css[ $side_header_media_query ]['body #wrapper #side-header']['position'] = 'relative';
			if ( ( get_post_meta( $c_page_id, 'pyre_page_bg', true ) && 'no' === fusion_get_page_option( 'pyre_page_bg_full', $c_page_id ) ) ||
			( ! get_post_meta( $c_page_id, 'pyre_page_bg', true ) && ! Avada()->settings->get( 'bg_full' ) ) ) {
				$css['global']['.fusion-bottom-frame']['background-position'] = '0 calc( ( 100vh - ' . $margin_bottom . ' ) * -1 )';
				$css['global']['.admin-bar .fusion-bottom-frame']['background-position'] = '0 calc( ( 100vh - ' . $margin_bottom . ' - 32px ) * -1 )';
				$css['global'][ $mobile_wordpress ]['background-position'] = '0 calc( ( 100vh - ' . $margin_bottom . ' - 46px ) * -1 )';
			}

			$css['global']['#slidingbar-area']['position'] = 'fixed';
			$css['global']['#toTop']['z-index'] = '99997';
		} // End if().

		$css['global']['#sliders-container .main-flex[data-parallax="1"]']['position'] = 'fixed';

		if ( 'Top' === Avada()->settings->get( 'header_position' ) ) {
			$boxed_shadow_selectors = '#wrapper, .fusion-boxed-shadow';
			if ( 'footer_sticky' === Avada()->settings->get( 'footer_special_effects' ) ) {
				$boxed_shadow_selectors = '.above-footer-wrapper, .fusion-boxed-shadow';
			}

			if ( 'Light' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global'][ $boxed_shadow_selectors ]['box-shadow'] = '0px 0px 50px 1px rgba(0,0,0,0.22)';
			} elseif ( 'Medium' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global'][ $boxed_shadow_selectors ]['box-shadow'] = '0px 0px 50px 5px rgba(0,0,0,0.35)';
			} elseif ( 'Hard' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global'][ $boxed_shadow_selectors ]['box-shadow'] = '0px 0px 50px 10px rgba(0,0,0,0.55)';
			}
			if ( 'full' === Avada()->settings->get( 'scroll_offset' ) ) {
				$css['global']['#sliders-container .tfs-slider']['position']    = 'absolute !important';
				$css['global']['#sliders-container .tfs-slider.fusion-fixed-slider']['position'] = 'fixed !important';
				$css['global']['#sliders-container .tfs-slider']['top'] = '0';
			}
		} else {
			if ( 'full' === Avada()->settings->get( 'scroll_offset' ) ) {
				$css['global']['#sliders-container .tfs-slider']['position']    = 'relative !important';
				$css['global']['#sliders-container .tfs-slider.fusion-fixed-slider']['position'] = 'fixed !important';
				if ( 'Left' === Avada()->settings->get( 'header_position' ) ) {
					$css['global']['#sliders-container .tfs-slider']['margin-left'] = 'auto !important';
					$css['global']['#sliders-container .tfs-slider']['left']        = 'auto !important';
				} else {
					$css['global']['#sliders-container .tfs-slider']['margin-left'] = 'auto !important';
					$css['global']['#sliders-container .tfs-slider']['right']       = 'auto !important';
					$css['global']['#sliders-container .tfs-slider']['left']        = 'auto !important';
				}
			}
			if ( 'Light' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global']['#boxed-wrapper, .fusion-boxed-shadow']['box-shadow'] = '0px 0px 50px 1px rgba(0,0,0,0.22)';
			} elseif ( 'Medium' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global']['#boxed-wrapper, .fusion-boxed-shadow']['box-shadow'] = '0px 0px 50px 5px rgba(0,0,0,0.35)';
			} elseif ( 'Hard' === Avada()->settings->get( 'boxed_modal_shadow' ) ) {
				$css['global']['#boxed-wrapper, .fusion-boxed-shadow']['box-shadow'] = '0px 0px 50px 10px rgba(0,0,0,0.55)';
			}
		} // End if().
	} // End if().

	if ( 'boxed' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {

		$elements = array( 'html', 'body' );

		$background_color = ( get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) ) ? get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) : Fusion_Sanitize::color( Avada()->settings->get( 'bg_color' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = Fusion_Sanitize::color( $background_color );

		if ( get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) {

			$css['global']['body']['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) . '")';
			$css['global']['body']['background-repeat'] = get_post_meta( $c_page_id, 'pyre_page_bg_repeat', true );

			if ( 'yes' === get_post_meta( $c_page_id, 'pyre_page_bg_full', true ) ) {

				$css['global']['body']['background-attachment'] = 'fixed';
				$css['global']['body']['background-position']   = 'center center';
				$css['global']['body']['background-size']       = 'cover';

			}
		} elseif ( '' != Avada()->settings->get( 'bg_image', 'url' ) ) {

			$css['global']['body']['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'bg_image', 'url' ) ) . '")';
			$css['global']['body']['background-repeat'] = esc_attr( Avada()->settings->get( 'bg_repeat' ) );

			if ( Avada()->settings->get( 'bg_full' ) ) {

				$css['global']['body']['background-attachment'] = 'fixed';
				$css['global']['body']['background-position']   = 'center center';
				$css['global']['body']['background-size']       = 'cover';

			}
		}

		if ( Avada()->settings->get( 'bg_pattern_option' ) && Avada()->settings->get( 'bg_pattern' ) && ! ( get_post_meta( $c_page_id, 'pyre_page_bg_color', true ) || get_post_meta( $c_page_id, 'pyre_page_bg', true ) ) ) {

			$css['global']['body']['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( Avada::$template_dir_url . '/assets/images/patterns/' . esc_attr( Avada()->settings->get( 'bg_pattern' ) ) . '.png' ) . '")';
			$css['global']['body']['background-repeat'] = 'repeat';

		}

		$elements = array( '#wrapper', '.fusion-footer-parallax' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']     = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin']    = '0 auto';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = '100%';

		$css['global']['.wrapper_blank']['display'] = 'block';

	} // End if().

	if ( 'wide' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {

		$css['global']['#wrapper']['width']     = '100%';
		$css['global']['#wrapper']['max-width'] = 'none';

	}

	if ( get_post_meta( $c_page_id, 'pyre_page_bg', true ) || '' != Avada()->settings->get( 'bg_image', 'url' ) ) {
		$css['global']['html']['background'] = 'none';
	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_bar_bg', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_page_title_bar_bg', true ) ) . '")';
	} elseif ( '' != Avada()->settings->get( 'page_title_bg', 'url' ) ) {
		$css['global']['.fusion-page-title-bar']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'page_title_bg', 'url' ) ) . '")';
	}

	$css['global']['.fusion-page-title-bar']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'page_title_bg_color' ) );
	if ( get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_color', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-color'] = get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_color', true );
	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_bar_borders_color', true ) ) {
		$css['global']['.fusion-page-title-bar']['border-color'] = get_post_meta( $c_page_id, 'pyre_page_title_bar_borders_color', true );
	}

	if ( '' != Avada()->settings->get( 'header_bg_image', 'url' ) ) {
		// Top bar semi transparent for header 3, move header background to wrapper.
		if ( in_array( Avada()->settings->get( 'header_layout' ), array( 'v2', 'v3' ) ) && 'Top' == Avada()->settings->get( 'header_position' ) && Fusion_Color::new_color( Avada()->settings->get( 'header_top_bg_color' ) )->alpha < 1 ) {

			if ( intval( Avada()->settings->get( 'sec_menu_lh' ) ) > 43 ) {
				$top_bar_height = ( intval( Avada()->settings->get( 'sec_menu_lh' ) ) / 2 ) . 'px';
			} else {
				$top_bar_height = '21.5px';
			}

			$css['global']['body .fusion-header-wrapper .fusion-header']['background-color'] = 'transparent';
			$css['global']['.fusion-header-wrapper, .fusion-is-sticky .fusion-header']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'header_bg_image', 'url' ) ) . '")';

			if ( in_array( Avada()->settings->get( 'header_bg_repeat' ), array( 'repeat-y', 'no-repeat' ) ) ) {
				$css['global']['.fusion-header-wrapper']['background-position'] = 'center center';
				$css['global']['.fusion-is-sticky .fusion-header']['background-position'] = '50% ' . Fusion_Sanitize::add_css_values( array( '50%', '-' . $top_bar_height ) );
			}

			$css['global']['.fusion-header-wrapper']['background-repeat'] = esc_attr( Avada()->settings->get( 'header_bg_repeat' ) );

			if ( Avada()->settings->get( 'header_bg_full' ) ) {
				$css['global']['.fusion-header-wrapper, .fusion-is-sticky .fusion-header']['background-attachment'] = 'scroll';
				$css['global']['.fusion-header-wrapper']['background-position'] = 'center center';
				$css['global']['.fusion-is-sticky .fusion-header']['background-position'] = '50% ' . Fusion_Sanitize::add_css_values( array( '50%', '-' . $top_bar_height ) );
				$css['global']['.fusion-header-wrapper, .fusion-is-sticky .fusion-header']['background-size']     = 'cover';
			}

			if ( Avada()->settings->get( 'header_bg_parallax' ) ) {
				$css['global']['.fusion-header-wrapper, .fusion-is-sticky .fusion-header']['background-attachment'] = 'fixed';
				$css['global']['.fusion-header-wrapper, .fusion-is-sticky .fusion-header']['background-position']   = 'top center';
			}
		} else {

			$css['global']['.side-header-background-image']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'header_bg_image', 'url' ) ) . '")';
			$css['global']['.fusion-header']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'header_bg_image', 'url' ) ) . '")';

			if ( in_array( Avada()->settings->get( 'header_bg_repeat' ), array( 'repeat-y', 'no-repeat' ) ) ) {
				$css['global']['.side-header-background-image']['background-position'] = 'center center';
				$css['global']['.fusion-header']['background-position'] = 'center center';
			}

			$css['global']['.side-header-background-image']['background-repeat'] = esc_attr( Avada()->settings->get( 'header_bg_repeat' ) );
			$css['global']['.fusion-header']['background-repeat'] = esc_attr( Avada()->settings->get( 'header_bg_repeat' ) );

			if ( Avada()->settings->get( 'header_bg_full' ) ) {
				if ( 'Top' == Avada()->settings->get( 'header_position' ) ) {
					$css['global']['.side-header-background-image']['background-attachment'] = 'scroll';
					$css['global']['.fusion-header']['background-attachment'] = 'scroll';
				}
				$css['global']['.side-header-background-image']['background-position'] = 'center center';
				$css['global']['.fusion-header']['background-position'] = 'center center';
				$css['global']['.side-header-background-image']['background-size']     = 'cover';
				$css['global']['.fusion-header']['background-size']     = 'cover';
			}
			if (
			 Avada()->settings->get( 'header_bg_parallax' ) && 'Top' == Avada()->settings->get( 'header_position' ) ) {
				$css['global']['.side-header-background-image']['background-attachment'] = 'fixed';
				$css['global']['.fusion-header']['background-attachment'] = 'fixed';
				$css['global']['.side-header-background-image']['background-position']   = 'top center';
				$css['global']['.fusion-header']['background-position']   = 'top center';
			}
		} // End if().
	} // End if().

	$header_bg_opacity = 1;
	if ( '' != get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) {
		$header_bg_opacity = get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true );
	} else if ( 1 > Fusion_Color::new_color( Avada()->settings->get( 'header_bg_color' ) )->alpha ) {
		$header_bg_opacity = Fusion_Color::new_color( Avada()->settings->get( 'header_bg_color' ) )->alpha;
	}

	if ( get_post_meta( $c_page_id, 'pyre_header_bg_color', true ) && ! is_search() ) {
		$header_bg_color_rgb = fusion_hex2rgb( get_post_meta( $c_page_id, 'pyre_header_bg_color', true ) );
	} else {
		$header_bg_color = Avada()->settings->get( 'header_bg_color' );

		$header_bg_color_obj = Fusion_Color::new_color( $header_bg_color );
		$header_bg_color_rgb = array(
			$header_bg_color_obj->red,
			$header_bg_color_obj->green,
			$header_bg_color_obj->blue,
		);
	}

	if ( 'Top' !== Avada()->settings->get( 'header_position' ) ) {
		$elements = array(
			'.side-header-background-color',
			'.side-header .fusion-footer-parallax',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ',' . $header_bg_opacity . ')';
		$css['global']['.fusion-arrow-svg path']['fill']                                 = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ',' . $header_bg_opacity . ')';
	} else {
		$elements = array(
			'.fusion-header',
		);

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ', 1)';
		$css['global']['.fusion-arrow-svg path']['fill']                                 = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ', 1)';

		if ( 1 > $header_bg_opacity ) {

			if ( ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) || ( ! is_archive() && ! is_404() && ! is_search() && ! is_attachment() ) ) {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ',' . $header_bg_opacity . ')';
				$css['global']['.fusion-arrow-svg path']['fill']                                 = 'rgba(' . $header_bg_color_rgb[0] . ',' . $header_bg_color_rgb[1] . ',' . $header_bg_color_rgb[2] . ',' . $header_bg_opacity . ')';
			}
		}
	}

	$header_bg_color_rgb = Fusion_Sanitize::color( Avada()->settings->get( 'menu_h45_bg_color' ) );

	// If v4 or v5 the arrow svg must come from separate option.
	if ( ( 'v4' === Avada()->settings->get( 'header_layout' ) || 'v5' === Avada()->settings->get( 'header_layout' ) ) &&
		'Top' === Avada()->settings->get( 'header_position' ) &&
		'arrow' === Avada()->settings->get( 'menu_highlight_style' ) ) {
		$css['global']['.fusion-arrow-svg path']['fill'] = $header_bg_color_rgb;
	}

	$css['global']['.fusion-secondary-main-menu']['background-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'menu_h45_bg_color' ) );

	$elements = array( '.fusion-header', '.side-header-background-image' );

	if ( get_post_meta( $c_page_id, 'pyre_header_bg', true ) ) {

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_header_bg', true ) ) . '")';

		if ( in_array( get_post_meta( $c_page_id, 'pyre_header_bg_repeat', true ), array( 'repeat-y', 'no-repeat' ) ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position'] = 'center center';
		}

		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-repeat'] = get_post_meta( $c_page_id, 'pyre_header_bg_repeat', true );

		if ( 'yes' == get_post_meta( $c_page_id, 'pyre_header_bg_full', true ) ) {

			if ( 'Top' == Avada()->settings->get( 'header_position' ) ) {
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'fixed';
			}
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position'] = 'center center';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-size'] = 'cover';

		}

		if ( Avada()->settings->get( 'header_bg_parallax' ) && 'Top' == Avada()->settings->get( 'header_position' ) ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'fixed';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position']   = 'top center';
		}
	}

	/**
	 * If the header opacity is < 1, then do not display the header background image.
	 */
	if ( is_archive() || is_search() || is_404() || is_attachment() ) {
		$header_bg_opacity = 1;
	} elseif ( '' != get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) {
		$header_bg_opacity = get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true );
	} else {
		$header_bg_opacity = Fusion_Color::new_color( Avada()->settings->get( 'header_bg_color' ) )->alpha;
	}

	if ( 1 > $header_bg_opacity ) {
		$css['global']['.fusion-header-wrapper .fusion-header']['background-image'] = 'none';
	}

	// Adjust header padding if opacity is not 1 for smooth scroll.
	if ( 1 > $header_bg_opacity ) {
		$css['global']['.no-overflow-y .fusion-header-v1 .fusion-header']['padding-right'] = '39px';
		$css['global']['.no-overflow-y .fusion-header-v2 .fusion-header']['padding-right'] = '39px';
		$css['global']['.no-overflow-y .fusion-header-v3 .fusion-header']['padding-right'] = '39px';
		$css['global']['.no-overflow-y .fusion-sticky-menu-only .fusion-secondary-main-menu']['padding-right'] = '39px';
	}

	if ( 'no' == get_post_meta( $c_page_id, 'pyre_avada_rev_styles', true ) || ( Avada()->settings->get( 'avada_rev_styles' ) && 'yes' != get_post_meta( $c_page_id, 'pyre_avada_rev_styles', true ) ) ) {

		$css['global']['.rev_slider_wrapper']['position'] = 'relative';

		if ( class_exists( 'RevSliderFront' ) ) {
			$header_bg_opacity = Fusion_Color::new_color( Avada()->settings->get( 'header_bg_color' ) )->alpha;
			if ( ( '1' == $header_bg_opacity && ! get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) || ( get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) && 1 == get_post_meta( $c_page_id, 'pyre_header_bg_opacity', true ) ) ) {

				$css['global']['.rev_slider_wrapper .shadow-left']['position']            = 'absolute';
				$css['global']['.rev_slider_wrapper .shadow-left']['pointer-events']      = 'none';
				$css['global']['.rev_slider_wrapper .shadow-left']['background-image']    = 'url("' . Fusion_Sanitize::css_asset_url( Avada::$template_dir_url . '/assets/images/shadow-top.png' ) . '")';
				$css['global']['.rev_slider_wrapper .shadow-left']['background-repeat']   = 'no-repeat';
				$css['global']['.rev_slider_wrapper .shadow-left']['background-position'] = 'top center';
				$css['global']['.rev_slider_wrapper .shadow-left']['height']              = '42px';
				$css['global']['.rev_slider_wrapper .shadow-left']['width']               = '100%';
				$css['global']['.rev_slider_wrapper .shadow-left']['top']                 = '0';
				$css['global']['.rev_slider_wrapper .shadow-left']['z-index']             = '99';

				$css['global']['.rev_slider_wrapper .shadow-left']['top'] = '-1px';

			}

			$css['global']['.rev_slider_wrapper .shadow-right']['position']            = 'absolute';
			$css['global']['.rev_slider_wrapper .shadow-right']['pointer-events']      = 'none';
			$css['global']['.rev_slider_wrapper .shadow-right']['background-image']    = 'url("' . Fusion_Sanitize::css_asset_url( Avada::$template_dir_url . '/assets/images/shadow-bottom.png' ) . '")';
			$css['global']['.rev_slider_wrapper .shadow-right']['background-repeat']   = 'no-repeat';
			$css['global']['.rev_slider_wrapper .shadow-right']['background-position'] = 'bottom center';
			$css['global']['.rev_slider_wrapper .shadow-right']['height']              = '32px';
			$css['global']['.rev_slider_wrapper .shadow-right']['width']               = '100%';
			$css['global']['.rev_slider_wrapper .shadow-right']['bottom']              = '0';
			$css['global']['.rev_slider_wrapper .shadow-right']['z-index']             = '99';

		}

		$css['global']['.avada-skin-rev']['border-top']    = '1px solid #d2d3d4';
		$css['global']['.avada-skin-rev']['border-bottom'] = '1px solid #d2d3d4';
		$css['global']['.avada-skin-rev']['box-sizing']    = 'content-box';

		$css['global']['.tparrows']['border-radius'] = '0';

		if ( class_exists( 'RevSliderFront' ) ) {

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity']          = '0.8 !important';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position']         = 'absolute';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['top']              = '50% !important';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-top']       = '-31px !important';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']            = '63px !important';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height']           = '63px !important';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background']       = 'none';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = 'rgba(0, 0, 0, 0.5)';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']            = '#fff';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-radius']    = '0';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows:before']['content']                = '"\e61e"';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows:before']['-webkit-font-smoothing'] = 'antialiased';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows:before']['content']                = '"\e620"';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows:before']['-webkit-font-smoothing'] = 'antialiased';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows:before',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows:before',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position']    = 'absolute';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding']     = '0';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']       = '100%';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = '63px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['text-align']  = 'center';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size']   = '25px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-family'] = "'icomoon'";

			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows:before']['margin-left']  = '-2px';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows:before']['margin-left'] = '-1px';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['left']  = 'auto';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['right'] = '0';

			$elements = array(
				'.no-rgba .rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows',
				'.no-rgba .rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = '#ccc';

			$elements = array(
				'.rev_slider_wrapper:hover .rev_slider .tp-leftarrow.tparrows',
				'.rev_slider_wrapper:hover .rev_slider .tp-rightarrow.tparrows',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity'] = '0.8 !important';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows:hover',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows:hover',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity'] = '1 !important';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows']['background-position'] = '19px 19px';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows']['left']                = '0';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows']['margin-left']         = '0';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows']['z-index']             = '100';

			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['background-position'] = '29px 19px';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['right']               = '0';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['margin-left']         = '0';
			$css['global']['.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows']['z-index']             = '100';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows.hidearrows',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows.hidearrows',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['opacity'] = '0';

			// Additional arrow styles.
			$css['global']['.rev_slider_wrapper .rev_slider .tparrows.hades .tp-arr-allwrapper']['width']    = '63px';
			$css['global']['.rev_slider_wrapper .rev_slider .tparrows.hades .tp-arr-allwrapper']['height']    = '63px';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows.hebe:before',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows.hebe:before',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['position']    = 'relative';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width']       = 'auto';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows.zeus',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows.zeus',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['min-width']    = '63px';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['min-height']    = '63px';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows.zeus .tp-title-wrap',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows.zeus .tp-title-wrap',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-radius']    = '0';

			$elements = array(
				'.rev_slider_wrapper .rev_slider .tp-leftarrow.tparrows.metis',
				'.rev_slider_wrapper .rev_slider .tp-rightarrow.tparrows.metis',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding']    = '0';
		} // End if().

		$css['global']['.tp-bullets .bullet.last']['clear'] = 'none';

	} // End if().

	if ( '' != Avada()->settings->get( 'content_bg_image', 'url' ) && ! get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true ) ) {

		$css['global']['#main']['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'content_bg_image', 'url' ) ) . '")';
		$css['global']['#main']['background-repeat'] = esc_attr( Avada()->settings->get( 'content_bg_repeat' ) );

		if ( Avada()->settings->get( 'content_bg_full' ) ) {

			$css['global']['#main']['background-attachment'] = 'fixed';
			$css['global']['#main']['background-position']   = 'center center';
			$css['global']['#main']['background-size']       = 'cover';

		}
	}

	if ( ( Avada()->settings->get( 'main_padding', 'top' ) || Avada()->settings->get( 'main_padding', 'top' ) == '0' ) && ( ( ! get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) && get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) !== '0' ) || ! $c_page_id ) ) {
		$css['global']['#main']['padding-top'] = Fusion_Sanitize::size( Avada()->settings->get( 'main_padding', 'top' ) );
	}

	if ( ( Avada()->settings->get( 'main_padding', 'bottom' ) || Avada()->settings->get( 'main_padding', 'bottom' ) == '0' ) && ( ( ! get_post_meta( $c_page_id, 'pyre_main_bottom_padding', true ) &&  get_post_meta( $c_page_id, 'pyre_main_bottom_padding', true ) !== '0' ) || ! $c_page_id ) ) {
		$css['global']['#main']['padding-bottom'] = Fusion_Sanitize::size( Avada()->settings->get( 'main_padding', 'bottom' ) );
	}

	if ( 'wide' == get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) && get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true ) ) {
		$elements = array( 'html', 'body', '#wrapper' );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true );
	}

	if ( get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true ) ) {
		$elements = array(
			'#main',
			'#wrapper',
			'.fusion-separator .icon-wrapper',
		);
		if ( class_exists( 'bbPress' ) ) {
			$elements[] = '.bbp-arrow';
		}
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true );

		$alpha = 1;
		if ( class_exists( 'Fusion_Color' ) ) {
			$alpha = Fusion_Color::new_color( get_post_meta( $c_page_id, 'pyre_wide_page_bg_color', true ) )->alpha;
		}

		if ( 1 > $alpha ) {
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-blend-mode'] = 'overlay';
		}
	}

	if ( get_post_meta( $c_page_id, 'pyre_wide_page_bg', true ) ) {
		$elements = array(
			'.wrapper_blank #main',
			'#main',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image']  = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_wide_page_bg', true ) ) . '")';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-repeat'] = get_post_meta( $c_page_id, 'pyre_wide_page_bg_repeat', true );

		if ( 'yes' == get_post_meta( $c_page_id, 'pyre_wide_page_bg_full', true ) ) {

			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-attachment'] = 'fixed';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-position']   = 'center center';
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-size']       = 'cover';

		}
	}

	if ( get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) || get_post_meta( $c_page_id, 'pyre_main_top_padding', true ) === '0' ) {
		$css['global']['#main']['padding-top'] = get_post_meta( $c_page_id, 'pyre_main_top_padding', true );
	}

	if ( get_post_meta( $c_page_id, 'pyre_main_bottom_padding', true ) || get_post_meta( $c_page_id, 'pyre_main_bottom_padding', true ) === '0' ) {
		$css['global']['#main']['padding-bottom'] = get_post_meta( $c_page_id, 'pyre_main_bottom_padding', true );
	}

	if ( get_post_meta( $c_page_id, 'pyre_sidebar_bg_color', true ) ) {
		$elements = array(
			'#main .sidebar:not( .fusion-sticky-sidebar )',
			'#main .fusion-sticky-sidebar .fusion-sidebar-inner-content',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = get_post_meta( $c_page_id, 'pyre_sidebar_bg_color', true );
	}

	if ( Avada()->settings->get( 'page_title_bg_full' ) ) {
		$css['global']['.fusion-page-title-bar']['background-size'] = 'cover';
	}

	if ( 'yes' == get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_full', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-size'] = 'cover';
	} elseif ( 'no' == get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_full', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-size'] = 'auto';
	}

	if ( Avada()->settings->get( 'page_title_bg_parallax' ) ) {
		$css['global']['.fusion-page-title-bar']['background-attachment'] = 'fixed';
		$css['global']['.fusion-page-title-bar']['background-position']   = 'top center';
	}

	if ( 'yes' == get_post_meta( $c_page_id, 'pyre_page_title_bg_parallax', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-attachment'] = 'fixed';
		$css['global']['.fusion-page-title-bar']['background-position']   = 'top center';
	} elseif ( 'no' == get_post_meta( $c_page_id, 'pyre_page_title_bg_parallax', true ) ) {
		$css['global']['.fusion-page-title-bar']['background-attachment'] = 'scroll';
	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_height', true ) ) {
		$css['global']['.fusion-page-title-bar']['height'] = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_page_title_height', true ) );
	} else {
		$css['global']['.fusion-page-title-bar']['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'page_title_height' ) );
	}

	if ( is_single() && get_post_meta( $c_page_id, 'pyre_fimg_width', true ) ) {

		if ( 'auto' != get_post_meta( $c_page_id, 'pyre_fimg_width', true ) ) {
			$css['global'][ '#post-' . $c_page_id . ' .fusion-post-slideshow' ]['max-width'] = get_post_meta( $c_page_id, 'pyre_fimg_width', true );
		} else {
			$css['global']['.fusion-post-slideshow .flex-control-nav']['position']   = 'relative';
			$css['global']['.fusion-post-slideshow .flex-control-nav']['text-align'] = 'center';
			$css['global']['.fusion-post-slideshow .flex-control-nav']['margin-top'] = '10px';

			$css['global'][ '#post-' . $c_page_id . ' .fusion-post-slideshow img' ]['width'] = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_fimg_width', true ) );
		}

		$css['global'][ '#post-' . $c_page_id . ' .fusion-post-slideshow img' ]['max-width'] = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_fimg_width', true ) );
	}

	if ( is_single() && get_post_meta( $c_page_id, 'pyre_fimg_height', true ) ) {
		$elements = array(
			'#post-' . $c_page_id . ' .fusion-post-slideshow',
			'#post-' . $c_page_id . ' .fusion-post-slideshow img',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-height'] = get_post_meta( $c_page_id, 'pyre_fimg_height', true );
		$css['global'][ '#post-' . $c_page_id . ' .fusion-post-slideshow .slides' ]['max-height'] = '100%';
	}

	// Page Title Bar Retina.
	if ( get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_retina', true ) ) {
		$media_query = '@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi), only screen and (min-resolution: 1.5dppx)';
		$css[ $media_query ]['.fusion-page-title-bar']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( get_post_meta( $c_page_id, 'pyre_page_title_bar_bg_retina', true ) ) . '")';
		$css[ $media_query ]['.fusion-page-title-bar']['background-size']  = 'cover';
	} elseif ( '' != Avada()->settings->get( 'page_title_bg_retina', 'url' ) ) {
		$media_query = '@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi), only screen and (min-resolution: 1.5dppx)';
		$css[ $media_query ]['.fusion-page-title-bar']['background-image'] = 'url("' . Fusion_Sanitize::css_asset_url( Avada()->settings->get( 'page_title_bg_retina', 'url' ) ) . '")';
		$css[ $media_query ]['.fusion-page-title-bar']['background-size']  = 'cover';
	}

	if ( ( 'content_only' == Avada()->settings->get( 'page_title_bar' ) && ( 'default' == get_post_meta( $c_page_id, 'pyre_page_title', true ) || ! get_post_meta( $c_page_id, 'pyre_page_title', true ) ) ) || 'yes_without_bar' == get_post_meta( $c_page_id, 'pyre_page_title', true ) ) {
		$css['global']['.fusion-page-title-bar']['background'] = 'none';
		$css['global']['.fusion-page-title-bar']['border']     = 'none';
	}

	$elements = array(
		'.width-100 .nonhundred-percent-fullwidth',
	);

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']  = $hundredplr_padding_negative_margin;
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = $hundredplr_padding_negative_margin;

	if ( (float) $wp_version < 3.8 ) {
		$css['global']['#wpadminbar *']['color'] = '#ccc';
		$elements = array(
			'#wpadminbar .hover a',
			'#wpadminbar .hover a span',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = '#464646';
	}

	if ( class_exists( 'WooCommerce' ) ) {

		$css['global']['.woocommerce-invalid:after']['content']    = __( 'Please enter correct details for this required field.', 'Avada' );
		$css['global']['.woocommerce-invalid:after']['display']    = 'inline-block';
		$css['global']['.woocommerce-invalid:after']['margin-top'] = '7px';
		$css['global']['.woocommerce-invalid:after']['color']      = 'red';

	}

	if ( 'no' != get_post_meta( get_queried_object_id(), 'pyre_display_header', true ) ) {

		$elements = array(
			'body.side-header-left #wrapper',
			'.side-header-left .fusion-footer-parallax',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

		$elements = array(
			'body.side-header-right #wrapper',
			'.side-header-right .fusion-footer-parallax',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

		$elements = array(
			'body.side-header-left #side-header #nav > ul > li > ul',
			'body.side-header-left #side-header #nav .login-box',
			'body.side-header-left #side-header #nav .main-nav-search-form',
		);
		if ( class_exists( 'WooCommerce' ) ) {
			$elements[] = 'body.side-header-left #side-header #nav .cart-contents';
		}
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['left'] = ( intval( Avada()->settings->get( 'side_header_width' ) ) - 1 ) . 'px';

		if ( is_rtl() ) {
			$css['global']['.rtl.side-header-left #boxed-wrapper']['direction'] = 'ltr';
			$css['global']['.rtl.side-header-left #side-header']['direction'] = 'rtl';
			$css['global']['.rtl.side-header-left #sliders-container']['direction'] = 'rtl';
			$css['global']['.rtl.side-header-left #main']['direction'] = 'rtl';
			$css['global']['.rtl.side-header-left .fusion-footer']['direction'] = 'rtl';
		}

		if ( 'Boxed' != Avada()->settings->get( 'layout' ) && 'boxed' != get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) {

			$elements = array(
				'body.side-header-left #slidingbar .avada-row',
				'body.side-header-right #slidingbar .avada-row',
			);
			$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = 'none';

		}
	} // End if().

	if ( ( ( 'Boxed' === Avada()->settings->get( 'layout' ) && 'wide' !== get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) || 'boxed' === get_post_meta( $c_page_id, 'pyre_page_bg_layout', true ) ) && 'Top' !== Avada()->settings->get( 'header_position' ) ) {

		$css['global']['#boxed-wrapper']['min-height'] = '100vh';

		$elements = array(
			'#boxed-wrapper',
			'#slidingbar-area .fusion-row',
			'.fusion-footer-parallax',
			'body > #lang_sel_footer',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-left'] = 'auto';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['margin-right'] = 'auto';
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );

		if ( $site_width_percent ) {
			$css['global']['#wrapper']['max-width'] = 'none';
		}

		if ( 'Left' === Avada()->settings->get( 'header_position' ) ) {

			$css['global']['body.side-header-left #side-header']['left']        = 'auto';
			$css['global']['body.side-header-left #side-header']['margin-left'] = '-' . intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

			$css['global']['.side-header-left .fusion-footer-parallax']['margin'] = '0 auto';
			$css['global']['.side-header-left .fusion-footer-parallax']['padding-left'] = intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

		} else {

			$css['global']['#boxed-wrapper']['position'] = 'relative';

			$css['global']['body.admin-bar #wrapper #slidingbar-area']['top'] = '0';

			$css['global']['.side-header-right .fusion-footer-parallax']['margin'] = '0 auto';
			$css['global']['.side-header-right .fusion-footer-parallax']['padding-right'] = intval( Avada()->settings->get( 'side_header_width' ) ) . 'px';

			$media_query = '@media only screen and (min-width: ' . intval( Avada()->settings->get( 'side_header_break_point' ) ) . 'px)';
			$css[ $media_query ]['body.side-header-right #side-header']['position'] = 'absolute';
			$css[ $media_query ]['body.side-header-right #side-header']['top']      = '0';
			$css[ $media_query ]['body.side-header-right #side-header']['right'] = '0';

			$css[ $media_query ]['body.side-header-right #side-header .side-header-wrapper']['position'] = 'fixed';

		}
	} // End if().

	if ( is_page_template( 'contact.php' ) && Avada()->settings->get( 'gmap_address' ) && Avada()->settings->get( 'status_gmap' ) ) {

		$css['global']['.avada-google-map']['width']  = Fusion_Sanitize::size( Avada()->settings->get( 'gmap_dimensions', 'width' ) );
		$css['global']['.avada-google-map']['margin'] = '0 auto';

		if ( '100%' != Avada()->settings->get( 'gmap_dimensions', 'width' ) ) {
			$margin_top = ( Avada()->settings->get( 'gmap_topmargin' ) ) ? Avada()->settings->get( 'gmap_topmargin' ) : '55px';
			$css['global']['.avada-google-map']['margin-top'] = Fusion_Sanitize::size( $margin_top );
		}

		$gmap_height = ( Avada()->settings->get( 'gmap_dimensions', 'height' ) ) ? Avada()->settings->get( 'gmap_dimensions', 'height' ) : '415px';
		$css['global']['.avada-google-map']['height'] = Fusion_Sanitize::size( $gmap_height );

	} elseif ( is_page_template( 'contact-2.php' ) && Avada()->settings->get( 'gmap_address' ) && Avada()->settings->get( 'status_gmap' ) ) {

		$css['global']['.avada-google-map']['margin']     = '0 auto';
		$css['global']['.avada-google-map']['margin-top'] = '55px';
		$css['global']['.avada-google-map']['height']     = '415px !important';
		$css['global']['.avada-google-map']['width']      = '940px !important';

	}

	if ( 'yes' == get_post_meta( $c_page_id, 'pyre_footer_100_width', true ) ) {

		$elements = array(
			'.layout-wide-mode .fusion-footer-widget-area > .fusion-row',
			'.layout-wide-mode .fusion-footer-copyright-area > .fusion-row',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = '100% !important';

	} elseif ( 'no' == get_post_meta( $c_page_id, 'pyre_footer_100_width', true ) ) {

		$elements = array(
			'.layout-wide-mode .fusion-footer-widget-area > .fusion-row',
			'.layout-wide-mode .fusion-footer-copyright-area > .fusion-row',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['max-width'] = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) ) . ' !important';

	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_font_color', true ) && '' != get_post_meta( $c_page_id, 'pyre_page_title_font_color', true ) ) {

		$elements = array(
			'.fusion-page-title-bar h1',
			'.fusion-page-title-bar h3',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = Fusion_Sanitize::color( get_post_meta( $c_page_id, 'pyre_page_title_font_color', true ) );

	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_text_size', true ) && '' != get_post_meta( $c_page_id, 'pyre_page_title_text_size', true ) ) {
		$css['global']['.fusion-page-title-bar .fusion-page-title-row h1']['font-size']   = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_page_title_text_size', true ) );
	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_line_height', true ) && '' != get_post_meta( $c_page_id, 'pyre_page_title_line_height', true ) ) {
		$css['global']['.fusion-page-title-bar .fusion-page-title-row h1']['line-height'] = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_page_title_line_height', true ) );
	}

	if ( get_post_meta( $c_page_id, 'pyre_page_title_custom_subheader_text_size', true ) && '' != get_post_meta( $c_page_id, 'pyre_page_title_custom_subheader_text_size', true ) ) {
		$css['global']['.fusion-page-title-bar h3']['font-size']   = Fusion_Sanitize::size( get_post_meta( $c_page_id, 'pyre_page_title_custom_subheader_text_size', true ) );
		$css['global']['.fusion-page-title-bar h3']['line-height'] = Fusion_Sanitize::add_css_values( array( Fusion_Sanitize::size( Avada()->settings->get( 'page_title_subheader_font_size' ) ), '12px' ) );
	}

	if ( 'yes' == get_post_meta( $c_page_id, 'pyre_page_title_100_width', true ) ) {
		$css['global']['.layout-wide-mode .fusion-page-title-row']['max-width'] = '100%';
	}

	$header_width = Fusion_Sanitize::size( Avada()->settings->get( 'header_100_width' ) );

	if ( 'yes' == get_post_meta( $c_page_id, 'pyre_header_100_width', true ) ) {
		$header_width = true;
	} elseif ( 'no' == get_post_meta( $c_page_id, 'pyre_header_100_width', true ) ) {
		$header_width = false;
	}

	if ( $header_width ) {
		$css['global']['.layout-wide-mode .fusion-header-wrapper .fusion-row']['max-width'] = '100%';
	}

	if ( ! Avada()->settings->get( 'responsive' ) ) {
		$css['global']['body']['min-width']  = Fusion_Sanitize::size( Avada()->settings->get( 'site_width' ) );

		if ( ! $site_width_percent ) {
			$css['global']['html']['overflow-x'] = 'auto';
			$css['global']['body']['overflow-x'] = 'auto';
		}
	}

	$elements = array(
		'.fusion-flexslider .flex-direction-nav a',
		'.fusion-flexslider.flexslider-posts .flex-direction-nav a',
		'.fusion-flexslider.flexslider-posts-with-excerpt .flex-direction-nav a',
		'.fusion-flexslider.flexslider-attachments .flex-direction-nav a',
		'.fusion-slider-sc .flex-direction-nav a',
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$elements[] = '.woocommerce-product-gallery .flex-direction-nav a';
	}

	$carousel_elements = array(
		'.fusion-carousel .fusion-carousel-nav .fusion-nav-prev',
		'.fusion-carousel .fusion-carousel-nav .fusion-nav-next',
	);

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'width' ) );
	$css['global'][ $dynamic_css_helpers->implode( $carousel_elements ) ]['width'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'width' ) );

	preg_match_all( '!\d+!', Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ), $matches );
	$half_slider_nav_box_height = $matches[0][0] / 2 . Fusion_Sanitize::get_unit( Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ) );

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ) );
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ) );

	$css['global'][ $dynamic_css_helpers->implode( $carousel_elements ) ]['height'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ) );
	$css['global'][ $dynamic_css_helpers->implode( $carousel_elements ) ]['margin-top'] = '-' . $half_slider_nav_box_height;

	$carousel_elements = array(
		'.fusion-carousel .fusion-carousel-nav .fusion-nav-prev:before',
		'.fusion-carousel .fusion-carousel-nav .fusion-nav-next:before',
	);

	$css['global'][ $dynamic_css_helpers->implode( $carousel_elements ) ]['line-height'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_nav_box_dimensions', 'height' ) );

	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_arrow_size' ) );

	$css['global'][ $dynamic_css_helpers->implode( $carousel_elements ) ]['font-size'] = Fusion_Sanitize::size( Avada()->settings->get( 'slider_arrow_size' ) );

	$elements = array(
		'.pagination a.inactive',
		'.page-links a',
		'.woocommerce-pagination .page-numbers',
		'.bbp-pagination .bbp-pagination-links a.inactive',
		'.bbp-topic-pagination .page-numbers',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'height' ) ) . ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'width' ) );

	$elements = array(
		'.pagination .current',
		'.page-links > .page-number',
		'.woocommerce-pagination .current',
		'.bbp-pagination .bbp-pagination-links .current',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'height' ) ) . ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'width' ) );

	$elements = array(
		'.pagination .pagination-prev',
		'.woocommerce-pagination .prev',
		'.fusion-hide-pagination-text .woocommerce-pagination .prev',
		'.bbp-pagination .bbp-pagination-links .pagination-prev',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'height' ) ) . ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'width' ) );

	$elements = array(
		'.pagination .pagination-next',
		'.woocommerce-pagination .next',
		'.fusion-hide-pagination-text .woocommerce-pagination .next',
		'.bbp-pagination .bbp-pagination-links .pagination-next',
		'.bbp-pagination-links span.dots',
	);
	$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'height' ) ) . ' ' . Fusion_Sanitize::size( Avada()->settings->get( 'pagination_box_padding', 'width' ) );

	if ( ! Avada()->settings->get( 'pagination_text_display' ) ) {
		$elements = array(
			'.fusion-hide-pagination-text .page-text'
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'none';

		$css['global']['.fusion-hide-pagination-text .pagination-prev, .fusion-hide-pagination-text .pagination-next']['border-width'] = '1px';
		$css['global']['.fusion-hide-pagination-text .pagination-prev, .fusion-hide-pagination-text .pagination-next']['border-style'] = 'solid';
		$css['global']['.fusion-hide-pagination-text .pagination-prev']['margin'] = '0';
		$css['global']['.fusion-hide-pagination-text .pagination-next, .fusion-hide-pagination-text .fusion-blog-pagination .pagination-next']['margin-left'] = '5px';
		$css['global']['.fusion-hide-pagination-text .pagination-prev:before, .fusion-hide-pagination-text .pagination-next:after']['line-height'] = 'normal';
		$css['global']['.fusion-hide-pagination-text .pagination-prev:before, .fusion-hide-pagination-text .pagination-next:after']['position'] = 'relative';
		$css['global']['.fusion-hide-pagination-text .pagination-prev:before, .fusion-hide-pagination-text .pagination-next:after']['margin'] = '0';
		$css['global']['.fusion-hide-pagination-text .pagination-prev:before, .fusion-hide-pagination-text .pagination-next:after']['padding'] = '0';

		$css['global']['.fusion-hide-pagination-text .pagination-next:after']['right'] = 'auto';

		if ( class_exists( 'WooCommerce' ) ) {
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev, .fusion-hide-pagination-text .woocommerce-pagination .next']['border-width'] = '1px';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev, .fusion-hide-pagination-text .woocommerce-pagination .next']['border-style'] = 'solid';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev']['margin'] = '0';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .next']['margin-left'] = '5px';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev:before, .fusion-hide-pagination-text .woocommerce-pagination .next:after']['line-height'] = 'normal';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev:before, .fusion-hide-pagination-text .woocommerce-pagination .next:after']['position'] = 'relative';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev:before, .fusion-hide-pagination-text .woocommerce-pagination .next:after']['margin'] = '0';
			$css['global']['.fusion-hide-pagination-text .woocommerce-pagination .prev:before, .fusion-hide-pagination-text .woocommerce-pagination .next:after']['padding'] = '0';
		}

		if ( class_exists( 'bbPress' ) ) {
			$css['global']['.fusion-hide-pagination-text  .bbp-pagination-links .page-text']['display'] = 'none';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next']['border-width'] = '1px';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next']['border-style'] = 'solid';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev']['margin'] = '0';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-next']['margin-left'] = '5px';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev:before, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next:after']['line-height'] = 'normal';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev:before, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next:after']['position'] = 'relative';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev:before, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next:after']['margin'] = '0';
			$css['global']['.fusion-hide-pagination-text .bbp-pagination-links .pagination-prev:before, .fusion-hide-pagination-text .bbp-pagination-links .pagination-next:after']['padding'] = '0';
		}
	} // End if().

	// Button styling.
	if ( ! class_exists( 'FusionBuilder' ) ) {
		$elements = array(
			'.fusion-button-default',
			'.wpcf7-submit',
			'.comment-form .fusion-button-default',
			'.single_add_to_cart_button',
			'.comment-respond .submit',
		);
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = Fusion_Sanitize::color( Avada()->settings->get( 'primary_color' ) );
		$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = '#ffffff';
	}

	/**
	 * Hack to fix font-names using '+' instead of ' '.
	 * This happens when using WPML coupled with the string-translation plugin.
	 * FIxes #3309
	 */
	if ( defined( 'ICL_SITEPRESS_VERSION' || class_exists( 'SitePress' ) ) ) {
		foreach ( $css as $media_query => $elements ) {
			foreach ( $elements as $element => $properties ) {
				foreach ( $properties as $property => $value ) {
					if ( 'font-family' === $property ) {
						$css[ $media_query ][ $element ][ $property ] = str_replace( '+', ' ', $value );
					}
				}
			}
		}
	}

	$avada_dynamic_css_array_added = true;

	$css = array_replace_recursive( $css, $original_css );

	return apply_filters( 'avada_dynamic_css_array', $css );

}
add_filter( 'fusion_dynamic_css_array', 'avada_dynamic_css_array', 999 );