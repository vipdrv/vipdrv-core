<?php
/**
 * FAQ Element Preview.
 *
 * @package Fusion-Core
 * @since 3.1.0
 */

?>
<script type="text/template" id="fusion-builder-block-module-faq-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>
	<# var categories = ( null === params.cats_slug || '' === params.cats_slug ) ? 'All' : params.cats_slug; #>
	<?php printf( esc_html__( 'categories = %s', 'fusion-core' ), '{{ categories }}' ); ?>
</script>
