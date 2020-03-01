<?php
/**
 * Fusion Framework
 * WARNING: This file is part of the Fusion Core Framework.
 * Do not edit the core files.
 * Add any modifications necessary under a child theme.
 *
 * @author     ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Fusion Builder
 * @subpackage Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Don't duplicate me!
if ( ! class_exists( 'Avada_Woocommerce' ) && ! class_exists( 'FusionBuilder_Woocommerce' ) ) {

	/**
	 * Class to apply woocommerce templates.
	 *
	 * @since 4.0.0
	 */
	class FusionBuilder_Woocommerce {

		/**
		 * Constructor.
		 */
		function __construct() {
			add_action( 'avada_woocommerce_buttons_on_rollover',  array( $this, 'template_loop_add_to_cart' ), 10 );
			add_action( 'avada_woocommerce_buttons_on_rollover',  array( $this, 'rollover_buttons_linebreak' ), 15 );
			add_action( 'avada_woocommerce_buttons_on_rollover', array( $this, 'show_details_button' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'woocommerce_scripts' ) );
			add_filter( 'fusion_dynamic_css_final', array( $this, 'woocommerce_styles_dynamic_css' ) );
		}

		/**
		 * Helper method to get the version of the currently installed WooCommerce.
		 *
		 * @since 3.7.2
		 * @return string woocommerce version number or null.
		 */
		private static function get_wc_version() {
			return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
		}

		/**
		 * Add to cart loop.
		 *
		 * @access public
		 * @param array $args The arguments.
		 */
		function template_loop_add_to_cart( $args = array() ) {
			global $product;

			if ( $product && ( ( $product->is_purchasable() && $product->is_in_stock() ) || $product->is_type( 'external' ) ) ) {

				if ( version_compare( self::get_wc_version(), '2.5', '>=' ) ) {

					$defaults = array(
						'quantity' => 1,
						'class'    => implode( ' ', array_filter( array(
							'button',
							'product_type_' . $product->get_type(),
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
			global $product, $fusion_settings; ?>
			<?php if ( $product && ( ( $product->is_purchasable() && $product->is_in_stock() ) || $product->is_type( 'external' ) ) ) : ?>
				<span class="fusion-rollover-linebreak">
					<?php echo ( 'clean' === $fusion_settings->get( 'woocommerce_product_box_design' ) ) ? '/' : ''; ?>
				</span>
			<?php endif;
		}

		/**
		 * Renders the "Details" button.
		 *
		 * @access public
		 */
		public function show_details_button() {
			global $product;

			$styles = '';
			if ( ( ! $product->is_purchasable() || ! $product->is_in_stock() ) && ! $product->is_type( 'external' ) ) {
				$styles = ' style="float:none;max-width:none;text-align:center;"';
			}
			// @codingStandardsIgnoreLine
			echo '<a href="' . esc_url_raw( get_permalink() ) . '" class="show_details_button"' . $styles . '>' . esc_attr__( 'Details', 'fusion-builder' ) . '</a>';
		}

		/**
		 * Enqueue scripts for woocommerce.
		 *
		 * @access public
		 */
		public function woocommerce_scripts() {
			wp_enqueue_script( 'fusion_builder_woocommerce_js', FUSION_BUILDER_PLUGIN_URL . 'inc/woocommerce/js/woocommerce.js', '', FUSION_BUILDER_VERSION, true );
			$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
			$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;

			if ( 'file' !== $mode ) {
				wp_enqueue_style( 'fusion-builder-woocommerce', FUSION_BUILDER_PLUGIN_URL . 'inc/woocommerce/css/woocommerce.min.css', array(), FUSION_BUILDER_VERSION );
			}
		}

		/**
		 * Add scripts to dynamic-css if using a file compiler.
		 *
		 * @access public
		 * @since 5.1.5
		 * @param string $original_styles The dynamic-css styles.
		 * @return string The dynamic-css styles with additional stylesheets appended if necessary.
		 */
		public function woocommerce_styles_dynamic_css( $original_styles ) {
			$dynamic_css_obj = Fusion_Dynamic_CSS::get_instance();
			$mode = ( method_exists( $dynamic_css_obj, 'get_mode' ) ) ? $dynamic_css_obj->get_mode() : $dynamic_css_obj->mode;
			$styles = '';

			if ( 'file' === $mode ) {
				$wp_filesystem = Fusion_Helper::init_filesystem();

				// Stylesheet ID: fusion-builder-woocommerce. @codingStandardsIgnoreLine
				$styles .= @file_get_contents( FUSION_BUILDER_PLUGIN_DIR . 'inc/woocommerce/css/woocommerce.min.css' );
			}
			return $styles . $original_styles;
		}
	}

	new FusionBuilder_Woocommerce();
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
