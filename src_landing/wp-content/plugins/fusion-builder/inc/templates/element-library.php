<script type="text/template" id="fusion-builder-modules-template">
	<div class="fusion-builder-modal-top-container">
		<h2 class="fusion-builder-settings-heading">
			{{ fusionBuilderText.select_element }}
			<input type="text" class="fusion-elements-filter" placeholder="{{ fusionBuilderText.search_elements }}" />
		</h2>

		<ul class="fusion-tabs-menu">
			<li class=""><a href="#default-elements">{{ fusionBuilderText.builder_elements }}</a></li>
			<# if ( FusionPageBuilderApp.shortcodeGenerator !== true ) { #>
				<li class=""><a href="#custom-elements">{{ fusionBuilderText.library_elements }}</a></li>
			<# } #>
			<# if ( FusionPageBuilderApp.shortcodeGenerator === true ) { #>
				<li class=""><a href="#default-columns">{{ fusionBuilderText.columns }}</a></li>
			<# } #>
			<# if ( FusionPageBuilderApp.innerColumn == 'false' && FusionPageBuilderApp.shortcodeGenerator !== true ) { #>
				<li class=""><a href="#inner-columns">{{ fusionBuilderText.inner_columns }}</a></li>
			<# } #>
		</ul>
	</div>

	<div class="fusion-builder-main-settings fusion-builder-main-settings-full has-group-options">
		<div class="fusion-builder-all-elements-container">
			<div class="fusion-tabs">
				<div id="default-elements" class="fusion-tab-content">
					<ul class="fusion-builder-all-modules">
						<# _.each( modules, function(module) { #>
							<li class="{{ module.label }} fusion-builder-element">
								<h4 class="fusion_module_title">
									<# if ( typeof( fusionAllElements[module.label].icon ) !== 'undefined' ) { #>
										<div class="fusion-module-icon {{ fusionAllElements[module.label].icon }}"></div>
									<# } #>
									{{ module.title }}
								</h4>
								<span class="fusion_module_label">{{ module.label }}</span>
							</li>
						<# } ); #>
					</ul>
				</div>
				<# if ( FusionPageBuilderApp.innerColumn == 'false' && FusionPageBuilderApp.shortcodeGenerator !== true ) { #>
					<div id="inner-columns" class="fusion-tab-content">
						<?php echo fusion_builder_inner_column_layouts(); // WPCS: XSS ok. ?>
					</div>
				<# } #>
				<# if ( FusionPageBuilderApp.shortcodeGenerator === true ) { #>
					<div id="default-columns" class="fusion-tab-content">
						<?php echo fusion_builder_generator_column_layouts(); // WPCS: XSS ok. ?>
					</div>
				<# } #>
				<div id="custom-elements" class="fusion-tab-content"></div>
			</div>
		</div>
	</div>

	<div class="fusion-builder-modal-bottom-container">
		<a href="#" class="fusion-builder-modal-close"><span>{{ fusionBuilderText.cancel }}</span></a>
	</div>
</script>
