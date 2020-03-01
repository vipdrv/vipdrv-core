/*global jQuery, document, fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.sortable = fusionredux.field_objects.sortable || {};

    var scroll = '';

    fusionredux.field_objects.sortable.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-sortable:visible' );
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
                el.find( ".fusionredux-sortable" ).sortable(
                    {
                        handle: ".drag",
                        placeholder: "placeholder",
                        opacity: 0.7,
                        scroll: false,
                        out: function( event, ui ) {
                            if ( !ui.helper ) return;
                            if ( ui.offset.top > 0 ) {
                                scroll = 'down';
                            } else {
                                scroll = 'up';
                            }
                            fusionredux.field_objects.sortable.scrolling( $( this ).parents( '.fusionredux-field-container:first' ) );
                        },

                        over: function( event, ui ) {
                            scroll = '';
                        },

                        deactivate: function( event, ui ) {
                            scroll = '';
                        },

                        update: function() {
                            fusionredux_change( $( this ) );
                        }
                    }
                );

                el.find( '.checkbox_sortable' ).on(
                    'click', function() {
                        if ( $( this ).is( ":checked" ) ) {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( 1 );
                        } else {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( '' );
                        }
                    }
                );
            }
        );
    };

    fusionredux.field_objects.sortable.scrolling = function( selector ) {
        if (selector === undefined) {
            return;
        }

        var $scrollable = selector.find( ".fusionredux-sorter" );

        if ( scroll == 'up' ) {
            $scrollable.scrollTop( $scrollable.scrollTop() - 20 );
            setTimeout( fusionredux.field_objects.sortable.scrolling, 50 );
        } else if ( scroll == 'down' ) {
            $scrollable.scrollTop( $scrollable.scrollTop() + 20 );
            setTimeout( fusionredux.field_objects.sortable.scrolling, 50 );
        }
    };

})( jQuery );
