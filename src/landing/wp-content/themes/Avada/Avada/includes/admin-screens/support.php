<?php
/**
 * Support Admin page.
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
	<?php $this->get_admin_screens_header( 'support' ); ?>
	<div class="avada-important-notice">
		<p class="about-description">
			<?php printf( __( 'Avada comes with 6 months of free support for every license you purchase. Support can be <a %1$s>extended through subscriptions</a> via ThemeForest. All support for Avada is handled through our support center on our company site. To access it, you must first setup an account by <a %2$s>following these steps</a>. If you purchased Avada through Envato\'s guest checkout <a %3$s>please view this link</a> to create an Envato account before receiving item support. Below are all the resources we offer in our support center and Avada community.', 'Avada' ), 'href="https://help.market.envato.com/hc/en-us/articles/207886473-Extending-and-Renewing-Item-Support" target="_blank"', 'href="https://theme-fusion.com/avada-doc/getting-started/avada-theme-support/" target="_blank"', 'href="https://help.market.envato.com/hc/en-us/articles/217397206-A-Guide-to-Using-Guest-Checkout" target="_blank"' ); // WPCS: XSS ok. ?>
		</p>
		<p><a href="https://theme-fusion.com/avada-doc/getting-started/avada-theme-support/" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_attr_e( 'Create A Support Account', 'Avada' ); ?></a></p>
	</div>
	<div class="avada-registration-steps">
		<div class="feature-section col three-col">
			<div class="col">
				<h3><span class="dashicons dashicons-sos"></span><?php esc_attr_e( 'Submit A Ticket', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'We offer excellent support through our advanced ticket system. Make sure to register your purchase first to access our support services and other resources.', 'Avada' ); ?></p>
				<a href="<?php echo esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'support-ticket/'; ?>" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Submit a ticket', 'Avada' ); ?></a>
			</div>
			<div class="col">
				<h3><span class="dashicons dashicons-book"></span><?php esc_attr_e( 'Documentation', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'This is the place to go to reference different aspects of the theme. Our online documentaiton is an incredible resource for learning the ins and outs of using Avada.', 'Avada' ); ?></p>
				<a href="<?php echo esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'support/documentation/avada-documentation/'; ?>" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Documentation', 'Avada' ); ?></a>
			</div>
			<div class="col last-feature">
				<h3><span class="dashicons dashicons-portfolio"></span><?php esc_attr_e( 'Knowledgebase', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'Our knowledgebase contains additional content that is not inside of our documentation. This information is more specific and unique to various versions or aspects of Avada.', 'Avada' ); ?></p>
				<a href="<?php echo esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'support/knowledgebase/'; ?>" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Knowledgebase', 'Avada' ); ?></a>
			</div>
			<div class="col">
				<h3><span class="dashicons dashicons-format-video"></span><?php esc_attr_e( 'Video Tutorials', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'Nothing is better than watching a video to learn. We have a growing library of high-definititon, narrated video tutorials to help teach you the different aspects of using Avada.', 'Avada' ); ?></p>
				<a href="<?php echo esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'support/video-tutorials/avada-videos/'; ?>" class="button button-large button-primary avada-large-button" target="_blank"><?php esc_html_e( 'Watch Videos', 'Avada' ); ?></a>
			</div>
			<div class="col">
				<h3><span class="dashicons dashicons-groups"></span><?php esc_attr_e( 'Community Forum', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'We also have a community forum for user to user interactions. Ask another Avada user! Please note that ThemeFusion does not provide product support here.', 'Avada' ); ?></p>
				<a href="<?php echo esc_url_raw( trailingslashit( $this->theme_fusion_url ) ) . 'forums/forum/avada-community-forum/'; ?>" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Community Forum', 'Avada' ); ?></a>
			</div>
			<div class="col last-feature">
				<h3><span class="dashicons dashicons-facebook"></span><?php esc_attr_e( 'Facebook Group', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'We have an amazing Facebook Group! Come and share with other Avada users and help grow our community. Please note, ThemeFusion does not provide support here.', 'Avada' ); ?></p>
				<a href="https://www.facebook.com/groups/AvadaUsers/" class="button button-large button-primary avada-large-button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook Group', 'Avada' ); ?></a>
			</div>
		</div>
		<?php do_action( 'avada_admin_pages_support_after_list' ); ?>
	</div>
	<div class="avada-thanks">
		<p class="description"><?php esc_attr_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
	</div>
</div>
