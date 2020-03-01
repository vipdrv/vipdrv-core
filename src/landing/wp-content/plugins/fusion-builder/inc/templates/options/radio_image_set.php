<div class="fusion-form-radio-button-set radio-image-set ui-buttonset {{ param.param_name }}">
	<# var choice = option_value, index = 0; #>
	<input type="hidden" id="{{ param.param_name }}" name="{{ param.param_name }}" value="{{ choice }}" class="button-set-value" />
	<# _.each( param.value, function( src, value ) { #>
		<# index++; #>
		<# var selected = ( value == choice ) ? ' ui-state-active' : ''; #>
		<# var width = ( '' !== param.width ) ? param.width : '32px'; #>
		<# var height = ( '' !== param.height ) ? param.height : '32px'; #>
		<a href="#" class="ui-button buttonset-item{{ selected }}" data-value="{{ value }}"><img src="{{ src }}" style="width: {{ width }}; height: {{ height }};"/></a>
	<# } ); #>
</div>
