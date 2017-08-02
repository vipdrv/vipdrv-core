<script type="text/template" id="fusion-builder-block-module-alert-preview-template">

	<#
	var icon_type       = '',
	    content         = params.element_content,
	    text_block      = jQuery.parseHTML( content ),
	    element_content = jQuery(text_block).text();

	if ( params.type == 'general' ) {
		icon_type = 'fa-info-circle';
	}
	if ( params.type == 'error' ) {
		icon_type = 'fa-exclamation-triangle';
	}
	if ( params.type == 'success' ) {
		icon_type = 'fa-check-circle';
	}
	if ( params.type == 'notice' ) {
		icon_type = 'fa-lg fa-cog';
	}
	if ( params.type == 'custom' ) {
		icon_type = params.icon;
	}
	#>

	<span class="fusion-module-icon fa fa-lg {{ icon_type }}"></span> {{{ element_content }}}

</script>
