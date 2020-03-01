<div class="fusion-form-checkbox-button-set ui-buttonset {{ param.param_name }}">
	<# var choice = option_value, index = 0; #>
	<# if ( 'undefined' !== typeof choice && '' !== choice && null !== choice ) { #>
		<# var choices = ( jQuery.isArray( choice ) ) ? choice : choice.split( ',' ); #>
	<# } else { #>
		<# var choices = ''; #>
	<# } #>
	<input type="hidden" id="{{ param.param_name }}" name="{{ param.param_name }}" value="{{ choice }}" class="button-set-value" />
	<# _.each( param.value, function( name, value ) { #>
		<# index++; #>
		<# var selected = ( jQuery.inArray( value, choices ) > -1 ) ? ' ui-state-active' : ''; #>
		<a href="#" class="ui-button buttonset-item{{ selected }}" data-value="{{ value }}">{{ name }}</a>
	<# }); #>

</div>
