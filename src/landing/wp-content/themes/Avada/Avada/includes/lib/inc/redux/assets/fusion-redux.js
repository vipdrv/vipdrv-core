jQuery( document ).ready( function() {

	var $parentElement,
	    $fusionMenu;

	jQuery( '.custom_color_save_button' ).on( 'click', function( e ) {

		var overlay,
		    $colorName,
		    $type,
		    $notificationBar,
		    $saveAction,
		    $themeOptionsName,
		    $customColors,
		    $data;

		e.preventDefault();

		overlay    = jQuery( '#fusionredux_ajax_overlay' );
		$colorName = '';
		$type      = '';

		overlay.fadeIn();
		jQuery( '.fusionredux-action_bar .spinner' ).addClass( 'is-active' );

		jQuery( '.fusionredux-action_bar input' ).attr( 'disabled', 'disabled' );
		$notificationBar = jQuery( '#fusionredux_notification_bar' );
		$notificationBar.slideUp();
		jQuery( '.fusionredux-save-warn' ).slideUp();
		jQuery( '.fusionredux_ajax_save_error' ).slideUp(
			'medium', function() {
				jQuery( this ).remove();
			}
		);

		// Check the action to use used.
		$saveAction = jQuery( this ).attr( 'id' );

		// If save action is not an import, then use TO values.
		if ( 'custom_color_import_submit' != $saveAction ) {

			// Set the name to save as.
			if ( 'custom_color_save_update' == $saveAction ) {

				// If updating, use selected name.
				$colorName = jQuery( '#color-scheme-update-name' ).val();
				$type = 'update';
			} else {

				//  If saving as new, use input name.
				$colorName = jQuery( '#color-scheme-new-name' ).val();
				$type = 'save';
			}
			$themeOptionsName = fusionFusionreduxVars.theme_options_name;
			$customColors = {
				primary_color:                            jQuery( 'input[name="' + $themeOptionsName + '[primary_color]"]' ).val(),
				pricing_box_color:                        '',
				image_gradient_top_color:                 jQuery( 'input[name="' + $themeOptionsName + '[image_gradient_top_color]"]' ).val(),
				image_gradient_bottom_color:              jQuery( 'input[name="' + $themeOptionsName + '[image_gradient_bottom_color]"]' ).val(),
				button_gradient_top_color:                '',
				button_gradient_bottom_color:             '',
				button_gradient_top_color_hover:          '',
				button_gradient_bottom_color_hover:       '',
				button_accent_color:                      '',
				button_accent_hover_color:                '',
				button_bevel_color:                       '',
				checklist_circle_color:                   '',
				counter_box_color:                        '',
				countdown_background_color:               '',
				dropcap_color:                            '',
				flip_boxes_back_bg:                       '',
				progressbar_filled_color:                 '',
				counter_filled_color:                     '',
				ec_sidebar_widget_bg_color:               jQuery( 'input[name="' + $themeOptionsName + '[ec_sidebar_widget_bg_color]"]' ).val(),
				menu_hover_first_color:                   jQuery( 'input[name="' + $themeOptionsName + '[menu_hover_first_color]"]' ).val(),
				header_top_bg_color:                      jQuery( 'input[name="' + $themeOptionsName + '[header_top_bg_color]"]' ).val(),
				content_box_hover_animation_accent_color: '',
				map_overlay_color:                        jQuery( 'input[name="' + $themeOptionsName + '[map_overlay_color]"]' ).val(),
				flyout_menu_icon_hover_color:             jQuery( 'input[name="' + $themeOptionsName + '[flyout_menu_icon_hover_color]"]' ).val()
			};

			$data = $customColors;
		} else {

			// Importing.
			$data = jQuery( '#avada-import-custom-color-textarea' ).val();
			$type = 'import';
		}

		jQuery.ajax({
			type:     'post',
			dataType: 'json',
			url:       ajaxurl,
			data: {
				action: 'custom_colors_ajax_save',
				data: { name: $colorName, values: $data, type: $type }
			},
			error: function( response ) {
				jQuery( '.fusionredux-action_bar input' ).removeAttr( 'disabled' );
				overlay.fadeOut( 'fast' );
				jQuery( '.fusionredux-action_bar .spinner' ).removeClass( 'is-active' );
			},
			success: function( response ) {
				var $interval;
				jQuery( '#fusionredux_save' ).trigger( 'click' );

				$interval = setInterval( afterSave, 500 );
				function afterSave() {
					if ( ! overlay.is( ':visible' ) ) {
						clearInterval( $interval );
						location.reload( true );
					}
				}
			}
		});
		return false;
	});

	// Custom colors, toggle selection.
	jQuery( '.custom-color-toggle' ).on( 'click', function( e ) {

		var $toggleTarget;

		e.preventDefault();

		// If its the delete toggle, allow scheme selection by adding class to body of page.
		if ( 'avada-delete-custom-color' == jQuery( this ).data( 'toggle' ) ) {
			jQuery( 'body' ).toggleClass( 'color-scheme-selection' );
		} else {
			jQuery( 'body' ).removeClass( 'color-scheme-selection' );
		}

		// Toggle target content visibility.
		$toggleTarget = '#' + jQuery( this ).data( 'toggle' );
		jQuery( '.color-toggle:not(' + $toggleTarget + ')' ).addClass( 'color-hidden' );
		jQuery( $toggleTarget ).toggleClass( 'color-hidden' );
	});

	// On click, toggle item for deletion.
	jQuery( document ).on( 'click', '.color-scheme-selection .fusion_theme_options-color_scheme li:nth-child(n+11)', function( e ) {
		e.preventDefault();
		jQuery( this ).toggleClass( 'delete-selected' );
	});

	// Cancel deletion selection.
	jQuery( '#custom_color_delete_cancel' ).on( 'click', function( e ) {
		e.preventDefault();
		jQuery( '.delete-selected' ).removeClass( 'delete-selected' );
	});

	// Send the deletion request.
	jQuery( '#custom_color_delete_confirm' ).on( 'click', function( e ) {

		var overlay,
		    $schemeNames = [],
		    $noSelection,
		    $notificationBar;

		e.preventDefault();

		overlay      = jQuery( '#fusionredux_ajax_overlay' );
		$noSelection = jQuery( '#avada-delete-custom-color .hidden' ).text();

		overlay.fadeIn();
		jQuery( '.fusionredux-action_bar .spinner' ).addClass( 'is-active' );

		jQuery( '.fusionredux-action_bar input' ).attr( 'disabled', 'disabled' );
		$notificationBar = jQuery( '#fusionredux_notification_bar' );
		$notificationBar.slideUp();
		jQuery( '.redux-save-warn' ).slideUp();
		jQuery( '.redux_ajax_save_error' ).slideUp(
			'medium', function() {
				jQuery( this ).remove();
			}
		);

		// Make an array of select scheme names to delete.
		jQuery( '.delete-selected' ).each( function( i ) {
			$schemeNames[i] = jQuery( this ).find( 'input' ).val();
		});

		// If there are some selected, then delete theme.
		if ( jQuery( '.delete-selected' ).length ) {
			jQuery.ajax({
				type:     'post',
				dataType: 'json',
				url:       ajaxurl,
				data: {
					action: 'custom_colors_ajax_delete',
					data: { names: $schemeNames }
				},
				error: function( response ) {
					jQuery( '.fusionredux-action_bar input' ).removeAttr( 'disabled' );
					overlay.fadeOut( 'fast' );
					jQuery( '.fusionredux-action_bar .spinner' ).removeClass( 'is-active' );
				},
				success: function( response ) {
					var $interval;
					jQuery( '#fusionredux_save' ).trigger( 'click' );

					$interval = setInterval( afterSave, 500 );
					function afterSave() {
						if ( ! overlay.is( ':visible' ) ) {
							clearInterval( $interval );
							location.reload( true );
						}
					}
				}
			});
		} else {
			alert( $noSelection );
			jQuery( '.fusionredux-action_bar input' ).removeAttr( 'disabled' );
			overlay.fadeOut( 'fast' );
			jQuery( '.fusionredux-action_bar .spinner' ).removeClass( 'is-active' );
		}
	});

	jQuery( '#fusionredux-import' ).on( 'click', function( e ) {
		jQuery.ajax({
			type:     'post',
			dataType: 'json',
			url:       ajaxurl,
			data: {
				action: 'custom_option_import',
				data: jQuery( '#import-code-value' ).val()
			}
		});
	});

	// Style the update selections.
	jQuery( 'select.update-select.fusionredux-select-item' ).each(
		function() {
			var defaultParams = {
				width: '180px',
				triggerChange: true,
				allowClear: true,
				minimumResultsForSearch: Infinity
			};
			jQuery( this ).select3( defaultParams );
			jQuery( this ).on( 'change', function() {

				// jscs:disable
				/* jshint ignore:start */
				fusionredux_change( jQuery( jQuery( this ) ) );


				// jscs:enable
				/* jshint ignore:end */
				jQuery( this ).select3SortableOrder();
			});
		}
	);

	// Activate the Fusion admin menu theme option entry when theme options are active
	if ( jQuery( 'a[href="themes.php?page=fusion_options"]' ).hasClass( 'current' ) ) {
		$fusionMenu = jQuery( '#toplevel_page_fusion' );

		$fusionMenu.addClass( 'wp-has-current-submenu wp-menu-open' );
		$fusionMenu.children( 'a' ).addClass( 'wp-has-current-submenu wp-menu-open' );
		$fusionMenu.children( '.wp-submenu' ).find( 'li' ).last().addClass( 'current' );
		$fusionMenu.children( '.wp-submenu' ).find( 'li' ).last().children().addClass( 'current' );

		// Do not show the appearance menu as active
		jQuery( '#menu-appearance a[href="themes.php"]' ).removeClass( 'wp-has-current-submenu wp-menu-open' );
		jQuery( '#menu-appearance' ).removeClass( 'wp-has-current-submenu wp-menu-open' );
		jQuery( '#menu-appearance' ).addClass( 'wp-not-current-submenu' );
		jQuery( '#menu-appearance a[href="themes.php"]' ).addClass( 'wp-not-current-submenu' );
		jQuery( '#menu-appearance' ).children( '.wp-submenu' ).find( 'li' ).removeClass( 'current' );
	}

	$parentElement = jQuery( '#' + fusionFusionreduxVars.option_name + '-social_media_icons .fusionredux-repeater-accordion' );

	// Initialize fusionredux color fields, even when they are insivible
	fusionredux.field_objects.color.init( $parentElement.find( '.fusionredux-container-color ' ) );

	$parentElement.set_social_media_repeater_custom_field_logic();

	jQuery( '.fusionredux-repeaters-add' ).click( function() {
		setTimeout( function() {
			$parentElement = jQuery( '#' + fusionFusionreduxVars.option_name + '-social_media_icons .fusionredux-repeater-accordion' );
			$parentElement.set_social_media_repeater_custom_field_logic();
		}, 50 );
	});

	// Make sure the sub menu flyouts are closed, when a new menu item is activated
	jQuery( '.fusionredux-group-tab-link-li a' ).click( function() {
		jQuery( '.fusionredux-group-tab-link-li' ).removeClass( 'fusion-section-hover' );
		jQuery.fusionredux.required();
		jQuery.fusionredux.check_active_tab_dependencies();
	});

	// Make submenus flyout when a main menu item is hovered
	jQuery( '.fusionredux-group-tab-link-li.hasSubSections' ).each( function() {
		jQuery( this ).mouseenter( function() {
			if ( ! jQuery( this ).hasClass( 'activeChild' ) ) {
				jQuery( this ).addClass( 'fusion-section-hover' );
			}
		});

		jQuery( this ).mouseleave( function() {
			jQuery( this ).removeClass( 'fusion-section-hover' );
		});
	});

	// Add a pattern preview container to show off the background patterns
	jQuery( '.fusion_theme_options-bg_pattern' ).append( '<div class="fusion-pattern-preview"></div>' );

	// On pattern image click update the preview
	jQuery( '.fusion_theme_options-bg_pattern' ).find( 'ul li img' ).click( function() {
		var $background = 'url("' + jQuery( this ).attr( 'src' ) + '") repeat';
		jQuery( '.fusion-pattern-preview' ).css( 'background', $background );
	});

	// Setup tooltips on color presets
	jQuery( '.fusion_theme_options-scheme_type li, .fusion_theme_options-color_scheme li' ).qtip({
		content: {
			text: function( event, api ) {
				return jQuery( this ).find( 'img' ).attr( 'alt' );
			}
		},
		position: {
			my: 'bottom center',
			at: 'top center'
		},
		style: {
			classes: 'fusion-tooltip qtip-light qtip-rounded qtip-shadow'
		}
	});

	// Color picker fallback for pre WP 4.4 versions
	jQuery( '.wp-color-result' ).on( 'click', function() {
		jQuery( this ).parent().addClass( 'wp-picker-active' );
	});

	jQuery( '.fusion_theme_options-header_layout img' ).on( 'click', function() {

		// Auto adjust main menu height
		var $headerVersion = jQuery( this ).attr( 'alt' ),
		    $mainMenuHeight = '0';

		if ( 'v1' === $headerVersion || 'v2' === $headerVersion || 'v3' === $headerVersion || 'v7' === $headerVersion ) {
			$mainMenuHeight = '84';
		} else {
			$mainMenuHeight = '40';
		}

		jQuery( 'input#nav_height' ).val( $mainMenuHeight );

		// Auto adjust logo margin
		if ( 'v4' === $headerVersion ) {
			jQuery( '.fusion_theme_options-logo_margin .fusionredux-spacing-bottom, .fusion_theme_options-logo_margin #logo_margin-bottom' ).val( '0px' );
		} else {
			jQuery( '.fusion_theme_options-logo_margin .fusionredux-spacing-bottom, .fusion_theme_options-logo_margin #logo_margin-bottom' ).val( '31px' );
		}
		jQuery( '.fusion_theme_options-logo_margin .fusionredux-spacing-top, .fusion_theme_options-logo_margin #logo_margin-top' ).val( '31px' );

		// Auto adjust header v2 topbar color
		if ( 'v2' === $headerVersion ) {
			jQuery( '.fusion_theme_options-header_top_bg_color #header_top_bg_color-color' ).val( '#fff' );
		} else {
			jQuery( '.fusion_theme_options-header_top_bg_color #header_top_bg_color-color' ).val( jQuery( '#primary_color-color' ).val() );
		}
	});

	jQuery( '#fusion_options-header_position label' ).on( 'click', function() {
		var $headerPosition = jQuery( this ).find( 'span' ).text(),
		    $headerVersion  = jQuery( '.fusion_theme_options-header_layout' ).find( '.fusionredux-image-select-selected img' ).attr( 'alt' ),
		    $mainMenuHeight;

		// Auto adjust main menu height
		if ( 'Top' === $headerPosition ) {
			if ( 'v1' === $headerVersion || 'v2' === $headerVersion || 'v3' === $headerVersion ) {
				$mainMenuHeight = '84';
			} else {
				$mainMenuHeight = '40';
			}
		} else {
			$mainMenuHeight = '40';
		}
		jQuery( 'input#nav_height' ).val( $mainMenuHeight );

		// Auto set header padding
		jQuery( '.fusion_theme_options-header_padding input' ).val( '0px' );
		if ( 'Top' !== $headerPosition ) {
			jQuery( '.fusion_theme_options-header_padding input.fusionredux-spacing-left, .fusion_theme_options-header_padding #header_padding-left, .fusion_theme_options-header_padding input.fusionredux-spacing-right, .fusion_theme_options-header_padding #header_padding-right' ).val( '60px' );
		}

		// Auto adjust logo margin
		jQuery( '.fusion_theme_options-logo_margin .fusionredux-spacing-top, .fusion_theme_options-logo_margin #logo_margin-top, .fusion_theme_options-logo_margin .fusionredux-spacing-bottom, .fusion_theme_options-logo_margin #logo_margin-bottom' ).val( '31px' );
		if ( 'Top' === $headerPosition && 'v4' === $headerVersion ) {
			jQuery( '.fusion_theme_options-logo_margin .fusionredux-spacing-bottom, .fusion_theme_options-logo_margin #logo_margin-bottom' ).val( '0px' );
		}
	});

	// Listen for changes to header position and reset to 1 if changing away from top.
	jQuery( '.fusion_theme_options-header_position' ).on( 'change', function() {
		var $widthVal = jQuery( '#menu_arrow_size-width' ).val(),
		    $heightVal = jQuery( '#menu_arrow_size-height' ).val(),
			$widthDimension = jQuery( '#menu_arrow_size .fusionredux-dimensions-width, #menu_arrow_size-width' ),
		    $heightDimension = jQuery( '#menu_arrow_size .fusionredux-dimensions-height, #menu_arrow_size-height' );

		if ( 'Top' !== jQuery( this ).find( '.ui-state-active' ).prev( 'input' ).val() ) {
			if ( parseInt( $widthVal ) > parseInt( $heightVal ) ) {
				$widthDimension.val( $heightVal );
				$heightDimension.val( $widthVal );
			}
		} else if ( parseInt( $heightVal ) > parseInt( $widthVal ) ) {
			$widthDimension.val( $heightVal );
			$heightDimension.val( $widthVal );
		}
	});

	function fusionMenuHint() {
		var $logoHeight = jQuery( '.fusion_theme_options-logo .upload-height' ).val(),
		    $logoTopMargin = ( '' === jQuery( 'input[rel="logo_margin-top"]' ).val() ) ? '0' : jQuery( 'input[rel="logo_margin-top"]' ).val(),
		    $logoBottomMargin = ( '' === jQuery( 'input[rel="logo_margin-bottom"]' ).val() ) ? '0' : jQuery( 'input[rel="logo_margin-bottom"]' ).val(),
		    $fullLogoHeight = '',
		    $headerVersion = jQuery( '.fusion_theme_options-header_layout' ).find( '.fusionredux-image-select-selected img' ).attr( 'alt' );

		if ( 'undefined' !== typeof $logoTopMargin && ( -1 !== $logoTopMargin.indexOf( 'px' ) || '0' === $logoTopMargin ) && ( -1 !== $logoBottomMargin.indexOf( 'px' ) || '0' === $logoBottomMargin ) && $logoHeight && 'v4' !== $headerVersion && 'v5' !== $headerVersion && 'v6' !== $headerVersion ) {
			$fullLogoHeight = parseInt( $logoHeight ) + parseInt( $logoTopMargin  ) + parseInt( $logoBottomMargin  );
			jQuery( '#fusion-menu-height-hint strong' ).html( $fullLogoHeight );
			jQuery( '#fusion-menu-height-hint' ).fadeIn( 'fast' );
			jQuery( '#fusion-menu-height-hint' ).css( 'display', 'inline' );
		} else {
			jQuery( '#fusion-menu-height-hint' ).hide();
		}
	}

	// Trigger on load.
	fusionMenuHint();

	// When we load the menu tab, recalculate menu hint.
	jQuery( 'a[data-css-id="heading_menu_section"], a[data-css-id="heading_menu"]' ).on( 'click', function() {
		fusionMenuHint();
	});

	// Listen for changes to medium and update large description.
	jQuery( '#visibility_medium, .fusion_theme_options-visibility_medium noUi-handle' ).on( 'change update click', function() {
		jQuery( '#fusion-visibility-large span' ).html( jQuery( this ).val() );
	});

	jQuery( '#shortcode_animations_accordion_start_accordion' ).prev( '.form-table' ).remove();

});

