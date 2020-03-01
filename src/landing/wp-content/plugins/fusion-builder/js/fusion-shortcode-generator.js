( function( $ ) {

	// Insert shortcode into post editor
	fusionBuilderInsertIntoEditor = function( shortcode, editorID ) {
		var currentEditor = window.SCmoduleContentEditorMode,
		    editorArea,
		    editor;

		if ( 'tinymce' === window.SCmoduleContentEditorMode && ( '' === editorID || 'undefined' === typeof editorID ) ) {

			if ( 'undefined' !== typeof window.tinyMCE ) {

				// Set active editor
				editor = FusionPageBuilderApp.shortcodeGeneratorActiveEditor;
				editor.focus();

				if ( 'excerpt' === editor.id ) {
					FusionPageBuilderApp.fromExcerpt = true;
				}

				// Insert shortcode
				window.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, shortcode );
				window.tinyMCE.activeEditor.execCommand( 'mceCleanup', false );
			}

		} else {

			if ( null === editorID || '' === editorID || 'undefined' === typeof editorID ) {
				editorArea = $( window.editorArea );

			} else {
				editorArea = $( '#' + editorID );
			}

			if ( 'excerpt' === editorArea.attr( 'id' ) ) {
				FusionPageBuilderApp.fromExcerpt = true;
			}

			if ( 'undefined' === typeof window.cursorPosition ) {
				if ( 0 === editorArea.getCursorPosition() ) {
					editorArea.val( shortcode + editorArea.val() );
				} else if ( editorArea.val().length === editorArea.getCursorPosition() ) {
					editorArea.val( editorArea.val() + shortcode );
				} else {
					editorArea.val( editorArea.val().slice( 0, editorArea.getCursorPosition() ) + shortcode + editorArea.val().slice( editorArea.getCursorPosition() ) );
				}
			} else {
				editorArea.val( [ editorArea.val().slice( 0, window.cursorPosition ), shortcode, editorArea.val().slice( window.cursorPosition ) ].join( '' ) );
			}
		}

		if ( false === FusionPageBuilderApp.manuallyAdded ) {
			FusionPageBuilderApp.shortcodeGeneratorActiveEditor = '';
		}
	};

} )( jQuery );

function openShortcodeGenerator( trigger ) {

	// Get editor id from event.trigger.  parent.parent

	var view,
	    editorArea = '#' + trigger.parent().parent().find( '.wp-editor-area' ).attr( 'id' );

	window.cursorPosition = 0;
	window.editorArea = editorArea;

	// Set shortcode generator flag
	FusionPageBuilderApp.shortcodeGenerator = true;

	// Get active editor mode
	if ( FusionPageBuilderApp.isTinyMceActive() ) {
		window.SCmoduleContentEditorMode = 'tinymce';
	} else {
		window.SCmoduleContentEditorMode = 'html';
	}

	// Get current cursor position ( for html editor )
	if ( 'tinymce' !== window.SCmoduleContentEditorMode ) {
		window.cursorPosition = jQuery( editorArea ).getCursorPosition();
	}

	view = new FusionPageBuilder.ModalView( {
		model: this.model,
		collection: FusionPageBuilderElements,
		attributes: {
			'data-modal_view': 'all_elements_generator'
		},
		view: this
	} );

	jQuery( 'body' ).append( view.render().el );
}

// Helper function to check the cursor position of text editor content field before the shortcode generator is opened
( function( $, undefined ) {
	$.fn.getCursorPosition = function() {
		var el  = $( this ).get( 0 ),
		    pos = 0,
		    Sel,
		    SelLength;

		if ( 'selectionStart' in el ) {
			pos = el.selectionStart;
		} else if ( 'selection' in document ) {
			el.focus();
			Sel       = document.selection.createRange();
			SelLength = document.selection.createRange().text.length;
			Sel.moveStart( 'character', -el.value.length );
			pos = Sel.text.length - SelLength;
		}
		return pos;
	};
})( jQuery );
