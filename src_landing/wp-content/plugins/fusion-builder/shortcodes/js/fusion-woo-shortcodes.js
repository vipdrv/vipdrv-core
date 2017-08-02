( function( $ ) {

	$( document ).ready( function() {
		// Woo shortcodes handler
		WooShortcodeHanlder = $( '#fusion_woo_shortcode' );

		// WooCommerce shortocodes handler
		WooShortcodeHanlder.live( 'change', function( e ) {
			var shortoCodes = new Array(
				' ',
				'[woocommerce_order_tracking]',
				'[add_to_cart id="" sku=""]',
				'[product id="" sku=""]',
				'[products ids="" skus=""]',
				'[product_categories number=""]',
				'[product_category category="" per_page="12" columns="4" orderby="date" order="desc"]',
				'[recent_products per_page="12" columns="4" orderby="date" order="desc"]',
				'[featured_products per_page="12" columns="4" orderby="date" order="desc"]',
				'[shop_messages]'
			),
			selected = $( this ).val();

			// Update content
			if ( true === FusionPageBuilderApp.shortcodeGenerator ) {
				$( '#generator_element_content' ).val( shortoCodes[selected] );
			} else {
				$( '#element_content' ).val( shortoCodes[selected] );
			}

		} );
	});

}( jQuery ) );
