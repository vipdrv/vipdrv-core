<script type="text/template" id="fusion-builder-row-inner-template">
	<h4 class="fusion_module_title">
		{{ fusionAllElements[element_type].name }}
	</h4>
	<div class="fusion-builder-row-content fusion-builder-data-cid" data-cid="{{ cid }}">

		<div class="fusion-builder-modal-top-container">
			<# if ( typeof( fusionAllElements[element_type] ) !== 'undefined' ) { #>
				<h2>{{ fusionAllElements[element_type].name }}</h2>
			<# }; #>
			<div class="fusion-builder-inner-row-close-icon fusion-builder-modal-close fusiona-plus2"></div>
		</div>

		<div class="fusion-builder-modal-bottom-container">
			<a href="#" class="fusion-builder-inner-row-close"><span>{{ fusionBuilderText.save }}</span></a>
			<a href="#" class="fusion-builder-inner-row-close"><span>{{ fusionBuilderText.cancel }}</span></a>
		</div>
		<div id="fusion-builder-row-{{ cid }}" class="fusion-builder-row-container-inner"></div>
	</div>


	<div class="fusion-builder-module-controls-container">
		<div class="fusion-builder-controls fusion-builder-inner-row-controls">
			<a href="#" class="fusion-builder-inner-row-overlay" title="{{ fusionBuilderText.column_settings }}"><span class="fusiona-pen"></span></a>
			<a href="#" class="fusion-builder-clone fusion-builder-clone-inner-row" title="{{ fusionBuilderText.clone_inner_columns }}"><span class="fusiona-file-add"></span></a>
			<a href="#" class="fusion-builder-save-inner-row-dialog-button" title="{{ fusionBuilderText.save_inner_columns }}"><span class="fusiona-drive"></span></a>
			<a href="#" class="fusion-builder-remove-inner-row" title="{{ fusionBuilderText.delete_inner_columns }}"><span class="fusiona-trash-o"></span></a>
		</div>
	</div>
</script>
