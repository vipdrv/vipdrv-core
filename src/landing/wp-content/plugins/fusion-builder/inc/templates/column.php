<script type="text/template" id="fusion-builder-column-template">
	<div class="column-sizes">
		<div class="column-size column-size-1_6" data-column-size="1_6">1/6</div>
		<div class="column-size column-size-1_5" data-column-size="1_5">1/5</div>
		<div class="column-size column-size-1_4" data-column-size="1_4">1/4</div>
		<div class="column-size column-size-1_3" data-column-size="1_3">1/3</div>
		<div class="column-size column-size-2_5" data-column-size="2_5">2/5</div>
		<div class="column-size column-size-1_2" data-column-size="1_2">1/2</div>
		<div class="column-size column-size-3_5" data-column-size="3_5">3/5</div>
		<div class="column-size column-size-2_3" data-column-size="2_3">2/3</div>
		<div class="column-size column-size-3_4" data-column-size="3_4">3/4</div>
		<div class="column-size column-size-4_5" data-column-size="4_5">4/5</div>
		<div class="column-size column-size-5_6" data-column-size="5_6">5/6</div>
		<div class="column-size column-size-1_1" data-column-size="1_1">1/1</div>
	</div>

	<div class="fusion-builder-controls fusion-builder-column-controls">
		<a href="#" class="fusion-builder-resize-column" title="{{ fusionBuilderText.resize_column }}">
			<# if ( typeof layout !== 'undefined' ) { #>
				{{ layout.replace('_','/') }}
			<# } #>
		</a>
		<a href="#" class="fusion-builder-settings fusion-builder-settings-column" title="{{ fusionBuilderText.column_settings }}"><span class="fusiona-pen"></span></a>
		<a href="#" class="fusion-builder-clone fusion-builder-clone-column" title="{{ fusionBuilderText.clone_column }}"><span class="fusiona-file-add"></span></a>
		<a href="#" class="fusion-builder-save fusion-builder-save-column-dialog" title="{{ fusionBuilderText.save_column }}"><span class="fusiona-drive"></span></a>
		<a href="#" class="fusion-builder-remove fusion-builder-remove-column" title="{{ fusionBuilderText.delete_column }}"><span class="fusiona-trash-o"></span></a>
	</div>
	<a href="#" class="fusion-builder-add-element" title="{{ fusionBuilderText.add_element }}"><span class="fusiona-plus"></span> {{ fusionBuilderText.element }}</a>
</script>
