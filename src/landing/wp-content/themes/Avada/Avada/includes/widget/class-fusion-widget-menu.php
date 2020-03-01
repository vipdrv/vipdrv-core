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
class Fusion_Widget_Menu extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	function __construct() {

		$widget_ops  = array(
			'classname' => 'menu',
			'description' => '',
		);
		$control_ops = array(
			'id_base' => 'menu-widget',
		);
		parent::__construct( 'menu-widget', 'Avada: Horizontal Menu', $widget_ops, $control_ops );

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

		echo $before_widget; // WPCS: XSS ok.

		// Get menu.
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		echo '<style type="text/css">';

		echo '#' . esc_attr( $this->id ) . ' > .fusion-widget-menu ul{';
		echo 'text-align:' . esc_attr( strtolower( $instance['alignment'] ) ) . ';';
		echo '}';

		echo '#' . esc_attr( $this->id ) . ' > .fusion-widget-menu li{display: inline-block;}';
		echo '#' . esc_attr( $this->id ) . ' ul li a{';
		echo 'display: inline-block;padding:0;border:0;';
		echo 'color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_color'] ) ) . ';';
		echo 'font-size:' . esc_attr( Fusion_Sanitize::size( $instance['font_size'] ) ) . ';';
		echo '}';

		echo '#' . esc_attr( $this->id ) . ' ul li a:after{';
		echo 'content:\'' . esc_attr( $instance['sep_text'] ) . '\';';
		echo 'color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_color'] ) ) . ';';
		echo 'padding-right:' . esc_attr( Fusion_Sanitize::size( $instance['menu_padding'] ) ) . ';';
		echo 'padding-left:' . esc_attr( Fusion_Sanitize::size( $instance['menu_padding'] ) ) . ';';
		echo 'font-size:' . esc_attr( Fusion_Sanitize::size( $instance['font_size'] ) ) . ';';
		echo '}';

		echo '#' . esc_attr( $this->id ) . ' ul li a:hover, #' . esc_attr( $this->id ) . ' ul .menu-item.current-menu-item a {';
		echo 'color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_hover_color'] ) ) . ';';
		echo '}';

		echo '#' . esc_attr( $this->id ) . ' ul li:last-child a:after{display: none}';

		echo '#' . esc_attr( $this->id ) . ' ul li .fusion-widget-cart-number{';
		echo 'margin:0 7px;';
		echo 'background-color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_hover_color'] ) ) . ';';
		echo 'color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_color'] ) ) . ';';
		echo '}';

		echo '#' . esc_attr( $this->id ) . ' ul li.fusion-active-cart-icon .fusion-widget-cart-icon:after{';
		echo 'color:' . esc_attr( Fusion_Sanitize::color( $instance['menu_link_hover_color'] ) ) . ';';
		echo '}';

		echo '</style>';

		$nav_menu_args = array(
			'fallback_cb' 	  => '',
			'menu'        	  => $nav_menu,
			'depth'		  	  => -1,
			'container_class' => 'fusion-widget-menu',
			'container'       => 'nav',
			'item_spacing'    => 'discard',
		);

		wp_nav_menu( $nav_menu_args );

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
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['nav_menu']				= isset( $new_instance['nav_menu'] ) ? $new_instance['nav_menu'] : '';
		$instance['alignment']				= isset( $new_instance['alignment'] ) ? $new_instance['alignment'] : '';
		$instance['menu_padding']  			= isset( $new_instance['menu_padding'] ) ? $new_instance['menu_padding'] : '';
		$instance['menu_link_color']    	= isset( $new_instance['menu_link_color'] ) ? $new_instance['menu_link_color'] : '';
		$instance['menu_link_hover_color']  = isset( $new_instance['menu_link_hover_color'] ) ? $new_instance['menu_link_hover_color'] : '';
		$instance['sep_text']      			= isset( $new_instance['sep_text'] ) ? $new_instance['sep_text'] : '';
		$instance['font_size']      		= isset( $new_instance['font_size'] ) ? $new_instance['font_size'] : '';

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
			'nav_menu' 				=> '',
			'alignment'				=> 'Left',
			'menu_padding'  		=> '25px',
			'menu_link_color'    	=> '#ccc',
			'menu_link_hover_color' => '#fff',
			'sep_text'      		=> '|',
			'font_size'				=> '14px',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Get menus.
		$menus = wp_get_nav_menus();
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'nav_menu' ) ); ?>"><?php esc_attr_e( 'Select Menu:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'nav_menu' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'nav_menu' ) ); ?>" class="widefat" style="width:100%;">
				<option value="0"><?php esc_attr_e( '&mdash; Select &mdash;' ); ?></option>
				<?php foreach ( $menus as $menu ) : ?>
					<option value="<?php echo esc_attr( $menu->slug ); ?>" <?php selected( $nav_menu, $menu->slug ); ?>>
						<?php echo esc_html( $menu->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'alignment' ) ); ?>"><?php esc_attr_e( 'Alignment:', 'Avada' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'alignment' ) ); ?>" class="widefat" style="width:100%;">
				<option value="Left" <?php if ( 'Left' == $instance['alignment'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Left', 'Avada' ); ?></option>
				<option value="Center" <?php if ( 'Center' == $instance['alignment'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Center', 'Avada' ); ?></option>
				<option value="Right" <?php if ( 'Right' == $instance['alignment'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Right', 'Avada' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'menu_padding' ) ); ?>"><?php esc_attr_e( 'Menu Padding:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'menu_padding' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'menu_padding' ) ); ?>" value="<?php echo esc_attr( $instance['menu_padding'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'menu_link_color' ) ); ?>"><?php esc_attr_e( 'Menu Link Color:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'menu_Link_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'menu_link_color' ) ); ?>" value="<?php echo esc_attr( $instance['menu_link_color'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'menu_link_hover_color' ) ); ?>"><?php esc_attr_e( 'Menu Link Hover Color:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'menu_link_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'menu_link_hover_color' ) ); ?>" value="<?php echo esc_attr( $instance['menu_link_hover_color'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sep_text' ) ); ?>"><?php esc_attr_e( 'Separator Text:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'sep_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sep_text' ) ); ?>" value="<?php echo esc_attr( $instance['sep_text'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>"><?php esc_attr_e( 'Font Size:', 'Avada' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'font_size' ) ); ?>" value="<?php echo esc_attr( $instance['font_size'] ); ?>" />
		</p>
		<?php

	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
