<?php
/**
 * Welcome Admin page.
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
	<?php $this->get_admin_screens_header( 'welcome' ); ?>

	<!-- <p class="about-description"><span class="dashicons dashicons-admin-network avada-icon-key"></span><?php esc_html_e( 'Your Purchase Must Be Registered To Receive Theme Support & Auto Updates', 'Avada' ); ?></p> -->
	<div class="avada-welcome-tour">
		<iframe width="1120" height="630" src="https://www.youtube.com/embed/X92mpPz1COM?rel=0" frameborder="0" allowfullscreen></iframe>

		<div class="col three-col">

			<div class="col">
				<h3><?php esc_attr_e( 'Welcome To Avada', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'In 2012 we set out to make the perfect theme and Avada was born. Since then it has been the #1 selling theme with an ever growing user base of nearly 300,000 customers. We are thrilled you chose Avada and feel it will change your outlook on what a Wordpress theme can do.', 'Avada' ); ?></p>
			</div>

			<div class="col">
				<h3><?php esc_attr_e( 'Powerful Customization Tools', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'Avada includes an incredibly advanced options network. This network consists of Fusion Theme Options, Fusion Page Options and Fusion Builder. Together these tools, along with other included assets allow you to build professional websites without having to code.', 'Avada' ); ?></p>
			</div>

			<div class="col last-feature last">
				<h3><?php esc_attr_e( '5 Star Customer Support', 'Avada' ); ?></h3>
				<p><?php esc_attr_e( 'ThemeFusion understands that there can be no product success, without excellent customer support. We strive to always provide 5 star support and to treat you as we would want to be treated. This helps form a relationship between us that benefits all Avada customers.', 'Avada' ); ?></p>
			</div>

		</div>
	</div>
	<div class="avada-thanks">
		<p class="description"><?php esc_attr_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
	</div>
</div>
