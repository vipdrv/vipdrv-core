<script type="text/template" id="fusion-builder-block-module-settings-table-template">

	<div class="fusion-builder-modal-top-container">
		<# if ( typeof( fusionAllElements[atts.element_type] ) !== 'undefined' ) { #>
				<h2>{{ fusionAllElements[atts.element_type].name }}</h2>
		<# }; #>

		<div class="fusion-builder-modal-close fusiona-plus2"></div>
		<ul class="fusion-tabs-menu">
			<li class=""><a href="#table-options">{{ fusionBuilderText.table_options }}</a></li>
			<li class=""><a href="#table">{{ fusionBuilderText.table }}</a></li>
		</ul>
	</div>

	<div class="fusion-builder-modal-bottom-container">
		<a href="#" class="fusion-builder-modal-save"><span>

			<# if ( FusionPageBuilderApp.shortcodeGenerator === true && FusionPageBuilderApp.shortcodeGeneratorMultiElementChild !== true ) { #>
				{{ fusionBuilderText.insert }}
			<# } else { #>
				{{ fusionBuilderText.save }}
			<# } #>

		</span></a>

		<a href="#" class="fusion-builder-modal-close">
			<span>
				{{ fusionBuilderText.cancel }}
			</span>
		</a>
	</div>

	<div class="fusion-builder-main-settings fusion-builder-main-settings-full has-group-options">
		<div class="fusion-tabs">

			<div id="table-options" class="fusion-tab-content">

				<?php fusion_element_options_loop('fusionAllElements[atts.element_type].params'); ?>

			</div>

			<div id="table" class="fusion-tab-content">

				<div class="fusion-table-builder">
					<div class="fusion-builder-layouts-header-info">
						<h2>{{ fusionBuilderText.table_intro }}</h2>
						<span class="fusion-table-builder-add-column fusion-builder-button-default ">{{ fusionBuilderText.add_table_column }}</span>
						<span class="fusion-table-builder-add-row fusion-builder-button-default ">{{ fusionBuilderText.add_table_row }}</span>
					</div>
					<#
					var pricing_columns = FusionPageBuilderApp.findShortcodeMatches( atts.params.element_content, 'fusion_pricing_column' );

					if ( pricing_columns !== null ) {
						column_counter = 0;
						total_column_rows = 0;
						td = [];
						th = [];
						th_standout = [];
						td_pricing = {};
						tfoot_td = [];

						_.each( pricing_columns, function ( pricing_column ) {
							column_counter++;
							var row_counter = 0;
							row_content_array = [];

							var
							inner_reg_exp = FusionPageBuilderApp.regExpShortcode( 'fusion_pricing_column' ),
							pricing_column_element = pricing_column.match( inner_reg_exp ),
							pricing_column_attributes = pricing_column_element[3] !== '' ? window.wp.shortcode.attrs( pricing_column_element[3] ) : '',
							pricing_column_content = pricing_column_element[5],
							pricing_column_title = typeof pricing_column_attributes.named['title'] !== 'undefined' ? pricing_column_attributes.named['title'] : '';
							pricing_column_standout = typeof pricing_column_attributes.named['standout'] !== 'undefined' ? pricing_column_attributes.named['standout'] : '';

							th[column_counter] = pricing_column_title;
							th_standout[column_counter] = pricing_column_standout;


							if ( pricing_column_content !== 'undefined' && pricing_column_content !== '' ) {

								var
								pricing_column_rows = FusionPageBuilderApp.findShortcodeMatches( pricing_column_content, 'fusion_pricing_row' );

								if ( pricing_column_rows !== null ) {

									_.each( pricing_column_rows, function ( pricing_column_row ) {
										row_counter++;
										td[column_counter] = '';

										var
										inner_reg_exp_row = FusionPageBuilderApp.regExpShortcode( 'fusion_pricing_row' ),
										pricing_column_row_element = pricing_column_row.match( inner_reg_exp_row ),
										pricing_column_row_content = pricing_column_row_element[5];

										row_content = typeof pricing_column_row_content !== 'undefined' ? pricing_column_row_content : '';
										row_content_array[row_counter] = row_content;
										td[column_counter] = row_content_array;

									} );

									if ( total_column_rows < row_counter ) {
										total_column_rows = row_counter;
									}
								}
							}

							var pricing_prices = FusionPageBuilderApp.findShortcodeMatches( pricing_column_content, 'fusion_pricing_price' );

							if ( pricing_prices !== null ) {

								_.each( pricing_prices, function ( price_shortcode ) {

									var
									inner_reg_exp = FusionPageBuilderApp.regExpShortcode( 'fusion_pricing_price' ),
									price_shortcode_element = price_shortcode.match( inner_reg_exp ),
									price_shortcode_attributes = price_shortcode_element[3] !== '' ? window.wp.shortcode.attrs( price_shortcode_element[3] ) : '',

									currency = typeof price_shortcode_attributes.named['currency'] !== 'undefined' ? price_shortcode_attributes.named['currency'] : '',
									currency_position = typeof price_shortcode_attributes.named['currency_position'] !== 'undefined' ? price_shortcode_attributes.named['currency_position'] : 'left',
									price = typeof price_shortcode_attributes.named['price'] !== 'undefined' ? price_shortcode_attributes.named['price'] : '',
									time = typeof price_shortcode_attributes.named['time'] !== 'undefined' ? price_shortcode_attributes.named['time'] : '';

									td_pricing[column_counter] = {
										currency : currency,
										price : price,
										time : time,
										currency_position : currency_position
									};

								} );

							}

							// Column footer
							if ( pricing_column_content !== 'undefined' && pricing_column_content !== '' ) {

								var
								pricing_column_footers = FusionPageBuilderApp.findShortcodeMatches( pricing_column_content, 'fusion_pricing_footer' );

								if ( pricing_column_footers !== null ) {

									_.each( pricing_column_footers, function ( pricing_column_footer ) {
										tfoot_td[column_counter] = '';

										var
										inner_reg_exp_footer = FusionPageBuilderApp.regExpShortcode( 'fusion_pricing_footer' ),
										pricing_column_footer_element = pricing_column_footer.match( inner_reg_exp_footer ),
										pricing_column_footer_content = pricing_column_footer_element[5];

										footer_content = typeof pricing_column_footer_content !== 'undefined' ? pricing_column_footer_content : '';

										tfoot_td[column_counter] = footer_content;

									} );
								}
							}

						} );
					}
					#>

					<table class="fusion-builder-table">
						<thead>
							<tr>
								<#
								if ( pricing_columns !== null && pricing_columns !== '' ) {

									for ( c = 1; c <= column_counter; c++ ) { #>

										<th align="left" class="th-{{ c }}" data-th-id="{{ c }}">
											<div class="fusion-builder-table-hold">
												<div class="fusion-builder-table-column-options">
													<strong>{{ fusionBuilderText.column_title }}</strong>
													<span class="fa fusiona-trash-o fusion-builder-table-delete-column" title="{{ fusionBuilderText.delete_column }}" data-column-id="{{ c }}" />
													<span class="fa fusiona-file-add fusion-builder-table-clone-column" title="{{ fusionBuilderText.clone_column }}" data-column-id="{{ c }}" />
												</div>
											</div>
											<div class="fusion-builer-table-featured">
												<span>{{ fusionBuilderText.standout_design }}</span>
												<div class="fusion-form-radio-button-set ui-buttonset th_standout-{{c}}">

													<input type="hidden" id="standout-{{c}}" name="standout-{{c}}" value="{{th_standout[c]}}" class="button-set-value" />
													<a href="#" class="fusion-form-radio-button-set-button ui-button buttonset-item{{ typeof( th_standout[c] ) !== 'undefined' && th_standout[c] === 'no' ?  ' ui-state-active' : '' }}" data-value="no">{{ fusionBuilderText.no }}</a>

													<a href="#" class="fusion-form-radio-button-set-button ui-button buttonset-item{{ typeof( th_standout[c] ) !== 'undefined' && th_standout[c] === 'yes' ?  ' ui-state-active' : '' }}" data-value="yes">{{ fusionBuilderText.yes }}</a>
												</div>

											</div>
											<div class="fusion-builder-column-title">
												<input type="text" placeholder="{{ fusionBuilderText.head_title }}" value="{{ th[c] }}" />
											</div>
										</th>

									<# }

								} else { #>

									<th align="left" class="th-1" data-th-id="1">
										<div class="fusion-builder-table-hold">
											<div class="fusion-builder-table-column-options">
												<strong>{{ fusionBuilderText.column_title }}</strong>
												<span class="fa fusiona-trash-o fusion-builder-table-delete-column" title="{{ fusionBuilderText.delete_column }}" data-column-id="{{ c }}" />
												<span class="fa fusiona-file-add fusion-builder-table-clone-column" title="{{ fusionBuilderText.clone_column }}" data-column-id="{{ c }}" />
											</div>
										</div>
										<div class="fusion-builer-table-featured">
											<span>{{ fusionBuilderText.standout_design }}</span>
											<div class="fusion-form-radio-button-set ui-buttonset th_standout-{{c}}">

												<input type="hidden" id="standout-{{c}}" name="standout-{{c}}" value="{{th_standout[c]}}" class="button-set-value" />
												<a href="#" class="ui-button buttonset-item{{ typeof( th_standout[c] ) !== 'undefined' && th_standout[c] === 'no' ?  ' ui-state-active' : '' }}" data-value="no">{{ fusionBuilderText.no }}</a>

												<a href="#" class="ui-button buttonset-item{{ typeof( th_standout[c] ) !== 'undefined' && th_standout[c] === 'yes' ?  ' ui-state-active' : '' }}" data-value="yes">{{ fusionBuilderText.yes }}</a>
											</div>

										</div>
										<div class="fusion-builder-column-title">
											<input type="text" placeholder="{{ fusionBuilderText.head_title }}" value="{{ th[c] }}" />
										</div>
									</th>

								<# } #>

							</tr>
						</thead>

						<tbody>

							<tr class="price">
								<#
								if ( pricing_columns !== null && pricing_columns !== '' ) {

									for ( c = 1; c <= column_counter; c++ ) { #>

										<td class="td-{{ c }}" data-td-id="{{ c }}">
											<div class="fusion-currency-holder">
												<input type="text" class="currency-input" placeholder="{{ fusionBuilderText.currency }}" value="{{ td_pricing[c].currency }}" />
												<div class="fusion-form-radio-button-set ui-buttonset currencypos-{{c}}">

													<input type="hidden" id="currencypos-{{c}}" name="currencypos-{{c}}" value="{{ td_pricing[c].currency_position }}" class="button-set-value currency-position" />
													<a href="#" class="fusion-form-radio-button-set-button ui-button buttonset-item{{ typeof( td_pricing[c].currency_position ) !== 'undefined' && td_pricing[c].currency_position === 'left' ?  ' ui-state-active' : '' }}" data-value="left">{{ fusionBuilderText.currency_before }}</a>

													<a href="#" class="fusion-form-radio-button-set-button ui-button buttonset-item{{ typeof( td_pricing[c].currency_position ) !== 'undefined' && td_pricing[c].currency_position === 'right' ?  ' ui-state-active' : '' }}" data-value="right">{{ fusionBuilderText.currency_after }}</a>
												</div>
											</div>
											<input type="text" class="price-input" placeholder="{{ fusionBuilderText.price }}" value="{{ td_pricing[c].price }}" />
											<input type="text" class="time-input" placeholder="{{ fusionBuilderText.period }}" value="{{ td_pricing[c].time }}" />
										</td>

									<# }

								} else { #>

									<td class="td-1" data-td-id="1">
										<input type="text" class="currency-input" placeholder="{{ fusionBuilderText.currency }}" value="" />
										<input type="text" class="price-input" placeholder="{{ fusionBuilderText.price }}" value="" />
										<input type="text" class="time-input" placeholder="{{ fusionBuilderText.period }}" value="" />
									</td>

								<# } #>
							</tr>

							<#
							if ( pricing_columns !== null && pricing_columns !== '' ) {

								for ( i = 1; i <= total_column_rows; i++ ) { #>

									<tr class="fusion-table-row tr-{{ i }}" data-tr-id="{{ i }}">

										<# for ( c = 1; c <= column_counter; c++ ) {

											if ( typeof td[c] !== 'undefined' ) {

												var td_value = typeof td[c][i] !== 'undefined' && td[c][i] !== '' ? td[c][i] : ''; #>

												<td class="td-{{ c }}" data-td-id="{{ c }}"><input type="text" placeholder="{{ fusionBuilderText.enter_text }}" value="{{ td_value }}" /><span class="fa fusiona-trash-o fusion-builder-table-delete-row" title="{{ fusionBuilderText.delete_row }}" data-row-id="{{ c }}" /></td>

											<# } else { #>

												<td class="td-{{ c }}" data-td-id="{{ c }}"><input type="text" placeholder="{{ fusionBuilderText.enter_text }}" value="" /><span class="fa fusiona-trash-o fusion-builder-table-delete-row" title="{{ fusionBuilderText.delete_row }}" data-row-id="{{ c }}" /></td>

											<# } #>

										<# } #>

									</tr>

								<# }

							} else { #>

								<tr class="fusion-table-row tr-1" data-tr-id="1">
									<td class="td-1" data-td-id="1"><input type="text" placeholder="{{ fusionBuilderText.enter_text }}" value="" /><span class="fa fusiona-trash-o fusion-builder-table-delete-row" title="{{ fusionBuilderText.delete_row }}" data-row-id="{{ c }}" /></td>
								</tr>

							<# } #>

						</tbody>

						<tfoot>
							<tr>
								<#
								if ( pricing_columns !== null && pricing_columns !== '' ) {

									for ( c = 1; c <= column_counter; c++ ) {

										if ( typeof tfoot_td[c] !== 'undefined' ) {

											var footer_td_value = typeof tfoot_td[c] !== 'undefined' && tfoot_td[c] !== '' ? tfoot_td[c] : ''; #>

											<td class="td-{{ c }}" data-td-id="{{ c }}">
												<a href="#" class="fusion-builder-table-add-button fusion-builder-button-default" data-type="fusion_button" data-id="{{ c }}">{{ fusionBuilderText.add_button }}</a>
												<textarea placeholder="{{ fusionBuilderText.enter_text }}" id="button_{{ c }}">{{ footer_td_value }}</textarea>
											</td>

										<# } else { #>

											<td class="td-{{ c }}" data-td-id="{{ c }}">
												<a href="#" class="fusion-builder-table-add-button fusion-builder-button-default" data-type="fusion_button" data-id="{{ c }}">{{ fusionBuilderText.add_button }}</a>
												<textarea placeholder="{{ fusionBuilderText.enter_text }}" id="button_{{ c }}"></textarea>
											</td>

										<# }
									}

								} else { #>

									<td class="td-1" data-td-id="1">
										<a href="#" class="fusion-builder-table-add-button fusion-builder-button-default" data-type="fusion_button" data-id="1">{{ fusionBuilderText.add_button }}</a>
										<textarea placeholder="{{ fusionBuilderText.enter_text }}" id="button_1"></textarea>
									</td>

								<# } #>
							</tr>

						</tfoot>

					</table>

				</div>

			</div>

		</div>

	</div>

</script>
