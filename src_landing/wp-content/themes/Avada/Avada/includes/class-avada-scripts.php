<?php
/**
 * Enqueues scripts and styles.
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
 * Handle enqueueing scrips.
 */
class Avada_Scripts {

	/**
	 * The theme version.
	 *
	 * @static
	 * @access private
	 * @since 5.0.0
	 * @var string
	 */
	private static $version;

	/**
	 * The CSS-compiling mode.
	 *
	 * @access private
	 * @since 5.1.5
	 * @var string
	 */
	private $compiler_mode;

	/**
	 * The class construction
	 *
	 * @access public
	 */
	public function __construct() {
		self::$version = Avada::get_theme_version();

		$dynamic_css_obj     = Fusion_Dynamic_CSS::get_instance();
		$this->compiler_mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;

		if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
			add_action( 'wp', array( $this, 'wp_action' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'script_loader_tag', array( $this, 'add_async' ), 10, 2 );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'woocommerce_enqueue_styles', array( $this, 'remove_woo_scripts' ) );
		}

		add_filter( 'fusion_dynamic_css_final', array( $this, 'combine_stylesheets' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ), 999 );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );

	}

	/**
	 * A method that runs on 'wp'.
	 *
	 * @access public
	 * @since 5.1.0
	 * @return void
	 */
	public function wp_action() {

		$this->enqueue_scripts();
		$this->localize_scripts();

	}

	/**
	 * Adds our scripts using Fusion_Dynamic_JS.
	 *
	 * @access protected
	 * @since 5.1.0
	 * @return void
	 */
	protected function enqueue_scripts() {

		global $wp_styles, $woocommerce, $fusion_library;
		$multilingual = $fusion_library->multilingual;

		$page_id = Avada()->fusion_library->get_page_id();

		$js_folder_suffix = '/assets/min/js';
		$js_folder_url = Avada::$template_dir_url . $js_folder_suffix;
		$js_folder_path = Avada::$template_dir_path . $js_folder_suffix;

		$scripts = array(
			array(
				'bootstrap-scrollspy',
				$js_folder_url . '/library/bootstrap.scrollspy.js',
				$js_folder_path . '/library/bootstrap.scrollspy.js',
				array( 'jquery' ),
				'3.3.2',
				true,
			),
			array(
				'avada-comments',
				$js_folder_url . '/general/avada-comments.js',
				$js_folder_path . '/general/avada-comments.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			array(
				'avada-general-footer',
				$js_folder_url . '/general/avada-general-footer.js',
				$js_folder_path . '/general/avada-general-footer.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			array(
				'avada-quantity',
				$js_folder_url . '/general/avada-quantity.js',
				$js_folder_path . '/general/avada-quantity.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			array(
				'avada-scrollspy',
				$js_folder_url . '/general/avada-scrollspy.js',
				$js_folder_path . '/general/avada-scrollspy.js',
				// @codingStandardsIgnoreLine
				( ! is_page_template( 'blank.php' ) && 'no' != fusion_get_page_option( 'display_header', $page_id ) ) ? array( 'avada-header', 'fusion-waypoints', 'bootstrap-scrollspy' ) : array( 'fusion-waypoints', 'bootstrap-scrollspy' ),
				self::$version,
				true,
			),
			array(
				'avada-select',
				$js_folder_url . '/general/avada-select.js',
				$js_folder_path . '/general/avada-select.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			array(
				'avada-sidebars',
				$js_folder_url . '/general/avada-sidebars.js',
				$js_folder_path . '/general/avada-sidebars.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			$scripts[] = array(
				'jquery-sticky-kit',
				$js_folder_url . '/library/jquery.sticky-kit.js',
				$js_folder_path . '/library/jquery.sticky-kit.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
			array(
				'avada-tabs-widget',
				$js_folder_url . '/general/avada-tabs-widget.js',
				$js_folder_path . '/general/avada-tabs-widget.js',
				array( 'jquery' ),
				self::$version,
				true,
			),
		);

		// Conditional scripts.
		$available_languages = $multilingual->get_available_languages();
		if ( ! empty( $available_languages ) ) {
			$scripts[] = array(
				'avada-wpml',
				$js_folder_url . '/general/avada-wpml.js',
				$js_folder_path . '/general/avada-wpml.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
		}
		if ( is_page_template( 'side-navigation.php' ) ) {
			$scripts[] = array(
				'avada-side-nav',
				$js_folder_url . '/general/avada-side-nav.js',
				$js_folder_path . '/general/avada-side-nav.js',
				array( 'jquery', 'jquery-hover-intent' ),
				self::$version,
				true,
			);
		}
		if ( ! is_page_template( 'blank.php' ) && 'no' != fusion_get_page_option( 'display_header', $page_id ) ) {
			$scripts[] = array(
				'avada-header',
				$js_folder_url . '/general/avada-header.js',
				$js_folder_path . '/general/avada-header.js',
				array( 'modernizr', 'jquery', 'jquery-easing' ),
				self::$version,
				true,
			);
			$scripts[] = array(
				'avada-menu',
				$js_folder_url . '/general/avada-menu.js',
				$js_folder_path . '/general/avada-menu.js',
				array( 'modernizr', 'jquery', 'avada-header' ),
				self::$version,
				true,
			);
		}
		if ( Avada()->settings->get( 'status_totop' ) || Avada()->settings->get( 'status_totop_mobile' ) ) {
			$scripts[] = array(
				'jquery-to-top',
				$js_folder_url . '/library/jquery.toTop.js',
				$js_folder_path . '/library/jquery.toTop.js',
				array( 'jquery' ),
				'1.2',
				true,
			);
			$scripts[] = array(
				'avada-to-top',
				$js_folder_url . '/general/avada-to-top.js',
				$js_folder_path . '/general/avada-to-top.js',
				array( 'jquery', 'cssua', 'jquery-to-top' ),
				self::$version,
				true,
			);
		}
		if ( Avada()->settings->get( 'slidingbar_widgets' ) ) {
			$scripts[] = array(
				'avada-sliding-bar',
				$js_folder_url . '/general/avada-sliding-bar.js',
				$js_folder_path . '/general/avada-sliding-bar.js',
				array( 'jquery', 'jquery-easing' ),
				self::$version,
				true,
			);
		}
		if ( Avada()->settings->get( 'avada_styles_dropdowns' ) ) {
			$scripts[] = array(
				'avada-drop-down',
				$js_folder_url . '/general/avada-drop-down.js',
				$js_folder_path . '/general/avada-drop-down.js',
				array( 'jquery', 'avada-select' ),
				self::$version,
				true,
			);
		}
		if ( 'Top' !== Avada()->settings->get( 'header_position' ) ) {
			$scripts[] = array(
				'avada-side-header-scroll',
				$js_folder_url . '/general/avada-side-header-scroll.js',
				$js_folder_path . '/general/avada-side-header-scroll.js',
				array( 'modernizr', 'jquery' ),
				self::$version,
				true,
			);
		}

		$avada_rev_styles = get_post_meta( $page_id, 'pyre_avada_rev_styles', true );
		if ( class_exists( 'RevSliderFront' ) && ( 'no' === $avada_rev_styles || ( Avada()->settings->get( 'avada_rev_styles' ) && 'yes' !== $avada_rev_styles ) ) ) {

			// If revolution slider is active.  Can't check for rev styles option as it can be enabled in page options.
			$scripts[] = array(
				'avada-rev-styles',
				$js_folder_url . '/general/avada-rev-styles.js',
				$js_folder_path . '/general/avada-rev-styles.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
		}
		if ( 'footer_parallax_effect' === Avada()->settings->get( 'footer_special_effects' ) ) {
			$scripts[] = array(
				'avada-parallax-footer',
				$js_folder_url . '/general/avada-parallax-footer.js',
				$js_folder_path . '/general/avada-parallax-footer.js',
				array( 'jquery', 'modernizr' ),
				self::$version,
				true,
			);
		}
		if ( ! Avada()->settings->get( 'disable_mobile_image_hovers' ) ) {
			$scripts[] = array(
				'avada-mobile-image-hover',
				$js_folder_url . '/general/avada-mobile-image-hover.js',
				$js_folder_path . '/general/avada-mobile-image-hover.js',
				array( 'jquery', 'modernizr' ),
				self::$version,
				true,
			);
		}
		if ( Avada()->settings->get( 'page_title_fading' ) ) {

			// If we add a page option for this, it will need to be changed here too.
			$scripts[] = array(
				'avada-fade',
				$js_folder_url . '/general/avada-fade.js',
				$js_folder_path . '/general/avada-fade.js',
				array( 'jquery', 'cssua', 'jquery-fade' ),
				self::$version,
				true,
			);
		}
		if ( defined( 'WPCF7_PLUGIN' ) ) {
			$scripts[] = array(
				'avada-contact-form-7',
				$js_folder_url . '/general/avada-contact-form-7.js',
				$js_folder_path . '/general/avada-contact-form-7.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
		}
		if ( class_exists( 'GFForms' ) && Avada()->settings->get( 'avada_styles_dropdowns' ) ) {
			$scripts[] = array(
				'avada-gravity-forms',
				$js_folder_url . '/general/avada-gravity-forms.js',
				$js_folder_path . '/general/avada-gravity-forms.js',
				array( 'jquery', 'avada-select' ),
				self::$version,
				true,
			);
		}
		if ( Avada()->settings->get( 'status_eslider' ) ) {
			$scripts[] = array(
				'jquery-elastic-slider',
				$js_folder_url . '/library/jquery.elasticslider.js',
				$js_folder_path . '/library/jquery.elasticslider.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
			$scripts[] = array(
				'avada-elastic-slider',
				$js_folder_url . '/general/avada-elastic-slider.js',
				$js_folder_path . '/general/avada-elastic-slider.js',
				array( 'jquery', 'jquery-elastic-slider' ),
				self::$version,
				true,
			);
		}
		if ( class_exists( 'WooCommerce' ) ) {
			$scripts[] = array(
				'avada-woocommerce',
				$js_folder_url . '/general/avada-woocommerce.js',
				$js_folder_path . '/general/avada-woocommerce.js',
				array( 'jquery', 'fusion-equal-heights' ),
				self::$version,
				true,
			);
		}
		if ( function_exists( 'is_bbpress' ) ) {
			$scripts[] = array(
				'avada-bbpress',
				$js_folder_url . '/general/avada-bbpress.js',
				$js_folder_path . '/general/avada-bbpress.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
		}
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$scripts[] = array(
				'avada-events',
				$js_folder_url . '/general/avada-events.js',
				$js_folder_path . '/general/avada-events.js',
				array( 'jquery' ),
				self::$version,
				true,
			);
		}

		if ( Avada()->settings->get( 'smooth_scrolling' ) ) {
			$scripts[] = array(
				'jquery-nicescroll',
				$js_folder_url . '/library/jquery.nicescroll.js',
				$js_folder_path . '/library/jquery.nicescroll.js',
				array( 'jquery' ),
				'3.5.0',
				true,
			);
			$scripts[] = array(
				'avada-nicescroll',
				$js_folder_url . '/general/avada-nicescroll.js',
				$js_folder_path . '/general/avada-nicescroll.js',
				array( 'jquery', 'jquery-nicescroll' ),
				self::$version,
				true,
			);
		}

		if ( ! class_exists( 'FusionBuilder' ) ) {
			$scripts[] = array(
				'fusion-carousel',
				str_replace( Avada::$template_dir_url, FUSION_LIBRARY_URL, $js_folder_url ) . '/general/fusion-carousel.js',
				str_replace( Avada::$template_dir_path, FUSION_LIBRARY_PATH, $js_folder_path ) . '/general/fusion-carousel.js',
				array( 'jquery-caroufredsel', 'jquery-touch-swipe' ),
				'1',
				true,
			);
			$scripts[] = array(
				'fusion-blog',
				str_replace( Avada::$template_dir_url, FUSION_LIBRARY_URL, $js_folder_url ) . '/general/fusion-blog.js',
				str_replace( Avada::$template_dir_path, FUSION_LIBRARY_PATH, $js_folder_path ) . '/general/fusion-blog.js',
				array( 'jquery', 'isotope', 'fusion-lightbox', 'fusion-flexslider', 'jquery-infinite-scroll', 'images-loaded' ),
				'1',
				true,
			);
		}

		foreach ( $scripts as $script ) {
			Fusion_Dynamic_JS::enqueue_script(
				$script[0],
				$script[1],
				$script[2],
				$script[3],
				$script[4],
				$script[5]
			);
		}

		Fusion_Dynamic_JS::enqueue_script( 'fusion-alert' );

		if ( ! class_exists( 'FusionBuilder' ) ) {
			Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/shared.min.css', Avada::$template_dir_url . '/assets/css/shared.min.css' );
		}

		if ( Avada()->settings->get( 'status_lightbox' )  && ! class_exists( 'FusionBuilder' ) ) {
			Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/ilightbox.min.css', Avada::$template_dir_url . '/assets/css/ilightbox.min.css' );
		}

		if ( Avada()->settings->get( 'use_animate_css' ) && ! class_exists( 'FusionBuilder' ) ) {
			Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/animations.min.css', Avada::$template_dir_url . '/assets/css/animations.min.css' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/woocommerce.min.css', Avada::$template_dir_url . '/assets/css/woocommerce.min.css' );
		}

		if ( class_exists( 'bbPress' ) ) {
			Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/bbpress.min.css', Avada::$template_dir_url . '/assets/css/bbpress.min.css' );
		}
	}

	/**
	 * Localize the dynamic JS files.
	 *
	 * @access protected
	 * @since 5.1.0
	 * @return void
	 */
	protected function localize_scripts() {

		global $wp_styles, $woocommerce, $fusion_library;
		$multilingual     = $fusion_library->multilingual;
		$page_bg_layout   = get_post_meta( Avada()->fusion_library->get_page_id(), 'pyre_page_bg_layout', true );
		$avada_rev_styles = get_post_meta( Avada()->fusion_library->get_page_id(), 'pyre_avada_rev_styles', true );
		$layout           = ( 'boxed' === $page_bg_layout || 'wide' === $page_bg_layout ) ? $page_bg_layout : Avada()->settings->get( 'layout' );
		$avada_rev_styles = ( 'no' === $avada_rev_styles || ( Avada()->settings->get( 'avada_rev_styles' ) && 'yes' !== $avada_rev_styles ) ) ? 1 : 0;

		$scripts = array(
			array(
				'avada-header',
				'avadaHeaderVars',
				array(
					'header_position'            => strtolower( Avada()->settings->get( 'header_position' ) ),
					'header_layout'              => Avada()->settings->get( 'header_layout' ),
					'header_sticky'              => Avada()->settings->get( 'header_sticky' ),
					'header_sticky_type2_layout' => Avada()->settings->get( 'header_sticky_type2_layout' ),
					'side_header_break_point'    => (int) Avada()->settings->get( 'side_header_break_point' ),
					'header_sticky_mobile'       => Avada()->settings->get( 'header_sticky_mobile' ),
					'header_sticky_tablet'       => Avada()->settings->get( 'header_sticky_tablet' ),
					'mobile_menu_design'         => Avada()->settings->get( 'mobile_menu_design' ),
					'sticky_header_shrinkage'    => Avada()->settings->get( 'header_sticky_shrinkage' ),
					'nav_height'                 => (int) Avada()->settings->get( 'nav_height' ),
					'nav_highlight_border'       => ( 'bar' === Avada()->settings->get( 'menu_highlight_style' ) ) ? (int) Avada()->settings->get( 'nav_highlight_border' ) : '0',
					'logo_margin_top'            => Avada()->settings->get( 'logo_margin', 'top' ),
					'logo_margin_bottom'         => Avada()->settings->get( 'logo_margin', 'bottom' ),
					'layout_mode'                => strtolower( $layout ),
					'header_padding_top'         => Avada()->settings->get( 'header_padding', 'top' ),
					'header_padding_bottom'      => Avada()->settings->get( 'header_padding', 'bottom' ),
					'offset_scroll'              => Avada()->settings->get( 'scroll_offset' ),
				),
			),
			array(
				'avada-menu',
				'avadaMenuVars',
				array(
					'header_position'         => Avada()->settings->get( 'header_position' ),
					'logo_alignment'          => Avada()->settings->get( 'logo_alignment' ),
					'header_sticky'           => Avada()->settings->get( 'header_sticky' ),
					'side_header_break_point' => (int) Avada()->settings->get( 'side_header_break_point' ),
					'mobile_menu_design'      => Avada()->settings->get( 'mobile_menu_design' ),
					'dropdown_goto'           => __( 'Go to...', 'Avada' ),
					'mobile_nav_cart'         => __( 'Shopping Cart', 'Avada' ),
					'submenu_slideout'        => Avada()->settings->get( 'mobile_nav_submenu_slideout' ),
				),
			),
			array(
				'avada-comments',
				'avadaCommentVars',
				array(
					'title_style_type'    => Avada()->settings->get( 'title_style_type' ),
					'title_margin_top'    => Avada()->settings->get( 'title_margin', 'top' ),
					'title_margin_bottom' => Avada()->settings->get( 'title_margin', 'bottom' ),
				),
			),
			array(
				'jquery-to-top',
				'toTopscreenReaderText',
				array(
					'label' => esc_attr__( 'Go to Top', 'Avada' ),
				),
			),
			array(
				'avada-to-top',
				'avadaToTopVars',
				array(
					'status_totop_mobile' => Avada()->settings->get( 'status_totop_mobile' ),
				),
			),
			array(
				'avada-wpml',
				'avadaLanguageVars',
				array(
					'language_flag' => $multilingual->get_active_language(),
				),
			),
			array(
				'avada-sidebars',
				'avadaSidebarsVars',
				array(
					'header_position'            => strtolower( Avada()->settings->get( 'header_position' ) ),
					'header_layout'              => Avada()->settings->get( 'header_layout' ),
					'header_sticky'              => Avada()->settings->get( 'header_sticky' ),
					'header_sticky_type2_layout' => Avada()->settings->get( 'header_sticky_type2_layout' ),
					'side_header_break_point'    => (int) Avada()->settings->get( 'side_header_break_point' ),
					'header_sticky_tablet'       => Avada()->settings->get( 'header_sticky_tablet' ),
					'sticky_header_shrinkage'    => Avada()->settings->get( 'header_sticky_shrinkage' ),
					'nav_height'                 => (int) Avada()->settings->get( 'nav_height' ),
					'content_break_point'        => Avada()->settings->get( 'content_break_point' ),
				),
			),
			array(
				'avada-side-nav',
				'avadaSideNavVars',
				array(
					'sidenav_behavior' => Avada()->settings->get( 'sidenav_behavior' ),
				),
			),
			array(
				'avada-side-header-scroll',
				'avadaSideHeaderVars',
				array(
					'side_header_break_point' => (int) Avada()->settings->get( 'side_header_break_point' ),
					'layout_mode'             => strtolower( $layout ),
					'boxed_offset_top'        => Avada()->settings->get( 'margin_offset', 'top' ),
					'boxed_offset_bottom'     => Avada()->settings->get( 'margin_offset', 'bottom' ),
					'offset_scroll'           => Avada()->settings->get( 'offset_scroll' ),
					'footer_special_effects'  => Avada()->settings->get( 'footer_special_effects' ),
				),
			),
			array(
				'avada-side-header-height',
				'avadaSideHeaderHeightVars',
				array(
					'side_header_break_point' => (int) Avada()->settings->get( 'side_header_break_point' ),
					'layout_mode'             => strtolower( $layout ),
					'boxed_offset_top'        => Avada()->settings->get( 'margin_offset', 'top' ),
					'boxed_offset_bottom'     => Avada()->settings->get( 'margin_offset', 'bottom' ),
				),
			),
			array(
				'avada-rev-styles',
				'avadaRevVars',
				array(
					'avada_rev_styles' => $avada_rev_styles,
				),
			),
			array(
				'avada-parallax-footer',
				'avadaParallaxFooterVars',
				array(
					'side_header_break_point' => (int) Avada()->settings->get( 'side_header_break_point' ),
					'header_position'         => Avada()->settings->get( 'header_position' ),
				),
			),
			array(
				'avada-mobile-image-hover',
				'avadaMobileImageVars',
				array(
					'side_header_break_point' => (int) Avada()->settings->get( 'side_header_break_point' ),
				),
			),
			array(
				'avada-nicescroll',
				'avadaNiceScrollVars',
				array(
					'side_header_width' => ( 'Top' !== Avada()->settings->get( 'header_position' ) ) ? intval( Avada()->settings->get( 'side_header_width' ) ) : '0',
					'smooth_scrolling'  => Avada()->settings->get( 'smooth_scrolling' ),
				),
			),
			array(
				'avada-woocommerce',
				'avadaWooCommerceVars',
				array(
					'order_actions'                   => __( 'Details' , 'Avada' ),
					'title_style_type'                => Avada()->settings->get( 'title_style_type' ),
					'woocommerce_shop_page_columns'   => Avada()->settings->get( 'woocommerce_shop_page_columns' ),
					'woocommerce_checkout_error'      => esc_attr__( 'Not all fields have been filled in correctly.', 'Avada' ),
					'woocommerce_single_gallery_size' => Fusion_Sanitize::number( Avada()->settings->get( 'woocommerce_single_gallery_size' ) ),
				),
			),
			array(
				'avada-elastic-slider',
				'avadaElasticSliderVars',
				array(
					'tfes_autoplay'  => Avada()->settings->get( 'tfes_autoplay' ),
					'tfes_animation' => Avada()->settings->get( 'tfes_animation' ),
					'tfes_interval'  => (int) Avada()->settings->get( 'tfes_interval' ),
					'tfes_speed'     => (int) Avada()->settings->get( 'tfes_speed' ),
					'tfes_width'     => (int) Avada()->settings->get( 'tfes_width' ),
				),
			),
			array(
				'avada-fade',
				'avadaFadeVars',
				array(
					'page_title_fading' => Avada()->settings->get( 'page_title_fading' ),
					'header_position'   => Avada()->settings->get( 'header_position' ),
				),
			),
		);

		foreach ( $scripts as $script ) {
			Fusion_Dynamic_JS::localize_script(
				$script[0],
				$script[1],
				$script[2]
			);

		}

	}

	/**
	 * Takes care of enqueueing all our scripts.
	 *
	 * @access public
	 */
	public function wp_enqueue_scripts() {

		wp_enqueue_script( 'jquery' );

		// The comment-reply script.
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		if ( function_exists( 'novagallery_shortcode' ) ) {
			wp_enqueue_script( 'novagallery_modernizr' );
		}

		if ( function_exists( 'ccgallery_shortcode' ) ) {
			wp_enqueue_script( 'ccgallery_modernizr' );
		}

		wp_enqueue_style( 'avada-stylesheet', Avada::$template_dir_url . '/assets/css/style.min.css', array(), self::$version );

		if ( Avada()->settings->get( 'status_fontawesome' ) ) {
			if ( 'off' === Avada()->settings->get( 'css_cache_method' ) ) {
				wp_enqueue_style( 'fusion-font-awesome', FUSION_LIBRARY_URL . '/assets/fonts/fontawesome/font-awesome.css', array(), self::$version );
			}
			wp_enqueue_style( 'avada-IE-fontawesome', FUSION_LIBRARY_URL . '/assets/fonts/fontawesome/font-awesome.css', array(), self::$version );
			wp_style_add_data( 'avada-IE-fontawesome', 'conditional', 'lte IE 9' );
		}

		wp_enqueue_style( 'avada-IE', Avada::$template_dir_url . '/assets/css/ie.css', array(), self::$version );
		wp_style_add_data( 'avada-IE', 'conditional', 'IE' );

		if ( Avada()->settings->get( 'status_lightbox' ) && class_exists( 'WooCommerce' ) ) {
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
		}

		if ( is_rtl() && 'file' !== $this->compiler_mode ) {
			wp_enqueue_style( 'avada-rtl', Avada::$template_dir_url . '/assets/css/rtl.min.css', array(), self::$version );
		}

		if ( is_page_template( 'contact.php' ) ) {
			$options = get_option( Avada::get_option_name() );
			if ( $options['recaptcha_public'] && $options['recaptcha_private'] && ! function_exists( 'recaptcha_get_html' ) ) {
				if ( version_compare( PHP_VERSION, '5.3' ) >= 0 && ! class_exists( 'ReCaptcha' ) ) {
					wp_enqueue_script( 'recaptcha-api', 'https://www.google.com/recaptcha/api.js?hl=' . get_locale() );
				}
			}
		}
	}

	/**
	 * Adds assets to the compiled CSS.
	 *
	 * @access public
	 * @since 5.1.5
	 * @param string $original_styles The compiled styles.
	 * @return string The compiled styles with any additional CSS appended.
	 */
	public function combine_stylesheets( $original_styles ) {
		$wp_filesystem = Fusion_Helper::init_filesystem();
		$styles = '';

		if ( 'off' !== Avada()->settings->get( 'css_cache_method' ) ) {
			if ( Avada()->settings->get( 'status_fontawesome' ) ) {
				// Stylesheet ID: fusion-font-awesome. @codingStandardsIgnoreLine
				$font_awesome_css = @file_get_contents( FUSION_LIBRARY_PATH . '/assets/fonts/fontawesome/font-awesome.min.css' );

				$font_url = FUSION_LIBRARY_URL . '/assets/fonts/fontawesome';
				$font_url = str_replace( array( 'http://', 'https://' ), '//', $font_url );
				$styles .= str_replace( 'url(fontawesome-webfont', 'url(' . $font_url . '/fontawesome-webfont', $font_awesome_css );

			}
			if ( is_rtl() ) {
				// Stylesheet ID: avada-rtl. @codingStandardsIgnoreLine
				$styles .= @file_get_contents( Avada::$template_dir_path . '/assets/css/rtl.min.css' );
			}
		}
		return $styles . $original_styles;
	}

	/**
	 * Removes WooCommerce scripts.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param array $scripts The WooCommerce scripts.
	 * @return array
	 */
	public function remove_woo_scripts( $scripts ) {

		if ( isset( $scripts['woocommerce-layout'] ) ) {
			unset( $scripts['woocommerce-layout'] );
		}
		if ( isset( $scripts['woocommerce-smallscreen'] ) ) {
			unset( $scripts['woocommerce-smallscreen'] );
		}
		if ( isset( $scripts['woocommerce-general'] ) ) {
			unset( $scripts['woocommerce-general'] );
		}
		return $scripts;

	}

	/**
	 * Add admin CSS
	 *
	 * @access public
	 */
	public function admin_css() {
		wp_enqueue_style( 'avada_wp_admin_css', get_template_directory_uri() . '/assets/admin/css/admin.css', false, self::$version );
	}

	/**
	 * Add async to avada javascript file for performance
	 *
	 * @access public
	 * @param  string $tag    The script tag.
	 * @param  string $handle The script handle.
	 */
	public function add_async( $tag, $handle ) {
		return ( 'avada' == $handle ) ? preg_replace( '/(><\/[a-zA-Z][^0-9](.*)>)$/', ' async $1 ', $tag ) : $tag;
	}

	/**
	 * Add extra admin styles.
	 *
	 * @access public
	 * @since 5.1.2
	 */
	public function admin_styles() {

		$font_url = untrailingslashit( FUSION_LIBRARY_URL ) . '/assets/fonts/icomoon';
		$font_url = str_replace( array( 'http://', 'https://' ), '//', $font_url );
		?>
		<style type="text/css">
			@font-face {
				font-family: 'icomoon';
				src:url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.eot');
				src:url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.eot?#iefix') format('embedded-opentype'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.woff') format('woff'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.ttf') format('truetype'),
					url('<?php echo esc_url_raw( $font_url ); ?>/icomoon.svg#icomoon') format('svg');
				font-weight: normal;
				font-style: normal;
			}
		</style>
		<?php

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
