jQuery( document ).ready( function() {

	jQuery( '.fusion-builder-admin-toggle-heading' ).on( 'click', function() {
		jQuery( this ).parent().find( '.fusion-builder-admin-toggle-content' ).slideToggle( 300 );

		if ( jQuery( this ).find( '.fusion-builder-admin-toggle-icon' ).hasClass( 'dashicons-plus' ) ) {
			jQuery( this ).find( '.fusion-builder-admin-toggle-icon' ).removeClass( 'dashicons-plus' ).addClass( 'dashicons-minus' );
		} else {
			jQuery( this ).find( '.fusion-builder-admin-toggle-icon' ).removeClass( 'dashicons-minus' ).addClass( 'dashicons-plus' );
		}

	});

	jQuery( '.enable-builder-ui .ui-button' ).on( 'click', function( e ) {
		e.preventDefault();

		jQuery( this ).parent().find( '#enable_builder_ui_by_default' ).val( jQuery( this ).data( 'value' ) );
		jQuery( this ).parent().find( '.ui-button' ).removeClass( 'ui-state-active' );
		jQuery( this ).addClass( 'ui-state-active' );
	});

	jQuery( '.fusion-check-all' ).click( function( e ) {
		e.preventDefault();
		jQuery( this ).parents( '.fusion-builder-option' ).find( '.fusion-builder-option-field input' ).prop( 'checked', true );
	});

	jQuery( '.fusion-uncheck-all' ).click( function( e ) {
		e.preventDefault();
		jQuery( this ).parents( '.fusion-builder-option' ).find( '.fusion-builder-option-field input' ).prop( 'checked', false );
	});

});
