/**
 * FusionRedux Background
 * Dependencies        : jquery, wp media uploader
 * Feature added by    : Dovy Paukstys
 * Date                : 07 Jan 2014
 */

/*global fusionredux_change, wp, fusionredux*/

(function( $ ) {
	"use strict";

	fusionredux.field_objects = fusionredux.field_objects || {};
	fusionredux.field_objects.background = fusionredux.field_objects.background || {};

	fusionredux.field_objects.background.init = function( selector ) {
		if ( !selector ) {
			selector = $( document ).find( ".fusionredux-group-tab:visible" ).find( '.fusionredux-container-background:visible' );
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
				// Remove the image button
				el.find( '.fusionredux-remove-background' ).unbind( 'click' ).on(
					'click', function( e ) {
						e.preventDefault();
						fusionredux.field_objects.background.removeImage( $( this ).parents( '.fusionredux-container-background:first' ) );
						return false;
					}
				);

				// Upload media button
				el.find( '.fusionredux-background-upload' ).unbind().on(
					'click', function( event ) {
						fusionredux.field_objects.background.addImage(
							event, $( this ).parents( '.fusionredux-container-background:first' )
						);
					}
				);

				el.find( '.fusionredux-background-input' ).on(
					'change', function() {
						fusionredux.field_objects.background.preview( $( this ) );
					}
				);

				el.find( '.fusionredux-color' ).wpColorPicker(
					{
						change: function( e, ui ) {
							$( this ).val( ui.color.toString() );
							fusionredux_change( $( this ) );
							$( '#' + e.target.id + '-transparency' ).removeAttr( 'checked' );
							fusionredux.field_objects.background.preview( $( this ) );
						},

						clear: function( e, ui ) {
							$( this ).val( ui.color.toString() );
							fusionredux_change( $( this ).parent().find( '.fusionredux-color-init' ) );
							fusionredux.field_objects.background.preview( $( this ) );
						}
					}
				);

				// Replace and validate field on blur
				el.find( '.fusionredux-color' ).on(
					'blur', function() {
						var value = $( this ).val();
						var id = '#' + $( this ).attr( 'id' );

						if ( value === "transparent" ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css(
								'background-color', 'transparent'
							);

							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							if ( colorValidate( this ) === value ) {
								if ( value.indexOf( "#" ) !== 0 ) {
									$( this ).val( $( this ).data( 'oldcolor' ) );
								}
							}

							el.find( id + '-transparency' ).removeAttr( 'checked' );
						}
					}
				);

				el.find( '.fusionredux-color' ).on(
					'focus', function() {
						$( this ).data( 'oldcolor', $( this ).val() );
					}
				);

				el.find( '.fusionredux-color' ).on(
					'keyup', function() {
						var value = $( this ).val();
						var color = colorValidate( this );
						var id = '#' + $( this ).attr( 'id' );

						if ( value === "transparent" ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css(
								'background-color', 'transparent'
							);
							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							el.find( id + '-transparency' ).removeAttr( 'checked' );

							if ( color && color !== $( this ).val() ) {
								$( this ).val( color );
							}
						}
					}
				);

				// When transparency checkbox is clicked
				el.find( '.color-transparency' ).on(
					'click', function() {
						if ( $( this ).is( ":checked" ) ) {
							el.find( '.fusionredux-saved-color' ).val( $( '#' + $( this ).data( 'id' ) ).val() );
							el.find( '#' + $( this ).data( 'id' ) ).val( 'transparent' );
							el.find( '#' + $( this ).data( 'id' ) ).parent().parent().find( '.wp-color-result' ).css(
								'background-color', 'transparent'
							);
						} else {
							if ( el.find( '#' + $( this ).data( 'id' ) ).val() === 'transparent' ) {
								var prevColor = $( '.fusionredux-saved-color' ).val();

								if ( prevColor === '' ) {
									prevColor = $( '#' + $( this ).data( 'id' ) ).data( 'default-color' );
								}

								el.find( '#' + $( this ).data( 'id' ) ).parent().parent().find( '.wp-color-result' ).css(
									'background-color', prevColor
								);
								el.find( '#' + $( this ).data( 'id' ) ).val( prevColor );
							}
						}
						fusionredux.field_objects.background.preview( $( this ) );
						fusionredux_change( $( this ) );
					}
				);

				var default_params = {
					width: 'resolve',
					triggerChange: true,
					allowClear: true
				};

				var select3_handle = el.find( '.select3_params' );
				if ( select3_handle.size() > 0 ) {
					var select3_params = select3_handle.val();

					select3_params = JSON.parse( select3_params );
					default_params = $.extend( {}, default_params, select3_params );
				}

				el.find( " .fusionredux-background-repeat, .fusionredux-background-clip, .fusionredux-background-origin, .fusionredux-background-size, .fusionredux-background-attachment, .fusionredux-background-position" ).select3( default_params );

			}
		);
	};

	// Update the background preview
	fusionredux.field_objects.background.preview = function( selector ) {
		var parent = $( selector ).parents( '.fusionredux-container-background:first' );
		var preview = $( parent ).find( '.background-preview' );

		if ( !preview ) { // No preview present
			return;
		}
		var hide = true;

		var css = 'height:' + preview.height() + 'px;';
		$( parent ).find( '.fusionredux-background-input' ).each(
			function() {
				var data = $( this ).serializeArray();
				data = data[0];
				if ( data && data.name.indexOf( '[background-' ) != -1 ) {
					if ( data.value !== "" ) {
						hide = false;
						data.name = data.name.split( '[background-' );
						data.name = 'background-' + data.name[1].replace( ']', '' );
						if ( data.name == "background-image" ) {
							css += data.name + ':url("' + data.value + '");';
						} else {
							css += data.name + ':' + data.value + ';';
						}
					}
				}
			}
		);
		if ( !hide ) {
			preview.attr( 'style', css ).fadeIn();
		} else {
			preview.slideUp();
		}


	};

	// Add a file via the wp.media function
	fusionredux.field_objects.background.addImage = function( event, selector ) {
		event.preventDefault();

		var frame;
		var jQueryel = $( this );

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media(
			{
				multiple: false,
				library: {
					//type: 'image' //Only allow images
				},
				// Set the title of the modal.
				title: jQueryel.data( 'choose' ),
				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: jQueryel.data( 'update' )
					// Tell the button not to close the modal, since we're
					// going to refresh the page when the image is selected.

				}
			}
		);

		// When an image is selected, run a callback.
		frame.on(
			'select', function() {
				// Grab the selected attachment.
				var attachment = frame.state().get( 'selection' ).first();
				frame.close();

				//console.log(attachment.attributes.type);

				if ( attachment.attributes.type !== "image" ) {
					return;
				}

				selector.find( '.upload' ).val( attachment.attributes.url );
				selector.find( '.upload-id' ).val( attachment.attributes.id );
				selector.find( '.upload-height' ).val( attachment.attributes.height );
				selector.find( '.upload-width' ).val( attachment.attributes.width );
				fusionredux_change( $( selector ).find( '.upload-id' ) );
				var thumbSrc = attachment.attributes.url;
				if ( typeof attachment.attributes.sizes !== 'undefined' && typeof attachment.attributes.sizes.thumbnail !== 'undefined' ) {
					thumbSrc = attachment.attributes.sizes.thumbnail.url;
				} else if ( typeof attachment.attributes.sizes !== 'undefined' ) {
					var height = attachment.attributes.height;
					for ( var key in attachment.attributes.sizes ) {
						var object = attachment.attributes.sizes[key];
						if ( object.height < height ) {
							height = object.height;
							thumbSrc = object.url;
						}
					}
				} else {
					thumbSrc = attachment.attributes.icon;
				}
				selector.find( '.upload-thumbnail' ).val( thumbSrc );
				if ( !selector.find( '.upload' ).hasClass( 'noPreview' ) ) {
					selector.find( '.screenshot' ).empty().hide().append( '<img class="fusionredux-option-image" src="' + thumbSrc + '">' ).slideDown( 'fast' );
				}

				selector.find( '.fusionredux-remove-background' ).removeClass( 'hide' );//show "Remove" button
				selector.find( '.fusionredux-background-input-properties' ).slideDown();
				fusionredux.field_objects.background.preview( selector.find( '.upload' ) );
			}
		);

		// Finally, open the modal.
		frame.open();
	};

	// Update the background preview
	fusionredux.field_objects.background.removeImage = function( selector ) {

		// This shouldn't have been run...
		if ( !selector.find( '.fusionredux-remove-background' ).addClass( 'hide' ) ) {
			return;
		}
		selector.find( '.fusionredux-remove-background' ).addClass( 'hide' ); //hide "Remove" button

		selector.find( '.upload' ).val( '' );
		selector.find( '.upload-id' ).val( '' );
		selector.find( '.upload-height' ).val( '' );
		selector.find( '.upload-width' ).val( '' );
		fusionredux_change( $( selector ).find( '.upload-id' ) );
		selector.find( '.fusionredux-background-input-properties' ).hide();
		var screenshot = selector.find( '.screenshot' );

		// Hide the screenshot
		screenshot.slideUp();

		selector.find( '.remove-file' ).unbind();
		// We don't display the upload button if .upload-notice is present
		// This means the user doesn't have the WordPress 3.5 Media Library Support
		if ( $( '.section-upload .upload-notice' ).length > 0 ) {
			$( '.fusionredux-background-upload' ).remove();
		}
	};
})( jQuery );
