jQuery( document ).ready( function() {

	// Table handler
	TableShortcodeHandler = jQuery( '#fusion_table_type, #fusion_table_columns' );

	TableShortcodeHandler.live( 'change', function( e ) {
		var types = new Array(
				'',
				'table-1',
				'table-2'
		    ),
		    type = jQuery( '#fusion_table_type' ).val(),
		    columns = jQuery( '#fusion_table_columns' ).val(),
		    exampleTable = '<div class="' + types[type] + '"><table width="100%"><thead><tr>',
		    i;

		// Add table headers
		for ( i = 0; i < columns; i++ ) {
				exampleTable += '<th align="left">Column ' + i + '</th>';
		}
		exampleTable += '</tr></thead><tr>';

		// Add table columns
		for ( i = 0; i < columns; i++ ) {
				exampleTable += '<td align="left">Column ' + i + ' Value</td>';
		}
		exampleTable += '</tr></tbody></table></div>';

		setTimeout( function() {
			if ( true === FusionPageBuilderApp.shortcodeGenerator ) {
				FusionPageBuilderApp.fusionBuilderSetContent( 'generator_element_content', exampleTable );
			} else {
				FusionPageBuilderApp.fusionBuilderSetContent( 'element_content', exampleTable );
			}

		}, 100 );

	});
});
