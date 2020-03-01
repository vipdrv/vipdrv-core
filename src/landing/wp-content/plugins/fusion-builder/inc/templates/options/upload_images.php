<div class="fusion-multiple-upload-images">
	<input
		type="hidden"
		name="{{ param.param_name }}"
		id="{{ param.param_name }}"
		class="fusion-multi-image-input"
		value="{{ option_value }}"
	/>
	<input
		type='button'
		class='button-upload fusion-builder-upload-button'
		value='{{ fusionBuilderText.select_images }}'
		data-type="image"
		data-title="{{ fusionBuilderText.select_images }}"
		data-id="fusion-multiple-images"
		data-element="{{ param.element }}"
	/>
	<div class="fusion-multiple-image-container">
		<#
		var imageIDs = option_value.split(','),
		multiImageHtml = '';
		jQuery.ajax( {
			type: 'POST',
			url: fusionBuilderConfig.ajaxurl,
			data: {
				action: 'fusion_builder_get_image_url',
				fusion_load_nonce: fusionBuilderConfig.fusion_load_nonce,
				fusion_image_ids: imageIDs
			},
			beforeSend: function() {
			},
			success: function( data ) {
				var dataObj;
				dataObj = JSON.parse( data );
				_.each( dataObj.images, function( image ) {
					jQuery('.fusion-multiple-image-container').append( image );
				} );
			},
			complete: function( data ) {
			}
		} );
		#>
	</div>
</div>
