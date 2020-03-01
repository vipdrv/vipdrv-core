<?php
/**
 * Modifications for WooCommerce.
 *
 * @author     ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class to apply woocommerce templates.
 *
 * @since 4.0.0
 */
class Avada_Woocommerce {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		add_filter( 'woocommerce_show_page_title', array( $this, 'shop_title' ), 10 );

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		add_action( 'woocommerce_before_main_content', array( $this, 'before_container' ), 10 );
		add_action( 'woocommerce_after_main_content', array( $this, 'after_container' ), 10 );

		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
		add_action( 'woocommerce_sidebar', array( $this, 'add_sidebar' ), 10 );

		// Products Loop.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'add_product_wrappers_open' ), 30 );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'product_title' ), 10 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_product_wrappers_close' ), 20 );

		add_action( 'avada_woocommerce_buttons_on_rollover',  array( $this, 'template_loop_add_to_cart' ), 10 );
		add_action( 'avada_woocommerce_buttons_on_rollover',  array( $this, 'rollover_buttons_linebreak' ), 15 );
		add_action( 'avada_woocommerce_buttons_on_rollover', array( $this, 'show_details_button' ), 20 );

		if ( 'clean' === Avada()->settings->get( 'woocommerce_product_box_design' ) ) {

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'before_shop_item_buttons' ), 9 );

		} else {

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_product_loop_outofstock_flash' ), 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'before_shop_loop_item_title_open' ), 5 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'before_shop_loop_item_title_close' ), 20 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'before_shop_item_buttons' ), 5 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'template_loop_add_to_cart' ), 10 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_details_button' ), 15 );

		}

		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_item_buttons' ), 20 );

		// Single Product Page.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'add_product_border' ), 19 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'template_single_title' ), 5 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_single_product_summary',  array( $this, 'stock_html' ), 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 11 );

		// Add product-title class to the cart item name link.
		add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10 );

		add_action( 'woocommerce_proceed_to_checkout', array( $this, 'proceed_to_checkout' ), 10 );

		add_action( 'woocommerce_before_account_navigation', array( $this, 'avada_top_user_container' ), 10 );

		// Add welcome user bar to checkout page.
		add_action( 'woocommerce_before_checkout_form', array( $this, 'avada_top_user_container' ), 1 );

		// Filter the pagination.
		add_filter( 'woocommerce_pagination_args', array( $this, 'change_pagination' ) );

		// Version sensitive hooks.
		if ( version_compare( self::get_wc_version(), '3.0', '<' ) ) {
			add_filter( 'woocommerce_template_path', array( $this, 'backwards_compatibility' ) );
		} else {
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'before_single_product_summary_open' ), 5 );
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'before_single_product_summary_close' ), 30 );

			add_filter( 'woocommerce_single_product_carousel_options', array( $this, 'single_product_carousel_options' ), 10 );
			add_filter( 'woocommerce_product_thumbnails_columns', array( $this, 'product_thumbnails_columns' ), 10 );

			if ( '1' === Avada()->settings->get( 'disable_woo_gallery' ) ) {
				add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'single_product_image_gallery_classes' ), 10 );
				add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'single_product_image_thumbnail_html' ), 10, 2 );
			}
		}

		// Checkout page.
		add_filter( 'woocommerce_order_button_html', array( $this, 'order_button_html' ) );

		// Account Page.
		add_action( 'woocommerce_account_dashboard', array( $this, 'account_dashboard' ), 5 );
		add_action( 'woocommerce_before_account_orders', array( $this, 'before_account_content_heading' ) );
		add_action( 'woocommerce_before_account_downloads', array( $this, 'before_account_content_heading' ) );
		add_action( 'woocommerce_before_account_payment_methods', array( $this, 'before_account_content_heading' ) );
		add_action( 'woocommerce_edit_account_form_start', array( $this, 'before_account_content_heading' ) );

		remove_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );
		add_action( 'woocommerce_view_order', array( $this, 'view_order' ), 10 );
		add_action( 'woocommerce_thankyou', array( $this, 'view_order' ) );

		add_action( 'wp_loaded', array( $this, 'wpml_fix' ), 30 );

		add_action( 'woocommerce_checkout_after_order_review', array( $this, 'checkout_after_order_review' ), 20 );
		add_filter( 'post_class', array( $this, 'change_product_class' ) );
		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
		add_action( 'woocommerce_after_customer_login_form', array( $this, 'after_customer_login_form' ) );
		add_action( 'woocommerce_before_customer_login_form', array( $this, 'before_customer_login_form' ) );
		add_filter( 'get_product_search_form', array( $this, 'product_search_form' ) );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		add_action( 'pre_get_posts', array( $this, 'product_ordering' ), 5 );
		add_filter( 'loop_shop_per_page', array( $this, 'loop_shop_per_page' ) );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'thumbnail' ), 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

		add_filter( 'wp_nav_menu_items', array( $this, 'add_woo_cart_to_widget' ), 20, 4 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'header_add_to_cart_fragment' ) );

		add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_summary_open' ), 1 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_summary_close' ), 100 );

		add_action( 'woocommerce_after_single_product_summary', array( $this, 'after_single_product_summary' ), 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'output_related_products' ), 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'upsell_display' ), 10 );

		add_action( 'woocommerce_before_cart_table', array( $this, 'before_cart_table' ), 20 );
		add_action( 'woocommerce_after_cart_table', array( $this, 'after_cart_table' ), 20 );

		add_action( 'woocommerce_cart_collaterals', array( $this, 'cart_collaterals' ), 5 );
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_cart_collaterals', array( $this, 'cross_sell_display' ), 5 );

		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_coupon_form' ), 10 );

		if ( ! Avada()->settings->get( 'woocommerce_one_page_checkout' ) ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'before_checkout_form' ) );
			add_action( 'woocommerce_after_checkout_form', array( $this, 'after_checkout_form' ) );
		} else {
			add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'checkout_before_customer_details' ) );
			add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'checkout_after_customer_details' ) );
		}
		add_action( 'woocommerce_checkout_billing', array( $this, 'checkout_billing' ), 20 );
		add_action( 'woocommerce_checkout_shipping', array( $this, 'checkout_shipping' ), 20 );
		add_filter( 'woocommerce_enable_order_notes_field', array( $this, 'enable_order_notes_field' ) );
	}

	/**
	 * Filter method to modify path to WooCommerce files if WooCommerce is a version less than 2.6.
	 *
	 * @access public
	 * @since 3.7.2
	 * @param string $path The path.
	 * @return string      The relative path of WooCommerce template files within the theme.
	 */
	public function backwards_compatibility( $path ) {
		return 'woocommerce/compatibility/2.6/';
	}

	/**
	 * Helper method to get the version of the currently installed WooCommerce.
	 *
	 * @static
	 * @access private
	 * @since 3.7.2
	 * @return string woocommerce version number or null.
	 */
	private static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Add content before the container.
	 *
	 * @access public
	 */
	public function before_container() {
		ob_start();
		Avada()->layout->add_class( 'content_class' );
		$content_class = ob_get_clean();

		ob_start();
		Avada()->layout->add_style( 'content_style' );
		$content_css = ob_get_clean();
		?>
		<div class="woocommerce-container">
			<section id="content"<?php echo $content_class . ' ' . $content_css; // WPCS: XSS ok. ?>>
		<?php
	}

	/**
	 * Returns false.
	 *
	 * @access public
	 * @return false
	 */
	public function shop_title() {
		return false;
	}

	/**
	 * Closes 2 divs that were previously opened.
	 *
	 * @access public
	 */
	public function after_container() {
		get_template_part( 'templates/wc-after-container' );
	}

	/**
	 * Adds the sidebar.
	 *
	 * @access public
	 */
	public function add_sidebar() {
		do_action( 'avada_after_content' );
	}

	/**
	 * Prints the out of stock warning.
	 *
	 * @access public
	 */
	public function show_product_loop_outofstock_flash() {
		get_template_part( 'templates/wc-product-loop-outofstock-flash' );
	}

	/**
	 * Adds the link to permalink.
	 *
	 * @access public
	 */
	public function before_shop_loop_item_title_open() {
		get_template_part( 'templates/wc-before-shop-loop-item-title-open' );
	}

	/**
	 * Closes the link.
	 *
	 * @access public
	 */
	public function before_shop_loop_item_title_close() {
		get_template_part( 'templates/wc-before-shop-loop-item-title-close' );
	}

	/**
	 * Content before the item buttons.
	 *
	 * @access public
	 */
	public function before_shop_item_buttons() {
		get_template_part( 'templates/wc-before-shop-item-buttons' );
	}

	/**
	 * Add to cart loop.
	 *
	 * @access public
	 * @param array $args The arguments.
	 */
	public function template_loop_add_to_cart( $args = array() ) {
		global $product;

		if ( $product && ( ( $product->is_purchasable() && $product->is_in_stock() ) || $product->is_type( 'external' ) ) ) {

			if ( version_compare( self::get_wc_version(), '2.5', '>=' ) ) {

				// WC 2.7 introduced the get_type method and deprecated the 'product_type' property.
				// We need to get creative in order to maintain backwards compatibility.
				$product_type = 'simple';
				if ( method_exists( $product, 'get_type' ) ) {
					$product_type = $product->get_type();
				} elseif ( property_exists( $product, 'product_type' ) ) {
					$product_type = $product->product_type;
				}

				$defaults = array(
					'quantity' => 1,
					'class'    => implode( ' ', array_filter( array(
						'button',
						'product_type_' . $product_type,
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
					) ) ),
				);

				$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );
			}

			wc_get_template( 'loop/add-to-cart.php' , $args );
		}
	}

	/**
	 * Adds the linebreak where needed.
	 *
	 * @access public
	 */
	public function rollover_buttons_linebreak() {
		global $product;
		if ( $product && ( ( $product->is_purchasable() && $product->is_in_stock() ) || $product->is_type( 'external' ) ) ) {
			get_template_part( 'templates/wc-rollover-buttons-linebreak' );
		}
	}

	/**
	 * Renders the "Details" button.
	 *
	 * @access public
	 */
	public function show_details_button() {
		get_template_part( 'templates/wc-show-details-button' );
	}

	/**
	 * Closes 2 divs that were previously opened.
	 *
	 * @access public
	 */
	public function after_shop_item_buttons() {
		get_template_part( 'templates/wc-after-shop-item-buttons' );
	}

	/**
	 * Adds a div that is used for borders.
	 *
	 * @access public
	 */
	function add_product_border() {
		get_template_part( 'templates/wc-add-product-border' );
	}

	/**
	 * Modifies the pagination.
	 *
	 * @access public
	 * @param array $options An array of our options.
	 * @return array         The options, modified.
	 */
	public function change_pagination( $options ) {
		$options['prev_text'] 	= '<span class="page-prev"></span><span class="page-text">' . __( 'Previous', 'Avada' ) . '</span>';
		$options['next_text'] 	= '<span class="page-text">' . __( 'Next', 'Avada' ) . '</span><span class="page-next"></span>';
		$options['type']		= 'plain';

		return $options;
	}

	/**
	 * Add wrapping container opening for single product image gallery.
	 *
	 * @since 5.1
	 * @access public
	 * @return void
	 */
	public function before_single_product_summary_open() {
		get_template_part( 'templates/wc-before-single-product-summary-open' );
	}

	/**
	 * Add wrapping container closing for single product image gallery.
	 *
	 * @since 5.1
	 * @access public
	 * @return void
	 */
	public function before_single_product_summary_close() {
		get_template_part( 'templates/wc-before-single-product-summary-close' );
	}

	/**
	 * Filters single product page image flexslider options.
	 *
	 * @since 5.1
	 * @access public
	 * @param array $flexslider_options Holds the default options for setting up the flexslider object.
	 * @return array The altered flexslider options.
	 */
	public function single_product_carousel_options( $flexslider_options ) {
		global $post;

		$flexslider_options['directionNav'] = true;

		$product = wc_get_product( $post );

		if ( is_object( $product ) ) {

			$attachment_ids = $product->get_gallery_image_ids();

			if ( '1' === Avada()->settings->get( 'disable_woo_gallery' ) && 0 < count( $attachment_ids ) ) {
				$flexslider_options['animationLoop'] = true;
				$flexslider_options['smoothHeight'] = true;
			}
		}

		return $flexslider_options;
	}

	/**
	 * Filters single product gallery thumbnail columns..
	 *
	 * @since 5.1
	 * @access public
	 * @param string $columns Holds the number of gallery thumbnail columns.
	 * @return string The altered gallery thumbnail columns.
	 */
	public function product_thumbnails_columns( $columns ) {
		return Avada()->settings->get( 'woocommerce_gallery_thumbnail_columns' );
	}

	/**
	 * Filters single product page image gallery classes.
	 *
	 * @since 5.1
	 * @access public
	 * @param string $classes Holds the single product image gallery classes.
	 * @return array The altered classes.
	 */
	public function single_product_image_gallery_classes( $classes ) {

		$classes[] = 'avada-product-gallery';
		return $classes;

	}

	/**
	 * Filters single product image thumbnail html.
	 *
	 * @since 5.1
	 * @access public
	 * @param string $html Holds the single product image thumbnail html.
	 * @param number $attachment_id The attachment id for single product image.
	 * @return array The altered html markup.
	 */
	public function single_product_image_thumbnail_html( $html, $attachment_id ) {
		global $post, $product;

		$attachment_count = count( $product->get_gallery_image_ids() );
		$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );

		$gallery = '[]';
		if ( $attachment_count > 0 ) {
			$gallery = '[product-gallery]';
		}

		$html = str_replace( '</div>', '<a class="avada-product-gallery-lightbox-trigger" href="' . esc_url( $full_size_image[0] ) . '" data-rel="iLightbox' . $gallery . '"></a></div>', $html );

		return $html;
	}



	/**
	 * Open wrapper divs.
	 *
	 * @access public
	 */
	public function add_product_wrappers_open() {
		get_template_part( 'templates/wc-add-product-wrappers-open' );
	}

	/**
	 * Renders the product title.
	 *
	 * @access public
	 */
	public function product_title() {
		get_template_part( 'templates/wc-product-title' );
	}

	/**
	 * Closes previously opened wrappers.
	 *
	 * @access public
	 */
	public function add_product_wrappers_close() {
		get_template_part( 'templates/wc-add-product-wrappers-close' );
	}


	/**
	 * Single Product Page functions.
	 *
	 * @access public
	 */
	public function template_single_title() {
		get_template_part( 'templates/wc-single-title' );
	}

	/**
	 * Add the availability HTML.
	 *
	 * @access public
	 */
	public function stock_html() {
		get_template_part( 'templates/wc-stock' );
	}

	/**
	 * Adds the product-title class to the cart item name link.
	 *
	 * @since 5.1
	 * @access public
	 * @param string $name The cart item name, can be wrapped by an a tag or not.
	 * @return string The cart item name.
	 */
	public function cart_item_name( $name ) {
		if ( false !== strpos( $name, 'href=' ) ) {
			return str_replace( '<a', '<a class="product-title"', $name );
		}
		return $name;
	}

	/**
	 * Added in the 'woocommerce_proceed_to_checkout' action.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function proceed_to_checkout() {
		get_template_part( 'templates/wc-proceed-to-checkout' );
	}

	/**
	 * Add the view-order markup.
	 *
	 * @param int $order_id The ID of the order we're querying.
	 */
	public function view_order( $order_id ) {
		include wp_normalize_path( locate_template( 'templates/wc-view-order.php' ) );
	}


	/**
	 * Account Page functions.
	 *
	 * @access public
	 */
	public function avada_top_user_container() {
		get_template_part( 'templates/wc-top-user-container' );
	}

	/**
	 * Change the HTML of the checkout button.
	 *
	 * @since 5.1
	 * @access public
	 * @param string $html The checkout button HTML.
	 * @return string The changed HTML.
	 */
	public function order_button_html( $html ) {
		return str_replace( 'class="button', 'class="button fusion-button button-default fusion-button-default-size', $html );
	}

	/**
	 * The account dashboard.
	 *
	 * @access public
	 */
	public function account_dashboard() {
		?>
		<style>
		.woocommerce-MyAccount-content{ display: -webkit-flex;display: -ms-flexbox;display:flex;-webkit-flex-flow: column wrap;flex-flow: column nowrap; }
		.avada-woocommerce-myaccount-heading{ -ms-flex-order: 0;-webkit-order: 0;order: 0; }
		.woocommerce-MyAccount-content > p, .woocommerce-MyAccount-content > div, .woocommerce-MyAccount-content > span{ -ms-flex-order: 1;-webkit-order: 1;order: 1; }
		.woocommerce-MyAccount-content > p:first-child { display: none; }
		</style>
		<?php
		self::before_account_content_heading();
	}

	/**
	 * Content injected before the content heading.
	 *
	 * @access public
	 */
	public function before_account_content_heading() {
		if ( is_account_page() ) {
			$account_items = wc_get_account_menu_items();
			$heading_content = esc_attr__( 'Dashboard', 'Avada' );

			if ( is_wc_endpoint_url( 'orders' ) ) {
				$heading_content = $account_items['orders'];
			} elseif ( is_wc_endpoint_url( 'downloads' ) ) {
				$heading_content = $account_items['downloads'];
			} elseif ( is_wc_endpoint_url( 'payment-methods' ) ) {
				$heading_content = $account_items['payment-methods'];
			} elseif ( is_wc_endpoint_url( 'edit-account' ) ) {
				$heading_content = $account_items['edit-account'];
			}
			?>
			<h2 class="avada-woocommerce-myaccount-heading">
				<?php echo $heading_content; // WPCS: XSS ok. ?>
			</h2>
			<?php
		}
	}

	/**
	 * Dealing with mini-cart cache in internal browser storage.
	 * Response to action 'woocommerce_add_to_cart_hash', which overwrites the default WC cart hash and cookies.
	 *
	 * @access public
	 * @since 5.0.2
	 * @param string $hash Default WC hash.
	 * @param array  $cart WC variable holding contents of the cart without language information.
	 */
	public function add_to_cart_hash( $hash, $cart ) {

		$hash = $this->get_cart_hash( $cart );
		if ( ! headers_sent() ) {
			wc_setcookie( 'woocommerce_cart_hash', $hash );
		}
		return $hash;
	}

	/**
	 * Dealing with mini-cart cache in internal browser storage.
	 *
	 * @access private
	 * @since 5.0.2
	 * @param  array $cart WC variable holding contents of the cart without language information.
	 * @return string Cart hash with language information
	 */
	private function get_cart_hash( $cart ) {

		$lang = Fusion_Multilingual::get_active_language();
		return md5( wp_json_encode( $cart ) . $lang );

	}

	/**
	 * Dealing with mini-cart cache in internal browser storage.
	 * Sets 'woocommerce_cart_hash' cookie.
	 *
	 * @access private
	 * @since 5.0.2
	 * @param array $cart wc variable holding contents of the cart without language information.
	 */
	private function set_cookies_cart_hash( $cart ) {

		if ( ! $cart ) {
			return;
		}
		$hash = $this->get_cart_hash( $cart );
		wc_setcookie( 'woocommerce_cart_hash', $hash );

	}

	/**
	 * Dealing with mini-cart cache in internal browser storage.
	 * Response to action 'woocommerce_cart_loaded_from_session'.
	 *
	 * @access public
	 * @since 5.0.2
	 * @param WC_Cart $wc_cart wc object without language information.
	 */
	public function cart_loaded_from_session( $wc_cart ) {

		if ( headers_sent() || ! $wc_cart ) {
			return;
		}
		$cart = $wc_cart->get_cart_for_session();
		$this->set_cookies_cart_hash( $cart );

	}

	/**
	 * Dealing with mini-cart cache in internal browser storage.
	 * Response to action 'woocommerce_set_cart_cookies', which overwrites the default WC cart hash and cookies.
	 *
	 * @access public
	 * @since 5.0.2
	 * @param bool $set is true if cookies need to be set, otherwse they are unset in calling function.
	 */
	public function set_cart_cookies( $set ) {

		if ( $set ) {
			$wc      = WC();
			$wc_cart = $wc->cart;
			$cart    = $wc_cart->get_cart_for_session();
			$this->set_cookies_cart_hash( $cart );
		}
	}

	/**
	 * Fix for WPML.
	 *
	 * @access public
	 * @since 5.1 (Moved from the constructor - Props @andreagrillo)
	 */
	public function wpml_fix() {
		if ( class_exists( 'SitePress' ) ) {
			add_filter( 'woocommerce_add_to_cart_hash', array( $this, 'add_to_cart_hash' ), 5, 2 );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 5 );
			add_action( 'woocommerce_set_cart_cookies', array( $this, 'set_cart_cookies' ) );
		}
	}

	/**
	 * Changes the markup for the product search form.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param string $form The HTML of the form.
	 * @return string      Modified HTML of the form.
	 */
	function product_search_form( $form ) {
		ob_start();
		get_template_part( 'templates/wc-product-search-form' );
		return ob_get_clean();
	}

	/**
	 * Closes the div.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	function checkout_after_order_review() {
		echo ( Avada()->settings->get( 'woocommerce_one_page_checkout' ) ) ? '</div>' : '';
	}

	/**
	 * Open a div if needed.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function before_customer_login_form() {
		echo ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) ? '<div id="customer_login" class="woocommerce-content-box full-width">' : '';
	}

	/**
	 * Markup to add after the customer-login form.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function after_customer_login_form() {
		echo ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) ? '</div>' : '';
	}

	/**
	 * The avada_change_product_class hook - Function to add 'product-list-view' class if the list view is being displayed.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $classes An array containing class names for the particular post / product.
	 * @return array $classes An array containing additional class 'product-list-view' if the product view is set to list.
	 */
	public function change_product_class( $classes ) {
		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
			if ( isset( $params['product_view'] ) ) {
				$product_view = $params['product_view'];
				if ( 'list' == $product_view ) {
					$classes[] = 'product-list-view';
				}
			}
		}
		return $classes;
	}

	/**
	 * Controls the actions adding the ordering boxes.
	 *
	 * @access public
	 * @since 5.0.4
	 * @param object $query The main query.
	 * @return void
	 */
	public function product_ordering( $query ) {

		// We only want to affect the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( $query->get( 'page_id' ) ) {
			$page_id = absint( $query->get( 'page_id' ) );
		} else {
			$page_id = absint( Avada()->fusion_library->get_page_id() );
		}

		if ( wc_get_page_id( 'shop' ) === $page_id || $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) {

			if ( Avada()->settings->get( 'woocommerce_avada_ordering' ) ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_ordering' ), 30 );

				add_action( 'woocommerce_get_catalog_ordering_args', array( $this, 'get_catalog_ordering_args' ), 20 );
			}
		}
	}

	/**
	 * Modified the ordering of products.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function catalog_ordering() {
		get_template_part( 'templates/wc-catalog-ordering' );
	}

	/**
	 * Gets the catalogue ordering arguments.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The arguments.
	 * @return array
	 */
	function get_catalog_ordering_args( $args ) {
		global $woocommerce;
		$woo_default_catalog_orderby = get_option( 'woocommerce_default_catalog_orderby' );

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
		}

		$pob = ( ! empty( $params['product_orderby'] ) ) ? $params['product_orderby'] : $woo_default_catalog_orderby;

		$po = 'asc';
		if ( isset( $params['product_order'] ) ) {
			$po = $params['product_order'];
		}

		if ( empty( $params['product_order'] ) && empty( $params['product_orderby'] ) ) {
			$po = $args['order'];
			if ( ! isset( $args['order'] ) ) {
				$po = 'asc';
				if ( 'date' === $woo_default_catalog_orderby || 'popularity' === $woo_default_catalog_orderby || 'price-desc' === $woo_default_catalog_orderby ) {
					$po = 'desc';
				}
			}
		}

		// Remove posts_clause filter, if default ordering is set to rating or popularity to make custom ordering work correctly.
		if ( 'default' !== $pob ) {
			if ( 'rating' === $woo_default_catalog_orderby || 'popularity' === $woo_default_catalog_orderby ) {
				WC()->query->remove_ordering_args();
			}
		}

		$orderby  = 'date';

		$meta_key = '';

		switch ( $pob ) {
			case 'menu_order':
			case 'default':
				$orderby  = $args['orderby'];
				$order    = $args['order'];
				break;
			case 'date':
				$order    = 'desc';
				break;
			case 'price':
				$orderby  = 'meta_value_num';
				$order    = 'asc';
				$meta_key = '_price';
				break;
			case 'price-desc':
				$orderby  = 'meta_value_num';
				$order    = 'desc';
				$meta_key = '_price';
				break;
			case 'popularity':
				$orderby  = 'meta_value_num';
				$order    = 'desc';
				$meta_key = 'total_sales';
				break;
			case 'rating':
				$orderby  = 'meta_value_num';
				$order    = 'desc';
				$meta_key = 'average_rating';
				break;
			case 'name':
				$orderby  = 'title';
				$order    = 'asc';
				break;
		}

		switch ( strtolower( $po ) ) {
			case 'desc':
				$order = 'desc';
				break;
			case 'asc':
				$order = 'asc';
				break;
			default:
				$order = 'asc';
				break;
		}

		$args['orderby']  = $orderby;
		$args['order']    = $order;
		$args['meta_key'] = $meta_key;

		if ( 'popularity' == $pob ) {
			add_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
			add_action( 'wp', array( $this, 'remove_ordering_args_filters' ) );
		}

		if ( 'rating' == $pob ) {
			$args['orderby']  = 'menu_order title';
			$args['order']    = 'desc' == $po ? 'desc' : 'asc';
			$args['order']    = strtoupper( $args['order'] );
			$args['meta_key'] = '';

			add_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
			add_action( 'wp', array( $this, 'remove_ordering_args_filters' ) );
		}
		return $args;
	}

	/**
	 * The order_by_popularity_post_clauses method.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param array $args The arguments array.
	 * @return array The altered arguments array.
	 */
	public function order_by_popularity_post_clauses( $args ) {
		global $wpdb;
		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
		}

		$order = empty( $params['product_order'] ) ? 'DESC' : strtoupper( $params['product_order'] );
		$args['orderby'] = "$wpdb->postmeta.meta_value+0 {$order}, $wpdb->posts.post_date {$order}";
		return $args;
	}

	/**
	 * Removes the order_by_popularity_post_clauses filter.
	 *
	 * @access public
	 * @since 5.0.4
	 */
	public function remove_ordering_args_filters() {
		remove_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
		remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
	}

	/**
	 * The order_by_rating_post_clauses method.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The arguments array.
	 * @return array The altered arguments array.
	 */
	public function order_by_rating_post_clauses( $args ) {

		global $wpdb;

		$args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";
		$args['where']  .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";

		$args['join'] .= "
		LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
		LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
		}
		$order = ! empty( $params['product_order'] ) ? $params['product_order'] : 'desc';
		$order = strtoupper( $order );

		$args['orderby'] = "average_rating {$order}, $wpdb->posts.post_date {$order}";
		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}

	/**
	 * Determine how many products we want to show per page.
	 *
	 * @access public
	 * @since 5.1.0
	 * @return int
	 */
	public function loop_shop_per_page() {

		if ( isset( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ), $params );
		}

		$per_page = 12;
		if ( Avada()->settings->get( 'woo_items' ) ) {
			$per_page = Avada()->settings->get( 'woo_items' );
		}

		return ( ! empty( $params['product_count'] ) ) ? $params['product_count'] : $per_page;
	}

	/**
	 * Shows the product image.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function thumbnail() {

		$mode = Avada()->settings->get( 'woocommerce_product_box_design' );
		$mode = ( ! $mode ) ? 'classic' : $mode;
		get_template_part( 'templates/wc-thumbnail', $mode );
	}

	/**
	 * Adds cart menu item.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param string $items The menu items.
	 * @param array  $args  The menu arguments.
	 * @return string
	 */
	public function add_woo_cart_to_widget( $items, $args ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $items;
		}
		$ubermenu = false;
		if ( function_exists( 'ubermenu_get_menu_instance_by_theme_location' ) && ubermenu_get_menu_instance_by_theme_location( $args->theme_location ) ) {
			// Disable woo cart on ubermenu navigations.
			$ubermenu = true;
		}
		if ( false == $ubermenu && 'fusion-widget-menu' == $args->container_class ) {
			$items .= fusion_add_woo_cart_to_widget_html();
		}

		return $items;
	}

	/**
	 * Modify the cart ajax.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $fragments Ajax fragments handled by WooCommerce.
	 * @return array
	 */
	public function header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;

		$header_top_cart = avada_nav_woo_cart( 'secondary' );
		$fragments['.fusion-secondary-menu-cart'] = $header_top_cart;

		$header_cart = avada_nav_woo_cart( 'main' );
		$fragments['.fusion-main-menu-cart'] = $header_cart;

		$widget_cart = fusion_add_woo_cart_to_widget_html();
		$fragments['.fusion-widget-cart'] = $widget_cart;

		return $fragments;
	}

	/**
	 * Opens a div.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function single_product_summary_open() {
		echo '<div class="summary-container">';
	}

	/**
	 * Closes the div.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function single_product_summary_close() {
		echo '</div>';
	}

	/**
	 * Markup to add after the summary on single products.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function after_single_product_summary() {
		get_template_part( 'templates/wc-after-single-product-summary' );
	}

	/**
	 * Add related products.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function output_related_products() {
		global $post;

		$number_of_columns = get_post_meta( $post->ID, 'pyre_number_of_related_products', true );
		if ( in_array( $number_of_columns, array( 'default', '' ) ) || ! $number_of_columns ) {
			$number_of_columns = Avada()->settings->get( 'woocommerce_related_columns' );
		}

		$args = array(
			'posts_per_page' => $number_of_columns,
			'columns'        => $number_of_columns,
			// @codingStandardsIgnoreLine
			'orderby'        => 'rand',
		);

		echo '<div class="fusion-clearfix"></div>';
		woocommerce_related_products( apply_filters( 'woocommerce_output_related_products_args', $args ) );
	}

	/**
	 * Displays upsells.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	public function upsell_display() {

		global $product, $post;

		// Check only needed for Woo versions prior to 2.7.
		$upsells = method_exists( $product, 'get_upsell_ids' ) ? $product->get_upsell_ids() : $product->get_upsells();

		if ( 0 === count( $upsells ) ) {
			return;
		}

		echo '<div class="fusion-clearfix"></div>';

		$number_of_columns = get_post_meta( $post->ID, 'pyre_number_of_related_products', true );
		if ( in_array( $number_of_columns, array( 'default', '' ) ) || ! $number_of_columns ) {
			$number_of_columns = Avada()->settings->get( 'woocommerce_related_columns' );
		}
		woocommerce_upsell_display( - 1, $number_of_columns );
	}

	/**
	 * Add markup before the cart table.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args Not really used here.
	 */
	public function before_cart_table( $args ) {
		global $woocommerce;
		?>
		<div class="woocommerce-content-box full-width clearfix">
			<?php if ( 1 == $woocommerce->cart->get_cart_contents_count() ) : ?>
				<h2><?php printf( esc_attr__( 'You Have %d Item In Your Cart', 'Avada' ), $woocommerce->cart->get_cart_contents_count() ); // WPCS: XSS ok. ?></h2>
			<?php else : ?>
				<h2><?php printf( esc_attr__( 'You Have %d Items In Your Cart', 'Avada' ), $woocommerce->cart->get_cart_contents_count() ); // WPCS: XSS ok. ?></h2>
			<?php endif; ?>
			<?php
	}

	/**
	 * Adds markup after the cart table.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args Not used here.
	 */
	function after_cart_table( $args ) {
		echo '</div>';
	}

	/**
	 * Adds coupon code form.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The formarguments.
	 */
	public function cart_collaterals( $args ) {
		get_template_part( 'templates/wc-cart-collaterals' );
	}

	/**
	 * Displays cross-sell.
	 *
	 * @access public
	 * @since 5.1.0
	 */
	function cross_sell_display() {
		global $product, $woocommerce_loop, $post;

		$crosssells = WC()->cart->get_cross_sells();

		if ( 0 == count( $crosssells ) ) {
			return;
		}

		$number_of_columns = Avada()->settings->get( 'woocommerce_related_columns' );

		woocommerce_cross_sell_display( apply_filters( 'woocommerce_cross_sells_total', - 1 ), $number_of_columns );
	}

	/**
	 * Adds coupon form in the checkout page.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The form arguments.
	 */
	public function checkout_coupon_form( $args ) {
		include wp_normalize_path( locate_template( 'templates/wc-checkout-coupon-form.php' ) );
	}

	/**
	 * Markup to add before the checkout form.
	 *
	 * @param array $args Not used in this context.
	 */
	public function before_checkout_form( $args ) {
		include wp_normalize_path( locate_template( 'templates/wc-before-checkout-form.php' ) );
	}

	/**
	 * Closes the div after the checkout form.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The arguments (not used here).
	 */
	public function after_checkout_form( $args ) {
		echo '</div>';
	}

	/**
	 * Markup to add before the customer details form.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The form arguments. Not used in the context of this function.
	 */
	public function checkout_before_customer_details( $args ) {
		global $woocommerce;

		if ( WC()->cart->needs_shipping() && ! WC()->cart->ship_to_billing_address_only() || apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) && ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) ) {
			return;
		}
		echo '<div class="avada-checkout-no-shipping">';
	}

	/**
	 * Adds markup after the customer details form.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The form arguments. Not used in the context of this function.
	 */
	public function checkout_after_customer_details( $args ) {
		global $woocommerce;

		if ( WC()->cart->needs_shipping() && ! WC()->cart->ship_to_billing_address_only() || apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) && ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) ) {
			echo '<div class="clearboth"></div>';
		} else {
			echo '<div class="clearboth"></div></div>';
		}
		echo '<div class="woocommerce-content-box full-width">';
	}

	/**
	 * Add checkout billing markup.
	 *
	 * @param array $args The form arguments. Not used in the context of this function.
	 */
	public function checkout_billing( $args ) {
		global $woocommerce;

		$data_name = 'order_review';
		if ( WC()->cart->needs_shipping() && ! WC()->cart->ship_to_billing_address_only() || apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) && ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) ) {
			$data_name = 'col-2';
		}
		?>
		<?php if ( ! Avada()->settings->get( 'woocommerce_one_page_checkout' ) ) : ?>
			<a data-name="<?php echo esc_attr( $data_name ); ?>" href="#" class="fusion-button button-default fusion-button-default-size button continue-checkout">
				<?php esc_attr_e( 'Continue', 'Avada' ); ?>
			</a>
			<div class="clearboth"></div>
		<?php endif;
	}

	/**
	 * Add checkout shipping markup.
	 *
	 * @access public
	 * @since 5.1.0
	 * @param array $args The form arguments. Not used in the context of this function.
	 */
	public function checkout_shipping( $args ) {
		?>
		<?php if ( ! Avada()->settings->get( 'woocommerce_one_page_checkout' ) ) : ?>
			<a data-name="order_review" href="#" class="fusion-button button-default fusion-button-default-size continue-checkout button">
				<?php esc_attr_e( 'Continue', 'Avada' ); ?>
			</a>
			<div class="clearboth"></div>
		<?php endif;
	}

	/**
	 * Determines if we should enable order notes or not.
	 *
	 * @access public
	 * @since 5.1.0
	 * @return bool
	 */
	public function enable_order_notes_field() {
		return ( ! Avada()->settings->get( 'woocommerce_enable_order_notes' ) ) ? 0 : 1;
	}

}
