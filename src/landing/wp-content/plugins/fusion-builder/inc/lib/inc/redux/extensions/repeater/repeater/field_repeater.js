/* global fusionredux_change */

/*global fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.repeater = fusionredux.field_objects.repeater || {};

    $( document ).ready(
        function() {

        }
    );

    fusionredux.field_objects.repeater.sort_repeaters = function( selector ) {
        if ( !selector.hasClass( 'fusionredux-container-repeater' ) ) {
            selector = selector.parents( '.fusionredux-container-repeater:first' );
        }

        selector.find( '.fusionredux-repeater-accordion-repeater' ).each(
            function( idx ) {

                var id = $( this ).attr( 'data-sortid' );
                var input = $( this ).find( "input[name*='[" + id + "]']" );
                input.each(
                    function() {
                        $( this ).attr( 'name', $( this ).attr( 'name' ).replace( '[' + id + ']', '[' + idx + ']' ) );
                    }
                );

                var select = $( this ).find( "select[name*='[" + id + "]']" );
                select.each(
                    function() {
                        $( this ).attr( 'name', $( this ).attr( 'name' ).replace( '[' + id + ']', '[' + idx + ']' ) );
                    }
                );
                $( this ).attr( 'data-sortid', idx );

                // Fix the accordian header
                var header = $( this ).find( '.ui-accordion-header' );
                var split = header.attr( 'id' ).split( '-header-' );
                header.attr( 'id', split[0] + '-header-' + idx );
                split = header.attr( 'aria-controls' ).split( '-panel-' );
                header.attr( 'aria-controls', split[0] + '-panel-' + idx );

                // Fix the accordian content
                var content = $( this ).find( '.ui-accordion-content' );
                var split = content.attr( 'id' ).split( '-panel-' );
                content.attr( 'id', split[0] + '-panel-' + idx );
                split = content.attr( 'aria-labelledby' ).split( '-header-' );
                content.attr( 'aria-labelledby', split[0] + '-header-' + idx );

            }
        );
    };


    fusionredux.field_objects.repeater.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-repeater:visible' );
        }

        $( selector ).each(
            function( idx ) {

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

                var parent = el;

                if ( !el.hasClass( 'fusionredux-field-container' ) ) {
                    parent = el.parents( '.fusionredux-field-container:first' );
                }

                var gid = parent.attr( 'data-id' );

                var blank = el.find( '.fusionredux-repeater-accordion-repeater:last-child' );
                fusionredux.repeater[gid].blank = blank.clone().wrap( '<p>' ).parent().html();


                if ( parent.hasClass( 'fusionredux-container-repeater' ) ) {
                    parent.addClass( 'fusionredux-field-init' );
                }

                if ( parent.hasClass( 'fusionredux-field-init' ) ) {
                    parent.removeClass( 'fusionredux-field-init' );
                } else {
                    return;
                }

                var active = false;

                //if ( el.find( '.slide-title' ).length < 2 ) {
                //    active = 0;
                //}

                var accordian = el.find( ".fusionredux-repeater-accordion" ).accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        //active: active,
                        activate: function( event, ui ) {
                            $.fusionredux.initFields();
                        },
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                );
                if ( fusionredux.repeater[gid].sortable == 1 ) {
                    accordian.sortable(
                        {
                            axis: "y",
                            handle: "h3",
                            connectWith: ".fusionredux-repeater-accordion",
                            placeholder: "ui-state-highlight",
                            start: function( e, ui ) {
                                ui.placeholder.height( ui.item.height() );
                                ui.placeholder.width( ui.item.width() );
                            },
                            stop: function( event, ui ) {
                                // IE doesn't register the blur when sorting
                                // so trigger focusout handlers to remove .ui-state-focus
                                ui.item.children( "h3" ).triggerHandler( "focusout" );

                                fusionredux.field_objects.repeater.sort_repeaters( $( this ) );

                            }
                        }
                    );
                } else {
                    accordian.find( 'h3.ui-accordion-header' ).css( 'cursor', 'pointer' );
                }

                el.find( '.fusionredux-repeater-accordion-repeater .bind_title' ).on(
                    'change keyup', function( event ) {
                        if ( $( event.target ).find( ':selected' ).text().length > 0 ) {
                            var value = $( event.target ).find( ':selected' ).text();
                        } else {
                            var value = $( event.target ).val()
                        }
                        $( this ).closest( '.fusionredux-repeater-accordion-repeater' ).find( '.fusionredux-repeater-header' ).text( value );
                    }
                );

                // Handler to remove the given repeater
                el.find( '.fusionredux-repeaters-remove' ).live(
                    'click', function() {
                        fusionredux_change( $( this ) );
                        var parent = $( this ).parents( '.fusionredux-container-repeater:first' );
                        var gid = parent.attr( 'data-id' );
                        fusionredux.repeater[gid].blank = $( this ).parents( '.fusionredux-repeater-accordion-repeater:first' ).clone(
                            true, true
                        );
                        $( this ).parents( '.fusionredux-repeater-accordion-repeater:first' ).slideUp(
                            'medium', function() {
                                $( this ).remove();
                                fusionredux.field_objects.repeater.sort_repeaters( el );
                                if ( fusionredux.repeater[gid].limit != '' ) {
                                    var count = parent.find( '.fusionredux-repeater-accordion-repeater' ).length;
                                    if ( count < fusionredux.repeater[gid].limit ) {
                                        parent.find( '.fusionredux-repeaters-add' ).removeClass( 'button-disabled' );
                                    }
                                }
                                parent.find( '.fusionredux-repeater-accordion-repeater:last .ui-accordion-header' ).click();
                            }
                        );

                    }
                );

                String.prototype.fusionreduxReplaceAll = function( s1, s2 ) {
                    return this.replace(
                        new RegExp( s1.replace( /[.^$*+?()[{\|]/g, '\\$&' ), 'g' ),
                        s2
                    );
                };


                el.find( '.fusionredux-repeaters-add' ).click(
                    function() {

                        if ( $( this ).hasClass( 'button-disabled' ) ) {
                            return;
                        }

                        var parent = $( this ).parent().find( '.fusionredux-repeater-accordion:first' );
                        var count = parent.find( '.fusionredux-repeater-accordion-repeater' ).length;

                        var gid = parent.attr( 'data-id' ); // Group id
                        if ( fusionredux.repeater[gid].limit != '' ) {
                            if ( count >= fusionredux.repeater[gid].limit ) {
                                $( this ).addClass( 'button-disabled' );
                                return;
                            }
                        }
                        count++;

                        var id = parent.find( '.fusionredux-repeater-accordion-repeater' ).size(); // Index number


                        if ( parent.find( '.fusionredux-repeater-accordion-repeater:last' ).find( '.ui-accordion-header' ).hasClass( 'ui-state-active' ) ) {
                            parent.find( '.fusionredux-repeater-accordion-repeater:last' ).find( '.ui-accordion-header' ).click();
                        }

                        var newSlide = parent.find( '.fusionredux-repeater-accordion-repeater:last' ).clone( true, true );

                        if ( newSlide.length == 0 ) {
                            newSlide = fusionredux.repeater[gid].blank;
                        }

                        if ( fusionredux.repeater[gid] ) {
                            fusionredux.repeater[gid].count = el.find( '.fusionredux-repeater-header' ).length;
                            var html = fusionredux.repeater[gid].html.fusionreduxReplaceAll( '99999', id );
                            $( newSlide ).find( '.fusionredux-repeater-header' ).text( '' );
                        }

                        newSlide.find( '.ui-accordion-content' ).html( html );
                        // Append to the accordian
                        $( parent ).append( newSlide );
                        // Reorder
                        fusionredux.field_objects.repeater.sort_repeaters( newSlide );
                        // Refresh the JS object
                        var newSlide = $( this ).parent().find( '.fusionredux-repeater-accordion:first' );
                        newSlide.find( '.fusionredux-repeater-accordion-repeater:last .ui-accordion-header' ).click();
                        newSlide.find( '.fusionredux-repeater-accordion-repeater:last .bind_title' ).on(
                            'change keyup', function( event ) {
                                if ( $( event.target ).find( ':selected' ).text().length > 0 ) {
                                    var value = $( event.target ).find( ':selected' ).text();
                                } else {
                                    var value = $( event.target ).val()
                                }
                                $( this ).closest( '.fusionredux-repeater-accordion-repeater' ).find( '.fusionredux-repeater-header' ).text( value );
                            }
                        );
                        if ( fusionredux.repeater[gid].limit > 0 && count >= fusionredux.repeater[gid].limit ) {
                            $( this ).addClass( 'button-disabled' );
                        }

                    }
                );
            }
        );
    };
})( jQuery );
