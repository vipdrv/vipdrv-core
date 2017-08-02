/*
 Field Button Set (button_set)
 */

/*global jQuery, document, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.button_set = fusionredux.field_objects.button_set || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.button_set.init();
            if ( $.fn.button.noConflict !== undefined ) {
                var btn = $.fn.button.noConflict();
                $.fn.btn = btn;
            }
        }
    );

    fusionredux.field_objects.button_set.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-button_set:visible' );
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
                el.find( '.buttonset' ).each(
                    function() {
                        if ( $( this ).is( ':checkbox' ) ) {
                            $( this ).find( '.buttonset-item' ).button();
                        }

                        $( this ).buttonset();
                    }
                );
            }
        );

    };
})( jQuery );
