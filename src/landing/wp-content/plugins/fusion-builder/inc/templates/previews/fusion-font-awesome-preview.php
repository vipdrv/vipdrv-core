<script type="text/template" id="fusion-builder-block-module-font-awesome-preview-template">

	<#
	var
	icon_color = '',
	circle_background = '',
	icon_color = params.iconcolor,
	circle_background = params.circlecolor;
	#>

	<# if ( params.circle === 'yes' ) { #>
		<div class="fusion-icon-circle-preview" style="background: {{ circle_background }}">
	<# } #>
		<span class="fa-preview fa {{ params.icon }}" style="color: {{ icon_color }}"></span>
	<# if ( params.circle === 'yes' ) { #>
		</div>
	<# } #>

</script>
