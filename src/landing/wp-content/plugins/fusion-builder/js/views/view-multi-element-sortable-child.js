var FusionPageBuilder = FusionPageBuilder || {};

( function( $ ) {

	$( document ).ready( function() {

		// Multi Element child sortable item
		FusionPageBuilder.MultiElementSortableChild = window.wp.Backbone.View.extend( {
			tagName: 'li',
			className: 'fusion-builder-data-cid',

			initialize: function() {
				this.template = FusionPageBuilder.template( $( '#fusion-builder-multi-child-sortable' ).html() );
				this.listenTo( FusionPageBuilderEvents, 'fusion-multi-child-update-preview', this.updatePreview );
			},

			events: {
				'click .fusion-builder-multi-setting-options': 'showSettings',
				'click .fusion-builder-multi-setting-remove': 'removeView',
				'click .fusion-builder-multi-setting-clone': 'cloneElement'
			},

			render: function() {
				var view;

				this.$el.html( this.template( { atts: this.model.attributes } ) );

				return this;
			},

			cloneElement: function( event ) {

				var elementAttributes,
					titleLabel = this.$el.find( '.multi-element-child-name' ).html();

				if ( event ) {
					event.preventDefault();
				}

				elementAttributes               = $.extend( true, {}, this.model.attributes );
				elementAttributes.created       = 'manually';
				elementAttributes.cid           = FusionPageBuilderViewManager.generateCid();
				elementAttributes.cloned        = true;
				elementAttributes.targetElement = this.$el;
				elementAttributes.titleLabel    = titleLabel;

				FusionPageBuilderApp.collection.add( elementAttributes );

				FusionPageBuilderEvents.trigger( 'fusion-multi-element-edited' );
			},

			showSettings: function( event ) {

				var modalView,
				    viewSettings,
				    $parentValues = {},
				    $parentOptions;

				if ( event ) {
					event.preventDefault();
				}

				FusionPageBuilderApp.MultiElementChildSettings = true;

				if ( true === FusionPageBuilderApp.shortcodeGenerator ) {
					FusionPageBuilderApp.shortcodeGeneratorMultiElementChild = true;
				}

				// Check for parent options with value set. If set, add to object.
				$parentOptions = jQuery( document ).find( '.fusion-builder-option.range .fusion-hidden-value, .wp-color-picker, .has-child-dependency input, .has-child-dependency select, .has-child-dependency textarea, .has-child-dependency #fusion_builder_content_main, .has-child-dependency #fusion_builder_content_main_child' ).not( ':input[type=button], .fusion-icon-search, .category-search-field, .fusion-builder-table input, .fusion-builder-table textarea, .single-builder-dimension .fusion-builder-dimension input, .fusion-hide-from-atts' );
				$parentOptions.each( function() {
					if ( jQuery( this ).val().length ) {
						$parentValues[ jQuery( this ).attr( 'id' ) ] = jQuery( this ).val();
					}
				});

				// Add parent value object to the child model.
				this.model.set({ parent_values: $parentValues });

				viewSettings = {
					model: this.model,
					collection: this.collection,
					attributes: {
						'data-modal_view': 'multi_element_child_settings'
					}
				};

				modalView = new FusionPageBuilder.ModalView( viewSettings );

				$( '.fusion_builder_modal_multi_element_settings_container' ).last().after( modalView.render().el );

			},

			updatePreview: function() {
				var $title,
				    $attributes = this.model.attributes,
				    $model      = this.model;

				if ( 'undefined' !== typeof $attributes ) {
					$title = '';
					if ( 'undefined' !== typeof $attributes.params.title && $attributes.params.title.length ) {
						$title = $attributes.params.title;
					} else if ( 'fusion_flip_box' == $model.get( 'element_type' ) && 'undefined' !== typeof $attributes.params.title_front && $attributes.params.title_front.length ) {
						$title = $attributes.params.title_front;
					} else if ( 'undefined' !== typeof $attributes.params.image && $attributes.params.image.length ) {
						$title = $attributes.params.image;

						// If contains backslash, retreive only last part.
						if ( -1 !== $title.indexOf( '/' ) && -1 === $title.indexOf( '[' ) ) {
							$title = $title.split( '/' );
							$title = $title.slice( -1 )[0];
						}
					} else if ( 'undefined' !== typeof $attributes.params.video && $attributes.params.video.length ) {
						$title = $attributes.params.video;
					} else if ( 'undefined' !== typeof $attributes.params.element_content && $attributes.params.element_content.length ) {
						$title = $attributes.params.element_content;
					}

					// Remove HTML tags but keep quotation marks etc.
					$title = jQuery( '<div/>' ).html( $title ).text();
					$title = jQuery( '<div/>' ).html( $title ).text();
					if ( $title ) {
						$title = ( $title.length  > 15 ) ? $title.substring( 0, 15 ) + '...' : $title;
						jQuery( 'li[data-cid=' + $model.get( 'cid' ) + '] .multi-element-child-name' ).text( $title );
					}
				}
			},

			removeView: function( event ) {
				if ( event ) {
					event.preventDefault();
				}

				this.remove();

				this.model.destroy();

				FusionPageBuilderEvents.trigger( 'fusion-multi-element-edited' );
			}

		} );

	} );

} )( jQuery );
