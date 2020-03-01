var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Element View
		FusionPageBuilder.ElementView = window.wp.Backbone.View.extend( {

			className: 'fusion_module_block fusion_builder_column_element',
			template: FusionPageBuilder.template( $( '#fusion-builder-block-module-template' ).html() ),
			events: {
				'click .fusion-builder-settings': 'showSettings',
				'click .fusion-builder-clone-module': 'cloneElement',
				'click .fusion-builder-remove': 'removeElement',
				'click .fusion-builder-save-module-dialog': 'saveElementDialog'
			},

			initialize: function() {
				this.elementIsCloning = false;
			},

			render: function() {
				this.$el.html( this.template( this.model.attributes ) );
				return this;
			},

			saveElementDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				FusionPageBuilderApp.showLibrary();

				// Change to elements tab
				$( '#fusion-builder-layouts-elements-trigger' ).click();

				$( '#fusion-builder-layouts-elements .fusion-builder-layouts-header-element-fields' ).append( '<div class="fusion-save-element-fields"><input type="text" value="" id="fusion-builder-save-element-input" class="fusion-builder-save-element-input" placeholder="' + fusionBuilderText.enter_name + '" /><a href="#" class="fusion-builder-save-column fusion-builder-element-button-save" data-element-cid="' + this.model.get( 'cid' ) + '">' + fusionBuilderText.save_element + '</a></div>' );
			},

			saveElement: function( event ) {

				var thisEl           = this.$el,
				    elementContent   = this.getElementContent(),
				    elementName      = $( '#fusion-builder-save-element-input' ).val(),
				    layoutsContainer = $( '#fusion-builder-layouts-elements .fusion-page-layouts' ),
				    emptyMessage     = $( '#fusion-builder-layouts-elements .fusion-page-layouts .fusion-empty-library-message' );

				if ( event ) {
					event.preventDefault();
				}

				if ( true === FusionPageBuilderApp.layoutIsSaving ) {
					return;
				}
				FusionPageBuilderApp.layoutIsSaving = true;

				if ( '' !== elementName ) {

					$.ajax( {
						type: 'POST',
						url: FusionPageBuilderApp.ajaxurl,
						dataType: 'json',
						data: {
							action: 'fusion_builder_save_layout',
							fusion_load_nonce: FusionPageBuilderApp.fusion_load_nonce,
							fusion_layout_name: elementName,
							fusion_layout_content: elementContent,
							fusion_layout_post_type: 'fusion_element',
							fusion_layout_new_cat: 'elements'
						},
						complete: function( data ) {
							FusionPageBuilderApp.layoutIsSaving = false;
							layoutsContainer.prepend( data.responseText );
							$( '.fusion-save-element-fields' ).remove();
							emptyMessage.hide();
						}
					});

				} else {
					alert( fusionBuilderText.please_enter_element_name );
				}
			},

			getElementContent: function() {
				return FusionPageBuilderApp.generateElementShortcode( this.$el, false );
			},

			removeElement: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				// Remove element view
				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				// Destroy element model
				this.model.destroy();

				this.remove();

				// If element is removed manually
				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.deleted + ' ' + fusionAllElements[ this.model.get( 'element_type' ) ].name + ' ' + fusionBuilderText.element;

					FusionPageBuilderEvents.trigger( 'fusion-element-removed' );
				}

			},

			cloneElement: function( event, parentCID ) {
				var elementAttributes;

				if ( event ) {
					event.preventDefault();
				}

				if ( true === this.elementIsCloning ) {
					return;
				} else {
					this.elementIsCloning = true;
				}

				elementAttributes = $.extend( true, {}, this.model.attributes );
				elementAttributes.created = 'manually';
				elementAttributes.cid = FusionPageBuilderViewManager.generateCid();
				elementAttributes.targetElement = this.$el;
				if ( 'undefined' !== elementAttributes.from ) {
					delete elementAttributes.from;
				}

				if ( parentCID ) {
					elementAttributes.parent = parentCID;
				}

				FusionPageBuilderApp.collection.add( elementAttributes );

				if ( ! parentCID ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.cloned + ' ' + fusionAllElements[ this.model.get( 'element_type' ) ].name + ' ' + fusionBuilderText.element;
				}

				this.elementIsCloning = false;

				if ( event ) {
					FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
				}

			},

			showSettings: function( event ) {
				var modalView,
				    viewSettings = {
						model: this.model,
						collection: this.collection,
						attributes: {
							'data-modal_view': 'element_settings'
						}
				    };

				if ( event ) {
					event.preventDefault();
				}

				modalView = new FusionPageBuilder.ModalView( viewSettings );

				$( 'body' ).append( modalView.render().el );
			}

		} );

	} );

} )( jQuery );
