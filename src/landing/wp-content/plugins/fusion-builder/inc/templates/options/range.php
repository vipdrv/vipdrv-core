	<#
		min = ( ( param.min ) ? param.min : 0 );
		max = ( ( param.max ) ? param.max : 100 );
		step = ( ( param.step ) ? param.step : '1' );
		defaultStatus = ( ( param.default ) ? 'fusion-with-default' : '' );
		isChecked = ( ( '' == option_value ) ? 'checked' : '' );
		regularId = ( ( ! param.default ) ? param.param_name : 'slider' + param.param_name );
		displayValue = ( ( '' == option_value ) ? param.default : option_value );

		if ( '.' === step.charAt( 0 ) ) {
			step = '0' + step;
		}
	#>
	<input
		type="text"
		name="{{ param.param_name }}"
		id="{{ regularId }}"
		value="{{ displayValue }}"
		class="fusion-slider-input {{ defaultStatus }} <# if ( param.default ) { #>fusion-hide-from-atts<# } #>"
	/>
	<div
		class="fusion-slider-container {{ param.param_name }}"
		data-id="{{ param.param_name }}"
		data-min="{{ min }}"
		data-max="{{ max }}"
		data-step="{{ step }}"
		data-direction="<?php echo ( is_rtl() ) ? 'rtl' : 'ltr'; ?>">
	</div>
	<# if ( param.default ) { #>
	<input type="hidden"
		   id="{{ param.param_name }}"
		   value="{{ option_value }}"
		   class="fusion-hidden-value" />
	<# } #>
