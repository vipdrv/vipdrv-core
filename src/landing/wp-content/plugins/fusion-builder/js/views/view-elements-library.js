var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Elements View
		FusionPageBuilder.ElementLibraryView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_modal_settings',

			template: FusionPageBuilder.template( $( '#fusion-builder-modules-template' ).html() ),

			events: {
				'click .fusion-builder-all-modules .fusion-builder-element': 'addModule',
				'click .fusion_builder_custom_elements_load': 'addCustomModule',
				'click .fusion-builder-column-layouts li': 'addNestedColumns'
			},

			initialize: function( attributes ) {
				this.options = attributes;
				this.listenTo( FusionPageBuilderEvents, 'fusion-modal-view-removed', this.remove );
			},

			render: function() {
				this.$el.html( this.template( FusionPageBuilderViewManager.toJSON() ) );

				// Load saved elements
				FusionPageBuilderApp.showSavedElements( 'elements', this.$el.find( '#custom-elements' ) );

				// If adding element to nested column
				if ( 'true' === FusionPageBuilderApp.innerColumn ) {
					this.$el.addClass( 'fusion-add-to-nested' );
				}

				return this;
			},

			addCustomModule: function( event ) {
				var layoutID,
				    title;

				if ( event ) {
					event.preventDefault();
				}

				if ( true === FusionPageBuilderApp.layoutIsLoading ) {
					return;
				} else {
					FusionPageBuilderApp.layoutIsLoading = true;
				}

				layoutID = $( event.currentTarget ).closest( 'li' ).data( 'layout_id' );
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

						FusionPageBuilderApp.shortcodesToBuilder( dataObj.post_content, FusionPageBuilderApp.parentColumnId );
						FusionPageBuilderApp.layoutIsLoading = false;

						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).css( 'opacity', '1' );
						$( event.currentTarget ).parent( '.fusion-builder-all-modules' ).prev( '#fusion-loader' ).hide();
					},

					complete: function() {

						// Save history state
						fusionHistoryManager.turnOnTracking();
						fusionHistoryState = fusionBuilderText.added_custom_element + title;

						FusionPageBuilderEvents.trigger( 'fusion-element-added' );
					}
				} );
			},

			addModule: function( event ) {
				var $thisEl,
				    label,
				    params,
				    multi,
				    type,
				    name,
				    allowGenerator;

				if ( event ) {
					event.preventDefault();
				}

				$thisEl = $( event.currentTarget );
				label   = $thisEl.find( '.fusion_module_label' ).text();

				if ( label in fusionAllElements ) {

					params = fusionAllElements[ label ].params;
					multi   = fusionAllElements[ label ].multi;
					type   = fusionAllElements[ label ].shortcode;
					name   = fusionAllElements[ label ].name;
					allowGenerator = fusionAllElements[ label ].allow_generator;

				} else {
					params = '';
					multi   = '';
					type   = '';
					allowGenerator = '';
				}

				if ( event ) {
					fusionHistoryState = fusionBuilderText.added + ' ' + name + ' ' + fusionBuilderText.element;
				}

				this.collection.add( [ {
					type: 'element',
					added: 'manually',
					cid: FusionPageBuilderViewManager.generateCid(),
					element_type: type,
					params: params,
					parent: this.attributes['data-parent_cid'],
					view: this.options.view,
					allow_generator: allowGenerator,
					multi: multi
				} ] );

				this.remove();

				FusionPageBuilderEvents.trigger( 'fusion-element-added' );

			},

			addNestedColumns: function( event, appendAfter ) {
				var moduleID,
				    that,
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

				moduleID = FusionPageBuilderViewManager.generateCid();

				this.collection.add( [ {
					type: 'fusion_builder_row_inner',
					element_type: 'fusion_builder_row_inner',
					cid: moduleID,
					parent: this.model.get( 'cid' ),
					view: this,
					appendAfter: appendAfter
				} ] );

				FusionPageBuilderApp.activeModal = 'column';

				that              = this;
				$layoutEl         = $( event.target ).is( 'li' ) ? $( event.target ) : $( event.target ).closest( 'li' );
				layout            = $layoutEl.data( 'layout' ).split( ',' );
				layoutElementsNum = _.size( layout );
				thisView          = this.options.view;

				// Get default options
				defaultParams = fusionAllElements.fusion_builder_column_inner.params;
				params = {};

				// Process default parameters from shortcode
				_.each( defaultParams, function( param )  {
					if ( _.isObject( param.value ) ) {
						value = param.default;
					} else {
						value = param.value;
					}
					params[param.param_name] = value;
				} );

				_.each( layout, function( element, index ) {
					var updateContent    = layoutElementsNum == ( index + 1 ) ? 'true' : 'false',
					    columnAttributes = {
							type: 'fusion_builder_column_inner',
							element_type: 'fusion_builder_column_inner',
							cid: FusionPageBuilderViewManager.generateCid(),
							parent: moduleID,
							layout: element,
							view: thisView,
							params: params
					    };

					that.collection.add( [ columnAttributes ] );

				} );

				this.remove();

				FusionPageBuilderEvents.trigger( 'fusion-columns-added' );

				if ( event ) {

					// Save history state
					fusionHistoryManager.turnOnTracking();
					fusionHistoryState = fusionBuilderText.added_nested_columns;

					FusionPageBuilderEvents.trigger( 'fusion-element-cloned' );
				}
			}

		} );

	} );

} )( jQuery );