jQuery( window ).load(function() {

	// If search field is not empty, make sidebar accessible again when an item is clicked and clear the search field
	jQuery( '.fusionredux-sidebar a' ).click( function() {

		var $tabToActivate,
		    $tabToActivateID,
		    $fusionreduxOptionTabExtras;

		if ( '' !== jQuery( '.fusionredux_field_search' ).val() ) {
			if ( jQuery( this ).parent().hasClass( 'hasSubSections' ) ) {
				$tabToActivateID = jQuery( this ).data( 'rel' ) + 1;
			} else {
				$tabToActivateID = jQuery( this ).data( 'rel' );
			}

			$tabToActivate = '#' + $tabToActivateID + '_section_group';
			$fusionreduxOptionTabExtras = jQuery( '.fusionredux-container' ).find( '.fusionredux-section-field, .fusionredux-info-field, .fusionredux-notice-field, .fusionredux-container-group, .fusionredux-section-desc, .fusionredux-group-tab h3, .fusionredux-accordion-field' );

			// Show the correct tab

			jQuery( '.fusionredux-main' ).find( '.fusionredux-group-tab' ).not( $tabToActivate ).hide();
			jQuery( '.fusionredux-accordian-wrap' ).hide();
			$fusionreduxOptionTabExtras.show();
			jQuery( '.form-table tr' ).show();
			jQuery( '.form-table tr.hide' ).hide();
			jQuery( '.fusionredux-notice-field.hide' ).hide();

			jQuery( '.fusionredux-container' ).removeClass( 'fusion-redux-search' );
			jQuery( '.fusionredux_field_search' ).val( '' );
			jQuery( '.fusionredux_field_search' ).trigger( 'change' );
		}
	});

	jQuery( '.fusionredux_field_search' ).typeWatch({

		callback: function( $searchString ) {
			var $tab;

			$searchString = $searchString.toLowerCase();

			if ( '' !== $searchString && null !== $searchString && 'undefined' !== typeof $searchString && $searchString.length > 2 ) {
				jQuery( '.fusionredux-sidebar .fusionredux-group-menu' ).find( 'li' ).removeClass( 'activeChild' ).removeClass( 'active' );
				jQuery( '.fusionredux-sidebar .fusionredux-group-menu' ).find( '.submenu' ).hide();

			} else {
				$tab = jQuery.cookie( 'fusionredux_current_tab' );

				if ( jQuery( '#' + $tab + '_section_group_li' ).parents( '.hasSubSections' ).length ) {
					jQuery( '#' + $tab + '_section_group_li' ).parents( '.hasSubSections' ).addClass( 'activeChild' );
					jQuery( '#' + $tab + '_section_group_li' ).parents( '.hasSubSections' ).find( '.submenu' ).show();
				}
				jQuery( '#' + $tab + '_section_group_li' ).addClass( 'active' );
			}
		},

		wait: 500,
		highlight: false,
		captureLength: 0

	} );
});

