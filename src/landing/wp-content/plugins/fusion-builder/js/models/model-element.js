var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder element model
		FusionPageBuilder.Element = Backbone.Model.extend( {

			defaults: {
				type: 'element'
			},

			initialize: function() {
			}

		} );

	} );

})( jQuery );
