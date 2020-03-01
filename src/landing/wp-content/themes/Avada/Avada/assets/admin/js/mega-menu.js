/**
 * Handles the admin manipulation of the mega menu plugin.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      2.0.0
 */

( function( $ ) {

	"use strict";

	jQuery( document ).ready( function() {

		// Show or hide megamenu fields on parent and child list items.
		fusionMegamenu.menuItemMouseup();
		fusionMegamenu.megamenuStatusUpdate();
		fusionMegamenu.updateMegamenuFields();

		// Setup automatic thumbnail handling.
		jQuery( '#post-body' ).on( 'click', '.avada-remove-button', function( event ) {
			jQuery( this ).parents( '.fusion-upload-image' ).removeClass( 'fusion-image-set' );
			jQuery( this ).parents( '.fusion-upload-image' ).find( 'img' ).attr( 'src', '' );
			jQuery( this ).parents( '.fusion-upload-image' ).find( '.fusion-builder-upload-field' ).val( '' );
		});

		jQuery( '.fusion-megamenu-thumbnail-image' ).css( 'display', 'block' );
		jQuery( '.fusion-megamenu-thumbnail-image[src=""]' ).css( 'display', 'none' );

		// Setup new media uploader frame.
		fusionMediaFrameSetup();
	});

	// "Extending" wpNavMenu.
	var fusionMegamenu = {

		menuItemMouseup: function() {
			jQuery( document ).on( 'mouseup', '.menu-item-bar', function( event, ui ) {
				if ( ! jQuery( event.target ).is( 'a' ) ) {
					setTimeout( fusionMegamenu.updateMegamenuFields, 300 );
				}
			});
		},

		megamenuStatusUpdate: function() {

			jQuery( document ).on( 'click', '.edit-menu-item-megamenu-status a', function() {
				var parentLiItem = jQuery( this ).parents( '.menu-item:eq( 0 )' );

				if ( 'enabled' === jQuery( this ).parent().find( '.button-set-value' ).val() ) {
					parentLiItem.addClass( 'fusion-megamenu' );
				} else {
					parentLiItem.removeClass( 'fusion-megamenu' );
				}

				fusionMegamenu.updateMegamenuFields();
			});
		},

		megamenuFullwidthUpdate: function() {
			jQuery( document ).on( 'click', '.edit-menu-item-megamenu-width a', function() {
				var parentLiItem = jQuery( this ).parents( '.menu-item:eq( 0 )' );

				if ( 'fullwidth' === jQuery( this ).parent().find( '.button-set-value' ).val() ) {
					parentLiItem.addClass( 'fusion-megamenu-fullwidth' );
				} else {
					parentLiItem.removeClass( 'fusion-megamenu-fullwidth' );
				}

				fusionMegamenu.updateMegamenuFields();
			});
		},

		updateMegamenuFields: function() {
			var parentLiItem = $( '.menu-item' );

			parentLiItem.each( function( i ) {

				var megamenuStatus = jQuery( '.edit-menu-item-megamenu-status .button-set-value', this ),
					megamenuFullwidth = jQuery( '.edit-menu-item-megamenu-width .button-set-value', this ),
					checkAgainst;

				if ( ! jQuery( this ).is( '.menu-item-depth-0' ) ) {
					checkAgainst = parentLiItem.filter( ':eq(' + ( i - 1 ) + ')' );

					if ( checkAgainst.is( '.fusion-megamenu' ) ) {
						megamenuStatus.val( 'enabled' );
						jQuery( this ).addClass( 'fusion-megamenu' );
					} else {
						megamenuStatus.val( 'off' );
						jQuery( this ).removeClass( 'fusion-megamenu' );
					}

					if ( checkAgainst.is( '.fusion-megamenu-fullwidth' ) ) {
						megamenuFullwidth.val( 'fullwidth' );
						jQuery( this ).addClass( 'fusion-megamenu-fullwidth' );
					} else {
						megamenuFullwidth.val( 'off' );
						jQuery( this ).removeClass( 'fusion-megamenu-fullwidth' );
					}
				} else {
					if ( 'enabled' === megamenuStatus.val() ) {
						jQuery( this ).addClass( 'fusion-megamenu' );
					}

					if ( 'fullwidth' === megamenuFullwidth.val() ) {
						jQuery( this ).addClass( 'fusion-megamenu-fullwidth' );
					}
				}
			});
		}

	};

	function fusionMediaFrameSetup() {
		var fusionMediaFrame,
			itemId;

		jQuery( document.body ).on( 'click.fusionOpenMediaManager', '.button-upload', function( e ) {

			e.preventDefault();

			itemId = jQuery( this ).data( 'id' );

			if ( fusionMediaFrame ) {
				fusionMediaFrame.open();
				return;
			}

			fusionMediaFrame = wp.media.frames.fusionMediaFrame = wp.media({

				className: 'media-frame fusion-media-frame',
				frame: 'select',
				multiple: false,
				library: {
					type: 'image'
				}
			});

			fusionMediaFrame.on( 'select', function() {

				var mediaAttachment = fusionMediaFrame.state().get( 'selection' ).first().toJSON();

				jQuery( '#edit-menu-item-megamenu-' + itemId ).val( mediaAttachment.url );
				jQuery( '#fusion-media-img-' + itemId ).attr( 'src', mediaAttachment.url ).css( 'display', 'block' );
				jQuery( '#fusion-media-img-' + itemId ).parents( '.fusion-upload-image' ).addClass( 'fusion-image-set' );

			});

			fusionMediaFrame.open();
		});
	}
})( jQuery );
