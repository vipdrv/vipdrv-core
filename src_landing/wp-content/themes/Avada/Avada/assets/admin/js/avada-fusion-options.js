jQuery( document ).ready( function() {

	var $rangeSlider,
	    $i,
		$defaultReset;

	jQuery( '.pyre_field select:not(.hidden-sidebar)' ).select2({
		minimumResultsForSearch: 10,
		dropdownCssClass: 'avada-select2'
	});

	jQuery( '.pyre_field.avada-buttonset a' ).on( 'click', function( e ) {
		var $radiosetcontainer;

		e.preventDefault();
		$radiosetcontainer = jQuery( this ).parents( '.fusion-form-radio-button-set' );
		$radiosetcontainer.find( '.ui-state-active' ).removeClass( 'ui-state-active' );
		jQuery( this ).addClass( 'ui-state-active' );
		$radiosetcontainer.find( '.button-set-value' ).val( $radiosetcontainer.find( '.ui-state-active' ).data( 'value' ) ).trigger( 'change' );
	});

	jQuery( '.pyre_field.avada-color input' ).each( function() {
		var self = jQuery( this ),
			$defaultReset = self.parents( '.pyre_metabox_field' ).find( '.pyre-default-reset' );

		// Picker with default.
		if ( jQuery( this ).data( 'default' ) &&  jQuery( this ).data( 'default' ).length ) {
			 jQuery( this ).wpColorPicker( {
				change: function( event, ui ) {
					colorChange( ui.color.toString(), self, $defaultReset );
				},
				clear: function( event, ui ) {
					colorClear( event, self );
				}
			} );

			// Make it so the reset link also clears color.
			$defaultReset.on( 'click', 'a', function( event ) {
				event.preventDefault();
				colorClear( event, self );
			});

		// Picker without default.
		} else {
			 jQuery( this ).wpColorPicker( {

			} );
		}

		// For some reason non alpha are not triggered straight away.
		if ( true !== jQuery( this ).data( 'alpha' ) ) {
			jQuery( this ).wpColorPicker().change();
		}
	});

	function avadaCheckDependency( $currentValue, $desiredValue, $comparison ) {
		var $passed = false;
		if ( '==' === $comparison ) {
			if ( $currentValue == $desiredValue ) {
				$passed = true;
			}
		}
		if ( '=' === $comparison ) {
			if ( $currentValue = $desiredValue ) {
				$passed = true;
			}
		}
		if ( '>=' === $comparison ) {
			if ( $currentValue >= $desiredValue ) {
				$passed = true;
			}
		}
		if ( '<=' === $comparison ) {
			if ( $currentValue <= $desiredValue ) {
				$passed = true;
			}
		}
		if ( '>' === $comparison ) {
			if ( $currentValue > $desiredValue ) {
				$passed = true;
			}
		}
		if ( '<' === $comparison ) {
			if ( $currentValue < $desiredValue ) {
				$passed = true;
			}
		}
		if ( '!=' === $comparison ) {
			if ( $currentValue != $desiredValue ) {
				$passed = true;
			}
		}

		return $passed;
	}
	function avadaLoopDependencies( $container ) {
		var $passed = false;
		$container.find( 'span' ).each( function() {

			var $value = jQuery( this ).data( 'value' ),
				$comparison = jQuery( this ).data( 'comparison' ),
				$field = jQuery( this ).data( 'field' );
			$passed = avadaCheckDependency( jQuery( '#pyre_' + $field ).val(), $value, $comparison );
			return $passed;
		});
		if ( $passed ) {
			 $container.parents( '.pyre_metabox_field' ).fadeIn( 300 );
		} else {
			 $container.parents( '.pyre_metabox_field' ).hide();
		}
	}

	jQuery( '.avada-dependency' ).each( function() {
		avadaLoopDependencies( jQuery( this ) );
	});
	jQuery( '[id*="pyre"]' ).on( 'change', function() {
		var $id = jQuery( this ).attr( 'id' ),
			$field = $id.replace( 'pyre_', '' );
		jQuery( 'span[data-field="' + $field + '"]' ).each( function() {
			avadaLoopDependencies( jQuery( this ).parents( '.avada-dependency' ) );
		});
	});

	function createSlider( $slide, $targetId, $rangeInput, $min, $max, $step, $value, $decimals, $rangeDefault, $hiddenValue, $defaultValue, $direction ) {

		// Create slider with values passed on in data attributes.
		var $slider = noUiSlider.create( $rangeSlider[$slide], {
				start: [ $value ],
				step: $step,
				direction: $direction,
				range: {
					'min': $min,
					'max': $max
				},
				format: wNumb({
					decimals: $decimals
				})
			}),
		    $notFirst = false;

		// Check if default is currently set.
		if ( $rangeDefault && '' === $hiddenValue.val() ) {
			$rangeDefault.parent().addClass( 'checked' );
		}

		// If this range has a default option then if checked set slider value to data-value.
		if ( $rangeDefault ) {
			$rangeDefault.on( 'click', function( e ) {
				e.preventDefault();
				$rangeSlider[$slide].noUiSlider.set( $defaultValue );
				$hiddenValue.val( '' );
				jQuery( this ).parent().addClass( 'checked' );
			});
		}

		// On slider move, update input
		$slider.on( 'update', function( values, handle ) {
			if ( $rangeDefault && $notFirst ) {
				$rangeDefault.parent().removeClass( 'checked' );
				$hiddenValue.val( values[handle] );
			}
			$notFirst = true;
			jQuery( this.target ).closest( '.fusion-slider-container' ).prev().val( values[handle] );
			jQuery( '#' + $targetId ).trigger( 'change' );
		});

		// On manual input change, update slider position
		$rangeInput.on( 'keyup', function( values, handle ) {
			if ( $rangeDefault ) {
				$rangeDefault.parent().removeClass( 'checked' );
				$hiddenValue.val( values[handle] );
			}

			if ( this.value !== $rangeSlider[$slide].noUiSlider.get() ) {
				$rangeSlider[$slide].noUiSlider.set( this.value );
			}
		});
	}

	$rangeSlider = jQuery( '.pyre_field.avada-range .fusion-slider-container' );

	if ( $rangeSlider.length ) {

		// Counter variable for sliders
		$i = 0;

		// Method for retreiving decimal places from step
		Number.prototype.countDecimals = function() {
		    if ( Math.floor( this.valueOf() ) === this.valueOf() ) {
				return 0;
			}
		    return this.toString().split( '.' )[1].length || 0;
		};

		// Each slider on page, determine settings and create slider
		$rangeSlider.each( function() {

			var $targetId     = jQuery( this ).data( 'id' ),
			    $rangeInput   = jQuery( this ).prev( '.fusion-slider-input' ),
			    $min          = jQuery( this ).data( 'min' ),
			    $max          = jQuery( this ).data( 'max' ),
			    $step         = jQuery( this ).data( 'step' ),
				$direction    = jQuery( this ).data( 'direction' ),
			    $value        = $rangeInput.val(),
			    $decimals     = $step.countDecimals(),
			    $rangeDefault = ( jQuery( this ).parents( '.pyre_metabox_field' ).find( '.fusion-range-default' ).length ) ? jQuery( this ).parents( '.pyre_metabox_field' ).find( '.fusion-range-default' ) : false,
			    $hiddenValue  = ( $rangeDefault ) ? jQuery( this ).parent().find( '.fusion-hidden-value' ) : false,
			    $defaultValue = ( $rangeDefault ) ? jQuery( this ).parents( '.pyre_metabox_field' ).find( '.fusion-range-default' ).data( 'default' ) : false;

			createSlider( $i, $targetId, $rangeInput, $min, $max, $step, $value, $decimals, $rangeDefault, $hiddenValue, $defaultValue, $direction );

			$i++;
		});

	}

	function colorChange( value, self, defaultReset ) {
		var defaultColor = self.data( 'default' );

		if ( value === defaultColor ) {
			defaultReset.addClass( 'checked' );
		} else {
			defaultReset.removeClass( 'checked' );
		}

		if ( '' === value && null !== defaultColor ) {
			self.val( defaultColor );
			self.change();
			self.val( '' );
		}
	}

	function colorClear( event, self ) {
		var defaultColor = self.data( 'default' );

		if ( null !== defaultColor ) {
			self.val( defaultColor );
			self.change();
			self.val( '' );
			self.parent().parent().find( '.wp-color-result' ).css( 'background-color', defaultColor );
		}
	}
});
