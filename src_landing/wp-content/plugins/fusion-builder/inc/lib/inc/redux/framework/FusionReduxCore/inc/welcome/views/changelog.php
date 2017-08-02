<div class="wrap about-wrap">
	<h1><?php esc_html_e( 'FusionRedux Framework - Changelog', 'fusion-builder' ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'Our core mantra at FusionRedux is backwards compatibility. With hundreds of thousands of instances worldwide, you can be assured that we will take care of you and your clients.', 'fusion-builder' ); ?>
	</div>
	<div class="fusionredux-badge">
		<i class="el el-fusionredux"></i>
		<span>
			<?php printf( __( 'Version %s', 'fusion-builder' ), esc_html(FusionReduxFramework::$_version) ); ?>
		</span>
	</div>

	<?php $this->actions(); ?>
	<?php $this->tabs(); ?>

	<div class="changelog">
		<div class="feature-section">
			<?php echo wp_kses_post($this->parse_readme()); ?>
		</div>
	</div>

</div>
