/*
 Field Palette (color)
 */

/*global jQuery, document, fusionredux_change, fusionredux*/

(function( $ ) {
    'use strict';

    fusionredux.field_objects         = fusionredux.field_objects || {};
    fusionredux.field_objects.palette = fusionredux.field_objects.palette || {};

    fusionredux.field_objects.palette.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-palette:visible' );
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
                        $( this ).buttonset();
                    }
                );

//                el.find('.fusionredux-palette-set').click(
//                    function(){
//                        console.log($(this).val());
//                    }
//                )
            }
        );
    };
})( jQuery );
