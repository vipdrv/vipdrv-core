/*global fusionredux_change, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.select = fusionredux.field_objects.select || {};

    fusionredux.field_objects.select.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( '.fusionredux-container-select:visible' );
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

                el.find( 'select.fusionredux-select-item' ).each(
                    function() {

                        var default_params = {
                            width: 'resolve',
                            triggerChange: true,
                            allowClear: true
                        };
                        if ( $(this).attr('multiple') == "multiple" ) {
                            default_params.width = "100%";
                        }

                        if ( $( this ).siblings( '.select3_params' ).size() > 0 ) {
                            var select3_params = $( this ).siblings( '.select3_params' ).val();
                            select3_params = JSON.parse( select3_params );
                            default_params = $.extend( {}, default_params, select3_params );
                        }

                        if ( $( this ).hasClass( 'font-icons' ) ) {
                            default_params = $.extend(
                                {}, {
                                    formatResult: fusionredux.field_objects.select.addIcon,
                                    formatSelection: fusionredux.field_objects.select.addIcon,
                                    escapeMarkup: function( m ) {
                                        return m;
                                    }
                                }, default_params
                            );
                        }

                        $( this ).select3( default_params );

                        if ( $( this ).hasClass( 'select3-sortable' ) ) {
                            default_params = {};
                            default_params.bindOrder = 'sortableStop';
                            default_params.sortableOptions = {placeholder: 'ui-state-highlight'};
                            $( this ).select3Sortable( default_params );
                        }

                        $( this ).on(
                            "change", function() {
                                fusionredux_change( $( $( this ) ) );
                                $( this ).select3SortableOrder();
                            }
                        );
                    }
                );
            }
        );
    };

    fusionredux.field_objects.select.addIcon = function( icon ) {
        if ( icon.hasOwnProperty( 'id' ) ) {
            return "<span class='elusive'><i class='" + icon.id + "'></i>" + "&nbsp;&nbsp;" + icon.text + "</span>";
        }
    };
})( jQuery );
