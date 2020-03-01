<?php

if ( fusion_is_element_enabled( 'fusion_countdown' ) ) {

	if ( ! class_exists( 'FusionSC_Countdown' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Countdown extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * The countdown counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $countdown_counter = 1;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_countdown-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_countdown-shortcode-counter-wrapper', array( $this, 'counter_wrapper_attr' ) );
				add_filter( 'fusion_attr_countdown-shortcode-link', array( $this, 'link_attr' ) );

				add_shortcode( 'fusion_countdown', array( $this, 'render' ) );

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

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
						'class'                 => '',
						'id'                    => '',
						'background_color'      => $fusion_settings->get( 'countdown_background_color' ),
						'background_image'      => $fusion_settings->get( 'countdown_background_image', 'url' ),
						'background_position'   => $fusion_settings->get( 'countdown_background_position' ),
						'background_repeat'     => $fusion_settings->get( 'countdown_background_repeat' ),
						'border_radius'         => '',
						'counter_box_color'     => $fusion_settings->get( 'countdown_counter_box_color' ),
						'counter_text_color'    => $fusion_settings->get( 'countdown_counter_text_color' ),
						'countdown_end'         => '2000-01-01 00:00:00',
						'dash_titles'           => 'short',
						'heading_text'          => '',
						'heading_text_color'    => $fusion_settings->get( 'countdown_heading_text_color' ),
						'link_text'             => '',
						'link_text_color'       => $fusion_settings->get( 'countdown_link_text_color' ),
						'link_target'           => $fusion_settings->get( 'countdown_link_target' ),
						'link_url'              => '',
						'show_weeks'            => $fusion_settings->get( 'countdown_show_weeks' ),
						'subheading_text'       => '',
						'subheading_text_color' => $fusion_settings->get( 'countdown_subheading_text_color' ),
						'timezone'              => $fusion_settings->get( 'countdown_timezone' ),
					), $args
				);

				$defaults['border_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_radius'], 'px' );

				if ( 'default' === $defaults['link_target'] ) {
					$defaults['link_target'] = $fusion_settings->get( 'countdown_link_target' );
				}
				extract( $defaults );

				$this->args = $defaults;

				$html = '<div ' . FusionBuilder::attributes( 'countdown-shortcode' ) . '>';
				$html .= self::get_styles();
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-heading-wrapper' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-subheading' ) . '>' . $subheading_text . '</div>';
				$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-heading' ) . '>' . $heading_text . '</div>';
				$html .= '</div>';

				$html .= '<div ' . FusionBuilder::attributes( 'countdown-shortcode-counter-wrapper' ) . '>';

				$dashes = array(
					array(
						'show'      => $show_weeks,
						'class'     => 'weeks',
						'shortname' => esc_attr__( 'Weeks', 'fusion-builder' ),
						'longname'  => esc_attr__( 'Weeks', 'fusion-builder' ),
					),
					array(
						'show'      => 'yes',
						'class'     => 'days',
						'shortname' => esc_attr__( 'Days', 'fusion-builder' ),
						'longname'  => esc_attr__( 'Days', 'fusion-builder' ),
					),
					array(
						'show'      => 'yes',
						'class'     => 'hours',
						'shortname' => esc_attr__( 'Hrs', 'fusion-builder' ),
						'longname'  => esc_attr__( 'Hours', 'fusion-builder' ),
					),
					array(
						'show'      => 'yes',
						'class'     => 'minutes',
						'shortname' => esc_attr__( 'Min', 'fusion-builder' ),
						'longname'  => esc_attr__( 'Minutes', 'fusion-builder' ),
					),
					array(
						'show'      => 'yes',
						'class'     => 'seconds',
						'shortname' => esc_attr__( 'Sec', 'fusion-builder' ),
						'longname'  => esc_attr__( 'Seconds', 'fusion-builder' ),
					),
				);

				$dash_class = '';
				if ( ! $this->args['counter_box_color'] || 'transparent' == $this->args['counter_box_color'] ) {
					$dash_class = ' fusion-no-bg';
				}

				$dashes_count = count( $dashes );

				for ( $i = 0; $i < $dashes_count; $i++ ) {
					if ( 'yes' === $dashes[ $i ]['show'] ) {
						$html .= '<div class="fusion-dash-wrapper ' . $dash_class . '">';
						$html .= '<div class="fusion-dash fusion-dash-' . $dashes[ $i ]['class'] . '">';
						if ( 'days' === $dashes[ $i ]['class'] ) {
							$html .= '<div class="fusion-thousand-digit fusion-digit">0</div>';
						}
						if ( 'weeks' === $dashes[ $i ]['class'] || 'days' === $dashes[ $i ]['class'] ) {
							$html .= '<div class="fusion-hundred-digit fusion-digit">0</div>';
						}
						$html .= '<div class="fusion-digit">0</div><div class="fusion-digit">0</div>';
						$html .= '<div class="fusion-dash-title">' . $dashes[ $i ][ $dash_titles . 'name' ] . '</div>';
						$html .= '</div></div>';
					}
				}

				$html .= '</div>';

				$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-link-wrapper' ) . '>';
				$html .= '<a ' . FusionBuilder::attributes( 'countdown-shortcode-link' ) . '>' . $link_text . '</a>';
				$html .= '</div>';

				$html .= do_shortcode( $content );
				$html .= '</div>';

				$this->countdown_counter++;

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

				$attr = array(
					'class' => 'fusion-countdown fusion-countdown-' . $this->countdown_counter,
				);

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( ! $this->args['background_image'] && ( ! $this->args['background_color'] || 'transparent' == $this->args['background_color'] ) ) {
					$attr['class'] .= ' fusion-no-bg';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;
			}

			/**
			 * Builds the counter-wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function counter_wrapper_attr() {

				$attr = array(
					'class' => 'fusion-countdown-counter-wrapper',
					'id'    => 'fusion-countdown-' . $this->countdown_counter,
				);

				if ( 'site_time' == $this->args['timezone'] ) {
					$attr['data-gmt-offset'] = get_option( 'gmt_offset' );
				}

				if ( $this->args['countdown_end'] ) {
					$attr['data-timer'] = date( 'Y-m-d-H-i-s', strtotime( $this->args['countdown_end'] ) );
				}

				$attr['data-omit-weeks'] = ( 'yes' == $this->args['show_weeks'] ) ? '0' : '1';

				return $attr;
			}

			/**
			 * Builds the link attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function link_attr() {

				$attr = array(
					'class'  => 'fusion-countdown-link',
					'target' => $this->args['link_target'],
					'href'   => $this->args['link_url'],
				);

				if ( '_blank' == $this->args['link_target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				return $attr;
			}

			/**
			 * Gets the CSS styles.
			 *
			 * @access public
			 * @since 1.0
			 * @return string
			 */
			public function get_styles() {
				$styles = '';

				// Set custom background styles.
				if ( $this->args['background_image'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' {';
					$styles .= 'background:url(' . $this->args['background_image'] . ') ' . $this->args['background_position'] . ' ' . $this->args['background_repeat'] . ' ' . $this->args['background_color'] . ';';

					if ( 'no-repeat' == $this->args['background_repeat'] ) {
						$styles .= '-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;';
					}
					$styles .= '}';

				} elseif ( $this->args['background_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' {background-color:' . $this->args['background_color'] . ';}';
				}

				if ( $this->args['border_radius'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ', .fusion-countdown-' . $this->countdown_counter . ' .fusion-dash {border-radius:' . $this->args['border_radius'] . ';}';
				}

				if ( $this->args['heading_text_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-heading {color:' . $this->args['heading_text_color'] . ';}';
				}

				if ( $this->args['subheading_text_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-subheading {color:' . $this->args['subheading_text_color'] . ';}';
				}

				if ( $this->args['counter_text_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-counter-wrapper {color:' . $this->args['counter_text_color'] . ';}';
				}

				if ( $this->args['counter_box_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-dash {background-color:' . $this->args['counter_box_color'] . ';}';
				}

				if ( $this->args['link_text_color'] ) {
					$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-link {color:' . $this->args['link_text_color'] . ';}';
				}

				if ( $styles ) {
					$styles = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
				}

				return $styles;
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {
				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $dynamic_css_helpers;

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-countdown' ), '.fusion-countdown' );

				$elements = array_merge(
					array( '.fusion-countdown' ),
					$dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-heading-wrapper' ),
					$dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-counter-wrapper' ),
					$dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-link-wrapper' )
				);
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-heading-wrapper' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-counter-wrapper' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-top'] = '20px';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['margin-bottom'] = '10px';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-dash-title' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['display'] = 'block';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['padding'] = '0';
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['font-size'] = '16px';

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' .fusion-countdown-link-wrapper' );
				$css[ $content_media_query ][ $dynamic_css_helpers->implode( $elements ) ]['text-align'] = 'center';

				return $css;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1.0
			 * @return array $sections Countdown settings.
			 */
			public function add_options() {

				return array(
					'countdown_shortcode_section' => array(
						'label'  => esc_html__( 'Countdown Element', 'fusion-builder' ),
						'id'     => 'countdown_shortcode_section',
						'type'   => 'accordion',
						'fields' => array(
							'countdown_timezone' => array(
								'label'       => esc_html__( 'Countdown Timezone', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the timezone that is used for the countdown calculation.', 'fusion-builder' ),
								'id'          => 'countdown_timezone',
								'default'     => 'site_time',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'site_time' => esc_html__( 'Site Timezone', 'fusion-builder' ),
									'user_time' => esc_html__( 'User Timezone', 'fusion-builder' ),
								),
							),
							'countdown_show_weeks' => array(
								'label'       => esc_html__( 'Countdown Show Weeks', 'fusion-builder' ),
								'description' => esc_html__( 'Turn on to display the number of weeks in the countdown.', 'fusion-builder' ),
								'id'          => 'countdown_show_weeks',
								'default'     => 'no',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'yes'     => esc_html__( 'On', 'fusion-builder' ),
									'no'      => esc_html__( 'Off', 'fusion-builder' ),
								),
							),
							'countdown_background_color' => array(
								'label'       => esc_html__( 'Countdown Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the background color for the countdown box.', 'fusion-builder' ),
								'id'          => 'countdown_background_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'countdown_background_image' => array(
								'label'       => esc_html__( 'Countdown Background Image', 'fusion-builder' ),
								'description' => esc_html__( 'Select an image for the countdown box background.', 'fusion-builder' ),
								'id'          => 'countdown_background_image',
								'default'     => '',
								'mod'         => '',
								'type'        => 'media',
							),
							'countdown_background_repeat' => array(
								'label'       => esc_html__( 'Countdown Background Repeat', 'fusion-builder' ),
								'description' => esc_html__( 'Controls how the background image repeats.', 'fusion-builder' ),
								'id'          => 'countdown_background_repeat',
								'default'     => 'no-repeat',
								'type'        => 'select',
								'choices'     => array(
									'repeat'    => esc_html__( 'Repeat All', 'fusion-builder' ),
									'repeat-x'  => esc_html__( 'Repeat Horizontal', 'fusion-builder' ),
									'repeat-y'  => esc_html__( 'Repeat Vertical', 'fusion-builder' ),
									'no-repeat' => esc_html__( 'Repeat None', 'fusion-builder' ),
								),
							),
							'countdown_background_position' => array(
								'label'       => esc_html__( 'Countdown Background Position', 'fusion-builder' ),
								'description' => esc_html__( 'Controls how the background image is positioned.', 'fusion-builder' ),
								'id'          => 'countdown_background_position',
								'default'     => 'center center',
								'type'        => 'select',
								'choices'     => array(
									'top left'      => esc_html__( 'top left', 'fusion-builder' ),
									'top center'    => esc_html__( 'top center', 'fusion-builder' ),
									'top right'     => esc_html__( 'top right', 'fusion-builder' ),
									'center left'   => esc_html__( 'center left', 'fusion-builder' ),
									'center center' => esc_html__( 'center center', 'fusion-builder' ),
									'center right'  => esc_html__( 'center right', 'fusion-builder' ),
									'bottom left'   => esc_html__( 'bottom left', 'fusion-builder' ),
									'bottom center' => esc_html__( 'bottom center', 'fusion-builder' ),
									'bottom right'  => esc_html__( 'bottom right', 'fusion-builder' ),
								),
							),
							'countdown_counter_box_color' => array(
								'label'       => esc_html__( 'Countdown Counter Box Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the background color for the counter boxes.', 'fusion-builder' ),
								'id'          => 'countdown_counter_box_color',
								'default'     => '#333333',
								'type'        => 'color-alpha',
							),
							'countdown_counter_text_color' => array(
								'label'       => esc_html__( 'Countdown Counter Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color for the countdown timer text.', 'fusion-builder' ),
								'id'          => 'countdown_counter_text_color',
								'default'     => '#ffffff',
								'type'        => 'color',
							),
							'countdown_heading_text_color' => array(
								'label'       => esc_html__( 'Countdown Heading Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color for the countdown headings.', 'fusion-builder' ),
								'id'          => 'countdown_heading_text_color',
								'default'     => '#ffffff',
								'type'        => 'color',
							),
							'countdown_subheading_text_color' => array(
								'label'       => esc_html__( 'Countdown Subheading Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color for the countdown subheadings.', 'fusion-builder' ),
								'id'          => 'countdown_subheading_text_color',
								'default'     => '#ffffff',
								'type'        => 'color',
							),
							'countdown_link_text_color' => array(
								'label'       => esc_html__( 'Countdown Link Text Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color for the countdown link text.', 'fusion-builder' ),
								'id'          => 'countdown_link_text_color',
								'default'     => '#ffffff',
								'type'        => 'color',
							),
							'countdown_link_target' => array(
								'label'       => esc_html__( 'Countdown Link Target', 'fusion-builder' ),
								'description' => esc_html__( 'Controls how the link will open.', 'fusion-builder' ),
								'id'          => 'countdown_link_target',
								'default'     => '_self',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'_self'  => esc_html__( 'Same Window', 'fusion-builder' ),
									'_blank' => esc_html__( 'New Window', 'fusion-builder' ),
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

				Fusion_Dynamic_JS::enqueue_script(
					'fusion-count-down',
					FusionBuilder::$js_folder_url . '/general/fusion-countdown.js',
					FusionBuilder::$js_folder_path . '/general/fusion-countdown.js',
					array( 'jquery', 'fusion-animations', 'jquery-count-down' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_Countdown();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_countdown() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'       => esc_attr__( 'Countdown', 'fusion-builder' ),
		'shortcode'  => 'fusion_countdown',
		'icon'       => 'fusiona-calendar-check-o',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-countdown-preview.php',
		'preview_id' => 'fusion-builder-block-module-countdown-preview-template',
		'params'     => array(
			array(
				'type'        => 'date_time_picker',
				'heading'     => esc_attr__( 'Countdown Timer End', 'fusion-builder' ),
				'description' => __( 'Set the end date and time for the countdown time. Click the calendar icon to use the date picker. Use SQL time format: YYYY-MM-DD HH:MM:SS. E.g: 2016-05-10 12:30:00.', 'fusion-builder' ),
				'param_name'  => 'countdown_end',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Timezone', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose which timezone should be used for the countdown calculation.', 'fusion-builder' ),
				'param_name'  => 'timezone',
				'value'       => array(
					''          => esc_attr__( 'Default', 'fusion-builder' ),
					'site_time' => esc_attr__( 'Timezone of Site', 'fusion-builder' ),
					'user_time' => esc_attr__( 'Timezone of User', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Weeks', 'fusion-builder' ),
				'description' => esc_attr__( 'Select "yes" to show weeks for longer countdowns.', 'fusion-builder' ),
				'param_name'  => 'show_weeks',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the countdown wrapping box.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_background_color' ),
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Background Image', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image to display in the background.', 'fusion-builder' ),
				'param_name'  => 'background_image',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Background Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the background image.', 'fusion-builder' ),
				'param_name'  => 'background_position',
				'value'       => array(
					''              => esc_attr__( 'Default', 'fusion-builder' ),
					'left top'      => esc_attr__( 'Left Top', 'fusion-builder' ),
					'left center'   => esc_attr__( 'Left Center', 'fusion-builder' ),
					'left bottom'   => esc_attr__( 'Left Bottom', 'fusion-builder' ),
					'right top'     => esc_attr__( 'Right Top', 'fusion-builder' ),
					'right center'  => esc_attr__( 'Right Center', 'fusion-builder' ),
					'right bottom'  => esc_attr__( 'Right Bottom', 'fusion-builder' ),
					'center top'    => esc_attr__( 'Center Top', 'fusion-builder' ),
					'center center' => esc_attr__( 'Center Center', 'fusion-builder' ),
					'center bottom' => esc_attr__( 'Center Bottom', 'fusion-builder' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'background_image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Background Repeat', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how the background image repeats.' ),
				'param_name'  => 'background_repeat',
				'value'       => array(
					''          => esc_attr__( 'Default', 'fusion-builder' ),
					'no-repeat' => esc_attr__( 'No Repeat', 'fusion-builder' ),
					'repeat'    => esc_attr__( 'Repeat Vertically and Horizontally', 'fusion-builder' ),
					'repeat-x'  => esc_attr__( 'Repeat Horizontally', 'fusion-builder' ),
					'repeat-y'  => esc_attr__( 'Repeat Vertically', 'fusion-builder' ),
				),
				'default'     => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'background_image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the radius of outer box and also the countdown. In pixels (px), ex: 1px.', 'fusion-builder' ),
				'param_name'  => 'border_radius',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Countdown Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the countdown.', 'fusion-builder' ),
				'param_name'  => 'counter_box_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_counter_box_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Countdown Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown timer.', 'fusion-builder' ),
				'param_name'  => 'counter_text_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_counter_text_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Heading Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'heading_text',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Heading Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown heading.', 'fusion-builder' ),
				'param_name'  => 'heading_text_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_heading_text_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Subheading Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a subheading text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'subheading_text',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Subheading Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown subheading.', 'fusion-builder' ),
				'param_name'  => 'subheading_text_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_subheading_text_color' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link URL', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a url for the link. E.g: http://example.com.', 'fusion-builder' ),
				'param_name'  => 'link_url',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a link text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'link_text',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Link Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown link.', 'fusion-builder' ),
				'param_name'  => 'link_text_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'countdown_link_text_color' ),
				'group'       => __( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'link_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => esc_attr__( '_self = open in same window
                                      _blank = open in new window', 'fusion-builder' ),
				'param_name'  => 'link_target',
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-builder' ),
					'_self'   => esc_attr__( '_self', 'fusion-builder' ),
					'_blank'  => esc_attr__( '_blank', 'fusion-builder' ),
				),
				'default'     => 'default',
				'dependency'  => array(
					array(
						'element'  => 'link_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
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
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_countdown' );
