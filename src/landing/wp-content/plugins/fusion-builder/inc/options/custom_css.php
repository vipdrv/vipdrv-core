<?php

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
function fusion_builder_options_section_custom_css( $sections ) {

	$sections['custom_css'] = array(
		'label'    => esc_html__( 'Custom CSS', 'fusion-builder' ),
		'id'       => 'custom_css_section',
		'is_panel' => true,
		'priority' => 27,
		'icon'     => 'el-icon-css',
		'fields'   => array(
			'custom_css' => array(
				'label'       => esc_html__( 'CSS Code', 'fusion-builder' ),
				'description' => sprintf( esc_html__( 'Enter your CSS code in the field below. Do not include any tags or HTML in the field. Custom CSS entered here will override the theme CSS. In some cases, the !important tag may be needed. Don\'t URL encode image or svg paths. Contents of this field will be auto encoded.', 'fusion-builder' ), '<code>!important</code>' ),
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
