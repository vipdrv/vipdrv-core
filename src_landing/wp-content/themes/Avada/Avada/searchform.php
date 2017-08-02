<?php
/**
 * The search-form template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<form role="search" class="searchform" method="get" action="<?php echo esc_url_raw( home_url( '/' ) ); ?>">
	<div class="search-table">
		<div class="search-field">
			<input type="text" value="" name="s" class="s" placeholder="<?php esc_html_e( 'Search ...', 'Avada' ); ?>" required aria-required="true" aria-label="<?php esc_html_e( 'Search ...', 'Avada' ); ?>"/>
		</div>
		<div class="search-button">
			<input type="submit" class="searchsubmit" value="&#xf002;" />
		</div>
	</div>
</form>
