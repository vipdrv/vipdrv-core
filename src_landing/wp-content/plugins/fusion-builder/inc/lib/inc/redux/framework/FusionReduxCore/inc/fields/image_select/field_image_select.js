/* global confirm, fusionredux, fusionredux_change */

/*global fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.image_select = fusionredux.field_objects.image_select || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.image_select.init();
        }
    );

    fusionredux.field_objects.image_select.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-image_select:visible' );
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
                // On label click, change the input and class
                el.find( '.fusionredux-image-select label img, .fusionredux-image-select label .tiles' ).click(
                    function( e ) {
                        var id = $( this ).closest( 'label' ).attr( 'for' );

                        $( this ).parents( "fieldset:first" ).find( '.fusionredux-image-select-selected' ).removeClass( 'fusionredux-image-select-selected' ).find( "input[type='radio']" ).attr(
                            "checked", false
                        );
                        $( this ).closest( 'label' ).find( 'input[type="radio"]' ).prop( 'checked' );

                        if ( $( this ).closest( 'label' ).hasClass( 'fusionredux-image-select-preset-' + id ) ) { // If they clicked on a preset, import!
                            e.preventDefault();

                            var presets = $( this ).closest( 'label' ).find( 'input' );
                            var data = presets.data( 'presets' );
                            var merge = presets.data( 'merge' );

                            if( merge !== undefined && merge !== null ) {
                                if( $.type( merge ) === 'string' ) {
                                    merge = merge.split('|');
                                }

                                $.each(data, function( index, value ) {
                                    if( ( merge === true || $.inArray( index, merge ) != -1 ) && $.type( fusionredux.options[index] ) === 'object' ) {
                                        data[index] = $.extend(fusionredux.options[index], data[index]);
                                    }
                                });
                            }

                            if ( presets !== undefined && presets !== null ) {
                                var answer = confirm( fusionredux.args.preset_confirm );

                                if ( answer ) {
                                    el.find( 'label[for="' + id + '"]' ).addClass( 'fusionredux-image-select-selected' ).find( "input[type='radio']" ).attr(
                                        "checked", true
                                    );
                                    window.onbeforeunload = null;
                                    if ( $( '#import-code-value' ).length === 0 ) {
                                        $( this ).append( '<textarea id="import-code-value" style="display:none;" name="' + fusionredux.args.opt_name + '[import_code]">' + JSON.stringify( data ) + '</textarea>' );
                                    } else {
                                        $( '#import-code-value' ).val( JSON.stringify( data ) );
                                    }
                                    if ( $( '#publishing-action #publish' ).length !== 0 ) {
                                        $( '#publish' ).click();
                                    } else {
                                        $( '#fusionredux-import' ).click();
                                    }
                                }
                            } else {
                            }

                            return false;
                        } else {
                            el.find( 'label[for="' + id + '"]' ).addClass( 'fusionredux-image-select-selected' ).find( "input[type='radio']" ).attr(
                                "checked", true
                            ).trigger('change');

                            fusionredux_change( $( this ).closest( 'label' ).find( 'input[type="radio"]' ) );
                        }
                    }
                );

                // Used to display a full image preview of a tile/pattern
                el.find( '.tiles' ).qtip(
                    {
                        content: {
                            text: function( event, api ) {
                                return "<img src='" + $( this ).attr( 'rel' ) + "' style='max-width:150px;' alt='' />";
                            },
                        },
                        style: 'qtip-tipsy',
                        position: {
                            my: 'top center', // Position my top left...
                            at: 'bottom center', // at the bottom right of...
                        }
                    }
                );
            }
        );

    };
})( jQuery );
