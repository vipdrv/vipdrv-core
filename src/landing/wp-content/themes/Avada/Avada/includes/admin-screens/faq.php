<?php
/**
 * FAQ Admin page.
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
	<?php $this->get_admin_screens_header( 'faqs' ); ?>
	<div class="avada-important-notice">
		<p class="about-description">
			<?php printf( esc_attr__( 'These are general frequently asked questions to help you get started. For more in-depth documentation, please visit our %s to view documentation, knowledgebase and video tutorials.', 'Avada' ), '<a href="https://theme-fusion.com/support/" target="_blank">' . esc_attr__( 'online support center', 'Avada' ) . '</a>' ); ?>
		</p>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'How Do I Register My Avada Purchase?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php printf( esc_attr__( 'Your Avada purchase requires product registration to receive the Avada demos, Slider Revolution, Layer Slider and automatic theme updates. You can easily register your product on the %s tab.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-registration' ) ) . '">' . esc_attr__( 'Product Registration', 'Avada' ) . '</a>' ); ?><br/><br/>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'How Do I Get Support For Avada?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php esc_attr_e( 'All support is handled through Avada\'s support center. First you create an account on our website which gives you access to our support center. Our support center includes online documentation, video tutorials and a hands on ticket system. Our team of experts will gladly help answer questions you may have. Please see the links below.', 'Avada' ); ?>
			<ul>
				<li><a href="https://theme-fusion.com/avada-doc/getting-started/avada-theme-support/" target="_blank"><?php esc_attr_e( 'Sign up at our support center with these steps', 'Avada' ); ?></a></li>
				<li><a href="https://theme-fusion.com/support-ticket/" target="_blank"><?php esc_attr_e( 'Submit a ticket to our team', 'Avada' ); ?></a></li>
				<li><a href="https://theme-fusion.com/support/documentation/avada-documentation/" target="_blank"><?php esc_attr_e( 'View Avada Documentation', 'Avada' ); ?></a></li>
				<li><a href="https://theme-fusion.com/support/video-tutorials/avada-videos/" target="_blank"><?php esc_attr_e( 'View Avada Video Tutorials', 'Avada' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'How Do I Use The Avada Options Network?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php esc_attr_e( 'Avada\'s Option Network consists of Fusion Theme Options, Fusion Page Options and Fusion Builder Options. This powerful network of options allows you to build professional sites without coding knowledge. Please see the link below to learn how these work together.', 'Avada' ); ?>
			<ul>
				<li><a href="https://theme-fusion.com/avada-doc/options/how-options-work/" target="_blank"><?php esc_attr_e( 'How To Use The Avada Option Network', 'Avada' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'What Are The Required & Recommended Plugins For Using Avada?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php printf( esc_attr__( 'Avada can be used by itself without any additional plugins it includes. However, to utilize all the features Avada offers, Fusion Core and Fusion Builder plugins must be installed and activated. They are considered required plugins. The recommended plugins are either premium plugins we bundle with Avada (Slider Revolution / Layer Slider) or free plugins that we offer design integration for (WooCommerce or The Events Calendar). All of these can be installed on the %s tab.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-plugins' ) ) . '">Plugins</a>' ); ?><br/><br/>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'How Do I Import The Avada Demos?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php printf( esc_attr__( 'Avada Demos can be fully imported with the same setup you see on our live demos; or you can import single pages through Fusion Builder. To import a full demo, simply visit the %s tab and select a demo. To import a single page from an Avada Demo, create a new page (make sure Fusion Builder is active) and click the "Library" tab. A window will open allowing you to select the "Demos" tab. Choose a demo from the dropdown field and the pages for that demo will load, allowing you to import the single demo page of your choice.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-demos' ) ) . '">Import Demos</a>' ); ?>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'Where Can I Find More Information About How To Use Avada?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php esc_attr_e( 'Avada has a complete set of documentation and growing video tutorial library. Both are stored on our company site in the support center, see the links below.', 'Avada' ); ?>
			<ul>
				<li><a href="https://theme-fusion.com/support/documentation/avada-documentation/" target="_blank"><?php esc_attr_e( 'Avada Documentation', 'Avada' ); ?></a></li>
				<li><a href="https://theme-fusion.com/support/video-tutorials/avada-videos/" target="_blank"><?php esc_attr_e( 'Avada Video Tutorials', 'Avada' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'What Are The Requirements For Using Avada?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php esc_attr_e( 'Avada\'s requirements can be found in our support center at the link below.', 'Avada' ); ?>
			<ul>
				<li><a href="https://theme-fusion.com/avada-doc/getting-started/requirements-for-avada/" target="_blank"><?php esc_attr_e( 'Requirements For Using Avada', 'Avada' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="avada-admin-toggle">
		<div class="avada-admin-toggle-heading">
			<h3><?php esc_attr_e( 'What Is The System Status Tab For?', 'Avada' ); ?></h3>
			<span class="avada-admin-toggle-icon dashicons dashicons-plus"></span>
		</div>
		<div class="avada-admin-toggle-content">
			<?php printf( esc_attr__( 'The %s tab contains a collection of relevant data that will help you debug your website more efficiently. In this tab, you can also generate a System Report, which you can include in your support tickets to help our support team find solutions for your issues much faster. This tab is divided into three sections; the WordPress Environment section, the Server Environment section, and the Active Plugins section. Please see the relevant links below about the System Status tab.', 'Avada' ), '<a href="' . esc_url_raw( admin_url( 'admin.php?page=avada-system-status' ) ) . '" target="_blank">System Status</a>' ); ?>
			<ul>
				<li><a href="https://theme-fusion.com/avada-doc/special-features/system-status/" target="_blank"><?php esc_attr_e( 'System Status General Information', 'Avada' ); ?></a></li>
				<li><a href="https://theme-fusion.com/avada-doc/special-features/system-status-limits/" target="_blank"><?php esc_attr_e( 'System Status Limits', 'Avada' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="avada-thanks">
		<p class="description"><?php esc_attr_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></p>
	</div>
</div>

<script type="text/javascript">
jQuery( '.avada-admin-toggle-heading' ).on( 'click', function() {
	jQuery( this ).parent().find( '.avada-admin-toggle-content' ).slideToggle( 300 );

	if ( jQuery( this ).find( '.avada-admin-toggle-icon' ).hasClass( 'dashicons-plus' ) ) {
		jQuery( this ).find( '.avada-admin-toggle-icon' ).removeClass( 'dashicons-plus' ).addClass( 'dashicons-minus' );
	} else {
		jQuery( this ).find( '.avada-admin-toggle-icon' ).removeClass( 'dashicons-minus' ).addClass( 'dashicons-plus' );
	}

});
</script>
