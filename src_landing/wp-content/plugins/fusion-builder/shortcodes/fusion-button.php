<?php

if ( fusion_is_element_enabled( 'fusion_button' ) ) {

	if ( ! class_exists( 'FusionSC_Button' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Button extends Fusion_Element {

			/**
			 * The button counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $button_counter = 1;

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
				add_filter( 'fusion_attr_button-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_button-shortcode-icon-divder', array( $this, 'icon_divider_attr' ) );
				add_filter( 'fusion_attr_button-shortcode-icon', array( $this, 'icon_attr' ) );
				add_filter( 'fusion_attr_button-shortcode-button-text', array( $this, 'button_text_attr' ) );

				add_shortcode( 'fusion_button', array( $this, 'render' ) );
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
						'accent_color'          => ( '' !== $fusion_settings->get( 'button_accent_color' ) ) ? strtolower( $fusion_settings->get( 'button_accent_color' ) ) : '#ffffff',
						'accent_hover_color'    => ( '' !== $fusion_settings->get( 'button_accent_hover_color' ) ) ? strtolower( $fusion_settings->get( 'button_accent_hover_color' ) ) : '#ffffff',
						'bevel_color'           => ( '' !== $fusion_settings->get( 'button_bevel_color' ) ) ? strtolower( $fusion_settings->get( 'button_bevel_color' ) ) : '#54770F',
						'border_width'          => intval( $fusion_settings->get( 'button_border_width' ) ) . 'px',
						'color'                 => 'default',
						'gradient_colors'       => '',
						'icon'                  => '',
						'icon_divider'          => 'no',
						'icon_position'         => 'left',
						'link'                  => '',
						'link_attributes'       => '',
						'modal'                 => '',
						'shape'                 => ( '' !== $fusion_settings->get( 'button_shape' ) ) ? strtolower( $fusion_settings->get( 'button_shape' ) ) : 'round',
						'size'                  => ( '' !== $fusion_settings->get( 'button_size' ) ) ? strtolower( $fusion_settings->get( 'button_size' ) ) : 'large',
						'stretch'               => ( '' !== $fusion_settings->get( 'button_span' ) ) ? $fusion_settings->get( 'button_span' ) : 'no',
						'target'                => '_self',
						'title'                 => '',
						'type'                  => ( '' !== $fusion_settings->get( 'button_type' ) ) ? strtolower( $fusion_settings->get( 'button_type' ) ) : 'flat',
						'alignment'             => '',
						'animation_type'        => '',
						'animation_direction'   => 'down',
						'animation_speed'       => '',
						'animation_offset'      => $fusion_settings->get( 'animation_offset' ),

						// Combined in accent_color.
						'border_color'          => '',
						'icon_color'            => '',
						'text_color'            => '',

						// Combined in accent_hover_color.
						'border_hover_color'    => '',
						'icon_hover_color'      => '',
						'text_hover_color'      => '',

						// Combined with gradient_colors.
						'gradient_hover_colors' => '',

						'button_gradient_top_color'          => ( '' !== $fusion_settings->get( 'button_gradient_top_color' ) ) ? $fusion_settings->get( 'button_gradient_top_color' ) : '#a0ce4e',
						'button_gradient_bottom_color'       => ( '' !== $fusion_settings->get( 'button_gradient_bottom_color' ) ) ? $fusion_settings->get( 'button_gradient_bottom_color' ) : '#a0ce4e',
						'button_gradient_top_color_hover'    => ( '' !== $fusion_settings->get( 'button_gradient_top_color_hover' ) ) ? $fusion_settings->get( 'button_gradient_top_color_hover' ) : '#96c346',
						'button_gradient_bottom_color_hover' => ( '' !== $fusion_settings->get( 'button_gradient_bottom_color_hover' ) ) ? $fusion_settings->get( 'button_gradient_bottom_color_hover' ) : '#96c346',

					), $args
				);
				$defaults = apply_filters( 'fusion_builder_default_args', $defaults, 'fusion_button' );

				// BC support for old 'gradient_colors' format.
				$button_gradient_top_color    = $defaults['button_gradient_top_color'];
				$button_gradient_bottom_color = $defaults['button_gradient_bottom_color'];

				$button_gradient_top_color_hover    = $defaults['button_gradient_top_color_hover'];
				$button_gradient_bottom_color_hover = $defaults['button_gradient_bottom_color_hover'];

				if ( empty( $defaults['gradient_colors'] ) ) {
					$defaults['gradient_colors'] = strtolower( $defaults['button_gradient_top_color'] ) . '|' . strtolower( $defaults['button_gradient_bottom_color'] );
				}

				if ( empty( $defaults['gradient_hover_colors'] ) ) {
					$defaults['gradient_hover_colors'] = strtolower( $defaults['button_gradient_top_color_hover'] ) . '|' . strtolower( $defaults['button_gradient_bottom_color_hover'] );
				}

				$defaults['border_width'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_width'], 'px' );

				if ( 'default' === $defaults['color'] ) {
					$defaults['accent_color']          = ( '' !== $fusion_settings->get( 'button_accent_color' ) ) ? strtolower( $fusion_settings->get( 'button_accent_color' ) ) : '#ffffff';
					$defaults['accent_hover_color']    = ( '' !== $fusion_settings->get( 'button_accent_hover_color' ) ) ? strtolower( $fusion_settings->get( 'button_accent_hover_color' ) ) : '#ffffff';
					$defaults['bevel_color']           = ( '' !== $fusion_settings->get( 'button_bevel_color' ) ) ? strtolower( $fusion_settings->get( 'button_bevel_color' ) ) : '#54770F';
					$defaults['gradient_colors']       = strtolower( $button_gradient_top_color ) . '|' . strtolower( $button_gradient_bottom_color );
					$defaults['gradient_hover_colors'] = strtolower( $button_gradient_top_color_hover ) . '|' . strtolower( $button_gradient_bottom_color_hover );
				}

				// Combined variable settings.
				$old_border_color = $defaults['border_color'];
				$old_text_color = $defaults['text_color'];

				$defaults['border_color'] = $defaults['icon_color'] = $defaults['text_color'] = $defaults['accent_color'];
				$defaults['border_hover_color'] = $defaults['icon_hover_color'] = $defaults['text_hover_color'] = $defaults['accent_hover_color'];

				/*
				TODO:
				$defaults['gradient_hover_colors'] = $defaults['gradient_hover_colors']; // See below for array reverting.
				*/
				if ( $old_border_color ) {
					$defaults['border_color'] = $old_border_color;
				}

				if ( $old_text_color ) {
					$defaults['text_color'] = $old_border_color;
				}

				if ( $defaults['modal'] ) {
					$defaults['link'] = '#';
				}

				$defaults['type'] = strtolower( $defaults['type'] );

				extract( $defaults );

				$this->args = $defaults;

				$style_tag = $styles = '';
				// If its custom, default or a custom color scheme.
				if ( ( 'custom' === $color || 'default' === $color || false !== strpos( $color, 'scheme-' ) ) && ( $bevel_color || $accent_color || $accent_hover_color || $border_width || $gradient_colors ) ) {

					$general_styles = $text_color_styles = $button_3d_styles = $hover_styles = $text_color_hover_styles = $gradient_styles = $gradient_hover_styles = '';

					if ( ( '3d' === $type ) && $bevel_color ) {
						if ( 'small' === $size ) {
							$button_3d_add = 0;
						} elseif ( 'medium' === $size ) {
							$button_3d_add = 1;
						} elseif ( 'large' === $size ) {
							$button_3d_add = 2;
						} elseif ( 'xlarge' === $size ) {
							$button_3d_add = 3;
						}

						$button_3d_shadow_part_1 = 'inset 0px 1px 0px #fff,';

						$button_3d_shadow_part_2 = '0px ' . ( 2 + $button_3d_add ) . 'px 0px ' . $bevel_color . ',';

						$button_3d_shadow_part_3 = '1px ' . ( 4 + $button_3d_add ) . 'px ' . ( 4 + $button_3d_add ) . 'px 3px rgba(0,0,0,0.3)';
						if ( 'small' === $size ) {
							$button_3d_shadow_part_3 = str_replace( '3px', '2px', $button_3d_shadow_part_3 );
						}
						$button_3d_shadow = $button_3d_shadow_part_1 . $button_3d_shadow_part_2 . $button_3d_shadow_part_3;

						$button_3d_styles = '-webkit-box-shadow: ' . $button_3d_shadow . ';-moz-box-shadow: ' . $button_3d_shadow . ';box-shadow: ' . $button_3d_shadow . ';';
					}

					if ( $old_text_color ) {
						$text_color_styles .= 'color:' . $old_text_color . ';';
					} elseif ( $accent_color ) {
						$text_color_styles .= 'color:' . $accent_color . ';';
					}

					if ( $border_width ) {
						$general_styles .= 'border-width:' . $border_width . ';';
						$hover_styles .= 'border-width:' . $border_width . ';';
					}

					if ( $old_border_color ) {
						$general_styles .= 'border-color:' . $old_border_color . ';';
					} elseif ( $accent_color ) {
						$general_styles .= 'border-color:' . $border_color . ';';
					}

					if ( $old_text_color ) {
						$text_color_hover_styles .= 'color:' . $old_text_color . ';';
					} elseif ( $accent_hover_color ) {
						$text_color_hover_styles .= 'color:' . $accent_hover_color . ';';
					} elseif ( $accent_color ) {
						$text_color_hover_styles .= 'color:' . $accent_color . ';';
					}

					if ( $old_border_color ) {
						$hover_styles .= 'border-color:' . $old_border_color . ';';
					} elseif ( $accent_hover_color ) {
						$hover_styles .= 'border-color:' . $accent_hover_color . ';';
					} elseif ( $accent_color ) {
						$hover_styles .= 'border-color:' . $accent_color . ';';
					}

					if ( $text_color_styles ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ' .fusion-button-text, .fusion-button.button-' . $this->button_counter . ' i {' . $text_color_styles . '}';
					}

					if ( $general_styles ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ' {' . $general_styles . '}';
					}

					if ( $accent_color ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ' .fusion-button-icon-divider{border-color:' . $accent_color . ';}';
					}

					if ( $button_3d_styles ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . '.button-3d{' . $button_3d_styles . '}.button-' . $this->button_counter . '.button-3d:active{' . $button_3d_styles . '}';
					}

					if ( $text_color_hover_styles ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ':hover .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':hover i,.fusion-button.button-' . $this->button_counter . ':focus .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':focus i,.fusion-button.button-' . $this->button_counter . ':active .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':active{' . $text_color_hover_styles . '}';
					}

					if ( $hover_styles ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ':hover, .fusion-button.button-' . $this->button_counter . ':focus, .fusion-button.button-' . $this->button_counter . ':active{' . $hover_styles . '}';
					}

					if ( $accent_hover_color ) {
						$styles .= '.fusion-button.button-' . $this->button_counter . ':hover .fusion-button-icon-divider, .fusion-button.button-' . $this->button_counter . ':hover .fusion-button-icon-divider, .fusion-button.button-' . $this->button_counter . ':active .fusion-button-icon-divider{border-color:' . $accent_hover_color . ';}';
					}

					if ( $gradient_colors && 'default' !== $color ) {
						// Checking for deprecated separators.
						if ( strpos( $gradient_colors, ';' ) ) {
							$grad_colors = explode( ';', $gradient_colors );
						} else {
							$grad_colors = explode( '|', $gradient_colors );
						}

						if ( 1 === count( $grad_colors ) || empty( $grad_colors[1] ) || $grad_colors[0] === $grad_colors[1] ) {
							$gradient_styles = "background: {$grad_colors[0]};";
						} else {
							$gradient_styles =
							"background: {$grad_colors[0]};
							background-image: -webkit-gradient( linear, left bottom, left top, from( {$grad_colors[1]} ), to( {$grad_colors[0]} ) );
							background-image: -webkit-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
							background-image:   -moz-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
							background-image:     -o-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
							background-image: linear-gradient( to top, {$grad_colors[1]}, {$grad_colors[0]} );";
						}

						$styles .= '.fusion-button.button-' . $this->button_counter . '{' . $gradient_styles . '}';
					}

					if ( $gradient_hover_colors && 'default' !== $color ) {

						// Checking for deprecated separators.
						if ( strpos( $gradient_hover_colors, ';' ) ) {
							$grad_hover_colors = explode( ';', $gradient_hover_colors );
						} else {
							$grad_hover_colors = explode( '|', $gradient_hover_colors );
						}

						// $grad_hover_colors = array_reverse( $grad_hover_colors ); // For combination of gradient_hover_colors and gradient_hover_colors.
						if ( 1 === count( $grad_hover_colors ) || '' === $grad_hover_colors[1] || $grad_hover_colors[0] === $grad_hover_colors[1] ) {
							$gradient_hover_styles = "background: {$grad_hover_colors[0]};";
						} else {
							$gradient_hover_styles .=
							"background: {$grad_hover_colors[0]};
							background-image: -webkit-gradient( linear, left bottom, left top, from( {$grad_hover_colors[1]} ), to( {$grad_hover_colors[0]} ) );
							background-image: -webkit-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
							background-image:   -moz-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
							background-image:     -o-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
							background-image: linear-gradient( to top, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );";
						}

						$styles .= '.fusion-button.button-' . $this->button_counter . ':hover,.button-' . $this->button_counter . ':focus,.fusion-button.button-' . $this->button_counter . ':active{' . $gradient_hover_styles . '}';
					}
				}

				if ( 'default' === $this->args['stretch'] ) {
					$this->args['stretch'] = $fusion_settings->get( 'button_span' );
				}

				if ( 'yes' === $this->args['stretch'] ) {
					$styles .= '.fusion-button.button-' . $this->button_counter . '{width:100%;}';
				} elseif ( 'no' === $this->args['stretch'] ) {
					$styles .= '.fusion-button.button-' . $this->button_counter . '{width:auto;}';
				}

				if ( $styles ) {
					$style_tag = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
				}

				$icon_html = '';
				if ( $icon ) {
					$icon_html = '<i ' . FusionBuilder::attributes( 'button-shortcode-icon' ) . '></i>';

					if ( 'yes' === $icon_divider ) {
						$icon_html = '<span ' . FusionBuilder::attributes( 'button-shortcode-icon-divder' ) . '>' . $icon_html . '</span>';
					}
				}

				$button_text = '<span ' . FusionBuilder::attributes( 'button-shortcode-button-text' ) . '>' . do_shortcode( $content ) . '</span>';

				$inner_content = ( 'left' === $icon_position ) ? $icon_html . $button_text : $button_text . $icon_html;

				$html = $style_tag . '<a ' . FusionBuilder::attributes( 'button-shortcode' ) . '>' . $inner_content . '</a>';

				// Add wrapper to the button for alignment and scoped styling.
				if ( $alignment && 'no' === $this->args['stretch'] ) {
					$alignment = ' fusion-align' . $alignment;
				}

				$html = '<div class="fusion-button-wrapper' . $alignment . '">' . $html . '</div>';

				$this->button_counter++;

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

				$attr['class'] = 'fusion-button button-' . $this->args['type'] . ' fusion-button-' . $this->args['shape'] . ' button-' . $this->args['size'] . ' button-' . $this->args['color'] . ' button-' . $this->button_counter;

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( $this->args['animation_type'] ) {
					$animations = FusionBuilder::animations( array(
						'type'      => $this->args['animation_type'],
						'direction' => $this->args['animation_direction'],
						'speed'     => $this->args['animation_speed'],
						'offset'    => $this->args['animation_offset'],
					) );

					$attr = array_merge( $attr, $animations );

					$attr['class'] .= ' ' . $attr['animation_class'];
					unset( $attr['animation_class'] );
				}

				$attr['target'] = $this->args['target'];
				if ( '_blank' === $this->args['target'] ) {
					$attr['rel'] = 'noopener noreferrer';
				}

				// Add additional, custom link attributes correctly formatted to the anchor.
				if ( $this->args['link_attributes'] ) {
					$link_attributs = explode( ' ', $this->args['link_attributes'] );
					$brackets_search = array( '{', '}' );
					$brackets_replace = array( '[', ']' );

					foreach ( $link_attributs as $link_attribute ) {
						$attribute_key_value = explode( '=', $link_attribute );

						if ( isset( $attribute_key_value[0] ) ) {
							if ( isset( $attribute_key_value[1] ) ) {
								$attribute_key_value[1] = str_replace( $brackets_search, $brackets_replace, $attribute_key_value[1] );
								$attribute_key_value[1] = trim( html_entity_decode( $attribute_key_value[1], ENT_QUOTES ), "'" );

								if ( 'rel' === $attribute_key_value[0] ) {
									$attr['rel'] = ( isset( $attr['rel'] ) ) ? $attr['rel'] . ' ' . $attribute_key_value[1] : $attribute_key_value[1];
								} else {
									$attr[ $attribute_key_value[0] ] = $attribute_key_value[1];
								}
							} else {
								$attr[ $attribute_key_value[0] ] = 'valueless_attribute';
							}
						}
					}
				}

				$attr['title'] = $this->args['title'];
				$attr['href']  = $this->args['link'];

				if ( $this->args['modal'] ) {
					$attr['data-toggle'] = 'modal';
					$attr['data-target'] = '.fusion-modal.' . $this->args['modal'];
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
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_divider_attr() {

				$attr = array();

				$attr['class'] = 'fusion-button-icon-divider button-icon-divider-' . $this->args['icon_position'];

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

				$attr = array();

				$attr['class'] = 'fa ' . FusionBuilder::font_awesome_name_handler( $this->args['icon'] );

				if ( 'yes' !== $this->args['icon_divider'] ) {
					$attr['class'] .= ' button-icon-' . $this->args['icon_position'];
				}

				if ( $this->args['icon_color'] !== $this->args['accent_color'] ) {
					$attr['style'] = 'color:' . $this->args['icon_color'] . ';';
				}

				return $attr;

			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function button_text_attr() {

				$attr = array(
					'class' => 'fusion-button-text',
				);

				if ( $this->args['icon'] && 'yes' === $this->args['icon_divider'] ) {
					$attr['class'] = 'fusion-button-text fusion-button-text-' . $this->args['icon_position'];
				}

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

				global $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$button_accent_hover_color = ( ! $fusion_settings->get( 'button_accent_hover_color' ) ) ? 'transparent' : $fusion_library->sanitize->color( $fusion_settings->get( 'button_accent_hover_color' ) );

				// Button default styling.
				$main_elements = $elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-button-default' ), '.fusion-button-default' );
				$all_elements = array_merge( array( '.fusion-button' ), $main_elements );

				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['text-transform'] = 'uppercase';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']      = $fusion_library->sanitize->color( $fusion_settings->get( 'button_accent_color' ) );
				if ( $fusion_settings->get( 'button_gradient_top_color' ) != $fusion_settings->get( 'button_gradient_bottom_color' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image'][] = '-webkit-gradient( linear, left bottom, left top, from( ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_bottom_color' ) ) . ' ), to( ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color' ) ) . ' ) )';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image'][] = 'linear-gradient( to top, ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_bottom_color' ) ) . ', ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color' ) ) . ' )';
				}
				if ( 'Pill' != $fusion_settings->get( 'button_shape' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $main_elements ) ]['filter'] = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' . Fusion_Color::new_color( $fusion_settings->get( 'button_gradient_top_color' ) )->to_css( 'hex' ) . ', endColorstr=' . Fusion_Color::new_color( $fusion_settings->get( 'button_gradient_bottom_color' ) )->to_css( 'hex' ) . ')';
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['transition'] = 'all .2s';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-width'] = intval( $fusion_settings->get( 'button_border_width' ) ) . 'px';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-style'] = 'solid';
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_accent_color' ) );
				if ( 'Pill' == $fusion_settings->get( 'button_shape' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-radius'] = '25px';
				} elseif ( 'Square' == $fusion_settings->get( 'button_shape' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-radius'] = '0';
				} elseif ( 'Round' == $fusion_settings->get( 'button_shape' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-radius'] = '2px';
				}
				if ( 'yes' == $fusion_settings->get( 'button_span' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['width'] = '100%';
				}
				$button_size = strtolower( esc_attr( $fusion_settings->get( 'button_size' ) ) );

				$default_size_selector = apply_filters( 'fusion_builder_element_classes', array( '.fusion-button-default-size' ), '.fusion-button-default-size' );
				$quantity_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-button-quantity' ), '.fusion-button-quantity' );

				switch ( $button_size ) {

					case 'small' :
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['padding']     = '9px 20px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['line-height'] = '14px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['font-size']   = '12px';
						if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
							$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 2px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 4px 4px 2px rgba(0, 0, 0, 0.3)';
						}

						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['line-height'] = '14px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['font-size']   = '12px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['height']      = '31px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['width']       = '31px';

						$css['global']['.quantity']['width'] = '95px';

						break;

					case 'medium' :
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['padding']     = '11px 23px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['line-height'] = '16px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['font-size']   = '13px';
						if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
							$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 3px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 5px 5px 3px rgba(0, 0, 0, 0.3)';
						}

						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['line-height'] = '16px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['font-size']   = '13px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['height']      = '36px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['width']       = '36px';

						$css['global']['.quantity']['width'] = '110px';
						$css['global']['.single-product .product .summary .cart .quantity']['width'] = '110px';

						break;

					case 'large' :
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['padding']     = '13px 29px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['line-height'] = '17px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['font-size']   = '14px';
						if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
							$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 4px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 6px 6px 3px rgba(0, 0, 0, 0.3)';
						}

						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['line-height'] = '17px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['font-size']   = '14px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['height']      = '40px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['width']       = '40px';

						$css['global']['.quantity']['width'] = '122px';

						break;

					case 'xlarge' :
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['padding']     = '17px 40px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['line-height'] = '21px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['font-size']   = '18px';
						if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
							$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 5px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 7px 7px 3px rgba(0, 0, 0, 0.3)';
						}

						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['line-height'] = '21px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['font-size']   = '18px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['height']      = '53px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['width']       = '53px';

						$css['global']['.quantity']['width'] = '161px';

						break;
					default : // Fallback to medium.
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['padding']     = '11px 23px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['line-height'] = '16px';
						$css['global'][ $dynamic_css_helpers->implode( $default_size_selector ) ]['font-size']   = '13px';
						if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
							$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 3px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 5px 5px 3px rgba(0, 0, 0, 0.3)';
						}

						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['line-height'] = '16px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['font-size']   = '13px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['height']      = '36px';
						$css['global'][ $dynamic_css_helpers->implode( $quantity_elements ) ]['width']       = '36px';

						$css['global']['.quantity']['width'] = '110px';

				}

				$css['global'][ $dynamic_css_helpers->implode( $all_elements ) ]['font-family']    = $dynamic_css_helpers->combined_font_family( $fusion_settings->get( 'button_typography' ) );
				$css['global'][ $dynamic_css_helpers->implode( $all_elements ) ]['font-weight']    = intval( $fusion_settings->get( 'button_typography', 'font-weight' ) );
				$css['global'][ $dynamic_css_helpers->implode( $all_elements ) ]['letter-spacing'] = round( $fusion_library->sanitize->size( $fusion_settings->get( 'button_typography', 'letter-spacing' ) ) ) . 'px';

				$font_style = $fusion_settings->get( 'button_typography', 'font-style' );
				if ( ! empty( $font_style ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $all_elements ) ]['font-style'] = esc_attr( $fusion_settings->get( 'button_typography', 'font-style' ) );
				}

				$elements = $dynamic_css_helpers->map_selector( $elements, ':visited' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color']      = $fusion_library->sanitize->color( $fusion_settings->get( 'button_accent_color' ) );

				// Small 3D Button Styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, '.button-3d.button-small' );
				if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
					$elements = array_merge( $elements, $dynamic_css_helpers->map_selector( $main_elements, '.button-small' ) );
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 2px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 4px 4px 2px rgba(0, 0, 0, 0.3)';

				$elements = $dynamic_css_helpers->map_selector( $elements, ':active' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 1px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 4px 4px 2px rgba(0, 0, 0, 0.3)';

				// Medium 3D Button Styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, '.button-3d.button-medium' );
				if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
					$elements = array_merge( $elements, $dynamic_css_helpers->map_selector( $main_elements, '.button-medium' ) );
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 3px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 5px 5px 3px rgba(0, 0, 0, 0.3)';

				$elements = $dynamic_css_helpers->map_selector( $elements, ':active' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 1px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 5px 5px 3px rgba(0, 0, 0, 0.3)';

				// Large 3D Button Styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, '.button-3d.button-large' );
				if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
					$elements = array_merge( $elements, $dynamic_css_helpers->map_selector( $main_elements, '.button-large' ) );
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 4px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 5px 6px 3px rgba(0, 0, 0, 0.3)';

				$elements = $dynamic_css_helpers->map_selector( $elements, ':active' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 1px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 6px 6px 3px rgba(0, 0, 0, 0.3)';

				// Extra Large 3D Button Styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, '.button-3d.button-xlarge' );
				if ( '3d' == $fusion_settings->get( 'button_type' ) ) {
					$elements = array_merge( $elements, $dynamic_css_helpers->map_selector( $main_elements, '.button-xlarge' ) );
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 5px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 7px 7px 3px rgba(0, 0, 0, 0.3)';

				$elements = $dynamic_css_helpers->map_selector( $elements, ':active' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['box-shadow'] = 'inset 0px 1px 0px #ffffff, 0px 2px 0px ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_bevel_color' ) ) . ', 1px 7px 7px 3px rgba(0, 0, 0, 0.3)';

				// Button hover styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, ':hover' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color_hover' ) );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $button_accent_hover_color;
				if ( $fusion_settings->get( 'button_gradient_top_color_hover' ) != $fusion_settings->get( 'button_gradient_bottom_color_hover' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image'][] = '-webkit-gradient( linear, left bottom, left top, from( ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_bottom_color_hover' ) ) . ' ), to( ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color_hover' ) ) . ' ) )';
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-image'][] = 'linear-gradient( to top, ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_bottom_color_hover' ) ) . ', ' . $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color_hover' ) ) . ' )';
				}
				if ( 'Pill' != $fusion_settings->get( 'button_shape' ) ) {
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['filter'] = 'progid:DXImageTransform.Microsoft.gradient(startColorstr=' . Fusion_Color::new_color( $fusion_settings->get( 'button_gradient_top_color_hover' ) )->to_css( 'hex' ) . ', endColorstr=' . Fusion_Color::new_color( $fusion_settings->get( 'button_gradient_bottom_color_hover' ) )->to_css( 'hex' ) . ')';
				}
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_accent_hover_color' ) );

				// No gradients button styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, '', '.no-cssgradients ' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color' ) );

				// No gradients, hover styling.
				$elements = $dynamic_css_helpers->map_selector( $main_elements, ':hover', '.no-cssgradients ' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background'] = $fusion_library->sanitize->color( $fusion_settings->get( 'button_gradient_top_color_hover' ) ) . ' !important';

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Button settings.
			 */
			public function add_options() {

				return array(
					'button_shortcode_section' => array(
						'label'       => esc_html__( 'Button Element', 'fusion-builder' ),
						'id'          => 'button_shortcode_section',
						'type'        => 'accordion',
						'fields'      => array(
							'button_size' => array(
								'label'       => esc_html__( 'Button Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the default button size.', 'fusion-builder' ),
								'id'          => 'button_size',
								'default'     => 'Large',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Small'  => esc_html__( 'Small', 'fusion-builder' ),
									'Medium' => esc_html__( 'Medium', 'fusion-builder' ),
									'Large'  => esc_html__( 'Large', 'fusion-builder' ),
									'XLarge' => esc_html__( 'X-Large', 'fusion-builder' ),
								),
							),
							'button_span' => array(
								'label'       => esc_html__( 'Button Span', 'fusion-builder' ),
								'description' => esc_html__( 'Controls if the button spans the full width of its container.', 'fusion-builder' ),
								'id'          => 'button_span',
								'default'     => 'no',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'yes' => esc_html__( 'Yes', 'fusion-builder' ),
									'no'  => esc_html__( 'No', 'fusion-builder' ),
								),
							),
							'button_shape' => array(
								'label'       => esc_html__( 'Button Shape', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the default button shape.', 'fusion-builder' ),
								'id'          => 'button_shape',
								'default'     => 'Round',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Square' => esc_html__( 'Square', 'fusion-builder' ),
									'Round'  => esc_html__( 'Round', 'fusion-builder' ),
									'Pill'   => esc_html__( 'Pill', 'fusion-builder' ),
								),
							),
							'button_type' => array(
								'label'       => esc_html__( 'Button Type', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the default button type.', 'fusion-builder' ),
								'id'          => 'button_type',
								'default'     => 'Flat',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'Flat' => esc_html__( 'Flat', 'fusion-builder' ),
									'3d'   => esc_html__( '3D', 'fusion-builder' ),
								),
							),
							'button_typography' => array(
								'id'          => 'button_typography',
								'label'       => esc_html__( 'Button Typography', 'fusion-builder' ),
								'description' => esc_html__( 'These settings control the typography for all button text.', 'fusion-builder' ),
								'type'        => 'typography',
								'choices'     => array(
									'font-family'    => true,
									'font-weight'    => true,
									'letter-spacing' => true,
								),
								'default'     => array(
									'font-family'    => 'PT Sans',
									'font-weight'    => '400',
									'letter-spacing' => '0',
								),
							),
							'button_gradient_top_color' => array(
								'label'       => esc_html__( 'Button Gradient Top Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the top color of the button background.', 'fusion-builder' ),
								'id'          => 'button_gradient_top_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'button_gradient_bottom_color' => array(
								'label'       => esc_html__( 'Button Gradient Bottom Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the bottom color of the button background.', 'fusion-builder' ),
								'id'          => 'button_gradient_bottom_color',
								'default'     => '#a0ce4e',
								'type'        => 'color-alpha',
							),
							'button_gradient_top_color_hover' => array(
								'label'       => esc_html__( 'Button Gradient Top Hover Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the top hover color of the button background.', 'fusion-builder' ),
								'id'          => 'button_gradient_top_color_hover',
								'default'     => '#96c346',
								'type'        => 'color-alpha',
							),
							'button_gradient_bottom_color_hover' => array(
								'label'       => esc_html__( 'Button Gradient Bottom Hover Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the bottom hover color of the button background.', 'fusion-builder' ),
								'id'          => 'button_gradient_bottom_color_hover',
								'default'     => '#96c346',
								'type'        => 'color-alpha',
							),
							'button_accent_color' => array(
								'label'       => esc_html__( 'Button Accent Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the button border, divider, text and icon.', 'fusion-builder' ),
								'id'          => 'button_accent_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
							'button_accent_hover_color' => array(
								'label'       => esc_html__( 'Button Accent Hover Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the hover color of the button border, divider, text and icon.', 'fusion-builder' ),
								'id'          => 'button_accent_hover_color',
								'default'     => '#ffffff',
								'type'        => 'color-alpha',
							),
							'button_bevel_color' => array(
								'label'       => esc_html__( 'Button Bevel Color For 3D Mode', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the bevel color of the buttons when using 3D button type.', 'fusion-builder' ),
								'id'          => 'button_bevel_color',
								'default'     => '#54770F',
								'type'        => 'color-alpha',
							),
							'button_border_width' => array(
								'label'       => esc_html__( 'Button Border Width', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border width for buttons.', 'fusion-builder' ),
								'id'          => 'button_border_width',
								'default'     => '0',
								'type'        => 'slider',
								'choices'     => array(
									'min'  => '0',
									'max'  => '20',
									'step' => '1',
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

				Fusion_Dynamic_JS::enqueue_script( 'fusion-button' );
			}
		}
	}

	new FusionSC_Button();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_button() {

	global $fusion_settings;

	$standard_schemes = array(
		'default'   => esc_attr__( 'Default', 'fusion-builder' ),
		'custom'    => esc_attr__( 'Custom', 'fusion-builder' ),
		'green'     => esc_attr__( 'Green', 'fusion-builder' ),
		'darkgreen' => esc_attr__( 'Dark Green', 'fusion-builder' ),
		'orange'    => esc_attr__( 'Orange', 'fusion-builder' ),
		'blue'      => esc_attr__( 'Blue', 'fusion-builder' ),
		'red'       => esc_attr__( 'Red', 'fusion-builder' ),
		'pink'      => esc_attr__( 'Pink', 'fusion-builder' ),
		'darkgray'  => esc_attr__( 'Dark Gray', 'fusion-builder' ),
		'lightgray' => esc_attr__( 'Light Gray', 'fusion-builder' ),
	);
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Button', 'fusion-builder' ),
		'shortcode'  => 'fusion_button',
		'icon'       => 'fusiona-check-empty',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-button-preview.php',
		'preview_id' => 'fusion-builder-block-module-button-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button URL', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
				'description' => esc_attr__( "Add the button's url ex: http://example.com.", 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Text', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_attr__( 'Button Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the text that will display on button.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Title Attribute', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
				'description' => esc_attr__( 'Set a title attribute for the button link.', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Target', 'fusion-builder' ),
				'description' => esc_attr__( '_self = open in same browser tab, _blank = open in new browser tab.', 'fusion-builder' ),
				'param_name'  => 'target',
				'default'     => '_self',
				'value'       => array(
					'_self'  => esc_attr__( '_self', 'fusion-builder' ),
					'_blank' => esc_attr__( '_blank', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Additional Attributes', 'fusion-builder' ),
				'param_name'  => 'link_attributes',
				'value'       => '',
				'description' => esc_attr__( "Add additional attributes to the anchor tag. Separate attributes with a whitespace and use single quotes on the values, doubles don't work. If you need to add square brackets, [ ], to your attributes, please use curly brackets, { }, instead. They will be replaced correctly on the frontend. ex: rel='nofollow'.", 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's alignment.", 'fusion-builder' ),
				'param_name'  => 'alignment',
				'default'     => '',
				'value'       => array(
					''       => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'center' => esc_attr__( 'Center', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Modal Window Anchor', 'fusion-builder' ),
				'param_name'  => 'modal',
				'value'       => '',
				'description' => esc_attr__( 'Add the class name of the modal window you want to open on button click.', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Button Style', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's color. Select default or color name for theme options, or select custom to use advanced color options below.", 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => array(
					'default'    => esc_attr__( 'Default', 'fusion-builder' ),
					'custom'     => esc_attr__( 'Custom', 'fusion-builder' ),
					'green'      => esc_attr__( 'Green', 'fusion-builder' ),
					'darkgreen'  => esc_attr__( 'Dark Green', 'fusion-builder' ),
					'orange'     => esc_attr__( 'Orange', 'fusion-builder' ),
					'blue'       => esc_attr__( 'Blue', 'fusion-builder' ),
					'red'        => esc_attr__( 'Red', 'fusion-builder' ),
					'pink'       => esc_attr__( 'Pink', 'fusion-builder' ),
					'darkgray'   => esc_attr__( 'Dark Gray', 'fusion-builder' ),
					'lightgray'  => esc_attr__( 'Light Gray', 'fusion-builder' ),
				),
				'default'     => 'default',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Top Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the top color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_top_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_gradient_top_color' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Bottom Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bottom color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_bottom_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_gradient_bottom_color' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Top Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the top hover color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_top_color_hover',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_gradient_top_color_hover' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Bottom Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bottom hover color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_bottom_color_hover',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_gradient_bottom_color_hover' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Accent Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the button border, divider, text and icon.', 'fusion-builder' ),
				'param_name'  => 'accent_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_accent_color' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Accent Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the hover color of the button border, divider, text and icon.', 'fusion-builder' ),
				'param_name'  => 'accent_hover_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_accent_hover_color' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button type.', 'fusion-builder' ),
				'param_name'  => 'type',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					''     => esc_attr__( 'Default', 'fusion-builder' ),
					'flat' => esc_attr__( 'Flat', 'fusion-builder' ),
					'3d'   => esc_attr__( '3D', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Bevel Color For 3D Mode', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bevel color of the button when using 3D button type.', 'fusion-builder' ),
				'param_name'  => 'bevel_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => $fusion_settings->get( 'button_bevel_color' ),
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'flat',
						'operator' => '!=',
					),
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Button Border Width', 'fusion-builder' ),
				'param_name'  => 'border_width',
				'description' => esc_attr__( 'Controls the border width. In pixels.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'value'       => '',
				'default'     => $fusion_settings->get( 'button_border_width' ),

			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button size.', 'fusion-builder' ),
				'param_name'  => 'size',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'small'  => esc_attr__( 'Small', 'fusion-builder' ),
					'medium' => esc_attr__( 'Medium', 'fusion-builder' ),
					'large'  => esc_attr__( 'Large', 'fusion-builder' ),
					'xlarge' => esc_attr__( 'X-Large', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Span', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls if the button spans the full width of its container.', 'fusion-builder' ),
				'param_name'  => 'stretch',
				'default'     => 'default',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					'default' => esc_attr__( 'Default', 'fusion-builder' ),
					'yes'     => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'      => esc_attr__( 'No', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Shape', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button shape.', 'fusion-builder' ),
				'param_name'  => 'shape',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					''       => esc_attr__( 'Default', 'fusion-builder' ),
					'square' => esc_attr__( 'Square', 'fusion-builder' ),
					'pill'   => esc_attr__( 'Pill', 'fusion-builder' ),
					'round'  => esc_attr__( 'Round', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the icon on the button.', 'fusion-builder' ),
				'param_name'  => 'icon_position',
				'value'       => array(
					'left'  => esc_attr__( 'Left', 'fusion-builder' ),
					'right' => esc_attr__( 'Right', 'fusion-builder' ),
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon Divider', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display a divider between icon and text.', 'fusion-builder' ),
				'param_name'  => 'icon_divider',
				'default'     => 'no',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'       => array(
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'       => array(
					'down'   => esc_attr__( 'Top', 'fusion-builder' ),
					'right'  => esc_attr__( 'Right', 'fusion-builder' ),
					'up'     => esc_attr__( 'Bottom', 'fusion-builder' ),
					'left'   => esc_attr__( 'Left', 'fusion-builder' ),
					'static' => esc_attr__( 'Static', 'fusion-builder' ),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'        => array(
					''                => esc_attr__( 'Default', 'fusion-builder' ),
					'top-into-view'   => esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ),
					'top-mid-of-view' => esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ),
					'bottom-in-view'  => esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' ),
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
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_button' );
