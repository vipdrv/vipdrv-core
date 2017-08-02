var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Builder Element Preview View
		FusionPageBuilder.ElementPreviewView = window.wp.Backbone.View.extend( {

			className: 'fusion_module_block_preview ',

			initialize: function() {
				this.template = FusionPageBuilder.template( $( '#' + fusionAllElements[this.model.attributes.element_type].preview_id ).html() );
			},

			render: function() {
				this.$el.html( this.template( this.model.attributes ) );

				return this;
			}

		});

	});

} )( jQuery );
