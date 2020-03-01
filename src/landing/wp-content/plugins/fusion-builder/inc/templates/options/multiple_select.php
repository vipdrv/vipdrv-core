<select id="{{ param.param_name }}" name="{{ param.param_name }}" multiple="multiple" class="fusion-form-multiple-select fusion-input">
	<# var choice = option_value; #>
	<# if ( 'undefined' !== typeof choice && '' !== choice && null !== choice ) { #>
		<# var choices = ( jQuery.isArray( choice ) ) ? choice : choice.split( ',' ); #>
	<# } else { #>
		<# var choices = ''; #>
	<# } #>

	<# _.each( param.value, function( name, value ) { #>
		<# var selected = ( jQuery.inArray( value, choices ) > -1 ) ? ' selected="selected"' : ''; #>
		<option value="{{ value }}"{{ selected }} >{{ name }}</option>
	<# } ); #>
</select>
