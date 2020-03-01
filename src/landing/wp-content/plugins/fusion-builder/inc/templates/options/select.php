<div class="select_arrow"></div>
<select id="{{ param.param_name }}" name="{{ param.param_name }}" class="fusion-select-field<?php echo ( is_rtl() ) ? 'chosen-rtl fusion-select-field-rtl' : ''; ?>">
<# _.each( param.value, function( name, value ) { #>
	<option value="{{ value }}" {{ typeof( option_value ) !== 'undefined' && value === option_value ?  ' selected="selected"' : '' }} >{{ name }}</option>
<# }); #>
</select>
