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
class Fusion_Widget_Ad_125_125 extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	function __construct() {

		$widget_ops = array(
			'classname' => 'ad_125_125',
			'description' => 'Add 125x125 ads.',
		);
		$control_ops = array(
			'id_base' => 'ad_125_125-widget',
		);
		parent::__construct( 'ad_125_125-widget', 'Avada: 125x125 Ads', $widget_ops, $control_ops );

	}

	/**
	 * Echoes the widget content.
	 *
	 * @access public
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		extract( $args );
		?>

		<div class="img-row">
			<?php $ads = array( 1, 2, 3, 4 ); ?>
			<?php foreach ( $ads as $ad_count ) : ?>
				<?php if ( $instance[ 'ad_125_img_' . $ad_count ] && $instance[ 'ad_125_link_' . $ad_count ] ) : ?>
					<div class="img-holder">
						<span class="hold">
							<a href="<?php echo esc_url_raw( $instance[ 'ad_125_link_' . $ad_count ] ); ?>">
								<img src="<?php echo esc_url_raw( $instance[ 'ad_125_img_' . $ad_count ] ); ?>" alt="" width="123" height="123" />
							</a>
						</span>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
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
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['ad_125_img_1']  = isset( $new_instance['ad_125_img_1'] ) ? $new_instance['ad_125_img_1'] : '';
		$instance['ad_125_link_1'] = isset( $new_instance['ad_125_link_1'] ) ? $new_instance['ad_125_link_1'] : '';
		$instance['ad_125_img_2']  = isset( $new_instance['ad_125_img_2'] ) ? $new_instance['ad_125_img_2'] : '';
		$instance['ad_125_link_2'] = isset( $new_instance['ad_125_link_2'] ) ? $new_instance['ad_125_link_2'] : '';
		$instance['ad_125_img_3']  = isset( $new_instance['ad_125_img_3'] ) ? $new_instance['ad_125_img_3'] : '';
		$instance['ad_125_link_3'] = isset( $new_instance['ad_125_link_3'] ) ? $new_instance['ad_125_link_3'] : '';
		$instance['ad_125_img_4']  = isset( $new_instance['ad_125_img_4'] ) ? $new_instance['ad_125_img_4'] : '';
		$instance['ad_125_link_4'] = isset( $new_instance['ad_125_link_4'] ) ? $new_instance['ad_125_link_4'] : '';

		return $instance;

	}

	/**
	 * Outputs the settings update form.
	 *
	 * @access public
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {

		$defaults = array(
			'ad_125_img_1'	=> '',
			'ad_125_link_1'	=> '',
			'ad_125_img_2'	=> '',
			'ad_125_link_2'	=> '',
			'ad_125_img_3'	=> '',
			'ad_125_link_3' => '',
			'ad_125_img_4'	=> '',
			'ad_125_link_4'	=> '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<p><strong><?php printf( esc_attr__( 'Ad %s', 'Avada' ), '1' ); ?></strong></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_1' ) ); ?>"><?php esc_attr_e( 'Image Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_img_1' ) );?>" value="<?php echo esc_attr( $instance['ad_125_img_1'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_1' ) ); ?>"><?php esc_attr_e( 'Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_link_1' ) ); ?>" value="<?php echo esc_attr( $instance['ad_125_link_1'] ); ?>" />
		</p>
		<p><strong><?php printf( esc_attr__( 'Ad %s', 'Avada' ), '2' ); ?></strong></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_2' ) ); ?>"><?php esc_attr_e( 'Image Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_img_2' ) );?>" value="<?php echo esc_attr( $instance['ad_125_img_2'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_2' ) ); ?>"><?php esc_attr_e( 'Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_link_2' ) ); ?>" value="<?php echo esc_attr( $instance['ad_125_link_2'] ); ?>" />
		</p>
		<p><strong><?php printf( esc_attr__( 'Ad %s', 'Avada' ), '3' ); ?></strong></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_3' ) ); ?>"><?php esc_attr_e( 'Image Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_3' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_img_3' ) );?>" value="<?php echo esc_attr( $instance['ad_125_img_3'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_3' ) ); ?>"><?php esc_attr_e( 'Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_3' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_link_3' ) ); ?>" value="<?php echo esc_attr( $instance['ad_125_link_3'] ); ?>" />
		</p>
		<p><strong><?php printf( esc_attr__( 'Ad %s', 'Avada' ), '4' ); ?></strong></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_4' ) ); ?>"><?php esc_attr_e( 'Image Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_img_4' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_img_4' ) );?>" value="<?php echo esc_attr( $instance['ad_125_img_4'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_4' ) ); ?>"><?php esc_attr_e( 'Ad Link:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'ad_125_link_4' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ad_125_link_4' ) ); ?>" value="<?php echo esc_attr( $instance['ad_125_link_4'] ); ?>" />
		</p>
		<?php

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
