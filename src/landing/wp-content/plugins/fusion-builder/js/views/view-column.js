var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Column View
		FusionPageBuilder.ColumnView = window.wp.Backbone.View.extend( {

			template: FusionPageBuilder.template( $( '#fusion-builder-column-template' ).html() ),

			events: {
				'click .fusion-builder-add-element:not(.fusion-builder-column-inner .fusion-builder-add-element)': 'addModule',
				'click .fusion-builder-settings-column:not(.fusion-builder-column-inner .fusion-builder-settings-column)': 'showSettings',
				'click .fusion-builder-resize-column:not(.fusion-builder-column-inner .fusion-builder-resize-column)': 'columnSizeDialog',
				'click .column-size:not(.fusion-builder-column-inner .column-size)': 'columnSize',
				'click .fusion-builder-clone-column:not(.fusion-builder-column-inner .fusion-builder-clone-column)': 'cloneColumn',
				'click .fusion-builder-remove-column:not(.fusion-builder-column-inner .fusion-builder-remove-column)': 'removeColumn',
				'click .fusion-builder-save-column-dialog:not(.fusion-builder-column-inner .fusion-builder-save-column-dialog)': 'saveColumnDialog'
			},

			initialize: function() {
				this.$el.attr( 'data-cid', this.model.get( 'cid' ) );
				this.$el.attr( 'data-column-size', this.model.get( 'layout' ) );
			},

			render: function() {
				var columnSize,
				    fractionSize;

				this.$el.html( this.template( this.model.toJSON() ) );

				this.sortableElements();

				// Add active column size CSS class
				columnSize = this.model.get( 'layout' );
				this.$el.find( '.column-size-' + columnSize ).addClass( 'active-size' );

				// Set column size fraction
				fractionSize = columnSize.replace( '_', '/' );
				this.$el.find( '.fusion-builder-resize-column' ).text( fractionSize );

				return this;
			},

			sortableElements: function( event ) {
				var thisEl = this;
				this.$el.sortable( {
					items: '.fusion_module_block:not(.fusion_builder_row_inner .fusion_module_block), .fusion_builder_row_inner',
					connectWith: '.fusion-builder-column-outer',
					cancel: '.fusion-builder-settings, .fusion-builder-clone, .fusion-builder-remove, .fusion-builder-add-element, .fusion-builder-insert-column, .fusion-builder-save-module-dialog, .fusion-builder-remove-inner-row, .fusion-builder-save-inner-row-dialog-button, .fusion-builder-remove-inner-row, .fusion_builder_row_inner .fusion-builder-row-content',
					tolerance: 'pointer',

					over: function( event, ui ) {

						// Move sortable palceholder above +Element button for empty columns.
						if ( 1 === $( event.target ).find( '.fusion_module_block, .fusion_builder_row_inner' ).length ) {
							$( event.target ).find( '.ui-sortable-placeholder' ).insertBefore( $( event.target ).find( '.fusion-builder-add-element' ) );
						}
					},

					update: function( event, ui ) {
						var $moduleBlock = $( ui.item ),
						    moduleCID    = ui.item.data( 'cid' ),
						    model        = thisEl.collection.find( function( model ) {
								return model.get( 'cid' ) == moduleCID;
						    } );

						// If column is empty add element before "Add Element" button
						if ( $( ui.item ).closest( event.target ).length && 1 === $( event.target ).find( '.fusion_module_block, .fusion_builder_row_inner' ).length ) {

							$moduleBlock.insertBefore( $( event.target ).find( '> .fusion-builder-add-element' ) );
						}

						// Moved the element within the same column
						if ( model.get( 'parent' ) === thisEl.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {

						// Moved the element to a different column
						} else {
							model.set( 'parent', thisEl.model.attributes.cid );
						}

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.moved + ' ' + fusionAllElements[ model.get( 'element_type' ) ].name + ' ' + fusionBuilderText.element;
						FusionPageBuilderEvents.trigger( 'fusion-element-sorted' );
					}

				} );
			},

			saveColumnDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				FusionPageBuilderApp.showLibrary();

				$( '#fusion-builder-layouts-columns-trigger' ).click();

				$( '#fusion-builder-layouts-columns .fusion-builder-layouts-header-element-fields' ).append( '<div class="fusion-save-element-fields"><input type="text" value="" id="fusion-builder-save-element-input" class="fusion-builder-save-element-input" placeholder="' + fusionBuilderText.enter_name + '" /><a href="#" class="fusion-builder-save-column fusion-builder-element-button-save" data-element-cid="' + this.model.get( 'cid' ) + '">' + fusionBuilderText.save_column + '</a></div>' );
			},

			// Save column
			saveElement: function( event ) {
				var $thisColumn      = this.$el,
				    elementContent   = this.getColumnContent( $thisColumn ),
				    elementName      = $( '#fusion-builder-save-element-input' ).val(),
				    layoutsContainer = $( '#fusion-builder-layouts-columns .fusion-page-layouts' ),
				    emptyMessage     = $( '#fusion-builder-layouts-columns .fusion-page-layouts .fusion-empty-library-message' );

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
						url: fusionBuilderConfig.ajaxurl,
						dataType: 'json',
						data: {
							action: 'fusion_builder_save_layout',
							fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
							fusion_layout_name: elementName,
							fusion_layout_content: elementContent,
							fusion_layout_post_type: 'fusion_element',
							fusion_layout_new_cat: 'columns'
						},
						complete: function( data ) {
							FusionPageBuilderApp.layoutIsSaving = false;
							layoutsContainer.prepend( data.responseText );
							$( '.fusion-save-element-fields' ).remove();
							emptyMessage.hide();
						}
					} );

				} else {
					alert( fusionBuilderText.please_enter_element_name );
				}
			},

			getColumnContent: function( $thisColumn ) {
				var shortcode    = '',
				    columnCID    = $thisColumn.data( 'cid' ),
				    module       = FusionPageBuilderElements.findWhere( { cid: columnCID } ),
				    columnParams = {},
				    ColumnAttributesCheck;

				_.each( module.get( 'params' ), function( value, name ) {
					if ( 'undefined' === value ) {
						columnParams[ name ] = '';
					} else {
						columnParams[ name ] = value;
					}

				} );

				// Legacy support for new column options
				ColumnAttributesCheck = {
					min_height: '',
					last: 'no',
					hover_type: 'none',
					link: '',
					border_position: 'all'
				};

				_.each( ColumnAttributesCheck, function( value, name ) {

					if ( 'undefined' === typeof columnParams[ name ] ) {
						columnParams[ name ] = value;
					}

				} );

				// Build column shortcode
				shortcode += '[fusion_builder_column type="' + module.get( 'layout' ) + '"';

				_.each( columnParams, function( value, name ) {

					shortcode += ' ' + name + '="' + value + '"';

				});

				shortcode += ']';

				// Find elements inside this column
				$thisColumn.find( '.fusion_builder_column_element:not(.fusion-builder-column-inner .fusion_builder_column_element)' ).each( function() {
					var $thisRowInner;

					// Find standard elements
					if ( $( this ).hasClass( 'fusion_module_block' ) ) {
						shortcode += FusionPageBuilderApp.generateElementShortcode( $( this ), false );

					// Find inner rows
					} else {
						$thisRowInner = $( this );
						shortcode += '[fusion_builder_row_inner]';

						// Find nested columns
						$thisRowInner.find( '.fusion-builder-column-inner' ).each( function() {
							var $thisColumnInner  = $( this ),
							    columnInnerCID    = $thisColumnInner.data( 'cid' ),
							    module            = FusionPageBuilderElements.findWhere( { cid: columnInnerCID } ),
							    innerColumnParams = {},
							    innerColumnAttributesCheck;

							_.each( module.get( 'params' ), function( value, name ) {

								if ( 'undefined' === value ) {
									innerColumnParams[ name ] = '';
								} else {
									innerColumnParams[ name ] = value;
								}

							} );

							// Legacy support for new column options
							innerColumnAttributesCheck = {
								min_height: '',
								last: 'no',
								hover_type: 'none',
								link: '',
								border_position: 'all'
							};

							_.each( innerColumnAttributesCheck, function( value, name ) {

								if ( 'undefined' === typeof innerColumnParams[ name ] ) {
									innerColumnParams[ name ] = value;
								}

							} );

							// Build nested column shortcode
							shortcode += '[fusion_builder_column_inner type="' + module.get( 'layout' ) + '"';

								_.each( innerColumnParams, function( value, name ) {

									shortcode += ' ' + name + '="' + value + '"';

								});

								shortcode += ']';

								// Find elements within nested columns
								$thisColumnInner.find( '.fusion_module_block' ).each( function() {
									shortcode += FusionPageBuilderApp.generateElementShortcode( $( this ), false );
								} );

							shortcode += '[/fusion_builder_column_inner]';

						} );

						shortcode += '[/fusion_builder_row_inner]';
					}

				} );

				shortcode += '[/fusion_builder_column]';

				return shortcode;
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
					if ( 'fusion_builder_row' === module.model.get( 'type' ) || 'fusion_builder_row_inner' === module.model.get( 'type' ) ) {
						module.removeRow();
					} else {
						module.removeElement();
					}
				} );

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();

				// If the column is deleted manually
				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.deleted + ' ' + fusionBuilderText.column;

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

				FusionPageBuilderApp.innerColumn = 'false';
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
			},

			cloneColumn: function( event ) {
				var columnAttributes = $.extend( true, {}, this.model.attributes ),
				    $thisColumn;

				if ( event ) {
					event.preventDefault();
				}

				columnAttributes.created       = 'manually';
				columnAttributes.cid           = FusionPageBuilderViewManager.generateCid();
				columnAttributes.targetElement = this.$el;
				columnAttributes.cloned        = true;

				FusionPageBuilderApp.collection.add( columnAttributes );

				// Parse column elements
				$thisColumn = this.$el;
				$thisColumn.find( '.fusion_builder_column_element:not(.fusion-builder-column-inner .fusion_builder_column_element)' ).each( function() {
					var $thisModule,
					    moduleCID,
					    module,
					    elementAttributes,
					    $thisInnerRow,
					    innerRowCID,
					    innerRowView;

					// Standard element
					if ( $( this ).hasClass( 'fusion_module_block' ) ) {
						$thisModule = $( this );
						moduleCID = 'undefined' === typeof $thisModule.data( 'cid' ) ? $thisModule.find( '.fusion-builder-data-cid' ).data( 'cid' ) : $thisModule.data( 'cid' );

						// Get model from collection by cid
						module = FusionPageBuilderElements.find( function( model ) {
							return model.get( 'cid' ) == moduleCID;
						} );

						// Clone model attritubes
						elementAttributes         = $.extend( true, {}, module.attributes );

						elementAttributes.created = 'manually';
						elementAttributes.cid     = FusionPageBuilderViewManager.generateCid();
						elementAttributes.parent  = columnAttributes.cid;
						elementAttributes.from    = 'fusion_builder_column';

						FusionPageBuilderApp.collection.add( elementAttributes );

					// Inner row/nested element
					} else if ( $( this ).hasClass( 'fusion_builder_row_inner' ) ) {
						$thisInnerRow = $( this );
						innerRowCID = 'undefined' === typeof $thisInnerRow.data( 'cid' ) ? $thisInnerRow.find( '.fusion-builder-data-cid' ).data( 'cid' ) : $thisInnerRow.data( 'cid' );

						innerRowView = FusionPageBuilderViewManager.getView( innerRowCID );

						// Clone inner row
						if ( 'undefined' !== typeof innerRowView ) {
							innerRowView.cloneNestedRow( '', columnAttributes.cid );
						}
					}

				} );

				// If column is cloned manually
				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.cloned + ' ' + fusionBuilderText.column;

					FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
				}
			},

			columnSizeDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				this.$el.find( '.column-sizes' ).toggle();
			},

			columnSize: function( event ) {
				var $thisEl = $( event.currentTarget ),

					// Get current column size
					size = this.model.get( 'layout' ),

					// New column size
					newSize = $thisEl.attr( 'data-column-size' ),

					// Fraction size
					fractionSize = '';

					if ( event ) {
						event.preventDefault();
					}

				if ( 'undefined' !== typeof ( newSize ) ) {

					// Set new size
					this.model.set( 'layout', newSize );

					// Change css size class
					this.$el.removeClass( 'fusion-builder-column-' + size );
					this.$el.addClass( 'fusion-builder-column-' + newSize );

					fractionSize = newSize.replace( '_', '/' );

					this.$el.find( '.fusion-builder-resize-column' ).text( fractionSize );
					this.$el.find( '.column-sizes' ).hide();
					this.$el.find( '.column-sizes .column-size' ).removeClass( 'active-size' );
					this.$el.find( '.column-size-' + newSize ).addClass( 'active-size' );

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.resized_column + ' ' + fractionSize;

					FusionPageBuilderEvents.trigger( 'fusion-element-edited' );
				}
			}

		} );

	} );

} )( jQuery );
