<?php

if ( fusion_is_element_enabled( 'fusion_pricing_table' ) ) {

	if ( ! class_exists( 'FusionSC_PricingTable' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_PricingTable extends Fusion_Element {

			/**
			 * The pricing table counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $pricing_table_counter = 1;

			/**
			 * True if this is the first row, otherwise false.
			 *
			 * @access private
			 * @since 1.0
			 * @var bool
			 */
			private $is_first_row = true;

			/**
			 * True if this is the first column, otherwise defaults to false.
			 *
			 * @access private
			 * @since 1.0
			 * @var bool
			 */
			private $is_first_column = true;

			/**
			 * True if this is the list group is closed, otherwise false.
			 *
			 * @access private
			 * @since 1.0
			 * @var bool
			 */
			private $is_list_group_closed = false;

			/**
			 * Parent SC arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $parent_args;

			/**
			 * Arguments for the column.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected static $child_column_args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_pricingtable-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_pricingtable-shortcode-column-wrapper', array( $this, 'column_wrapper_attr' ) );
				add_filter( 'fusion_attr_pricingtable-shortcode-price', array( $this, 'price_attr' ) );
				add_filter( 'fusion_attr_pricingtable-shortcode-row', array( $this, 'row_attr' ) );
				add_filter( 'fusion_attr_pricingtable-shortcode-footer', array( $this, 'footer_attr' ) );

				add_shortcode( 'fusion_pricing_table', array( $this, 'render_parent' ) );
				add_shortcode( 'fusion_pricing_column', array( $this, 'render_child_column' ) );
				add_shortcode( 'fusion_pricing_price', array( $this, 'render_child_price' ) );
				add_shortcode( 'fusion_pricing_row', array( $this, 'render_child_row' ) );
				add_shortcode( 'fusion_pricing_footer', array( $this, 'render_child_footer' ) );

			}

			/**
			 * Render the parent shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_parent( $args, $content = '' ) {

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'  => fusion_builder_default_visibility( 'string' ),
						'class'           => '',
						'id'              => '',
						'backgroundcolor' => $fusion_settings->get( 'pricing_bg_color' ),
						'bordercolor'     => $fusion_settings->get( 'pricing_border_color' ),
						'columns'         => '',
						'dividercolor'    => $fusion_settings->get( 'pricing_divider_color' ),
						'type'            => '1',
					), $args
				);

				extract( $defaults );

				$this->parent_args = $defaults;

				$this->parent_args['columns'] = min( $this->parent_args['columns'], 6 );

				$this->set_num_of_columns( $content );

				$this->is_first_column = true;

				$styles = "<style type='text/css'>
				.pricing-table-{$this->pricing_table_counter} .panel-container, .pricing-table-{$this->pricing_table_counter} .standout .panel-container,
				.pricing-table-{$this->pricing_table_counter}.full-boxed-pricing {background-color:{$bordercolor};}
				.pricing-table-{$this->pricing_table_counter} .list-group .list-group-item,
				.pricing-table-{$this->pricing_table_counter} .list-group .list-group-item:last-child{background-color:{$backgroundcolor}; border-color:{$dividercolor};}
				.pricing-table-{$this->pricing_table_counter}.full-boxed-pricing .panel-wrapper:hover .panel-heading,
				.pricing-table-{$this->pricing_table_counter} .panel-wrapper:hover .list-group-item {background-color:$bordercolor;}
				.pricing-table-{$this->pricing_table_counter}.full-boxed-pricing .panel-heading{background-color:{$backgroundcolor};}
				.pricing-table-{$this->pricing_table_counter} .fusion-panel, .pricing-table-{$this->pricing_table_counter} .panel-wrapper:last-child .fusion-panel,
				.pricing-table-{$this->pricing_table_counter} .standout .fusion-panel, .pricing-table-{$this->pricing_table_counter}  .panel-heading,
				.pricing-table-{$this->pricing_table_counter} .panel-body, .pricing-table-{$this->pricing_table_counter} .panel-footer{border-color:{$dividercolor};}
				.pricing-table-{$this->pricing_table_counter} .panel-body,.pricing-table-{$this->pricing_table_counter} .panel-footer{background-color:{$bordercolor};}
				</style>";

				$html = $styles . '<div ' . FusionBuilder::attributes( 'pricingtable-shortcode' ) . '>' . do_shortcode( $content ) . '</div>';

				$this->pricing_table_counter++;

				return $html;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = array();

				$type = 'sep';
				if ( '1' == $this->parent_args['type'] ) {
					$type = 'full';
				}

				$attr['class'] = 'fusion-pricing-table pricing-table-' . $this->pricing_table_counter . ' ' . $type . '-boxed-pricing row fusion-columns-' . $this->parent_args['columns'] . ' columns-' . $this->parent_args['columns'] . ' fusion-clearfix';

				$attr = fusion_builder_visibility_atts( $this->parent_args['hide_on_mobile'], $attr );

				if ( $this->parent_args['class'] ) {
					$attr['class'] .= ' ' . $this->parent_args['class'];
				}

				if ( $this->parent_args['id'] ) {
					$attr['id'] = $this->parent_args['id'];
				}

				return $attr;

			}

			/**
			 * Render the child shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child_column( $args, $content = '' ) {

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'class'    => 'fusion-pricingtable-column',
						'id'       => '',
						'standout' => 'no',
						'title'    => '',
					), $args
				);

				extract( $defaults );

				self::$child_column_args = $defaults;

				$this->is_first_row = true;

				$html  = '<div ' . FusionBuilder::attributes( 'pricingtable-shortcode-column-wrapper' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'panel-container' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-panel' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'panel-heading' ) . '>';
				$html .= '<h3 ' . FusionBuilder::attributes( 'title-row' ) . '>' . $title . '</h3>';
				$html .= '</div>';
				$html .= do_shortcode( $content );

				if ( ! $this->is_list_group_closed ) {
					$html .= '</ul>';
				}

				$html .= '</div></div></div>';

				return $html;

			}

			/**
			 * Builds the column-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function column_wrapper_attr() {

				$attr = array();

				$columns = 1;
				if ( $this->parent_args['columns'] ) {
					$columns = 12 / $this->parent_args['columns'];
				}

				$attr['class'] = 'panel-wrapper fusion-column column col-lg-' . $columns . ' col-md-' . $columns . ' col-sm-' . $columns;

				if ( '5' == $this->parent_args['columns'] ) {
					$attr['class'] = 'panel-wrapper fusion-column column col-lg-2 col-md-2 col-sm-2';
				}

				if ( 'yes' == self::$child_column_args['standout'] ) {
					$attr['class'] .= ' standout';
				}

				if ( self::$child_column_args['class'] ) {
					$attr['class'] .= ' ' . self::$child_column_args['class'];
				}

				if ( self::$child_column_args['id'] ) {
					$attr['id'] = self::$child_column_args['id'];
				}

				return $attr;

			}

			/**
			 * Render the child shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child_price( $args, $content = '' ) {

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'currency'          => '',
						'currency_position' => 'left',
						'price'             => '',
						'time'              => '',
					), $args
				);

				extract( $defaults );

				$html = '<div ' . FusionBuilder::attributes( 'panel-body pricing-row' ) . '></div>' . do_shortcode( $content );

				if ( isset( $price ) && ( ! empty( $price ) || ( '0' == $price ) ) ) {

					$pricing_class = $pricing = '';
					$price = explode( '.' , $price );
					if ( array_key_exists( '1', $price ) ) {
						$pricing_class = 'price-with-decimal';
					}

					if ( 'right' != $currency_position ) {
						$pricing = '<span ' . FusionBuilder::attributes( 'currency' ) . '>' . $currency . '</span>';
					}

					$pricing .= '<span ' . FusionBuilder::attributes( 'integer-part' ) . '>' . $price[0] . '</span>';

					if ( array_key_exists( '1', $price ) ) {
						$pricing .= '<sup ' . FusionBuilder::attributes( 'decimal-part' ) . '>' . $price[1] . '</sup>';
					}

					if ( 'right' == $currency_position ) {
						$currency_classes = 'currency pos-right';
						$time_classes     = 'time pos-right';
						if ( ! array_key_exists( '1', $price ) ) {
							$currency_classes = 'currency pos-right price-without-decimal';
							$time_classes     = 'time pos-right price-without-decimal';
						}

						$pricing .= '<span ' . FusionBuilder::attributes( $currency_classes ) . '>' . $currency . '</span>';

						if ( $time ) {
							$pricing .= '<span ' . FusionBuilder::attributes( $time_classes ) . '>' . $time . '</span>';
						}
					}

					if ( $time && 'right' != $currency_position ) {
						$time_classes = 'time';
						if ( ! array_key_exists( '1', $price ) ) {
							$time_classes = 'time price-without-decimal';
						}

						$pricing .= '<span ' . FusionBuilder::attributes( $time_classes ) . '>' . $time . '</span>';
					}

					$html  = '<div ' . FusionBuilder::attributes( 'panel-body pricing-row' ) . '>';
					$html .= '<div ' . FusionBuilder::attributes( 'price ' . $pricing_class ) . '>' . $pricing . '</div></div>';
					$html .= do_shortcode( $content );

				}

				return $html;

			}

			/**
			 * Render the child shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child_row( $args, $content = '' ) {

				$html = '';

				if ( $this->is_first_row ) {
					$html = '<ul ' . FusionBuilder::attributes( 'list-group' ) . '>';
					$this->is_first_row = false;
				}

				$html .= '<li ' . FusionBuilder::attributes( 'list-group-item normal-row' ) . '>' . do_shortcode( $content ) . '</li>';

				return $html;

			}

			/**
			 * Render the child shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render_child_footer( $args, $content = '' ) {

				$html = '</ul><div ' . FusionBuilder::attributes( 'panel-footer footer-row' ) . '>' . do_shortcode( $content ) . '</div>';

				$this->is_list_group_closed = true;

				return $html;

			}

			/**
			 * Calculate the number of columns automatically.
			 *
			 * @access public
			 * @since 1.0
			 * @param string $content Content to be parsed.
			 */
			public function set_num_of_columns( $content ) {
				if ( ! $this->parent_args['columns'] ) {
					preg_match_all( '/(\[fusion_pricing_column (.*?)\](.*?)\[\/fusion_pricing_column\])/s', $content, $matches );
					$this->parent_args['columns'] = 1;
					if ( is_array( $matches ) && ! empty( $matches ) ) {
						$this->parent_args['columns'] = count( $matches[0] );
						if ( $this->parent_args['columns'] > 6 ) {
							$this->parent_args['columns'] = 6;
						}
					}
				} elseif ( $this->parent_args['columns'] > 6 ) {
					$this->parent_args['columns'] = 6;
				}
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function add_styling() {

				global $wp_version, $content_min_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = array(
					'.fusion-pricing-table .panel-body .price .integer-part',
					'.fusion-pricing-table .panel-body .price .decimal-part',
					'.full-boxed-pricing.fusion-pricing-table .standout .panel-heading h3',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'pricing_box_color' ) );

				$css['global']['.sep-boxed-pricing .panel-heading']['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'pricing_box_color' ) );
				$css['global']['.sep-boxed-pricing .panel-heading']['border-color']     = $fusion_library->sanitize->color( $fusion_settings->get( 'pricing_box_color' ) );

				$css['global']['.full-boxed-pricing.fusion-pricing-table .panel-heading h3']['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'full_boxed_pricing_box_heading_color' ) );

				$css['global']['.sep-boxed-pricing .panel-heading h3']['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'sep_pricing_box_heading_color' ) );

				$css[ $content_min_media_query ]['.sep-boxed-pricing .panel-wrapper']['padding'] = '0';
				$css[ $content_min_media_query ]['.fusion-pricing-table .standout .panel-container']['z-index'] = '1000';
				$css[ $content_min_media_query ]['.fusion-pricing-table .standout .panel-footer, .fusion-pricing-table .standout .panel-heading']['padding'] = '20px';
				$css[ $content_min_media_query ]['.full-boxed-pricing']['padding'] = '0 9px';
				$css[ $content_min_media_query ]['.full-boxed-pricing']['background-color'] = '#F8F8F8';
				$css[ $content_min_media_query ]['.full-boxed-pricing .panel-container']['padding'] = '9px 0';
				$css[ $content_min_media_query ]['.full-boxed-pricing .panel-wrapper:last-child .fusion-panel']['border-right'] = '1px solid #E5E4E3';
				$css[ $content_min_media_query ]['.full-boxed-pricing .fusion-panel']['border-right'] = 'none';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['position'] = 'relative';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['box-sizing'] = 'content-box';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['margin'] = '-10px -9px';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['padding'] = '9px';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['box-shadow'] = '0 0 6px 6px rgba(0, 0, 0, 0.08)';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-container']['background-color'] = '#F8F8F8';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .fusion-panel']['border-right'] = '1px solid #E5E4E3';
				$css[ $content_min_media_query ]['.full-boxed-pricing .standout .panel-heading h3']['color'] = '#A0CE4E';
				$css[ $content_min_media_query ]['.sep-boxed-pricing']['margin'] = '0 -15px 20px';
				$css[ $content_min_media_query ]['.sep-boxed-pricing .panel-wrapper']['margin'] = '0';
				$css[ $content_min_media_query ]['.sep-boxed-pricing .panel-wrapper']['padding'] = '0 12px';
				$css[ $content_min_media_query ]['.sep-boxed-pricing .standout .panel-container']['margin'] = '-10px';
				$css[ $content_min_media_query ]['.sep-boxed-pricing .standout .panel-container']['box-shadow'] = '0 0 15px 5px rgba(0, 0, 0, 0.16)';

				$css[ $three_twenty_six_fourty_media_query ]['#wrapper .sep-boxed-pricing .panel-wrapper']['padding'] = '0';
				$css[ $ipad_portrait_media_query ]['#wrapper .sep-boxed-pricing .panel-wrapper']['padding'] = '0';

				$elements = array(
					'.full-boxed-pricing .column',
					'.sep-boxed-pricing .column',
				);
				$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'none';
				$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '10px';
				$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '0';
				$css[ $six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']         = '100%';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'none';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '10px';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '0';
				$css[ $ipad_portrait_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']         = '100%';
				$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['float']         = 'none';
				$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '10px';
				$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-left']   = '0';
				$css[ $three_twenty_six_fourty_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['width']         = '100%';

				$css['global']['.table-2 table thead']['background-color'] = $fusion_library->sanitize->color( fusion_library()->get_option( 'primary_color' ) );
				$css['global']['.table-2 table thead']['border-color'] = $fusion_library->sanitize->color( fusion_library()->get_option( 'primary_color' ) );

				return $css;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Pricing Table settings.
			 */
			public function add_options() {

				return array(
					'pricing_table_shortcode_section' => array(
						'label'       => esc_html__( 'Pricing Table Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'pricingtable_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'full_boxed_pricing_box_heading_color' => array(
								'label'       => esc_html__( 'Pricing Box Style 1 Heading Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of style 1 pricing table headings.', 'fusion-builder' ),
								'id'          => 'full_boxed_pricing_box_heading_color',
								'default'     => '#333333',
								'type'        => 'color',
							),
							'sep_pricing_box_heading_color' => array(
								'label'       => esc_html__( 'Pricing Box Style 2 Heading Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of style 2 pricing table headings.', 'fusion-builder' ),
								'id'          => 'sep_pricing_box_heading_color',
								'default'     => '#333333',
								'type'        => 'color',
							),
							'pricing_box_color' => array(
								'label'       => esc_html__( 'Pricing Box Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color portions of pricing boxes.', 'fusion-builder' ),
								'id'          => 'pricing_box_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'pricing_bg_color' => array(
								'label'       => esc_html__( 'Pricing Box Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the main background and title background.', 'fusion-builder' ),
								'id'          => 'pricing_bg_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
							'pricing_border_color' => array(
								'label'       => esc_html__( 'Pricing Box Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the outer border, pricing row and footer row backgrounds.', 'fusion-builder' ),
								'id'          => 'pricing_border_color',
								'default'     => '#f8f8f8',
								'type'        => 'color-alpha',
							),
							'pricing_divider_color' => array(
								'label'       => esc_html__( 'Pricing Box Divider Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the dividers in-between pricing rows.', 'fusion-builder' ),
								'id'          => 'pricing_divider_color',
								'default'     => '#ededed',
								'type'        => 'color-alpha',
							),
						),
					),
				);
			}
		}
	}

	new FusionSC_PricingTable();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_pricing_table() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Pricing Table', 'fusion-builder' ),
		'shortcode'  => 'fusion_pricing_table',
		'icon'       => 'fusiona-dollar',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-pricing-table-preview.php',
		'preview_id' => 'fusion-builder-block-module-pricing-table-preview-template',

		'custom_settings_view_name'     => 'ModuleSettingsTableView',
		'custom_settings_view_js'       => FUSION_BUILDER_PLUGIN_URL . 'inc/templates/custom/js/fusion-pricing-table-settings.js',
		'custom_settings_template_file' => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/custom/fusion-pricing-table-settings.php',
		// 'custom_settings_template_css'  => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/custom/css/fusion-pricing-table-settings.css',
		'on_save'           => 'pricingTableShortcodeFilter',
		'admin_enqueue_js'  => FUSION_BUILDER_PLUGIN_URL . 'shortcodes/js/fusion-pricing-table.js',
		'params'            => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of pricing table.', 'fusion-builder' ),
				'param_name'  => 'type',
				'value'       => array(
					'1' => esc_attr__( 'Style 1', 'fusion-builder' ),
					'2' => esc_attr__( 'Style 2', 'fusion-builder' ),
				),
				'default'     => '1',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Leave blank for default.', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'pricing_bg_color' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Leave blank for default.', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'pricing_border_color' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Divider Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Leave blank for default.', 'fusion-builder' ),
				'param_name'  => 'dividercolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'pricing_divider_color' ),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Short Code', 'fusion-builder' ),
				'description' => esc_attr__( 'Pricing Table short code content.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_pricing_column title="Standard" standout="no" class="" id=""][fusion_pricing_price currency="$" price="15.55" time="monthly"][/fusion_pricing_price][fusion_pricing_row]Feature 1[/fusion_pricing_row][fusion_pricing_footer][/fusion_pricing_footer][/fusion_pricing_column][fusion_pricing_column title="Premium" standout="yes" class="" id=""][fusion_pricing_price currency="$" price="25.55" time="monthly"][/fusion_pricing_price][fusion_pricing_row]Feature 1[/fusion_pricing_row][fusion_pricing_footer][/fusion_pricing_footer][/fusion_pricing_column]',
				'hidden'      => true,
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_pricing_table' );
