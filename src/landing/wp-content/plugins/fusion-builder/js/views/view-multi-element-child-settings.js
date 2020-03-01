var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		FusionPageBuilder.MultiElementSettingsView = FusionPageBuilder.ElementSettingsView.extend( {

			events: {
				'click .insert-slider-video': 'addSliderVideo'
			},

			addSliderVideo: function( event ) {

				var defaultParams,
				    params,
				    elementType,
				    value;

				if ( event ) {
					event.preventDefault();
				}
				FusionPageBuilderApp.manualGenerator            = FusionPageBuilderApp.shortcodeGenerator;
				FusionPageBuilderApp.manualEditor               = FusionPageBuilderApp.shortcodeGeneratorEditorID;
				FusionPageBuilderApp.manuallyAdded              = true;
				FusionPageBuilderApp.shortcodeGenerator         = true;
				FusionPageBuilderApp.shortcodeGeneratorEditorID = 'video';

				elementType = $( event.currentTarget ).data( 'type' );

				// Get default options
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

				this.collection.add( [ {
					type: 'generated_element',
					added: 'manually',
					element_type: elementType,
					params: params
				} ] );
			}

		} );

	} );

} )( jQuery );
