<?php

// If fusion-builder is bundled in another plugin/theme, registration isn't available, so exit early to avoid fatal errors.
if ( null == FusionBuilder()->registration ) {
	return;
}

$bundled_products = FusionBuilder()->registration->get_bundled();
// If fusion-builder is bundled in another plugin/theme, hide registration.
if ( isset( $bundled_products['fusionbuilder'] ) ) {
	return;
}
?>
<div class="feature-section">
	<div class="fusion-builder-important-notice">
		<p class="about-description"><?php esc_html_e( 'Thank you for choosing Fusion Builder! Your product must be registered to receive all the included demos and auto theme updates. The instructions below must be followed exactly.', 'fusion-builder' ); ?></p>
	</div>
	<?php FusionBuilder()->registration->the_form(); ?>
</div>
