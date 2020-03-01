var $avadaVersion;
window.$versionSuffix = ' rc1';

if ( window.jQuery ) {
	jQuery( document ).ready( function( e ) {

		// Main logos
		jQuery( '.avada-logo .avada-version' ).text( jQuery( '.avada-logo .avada-version' ).text() + window.$versionSuffix );
		jQuery( '.avada-logo .fusion-builder-version' ).text( jQuery( '.avada-logo .fusion-builder-version' ).text() + window.$versionSuffix );

		// TO.
		jQuery( '.fusionredux-sidebar h2 span' ).text( jQuery( '.fusionredux-sidebar h2 span' ).text() + window.$versionSuffix );

		// Avada Plugins page
		jQuery( '.avada-install-plugins' ).find( '.fusion-admin-box:nth-child(1), .fusion-admin-box:nth-child(2)' ).each( function() {
			var $versionContainer = jQuery( this ).find( '.plugin-info' ),
				$html = $versionContainer.html().replace( '|', window.$versionSuffix + ' |' );

			$versionContainer.html( $html );
		});

		// WP Plugins page
		jQuery( 'table.plugins #the-list' ).find( '[data-slug="fusion-core"], [data-slug="fusion-builder"]' ).each( function() {
			var $versionContainer = jQuery( this ).find( '.plugin-version-author-uri' ),
				$html = $versionContainer.html().replace( '| By', window.$versionSuffix + ' | By' );

			$versionContainer.html( $html );
		});

	});
} else {

	// Splash Screens
	$avadaVersion = document.getElementsByClassName( 'avada-version-inner' );
	$avadaVersion['0'].textContent = $avadaVersion['0'].textContent + window.$versionSuffix;
}
