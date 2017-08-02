<script type="text/template" id="fusion-builder-block-module-image-carousel-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>
	<div>
		<#
		var
		content = typeof params.element_content !== 'undefined' ? params.element_content : '',
		slider_reg_exp = window.wp.shortcode.regexp( 'fusion_image' ),
		slider_inner_reg_exp = new RegExp( '\\[(\\[?)(fusion_image)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' ),
		slider_matches = content.match( slider_reg_exp );

		_.each( slider_matches.slice(0,5), function ( slider_shortcode ) {
			var
			slider_shortcode_element = slider_shortcode.match( slider_inner_reg_exp ),
			slider_shortcode_content = slider_shortcode_element[5],
			slider_shortcode_attributes = slider_shortcode_element[3] !== '' ? window.wp.shortcode.attrs( slider_shortcode_element[3] ) : ''; #>

			<img src="{{ slider_shortcode_attributes.named['image'] }}" class="fusion-slide-preview" />
		<#
		});
		#>
	</div>
</script>
