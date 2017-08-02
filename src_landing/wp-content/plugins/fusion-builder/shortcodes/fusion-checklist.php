<?php

if ( fusion_is_element_enabled( 'fusion_checklist' ) ) {

	if ( ! class_exists( 'FusionSC_Checklist' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Checklist extends Fusion_Element {

			/**
			 * The checklist counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $checklist_counter = 1;

			/**
			 * The CSS class of circle elements.
			 *
			 * @access private
			 * @since 1.0
			 * @var string
			 */
			private $circle_class = 'circle-no';

			/**
			 * Parent SC arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $parent_args;

			/**
			 * Child SC arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $child_args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_checklist-shortcode', array( $this, 'attr' ) );
				add_shortcode( 'fusion_checklist', array( $this, 'render_parent' ) );

				add_filter( 'fusion_attr_checklist-shortcode-li-item', array( $this, 'li_attr' ) );
				add_filter( 'fusion_attr_checklist-shortcode-span', array( $this, 'span_attr' ) );
				add_filter( 'fusion_attr_checklist-shortcode-icon', array( $this, 'icon_attr' ) );
				add_filter( 'fusion_attr_checklist-shortcode-item-content', array( $this, 'item_content_attr' ) );

				add_shortcode( 'fusion_li_item', array( $this, 'render_child' ) );

			}

			/**
			 * Render the parent shortcode.
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args   Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string         HTML output.
			 */
			public function render_parent( $args, $content = '' ) {

				global $fusion_settings;
				if ( ! $fusion_settings ) {
					$fusion_settings = Fusion_Settings::get_instance();
				}

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'circle'         => strtolower( $fusion_settings->get( 'checklist_circle' ) ),
						'circlecolor'    => $fusion_settings->get( 'checklist_circle_color' ),
						'icon'           => 'fa-check',
						'iconcolor'      => $fusion_settings->get( 'checklist_icons_color' ),
						'size'           => '13px',
					), $args
				);

				$defaults['size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['size'], 'px' );

				$defaults['circle'] = ( 1 == $defaults['circle'] ) ? 'yes' : $defaults['circle'];

				// Fallbacks for old size parameter and 'px' check.
				if ( 'small' === $defaults['size'] ) {
					$defaults['size'] = '13px';
				} elseif ( 'medium' === $defaults['size'] ) {
					$defaults['size'] = '18px';
				} elseif ( 'large' === $defaults['size'] ) {
					$defaults['size'] = '40px';
				} elseif ( ! strpos( $defaults['size'], 'px' ) ) {
					$defaults['size'] = $defaults['size'] . 'px';
				}

				// Dertmine line-height and margin from font size.
				$font_size = str_replace( 'px', '', $defaults['size'] );
				$defaults['circle_yes_font_size'] = $font_size * 0.88;
				$defaults['line_height'] = $font_size * 1.7;
				$defaults['icon_margin'] = $font_size * 0.7;
				$defaults['icon_margin_position'] = ( is_rtl() ) ? 'left' : 'right';
				$defaults['content_margin'] = $defaults['line_height'] + $defaults['icon_margin'];
				$defaults['content_margin_position'] = ( is_rtl() ) ? 'right' : 'left';

				extract( $defaults );

				$this->parent_args = $defaults;

				// Legacy checklist integration.
				if ( strpos( $content, '<li>' ) && strpos( $content, '[fusion_li_item' ) === false ) {
					$content = str_replace( '<ul>', '', $content );
					$content = str_replace( '</ul>', '', $content );
					$content = str_replace( '<li>', '[fusion_li_item]', $content );
					$content = str_replace( '</li>', '[/fusion_li_item]', $content );
				}

				$html = '<ul ' . FusionBuilder::attributes( 'checklist-shortcode' ) . '>' . do_shortcode( $content ) . '</ul>';

				$html = str_replace( '</li><br />', '</li>', $html );

				$this->checklist_counter++;

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

				$attr['class'] = 'fusion-checklist fusion-checklist-' . $this->checklist_counter;

				$attr = fusion_builder_visibility_atts( $this->parent_args['hide_on_mobile'], $attr );

				$font_size = str_replace( 'px', '', $this->parent_args['size'] );
				$line_height = $font_size * 1.7;
				$attr['style'] = 'font-size:' . $this->parent_args['size'] . ';line-height:' . $line_height . 'px;';

				if ( $this->parent_args['class'] ) {
					$attr['class'] .= ' ' . $this->parent_args['class'];
				}

				if ( $this->parent_args['id'] ) {
					$attr['id'] = $this->parent_args['id'];
				}

				return $attr;

			}

			/**
			 * Render the child shortcode.
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args   Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string         HTML output.
			 */
			public function render_child( $args, $content = '' ) {

				$defaults = shortcode_atts(
					array(
						'circle'      => '',
						'circlecolor' => '',
						'icon'        => '',
						'iconcolor'   => '',
					), $args
				);

				extract( $defaults );

				$this->child_args = $defaults;

				$html  = '<li ' . FusionBuilder::attributes( 'checklist-shortcode-li-item' ) . '>';
				$html .= '<span ' . FusionBuilder::attributes( 'checklist-shortcode-span' ) . '>';
				$html .= '<i ' . FusionBuilder::attributes( 'checklist-shortcode-icon' ) . '></i>';
				$html .= '</span>';
				$html .= '<div ' . FusionBuilder::attributes( 'checklist-shortcode-item-content' ) . '>' . do_shortcode( $content ) . '</div>';
				$html .= '</li>';

				$this->circle_class = 'circle-no';

				return $html;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function li_attr() {

				$attr = array();

				$attr['class'] = 'fusion-li-item';

				return $attr;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function item_content_attr() {
				return array(
					'class' => 'fusion-li-item-content',
					'style' => 'margin-' . $this->parent_args['content_margin_position'] . ':' . $this->parent_args['content_margin'] . 'px;',
				);
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function span_attr() {

				$attr = array(
					'style' => '',
				);

				if ( 'yes' === $this->child_args['circle'] || 'yes' === $this->parent_args['circle'] && ( 'no' !== $this->child_args['circle'] ) ) {
					$this->circle_class = 'circle-yes';

					if ( ! $this->child_args['circlecolor'] ) {
						$circlecolor = $this->parent_args['circlecolor'];
					} else {
						$circlecolor = $this->child_args['circlecolor'];
					}
					$attr['style'] = 'background-color:' . $circlecolor . ';';

					$attr['style'] .= 'font-size:' . $this->parent_args['circle_yes_font_size'] . 'px;';
				}

				$attr['class'] = 'icon-wrapper ' . $this->circle_class;

				$attr['style'] .= 'height:' . $this->parent_args['line_height'] . 'px;';
				$attr['style'] .= 'width:' . $this->parent_args['line_height'] . 'px;';
				$attr['style'] .= 'margin-' . $this->parent_args['icon_margin_position'] . ':' . $this->parent_args['icon_margin'] . 'px;';

				return $attr;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_attr() {

				if ( ! $this->child_args['icon'] ) {
					$icon = FusionBuilder::font_awesome_name_handler( $this->parent_args['icon'] );
				} else {
					$icon = FusionBuilder::font_awesome_name_handler( $this->child_args['icon'] );
				}

				if ( ! $this->child_args['iconcolor'] ) {
					$iconcolor = $this->parent_args['iconcolor'];
				} else {
					$iconcolor = $this->child_args['iconcolor'];
				}

				return array(
					'class' => 'fusion-li-icon fa ' . $icon,
					'style' => 'color:' . $iconcolor . ';',
				);
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Checklist settings.
			 */
			public function add_options() {

				return array(
					'checklist_shortcode_section' => array(
						'label'       => esc_html__( 'Checklist Element', 'fusion-builder' ),
						'description' => '',
						'id'          => 'checklist_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'checklist_icons_color' => array(
								'label'       => esc_html__( 'Checklist Icon Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the checklist icon.', 'fusion-builder' ),
								'id'          => 'checklist_icons_color',
								'default'     => '#ffffff',
								'type'        => 'color',
							),
							'checklist_circle' => array(
								'label'       => esc_html__( 'Checklist Circle', 'fusion-builder' ),
								'description' => esc_html__( 'Turn on if you want to display a circle background for checklists.', 'fusion-builder' ),
								'id'          => 'checklist_circle',
								'default'     => '1',
								'type'        => 'switch',
							),
							'checklist_circle_color' => array(
								'label'       => esc_html__( 'Checklist Circle Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the checklist circle background.', 'fusion-builder' ),
								'id'          => 'checklist_circle_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
								'required'    => array(
									array(
										'setting'  => 'checklist_circle',
										'operator' => '!=',
										'value'    => '0',
									),
								),
							),
						),
					),
				);
			}
		}
	}

	new FusionSC_Checklist();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_checklist() {

	global $fusion_settings;
	if ( ! $fusion_settings ) {
		$fusion_settings = Fusion_Settings::get_instance();
	}

	fusion_builder_map( array(
		'name'          => esc_attr__( 'Checklist', 'fusion-builder' ),
		'shortcode'     => 'fusion_checklist',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_li_item',
		'icon'          => 'fusiona-list-ul',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-checklist-preview.php',
		'preview_id'    => 'fusion-builder-block-module-checklist-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_li_item icon=""]' . esc_attr__( 'Your Content Goes Here', 'fusion-builder' ) . '[/fusion_li_item]',
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Global setting for all list items, this can be overridden individually. Click an icon to select, click again to deselect.', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Checklist Icon Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Global setting for all list items.  Controls the color of the checklist icon.', 'fusion-builder' ),
				'param_name'  => 'iconcolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'checklist_icons_color' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Checklist Circle', 'fusion-builder' ),
				'description' => esc_attr__( 'Global setting for all list items. Turn on if you want to display a circle background for checklists.', 'fusion-builder' ),
				'param_name'  => 'circle',
				'default'     => '',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Checklist Circle Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Global setting for all list items.  Controls the color of the checklist circle background.', 'fusion-builder' ),
				'param_name'  => 'circlecolor',
				'value'       => '',
				'default'     => $fusion_settings->get( 'checklist_circle_color' ),
				'dependency'  => array(
					array(
						'element'  => 'circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Item Size', 'fusion-builder' ),
				'description' => esc_attr__( "Select the list item's size. In pixels (px), ex: 13px.", 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => '13px',
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
add_action( 'fusion_builder_before_init', 'fusion_element_checklist' );

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_checklist_item() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'List Item', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this textblock', 'fusion-builder' ),
		'shortcode'         => 'fusion_li_item',
		'hide_from_builder' => true,
		'allow_generator'   => true,
		'params'            => array(
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'This setting will override the global setting. ', 'fusion-builder' ),
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'List Item Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Add list item content.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_checklist_item' );
