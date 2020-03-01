<?php
/**
 * Extra Redux Validation functions.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

if ( ! function_exists( 'fusion_redux_validate_dimension' ) ) {
	/**
	 * Validates & sanitizes values for dimension controls.
	 *
	 * @since 4.0.0
	 * @param array  $field          The field with all its arguments.
	 * @param string $value          The field value.
	 * @param string $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_dimension( $field, $value, $existing_value ) {

		$return = array();

		$value = trim( strtolower( $value ) );
		if ( in_array( $value, array( 'auto', 'initial', 'inherit' ), true ) ) {
			return array(
				'value' => $value,
			);
		}
		$warning = false;

		if ( 'round' === $value ) {
			$value = '50%';
		}

		if ( '' === $existing_value || null === $existing_value || false === $existing_value && class_exists( 'Avada' ) ) {
			$existing_value = Avada()->settings->get( $field['id'] );
		}

		if ( '' === $value || null === $value || false === $value ) {
			$value = $existing_value;
		}

		// If using calc() return the value.
		if ( false !== strpos( $value, 'calc' ) ) {
			return array(
				'warning' => $field,
				'value'   => $value,
			);
		}

		// Remove spaces from the value.
		$value = trim( str_replace( ' ', '', $value ) );
		// Get the numeric value.
		$value_numeric = Fusion_Sanitize::number( $value );
		if ( empty( $value_numeric ) ) {
			$value_numeric = '0';
		}
		// Get the units.
		$value_unit = str_replace( $value_numeric, '', $value );
		$value_unit = strtolower( $value_unit );
		if ( '0' !== $value_numeric && empty( $value_unit ) ) {
			$warning = true;
		}

		// An array of valid CSS units.
		$valid_units = array( 'rem', 'em', 'ex', '%', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'vh', 'vw', 'vmin', 'vmax' );

		// If we can't find a valid CSS unit in the value,
		// show a warning message and fallback to using pixels.
		if ( '0' !== $value_numeric && ! in_array( $value_unit, $valid_units, true ) ) {
			$warning = true;
		}

		// If the numeric value is 0, remove units.
		if ( '0' === $value_numeric ) {
			$value_unit = '';
		}

		if ( $warning ) {
			$replaced_units_message = esc_html__( 'We could not find a valid unit for this field, falling back to "%1$s". Saved value "%2$s" and not "%3$s".', 'Avada' );
			$units_message          = esc_html__( 'No units were entered, falling back to using pixels. Saved value "%2$s" and not "%3$s".', 'Avada' );
			if ( empty( $value_unit ) ) {
				$message    = $units_message;
				$value_unit = 'px';
				$unit_found = true;
			} else {
				$message    = $replaced_units_message;
				$unit_found = false;
				foreach ( $valid_units as $valid_unit ) {
					if ( $unit_found ) {
						continue;
					}
					if ( false !== strrpos( $value_unit, $valid_unit ) ) {
						$value_unit = $valid_unit;
						$unit_found = true;
					}
				}
			}
			if ( ! $unit_found ) {
				$value_unit = 'px';
			}
			$field['msg']      = sprintf( $message, $value_unit, $value_numeric . $value_unit, $value );
			$return['warning'] = $field;
		}

		$return['value'] = $value_numeric . $value_unit;

		return $return;

	}
}// End if().

if ( ! function_exists( 'fusion_redux_validate_font_size' ) ) {
	/**
	 * Validates & sanitizes values for font-size controls.
	 *
	 * @since 4.0.0
	 * @param array  $field          The field with all its arguments.
	 * @param string $value          The field value.
	 * @param string $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_font_size( $field, $value, $existing_value ) {
		$warning = false;
		$value = trim( strtolower( $value ) );

		$return = array();

		if ( '' === $existing_value || null === $existing_value || false === $existing_value && class_exists( 'Avada' ) ) {
			$existing_value = Avada()->settings->get( $field['id'] );
		}

		if ( '' === $value || null === $value || false === $value ) {
			$value = $existing_value;
		}

		// Remove spaces from the value.
		$value = trim( str_replace( ' ', '', $value ) );
		// Get the numeric value.
		$value_numeric = Fusion_Sanitize::number( $value );
		if ( empty( $value_numeric ) ) {
			$value_numeric = '0';
		}
		// Get the units.
		$value_unit = str_replace( $value_numeric, '', $value );
		$value_unit = strtolower( $value_unit );
		if ( empty( $value_unit ) ) {
			$warning = true;
		}

		// An array of valid CSS units.
		$valid_units = array( 'rem', 'em', 'px' );

		// If we can't find a valid CSS unit in the value.
		// show a warning message and fallback to using pixels.
		if ( ! in_array( $value_unit, $valid_units, true ) ) {
			$warning = true;
		}

		if ( $warning ) {
			$replaced_units_message = esc_html__( 'We could not find a valid unit for this field, falling back to "%1$s". Valid units are %4$s. Saved value "%2$s" and not "%3$s.".', 'Avada' );
			$units_message          = esc_html__( 'No units were entered, falling back to using pixels. Saved value "%2$s" and not "%3$s".', 'Avada' );
			if ( empty( $value_unit ) ) {
				$message    = $units_message;
				$value_unit = 'px';
				$unit_found = true;
			} else {
				$message    = $replaced_units_message;
				$unit_found = false;
				foreach ( $valid_units as $valid_unit ) {
					if ( $unit_found ) {
						continue;
					}
					if ( false !== strrpos( $value_unit, $valid_unit ) ) {
						$value_unit = $valid_unit;
						$unit_found = true;
					}
				}
			}
			if ( ! $unit_found ) {
				$value_unit = 'px';
			}
			$imploded_valid_units = implode( ', ', $valid_units );
			$field['msg']         = sprintf( $message, $value_unit, $value_numeric . $value_unit, $value, $imploded_valid_units );
			$return['warning']    = $field;
		}

		$return['value'] = $value_numeric . $value_unit;

		return $return;

	}
}// End if().

if ( ! function_exists( 'fusion_redux_validate_typography' ) ) {
	/**
	 * Validates & sanitizes values for typography controls.
	 *
	 * @since 4.0.0
	 * @param array $field          The field with all its arguments.
	 * @param array $value          The field value.
	 * @param array $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_typography( $field, $value, $existing_value ) {

		$return = array();

		$limit_units_fields = array(
			'font-size',
			'line-height',
			'letter-spacing',
		);
		if ( is_array( $value ) ) {
			// An array of valid CSS units.
			$valid_units = array( 'px', 'rem', 'em' );
			$warning     = array();
			$message     = array();

			$imploded_valid_units = implode( ', ', $valid_units );

			foreach ( $value as $key => $subvalue ) {
				$replaced_units_message = '';
				$units_message          = '';
				$subvalue_numeric = '';
				$subvalue_unit = '';
				$warning[ $key ] = false;
				if ( in_array( $key, $limit_units_fields, true ) ) {
					if ( '' === $existing_value[ $key ] || null === $existing_value[ $key ] || false === $existing_value[ $key ] && class_exists( 'Avada' ) ) {
						$existing_value[ $key ] = Avada()->settings->get( $field['id'], $key );
					}
					if ( '' === $subvalue || null === $subvalue || false === $subvalue ) {
						$subvalue = $existing_value[ $key ];
					}
					// Remove spaces from the value.
					$subvalue = trim( str_replace( ' ', '', $subvalue ) );
					// Get the numeric value.
					$subvalue_numeric = Fusion_Sanitize::number( $subvalue );
					if ( empty( $subvalue_numeric ) ) {
						$subvalue_numeric = '0';
					}
					// Get the units.
					$subvalue_unit = str_replace( $subvalue_numeric, '', $subvalue );
					$subvalue_unit = strtolower( $subvalue_unit );
					if ( empty( $subvalue_unit ) ) {
						if ( '0' === $subvalue_numeric ) {
							if ( 'font-size' === $key ) {
								$warning[ $key ] = true;
							}
						} elseif ( 'line-height' !== $key ) {
							$warning[ $key ] = true;
						}
					}

					// If we can't find a valid CSS unit in the value,
					// show a warning message and fallback to using pixels.
					if ( ! in_array( $subvalue_unit, $valid_units, true ) ) {
						if ( ! ( 'line-height' === $key && empty( $subvalue_unit ) ) && ! ( '0' === $subvalue && 'font-size' !== $key ) ) {
							$warning[ $key ] = true;
						}
					}

					if ( true === $warning[ $key ] ) {
						if ( ! isset( $field['msg'] ) ) {
							$field['msg'] = '';
						}
						$replaced_units_message = esc_html__( 'We could not find a valid unit for %1$s, falling back to "%2$s". Valid units are %3$s. Saved value "%4$s" and not "%5$s.".', 'Avada' );
						$units_message          = esc_html__( 'No units were entered for %1$s, falling back to using pixels. Saved value "%4$s" and not "%5$s".', 'Avada' );
						if ( empty( $subvalue_unit ) ) {
							$message[]     = sprintf( $units_message, $key, $subvalue_unit, $imploded_valid_units, $subvalue_numeric . $subvalue_unit, $subvalue );
							$subvalue_unit = 'px';
							$unit_found    = true;
						} else {
							$unit_found           = false;
							foreach ( $valid_units as $valid_unit ) {
								if ( $unit_found ) {
									continue;
								}
								if ( false !== strrpos( $subvalue_unit, $valid_unit ) ) {
									$subvalue_unit = $valid_unit;
									$unit_found = true;
								}
							}
							if ( ! $unit_found ) {
								$subvalue_unit = 'px';
							}
							$message[] = sprintf( $replaced_units_message, $key, $subvalue_unit, $imploded_valid_units, $subvalue_numeric . $subvalue_unit, $subvalue );
						}
						if ( ! $unit_found ) {
							$subvalue_unit = 'px';
						}
					}
					$value[ $key ] = $subvalue_numeric . $subvalue_unit;
				}// End if().
			}// End foreach().

			// Take care of font-weight sanitization.
			if ( ! class_exists( 'Fusion_Redux_Get_GoogleFonts' ) ) {
				include_once wp_normalize_path( Fusion::$template_dir_path . '/includes/fusionredux/custom-fields/typography/googlefonts.php' );
			}
			$googlefonts    = Fusion_Redux_Get_GoogleFonts::get_instance();
			$font_family    = $value['font-family'];
			if ( isset( $googlefonts->fonts[ $font_family ] ) ) {
				$variants       = $googlefonts->fonts[ $font_family ]['variants'];
				$valid_variants = array();
				foreach ( $variants as $variant ) {
					if ( isset( $variant['id'] ) ) {
						$valid_variants[] = $variant['id'];
					}
				}
				$forced_font_weight = false;
				if ( ! isset( $value['font-weight'] ) || empty( $value['font-weight'] ) ) {
					$forced_font_weight = true;
				}
				if ( ! in_array( $value['font-weight'], $valid_variants, true ) ) {
					$forced_font_weight = true;
				}
				if ( $forced_font_weight ) {
					if ( in_array( '400', $valid_variants, true ) ) {
						$value['font-weight'] = '400';
					} elseif ( in_array( '300', $valid_variants, true ) ) {
						$value['font-weight'] = '300';
					} elseif ( in_array( '500', $valid_variants, true ) ) {
						$value['font-weight'] = '500';
					} else {
						$value['font-weight'] = $valid_variants[0];
					}
				}
			}
		}// End if().
		if ( ! empty( $message ) ) {
			$field['msg']      = implode( ' ', $message );
			$return['warning'] = $field;
		}

		$return['value'] = $value;

		return $return;

	}
}// End if().

if ( ! function_exists( 'fusion_redux_validate_dimensions' ) ) {
	/**
	 * Validates & sanitizes values for dimentions controls.
	 *
	 * @since 4.0.0
	 * @param array $field          The field with all its arguments.
	 * @param array $value          The field value.
	 * @param array $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_dimensions( $field, $value, $existing_value ) {

		$warning       = array();
		$error_message = array();

		$return = array();

		// An array of valid CSS units.
		$valid_units = array( 'rem', 'em', 'ex', '%', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'vh', 'vw', 'vmin', 'vmax' );

		if ( ! is_array( $value ) ) {
			return array(
				'value' => $value,
			);
		}
		foreach ( $value as $key => $subvalue ) {
			$warning[ $key ] = false;

			if ( 'round' === $subvalue ) {
				$value = '50%';
			}

			if ( ! isset( $existing_value[ $key ] ) || '' === $existing_value[ $key ] || null === $existing_value[ $key ] || false === $existing_value[ $key ] && class_exists( 'Avada' ) ) {
				$existing_value = Avada()->settings->get( $field['id'], $key );
			}

			if ( '' === $subvalue || null === $subvalue || false === $subvalue ) {
				if ( isset( $existing_value[ $key ] ) ) {
					$subvalue = $existing_value[ $key ];
				}
			}

			// Remove spaces from the value.
			$subvalue = trim( str_replace( ' ', '', $subvalue ) );
			// Get the numeric value.
			$subvalue_numeric = Fusion_Sanitize::number( $subvalue );
			if ( empty( $subvalue_numeric ) ) {
				$subvalue_numeric = '0';
			}
			// Get the units.
			$subvalue_unit = str_replace( $subvalue_numeric, '', $subvalue );
			$subvalue_unit = strtolower( $subvalue_unit );
			if ( empty( $subvalue_unit ) ) {
				$warning[ $key ] = true;
			}

			// If we can't find a valid CSS unit in the value,
			// show a warning message and fallback to using pixels.
			if ( ! in_array( $subvalue_unit, $valid_units, true ) ) {
				$warning[ $key ] = true;
			}

			if ( $warning[ $key ] ) {
				$replaced_units_message = esc_html__( 'We could not find a valid unit for this field, falling back to "%1$s". Saved value "%2$s" and not "%3$s".', 'Avada' );
				$units_message          = esc_html__( 'No units were entered, falling back to using pixels. Saved value "%2$s" and not "%3$s".', 'Avada' );
				if ( empty( $subvalue_unit ) ) {
					$message       = $units_message;
					$subvalue_unit = 'px';
					$subunit_found = true;
				} else {
					$message       = $replaced_units_message;
					$subunit_found = false;
					foreach ( $valid_units as $valid_unit ) {
						if ( $subunit_found ) {
							continue;
						}
						if ( false !== strrpos( $subvalue_unit, $valid_unit ) ) {
							$subvalue_unit = $valid_unit;
							$subunit_found = true;
						}
					}
				}

				if ( ! $subunit_found ) {
					$subvalue_unit = 'px';
				}
				$error_message[]   = sprintf( $message, $subvalue_unit, $subvalue_numeric . $subvalue_unit, $subvalue );

			}

			$return['value'][ $key ] = $subvalue_numeric . $subvalue_unit;

		}// End foreach().
		if ( ! empty( $error_message ) ) {
			$field['msg']      = implode( ' ', $error_message );
			$return['warning'] = $field;
		}

		return $return;

	}
}// End if().

if ( ! function_exists( 'fusion_redux_validate_color_rgba' ) ) {
	/**
	 * Validates & sanitizes values for RGBA color controls.
	 *
	 * @since 4.0.0
	 * @param array  $field          The field with all its arguments.
	 * @param string $value          The field value.
	 * @param string $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_color_rgba( $field, $value, $existing_value ) {

		$return = array();

		$error = false;
		$sanitized_value = Fusion_Sanitize::color( $value );
		$return['value'] = $sanitized_value;

		// @codingStandardsIgnoreLine
		if ( $value != $sanitized_value ) {
			$error = true;
			$field['msg'] = sprintf(
				esc_html__( 'Sanitized value and saved as %1$s instead of %2$s.', 'Avada' ),
				'<code>' . $sanitized_value . '</code>',
				'<code>' . $value . '</code>'
			);
			$return['warning'] = $field;
		}
		return $return;
	}
}

if ( ! function_exists( 'fusion_redux_validate_color_hex' ) ) {
	/**
	 * Validates & sanitizes values for HEX color controls.
	 *
	 * @since 4.0.0
	 * @param array  $field          The field with all its arguments.
	 * @param string $value          The field value.
	 * @param string $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_color_hex( $field, $value, $existing_value ) {

		$return = array();

		$error = false;
		$sanitized_value = Fusion_Sanitize::color( $value );
		if ( false !== strpos( $sanitized_value, 'rgba' ) ) {
			$sanitized_value = Fusion_Color::new_color( $sanitized_value )->to_css( 'hex' );
		}
		$return['value'] = $sanitized_value;

		// @codingStandardsIgnoreLine
		if ( $value != $sanitized_value ) {
			$error = true;
			$field['msg'] = sprintf(
				esc_html__( 'Sanitized value and saved as %1$s instead of %2$s.', 'Avada' ),
				'<code>' . $sanitized_value . '</code>',
				'<code>' . $value . '</code>'
			);
			$return['warning'] = $field;
		}
		return $return;
	}
}

if ( ! function_exists( 'fusion_redux_validate_custom_fonts' ) ) {
	/**
	 * Validates & sanitizes values for custom-fonts controls.
	 *
	 * @since 4.0.0
	 * @param array $field          The field with all its arguments.
	 * @param array $value          The field value.
	 * @param array $existing_value The previous value of the control.
	 * @return array
	 */
	function fusion_redux_validate_custom_fonts( $field, $value, $existing_value ) {
		$return = array();

		if ( isset( $value['name'] ) ) {

			foreach ( $value['name'] as $name_key => $name_value ) {
				$value['name'][ $name_key ] = trim( $name_value );
				$value['name'][ $name_key ] = esc_attr( $value['name'][ $name_key ] );
			}
		}

		return array(
			'value' => $value,
		);
	}
}
