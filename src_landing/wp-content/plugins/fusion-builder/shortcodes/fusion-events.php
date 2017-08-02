<?php

if ( fusion_is_element_enabled( 'fusion_events' ) ) {

	if ( ! class_exists( 'FusionSC_FusionEvents' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_FusionEvents extends Fusion_Element {

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
				add_shortcode( 'fusion_events', array( $this, 'render' ) );
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

				$html     = '';
				$defaults = shortcode_atts(
					array(
						'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
						'class'          => '',
						'id'             => '',
						'cat_slug'       => '',
						'columns'        => '4',
						'number_posts'   => '4',
						'picture_size'   => 'cover',
					), $args
				);

				extract( $defaults );

				if ( class_exists( 'Tribe__Events__Main' ) ) {

					$args = array(
						'post_type' => 'tribe_events',
						'posts_per_page' => $number_posts,
						'order' => 'ASC',
					);

					if ( $cat_slug ) {
						$terms = explode( ',', $cat_slug );
						$args['tax_query'] = array(
							array(
								'taxonomy'  => 'tribe_events_cat',
								'field'     => 'slug',
								'terms'     => array_map( 'trim', $terms ),
							),
						);
					}

					switch ( $columns ) {
						case '1':
							$column_class = 'full-one';
							break;
						case '2':
							$column_class = 'one-half';
							break;
						case '3':
							$column_class = 'one-third';
							break;
						case '4':
							$column_class = 'one-fourth';
							break;
						case '5':
							$column_class = 'one-fifth';
							break;
						case '6':
							$column_class = 'one-sixth';
							break;
					}

					$events = fusion_cached_query( $args );

					if ( ! $events->have_posts() ) {
						return fusion_builder_placeholder( 'tribe_events', 'events' );
					}

					$class = fusion_builder_visibility_atts( $hide_on_mobile, $class );

					if ( $events->have_posts() ) {
						if ( $id ) {
							$id = ' id="' . $id . '"';
						}
						$html .= '<div class="fusion-events-shortcode ' . $class . '"' . $id . '>';
						$i       = 1;
						$last    = false;
						$columns = (int) $columns;

						while ( $events->have_posts() ) {
							$events->the_post();

							if ( $i == $columns ) {
								$last = true;
							}

							if ( $i > $columns ) {
								$i    = 1;
								$last = false;
							}

							if ( 1 == $columns ) {
								$last = true;
							}

							$html .= '<div class="fusion-' . $column_class . ' fusion-spacing-yes fusion-layout-column ' . ( ( $last ) ? 'fusion-column-last' : '' ) . '">';
							$html .= '<div class="fusion-column-wrapper">';
							$thumb_id = get_post_thumbnail_id();
							$thumb_link = wp_get_attachment_image_src( $thumb_id, 'full', true );
							$thumb_url = '';

							if ( has_post_thumbnail( get_the_ID() ) ) {
								$thumb_url = $thumb_link[0];
							} elseif ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
								$thumb_url = esc_url( trailingslashit( Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/tribe-related-events-placeholder.png' );
							}

							$img_class = ( has_post_thumbnail( get_the_ID() ) ) ? '' : 'fusion-events-placeholder';

							if ( $thumb_url ) {
								$thumb_img = '<img class="' . $img_class . '" src="' . $thumb_url . '" alt="' . esc_attr( get_the_title( get_the_ID() ) ) . '" />';
								if ( has_post_thumbnail( get_the_ID() ) && 'auto' == $picture_size ) {
									$thumb_img = get_the_post_thumbnail( get_the_ID(), 'full' );
								}
								$thumb_bg = '<span class="tribe-events-event-image" style="background-image: url(' . $thumb_url . '); -webkit-background-size: cover; background-size: cover; background-position: center center;"></span>';
							}
							$html .= '<div class="fusion-events-thumbnail hover-type-' . $fusion_settings->get( 'ec_hover_type' ) . '">';
							$html .= '<a href="' . get_the_permalink() . '" class="url" rel="bookmark" aria-label="' . get_the_title() . '">';

							if ( $thumb_url ) {
								$html .= ( 'auto' == $picture_size ) ? $thumb_img : $thumb_bg;
							} else {
								ob_start();
								/**
								 * The avada_placeholder_image hook.
								 *
								 * @hooked fusion_render_placeholder_image - 10 (outputs the HTML for the placeholder image)
								 */
								do_action( 'fusion_render_placeholder_image', 'fixed' );

								$placeholder = ob_get_clean();
								$html .= str_replace( 'fusion-placeholder-image', ' fusion-placeholder-image tribe-events-event-image', $placeholder );
							}

							$html .= '</a>';
							$html .= '</div>';
							$html .= '<div class="fusion-events-meta">';
							$html .= '<h2><a href="' . get_the_permalink() . '" class="url" rel="bookmark">' . get_the_title() . '</a></h2>';
							$html .= '<h4>' . tribe_events_event_schedule_details() . '</h4>';
							$html .= '</div>';
							$html .= '</div>';
							$html .= '</div>';

							if ( $last ) {
								$html .= '<div class="fusion-clearfix"></div>';
							}
							$i++;
						}
						// @codingStandardsIgnoreLine
						wp_reset_query();
						$html .= '<div class="fusion-clearfix"></div>';
						$html .= '</div>';
					}
					return $html;
				}
			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {

				global $wp_version, $content_media_query, $six_fourty_media_query, $three_twenty_six_fourty_media_query, $ipad_portrait_media_query, $content_min_media_query, $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$elements = array(
					'.fusion-events-shortcode .fusion-layout-column',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['background-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'timeline_bg_color' ) );

				if ( '0' !== Fusion_Color::new_color( $fusion_settings->get( 'timeline_bg_color' ) )->alpha ) {
					$css['global']['.fusion-events-shortcode .fusion-events-meta']['padding'] = '20px';
				}

				$elements = array(
					'.fusion-events-shortcode .fusion-layout-column',
					'.fusion-events-shortcode .fusion-events-thumbnail',
				);
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['border-color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'timeline_color' ) );

				return $css;

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
					'fusion-events',
					FusionBuilder::$js_folder_url . '/general/fusion-events.js',
					FusionBuilder::$js_folder_path . '/general/fusion-events.js',
					array( 'jquery', 'fusion-equal-heights' ),
					'1',
					true
				);
			}
		}
	}

	new FusionSC_FusionEvents();

}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_events() {
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		fusion_builder_map( array(
			'name'      => esc_attr__( 'Events', 'fusion-builder' ),
			'shortcode' => 'fusion_events',
			'icon'      => 'fusiona-tag',
			'params'    => array(
				array(
					'type'        => 'multiple_select',
					'heading'     => esc_attr__( 'Categories', 'fusion-builder' ),
					'description' => esc_attr__( 'Select a category or leave blank for all.', 'fusion-builder' ),
					'param_name'  => 'cat_slug',
					'value'       => fusion_builder_shortcodes_categories( 'tribe_events_cat' ),
					'default'     => '',
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_attr__( 'Number of Events', 'fusion-builder' ),
					'description' => esc_attr__( 'Select the number of events to display.', 'fusion-builder' ),
					'param_name'  => 'number_posts',
					'value'       => '4',
				),
				array(
					'type'        => 'select',
					'heading'     => esc_attr__( 'Maximum Columns', 'fusion-builder' ),
					'description' => esc_attr__( 'Select the number of max columns to display.', 'fusion-builder' ),
					'param_name'  => 'columns',
					'value'       => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'default'     => '4',
				),
				array(
					'type'        => 'radio_button_set',
					'heading'     => esc_attr__( 'Picture Size', 'fusion-builder' ),
					'description' => __( 'cover = image will scale to cover the container, <br />auto = width and height will adjust to the image.', 'fusion-builder' ),
					'param_name'  => 'picture_size',
					'value'       => array(
						'cover' => esc_attr__( 'Cover', 'fusion-builder' ),
						'auto'  => esc_attr__( 'Auto', 'fusion-builder' ),
					),
					'default' => 'cover',
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
}
add_action( 'wp_loaded', 'fusion_element_events' );
