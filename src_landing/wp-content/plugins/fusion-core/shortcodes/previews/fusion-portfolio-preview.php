<?php
/**
 * Portfolio Element Preview.
 *
 * @package Fusion-Core
 * @since 3.1.0
 */

?>
<script type="text/template" id="fusion-builder-block-module-portfolio-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>
	<?php printf( esc_html__( 'layout = %s', 'fusion-core' ), '{{ params.layout }}' ); ?>
	<br />

	<#
	var categories = ( null === params.cat_slug || '' === params.cat_slug ) ? 'All' : params.cat_slug;
	var tags = ( null === params.tag_slug || '' === params.tag_slug ) ? 'All' : params.tag_slug;
	#>
	<# if ( 'tag' === params.pull_by ) { #>
		<?php printf( esc_html__( 'tags = %s', 'fusion-core' ), '{{ tags }}' ); ?>
	<# } else { #>
		<?php printf( esc_html__( 'categories = %s', 'fusion-core' ), '{{ categories }}' ); ?>
	<# } #>

</script>
