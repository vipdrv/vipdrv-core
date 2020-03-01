<script type="text/template" id="fusion-builder-multi-child-sortable">
	<span class="multi-element-child-name">{{ ( ( atts.element_name ) ? atts.element_name : fusionAllElements[atts.element_type].name ) }}</span>
	<div class="fusion-builder-controls">
		<a href="#" class="fusion-builder-multi-setting-remove" title="{{ fusionBuilderText.delete_item }}"><span class="fusiona-trash-o"></span></a>
		<a href="#" class="fusion-builder-multi-setting-clone" title="{{ fusionBuilderText.clone_item }}"><span class="fusiona-file-add"></span></a>
		<a href="#" class="fusion-builder-multi-setting-options" title="{{ fusionBuilderText.edit_item }}"><span class="fusiona-pen"></span></a>
	</div>
</script>
