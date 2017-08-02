<?php
/**
 * Fusion-Builder Shortcode Element.
 *
 * @package Fusion-Core
 * @since 3.1.0
 */

if ( function_exists( 'fusion_is_element_enabled' ) && fusion_is_element_enabled( 'fusion_fusionslider' ) ) {

	if ( ! class_exists( 'FusionSC_FusionSlider' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FusionSlider {

			/**
			 * Parent SC arguments.
			 *
			 * @static
			 * @access public
			 * @since 1.0
			 * @var array
			 */
			public static $parent_args;

			/**
			 * The slider settings.
			 *
			 * @static
			 * @access public
			 * @since 1.0
			 * @var array
			 */
			public static $slider_settings;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {

				add_shortcode( 'fusion_fusionslider', array( $this, 'render_parent' ) );

				add_filter( 'fusion_attr_fusion-slider-wrapper', array( $this, 'wrapper_attr' ) );
				add_filter( 'fusion_attr_fusion-slider-container', array( $this, 'container_attr' ) );

			}

			/**
			 * Render the parent shortcode
			 *
			 * @param  array  $args    Shortcode paramters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			function render_parent( $args, $content = '' ) {
				global $fusion_library;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'name'           => '',
					), $args
				);

				extract( $defaults );

				self::$parent_args = $defaults;

				ob_start();

				$term = $name;

				$term_details = get_term_by( 'slug', $term, 'slide-page' );

				if ( ! $term_details ) {
					return do_shortcode( '[fusion_alert type="error"  border_size="1px" box_shadow="yes"]Incorrect slider name. Please make sure to use a valid slider slug.[/fusion_alert]' );
				}

				$slider_settings = get_option( 'taxonomy_' . $term_details->term_id, array() );

				if ( ! isset( $slider_settings['nav_box_width'] ) ) {
					$slider_settings['nav_box_width'] = '63px';
				}

				if ( ! isset( $slider_settings['nav_box_height'] ) ) {
					$slider_settings['nav_box_height'] = '63px';
				}

				if ( ! isset( $slider_settings['nav_arrow_size'] ) ) {
					$slider_settings['nav_arrow_size'] = '25px';
				}

				if ( $slider_settings['nav_box_height'] ) {
					$nav_box_height_half = intval( $slider_settings['nav_box_height'] ) / 2;
				}

				self::$slider_settings = $slider_settings;

				$content_max_width = '';
				if ( isset( $slider_settings['slider_content_width'] ) && '' !== $slider_settings['slider_content_width'] ) {
					$content_max_width = 'max-width:' . $slider_settings['slider_content_width'];
				}

				$args = array(
					'post_type'        => 'slide',
					'posts_per_page'   => -1,
					'suppress_filters' => 0,
				);
				$args['tax_query'][] = array(
					'taxonomy' => 'slide-page',
					'field'    => 'slug',
					'terms'    => $term,
				);

				$query = FusionCore_Plugin::fusion_core_cached_query( $args );
				if ( $query->have_posts() ) : ?>
					<div <?php echo FusionBuilder::attributes( 'fusion-slider-wrapper' ); // WPCS: XSS ok. ?>>
						<style type="text/css" scoped="scoped">
						.fusion-slider-<?php echo esc_attr( $term_details->term_id ); ?> .flex-direction-nav a {
							<?php
							if ( $slider_settings['nav_box_width'] ) {
								echo 'width:' . esc_attr( $slider_settings['nav_box_width'] ) . ';';
							}
							if ( $slider_settings['nav_box_height'] ) {
								echo 'height:' . esc_attr( $slider_settings['nav_box_height'] ) . ';';
								echo 'line-height:' . esc_attr( $slider_settings['nav_box_height'] ) . ';';
							}
							if ( $slider_settings['nav_arrow_size'] ) {
								echo 'font-size:' . esc_attr( $slider_settings['nav_arrow_size'] ) . ';';
							}
							?>
						}
						</style>
						<div class="fusion-slider-loading"><?php esc_html_e( 'Loading...', 'fusion-core' ); ?></div>
						<div <?php echo FusionBuilder::attributes( 'fusion-slider-container' ); // WPCS: XSS ok. ?>>
							<ul class="slides">
								<?php
								while ( $query->have_posts() ) :  $query->the_post();
									$metadata = get_metadata( 'post', get_the_ID() );

									$background_image = '';
									$background_class = '';

									$img_width = '';
									$image_url = array( '', '' );

									if ( isset( $metadata['pyre_type'][0] ) && 'image' === $metadata['pyre_type'][0] && has_post_thumbnail() ) {
										$image_id         = get_post_thumbnail_id();
										$image_url        = wp_get_attachment_image_src( $image_id, 'full', true );
										$background_image = 'background-image: url(' . $image_url[0] . ');';
										$background_class = 'background-image';
										$img_width        = $image_url[1];
									}

									$aspect_ratio       = '16:9';
									$video_attributes   = '';
									$youtube_attributes = '';
									$vimeo_attributes   = '';
									$data_mute          = 'no';
									$data_loop          = 'no';
									$data_autoplay      = 'no';

									if ( isset( $metadata['pyre_aspect_ratio'][0] ) && $metadata['pyre_aspect_ratio'][0] ) {
										$aspect_ratio = $metadata['pyre_aspect_ratio'][0];
									}

									if ( isset( $metadata['pyre_mute_video'][0] ) && 'yes' === $metadata['pyre_mute_video'][0] ) {
										$video_attributes = 'muted';
										$data_mute        = 'yes';
									}

									if ( isset( $metadata['pyre_autoplay_video'][0] ) && 'yes' === $metadata['pyre_autoplay_video'][0] ) {
										$video_attributes   .= ' autoplay';
										$youtube_attributes .= '&amp;autoplay=0';
										$vimeo_attributes   .= '&amp;autoplay=0';
										$data_autoplay       = 'yes';
									}

									if ( isset( $metadata['pyre_loop_video'][0] ) && 'yes' === $metadata['pyre_loop_video'][0] ) {
										$video_attributes   .= ' loop';
										$youtube_attributes .= '&amp;loop=1&amp;playlist=' . $metadata['pyre_youtube_id'][0];
										$vimeo_attributes   .= '&amp;loop=1';
										$data_loop           = 'yes';
									}

									if ( isset( $metadata['pyre_hide_video_controls'][0] ) && 'no' === $metadata['pyre_hide_video_controls'][0] ) {
										$video_attributes   .= ' controls';
										$youtube_attributes .= '&amp;controls=1';
										$video_zindex        = 'z-index: 1;';
									} else {
										$youtube_attributes .= '&amp;controls=0';
										$video_zindex        = 'z-index: -99;';
									}

									$heading_color = '';

									if ( isset( $metadata['pyre_heading_color'][0] ) && $metadata['pyre_heading_color'][0] ) {
										$heading_color = 'color:' . $metadata['pyre_heading_color'][0] . ';';
									}

									$heading_bg = '';

									if ( isset( $metadata['pyre_heading_bg'][0] ) && 'yes' === $metadata['pyre_heading_bg'][0] ) {
										$heading_bg = 'background-color: rgba(0,0,0, 0.4);';
										if ( isset( $metadata['pyre_heading_bg_color'][0] ) && '' !== $metadata['pyre_heading_bg_color'][0] ) {
											$rgb        = fusion_hex2rgb( $metadata['pyre_heading_bg_color'][0] );
											$heading_bg = 'background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',0.4);';
										}
									}

									$caption_color = '';

									if ( isset( $metadata['pyre_caption_color'][0] ) && $metadata['pyre_caption_color'][0] ) {
										$caption_color = 'color:' . $metadata['pyre_caption_color'][0] . ';';
									}

									$caption_bg = '';

									if ( isset( $metadata['pyre_caption_bg'][0] ) && 'yes' === $metadata['pyre_caption_bg'][0] ) {
										$caption_bg = 'background-color: rgba(0, 0, 0, 0.4);';

										if ( isset( $metadata['pyre_caption_bg_color'][0] ) && '' !== $metadata['pyre_caption_bg_color'][0] ) {
											$rgb        = fusion_hex2rgb( $metadata['pyre_caption_bg_color'][0] );
											$caption_bg = 'background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',0.4);';
										}
									}

									$video_bg_color = '';

									if ( isset( $metadata['pyre_video_bg_color'][0] ) && $metadata['pyre_video_bg_color'][0] ) {
										$video_bg_color_hex = fusion_hex2rgb( $metadata['pyre_video_bg_color'][0] );
										$video_bg_color     = 'background-color: rgba(' . $video_bg_color_hex[0] . ', ' . $video_bg_color_hex[1] . ', ' . $video_bg_color_hex[2] . ', 0.4);';
									}

									$video = false;

									if ( isset( $metadata['pyre_type'][0] ) ) {
										if ( isset( $metadata['pyre_type'][0] ) && in_array( $metadata['pyre_type'][0], array( 'self-hosted-video', 'youtube', 'vimeo' ), true ) ) {
											$video = true;
										}
									}

									if ( isset( $metadata['pyre_type'][0] ) && 'self-hosted-video' === $metadata['pyre_type'][0] ) {
										$background_class = 'self-hosted-video-bg';
									}

									$heading_font_size = 'font-size:60px;line-height:80px;';
									if ( isset( $metadata['pyre_heading_font_size'][0] ) && $metadata['pyre_heading_font_size'][0] ) {
										$line_height       = $metadata['pyre_heading_font_size'][0] * 1.2;
										$heading_font_size = 'font-size:' . $metadata['pyre_heading_font_size'][0] . 'px;line-height:' . $line_height . 'px;';
									}

									$caption_font_size = 'font-size: 24px;line-height:38px;';
									if ( isset( $metadata['pyre_caption_font_size'][0] ) && $metadata['pyre_caption_font_size'][0] ) {
										$line_height       = $metadata['pyre_caption_font_size'][0] * 1.2;
										$caption_font_size = 'font-size:' . $metadata['pyre_caption_font_size'][0] . 'px;line-height:' . $line_height . 'px;';
									}

									$heading_styles = $heading_color . $heading_font_size;
									$caption_styles = $caption_color . $caption_font_size;
									$heading_title_sc_wrapper_class = '';
									$caption_title_sc_wrapper_class = '';

									if ( ! isset( $metadata['pyre_heading_separator'][0] ) ) {
										$metadata['pyre_heading_separator'][0] = 'none';
									}

									if ( ! isset( $metadata['pyre_caption_separator'][0] ) ) {
										$metadata['pyre_caption_separator'][0] = 'none';
									}

									if ( 'center' !== $metadata['pyre_content_alignment'][0] ) {
										$metadata['pyre_heading_separator'][0] = 'none';
										$metadata['pyre_caption_separator'][0] = 'none';
									}

									if ( 'center' === $metadata['pyre_content_alignment'][0] ) {
										if ( 'none' !== $metadata['pyre_heading_separator'][0] ) {
											$heading_title_sc_wrapper_class = ' fusion-block-element';
										}

										if ( 'none' !== $metadata['pyre_caption_separator'][0] ) {
											$caption_title_sc_wrapper_class = ' fusion-block-element';
										}
									}
								?>
									<li data-mute="<?php echo esc_attr( $data_mute ); ?>" data-loop="<?php echo esc_attr( $data_loop ); ?>" data-autoplay="<?php echo esc_attr( (string) $data_autoplay ); ?>">
										<div class="slide-content-container slide-content-<?php if ( isset( $metadata['pyre_content_alignment'][0] ) && $metadata['pyre_content_alignment'][0] ) { echo esc_attr( $metadata['pyre_content_alignment'][0] ); } ?>" style="display: none;">
											<div class="slide-content" style="<?php echo esc_attr( $content_max_width ); ?>">
												<?php if ( isset( $metadata['pyre_heading'][0] ) && $metadata['pyre_heading'][0] ) : ?>
													<div class="heading <?php echo ( $heading_bg ) ? 'with-bg' : ''; ?>">
														<div class="fusion-title-sc-wrapper<?php echo esc_attr( $heading_title_sc_wrapper_class ); ?>" style="<?php echo esc_attr( $heading_bg ); ?>">
															<?php echo do_shortcode( '[fusion_title size="2" content_align="' . $metadata['pyre_content_alignment'][0] . '" sep_color="' . $metadata['pyre_heading_color'][0] . '" margin_top="0px" margin_bottom="0px" style_type="' . $metadata['pyre_heading_separator'][0] . '" style_tag="' . $heading_styles . '"]' . do_shortcode( $metadata['pyre_heading'][0] ) . '[/fusion_title]' ); ?>
														</div>
													</div>
												<?php endif; ?>
												<?php if ( isset( $metadata['pyre_caption'][0] ) && $metadata['pyre_caption'][0] ) : ?>
													<div class="caption <?php echo ( $caption_bg ) ? 'with-bg' : ''; ?>">
														<div class="fusion-title-sc-wrapper<?php echo esc_attr( $caption_title_sc_wrapper_class ); ?>" style="<?php echo esc_attr( $caption_bg ); ?>">
															<?php echo do_shortcode( '[fusion_title size="3" content_align="' . $metadata['pyre_content_alignment'][0] . '" sep_color="' . $metadata['pyre_caption_color'][0] . '" margin_top="0px" margin_bottom="0px" style_type="' . $metadata['pyre_caption_separator'][0] . '" style_tag="' . $caption_styles . '"]' . do_shortcode( $metadata['pyre_caption'][0] ) . '[/fusion_title]' ); ?>
														</div>
													</div>
												<?php endif; ?>
												<?php if ( isset( $metadata['pyre_link_type'][0] ) && 'button' === $metadata['pyre_link_type'][0] ) : ?>
													<div class="buttons" >
														<?php if ( isset( $metadata['pyre_button_1'][0] ) && $metadata['pyre_button_1'][0] ) : ?>
															<div class="tfs-button-1"><?php echo do_shortcode( $metadata['pyre_button_1'][0] ); ?></div>
														<?php endif; ?>
														<?php if ( isset( $metadata['pyre_button_2'][0] ) && $metadata['pyre_button_2'][0] ) : ?>
															<div class="tfs-button-2"><?php echo do_shortcode( $metadata['pyre_button_2'][0] ); ?></div>
														<?php endif; ?>
													</div>
												<?php endif; ?>
											</div>
										</div>
										<?php if ( isset( $metadata['pyre_link_type'][0] ) && 'full' === $metadata['pyre_link_type'][0] && isset( $metadata['pyre_slide_link'][0] ) && $metadata['pyre_slide_link'][0] ) : ?>
											<a href="<?php echo esc_url_raw( $metadata['pyre_slide_link'][0] ); ?>" class="overlay-link" <?php echo ( isset( $metadata['pyre_slide_target'][0] ) && 'yes' === $metadata['pyre_slide_target'][0] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>></a>
										<?php endif; ?>
										<?php if ( isset( $metadata['pyre_preview_image'][0] ) && $metadata['pyre_preview_image'][0] && isset( $metadata['pyre_type'][0] ) && 'self-hosted-video' === $metadata['pyre_type'][0] ) : ?>
											<?php $mobile_video_image = ( class_exists( 'Fusion_Sanitize' ) ) ? $fusion_library->sanitize->css_asset_url( $metadata['pyre_preview_image'][0] ) : ''; ?>
											<div class="mobile_video_image" style="background-image: url(<?php echo esc_url_raw( $mobile_video_image ); ?>);"></div>
										<?php elseif ( isset( $metadata['pyre_type'][0] ) && 'self-hosted-video' === $metadata['pyre_type'][0] ) : ?>
											<?php $mobile_video_image = ( class_exists( 'Fusion_Sanitize' ) ) ? $fusion_library->sanitize->css_asset_url( FUSION_CORE_URL . '/images/video_preview.jpg' ) : FUSION_CORE_URL . '/images/video_preview.jpg'; ?>
											<div class="mobile_video_image" style="background-image: url(<?php echo esc_url_raw( $mobile_video_image ); ?>);"></div>
										<?php endif; ?>
										<?php if ( $video_bg_color && true === $video ) : ?>
											<div class="overlay" style="<?php echo esc_attr( $video_bg_color ); ?>"></div>
										<?php endif; ?>
										<div class="background <?php echo esc_attr( $background_class ); ?>" style="<?php echo esc_attr( $background_image ); ?>max-width:<?php echo esc_attr( $slider_settings['slider_width'] ); ?>;height:<?php echo esc_attr( $slider_settings['slider_height'] ); ?>;filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo esc_url_raw( $image_url[0] ); ?>', sizingMethod='scale');-ms-filter:'progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo esc_url_raw( $image_url[0] ); ?>', sizingMethod='scale')';" data-imgwidth="<?php echo esc_attr( $img_width ); ?>">
											<?php if ( isset( $metadata['pyre_type'][0] ) ) : ?>
												<?php if ( 'self-hosted-video' === $metadata['pyre_type'][0] && ( $metadata['pyre_webm'][0] || $metadata['pyre_mp4'][0] || $metadata['pyre_ogg'][0] ) ) : ?>
													<video width="1800" height="700" <?php echo $video_attributes; // WPCS: XSS ok. ?> preload="auto">
														<?php if ( array_key_exists( 'pyre_mp4', $metadata ) && $metadata['pyre_mp4'][0] ) : ?>
															<source src="<?php echo esc_url_raw( $metadata['pyre_mp4'][0] ); ?>" type="video/mp4">
														<?php endif; ?>
														<?php if ( array_key_exists( 'pyre_ogg', $metadata ) && $metadata['pyre_ogg'][0] ) : ?>
															<source src="<?php echo esc_url_raw( $metadata['pyre_ogg'][0] ); ?>" type="video/ogg">
														<?php endif; ?>
														<?php if ( array_key_exists( 'pyre_webm', $metadata ) && $metadata['pyre_webm'][0] ) : ?>
															<source src="<?php echo esc_url_raw( $metadata['pyre_webm'][0] ); ?>" type="video/webm">
														<?php endif; ?>
													</video>
												<?php endif; ?>
											<?php endif; ?>
											<?php if ( isset( $metadata['pyre_type'][0] ) && isset( $metadata['pyre_youtube_id'][0] ) && 'youtube' === $metadata['pyre_type'][0] && $metadata['pyre_youtube_id'][0] ) : ?>
												<div style="position: absolute; top: 0; left: 0; <?php echo esc_attr( $video_zindex ); ?> width: 100%; height: 100%" data-youtube-video-id="<?php echo esc_attr( $metadata['pyre_youtube_id'][0] ); ?>" data-video-aspect-ratio="<?php echo esc_attr( $aspect_ratio ); ?>">
													<div id="video-<?php echo esc_attr( $metadata['pyre_youtube_id'][0] ); ?>-inner">
														<iframe height="100%" width="100%" src="https://www.youtube.com/embed/<?php echo $metadata['pyre_youtube_id'][0]; // WPCS: XSS ok. ?>?wmode=transparent&amp;modestbranding=1&amp;showinfo=0&amp;autohide=1&amp;enablejsapi=1&amp;rel=0&amp;vq=hd720&amp;<?php echo $youtube_attributes; // WPCS: XSS ok. ?>"></iframe>
													</div>
												</div>
											<?php endif; ?>
											<?php if ( isset( $metadata['pyre_type'][0] ) && isset( $metadata['pyre_vimeo_id'][0] ) &&  'vimeo' === $metadata['pyre_type'][0] && $metadata['pyre_vimeo_id'][0] ) : ?>
												<div style="position: absolute; top: 0; left: 0; <?php echo esc_attr( $video_zindex ); ?> width: 100%; height: 100%" data-mute="<?php echo esc_attr( $data_mute ); ?>" data-vimeo-video-id="<?php echo esc_attr( $metadata['pyre_vimeo_id'][0] ); ?>" data-video-aspect-ratio="<?php echo esc_attr( $aspect_ratio ); ?>">
													<iframe src="https://player.vimeo.com/video/<?php echo $metadata['pyre_vimeo_id'][0]; // WPCS: XSS ok. ?>?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;badge=0&amp;title=0<?php echo $vimeo_attributes; // WPCS: XSS ok. ?>" height="100%" width="100%" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
												</div>
											<?php endif; ?>
										</div>
									</li>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</ul>
						</div>
					</div>
				<?php endif;

				$html = ob_get_clean();

				return $html;

			}

			/**
			 * Builds the wrapper attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function wrapper_attr() {

				$attr = fusion_builder_visibility_atts( self::$parent_args['hide_on_mobile'], array(
					'class' => 'fusion-slider-container',
				) );

				$term_details = get_term_by( 'slug', self::$parent_args['name'], 'slide-page' );

				$attr['class'] .= ' fusion-slider-' . $term_details->term_id;

				if ( '100%' === self::$slider_settings['slider_width'] && ! self::$slider_settings['full_screen'] ) {
					$attr['class'] .= ' full-width-slider';
				}

				if ( '100%' !== self::$slider_settings['slider_width'] && ! self::$slider_settings['full_screen'] ) {
					$attr['class'] .= ' fixed-width-slider';
				}

				$attr['class'] .= ' ' . self::$parent_args['class'];
				$attr['id'] = 'fusion-slider-sc-' . self::$parent_args['name'];

				$attr['style'] = 'height:' . self::$slider_settings['slider_height'] . '; max-width:' . self::$slider_settings['slider_width'] . ';';

				return $attr;

			}

			/**
			 * Builds the container attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function container_attr() {

				$attr = array(
					'class' => 'tfs-slider flexslider main-flex',
				);

				if ( self::$slider_settings ) {
					foreach ( self::$slider_settings as $slider_setting => $slider_setting_value ) {
						$attr[ 'data-' . $slider_setting ] = $slider_setting_value;
					}
				}

				if ( '100%' === self::$slider_settings['slider_width'] && ! self::$slider_settings['full_screen'] ) {
					$attr['class'] .= ' full-width-slider';
				}

				if ( '100%' !== self::$slider_settings['slider_width'] && ! self::$slider_settings['full_screen'] ) {
					$attr['class'] .= ' fixed-width-slider';
				}

				$attr['style'] = 'max-width:' . self::$slider_settings['slider_width'] . ';';

				return $attr;

			}
		}
	} // End if().

	new FusionSC_FusionSlider();
} // End if().

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_fusionslider() {
	if ( ! class_exists( 'Fusion_Slider' ) ) {
		return;
	}
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Fusion Slider', 'fusion-core' ),
		'shortcode'  => 'fusion_fusionslider',
		'icon'       => 'fusiona-TFicon',
		'preview'    => FUSION_CORE_PATH . '/shortcodes/previews/fusion-fusion-slider-preview.php',
		'preview_id' => 'fusion-builder-block-module-fusion-slider-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Slider Name', 'fusion-core' ),
				'description' => esc_attr__( 'Select the slider you want to use.  The options will appear as the slider name next to the number of slides in brackets.', 'fusion-core' ),
				'param_name'  => 'name',
				'value'       => FusionCore_Plugin::get_fusion_sliders(),
				'default'     => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-core' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-core' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-core' ),
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-core' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-core' ),
				'param_name'  => 'id',
				'value'       => '',
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-core' ),
			),
		),
	) );
}

// Priority 20 to make sure its loaded after setup_fusion_slider.
add_action( 'wp_loaded', 'fusion_element_fusionslider', 20 );
