<# if ( 'object' == typeof param.value ) { #>
	<# _.each( param.value, function( sub_value, sub_param ) { #>
		<#
		var dimension_value = ( 'undefined' !== atts.params[ sub_param ] ) ? atts.params[ sub_param ] : sub_value;
		icon_class = 'fa fa-arrows-h';
		if ( sub_param.indexOf( 'height' ) > -1 ) {
			icon_class = 'fa fa-arrows-v';
		}
		if ( sub_param.indexOf( 'top' ) > -1 ) {
			icon_class = 'dashicons dashicons-arrow-up-alt';
		}
		if ( sub_param.indexOf( 'right' ) > -1 ) {
			icon_class = 'dashicons dashicons-arrow-right-alt';
		}
		if ( sub_param.indexOf( 'bottom' ) > -1 ) {
			icon_class = 'dashicons dashicons-arrow-down-alt';
		}
		if ( sub_param.indexOf( 'left' ) > -1 ) {
			icon_class = 'dashicons dashicons-arrow-left-alt';
		}
		if ( sub_param.indexOf( 'all' ) > -1 ) {
			icon_class = 'fa fa-arrows';
			if ( 'object' == typeof dimension_value ) {
				dimension_value = dimension_value.value[ sub_param ];
			}
		}
		#>
		<div class="fusion-builder-dimension">
		<span class="add-on"><i class="{{ icon_class }}"></i></span>
		<input type="text" name="{{ sub_param }}" id="{{ sub_param }}" value="{{ dimension_value }}" />
		</div>
	<# } ); #>
<# } else { #>
	<#
	values = option_value.split(' ');
	if ( 1 == values.length ) {
		var dimension_top = values[0];
		var dimension_bottom = values[0];
		var dimension_left = values[0];
		var dimension_right = values[0];
	}
	if ( 2 == values.length ) {
		var dimension_top = values[0];
		var dimension_bottom = values[0];
		var dimension_left = values[1];
		var dimension_right = values[1];
	}
	if ( 3 == values.length ) {
		var dimension_top = values[0];
		var dimension_left = values[1];
		var dimension_right = values[1];
		var dimension_bottom = values[2];
	}
	if ( 4 == values.length ) {
		var dimension_top = values[0];
		var dimension_left = values[3];
		var dimension_right = values[1];
		var dimension_bottom = values[2];
	}
	#>
	<div class="single-builder-dimension">
		<div class="fusion-builder-dimension">
			<span class="add-on"><i class="dashicons dashicons-arrow-up-alt"></i></span>
			<input type="text" name="{{ param.param_name }}_top" id="{{ param.param_name }}_top" value="{{ dimension_top }}" />
		</div>
		<div class="fusion-builder-dimension">
			<span class="add-on"><i class="dashicons dashicons-arrow-right-alt"></i></span>
			<input type="text" name="{{ param.param_name }}_right" id="{{ param.param_name }}_right" value="{{ dimension_right }}" />
		</div>
		<div class="fusion-builder-dimension">
			<span class="add-on"><i class="dashicons dashicons-arrow-down-alt"></i></span>
			<input type="text" name="{{ param.param_name }}_bottom" id="{{ param.param_name }}_bottom" value="{{ dimension_bottom }}" />
		</div>
		<div class="fusion-builder-dimension">
			<span class="add-on"><i class="dashicons dashicons-arrow-left-alt"></i></span>
			<input type="text" name="{{ param.param_name }}_left" id="{{ param.param_name }}_left" value="{{ dimension_left }}" />
		</div>
		<input type="hidden" name="{{ param.param_name }}" id="{{ param.param_name }}" value="{{ option_value }}" />
	</div>
<# } #>
