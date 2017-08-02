var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Generator elements library
		FusionPageBuilder.GeneratorElementsView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_modal_settings',

			template: FusionPageBuilder.template( $( '#fusion-builder-generator-modules-template' ).html() ),

			events: {
				'click .fusion-builder-all-modules .fusion-builder-element': 'addElement',
				'click .fusion-builder-column-layouts .generator-column': 'addColumns',
				'click .fusion-builder-column-layouts .generator-section': 'addContainer'
			},

			initialize: function( attributes ) {
				this.options = attributes;
				this.listenTo( FusionPageBuilderEvents, 'fusion-modal-view-removed', this.remove );
			},

			render: function() {
				this.$el.html( this.template( FusionPageBuilderViewManager.toJSON() ) );
				return this;
			},

			addElement: function( event ) {
				var $thisEl,
				    title,
				    label,
				    params,
				    multi,
				    type,
				    selection,
				    defaultParams,
				    elementSettings;

				if ( event ) {
					event.preventDefault();
				}

				$thisEl = $( event.currentTarget );
				title   = $thisEl.find( '.fusion_module_title' ).text();
				label   = $thisEl.find( '.fusion_module_label' ).text();

				if ( label in fusionAllElements ) {

					multi   = fusionAllElements[ label ].multi;
					type   = fusionAllElements[ label ].shortcode;

				} else {

					params = '';
					multi   = '';
					type   = '';
				}

				// Get default settings
				defaultParams = $.extend( true, {}, fusionAllElements[ label ].params );
				params = {};

				// Process default parameters from shortcode
				_.each( defaultParams, function( param )  {
					var value;
					if ( _.isObject( param.value ) ) {
						value = param.default;
					} else {
						value = param.value;
					}
					params[param.param_name] = value;
				} );

				elementSettings = {
					type: 'generated_element',
					added: 'manually',
					element_type: type,
					params: params,
					view: this.options.view,
					multi: multi,
					cid: FusionPageBuilderViewManager.generateCid()
				};
				if ( 'undefined' !== params.element_content && 'undefined' !== typeof tinyMCE && 'undefined' !== tinyMCE.activeEditor && 'undefined' === typeof multi && window.tinyMCE.activeEditor ) {

					selection = window.tinyMCE.activeEditor.selection.getContent();

					if ( selection ) {

						elementSettings.params.element_content = selection;

						window.tinyMCE.activeEditor.selection.setContent( '' );
						selection = '';

						delete elementSettings.added;
					}
				}

				this.collection.add( elementSettings );

				this.remove();
			},

			addColumns: function( event ) {
				var that,
				    $layoutEl,
				    layout,
				    layoutElementsNum,
				    thisView,
				    defaultParams,
				    params,
				    value,
				    columnModel,
				    generatedShortcode = '[fusion_builder_row_inner]',
				    elementType        = 'fusion_builder_column_inner',
				    closingTag         = '[/fusion_builder_row_inner]';

				if ( ! FusionPageBuilderApp.builderActive && jQuery( event.target ).parents( '#builder-regular-columns' ).length ) {
					generatedShortcode = '';
					elementType = 'fusion_builder_column';
					closingTag = '';
				}
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
				defaultParams = fusionAllElements[elementType].params;
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

					var updateContent,
					    columnAttributes;

					params.type = element;

					updateContent    = layoutElementsNum == ( index + 1 ) ? 'true' : 'false';
					columnAttributes = {
						type: 'generated_element',
						added: 'manually',
						element_type: elementType,
						layout: element,
						view: thisView,
						params: params
					};

					columnModel = that.collection.add( columnAttributes );

					generatedShortcode += FusionPageBuilderApp.generateElementShortcode( columnModel, false, true );

				} );

				generatedShortcode += closingTag;

				fusionBuilderInsertIntoEditor( generatedShortcode, FusionPageBuilderApp.shortcodeGeneratorEditorID );

				// Reset shortcode generator
				FusionPageBuilderApp.shortcodeGenerator = '';
				FusionPageBuilderApp.shortcodeGeneratorEditorID = '';

				this.remove();
			},

			addContainer: function( event ) {
				var elementID,
				    defaultParams,
				    params,
				    value;

				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderApp.activeModal = 'container';

				elementID = FusionPageBuilderViewManager.generateCid();
				defaultParams = fusionAllElements.fusion_builder_container.params;
				params = {};

				// Process default parameters from shortcode
				_.each( defaultParams, function( param ) {
					if ( _.isObject( param.value ) ) {
						value = param.default;
					} else {
						value = param.value;
					}
					params[ param.param_name ] = value;
				} );

				this.collection.add( [ {
					type: 'generated_element',
					added: 'manually',
					element_type: 'fusion_builder_container',
					params: params,
					view: this
				} ] );
			}

		} );

	} );

} )( jQuery );
