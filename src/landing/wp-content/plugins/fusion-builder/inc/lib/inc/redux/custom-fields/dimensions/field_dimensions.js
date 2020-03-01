
/*global jQuery, document, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.dimensions = fusionredux.field_objects.dimensions || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.dimensions.init();
        }
    );

    fusionredux.field_objects.dimensions.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.fusionredux-container-dimensions:visible' );
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

                el.find( '.fusionredux-dimensions-input' ).on(
                    'change', function() {
                        el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() );
                    }
                );

            }
        );


    };
})( jQuery );
