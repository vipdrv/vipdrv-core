var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Next Page View
		FusionPageBuilder.NextPage = window.wp.Backbone.View.extend( {

			className: 'fusion-builder-next-page',
			template: FusionPageBuilder.template( $( '#fusion-builder-next-page-template' ).html() ),
			events: {
				'click .fusion-builder-delete-next-page': 'removeContainer'
			},

			initialize: function() {
			},

			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );

				return this;
			},

			removeContainer: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderViewManager.removeView( this.model.get( 'cid' ) );

				this.model.destroy();

				this.remove();

				if ( event ) {
					FusionPageBuilderEvents.trigger( 'fusion-element-removed' );
				}
			}

		} );

	} );

} )( jQuery );
