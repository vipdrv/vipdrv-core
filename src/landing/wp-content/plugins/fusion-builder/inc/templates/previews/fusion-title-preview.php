<?php
global $fusion_settings;
if ( ! $fusion_settings ) {
	$fusion_settings = Fusion_Settings::get_instance();
}

$theme_options_style = strtolower( $fusion_settings->get( 'title_style_type' ) );
?>
<script type="text/template" id="fusion-builder-block-module-title-preview-template">

	<div class="fusion-title-preview">
		<#
		var style_type = ( params.style_type ) ? params.style_type.replace( ' ', '_' ) : 'default';
		var
		content = params.element_content,
		text_blocks       = jQuery.parseHTML( content ),
		shortcode_content = '';

		if ( 'default' === params.style_type ) {
			style_type = '<?php echo $theme_options_style; ?>';
			style_type = style_type.replace( ' ', '_' );
		}

		jQuery(text_blocks).each(function() {
			shortcode_content += jQuery(this).text();
		});

		var align = 'align-' + params.content_align;
		#>

		<span class="{{ style_type }}" style="border-color: {{ params.sep_color }};"><sub class="title_text {{ align }}">{{ shortcode_content }}</sub></span>
	</div>

</script>
