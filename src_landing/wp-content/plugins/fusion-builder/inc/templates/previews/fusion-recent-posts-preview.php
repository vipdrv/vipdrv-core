<script type="text/template" id="fusion-builder-block-module-recent-posts-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>
	<?php printf( esc_html__( 'layout = %s', 'fusion-builder' ), '{{ params.layout }}' ); ?>
	<br />
	<?php printf( esc_html__( 'columns = %s', 'fusion-builder' ), '{{ params.columns }}' ); ?>
	<br />
	<#
	var categories = ( null === params.cat_slug || '' === params.cat_slug ) ? 'All' : params.cat_slug;
	var tags = ( null === params.tag_slug || '' === params.tag_slug ) ? 'All' : params.tag_slug;
	#>
	<# if ( 'tag' === params.pull_by ) { #>
		<?php printf( esc_html__( 'tags = %s', 'fusion-builder' ), '{{ tags }}' ); ?>
	<# } else { #>
		<?php printf( esc_html__( 'categories = %s', 'fusion-builder' ), '{{ categories }}' ); ?>
	<# } #>

</script>
