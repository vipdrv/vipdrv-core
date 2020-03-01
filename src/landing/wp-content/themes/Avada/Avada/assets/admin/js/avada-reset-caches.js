function fusionResetCaches( e ) {
	var data = {
			action: 'avada_reset_all_caches'
		},
	    confirm = window.confirm( avadaReduxResetCaches.confirm );

	e.preventDefault();

	if ( true === confirm ) {
		jQuery.post( avadaReduxResetCaches.ajaxurl, data, function( response ) {
			window.confirm( avadaReduxResetCaches.success );
		} );
	}
}