jQuery.fn.set_social_media_repeater_custom_field_logic = function() {
	jQuery( this ).each( function( i, obj ) {

		var $iconSelect    = jQuery( '#icon-' + i + '-select' ),
		    $customFields  = jQuery( '#' + fusionFusionreduxVars.option_name + '-custom_title-' + i + ', #' + fusionFusionreduxVars.option_name + '-custom_source-' + i );

		// Get the initial value of the select input and depending on its value
		// show or hide the custom icon input elements
		if ( 'custom' == $iconSelect.val() ) {

			// Show input fields & headers
			$customFields.show();
			$customFields.prev().show();
		} else {

			// Hide input fields & headers
			$customFields.hide();
			$customFields.prev().hide();
		}

		if ( ! $iconSelect.val() ) {
			$iconSelect.parents( '.ui-accordion-content' ).css( 'height', '' );
		}

		// Check if the value of the select has changed and show/hide the elements conditionally.
		$iconSelect.change( function() {
			$iconSelect.parents( '.ui-accordion-content' ).css( 'height', '' );

			if ( 'custom' == jQuery( this ).val() ) {

				// Show input fields & headers
				$customFields.show();
				$customFields.prev().show();
			} else {

				// Hide input fields & headers
				$customFields.hide();
				$customFields.prev().hide();
			}
		});
	});
};

