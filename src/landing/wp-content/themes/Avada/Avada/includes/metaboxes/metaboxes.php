<?php
/**
 * The metaboxes class.
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
 * The Metaboxes class.
 */
class PyreThemeFrameworkMetaboxes {

	/**
	 * The settings.
	 *
	 * @access public
	 * @var array
	 */
	public $data;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		$this->data = Avada()->settings->get_all();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 11 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script_loader' ), 99 );

	}

	/**
	 * Load backend scripts
	 */
	function admin_script_loader() {

		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && in_array( $screen->post_type, apply_filters( 'avada_hide_page_options', array() ) ) ) {
			return;
		}
		$theme_info = wp_get_theme();

		wp_enqueue_script(
			'jquery.biscuit',
			Avada::$template_dir_url . '/assets/admin/js/jquery.biscuit.js',
			array( 'jquery' ),
			$theme_info->get( 'Version' )
		);
		wp_register_script(
			'avada_upload',
			Avada::$template_dir_url . '/assets/admin/js/upload.js',
			array( 'jquery' ),
			$theme_info->get( 'Version' )
		);
		wp_enqueue_script( 'avada_upload' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-button' );

		// Select field assets.
		wp_dequeue_script( 'tribe-events-select2' );
		wp_enqueue_style(
			'select2-css',
			Avada::$template_dir_url . '/assets/admin/css/select2.css',
			array(),
			'4.0.3',
			'all'
		);
		wp_dequeue_script( 'yoast-seo-select2' );
		wp_deregister_script( 'yoast-seo-select2' );
		wp_dequeue_script( 'select2' );
		wp_enqueue_script(
			( class_exists( 'WPSEO_Admin_Asset_Manager' ) ) ? 'yoast-seo-select2' : 'select2-avada-js',
			Avada::$template_dir_url . '/assets/admin/js/select2.min.js',
			array( 'jquery' ),
			'4.0.3'
		);

		// Range field assets.
		wp_enqueue_style(
			'avadaredux-nouislider-css',
			FUSION_LIBRARY_URL . '/inc/redux/framework/FusionReduxCore/inc/fields/slider/vendor/nouislider/fusionredux.jquery.nouislider.css',
			array(),
			'5.0.0',
			'all'
		);

		wp_enqueue_script(
			'avadaredux-nouislider-js',
			Avada::$template_dir_url . '/assets/admin/js/jquery.nouislider.min.js',
			array( 'jquery' ),
			'5.0.0',
			true
		);
		wp_enqueue_script(
			'wnumb-js',
			Avada::$template_dir_url . '/assets/admin/js/wNumb.js',
			array( 'jquery' ),
			'1.0.2',
			true
		);

		// Color fields.
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script(
			'wp-color-picker-alpha',
			Avada::$template_dir_url . '/assets/admin/js/wp-color-picker-alpha.js',
			array( 'wp-color-picker' )
		);

		// General JS for fields.
		wp_enqueue_script(
			'avada-fusion-options', Avada::$template_dir_url . '/assets/admin/js/avada-fusion-options.js',
			array( 'jquery' ),
			$theme_info->get( 'Version' )
		);

	}

	/**
	 * Adds the metaboxes.
	 *
	 * @access public
	 */
	public function add_meta_boxes() {

		$post_types = get_post_types( array(
			'public' => true,
		) );

		$disallowed = array( 'page', 'post', 'attachment', 'avada_portfolio', 'themefusion_elastic', 'product', 'wpsc-product', 'slide', 'tribe_events' );

		$disallowed = array_merge( $disallowed, apply_filters( 'avada_hide_page_options', array() ) );
		foreach ( $post_types as $post_type ) {
			if ( in_array( $post_type, $disallowed ) ) {
				continue;
			}
			$this->add_meta_box( 'post_options', 'Avada Options', $post_type );
		}

		$this->add_meta_box( 'post_options', 'Fusion Page Options', 'avada_faq' );
		$this->add_meta_box( 'post_options', 'Fusion Page Options', 'post' );
		$this->add_meta_box( 'page_options', 'Fusion Page Options', 'page' );
		$this->add_meta_box( 'portfolio_options', 'Fusion Page Options', 'avada_portfolio' );
		$this->add_meta_box( 'es_options', 'Elastic Slide Options', 'themefusion_elastic' );
		$this->add_meta_box( 'woocommerce_options', 'Fusion Page Options', 'product' );
		$this->add_meta_box( 'slide_options', 'Slide Options', 'slide' );
		$this->add_meta_box( 'events_calendar_options', 'Events Calendar Options', 'tribe_events' );

	}

	/**
	 * Adds a metabox.
	 *
	 * @access public
	 * @param string $id        The metabox ID.
	 * @param string $label     The metabox label.
	 * @param string $post_type The post-type.
	 */
	public function add_meta_box( $id, $label, $post_type ) {
		add_meta_box( 'pyre_' . $id, $label, array( $this, $id ), $post_type, 'advanced', 'high' );
	}

	/**
	 * Saves the metaboxes.
	 *
	 * @access public
	 * @param string|int $post_id The post ID.
	 */
	public function save_meta_boxes( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// @codingStandardsIgnoreLine
		$fusion_meta = array_intersect_key( $_POST, array_flip( preg_grep( '/^pyre_/', array_keys( $_POST ) ) ) );
		foreach ( $fusion_meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

	}

	/**
	 * Handle rendering options for pages.
	 *
	 * @access public
	 */
	public function page_options() {
		$this->render_option_tabs( array( 'sliders', 'page', 'header', 'footer', 'sidebars', 'background', 'pagetitlebar' ) );
	}

	/**
	 * Handle rendering options for posts.
	 *
	 * @access public
	 */
	public function post_options() {
		$this->render_option_tabs( array( 'post', 'sliders', 'page', 'header', 'footer', 'sidebars', 'background', 'pagetitlebar' ) );
	}

	/**
	 * Handle rendering options for portfolios.
	 *
	 * @access public
	 */
	public function portfolio_options() {
		$this->render_option_tabs( array( 'portfolio_post', 'sliders', 'page', 'header', 'footer', 'sidebars', 'background', 'pagetitlebar' ) );
	}

	/**
	 * Handle rendering options for woocommerce.
	 *
	 * @access public
	 */
	public function woocommerce_options() {
		$this->render_option_tabs( array( 'page', 'header', 'footer', 'sidebars', 'sliders', 'background', 'pagetitlebar' ), 'product' );
	}

	/**
	 * Handle rendering options for ES.
	 *
	 * @access public
	 */
	public function es_options() {
		include 'options/options_es.php';
	}

	/**
	 * Handle rendering options for slides.
	 *
	 * @access public
	 */
	public function slide_options() {
		include 'options/options_slide.php';
	}

	/**
	 * Handle rendering options for events.
	 *
	 * @access public
	 */
	public function events_calendar_options() {
		$this->render_option_tabs( array( 'sliders', 'page', 'header', 'footer', 'sidebars', 'background', 'pagetitlebar' ) );
	}

	/**
	 * Handle rendering options.
	 *
	 * @access public
	 * @param array  $requested_tabs The requested tabs.
	 * @param string $post_type      The post-type.
	 */
	public function render_option_tabs( $requested_tabs, $post_type = 'default' ) {
		$screen = get_current_screen();

		$tabs_names = array(
			'sliders'        => esc_html__( 'Sliders', 'Avada' ),
			'page'           => esc_html__( 'Page', 'Avada' ),
			'post'           => ( 'avada_faq' === $screen->post_type ) ? esc_html__( 'FAQ', 'Avada' ) : esc_html__( 'Post', 'Avada' ),
			'header'         => esc_html__( 'Header', 'Avada' ),
			'footer'         => esc_html__( 'Footer', 'Avada' ),
			'sidebars'       => esc_html__( 'Sidebars', 'Avada' ),
			'background'     => esc_html__( 'Background', 'Avada' ),
			'pagetitlebar'   => esc_html__( 'Page Title Bar', 'Avada' ),
			'portfolio_post' => esc_html__( 'Portfolio', 'Avada' ),
			'product'        => esc_html__( 'Product', 'Avada' ),
		);
		?>

		<ul class="pyre_metabox_tabs">

			<?php foreach ( $requested_tabs as $key => $tab_name ) : ?>
				<?php $class_active = ( 0 === $key ) ? 'active' : ''; ?>
				<?php if ( 'page' == $tab_name && 'product' == $post_type ) : ?>
					<li class="<?php echo esc_attr( $class_active ); ?>"><a href="<?php echo esc_attr( $tab_name ); ?>"><?php echo esc_attr( $tabs_names[ $post_type ] ); ?></a></li>
				<?php else : ?>
					<li class="<?php echo esc_attr( $class_active ); ?>"><a href="<?php echo esc_attr( $tab_name ); ?>"><?php echo esc_attr( $tabs_names[ $tab_name ] ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>

		</ul>

		<div class="pyre_metabox">

			<?php foreach ( $requested_tabs as $key => $tab_name ) : ?>
				<div class="pyre_metabox_tab" id="pyre_tab_<?php echo esc_attr( $tab_name ); ?>">
					<?php require_once wp_normalize_path( dirname( __FILE__ ) . '/tabs/tab_' . $tab_name . '.php' ); ?>
				</div>
			<?php endforeach; ?>

		</div>
		<div class="clear"></div>
		<?php

	}

	/**
	 * Text controls.
	 *
	 * @access public
	 * @param string $id         The ID.
	 * @param string $label      The label.
	 * @param string $desc       The description.
	 * @param array  $dependency The dependencies array.
	 */
	public function text( $id, $label, $desc = '', $dependency = array() ) {
		global $post;
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $label ); ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field">
				<input type="text" id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, 'pyre_' . $id, true ) ); ?>" />
			</div>
		</div>
		<?php

	}

	/**
	 * Select controls.
	 *
	 * @access public
	 * @param string $id         The ID.
	 * @param string $label      The label.
	 * @param array  $options    The options array.
	 * @param string $desc       The description.
	 * @param array  $dependency The dependencies array.
	 */
	public function select( $id, $label, $options, $desc = '', $dependency = array() ) {
		global $post;
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $label ); ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field">
				<select id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>" style="width:100%">
					<?php foreach ( $options as $key => $option ) : ?>
						<?php $selected = ( get_post_meta( $post->ID, 'pyre_' . $id, true ) == $key ) ? 'selected="selected"' : ''; ?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php

	}

	/**
	 * Color picker field.
	 *
	 * @access public
	 * @since 5.0.0
	 * @param string  $id         ID of input field.
	 * @param string  $label      Label of field.
	 * @param string  $desc       Description of field.
	 * @param boolean $alpha      Whether or not to show alpha.
	 * @param array   $dependency The dependencies array.
	 * @param string  $default    Default value from TO.
	 */
	public function color( $id, $label, $desc = '', $alpha = false, $dependency = array(), $default = '' ) {
		global $post;
		$styling_class = ( $alpha ) ? 'colorpickeralpha' : 'colorpicker';

		if ( $default ) {
			if ( ! $alpha && ( 'transparent' === $default || ! is_string( $default ) ) ) {
				$default = '#ffffff';
			}
			$desc .= '  <span class="pyre-default-reset"><a href="#" id="default-' . $id . '" class="fusion-range-default fusion-hide-from-atts" type="radio" name="' . $id . '" value="" data-default="' . $default . '">' . esc_attr( 'Reset to default.', 'Avada' ) . '</a><span>' . esc_attr( 'Using default value.', 'Avada' ) . '</span></span>';
		}
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field avada-color <?php echo esc_attr( $styling_class ); ?>">
				<input id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>" class="fusion-builder-color-picker-hex color-picker" type="text" value="<?php echo esc_attr( get_post_meta( $post->ID, 'pyre_' . $id, true ) ) ?>" <?php if ( $alpha ) { ?> data-alpha="true" <?php } ?> <?php if ( $default ) { echo 'data-default="' . esc_attr( $default ) . '"';} ?>/>
			</div>
		</div>
		<?php

	}

	/**
	 * Range field.
	 *
	 * @since 5.0.0
	 * @param string           $id         ID of input field.
	 * @param string           $label      Label of field.
	 * @param string           $desc       The description.
	 * @param string|int|float $min        The minimum value.
	 * @param string|int|float $max        The maximum value.
	 * @param string|int|float $step       The steps value.
	 * @param string|int|float $default    The default value.
	 * @param string|int|float $value      The value.
	 * @param array            $dependency The dependencies array.
	 */
	public function range( $id, $label, $desc = '', $min, $max, $step, $default, $value, $dependency = array() ) {
		global $post;
		if ( isset( $default ) && '' !== $default ) {
			$desc .= '  <span class="pyre-default-reset"><a href="#" id="default-' . $id . '" class="fusion-range-default fusion-hide-from-atts" type="radio" name="' . $id . '" value="" data-default="' . $default . '">' . esc_attr( 'Reset to default.', 'Avada' ) . '</a><span>' . esc_attr( 'Using default value.', 'Avada' ) . '</span></span>';
		}
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $label ); ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field avada-range">
				<?php
					$default_status = ( ( $default ) ? 'fusion-with-default' : '' );
					$is_checked = ( '' == get_post_meta( $post->ID, 'pyre_' . $id, true ) );
					$regular_id = ( ( '' != get_post_meta( $post->ID, 'pyre_' . $id, true ) ) ? $id : 'slider' . $id );
					$display_value = ( ( '' == get_post_meta( $post->ID, 'pyre_' . $id, true ) ) ? $default : get_post_meta( $post->ID, 'pyre_' . $id, true ) );
				?>
				<input
					type="text"
					name="<?php echo esc_attr( $id ); ?>"
					id="<?php echo esc_attr( $regular_id ); ?>"
					value="<?php echo esc_attr( $display_value ); ?>"
					class="fusion-slider-input <?php echo esc_attr( $default_status ); ?> <?php if ( isset( $default ) && '' !== $default ) { echo 'fusion-hide-from-atts'; } ?>"
				/>
				<div
					class="fusion-slider-container"
					data-id="<?php echo esc_attr( $id ); ?>"
					data-min="<?php echo esc_attr( $min ); ?>"
					data-max="<?php echo esc_attr( $max ); ?>"
					data-step="<?php echo esc_attr( $step ); ?>">
				</div>
				<?php if ( isset( $default ) && '' !== $default ) { ?>
				<input type="hidden"
					   id="pyre_<?php echo esc_attr( $id ) ?>"
					   name="pyre_<?php echo esc_attr( $id ) ?>"
					   value="<?php echo esc_attr( get_post_meta( $post->ID, 'pyre_' . $id, true ) ); ?>"
					   class="fusion-hidden-value" />
				<?php } ?>

			</div>
		</div>
		<?php

	}

	/**
	 * Radio button set field.
	 *
	 * @since 5.0.0
	 * @param string $id         ID of input field.
	 * @param string $label      Label of field.
	 * @param array  $options    Options to select from.
	 * @param string $desc       Description of field.
	 * @param array  $dependency The dependencies array.
	 */
	public function radio_buttonset( $id, $label, $options, $desc = '', $dependency = array() ) {
		global $post;
		$options_reset = $options;
		reset( $options_reset );
		$value = ( '' == get_post_meta( $post->ID, 'pyre_' . $id, true )  ) ? key( $options_reset ) : get_post_meta( $post->ID, 'pyre_' . $id, true );
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field avada-buttonset">
				<div class="fusion-form-radio-button-set ui-buttonset">
					<input type="hidden" id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" class="button-set-value" />
					<?php foreach ( $options as $key => $option ) : ?>
						<?php $selected = ( $key == $value ) ? ' ui-state-active' : ''; ?>
						<a href="#" class="ui-button buttonset-item<?php echo esc_attr( $selected ); ?>" data-value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $option ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php

	}

	/**
	 * Dimensions field.
	 *
	 * @since 5.0.0
	 * @param array  $ids        IDs of input fields.
	 * @param string $label      Label of field.
	 * @param string $desc       Description of field.
	 * @param array  $dependency The dependencies array.
	 */
	public function dimension( $ids, $label, $desc = '', $dependency = array() ) {
		global $post;
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $ids[0] ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field avada-dimension">
				<?php foreach ( $ids as $field_id ) : ?>
					<?php
					$icon_class = 'fa fa-arrows-h';
					if ( false !== strpos( $field_id, 'height' ) ) {
						$icon_class = 'fa fa-arrows-v';
					}
					if ( false !== strpos( $field_id, 'top' ) ) {
						$icon_class = 'dashicons dashicons-arrow-up-alt';
					}
					if ( false !== strpos( $field_id, 'right' ) ) {
						$icon_class = 'dashicons dashicons-arrow-right-alt';
					}
					if ( false !== strpos( $field_id, 'bottom' ) ) {
						$icon_class = 'dashicons dashicons-arrow-down-alt';
					}
					if ( false !== strpos( $field_id, 'left' ) ) {
						$icon_class = 'dashicons dashicons-arrow-left-alt';
					}
					?>
					<div class="fusion-builder-dimension">
						<span class="add-on"><i class="<?php echo esc_attr( $icon_class ); ?>"></i></span>
						<input type="text" name="pyre_<?php echo esc_attr( $field_id ); ?>" id="pyre_<?php echo esc_attr( $field_id ); ?>"" value="<?php echo esc_attr( get_post_meta( $post->ID, 'pyre_' . $field_id, true ) ); ?>"" />
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php

	}

	/**
	 * Multiselect field.
	 *
	 * @param array  $id         IDs of input fields.
	 * @param string $label      Label of field.
	 * @param array  $options    The options to choose from.
	 * @param string $desc       Description of field.
	 * @param array  $dependency The dependencies array.
	 */
	public function multiple( $id, $label, $options, $desc = '', $dependency = array() ) {
		global $post;
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field">
				<select multiple="multiple" id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>[]">
					<?php foreach ( $options as $key => $option ) : ?>
						<?php $selected = ( is_array( get_post_meta( $post->ID, 'pyre_' . $id, true ) ) && in_array( $key, get_post_meta( $post->ID, 'pyre_' . $id, true ) ) ) ? 'selected="selected"' : ''; ?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $option ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php

	}

	/**
	 * Textarea field.
	 *
	 * @param array  $id         IDs of input fields.
	 * @param string $label      Label of field.
	 * @param string $desc       Description of field.
	 * @param string $default    The default value.
	 * @param array  $dependency The dependencies array.
	 */
	public function textarea( $id, $label, $desc = '', $default = '', $dependency = array() ) {
		global $post;
		$db_value = get_post_meta( $post->ID, 'pyre_' . $id, true );
		$value = ( metadata_exists( 'post', $post->ID, 'pyre_' . $id ) ) ? $db_value : $default;
		$rows = 10;
		if ( 'heading' === $id || 'caption' === $id ) {
			$rows = 5;
		} elseif ( 'page_title_custom_text' == $id || 'page_title_custom_subheader' == $id ) {
			$rows = 1;
		}
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field">
				<textarea cols="120" rows="<?php echo (int) $rows; ?>" id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
			</div>
		</div>
		<?php

	}

	/**
	 * Upload field.
	 *
	 * @param array  $id         IDs of input fields.
	 * @param string $label      Label of field.
	 * @param string $desc       Description of field.
	 * @param array  $dependency The dependencies array.
	 */
	public function upload( $id, $label, $desc = '', $dependency = array() ) {
		global $post;
		?>

		<div class="pyre_metabox_field">
			<?php // No need to sanitize this, we already know what's in here. ?>
			<?php echo $this->dependency( $dependency ); // WPCS: XSS ok. ?>
			<div class="pyre_desc">
				<label for="pyre_<?php echo esc_attr( $id ); ?>"><?php echo $label; // WPCS: XSS ok. ?></label>
				<?php if ( $desc ) : ?>
					<p><?php echo $desc; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
			<div class="pyre_field">
				<div class="pyre_upload">
					<input name="pyre_<?php echo esc_attr( $id ); ?>" class="upload_field" id="pyre_<?php echo esc_attr( $id ); ?>" type="text" value="<?php echo esc_attr( get_post_meta( $post->ID, 'pyre_' . $id, true ) ); ?>" />
					<input class="fusion_upload_button button" type="button" value="<?php esc_attr_e( 'Browse', 'Avada' ); ?>" />
				</div>
			</div>
		</div>
		<?php

	}
	/**
	 * Hidden input.
	 *
	 * @since 5.0.0
	 * @param string $id    id of input field.
	 * @param string $value value of input field.
	 */
	public function hidden( $id, $value ) {
		global $post;
		?>
		<input type="hidden" id="pyre_<?php echo esc_attr( $id ); ?>" name="pyre_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php

	}


	/**
	 * Dependency markup.
	 *
	 * @since 5.0.0
	 * @param array $dependency dependence options.
	 * @return string $data_dependence markup
	 */
	private function dependency( $dependency = array() ) {

		// Disable dependencies if 'dependencies_status' is set to 0.
		if ( '0' === Avada()->settings->get( 'dependencies_status' ) ) {
			return '';
		}

		$data_dependency = '';
		if ( 0 < count( $dependency ) ) {
			$data_dependency .= '<div class="avada-dependency">';
			foreach ( $dependency as $dependence ) {
				$data_dependency .= '<span class="hidden" data-value="' . $dependence['value'] . '" data-field="' . $dependence['field'] . '" data-comparison="' . $dependence['comparison'] . '"></span>';
			}
			$data_dependency .= '</div>';
		}
		return $data_dependency;
	}
}

global $pagenow;

if ( is_admin() && ( ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) || ! isset( $pagenow ) ) ) {
	$metaboxes = new PyreThemeFrameworkMetaboxes;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
