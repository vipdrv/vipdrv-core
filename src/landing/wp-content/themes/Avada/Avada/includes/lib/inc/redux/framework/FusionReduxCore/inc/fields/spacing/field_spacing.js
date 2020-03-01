/*global fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.spacing = fusionredux.field_objects.spacing || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.spacing.init();
        }
    );

    fusionredux.field_objects.spacing.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-spacing:visible' );
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

                el.find( ".fusionredux-spacing-units" ).select3( default_params );

                el.find( '.fusionredux-spacing-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.fusionredux-field:first' ).find( '.field-units' ).val();

                        if ( $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-spacing-units' ).length !== 0 ) {
                            units = $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-spacing-units option:selected' ).val();
                        }

                        var value = $( this ).val();

                        if ( typeof units !== 'undefined' && value ) {
                            value += units;
                        }

                        if ( $( this ).hasClass( 'fusionredux-spacing-all' ) ) {
                            $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-spacing-value' ).each(
                                function() {
                                    $( this ).val( value );
                                }
                            );
                        } else {
                            $( '#' + $( this ).attr( 'rel' ) ).val( value );
                        }
                    }
                );

                el.find( '.fusionredux-spacing-units' ).on(
                    'change', function() {
                        $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-spacing-input' ).change();
                    }
                );
            }
        );
    };
})( jQuery );
