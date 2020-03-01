<script type="text/template" id="fusion-builder-block-module-checklist-preview-template">
	<h4 class="fusion_module_title"><span class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></span>{{ fusionAllElements[element_type].name }}</h4>

	<ul>
		<#
		var
		content = typeof params.element_content !== 'undefined' ? params.element_content : '',
		shortcode_reg_exp = window.wp.shortcode.regexp( 'fusion_li_item' ),
		shortcode_inner_reg_exp = new RegExp( '\\[(\\[?)(fusion_li_item)(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' ),
		shortcode_matches = content.match( shortcode_reg_exp ),
		counter = 0;

		_.each( shortcode_matches, function ( inner_item ) {

			if ( counter < 3 ) {
				var
				shortcode_element = inner_item.match( shortcode_inner_reg_exp ),
				shortcode_content = shortcode_element[5];
				shortcode_icon = shortcode_element[3];
				shortcode_icon = shortcode_icon.replace( '"', '' ).replace( 'icon=', '' ).replace( '"', '' );

				if ( shortcode_icon === ' ' || shortcode_icon === '' ) {
					shortcode_icon = params.icon;
				}

				if ( /<[a-z][\s\S]*>/i.test( shortcode_content ) ) {
					shortcode_content = jQuery.parseHTML( shortcode_content );
					shortcode_content = jQuery(shortcode_content).text();
				} else {
					shortcode_content = shortcode_content;
				}

				#>
				<li><# if ( shortcode_icon !== '' ) { #><span class="fa fusion-checklist-preview-icon {{ shortcode_icon }}"></span><# } #> {{ shortcode_content }}</li>
			<#
			}

			counter++;

		} );

		if ( counter > 3 ) { #>
			<span>...</span>
		<#
		}
		#>
	</ul>

</script>
