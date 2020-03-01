jQuery( document ).ready( function() {

	var avadaMenu;

	// Hide the menu from Appearance.
	jQuery( '#menu-appearance a[href="themes.php?page=avada_options"]' ).css( 'display', 'none' );

	// Activate the Avada admin menu theme option entry when theme options are active
	if ( jQuery( 'a[href="themes.php?page=avada_options"]' ).hasClass( 'current' ) ) {
		avadaMenu = jQuery( '#toplevel_page_avada' );

		avadaMenu.addClass( 'wp-has-current-submenu wp-menu-open' );
		avadaMenu.children( 'a' ).addClass( 'wp-has-current-submenu wp-menu-open' );
		avadaMenu.children( '.wp-submenu' ).find( 'a[href="themes.php?page=avada_options"]' ).parent().addClass( 'current' );
		avadaMenu.children( '.wp-submenu' ).find( 'a[href="themes.php?page=avada_options"]' ).addClass( 'current' );

		// Do not show the appearance menu as active
		jQuery( '#menu-appearance a[href="themes.php"]' ).removeClass( 'wp-has-current-submenu wp-menu-open' );
		jQuery( '#menu-appearance' ).removeClass( 'wp-has-current-submenu wp-menu-open' );
		jQuery( '#menu-appearance' ).addClass( 'wp-not-current-submenu' );
		jQuery( '#menu-appearance a[href="themes.php"]' ).addClass( 'wp-not-current-submenu' );
		jQuery( '#menu-appearance' ).children( '.wp-submenu' ).find( 'li' ).removeClass( 'current' );
	}
} );
