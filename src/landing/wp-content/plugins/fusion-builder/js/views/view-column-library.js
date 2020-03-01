var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Column Library View
		FusionPageBuilder.ColumnLibraryView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_modal_settings',

			template: FusionPageBuilder.template( $( '#fusion-builder-column-library-template' ).html() ),

			events: {
				'click .fusion-builder-column-layouts li': 'addColumns',
				'click .fusion_builder_custom_columns_load': 'addCustomColumn',
				'click .fusion_builder_custom_sections_load': 'addCustomSection',
				'click .fusion-builder-section-next-page': 'addNextPage'
			},

			initialize: function( attributes ) {
				this.listenTo( FusionPageBuilderEvents, 'fusion-columns-added', this.removeView );
				this.listenTo( FusionPageBuilderEvents, 'fusion-modal-view-removed', this.removeView );

				this.options = attributes;
			},

			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				// Show saved custom columns
				FusionPageBuilderApp.showSavedElements( 'columns', this.$el.find( '#custom-columns' ) );

				// Show saved custom sections
				if ( 'container' === FusionPageBuilderApp.activeModal ) {
					FusionPageBuilderApp.showSavedElements( 'sections', this.$el.find( '#custom-sections' ) );
				}

				return this;
			},

			addCustomColumn: function( event ) {
				var thisModel,
				    layoutID,
				    title;

				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderApp.activeModal = 'column';

				if ( true === FusionPageBuilderApp.layoutIsLoading ) {
					return;
				} else {
					FusionPageBuilderApp.layoutIsLoading = true;
				}

				thisModel = this.model;
				layoutID  = $( event.currentTarget ).data( 'layout_id' );
				title     = $( event.currentTarget ).find( '.fusion_module_title' ).text();

				$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).css( 'opacity', '0' );
				$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).prev( '#fusion-loader' ).show();

				$.ajax( {
					type: 'POST',
					url: FusionPageBuilderApp.ajaxurl,
					data: {
						action: 'fusion_builder_load_layout',
						fusion_load_nonce: FusionPageBuilderApp.fusion_load_nonce,
						fusion_layout_id: layoutID
					},
					success: function( data ) {

						var dataObj = JSON.parse( data );

						FusionPageBuilderApp.shortcodesToBuilder( dataObj.post_content, FusionPageBuilderApp.parentRowId );

						FusionPageBuilderApp.layoutIsLoading = false;

						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).css( 'opacity', '1' );
						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).prev( '#fusion-loader' ).hide();

					},
					complete: function() {

						// Unset 'added' attribute from newly created row model
						thisModel.unset( 'added' );

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.added_custom_column + title;

						FusionPageBuilderEvents.trigger( 'fusion-columns-added' );
						FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
					}
				} );
			},

			addColumns: function( event ) {

				var that,
				    $layoutEl,
				    layout,
				    layoutElementsNum,
				    thisView,
				    defaultParams,
				    params,
				    value;

				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderApp.activeModal = 'column';

				that              = this;
				$layoutEl         = $( event.target ).is( 'li' ) ? $( event.target ) : $( event.target ).closest( 'li' );
				layout            = $layoutEl.data( 'layout' ).split( ',' );
				layoutElementsNum = _.size( layout );
				thisView          = this.options.view;

				// Get default settings
				defaultParams = fusionAllElements.fusion_builder_column.params;

				_.each( layout, function( element, index ) {
					var params = {},
					    updateContent,
					    columnAttributes;

					// Process default parameters from shortcode
					_.each( defaultParams, function( param )  {
						if ( _.isObject( param.value ) ) {
							value = param.default;
						} else {
							value = param.value;
						}
						params[param.param_name] = value;
					} );

					updateContent    = layoutElementsNum == ( index + 1 ) ? 'true' : 'false';
					columnAttributes = {
						type: 'fusion_builder_column',
						element_type: 'fusion_builder_column',
						cid: FusionPageBuilderViewManager.generateCid(),
						parent: that.model.get( 'cid' ),
						layout: element,
						view: thisView,
						params: params
					};

					that.collection.add( [ columnAttributes ] );

				} );

				// Unset 'added' attribute from newly created row model
				this.model.unset( 'added' );

				FusionPageBuilderEvents.trigger( 'fusion-columns-added' );

				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();

					if ( true == FusionPageBuilderApp.newContainerAdded ) {
						fusionHistoryState = fusionBuilderText.added_section;
						FusionPageBuilderApp.newContainerAdded = false;
					} else {
						fusionHistoryState = fusionBuilderText.added_columns;
					}

					FusionPageBuilderEvents.trigger( 'fusion-element-added' );
				}

			},

			removeView: function() {
				this.remove();
			},

			addCustomSection: function( event ) {
				var thisModel  = this.model,
				    parentID   = this.model.get( 'parent' ),
				    parentView = FusionPageBuilderViewManager.getView( parentID ),
				    layoutID,
				    title,
				    targetContainer;

				targetContainer = parentView.$el.prev( '.fusion_builder_container' );
				FusionPageBuilderApp.targetContainerCID = targetContainer.find( '.fusion-builder-data-cid' ).data( 'cid' );

				if ( event ) {
					event.preventDefault();
				}

				if ( 'undefined' !== typeof parentView ) {
					parentView.removeContainer();
				}

				if ( true === FusionPageBuilderApp.layoutIsLoading ) {
					return;
				} else {
					FusionPageBuilderApp.layoutIsLoading = true;
				}

				layoutID = $( event.currentTarget ).data( 'layout_id' );
				title    = $( event.currentTarget ).find( '.fusion_module_title' ).text();

				$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).css( 'opacity', '0' );
				$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).prev( '#fusion-loader' ).show();

				$.ajax( {
					type: 'POST',
					url: FusionPageBuilderApp.ajaxurl,
					data: {
						action: 'fusion_builder_load_layout',
						fusion_load_nonce: FusionPageBuilderApp.fusion_load_nonce,
						fusion_layout_id: layoutID
					},
					success: function( data ) {
						var dataObj = JSON.parse( data );

						FusionPageBuilderApp.shortcodesToBuilder( dataObj.post_content, FusionPageBuilderApp.parentRowId );

						FusionPageBuilderApp.layoutIsLoading = false;

						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).css( 'opacity', '1' );
						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).prev( '#fusion-loader' ).hide();

					},
					complete: function() {

						// Unset 'added' attribute from newly created section model
						thisModel.unset( 'added' );

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.added_custom_section + title;

						FusionPageBuilderEvents.trigger( 'fusion-columns-added' );
						FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
					}
				});
			},

			addNextPage: function( event ) {
				var parentID   = this.model.get( 'parent' ),
				    parentView = FusionPageBuilderViewManager.getView( parentID ),
				    targetContainer,
				    moduleID,
				    params = {};

				if ( event ) {
					event.preventDefault();
				}

				targetContainer = parentView.$el.prev( '.fusion_builder_container' );
				FusionPageBuilderApp.targetContainerCID = targetContainer.find( '.fusion-builder-data-cid' ).data( 'cid' );
				moduleID = FusionPageBuilderViewManager.generateCid();

				this.collection.add( [ {
					type: 'fusion_builder_next_page',
					added: 'manually',
					module_type: 'fusion_builder_next_page',
					cid: moduleID,
					params: params,
					view: parentView,
					appendAfter: targetContainer,
					created: 'auto'
				} ] );

				if ( 'undefined' !== typeof parentView ) {
					FusionPageBuilderApp.targetContainerCID = '';
					parentView.removeContainer();
				}

				FusionPageBuilderEvents.trigger( 'fusion-columns-added' );
				FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );

			}

		} );

	});

} )( jQuery );
