<?php
/**
 * The main Patcher class.
 *
 * @package Fusion-Library
 * @subpackage Fusion-Patcher
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The admin screen class for teh patcher.
 *
 * @since 1.0.0
 */
class Fusion_Patcher_Admin_Screen {

	/**
	 * Whether or not we've already added the menu.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $menu_added = array();

	/**
	 * An array of printed forms.
	 *
	 * @static
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected static $printed_forms = array();

	/**
	 * An instance of the Fusion_Patcher class.
	 *
	 * @access private
	 * @since 1.0.0
	 * @var array
	 */
	private $patcher = array();

	/**
	 * The patches.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @var array
	 */
	protected $patches = array();

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param object $patcher The Fusion_Patcher instance.
	 */
	public function __construct( $patcher ) {

		// Set the $patcher property.
		$this->patcher = $patcher;

		// If the product is bundled, early exit.
		$is_bundled = $this->patcher->is_bundled();
		if ( $is_bundled ) {
			return;
		}

		// Get the patches when we're in the patcher page.
		$args = $this->patcher->get_args();
		if ( isset( $args['is_patcher_page'] ) && true === $args['is_patcher_page'] ) {
			$this->patches = Fusion_Patcher_Client::get_patches( $this->patcher->get_args() );
		}

		// Add menu page.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// Call register settings function.
		add_action( 'admin_init', array( $this, 'settings' ) );

		add_action( 'admin_init', array( $this, 'init' ), 999 );

		add_filter( 'whitelist_options', array( $this, 'whitelist_options' ) );

		add_filter( 'custom_menu_order', array( $this, 'reorder_submenus' ) );

	}

	/**
	 * Additional actions.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function init() {

		$is_patcher_page = $this->patcher->get_args( 'is_patcher_page' );
		if ( null === $is_patcher_page || false === $is_patcher_page ) {
			return;
		}

		// Set the $patches property.
		$bundles = $this->patcher->get_args( 'bundled' );
		if ( ! $bundles ) {
			$bundles = array();
		}
		foreach ( $bundles as $bundle ) {
			$instance = $this->patcher->get_instance( $bundle );
			if ( is_object( $instance ) ) {
				$bundle_patches = Fusion_Patcher_Client::get_patches( $instance->get_args() );
				foreach ( $bundle_patches as $key => $value ) {
					if ( ! isset( $this->patches[ $key ] ) ) {
						$this->patches[ $key ] = $value;
					}
				}
			}
		}
		// Add the patcher to the support screen.
		add_action( 'fusion_admin_pages_patcher', array( $this, 'form' ) );

	}


	/**
	 * Adds a submenu page.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function admin_menu() {

		if ( isset( self::$menu_added[ $this->patcher->get_args( 'context' ) ] ) && self::$menu_added[ $this->patcher->get_args( 'context' ) ] ) {
			return;
		}

		add_submenu_page(
			$this->patcher->get_args( 'parent_slug' ),
			$this->patcher->get_args( 'page_title' ),
			$this->patcher->get_args( 'menu_title' ),
			'manage_options',
			$this->patcher->get_args( 'context' ) . '-fusion-patcher',
			array( $this, 'admin_page' )
		);
		self::$menu_added[ $this->patcher->get_args( 'context' ) ] = true;

	}

	/**
	 * Reorders the Avada/FB submenu page.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function reorder_submenus() {
		global $submenu;

		if ( isset( $submenu['avada'] ) && isset( $submenu['avada'][8] ) ) {
			$theme_options_entry = $submenu['avada'][7];
			$patcher_entry = $submenu['avada'][8];

			$submenu['avada'][7] = $patcher_entry;
			$submenu['avada'][8] = $theme_options_entry;
		}
	}

	/**
	 * The admin-page contents.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function admin_page() {
		?>
		<div class="wrap fusion-wrap">
			<?php
			/**
			 * Make sure that any patches marked as manually applied
			 * using the FUSION_MANUALLY_APPLIED_PATCHES constant are marked as complete.
			 */
			$this->manually_applied_patches();
			?>

