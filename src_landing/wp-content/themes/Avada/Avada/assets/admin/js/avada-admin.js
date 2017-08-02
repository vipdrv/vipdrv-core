this.imagePreview = function() {
	jQuery( '.theme' ).hover( function( e ) {
			jQuery( this ).find( '.screenshot-hover' )
				.css( 'visibility', 'visible' );
		},
		function() {
			jQuery( this ).find( '.screenshot-hover' )
				.css( 'visibility', 'visible' );
		});
};

// Starting the script on page load.
jQuery( document ).ready( function() {

	jQuery( '.help_tip' ).tipTip({
		attribute: 'data-tip'
	});

	jQuery( 'a.help_tip' ).click( function() {
		return false;
	});

	jQuery( 'a.debug-report' ).click( function() {

		var report = '';

		jQuery( '.avada-system-status table:not(.fusion-system-status-debug) thead, .avada-system-status:not(.fusion-system-status-debug) tbody' ).each( function() {

			var label;

			if ( jQuery( this ).is( 'thead' ) ) {

				label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
				report = report + '\n### ' + jQuery.trim( label ) + ' ###\n\n';

			} else {

				jQuery( 'tr', jQuery( this ) ).each( function() {

					var label           = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text(),
					    theName         = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ), // Remove HTML.
					    theValueElement = jQuery( this ).find( 'td:eq(2)' ),
					    theValue,
					    valueArray,
					    output,
					    tempLine;

					if ( jQuery( theValueElement ).find( 'img' ).length >= 1 ) {
						theValue = jQuery.trim( jQuery( theValueElement ).find( 'img' ).attr( 'alt' ) );
					} else {
						theValue = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
					}
					valueArray = theValue.split( ', ' );

					if ( valueArray.length > 1 ) {

						// If value have a list of plugins ','
						// Split to add new line.
						output   = '';
						tempLine = '';
						jQuery.each( valueArray, function( key, line ) {
							tempLine = tempLine + line + '\n';
						});

						theValue = tempLine;
					}

					report = report + '' + theName + ': ' + theValue + '\n';
				});

			}
		});

		try {
			jQuery( '#debug-report' ).slideDown();
			jQuery( '#debug-report textarea' ).val( report ).focus().select();
			jQuery( this ).parent().fadeOut();
			return false;
		} catch ( e ) {
		}

		return false;
	});

	jQuery( '#copy-for-support' ).tipTip({
		'attribute':  'data-tip',
		'activation': 'click',
		'fadeIn':     50,
		'fadeOut':    50,
		'delay':      0
	});

	jQuery( 'body' ).on( 'copy', '#copy-for-support', function( e ) {
		e.clipboardData.clearData();
		e.clipboardData.setData( 'text/plain', jQuery( '#debug-report textarea' ).val() );
		e.preventDefault();
	});
});

