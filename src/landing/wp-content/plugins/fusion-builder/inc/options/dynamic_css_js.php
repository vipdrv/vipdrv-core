<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Advanced settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function fusion_builder_options_section_advanced( $sections ) {

	$sections['dynamic_css_js'] = array(
		'label'    => esc_html__( 'Dynamic CSS & JS', 'fusion-builder' ),
		'id'       => 'dynamic_css_js',
		'is_panel' => true,
		'priority' => 25,
		'icon'     => 'el-icon-puzzle',
		'fields'   => array(
			'js_compiler_note' => ( 'no' != get_transient( 'fusion_dynamic_js_readable' ) ) ? array() : array(
				'label'       => '',
				'description' => '<div class="fusion-redux-important-notice">' . __( '<strong>IMPORTANT NOTE:</strong> JS Compiler is disabled. File does not exist or access is restricted.', 'fusion-builder' ) . '</div>',
				'id'          => 'js_compiler_note',
				'type'        => 'custom',
			),
			'js_compiler' => array(
				'label'       => esc_html__( 'Enable JS compiler', 'fusion-builder' ),
				'description' => ( Fusion_Dynamic_JS::is_http2() ) ? esc_html__( 'We have detected that your server supports HTTP/2. We recommend you leave the compiler disabled as that will improve performance of your site by allowing multiple JS files to be downloaded simultaneously.', 'fusion-builder' ) : 	esc_html__( 'By default all the javascript files are combined. Disabling the JS compiler will load non-combined javascript files. This will have an impact on the performance of your site.', 'fusion-builder' ),
				'id'          => 'js_compiler',
				'default'     => ( Fusion_Dynamic_JS::is_http2() ) ? '0' : '1',
				'type'        => 'switch',
			),
			'css_cache_method' => array(
				'label'       => esc_html__( 'CSS Caching method', 'fusion-builder' ),
				'description' => esc_html__( 'Select "File" mode to compile the dynamic CSS to files (a separate file will be created for each of your pages & posts inside of the uploads/fusion-styles folder), "Database" mode to cache the CSS in your database, or select "Off" to disable.', 'fusion-builder' ),
				'id'          => 'css_cache_method',
				'default'     => 'file',
				'type'        => 'radio-buttonset',
				'choices'     => array(
					'file' => esc_attr__( 'File', 'fusion-builder' ),
					'db'   => esc_attr__( 'Database', 'fusion-builder' ),
					'off'  => esc_attr__( 'Off', 'fusion-builder' ),
				),
			),
			'cache_server_ip' => array(
				'label'       => esc_html__( 'Cache Server IP', 'fusion-builder' ),
				'description' => esc_html__( 'For unique cases where you are using cloud flare and a cache server, ex: varnish cache. Enter your cache server IP to clear the theme options dynamic CSS cache. Consult with your server admin for help.', 'fusion-builder' ),
				'id'          => 'cache_server_ip',
				'default'     => '',
				'type'        => 'text',
			),
		),
	);

	return $sections;

}
