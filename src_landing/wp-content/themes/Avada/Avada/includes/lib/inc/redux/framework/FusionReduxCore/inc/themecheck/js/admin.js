(function( $ ) {
	"use strict";

	$(function() {

		$('#theme-check > h2').html( $('#theme-check > h2').html() + ' with FusionRedux Theme-Check' );

		if ( typeof fusionredux_check_intro !== 'undefined' ) {
			$('#theme-check .theme-check').append( fusionredux_check_intro.text );
		}
		$('#theme-check form' ).append('&nbsp;&nbsp;<input name="fusionredux_wporg" type="checkbox">  Extra WP.org Requirements.');
	});

}(jQuery));
