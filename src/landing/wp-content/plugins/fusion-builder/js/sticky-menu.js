! function( name, definition ) {

	if ( 'undefined' !== typeof module && module.exports ) {
		module.exports = definition();
	} else if ( 'function' === typeof define ) {
		define( definition );
	} else {
		this[ name ] = definition();
	}

}( 'fusionBuilderStickyHeader', function() {

	return function fusionBuilderStickyHeader( el, top ) {
		var $container = document.getElementById( 'fusion_builder_container' ),
		    requiredTop = top || 0,
		    topBorderSize = 22,
		    originalRect = calcRect( el ),
		    styles = {
				position: 'fixed',
				top: requiredTop + 'px',
				left: originalRect.left + 'px',
				width: originalRect.width + 'px',
				'border-top': topBorderSize + 'px solid #ffffff',
				'z-index': 999
		    },
		    requiredOriginalStyles = ['position', 'top', 'left', 'z-index', 'border-top'],
		    originalStyles = {},
		    onscroll,
		    onresize;

		requiredOriginalStyles.forEach( function( key ) {
			originalStyles[ key ]  = el.style[ key ];
			originalStyles.width = '100%';
		});

		jQuery( '.fusion-builder-history-list' ).css( 'max-height', jQuery( window ).height() - 100 );

		if ( window.onscroll ) {
			onscroll = window.onscroll;
		}

		if ( window.onresize ) {
			onresize = window.onresize;
		}

		window.onscroll = function( event ) {

			var $mainContainer         = document.getElementById( 'fusion_builder_main_container' ),
			    $mainContainerRect     = calcRect( $mainContainer ),
			    $builderControlsHeight = jQuery( '#fusion_builder_controls' ).height(),
			    $mainContainerHeight   = ( 'fixed' === jQuery( '#fusion_builder_controls' ).css( 'position' ) ) ? $mainContainerRect.height + originalRect.height - $builderControlsHeight : $mainContainerRect.height,
			    calContainer,
			    left,
			    key;

			jQuery( '.fusion-builder-history-list' ).css( 'max-height', jQuery( window ).height() - 100 );
			if ( getWindowScroll().top > originalRect.top - requiredTop - topBorderSize && getWindowScroll().top + requiredTop + topBorderSize + originalRect.height < $mainContainerRect.top + $mainContainerHeight ) {
				calContainer = ( $container );
				left         = calContainer.left + 'px';

				styles.left = left;
				styles.width = jQuery( '#fusion_builder_container' ).outerWidth() + 'px';

				for ( key in styles ) {
					el.style[ key ] = styles[ key ];
				}
			} else {
				for ( key in originalStyles ) {
					el.style[ key ] = originalStyles[ key ];
				}
			}

			onscroll && onscroll( event );
		};

		window.onresize = function( event ) {
			var parentWidth = jQuery( '#fusion_builder_container' ).outerWidth() + 'px';

			jQuery( '.fusion-builder-history-list' ).css( 'max-height', jQuery( window ).height() - 100 );

			if ( getWindowScroll().top > originalRect.top - requiredTop ) {
				el.style.width = parentWidth;
			} else {
				el.style.width = originalStyles.width;
			}

			onresize && onresize( event );
		};

		function calcRect( el ) {
			var rect = el.getBoundingClientRect(),
			    windowScroll = getWindowScroll(),
			    headingRect,
			    top;

			// If the whole panel is collapsed, the top position needs checked from the heading
			top = rect.top + windowScroll.top;
			if ( jQuery( el ).parents( '#fusion_builder_layout' ).hasClass( 'closed' ) ) {
				headingRect = jQuery( el ).parents( '#fusion_builder_layout' ).find( '.ui-sortable-handle' )[0].getBoundingClientRect();
				top =  headingRect.top + headingRect.height + windowScroll.top;
			}

			return {
				left: rect.left + windowScroll.left,
				top: top,
				width: rect.width,
				height: rect.height
			};
		}

		function getWindowScroll() {
			return {
				top: window.pageYOffset || document.documentElement.scrollTop,
				left: window.pageXOffset || document.documentElement.scrollLeft
			};
		}
	};
});
