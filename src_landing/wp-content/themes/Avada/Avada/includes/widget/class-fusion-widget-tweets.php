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
class Fusion_Widget_Tweets extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {

		$widget_ops = array(
			'classname' => 'tweets',
			'description' => '',
		);
		$control_ops = array(
			'id_base' => 'tweets-widget',
		);

		parent::__construct( 'tweets-widget', 'Avada: Twitter', $widget_ops, $control_ops );

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

		$title               = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$twitter_id          = isset( $instance['twitter_id'] ) ? $instance['twitter_id'] : '';
		$count               = (int) isset( $instance['count'] ) ? $instance['count'] : 3;

		$icon_color          = isset( $instance['icon_color'] ) ? $instance['icon_color'] : '';
		$consumer_key        = isset( $instance['consumer_key'] ) ? $instance['consumer_key'] : '';
		$consumer_secret     = isset( $instance['consumer_secret'] ) ? $instance['consumer_secret'] : '';
		$access_token        = isset( $instance['access_token'] ) ? $instance['access_token'] : '';
		$access_token_secret = isset( $instance['access_token_secret'] ) ? $instance['access_token_secret'] : '';

		$widget_id           = isset( $instance['widget_id'] ) ? $instance['widget_id'] : '';
		$widget_type         = isset( $instance['widget_type'] ) ? $instance['widget_type'] : 'avada_style';
		$width               = isset( $instance['width'] ) ? $instance['width'] : '400';
		$height              = isset( $instance['height'] ) ? $instance['height'] : '400';
		$theme               = isset( $instance['theme'] ) ? $instance['theme'] : 'light';
		$link_color          = isset( $instance['link_color'] ) ? $instance['link_color'] : Avada()->settings->get( 'link_color' );
		$border_color        = isset( $instance['border_color'] ) ? $instance['border_color'] : Avada()->settings->get( 'timeline_color' );
		$show_header         = isset( $instance['show_header'] ) ? $instance['show_header'] : 1;
		$show_footer         = isset( $instance['show_footer'] ) ? $instance['show_footer'] : 1;
		$show_borders        = isset( $instance['show_borders'] ) ? $instance['show_borders'] : 1;
		$transparent_bg      = isset( $instance['transparent_bg'] ) ? $instance['transparent_bg'] : 0;
		$chrome              = $this->get_chrome( $instance );

		echo $before_widget; // WPCS: XSS ok.

		if ( $title ) {
			echo $before_title . $title . $after_title; // WPCS: XSS ok.
		}

		if ( 'twitter_style' == $widget_type || ( 'twitter_preconfig_style' == $widget_type && $widget_id ) ) {
			$widget_params = array(
				'title'          => $title,
				'twitter_id'     => $twitter_id,
				'count'          => $count,
				'widget_id'      => $widget_id,
				'widget_type'    => $widget_type,
				'width'          => $width,
				'height'         => $height,
				'theme'          => $theme,
				'link_color'     => $link_color,
				'border_color'   => $border_color,
				'show_header'    => $show_header,
				'show_footer'    => $show_footer,
				'show_borders'   => $show_borders,
				'transparent_bg' => $transparent_bg,
				'chrome'         => $chrome,
			);
			$this->render_new_widget( $widget_params );
		} else {
			if ( $twitter_id && $consumer_key && $consumer_secret && $access_token && $access_token_secret && $count ) {
				$widget_id = $args['widget_id'];
				$widget_params = array(
					'title'               => $title,
					'twitter_id'          => $twitter_id,
					'count'               => $count,
					'widget_id'           => $widget_id,
					'icon_color'          => $icon_color,
					'consumer_key'        => $consumer_key,
					'consumer_secret'     => $consumer_secret,
					'access_token'        => $access_token,
					'access_token_secret' => $access_token_secret,
				);
				$this->render_old_widget( $widget_params );
			}
		} // End if().

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

		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['twitter_id']          = $new_instance['twitter_id'];
		$instance['count']               = $new_instance['count'];

		$instance['icon_color']          = $new_instance['icon_color'];
		$instance['consumer_key']        = $new_instance['consumer_key'];
		$instance['consumer_secret']     = $new_instance['consumer_secret'];
		$instance['access_token']        = $new_instance['access_token'];
		$instance['access_token_secret'] = $new_instance['access_token_secret'];

