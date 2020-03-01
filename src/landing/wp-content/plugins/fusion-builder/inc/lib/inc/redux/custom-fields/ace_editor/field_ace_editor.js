/*global jQuery, document, fusionredux*/

(function( $ ) {
	'use strict';

	fusionredux.field_objects = fusionredux.field_objects || {};
	fusionredux.field_objects.ace_editor = fusionredux.field_objects.ace_editor || {};

	fusionredux.field_objects.ace_editor.init = function( selector ) {
		if ( ! selector ) {
			selector = $( document ).find( '.fusionredux-group-tab:visible' ).find( '.fusionredux-container-ace_editor:visible' );
		}

		$( selector ).each( function() {
			var el     = $( this ),
			    parent = el;

			if ( ! el.hasClass( 'fusionredux-field-container' ) ) {
				parent = el.parents( '.fusionredux-field-container:first' );
			}
			if ( parent.is( ':hidden' ) ) { // Skip hidden fields
				return;
			}
			if ( parent.hasClass( 'fusionredux-field-init' ) ) {
				parent.removeClass( 'fusionredux-field-init' );
			} else {
				return;
			}

			el.find( '.ace-editor' ).each( function( index, element ) {
				var area      = element,
				    params    = JSON.parse( $( this ).parent().find( '.localize_data' ).val() ),
				    editor    = $( element ).attr( 'data-editor' ),
				    aceeditor = ace.edit( editor ),
				    parent    = '';

				aceeditor.setTheme( 'ace/theme/chrome' );
				aceeditor.getSession().setMode( 'ace/mode/' + $( element ).attr( 'data-mode' ) );

				if ( el.hasClass( 'fusionredux-field-container' ) ) {
					parent = el.attr( 'data-id' );
				} else {
					parent = el.parents( '.fusionredux-field-container:first' ).attr( 'data-id' );
				}

				aceeditor.setOptions( params );
				aceeditor.on(
					'change', function( e ) {
						$( '#' + area.id ).val( aceeditor.getSession().getValue() );
						fusionredux_change( $( element ) );
						aceeditor.resize();
					}
				);
			});
		});
	};
})( jQuery );
