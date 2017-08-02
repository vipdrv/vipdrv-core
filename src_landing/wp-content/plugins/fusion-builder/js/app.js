var FusionPageBuilder = FusionPageBuilder || {};

// Events
var FusionPageBuilderEvents = _.extend( {}, Backbone.Events );

( function( $ ) {

	var FusionIconPickHandler,
		FusionDelay;

	$.fn.outerHTML = function() {
		return ( ! this.length ) ? this : ( this[0].outerHTML || ( function( el ) {
			var div = document.createElement( 'div' ),
			    contents;

			div.appendChild( el.cloneNode( true ) );
			contents = div.innerHTML;
			div = null;
			return contents;
		})( this[0] ) );
	};

	fusionBuilderGetContent = function( textareaID, removeAutoP, initialLoad ) {

		var content;

		if ( 'undefined' === typeof removeAutoP ) {
			removeAutoP = false;
		}

		if ( 'undefined' === typeof initialLoad ) {
			initialLoad = false;
		}

		if ( ! initialLoad && 'undefined' !== typeof window.tinyMCE && window.tinyMCE.get( textareaID ) && ! window.tinyMCE.get( textareaID ).isHidden() ) {
			content = window.tinyMCE.get( textareaID ).getContent();
		} else {
			content = $( '#' + textareaID ).val().replace( /\r?\n/g, '\r\n' );
		}

		// Remove auto p tags from content.
		if ( removeAutoP && 'undefined' !== typeof window.tinyMCE ) {
			content = content.replace( /<p>\[/g, '[' );
			content = content.replace( /\]<\/p>/g, ']' );
		}

		if ( 'undefined' !== typeof content ) {
			return content.trim();
		}
	};

	// Icon picker handler
	FusionIconPickHandler = $( '.icon_select_container .icon_preview' );

	// Delay
	FusionDelay = ( function() {
		var timer = 0;

		return function( callback, ms ) {
			clearTimeout( timer );
			timer = setTimeout( callback, ms );
		};
	})();

	$( window ).load( function() {
		if ( $( '#fusion_toggle_builder' ).data( 'enabled' ) ) {
			$( '#fusion_toggle_builder' ).trigger( 'click' );
		}
	});

	$( document ).ready( function() {

		var WooShortcodeHanlder,
		    $selectedDemo,
		    $useBuilderMetaField,
		    $toggleBuilderButton,
		    $builder,
		    $mainEditorWrapper,
		    $container,
		    LightboxShortcodeHandler;

		// Column sizes dialog. Close on outside click.
		$( document ).click( function( e ) {
			if ( $( e.target ).parents( '.column-sizes' ).length || $( e.target ).hasClass( 'fusion-builder-resize-column' ) ) {

				// Column sizes dialog clicked
			} else {
				$( '.column-sizes' ).hide();
			}
		} );

		// Fusion Builder App View
		FusionPageBuilder.AppView = window.wp.Backbone.View.extend( {

			el: $( '#fusion_builder_main_container' ),

			template: FusionPageBuilder.template( $( '#fusion-builder-app-template' ).html() ),

			events: {
				'click .fusion-builder-layout-button-save': 'saveLayout',
				'click .fusion-builder-layout-button-load': 'loadLayout',
				'click .fusion-builder-layout-button-delete': 'deleteLayout',
				'click .fusion-builder-layout-buttons-clear': 'clearLayout',
				'click .fusion-builder-demo-button-load': 'loadDemoPage',
				'click .fusion-builder-layout-custom-css': 'customCSS',
				'click .fusion-builder-template-buttons-save': 'saveTemplateDialog',
				'click #fusion-builder-layouts .fusion-builder-modal-close': 'hideLibrary',
				'click .fusion-builder-library-dialog': 'openLibrary',
				'mouseenter .fusion-builder-layout-buttons-history': 'showHistoryDialog',
				'mouseleave .fusion-builder-layout-buttons-history': 'hideHistoryDialog',
				'click .fusion-builder-element-button-save': 'saveElement',
				'click #fusion-load-template-dialog': 'loadPreBuiltPage',
				'click .fusion-builder-layout-buttons-toggle-containers': 'toggleAllContainers'
			},

			initialize: function() {

				this.builderActive             = false;
				this.ajaxurl                   = fusionBuilderConfig.ajaxurl;
				this.fusion_load_nonce         = fusionBuilderConfig.fusion_load_nonce;
				this.fusion_builder_plugin_dir = fusionBuilderConfig.fusion_builder_plugin_dir;
				this.layoutIsLoading           = false;
				this.layoutIsSaving            = false;
				this.layoutIsDeleting          = false;
				this.parentRowId               = '';
				this.parentColumnId            = '';
				this.targetContainerCID        = '';
				this.activeModal               = '';
				this.innerColumn               = '';
				this.blankPage                 = '';
				this.newLayoutLoaded           = false;
				this.newContainerAdded         = false;
				this.fullWidth                 = fusionBuilderConfig.full_width;

				// Shortcode Generator
				this.shortcodeGenerator                  = '';
				this.shortcodeGeneratorMultiElement      = '';
				this.shortcodeGeneratorMultiElementChild = '';
				this.allowShortcodeGenerator             = '';
				this.shortcodeGeneratorActiveEditor      = '';
				this.shortcodeGeneratorEditorID          = '';
				this.manuallyAdded                       = false;
				this.manualGenerator                     = false;
				this.manualEditor                        = '';
				this.fromExcerpt                         = false;

				// Code Block encoding
				this.disable_encoding = fusionBuilderConfig.disable_encoding;
				this._keyStr          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
				this.codeEditor       = '';

				this.MultiElementChildSettings = false;

				// Listen for new elements
				this.listenTo( this.collection, 'add', this.addBuilderElement );

				// Convert builder layout to shortcodes
				this.listenTo( FusionPageBuilderEvents, 'fusion-element-added', this.builderToShortcodes );
				this.listenTo( FusionPageBuilderEvents, 'fusion-element-removed', this.builderToShortcodes );
				this.listenTo( FusionPageBuilderEvents, 'fusion-element-cloned', this.builderToShortcodes );
				this.listenTo( FusionPageBuilderEvents, 'fusion-element-edited', this.builderToShortcodes );
				this.listenTo( FusionPageBuilderEvents, 'fusion-element-sorted', this.builderToShortcodes );

				// Loader animation
				this.listenTo( FusionPageBuilderEvents, 'fusion-show-loader', this.showLoader );
				this.listenTo( FusionPageBuilderEvents, 'fusion-hide-loader', this.hideLoader );

				// Hide library
				this.listenTo( FusionPageBuilderEvents, 'fusion-hide-library', this.hideLibrary );

				// Save layout template on return key
				this.listenTo( FusionPageBuilderEvents, 'fusion-save-layout', this.saveLayout );

				// Save history state
				this.listenTo( FusionPageBuilderEvents, 'fusion-save-history-state', this.saveHistoryState );

				// Toggled Containers
				this.toggledContainers = true;

				this.render();

				if ( $( '#fusion_toggle_builder' ).hasClass( 'fusion_builder_is_active' ) ) {

					// Create builder layout on initial load.
					this.initialBuilderLayout( true );
				}

				// Turn on history tracking. Capture editor. Save initial history state.
				fusionHistoryManager.turnOnTracking();
				fusionHistoryManager.captureEditor();
				fusionHistoryManager.turnOffTracking();
			},

			render: function() {
				this.$el.html( this.template() );
				this.sortableContainers();

				return this;
			},

			isTinyMceActive: function() {
				var isActive = ( 'undefined' !== typeof tinyMCE ) && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden();

				return isActive;
			},

			base64Encode: function( data ) {
				var b64 = this._keyStr,
				    o1,
				    o2,
				    o3,
				    h1,
				    h2,
				    h3,
				    h4,
				    bits,
				    i      = 0,
				    ac     = 0,
				    enc    = '',
				    tmpArr = [],
				    r;

				if ( ! data ) {
					return data;
				}

				data = unescape( encodeURIComponent( data ) );

				do {

					// Pack three octets into four hexets
					o1 = data.charCodeAt( i++ );
					o2 = data.charCodeAt( i++ );
					o3 = data.charCodeAt( i++ );

					bits = o1 << 16 | o2 << 8 | o3;

					h1 = bits >> 18 & 0x3f;
					h2 = bits >> 12 & 0x3f;
					h3 = bits >> 6 & 0x3f;
					h4 = bits & 0x3f;

					// Use hexets to index into b64, and append result to encoded string.
					tmpArr[ ac++ ] = b64.charAt( h1 ) + b64.charAt( h2 ) + b64.charAt( h3 ) + b64.charAt( h4 );
				} while ( i < data.length );

				enc = tmpArr.join( '' );
				r   = data.length % 3;

				return ( r ? enc.slice( 0, r - 3 ) : enc ) + '==='.slice( r || 3 );
			},

			base64Decode: function( input ) {
				var output = '',
				    chr1,
				    chr2,
				    chr3,
				    enc1,
				    enc2,
				    enc3,
				    enc4,
				    i = 0;

				input = input.replace( /[^A-Za-z0-9\+\/\=]/g, '' );

				while ( i < input.length ) {

					enc1 = this._keyStr.indexOf( input.charAt( i++ ) );
					enc2 = this._keyStr.indexOf( input.charAt( i++ ) );
					enc3 = this._keyStr.indexOf( input.charAt( i++ ) );
					enc4 = this._keyStr.indexOf( input.charAt( i++ ) );

					chr1 = ( enc1 << 2 ) | ( enc2 >> 4 );
					chr2 = ( ( enc2 & 15 ) << 4 ) | ( enc3 >> 2 );
					chr3 = ( ( enc3 & 3 ) << 6 ) | enc4;

					output = output + String.fromCharCode( chr1 );

					if ( 64 != enc3 ) {
						output = output + String.fromCharCode( chr2 );
					}
					if ( 64 != enc4 ) {
						output = output + String.fromCharCode( chr3 );
					}

				}

				output = this.utf8Decode( output );

				return output;
			},

			utf8Decode: function( utftext ) {
				var string = '',
				    i  = 0,
				    c  = 0,
				    c1 = 0,
				    c2 = 0,
				    c3;

				while ( i < utftext.length ) {

					c = utftext.charCodeAt( i );

					if ( c < 128 ) {
						string += String.fromCharCode( c );
						i++;
					} else if ( ( c > 191 ) && ( c < 224 ) ) {
						c2 = utftext.charCodeAt( i + 1 );
						string += String.fromCharCode( ( ( c & 31 ) << 6 ) | ( c2 & 63 ) );
						i += 2;
					} else {
						c2 = utftext.charCodeAt( i + 1 );
						c3 = utftext.charCodeAt( i + 2 );
						string += String.fromCharCode( ( ( c & 15 ) << 12 ) | ( ( c2 & 63 ) << 6 ) | ( c3 & 63 ) );
						i += 3;
					}

				}

				return string;
			},

			fusionBuilderMCEremoveEditor: function( id ) {
				if ( 'undefined' !== typeof window.tinyMCE ) {
					window.tinyMCE.execCommand( 'mceRemoveEditor', false, id );
					if ( 'undefined' !== typeof window.tinyMCE.get( id ) ) {
						window.tinyMCE.remove( '#' + id );
					}
				}
			},

			fusion_builder_iconpicker: function( value, id, container, search ) {

				var icons  = fusionBuilderConfig.fontawesomeicons,
				    output = '';

				_.each( icons, function( icon ) {

					var selectedClass = '';

					if ( value == icon ) {
						selectedClass = 'selected-element';
					}

					output += '<span class="icon_preview icon-' + icon + ' ' + selectedClass + '"><i class="fa ' + icon + '" data-name="' + icon + '"></i></span>';
				} );

				$( container ).append( output );

				// Icon Search bar
				$( search ).on( 'change paste keyup', function() {
					var thisEl = $( this );

					FusionDelay( function() {
						if ( thisEl.val() ) {
							value = thisEl.val().toLowerCase();

							_.each( icons, function( icon ) {
								name = icon.toLowerCase();

								if ( name.search( value ) !== -1 ) {
									$( thisEl.parent().find( '.icon_select_container .icon-' + name ) ).show();
								} else {
									$( thisEl.parent().find( '.icon_select_container .icon-' + name ) ).hide();
								}
							} );

						} else {
							$( '.icon_select_container .icon_preview' ).show();
						}
					}, 500 );
				} );
			},

			fusionBuilderImagePreview: function( $uploadButton ) {
				var $uploadField = $uploadButton.siblings( '.fusion-builder-upload-field' ),
				    $preview     = $uploadField.siblings( '.fusion-builder-upload-preview' ),
				    $removeBtn   = $uploadButton.siblings( '.upload-image-remove' ),
				    imageURL     = $uploadField.val().trim(),
				    imagePreview;

				if ( 0 <= imageURL.indexOf( '<img' ) ) {
					imagePreview = imageURL;
				} else {
					imagePreview = '<img src="' + imageURL + '" />';
				}

				if ( 'image' !== $uploadButton.data( 'type' ) ) {
					return;
				}

				if ( $uploadButton.hasClass( 'hide-edit-buttons' ) ) {
					return;
				}

				if ( '' === imageURL ) {
					if ( $preview.length ) {
						$preview.remove();
						$removeBtn.remove();
						$uploadButton.val( 'Upload Image' );
					}

					if ( $( '#image_id' ).length ) {
						$( '#image_id' ).val( '' );
					}

					return;
				}

				if ( ! $preview.length ) {
					$uploadButton.siblings( '.preview' ).before( '<div class="fusion-builder-upload-preview">' + '<strong class="fusion-builder-upload-preview-title">Preview</strong>' + '<div class="fusion-builder-preview-image"><img src="" width="300" height="300" /></div></div>' );
					$uploadButton.after( '<input type="button" class="upload-image-remove" value="Remove" />' );
					$uploadButton.val( 'Edit' );
					$preview = $uploadField.siblings( '.fusion-builder-upload-preview' );

				}

				$preview.find( 'img' ).replaceWith( imagePreview );
			},

			FusionBuilderActivateUpload: function( $uploadButton ) {
				$uploadButton.click( function( event ) {

					var $thisEl,
					    fileFrame,
					    defaultParam,
					    multiImageContainer,
					    multiImageInput,
					    multiUpload    = false,
					    multiImages    = false,
					    multiImageHtml = '',
					    ids            = '',
					    attachment     = '',
					    attachments    = [];

					if ( event ) {
						event.preventDefault();
					}

					$thisEl = $( this );

					// If its a multi upload element, clone default params.
					if ( 'fusion-multiple-upload' === $thisEl.data( 'id' ) ) {
						multiUpload = true;
						defaultParam = fusionAllElements[ $thisEl.data( 'element' ) ].params[ $thisEl.data( 'param' ) ].value;
					}

					if ( 'fusion-multiple-images' === $thisEl.data( 'id' ) ) {
						multiImages = true;
						multiImageContainer = jQuery( $thisEl.next( '.fusion-multiple-image-container' ) )[0];
						multiImageInput = jQuery( $thisEl ).prev( '.fusion-multi-image-input' );
					}

					fileFrame = wp.media.frames.file_frame = wp.media({
						library: {
							type: $thisEl.data( 'type' )
						},
						title: $thisEl.data( 'title' ),
						multiple: ( multiUpload || multiImages ) ? 'between' : false,
						frame: 'post',
						className: 'media-frame mode-select fusion-builder-media-dialog ' + $thisEl.data( 'id' ),
						displayUserSettings: false,
						displaySettings: true,
						allowLocalEdits: true
					} );

					// Set the media dialog box state as 'gallery' if the element is gallery.
					if ( multiImages && 'fusion_gallery' === $thisEl.data( 'element' ) ) {
						ids         = multiImageInput.val().split( ',' );
						attachments = [];
						attachment  = '';

						jQuery.each( ids, function( index, id ) {
							if ( '' !== id && 'NaN' !== id ) {
								attachment = wp.media.attachment( id );
								attachment.fetch();
								attachments.push( attachment );
							}
						} );

						wp.media._galleryDefaults.link  = 'none';
						wp.media._galleryDefaults.size  = 'thumbnail';
						fileFrame.options.syncSelection = true;

						if ( attachments.length ) {
							fileFrame.options.state = 'gallery-edit';
						} else {
							fileFrame.options.state = 'gallery';
						}
					}

					// Select currently active image automatically.
					fileFrame.on( 'open', function() {
						var selection = fileFrame.state().get( 'selection' ),
						    library   = fileFrame.state().get( 'library' ),
						    attachment,
						    id;

						if ( multiImages ) {
							if ( 'fusion_gallery' !== $thisEl.data( 'element' ) || 'gallery-edit' !== fileFrame.options.state ) {
								$( '.fusion-builder-media-dialog' ).addClass( 'hide-menu' );
							}
							selection.add( attachments );
							library.add( attachments );
						} else {
							id = $thisEl.parents( '.fusion-builder-module-settings' ).find( '#image_id' ).val();
							attachment = wp.media.attachment( id );

							$( '.fusion-builder-media-dialog' ).addClass( 'hide-menu' );
							if ( id ) {
								attachment.fetch();
								selection.add( attachment ? [ attachment ] : [] );
							}
						}
					});

					// Set the attachment ids from gallery selection if the element is gallery.
					if ( multiImages && 'fusion_gallery' == $thisEl.data( 'element' ) ) {
						fileFrame.on( 'update', function( selection ) {
							var state    = fileFrame.state(),
							    imageIDs = '',
							    imageURL = '',
							    display  = '';

							imageIDs = selection.map( function( attachment ) {
								var imageID = attachment.id,
								    display = state.display( attachment ).toJSON();

								if ( attachment.attributes.sizes && 'undefined' !== typeof attachment.attributes.sizes.thumbnail ) {
									imageURL = attachment.attributes.sizes.thumbnail.url;
								} else if ( attachment.attributes.url ) {
									imageURL = attachment.attributes.url;
								}

								if ( multiImages ) {
									multiImageHtml += '<div class="fusion-multi-image" data-image-id="' + imageID + '">';
									multiImageHtml += '<img src="' + imageURL + '"/>';
									multiImageHtml += '<span class="fusion-multi-image-remove dashicons dashicons-no-alt"></span>';
									multiImageHtml += '</div>';
								}
								return attachment.id;
							} );

							multiImageInput.val( imageIDs );
							jQuery( multiImageContainer ).html( multiImageHtml );
						} );
					}

					fileFrame.on( 'select insert', function() {

						var imageURL,
							imageID,
							imageIDs,
							state = fileFrame.state(),
							firstElementNode,
							firstElement;

						if ( 'undefined' === typeof state.get( 'selection' ) ) {
							imageURL = jQuery( fileFrame.$el ).find( '#embed-url-field' ).val();
						} else {

							imageIDs = state.get( 'selection' ).map( function( attachment ) {
								return attachment.id;
							} );

							// If its a multi image element, add the images container and IDs to input field.
							if ( multiImages ) {
								multiImageInput.val( imageIDs );
							}

							// Remove default item.
							if ( multiUpload ) {
								firstElementNode = jQuery( $thisEl ).parents( '.fusion-builder-main-settings' ).find( '.fusion-builder-sortable-options li:first-child' );
								if ( firstElementNode.length ) {
									firstElement = FusionPageBuilderElements.find( function( model ) {
										return model.get( 'cid' ) == firstElementNode.data( 'cid' );
									} );
									if ( firstElement && undefined === firstElement.attributes.params[ $thisEl.data( 'param' ) ] ) {
										jQuery( $thisEl ).parents( '.fusion-builder-main-settings' ).find( '.fusion-builder-sortable-options li:first-child .fusion-builder-multi-setting-remove' ).trigger( 'click' );
									}
								}
							}

							state.get( 'selection' ).map( function( attachment ) {
								var element = attachment.toJSON();
								var display = state.display( attachment ).toJSON();

								imageID = element.id;
								if ( element.sizes && element.sizes[display.size] && element.sizes[display.size].url ) {
									imageURL = element.sizes[display.size].url;
								} else if ( element.url ) {
									imageURL = element.url;
								}

								if ( multiImages ) {
									multiImageHtml += '<div class="fusion-multi-image" data-image-id="' + imageID + '">';
									multiImageHtml += '<img src="' + imageURL + '"/>';
									multiImageHtml += '<span class="fusion-multi-image-remove dashicons dashicons-no-alt"></span>';
									multiImageHtml += '</div>';
								}

								// If its a multi upload element, add the image to defaults and trigger a new item to be added.
								if ( multiUpload ) {
									fusionAllElements[ $thisEl.data( 'element' ) ].params[ $thisEl.data( 'param' ) ].value = imageURL;
									jQuery( $thisEl ).parents( '.fusion-builder-main-settings' ).find( '.fusion-builder-add-multi-child' ).trigger( 'click' );
									FusionPageBuilderEvents.trigger( 'fusion-multi-child-update-preview' );
									fusionAllElements[ $thisEl.data( 'element' ) ].params[ $thisEl.data( 'param' ) ].value = defaultParam;
								}
							} );
						}

						jQuery( multiImageContainer ).html( multiImageHtml );
						if ( ! multiUpload && ! multiImages ) {
							$thisEl.siblings( '.fusion-builder-upload-field' ).val( imageURL ).trigger( 'change' );

							// Set image id.
							if ( $( '#image_id' ).length ) {
								$( '#image_id' ).val( imageID );
							}

							FusionPageBuilderApp.fusionBuilderImagePreview( $thisEl );
						}
					} );

					fileFrame.open();

					return false;
				} );

				$uploadButton.siblings( '.fusion-builder-upload-field' ).on( 'input', function() {
					FusionPageBuilderApp.fusionBuilderImagePreview( $( this ).siblings( '.fusion-builder-upload-button' ) );
				} );

				$uploadButton.siblings( '.fusion-builder-upload-field' ).each( function() {
					FusionPageBuilderApp.fusionBuilderImagePreview( $( this ).siblings( '.fusion-builder-upload-button' ) );
				} );

				jQuery( 'body' ).on( 'click', '.fusion-multi-image-remove', function( e ) {
					var input = jQuery( this ).parents( '.fusion-multiple-upload-images' ).find( '.fusion-multi-image-input' ),
					    imageIDs,
							imageID,
							imageIndex;
					imageID = jQuery( this ).parent( '.fusion-multi-image' ).data( 'image-id' );
					imageIDs = input.val().split( ',' ).map( function( v ) {
						return parseInt( v );
					} );
					imageIndex = imageIDs.indexOf( imageID );
					if ( -1 !== imageIndex ) {
						imageIDs.splice( imageIndex, 1 );
					}
					imageIDs = imageIDs.join( ',' );
					input.val( imageIDs );
					jQuery( this ).parent( '.fusion-multi-image' ).remove();
				} );
			},

			fusionBuilderActivateLinkSelector: function( $linkButton ) {
				var $linkSubmit = jQuery( '#wp-link-submit' ),
					$linkTitle = jQuery( '.wp-link-text-field' ),
					$linkTarget = jQuery( '.link-target' ),
					$fusionLinkSubmit = jQuery( '<input type="button" name="fusion-link-submit" id="fusion-link-submit" class="button-primary" value="Set Link">' ),
					$input,
					$linkDialog,
					$url,
					wpLinkL10n = window.wpLinkL10n;

				jQuery( $linkButton ).click( function( e ) {
					$fusionLinkSubmit.insertBefore( $linkSubmit );
					$input = jQuery( e.target ).prev( '.fusion-builder-link-field' );
					$url = $input.val();
					$linkSubmit.hide();
					$linkTitle.hide();
					$linkTarget.hide();
					$fusionLinkSubmit.show();
					$linkDialog = ! window.wpLink && $.fn.wpdialog && jQuery( '#wp-link' ).length ? {
						$link: ! 1,
						open: function() {
							this.$link = jQuery( '#wp-link' ).wpdialog({
								title: wpLinkL10n.title,
								width: 480,
								height: 'auto',
								modal: ! 0,
								dialogClass: 'wp-dialog',
								zIndex: 3e5
							});
						},
						close: function() {
							this.$link.wpdialog( 'close' );
						}
					} : window.wpLink;
					$linkDialog.fusionUpdateLink = function( $fusionLinkSubmit ) {
						e.preventDefault();
						e.stopImmediatePropagation();
						e.stopPropagation();
						$url = jQuery( '#wp-link-url' ).length ? jQuery( '#wp-link-url' ).val() : jQuery( '#url-field' ).val();
						$input.val( $url );
						$linkSubmit.show();
						$linkTitle.show();
						$linkTarget.show();
						$fusionLinkSubmit.remove();
						jQuery( '#wp-link-cancel' ).unbind( 'click' );
						$linkDialog.close();
						window.wpLink.textarea = '';
					},
					$linkDialog.open( 'content' );
					jQuery( '#wp-link-url' ).val( $url );
				});

				jQuery( 'body' ).on( 'click', '#fusion-link-submit', function( e ) {
					$linkDialog.fusionUpdateLink( jQuery( this ) );
				});

				jQuery( 'body' ) .on( 'click', '#wp-link-cancel, #wp-link-close, #wp-link-backdrop', function( e ) {
					$linkSubmit.show();
					$linkTitle.show();
					$linkTarget.show();
					$fusionLinkSubmit.remove();
				});
			},

			fusionBuilderSetContent: function( textareaID, content ) {
				if ( 'undefined' !== typeof window.tinyMCE && window.tinyMCE.get( textareaID ) && ! window.tinyMCE.get( textareaID ).isHidden() ) {

					if ( window.tinyMCE.get( textareaID ).getParam( 'wpautop', true ) && 'undefined' !== typeof window.switchEditors ) {
						content = window.switchEditors.wpautop( content );
					}

					window.tinyMCE.get( textareaID ).setContent( content, { format: 'html' } );
				} else {
					$( '#' + textareaID ).val( content );
				}
			},

			layoutLoaded: function() {
				this.newLayoutLoaded = true;
			},

			clearLayout: function( event ) {

				var r;

				if ( event ) {
					event.preventDefault();
				}

				r = confirm( fusionBuilderText.are_you_sure_you_want_to_delete_this_layout );

				if ( false == r ) {
					return false;
				}

				this.blankPage = true;
				this.clearBuilderLayout( true );

				// Clear history
				fusionHistoryManager.clearEditor( 'blank' );

			},

			showHistoryDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				this.$el.find( '.fusion-builder-history-list' ).show();
			},

			hideHistoryDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				this.$el.find( '.fusion-builder-history-list' ).hide();
			},

			saveTemplateDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				this.showLibrary();
				$( '#fusion-builder-layouts-templates-trigger' ).click();
			},

			loadPreBuiltPage: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				this.showLibrary();
				$( '#fusion-builder-layouts-demos-trigger' ).click();
			},

			saveLayout: function( event ) {

				var templateContent,
				    templateName,
				    layoutsContainer,
				    currentPostID,
				    emptyMessage,
				    customCSS,
				    pageTemplate,
				    $customFields = [],
				    $name,
				    $value;

				if ( event ) {
					event.preventDefault();
				}

				// Get custom field values for saving.
				jQuery( 'input[id^="pyre_"], select[id^="pyre_"]' ).each( function( n ) {
					$name = jQuery( this ).attr( 'id' );
					$value = jQuery( this ).val();
					if ( 'undefined' !== typeof $name && 'undefined' !== typeof $value ) {
						$customFields[n] = [$name, $value];
					}
				});

				templateContent  = fusionBuilderGetContent( 'content' );
				templateName     = $( '#new_template_name' ).val();
				layoutsContainer = $( '#fusion-builder-layouts-templates .fusion-page-layouts' );
				currentPostID    = $( '#fusion_builder_main_container' ).data( 'post-id' );
				emptyMessage     = $( '#fusion-builder-layouts-templates .fusion-page-layouts .fusion-empty-library-message' );
				customCSS = $( '#fusion-custom-css-field' ).val();
				pageTemplate = $( '#page_template' ).val();

				if ( '' !== templateName ) {

					$.ajax( {
						type: 'POST',
						url: fusionBuilderConfig.ajaxurl,
						dataType: 'json',
						data: {
							action: 'fusion_builder_save_layout',
							fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
							fusion_layout_name: templateName,
							fusion_layout_content: templateContent,
							fusion_layout_post_type: 'fusion_template',
							fusion_current_post_id: currentPostID,
							fusion_custom_css: customCSS,
							fusion_page_template: pageTemplate,
							fusion_options: $customFields
						},
						complete: function( data ) {
							layoutsContainer.prepend( data.responseText );
							emptyMessage.hide();
						}
					} );

					$( '#new_template_name' ).val( '' );

				} else {
					alert( fusionBuilderText.please_enter_template_name );
				}
			},

			saveElement: function( event ) {
				var fusionElementType,
				    elementCID,
				    elementView;

				if ( event ) {
					event.preventDefault();
				}

				fusionElementType = $( event.currentTarget ).data( 'element-type' );
				elementCID        = $( event.currentTarget ).data( 'element-cid' );
				elementView       = FusionPageBuilderViewManager.getView( elementCID );

				elementView.saveElement();
			},

			loadLayout: function( event ) {
				var $layout,
				    contentPlacement,
				    content,
				    $customCSS;

				if ( event ) {
					event.preventDefault();
				}

				if ( true === this.layoutIsLoading ) {
					return;
				} else {
					this.layoutIsLoading = true;
				}

				$layout          = $( event.currentTarget ).closest( 'li' );
				contentPlacement = $( event.currentTarget ).data( 'load-type' );
				content          = fusionBuilderGetContent( 'content' );
				$customCSS       = jQuery( '#fusion-custom-css-field' ).val();

				$.ajax( {
					type: 'POST',
					url: fusionBuilderConfig.ajaxurl,
					data: {
						action: 'fusion_builder_load_layout',
						fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
						fusion_layout_id: $layout.data( 'layout_id' )
					},
					beforeSend: function() {
						FusionPageBuilderEvents.trigger( 'fusion-show-loader' );

						$( 'body' ).removeClass( 'fusion_builder_inner_row_no_scroll' );
						$( '.fusion_builder_modal_inner_row_overlay' ).remove();
						$( '#fusion-builder-layouts' ).hide();

					},
					success: function( data ) {
						var dataObj;

						// New layout loaded
						FusionPageBuilderApp.layoutLoaded();

						dataObj = JSON.parse( data );

						if ( 'above' === contentPlacement ) {
							content = dataObj.post_content + content;

							// Set custom css above
							if ( 'undefined' !== typeof ( dataObj.custom_css ) ) {
								$( '#fusion-custom-css-field' ).val( dataObj.custom_css + '\n' + $customCSS );
							}

						} else if ( 'below' === contentPlacement ) {
							content = content + dataObj.post_content;

							// Set custom css below
							if ( 'undefined' !== typeof ( dataObj.custom_css ) ) {
								if ( $customCSS.length ) {
									$( '#fusion-custom-css-field' ).val( $customCSS + '\n' + dataObj.custom_css );
								} else {
									$( '#fusion-custom-css-field' ).val( dataObj.custom_css );
								}
							}

						} else {
							content = dataObj.post_content;

							// Set custom css.
							if ( 'undefined' !== typeof ( dataObj.custom_css ) ) {
								$( '#fusion-custom-css-field' ).val( dataObj.custom_css );
							}

							//  Set Fusion Option selection.
							jQuery.each( dataObj.post_meta, function( $name, $value ) {
								jQuery( '#' + $name ).val( $value ).trigger( 'change' );
							});
						}

						FusionPageBuilderApp.clearBuilderLayout();

						FusionPageBuilderApp.createBuilderLayout( content );

						// Set page template
						if ( 'undefined' !== typeof ( dataObj.page_template ) ) {
							$( '#page_template' ).val( dataObj.page_template );
						}

						FusionPageBuilderApp.layoutIsLoading = false;
					},
					complete: function() {
						FusionPageBuilderEvents.trigger( 'fusion-hide-loader' );
					}
				} );
			},

			loadDemoPage: function( event ) {
				var pageName,
				    demoName,
				    postId,
				    content,
				    r;

				if ( event ) {
					event.preventDefault();
				}

				r = confirm( fusionBuilderText.importing_single_page );

				if ( false == r ) {
					return false;
				}

				if ( true === this.layoutIsLoading ) {
					return;
				} else {
					this.layoutIsLoading = true;
				}

				pageName = $( event.currentTarget ).data( 'page-name' );
				demoName = $( event.currentTarget ).data( 'demo-name' );
				postId = $( event.currentTarget ).data( 'post-id' );

				$.ajax( {
					type: 'POST',
					url: fusionBuilderConfig.ajaxurl,
					data: {
						action: 'fusion_builder_load_demo',
						fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
						page_name: pageName,
						demo_name: demoName,
						post_id: postId
					},
					beforeSend: function() {
						FusionPageBuilderEvents.trigger( 'fusion-show-loader' );

						$( 'body' ).removeClass( 'fusion_builder_inner_row_no_scroll' );
						$( '.fusion_builder_modal_inner_row_overlay' ).remove();
						$( '#fusion-builder-layouts' ).hide();

					},
					success: function( data ) {
						var dataObj,
						    meta;

						// New layout loaded
						FusionPageBuilderApp.layoutLoaded();

						dataObj = JSON.parse( data );

						content = dataObj.post_content;

						FusionPageBuilderApp.clearBuilderLayout( false );

						FusionPageBuilderApp.createBuilderLayout( content );

						// Set page template
						if ( 'undefined' !== typeof ( dataObj.page_template ) ) {
							$( '#page_template' ).val( dataObj.page_template );
						}

						meta = dataObj.meta;

						// Set page options
						_.each( meta, function( value, name ) {
							$( '#' + name ).val( value ).trigger( 'change' );
						});

						FusionPageBuilderApp.layoutIsLoading = false;
					},
					complete: function() {
						FusionPageBuilderEvents.trigger( 'fusion-hide-loader' );
					}
				} );
			},

			deleteLayout: function( event ) {

				var $layout,
				    r;

				if ( event ) {
					event.preventDefault();

					r = confirm( fusionBuilderText.are_you_sure_you_want_to_delete_this );

					if ( false == r ) {
						return false;
					}
				}

				if ( true === this.layoutIsDeleting ) {
					return;
				} else {
					this.layoutIsDeleting = true;
				}

				$layout = $( event.currentTarget ).closest( 'li' );

				$.ajax( {
					type: 'POST',
					url: fusionBuilderConfig.ajaxurl,
					data: {
						action: 'fusion_builder_delete_layout',
						fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
						fusion_layout_id: $layout.data( 'layout_id' )
					},
					success: function( data ) {
						var $containerSuffix;
						if ( $layout.parents( '#fusion-builder-layouts-templates' ).length ) {
							$containerSuffix = 'templates';
						} else {
							$containerSuffix = 'elements';
						}

						$layout.remove();

						FusionPageBuilderApp.layoutIsDeleting = false;
						if ( ! $( '#fusion-builder-layouts-' + $containerSuffix + ' .fusion-page-layouts' ).find( 'li' ).length ) {
							$( '#fusion-builder-layouts-' + $containerSuffix + ' .fusion-page-layouts .fusion-empty-library-message' ).show();
						}
					}
				} );
			},

			openLibrary: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				this.showLibrary();
				$( '#fusion-builder-layouts-templates-trigger' ).click();
			},

			showLibrary: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				$( '#fusion-builder-layouts' ).show();
				$( 'body' ).addClass( 'fusion_builder_inner_row_no_scroll' ).append( '<div class="fusion_builder_modal_inner_row_overlay"></div>' );
			},

			hideLibrary: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				$( '#fusion-builder-layouts' ).hide();
				$( 'body' ).removeClass( 'fusion_builder_inner_row_no_scroll' );
				$( '.fusion_builder_modal_inner_row_overlay' ).remove();
				$( '.fusion-save-element-fields' ).remove();
			},

			showLoader: function() {
				$( '#fusion_builder_main_container' ).css( 'height', '148px' );
				$( '#fusion_builder_container' ).hide();
				$( '#fusion-loader' ).fadeIn( 'fast' );
			},

			hideLoader: function() {
				$( '#fusion_builder_container' ).fadeIn( 'fast' );
				$( '#fusion_builder_main_container' ).removeAttr( 'style' );
				$( '#fusion-loader' ).fadeOut( 'fast' );
			},

			sortableContainers: function() {
				this.$el.sortable( {
					handle: '.fusion-builder-section-header',
					items: '.fusion_builder_container, .fusion-builder-next-page',
					cancel: '.fusion-builder-section-name, .fusion-builder-settings, .fusion-builder-clone, .fusion-builder-remove, .fusion-builder-section-add, .fusion-builder-add-element, .fusion-builder-insert-column, #fusion_builder_controls, .fusion-builder-save-element',
					cursor: 'move',
					update: function( event, ui ) {
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.moved_container;
						FusionPageBuilderEvents.trigger( 'fusion-element-sorted' );
					}
				} );
			},

			initialBuilderLayout: function( initialLoad ) {

				// Clear all views
				FusionPageBuilderViewManager.removeViews();

				FusionPageBuilderEvents.trigger( 'fusion-show-loader' );

				setTimeout( function() {

					var content                   = fusionBuilderGetContent( 'content', true, initialLoad ),
					    contentErrorMarkup        = '',
					    contentErrorMarkupWrapper = '',
					    contentErrorMarkupClone   = '';

					try {

						content = FusionPageBuilderApp.validateContent( content );

						FusionPageBuilderApp.createBuilderLayout( content );

						FusionPageBuilderEvents.trigger( 'fusion-hide-loader' );

					} catch ( error ) {
						console.log( error );
						FusionPageBuilderApp.fusionBuilderSetContent( 'content', content );
						jQuery( '#fusion_toggle_builder' ).trigger( 'click' );

						contentErrorMarkup = FusionPageBuilderApp.$el.find( '#content-error' );
						contentErrorMarkupWrapper = FusionPageBuilderApp.$el;
						contentErrorMarkupClone = contentErrorMarkup.clone();

						contentErrorMarkup.dialog({
							dialogClass: 'fusion-builder-dialog',
							autoOpen: false,
							modal: true,
							buttons: {
								OK: function() {
									jQuery( this ).dialog( 'close' );
								}
							},
							close: function() {
								contentErrorMarkupWrapper.append( contentErrorMarkupClone );
							}
						});

						contentErrorMarkup.dialog( 'open' );
					}

				}, 50 );
			},

			validateContent: function( content ) {
				var contentIsEmpty      = '' == content,
				    container           = '',
				    column              = '',
				    textNodes           = '',
				    innerColumnsContent = '',
				    columns             = [],
				    containers          = [],
				    shortcodeTags,
				    columnwrapped,
				    insertionFlag;

				// Throw exception with the fullwidth shortcode.
				if ( -1 !== content.indexOf( '[fullwidth' ) ) {
					throw 'Avada 4.0.3 or earlier fullwidth container used!';
				}

				if ( ! contentIsEmpty ) {

					// Fixes [fusion_text /] instances, which were created in 5.0.1 for empty text blocks.
					content = content.replace( /\[fusion\_text \/\]/g, '[fusion_text][/fusion_text]' ).replace(  /\[\/fusion\_text\]\[\/fusion\_text\]/g, '[/fusion_text]' );

					content = content.replace( /\$\$/g, '&#36;&#36;' );
					textNodes = content;

					// Add container if missing.
					textNodes = wp.shortcode.replace( 'fusion_builder_container', textNodes, function( tag ) {
						return '|';
					});
					textNodes = wp.shortcode.replace( 'fusion_builder_next_page', textNodes, function( tag ) {
						return '|';
					});
					textNodes = textNodes.trim().split( '|' );

					_.each( textNodes, function( textNodes ) {
						if ( '' !== textNodes.trim() ) {
							content = content.replace( textNodes, '[fusion_builder_container hundred_percent="no" equal_height_columns="no" menu_anchor="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="" id="" background_color="" background_image="" background_position="center center" background_repeat="no-repeat" fade="no" background_parallax="none" parallax_speed="0.3" video_mp4="" video_webm="" video_ogv="" video_url="" video_aspect_ratio="16:9" video_loop="yes" video_mute="yes" overlay_color="" overlay_opacity="0.5" video_preview_image="" border_size="" border_color="" border_style="solid" padding_top="" padding_bottom="" padding_left="" padding_right=""][fusion_builder_row]' + textNodes + '[/fusion_builder_row][/fusion_builder_container]' );
						}
					});

					textNodes = wp.shortcode.replace( 'fusion_builder_container', content, function( tag ) {
						containers.push( tag.content );
					});

					_.each( containers, function( textNodes ) {

						// Add column if missing.
						textNodes = wp.shortcode.replace( 'fusion_builder_row', textNodes, function( tag ) {
							return tag.content;
						});

						textNodes = wp.shortcode.replace( 'fusion_builder_column', textNodes, function( tag ) {
							return '|';
						});

						textNodes = textNodes.trim().split( '|' );
						_.each( textNodes, function( textNodes ) {
							if ( '' !== textNodes.trim() && '[fusion_builder_row][/fusion_builder_row]' !== textNodes.trim() ) {
								columnwrapped = '[fusion_builder_column type="1_1" background_position="left top" background_color="" border_size="" border_color="" border_style="solid" border_position="all" spacing="yes" background_image="" background_repeat="no-repeat" padding="" margin_top="0px" margin_bottom="0px" class="" id="" animation_type="" animation_speed="0.3" animation_direction="left" hide_on_mobile="small-visibility,medium-visibility,large-visibility" center_content="no" last="no" min_height="" hover_type="none" link=""]' + textNodes + '[/fusion_builder_column]';
								content = content.replace( textNodes, columnwrapped );

							}
						});
					});

					textNodes = wp.shortcode.replace( 'fusion_builder_column_inner', content, function( tag ) {
						columns.push( tag.content );
					});
					textNodes = wp.shortcode.replace( 'fusion_builder_column', content, function( tag ) {
						columns.push( tag.content );
					});
					_.each( columns, function( textNodes ) {

						// Wrap non fusion elements.
						shortcodeTags = fusionAllElements;
						_.each( shortcodeTags, function( shortcode ) {
							if ( 'undefined' === typeof shortcode.generator_only ) {
								textNodes = wp.shortcode.replace( shortcode.shortcode, textNodes, function( tag ) {
									return '|';
								} );
							}
						});

						textNodes = textNodes.trim().split( '|' );
						_.each( textNodes, function( textNodes ) {
							if ( '' !== textNodes.trim() ) {
								insertionFlag = '@=%~@';
								if ( '@' === textNodes.slice( -1 ) ) {
									insertionFlag = '#=%~#';
								}
								content = content.replace( textNodes, '[fusion_text]' + textNodes.slice( 0, -1 ) + insertionFlag + textNodes.slice( -1 ) + '[/fusion_text]' );
							}
						});
					});
					content = content.replace( /@=%~@/g, '' ).replace( /#=%~#/g, '' );

					// Check for once deactivated elements in text blocks that are active again.
					content = wp.shortcode.replace( 'fusion_text', content, function( tag ) {
						shortcodeTags = fusionAllElements;
						textNodes = tag.content;
						_.each( shortcodeTags, function( shortcode ) {
							if ( 'undefined' === typeof shortcode.generator_only ) {
								textNodes = wp.shortcode.replace( shortcode.shortcode, textNodes, function( tag ) {
									return '|';
								} );
							}
						});
						if ( ! textNodes.replace( /\|/g, '' ).length ) {
							return tag.content;
						}
					});
				}

				function replaceDollars( match ) {
					return '$$';
				}

				content = content.replace( /&#36;&#36;/g, replaceDollars );

				return content;
			},

			clearBuilderLayout: function( blankPageLayout ) {

				// Remove blank page layout
				this.$el.find( '.fusion-builder-blank-page-content' ).each( function() {
					var
					$that = $( this ),
					thisView = FusionPageBuilderViewManager.getView( $that.data( 'cid' ) );

					if ( 'undefined' !== typeof thisView ) {
						thisView.removeBlankPageHelper();
					}
				} );

				// Remove all containers
				this.$el.find( '.fusion-builder-section-content' ).each( function() {
					var
					$that = $( this ),
					thisView = FusionPageBuilderViewManager.getView( $that.data( 'cid' ) );

					if ( 'undefined' !== typeof thisView ) {
						thisView.removeContainer();
					}
				} );

				// Create blank page layout
				if ( blankPageLayout ) {

					if ( true === this.blankPage ) {
						if ( ! this.$el.find( '.fusion-builder-blank-page-content' ).length ) {
							this.createBuilderLayout( '[fusion_builder_blank_page][/fusion_builder_blank_page]' );
						}

						this.blankPage = false;
					}

				}

			},

			createBuilderLayout: function( content ) {
				this.shortcodesToBuilder( content );
				this.builderToShortcodes();
			},

			shortcodesToBuilder: function( content, parentCID ) {
				var thisEl,
				    regExp,
				    innerRegExp,
				    matches,
				    shortcodeTags;

				// Show blank page layout
				if ( '' === content && ! this.$el.find( '.fusion-builder-blank-page-content' ).length ) {
					this.createBuilderLayout( '[fusion_builder_blank_page][/fusion_builder_blank_page]' );

					return;
				}

				thisEl        = this;
				shortcodeTags = _.keys( fusionAllElements ).join( '|' );
				regExp        = window.wp.shortcode.regexp( shortcodeTags );
				innerRegExp   = this.regExpShortcode( shortcodeTags );
				matches       = content.match( regExp );

				_.each( matches, function( shortcode ) {

					var shortcodeElement    = shortcode.match( innerRegExp ),
					    shortcodeName       = shortcodeElement[2],
					    shortcodeAttributes = '' !== shortcodeElement[3] ? window.wp.shortcode.attrs( shortcodeElement[3] ) : '',
					    shortcodeContent    = shortcodeElement[5],
					    elementCID           = FusionPageBuilderViewManager.generateCid(),
					    prefixedAttributes  = { params: ({}) },
					    elementSettings,
					    key,
					    prefixedKey,
					    dependencyOption,
					    dependencyOptionValue,
					    elementContent,
					    alpha,
					    paging,

						// Check for shortcodes inside shortcode content
						shortcodesInContent = 'undefined' !== typeof shortcodeContent && '' !== shortcodeContent && shortcodeContent.match( regExp ),

						// Check if shortcode allows generator
						allowGenerator = 'undefined' !== typeof fusionAllElements[ shortcodeName ].allow_generator ? fusionAllElements[ shortcodeName ].allow_generator : '';

					elementSettings = {
						type: shortcodeName,
						element_type: shortcodeName,
						cid: elementCID,
						created: 'manually',
						multi: '',
						params: {},
						allow_generator: allowGenerator
					};

					if ( 'fusion_builder_container' !== shortcodeName || 'fusion_builder_next_page' !== shortcodeName ) {
						elementSettings.parent = parentCID;
					}

					if ( 'fusion_builder_container' !== shortcodeName && 'fusion_builder_row' !== shortcodeName && 'fusion_builder_column' !== shortcodeName && 'fusion_builder_column_inner' !== shortcodeName && 'fusion_builder_row_inner' !== shortcodeName  && 'fusion_builder_blank_page' !== shortcodeName && 'fusion_builder_next_page' !== shortcodeName ) {

						if ( -1 !== shortcodeName.indexOf( 'fusion_' ) ||
							 -1 !== shortcodeName.indexOf( 'layerslider' ) ||
							 -1 !== shortcodeName.indexOf( 'rev_slider' ) ||
							 'undefined' !== typeof fusionAllElements[ shortcodeName ] ) {
							elementSettings.type = 'element';
						}
					}

					if ( _.isObject( shortcodeAttributes.named ) ) {
						for ( key in shortcodeAttributes.named ) {

							prefixedKey = key;
							if ( ( 'fusion_builder_column' === shortcodeName || 'fusion_builder_column_inner' === shortcodeName ) && 'type' === prefixedKey ) {
								prefixedKey = 'layout';

								prefixedAttributes[ prefixedKey ] = shortcodeAttributes.named[ key ];
							}

							prefixedAttributes.params[ prefixedKey ] = shortcodeAttributes.named[ key ];
							if ( 'fusion_products_slider' === shortcodeName && 'cat_slug' === key ) {
								prefixedAttributes.params.cat_slug = shortcodeAttributes.named[ key ].replace( /\|/g, ',' );
							}
							if ( 'gradient_colors' === key ) {
								delete prefixedAttributes.params[ prefixedKey ];
								if ( -1 !== shortcodeAttributes.named[ key ].indexOf( '|' ) ) {
									prefixedAttributes.params.button_gradient_top_color = shortcodeAttributes.named[ key ].split( '|' )[0].replace( 'transparent', 'rgba(255,255,255,0)' );
									prefixedAttributes.params.button_gradient_bottom_color = shortcodeAttributes.named[ key ].split( '|' )[1] ? shortcodeAttributes.named[ key ].split( '|' )[1].replace( 'transparent', 'rgba(255,255,255,0)' ) : shortcodeAttributes.named[ key ].split( '|' )[0].replace( 'transparent', 'rgba(255,255,255,0)' );
								} else {
									prefixedAttributes.params.button_gradient_bottom_color = prefixedAttributes.params.button_gradient_top_color = shortcodeAttributes.named[ key ].replace( 'transparent', 'rgba(255,255,255,0)' );
								}
							}
							if ( 'gradient_hover_colors' === key ) {
								delete prefixedAttributes.params[ prefixedKey ];
								if ( -1 !== shortcodeAttributes.named[ key ].indexOf( '|' ) ) {
									prefixedAttributes.params.button_gradient_top_color_hover = shortcodeAttributes.named[ key ].split( '|' )[0].replace( 'transparent', 'rgba(255,255,255,0)' );
									prefixedAttributes.params.button_gradient_bottom_color_hover = shortcodeAttributes.named[ key ].split( '|' )[1] ? shortcodeAttributes.named[ key ].split( '|' )[1].replace( 'transparent', 'rgba(255,255,255,0)' ) : shortcodeAttributes.named[ key ].split( '|' )[0].replace( 'transparent', 'rgba(255,255,255,0)' );
								} else {
									prefixedAttributes.params.button_gradient_bottom_color_hover = prefixedAttributes.params.button_gradient_top_color_hover = shortcodeAttributes.named[ key ].replace( 'transparent', 'rgba(255,255,255,0)' );
								}
							}
							if ( 'overlay_color' === key && '' !== shortcodeAttributes.named[ key ] && 'fusion_builder_container' === shortcodeName ) {
								delete prefixedAttributes.params[ prefixedKey ];
								alpha = ( 'undefined' !== typeof shortcodeAttributes.named.overlay_opacity ) ? shortcodeAttributes.named.overlay_opacity : 1;
								prefixedAttributes.params.background_color = jQuery.Color( shortcodeAttributes.named[ key ] ).alpha( alpha ).toRgbaString();
							}
							if ( 'overlay_opacity' === key ) {
								delete prefixedAttributes.params[ prefixedKey ];
							}
							if ( 'scrolling' === key && 'fusion_blog' === shortcodeName ) {
								delete prefixedAttributes.params.paging;
								paging = ( 'undefined' !== typeof shortcodeAttributes.named.paging ) ? shortcodeAttributes.named.paging : '';
								if ( 'no' === paging && 'pagination' == shortcodeAttributes.named.scrolling ) {
									prefixedAttributes.params.scrolling = 'no';
								}
							}

							// The grid-with-text layout was removed in Avada 5.2, so layout has to
							// be converted to grid. And boxed_layout was replaced by new text_layout.
							if ( 'fusion_portfolio' === shortcodeName ) {
								if ( 'layout' === key ) {
									if ( 'grid' === shortcodeAttributes.named[ key ] && shortcodeAttributes.named.hasOwnProperty( 'boxed_text' ) ) {
										shortcodeAttributes.named.boxed_text = 'no_text';
									} else if ( 'grid-with-text' === shortcodeAttributes.named[ key ] ) {
										prefixedAttributes.params[ key ] = 'grid';
									}
								}

								if ( 'boxed_text' === key ) {
									prefixedAttributes.params.text_layout = shortcodeAttributes.named[ key ];
									delete prefixedAttributes.params[ key ];
								}

								if ( 'content_length' === key && 'full-content' === shortcodeAttributes.named[ key ] ) {
									prefixedAttributes.params[ key ] = 'full_content';
								}

							}

						}

						elementSettings = _.extend( elementSettings, prefixedAttributes );
					}

					if ( ! shortcodesInContent && 'fusion_builder_column' !== shortcodeName ) {
						elementSettings.params.element_content = shortcodeContent;
					}

					// Compare shortcode name to multi elements object / array
					if ( shortcodeName in fusionMultiElements ) {
						elementSettings.multi = 'multi_element_parent';
					}

					// Set content for elements with dependency options
					if ( 'undefined' !== typeof fusionAllElements[ shortcodeName ].option_dependency ) {

						dependencyOption      = fusionAllElements[ shortcodeName ].option_dependency;
						dependencyOptionValue = prefixedAttributes.params[ dependencyOption ];
						elementContent         = prefixedAttributes.params.element_content;
						prefixedAttributes.params[ dependencyOptionValue ] = elementContent;
					}

					if ( shortcodesInContent ) {
						if ( 'fusion_builder_container' !== shortcodeName && 'fusion_builder_row' !== shortcodeName && 'fusion_builder_row_inner' !== shortcodeName && 'fusion_builder_column' !== shortcodeName && 'fusion_builder_column_inner' !== shortcodeName && 'fusion_builder_next_page' !== shortcodeName ) {
							elementSettings.params.element_content = shortcodeContent;
						}
					}

					thisEl.collection.add( [ elementSettings ] );

					if ( shortcodesInContent ) {

						if ( 'fusion_builder_container' == shortcodeName || 'fusion_builder_row' == shortcodeName || 'fusion_builder_row_inner' == shortcodeName || 'fusion_builder_column' == shortcodeName || 'fusion_builder_column_inner' == shortcodeName ) {
							thisEl.shortcodesToBuilder( shortcodeContent, elementCID );
						}
					}

				} );
			},

			addBuilderElement: function( element ) {

				var view,
				    modalView,
				    viewSettings = {
						model: element,
						collection: FusionPageBuilderElements
				    },
				    parentModel,
				    elementType,
				    previewView;

				switch ( element.get( 'type' ) ) {

					case 'fusion_builder_blank_page':

						view = new FusionPageBuilder.BlankPageView( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						if ( ! _.isUndefined( element.get( 'view' ) ) ) {
							element.get( 'view' ).$el.after( view.render().el );

						} else {
							this.$el.find( '#fusion_builder_container' ).append( view.render().el );
						}

						break;

					case 'fusion_builder_container':

						// Check custom container position
						if ( '' !== FusionPageBuilderApp.targetContainerCID ) {
							element.attributes.view = FusionPageBuilderViewManager.getView( FusionPageBuilderApp.targetContainerCID );

							FusionPageBuilderApp.targetContainerCID = '';
						}

						view = new FusionPageBuilder.ContainerView( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						if ( ! _.isUndefined( element.get( 'view' ) ) ) {
							element.get( 'view' ).$el.after( view.render().el );

						} else {
							this.$el.find( '#fusion_builder_container' ).append( view.render().el );
							this.$el.find( '.fusion_builder_blank_page' ).remove();
						}

						// Add row if needed
						if ( 'manually' !== element.get( 'created' ) ) {
							view.addRow();
						}

						// Check if container is toggled
						if ( ! _.isUndefined( element.attributes.params.admin_toggled ) && 'no' === element.attributes.params.admin_toggled || _.isUndefined( element.attributes.params.admin_toggled ) ) {
							FusionPageBuilderApp.toggledContainers = false;
							$( '.fusion-builder-layout-buttons-toggle-containers' ).find( 'span' ).addClass( 'dashicons-arrow-up' ).removeClass( 'dashicons-arrow-down' );
						}

						break;

					case 'fusion_builder_row':

						view = new FusionPageBuilder.RowView( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						if ( FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-section-content' ).length ) {
							FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-section-content' ).append( view.render().el );

						} else {
							FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '> .fusion-builder-add-element' ).hide().end().append( view.render().el );
						}

						// Add parent view to inner rows that have been converted from shortcodes
						if ( 'manually' === element.get( 'created' ) && 'row_inner' === element.get( 'element_type' ) ) {
							element.set( 'view', FusionPageBuilderViewManager.getView( element.get( 'parent' ) ), { silent: true } );
						}

						break;

					case 'fusion_builder_row_inner':

						view = new FusionPageBuilder.InnerRowView( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						if ( ! _.isUndefined( element.get( 'appendAfter' ) ) ) {
							element.get( 'appendAfter' ).after( view.render().el );
							element.unset( 'appendAfter' );

						} else {

							if ( FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-section-content' ).length ) {
								FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-section-content' ).append( view.render().el );

							} else {
								FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '> .fusion-builder-add-element' ).before( view.render().el );
							}
						}

						// Add parent view to inner rows that have been converted from shortcodes
						if ( 'manually' === element.get( 'created' ) && 'row_inner' === element.get( 'element_type' ) ) {
							element.set( 'view', FusionPageBuilderViewManager.getView( element.get( 'parent' ) ), { silent: true } );
						}

						break;

					case 'fusion_builder_column':

						if ( element.get( 'layout' ) ) {
							viewSettings.className = 'fusion-builder-column fusion-builder-column-outer fusion-builder-column-' + element.get( 'layout' );
							view = new FusionPageBuilder.ColumnView( viewSettings );

							// This column was cloned
							if ( ! _.isUndefined( element.get( 'cloned' ) ) && true === element.get( 'cloned' ) ) {
								element.targetElement = view.$el;
								element.unset( 'cloned' );
							}

							FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

							if ( ! _.isUndefined( element.get( 'targetElement' ) ) && 'undefined' === typeof element.get( 'from' ) ) {
								element.get( 'targetElement' ).after( view.render().el );
							} else {
								FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-row-container' ).append( view.render().el );
								element.unset( 'from' );
							}
						}
						break;

					case 'fusion_builder_column_inner':

						viewSettings.className = 'fusion-builder-column fusion-builder-column-inner fusion-builder-column-' + element.get( 'layout' );

						view = new FusionPageBuilder.NestedColumnView( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-row-container-inner' ).append( view.render().el );

						break;

					case 'element':

						viewSettings.attributes = {
							'data-cid': element.get( 'cid' )
						};

						// Multi element child
						if ( 'undefined' !== typeof element.get( 'multi' ) && 'multi_element_child' === element.get( 'multi' ) ) {

							view = new FusionPageBuilder.MultiElementSortableChild( viewSettings );

							element.targetElement = view.$el;

							element.attributes.view.child_views.push( view );

							FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

							if ( ! _.isUndefined( element.get( 'targetElement' ) ) ) {
								element.get( 'targetElement' ).after( view.render().el );

							} else {
								FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-sortable-options' ).append( view.render().el );
							}

							// This child was cloned
							if ( ! _.isUndefined( element.get( 'titleLabel' ) ) ) {
								if ( ! _.isUndefined( element.get( 'cloned' ) ) ) {
									view.$el.find( '.multi-element-child-name' ).text( element.get( 'titleLabel' ) );
								}
								element.unset( 'cloned' );
							}

						// Standard element
						} else {

							FusionPageBuilderEvents.trigger( 'fusion-remove-modal-view' );

							view = new FusionPageBuilder.ElementView( viewSettings );

							// Get element parent
							parentModel = this.collection.find( function( model ) {
								return model.get( 'cid' ) == element.get( 'parent' );
							} );

							// Add element builder view to proper column
							if (  'undefined' !== typeof parentModel && 'fusion_builder_column_inner' === parentModel.get( 'type' ) ) {

								if ( ! _.isUndefined( element.get( 'targetElement' ) ) && 'undefined' === typeof element.get( 'from' ) ) {
										element.get( 'targetElement' ).after( view.render().el );
								} else {
									FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-add-element' ).before( view.render().el );
								}

							} else {

								if ( ! _.isUndefined( element.get( 'targetElement' ) ) && 'undefined' === typeof element.get( 'from' ) ) {
									element.get( 'targetElement' ).after( view.render().el );
								} else {
									FusionPageBuilderViewManager.getView( element.get( 'parent' ) ).$el.find( '.fusion-builder-add-element:not(.fusion-builder-column-inner .fusion-builder-add-element)' ).before( view.render().el );
								}
							}

							FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

							// Check if element was added manually
							if ( 'manually' == element.get( 'added' ) ) {

								viewSettings.attributes = {
									'data-modal_view': 'element_settings'
								};

								view = new FusionPageBuilder.ModalView( viewSettings );

								$( 'body' ).append( view.render().el );

							// Generate element preview
							} else {

								elementType = element.get( 'element_type' );

								if ( 'undefined' !== typeof fusionAllElements[ elementType ].preview ) {

									previewView = new FusionPageBuilder.ElementPreviewView( viewSettings );
									view.$el.find( '.fusion-builder-module-preview' ).append( previewView.render().el );
								}
							}
						}

						break;

					case 'generated_element':

						FusionPageBuilderEvents.trigger( 'fusion-remove-modal-view' );

						// Ignore modals for columns inserted with generator
						if ( 'fusion_builder_column_inner' !== element.get( 'element_type' ) && 'fusion_builder_column' !== element.get( 'element_type' ) ) {

							viewSettings.attributes = {
								'data-modal_view': 'element_settings'
							};
							view = new FusionPageBuilder.ModalView( viewSettings );
							$( 'body' ).append( view.render().el );

						}

						break;

					case 'fusion_builder_next_page':
						view = new FusionPageBuilder.NextPage( viewSettings );

						FusionPageBuilderViewManager.addView( element.get( 'cid' ), view );

						if ( ! _.isUndefined( element.get( 'appendAfter' ) ) ) {

							if ( ! element.get( 'appendAfter' ).next().next().hasClass( 'fusion-builder-next-page' ) ) {
								element.get( 'appendAfter' ).after( view.render().el );
							}
						} else {
							$( '.fusion_builder_container:last-child' ).after( view.render().el );
						}

						break;

				}
			},

			regExpShortcode: _.memoize( function( tag ) {
				return new RegExp( '\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' );
			} ),

			findShortcodeMatches: function( content, match ) {

				var shortcodeMatches,
				    shortcodeRegExp,
				    shortcodeInnerRegExp;

				if ( _.isObject( content ) ) {
					content = content.value;
				}

				shortcodeMatches     = '';
				content              = 'undefined' !== typeof content ? content : '';
				shortcodeRegExp      = window.wp.shortcode.regexp( match );
				shortcodeInnerRegExp = new RegExp( '\\[(\\[?)(' + match + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' );

				if ( 'undefined' !== typeof content && '' !== content ) {
					shortcodeMatches = content.match( shortcodeRegExp );
				}

				return shortcodeMatches;
			},

			builderToShortcodes: function() {

				var shortcode = '',
				    thisEl    = this;

				this.$el.find( '.fusion_builder_container' ).each( function() {

					var $thisContainer = $( this ).find( '.fusion-builder-section-content' );

					shortcode += thisEl.generateElementShortcode( $( this ), true );

					$thisContainer.find( '.fusion_builder_row' ).each( function() {

						var $thisRow = $( this );

						shortcode += '[fusion_builder_row]';

						$thisRow.find( '.fusion-builder-column-outer' ).each( function() {
							var
							$thisColumn = $( this ),
							columnCID   = $thisColumn.data( 'cid' ),
							columnView  = FusionPageBuilderViewManager.getView( columnCID );

							shortcode += columnView.getColumnContent( $thisColumn );

						} );

						shortcode += '[/fusion_builder_row]';

					} );

					shortcode += '[/fusion_builder_container]';

					// Check for next page shortcode
					if ( $( this ).next().hasClass( 'fusion-builder-next-page' ) ) {
						shortcode += '[fusion_builder_next_page]';
					}

				} );

				setTimeout( function() {

					FusionPageBuilderApp.fusionBuilderSetContent( 'content', shortcode );
					FusionPageBuilderEvents.trigger( 'fusion-save-history-state' );

				}, 500 );

			},

			saveHistoryState: function() {

				if ( true === this.newLayoutLoaded ) {
					fusionHistoryManager.clearEditor();
					this.newLayoutLoaded = false;
				}

				fusionHistoryManager.captureEditor();
				fusionHistoryManager.turnOffTracking();
			},

			generateElementShortcode: function( $element, openTagOnly, generator ) {
				var attributes = '',
				    content    = '',
				    element,
				    $thisElement,
				    elementCID,
				    elementType,
				    elementSettings = '',
				    shortcode,
				    ignoredAtts,
				    optionDependency,
				    optionDependencyValue,
				    key,
				    setting,
				    settingName,
				    settingValue,
				    param,
				    keyName,
				    optionValue,
				    ignored,
				    paramDependency,
				    paramDependencyElement,
				    paramDependencyValue;

				// Check if added from Shortcode Generator
				if ( true === generator ) {
					element = $element;
				} else {
					$thisElement = $element;

					// Get cid from html element
					elementCID = 'undefined' === typeof $thisElement.data( 'cid' ) ? $thisElement.find( '.fusion-builder-data-cid' ).data( 'cid' ) : $thisElement.data( 'cid' ),

					// Get model by cid
					element = FusionPageBuilderElements.find( function( model ) {
						return model.get( 'cid' ) == elementCID;
					} );
				}

				elementType     = 'undefined' !== typeof element ? element.get( 'element_type' ) : 'undefined';
				elementSettings = '';
				shortcode      = '';
				elementSettings = element.attributes;

				// Ignored shortcode attributes
				ignoredAtts = 'undefined' !== typeof fusionAllElements[ elementType ].remove_from_atts ? fusionAllElements[ elementType ].remove_from_atts : '';
				ignoredAtts.push( 'undefined' );

				// Option dependency
				optionDependency = 'undefined' !== typeof( fusionAllElements[ elementType ].option_dependency ) ? fusionAllElements[ elementType ].option_dependency : '';

				for ( key in elementSettings ) {

					settingName = key;

					if ( 'params' !== settingName ) {
						continue;
					}

					settingValue = 'undefined' !== typeof element.get( settingName ) ? element.get( settingName ) : '';

					if ( 'params' === settingName ) {

						// Loop over params
						for ( param in settingValue ) {

							keyName = param;

							if ( 'element_content' === keyName ) {

								optionValue = 'undefined' !== typeof( settingValue[ param ] ) ? settingValue[ param ] : '';

								content = optionValue;

								if ( 'undefined' !== typeof settingValue[ optionDependency ] ) {
									optionDependency = fusionAllElements[ elementType ].option_dependency;
									optionDependencyValue = 'undefined' !== typeof( settingValue[ optionDependency ] ) ? settingValue[ optionDependency ] : '';

									// Set content
									content = settingValue[ optionDependencyValue ];
								}

							} else {

								ignored = '';

								if ( '' !== optionDependency ) {

									setting = keyName;

									// Get option dependency value ( value for type )
									optionDependencyValue = 'undefined' !== typeof ( settingValue[ optionDependency ] ) ? settingValue[ optionDependency ] : '';

									// Check for old fusion_map array structure
									if ( 'undefined' !== typeof  fusionAllElements[ elementType ].params[ setting ] ) {

										// Dependency exists
										if ( 'undefined' !== typeof ( fusionAllElements[ elementType ].params[ setting ].dependency ) ) {

											paramDependency = fusionAllElements[ elementType ].params[setting].dependency;

											paramDependencyElement = 'undefined' !== typeof ( paramDependency.element ) ? paramDependency.element : '';

											paramDependencyValue = 'undefined' !== typeof ( paramDependency.value ) ? paramDependency.value : '';

											if ( paramDependencyElement === optionDependency ) {

												if ( paramDependencyValue !== optionDependencyValue ) {

													ignored = '';
													ignored = setting;

												}
											}
										}
									}
								}

								// Ignore shortcode attributes tagged with "remove_from_atts"
								if ( $.inArray( param, ignoredAtts ) > -1 || ignored === param ) {

									// This attribute should be ignored from the shortcode

								} else {

									optionValue = 'undefined' !== typeof settingValue[ param ] ? settingValue[ param ] : '';

									// Check if attribute value is null
									if ( null === optionValue ) {
										optionValue = '';
									}

									attributes += ' ' + param + '="' + optionValue + '"';
								}
							}
						}

					} else if ( '' !== settingValue ) {
						attributes += ' ' + settingName + '="' + settingValue + '"';
					}
				}

				shortcode = '[' + elementType + attributes;

				if ( '' === content && 'fusion_text' !== elementType && 'fusion_code' !== elementType && ( 'undefined' !== typeof elementSettings.type && 'element' === elementSettings.type ) ) {
					openTagOnly = true;
					shortcode += ' /]';
				} else {
					shortcode += ']';
				}

				if ( ! openTagOnly ) {
					shortcode += content + '[/' + elementType + ']';
				}

				return shortcode;
			},

			customCSS: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				$( '.fusion-custom-css' ).slideToggle();
			},

			toggleAllContainers: function( event ) {

				var toggleButton,
					containerCID,
					containerModel,
					that = this;

				if ( event ) {
					event.preventDefault();
				}

				toggleButton = $( '.fusion-builder-layout-buttons-toggle-containers' ).find( 'span' );

				if ( toggleButton.hasClass( 'dashicons-arrow-up' ) ) {
					toggleButton.removeClass( 'dashicons-arrow-up' ).addClass( 'dashicons-arrow-down' );

					jQuery( '.fusion_builder_container' ).each( function() {
						var containerModel;

						containerCID   = jQuery( this ).find( '.fusion-builder-data-cid' ).data( 'cid' );
						containerModel = that.collection.find( function( model ) {
							return model.get( 'cid' ) == containerCID;
						} );
						containerModel.attributes.params.admin_toggled = 'yes';
						jQuery( this ).addClass( 'fusion-builder-section-folded' );
						jQuery( this ).find( '.fusion-builder-toggle > span' ).removeClass( 'dashicons-arrow-up' ).addClass( 'dashicons-arrow-down' );
					});

				} else {
					toggleButton.addClass( 'dashicons-arrow-up' ).removeClass( 'dashicons-arrow-down' );
					jQuery( '.fusion_builder_container' ).each( function() {
						var containerModel;

						containerCID   = jQuery( this ).find( '.fusion-builder-data-cid' ).data( 'cid' );
						containerModel = that.collection.find( function( model ) {
							return model.get( 'cid' ) == containerCID;
						} );
						containerModel.attributes.params.admin_toggled = 'no';
						jQuery( this ).removeClass( 'fusion-builder-section-folded' );
						jQuery( this ).find( '.fusion-builder-toggle > span' ).addClass( 'dashicons-arrow-up' ).removeClass( 'dashicons-arrow-down' );
					});
				}

				FusionPageBuilderEvents.trigger( 'fusion-element-edited' );
			},

			showSavedElements: function( elementType, container ) {

				var data = jQuery( '#fusion-builder-layouts-' + elementType ).find( '.fusion-page-layouts' ).clone(),
				    postId;
				data.find( 'li' ).each( function() {
					postId = jQuery( this ).find( '.fusion-builder-demo-button-load' ).attr( 'data-post-id' );
					jQuery( this ).find( '.fusion-layout-buttons' ).remove();
					jQuery( this ).find( 'h4' ).attr( 'class', 'fusion_module_title' );
					jQuery( this ).attr( 'data-layout_id', postId );
					jQuery( this ).addClass( 'fusion_builder_custom_' + elementType + '_load' );
					if ( '' !== jQuery( this ).attr( 'data-layout_type' ) ) {
						jQuery( this ).addClass( 'fusion-element-type-' + jQuery( this ).attr( 'data-layout_type' ) );
					}
				} );
				container.append( '<div id="fusion-loader"><span class="fusion-builder-loader"></span></div>' );
				container.append( '<ul class="fusion-builder-all-modules">' + data.html() + '</div>' );
			},

			rangeOptionPreview: function( view ) {
				 view.find( '.fusion-range-option' ).each( function() {
					$( this ).next().html( $( this ).val() );
					$( this ).on( 'change mousemove', function() {
						$( this ).next().html( $( this ).val() );
					} );
				} );
			},

			checkOptionDependency: function( view, thisEl, parentValues ) {
				var
					$dependencies = {},
					$currentVal,
					$dependencyIds = '',
					$currentId,
					$optionId,
					$passed,
					$passedArray,
					dividerType,
					upAndDown,
					centerOption;

				function doesTestPass( current, comparison, operator ) {
					$passed = false;
					if ( '==' == operator && current == comparison ) {
						$passed = true;
					}
					if ( '!=' == operator && current != comparison ) {
						$passed = true;
					}
					if ( '>' == operator && current > comparison ) {
						$passed = true;
					}
					if ( '<' == operator && current < comparison ) {
						$passed = true;
					}
					return $passed;
				}

				// Special check for section separator.
				if ( 'undefined' !== typeof view.shortcode && 'fusion_section_separator' === view.shortcode ) {
					dividerType = thisEl.find( '#divider_type' );
					upAndDown = dividerType.parents( 'ul' ).find( 'li[data-option-id="divider_candy"]' ).find( '.divider_candy' ).find( '.ui-button[data-value="bottom,top"]' );
					centerOption = dividerType.parents( 'ul' ).find( 'li[data-option-id="divider_position"]' ).find( '.divider_position' ).find( '.ui-button[data-value="center"]' );

					if ( 'triangle' !== dividerType.val() ) {
						upAndDown.hide();
					} else {
						upAndDown.show();
					}

					if ( 'bigtriangle' !== dividerType.val() ) {
						centerOption.hide();
					} else {
						centerOption.show();
					}

					dividerType.on( 'change paste keyup', function() {

						if ( 'triangle' !== jQuery( this ).val() ) {
							upAndDown.hide();
						} else {
							upAndDown.show();
						}

						if ( 'bigtriangle' !== jQuery( this ).val() ) {
							centerOption.hide();
							if ( centerOption.hasClass( 'ui-state-active' ) ) {
								centerOption.prev().click();
							}
						} else {
							centerOption.show();
						}

					});
				}

				// Initial checks and create helper objects.
				jQuery.each( view.params, function( index, value ) {
					if ( 'undefined' !== typeof value.dependency ) {
						$optionId = index;
						$passedArray = [];

						// Check each dependency for this option
						jQuery.each( value.dependency, function( index, dependency ) {

							// Create IDs of fields to check for.
							if ( 0 > $dependencyIds.indexOf( '#' + dependency.element ) ) {
								$dependencyIds += ', #' + dependency.element;
							}

							// If option has dependency add to check array.
							if ( 'undefined' == typeof $dependencies[dependency.element] ) {
								$dependencies[dependency.element] = [ { option: $optionId, or: value.or } ];
							} else {
								$dependencies[dependency.element].push( { option: $optionId, or: value.or } );
							}

							// If parentValues is an object and this is a parent dependency, then we should take value from there.
							if ( 'parent_' === dependency.element.substring( 0, 7 ) ) {
								if ( 'object' === typeof parentValues && parentValues[ dependency.element.replace( dependency.element.substring( 0, 7 ), '' ) ] ) {
									$currentVal = parentValues[ dependency.element.replace( dependency.element.substring( 0, 7 ), '' ) ];
								} else {
									$currentVal = '';
								}
							} else {
								$currentVal = thisEl.find( '#' + dependency.element ).val();
							}
							$passedArray.push( doesTestPass( $currentVal, dependency.value, dependency.operator ) );
						});

						// Check if it passes for regular "and" test.
						if ( -1 === $.inArray( false, $passedArray ) && 'undefined' === typeof value.or ) {
							thisEl.find( '#' + index ).parents( '.fusion-builder-option' ).fadeIn( 300 );

						// Check if it passes "or" test.
						} else if (  -1 !== $.inArray( true, $passedArray ) && 'undefined' !== typeof value.or ) {
							thisEl.find( '#' + index ).parents( '.fusion-builder-option' ).fadeIn( 300 );

						// If it fails.
						} else {
							thisEl.find( '#' + index ).parents( '.fusion-builder-option' ).hide();
						}
					}
				});

				// Listen for changes to options which other are dependent on.
				if ( $dependencyIds.length ) {
					thisEl.on( 'change paste keyup', $dependencyIds.substring( 2 ), function() {
						$currentId    = jQuery( this ).attr( 'id' );

						// Loop through each option id that is dependent on this option.
						jQuery.each( $dependencies[ $currentId ], function( index, value ) {
							$passedArray = [];

							// Check each dependency for that id.
							jQuery.each( view.params[value.option].dependency, function( index, dependency ) {

								// If parentValues is an object and this is a parent dependency, then we should take value from there.
								if ( 'parent_' === dependency.element.substring( 0, 7 ) ) {
									if ( 'object' === typeof parentValues && parentValues[ dependency.element.replace( dependency.element.substring( 0, 7 ), '' ) ] ) {
										$currentVal = parentValues[ dependency.element.replace( dependency.element.substring( 0, 7 ), '' ) ];
									} else {
										$currentVal = '';
									}
								} else {
									$currentVal = thisEl.find( '#' + dependency.element ).val();
								}
								$passedArray.push( doesTestPass( $currentVal, dependency.value, dependency.operator ) );
							});

							// Check if it passes for regular "and" test.
							if ( -1 === $.inArray( false, $passedArray ) && 'undefined' === typeof value.or ) {
								thisEl.find( '#' + value.option ).parents( '.fusion-builder-option' ).fadeIn( 300 );

							// Check if it passes "or" test.
							} else if (  -1 !== $.inArray( true, $passedArray ) && 'undefined' !== typeof value.or ) {
								thisEl.find( '#' + value.option ).parents( '.fusion-builder-option' ).fadeIn( 300 );

							// If it fails.
							} else {
								thisEl.find( '#' + value.option ).parents( '.fusion-builder-option' ).hide();
							}
						});

					});
				}

			}

		} );

		// Instantiate Builder App
		FusionPageBuilderApp = new FusionPageBuilder.AppView( {
			model: FusionPageBuilder.Element,
			collection: FusionPageBuilderElements
		} );

		// Stores 'active' value in fusion_builder_status meta key if builder is activa
		$useBuilderMetaField = $( '#fusion_use_builder' );

		// Fusion Builder Toggle Button
		$toggleBuilderButton = $( '#fusion_toggle_builder' );

		// Fusion Builder div
		$builder = $( '#fusion_builder_layout' );

		// Main wrap for the main editor
		$mainEditorWrapper = $( '#fusion_main_editor_wrap' );

		// Show builder div if it's activated
		if ( $toggleBuilderButton.hasClass( 'fusion_builder_is_active' ) ) {
			$builder.show();
			FusionPageBuilderApp.builderActive = true;

			// Sticky header
			fusionBuilderEnableStickyHeader();
		}

		// Builder toggle button event
		$toggleBuilderButton.click( function( event ) {

			var isBuilderUsed;

			if ( event ) {
				event.preventDefault();
			}

			isBuilderUsed = $( this ).hasClass( 'fusion_builder_is_active' );

			if ( isBuilderUsed ) {
				fusionBuilderDeactivate( $( this ) );
				FusionPageBuilderApp.builderActive = false;
			} else {
				fusionBuilderActivate( $( this ) );
				FusionPageBuilderApp.builderActive = true;
			}
		} );

		// Sticky builder header
		function fusionBuilderEnableStickyHeader() {
			var builderHeader = document.getElementById( 'fusion_builder_controls' );
			fusionBuilderStickyHeader( builderHeader, jQuery( '#wpadminbar' ).height() );
		}

		function fusionBuilderActivate( toggle ) {

			fusionBuilderReset();

			FusionPageBuilderApp.initialBuilderLayout();

			$useBuilderMetaField.val( 'active' );

			$builder.show();

			toggle.text( toggle.data( 'editor' ) );

			$mainEditorWrapper.toggleClass( 'fusion_builder_hidden' );

			toggle.toggleClass( 'fusion_builder_is_active' );

			// Sticky header
			fusionBuilderEnableStickyHeader();

		}

		function fusionBuilderReset() {

			// Clear all models and views
			FusionPageBuilderElements.reset();
			FusionPageBuilderViewManager.set( 'elementCount', 0 );
			FusionPageBuilderViewManager.set( 'views', {} );

			// Clear layout
			$( '#fusion_builder_container' ).html( '' );
		}

		function fusionBuilderDeactivate( toggle ) {
			var $body,
			    pagePosition;

			fusionBuilderReset();

			$body        = $( 'body' );
			pagePosition = 0;

			window.wpActiveEditor = 'content';

			$useBuilderMetaField.val( 'off' );

			$builder.hide();

			$toggleBuilderButton.text( $toggleBuilderButton.data( 'builder' ) ).toggleClass( 'fusion_builder_is_active' );

			$mainEditorWrapper.toggleClass( 'fusion_builder_hidden' );

			FusionPageBuilderApp.$el.find( '.fusion_builder_container' ).remove();

			pagePosition = $body.scrollTop();
			jQuery( 'html, body' ).scrollTop( pagePosition + 1 );

		}

		// Remove preview image.
		$container = $( 'body' );
		$container.on( 'click', '.upload-image-remove', function( event ) {
			var $field,
			    $preview,
			    $upload;

			if ( event ) {
				event.preventDefault();
			}

			$field   = $( this ).parents( '.fusion-builder-option-container' ).find( '.fusion-builder-upload-field' );
			$preview = $( this ).parents( '.fusion-builder-option-container' ).find( '.fusion-builder-upload-preview' );
			$upload  = $( this ).parents( '.fusion-builder-option-container' ).find( '.fusion-builder-upload-button' );

			$field.val( '' ).trigger( 'change' );
			$upload.val( 'Upload Image' );
			$preview.remove();

			if ( $( '#image_id' ).length ) {
				$( '#image_id' ).val( '' );
			}
			jQuery( this ).remove();
		} );

		// History steps.
		$( '.fusion-builder-history-list li' ).live( 'click', function( event ) {
			var step;

			if ( event ) {
				event.preventDefault();
			}

			step = $( event.currentTarget ).data( 'state-id' );
			fusionHistoryManager.historyStep( step );
		} );

		// Element option tabs.
		$( '.fusion-tabs-menu a' ).live( 'click', function( event ) {

			var tab;

			if ( event ) {
				event.preventDefault();
			}

			$( this ).parent().addClass( 'current' ).removeClass( 'inactive' );
			$( this ).parent().siblings().removeClass( 'current' ).addClass( 'inactive' );
			tab = $( this ).attr( 'href' );
			$( this ).parents( '.fusion-builder-modal-container' ).find( '.fusion-tab-content' ).not( tab ).css( 'display', 'none' );
			$( '.fusion-builder-layouts-tab' ).hide();

			if ( '#design' === tab && $( this ).parents( '.fusion-builder-modal-container' ).length ) {
				$( this ).parents( '.fusion-builder-modal-container' ).find( tab ).fadeIn( 'fast' );
			} else {
				$( tab ).fadeIn( 'fast' );
			}
		} );

		// Close modal on overlick click.
		$( '.fusion_builder_modal_overlay' ).live( 'click', function() {
			FusionPageBuilderEvents.trigger( 'fusion-remove-modal-view' );
			FusionPageBuilderEvents.trigger( 'fusion-close-modal' );
		} );

		// Close nested modal on overlick click.
		$( '.fusion_builder_modal_inner_row_overlay' ).live( 'click', function() {
			FusionPageBuilderEvents.trigger( 'fusion-close-inner-modal' );
			FusionPageBuilderEvents.trigger( 'fusion-hide-library' );
		} );

		// Demo select.
		$selectedDemo = $( '.fusion-builder-demo-select' ).val();
		$( '#fusion-builder-layouts-demos .demo-' + $selectedDemo ).show();

		$( '.fusion-builder-demo-select' ).live( 'change', function( event ) {
			$selectedDemo = $( '.fusion-builder-demo-select' ).val();
			$( '#fusion-builder-layouts-demos .fusion-page-layouts' ).hide();
			$( '#fusion-builder-layouts-demos .demo-' + $selectedDemo ).show();
		} );

		// Iconpicker select/deselect handler.
		FusionIconPickHandler.live( 'click', function( e ) {

			var iconWithPrefix,
			    fontName;

			e.preventDefault();

			iconWithPrefix  = $( this ).find( 'i' ).attr( 'class' );
			fontName		= $( this ).find( 'i' ).attr( 'data-name' );

			if ( $( this ).hasClass( 'selected-element' ) ) {
				$( this ).find( 'i' ).parent().parent().find( '.selected-element' ).removeClass( 'selected-element' );
				$( this ).find( 'i' ).parent().parent().parent().find( '.fusion-iconpicker-input' ).attr( 'value', '' ).trigger( 'change' );

			} else {

				$( this ).find( 'i' ).parent().parent().find( '.selected-element' ).removeClass( 'selected-element' );
				$( this ).find( 'i' ).parent().addClass( 'selected-element' );
				$( this ).find( 'i' ).parent().parent().parent().find( '.fusion-iconpicker-input' ).attr( 'value', fontName ).trigger( 'change' );
			}
		} );

		// Open shortcode generator.
		$( '#qt_content_fusion_shortcodes_text_mode, #qt_excerpt_fusion_shortcodes_text_mode' ).live( 'click', function() {
			openShortcodeGenerator( $( this ) );
		} );

		// Save layout template on return key.
		$( '#new_template_name' ).keydown( function( e ) {
			if ( 13 == e.keyCode ) {
				e.preventDefault();
				e.stopPropagation();
				FusionPageBuilderEvents.trigger( 'fusion-save-layout' );
				return false;
			} else {
				return true;
			}
		} );

		// Save elements on return key.
		$( 'body' ).on( 'keydown', '#fusion-builder-save-element-input', function( e ) {
			if ( 13 == e.keyCode ) {
				e.preventDefault();
				e.stopPropagation();
				$( '.fusion-builder-element-button-save' ).trigger( 'click' );
				return false;
			} else {
				return true;
			}
		} );
	} );
} )( jQuery );
