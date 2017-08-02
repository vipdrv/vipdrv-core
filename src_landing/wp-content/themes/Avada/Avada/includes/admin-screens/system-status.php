<?php
/**
 * System-Status Admin page.
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
	<?php $this->get_admin_screens_header( 'system-status' ); ?>
	<div class="avada-system-status">
		<table class="widefat fusion-system-status-debug" cellspacing="0">
			<tbody>
				<tr>
					<td colspan="3" data-export-label="Avada Versions">
						<span class="get-system-status"><a href="#" class="button-primary debug-report"><?php esc_attr_e( 'Get System Report', 'Avada' ); ?></a><span class="system-report-msg"><?php esc_attr_e( 'Click the button to produce a report, then copy and paste into your support ticket.', 'Avada' ); ?></span></span>

						<div id="debug-report">
							<textarea readonly="readonly"></textarea>
							<p class="submit"><button id="copy-for-support" class="button-primary" href="#" data-tip="<?php esc_attr_e( 'Copied!', 'Avada' ); ?>"><?php esc_attr_e( 'Copy for Support', 'Avada' ); ?></button></p>
						</div>
					</td>
				</tr>
			</tbody>
		</div>
		<h3 class="screen-reader-text"><?php esc_attr_e( 'Avada Versions', 'Avada' ); ?></h3>
		<table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Avada Versions"><?php esc_attr_e( 'Avada Versions', 'Avada' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td data-export-label="Current Version"><?php esc_attr_e( 'Current Version:', 'Avada' ); ?></td>
					<td class="help">&nbsp;</td>
					<td><?php echo esc_attr( $this->theme_version ); ?></td>
				</tr>
				<tr>
					<td data-export-label="Previous Version"><?php esc_attr_e( 'Previous Versions:', 'Avada' ); ?></td>
					<td class="help">&nbsp;</td>
					<?php
					$previous_version = get_option( 'avada_previous_version', false );
					$previous_versions_array = is_array( $previous_version ) ? $previous_version : array();
					if ( $previous_version && is_array( $previous_version ) ) {
						foreach ( $previous_version as $key => $value ) {
							if ( ! $value ) {
								unset( $previous_version[ $key ] );
							}
						}
					}
					if ( ! $previous_version ) {
						$previous_version = __( 'No previous versions could be detected', 'Avada' );
					} else {
						if ( is_array( $previous_version ) ) {
							$previous_versions_array = $previous_version;
							$previous_version = array_slice( $previous_version, -3, 3, true );
							$previous_version = implode( ' <span style="font-size:1em;line-height:inherit;" class="dashicons dashicons-arrow-right-alt"></span> ', array_map( 'esc_attr', $previous_version ) );
						}
					}
					?>
					<td>
						<?php echo $previous_version; // WPCS: XSS ok. ?>
					</td>
				</tr>
				<?php
				$show_400_migration = false;
				$force_hide_400_migration = false;
				$show_500_migration = false;
				$versions_count = count( $previous_versions_array );
				if ( isset( $previous_versions_array[ $versions_count - 1 ] ) && isset( $previous_versions_array[ $versions_count - 2 ] ) ) {
					if ( version_compare( $previous_versions_array[ $versions_count - 1 ], '4.0.0', '>=' ) && version_compare( $previous_versions_array[ $versions_count - 2 ], '4.0.0', '<=' ) ) {
						$force_hide_400_migration = true;
					}
				}
				$previous_version = get_option( 'avada_previous_version', false );
				if ( false !== $previous_version && ! empty( $previous_version ) ) {
					if ( is_array( $previous_version ) ) {
						foreach ( $previous_version as $ver ) {
							$ver = Avada_Helper::normalize_version( $ver );
							if ( $ver && ! empty( $ver ) && version_compare( $ver, '4.0.0', '<' ) ) {
								$show_400_migration = true;
								$last_pre_4_version = $ver;
							}

							if ( $ver && ! empty( $ver ) && version_compare( $ver, '5.0.0', '<' ) ) {
								$show_500_migration = true;
								$last_pre_5_version = $ver;
							}
							$last_version = $ver;
						}
					} else {
						$previous_version = Avada_Helper::normalize_version( $previous_version );
						if ( version_compare( $previous_version, '4.0.0', '<' ) ) {
							$show_400_migration = true;
							$last_pre_4_version = $previous_version;
						}

						if ( version_compare( $previous_version, '5.0.0', '<' ) ) {
							$show_500_migration = true;
							$last_pre_5_version = $previous_version;
						}
						$last_version = $previous_version;
					}
				}
				?>
				<?php if ( $show_400_migration && false === $force_hide_400_migration ) : ?>
					<?php $latest_version     = ( empty( $last_version ) || ! $last_version ) ? esc_attr__( 'Previous Version', 'Avada' ) : sprintf( esc_attr__( 'Version %s', 'Avada' ), esc_attr( $last_version ) ); ?>
					<?php $last_pre_4_version = ( isset( $last_pre_4_version ) ) ? $last_pre_4_version : $latest_version; ?>
					<tr>
						<td><?php esc_attr_e( 'Avada 4.0 Conversion:', 'Avada' ); ?></td>
						<td class="help">&nbsp;</td>
						<td>
							<table class="widefat fusion-conversion-button">
								<tr>
									<td style="width:auto;"><?php printf( esc_attr__( 'Rerun Theme Options Conversion from version %s to version 4.0 manually.', 'Avada' ), esc_attr( $last_pre_4_version ) ); ?></td>
									<td style="min-width:140px;"><a class="button button-small button-primary" style="display:block;width:100%;text-align:center;" id="avada-manual-400-migration-trigger" href="#"><?php esc_attr_e( 'Run Conversion', 'Avada' ); ?></a></td>
								</tr>
							</table>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( $show_500_migration ) : ?>
					<?php $latest_version     = ( empty( $last_version ) || ! $last_version ) ? esc_attr__( 'Previous Version', 'Avada' ) : sprintf( esc_attr__( 'Version %s', 'Avada' ), $last_version ); ?>
					<?php $last_pre_5_version = ( isset( $last_pre_5_version ) ) ? $last_pre_5_version : $latest_version; ?>
					<tr>
						<td><?php esc_attr_e( 'Avada 5.0 Conversion:', 'Avada' ); ?></td>
						<td class="help">&nbsp;</td>
						<td>
							<table class="widefat fusion-conversion-button">
								<tr>
									<td style="width:auto;"><?php printf( esc_attr__( 'Rerun Shortcode Conversion from version %s to version 5.0 manually.', 'Avada' ), esc_attr( $last_pre_5_version ) ); ?></td>
									<td style="min-width:140px;"><a class="button button-small button-primary" style="display:block;width:100%;text-align:center;" id="avada-manual-500-migration-trigger" href="#"><?php esc_attr_e( 'Run Conversion', 'Avada' ); ?></a></td>
								</tr>
								<?php
								$option_name = Avada::get_option_name();
								$backup = get_option( $option_name . '_500_backup', false );
								if ( ! $backup && 'fusion_options' === $option_name ) {
									$backup = get_option( 'avada_theme_options_500_backup', false );
								}
								?>
								<?php if ( false !== get_option( 'fusion_core_unconverted_posts_converted', false ) ) : ?>
									<?php if ( false !== $backup || false !== get_option( 'scheduled_avada_fusionbuilder_migration_cleanups', false ) ) : ?>
										<tr>
											<td style="width:auto;"><?php esc_attr_e( 'Revert Fusion-Builder Conversion', 'Avada' ); ?></td>
											<td style="min-width:140px;"><a class="button button-small button-primary" style="display:block;width:100%;text-align:center;" id="avada-manual-500-migration-revert-trigger" href="#"><?php esc_attr_e( 'Revert Conversion', 'Avada' ); ?></a></td>
										</tr>
									<?php endif; ?>
								<?php endif; ?>
								<?php if ( false !== $backup || false !== get_option( 'scheduled_avada_fusionbuilder_migration_cleanups', false ) ) : ?>
									<tr>
										<td style="width:auto;">
											<?php $show_remove_backups_button = false; ?>
											<?php if ( isset( $_GET['cleanup-500-backups'] ) && '1' == $_GET['cleanup-500-backups'] ) : ?>
												<?php update_option( 'scheduled_avada_fusionbuilder_migration_cleanups', true ); ?>
												<?php esc_attr_e( 'The backups cleanup process has been scheduled and your the version 5.0 conversion backups will be purged from your database.', 'Avada' ); ?>
											<?php else : ?>
												<?php if ( false !== get_option( 'avada_migration_cleanup_id', false ) ) : ?>
													<?php
													// The post types we'll need to check.
													$post_types = apply_filters( 'fusion_builder_shortcode_migration_post_types', array(
														'page',
														'post',
														'avada_faq',
														'avada_portfolio',
														'product',
														'tribe_events',
													) );
													foreach ( $post_types as $key => $post_type ) {
														if ( ! post_type_exists( $post_type ) ) {
															unset( $post_types[ $key ] );
														}
													}

													// Build the query array.
													$args = array(
														'posts_per_page' => 1,
														'orderby'        => 'ID',
														'order'          => 'DESC',
														'post_type'      => $post_types,
														'post_status'    => 'any',
													);

													// The query to get posts that meet our criteria.
													$posts = fusion_cached_get_posts( $args );

													$current_step = get_option( 'avada_migration_cleanup_id', false );
													$total_steps  = $posts[0]->ID;
													?>
													<?php printf( esc_attr__( 'Currently removing backups from your database (step %1$s of %2$s)', 'Avada' ), (int) $current_step, (int) $total_steps ); ?>
												<?php else : ?>
													<?php $show_remove_backups_button = true; ?>
													<?php esc_attr_e( 'Remove Shortcode Conversion Backups created during the version 5.0 conversion.', 'Avada' ); ?>
												<?php endif; ?>
											<?php endif; ?>
										</td>
										<?php if ( isset( $show_remove_backups_button ) && true === $show_remove_backups_button ) : ?>
											<td style="min-width:140px;">
												<a class="button button-small button-primary" style="display:block;width:100%;text-align:center;" id="avada-remove-500-migration-backups" href="#"><?php esc_attr_e( 'Remove Backups', 'Avada' ); ?></a>
											</td>
										<?php endif; ?>
									</tr>
								<?php endif; ?>
							</table>
						</td>
					</tr>

				<?php endif; ?>
			</tbody>
		</table>

		<h3 class="screen-reader-text"><?php esc_attr_e( 'WordPress Environment', 'Avada' ); ?></h3>
		<table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="WordPress Environment"><?php esc_attr_e( 'WordPress Environment', 'Avada' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td data-export-label="Home URL"><?php esc_attr_e( 'Home URL:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The URL of your site\'s homepage.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo esc_url_raw( home_url() ); ?></td>
				</tr>
				<tr>
					<td data-export-label="Site URL"><?php esc_attr_e( 'Site URL:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The root URL of your site.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo esc_url_raw( site_url() ); ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Content Path"><?php esc_attr_e( 'WP Content Path:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'System path of your wp-content directory.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo defined( 'WP_CONTENT_DIR' ) ? esc_html( WP_CONTENT_DIR ) : esc_html__( 'N/A', 'Avada' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Path"><?php esc_attr_e( 'WP Path:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'System path of your WP root directory.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo defined( 'ABSPATH' ) ? esc_html( ABSPATH ) : esc_html__( 'N/A', 'Avada' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Version"><?php esc_attr_e( 'WP Version:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of WordPress installed on your site.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php bloginfo( 'version' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Multisite"><?php esc_attr_e( 'WP Multisite:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo ( is_multisite() ) ? '&#10004;' : '&ndash;'; ?></td>
				</tr>
				<tr>
					<td data-export-label="PHP Memory Limit"><?php esc_attr_e( 'PHP Memory Limit:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td>
						<?php
						// Get the memory from PHP's configuration.
						$memory = ini_get( 'memory_limit' );
						// If we can't get it, fallback to WP_MEMORY_LIMIT.
						if ( ! $memory || -1 === $memory ) {
							$memory = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
						}
						// Make sure the value is properly formatted in bytes.
						if ( ! is_numeric( $memory ) ) {
							$memory = wp_convert_hr_to_bytes( $memory );
						}
						?>
						<?php if ( $memory < 128000000 ) : ?>
							<mark class="error">
								<?php printf( __( '%1$s - We recommend setting memory to at least <strong>128MB</strong>. Please define memory limit in <strong>wp-config.php</strong> file. To learn how, see: <a href="%2$s" target="_blank" rel="noopener noreferrer">Increasing memory allocated to PHP.</a>', 'Avada' ), esc_attr( size_format( $memory ) ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ); // WPCS: XSS ok. ?>
							</mark>
						<?php else : ?>
							<mark class="yes">
								<?php echo esc_attr( size_format( $memory ) ); ?>
							</mark>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td data-export-label="WP Debug Mode"><?php esc_attr_e( 'WP Debug Mode:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td>
						<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
							<mark class="yes">&#10004;</mark>
						<?php else : ?>
							<mark class="no">&ndash;</mark>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td data-export-label="Language"><?php esc_attr_e( 'Language:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The current language used by WordPress. Default = English', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo esc_attr( get_locale() ) ?></td>
				</tr>
			</tbody>
		</table>

		<h3 class="screen-reader-text"><?php esc_attr_e( 'Server Environment', 'Avada' ); ?></h3>
		<table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Server Environment"><?php esc_attr_e( 'Server Environment', 'Avada' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td data-export-label="Server Info"><?php esc_attr_e( 'Server Info:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Information about the web server that is currently hosting your site.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo isset( $_SERVER['SERVER_SOFTWARE'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) ) : esc_attr__( 'Unknown', 'Avada' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="PHP Version"><?php esc_attr_e( 'PHP Version:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of PHP installed on your hosting server.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td>
						<?php
						$php_version = null;
						if ( defined( 'PHP_VERSION' ) ) {
							$php_version = PHP_VERSION;
						} elseif ( function_exists( 'phpversion' ) ) {
							$php_version = phpversion();
						}
						if ( null === $php_version ) {
							$message = esc_attr__( 'PHP Version could not be detected.', 'Avada' );
						} else {
							if ( version_compare( $php_version, '7.0.0' ) >= 0 ) {
								$message = $php_version;
							} else {
								$message = sprintf( esc_attr__( '%1$s. WordPress recomendation: 7.0.0 or above. See %2$s for details.', 'Avada' ), $php_version, '<a href="https://wordpress.org/about/requirements/" target="_blank">WordPress Requirements</a>' );
							}
						}
						echo $message; // WPCS: XSS ok.
						?>
					</td>
				</tr>
				<?php if ( function_exists( 'ini_get' ) ) : ?>
					<tr>
						<td data-export-label="PHP Post Max Size"><?php esc_attr_e( 'PHP Post Max Size:', 'Avada' ); ?></td>
						<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The largest file size that can be contained in one post.', 'Avada' ) . '">[?]</a>'; ?></td>
						<td><?php echo esc_attr( size_format( wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) ) ) ); ?></td>
					</tr>
					<tr>
						<td data-export-label="PHP Time Limit"><?php esc_attr_e( 'PHP Time Limit:', 'Avada' ); ?></td>
						<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'Avada' ) . '">[?]</a>'; ?></td>
						<td>
							<?php
							$time_limit = ini_get( 'max_execution_time' );

							if ( 180 > $time_limit && 0 != $time_limit ) {
								echo '<mark class="error">' . sprintf( __( '%1$s - We recommend setting max execution time to at least 180.<br />See: <a href="%2$s" target="_blank" rel="noopener noreferrer">Increasing max execution to PHP</a>', 'Avada' ), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded' ) . '</mark>'; // WPCS: XSS ok.
							} else {
								echo '<mark class="yes">' . esc_attr( $time_limit ) . '</mark>';
							}
							?>
						</td>
					</tr>
					<tr>
						<td data-export-label="PHP Max Input Vars"><?php esc_attr_e( 'PHP Max Input Vars:', 'Avada' ); ?></td>
						<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'Avada' ) . '">[?]</a>'; ?></td>
						<?php
						$registered_navs = get_nav_menu_locations();
						$menu_items_count = array(
							'0' => '0',
						);
						foreach ( $registered_navs as $handle => $registered_nav ) {
							$menu = wp_get_nav_menu_object( $registered_nav );
							if ( $menu ) {
								$menu_items_count[] = $menu->count;
							}
						}

						$max_items = max( $menu_items_count );
						if ( Avada()->settings->get( 'disable_megamenu' ) ) {
							$required_input_vars = $max_items * 20;
						} else {
							$required_input_vars = $max_items * 12;
						}
						?>
						<td>
							<?php
							$max_input_vars = ini_get( 'max_input_vars' );
							$required_input_vars = $required_input_vars + ( 500 + 1000 );
							// 1000 = theme options
							if ( $max_input_vars < $required_input_vars ) {
								echo '<mark class="error">' . sprintf( __( '%1$s - Recommended Value: %2$s.<br />Max input vars limitation will truncate POST data such as menus. See: <a href="%3$s" target="_blank" rel="noopener noreferrer">Increasing max input vars limit.</a>', 'Avada' ), $max_input_vars, '<strong>' . $required_input_vars . '</strong>', 'http://sevenspark.com/docs/ubermenu-3/faqs/menu-item-limit' ) . '</mark>'; // WPCS: XSS ok.
							} else {
								echo '<mark class="yes">' . esc_attr( $max_input_vars ) . '</mark>';
							}
							?>
						</td>
					</tr>
					<tr>
						<td data-export-label="SUHOSIN Installed"><?php esc_attr_e( 'SUHOSIN Installed:', 'Avada' ); ?></td>
						<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself.
		If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'Avada'  ) . '">[?]</a>'; ?></td>
						<td><?php echo extension_loaded( 'suhosin' ) ? '&#10004;' : '&ndash;'; ?></td>
					</tr>
					<?php if ( extension_loaded( 'suhosin' ) ) :  ?>
						<tr>
							<td data-export-label="Suhosin Post Max Vars"><?php esc_attr_e( 'Suhosin Post Max Vars:', 'Avada' ); ?></td>
							<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'Avada' ) . '">[?]</a>'; ?></td>
							<?php
							$registered_navs = get_nav_menu_locations();
							$menu_items_count = array(
								'0' => '0',
							);
							foreach ( $registered_navs as $handle => $registered_nav ) {
								$menu = wp_get_nav_menu_object( $registered_nav );
								if ( $menu ) {
									$menu_items_count[] = $menu->count;
								}
							}

							$max_items = max( $menu_items_count );
							if ( Avada()->settings->get( 'disable_megamenu' ) ) {
								$required_input_vars = $max_items * 20;
							} else {
								$required_input_vars = $max_items * 12;
							}
							?>
							<td>
								<?php
								$max_input_vars = ini_get( 'suhosin.post.max_vars' );
								$required_input_vars = $required_input_vars + ( 500 + 1000 );

								if ( $max_input_vars < $required_input_vars ) {
									echo '<mark class="error">' . sprintf( __( '%1$s - Recommended Value: %2$s.<br />Max input vars limitation will truncate POST data such as menus. See: <a href="%3$s" target="_blank" rel="noopener noreferrer">Increasing max input vars limit.</a>', 'Avada' ), $max_input_vars, '<strong>' . ( $required_input_vars ) . '</strong>', 'http://sevenspark.com/docs/ubermenu-3/faqs/menu-item-limit' ) . '</mark>'; // WPCS: XSS ok.
								} else {
									echo '<mark class="yes">' . esc_attr( $max_input_vars ) . '</mark>';
								}
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Suhosin Request Max Vars"><?php esc_attr_e( 'Suhosin Request Max Vars:', 'Avada' ); ?></td>
							<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'Avada' ) . '">[?]</a>'; ?></td>
							<?php
							$registered_navs = get_nav_menu_locations();
							$menu_items_count = array(
								'0' => '0',
							);
							foreach ( $registered_navs as $handle => $registered_nav ) {
								$menu = wp_get_nav_menu_object( $registered_nav );
								if ( $menu ) {
									$menu_items_count[] = $menu->count;
								}
							}

							$max_items = max( $menu_items_count );
							if ( Avada()->settings->get( 'disable_megamenu' ) ) {
								$required_input_vars = $max_items * 20;
							} else {
								$required_input_vars = ini_get( 'suhosin.request.max_vars' );
							}
							?>
							<td>
								<?php
								$max_input_vars = ini_get( 'suhosin.request.max_vars' );
								$required_input_vars = $required_input_vars + ( 500 + 1000 );

								if ( $max_input_vars < $required_input_vars ) {
									echo '<mark class="error">' . sprintf( __( '%1$s - Recommended Value: %2$s.<br />Max input vars limitation will truncate POST data such as menus. See: <a href="%3$s" target="_blank" rel="noopener noreferrer">Increasing max input vars limit.</a>', 'Avada' ), $max_input_vars, '<strong>' . ( $required_input_vars + ( 500 + 1000 ) ) . '</strong>', 'http://sevenspark.com/docs/ubermenu-3/faqs/menu-item-limit' ) . '</mark>'; // WPCS: XSS ok.
								} else {
									echo '<mark class="yes">' . esc_attr( $max_input_vars ) . '</mark>';
								}
								?>
							</td>
						</tr>
						<tr>
							<td data-export-label="Suhosin Post Max Value Length"><?php esc_attr_e( 'Suhosin Post Max Value Length:', 'Avada' ); ?></td>
							<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Defines the maximum length of a variable that is registered through a POST request.', 'Avada' ) . '">[?]</a>'; ?></td>
							<td><?php
								$suhosin_max_value_length = ini_get( 'suhosin.post.max_value_length' );
								$recommended_max_value_length = 2000000;

							if ( $suhosin_max_value_length < $recommended_max_value_length ) {
								echo '<mark class="error">' . sprintf( __( '%1$s - Recommended Value: %2$s.<br />Post Max Value Length limitation may prohibit the Theme Options data from being saved to your database. See: <a href="%3$s" target="_blank" rel="noopener noreferrer">Suhosin Configuration Info</a>.', 'Avada' ), $suhosin_max_value_length, '<strong>' . $recommended_max_value_length . '</strong>', 'http://suhosin.org/stories/configuration.html' ) . '</mark>'; // WPCS: XSS ok.
							} else {
								echo '<mark class="yes">' . esc_attr( $suhosin_max_value_length ) . '</mark>';
							}
							?></td>
						</tr>
					<?php endif; ?>
				<?php endif; ?>
				<tr>
					<td data-export-label="ZipArchive"><?php esc_attr_e( 'ZipArchive:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'ZipArchive is required for importing demos. They are used to import and export zip files specifically for sliders.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo class_exists( 'ZipArchive' ) ? '<mark class="yes">&#10004;</mark>' : '<mark class="error">ZipArchive is not installed on your server, but is required if you need to import demo content.</mark>'; ?></td>
				</tr>
				<tr>
					<td data-export-label="MySQL Version"><?php esc_attr_e( 'MySQL Version:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of MySQL installed on your hosting server.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td>
						<?php global $wpdb; ?>
						<?php echo esc_attr( $wpdb->db_version() ); ?>
					</td>
				</tr>
				<tr>
					<td data-export-label="Max Upload Size"><?php esc_attr_e( 'Max Upload Size:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The largest file size that can be uploaded to your WordPress installation.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo esc_attr( size_format( wp_max_upload_size() ) ); ?></td>
				</tr>
				<tr>
					<td data-export-label="DOMDocument"><?php esc_attr_e( 'DOMDocument:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'DOMDocument is required for the Fusion Builder plugin to properly function.', 'Avada' ) . '">[?]</a>'; ?></td>
					<td><?php echo class_exists( 'DOMDocument' ) ? '<mark class="yes">&#10004;</mark>' : '<mark class="error">DOMDocument is not installed on your server, but is required if you need to use the Fusion Page Builder.</mark>'; ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Remote Get"><?php esc_attr_e( 'WP Remote Get:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Avada uses this method to communicate with different APIs, e.g. Google, Twitter, Facebook.', 'Avada' ) . '">[?]</a>'; ?></td>
					<?php $response = wp_safe_remote_get( 'https://build.envato.com/api/', array(
						'decompress' => false,
						'user-agent' => 'avada-remote-get-test',
					) ); ?>
					<td><?php echo ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) ? '<mark class="yes">&#10004;</mark>' : '<mark class="error">wp_remote_get() failed. Some theme features may not work. Please contact your hosting provider and make sure that https://build.envato.com/api/ is not blocked.</mark>'; ?></td>
				</tr>
				<tr>
					<td data-export-label="WP Remote Post"><?php esc_attr_e( 'WP Remote Post:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Avada uses this method to communicate with different APIs, e.g. Google, Twitter, Facebook.', 'Avada' ) . '">[?]</a>'; ?></td>
					<?php $response = wp_safe_remote_post( 'https://envato.com/', array(
						'decompress' => false,
						'user-agent' => 'avada-remote-get-test',
					) ); ?>
					<td><?php echo ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) ? '<mark class="yes">&#10004;</mark>' : '<mark class="error">wp_remote_post() failed. Some theme features may not work. Please contact your hosting provider and make sure that https://envato.com/ is not blocked.</mark>'; ?></td>
				</tr>
				<tr>
					<td data-export-label="GD Library"><?php esc_attr_e( 'GD Library:', 'Avada' ); ?></td>
					<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Avada uses this library to resize images and speed up your site\'s loading time', 'Avada' ) . '">[?]</a>'; ?></td>
					<td>
						<?php
						$info = esc_attr__( 'Not Installed', 'Avada' );
						if ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) {
							$info = esc_attr__( 'Installed', 'Avada' );
							$gd_info = gd_info();
							if ( isset( $gd_info['GD Version'] ) ) {
								$info = $gd_info['GD Version'];
							}
						}
						echo esc_attr( $info );
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<h3 class="screen-reader-text"><?php esc_attr_e( 'Active Plugins', 'Avada' ); ?></h3>
		<?php
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
		}
		?>
		<table class="widefat" cellspacing="0" id="status">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Active Plugins (<?php echo count( $active_plugins ); ?>)"><?php esc_attr_e( 'Active Plugins', 'Avada' ); ?> (<?php echo count( $active_plugins ); ?>)</th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach ( $active_plugins as $plugin ) {

					$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					$dirname        = dirname( $plugin );
					$version_string = '';
					$network_string = '';

					if ( ! empty( $plugin_data['Name'] ) ) {

						// Link the plugin name to the plugin url if available.
						if ( ! empty( $plugin_data['PluginURI'] ) ) {
							$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'Avada' ) . '">' . esc_html( $plugin_data['Name'] ) . '</a>';
						} else {
							$plugin_name = esc_html( $plugin_data['Name'] );
						}
						?>
						<tr>
							<td>
								<?php echo $plugin_name; // WPCS: XSS ok. ?>
							</td>
							<td class="help">&nbsp;</td>
							<td>
								<?php printf( esc_attr__( 'by %s', 'Avada' ), '<a href="' . esc_url( $plugin_data['AuthorURI'] ) . '" target="_blank">' . esc_html( $plugin_data['AuthorName'] ) . '</a>' ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; // WPCS: XSS ok. ?>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="avada-thanks">
		<hr />
		<p class="description"><?php esc_attr_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
	</div>
</div>

<?php if ( $show_400_migration && false === $force_hide_400_migration ) : ?>
	<script type="text/javascript">
		jQuery( '#avada-manual-400-migration-trigger' ).on( 'click', function( e ) {
			e.preventDefault();
			var migration_response = confirm( "<?php printf( esc_attr__( 'Warning: By clicking OK, all changes made to your theme options after installing Avada 4.0 will be lost. Your Theme Options will be reset to the values from %s and then converted again to 4.0.', 'Avada' ), esc_attr( $latest_version ) ); ?>" );
			if ( true == migration_response ) {
				window.location= "<?php echo esc_url_raw( admin_url( 'index.php?avada_update=1&ver=400&new=1' ) ); ?>";
			}
		});
	</script>
<?php endif; ?>

<?php if ( $show_500_migration ) : ?>
	<script type="text/javascript">
		if ( document.getElementById( 'avada-manual-500-migration-trigger' ) ) {
			jQuery( '#avada-manual-500-migration-trigger' ).on( 'click', function( e ) {
				e.preventDefault();
				var migration_response = confirm( "<?php esc_attr_e( 'Warning: By clicking OK, you will be redirected to the conversion splash screen, where you can restart the conversion of your page contents to the new Fusion Builder format.', 'Avada' ); ?>" );
				if ( migration_response == true ) {
					window.location= "<?php echo esc_url_raw( admin_url( 'index.php?fusion_builder_migrate=1&ver=500' ) ); ?>";
				}
			});
		}
		if ( document.getElementById( 'avada-manual-500-migration-revert-trigger' ) ) {
			jQuery( '#avada-manual-500-migration-revert-trigger' ).on( 'click', function( e ) {
				e.preventDefault();
				var migration_response = confirm( "<?php esc_attr_e( 'Warning: By clicking OK, you will be redirected to the conversion splash screen, where you can start the conversion reversion of your page contents to the old Fusion Builder format.', 'Avada' ); ?>" );
				if ( migration_response == true ) {
					window.location= "<?php echo esc_url_raw( admin_url( 'index.php?fusion_builder_migrate=1&ver=500&revert=1' ) ); ?>";
				}
			});
		}
		if ( document.getElementById( 'avada-remove-500-migration-backups' ) ) {
			jQuery( '#avada-remove-500-migration-backups' ).on( 'click', function( e ) {
				e.preventDefault();
				var migration_response = confirm( "<?php esc_attr_e( 'Warning: This is a non-reversable process. By clicking OK, all backups created during the 5.0 shortcode-conversion process will be removed from your database.', 'Avada' ); ?>" );
				if ( migration_response == true ) {
					window.location= "<?php echo esc_url_raw( admin_url( 'admin.php?page=avada-system-status&cleanup-500-backups=1' ) ); ?>";
				}
			});
		}
	</script>
<?php endif; ?>
