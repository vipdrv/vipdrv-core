<div class="fusion-form-radio-button-set ui-buttonset {{ param.param_name }}">
	<# var choice = option_value, index = 0; #>
	<input type="hidden" id="{{ param.param_name }}" name="{{ param.param_name }}" value="{{ choice }}" class="button-set-value" />
	<# _.each( param.value, function( name, value ) { #>
		<# index++; #>
		<# var selected = ( value == choice ) ? ' ui-state-active' : ''; #>
		<a href="#" class="ui-button buttonset-item{{ selected }}" data-value="{{ value }}">{{ name }}</a>
	<# } ); #>
</div>