jQuery( window ).load( function() {

	var $hrefTarget,
	    $optionTarget,
	    $tabTarget,
	    $adminbarHeight,
	    $theTarget;

	// Check option name and open relevant tab.
	if ( location.hash ) {
		$hrefTarget = window.location.href.split( '#' );

		// If it doesn't contains tab- then assume as option.
		if ( $hrefTarget[1].indexOf( 'tab-' ) == -1 ) {
			$optionTarget   = '.fusion_theme_options-' + $hrefTarget[1];
			$tabTarget      = jQuery( $optionTarget ).parents( '.fusionredux-group-tab' ).data( 'rel' );
			$adminbarHeight = 0;

			if ( $tabTarget ) {

				// Check if target element exists.
				$theTarget = jQuery( 'a[data-key="' + $tabTarget + '"]' );
				if ( $theTarget ) {
					setTimeout( function() {

						// Open desired tab.
						jQuery( 'a[data-key="' + $tabTarget + '"]' ).click();
						if ( 'heading_shortcode_styling' == $theTarget.data( 'css-id' ) || 'fusion_builder_elements' == $theTarget.data( 'css-id' ) || 'fusion_builder_addons' == $theTarget.data( 'css-id' ) ) {
							jQuery( $optionTarget ).parents( '.fusionredux-accordian-wrap' ).prev( 'div' ).click();
						}
						setTimeout( function() {

							// Scroll to the desired option.
							if ( jQuery( '#wpadminbar' ).length ) {
								$adminbarHeight = parseInt( jQuery( '#wpadminbar' ).outerHeight() );
							}
							jQuery( 'html, body' ).animate({
								scrollTop: jQuery( $optionTarget ).parents( 'tr' ).offset().top - $adminbarHeight }, 450
							);
						}, 200 );
					}, 100 );
				}

			}
		} else {
			$tabTarget = $hrefTarget[1].split( '-' );
			$theTarget = jQuery( 'a[data-css-id="' + $tabTarget[1] + '"]' );

			// Check if desired tab exists.
			if ( $theTarget.length ) {

				// Open desired tab.
				setTimeout( function() {
					$theTarget.click();
				}, 100 );
			}
		}
	}
});

jQuery( document ).ready( function() {

	// Check to see if the Ajax Notification is visible.
	if ( jQuery( '#remote-media-found-in-fusion-options' ).length > 0 ) {

		jQuery( '#dismiss-fusion-redux-ajax-notification' ).click( function( event ) {

			event.preventDefault();

			// Initiate a request to the server-side
			jQuery.post( ajaxurl, {
				action: 'fusionredux_hide_remote_media_admin_notification',
				nonce: jQuery.trim( jQuery( '#fusion-redux-remote-media-ajax-notification-nonce' ).text() )
			}, function( response ) {
				if ( '1' === response || 1 === response || true === response ) {
					jQuery( '#remote-media-found-in-fusion-options' ).hide();
				}
				console.log( response );
			});
		});
	}
});
