/*global fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.select_image = fusionredux.field_objects.select_image || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.select_image.init();
        }
    );

    fusionredux.field_objects.select_image.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-select_image:visible' );
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

                var select3_handle = el.find( '.fusionredux-container-select_image' ).find( '.select3_params' );

                if ( select3_handle.size() > 0 ) {
                    var select3_params = select3_handle.val();

                    select3_params = JSON.parse( select3_params );
                    default_params = $.extend( {}, default_params, select3_params );
                }

                el.find( 'select.fusionredux-select-images' ).select3( default_params );

                el.find( '.fusionredux-select-images' ).on(
                    'change', function() {
                        var preview = $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-preview-image' );

                        if ( $( this ).val() === "" ) {
                            preview.fadeOut(
                                'medium', function() {
                                    preview.attr( 'src', '' );
                                }
                            );
                        } else {
                            preview.attr( 'src', $( this ).val() );
                            preview.fadeIn().css( 'visibility', 'visible' );
                        }
                    }
                );
            }
        );
    };
})( jQuery );
