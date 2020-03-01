	var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Inner Row View
		FusionPageBuilder.InnerRowView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_row_inner fusion_builder_column_element',

			template: FusionPageBuilder.template( $( '#fusion-builder-row-inner-template' ).html() ),

			events: {
				'click .fusion-builder-remove-inner-row': 'removeRow',
				'click .fusion-builder-save-inner-row-dialog-button': 'saveElementDialog',
				'click .fusion-builder-clone-inner-row': 'cloneNestedRow',
				'click .fusion-builder-inner-row-overlay': 'showInnerRowDialog',
				'click .fusion-builder-inner-row-close': 'hideInnerRowDialog',
				'click .fusion-builder-inner-row-close-icon': 'hideInnerRowDialog'
			},

			initialize: function() {
				this.$el.attr( 'data-cid', this.model.get( 'cid' ) );

				// Close modal view
				this.listenTo( FusionPageBuilderEvents, 'fusion-close-inner-modal', this.hideInnerRowDialog );
			},

			showInnerRowDialog: function( event ) {

				var thisEl = this.$el;

				if ( event ) {
					event.preventDefault();
				}

				thisEl.find( '.fusion-builder-row-content' ).show();

				$( 'body' ).addClass( 'fusion_builder_inner_row_no_scroll' ).append( '<div class="fusion_builder_modal_inner_row_overlay"></div>' );
			},

			hideInnerRowDialog: function( event ) {

				var thisEl             = this.$el,
				    innerColumnsString = '';

				if ( event ) {
					event.preventDefault();
				}

				thisEl.find( '.fusion-builder-row-content' ).hide();

				$( 'body' ).removeClass( 'fusion_builder_inner_row_no_scroll' );
				$( '.fusion_builder_modal_inner_row_overlay' ).remove();

				this.$el.find( '.fusion-builder-column-inner' ).each( function() {
					innerColumnsString += jQuery( this ).data( 'column-size' ).replace( '_', '/' ) + ' + ';
				});

				this.$el.find( '> p' ).html( innerColumnsString.slice( 0, innerColumnsString.length - 3 ) );
			},

			render: function() {
				var innerColumnsWrapper = this.$el,
				    innerColumnsString  = '';

				this.$el.html( this.template( this.model.toJSON() ) );

				this.sortableColumns();

				setTimeout( function() {
					innerColumnsWrapper.find( '.fusion-builder-column-inner' ).each( function() {
						innerColumnsString += jQuery( this ).data( 'column-size' ).replace( '_', '/' ) + ' + ';
					});

					innerColumnsWrapper.find( '> h4' ).after( '<p>' + innerColumnsString.slice( 0, innerColumnsString.length - 3 ) + '</p>' );
				}, 100 );

				return this;
			},

			cloneNestedRow: function( event, parentCID ) {
				var innerRowAttributes,
				    thisInnerRow;

				if ( event ) {
					event.preventDefault();
				}

				innerRowAttributes         = $.extend( true, {}, this.model.attributes );
				innerRowAttributes.created = 'manually';
				innerRowAttributes.cid     = FusionPageBuilderViewManager.generateCid();

				if ( event ) {
					innerRowAttributes.appendAfter = this.$el;
				}

				if ( parentCID ) {
					innerRowAttributes.parent = parentCID;
				}

				FusionPageBuilderApp.collection.add( innerRowAttributes );

				// Parse inner columns
				thisInnerRow = this.$el;
				thisInnerRow.find( '.fusion-builder-column-inner' ).each( function() {
					var $thisColumnInner  = $( this ),
					    columnInnerCID    = $thisColumnInner.data( 'cid' ),
					    innerColumnModule = FusionPageBuilderElements.findWhere( { cid: columnInnerCID } ),

					    // Clone model attritubes
					    innerColAttributes = $.extend( true, {}, innerColumnModule.attributes );

					innerColAttributes.created = 'manually';
					innerColAttributes.cid     = FusionPageBuilderViewManager.generateCid();
					innerColAttributes.parent  = innerRowAttributes.cid;

					FusionPageBuilderApp.collection.add( innerColAttributes );

					// Parse elements inside inner col
					$thisColumnInner.find( '.fusion_module_block' ).each( function() {
						var thisModule = $( this ),
						    moduleCID  = 'undefined' === typeof thisModule.data( 'cid' ) ? thisModule.find( '.fusion-builder-data-cid' ).data( 'cid' ) : thisModule.data( 'cid' ),

						    // Get model from collection by cid
						    module = FusionPageBuilderElements.find( function( model ) {
								return model.get( 'cid' ) == moduleCID;
						    } ),

						    // Clone model attritubes
						    innerElementAttributes = $.extend( true, {}, module.attributes );

						innerElementAttributes.created = 'manually';
						innerElementAttributes.cid     = FusionPageBuilderViewManager.generateCid();
						innerElementAttributes.parent  = innerColAttributes.cid;
						innerElementAttributes.from    = 'fusion_builder_row_inner';

						FusionPageBuilderApp.collection.add( innerElementAttributes );
					} );

				} );

				if ( ! parentCID ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.cloned_nested_columns;

					FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
				}

			},

			saveElementDialog: function( event ) {
				if ( event ) {
					event.preventDefault();
				}
				FusionPageBuilderApp.showLibrary();

				$( '#fusion-builder-layouts-elements-trigger' ).click();

				$( '#fusion-builder-layouts-elements .fusion-builder-layouts-header-element-fields' ).append( '<div class="fusion-save-element-fields"><input type="text" value="" id="fusion-builder-save-element-input" class="fusion-builder-save-element-input" placeholder="' + fusionBuilderText.enter_name + '" /><a href="#" class="fusion-builder-save-column fusion-builder-element-button-save" data-element-cid="' + this.model.get( 'cid' ) + '">' + fusionBuilderText.save_element + '</a></div>' );

			},

			saveElement: function( event ) {

				var thisEl           = this.$el,
				    elementContent   = this.getInnerRowContent(),
				    elementName      = $( '#fusion-builder-save-element-input' ).val(),
				    layoutsContainer = $( '#fusion-builder-layouts-elements .fusion-page-layouts' ),
				    emptyMessage     = $( '#fusion-builder-layouts-elements .fusion-page-layouts .fusion-empty-library-message' );

				if ( event ) {
					event.preventDefault();
				}

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
							fusion_layout_new_cat: 'elements',
							fusion_layout_element_type: 'nested'
						},
						complete: function( data ) {
							layoutsContainer.prepend( data.responseText );
							$( '.fusion-save-element-fields' ).remove();
							emptyMessage.hide();
						}
					} );

				} else {
					alert( fusionBuilderText.please_enter_element_name );
				}
			},

			getInnerRowContent: function() {
				var shortcode       = '',
				    $thisRowInner   = this.$el,
				    thisRowInnerCID = $thisRowInner.data( 'cid' ),
				    module          = FusionPageBuilderElements.findWhere( { cid: thisRowInnerCID } );

				shortcode += '[fusion_builder_row_inner]';

				// Find nested columns in this row
				$thisRowInner.find( '.fusion-builder-column-inner' ).each( function() {
					var $thisColumnInner = $( this ),
					    columnInnerCID   = $thisColumnInner.data( 'cid' ),
					    module           = FusionPageBuilderElements.findWhere( { cid: columnInnerCID } ),
					    columnParams     = {},
					    columnAttributesCheck;

					_.each( module.get( 'params' ), function( value, name ) {

						if ( 'undefined' === value ) {
							columnParams[name] = '';
						} else {
							columnParams[name] = value;
						}

					} );

					// Legacy support for new column options
					columnAttributesCheck = {
						min_height: '',
						last: 'no',
						hover_type: 'none',
						link: '',
						border_position: 'all'
					};

					_.each( columnAttributesCheck, function( value, name ) {

						if ( 'undefined' === typeof columnParams[ name ] ) {
							columnParams[name] = value;
						}

					} );

					// Build column shortcdoe
					shortcode += '[fusion_builder_column_inner type="' + module.get( 'layout' ) + '" background_position="' + columnParams.background_position + '" background_color="' + columnParams.background_color + '" border_size="' + columnParams.border_size + '" border_color="' + columnParams.border_color + '" border_style="' + columnParams.border_style + '" spacing="' + columnParams.spacing + '" background_image="' + columnParams.background_image + '" background_repeat="' + columnParams.background_repeat + '" padding="' + columnParams.padding + '" margin_top="' + columnParams.margin_top + '" margin_bottom="' + columnParams.margin_bottom + '" class="' + columnParams.class + '" id="' + columnParams.id + '" animation_type="' + columnParams.animation_type + '" animation_speed="' + columnParams.animation_speed + '" animation_direction="' + columnParams.animation_direction + '" hide_on_mobile="' + columnParams.hide_on_mobile + '" center_content="' + columnParams.center_content + '" last="' + columnParams.last + '" min_height="' + columnParams.min_height + '" hover_type="' + columnParams.hover_type + '" link="' + columnParams.link + '"]';

						// Find elements in this column
						$thisColumnInner.find( '.fusion_module_block' ).each( function() {
							shortcode += FusionPageBuilderApp.generateElementShortcode( $( this ), false );
						} );

					shortcode += '[/fusion_builder_column_inner]';

				} );

				shortcode += '[/fusion_builder_row_inner]';

				return shortcode;
			},

			sortableColumns: function() {
				var thisEl     = this,
				    selectedEl = thisEl.$el.find( '.fusion-builder-row-container-inner' ),
				    cid        = this.model.get( 'cid' );

				selectedEl.sortable( {
					items: '.fusion-builder-column-inner',
					helper: 'clone',
					cancel: '.fusion-builder-settings, .fusion-builder-clone, .fusion-builder-remove, .fusion-builder-section-add, .fusion-builder-add-element, .fusion-builder-insert-column, #fusion_builder_controls, .fusion-builder-save-column, .fusion-builder-resize-column, .column-sizes, .fusion-builder-save-column-dialog',
					tolerance: 'pointer',

					update: function( event, ui ) {
						var moduleCID = ui.item.data( 'cid' ),
						    model     = thisEl.collection.find( function( model ) {
								return model.get( 'cid' ) == moduleCID;
						    } );

						// Moved the column within the same row
						if ( model.get( 'parent' ) === thisEl.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {

						// Moved the column to a different row
						} else {
							model.set( 'parent', thisEl.model.attributes.cid );
						}

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.moved_nested_column;

						FusionPageBuilderEvents.trigger( 'fusion-element-sorted' );
					}

				} ).disableSelection();
			},

			removeRow: function( event, force ) {

				var columns;

				if ( event ) {
					event.preventDefault();
				}

				columns = FusionPageBuilderViewManager.getChildViews( this.model.get( 'cid' ) );

				// Remove columns
				_.each( columns, function( column ) {
					column.removeColumn();
				} );

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();

				// If row ( nested columns ) is removed manually
				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.deleted_nested_columns;

					FusionPageBuilderEvents.trigger( 'fusion-element-removed' );
				}
			}

		} );

	} );

} )( jQuery );
