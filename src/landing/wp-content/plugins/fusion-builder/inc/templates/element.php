<script type="text/template" id="fusion-builder-block-module-template">
	<div class="fusion-builder-module-controls-container">
		<div class="fusion-builder-controls fusion-builder-module-controls">
			<a href="#" class="fusion-builder-settings" title="{{ fusionBuilderText.element_settings }}"><span class="fusiona-pen"></span></a>
			<a href="#" class="fusion-builder-clone fusion-builder-clone-module" title="{{ fusionBuilderText.clone_element }}"><span class="fusiona-file-add"></span></a>
			<a href="#" class="fusion-builder-save fusion-builder-save-module-dialog" title="{{ fusionBuilderText.save_element }}"><span class="fusiona-drive"></span></a>
			<a href="#" class="fusion-builder-remove" title="{{ fusionBuilderText.delete_element }}"><span class="fusiona-trash-o"></span></a>
		</div>
	</div>
	<# if ( typeof( fusionAllElements[element_type].preview ) == 'undefined' ) { #>
		<span class="fusion-builder-module-title">
			<# if ( typeof( fusionAllElements[element_type].icon ) !== 'undefined' ) { #>
				<div class="fusion-module-icon {{ fusionAllElements[element_type].icon }}"></div>
			<# } #>
			{{ typeof( fusionAllElements[element_type].name ) !== 'undefined' ?  fusionAllElements[element_type].name : '' }}
		</span>
	<# } #>
	<div class="fusion-builder-module-preview"></div>
</script>
