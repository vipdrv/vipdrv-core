<?php

global $fusion_settings;
if ( ! $fusion_settings ) {
	$fusion_settings = Fusion_Settings::get_instance();
}

$size  = strtolower( $fusion_settings->get( 'button_size' ) );
$shape = strtolower( $fusion_settings->get( 'button_shape' ) );
$type  = strtolower( $fusion_settings->get( 'button_type' ) );
$gradient_top = $gradient_bottom = $accent_color = $border_color = $border_width = '';
/**
 * Determines if a color needs adjusting or not.
 *
 * @param string $color The color.
 * @return bool
 */
function color_needs_adjustment( $color ) {
	if ( '#ffffff' === $color || 'transparent' === $color || '0' === Fusion_Color::new_color( $color )->alpha ) {
		return true;
	}

	return false;
}

$gradient_top = color_needs_adjustment( $fusion_settings->get( 'button_gradient_top_color' ) ) ? '#f8f8f8' : $fusion_settings->get( 'button_gradient_top_color' );
$gradient_bottom = color_needs_adjustment( $fusion_settings->get( 'button_gradient_bottom_color' ) ) ? '#f8f8f8' : $fusion_settings->get( 'button_gradient_bottom_color' );
$accent_color = color_needs_adjustment( $fusion_settings->get( 'button_accent_color' ) ) ? '#f8f8f8' : $fusion_settings->get( 'button_accent_color' );
$border_width = $fusion_settings->get( 'button_border_width' );
?>

<script type="text/template" id="fusion-builder-block-module-button-preview-template">

	<#
	var button_style = '';
	var button_icon = '';

	if ( '' === params.shape ) {
		var button_shape = '<?php echo $shape; ?>';
	} else {
		var button_shape = params.shape;
	}

	if ( '' === params.type ) {
		var button_type = '<?php echo $type; ?>';
	} else {
		var button_type = params.type;
	}

	if ( '' === params.size || ! params.size ) {
		var button_size = '<?php echo $size; ?>';
	} else {
		var button_size = params.size;
	}

	if ( 'default' === params.color ) {
		var accent_color = '<?php echo $accent_color; ?>';
		var border_width = '<?php echo $border_width; ?>';
		var button_background = 'linear-gradient(<?php echo $gradient_top; ?>, <?php echo $gradient_bottom; ?>)';

	} else if ( 'custom' === params.color ) {
		var accent_color = ( params.accent_color ) ? params.accent_color : '<?php echo $accent_color; ?>';

		if ( params.border_width ) {
			var border_width = ( -1 === params.border_width.indexOf( 'px' ) ) ? params.border_width + 'px' : params.border_width;
		} else {
			var border_width = '<?php echo $border_width; ?>';
		}

		var gradient_top = ( params.button_gradient_top_color ) ? params.button_gradient_top_color : '<?php echo $gradient_top; ?>';
		var gradient_bottom = ( params.button_gradient_bottom_color ) ? params.button_gradient_bottom_color : '<?php echo $gradient_bottom; ?>';

		if ( '' !== gradient_top && '' !== gradient_bottom ) {
			var button_background = 'linear-gradient(' + gradient_top + ', ' + gradient_bottom + ')';
		} else {
			var button_background = gradient_top;
		}

		if ( ( '' === button_background || ( -1 !== gradient_top.indexOf( 'rgba(255,255,255' ) && -1 !== gradient_bottom.indexOf( 'rgba(255,255,255' ) ) ) && ( '#ffffff' === accent_color || -1 !== accent_color.indexOf( 'rgba(255,255,255' ) ) ) {
			button_background = '#dddddd';
		}

	} else {
		var button_color = params.color;
	}

	if ( '' !== params.icon ) {
		var button_icon = params.icon;
	} else {
		var button_icon = 'no-icon';
	}
	#>

	<# if ( 'custom' === params.color || 'default' === params.color ) { #>

		<a class="fusion-button button-default button-{{ button_shape }} button-{{ button_type }} button-{{ button_size }}" style="background: {{ button_background }}; border: {{ border_width }} solid {{ accent_color }}; color: {{ accent_color }}"><span class="fusion-button-text"><span class="fusion-module-icon fa {{ button_icon }}"></span>{{{ params.element_content }}}</span></a>

	<# } else { #>

		<a class="fusion-button button-default button-{{ button_shape }} button-{{ button_type }} button-{{ button_size }} button-{{ button_color }}"><span class="fusion-button-text"><span class="fusion-module-icon fa {{ button_icon }}"></span>{{{ params.element_content }}}</span></a>

	<# }#>
</script>
