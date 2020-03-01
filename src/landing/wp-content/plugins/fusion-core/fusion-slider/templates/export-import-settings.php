<?php
/**
 * Export/Import settings template.
 *
 * @package Fusion-Slider
 * @subpackage Templates
 * @since 1.0.0
 */

?>
<div class="wrap">
	<h2><?php esc_attr_e( 'Export and Import Fusion Sliders', 'fusion-core' ); ?></h2>
	<form enctype="multipart/form-data" method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_attr_e( 'Export', 'fusion-core' ); ?></th>
				<td><input type="submit" class="button button-primary" name="export_button" value="<?php esc_attr_e( 'Export All Sliders', 'fusion-core' ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th>
					<label for="upload"><?php esc_attr__( 'Choose a file from your computer:', 'fusion-core' ); ?></label>
				</th>
				<td>
					<input type="file" id="upload" name="import" size="25" />
					<input type="hidden" name="action" value="save" />
					<input type="hidden" name="max_file_size" value="33554432" />
					<p class="submit"><input type="submit" name="upload" id="submit" class="button" value="Upload file and import"  /></p>
				</td>
			</tr>
		</table>
	</form>
</div>
