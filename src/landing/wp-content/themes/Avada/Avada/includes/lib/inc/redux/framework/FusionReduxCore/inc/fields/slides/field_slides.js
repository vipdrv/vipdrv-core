/*global fusionredux_change, wp, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.slides = fusionredux.field_objects.slides || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.slides.init();
        }
    );

    fusionredux.field_objects.slides.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-slides:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );

                fusionredux.field_objects.media.init(el);

                var parent = el;
                if ( !el.hasClass( 'fusionredux-field-container' ) ) {
                    parent = el.parents( '.fusionredux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }

                if ( parent.hasClass( 'fusionredux-container-slides' ) ) {
                    parent.addClass( 'fusionredux-field-init' );
                }

                if ( parent.hasClass( 'fusionredux-field-init' ) ) {
                    parent.removeClass( 'fusionredux-field-init' );
                } else {
                    return;
                }

                el.find( '.fusionredux-slides-remove' ).live(
                    'click', function() {
                        fusionredux_change( $( this ) );

                        $( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
                        $( this ).parent().siblings().find( 'textarea' ).val( '' );
                        $( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );

                        var slideCount = $( this ).parents( '.fusionredux-container-slides:first' ).find( '.fusionredux-slides-accordion-group' ).length;

                        if ( slideCount > 1 ) {
                            $( this ).parents( '.fusionredux-slides-accordion-group:first' ).slideUp(
                                'medium', function() {
                                    $( this ).remove();
                                }
                            );
                        } else {
                            var content_new_title = $( this ).parent( '.fusionredux-slides-accordion' ).data( 'new-content-title' );

                            $( this ).parents( '.fusionredux-slides-accordion-group:first' ).find( '.remove-image' ).click();
                            $( this ).parents( '.fusionredux-container-slides:first' ).find( '.fusionredux-slides-accordion-group:last' ).find( '.fusionredux-slides-header' ).text( content_new_title );
                        }
                    }
                );

                //el.find( '.fusionredux-slides-add' ).click(
                el.find( '.fusionredux-slides-add' ).off('click').click(
                    function() {
                        var newSlide = $( this ).prev().find( '.fusionredux-slides-accordion-group:last' ).clone( true );

                        var slideCount = $( newSlide ).find( '.slide-title' ).attr( "name" ).match( /[0-9]+(?!.*[0-9])/ );
                        var slideCount1 = slideCount * 1 + 1;

                        $( newSlide ).find( 'input[type="text"], input[type="hidden"], textarea' ).each(
                            function() {

                                $( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );
                                $( this ).val( '' );
                                if ( $( this ).hasClass( 'slide-sort' ) ) {
                                    $( this ).val( slideCount1 );
                                }
                            }
                        );

                        var content_new_title = $( this ).prev().data( 'new-content-title' );

                        $( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
                        $( newSlide ).find( '.screenshot' ).addClass( 'hide' );
                        $( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
                        $( newSlide ).find( '.remove-image' ).addClass( 'hide' );
                        $( newSlide ).find( '.fusionredux-slides-image' ).attr( 'src', '' ).removeAttr( 'id' );
                        $( newSlide ).find( 'h3' ).text( '' ).append( '<span class="fusionredux-slides-header">' + content_new_title + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
                        $( this ).prev().append( newSlide );
                    }
                );

                el.find( '.slide-title' ).keyup(
                    function( event ) {
                        var newTitle = event.target.value;
                        $( this ).parents().eq( 3 ).find( '.fusionredux-slides-header' ).text( newTitle );
                    }
                );


                el.find( ".fusionredux-slides-accordion" )
                    .accordion(
                    {
                        header: "> div > fieldset > h3",
                        collapsible: true,
                        active: false,
                        heightStyle: "content",
                        icons: {
                            "header": "ui-icon-plus",
                            "activeHeader": "ui-icon-minus"
                        }
                    }
                )
                    .sortable(
                    {
                        axis: "y",
                        handle: "h3",
                        connectWith: ".fusionredux-slides-accordion",
                        start: function( e, ui ) {
                            ui.placeholder.height( ui.item.height() );
                            ui.placeholder.width( ui.item.width() );
                        },
                        placeholder: "ui-state-highlight",
                        stop: function( event, ui ) {
                            // IE doesn't register the blur when sorting
                            // so trigger focusout handlers to remove .ui-state-focus
                            ui.item.children( "h3" ).triggerHandler( "focusout" );
                            var inputs = $( 'input.slide-sort' );
                            inputs.each(
                                function( idx ) {
                                    $( this ).val( idx );
                                }
                            );
                        }
                    }
                );
            }
        );
    };
})( jQuery );
