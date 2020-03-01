<?php
/**
 * Avada Options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Color settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_colors( $sections ) {

	$sections['colors'] = array(
		'label'    => esc_html__( 'Colors', 'Avada' ),
		'id'       => 'colors_section',
		'priority' => 3,
		'icon'     => 'el-icon-brush',
		'fields'   => array(
			'colors_important_note_info' => array(
				'label'       => '',
				'description' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> This tab contains general color options. Additional color options for specific areas, can be found within other tabs. Example: For menu color options go to the menu tab.', 'Avada' ) . '</div>',
				'id'          => 'colors_important_note_info',
				'type'        => 'custom',
			),
			'scheme_type' => array(
				'label'       => esc_html__( 'Predefined Theme Skin', 'Avada' ),
				'description' => esc_html__( 'Controls the main theme skin to be light or dark. Select a skin and all color options will change to the defined selection. Please note that individual pages have containers and Fusion Page Options that can override this setting, therefor you may not fully see the changes. If you change to light and a page is still dark, edit the page and look at each container background settings, along with Fusion Page Options.', 'Avada' ),
				'id'          => 'scheme_type',
				'default'     => '',
				'type'        => 'preset',
				'choices'     => array(
					'Light' => array(
						'label'    => esc_html__( 'Light', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/ffffff.png',
						'settings' => Fusion_Data::color_theme( 'light' ),
					),
					'Dark' => array(
						'label'    => esc_html__( 'Dark', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/29292a.png',
						'settings' => Fusion_Data::color_theme( 'dark' ),
					),
				),
			),
			'color_scheme' => array(
				'label'       => esc_html__( 'Predefined Color Scheme', 'Avada' ),
				'description' => esc_html__( 'Controls the main color scheme throughout the theme. Select a scheme and all the color options will change to the defined selection. Click the Save button to save your own current custom color scheme. Click the Import button to import a custom scheme. To delete or export a scheme, you must first have a custom scheme saved.', 'Avada' ),
				'id'          => 'color_scheme',
				'default'     => 'Green',
				'type'        => 'preset',
				'choices'     => array(
					'Red'        => array(
						'label'    => esc_html__( 'Red', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/e10707.png',
						'settings' => Fusion_Data::color_theme( 'red' ),
					),
					'Light Red'  => array(
						'label'    => esc_html__( 'Light Red', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/f05858.png',
						'settings' => Fusion_Data::color_theme( 'lightred' ),
					),
					'Blue'       => array(
						'label'    => esc_html__( 'Blue', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/1a80b6.png',
						'settings' => Fusion_Data::color_theme( 'blue' ),
					),
					'Light Blue' => array(
						'label'    => esc_html__( 'Light Blue', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/67b7e1.png',
						'settings' => Fusion_Data::color_theme( 'lightblue' ),
					),
					'Green'      => array(
						'label'    => esc_html__( 'Green', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/a0ce4e.png',
						'settings' => Fusion_Data::color_theme( 'green' ),
					),
					'Dark Green' => array(
						'label'    => esc_html__( 'Dark Green', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/9db668.png',
						'settings' => Fusion_Data::color_theme( 'darkgreen' ),
					),
					'Orange'     => array(
						'label'    => esc_html__( 'Orange', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/e9a825.png',
						'settings' => Fusion_Data::color_theme( 'orange' ),
					),
					'Pink'       => array(
						'label'    => esc_html__( 'Pink', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/e67fb9.png',
						'settings' => Fusion_Data::color_theme( 'pink' ),
					),
					'Brown'      => array(
						'label'    => esc_html__( 'Brown', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/ab8b65.png',
						'settings' => Fusion_Data::color_theme( 'brown' ),
					),
					'Light Grey' => array(
						'label'    => esc_html__( 'Light Grey', 'Avada' ),
						'image'    => Avada::$template_dir_url . '/assets/images/colors/9e9e9e.png',
						'settings' => Fusion_Data::color_theme( 'lightgrey' ),
					),
				),
			),
			'custom_color' => array(
				'description' => '',
				'id'          => 'custom_color_scheme_options',
				'type'        => 'custom',
			),
			'primary_color' => array(
				'label'       => esc_html__( 'Primary Color', 'Avada' ),
				'description' => esc_html__( 'Controls the main highlight color throughout the theme.', 'Avada' ),
				'id'          => 'primary_color',
				'default'     => '#a0ce4e',
				'type'        => 'color',
			),
		),
	);

	// Custom color schemes.
	$custom_colors = get_option( 'avada_custom_color_schemes' );
	$is_custom_color = ( is_array( $custom_colors ) && count( $custom_colors ) > 0 ) ? true : false;
	$export = '';
	$update = '';
	$delete = '';

	// Add save button.
	$buttons =
		'<input type="submit" name="custom_color_save id="custom_color_save" data-toggle="avada-save-custom-color" class="button button-secondary custom-color-toggle" value="' . esc_attr__( 'Save', 'Avada' ) . '"> ';

	// If color already set, add update button and delete button.
	if ( $is_custom_color ) {

		$buttons .=
			'<input type="submit" name="custom_color_update" data-toggle="avada-update-custom-color" id="custom_color_update" class="button button-secondary custom-color-toggle" value="' . esc_attr__( 'Update', 'Avada' ) . '"> ';

		$buttons .=
			'<input type="submit" name="custom_color_delete" data-toggle="avada-delete-custom-color" id="custom_color_delete" class="button button-secondary custom-color-toggle" value="' . esc_attr__( 'Delete', 'Avada' ) . '"> ';

	}

	// Add import button.
	$buttons .=
		'<input type="submit" name="custom_color_import" data-toggle="avada-import-custom-color" id="custom_color_import" class="button button-secondary custom-color-toggle" value="' . esc_attr__( 'Import', 'Avada' ) . '"> ';

	// If color already exist, add export button, export markup, update markup and delete markup.
	if ( $is_custom_color ) {

		$buttons .=
			'<input type="submit" name="custom_color_export" data-toggle="avada-export-custom-color" id="custom_color_export" class="button button-secondary custom-color-toggle" value="' . esc_attr__( 'Export', 'Avada' ) . '">';

		$update .=
			'<div id="avada-update-custom-color" class="color-hidden color-toggle">
				<p class="description">' . esc_attr__( 'This will update the selected custom color scheme with the current selected color options.', 'Avada' ) . '</p>
				<select name="color-scheme-update-name" id="color-scheme-update-name" class="avadaredux-select-item avada_options update-select">';

		foreach ( $custom_colors as $scheme ) {
			$update .= '<option value="' . $scheme['name'] . '">' . $scheme['name'] . '</option>';
		}

		$update .=
			'	</select>
				<input type="submit" name="custom_color_save_update" id="custom_color_save_update" class="button button-primary custom_color_save_button" value="' . esc_attr__( 'Update', 'Avada' ) . '">
			</div>';

		$export =
			'<div id="avada-export-custom-color" class="color-hidden color-toggle">
				<p class="description">' . esc_attr__( 'Copy the export code from the text area and paste it into another installation via the import button.', 'Avada' ) . '</p>
				<textarea id="avada-export-custom-color-textarea">' . wp_json_encode( $custom_colors ) . '</textarea>
			</div>';

		$delete =
			'<div id="avada-delete-custom-color" class="color-hidden color-toggle">
				<p class="description">' . esc_attr__( 'Select the color schemes you wish to delete and then click the "Delete" button.', 'Avada' ) . '</p>
				<input type="submit" name="custom_color_delete_cancel" id="custom_color_delete_cancel" class="button button-secondary" value="' . esc_attr__( 'Cancel Selection', 'Avada' ) . '">
				<input type="submit" name="custom_color_delete_confirm" id="custom_color_delete_confirm" class="button button-primary" value="' . esc_attr__( 'Delete', 'Avada' ) . '">
				<p class="hidden description">No color schemes selected.  Please select a scheme to delete from the color thumbnails before clicking "Delete".</p>
			</div>';
	}
	// Add save markup.
	$save =
		'<div id="avada-save-custom-color" class="color-hidden color-toggle">
			<p class="description">' . esc_attr__( 'This will save the current selected color options as a new custom color scheme.', 'Avada' ) . '</p>
			<input name="color-scheme-new-name" maxlength="30" id="color-scheme-new-name" type="text" value="" placeholder="' . esc_attr__( 'New Scheme', 'Avada' ) . '"/>
			<input type="submit" name="custom_color_save_new" id="custom_color_save_new" class="button button-primary custom_color_save_button" value="' . esc_attr__( 'Save', 'Avada' ) . '">
		</div>';

	// Add import markup.
	$import =
		'<div id="avada-import-custom-color" class="color-hidden color-toggle">
			<p class="description">' . esc_attr__( 'Paste the code that was exported from the color scheme into the text area and then click the "Import" button.', 'Avada' ) . '</p>
			<textarea id="avada-import-custom-color-textarea"></textarea>
			<input type="submit" name="custom_color_import" id="custom_color_import_submit" class="button button-primary custom_color_save_button" value="' . esc_attr__( 'Import', 'Avada' ) . '">
		</div>';

	// Add all buttons and markup to field description.
	$sections['colors']['fields']['custom_color']['description'] = '<div class="fusion-custom-color-scheme">'
			 . $buttons . $save . $update . $import . $export . $delete . '</div>';

	// Add each scheme as an option.
	if ( $is_custom_color ) {
		foreach ( $custom_colors as $scheme ) {
			$scheme_name = esc_html( $scheme['name'] );
			$sections['colors']['fields']['color_scheme']['choices'][ $scheme_name ] = array(
				'label'    => $scheme_name,
				'image'    => Avada::$template_dir_url . '/assets/images/colors/custom.png',
				'settings' => $scheme['values'],
			);
		}
	}

	return $sections;

}
