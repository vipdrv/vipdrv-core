<input
	id="{{ param.param_name }}"
	name="{{ param.param_name }}"
	class="fusion-builder-color-picker-hex color-picker"
	type="text"
	value="{{ option_value }}"
	data-alpha="true"
	<# if ( param.default ) { #>
		data-default="{{ param.default }}"
	<# } #>
/>