		$instance['widget_id']           = $new_instance['widget_id'];
		$instance['widget_type']         = $new_instance['widget_type'];
		$instance['width']               = $new_instance['width'];
		$instance['height']              = $new_instance['height'];
		$instance['theme']               = $new_instance['theme'];
		$instance['link_color']          = $new_instance['link_color'];
		$instance['border_color']        = $new_instance['border_color'];
		$instance['show_header']         = $new_instance['show_header'];
		$instance['show_footer']         = $new_instance['show_footer'];
		$instance['show_borders']        = $new_instance['show_borders'];
		$instance['transparent_bg']      = $new_instance['transparent_bg'];

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
			'title'               => __( 'Recent Tweets', 'Avada' ),
			'twitter_id'          => '',
			'count'               => 3,

			'icon_color'          => '',
			'consumer_key'        => '',
			'consumer_secret'     => '',
			'access_token'        => '',
			'access_token_secret' => '',

			'widget_id'           => '',
			'widget_type'         => 'avada_style',
			'width'               => '',
			'height'              => '',
			'theme'               => 'light',
			'link_color'          => Avada()->settings->get( 'link_color' ),
			'border_color'        => Avada()->settings->get( 'timeline_color' ),
			'show_header'         => 'on',
			'show_footer'         => 'on',
			'show_borders'        => 'on',
			'transparent_bg'      => 'off',
		);

		$instance        = wp_parse_args( (array) $instance, $defaults );
		$twitter_doc_url = 'https://theme-fusion.com/avada-doc/twitter-widget/';

		?>

		<script type="text/javascript">
			jQuery( document ).ready( function() {
			var $widget_type_select = jQuery( '#<?php echo esc_attr( $this->get_field_id( 'widget_type' ) ); ?>' );
				if ( $widget_type_select.val() == 'twitter_style' ) {
					$widget_type_select.parents( '.widget' ).find( '.avada_style' ).hide();
					$widget_type_select.parents( '.widget' ).find( '.twitter_style' ).show();
					$widget_type_select.parents( '.widget' ).find( '.general_option' ).show();
					$widget_type_select.parents( '.widget' ).find( '.widget_id' ).hide();
				} else if ( $widget_type_select.val() == 'avada_style' ) {
					$widget_type_select.parents( '.widget' ).find( '.avada_style' ).show();
					$widget_type_select.parents( '.widget' ).find( '.general_option' ).show();
					$widget_type_select.parents( '.widget' ).find( '.twitter_style' ).hide();
				} else {
					$widget_type_select.parents( '.widget' ).find( '.twitter_style' ).hide();
					$widget_type_select.parents( '.widget' ).find( '.avada_style' ).hide();
					$widget_type_select.parents( '.widget' ).find( '.general_option' ).hide();
					$widget_type_select.parents( '.widget' ).find( '.widget_id' ).show();
					$widget_type_select.parents( '.widget' ).find( '.twitter_id' ).hide();

				}

				jQuery( '.widget-type-selector' ).change( function() {
					if ( jQuery( this ).val() == 'twitter_style' ) {
						jQuery( this ).parents( '.widget' ).find( '.avada_style' ).hide();
						jQuery( this ).parents( '.widget' ).find( '.twitter_style' ).show();
						jQuery( this ).parents( '.widget' ).find( '.general_option' ).show();
						jQuery( this ).parents( '.widget' ).find( '.widget_id' ).hide();
					} else if ( jQuery( this ).val() == 'avada_style' ) {
						jQuery( this ).parents( '.widget' ).find( '.avada_style' ).show();
						jQuery( this ).parents( '.widget' ).find( '.general_option' ).show();
						jQuery( this ).parents( '.widget' ).find( '.twitter_style' ).hide();
					} else {
						jQuery( this ).parents( '.widget' ).find( '.twitter_style' ).hide();
						jQuery( this ).parents( '.widget' ).find( '.avada_style' ).hide();
						jQuery( this ).parents( '.widget' ).find( '.general_option' ).hide();
						jQuery( this ).parents( '.widget' ).find( '.widget_id' ).show();
						jQuery( this ).parents( '.widget' ).find( '.twitter_id' ).hide();

					}
				});
			});
		</script>

		<p><?php printf( esc_attr__( 'For general setup information or information on how to setup a Twitter App or a Twitter Widget on twitter.com, please see our documentation: %s', 'Avada' ), '<a href="' . esc_url_raw( $twitter_doc_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_attr__( 'How to setup the Avada twitter widget.', 'Avada' ) . '</a>' ); ?></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_type' ) ); ?>"><?php esc_attr_e( 'Widget Style:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'widget_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_type' ) ); ?>" class="widefat widget-type-selector" style="width:100%;">
				<option value="twitter_preconfig_style" <?php selected( $instance['widget_type'], 'twitter_preconfig_style' ); ?>><?php esc_attr_e( 'Twitter Preconfigured Style (deprecated)', 'Avada' ); ?></option>
				<option value="twitter_style" <?php selected( $instance['widget_type'], 'twitter_style' ); ?>><?php esc_attr_e( 'Twitter Style', 'Avada' ); ?></option>
				<option value="avada_style" <?php selected( $instance['widget_type'], 'avada_style' ); ?>><?php esc_attr_e( 'Avada Style', 'Avada' ); ?></option>
			</select>
		</p>

		<p  class="general_option twitter_id">
			<label for="<?php echo esc_attr( $this->get_field_id( 'twitter_id' ) ); ?>"><?php esc_attr_e( 'Twitter Username:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'twitter_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_id' ) ); ?>" value="<?php echo esc_attr( $instance['twitter_id'] ); ?>" />
		</p>

		<p class="twitter_style widget_id">
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>"><?php esc_attr_e( 'Twitter Widget ID:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_id' ) ); ?>" value="<?php echo esc_attr( $instance['widget_id'] ); ?>" />
		</p>

		<p class="avada_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>"><?php esc_attr_e( 'Consumer Key:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumer_key' ) ); ?>" value="<?php echo esc_attr( $instance['consumer_key'] ); ?>" />
		</p>

		<p class="avada_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>"><?php esc_attr_e( 'Consumer Secret:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'consumer_secret' ) ); ?>" value="<?php echo esc_attr( $instance['consumer_secret'] ); ?>" />
		</p>

		<p class="avada_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>"><?php esc_attr_e( 'Access Token:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'access_token' ) ); ?>" value="<?php echo esc_attr( $instance['access_token'] ); ?>" />
		</p>

		<p class="avada_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'access_token_secret' ) ); ?>"><?php esc_attr_e( 'Access Token Secret:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'access_token_secret' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'access_token_secret' ) ); ?>" value="<?php echo esc_attr( $instance['access_token_secret'] ); ?>" />
		</p>

		<p  class="general_option">
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_attr_e( 'Number of Tweet (max. 20 tweets possible):', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" />
		</p>

		<p class="avada_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>"><?php esc_attr_e( 'Icon Color (leave empty for default icon color):', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>" value="<?php echo esc_attr( $instance['icon_color'] ); ?>" />
		</p>

		<p class="twitter_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_attr_e( 'Max. Width (has to be between 180 and 520):', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" value="<?php echo esc_attr( $instance['width'] ); ?>" />
		</p>

		<p class="twitter_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_attr_e( 'Height (min. is 200):', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" value="<?php echo esc_attr( $instance['height'] ); ?>" />
		</p>

		<p class="twitter_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>"><?php esc_attr_e( 'Color Scheme:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'theme' ) ); ?>" class="widefat" style="width:100%;">
				<option value="light" <?php echo ( 'light' == $instance['theme'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Light', 'Avada' ); ?></option>
				<option value="dark" <?php echo ( 'dark' == $instance['theme'] ) ? 'selected="selected"' : ''; ?>><?php esc_attr_e( 'Dark', 'Avada' ); ?></option>
			</select>
		</p>

		<p class="twitter_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_color' ) ); ?>"><?php esc_attr_e( 'Link Color (leave empty for Theme default link color):', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'link_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_color' ) ); ?>" value="<?php echo esc_attr( $instance['link_color'] ); ?>" />
		</p>

		<p class="twitter_style">
			<label for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_attr_e( 'Border Color:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" value="<?php echo esc_attr( $instance['border_color'] ); ?>" />
		</p>

		<p class="twitter_style">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_header'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_header' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_header' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_header' ) ); ?>"><?php esc_attr_e( 'Show Header', 'Avada' ); ?></label>
		</p>

		<p class="twitter_style">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_footer'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_footer' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_footer' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_footer' ) ); ?>"><?php esc_attr_e( 'Show Footer', 'Avada' ); ?></label>
		</p>

		<p class="twitter_style">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_borders'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_borders' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_borders' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_borders' ) ); ?>"><?php esc_attr_e( 'Show Borders', 'Avada' ); ?></label>
		</p>

		<p class="twitter_style">
			<input class="checkbox" type="checkbox" <?php checked( $instance['transparent_bg'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'transparent_bg' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'transparent_bg' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'transparent_bg' ) ); ?>"><?php esc_attr_e( 'Transparent Background', 'Avada' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Renders the new twitter widget directly from twitter.
	 *
	 * @param array $widget_params The needed twitter API parameters to render the widget.
	 * @return void
	 */
	public function render_new_widget( $widget_params ) {
		?>
		<?php extract( $widget_params ); ?>
		<div style="overflow:hidden">
			<?php if ( 'twitter_style' == $widget_type ) : ?>
				<a class="twitter-timeline" data-dnt="true" href="<?php echo esc_url_raw( 'https://twitter.com/' . $twitter_id ); ?>" data-tweet-limit="<?php echo esc_attr( $count ); ?>" data-width="<?php echo esc_attr( $width ); ?>" data-height="<?php echo esc_attr( $height ); ?>" width="<?php echo esc_attr( $width ); ?>" height="<?php echo esc_attr( $height ); ?>" data-theme="<?php echo esc_attr( $theme ); ?>" data-link-color="<?php echo esc_attr( $link_color ); ?>" data-border-color="<?php echo esc_attr( $border_color ); ?>" data-chrome="<?php echo esc_attr( $chrome ); ?>">Tweets by <?php echo esc_attr( $twitter_id ); ?></a>
			<?php else : ?>
				<a class="twitter-timeline" data-dnt="true" href="<?php echo esc_url_raw( 'https://twitter.com/' . $twitter_id ); ?>" data-widget-id="<?php echo esc_attr( $widget_id ); ?>"><?php printf( esc_attr__( 'Tweets by %s', 'Avada' ), esc_attr( $twitter_id ) ); ?></a>
			<?php endif; ?>
			<?php // @codingStandardsIgnoreLine ?>
			<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
		</div>
		<?php
	}

	/**
	 * Returns the correct flag string for twitter chrome params.
	 *
	 * @param array $instance     The twitter widget's instance parameters.
	 * @return string             The flag string for the twitter widget js API.
	 */
	public function get_chrome( $instance ) {
		$chrome = '';

		$instance['show_header']    = isset( $instance['show_header'] ) ? $instance['show_header'] : 1;
		$instance['show_footer']    = isset( $instance['show_footer'] ) ? $instance['show_footer'] : 1;
		$instance['show_borders']   = isset( $instance['show_borders'] ) ? $instance['show_borders'] : 1;
		$instance['transparent_bg'] = isset( $instance['transparent_bg'] ) ? $instance['transparent_bg'] : 0;

		if ( 'on' != $instance['show_header'] ) {
			$chrome .= 'noheader ';
		}

		if ( 'on' != $instance['show_footer'] ) {
			$chrome .= 'nofooter ';
		}

		if ( 'on' != $instance['show_borders'] ) {
			$chrome .= 'noborders ';
		}

		if ( $instance['transparent_bg'] && 'on' == $instance['transparent_bg'] ) {
			$chrome .= 'transparent ';
		}

		return rtrim( $chrome );
	}

	/**
	 * Renders the old twitter widget using the REST API v1.1.
	 *
	 * @param array $widget_params The needed twitter API parameters to render the widget.
	 * @return void
	 */
	public function render_old_widget( $widget_params ) {

		extract( $widget_params );

		$tweets_body = get_site_transient( $consumer_key );

		if ( false === $tweets_body ) {
			$token = get_option( 'cfTwitterToken_' . $widget_id );
			// Get a new token anyways.
			delete_option( 'cfTwitterToken_' . $widget_id );

			// Getting new auth bearer only if we don't have one.
			if ( ! $token ) {

				// Preparing credentials.
				$credentials = $consumer_key . ':' . $consumer_secret;
				$to_send     = base64_encode( $credentials );

				// Http post arguments.
				$args = array(
					'method'      => 'POST',
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'Authorization' => 'Basic ' . $to_send,
						'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
					),
					'body' => array(
						'grant_type' => 'client_credentials',
					),
				);

				add_filter( 'https_ssl_verify', '__return_false' );
				$response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );

				$keys = json_decode( wp_remote_retrieve_body( $response ) );

				if ( $keys && isset( $keys->access_token ) ) {

					// Saving token to wp_options table.
					update_option( 'cfTwitterToken_' . $widget_id, $keys->access_token );
					$token = $keys->access_token;
				}
			}

			// We have bearer token wether we obtained it from API or from options.
			$args = array(
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => array(
					'Authorization' => "Bearer $token",
				),
			);

			add_filter( 'https_ssl_verify', '__return_false' );
			$api_url  = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitter_id . '&count=' . $count;
			$response = wp_remote_get( $api_url, $args );
			$tweets_body = wp_remote_retrieve_body( $response );

			set_site_transient( $consumer_key, $tweets_body, 960 );

		} // End if().

		$tweets = false;
		if ( ! empty( $tweets_body ) ) {
			$tweets = json_decode( $tweets_body, true );
			if ( ! is_array( $tweets ) ) {
				$tweets = false;
			}
		}

		if ( $tweets && is_array( $tweets ) ) {

			if ( '' !== $icon_color ) {
				echo '<style type="text/css">';
				echo '.fusion-content-widget-area #' . esc_attr( $widget_id ) . ' .jtwt .jtwt_tweet:before, ';
				echo '.fusion-footer-widget-area #' . esc_attr( $widget_id ) . ' .jtwt .jtwt_tweet:before {';
				echo 'color: ' . esc_attr( Fusion_Sanitize::color( $icon_color ) );
				echo '}';
				echo '</style>';
			}

			?>
			<div class="twitter-box">
				<div class="twitter-holder">
					<div class="b">
						<div class="tweets-container" id="tweets_<?php echo esc_attr( $widget_id ); ?>">
							<ul class="jtwt">
								<?php foreach ( $tweets as $tweet ) : ?>
									<li class="jtwt_tweet">
										<p class="jtwt_tweet_text">
											<?php $latest_tweet = $this->tweet_get_html( $tweet ); ?>
											<?php if ( ! $latest_tweet ) : ?>
												<?php continue; ?>
											<?php endif; ?>
											<?php echo $latest_tweet; // WPCS: XSS ok. ?>
										</p>
										<?php $twitter_time = strtotime( $tweet['created_at'] ); ?>
										<?php $time_ago     = $this->ago( $twitter_time ); ?>
										<a href="<?php echo esc_url_raw( 'http://twitter.com/' . $tweet['user']['screen_name'] . '/statuses/' . $tweet['id_str'] ); ?>" class="jtwt_date"><?php echo esc_attr( $time_ago ); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<span class="arrow"></span>
			</div>
		<?php
		} // End if().
	}

	/**
	 * Converts the tweet text into a HTML formatted string, incl. links for URLs, Hashtags and users.
	 *
	 * @param string  $tweet    The tweet text.
	 * @param boolean $links    Flag if link tags for URLs should be added.
	 * @param boolean $users    Flag if link tags for users should be added.
	 * @param boolean $hashtags Flag if link tags for hashtags should be added.
	 * @param boolean $media    Flag if link tags for media should be added.
	 *
	 * @return string|false     The HTML formatted tweet text.
	 */
	public function tweet_get_html( $tweet, $links = true, $users = true, $hashtags = true, $media = true ) {

		if ( array_key_exists( 'retweeted_status', $tweet ) ) {
			$tweet = $tweet['retweeted_status'];
		}

		if ( ! isset( $tweet['text'] ) ) {
			return false;
		}

		$return = $tweet['text'];

		$entities = array();
		$temp = array();

		if ( $links && is_array( $tweet['entities']['urls'] ) ) {

			foreach ( $tweet['entities']['urls'] as $e ) {
				$temp['start']       = $e['indices'][0];
				$temp['end']         = $e['indices'][1];
				$temp['replacement'] = '<a href="' . $e['expanded_url'] . '" target="_blank" rel="noopener noreferrer">' . $e['display_url'] . '</a>';
				$entities[]          = $temp;
			}
		}

		if ( $users && is_array( $tweet['entities']['user_mentions'] ) ) {

			foreach ( $tweet['entities']['user_mentions'] as $e ) {
				$temp['start']       = $e['indices'][0];
				$temp['end']         = $e['indices'][1];
				$temp['replacement'] = '<a href="https://twitter.com/' . $e['screen_name'] . '" target="_blank" rel="noopener noreferrer">@' . $e['screen_name'] . '</a>';
				$entities[]          = $temp;
			}
		}

		if ( $hashtags && is_array( $tweet['entities']['hashtags'] ) ) {

			foreach ( $tweet['entities']['hashtags'] as $e ) {
				$temp['start']       = $e['indices'][0];
				$temp['end']         = $e['indices'][1];
				$temp['replacement'] = '<a href="https://twitter.com/hashtag/' . $e['text'] . '?src=hash" target="_blank" rel="noopener noreferrer">#' . $e['text'] . '</a>';
				$entities[]          = $temp;
			}
		}

		if ( $media && array_key_exists( 'media', $tweet['entities'] ) ) {

			foreach ( $tweet['entities']['media'] as $e ) {
				$temp['start']       = $e['indices'][0];
				$temp['end']         = $e['indices'][1];
				$temp['replacement'] = '<a href="' . $e['url'] . '" target="_blank" rel="noopener noreferrer">' . $e['display_url'] . '</a>';
				$entities[]          = $temp;
			}
		}

		usort( $entities, array( $this, 'sort_tweets' ) );

		foreach ( $entities as $item ) {
			$return = $this->mb_substr_replace( $return, $item['replacement'], $item['start'], $item['end'] - $item['start'] );
		}

		return $return;
	}

	/**
	 * The PHP substr_replace equivalent for multibyte encoded strings.
	 *
	 * @param string $string      The string in which replacement should take place.
	 * @param string $replacement The replacement string.
	 * @param int    $start       The index where the replacement should start.
	 * @param int    $length      The length of $string that should be replaced.
	 * @return array|string       The correctly replaced string. When the result is an array, it runs recursively.
	 */
	public function mb_substr_replace( $string, $replacement, $start, $length = null ) {
		if ( is_array( $string ) ) {
			$num = count( $string );
			// $replacement.
			$replacement = is_array( $replacement ) ? array_slice( $replacement, 0, $num ) : array_pad( array( $replacement ), $num, $replacement );

			// $start.
			if ( is_array( $start ) ) {
				$start = array_slice( $start, 0, $num );
				foreach ( $start as $key => $value ) {
					$start[ $key ] = is_int( $value ) ? $value : 0;
				}
			} else {
				$start = array_pad( array( $start ), $num, $start );
			}

			// $length.
			if ( ! isset( $length ) ) {
				$length = array_fill( 0, $num, 0 );
			} elseif ( is_array( $length ) ) {
				$length = array_slice( $length, 0, $num );
				foreach ( $length as $key => $value ) {
					$length[ $key ] = isset( $value ) ? ( is_int( $value ) ? $value : $num ) : 0;
				}
			} else {
				$length = array_pad( array( $length ), $num, $length );
			}

			// Recursive call.
			return array_map( __FUNCTION__, $string, $replacement, $start, $length );
		}

		preg_match_all( '/./us', (string) $string, $smatches );
		preg_match_all( '/./us', (string) $replacement, $rmatches );
		if ( null === $length ) {
			$length = mb_strlen( $string );
		}
		array_splice( $smatches[0], $start, $length, $rmatches[0] );

		return join( $smatches[0] );
	}

	/**
	 * Compare the start indices of two twitter entities.
	 *
	 * @param array $a A twitter entity.
	 * @param array $b A twitter entity.
	 * @return int The difference of the start indices.
	 */
	public function sort_tweets( $a, $b ) {
		return ( $b['start'] - $a['start'] );
	}

	/**
	 * Function to display the correct time format for each tweet.
	 *
	 * @param int $time A timestamp for the tweet publishing date.
	 * @return string The formatted date for the twwet's publishing date.
	 */
	public function ago( $time ) {
		return sprintf( _x( '%s ago', '%s = human-readable time difference', 'Avada' ), human_time_diff( $time, current_time( 'timestamp' ) ) );
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
