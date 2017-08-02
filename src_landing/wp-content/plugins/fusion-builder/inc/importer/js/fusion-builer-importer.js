jQuery( document ).ready( function() {

    jQuery( '#fusion-builder-import-file' ).on( 'change', FusionPrepareUpload );

    jQuery( '.fusion-builder-import-data' ).on( 'click', FusionUploadFiles );

    function FusionPrepareUpload( event ) {
		var files;
        if ( '' !== jQuery( this ).val() ) {
            jQuery( '.fusion-builder-import-data' ).prop( 'disabled', false );
        } else {
            jQuery( '.fusion-builder-import-data' ).prop( 'disabled', true );
        }
        window.fusionBuilderImporterFiles = event.target.files;
    }

    function FusionUploadFiles( event ) {

		var data,
		    inputField;

        if ( event ) {
            event.stopPropagation();
            event.preventDefault();
        }

        data = new FormData();
        inputField = jQuery( '#fusion-builder-import-file' );

        jQuery.each( window.fusionBuilderImporterFiles, function( key, value ) {
            data.append( key, value );
        } );

        data.append( 'action', 'fusion_builder_importer' );
        data.append( 'fusion_import_nonce', fusionBuilderConfig.fusion_import_nonce );

        jQuery.ajax( {
            type: 'POST',
            url: fusionBuilderConfig.ajaxurl,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: data,
            cache: false,
            complete: function( data ) {
                inputField.val( '' );
                jQuery( '.fusion-builder-import-success' ).show();
            }
        } );
    }
} );
