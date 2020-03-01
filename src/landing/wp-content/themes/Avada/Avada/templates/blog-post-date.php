<?php
/**
 * Blog-post date template.
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
?>
<div class="fusion-date-box">
	<span class="fusion-date">
		<?php echo esc_attr( get_the_time( Avada()->settings->get( 'alternate_date_format_day' ) ) ); ?>
	</span>
	<span class="fusion-month-year">
		<?php echo esc_attr( get_the_time( Avada()->settings->get( 'alternate_date_format_month_year' ) ) ); ?>
	</span>
</div>
