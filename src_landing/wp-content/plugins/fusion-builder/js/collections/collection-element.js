var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Element collection
		FusionPageBuilder.Collection = Backbone.Collection.extend( {
			model: FusionPageBuilder.Element
		} );

        FusionPageBuilderElements = new FusionPageBuilder.Collection();

	} );

} )( jQuery );
