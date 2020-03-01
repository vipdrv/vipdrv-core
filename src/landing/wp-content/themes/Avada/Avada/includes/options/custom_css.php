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
 * Custom CSS settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_options_section_custom_css( $sections ) {

	$sections['custom_css'] = array(
		'label'    => esc_html__( 'Custom CSS', 'Avada' ),
		'id'       => 'custom_css_section',
		'priority' => 27,
		'icon'     => 'el-icon-css',
		'fields'   => array(
			'custom_css' => array(
				'label'       => esc_html__( 'CSS Code', 'Avada' ),
				'description' => sprintf( esc_html__( 'Enter your CSS code in the field below. Do not include any tags or HTML in the field. Custom CSS entered here will override the theme CSS. In some cases, the !important tag may be needed. Don\'t URL encode image or svg paths. Contents of this field will be auto encoded.', 'Avada' ), '<code>!important</code>' ),
				'id'          => 'custom_css',
				'default'     => '',
				'type'        => 'code',
				'choices'     => array(
					'language' => 'css',
					'height'   => 450,
					'theme'    => 'chrome',
					'minLines' => 40,
					'maxLines' => 50,
				),
			),
		),
	);

	return $sections;

}
