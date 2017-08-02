( function( $ ) {

if ( 'undefined' !== typeof( tinymce ) ) {

	tinymce.PluginManager.add( 'fusion_button', function( editor ) {

		if ( 'undefined' !== typeof FusionPageBuilderApp ) {
			if ( 'undefined' !== typeof FusionPageBuilderApp && true === FusionPageBuilderApp.allowShortcodeGenerator && true !== FusionPageBuilderApp.shortcodeGenerator || 'content' === editor.id || 'excerpt' === editor.id ) {

				editor.addButton( 'fusion_button', {
					title: 'Fusion Builder Element Generator',
					icon: true,
					image: FusionPageBuilderApp.fusion_builder_plugin_dir + 'images/icons/fb_logo.svg',
					onclick: function() {

						// Set editor that triggered shortcode generator
						FusionPageBuilderApp.shortcodeGeneratorActiveEditor = editor;

						// Open shortcode generator
						openShortcodeGenerator( $( this ) );
					}
				});
			}
		}
	});
}

})( jQuery );
