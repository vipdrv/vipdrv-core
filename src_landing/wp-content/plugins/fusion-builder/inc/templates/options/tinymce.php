<textarea
	id="{{ param.param_name }}"
	class="fusion-editor-field"
	<# if ( param.placeholder ) { #>
		data-placeholder="{{ param.value }}"
	<# } #>
	<# if ( 'fusion_text' == atts.element_type ) { #>
		data-element="fusion_text"
	<# } #>
>{{ option_value }}</textarea>

