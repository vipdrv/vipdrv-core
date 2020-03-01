var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	/**
	 * Fetch a JavaScript template for an id, and return a templating function for it.
	 *
	 * @param  {string} id   A string that corresponds to a DOM element
	 * @return {function}    A function that lazily-compiles the template requested.
	 */
	FusionPageBuilder.template = _.memoize( function( html ) {
		var compiled,
			/*
			 * Underscore's default ERB-style templates are incompatible with PHP
			 * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
			 */
		    options = {
				evaluate:    /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape:      /\{\{([^\}]+?)\}\}(?!\})/g
		    };

		return function( data ) {
			compiled = compiled || _.template( html, null, options );
			return compiled( data );
		};
	});
}( jQuery ) );
