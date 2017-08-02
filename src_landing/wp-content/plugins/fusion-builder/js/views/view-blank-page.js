var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Blank Page View
		FusionPageBuilder.BlankPageView = window.wp.Backbone.View.extend( {

			className: 'fusion_builder_blank_page',

			template: FusionPageBuilder.template( $( '#fusion-builder-blank-page-template' ).html() ),

			events: {
				'click .fusion-builder-new-section-add': 'addContainer',
				'click .fusion-builder-video-button': 'openVideoModal'
			},

			initialize: function() {
			},

			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				this.$el.find( '#video-dialog' ).dialog({
					dialogClass: 'fusion-builder-dialog',
					autoOpen: false,
					modal: true,
					height: 420,
					width: 590
				} );

				return this;
			},

			openVideoModal: function( event ) {
				event.preventDefault();

				jQuery( '#video-dialog' ).dialog( 'open' );
			},

			addContainer: function( event ) {

				var moduleID,
				    defaultParams,
				    params,
				    value;

				if ( event ) {
					event.preventDefault();

					FusionPageBuilderApp.newContainerAdded = true;
				}

				FusionPageBuilderApp.activeModal = 'container';

				moduleID = FusionPageBuilderViewManager.generateCid(),
				defaultParams = fusionAllElements.fusion_builder_container.params,
				params = {};

				// Process default options for shortcode.
				_.each( defaultParams, function( param )  {
					if ( _.isObject( param.value ) ) {
						value = param.default;
					} else {
						value = param.value;
					}
					params[param.param_name] = value;

					if ( 'dimension' === param.type && _.isObject( param.value ) ) {
						_.each( param.value, function( value, name )  {
							params[name] = value;
						});
					}
				});

				this.collection.add( [ {
					type: 'fusion_builder_container',
					added: 'manually',
					element_type: 'fusion_builder_container',
					cid: moduleID,
					params: params,
					view: this,
					created: 'auto'
				} ] );

				this.remove();
			},

			removeBlankPageHelper: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();
			}

		} );

	} );

} )( jQuery );
