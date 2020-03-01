<script type="text/template" id="fusion-builder-block-module-image-frame-preview-template">

	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<#
	var elementContent = params.element_content,
		imagePreview;

	if ( 'undefined' !== typeof ( elementContent )  ) {
		if ( elementContent.indexOf( '&lt;img' ) >= 0 ) {
			imagePreview = jQuery( '<div></div>' ).html( elementContent ).text();
		} else if ( elementContent.indexOf( '<img' ) >= 0 ) {
			imagePreview =  jQuery( '<div></div>' ).html( elementContent ).html();
		} else {
			imagePreview = '<img src="' + elementContent + '" />';
		}
	}
	#>

	{{{ imagePreview }}}

</script>
