/*
 Field Border (border)
 */

/*global fusionredux_change, wp, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.border = fusionredux.field_objects.border || {};

    fusionredux.field_objects.border.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-border:visible' );
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
                el.find( ".fusionredux-border-top, .fusionredux-border-right, .fusionredux-border-bottom, .fusionredux-border-left, .fusionredux-border-all" ).numeric(
                    {
                        allowMinus: false
                    }
                );

                var default_params = {
                    triggerChange: true,
                    allowClear: true
                };

                var select3_handle = el.find( '.fusionredux-container-border' ).find( '.select3_params' );

                if ( select3_handle.size() > 0 ) {
                    var select3_params = select3_handle.val();

                    select3_params = JSON.parse( select3_params );
                    default_params = $.extend( {}, default_params, select3_params );
                }

                el.find( ".fusionredux-border-style" ).select3( default_params );

                el.find( '.fusionredux-border-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.fusionredux-field:first' ).find( '.field-units' ).val();
                        if ( $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-border-units' ).length !== 0 ) {
                            units = $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-border-units option:selected' ).val();
                        }
                        var value = $( this ).val();
                        if ( typeof units !== 'undefined' && value ) {
                            value += units;
                        }
                        if ( $( this ).hasClass( 'fusionredux-border-all' ) ) {
                            $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-border-value' ).each(
                                function() {
                                    $( this ).val( value );
                                }
                            );
                        } else {
                            $( '#' + $( this ).attr( 'rel' ) ).val( value );
                        }
                    }
                );

                el.find( '.fusionredux-border-units' ).on(
                    'change', function() {
                        $( this ).parents( '.fusionredux-field:first' ).find( '.fusionredux-border-input' ).change();
                    }
                );

                el.find( '.fusionredux-color-init' ).wpColorPicker(
                    {
                        change: function( e, ui ) {
                            $( this ).val( ui.color.toString() );
                            fusionredux_change( $( this ) );
                            el.find( '#' + e.target.getAttribute( 'data-id' ) + '-transparency' ).removeAttr( 'checked' );
                        },

                        clear: function( e, ui ) {
                            $( this ).val( ui.color.toString() );
                            fusionredux_change( $( this ).parent().find( '.fusionredux-color-init' ) );
                        }
                    }
                );

                el.find( '.fusionredux-color' ).on(
                    'keyup', function() {
                        var color = colorValidate( this );

                        if ( color && color !== $( this ).val() ) {
                            $( this ).val( color );
                        }
                    }
                );

                // Replace and validate field on blur
                el.find( '.fusionredux-color' ).on(
                    'blur', function() {
                        var value = $( this ).val();

                        if ( colorValidate( this ) === value ) {
                            if ( value.indexOf( "#" ) !== 0 ) {
                                $( this ).val( $( this ).data( 'oldcolor' ) );
                            }
                        }
                    }
                );

                // Store the old valid color on keydown
                el.find( '.fusionredux-color' ).on(
                    'keydown', function() {
                        $( this ).data( 'oldkeypress', $( this ).val() );
                    }
                );
            }
        );
    };
})( jQuery );
