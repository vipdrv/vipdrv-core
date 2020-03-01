<div class="wrap about-wrap">
	<h1><?php esc_html_e( 'FusionRedux Framework - A Community Effort', 'Avada' ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'We recognize we are nothing without our community. We would like to thank all of those who help FusionRedux to be what it is. Thank you for your involvement.', 'Avada' ); ?>
	</div>
	<div class="fusionredux-badge">
		<i class="el el-fusionredux"></i>
		<span>
			<?php printf( __( 'Version %s', 'Avada' ), esc_html(FusionReduxFramework::$_version )); ?>
		</span>
	</div>

	<?php $this->actions(); ?>
	<?php $this->tabs(); ?>

	<p class="about-description">
		<?php echo sprintf( __( 'FusionRedux is created by a community of developers world wide. Want to have your name listed too? <a href="%d" target="_blank">Contribute to FusionRedux</a>.', 'Avada' ), 'https://github.com/fusionreduxframework/fusionredux-framework/blob/master/CONTRIBUTING.md' );?>
	</p>

	<?php echo wp_kses_post($this->contributors()); ?>
</div>
