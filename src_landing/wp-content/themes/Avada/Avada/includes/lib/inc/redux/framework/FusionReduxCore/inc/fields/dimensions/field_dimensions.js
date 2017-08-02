
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
                var default_params = {
                    width: 'resolve',
                    triggerChange: true,
                    allowClear: true
                };

                var select3_handle = el.find( '.select3_params' );
                if ( select3_handle.size() > 0 ) {
                    var select3_params = select3_handle.val();

                    select3_params = JSON.parse( select3_params );
                    default_params = $.extend( {}, default_params, select3_params );
                }

                el.find( ".fusionredux-dimensions-units" ).select3( default_params );

                el.find( '.fusionredux-dimensions-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.fusionredux-field:first' ).find( '.field-units' ).val();
                        if ( $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-dimensions-units' ).length !== 0 ) {
                            units = $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-dimensions-units option:selected' ).val();
                        }
                        if ( typeof units !== 'undefined' ) {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() + units );
                        } else {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() );
                        }
                    }
                );

                el.find( '.fusionredux-dimensions-units' ).on(
                    'change', function() {
                        $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-dimensions-input' ).change();
                    }
                );
            }
        );


    };
})( jQuery );
