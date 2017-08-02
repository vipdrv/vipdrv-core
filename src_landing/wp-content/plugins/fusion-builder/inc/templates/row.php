<script type="text/template" id="fusion-builder-row-template">
	<div class="fusion-builder-row-content fusion-builder-data-cid" data-cid="{{ cid }}">
		<div id="fusion-builder-row-{{ cid }}" class="fusion-builder-row-container">
			<#
			var emptSectionText = fusionBuilderText.empty_section,
				sectionId = cid - 1;

			if ( true === jQuery( '.fusion-builder-section-content[data-cid="' + sectionId + '"]' ).data( 'bg' ) ) {
				emptSectionText = fusionBuilderText.empty_section_with_bg;
			}
			#>
			<div href="#" class="fusion-builder-empty-section">{{ emptSectionText }}</div>
		</div>
	</div>
	<a href="#" class="fusion-builder-insert-column"><span class="fusiona-plus"></span> {{ fusionBuilderText.columns }}</a>
</script>
