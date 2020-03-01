/**
 * FusionRedux Editor on change callback
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 *                     : Kevin Provance (who helped)  :P
 * Date                : 07 June 2014
 */

/*global fusionredux_change, wp, tinymce, fusionredux*/
(function( $ ) {
	"use strict";

	fusionredux.field_objects = fusionredux.field_objects || {};
	fusionredux.field_objects.editor = fusionredux.field_objects.editor || {};

	$( document ).ready(
		function() {
			//fusionredux.field_objects.editor.init();
		}
	);

	fusionredux.field_objects.editor.init = function( selector ) {
		setTimeout(
			function() {
				if (typeof(tinymce) !== 'undefined') {
					for ( var i = 0; i < tinymce.editors.length; i++ ) {
						fusionredux.field_objects.editor.onChange( i );
					}
				}
			}, 1000
		);
	};

	fusionredux.field_objects.editor.onChange = function( i ) {
		tinymce.editors[i].on(
			'change', function( e ) {
				var el = jQuery( e.target.contentAreaContainer );
				if ( el.parents( '.fusionredux-container-editor:first' ).length !== 0 ) {
					fusionredux_change( $( '.wp-editor-area' ) );
				}
			}
		);
	};
})( jQuery );
