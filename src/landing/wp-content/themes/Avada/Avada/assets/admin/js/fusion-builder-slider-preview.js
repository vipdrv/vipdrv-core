jQuery( document ).ready( function() {
	jQuery( '#fusion_builder_layout' ).on( 'click', '#avada-slider-remove', function( e ) {
		e.preventDefault();
		jQuery( '#pyre_demo_slider' ).val( '' );
		jQuery( '#pyre_slider_type' ).val( 'no' ).trigger( 'change' );
		jQuery( '.fusion-builder-slider-helper' ).slideUp( 300 );
	});
	jQuery( '#pyre_slider_type, #pyre_slider, #pyre_wooslider, #pyre_revslider, #pyre_elasticslider, #pyre_demo_slider' ).on( 'change', function( e ) {
		var $sliderType    = jQuery( '#pyre_slider_type' ).val(),
			$layerSlider   = jQuery( '#pyre_slider' ).val(),
			$revSlider     = jQuery( '#pyre_revslider' ).val(),
			$wooSlider     = jQuery( '#pyre_wooslider' ).val(),
			$elasticSlider = jQuery( '#pyre_elasticslider' ).val(),
			$demoSlider    = jQuery( '#pyre_demo_slider' ).val();

		jQuery.ajax({
			type:     'post',
			dataType: 'json',
			url:       ajaxurl,
			data: {
				action: 'avada_slider_preview',
				data:    {
					slidertype: $sliderType,
					layerslider: $layerSlider,
					revslider: $revSlider,
					wooslider: $wooSlider,
					elasticslider: $elasticSlider,
					demoslider: $demoSlider
				}
			},
			error: function( response ) {
				if ( ! jQuery( '.fusion-builder-slider-helper' ).length ) {
					jQuery( '#fusion_builder_container' ).prepend( response.responseText.slice( 0, -1 ) );
				} else {
					jQuery( '.fusion-builder-slider-helper' ).replaceWith( response.responseText.slice( 0, -1 ) );
				}
			},
			success: function( response ) {
				if ( jQuery( '.fusion-builder-slider-helper' ).length ) {
					jQuery( '.fusion-builder-slider-helper' ).remove();
				}
			}
		});
	});
});
