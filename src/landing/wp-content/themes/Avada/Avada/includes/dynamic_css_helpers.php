<?php
/**
 * Dynamic-css helpers.
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
 * Takes care of adding custom fonts using @font-face.
 *
 * @param  string $css The CSS.
 * @return  string
 */
function avada_custom_fonts_font_faces( $css = '' ) {
	// Get the options.
	$options   = get_option( Avada::get_option_name(), array() );
	$font_face = '';
	// Make sure 'custom_fonts' are defined.
	if ( isset( $options['custom_fonts'] ) ) {
		$custom_fonts = $options['custom_fonts'];
		// Make sure we have titles for our fonts.
		if ( isset( $custom_fonts['name'] ) && is_array( $custom_fonts['name'] ) ) {
			foreach ( $custom_fonts['name'] as $key => $label ) {
				// Make sure we have some files to work with.
				$process = false;
				foreach ( array( 'woff', 'woff2', 'ttf', 'svg', 'eot' ) as $filetype ) {
					if ( ! $process && isset( $custom_fonts[ $filetype ] ) && isset( $custom_fonts[ $filetype ][ $key ] ) ) {
						$process = true;
					}
				}
				// If we don't have any files to process then skip this item.
				if ( ! $process ) {
					continue;
				}

				$firstfile = true;
				$font_face .= '@font-face{';
					$font_face .= 'font-family:';
					// If font-name has a space, then it must be wrapped in double-quotes.
				if ( false !== strpos( $label, ' ' ) ) {
					$font_face .= '"' . $label . '";';
				} else {
					$font_face .= $label . ';';
				}
				// Start adding sources.
				$font_face .= 'src:';
				// Add .eot file.
				if ( isset( $custom_fonts['eot'] ) && isset( $custom_fonts['eot'][ $key ] ) && $custom_fonts['eot'][ $key ]['url'] ) {
					$font_face .= 'url("' . str_replace( array( 'http://', 'https://' ), '//', $custom_fonts['eot'][ $key ]['url'] ) . '?#iefix") format("embedded-opentype")';
					$firstfile = false;
				}
				// Add .woff file.
				if ( isset( $custom_fonts['woff'] ) && isset( $custom_fonts['woff'][ $key ] ) && $custom_fonts['woff'][ $key ]['url'] ) {
					$font_face .= ( $firstfile ) ? '' : ',';
					$font_face .= 'url("' . str_replace( array( 'http://', 'https://' ), '//', $custom_fonts['woff'][ $key ]['url'] ) . '") format("woff")';
					$firstfile = false;
				}
				// Add .woff2 file.
				if ( isset( $custom_fonts['woff2'] ) && isset( $custom_fonts['woff2'][ $key ]['url'] ) && $custom_fonts['woff2'][ $key ]['url'] ) {
					$font_face .= ( $firstfile ) ? '' : ',';
					$font_face .= 'url("' . str_replace( array( 'http://', 'https://' ), '//', $custom_fonts['woff2'][ $key ]['url'] ) . '") format("woff2")';
					$firstfile = false;
				}
				// Add .ttf file.
				if ( isset( $custom_fonts['ttf'] ) && isset( $custom_fonts['ttf'][ $key ] ) && $custom_fonts['ttf'][ $key ]['url'] ) {
					$font_face .= ( $firstfile ) ? '' : ',';
					$font_face .= 'url("' . str_replace( array( 'http://', 'https://' ), '//', $custom_fonts['ttf'][ $key ]['url'] ) . '") format("truetype")';
					$firstfile = false;
				}
				// Add .svg file.
				if ( isset( $custom_fonts['svg'] ) && isset( $custom_fonts['svg'][ $key ] ) && $custom_fonts['svg'][ $key ]['url'] ) {
					$font_face .= ( $firstfile ) ? '' : ',';
					$font_face .= 'url("' . str_replace( array( 'http://', 'https://' ), '//', $custom_fonts['svg'][ $key ]['url'] ) . '") format("svg")';
					$firstfile = false;
				}
				$font_face .= ';font-weight: normal;font-style: normal;';
				$font_face .= '}';
			} // End foreach().
		} // End if().
	} // End if().
	return $font_face . $css;
}
add_filter( 'avada_dynamic_css', 'avada_custom_fonts_font_faces' );


