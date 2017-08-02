var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Nested Column View
		FusionPageBuilder.NestedColumnView = window.wp.Backbone.View.extend( {

			template: FusionPageBuilder.template( $( '#fusion-builder-inner-column-template' ).html() ),

			events: {
				'click .fusion-builder-add-element': 'addModule',
				'click .fusion-builder-settings-column': 'showSettings'
			},

			initialize: function() {
				this.$el.attr( 'data-cid', this.model.get( 'cid' ) );
				this.$el.attr( 'data-column-size', this.model.get( 'layout' ) );
			},

			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );
				this.sortableElements();

				return this;
			},

			sortableElements: function( event ) {
				var thisEl = this;
				this.$el.sortable( {
					items: '.fusion_module_block',
					connectWith: '.fusion-builder-column-inner',
					cancel: '.fusion-builder-settings, .fusion-builder-clone, .fusion-builder-remove, .fusion-builder-add-element, .fusion-builder-insert-column, .fusion-builder-save-module-dialog',
					tolerance: 'pointer',

					update: function( event, ui ) {
						var $moduleBlock = $( ui.item ),
						    moduleCID    = ui.item.data( 'cid' ),
						    model        = thisEl.collection.find( function( model ) {
								return model.get( 'cid' ) == moduleCID;
						    } );

						// If column is empty add before "Add Element" button
						if ( $( ui.item ).closest( event.target ).length && 1 === $( event.target ).find( '.fusion_module_block' ).length ) {
							$moduleBlock.insertBefore( $( event.target ).find( '.fusion-builder-add-element' ) );
						}

						// Moved the element within the same column
						if ( model.get( 'parent' ) === thisEl.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {

						// Moved the element to a different column
						} else {
							model.set( 'parent', thisEl.model.attributes.cid );
						}

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.moved + ' ' + fusionAllElements[model.get( 'element_type' )].name + ' ' + fusionBuilderText.element;
						FusionPageBuilderEvents.trigger( 'fusion-element-sorted' );
					}

				} );
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
			},

			removeColumn: function( event ) {
				var modules;

				if ( event ) {
					event.preventDefault();
				}

				modules = FusionPageBuilderViewManager.getChildViews( this.model.get( 'cid' ) );

				_.each( modules, function( module ) {
					module.removeElement();
				} );

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();

				// If the column is deleted manually
				if ( event ) {
					FusionPageBuilderEvents.trigger( 'fusion-element-removed' );
				}
			},

			addModule: function( event ) {
				var view,
				    $eventTarget,
				    $addModuleButton;

				if ( event ) {
					event.preventDefault();
					event.stopPropagation();
				}

				FusionPageBuilderApp.innerColumn = 'true';
				FusionPageBuilderApp.parentColumnId = this.model.get( 'cid' );

				$eventTarget     = $( event.target );
				$addModuleButton = $eventTarget.is( 'span' ) ? $eventTarget.parent( '.fusion-builder-add-element' ) : $eventTarget;

				if ( ! $addModuleButton.parent().is( event.delegateTarget ) ) {
					return;
				}

				view = new FusionPageBuilder.ModalView( {
					model: this.model,
					collection: this.collection,
					attributes: {
						'data-modal_view': 'element_library'
					},
					view: this
				} );

				$( 'body' ).append( view.render().el );
			}

		} );

	} );

} )( jQuery );
