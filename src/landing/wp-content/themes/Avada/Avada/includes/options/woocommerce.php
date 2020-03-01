<?php
/**
 * Avada Options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * WooCommerce settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_woocommerce( $sections ) {

	$wc_version_greater_than_3 = false;
	if ( defined( 'WC_VERSION' ) ) {
		if ( WC_VERSION && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$wc_version_greater_than_3 = true;
		}
	}

	$sections['woocommerce'] = ( Avada::$is_updating || class_exists( 'WooCommerce' ) ) ? array(
		'label'    => esc_html__( 'WooCommerce', 'Avada' ),
		'id'       => 'heading_woocommerce',
		'priority' => 26,
		'icon'     => 'el-icon-shopping-cart',
		'fields'   => array(
			'general_woocommerce_options_subsection' => array(
				'label'       => esc_html__( 'General WooCommerce', 'Avada' ),
				'description' => '',
				'id'          => 'general_woocommerce_options_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'woo_items' => array(
						'label'       => esc_html__( 'WooCommerce Number of Products per Page', 'Avada' ),
						'description' => esc_html__( 'Controls the number of products that display per page. ', 'Avada' ),
						'id'          => 'woo_items',
						'default'     => '12',
						'type'        => 'slider',
						'choices'     => array(
							'min'  => '1',
							'max'  => '50',
							'step' => '1',
						),
					),
					'woocommerce_shop_page_columns' => array(
						'label'           => esc_html__( 'WooCommerce Number of Product Columns', 'Avada' ),
						'description'     => esc_html__( 'Controls the number of columns for the main shop page.', 'Avada' ),
						'id'              => 'woocommerce_shop_page_columns',
						'default'         => 4,
						'type'            => 'slider',
						'choices'     => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
					'woocommerce_related_columns' => array(
						'label'           => esc_html__( 'WooCommerce Related/Up-Sell/Cross-Sell Product Number of Columns', 'Avada' ),
						'description'     => esc_html__( 'Controls the number of columns for the related and up-sell products on single posts and cross-sells on cart page.', 'Avada' ),
						'id'              => 'woocommerce_related_columns',
						'default'         => 4,
						'type'            => 'slider',
						'choices'     => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
					'woocommerce_archive_page_columns' => array(
						'label'           => esc_html__( 'WooCommerce Archive Number of Product Columns', 'Avada' ),
						'description'     => esc_html__( 'Controls the number of columns for the archive pages.', 'Avada' ),
						'id'              => 'woocommerce_archive_page_columns',
						'default'         => 3,
						'type'            => 'slider',
						'choices'     => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					),
					'disable_woo_gallery' => ( class_exists( 'WooCommerce' ) || Avada::$is_updating ) ? array(
						'label'           => esc_html__( 'Avada\'s WooCommerce Product Gallery Slider', 'Avada' ),
						'description'     => esc_html__( 'Turn on to enable Avada\'s product gallery slider.', 'Avada' ),
						'id'              => 'disable_woo_gallery',
						'default'         => '1',
						'type'            => 'switch',
						'active_callback' => array( 'Avada_Options_Conditionals', 'is_woo' ),
					) : array(),
					'woocommerce_single_gallery_size' => ( class_exists( 'WooCommerce' ) || Avada::$is_updating ) ? array(
						'label'       => esc_html__( 'WooCommerce Product Gallery Size', 'Avada' ),
						'description' => ( $wc_version_greater_than_3 ) ?
						__( 'Controls the size of the single product page image gallery. For the image gallery zoom feature to work, the images you upload must be larger than the gallery size you select for this option. <strong>Important:</strong> When this option is changed, you may need to adjust the Single Product Image size setting in WooCommerce Settings to make sure that one is larger and also regenerate thumbnails. <a href="http://theme-fusion.com/avada-doc/woocommerce-single-product-gallery/" target="_blank">See this post for more information.</a><br/>', 'Avada' ) :
						__( 'Controls the size of the single product page image gallery.', 'Avada' ),
						'id'          => 'woocommerce_single_gallery_size',
						'default'     => '500px',
						'type'        => 'dimension',
						'choices'     => array( 'px' ),
					) : array(),
					'woocommerce_gallery_thumbnail_columns' => ( ( ( class_exists( 'WooCommerce' ) && $wc_version_greater_than_3 ) || Avada::$is_updating ) ) ? array(
						'label'           => esc_html__( 'WooCommerce Product Gallery Thumbnails Columns', 'Avada' ),
						'description'     => esc_html__( 'Controls the number of columns of the single product page image gallery thumbnails. In order to avoid blurry thumbnails, make sure the Product Thumbnails size setting in WooCommerce Settings is large enough. It has to be at least WooCommerce Product Gallery Size setting divided by this number of columns.', 'Avada' ),
						'id'              => 'woocommerce_gallery_thumbnail_columns',
						'default'         => 4,
						'type'            => 'slider',
						'choices'     => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
					) : array(),
					'enable_woo_gallery_zoom' => (  class_exists( 'WooCommerce' ) && $wc_version_greater_than_3 ) ? array(
						'label'           => esc_html__( 'WooCommerce Product Gallery Zoom', 'Avada' ),
						'description'     => __( 'Turn on to enable the WooCommerce gallery zoom feature. Important: Every product image you use must be larger than the gallery container for zoom to work correctly.<br/><a href="http://theme-fusion.com/avada-doc/woocommerce-single-product-gallery/" target="_blank">See this post for more information.</a>', 'Avada' ),
						'id'              => 'enable_woo_gallery_zoom',
						'default'         => '1',
						'type'            => 'switch',
					) : array(),
					'woocommerce_avada_ordering' => array(
						'label'           => esc_html__( 'WooCommerce Shop Page Ordering Boxes', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the ordering boxes on the shop page.', 'Avada' ),
						'id'              => 'woocommerce_avada_ordering',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woocommerce_disable_crossfade_effect' => array(
						'label'           => esc_html__( 'WooCommerce Shop Page Crossfade Image Effect', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the product crossfade image effect on the shop page.', 'Avada' ),
						'id'              => 'woocommerce_disable_crossfade_effect',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woocommerce_one_page_checkout' => array(
						'label'           => esc_html__( 'WooCommerce One Page Checkout', 'Avada' ),
						'description'     => esc_html__( 'Turn on to use the one page checkout template.', 'Avada' ),
						'id'              => 'woocommerce_one_page_checkout',
						'default'         => '0',
						'type'            => 'switch',
					),
					'woocommerce_enable_order_notes' => array(
						'label'           => esc_html__( 'WooCommerce Order Notes on Checkout', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the order notes on the checkout page.', 'Avada' ),
						'id'              => 'woocommerce_enable_order_notes',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woocommerce_acc_link_main_nav' => array(
						'label'           => esc_html__( 'WooCommerce My Account Link in Main Menu', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the "My Account" link in the main menu. Not compatible with Ubermenu.', 'Avada' ),
						'id'              => 'woocommerce_acc_link_main_nav',
						'default'         => '0',
						'type'            => 'switch',
						'class'		  => 'fusion-or-gutter',
						'required'    => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '!=',
								'value'    => 'v6',
							),
						),
					),
					'woocommerce_cart_link_main_nav' => array(
						'label'           => esc_html__( 'WooCommerce Cart Icon in Main Menu', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the cart icon in the main menu. Not compatible with Ubermenu.', 'Avada' ),
						'id'              => 'woocommerce_cart_link_main_nav',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woocommerce_cart_counter' => array(
						'label'       => esc_html__( 'WooCommerce Menu Cart Icon Counter', 'Avada' ),
						'description' => esc_html__( 'Turn on to display the WooCommerce cart counter circle.', 'Avada' ),
						'id'          => 'woocommerce_cart_counter',
						'default'     => '0',
						'type'        => 'switch',
					),
					'woocommerce_acc_link_top_nav' => array(
						'label'           => esc_html__( 'WooCommerce My Account Link in Secondary Menu', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the "My Account" link in the secondary menu. Not compatible with Ubermenu.', 'Avada' ),
						'id'              => 'woocommerce_acc_link_top_nav',
						'default'         => '1',
						'type'            => 'switch',
						'class'		      => 'fusion-or-gutter',
						'required'        => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v2',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v3',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v4',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v5',
							),
						),
					),
					'woocommerce_cart_link_top_nav' => array(
						'label'           => esc_html__( 'WooCommerce Cart Icon in Secondary Menu', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the cart icon in the secondary menu. Not compatible with Ubermenu.', 'Avada' ),
						'id'              => 'woocommerce_cart_link_top_nav',
						'default'         => '1',
						'type'            => 'switch',
						'class'		      => 'fusion-or-gutter',
						'required'    	  => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v2',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v3',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v4',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '=',
								'value'    => 'v5',
							),
						),
					),
					'woocommerce_social_links' => array(
						'label'           => esc_html__( 'WooCommerce Social Icons', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the social icons on single product posts.', 'Avada' ),
						'id'              => 'woocommerce_social_links',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woocommerce_toggle_grid_list' => array(
						'label'           => esc_html__( 'WooCommerce Product Grid / List View', 'Avada' ),
						'description'     => esc_html__( 'Turn on to display the grid/list toggle on the main shop page and archive shop pages.', 'Avada' ),
						'id'              => 'woocommerce_toggle_grid_list',
						'default'         => '1',
						'type'            => 'switch',
					),
					'woo_acc_msg_1' => array(
						'label'           => esc_html__( 'WooCommerce Account Area Message 1', 'Avada' ),
						'description'     => esc_html__( 'Controls the text that displays in the first message box on the account page.', 'Avada' ),
						'id'              => 'woo_acc_msg_1',
						'default'         => 'Need Assistance? Call customer service at 888-555-5555.',
						'type'            => 'textarea',
					),
					'woo_acc_msg_2' => array(
						'label'           => esc_html__( 'WooCommerce Account Area Message 2', 'Avada' ),
						'description'     => esc_html__( 'Controls the text that displays in the second message box on the account page.', 'Avada' ),
						'id'              => 'woo_acc_msg_2',
						'default'         => 'E-mail them at info@yourshop.com',
						'type'            => 'textarea',
					),
				),
			),
			'woocommerce_styling_options_subsection' => array(
				'label'       => esc_html__( 'WooCommerce Styling', 'Avada' ),
				'description' => '',
				'id'          => 'woocommerce_styling_options_subsection',
				'icon'        => true,
				'type'        => 'sub-section',
				'fields'      => array(
					'woocommerce_product_box_design' => array(
						'type'        => 'radio-buttonset',
						'label'       => esc_html__( 'WooCommerce Product Box Design', 'Avada' ),
						'description' => esc_html__( 'Controls the design of the product boxes.', 'Avada' ),
						'id'          => 'woocommerce_product_box_design',
						'default'     => 'classic',
						'choices'     => array(
							'classic' => esc_html__( 'Classic', 'Avada' ),
							'clean'   => esc_html__( 'Clean', 'Avada' ),
						),
					),
					'woocommerce_product_tab_design' => array(
						'label'           => esc_html__( 'WooCommerce Tab Design', 'Avada' ),
						'description'     => esc_html__( 'Controls the design of all WooCommerce tabs.', 'Avada' ),
						'id'              => 'woocommerce_product_tab_design',
						'default'         => 'vertical',
						'type'            => 'radio-buttonset',
						'choices'     => array(
							'horizontal' => esc_html__( 'Horizontal Tabs', 'Avada' ),
							'vertical'   => esc_html__( 'Vertical Tabs', 'Avada' ),
						),
					),
					'qty_bg_color' => array(
						'label'           => esc_html__( 'WooCommerce Quantity Box Background Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the background color of the WooCommerce quantity box.', 'Avada' ),
						'id'              => 'qty_bg_color',
						'default'         => '#fbfaf9',
						'type'            => 'color-alpha',
					),
					'qty_bg_hover_color' => array(
						'label'           => esc_html__( 'WooCommerce Quantity Box Hover Background Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the hover color of the WooCommerce quantity box.', 'Avada' ),
						'id'              => 'qty_bg_hover_color',
						'default'         => '#ffffff',
						'type'            => 'color-alpha',
					),
					'woo_dropdown_bg_color' => array(
						'label'           => esc_html__( 'WooCommerce Order Dropdown Background Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the background color of the WooCommerce order dropdowns.', 'Avada' ),
						'id'              => 'woo_dropdown_bg_color',
						'default'         => '#fbfaf9',
						'type'            => 'color-alpha',
						'class'		      => 'fusion-or-gutter',
						'required'    	  => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '!=',
								'value'    => 'v6',
							),
						),
					),
					'woo_dropdown_text_color' => array(
						'label'           => esc_html__( 'WooCommerce Order Dropdown Text Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the color of the text and icons in the WooCommerce order dropdowns.', 'Avada' ),
						'id'              => 'woo_dropdown_text_color',
						'default'         => '#333333',
						'type'            => 'color',
						'class'		      => 'fusion-or-gutter',
						'required'    	  => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '!=',
								'value'    => 'v6',
							),
						),
					),
					'woo_dropdown_border_color' => array(
						'label'           => esc_html__( 'WooCommerce Order Dropdown Border Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the border color in the WooCommerce order dropdowns.', 'Avada' ),
						'id'              => 'woo_dropdown_border_color',
						'default'         => '#dbdbdb',
						'type'            => 'color-alpha',
						'class'		      => 'fusion-or-gutter',
						'required'    	  => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '!=',
								'value'    => 'v6',
							),
						),
					),
					'woo_cart_bg_color' => array(
						'label'           => esc_html__( 'WooCommerce Cart Menu Background Color', 'Avada' ),
						'description'     => esc_html__( 'Controls the bottom section background color of the WooCommerce cart dropdown.', 'Avada' ),
						'id'              => 'woo_cart_bg_color',
						'default'         => '#fafafa',
						'type'            => 'color-alpha',
						'class'		      => 'fusion-or-gutter',
						'required'    	  => array(
							array(
								'setting'  => 'header_position',
								'operator' => '!=',
								'value'    => 'Top',
							),
							array(
								'setting'  => 'header_layout',
								'operator' => '!=',
								'value'    => 'v6',
							),
						),
					),
					'woo_icon_font_size' => array(
						'label'           => esc_html__( 'WooCommerce Icon Font Size', 'Avada' ),
						'description'     => esc_html__( 'Controls the font size of the WooCommerce icons.', 'Avada' ),
						'id'              => 'woo_icon_font_size',
						'type'            => 'dimension',
						'default'         => '12px',
					),
				),
			),
		),
	) : array();

	return $sections;

}
