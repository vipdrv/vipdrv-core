<?php
/**
 * Fusion MegaMenu Functions
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Don't duplicate me!
if ( ! class_exists( 'Avada_Megamenu_Framework' ) ) {

	/**
	 * Main Avada_Megamenu_Framework Class
	 */
	class Avada_Megamenu_Framework {

		/**
		 * The theme info object.
		 *
		 * @static
		 * @access public
		 * @var object
		 */
		public static $theme_info;

		/**
		 * Array of objects.
		 *
		 * @static
		 * @access public
		 * @var mixed
		 */
		public static $_classes;

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {

			self::$theme_info = wp_get_theme();

			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_stylesheets' ) );

			do_action( 'fusion_init' );

			self::$_classes['menus'] = new Avada_Megamenu();

			// Add the first level menu style dropdown to the menu fields.
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_menu_button_fields' ), 10, 4 );

			// Add the mega menu custom fields to the menu fields.
			if ( Avada()->settings->get( 'disable_megamenu' ) ) {
				add_filter( 'avada_menu_options',  array( $this, 'add_megamenu_fields' ), 20, 4 );
			}
		}

		/**
		 * Register megamenu javascript assets.
		 *
		 * @since  3.4
		 * @access public
		 * @param string $hook The hook we're currently on.
		 * @return void
		 */
		public function register_scripts( $hook ) {
			if ( 'nav-menus.php' === $hook ) {

				// Scripts.
				wp_enqueue_media();
				wp_register_script( 'avada-megamenu', Avada::$template_dir_url . '/assets/admin/js/mega-menu.js', array(), self::$theme_info->get( 'Version' ) );
				wp_enqueue_script( 'avada-megamenu' );
			}
		}

		/**
		 * Enqueue megamenu stylesheets
		 *
		 * @since  3.4
		 * @access public
		 * @param string $hook The hook we're currently on.
		 * @return void
		 */
		public function register_stylesheets( $hook ) {
			if ( 'nav-menus.php' === $hook ) {
				wp_enqueue_style( 'avada-megamenu', Avada::$template_dir_url . '/assets/css/mega-menu.css', false, self::$theme_info->get( 'Version' ) );
			}
		}

		/**
		 * Adds the menu button fields.
		 *
		 * @access public
		 * @param string $item_id The ID of the menu item.
		 * @param object $item    The menu item object.
		 * @param int    $depth   The depth of the current item in the menu.
		 * @param array  $args    Menu arguments.
		 * @return void.
		 */
		public function add_menu_button_fields( $item_id, $item, $depth, $args ) {
			$name  = 'menu-item-fusion-megamenu-style';
			?>
			<div class="fusion-menu-options-container">
				<a class="button button-primary button-large fusion-menu-option-trigger" href="#"><?php esc_attr_e( 'Avada Options', 'Avada' ); ?></a>
				<div class="fusion_builder_modal_overlay" style="display:none"></div>
				<div id="fusion-menu-options-<?php echo esc_attr( $item_id ); ?>" class="fusion-options-holder fusion-builder-modal-settings-container" style="display:none">
					<div class="fusion-builder-modal-container fusion_builder_module_settings">
						<div class="fusion-builder-modal-top-container">
							<h2><?php esc_attr_e( 'Menu Options', 'Avada' ); ?></h2>
							<div class="fusion-builder-modal-close fusiona-plus2"></div>
						</div>
						<div class="fusion-builder-modal-bottom-container">
							<a href="#" class="fusion-builder-modal-save" ><span><?php esc_attr_e( 'Save', 'Avada' ); ?></span></a>
							<a href="#" class="fusion-builder-modal-close" ><span><?php esc_attr_e( 'Cancel', 'Avada' ); ?></span></a>
						</div>
						<div class="fusion-builder-main-settings fusion-builder-main-settings-full">
							<ul class="fusion-builder-module-settings">
								<li class="fusion-builder-option fusion-menu-style">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Menu First Level Style', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Select to use normal text (default) for the parent level menu item, or a button. Button styles are controlled in theme options > fusion builder elements.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container">
										<select id="<?php echo esc_attr( $name . '-' . $item_id ); ?>" class="widefat edit-menu-item-target" name="<?php echo esc_attr( $name ) . '[' . esc_attr( $item_id ) . ']'; ?>">
											<option value="" <?php selected( $item->fusion_menu_style, '' ); ?>><?php esc_attr_e( 'Default Style', 'Avada' ); ?></option>
											<option value="fusion-button-small" <?php selected( $item->fusion_menu_style, 'fusion-button-small' ); ?>><?php esc_attr_e( 'Button Small', 'Avada' ); ?></option>
											<option value="fusion-button-medium" <?php selected( $item->fusion_menu_style, 'fusion-button-medium' ); ?>><?php esc_attr_e( 'Button Medium', 'Avada' ); ?></option>
											<option value="fusion-button-large" <?php selected( $item->fusion_menu_style, 'fusion-button-large' ); ?>><?php esc_attr_e( 'Button Large', 'Avada' ); ?></option>
											<option value="fusion-button-xlarge" <?php selected( $item->fusion_menu_style, 'fusion-button-xlarge' ); ?>><?php esc_attr_e( 'Button xLarge', 'Avada' ); ?></option>
										</select>
									</div>
								</li>
								<li class="fusion-builder-option">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Icon Select', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Select an icon for your menu item. Icon styles can be controlled in theme options > main menu > main menu icons.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container fusion-iconpicker">
										<input type="text" class="fusion-icon-search" placeholder="Search Icons" />
										<div class="icon_select_container"></div>
										<input type="hidden" id="edit-menu-item-megamenu-icon-<?php echo esc_attr( $item_id ); ?>" class="fusion-iconpicker-input" name="menu-item-fusion-megamenu-icon[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_icon ); ?>" />
									</div>
								</li>
								<li class="fusion-builder-option fusion-menu-style">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Icon/Thumbnail Only', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Turn on to only show the icon/image thumbnail while hiding the menu text. Important: this does not apply to the mobile menu.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container">
										<div class="fusion-form-radio-button-set ui-buttonset edit-menu-item-fusion-menu-icononly-<?php echo esc_attr( $item_id ); ?>">
											<input type="hidden" id="edit-menu-item-fusion-menu-icononly-<?php echo esc_attr( $item_id ); ?>" name="menu-item-fusion-megamenu-icononly[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_menu_icononly ); ?>" class="button-set-value" />
												<a href="#" class="ui-button buttonset-item<?php if ( 'icononly' === $item->fusion_menu_icononly ) { echo ' ui-state-active'; } ?>" data-value="icononly"><?php esc_attr_e( 'On', 'Avada' ); ?></a>
												<a href="#" class="ui-button buttonset-item<?php if ( 'icononly' !== $item->fusion_menu_icononly ) { echo ' ui-state-active'; } ?>" data-value="off"><?php esc_attr_e( 'Off', 'Avada' ); ?></a>
										</div>
									</div>
								</li>
								<li class="fusion-builder-option field-menu-highlight-label">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Menu Highlight Label', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Set the highlight label for menu item.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container">
										<input type="text" id="edit-menu-item-megamenu-highlight-label-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-highlight-label" name="menu-item-fusion-megamenu-highlight-label[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_highlight_label ); ?>" />
									</div>
								</li>
								<li class="fusion-builder-option field-menu-highlight-background">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Menu Highlight Label Background Color', 'Avada' ); ?></h3>
										<p class="description"><?php printf( esc_attr__( 'Set the highlight label background color. To set a border radius, visit %s and modify the Menu Highlight Label Radius option.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'themes.php?page=avada_options#main_nav_highlight_radius' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_attr__( 'Theme Options', 'Avada' ) . '</a>' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container pyre_field avada-color colorpickeralpha">
										<input type="text" id="edit-menu-item-megamenu-highlight-label-background-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-highlight-label-background fusion-builder-color-picker-hex color-picker" data-alpha="true" name="menu-item-fusion-megamenu-highlight-label-background[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_highlight_label_background ); ?>" />
									</div>
								</li>
								<li class="fusion-builder-option field-menu-highlight-color">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Menu Highlight Label Text Color', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Set the highlight label text color.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container pyre_field avada-color colorpicker">
										<input type="text" id="edit-menu-item-megamenu-highlight-label-color-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-highlight-label-color fusion-builder-color-picker-hex color-picker" name="menu-item-fusion-megamenu-highlight-label-color[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_highlight_label_color ); ?>" />
									</div>
								</li>
								<li class="fusion-builder-option field-menu-highlight-border-color">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Menu Highlight Label Border Color', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Set the highlight label border color.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container pyre_field avada-color colorpicker">
										<input type="text" id="edit-menu-item-megamenu-highlight-label-border-color-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-highlight-label-border-color fusion-builder-color-picker-hex color-picker" name="menu-item-fusion-megamenu-highlight-label-border-color[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_highlight_label_border_color ); ?>" />
									</div>
								</li>
								<li class="fusion-builder-option">
									<div class="option-details">
										<h3><?php esc_attr_e( 'Modal Window Anchor', 'Avada' ); ?></h3>
										<p class="description"><?php esc_attr_e( 'Add the class name of the modal window you want to open on menu item click.', 'Avada' ); ?></p>
									</div>
									<div class="option-field fusion-builder-option-container fusion-iconpicker">
										<input type="text" id="edit-menu-item-megamenu-modal-<?php echo esc_attr( $item_id ); ?>" class="fusion-modal-anchor" name="menu-item-fusion-megamenu-modal[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_modal ); ?>" />
									</div>
								</li>
								<?php apply_filters( 'avada_menu_options', $item_id, $item, $depth, $args ); ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php }

		/**
		 * Adds the menu markup.
		 *
		 * @access public
		 * @param string $item_id The ID of the menu item.
		 * @param object $item    The menu item object.
		 * @param int    $depth   The depth of the current item in the menu.
		 * @param array  $args    Menu arguments.
		 * @return void.
		 */
		public function add_megamenu_fields( $item_id, $item, $depth, $args ) {
			?>
			<li class="fusion-builder-option field-megamenu-status">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Fusion Mega Menu', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Turn on to enable the mega menu.  Note this will only work for the main menu.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<div class="fusion-form-radio-button-set ui-buttonset edit-menu-item-megamenu-status">
						<input type="hidden" id="edit-menu-item-megamenu-status-<?php echo esc_attr( $item_id ); ?>" name="menu-item-fusion-megamenu-status[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_status ); ?>" class="button-set-value" />
						<a href="#" class="ui-button buttonset-item<?php if ( 'enabled' === $item->fusion_megamenu_status ) { echo ' ui-state-active'; } ?>" data-value="enabled"><?php esc_attr_e( 'On', 'Avada' ); ?></a>
						<a href="#" class="ui-button buttonset-item<?php if ( 'enabled' !== $item->fusion_megamenu_status ) { echo ' ui-state-active'; } ?>" data-value="off"><?php esc_attr_e( 'Off', 'Avada' ); ?></a>
					</div>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-width">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Full Width Mega Menu', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Turn on to have the mega menu full width, which is taken from the site width option in theme options. Note this overrides the column width option.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<div class="fusion-form-radio-button-set ui-buttonset edit-menu-item-megamenu-width">
						<input type="hidden" id="edit-menu-item-megamenu-width-<?php echo esc_attr( $item_id ); ?>" name="menu-item-fusion-megamenu-width[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_width ); ?>" class="button-set-value" />
						<a href="#" class="ui-button buttonset-item<?php if ( 'fullwidth' === $item->fusion_megamenu_width ) { echo ' ui-state-active'; } ?>" data-value="fullwidth"><?php esc_attr_e( 'On', 'Avada' ); ?></a>
						<a href="#" class="ui-button buttonset-item<?php if ( 'fullwidth' !== $item->fusion_megamenu_width ) { echo ' ui-state-active'; } ?>" data-value="off"><?php esc_attr_e( 'Off', 'Avada' ); ?></a>
					</div>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-columns">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Number of Columns', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Select the number of columns you want to use.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<select id="edit-menu-item-megamenu-columns-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-megamenu-columns" name="menu-item-fusion-megamenu-columns[<?php echo esc_attr( $item_id ); ?>]">
						<option value="auto" <?php selected( $item->fusion_megamenu_columns, 'auto' ); ?>><?php esc_attr_e( 'Auto', 'Avada' ); ?></option>
						<option value="1" <?php selected( $item->fusion_megamenu_columns, '1' ); ?>>1</option>
						<option value="2" <?php selected( $item->fusion_megamenu_columns, '2' ); ?>>2</option>
						<option value="3" <?php selected( $item->fusion_megamenu_columns, '3' ); ?>>3</option>
						<option value="4" <?php selected( $item->fusion_megamenu_columns, '4' ); ?>>4</option>
						<option value="5" <?php selected( $item->fusion_megamenu_columns, '5' ); ?>>5</option>
						<option value="6" <?php selected( $item->fusion_megamenu_columns, '6' ); ?>>6</option>
					</select>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-columnwidth">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Column Width', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Set the width of the column. In percentage, ex 60%.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<input type="text" id="edit-menu-item-megamenu-columnwidth-<?php echo esc_attr( $item_id ); ?>" class="edit-menu-item-megamenu-columnwidth" name="menu-item-fusion-megamenu-columnwidth[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_columnwidth ); ?>" />
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-title">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Column Title', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Turn on to display item as linked column title. Turn off to display item as normal mega menu entry.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<div class="fusion-form-radio-button-set ui-buttonset edit-menu-item-megamenu-title">
						<input type="hidden" id="edit-menu-item-megamenu-title-<?php echo esc_attr( $item_id ); ?>" name="menu-item-fusion-megamenu-title[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_title ); ?>" class="button-set-value" />
						<a href="#" class="ui-button buttonset-item<?php if ( 'disabled' !== $item->fusion_megamenu_title ) { echo ' ui-state-active'; } ?>" data-value="enabled"><?php esc_attr_e( 'On', 'Avada' ); ?></a>
						<a href="#" class="ui-button buttonset-item<?php if ( 'disabled' === $item->fusion_megamenu_title ) { echo ' ui-state-active'; } ?>" data-value="disabled"><?php esc_attr_e( 'Off', 'Avada' ); ?></a>
					</div>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-widgetarea">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Widget Area', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Select a widget area to be used as the content for the column.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<select id="edit-menu-item-megamenu-widgetarea-<?php echo esc_attr( $item_id ); ?>" class="widefat code edit-menu-item-megamenu-widgetarea" name="menu-item-fusion-megamenu-widgetarea[<?php echo esc_attr( $item_id ); ?>]">
						<option value="0"><?php esc_attr_e( 'Select Widget Area', 'Avada' ); ?></option>
						<?php global $wp_registered_sidebars; ?>
						<?php if ( ! empty( $wp_registered_sidebars ) && is_array( $wp_registered_sidebars ) ) : ?>
							<?php foreach ( $wp_registered_sidebars as $sidebar ) : ?>
								<option value="<?php echo esc_attr( $sidebar['id'] ); ?>" <?php selected( $item->fusion_megamenu_widgetarea, $sidebar['id'] ); ?>><?php echo esc_attr( $sidebar['name'] ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-thumbnail">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Thumbnail', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Select an image to use as a thumbnail for the menu item. The size of the thumbnail can be controlled in theme options > main menu > main menu icons.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<div class="fusion-upload-image <?php if ( isset( $item->fusion_megamenu_thumbnail ) && '' !== $item->fusion_megamenu_thumbnail ) { echo 'fusion-image-set' ; } ?>">
						<input type="hidden" id="edit-menu-item-megamenu-thumbnail-<?php echo esc_attr( $item_id ); ?>" class="regular-text fusion-builder-upload-field" name="menu-item-fusion-megamenu-thumbnail[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_thumbnail ); ?>" />
						<div class="fusion-builder-upload-preview">
							<img src="<?php echo esc_attr( $item->fusion_megamenu_thumbnail ); ?>" id="fusion-media-img-thumbnail-<?php echo esc_attr( $item_id ); ?>" class="fusion-megamenu-thumbnail-image" style="<?php echo ( trim( $item->fusion_megamenu_thumbnail ) ) ? 'display:inline;' : ''; ?>" />
						</div>
						<input type='button' data-id="thumbnail-<?php echo esc_attr( $item_id ); ?>" class='button-upload fusion-builder-upload-button avada-edit-button' data-type="image" value="<?php esc_attr_e( 'Edit', 'Avada' ); ?>" />
						<input type="button" data-id="thumbnail-<?php echo esc_attr( $item_id ); ?>" class="upload-image-remove avada-remove-button" value="<?php esc_attr_e( 'Remove', 'Avada' ); ?>"  />
						<input type='button' data-id="thumbnail-<?php echo esc_attr( $item_id ); ?>" class='button-upload fusion-builder-upload-button avada-upload-button' data-type="image" value="<?php esc_attr_e( 'Upload Image', 'Avada' ); ?>" />
					</div>
				</div>
			</li>
			<li class="fusion-builder-option field-megamenu-background-image">
				<div class="option-details">
					<h3><?php esc_attr_e( 'Mega Menu Background Image', 'Avada' ); ?></h3>
					<p class="description"><?php esc_attr_e( 'Select an image for the mega menu background. If left empty, the Main Menu Dropdown Background Color will be used. Each mega menu column can have its own background image, or you can have one image that spreads across the entire mega menu width.', 'Avada' ); ?></p>
				</div>
				<div class="option-field fusion-builder-option-container">
					<div class="fusion-upload-image <?php if ( isset( $item->fusion_megamenu_background_image ) && '' !== $item->fusion_megamenu_background_image ) { echo 'fusion-image-set' ; } ?>">
						<input type="hidden" id="edit-menu-item-megamenu-background-image-<?php echo esc_attr( $item_id ); ?>" class="regular-text fusion-builder-upload-field" name="menu-item-fusion-megamenu-background-image[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->fusion_megamenu_background_image ); ?>" />
						<div class="fusion-builder-upload-preview">
							<img src="<?php echo esc_attr( $item->fusion_megamenu_background_image ); ?>" id="fusion-media-img-background-image-<?php echo esc_attr( $item_id ); ?>" class="fusion-megamenu-background-image" style="<?php echo ( trim( $item->fusion_megamenu_background_image ) ) ? 'display:inline;' : ''; ?>" />
						</div>
						<input type='button' data-id="background-image-<?php echo esc_attr( $item_id ); ?>" class='button-upload fusion-builder-upload-button avada-edit-button' data-type="image" value="<?php esc_attr_e( 'Edit', 'Avada' ); ?>" />
						<input type="button" data-id="background-image-<?php echo esc_attr( $item_id ); ?>" class="upload-image-remove avada-remove-button" value="<?php esc_attr_e( 'Remove', 'Avada' ); ?>"  />
						<input type='button' data-id="background-image-<?php echo esc_attr( $item_id ); ?>" class='button-upload fusion-builder-upload-button avada-upload-button' data-type="image" value="<?php esc_attr_e( 'Upload Image', 'Avada' ); ?>" />
					</div>
				</div>
			</li>
			<?php
		}

		/* Omit closing PHP tag to avoid "Headers already sent" issues. */




	}
} // End if().

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
