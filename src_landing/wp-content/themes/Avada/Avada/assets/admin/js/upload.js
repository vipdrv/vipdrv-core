jQuery( document ).ready( function( $ ) {
	var id;

	if ( jQuery( '.fusion_upload_button' ).length ) {
		window.avadaUploadfield = '';
		window.mediaUploader;

		jQuery( '.fusion_upload_button' ).on( 'click', function() {
			window.avadaUploadfield = jQuery( '.upload_field', jQuery( this ).parent() );

			if ( window.mediaUploader ) {
				window.mediaUploader.open();
				return;
			}

			// Extend the wp.media object
			window.mediaUploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				}, multiple: false
			});

			window.mediaUploader.on( 'select', function() {
				var attachment = window.mediaUploader.state().get( 'selection' ).first().toJSON();
				window.sendToEditor( attachment );
			});

			// Open the uploader dialog
			window.mediaUploader.open();

			return false;
		});

		window.avadaSendToEditorBackup = window.sendToEditor;
		window.sendToEditor = function( attachment ) {
			var imageUrl = '',
				imageId = '',
				imageAlt = '',
				imageWidth = '',
				imageHeight = '',
				featuredImageWrapper = jQuery( window.avadaUploadfield ).parents( '.fusion-featured-image-meta-box' );
			if ( window.avadaUploadfield ) {
				if ( 0 < attachment.url.length ) {
					imageUrl = attachment.url;
					imageId = attachment.id;
					imageAlt = attachment.alt;
					imageWidth = attachment.width;
					imageHeight = attachment.height;
				}



				if ( featuredImageWrapper.length ) {
					featuredImageWrapper.find( '.fusion-preview-image' ).attr({
						'src': imageUrl,
						'alt': imageAlt,
						'width': imageWidth,
						'height': imageHeight,
						'srcset': '',
						'sizes': ''
					});
					jQuery( window.avadaUploadfield ).val( imageId ).trigger( 'change' );

					featuredImageWrapper.find( '.fusion-remove-featured-image' ).show();
					featuredImageWrapper.find( '.fusion-set-featured-image' ).hide();

				} else {
					jQuery( window.avadaUploadfield ).val( imageUrl ).trigger( 'change' );
				}
				window.avadaUploadfield = '';

			} else {
				window.avadaSendToEditorBackup( src );
			}
		};
	}

	// Remove the featured image preview and also the id from form input.
	jQuery( '.fusion-remove-featured-image' ).on( 'click', function( e ) {
		var featuredImageWrapper = jQuery( this ).parents( '.fusion-featured-image-meta-box' );

		e.preventDefault();

		featuredImageWrapper.find( '.fusion-preview-image' ).attr({
			'src': '',
			'alt': '',
			'width': '',
			'height': '',
			'srcset': '',
			'sizes': ''
		});

		featuredImageWrapper.find( '.upload_field' ).val( '' );

		featuredImageWrapper.find( '.fusion-remove-featured-image' ).hide();
		featuredImageWrapper.find( '.fusion-set-featured-image' ).show();
	});

	if ( jQuery.cookie( 'fusion_metabox_tab_' + jQuery( '#post_ID' ).val() ) ) {
		id = jQuery.cookie( 'fusion_metabox_tab_' + jQuery( '#post_ID' ).val() );

		jQuery( '.pyre_metabox_tabs li' ).removeClass( 'active' );
		jQuery( '.pyre_metabox_tabs li a[href=' + id + ']' ).parent().addClass( 'active' );

		jQuery( '.pyre_metabox_tabs li a[href=' + id + ']' ).parents( '.inside' ).find( '.pyre_metabox_tab' ).removeClass( 'active' ).hide();
		jQuery( '.pyre_metabox_tabs li a[href=' + id + ']' ).parents( '.inside' ).find( '#pyre_tab_' + id ).addClass( 'active' ).fadeIn();

		calcElementHeights();
	} else {
		jQuery( '.pyre_metabox_tabs li:first-child' ).addClass( 'active' );
		jQuery( '.pyre_metabox .pyre_metabox_tab:first-child' ).addClass( 'active' ).fadeIn();
	}

	jQuery( '.pyre_metabox_tabs li a' ).click( function( e ) {
		var id = jQuery( this ).attr( 'href' );

		e.preventDefault();

		jQuery.cookie( 'fusion_metabox_tab_' + jQuery( '#post_ID' ).val(), id, { expires: 7 } );

		jQuery( this ).parents( 'ul' ).find( 'li' ).removeClass( 'active' );
		jQuery( this ).parent().addClass( 'active' );

		jQuery( this ).parents( '.inside' ).find( '.pyre_metabox_tab' ).removeClass( 'active' ).hide();
		jQuery( this ).parents( '.inside' ).find( '#pyre_tab_' + id ).addClass( 'active' ).fadeIn();

		calcElementHeights();
	});

	// Calc height if the whole panel toggle is closed on load and opened later.
	jQuery( '#post-body #advanced-sortables #pyre_page_options .handlediv, #post-body #advanced-sortables #pyre_page_options .hndle' ).click( function( e ) {
		setTimeout( function() {
			calcElementHeights();
		}, 250 );

	});

	// Initialize heights on load.
	calcElementHeights();
});

function calcElementHeights() {
	var tabContentHeight,
		tabsHeight;

	// Set tabs pane height same as the tab content height.
	jQuery( '.pyre_metabox_tabs' ).removeAttr( 'style' );
	tabContentHeight = jQuery( '.pyre_metabox' ).outerHeight();
	tabsHeight = jQuery( '.pyre_metabox_tabs' ).height();
	if ( tabContentHeight > tabsHeight ) {
		jQuery( '.pyre_metabox_tabs' ).css( 'height', tabContentHeight );
	}

	// Set heights of select arrows correctly.
	jQuery( '.pyre_field .fusion-shortcodes-arrow' ).each( function() {
		if ( jQuery( this ).next().innerHeight() > 0 ) {
			jQuery( this ).css( {
				height: jQuery( this ).next().innerHeight(),
				width: jQuery( this ).next().innerHeight(),
				'line-height': jQuery( this ).next().innerHeight() + 'px'
			});
		}
	});

	// Set height of upload buttons to correspond with text field height.
	jQuery( '.pyre_field .fusion_upload_button' ).each( function() {
		var fieldHeight = jQuery( this ).parents( '.pyre_upload' ).find( 'input' ).outerHeight();

		if ( fieldHeight > 0 ) {
			jQuery( this ).css( 'height', fieldHeight );
		}
	});
}
