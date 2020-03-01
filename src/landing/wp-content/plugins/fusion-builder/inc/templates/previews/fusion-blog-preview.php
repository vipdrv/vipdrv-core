<script type="text/template" id="fusion-builder-block-module-blog-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<?php printf( esc_html__( 'layout = %s', 'fusion-builder' ), '{{ params.layout }}' ); ?>
	<br />
	<# if ( 'grid' === params.blog_grid_columns ) { #>
		<?php printf( esc_html__( 'columns = %s', 'fusion-builder' ), '{{ params.blog_grid_columns }}' ); ?>
	<# } #>

</script>
