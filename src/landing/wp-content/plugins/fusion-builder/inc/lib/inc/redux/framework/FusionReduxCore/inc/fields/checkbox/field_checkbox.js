/**
 * FusionRedux Checkbox
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 * Date                : 17 June 2014
 */

/*global fusionredux_change, wp, fusionredux*/

(function( $ ) {
	"use strict";

	fusionredux.field_objects = fusionredux.field_objects || {};
	fusionredux.field_objects.checkbox = fusionredux.field_objects.checkbox || {};

	$( document ).ready(
		function() {
			//fusionredux.field_objects.checkbox.init();
		}
	);

	fusionredux.field_objects.checkbox.init = function( selector ) {
		if ( !selector ) {
			selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-checkbox:visible' );
		}

		$( selector ).each(
			function() {
				var el = $( this );
				var parent = el;
				if ( !el.hasClass( 'fusionredux-field-container' ) ) {
					parent = el.parents( '.fusionredux-field-container:first' );
				}
				if ( parent.is( ":hidden" ) ) { // Skip hidden fields
					return;
				}
				if ( parent.hasClass( 'fusionredux-field-init' ) ) {
					parent.removeClass( 'fusionredux-field-init' );
				} else {
					return;
				}
				el.find( '.checkbox' ).on(
					'click', function( e ) {
						var val = 0;
						if ( $( this ).is( ':checked' ) ) {
							val = $( this ).parent().find( '.checkbox-check' ).attr( 'data-val' );
						}
						$( this ).parent().find( '.checkbox-check' ).val( val );
						fusionredux_change( $( this ) );
					}
				);
			}
		);
	};
})( jQuery );
