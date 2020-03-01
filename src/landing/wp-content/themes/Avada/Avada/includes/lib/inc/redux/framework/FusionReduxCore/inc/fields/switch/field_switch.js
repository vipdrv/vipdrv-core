/*global fusionredux_change, fusionredux*/

/**
 * Switch
 * Dependencies        : jquery
 * Feature added by    : Smartik - http://smartik.ws/
 * Date            : 03.17.2013
 */

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.switch = fusionredux.field_objects.switch || {};

    $( document ).ready(
        function() {
            //fusionredux.field_objects.switch.init();
        }
    );

    fusionredux.field_objects.switch.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-switch:visible' );
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
                el.find( ".cb-enable" ).click(
                    function() {
                        if ( $( this ).hasClass( 'selected' ) ) {
                            return;
                        }

                        var parent = $( this ).parents( '.switch-options' );

                        $( '.cb-disable', parent ).removeClass( 'selected' );
                        $( this ).addClass( 'selected' );
                        $( '.checkbox-input', parent ).val( 1 ).trigger('change');

                        fusionredux_change( $( '.checkbox-input', parent ) );

                        //fold/unfold related options
                        var obj = $( this );
                        var $fold = '.f_' + obj.data( 'id' );

                        el.find( $fold ).slideDown( 'normal', "swing" );
                    }
                );

                el.find( ".cb-disable" ).click(
                    function() {
                        if ( $( this ).hasClass( 'selected' ) ) {
                            return;
                        }

                        var parent = $( this ).parents( '.switch-options' );

                        $( '.cb-enable', parent ).removeClass( 'selected' );
                        $( this ).addClass( 'selected' );
                        $( '.checkbox-input', parent ).val( 0 ).trigger('change');

                        fusionredux_change( $( '.checkbox-input', parent ) );

                        //fold/unfold related options
                        var obj = $( this );
                        var $fold = '.f_' + obj.data( 'id' );

                        el.find( $fold ).slideUp( 'normal', "swing" );
                    }
                );

                el.find( '.cb-enable span, .cb-disable span' ).find().attr( 'unselectable', 'on' );
            }
        );
    };
})( jQuery );
