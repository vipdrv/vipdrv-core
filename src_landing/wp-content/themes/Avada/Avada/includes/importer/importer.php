<?php
/**
 * Avada Content importer.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Importer
 * @since      5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}


include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/avada-import-functions.php' );
include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/class-avada-demo-import.php' );
include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/class-avada-demo-remove.php' );

new Avada_Demo_Import();

new Avada_Demo_Remove();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
