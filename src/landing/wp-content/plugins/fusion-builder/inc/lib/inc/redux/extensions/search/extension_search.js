jQuery(function($) {
    $(document).ready(function() {

        $('.fusionredux-container').each( function() {
            if ( ! $(this).hasClass('fusionredux-no-sections') ) {
                $(this).find('.fusionredux-main').prepend('<span class="dashicons dashicons-search"></span><input class="fusionredux_field_search" name="" type="text" placeholder="' + fusionreduxsearch + '"/>');
            }
        } );

        $( '.fusionredux_field_search' ).keypress( function (evt) {
            //Deterime where our character code is coming from within the event
            var charCode = evt.charCode || evt.keyCode;
            if (charCode  == 13) { //Enter key's keycode
                return false;
            }
        } );

        var
        fusionredux_container = $('.fusionredux-container'),
        fusionredux_option_tab_extras = fusionredux_container.find('.fusionredux-section-field, .fusionredux-info-field, .fusionredux-notice-field, .fusionredux-container-group, .fusionredux-section-desc, .fusionredux-group-tab h3, .fusionredux-accordion-field'),
        search_targets = fusionredux_container.find( '.form-table tr, .fusionredux-group-tab'),
        fusionredux_menu_items = $('.fusionredux-group-menu .fusionredux-group-tab-link-li');

        jQuery('.fusionredux_field_search').typeWatch({

            callback:function( searchString ){
                searchString = searchString.toLowerCase();
                var searchArray = searchString.split(',');

                if ( searchString !== '' && searchString !== null && typeof searchString !== 'undefined' && searchString.length > 2 ) {
                    // Add a class to the fusionredux container
                    $('.fusionredux-container').addClass('fusion-redux-search');
                    // Show accordion content / options
                    setTimeout( function(){
                    	$('.fusionredux-accordian-wrap').show();
                    }, 50 );

                    // Hide option fields and tabs
                    fusionredux_option_tab_extras.hide();
                    search_targets.hide();

                    // Show matching results
                    search_targets.filter( function () {
                        var
                        item = $(this),
                        matchFound = true,
                        text = item.find('.fusionredux_field_th').text().toLowerCase();

                        if ( ! text || text == '' ) {
                            return false;
                        }

                        $.each( searchArray, function ( i, searchStr ) {
                            if ( text.indexOf( searchStr ) == -1 ) {
                                matchFound = false;
                            }
                        });

                        if ( matchFound ) {
                            item.show();
                        }

                        return matchFound;

                    } ).show();

                    // Initialize option fields
                    $.fusionredux.initFields();

                } else {
                    // remove the search class from .fusionredux-container if it exists
                    $('.fusionredux-container').removeClass('fusion-redux-search');

                    // Get active options tab id
                    var tab = $.cookie( 'fusionredux_current_tab' );

                    // Show the last tab that was active before the search
                    $('.fusionredux-group-tab').hide();
                    $('.fusionredux-accordian-wrap').hide();
                    fusionredux_option_tab_extras.show();
                    $('.form-table tr').show();
                    $('.form-table tr.hide').hide();
                    $('.fusionredux-notice-field.hide').hide();
                    $( '#' + tab + '_section_group' ).show();

                }

            },

            wait: 800,
            highlight: false,
            captureLength: 0,

        } );

    } );

} );
