( function( $ ) {

	$( document ).ready( function() {

		// Pricing table. Build pricing table shortcode.
		FusionPageBuilderApp.pricingTableShortcodeFilter = function( attributes, view ) {

			var shortcode     = '',
			    columnCounter = 0,
			    table         = view.$( '.fusion-builder-table' );

			// Table head
			table.find( 'thead th' ).each( function() {
				var $thisTh    = $( this ),
				    thCID      = $thisTh.data( 'th-id' ),
				    thTitle    = $thisTh.find( 'input:not(.button-set-value)' ).val(),
				    thStandout = $thisTh.find( '.button-set-value' ).val();

				columnCounter++;

				shortcode += '[fusion_pricing_column title="' + thTitle + '" standout="' + thStandout + '"]';

				// Table price
				table.find( 'tbody .price .td-' + thCID ).each( function() {
					var $thisPrice       = $( this ),
					    price            = $thisPrice.find( '.price-input' ).val(),
					    currency         = $thisPrice.find( '.currency-input' ).val(),
					    currencyPosition = $thisPrice.find( '.currency-position' ).val(),
					    time             = $thisPrice.find( '.time-input' ).val();

					shortcode += '[fusion_pricing_price currency="' + currency + '" currency_position="' + currencyPosition + '" price="' + price + '" time="' + time + '" ][/fusion_pricing_price]';
				} );

				// Table rows
				table.find( '.fusion-table-row .td-' + thCID ).each( function() {
					var $thisRow = $( this ),
					    content  = $thisRow.find( 'input' ).val();

					if ( '' !== content ) {
						shortcode += '[fusion_pricing_row]' + content + '[/fusion_pricing_row]';
					}
				} );

				// Table footer
				table.find( 'tfoot .td-' + thCID ).each( function() {
					var $thisFooter   = $( this ),
					    footerContent = $thisFooter.find( 'textarea' ).val();

					if ( '' !== footerContent ) {
						shortcode += '[fusion_pricing_footer]' + footerContent + '[/fusion_pricing_footer]';
					}
				} );

				shortcode += '[/fusion_pricing_column]';

			} );

			attributes.params.element_content = shortcode;
			attributes.params.columns = columnCounter;

			return attributes;

		};

	});

}( jQuery ) );