			<?php
			/**
			 * Adds the content of the form.
			 */
			do_action( 'fusion_admin_pages_patcher' );
			?>
			<?php
			/**
			 * Add the footer content.
			 */
			$this->footer_content();
			?>
		</div>
		<?php
	}

	/**
	 * Register the settings.
	 *
	 * @access public
	 * @return void
	 */
	public function settings() {

		if ( empty( $this->patches ) ) {
			return;
		}
		// Register settings for the patch contents.
		foreach ( $this->patches as $key => $value ) {
			register_setting( 'fusion_patcher_' . $key, 'fusion_patch_contents_' . $key );
		}
	}

	/**
	 * The page contents.
	 *
	 * @access public
	 * @return void
	 */
	public function form() {

		if ( isset( self::$printed_forms[ $this->patcher->get_args( 'context' ) ] ) ) {
			return;
		}

		// Determine if there are available patches, and build an array of them.
		$available_patches = array();
		foreach ( $this->patches as $patch_id => $patch_args ) {
			if ( ! isset( $patch_args['patch'] ) ) {
				continue;
			}
			foreach ( $patch_args['patch'] as $key => $unique_patch_args ) {
				// Make sure the context is right.
				if ( $this->patcher->get_args( 'context' ) === $unique_patch_args['context'] ) {
					// Make sure the version is right.
					if ( $this->patcher->get_args( 'version' ) === $unique_patch_args['version'] ) {
						$available_patches[] = $patch_id;
						$context[ $this->patcher->get_args( 'context' ) ] = true;
					}
				}
				// Check for bundled products.
				$bundles = $this->patcher->get_args( 'bundled' );
				if ( ! $bundles ) {
					$bundles = array();
				}
				foreach ( $bundles as $bundle ) {
					$instance = $this->patcher->get_instance( $bundle );
					if ( is_object( $instance ) ) {
						// Make sure the context is right.
						if ( $instance->get_args( 'context' ) === $unique_patch_args['context'] ) {
							// Make sure the version is right.
							if ( $instance->get_args( 'version' ) === $unique_patch_args['version'] ) {
								$available_patches[] = $patch_id;
								$context[ $instance->get_args( 'context' ) ] = true;
							}
						}
					}
				}
			}
		}
		// Make sure we have a unique array.
		$available_patches = array_unique( $available_patches );
		// Sort the array by value and re-index the keys.
		sort( $available_patches );

		// Get an array of the already applied patches.
		$applied_patches = get_site_option( 'fusion_applied_patches', array() );

		// Get an array of patches that failed to be applied.
		$failed_patches = get_site_option( 'fusion_failed_patches', array() );

		// Get the array of messages to display.
		$messages = Fusion_Patcher_Admin_Notices::get_messages();
		?>
		<div class="wrap about-wrap fusion-library-wrap">
			<div class="fusion-important-notice fusion-auto-patcher">

				<div class="fusion-patcher-heading">
					<p class="description">
						<?php if ( empty( $available_patches ) ) : ?>
							<?php printf( esc_html__( 'Fusion Patcher: Currently there are no patches available for %1$s version %2$s', 'fusion-builder' ), esc_attr( $this->patcher->get_args( 'name' ) ), esc_attr( $this->patcher->get_args( 'version' ) ) ); ?>
						<?php else : ?>
							<?php printf( esc_html__( 'Fusion Patcher: The following patches are available for %1$s version %2$s', 'fusion-builder' ), esc_attr( $this->patcher->get_args( 'name' ) ), esc_attr( $this->patcher->get_args( 'version' ) ) ); ?>
						<?php endif; ?>
						<span class="fusion-auto-patcher learn-more"><a href="https://theme-fusion.com/avada-doc/avada-patcher/" target="_blank" rel="noopener noreferrer"><?php esc_attr_e( 'Learn More', 'fusion-builder' ); ?></a></span>
					</p>
					<?php if ( ! empty( $available_patches ) ) : ?>
						<p class="sub-description">
							<?php esc_html_e( 'The status column displays if a patch was applied. However, a patch can be reapplied if necessary.', 'fusion-builder' ); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $messages ) ) : ?>
					<?php foreach ( $messages as $message_id => $message ) : ?>
						<?php if ( false !== strpos( $message_id, 'write-permissions-' ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<p class="fusion-patcher-error"><?php echo $message;  // WPCS: XSS ok. ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! empty( $available_patches ) ) : // Only display the table if we have patches to apply. ?>
					<table class="fusion-patcher-table">
						<tbody>
							<tr class="fusion-patcher-headings">
								<th style="min-width:6em;"><?php esc_attr_e( 'Patch #', 'fusion-builder' ); ?></th>
								<th>
									<?php if ( ! empty( $bundles ) ) : ?>
										<?php esc_attr_e( 'Product', 'fusion-builder' ); ?>
									<?php else : ?>
										<?php esc_attr_e( 'Issue Date', 'fusion-builder' ); ?>
									<?php endif; ?>
								</th>
								<th><?php esc_attr_e( 'Description', 'fusion-builder' ); ?></th>
								<th><?php esc_attr_e( 'Status', 'fusion-builder' ); ?></th>
								<th></th>
							</tr>
							</tr>
							<?php foreach ( $available_patches as $key => $patch_id ) :

								// Do not allow applying the patch initially.
								// We'll have to check if they can later.
								$can_apply = false;

								// Make sure the patch exists.
								if ( ! array_key_exists( $patch_id, $this->patches ) ) {
									continue;
								}

								// Get the patch arguments.
								$patch_args = $this->patches[ $patch_id ];

								// Has the patch been applied?
								$patch_applied = ( in_array( $patch_id, $applied_patches, true ) );

								// Has the patch failed?
								$patch_failed = ( in_array( $patch_id, $failed_patches, true ) );

								// If there is no previous patch, we can apply it.
								if ( ! isset( $available_patches[ $key - 1 ] ) ) {
									$can_apply = true;
								}

								// If the previous patch exists and has already been applied,
								// then we can apply this one.
								if ( isset( $available_patches[ $key - 1 ] ) ) {
									if ( in_array( $available_patches[ $key - 1 ], $applied_patches, true ) ) {
										$can_apply = true;
									}
								}
								?>

								<tr class="fusion-patcher-table-head">
									<td class="patch-id">
										#<?php echo intval( $patch_id ); ?>
										<?php if ( ! empty( $bundles ) ) : ?>
											<div style="font-size:10px;color:#999;">
												<?php echo esc_attr( $patch_args['date'][0] ); ?>
											</div>
										<?php endif; ?>
									</td>
									<?php if ( ! empty( $bundles ) ) : ?>
										<?php
										// Splitting to multiple lines for PHP 5.2 compatibility.
										$product_name = str_replace( array( '-', '_' ), ' ', $patch_args['patch'][0]['context'] );
										$product_name = ucwords( $product_name );
										?>
										<td class="patch-product"><?php echo esc_attr( $product_name ); ?></td>
									<?php else : ?>
										<td class="patch-date"><?php echo esc_attr( $patch_args['date'][0] ); ?></td>
									<?php endif; ?>
									<td class="patch-description">
										<?php if ( isset( $messages[ 'write-permissions-' . $patch_id ] ) ) : ?>
											<div class="fusion-patcher-error" style="font-size:.85rem;">
												<?php echo $messages[ 'write-permissions-' . $patch_id ]; // WPCS: XSS ok. ?>
											</div>
										<?php endif; ?>
										<?php echo $patch_args['description'][0]; // WPCS: XSS ok. ?>
									</td>
									<td class="patch-status">
										<?php if ( $patch_failed ) : ?>
											<span style="color:#E53935;" class="dashicons dashicons-no"></span>
										<?php elseif ( $patch_applied ) : ?>
											<span style="color:#4CAF50;" class="dashicons dashicons-yes"></span>
										<?php endif; ?>
									</td>
									<td class="patch-apply">
										<?php if ( $can_apply ) : ?>
											<form method="post" action="options.php">
												<?php settings_fields( 'fusion_patcher_' . $patch_id ); ?>
												<?php do_settings_sections( 'fusion_patcher_' . $patch_id ); ?>
												<input type="hidden" name="fusion_patch_contents_<?php echo intval( $patch_id ); ?>" value="<?php echo esc_html( $this->format_patch( $patch_args ) ); ?>" />
												<?php if ( $patch_applied ) : ?>
													<?php submit_button( esc_attr__( 'Patch Applied', 'fusion-builder' ) ); ?>
												<?php else : ?>
													<?php submit_button( esc_attr__( 'Apply Patch', 'fusion-builder' ) ); ?>
													<?php if ( $patch_failed ) : ?>
														<?php $dismiss_url = 'admin.php?page=' . $this->patcher->get_args( 'context' ) . '-fusion-patcher&manually-applied-patch=' . $patch_id; ?>
														<?php $dismiss_url = admin_url( $dismiss_url ); ?>
														<a class="button" style="margin-top:10px;font-size:11px;color:#b71c1c;display:block;" href="<?php echo esc_url_raw( $dismiss_url ); ?>"><?php esc_attr_e( 'Dismiss Notices', 'fusion-builder' ); ?></a>
													<?php endif; ?>
												<?php endif; ?>
											</form>
										<?php else : ?>
											<span class="button disabled button-small">
												<?php if ( isset( $available_patches[ $key - 1 ] ) ) : ?>
													<?php printf( esc_html__( 'Please apply patch #%s first.', 'fusion-builder' ), intval( $available_patches[ $key - 1 ] ) ); ?>
												<?php else : ?>
													<?php esc_html_e( 'Patch cannot be currently aplied.', 'fusion-builder' ); ?>
												<?php endif; ?>
											</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		</div>
		<?php
		self::$printed_forms[ $this->patcher->get_args( 'context' ) ] = true;
		// Delete some messages.
		Fusion_Patcher_Admin_Notices::remove_messages_option();
	}

	/**
	 * Format the patch.
	 * We're encoding everything here for security reasons.
	 * We're also going to check the current versions of Avada & Fusion-Core,
	 * and then build the hash for this patch using the files that are needed.
	 *
	 * @since 4.0.0
	 * @access private
	 * @param array $patch The patch array.
	 * @return string
	 */
	private function format_patch( $patch ) {
		$patches = array();
		if ( ! isset( $patch['patch'] ) ) {
			return;
		}
		foreach ( $patch['patch'] as $key => $args ) {
			if ( ! isset( $args['context'] ) || ! isset( $args['path'] ) || ! isset( $args['reference'] ) ) {
				continue;
			}
			$valid_contexts   = array();
			$valid_contexts[] = $this->patcher->get_args( 'context' );
			$bundled          = $this->patcher->get_args( 'bundled' );
			if ( ! empty( $bundled ) ) {
				foreach ( $bundled as $product ) {
					$valid_contexts[] = $product;
				}
			}
			foreach ( $valid_contexts as $context ) {
				if ( $context === $args['context'] ) {
					$patcher_instance = $this->patcher->get_instance( $context );
					if ( null === $patcher_instance ) {
						continue;
					}
					$v1 = Fusion_Helper::normalize_version( $patcher_instance->get_args( 'version' ) );
					$v2 = Fusion_Helper::normalize_version( $args['version'] );
					if ( version_compare( $v1, $v2, '==' ) ) {
						$patches[ $context ][ $args['path'] ] = $args['reference'];
					}
				}
			}
		}
		return base64_encode( wp_json_encode( $patches ) );
	}

	/**
	 * Make sure manually applied patches show as successful.
	 *
	 * @access private
	 * @since 5.0.3
	 */
	private function manually_applied_patches() {

		$manual_patches_found = '';
		if ( isset( $_GET['manually-applied-patch'] ) ) {
			$manual_patches_found = sanitize_text_field( wp_unslash( $_GET['manually-applied-patch'] ) );
		}

		if ( defined( 'FUSION_MANUALLY_APPLIED_PATCHES' ) ) {
			$manual_patches_found = FUSION_MANUALLY_APPLIED_PATCHES . ',' . $manual_patches_found;
		}
		if ( empty( $manual_patches_found ) ) {
			return;
		}
		$messages_option = get_site_option( Fusion_Patcher_Admin_Notices::$option_name );
		$manual_patches  = explode( ',', $manual_patches_found );
		$applied_patches = get_site_option( 'fusion_applied_patches', array() );
		$failed_patches  = get_site_option( 'fusion_failed_patches', array() );

		foreach ( $manual_patches as $patch ) {
			$patch = (int) trim( $patch );

			// Update the applied-patches option.
			if ( ! in_array( $patch, $applied_patches, true ) ) {
				$applied_patches[] = $patch;
				update_site_option( 'fusion_applied_patches', $applied_patches );
			}

			// If the patch is in the array of failed patches, remove it.
			if ( in_array( $patch, $failed_patches, true ) ) {
				$failed_key = array_search( $patch, $failed_patches, true );
				unset( $failed_patches[ $failed_key ] );
				update_site_option( 'fusion_failed_patches', $failed_patches );
			}

			// Remove messages if they exist.
			if ( isset( $this->patches[ $patch ] ) ) {
				foreach ( $this->patches[ $patch ]['patch'] as $args ) {
					$message_id = 'write-permissions-' . $patch;
					if ( isset( $messages_option[ $message_id ] ) ) {
						unset( $messages_option[ $message_id ] );
						update_site_option( Fusion_Patcher_Admin_Notices::$option_name, $messages_option );
					}
				}
			}
		}
	}

	/**
	 * Footer content.
	 *
	 * @access protected
	 * @since 1.0.0
	 */
	protected function footer_content() {
	}

	/**
	 * Whitelist options.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $options The whitelisted options.
	 * @return array
	 */
	public function whitelist_options( $options ) {

		$added = array();
		// Register settings for the patch contents.
		foreach ( $this->patches as $key => $value ) {
			$added[ 'fusion_patcher_' . $key ] = array(
				'fusion_patch_contents_' . $key,
			);
		}
		$options = add_option_whitelist( $added, $options );
		return $options;
	}
}
