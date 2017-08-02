<script type="text/template" id="fusion-builder-block-module-person-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<# if ( params.picture !== '' ) { #>
		<img src="{{ params.picture }}" alt="person" />
	<# } #>
	<span class="fusion-person-preview-name">{{ params.name }}</span>

</script>
