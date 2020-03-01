<script type="text/template" id="fusion-builder-block-module-slider-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<#
	var
	content = 'undefined' !== typeof params.element_content ? params.element_content : '',
	slider_reg_exp = window.wp.shortcode.regexp( 'fusion_slide' ),
	slider_inner_reg_exp = new RegExp( '\\[(\\[?)(fusion_slide)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' ),
	slider_matches = content.match( slider_reg_exp );

	_.each( slider_matches.slice(0,5), function ( slider_shortcode ) {
		var
		slider_shortcode_element = slider_shortcode.match( slider_inner_reg_exp ),
		slider_shortcode_content = slider_shortcode_element[5],
		slider_shortcode_attributes = '' !== slider_shortcode_element[3] ? window.wp.shortcode.attrs( slider_shortcode_element[3] ) : '';
		if ( 'undefined' === typeof slider_shortcode_attributes.named || 'image' === slider_shortcode_attributes.named['type'] ) { #>
			<img src="{{ slider_shortcode_content }}" class="fusion-slide-preview" />

		<# } else if ( 'video' === slider_shortcode_attributes.named['type'] && 'undefined' !== typeof slider_shortcode_content ) { #>
			<#
			youtube_regex = new RegExp( '\\[(\\[?)(fusion_youtube)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' );
			youtube_matches = slider_shortcode_content.match( youtube_regex );
			#>

			<# if ( youtube_matches ) { video_shortcode_attributes = ( youtube_matches[3] !== '' ) ? window.wp.shortcode.attrs( youtube_matches[3] ) : ''; #>
				<img src="http://img.youtube.com/vi/{{ video_shortcode_attributes.named['id'] }}/default.jpg" class="fusion-slide-preview" />
			<# } else { #>
				<span class="fusion-slide-preview fusion-slide-video-preview fusion-module-icon fusiona-youtube"></span>
			<# } #>
		<# }

	});
	#>

</script>