jQuery( document ).ready( function( e ) {

	var importedLabel,
	    importStagesLength,
	    removeStagesLength,
	    demoType,
	    disablePreview = jQuery( '.preview-all' ),
	    importerDialog = jQuery( '#dialog-demo-confirm' ),
	    importNotifications,
	    prepareDemoImport,
	    importDemo,
	    prepareDemoRemove,
	    removeDemo,
	    importReport,
	    findOne;

	if ( jQuery( 'body' ).hasClass( 'avada_page_avada-demos' ) ) {

		// If clicked on import data button.
		jQuery( '.button-install-demo' ).on( 'click', function( e ) {
			var selectedDemo = demoType;

			importNotifications = {
				'classic': avadaAdminL10nStrings.classic,
				'caffe': avadaAdminL10nStrings.caffe,
				'church': avadaAdminL10nStrings.church,
				'modern_shop': avadaAdminL10nStrings.modern_shop,
				'classic_shop': avadaAdminL10nStrings.classic_shop,
				'landing_product': avadaAdminL10nStrings.landing_product,
				'forum': avadaAdminL10nStrings.forum,
				'technology': avadaAdminL10nStrings.technology,
				'creative': avadaAdminL10nStrings.creative,
				'default': avadaAdminL10nStrings['default']
			};

			if ( importNotifications.hasOwnProperty( demoType ) ) {
				importerDialog.html( importNotifications[ demoType ] );
			} else {
				importerDialog.html( importNotifications['default'] );
			}

			jQuery( '#' + importerDialog.attr( 'id' ) ).dialog({
				dialogClass: 'avada-demo-dialog',
				resizable: false,
				draggable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					Cancel: function() {
						importerDialog.html( '' );
						jQuery( this ).dialog( 'close' );
					},
					'OK': function() {
						prepareDemoImport();
						importerDialog.html( '' );
						jQuery( this ).dialog( 'close' );
					}
				}
			});

			e.preventDefault();
		});

		importReport = function( message, progress ) {
			jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( message );

			jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-progress-bar' ).css( 'width', 100 * progress + '%' );
		};

		importDemo = function( data ) {

			if ( data.importStages.length === importStagesLength ) {
				importReport( avadaAdminL10nStrings.currently_processing.replace( '%s', avadaAdminL10nStrings.download ), ( importStagesLength - data.importStages.length ) / importStagesLength );
			}

			jQuery.post( ajaxurl, data, function( response ) {
				var importLabel;

				if ( 'content' == data.importStages[0] ) {

					jQuery.each( jQuery( '#import-' + data.demoType + ' input:checkbox[data-type=content]:checked' ), function( ) {

						jQuery( this ).prop( 'disabled', true );

						jQuery( '#remove-' + data.demoType + ' input:checkbox[value=' + jQuery( this ).val() + ']' ).prop( 'checked', true );
					});
				} else {
					jQuery( '#import-' + data.demoType + ' input:checkbox[value=' + data.importStages[0] + ']' ).prop( 'disabled', true );
					jQuery( '#remove-' + data.demoType + ' input:checkbox[value=' + data.importStages[0] + ']' ).prop( 'checked', true );
				}

				data.importStages.shift();

				if ( 0 < response.indexOf( 'partially completed' ) && 0 < data.importStages.length ) {

					if ( 'content' === data.importStages[0] ) {
						if ( 1 === data.contentTypes.length ) {
							importLabel = jQuery( 'label[for=import-' + data.contentTypes[0] + '-' + demoType + ']' ).html();
						} else {
							importLabel = avadaAdminL10nStrings.content;
						}
					} else if ( 'general_data' === data.importStages[0] ) {
						importLabel = 'General Data';
					} else {
						importLabel = jQuery( 'label[for=import-' + data.importStages[0] + '-' + demoType + ']' ).html();
					}

					importReport( avadaAdminL10nStrings.currently_processing.replace( '%s', importLabel ), ( importStagesLength - data.importStages.length ) / importStagesLength );

					importDemo( data );

				} else {
					if ( -1 === response && response.indexOf( 'imported' ) ) {
					} else if ( 1 < response.indexOf( avadaAdminL10nStrings.file_does_not_exist ) ) {
					} else {
						setTimeout( function() {
							jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'disabled', false );
							jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]' ).prop( 'disabled', true );
							jQuery( '#demo-modal-' + demoType ).removeClass( 'demo-import-in-progress' );

							importReport( '', 1 );
							jQuery( '#demo-modal-' + demoType + ' .button-done-demo' ).css( 'display', 'flex' );

							if ( true === data.allImport ) {
								importedLabel.html( avadaAdminL10nStrings.full_import );
							} else {
								importedLabel.html( avadaAdminL10nStrings.partial_import );
							}

							importedLabel.show();
							jQuery( '#theme-demo-' + demoType + ' .button-install-open-modal' ).html( avadaAdminL10nStrings.modify );
						}, 4000 );
					}
				}
			}).fail( function( xhr, textStatus, errorThrown ) {
				var message;

				if ( 'Request Timeout' == errorThrown ) {
					message = avadaAdminL10nStrings.error_timeout;
				} else {
					message = avadaAdminL10nStrings.error_php_limits;
				}

				importerDialog.html( message );
				jQuery( '#' + importerDialog.attr( 'id' ) ).dialog({
					dialogClass: 'avada-demo-dialog',
					resizable: false,
					draggable: false,
					height: 'auto',
					title: 'Import Failed',
					width: 400,
					modal: true,
					buttons: {
						'OK': function() {
							importerDialog.html( '' );
							jQuery( this ).dialog( 'close' );
							location.reload();
						}
					}
				});
			});

		};

		prepareDemoImport = function() {

			var selectedDemo     = demoType,
			    allImport        = false,
			    fetchAttachments = false,
			    data,
			    importArray,
			    importContentArray;

			importedLabel = jQuery( '#theme-demo-' + demoType + ' .demo-imported' );

			importArray        = ['download'];
			importContentArray = [];

			jQuery( '#import-' + demoType + ' input:checkbox:checked' ).each( function() {

				if ( ! this.disabled ) {

					if ( 'content' === this.getAttribute( 'data-type' ) ) {
						importContentArray.push( this.value );

						if ( -1 === importArray.indexOf( 'content' ) ) {
							importArray.push( 'content' );
						}

					} else {
						importArray.push( this.value );
					}

				}

				if ( 'all' === this.value ) {
					this.disabled = true;
					allImport = true;
				}
			});

			// If 'all' is selected menus should be imported and home page set (which is done at the end of the process).
			if ( -1 !== importArray.indexOf( 'all' ) ) {
				importArray.splice( importArray.indexOf( 'all' ), 1 );
				importArray.push( 'general_data' );
			}

			if ( 0 < importContentArray.length && -1 !== importContentArray.indexOf( 'attachment' ) ) {
				fetchAttachments = true;
			}

			importStagesLength = importArray.length;

			data = {
				action: 'fusion_import_demo_data',
				security: DemoImportNonce,
				demoType: demoType,
				importStages: importArray,
				contentTypes: importContentArray,
				fetchAttachments: fetchAttachments,
				allImport: allImport
			};

			jQuery( '#demo-modal-' + demoType ).addClass( 'demo-import-in-progress' );
			jQuery( '.button-install-demo[data-demo-id=' + demoType + ']' ).css( 'display', 'none' );

			importDemo( data );
		};

		removeDemo = function( data ) {

			var removeLabel;

			if ( 'content' === data.removeStages[0] ) {
				removeLabel = avadaAdminL10nStrings.content;
			} else {
				removeLabel = jQuery( 'label[for=remove-' + data.removeStages[0] + '-' + demoType + ']' ).html();
			}

			if ( data.removeStages.length === removeStagesLength ) {
				importReport( avadaAdminL10nStrings.currently_processing.replace( '%s', removeLabel ), ( removeStagesLength - data.removeStages.length ) / removeStagesLength );
			}

			jQuery.post( ajaxurl, data, function( $response ) {

				if ( 'content' == data.removeStages[0] ) {

					jQuery.each( jQuery( '#remove-' + data.demoType + ' input:checkbox[data-type=content]:checked' ), function( ) {

						jQuery( this ).prop( 'disabled', true );
						jQuery( this ).prop( 'checked', false );

						jQuery( '#import-' + data.demoType + ' input:checkbox[value=' + jQuery( this ).val() + ']' ).prop( 'checked', false );
						jQuery( '#import-' + data.demoType + ' input:checkbox[value=' + jQuery( this ).val() + ']' ).prop( 'disabled', false );
					});
				} else {
					jQuery( '#remove-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']' ).prop( 'disabled', true );
					jQuery( '#remove-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']' ).prop( 'checked', false );
					jQuery( '#import-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']' ).prop( 'checked', false );
					jQuery( '#import-' + data.demoType + ' input:checkbox[value=' + data.removeStages[0] + ']' ).prop( 'disabled', false );
				}

				data.removeStages.shift();

				if ( 0 <= $response.indexOf( 'partially removed' ) && 0 < data.removeStages.length  ) {
					importReport( avadaAdminL10nStrings.currently_processing.replace( '%s', removeLabel ), ( removeStagesLength - data.removeStages.length ) / removeStagesLength );

					removeDemo( data );

				} else {
					importReport( '', 1 );
					jQuery( '#demo-modal-' + demoType + ' .button-done-demo' ).css( 'display', 'flex' );
					importedLabel.hide();
					jQuery( '#theme-demo-' + demoType + ' .button-install-open-modal' ).html( avadaAdminL10nStrings['import'] );

					jQuery( '#import-' + demoType + ' input[type="checkbox"][value="all"]' ).prop( 'checked', false );
					jQuery( '#import-' + demoType + ' input[type="checkbox"]:not(:checked)' ).prop( 'disabled', false );
					jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'disabled', true );
					jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'checked', false );

					jQuery( '#demo-modal-' + demoType ).removeClass( 'demo-import-in-progress' );
				}

			}).fail( function() {
			});

		};

		prepareDemoRemove = function() {
			var data,
			    removeArray = [];

			importedLabel = jQuery( '#theme-demo-' + demoType + ' .demo-imported' );
			jQuery( '#remove-' + demoType + ' input:checkbox:checked' ).each(function() {

				if ( 'content' === this.getAttribute( 'data-type' ) ) {

					if ( -1 === removeArray.indexOf( 'content' ) ) {
						removeArray.push( 'content' );
					}

				} else {
					removeArray.push( this.value );
				}

			});
			removeStagesLength = removeArray.length;

			data = {
				action: 'fusion_remove_demo_data',
				demoType: demoType,
				security: DemoImportNonce,
				removeStages: removeArray
			};

			jQuery( '#demo-modal-' + demoType ).addClass( 'demo-import-in-progress' );
			jQuery( '.button-uninstall-demo[data-demo-id=' + demoType + ']' ).css( 'display', 'none' );

			removeDemo( data );
		};

		// If clicked on remove demo button.
		jQuery( '.button-uninstall-demo' ).on( 'click', function( e ) {

			importerDialog.html( avadaAdminL10nStrings.remove_demo );

			jQuery( '#' + importerDialog.attr( 'id' ) ).dialog({
				dialogClass: 'avada-demo-dialog',
				resizable: false,
				draggable: false,
				height: 'auto',
				width: 400,
				modal: true,
				buttons: {
					Cancel: function() {
						importerDialog.html( '' );
						jQuery( this ).dialog( 'close' );
					},
					'OK': function() {
						prepareDemoRemove();
						importerDialog.html( '' );
						jQuery( this ).dialog( 'close' );
					}
				}
			});

			e.preventDefault();

		});

		jQuery( '.demo-import-form input:checkbox' ).on( 'change', function( e ) {

			var form = jQuery( this ).closest( 'form' );

			if ( 'all' === jQuery( this ).val() ) {

				// 'all' checkbox is checked.

				form.find( 'input:checkbox:not(:disabled)' ).prop( 'checked', jQuery( this ).prop( 'checked' ) );

				if ( jQuery( this ).is( ':checked' ) ) {
					jQuery( '.button-install-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'flex' );

					jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( '' );
					jQuery( '#demo-modal-' + demoType + ' .button-done-demo' ).css( 'display', 'none' );
				} else {
					jQuery( '.button-install-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'none' );
				}

			} else if ( form.find( 'input[type="checkbox"]:checked' ).not( ':disabled' ).length > 0 ) {

				// Checkbox is checked, but there could be disabled (previously imported) checkboxes as well.

				jQuery( '.button-install-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'flex' );

				// We want to check 'all' if all checkboxes are selected and there are not "disabled" among them.
				if ( ! form.find( 'input[type="checkbox"]:checked' ).is( ':disabled' ) ) {

					// -1 is excluding 'all' checkbox.
					if ( ( form.find( 'input[type="checkbox"]' ).length - 1 ) === form.find( 'input[type="checkbox"]:checked' ).length ) {
						jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]' ).prop( 'checked', true );
					}
				}

				jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( '' );
				jQuery( '#demo-modal-' + demoType + ' .button-done-demo' ).css( 'display', 'none' );

				jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'disabled', true );
			} else {

				// Checkbox is unchecked.

				jQuery( '.button-install-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'none' );

				jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'disabled', false );
			}

			// Uncheck 'all' if checkbox was unchecked.
			if ( false === jQuery( this ).prop( 'checked' ) ) {
				jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="all"]' ).prop( 'checked', false );
			}

		});

		jQuery( '.demo-remove-form input:checkbox[value="uninstall"]' ).on( 'change', function( e ) {

			if ( jQuery( this ).is( ':checked' ) ) {
				jQuery( '.button-uninstall-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'flex' );

				jQuery( '#import-' + demoType + ' input[type="checkbox"]' ).prop( 'disabled', true );
				jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( '' );
				jQuery( '#demo-modal-' + demoType + ' .button-done-demo' ).css( 'display', 'none' );
			} else {
				jQuery( '.button-uninstall-demo[data-demo-id="' + demoType + '"]' ).css( 'display', 'none' );

				jQuery.each( jQuery( '#import-' + demoType + ' input[type="checkbox"]:not(:checked)' ), function() {
					if ( 'all' !== jQuery( this ).val() ) {
						jQuery( this ).prop( 'disabled', false );
					}
				});
			}

		});

		jQuery( '.button-install-open-modal' ).on( 'click', function( e ) {
			e.preventDefault();

			demoType = jQuery( this ).data( 'demo-id' );

			if ( 0 === jQuery( '#import-' + demoType ).find( 'input[type="checkbox"]:checked' ).length ) {
				jQuery( '#demo-modal-' + demoType + ' input[type="checkbox"][value="uninstall"]' ).prop( 'disabled', true );
			} else {
				jQuery( '#import-' + demoType + ' input[type="checkbox"][value="all"]' ).prop( 'disabled', true );
			}

			jQuery( 'body' ).addClass( 'fusion_builder_no_scroll' );
			disablePreview.show();

			jQuery( '#demo-modal-' + jQuery( this ).data( 'demo-id' ) ).css( 'display', 'block' );
		});

		jQuery( '.demo-update-modal-close' ).on( 'click', function( e ) {
			e.preventDefault();
			jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( '' );

			// Uncheck all checkboxes which aren't disabled (imported).
			jQuery( '#import-' + demoType ).find( 'input[type="checkbox"]:checked' ).not( ':disabled' ).prop( 'checked', false ).trigger( 'change' );

			demoType = null;
			jQuery( 'body' ).removeClass( 'fusion_builder_no_scroll' );
			disablePreview.hide();

			jQuery( this ).closest( '.demo-update-modal-wrap' ).css( 'display', 'none' );

		});

		if ( 'undefined' !== typeof allTags ) {

			// The tag-selector for demos.
			_.each( allTags, function( tagName, tagSlug ) {
				var tagButtonSelector = '.avada-importer-tags-selector button[data-tag="' + tagSlug + '"]';

				// When we click on a tag button.
				jQuery( tagButtonSelector ).click( function() {

					// De-select all buttons.
					jQuery( '.avada-importer-tags-selector button' ).removeClass( 'button-primary' );
					jQuery( '.avada-importer-tags-selector button' ).addClass( 'button-secondary' );

					// Select the current button.
					jQuery( this ).addClass( 'button-primary' );

					// Hide all demos except the ones corresponding to the tag we selected.
					jQuery( '.avada-demo-themes .theme' ).each( function() {
						var demo     = this,
						    demoTags = jQuery( this ).data( 'tags' ).split( ',' );

						if ( 'all' === tagSlug ) {
							jQuery( demo ).show();
						} else {
							jQuery( demo ).hide();
							_.each( demoTags, function( demoTag ) {
								if ( demoTag === tagSlug ) {
									jQuery( demo ).show();
								}
							});
						}
					});
				});
			});
		}

		/**
		 * @description determine if an array contains one or more items from another array.
		 * @param {array} haystack the array to search.
		 * @param {array} arr the array providing items to check for in the haystack.
		 * @return {boolean} true|false if haystack contains at least one item from arr.
		 */
		findOne = function( haystack, arr ) {
			return arr.some(function( v ) {
				return haystack.indexOf( v ) >= 0;
		    });
		};

	}

	if ( jQuery( 'body' ).hasClass( 'avada_page_avada-plugins' ) ) {

		jQuery( '.avada-install-plugins .theme-actions .button-primary.disabled' ).on( 'click', function( e ) {

			var confirm;

			e.preventDefault();

			if ( jQuery( this ).hasClass( 'fusion-builder' ) ) {
				confirm = window.alert( avadaAdminL10nStrings.update_fc.replace( '%s', jQuery( this ).data( 'version' ) ) );
			} else {
				confirm = window.alert( avadaAdminL10nStrings.register_first );
			}
		});

		jQuery( '#manage-plugins' ).on( 'click', function( e ) {

			var href              = jQuery( this ).attr( 'href' ),
			    hrefHash          = href.substr( href.indexOf( '#' ) ).slice( 1 ),
			    target            = jQuery( '#' + hrefHash ),
			    adminbarHeight    = jQuery( '#wpadminbar' ).height(),
			    newScrollPosition = target.offset().top - adminbarHeight;

			e.preventDefault();

			jQuery( 'html, body' ).animate({
				scrollTop: newScrollPosition
			}, 450 );
		});
	}

	jQuery( '.demo-required-plugins .activate a' ).on( 'click', function( e ) {

		var $this = jQuery( this ),
			data = {
				action: 'fusion_activate_plugin',
				avada_activate: 'activate-plugin',
				plugin: $this.data( 'plugin' ),
				plugin_name: $this.data( 'plugin_name' ),
				avada_activate_nonce: $this.data( 'nonce' )
			};

		$this.addClass( 'installing' );

		jQuery.get( ajaxurl, data, function( response ) {

			if ( true !== response.error ) {

				jQuery.each( jQuery( '.required-plugin-status a[data-plugin=' + data.plugin + ']' ), function( index, element ) {
					jQuery( element ).html( avadaAdminL10nStrings.plugin_active ).css( 'pointer-events', 'none' );
					jQuery( element ).parent().removeClass( 'activate' ).addClass( 'active' );
				});

			} else {
				jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( avadaAdminL10nStrings.plugin_install_failed );
			}

			$this.removeClass( 'installing' );
		}, 'json' );

		e.preventDefault();
	});

	jQuery( '.demo-required-plugins .install a' ).on( 'click', function( e ) {

		var $this = jQuery( this ),
			data = {
				action: 'fusion_install_plugin',
				avada_activate: 'activate-plugin',
				plugin: $this.data( 'plugin' ),
				plugin_name: $this.data( 'plugin_name' ),
				avada_activate_nonce: $this.data( 'nonce' ),
				page: 'install-required-plugins'
			};

		// 'page' arg needed so 'avada_get_required_and_recommened_plugins' sets proper plugin URL.

		data['tgmpa-install'] = 'install-plugin';
		data['tgmpa-nonce']   =  $this.data( 'tgmpa_nonce' );

		// Disable parallel plugin install
		jQuery( '#demo-modal-' + demoType ).addClass( 'plugin-install-in-progress' );

		$this.addClass( 'installing' );

		jQuery.get( ajaxurl, data, function( response ) {

			if ( 0 < response.indexOf( 'plugins.php?action=activate' ) ) {

				jQuery.each( jQuery( '.required-plugin-status a[data-plugin=' + data.plugin + ']' ), function( index, element ) {
					jQuery( element ).html( avadaAdminL10nStrings.plugin_active ).css( 'pointer-events', 'none' );
					jQuery( element ).parent().removeClass( 'install' ).addClass( 'active' );
				});

			} else {
				jQuery( '#demo-modal-' + demoType  + ' .demo-update-modal-status-bar-label span' ).html( avadaAdminL10nStrings.plugin_install_failed );
			}

			$this.removeClass( 'installing' );

			jQuery( '#demo-modal-' + demoType ).removeClass( 'plugin-install-in-progress' );
		}, 'html' );

		e.preventDefault();
	});
});
