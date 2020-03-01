<?php
/**
 * Plugins Admin page.
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

$plugins           = TGM_Plugin_Activation::$instance->plugins;
$installed_plugins = get_plugins();
$wp_api_plugins    = get_site_transient( 'fusion_wordpress_org_plugins' );

if ( ! function_exists( 'plugins_api' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // For plugins_api.
}
if ( ! $wp_api_plugins ) {
	$wp_org_plugins = array(
		'woocommerce'         => 'woocommerce/woocommerce.php',
		'bbpress'             => 'bbpress/bbpress.php',
		'the-events-calendar' => 'the-events-calendar/the-events-calendar.php',
		'contact-form-7'      => 'contact_form_7/wp-contact-form-7',
	);
	$wp_api_plugins = array();
	foreach ( $wp_org_plugins as $slug => $path ) {
		$wp_api_plugins[ $slug ] = array();
		$wp_api_plugins[ $slug ] = (array) plugins_api( 'plugin_information', array(
			'slug' => $slug,
		) );
	}
	set_site_transient( 'fusion_wordpress_org_plugins', $wp_api_plugins, 15 * MINUTE_IN_SECONDS );
}
?>
<div class="wrap about-wrap avada-wrap">
	<?php $this->get_admin_screens_header( 'plugins' ); ?>
	<?php add_thickbox(); ?>
	 <div class="avada-important-notice">
		<p class="about-description">
			<?php if ( false !== get_option( 'avada_previous_version' ) ) : ?>
				<?php printf( __( 'Fusion Core and Fusion Builder are required to use Avada. Fusion Builder can only be installed after Fusion Core is updated to version 3.0 or higher. Slider Revolution & Layer Slider are premium plugins that can be installed once your <a %1$s>product is registered</a>. The other plugins below offer design integration with Avada. You can manage the plugins from this tab. <a href="%2$s" target="_blank"> Subscribe to our newsletter</a> to be notified about new products coming in the future!', 'Avada' ), 'href="' . esc_url_raw( admin_url( 'admin.php?page=avada-registration' ) ) . '"', 'http://theme-fusion.us2.list-manage2.com/subscribe?u=4345c7e8c4f2826cc52bb84cd&id=af30829ace' ); // WPCS: XSS ok. ?>
			<?php else : ?>
				<?php printf( __( 'Fusion Core and Fusion Builder are required to use Avada. Slider Revolution & Layer Slider are premium plugins that can be installed once your <a %1$s>product is registered</a>. The other plugins below offer design integration with Avada. You can manage the plugins from this tab. <a href="%2$s" target="_blank">Subscribe to our newsletter</a> to be notified about new products coming in the future!', 'Avada' ), 'href="' . esc_url_raw( admin_url( 'admin.php?page=avada-registration' ) ) . '"', 'http://theme-fusion.us2.list-manage2.com/subscribe?u=4345c7e8c4f2826cc52bb84cd&id=af30829ace' ); // WPCS: XSS ok. ?>
			<?php endif; ?>
		</p>
	</div>
	<?php if ( ! Avada()->registration->is_registered() ) : ?>
		<div class="avada-important-notice" style="border-left: 4px solid #dc3232;">
			<h3 style="color: #dc3232; margin-top: 0;"><?php esc_attr_e( 'Premium Plugins Can Only Be Installed and Updated With A Valid Token Registration', 'Avada' ); ?></h3>
			<p><?php printf( esc_attr__( 'Please visit the %s page and enter a valid token to install or update the premium plugins; Slider Revolution and Layer Slider.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-registration' ) ) . '">' . esc_attr__( 'Product Registration', 'Avada' ) . '</a>' ); ?></p>
		</div>
	<?php endif; ?>
	<div id="avada-install-plugins" class="avada-demo-themes avada-install-plugins">
		<div class="feature-section theme-browser rendered">
			<?php $avada_registered_plugins = avada_get_required_and_recommened_plugins(); ?>
			<?php foreach ( $plugins as $plugin ) : ?>
				<?php
				if ( ! isset( $plugin['AuthorURI'] ) ) {
					$plugin['AuthorURI'] = '#';
				}
				if ( ! isset( $plugin['Author'] ) ) {
					$plugin['Author'] = '';
				}
				if ( ! array_key_exists( $plugin['slug'], $avada_registered_plugins ) ) {
					continue;
				}

				$class = '';
				$plugin_status = '';
				$file_path = $plugin['file_path'];
				$plugin_action = $this->plugin_link( $plugin );

				// We have a repo plugin.
				if ( ! $plugin['version'] ) {
					$plugin['version'] = TGM_Plugin_Activation::$instance->does_plugin_have_update( $plugin['slug'] );
				}

				if ( is_plugin_active( $file_path ) ) {
					$plugin_status = 'active';
					$class = 'active';
				}

				if ( isset( $plugin_action['update'] ) && $plugin_action['update'] ) {
					$class .= ' update';
				}

				?>
				<div class="fusion-admin-box">
					<div class="theme <?php echo esc_attr( $class ); ?>">
						<div class="theme-wrapper">
							<div class="theme-screenshot">
								<img src="<?php echo esc_url_raw( $plugin['image_url'] ); ?>" alt="" />
							</div>
							<?php if ( isset( $plugin_action['update'] ) && $plugin_action['update'] ) : ?>
								<div class="update-message notice inline notice-warning notice-alt">
									<p><?php printf( esc_attr__( 'New Version Available: %s', 'Avada' ), esc_attr( $plugin['version'] ) ); ?></p>
								</div>
							<?php endif; ?>
							<h3 class="theme-name">
								<?php if ( 'active' === $plugin_status ) : ?>
									<span><?php printf( esc_attr__( 'Active: %s', 'Avada' ), esc_attr( $plugin['name'] ) ); ?></span>
								<?php else : ?>
									<?php echo esc_attr( $plugin['name'] ); ?>
								<?php endif; ?>
								<div class="plugin-info">
									<?php if ( isset( $installed_plugins[ $plugin['file_path'] ] ) ) : ?>
										<?php printf( __( 'v%1$s | <a href="%2$s" target="_blank">%3$s</a>', 'Avada' ), esc_attr( $installed_plugins[ $plugin['file_path'] ]['Version'] ), esc_url_raw( $installed_plugins[ $plugin['file_path'] ]['AuthorURI'] ), esc_attr( $installed_plugins[ $plugin['file_path'] ]['Author'] ) ); // WPCS: XSS ok. ?>
									<?php elseif ( 'fusion-builder' === $plugin['slug'] || 'fusion-core' === $plugin['slug'] ) : ?>
										<?php printf( esc_attr__( 'Available Version: %s', 'Avada' ), esc_attr( $plugin['version'] ) ); // WPCS: XSS ok. ?>
									<?php else : ?>
										<?php
										$version = ( isset( $plugin['version'] ) ) ? $plugin['version'] : false;
										$version = ( isset( $wp_api_plugins[ $plugin['slug'] ] ) && isset( $wp_api_plugins[ $plugin['slug'] ]['version'] ) ) ? $wp_api_plugins[ $plugin['slug'] ]['version'] : $version;
										$author  = ( $plugin['Author'] && $plugin['AuthorURI'] ) ? "<a href='{$plugin['AuthorURI']}' target='_blank'>{$plugin['Author']}</a>" : false;
										$author  = ( isset( $wp_api_plugins[ $plugin['slug'] ] ) && isset( $wp_api_plugins[ $plugin['slug'] ]['author'] ) ) ? $wp_api_plugins[ $plugin['slug'] ]['author'] : $author;
										?>
										<?php if ( $version && $author ) : ?>
											<?php printf( __( 'v%1$s | %2$s', 'Avada' ), $version, $author ); // WPCS: XSS ok. ?>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</h3>
							<div class="theme-actions">
								<?php foreach ( $plugin_action as $action ) : ?>
									<?php
									// Sanitization is already taken care of in Avada_Admin class.
									// No need to re-sanitize it...
									echo $action; // WPCS: XSS ok.
									?>
								<?php endforeach; ?>
							</div>
							<?php if ( isset( $plugin['required'] ) && $plugin['required'] ) : ?>
								<div class="plugin-required">
									<?php esc_html_e( 'Required', 'Avada' ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="avada-thanks">
		<p class="description"><?php esc_html_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
	</div>
</div>
<div class="fusion-clearfix" style="clear: both;"></div>
<?php
