<?php
/**
 * Logo template.
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
$logo_opening_markup = '<div class="';
$logo_closing_markup = '</div>';
if ( 'v7' === Avada()->settings->get( 'header_layout' ) && ! Avada()->settings->get( 'logo_background' ) ) {
	$logo_opening_markup = '<li class="fusion-middle-logo-menu-logo ';
	$logo_closing_markup = '</li>';
} elseif ( 'v7' === Avada()->settings->get( 'header_layout' ) && Avada()->settings->get( 'logo_background' ) && 'Top' === Avada()->settings->get( 'header_position' ) ) {
	$logo_opening_markup = '<li class="fusion-logo-background fusion-middle-logo-menu-logo"><div class="';
	$logo_closing_markup = '</div></li>';
} elseif ( Avada()->settings->get( 'logo_background' ) && 'v4' !== Avada()->settings->get( 'header_layout' ) && 'v5' !== Avada()->settings->get( 'header_layout' ) && 'Top' === Avada()->settings->get( 'header_position' ) ) {
	$logo_opening_markup = '<div class="fusion-logo-background"><div class="';
	$logo_closing_markup = '</div></div>';
}

echo $logo_opening_markup; // WPCS: XSS ok. ?>fusion-logo" data-margin-top="<?php echo esc_attr( Avada()->settings->get( 'logo_margin', 'top' ) ); ?>" data-margin-bottom="<?php echo esc_attr( Avada()->settings->get( 'logo_margin', 'bottom' ) ); ?>" data-margin-left="<?php echo esc_attr( Avada()->settings->get( 'logo_margin', 'left' ) ); ?>" data-margin-right="<?php echo esc_attr( Avada()->settings->get( 'logo_margin', 'right' ) ); ?>">
	<?php
	/**
	 * The avada_logo_prepend hook.
	 */
	do_action( 'avada_logo_prepend' );
	?>
	<?php if ( ( Avada()->settings->get( 'logo', 'url' ) && '' !== Avada()->settings->get( 'logo', 'url' ) ) || ( Avada()->settings->get( 'logo_retina', 'url' ) && '' !== Avada()->settings->get( 'logo_retina', 'url' ) ) ) : ?>
		<a class="fusion-logo-link" href="<?php echo esc_url_raw( home_url( '/' ) ); ?>">
			<?php
			$logo_url = set_url_scheme( Avada()->settings->get( 'logo', 'url' ) );

			// Use retina logo, if default one is not set.
			if ( '' === $logo_url ) {
				$logo_url = set_url_scheme( Avada()->settings->get( 'logo_retina', 'url' ) );
				$logo_data = Avada()->images->get_logo_data( 'logo_retina' );
				$logo_data['style'] = '';
				if ( '' !== $logo_data['width'] ) {
					$logo_data['style'] = ' style="max-height:' . $logo_data['height'] . 'px;height:auto;"';
				}
			} else {
				$logo_data = Avada()->images->get_logo_data( 'logo' );
				$logo_data['style'] = '';
			}

			$logo_size['width']  = $logo_data['width'];
			$logo_size['height'] = $logo_data['height'];
			?>
			<img src="<?php echo esc_url_raw( $logo_url ); ?>" width="<?php echo esc_attr( $logo_size['width'] ); ?>" height="<?php echo esc_attr( $logo_size['height'] ); ?>"<?php echo $logo_data['style']; // WPCS: XSS ok. ?> alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Logo', 'Avada' ); ?>" class="fusion-logo-1x fusion-standard-logo" />

			<?php if ( Avada()->settings->get( 'logo_retina', 'url' ) && '' !== Avada()->settings->get( 'logo_retina', 'url' ) && '' !== Avada()->settings->get( 'logo_retina', 'width' ) && '' !== Avada()->settings->get( 'logo_retina', 'height' ) ) : ?>
				<?php $retina_logo = set_url_scheme( Avada()->settings->get( 'logo_retina', 'url' ) ); ?>
				<?php $style = 'style="max-height: ' . $logo_size['height'] . 'px; height: auto;"'; ?>
				<img src="<?php echo esc_url_raw( $retina_logo ); ?>" width="<?php echo esc_attr( $logo_size['width'] ); ?>" height="<?php echo esc_attr( $logo_size['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Retina Logo', 'Avada' ); ?>" <?php echo $style; // WPCS: XSS ok. ?> class="fusion-standard-logo fusion-logo-2x" />
			<?php else : ?>
				<img src="<?php echo esc_url_raw( $logo_url ); ?>" width="<?php echo esc_attr( $logo_size['width'] ); ?>" height="<?php echo esc_attr( $logo_size['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Retina Logo', 'Avada' ); ?>" class="fusion-standard-logo fusion-logo-2x" />
			<?php endif; ?>

			<!-- mobile logo -->
			<?php if ( Avada()->settings->get( 'mobile_logo', 'url' ) && '' !== Avada()->settings->get( 'mobile_logo', 'url' ) ) : ?>
				<?php $mobile_logo_data = Avada()->images->get_logo_data( 'mobile_logo' ); ?>
				<img src="<?php echo esc_url_raw( $mobile_logo_data['url'] ); ?>" width="<?php echo esc_attr( $mobile_logo_data['width'] ); ?>" height="<?php echo esc_attr( $mobile_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Mobile Logo', 'Avada' ); ?>" class="fusion-logo-1x fusion-mobile-logo-1x" />

				<?php if ( Avada()->settings->get( 'mobile_logo_retina', 'url' ) && '' != Avada()->settings->get( 'mobile_logo_retina', 'url' ) && '' != Avada()->settings->get( 'mobile_logo_retina', 'width' ) && '' != Avada()->settings->get( 'mobile_logo_retina', 'height' ) ) : ?>
					<?php
					$retina_mobile_logo_data = Avada()->images->get_logo_data( 'mobile_logo_retina' );
					$style = 'style="max-height: ' . $retina_mobile_logo_data['height'] . 'px; height: auto;"';
					?>
					<img src="<?php echo esc_url_raw( $retina_mobile_logo_data['url'] ); ?>" width="<?php echo esc_attr( $retina_mobile_logo_data['width'] ); ?>" height="<?php echo esc_attr( $retina_mobile_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Mobile Retina Logo', 'Avada' ); ?>" <?php echo $style; // WPCS: XSS ok. ?> class="fusion-logo-2x fusion-mobile-logo-2x" />
				<?php else : ?>
					<img src="<?php echo esc_url_raw( $mobile_logo_data['url'] ); ?>" width="<?php echo esc_attr( $mobile_logo_data['width'] ); ?>" height="<?php echo esc_attr( $mobile_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Mobile Retina Logo', 'Avada' ); ?>" class="fusion-logo-2x fusion-mobile-logo-2x" />
				<?php endif; ?>
			<?php endif; ?>

			<!-- sticky header logo -->
			<?php if ( Avada()->settings->get( 'sticky_header_logo', 'url' ) && '' !== Avada()->settings->get( 'sticky_header_logo', 'url' ) && ( in_array( Avada()->settings->get( 'header_layout' ), array( 'v1', 'v2', 'v3', 'v6', 'v7' ) ) || ( ( in_array( Avada()->settings->get( 'header_layout' ), array( 'v4', 'v5' ) ) && 'menu_and_logo' === Avada()->settings->get( 'header_sticky_type2_layout' ) ) ) ) ) : ?>
				<?php $sticky_logo_data = Avada()->images->get_logo_data( 'sticky_header_logo' ); ?>
				<img src="<?php echo esc_url_raw( $sticky_logo_data['url'] ); ?>" width="<?php echo esc_attr( $sticky_logo_data['width'] ); ?>" height="<?php echo esc_attr( $sticky_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Sticky Logo', 'Avada' ); ?>" class="fusion-logo-1x fusion-sticky-logo-1x" />

				<?php if ( Avada()->settings->get( 'sticky_header_logo_retina', 'url' ) && '' !== Avada()->settings->get( 'sticky_header_logo_retina', 'url' ) && '' !== Avada()->settings->get( 'sticky_header_logo_retina', 'width' ) && '' !== Avada()->settings->get( 'sticky_header_logo_retina', 'height' ) ) : ?>
					<?php
					$retina_sticky_logo_data = Avada()->images->get_logo_data( 'sticky_header_logo_retina' );
					$style = 'style="max-height: ' . $retina_sticky_logo_data['height'] . 'px; height: auto;"';
					?>
					<img src="<?php echo esc_url_raw( $retina_sticky_logo_data['url'] ); ?>" width="<?php echo esc_attr( $retina_sticky_logo_data['width'] ); ?>" height="<?php echo esc_attr( $retina_sticky_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Sticky Logo Retina', 'Avada' ); ?>" <?php echo $style; // WPCS: XSS ok. ?> class="fusion-logo-2x fusion-sticky-logo-2x" />
				<?php else : ?>
					<img src="<?php echo esc_url_raw( $sticky_logo_data['url'] ); ?>" width="<?php echo esc_attr( $sticky_logo_data['width'] ); ?>" height="<?php echo esc_attr( $sticky_logo_data['height'] ); ?>" alt="<?php bloginfo( 'name' ); ?> <?php esc_attr_e( 'Sticky Logo Retina', 'Avada' ); ?>" class="fusion-logo-2x fusion-sticky-logo-2x" />
				<?php endif; ?>
			<?php endif; ?>
		</a>
	<?php endif; ?>
	<?php
	/**
	 * The avada_logo_append hook.
	 *
	 * @hooked avada_header_content_3 - 10.
	 */
	do_action( 'avada_logo_append' );

	?>
<?php
echo $logo_closing_markup; // WPCS: XSS ok.

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
