<?php
/**
 * Widget Class.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Widget class.
 */
class Fusion_Widget_Social_Links extends WP_Widget {

	/**
	 * Custom social icon array.
	 *
	 * @static
	 * @access public
	 * @since 5.0.0
	 * @var array
	 */
	public static $custom_icons = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		$this->set_custom_icons();

		$widget_ops  = array(
			'classname' => 'social_links',
			'description' => '',
		);
		$control_ops = array(
			'id_base' => 'social_links-widget',
		);

		parent::__construct( 'social_links-widget', 'Avada: Social Links', $widget_ops, $control_ops );
	}

	/**
	 * Sets custom icons array variable.
	 *
	 * @static
	 * @access public
	 * @since  5.0.0
	 * @return  void
	 */
	public function set_custom_icons() {
		$theme_options_array = Avada()->settings->get( 'social_media_icons' );
		$custom_icons = array();

		if ( is_array( $theme_options_array ) && array_key_exists( 'icon', $theme_options_array ) && is_array( $theme_options_array['icon'] ) ) {
			$custom_icon_indices = array_keys( $theme_options_array['icon'], 'custom' );

			$i = 0;
			if ( isset( $custom_icon_indices ) && is_array( $custom_icon_indices ) ) {
				foreach ( $custom_icon_indices as $name => $index ) {
					$icon_key = 'custom_' . $index;
					$i++;
					$custom_icons[ $icon_key ] = array(
						'label' => 'Custom ' . $i . ' (' . $theme_options_array['custom_title'][ $index ] . ')',
						'color' => '',
					);
				}
			}
		}
		self::$custom_icons = $custom_icons;
	}

	/**
	 * Echoes the widget content.
	 *
	 * @access public
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		extract( $args );
		$title     = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$add_class = '';
		$style     = '';
		$nofollow  = ( Avada()->settings->get( 'nofollow_social_links' ) ) ? 'nofollow' : '';
		$to_social_networks = Avada()->settings->get( 'social_media_icons' );

		if ( ! isset( $instance['tooltip_pos'] ) || '' === $instance['tooltip_pos'] ) {
			$instance['tooltip_pos'] = 'top';
		}

		if ( ! isset( $instance['icon_color'] ) || '' === $instance['icon_color'] ) {
			$instance['icon_color'] = '#bebdbd';
		}

		if ( ! isset( $instance['boxed_icon'] ) || '' === $instance['boxed_icon'] ) {
			$instance['boxed_icon'] = 'Yes';
		}

		if ( ! isset( $instance['boxed_color'] ) || '' === $instance['boxed_color'] ) {
			$instance['boxed_color'] = '#e8e8e8';
		}

		if ( ! isset( $instance['boxed_icon_radius'] ) || '' === $instance['boxed_icon_radius'] ) {
			$instance['boxed_icon_radius'] = '4px';
		}

		if ( ! isset( $instance['linktarget'] ) || '' === $instance['linktarget'] ) {
			$instance['linktarget'] = '_self';
		}

		if ( ! isset( $instance['color_type'] ) || '' === $instance['color_type'] ) {
			$instance['color_type'] = 'custom';
		}

		if ( isset( $instance['boxed_icon'] ) && isset( $instance['boxed_icon_radius'] ) && 'Yes' === $instance['boxed_icon'] && ( $instance['boxed_icon_radius'] || '0' === $instance['boxed_icon_radius'] ) ) {
			$instance['boxed_icon_radius'] = ( 'round' === $instance['boxed_icon_radius'] ) ? '50%' : $instance['boxed_icon_radius'];
			$style .= 'border-radius:' . $instance['boxed_icon_radius'] . ';';
		}

		if ( isset( $instance['boxed_icon'] )  && 'Yes' === $instance['boxed_icon'] && isset( $instance['boxed_icon_padding'] ) && isset( $instance['boxed_icon_padding'] ) ) {
			$style .= 'padding:' . $instance['boxed_icon_padding'] . ';';
		}

		if ( isset( $instance['boxed_icon'] ) && 'Yes' === $instance['boxed_icon'] ) {
			$add_class .= ' boxed-icons';
		}

		if ( ! isset( $instance['icons_font_size'] ) || '' === $instance['icons_font_size'] ) {
			$instance['icons_font_size'] = '16px';
		}

		$style .= 'font-size:' . $instance['icons_font_size'] . ';';

		$social_networks = array();
		foreach ( $instance as $name => $value ) {

			if ( false !== strpos( $name, '_link' ) && $value ) {
				$new_value = str_replace( '_link', '', $name );

				if ( 'facebook' === $new_value ) {
					$new_value = 'fb';
				} elseif ( 'gplus' === $new_value ) {
					$new_value = 'google';
				}
				$social_networks[ $name ] = $new_value;
			}
		}

		$social_networks_ordered = array();

		// Check TO Social Links, if not in $social_networks and use_to option is yes, then add.
		if ( isset( $instance['use_to'] ) && 'Yes' === $instance['use_to'] && isset( $to_social_networks ) && is_array( $to_social_networks ) ) {
			foreach ( $to_social_networks['icon'] as $key => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				$new_value = $value;
				if ( 'facebook' === $new_value ) {
					$new_value = 'fb';
				} elseif ( 'gplus' === $new_value ) {
					$new_value = 'google';
				}
				if ( ! in_array( $new_value, $social_networks ) && 'custom' !== $value ) {
					$social_networks[ $new_value . '_link' ] = $value;
					$instance[ $new_value . '_link' ] = $to_social_networks['url'][ $key ];
				}
			}
		}

		if ( isset( $to_social_networks['fusionredux_repeater_data'] ) && $to_social_networks && 0 < count( $to_social_networks['fusionredux_repeater_data'] ) ) {
			// Loop through the set of social networks and order them
			// according to the Theme Options > Social Media tab ordering.
			// Append those icons that are not set in Theme Options at the end.
			foreach ( $social_networks as $name => $value ) {

				if ( 'fb' == $value ) {
					$compare_value = 'facebook';
				} elseif ( 'google' == $value ) {
					$compare_value = 'gplus';
				} else {
					$compare_value = $value;
				}

				$social_network_position = array_search( $compare_value, $to_social_networks['icon'] );
				if ( $social_network_position || 0 === $social_network_position ) {
					$social_networks_ordered[ $social_network_position ] = $name;
					unset( $social_networks[ $name ] );
				} else {
					$social_networks[ $name ] = $value . '_link';
				}
			}

			// Make sure all custom icons from Theme Options > Social Media tab are included, if the widget option is set.
			if ( isset( $instance['show_custom'] ) && 'Yes' === $instance['show_custom'] ) {
				$custom_icon_indices = array_keys( $to_social_networks['icon'], 'custom' );

				foreach ( $custom_icon_indices as $name => $index ) {

					$network_icon_height = $to_social_networks['custom_source'][ $index ]['height'];
					$network_icon_width	= $to_social_networks['custom_source'][ $index ]['width'];
					$network_link = $to_social_networks['url'][ $index ];

					// Check if different URL is set for this custom icon in the widget.  If so, use that instead.
					$instance_key = 'custom_' . $index . '_link';
					if ( isset( $instance[ $instance_key ] ) && '' !== $instance[ $instance_key ] ) {
						$network_link = $instance[ $instance_key ];
					}

					$social_networks_ordered[ $index ] = array(
						'network_name'			=> $to_social_networks['custom_title'][ $index ],
						'network_icon'			=> $to_social_networks['custom_source'][ $index ]['url'],
						'network_icon_height'	=> $network_icon_height,
						'network_icon_width'	=> $network_icon_width,
						'network_link'			=> $network_link,
					);
				}
			}
		} // End if().

		ksort( $social_networks_ordered );
		$social_networks_ordered = array_merge( $social_networks_ordered, $social_networks );

		$icon_colors     = array();
		$icon_colors_max = 1;

		if ( isset( $instance['icon_color'] ) && $instance['icon_color'] ) {
			$icon_colors     = explode( '|', $instance['icon_color'] );
			$icon_colors_max = count( $icon_colors );
		}

		$box_colors     = array();
		$box_colors_max = 1;

		if ( isset( $instance['boxed_color'] ) && $instance['boxed_color'] ) {
			$box_colors     = explode( '|', $instance['boxed_color'] );
			$box_colors_max = count( $box_colors );
		}

		echo $before_widget; // WPCS: XSS ok.

		if ( $title ) {
			echo $before_title . $title . $after_title; // WPCS: XSS ok.
		}
		?>

		<div class="fusion-social-networks<?php echo esc_attr( $add_class ); ?>">

			<div class="fusion-social-networks-wrapper">
				<?php $icon_color_count = 0; ?>
				<?php $box_color_count  = 0; ?>

				<?php foreach ( $social_networks_ordered as $name => $value ) : ?>
					<?php
					if ( is_string( $value ) ) {
						if ( $to_social_networks && isset( $to_social_networks['fusionredux_repeater_data'] ) && 0 < count( $to_social_networks['fusionredux_repeater_data'] ) ) {
							$name = $value;
						}

						$value = str_replace( '_link', '', $value );

						$value = ( 'fb' === $value ) ? 'facebook' : $value;
						$value = ( 'google' === $value ) ? 'googleplus' : $value;
						$value = ( 'email' === $value ) ? 'mail' : $value;

						$tooltip = $value;
						$tooltip = ( 'googleplus' === $tooltip ) ? 'Google+' : $tooltip;
					} else {
						$tooltip = $value['network_name'];
					}

					$icon_style = '';
					$box_style  = '';

					if ( 'brand' === $instance['color_type'] ) {
						// If not custom social icon.
						if ( is_string( $value ) ) {
							// Get a list of all the available social networks.
							$social_icon_boxed_colors  = Fusion_Data::fusion_social_icons( false, true );
							$social_icon_boxed_colors['googleplus'] = array(
								'label' => 'Google+',
								'color' => '#dc4e41',
							);
							$social_icon_boxed_colors['mail'] = array(
								'label' => esc_html__( 'Email Address', 'Avada' ),
								'color' => '#000000',
							);

							$color = ( 'Yes' === $instance['boxed_icon'] ) ? '#ffffff' : $social_icon_boxed_colors[ $value ]['color'];
							$bg_color = ( 'Yes' === $instance['boxed_icon'] ) ? $social_icon_boxed_colors[ $value ]['color'] : '';

							$icon_style = 'color:' . $color . ';';
							$box_style = 'background-color:' . $bg_color . ';border-color:' . $bg_color . ';';
						}
					} else {
						if ( isset( $icon_colors[ $icon_color_count ] ) && $icon_colors[ $icon_color_count ] ) {
							$icon_style = 'color:' . trim( $icon_colors[ $icon_color_count ] ) . ';';
						} elseif ( isset( $icon_colors[ ( $icon_colors_max - 1 ) ] ) ) {
							$icon_style = 'color:' . trim( $icon_colors[ ( $icon_colors_max - 1 ) ] ) . ';';
						}

						if ( isset( $instance['boxed_icon'] ) && 'Yes' === $instance['boxed_icon'] && isset( $box_colors[ $box_color_count ] ) && $box_colors[ $box_color_count ] ) {
							$box_style = 'background-color:' . trim( $box_colors[ $box_color_count ] ) . ';border-color:' . trim( $box_colors[ $box_color_count ] ) . ';';
						} elseif ( isset( $instance['boxed_icon'] ) && 'Yes' === $instance['boxed_icon'] && isset( $box_colors[ ( $box_colors_max - 1 ) ] ) && ( ! isset( $box_colors[ $box_color_count ] ) || ! $box_colors[ $box_color_count ] ) ) {
							$box_style = 'background-color:' . trim( $box_colors[ ( $box_colors_max - 1 ) ] ) . ';border-color:' . trim( $box_colors[ ( $box_colors_max - 1 ) ] ) . ';';
						}
					}

					$tooltip_params = ' ';
					if ( 'none' !== strtolower( $instance['tooltip_pos'] ) ) {
						$tooltip_params = ' data-placement="' . strtolower( $instance['tooltip_pos'] ) . '" data-title="' . ucwords( $tooltip ) . '" data-toggle="tooltip" data-original-title="" ';
					}
					$tooltip = ucwords( $tooltip );
					if ( 'youtube' === strtolower( $tooltip ) ) {
						$tooltip = 'YouTube';
					}
					?>
					<?php if ( is_string( $value ) ) : ?>
						<?php if ( 'mail' === $value ) : ?>
							<?php $instance[ $name ] = 'mailto:' . antispambot( $instance[ $name ] ); ?>
						<?php endif; ?>
						<?php if ( isset( $instance[ $name ] ) ) : ?>
							<a class="fusion-social-network-icon fusion-tooltip fusion-<?php echo esc_attr( $value ); ?> fusion-icon-<?php echo esc_attr( $value ); ?>" href="<?php echo esc_url_raw( $instance[ $name ] ); ?>" <?php echo $tooltip_params; // WPCS: XSS ok. ?> title="<?php echo esc_attr( $tooltip ); ?>" aria-label="<?php echo esc_attr( ucwords( $tooltip ) ); ?>" rel="<?php echo esc_attr( $nofollow ); ?>" target="<?php echo esc_attr( $instance['linktarget'] ); ?>" style="<?php echo esc_attr( $style . $icon_style . $box_style ); ?>"></a>
						<?php endif; ?>
					<?php else : ?>
						<?php if ( 'mail' === $value ) : ?>
							<?php $value['network_link'] = 'mailto:' . antispambot( $value['network_link'] ); ?>
						<?php endif; ?>
						<a class="fusion-social-network-icon fusion-tooltip" target="<?php echo esc_attr( $instance['linktarget'] ); ?>" href="<?php echo esc_url_raw( $value['network_link'] ); ?>" rel="<?php esc_attr( $nofollow ); ?>" <?php echo $tooltip_params; // WPCS: XSS ok.?> title="" style="<?php echo esc_attr( $style . $box_style ); ?>"><img src="<?php echo esc_url_raw( $value['network_icon'] ); ?>" height="<?php echo esc_attr( $value['network_icon_height'] ); ?>" width="<?php echo esc_attr( $value['network_icon_width'] ); ?>" alt="<?php echo esc_attr( $value['network_name'] ); ?>" /></a>
					<?php endif; ?>

					<?php $icon_color_count++; ?>
					<?php $box_color_count++; ?>

				<?php endforeach; ?>

			</div>
		</div>

		<?php
		echo $after_widget; // WPCS: XSS ok.

	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * This function should check that `$new_instance` is set correctly. The newly-calculated
	 * value of `$instance` should be returned. If false is returned, the instance won't be
	 * saved/updated.
	 *
	 * @access public
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']              = isset( $new_instance['title'] ) ? $new_instance['title'] : '';
		$instance['linktarget']         = isset( $new_instance['linktarget'] ) ? $new_instance['linktarget'] : '';
		$instance['icons_font_size']    = isset( $new_instance['icons_font_size'] ) ? $new_instance['icons_font_size'] : '';
		$instance['icon_color']         = isset( $new_instance['icon_color'] ) ? $new_instance['icon_color'] : '';
		$instance['boxed_icon']         = isset( $new_instance['boxed_icon'] ) ? $new_instance['boxed_icon'] : '';
		$instance['boxed_color']        = isset( $new_instance['boxed_color'] ) ? $new_instance['boxed_color'] : '';
		$instance['color_type']         = isset( $new_instance['color_type'] ) ? $new_instance['color_type'] : '';
		$instance['boxed_icon_radius']  = isset( $new_instance['boxed_icon_radius'] ) ? $new_instance['boxed_icon_radius'] : '';
		$instance['boxed_icon_padding'] = isset( $new_instance['boxed_icon_padding'] ) ? $new_instance['boxed_icon_padding'] : '';
		$instance['tooltip_pos']        = isset( $new_instance['tooltip_pos'] ) ? $new_instance['tooltip_pos'] : '';
		$instance['show_custom']        = isset( $new_instance['show_custom'] ) ? $new_instance['show_custom'] : '';
		$instance['fb_link']            = isset( $new_instance['fb_link'] ) ? $new_instance['fb_link'] : '';
		$instance['flickr_link']        = isset( $new_instance['flickr_link'] ) ? $new_instance['flickr_link'] : '';
		$instance['rss_link']           = isset( $new_instance['rss_link'] ) ? $new_instance['twitter_link'] : '';
		$instance['twitter_link']       = isset( $new_instance['twitter_link'] ) ? $new_instance['twitter_link'] : '';
		$instance['vimeo_link']         = isset( $new_instance['vimeo_link'] ) ? $new_instance['vimeo_link'] : '';
		$instance['youtube_link']       = isset( $new_instance['youtube_link'] ) ? $new_instance['youtube_link'] : '';
		$instance['instagram_link']     = isset( $new_instance['instagram_link'] ) ? $new_instance['instagram_link'] : '';
		$instance['pinterest_link']     = isset( $new_instance['pinterest_link'] ) ? $new_instance['pinterest_link'] : '';
		$instance['tumblr_link']        = isset( $new_instance['tumblr_link'] ) ? $new_instance['tumblr_link'] : '';
		$instance['google_link']        = isset( $new_instance['google_link'] ) ? $new_instance['google_link'] : '';
		$instance['dribbble_link']      = isset( $new_instance['dribbble_link'] ) ? $new_instance['dribbble_link'] : '';
		$instance['digg_link']          = isset( $new_instance['digg_link'] ) ? $new_instance['digg_link'] : '';
		$instance['linkedin_link']      = isset( $new_instance['linkedin_link'] ) ? $new_instance['linkedin_link'] : '';
		$instance['blogger_link']       = isset( $new_instance['blogger_link'] ) ? $new_instance['blogger_link'] : '';
		$instance['skype_link']         = isset( $new_instance['skype_link'] ) ? $new_instance['skype_link'] : '';
		$instance['forrst_link']        = isset( $new_instance['forrst_link'] ) ? $new_instance['forrst_link'] : '';
		$instance['myspace_link']       = isset( $new_instance['myspace_link'] ) ? $new_instance['myspace_link'] : '';
		$instance['deviantart_link']    = isset( $new_instance['deviantart_link'] ) ? $new_instance['deviantart_link'] : '';
		$instance['yahoo_link']         = isset( $new_instance['yahoo_link'] ) ? $new_instance['yahoo_link'] : '';
		$instance['reddit_link']        = isset( $new_instance['reddit_link'] ) ? $new_instance['reddit_link'] : '';
		$instance['paypal_link']        = isset( $new_instance['paypal_link'] ) ? $new_instance['paypal_link'] : '';
		$instance['dropbox_link']       = isset( $new_instance['dropbox_link'] ) ? $new_instance['dropbox_link'] : '';
		$instance['soundcloud_link']    = isset( $new_instance['soundcloud_link'] ) ? $new_instance['soundcloud_link'] : '';
		$instance['vk_link']            = isset( $new_instance['vk_link'] ) ? $new_instance['vk_link'] : '';
		$instance['xing_link']          = isset( $new_instance['xing_link'] ) ? $new_instance['xing_link'] : '';
		$instance['yelp_link']          = isset( $new_instance['yelp_link'] ) ? $new_instance['yelp_link'] : '';
		$instance['spotify_link']       = isset( $new_instance['spotify_link'] ) ? $new_instance['spotify_link'] : '';
		$instance['email_link']         = isset( $new_instance['email_link'] ) ? $new_instance['email_link'] : '';
		$instance['use_to']             = isset( $new_instance['use_to'] ) ? $new_instance['use_to'] : '';

		if ( 0 < count( self::$custom_icons ) ) {
			foreach ( self::$custom_icons as $key => $value ) {
				$instance[ $key . '_link' ] = $new_instance[ $key . '_link' ];
			}
		}

		return $instance;

	}

	/**
	 * Outputs the settings update form.
	 *
	 * @access public
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {

		$defaults = array(
			'title'              => __( 'Get Social', 'Avada' ),
			'linktarget'         => '',
			'icons_font_size'    => '16px',
			'icon_color'         => '',
			'boxed_icon'         => 'No',
			'boxed_color'        => '',
			'color_type'         => 'custom',
			'boxed_icon_radius'  => '4px',
			'boxed_icon_padding' => '8px',
			'tooltip_pos'        => 'top',
			'rss_link'           => '',
			'fb_link'            => '',
			'twitter_link'       => '',
			'dribbble_link'      => '',
			'google_link'        => '',
			'linkedin_link'      => '',
			'blogger_link'       => '',
			'tumblr_link'        => '',
			'reddit_link'        => '',
			'yahoo_link'         => '',
			'deviantart_link'    => '',
			'vimeo_link'         => '',
			'youtube_link'       => '',
			'pinterest_link'     => '',
			'digg_link'          => '',
			'flickr_link'        => '',
			'forrst_link'        => '',
			'myspace_link'       => '',
			'skype_link'         => '',
			'instagram_link'     => '',
			'vk_link'            => '',
			'xing_link'          => '',
			'dropbox_link'       => '',
			'soundcloud_link'    => '',
			'paypal_link'        => '',
			'yelp_link'          => '',
			'spotify_link'       => '',
			'email_link'         => '',
			'show_custom'        => 'No',
			'use_to'             => 'No',
		);

		if ( 0 < count( self::$custom_icons ) ) {
			foreach ( self::$custom_icons as $key => $value ) {
				$defaults[ $key . '_link' ] = '';
			}
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'linktarget' ) ); ?>"><?php esc_attr_e( 'Link Target:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'linktarget' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'linktarget' ) ); ?>" value="<?php echo esc_attr( $instance['linktarget'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'icons_font_size' ) ); ?>"><?php esc_attr_e( 'Icons Font Size:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icons_font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icons_font_size' ) ); ?>" value="<?php echo esc_attr( $instance['icons_font_size'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'color_type' ) ); ?>"><?php esc_attr_e( 'Icons Color Type:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'color_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'color_type' ) ); ?>" class="widefat" style="width:100%;">
				<option value="custom" <?php echo ( 'custom' == $instance['color_type'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Custom Color', 'Avada' ); ?></option>
				<option value="brand" <?php echo ( 'brand' == $instance['color_type'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Brand Colors', 'Avada' ); ?></option>
			</select>
		</p>

		<p class="avada-widget-color-type-option-child">
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>"><?php esc_attr_e( 'Icons Color Hex Code:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>" value="<?php echo esc_attr( $instance['icon_color'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'boxed_icon' ) ); ?>"><?php esc_attr_e( 'Icons Boxed:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'boxed_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'boxed_icon' ) ); ?>" class="widefat" style="width:100%;">
				<option value="No" <?php echo ( 'No' == $instance['boxed_icon'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'No', 'Avada' ); ?></option>
				<option value="Yes" <?php echo ( 'Yes' == $instance['boxed_icon'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Yes', 'Avada' ); ?></option>
			</select>
		</p>

		<p class="avada-widget-color-type-option-child avada-widget-boxed-icon-background">
			<label for="<?php echo esc_attr( $this->get_field_id( 'boxed_color' ) ); ?>"><?php esc_attr_e( 'Boxed Icons Background Color Hex Code:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'boxed_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'boxed_color' ) ); ?>" value="<?php echo esc_attr( $instance['boxed_color'] ); ?>" />
		</p>

		<p class="avada-widget-boxed-icon-option-child">
			<label for="<?php echo esc_attr( $this->get_field_id( 'boxed_icon_radius' ) ); ?>"><?php esc_attr_e( 'Boxed Icons Radius:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'boxed_icon_radius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'boxed_icon_radius' ) ); ?>" value="<?php echo esc_attr( $instance['boxed_icon_radius'] ); ?>" />
		</p>

		<p class="avada-widget-boxed-icon-option-child">
			<label for="<?php echo esc_attr( $this->get_field_id( 'boxed_icon_padding' ) ); ?>"><?php esc_attr_e( 'Boxed Icons Padding:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'boxed_icon_padding' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'boxed_icon_padding' ) ); ?>" value="<?php echo esc_attr( $instance['boxed_icon_padding'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tooltip_pos' ) ); ?>"><?php esc_attr_e( 'Tooltip Position:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'tooltip_pos' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tooltip_pos' ) ); ?>" class="widefat" style="width:100%;">
				<option value="Top" <?php echo ( 'Top' == $instance['tooltip_pos'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Top', 'Avada' ); ?></option>
				<option value="Right" <?php echo ( 'Right' == $instance['tooltip_pos'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Right', 'Avada' ); ?></option>
				<option value="Bottom" <?php echo ( 'Bottom' == $instance['tooltip_pos'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Bottom', 'Avada' ); ?></option>
				<option value="Left" <?php echo ( 'Left' == $instance['tooltip_pos'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Left', 'Avada' ); ?></option>
				<option value="None" <?php echo ( 'None' == $instance['tooltip_pos'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'None', 'Avada' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_custom' ) ); ?>"><?php esc_attr_e( 'Show Custom Icons:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'show_custom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_custom' ) ); ?>" class="widefat" style="width:100%;">
				<option value="No" <?php echo ( 'No' == $instance['show_custom'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'No', 'Avada' ); ?></option>
				<option value="Yes" <?php echo ( 'Yes' == $instance['show_custom'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Yes', 'Avada' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'use_to' ) ); ?>"><?php esc_attr_e( 'Use Theme Option Links:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'use_to' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_to' ) ); ?>" class="widefat" style="width:100%;">
				<option value="No" <?php echo ( 'No' == $instance['use_to'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'No', 'Avada' ); ?></option>
				<option value="Yes" <?php echo ( 'Yes' == $instance['use_to'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Yes', 'Avada' ); ?></option>
			</select>
		</p>

		<?php
		// Create social network fields.
		$social_networks_full_array = Fusion_Data::fusion_social_icons( false, true );
		if ( 0 < count( self::$custom_icons ) ) {
			$social_networks_full_array = array_merge( $social_networks_full_array, self::$custom_icons );
		}
		foreach ( $social_networks_full_array as $key => $value ) {

			if ( 'facebook' == $key ) {
				$key = 'fb';
			} elseif ( 'gplus' == $key ) {
				$key = 'google';
			}

			echo '<p>';
			echo '<label for="' . esc_attr( $this->get_field_id( $key . '_link' ) ) . '">' . sprintf( esc_attr__( '%s Link:', 'Avada' ), esc_attr( $value['label'] ) ) . '</label>';
			echo '<input class="widefat" type="text" id="' . esc_attr( $this->get_field_id( $key . '_link' ) ) . '" name="' . esc_attr( $this->get_field_name( $key . '_link' ) ) . '" value="' . esc_attr( $instance[ $key . '_link' ] ) . '" />';
			echo '</p>';

		}

		$color_type_id  = $this->get_field_id( 'color_type' );
		$boxed_icon_id = $this->get_field_id( 'boxed_icon' );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				var $color_type_field = $("#<?php echo esc_attr( $color_type_id ); ?>");
				var $boxed_icon_field = $("#<?php echo esc_attr( $boxed_icon_id ); ?>");

				function checkBoxedIcons() {
					var color_type = $color_type_field.val();
					var boxed_icon = $boxed_icon_field.val();

					if ( boxed_icon === 'No' ) {
						$boxed_icon_field.parent().parent().find('.avada-widget-boxed-icon-option-child').hide();
						$boxed_icon_field.parent().parent().find('.avada-widget-boxed-icon-background').hide();
					} else {
						$boxed_icon_field.parent().parent().find('.avada-widget-boxed-icon-option-child').show();

						if ( color_type === 'custom' ) {
							$boxed_icon_field.parent().parent().find('.avada-widget-boxed-icon-background').show();
						}
					}
				}

				function checkColorType() {
					var color_type = $color_type_field.val();
					var boxed_icon = $boxed_icon_field.val();

					if ( color_type === 'brand' ) {
						$color_type_field.parent().parent().find('.avada-widget-color-type-option-child').hide();
					} else {
						$color_type_field.parent().parent().find('.avada-widget-color-type-option-child').show();

						if ( boxed_icon === 'No' ) {
							$boxed_icon_field.parent().parent().find('.avada-widget-boxed-icon-background').hide();
						}
					}
				}

				checkColorType();
				checkBoxedIcons();

				$color_type_field.on('change', function() {
					checkColorType();
				});
				$boxed_icon_field.on('change', function() {
					checkBoxedIcons();
				});

			});
		</script>
		<?php
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
