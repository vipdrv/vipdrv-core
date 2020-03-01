var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		var fusionElements          = [],
		    fusionGeneratorElements = [];

		// Loop over all available elements and add them to Fusion Builder.
		// Ignore elements tagged with 'hide_from_builder' attribute.
		_.each( fusionAllElements, function( element ) {
			var newElement;

			if ( 'undefined' === typeof element.hide_from_builder && 'undefined' === typeof element.generator_only ) {

				newElement = {
					'title': element.name,
					'label': element.shortcode
				};

				fusionElements.push( newElement );
			}
		} );

		_.each( fusionAllElements, function( element ) {
			var newElement;

			if ( 'undefined' === typeof element.hide_from_builder ) {

				newElement = {
					'title': element.name,
					'label': element.shortcode
				};

				fusionGeneratorElements.push( newElement );
			}
		} );

		//Sort elements alphabetically
		fusionElements.sort( function( a, b ) {
			var titleA = a.title.toLowerCase(),
			    titleB = b.title.toLowerCase();

			return ( ( titleA < titleB ) ? -1 : ( ( titleA > titleB ) ? 1 : 0 ) );
		});

		// Sort generator elements alphabetically
		fusionGeneratorElements.sort( function( a, b ) {
			var titleA = a.title.toLowerCase(),
			    titleB = b.title.toLowerCase();

			return ( ( titleA < titleB ) ? -1 : ( ( titleA > titleB ) ? 1 : 0 ) );
		});

		FusionPageBuilder.ViewManager = Backbone.Model.extend( {
			defaults: {
				modules: fusionElements,
				generator_elements: fusionGeneratorElements,
				elementCount: 0,
				views: {}
			},

			initialize: function() {
			},

			getView: function( cid ) {
				return this.get( 'views' )[cid];
			},

			getChildViews: function( parentID ) {
				var views      = this.get( 'views' ),
				    childViews = {};

				_.each( views, function( view, key ) {
					if ( parentID === view.model.attributes.parent ) {
						childViews[ key ] = view;
					}
				} );

				return childViews;
			},

			generateCid: function() {
				var elementCount = this.get( 'elementCount' ) + 1;

				this.set( { 'elementCount': elementCount } );

				return elementCount;
			},

			addView: function( cid, view ) {
				var views = this.get( 'views' );

				views[ cid ] = view;
				this.set( { 'views': views } );
			},

			removeView: function( cid ) {
				var views    = this.get( 'views' ),
				    updatedViews = {};

				_.each( views, function( value, key ) {
					if ( key != cid ) {
						updatedViews[key] = value;
					}
				} );

				this.set( { 'views': updatedViews } );
			},

			removeViews: function( cid ) {
				var updatedViews = {};
				this.set( { 'views': updatedViews } );
			},

			countElementsByType: function( elementType ) {
				var views = this.get( 'views' ),
				    num   = 0;

				_.each( views, function( view ) {
					if ( view.model.attributes.type === elementType ) {
						num++;
					}
				} );

				return num;
			}

		} );

		FusionPageBuilderViewManager = new FusionPageBuilder.ViewManager();

	} );

} )( jQuery );
