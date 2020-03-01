<input
	id="{{ param.param_name }}"
	name="{{ param.param_name }}"
	class="fusion-builder-color-picker-hex"
	type="text"
	value="{{ option_value }}"
	<# if ( param.default ) { #>
		data-default="{{ param.default }}"
	<# } #>
/>
