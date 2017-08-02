/*global jQuery, document, fusionredux*/

(function( $ ) {
    "use strict";

    fusionredux.field_objects = fusionredux.field_objects || {};
    fusionredux.field_objects.import_export = fusionredux.field_objects.import_export || {};

    fusionredux.field_objects.import_export.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-import_export:visible' );
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
                el.each(
                    function() {
                        $( '#fusionredux-import' ).click(
                            function( e ) {
                                if ( $( '#import-code-value' ).val() === "" && $( '#import-link-value' ).val() === "" ) {
                                    e.preventDefault();
                                    return false;
                                }
                                window.onbeforeunload = null;
                                fusionredux.args.ajax_save = false;
                            }
                        );

                        $( this ).find( '#fusionredux-import-code-button' ).click(
                            function() {
                                var $el = $( '#fusionredux-import-code-wrapper' );
                                if ( $( '#fusionredux-import-link-wrapper' ).is( ':visible' ) ) {
                                    $( '#import-link-value' ).text( '' );
                                    $( '#fusionredux-import-link-wrapper' ).slideUp(
                                        'fast', function() {
                                            $el.slideDown(
                                                'fast', function() {
                                                    $( '#import-code-value' ).focus();
                                                }
                                            );
                                        }
                                    );
                                } else {
                                    if ( $el.is( ':visible' ) ) {
                                        $el.slideUp();
                                    } else {
                                        $el.slideDown(
                                            'medium', function() {
                                                $( '#import-code-value' ).focus();
                                            }
                                        );
                                    }
                                }
                            }
                        );

                        $( this ).find( '#fusionredux-import-link-button' ).click(
                            function() {
                                var $el = $( '#fusionredux-import-link-wrapper' );
                                if ( $( '#fusionredux-import-code-wrapper' ).is( ':visible' ) ) {
                                    $( '#import-code-value' ).text( '' );
                                    $( '#fusionredux-import-code-wrapper' ).slideUp(
                                        'fast', function() {
                                            $el.slideDown(
                                                'fast', function() {
                                                    $( '#import-link-value' ).focus();
                                                }
                                            );
                                        }
                                    );
                                } else {
                                    if ( $el.is( ':visible' ) ) {
                                        $el.slideUp();
                                    } else {
                                        $el.slideDown(
                                            'medium', function() {
                                                $( '#import-link-value' ).focus();
                                            }
                                        );
                                    }
                                }
                            }
                        );

                        $( this ).find( '#fusionredux-export-code-copy' ).click(
                            function() {
                                var $el = $( '#fusionredux-export-code' );
                                if ( $( '#fusionredux-export-link-value' ).is( ':visible' ) ) {
                                    $( '#fusionredux-export-link-value' ).slideUp(
                                        'fast', function() {
                                            $el.slideDown(
                                                'medium', function() {
                                                    var options = fusionredux.options;
                                                    options['fusionredux-backup'] = 1;
                                                    $( this ).text( JSON.stringify( options ) ).focus().select();
                                                }
                                            );
                                        }
                                    );
                                } else {
                                    if ( $el.is( ':visible' ) ) {
                                        $el.slideUp().text( '' );
                                    } else {
                                        $el.slideDown(
                                            'medium', function() {
                                                var options = fusionredux.options;
                                                options['fusionredux-backup'] = 1;
                                                $( this ).text( JSON.stringify( options ) ).focus().select();
                                            }
                                        );
                                    }
                                }
                            }
                        );

                        $( this ).find( 'textarea' ).focusout(
                            function() {
                                var $id = $( this ).attr( 'id' );
                                var $el = $( this );
                                var $container = $el;
                                if ( $id == "import-link-value" || $id == "import-code-value" ) {
                                    $container = $( this ).parent();
                                }
                                $container.slideUp(
                                    'medium', function() {
                                        if ( $id != "fusionredux-export-link-value" ) {
                                            $el.text( '' );
                                        }
                                    }
                                );
                            }
                        );


                        $( this ).find( '#fusionredux-export-link' ).click(
                            function() {
                                var $el = $( '#fusionredux-export-link-value' );
                                if ( $( '#fusionredux-export-code' ).is( ':visible' ) ) {
                                    $( '#fusionredux-export-code' ).slideUp(
                                        'fast', function() {
                                            $el.slideDown().focus().select();
                                        }
                                    );
                                } else {
                                    if ( $el.is( ':visible' ) ) {
                                        $el.slideUp();
                                    } else {
                                        $el.slideDown(
                                            'medium', function() {
                                                $( this ).focus().select();
                                            }
                                        );
                                    }

                                }
                            }
                        );

                        var textBox1 = document.getElementById( "fusionredux-export-code" );
                        textBox1.onfocus = function() {
                            textBox1.select();
                            // Work around Chrome's little problem
                            textBox1.onmouseup = function() {
                                // Prevent further mouseup intervention
                                textBox1.onmouseup = null;
                                return false;
                            };
                        };
                        var textBox2 = document.getElementById( "import-code-value" );
                        textBox2.onfocus = function() {
                            textBox2.select();
                            // Work around Chrome's little problem
                            textBox2.onmouseup = function() {
                                // Prevent further mouseup intervention
                                textBox2.onmouseup = null;
                                return false;
                            };
                        };

                    }
                );
            }
        );
    };
})( jQuery );


