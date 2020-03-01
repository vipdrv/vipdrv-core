<script type="text/template" id="fusion-builder-block-module-separator-preview-template">
	<# if ( params.style_type === 'none' ) { #>
		<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<# } else {

		if ( params.style_type == "single|dotted") {
			var sep_style = "sep-single sep-dotted";

		} else if ( params.style_type == "single|dashed") {
			var sep_style = "sep-single sep-dashed";

		} else if ( params.style_type == "double|dashed") {
			var sep_style = "sep-double sep-dashed";

		} else if ( params.style_type == "double|dotted") {
			var sep_style = "sep-double sep-dotted";

		} else {
			var sep_style = "sep-" + params.style_type;
		}

		var alignment = 'margin:0 auto';
		if ( 'center' != params.alignment ) {
			alignment = 'float:' + params.alignment;
		}
		#>

		<div class="fusion-separator fusion-full-width-sep {{ sep_style }}" style= "{{ alignment }};width:{{ params.width }};border-width:{{ params.border_size }};border-color:{{ params.sep_color }}"></div>
	<# } #>

</script>
