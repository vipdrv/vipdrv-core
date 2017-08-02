<?php
/**
 * Demos Admin page.
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

<div class="wrap about-wrap avada-wrap">
	<?php $this->get_admin_screens_header( 'demos' ); ?>

	<?php if ( Avada()->registration->is_registered() ) : ?>
		<?php
		// Include the Avada_Importer_Data class if it doesn't exist.
		if ( ! class_exists( 'Avada_Importer_Data' ) ) {
			include_once wp_normalize_path( Avada::$template_dir_path . '/includes/importer/class-avada-importer-data.php' );
		}
		?>

		<script type="text/javascript">
			var DemoImportNonce = '<?php echo esc_attr( wp_create_nonce( 'avada_demo_ajax' ) ); ?>';
		</script>
		<div class="avada-important-notice">
			<p class="about-description"><?php printf( wp_kses_post( __( "Avada demos can be fully imported (everything), or partially imported (only portions). Hover over the demo you want to use and make your selections. Any demo you use will display a badge on it after import so you can quickly recognize and modify the content you already imported. You can choose to uninstall this content at any time. Uninstalling content from a demo will remove ALL previously imported demo content from that demo and restore your site to it's previous state before the demo content was imported.<br /><strong>IMPORTANT:</strong> Demo imports can vary in time. The included plugins need to be installed and activated before you install a demo. Please check the %1\$s tab to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red. %2\$s.", 'Avada' ) ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-system-status' ) ) . '" target="_blank">' . esc_attr__( 'System Status', 'Avada' ) . '</a>', '<a href="' . esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'avada-doc/demo-content-info/import-xml-file/" target="_blank">' . esc_attr__( 'View more info here', 'Avada' ) . '</a>' ); ?></p>
		</div>
	<?php
	// Get theme version for later.
	$theme_version = Avada_Helper::normalize_version( $this->theme_version );

	$demos = Avada_Importer_Data::get_data();
	$all_tags = array(
		'all' => esc_attr__( 'All Demos' ),
	);

	foreach ( $demos as $demo => $demo_details ) {
		if ( ! isset( $demo_details['tags'] ) ) {
			$demo_details['tags'] = array();
		}
		$all_tags = array_replace_recursive( $all_tags, $demo_details['tags'] );
	}

	// Check which recommended plugins are installed and activated.
	$plugin_dependencies = TGM_Plugin_Activation::$instance->plugins;

	foreach ( $plugin_dependencies as $key => $plugin ) {
		$plugin_dependencies[ $key ]['active']    = is_plugin_active( $plugin['file_path'] );
		$plugin_dependencies[ $key ]['installed'] = file_exists( WP_PLUGIN_DIR . '/' . $plugin['file_path'] );
	}

	// Import / Remove demo.
	$imported_data = get_option( 'fusion_import_data', array() );

	$import_stages = array(
		array(
			'value' => 'post',
			'label' => esc_html__( 'Posts', 'Avada' ),
			'data'  => 'content',
			'feature_dependency' => 'post',
		),
		array(
			'value' => 'page',
			'label' => esc_html__( 'Pages', 'Avada' ),
			'data'  => 'content',
			'feature_dependency' => 'page',
		),
		array(
			'value' => 'avada_portfolio',
			'label' => esc_html__( 'Portfolios', 'Avada' ),
			'data'  => 'content',
			'feature_dependency' => 'avada_portfolio',
		),
		array(
			'value' => 'avada_faq',
			'label' => esc_html__( 'FAQs', 'Avada' ),
			'data'  => 'content',
			'feature_dependency' => 'avada_faq',
		),
		array(
			'value' => 'product',
			'label' => esc_html__( 'Products', 'Avada' ),
			'data'  => 'content',
			'plugin_dependency' => 'woocommerce',
		),
		array(
			'value' => 'event',
			'label' => esc_html__( 'Events', 'Avada' ),
			'data'  => 'content',
			'plugin_dependency' => 'the-events-calendar',
		),
		array(
			'value' => 'forum',
			'label' => esc_html__( 'Forum', 'Avada' ),
			'data'  => 'content',
			'plugin_dependency' => 'bbpress',
		),
		array(
			'value' => 'attachment',
			'label' => esc_html__( 'Images', 'Avada' ),
			'data'  => 'content',
		),
		array(
			'value' => 'sliders',
			'label' => esc_html__( 'Sliders', 'Avada' ),
		),
		array(
			'value' => 'theme_options',
			'label' => esc_html__( 'Theme Options', 'Avada' ),
		),
		array(
			'value' => 'widgets',
			'label' => esc_html__( 'Widgets', 'Avada' ),
		),
	);
	?>
	<?php
	/**
	 * WIP:
	 * The tag-selector is hidden for now, we can enable it when needed
	 * simply by removing the "display:none" from the wrapper.
	 */
	?>
		<div class="avada-importer-tags-selector" style="margin-bottom: 1.5em; display: none;">
			<?php foreach ( $all_tags as $key => $label ) : ?>
				<button class="button small button-small button-<?php echo ( 'all' === $key ) ? 'primary' : 'secondary'; ?>" data-tag="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<script type="text/javascript">
			var allTags = <?php echo wp_json_encode( $all_tags ); ?>;
		</script>

		<div class="avada-demo-themes">
			<div class="feature-section theme-browser rendered">

				<?php foreach ( $demos as $demo => $demo_details ) : // Loop through all demos. ?>

					<?php
					// Make sure we don't show demos that can't be applied to this version.
					if ( isset( $demo_details['minVersion'] ) ) {
						$min_version = Avada_Helper::normalize_version( $demo_details['minVersion'] );
						if ( version_compare( $theme_version, $min_version ) < 0 ) {
							continue;
						}
					}
					// Set tags (WIP).
					if ( ! isset( $demo_details['tags'] ) ) {
						$demo_details['tags'] = array();
					} else {
						$tags = array_keys( $demo_details['tags'] );
					}

					if ( empty( $demo_details['plugin_dependencies'] ) ) {
						$demo_details['plugin_dependencies'] = array();
					}

					$demo_details['plugin_dependencies']['fusion-core']    = true;
					$demo_details['plugin_dependencies']['fusion-builder'] = true;

					$demo_imported = false;
					// Generate Import / Remove forms.
					$import_form  = '<form id="import-' . esc_attr( strtolower( $demo ) ) . '" data-demo-id=' . esc_attr( strtolower( $demo ) ) . '>';
					$import_form .= '<p><input type="checkbox" value="all" id="import-all-' . esc_attr( strtolower( $demo ) ) . '"/> <label for="import-all-' . esc_attr( strtolower( $demo ) ) . '">' . esc_html__( 'All', 'Avada' ) . '</label></p>';
					$remove_form  = '<form id="remove-' . esc_attr( strtolower( $demo ) ) . '" data-demo-id=' . esc_attr( strtolower( $demo ) ) . '>';

					foreach ( $import_stages as $import_stage ) {

						$import_checked  = '';
						$remove_disabled = 'disabled';
						$data            = '';
						if ( ! empty( $import_stage['plugin_dependency'] ) && empty( $demo_details['plugin_dependencies'][ $import_stage['plugin_dependency'] ] ) ) {
							continue;
						}

						if ( ! empty( $import_stage['feature_dependency'] ) && ! in_array( $import_stage['feature_dependency'], $demo_details['features'] ) ) {
							continue;
						}

						if ( ! empty( $imported_data[ $import_stage['value'] ] ) ) {
							if ( in_array( strtolower( $demo ), $imported_data[ $import_stage['value'] ] ) ) {
								$import_checked  = 'checked="checked" disabled';
								$remove_disabled = 'checked="checked"';
								$demo_imported = true;
							}
						}
						if ( ! empty( $import_stage['data'] ) ) {
							$data = 'data-type="' . esc_attr( $import_stage['data'] ) . '"';
						}
						$import_form .= '<p><input type="checkbox" value="' . esc_attr( $import_stage['value'] ) . '" ' . $import_checked . ' ' . $data . ' id="import-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '" /> <label for="import-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '">' . $import_stage['label'] . '</label></p>';
						$remove_form .= '<p><input type="checkbox" value="' . esc_attr( $import_stage['value'] ) . '" ' . $remove_disabled . ' ' . $data . ' id="remove-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '" /> <label for="remove-' . esc_attr( $import_stage['value'] ) . '-' . esc_attr( strtolower( $demo ) ) . '">' . $import_stage['label'] . '</label></p>';
					}
					$import_form .= '</form>';
					$remove_form .= '</form>';

					$install_button_label = ! $demo_imported ? __( 'Import', 'Avada' ) : __( 'Modify', 'Avada' );

					if ( ! empty( $imported_data['all'] ) && in_array( strtolower( $demo ), $imported_data['all'] ) ) {
						$demo_import_badge = __( 'Full Import', 'Avada' );
					} else {
						$demo_import_badge = __( 'Partial Import', 'Avada' );
					}
					?>
					<div class="fusion-admin-box">
						<div id="theme-demo-<?php echo esc_attr( strtolower( $demo ) ); ?>" class="theme" data-tags="<?php echo esc_attr( implode( ',', $tags ) ); ?>">
							<div class="theme-wrapper">
								<div class="theme-screenshot">
									<img src="" <?php echo ( ! empty( $demo_details['previewImage'] ) ) ? 'data-src="' . esc_url_raw( $demo_details['previewImage'] ) . '"' : ''; ?> <?php echo ( ! empty( $demo_details['previewImageRetina'] ) ) ? 'data-src-retina="' . esc_url_raw( $demo_details['previewImageRetina'] ) . '"' : ''; ?>>
									<noscript>
										<img src="<?php echo esc_url_raw( $demo_details['previewImage'] ); ?>" width="325" height="244"/>
									</noscript>
								</div>
								<h3 class="theme-name" id="<?php esc_attr( $demo ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $demo ) ) ); ?></h3>
								<div class="theme-actions">
									<a class="button button-primary button-install-open-modal" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>" href="#"><?php echo esc_html( $install_button_label ); ?></a>
									<?php $preview_url = ( 'classic' === $demo ) ? $this->theme_url : $this->theme_url . str_replace( '_', '-', $demo ); ?>
									<a class="button button-primary" target="_blank" href="<?php echo esc_url_raw( $preview_url ); ?>"><?php esc_attr_e( 'Preview', 'Avada' ); ?></a>
								</div>

								<?php if ( isset( $demo_details['new'] ) && true === $demo_details['new'] ) : ?>
									<div class="plugin-required"><?php esc_attr_e( 'New', 'Avada' ); ?></div>
								<?php endif; ?>

								<div class="demo-imported" style="display: <?php echo esc_attr( true === $demo_imported ? 'block' : 'none' ); ?>;"><?php echo esc_html( $demo_import_badge ); ?></div>

								<div id="demo-modal-<?php echo esc_attr( strtolower( $demo ) ); ?>" class="demo-update-modal-wrap" style="display:none;">

									<div class="demo-update-modal-inner">

										<div class="demo-modal-thumbnail" style="background-image:url(<?php echo esc_attr( $demo_details['previewImage'] ); ?>);">

											<a class="demo-modal-preview" target="_blank" href="<?php echo esc_url_raw( $preview_url ); ?>"><?php esc_attr_e( 'Live Preview', 'Avada' ); ?></a>
										</div>

										<div class="demo-update-modal-content">

											<?php if ( in_array( true, $demo_details['plugin_dependencies'] ) ) : ?>
												<div class="demo-required-plugins">
													<h3>
														<?php esc_html_e( 'The Following Plugins Are Required To Import Content', 'Avada' ); ?>
													</h3>
													<ul class="required-plugins-list">

														<?php foreach ( $demo_details['plugin_dependencies'] as $slug => $required ) : ?>

															<?php if ( true === $required ) : ?>
																<li>
																	<span class="required-plugin-name">
																		<?php echo esc_html( $plugin_dependencies[ $slug ]['name'] ); ?>
																	</span>

																		<?php
																		$label  = __( 'Install', 'Avada' );
																		$status = 'install';
																		if ( $plugin_dependencies[ $slug ]['active'] ) {
																			$label  = __( 'Active', 'Avada' );
																			$status = 'active';
																		} elseif ( $plugin_dependencies[ $slug ]['installed'] ) {
																			$label  = __( 'Activate', 'Avada' );
																			$status = 'activate';
																		}
																		?>
																		<span class="required-plugin-status <?php echo esc_attr( $status ); ?> ">
																			<?php if ( 'activate' === $status ) : ?>
																				<a href="<?php echo esc_url( admin_url( 'admin.php?page=avada-plugins' ) ); ?>"
																				   target="_blank"
																				   data-nonce="<?php echo esc_attr( wp_create_nonce( 'avada-activate' ) ); ?>"
																				   data-plugin="<?php echo esc_attr( $slug ); ?>"
																				   data-plugin_name="<?php echo esc_attr( $plugin_dependencies[ $slug ]['name'] ); ?>"
																				>
																			<?php elseif ( 'install' === $status ) : ?>
																				<a href="<?php echo esc_url( admin_url( 'admin.php?page=avada-plugins' ) ); ?>"
																				   target="_blank"
																				   data-nonce="<?php echo esc_attr( wp_create_nonce( 'avada-activate' ) ); ?>"
																				   data-plugin="<?php echo esc_attr( $slug ); ?>"
																				   data-plugin_name="<?php echo esc_attr( $plugin_dependencies[ $slug ]['name'] ); ?>"
																				   data-tgmpa_nonce="<?php echo esc_attr( wp_create_nonce( 'tgmpa-install' ) ); ?>"
																				>
																			<?php endif; ?>

																				<?php echo esc_html( $label ); ?>

																			<?php if ( 'active' !== $status ) : ?>
																				</a>
																			<?php endif; ?>
																		</span>
																</li>
															<?php endif; ?>

														<?php endforeach; ?>

													</ul>

												</div>
											<?php endif; ?>

											<div class="demo-update-form-wrap">

												<div class="demo-import-form">
													<h4 class="demo-form-title">
														<?php esc_html_e( 'Import Content', 'Avada' ); ?> <span><?php esc_html_e( '(menus only import with "All")', 'Avada' ); ?></span>
													</h4>
													<?php echo $import_form; // WPCS: XSS ok. ?>
												</div>

												<div class="demo-remove-form">
													<h4 class="demo-form-title">
														<?php esc_html_e( 'Remove Content', 'Avada' ); ?>
													</h4>

													<p>
														<input type="checkbox" value="uninstall" id="uninstall-<?php echo esc_attr( strtolower( $demo ) ); ?>" /> <label for="uninstall-<?php echo esc_attr( strtolower( $demo ) ); ?>"><?php esc_html_e( 'Uninstall', 'Avada' ); ?></label>
													</p>
													<?php echo $remove_form; // WPCS: XSS ok. ?>
												</div>
											</div>

										</div>

										<div class="demo-update-modal-status-bar">
											<div class="demo-update-modal-status-bar-label"><span></span></div>
											<div class="demo-update-modal-status-bar-progress-bar"></div>

											<a class="button-install-demo" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>" href="#">
												<?php echo esc_html__( 'Import', 'Avada' ); ?>
											</a>

											<a class="button-uninstall-demo" data-demo-id="<?php echo esc_attr( strtolower( $demo ) ); ?>" href="#">
												<?php echo esc_html__( 'Remove', 'Avada' ); ?>
											</a>

											<a class="button-done-demo demo-update-modal-close" href="#">
												<?php echo esc_html__( 'Done', 'Avada' ); ?>
											</a>
										</div>
									</div>

									<a href="#" class="demo-update-modal-corner-close demo-update-modal-close"><span class="dashicons dashicons-no-alt"></span></a>
								</div> <!-- .demo-update-modal-wrap -->

							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="avada-thanks">
			<p class="description"><?php esc_attr_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
		</div>

		<div class="demo-import-overlay preview-all"></div>

		<div id="dialog-demo-confirm" title="<?php esc_attr_e( 'Warning ', 'Avada' ); ?>">

		</div>

		<script>
			!function(t){t.fn.unveil=function(i,e){function n(){var i=a.filter(function(){var i=t(this);if(!i.is(":hidden")){var e=o.scrollTop(),n=e+o.height(),r=i.offset().top,s=r+i.height();return s>=e-u&&n+u>=r}});r=i.trigger("unveil"),a=a.not(r)}var r,o=t(window),u=i||0,s=window.devicePixelRatio>1,l=s?"data-src-retina":"data-src",a=this;return this.one("unveil",function(){var t=this.getAttribute(l);t=t||this.getAttribute("data-src"),t&&(this.setAttribute("src",t),"function"==typeof e&&e.call(this))}),o.on("scroll.unveil resize.unveil lookup.unveil",n),n(),this}}(window.jQuery||window.Zepto);
			jQuery(document).ready(function() { jQuery( 'img' ).unveil( 200 ); });
		</script>
	<?php else : ?>
		<div class="avada-important-notice" style="border-left: 4px solid #dc3232;">
			<h3 style="color: #dc3232; margin-top: 0;"><?php esc_attr_e( 'Avada Demos Can Only Be Imported With A Valid Token Registration', 'Avada' ); ?></h3>
			<p><?php printf( esc_attr__( 'Please visit the %s page and enter a valid token to import the full Avada Demos and the single pages through Fusion Builder.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-registration' ) ) . '">' . esc_attr__( 'Product Registration', 'Avada' ) . '</a>' ); ?></p>
		</div>
	<?php endif; ?>
</div>
<?php
