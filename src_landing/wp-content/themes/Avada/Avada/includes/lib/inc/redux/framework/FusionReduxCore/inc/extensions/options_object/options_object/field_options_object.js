/*global fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects                 = fusionredux.field_objects || {};
    fusionredux.field_objects.options_object  = fusionredux.field_objects.options_object || {};

//    $( document ).ready(
//        function() {
//            fusionredux.field_objects.import_export.init();
//        }
//    );

    fusionredux.field_objects.options_object.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.fusionredux-container-options_object' );
        }

        var parent = selector;

        if ( !selector.hasClass( 'fusionredux-field-container' ) ) {
            parent = selector.parents( '.fusionredux-field-container:first' );
        }

        if ( parent.hasClass( 'fusionredux-field-init' ) ) {
            parent.removeClass( 'fusionredux-field-init' );
        } else {
            return;
        }

        $( '#consolePrintObject' ).on(
            'click', function( e ) {
                e.preventDefault();
                console.log( $.parseJSON( $( "#fusionredux-object-json" ).html() ) );
            }
        );

        if ( typeof jsonView === 'function' ) {
            jsonView( '#fusionredux-object-json', '#fusionredux-object-browser' );
        }
    };
})( jQuery );