/**
 * Avada body, h1, h2, h3, h4, h5, h6 typography.
 */

/**
 * CSS classes that inherit Avada's body typography settings.
 *
 * @return array
 */
function avada_get_body_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit body font size.
	$typography_elements['size'] = array(
		'body',
		'.sidebar .slide-excerpt h2',
		'.fusion-footer-widget-area .slide-excerpt h2',
		'#slidingbar-area .slide-excerpt h2',
		'.jtwt .jtwt_tweet',
		'.sidebar .jtwt .jtwt_tweet',
		'.project-content .project-info h4',
		'.gform_wrapper label',
		'.gform_wrapper .gfield_description',
		'.fusion-footer-widget-area ul',
		'#slidingbar-area ul',
		'.fusion-tabs-widget .tab-holder .news-list li .post-holder a',
		'.fusion-tabs-widget .tab-holder .news-list li .post-holder .meta',
		'.fusion-blog-layout-timeline .fusion-timeline-date',
		'.post-content blockquote',
		'.review blockquote q',

	);
	// CSS classes that inherit body font color.
	$typography_elements['color'] = array(
		'body',
		'.post .post-content',
		'.post-content blockquote',
		'#wrapper .fusion-tabs-widget .tab-holder .news-list li .post-holder .meta',
		'.sidebar .jtwt',
		'#wrapper .meta',
		'.review blockquote div',
		'.search input',
		'.project-content .project-info h4',
		'.title-row',
		'.fusion-rollover .price .amount',
		'.fusion-blog-timeline-layout .fusion-timeline-date',
		'#reviews #comments > h2',
		'.sidebar .widget_nav_menu li',
		'.sidebar .widget_categories li',
		'.sidebar .widget_product_categories li',
		'.sidebar .widget_meta li',
		'.sidebar .widget .recentcomments',
		'.sidebar .widget_recent_entries li',
		'.sidebar .widget_archive li',
		'.sidebar .widget_pages li',
		'.sidebar .widget_links li',
		'.sidebar .widget_layered_nav li',
		'.sidebar .widget_product_categories li',
		'body .sidebar .fusion-tabs-widget .tab-holder .tabs li a',
		'.sidebar .fusion-tabs-widget .tab-holder .tabs li a',
		'.fusion-main-menu .fusion-custom-menu-item-contents',

	);
	// CSS classes that inherit body font.
	$typography_elements['family'] = array(
		'body',
		'#nav ul li ul li a',
		'#sticky-nav ul li ul li a',
		'.more',
		'.avada-container h3',
		'.meta .fusion-date',
		'.review blockquote q',
		'.review blockquote div strong',
		'.post-content blockquote',
		'.fusion-load-more-button',
		'.ei-title h3',
		'.comment-form input[type="submit"]',
		'.fusion-page-title-bar h3',
		'#reviews #comments > h2',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-title',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content a',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .price',
		'#wrapper #nav ul li ul li > a',
		'#wrapper #sticky-nav ul li ul li > a',
		'.ticket-selector-submit-btn[type=submit]',
		'.gform_page_footer input[type=button]',
		'.fusion-main-menu .sub-menu',
		'.fusion-main-menu .sub-menu li a',
		'.fusion-megamenu-wrapper li .fusion-megamenu-title-disabled',
		'.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover',
		'.fusion-megamenu-widgets-container',
	);

	// CSS classes that inherit body font.
	$typography_elements['line-height'] = array(
		'body',
		'#nav ul li ul li a',
		'#sticky-nav ul li ul li a',
		'.more',
		'.avada-container h3',
		'.meta .fusion-date',
		'.review blockquote q',
		'.review blockquote div strong',
		'.post-content blockquote',
		'.ei-title h3',
		'.comment-form input[type="submit"]',
		'.fusion-page-title-bar h3',
		'#reviews #comments > h2',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-title',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-categories',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content a',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .price',
		'#wrapper #nav ul li ul li > a',
		'#wrapper #sticky-nav ul li ul li > a',
		'.ticket-selector-submit-btn[type=submit]',
		'.gform_page_footer input[type=button]',
		'.fusion-main-menu .sub-menu',
		'.fusion-main-menu .sub-menu li a',
		'.fusion-megamenu-wrapper li .fusion-megamenu-title-disabled',
		'.fusion-megamenu-wrapper .fusion-megamenu-submenu > a:hover',
		'.fusion-megamenu-widgets-container',
		'.fusion-accordian .panel-body',
		'#side-header .fusion-contact-info',
		'#side-header .header-social .top-menu',

	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H1 typography settings.
 *
 * @return array
 */
function avada_get_h1_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h1 size.
	$typography_elements['size'] = array(
		// 'h1',
		'.post-content h1',
		'.fusion-modal h1',
		'.fusion-widget-area h1',
	);
	// CSS classes that inherit h1 font family.
	$typography_elements['family'] = array(
		// 'h1',
		'.post-content h1',
		'.fusion-page-title-bar h1',
		'.fusion-modal h1',
		'.fusion-widget-area h1',
	);
	// CSS classes that inherit h1 color.
	$typography_elements['color'] = array(
		// 'h1',
		'.post-content h1',
		'.title h1',
		'.fusion-post-content h1',
		'.fusion-modal h1',
		'.fusion-widget-area h1',
	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H2 typography settings.
 *
 * @return array
 */
function avada_get_h2_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h2 size.
	$typography_elements['size'] = array(
		// 'h2',
		'#wrapper .post-content h2',
		'#wrapper .fusion-title h2',
		'#wrapper #main .post-content .fusion-title h2',
		'#wrapper .title h2',
		'#wrapper #main .post-content .title h2',
		'#main .post h2',
		'#wrapper  #main .post h2',
		'#main .fusion-portfolio h2',
		'h2.entry-title',
		'.fusion-modal h2',
		'.fusion-widget-area h2',
	);
	// CSS classes that inherit h2 color.
	$typography_elements['color'] = array(
		// 'h2',
		'#main .post h2',
		'.post-content h2',
		'.fusion-title h2',
		'.title h2',
		'.search-page-search-form h2',
		'.fusion-post-content h2',
		'.fusion-modal h2',
		'.fusion-widget-area h2',
	);
	// CSS classes that inherit h2 font family.
	$typography_elements['family'] = array(
		// 'h2',
		'#main .post h2',
		'.post-content h2',
		'.fusion-title h2',
		'.title h2',
		'#main .reading-box h2',
		'#main h2',
		'.ei-title h2',
		'.main-flex .slide-content h2',
		'.fusion-modal h2',
		'.fusion-widget-area h2',
	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H3 typography settings.
 *
 * @return array
 */
function avada_get_h3_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h3 font family.
	$typography_elements['family'] = array(
		// 'h3',
		'.post-content h3',
		'.project-content h3',
		'.sidebar .widget h3',
		'.main-flex .slide-content h3',
		'.fusion-author .fusion-author-title',
		'.fusion-header-tagline',
		'.fusion-modal h3',
		'.fusion-title h3',
		'.fusion-widget-area h3',
	);
	// CSS classes that inherit h3 size.
	$typography_elements['size'] = array(
		// 'h3',
		'.post-content h3',
		'.project-content h3',
		'.fusion-modal h3',
		'.fusion-widget-area h3',
	);

	// CSS classes that inherit h3 color.
	$typography_elements['color'] = array(
		// 'h3',
		'.post-content h3',
		'.sidebar .widget h3',
		'.project-content h3',
		'.fusion-title h3',
		'.title h3',
		'.fusion-post-content h3',
		'.fusion-modal h3',
		'.fusion-widget-area h3',
	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H4 typography settings.
 *
 * @return array
 */
function avada_get_h4_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h4 size.
	$typography_elements['size'] = array(
		// 'h4',
		'.post-content h4',
		'.fusion-portfolio-post .fusion-portfolio-content h4',
		'.fusion-rollover .fusion-rollover-content .fusion-rollover-title',
		'.fusion-carousel-title',
		'#wrapper .fusion-tabs-widget .tab-holder .tabs li a',
		'#reviews #comments > h2',
		'.fusion-accordian .panel-title',
		'.fusion-sharing-box h4',
		'.fusion-tabs .nav-tabs > li .fusion-tab-heading',
		'.fusion-modal h4',
		'.fusion-widget-area h4',
	);
	// CSS classes that inherit h4 color.
	$typography_elements['color'] = array(
		// 'h4',
		'.post-content h4',
		'.project-content .project-info h4',
		'.share-box h4',
		'.fusion-title h4',
		'.title h4',
		'#wrapper .fusion-tabs-widget .tab-holder .tabs li a',
		'.fusion-accordian .panel-title a',
		'.fusion-carousel-title',
		'.fusion-tabs .nav-tabs > li .fusion-tab-heading',
		'.fusion-post-content h4',
		'.fusion-modal h4',
		'.fusion-widget-area h4',
	);
	// CSS classes that inherit h4 font family.
	$typography_elements['family'] = array(
		// 'h4',
		'.post-content h4',
		'table th',
		'.fusion-megamenu-title',
		'.fusion-accordian .panel-title',
		'.fusion-carousel-title',
		'#wrapper .fusion-tabs-widget .tab-holder .tabs li a',
		'.share-box h4',
		'.project-content .project-info h4',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-title',
		'.fusion-image-wrapper .fusion-rollover .fusion-rollover-content .fusion-rollover-title a',
		'.fusion-modal h4',
		'.fusion-content-widget-area h4',
	);

	$typography_elements['line-height'] = array(
		// 'h4',
		'.project-content .project-info .project-terms',
		'.project-info-box span',
	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H5 typography settings.
 *
 * @return array
 */
function avada_get_h5_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h5 size.
	$typography_elements['size'] = array(
		// 'h5',
		'.post-content h5',
		'.fusion-modal h5',
		'.fusion-widget-area h5',
	);
	// CSS classes that inherit h5 color.
	$typography_elements['color'] = array(
		// 'h5',
		'.post-content h5',
		'.fusion-title h5',
		'.title h5',
		'.fusion-post-content h5',
		'.fusion-modal h5',
		'.fusion-widget-area h5',
	);
	// CSS classes that inherit h5 font family.
	$typography_elements['family'] = array(
		// 'h5',
		'.post-content h5',
		'.fusion-modal h5',
		'.fusion-widget-area h5',
	);

	return $typography_elements;
}

/**
 * CSS classes that inherit Avada's H6 typography settings.
 *
 * @return array
 */
function avada_get_h6_typography_elements() {
	$typography_elements = array();

	// CSS classes that inherit h6 size.
	$typography_elements['size'] = array(
		// 'h6',
		'.post-content h6',
		'.fusion-modal h6',
		'.fusion-widget-area h6',
	);
	// CSS classes that inherit h6 color.
	$typography_elements['color'] = array(
		// 'h6',
		'.post-content h6',
		'.fusion-title h6',
		'.title h6',
		'.fusion-post-content h6',
		'.fusion-modal h6',
		'.fusion-widget-area h6',
	);
	// CSS classes that inherit h6 font family.
	$typography_elements['family'] = array(
		// 'h6',
		'.post-content h6',
		'.fusion-modal h6',
		'.fusion-widget-area h6',
	);

	return $typography_elements;
}
