<script type="text/template" id="fusion-builder-block-module-text-preview-template">

	<#
	var
	content = params.element_content,
	text_block      = jQuery.parseHTML( content ),
	text_block_html = '';

	jQuery(text_block).each(function() {

		if ( jQuery(this).get(0).tagName != 'IMG' && typeof jQuery(this).get(0).tagName != 'undefined' ) {
			var childrens = jQuery(jQuery(this).get(0)).find('*');
			var child_img = false;
			if(childrens.length >= 1) {
				jQuery.each(childrens, function() {
					if(jQuery(this).get(0).tagName == 'IMG') {
						child_img = true;
					}
				});
			}
			if(child_img == true) {
				text_block_html += jQuery(this).outerHTML();
			} else {
				text_block_html += jQuery(this).text();
			}
		} else {
			text_block_html += jQuery(this).outerHTML();
		}
	});
	#>

	{{{ text_block_html }}}

</script>
