var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Row View
		FusionPageBuilder.RowView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_row',

			template: FusionPageBuilder.template( $( '#fusion-builder-row-template' ).html() ),

			events: {
				'click .fusion-builder-insert-column': 'displayColumnsOptions'
			},

			initialize: function() {
			},

			render: function() {

				this.$el.html( this.template( this.model.toJSON() ) );

				this.sortableColumns();

				// Show column settings when adding a new row
				if ( 'manually' !== this.model.get( 'created' ) ) {
					this.$el.find( '.fusion-builder-insert-column' ).trigger( 'click' );
				}

				return this;
			},

			sortableColumns: function() {
				var thisEl     = this,
				    selectedEl = thisEl.$el.find( '.fusion-builder-row-container' ),
				    cid        = this.model.get( 'cid' );

				selectedEl.sortable( {
					helper: 'clone',
					cancel: '.fusion-builder-settings, .fusion-builder-clone, .fusion-builder-remove, .fusion-builder-section-add, .fusion-builder-add-element, .fusion-builder-insert-column, #fusion_builder_controls, .fusion-builder-save-column, .fusion-builder-resize-column, .column-sizes, .fusion-builder-save-column-dialog, .fusion-builder-save-inner-row-dialog-button, .fusion-builder-remove-inner-row, .fusion_builder_row_inner .fusion-builder-row-content',
					items: '.fusion-builder-column-outer',
					connectWith: '.fusion-builder-row-container',
					tolerance: 'pointer',

					update: function( event, ui ) {
						var elementCID = ui.item.data( 'cid' ),
						    model     = thisEl.collection.find( function( model ) {
								return model.get( 'cid' ) == elementCID;
						    } );

						// Moved column within the same section/row
						if ( model.get( 'parent' ) === thisEl.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {

						// Moved column to a different section/row
						} else {
							model.set( 'parent', thisEl.model.attributes.cid );
						}

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.moved_column;

						FusionPageBuilderEvents.trigger( 'fusion-element-sorted' );
					}

				} ).disableSelection();
			},

			displayColumnsOptions: function( event ) {

				var view;

				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderApp.parentRowId = this.model.get( 'cid' );

				view = new FusionPageBuilder.ModalView( {
					model: this.model,
					collection: this.collection,
					attributes: {
						'data-modal_view': 'column_library'
					},
					view: this
				} );

				$( 'body' ).append( view.render().el );

			},

			removeRow: function( event, force ) {

				var columns;

				if ( event ) {
					event.preventDefault();
				}

				columns = FusionPageBuilderViewManager.getChildViews( this.model.get( 'cid' ) );

				// Remove all columns
				_.each( columns, function( column ) {
					column.removeColumn();
				} );

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();

				if ( event ) {
					FusionPageBuilderEvents.trigger( 'fusion-element-removed' );
				}
			}

		} );

	} );

} )( jQuery );
