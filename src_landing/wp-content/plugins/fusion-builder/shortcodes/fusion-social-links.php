<?php

if ( fusion_is_element_enabled( 'fusion_social_links' ) ) {

	if ( ! class_exists( 'FusionSC_SocialLinks' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_SocialLinks extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_social-links-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_social-links-shortcode-social-networks', array( $this, 'social_networks_attr' ) );
				add_filter( 'fusion_attr_social-links-shortcode-icon', array( $this, 'icon_attr' ) );

				add_shortcode( 'fusion_social_links', array( $this, 'render' ) );

			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {
				global $fusion_library, $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'     => fusion_builder_default_visibility( 'string' ),
						'class'              => '',
						'id'                 => '',
						'icons_boxed'        => ( 1 == $fusion_settings->get( 'social_links_boxed' ) ) ? 'yes' : $fusion_settings->get( 'social_links_boxed' ),
						'icons_boxed_radius' => $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_boxed_radius' ) ),
						'color_type'         => $fusion_settings->get( 'social_links_color_type' ),
						'icon_colors'        => $fusion_settings->get( 'social_links_icon_color' ),
						'box_colors'         => $fusion_settings->get( 'social_links_box_color' ),
						'icon_order'         => '',
						'show_custom'        => 'no',
						'alignment'          => '',
						'tooltip_placement'  => strtolower( $fusion_settings->get( 'social_links_tooltip_placement' ) ),
						'facebook'           => '',
						'twitter'            => '',
						'instagram'          => '',
						'linkedin'           => '',
						'dribbble'           => '',
						'rss'                => '',
						'youtube'            => '',
						'pinterest'          => '',
						'flickr'             => '',
						'vimeo'              => '',
						'tumblr'             => '',
						'google'             => '',
						'googleplus'         => '',
						'digg'               => '',
						'blogger'            => '',
						'skype'              => '',
						'myspace'            => '',
						'deviantart'         => '',
						'yahoo'              => '',
						'reddit'             => '',
						'forrst'             => '',
						'paypal'             => '',
						'dropbox'            => '',
						'soundcloud'         => '',
						'vk'                 => '',
						'xing'               => '',
						'yelp'               => '',
						'spotify'            => '',
						'email'              => '',
					),
					$args
				);
				foreach ( $args as $key => $arg ) {
					if ( false !== strpos( $key, 'custom_' ) ) {
						$defaults[ $key ] = $arg;
					}
				}
				$defaults['icons_boxed_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['icons_boxed_radius'], 'px' );

				extract( $defaults );

				$this->args = $defaults;

				$this->args['linktarget'] = ( $fusion_settings->get( 'social_icons_new' ) ) ? '_blank' : '_self';

				if ( '' == $defaults['color_type'] ) {
					$defaults['box_colors']  = $fusion_settings->get( 'social_links_box_color' );
					$defaults['icon_colors'] = $fusion_settings->get( 'social_links_icon_color' );
				}

				$social_networks = fusion_builder_get_social_networks( $defaults );

				$social_networks = fusion_builder_sort_social_networks( $social_networks );

				$icons = fusion_builder_build_social_links( $social_networks, 'social-links-shortcode-icon', $defaults );

				$html  = '<div ' . FusionBuilder::attributes( 'social-links-shortcode' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'social-links-shortcode-social-networks' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-social-networks-wrapper' ) . '>';
				$html .= $icons;
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

				if ( $alignment ) {
					$html = '<div class="align' . $alignment . '">' . $html . '</div>';
				}

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

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], array(
					'class' => 'fusion-social-links',
				) );

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Builds the social-networks attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function social_networks_attr() {

				$attr = array(
					'class' => 'fusion-social-networks',
				);

				if ( 'yes' == $this->args['icons_boxed'] ) {
					$attr['class'] .= ' boxed-icons';
				}

				return $attr;

			}

			/**
			 * Builds the icon attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function icon_attr( $args ) {

				global $fusion_settings;

				$attr = array(
					'class' => '',
					'style' => '',
				);

				$tooltip = ucfirst( $args['social_network'] );
				if ( 'custom_' === substr( $args['social_network'], 0, 7 ) ) {
					$attr['class'] .= 'custom ';
					$tooltip = str_replace( 'custom_', '', $args['social_network'] );
					$args['social_network'] = strtolower( $tooltip );
				}

				$attr['class'] .= 'fusion-social-network-icon fusion-tooltip fusion-' . $args['social_network'] . ' fusion-icon-' . $args['social_network'];

				$attr['aria-label'] = 'fusion-' . $args['social_network'];

				$link = $args['social_link'];

				$attr['target'] = $this->args['linktarget'];

				if ( '_blank' == $attr['target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				if ( 'mail' === $args['social_network'] ) {
					$link = ( 'http' === substr( $args['social_link'], 0, 4 ) ) ? $args['social_link'] : 'mailto:' . antispambot( str_replace( 'mailto:', '', $args['social_link'] ) );
					$attr['target'] = '_self';
				}

				$attr['href'] = $link;

				if ( $fusion_settings->get( 'nofollow_social_links' ) ) {
					$attr['rel'] = 'nofollow';
				}

				if ( $args['icon_color'] ) {
					$attr['style'] = 'color:' . $args['icon_color'] . ';';
				}

				if ( 'yes' == $this->args['icons_boxed'] && $args['box_color'] ) {
					$attr['style'] .= 'background-color:' . $args['box_color'] . ';border-color:' . $args['box_color'] . ';';
				}

				if ( 'yes' == $this->args['icons_boxed'] && $this->args['icons_boxed_radius'] || '0' === $this->args['icons_boxed_radius'] ) {
					if ( 'round' == $this->args['icons_boxed_radius'] ) {
						$this->args['icons_boxed_radius'] = '50%';
					}
					$attr['style'] .= 'border-radius:' . $this->args['icons_boxed_radius'] . ';';
				}

				if ( 'none' != strtolower( $this->args['tooltip_placement'] ) ) {
					$attr['data-placement'] = strtolower( $this->args['tooltip_placement'] );
					$tooltip = ( 'googleplus' == strtolower( $tooltip ) ) ? 'Google+' : $tooltip;
					$tooltip = ( 'youtube' == strtolower( $tooltip ) ) ? 'YouTube' : $tooltip;
					$attr['data-title']  = $tooltip;
					$attr['data-toggle'] = 'tooltip';
				}

				$attr['title'] = $tooltip;

				return $attr;

			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$css['global']['.fusion-social-links .boxed-icons .fusion-social-networks-wrapper .fusion-social-network-icon']['width']   = 'calc(' . $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_font_size' ) ) . ' + (2 * ' . $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_boxed_padding' ) ) . ') + 2px)';
				$elements = array(
					'.fusion-social-links .fusion-social-networks.boxed-icons a',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_boxed_padding' ) );
				$elements = array(
					'.fusion-social-links .fusion-social-networks a',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = $fusion_library->sanitize->size( $fusion_settings->get( 'social_links_font_size' ) );

				return $css;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Social Icon settings.
			 */
			public function add_options() {

				return array(
					'social_links_shortcode_section' => array(
						'label'       => esc_html__( 'Social Icon Elements', 'fusion-builder' ),
						'description' => '',
						'id'          => 'sociallinks_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'social_links_info' => array(
								'id'      => 'social_links_info',
								'type'    => 'raw',
								'content' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> These social icon global options control both the social link element and person element.', 'fusion-builder' ) . '</div>',
							),
							'social_links_font_size' => array(
								'label'       => esc_html__( 'Social Links Icons Font Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the font size for the social link icons.', 'fusion-builder' ),
								'id'          => 'social_links_font_size',
								'default'     => '16px',
								'type'        => 'dimension',
							),
							'social_links_color_type' => array(
								'label'       => esc_html__( 'Social Links Icon Color Type', 'fusion-builder' ),
								'description' => esc_html__( 'Custom colors allow you to choose a color for icons and boxes. Brand colors will use the exact brand color of each network for the icons or boxes.', 'fusion-builder' ),
								'id'          => 'social_links_color_type',
								'default'     => 'custom',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'custom' => esc_html__( 'Custom Colors', 'fusion-builder' ),
									'brand'  => esc_html__( 'Brand Colors', 'fusion-builder' ),
								),
							),
							'social_links_icon_color' => array(
								'label'       => esc_html__( 'Social Links Custom Icons Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the custom icons.', 'fusion-builder' ),
								'id'          => 'social_links_icon_color',
								'default'     => '#bebdbd',
								'type'        => 'color-alpha',
								'required'    => array(
									array(
										'setting'  => 'social_links_color_type',
										'operator' => '==',
										'value'    => 'custom',
									),
								),
							),
							'social_links_boxed' => array(
								'label'       => esc_html__( 'Social Links Icons Boxed', 'fusion-builder' ),
								'description' => esc_html__( 'Turn on to have the icon displayed in a small box. Turn off to have the icon displayed with no box.', 'fusion-builder' ),
								'id'          => 'social_links_boxed',
								'default'     => '0',
								'type'        => 'switch',
							),
							'social_links_box_color' => array(
								'label'       => esc_html__( 'Social Links Icons Custom Box Color', 'fusion-builder' ),
								'description' => esc_html__( 'Select a custom social icon box color.', 'fusion-builder' ),
								'id'          => 'social_links_box_color',
								'default'     => '#e8e8e8',
								'type'        => 'color-alpha',
								'required'    => array(
									array(
										'setting'  => 'social_links_boxed',
										'operator' => '==',
										'value'    => '1',
									),
									array(
										'setting'  => 'social_links_color_type',
										'operator' => '==',
										'value'    => 'custom',
									),
								),
							),
							'social_links_boxed_radius' => array(
								'label'       => esc_html__( 'Social Links Icons Boxed Radius', 'fusion-builder' ),
								'description' => esc_html__( 'Box radius for the social icons.', 'fusion-builder' ),
								'id'          => 'social_links_boxed_radius',
								'default'     => '4px',
								'type'        => 'dimension',
								'choices'     => array( 'px', 'em' ),
								'required'    => array(
									array(
										'setting'  => 'social_links_boxed',
										'operator' => '==',
										'value'    => '1',
									),
								),
							),
							'social_links_boxed_padding' => array(
								'label'       => esc_html__( 'Social Links Icons Boxed Padding', 'fusion-builder' ),
								'id'          => 'social_links_boxed_padding',
								'default'     => '8px',
								'type'        => 'dimension',
								'required'    => array(
									array(
										'setting'  => 'social_links_boxed',
										'operator' => '==',
										'value'    => '1',
									),
								),
							),
							'social_links_tooltip_placement' => array(
								'label'       => esc_html__( 'Social Links Icons Tooltip Position', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the tooltip position of the social links icons.', 'fusion-builder' ),
								'id'          => 'social_links_tooltip_placement',
								'default'     => 'Top',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Top'    => esc_html__( 'Top', 'fusion-builder' ),
									'Right'  => esc_html__( 'Right', 'fusion-builder' ),
									'Bottom' => esc_html__( 'Bottom', 'fusion-builder' ),
									'Left'   => esc_html__( 'Left', 'fusion-builder' ),
									'None'   => esc_html__( 'None', 'fusion-builder' ),
								),
							),
						),
					),
				);
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {
				Fusion_Dynamic_JS::enqueue_script( 'fusion-tooltip' );
			}
		}
	}

	new FusionSC_SocialLinks();

}
/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_social_links() {
	$social_options = array(
		'name'      => esc_attr__( 'Social Links', 'fusion-builder' ),
		'shortcode' => 'fusion_social_links',
		'icon'      => 'fusiona-link',
		'params'    => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Boxed Social Icons', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'icons_boxed',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Social Icon Box Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the radius of the boxed icons. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'icons_boxed_radius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icons_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icons Color Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'color_type',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'custom' => esc_attr__( 'Custom Colors', 'fusion-builder' ),
					'brand'  => esc_attr__( 'Brand Colors', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the color of social icons. ', 'fusion-builder' ),
				'param_name'  => 'icon_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Box Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the box color of social icons. ', 'fusion-builder' ),
				'param_name'  => 'box_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icons_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
					array(
						'element'  => 'color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icon Tooltip Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position for tooltips. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'tooltip_placement',
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'top'    => esc_attr__( 'Top', 'fusion-builder' ),
					'bottom' => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'Right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Blogger Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Blogger link.', 'fusion-builder' ),
				'param_name'  => 'blogger',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Deviantart Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Deviantart link.', 'fusion-builder' ),
				'param_name'  => 'deviantart',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Digg Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Digg link.', 'fusion-builder' ),
				'param_name'  => 'digg',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dribbble Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dribbble link.', 'fusion-builder' ),
				'param_name'  => 'dribbble',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dropbox Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dropbox link.', 'fusion-builder' ),
				'param_name'  => 'dropbox',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Facebook Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Facebook link.', 'fusion-builder' ),
				'param_name'  => 'facebook',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Flickr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Flickr link.', 'fusion-builder' ),
				'param_name'  => 'flickr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Forrst Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Forrst link.', 'fusion-builder' ),
				'param_name'  => 'forrst',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Google+ Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Google+ link.', 'fusion-builder' ),
				'param_name'  => 'googleplus',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Instagram Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Instagram link.', 'fusion-builder' ),
				'param_name'  => 'instagram',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'LinkedIn Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom LinkedIn link.', 'fusion-builder' ),
				'param_name'  => 'linkedin',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Myspace Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Myspace link.', 'fusion-builder' ),
				'param_name'  => 'myspace',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'PayPal Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom PayPal link.', 'fusion-builder' ),
				'param_name'  => 'paypal',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Pinterest Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Pinterest link.', 'fusion-builder' ),
				'param_name'  => 'pinterest',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Reddit Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Reddit link.', 'fusion-builder' ),
				'param_name'  => 'reddit',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'RSS Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom RSS link.', 'fusion-builder' ),
				'param_name'  => 'rss',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Skype Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Skype link.', 'fusion-builder' ),
				'param_name'  => 'skype',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'SoundCloud Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom SoundCloud link.', 'fusion-builder' ),
				'param_name'  => 'soundcloud',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Spotify Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Spotify link.', 'fusion-builder' ),
				'param_name'  => 'spotify',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Tumblr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Tumblr link.', 'fusion-builder' ),
				'param_name'  => 'tumblr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Twitter Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Twitter link.', 'fusion-builder' ),
				'param_name'  => 'twitter',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Vimeo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Vimeo link.', 'fusion-builder' ),
				'param_name'  => 'vimeo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'VK Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom VK link.', 'fusion-builder' ),
				'param_name'  => 'vk',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Xing Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Xing link.', 'fusion-builder' ),
				'param_name'  => 'xing',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yahoo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yahoo link.', 'fusion-builder' ),
				'param_name'  => 'yahoo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yelp Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yelp link.', 'fusion-builder' ),
				'param_name'  => 'yelp',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Youtube Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Youtube link.', 'fusion-builder' ),
				'param_name'  => 'youtube',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Email Address', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert an email address to display the email icon.', 'fusion-builder' ),
				'param_name'  => 'email',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Custom Social Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Show the custom social icon specified in Theme Options.', 'fusion-builder' ),
				'param_name'  => 'show_custom',
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => 'no',
			),
		),
	);
	$custom_social_networks = fusion_builder_get_custom_social_networks();
	if ( is_array( $custom_social_networks ) ) {
		$custom_networks = array();
		foreach ( $custom_social_networks as $key => $custom_network ) {
			$social_options['params'][] = array(
				'type'        => 'textfield',
				'heading'     => sprintf( esc_attr__( 'Custom %s Link', 'fusion-builder' ), $key + 1 ),
				'description' => esc_attr__( 'Insert your custom social link.', 'fusion-builder' ),
				'param_name'  => 'custom_' . $key,
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'show_custom',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			);
		}
	}
	$social_options['params'][] = array(
		'type'        => 'radio_button_set',
		'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
		'description' => esc_attr__( "Select the icon's alignment.", 'fusion-builder' ),
		'param_name'  => 'alignment',
		'value'       => array(
			''       => esc_attr__( 'Text Flow', 'fusion-builder' ),
			'left'   => esc_attr__( 'Left', 'fusion-builder' ),
			'center' => esc_attr__( 'Center', 'fusion-builder' ),
			'right'  => esc_attr__( 'Right', 'fusion-builder' ),
		),
		'default'     => '',
	);
	$social_options['params'][] = array(
		'type'        => 'checkbox_button_set',
		'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
		'param_name'  => 'hide_on_mobile',
		'value'       => fusion_builder_visibility_options( 'full' ),
		'default'     => fusion_builder_default_visibility( 'array' ),
		'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
	);
	$social_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
		'param_name'  => 'class',
		'value'       => '',
		'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
	);
	$social_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
		'param_name'  => 'id',
		'value'       => '',
		'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
	);
	fusion_builder_map( $social_options );
}
add_action( 'fusion_builder_before_init', 'fusion_element_social_links' );
