<?php
/**
 * Fusion-Settings handler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Get & set setting values.
 */
class Fusion_Settings {

	/**
	 * A single instance of this object.
	 *
	 * @access public
	 * @var null|object
	 */
	public static $instance = null;

	/**
	 * Options array.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $options_with_id = array();

	/**
	 * Saved options array.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $saved_options = array();

	/**
	 * Cached options array.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	protected static $cached_options = array();

	/**
	 * Custom color schemes array.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $custom_color_schemes = array();

	/**
	 * The original option name.
	 * This is the untainted option name, without using any languages.
	 * If you want the property including language, use $option_name instead.
	 *
	 * @static
	 * @access protected
	 * @var string
	 */
	protected static $original_option_name = 'fusion_options';

	/**
	 * The option name including the language suffix.
	 * If you want the option name without language, use $original_option_name.
	 *
	 * @static
	 * @access protected
	 * @var string
	 */
	protected static $option_name = '';

	/**
	 * The language we're using.
	 * This is used to modify $option_name.
	 * It is the language code prefixed with a '_'
	 *
	 * @static
	 * @access public
	 * @var string
	 */
	public static $lang = '';

	/**
	 * Determine if the language has been applied to the $option_name.
	 *
	 * @static
	 * @access public
	 * @var bool
	 */
	public static $lang_applied = false;

	/**
	 * Dertermine if the current language is set to "all".
	 *
	 * @static
	 * @access protected
	 * @var bool
	 */
	protected static $language_is_all = false;

	/**
	 * Determine if we're currently upgrading/migration options.
	 *
	 * @static
	 * @access public
	 * @var bool
	 */
	public static $is_updating  = false;

	/**
	 * Access the single instance of this class.
	 *
	 * @return Fusion_Settings
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The class constructor.
	 */
	protected function __construct() {

		// Allows us to hook stuff here.
		do_action( 'fusion_settings_construct' );

		$this->multilingual_options();

		self::$saved_options        = get_option( self::get_option_name(), array() );
		self::$options_with_id      = apply_filters( 'fusion_settings_all_fields', array() );
		self::$custom_color_schemes = get_option( 'avada_custom_color_schemes' );

		// When new options are added, make sure to get options.
		add_action( 'fusion_options_added', array( $this, 'get_available_options' ) );

		add_action( 'wp_loaded', array( $this, 'get_available_options' ) );
		if ( ! self::$is_updating && $_GET && isset( $_GET['avada_update'] ) && '1' === $_GET['avada_update'] ) {
			self::$is_updating = true;
		}
	}

	/**
	 * Get all settings.
	 */
	public function get_all() {

		return get_option( self::get_option_name(), array() );

	}

	/**
	 * Gets options on wp_loaded to make sure any theme added options are also included.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function get_available_options() {
		self::$saved_options   = get_option( self::get_option_name(), array() );
		self::$options_with_id = apply_filters( 'fusion_settings_all_fields', array() );
	}
	/**
	 * Gets the value of a single setting.
	 * This is a proxy methof for _get to avoid re-processing
	 * already retrieved options.
	 *
	 * @param null|string  $setting The setting.
	 * @param false|string $subset  If the result is an array, return the value of the defined key.
	 * @param mixed        $default A forced default value.
	 * @return  string|array
	 */
	public function get( $setting = null, $subset = false, $default = null ) {

		if ( is_null( $setting ) || empty( $setting ) ) {
			return '';
		}

		if ( empty( self::$saved_options ) ) {
			self::$cached_options[ $setting ] = $this->_get( $setting, $subset, $default );
		}

		// We don't need a subset.
		if ( ! $subset || empty( $subset ) ) {
			// Cache the value.
			if ( ! isset( self::$cached_options[ $setting ] ) ) {
				self::$cached_options[ $setting ] = $this->_get( $setting, false, $default );
			}
			// Return cached value.
			return self::$cached_options[ $setting ];
		}

		// If we got this far, we need a subset.
		if ( ! isset( self::$cached_options[ $setting ] ) || ! isset( self::$cached_options[ $setting ][ $subset ] ) ) {
			if ( ! isset( self::$cached_options[ $setting ] ) ) {
				self::$cached_options[ $setting ] = array();
			}
			if ( ! isset( self::$cached_options[ $setting ] ) || ! is_array( self::$cached_options[ $setting ] ) ) {
				self::$cached_options = array();
			}
			self::$cached_options[ $setting ][ $subset ] = $this->_get( $setting, $subset, $default );
		}
		// Return the cached value.
		return self::$cached_options[ $setting ][ $subset ];

	}

	/**
	 * Gets the value of a single setting.
	 *
	 * @param null|string  $setting The setting.
	 * @param false|string $subset  If the result is an array, return the value of the defined key.
	 * @param mixed        $default A forced default value.
	 * @return  string|array
	 */
	public function _get( $setting = null, $subset = false, $default = null ) {

		if ( is_null( $setting ) || empty( $setting ) ) {
			return '';
		}

		$settings = self::$saved_options;

		if ( is_array( $settings ) && isset( $settings[ $setting ] ) ) {
			// Setting is saved so retrieve it from the db.
			$value = apply_filters( "avada_setting_get_{$setting}", $settings[ $setting ] );

			// Set correct URL scheme.
			if ( isset( self::$options_with_id[ $setting ]['type'] ) && 'media' === self::$options_with_id[ $setting ]['type'] ) {
				if ( isset( $value['url'] ) && ! empty( $value['url'] ) ) {
					$value['url'] = set_url_scheme( $value['url'] );
				}
			}

			if ( $subset ) {
				// Hack for typography fields.
				if ( isset( self::$options_with_id[ $setting ]['type'] ) && 'typography' === self::$options_with_id[ $setting ]['type'] ) {
					if ( 'font-family' === $subset ) {
						if ( isset( $value['font-family'] ) && 'select font' === strtolower( $value['font-family'] ) ) {
							// @codingStandardsIgnoreLine
							return apply_filters( "avada_setting_get_{$setting}[{$subset}]", '' );
						}
					} elseif ( 'color' === $subset ) {
						if ( isset( $value['color'] ) && ( empty( $value['color'] ) || empty( $value['color'] ) ) ) {
							if ( null !== $default ) {
								return $default;
							}
							// Get the default value. Colors should not be empty. @codingStandardsIgnoreLine
							return apply_filters( "avada_setting_get_{$setting}[{$subset}]", $this->get_default( $setting, $subset ) );
						}
					}
				}

				if ( is_array( $value ) && isset( $value[ $subset ] ) ) {
					// The subset is set so we can just return it. @codingStandardsIgnoreLine
					return apply_filters( "avada_setting_get_{$setting}[{$subset}]", $value[ $subset ] );
				} else {
					if ( null !== $default ) {
						return $default;
					}
					// If we've reached this point then the setting has not been set in the db.
					// We'll need to get the default value. @codingStandardsIgnoreLine
					return apply_filters( "avada_setting_get_{$setting}[{$subset}]", $this->get_default( $setting, $subset ) );
				}
			} else {
				// Hack for color & color-alpha fields.
				if ( isset( self::$options_with_id[ $setting ]['type'] ) && in_array( self::$options_with_id[ $setting ]['type'], array( 'color', 'color-alpha' ), true ) ) {
					if ( empty( $value ) ) {
						if ( null !== $default ) {
							return $default;
						}
						// @codingStandardsIgnoreLine
						return apply_filters( "avada_setting_get_{$setting}[{$subset}]", $this->get_default( $setting, $subset ) );
					}
				}
				// We don't want a subset so just return the value.
				return $value;
			}// End if().
		} else {
			// If we've reached this point then the setting has not been set in the db.
			// We'll need to get the default value.
			if ( $subset ) {
				if ( null !== $default ) {
					return $default;
				}
				// @codingStandardsIgnoreLine
				return apply_filters( "avada_setting_get_{$setting}[{$subset}]", $this->get_default( $setting, $subset ) );
			}
			if ( null !== $default ) {
				return $default;
			}
			return apply_filters( "avada_setting_get_{$setting}", $this->get_default( $setting ) );
		}// End if().
	}

	/**
	 * Sets the value of a single setting.
	 *
	 * @param  string                  $setting The setting.
	 * @param  string|array|bool|float $value   The value we want to set.
	 */
	public function set( $setting, $value ) {

		$settings = self::$saved_options;
		$settings[ $setting ] = $value;
		update_option( self::get_option_name(), $settings );

	}

	/**
	 * Gets the default value of a single setting.
	 *
	 * @param  null|string  $setting The setting.
	 * @param  false|string $subset If the result is an array, return the value of the defined key.
	 * @return  string|array
	 */
	public function get_default( $setting = null, $subset = false ) {

		if ( is_null( $setting ) || empty( $setting ) ) {
			return '';
		}
		if ( ! is_array( self::$options_with_id ) || ! isset( self::$options_with_id[ $setting ] ) || ! isset( self::$options_with_id[ $setting ]['default'] ) ) {
			return '';
		}

		$default = self::$options_with_id[ $setting ]['default'];

		if ( ! $subset || ! is_array( $default ) ) {
			return $default;
		}

		if ( ! isset( $default[ $subset ] ) ) {
			return '';
		}

		return $default[ $subset ];

	}

	/**
	 * Gets the option value combined with relevant description.
	 *
	 * @since	4.1
	 * @param	string $setting name of option.
	 * @param	string $subset name of subset of option.
	 * @param	string $type description of option type.
	 * @param	string $reset option name for reset.
	 * @param	array  $param Shortcode params declared while mapping.
	 * @return	string $setting_description Setting description with default value link to Element Options.
	 */
	public function get_default_description( $setting = null, $subset = false, $type = null, $reset = '', $param = '' ) {

		if ( is_null( $setting ) || empty( $setting ) ) {
			return '';
		}

		if ( 'menu' !== $type ) {
			if ( ! is_array( $subset ) ) {
				$setting_value = $this->get( $setting, $subset );
			} else {
				$setting_values = array();
				foreach ( $subset as $sub ) {
					$setting_values[] = $this->get( $setting, $sub );
				}
				$setting_value = implode( ', ', $setting_values );
			}
		}

		if ( 'rollover' === $type ) {
			$link_status = $this->get( 'link_image_rollover' );
			$zoom_status = $this->get( 'zoom_image_rollover' );
			if ( $link_status && $zoom_status ) {
				$setting_value = __( 'Link & Zoom', 'Avada' );
			}
			if ( $link_status && ! $zoom_status ) {
				$setting_value = esc_attr__( 'Link', 'Avada' );
			}
			if ( ! $link_status && $zoom_status ) {
				$setting_value = esc_attr__( 'Zoom', 'Avada' );
			}
			if ( ! $link_status && ! $zoom_status ) {
				$setting_value = esc_attr__( 'No Icons', 'Avada' );
			}
		}
		if ( 'menu' !== $type ) {
			$setting_value = ( is_array( $param ) && is_string( $setting_value ) && isset( $param['value'] ) && isset( $param['value'][ $setting_value ] ) ) ? $param['value'][ $setting_value ] : $setting_value;
			if ( false !== strpos( $this->get_setting_link( $setting, $subset ), 'header_bg_color' ) && ! is_string( $setting_value ) ) {
				$setting_value = '#ffffff';
			}
			if ( 'header_bg_opacity' === $setting ) {
				$setting_value = Fusion_Color::new_color( $this->get( 'header_bg_color' ) )->alpha;
				$setting = 'header_bg_color';
			}
			if ( is_array( $setting_value ) && isset( $setting_value['all'] ) ) {
				$setting_value = $setting_value['all'];
			}
			$setting_link  = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
		}

		switch ( $type ) {

			case 'select':
				$all_fields = self::$options_with_id;
				if ( isset( $all_fields[ $setting ]['choices'][ $setting_value ] ) ) {
					$setting_value = $all_fields[ $setting ]['choices'][ $setting_value ];
					if ( is_array( $setting_value ) ) {
						$setting_value = $setting_value[0];
					}
				} else {
					$setting_value = ucwords( str_replace( '_', '', $setting_value ) );
				}
				$setting_link = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'showhide':
				// @codingStandardsIgnoreLine
				$setting_value = ( 1 == $setting_value ) ? esc_html__( 'Show', 'Avada' ) : esc_html__( 'Hide', 'Avada' );
				$setting_link  = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'yesno':
				// @codingStandardsIgnoreLine
				$setting_value = ( 1 == $setting_value ) ? esc_html__( 'Yes', 'Avada' ) : esc_html__( 'No', 'Avada' );
				$setting_link  = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'reverseyesno':
				$setting_value = ( 1 === $setting_value || '1' === $setting_value || true === $setting_value ) ? esc_html__( 'No', 'Avada' ) : esc_html__( 'Yes', 'Avada' );
				$setting_link  = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'menu':
				$menu_name = $setting;
				$locations = get_nav_menu_locations();
				$menu_id   = ( isset( $locations[ $menu_name ] ) ) ? $locations[ $menu_name ] : false;
				$menu      = ( false !== $menu_id ) ? wp_get_nav_menu_object( $menu_id ) : false;

				$setting_value = ( false !== $menu ) ? $menu->name : esc_attr__( 'none', 'Avada' );
				$setting_link  = '<a href="' . admin_url( 'nav-menus.php?action=locations' ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';

				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'sidebar':
				$setting_value = ucwords( str_replace( '_', '', $setting_value ) );
				$setting_link  = '<a href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer">' . $setting_value . '</a>';
				$setting_description = sprintf( esc_html__( '  Global sidebar is currently active and will override selection with %s.', 'Avada' ), $setting_link );
				break;

			case 'range':
				$setting_description = sprintf( esc_html__( '  Default currently set to %s.', 'Avada' ), $setting_link );
				break;

			case 'child':
				$setting_description = sprintf( esc_html__( '  Leave empty for value set in parent options.  If that is also empty, the %1$s value of %2$s will be used.', 'Avada' ), apply_filters( 'fusion_options_label', esc_attr__( 'Element Options', 'Avada' ) ), $setting_link );
				break;

			default:
				if ( '' !== $setting_value ) {
					$setting_description = sprintf( esc_html__( '  Leave empty for default value of %s.', 'Avada' ), $setting_link );
				} else {
					$setting_description = sprintf( __( '  Currently no default selected. Can be set globally from the <a %1$s>%2$s</a>.', 'Avada' ), 'href="' . $this->get_setting_link( $setting, $subset ) . '" target="_blank" rel="noopener noreferrer"', apply_filters( 'fusion_options_label', esc_attr__( 'Element Options', 'Avada' ) ) );
				}
				break;
		}// End switch().

		return '' === $reset ? $setting_description : $setting_description . '  <span class="fusion-builder-default-reset"><a href="#" id="default-' . $reset . '" class="fusion-range-default fusion-hide-from-atts" type="radio" name="' . $reset . '" value="" data-default="' . $this->get( $setting, $subset ) . '">' . esc_attr__( 'Reset to default.', 'Avada' ) . '</a><span>' . esc_attr__( 'Using default value.', 'Avada' ) . '</span></span>';
	}

	/**
	 * Gets the link to the setting.
	 *
	 * @since	4.1
	 * @param	string $setting name of option.
	 * @param	string $subset name of subset of option.
	 * @return	string URL to options page with option hash appended.
	 */
	public function get_setting_link( $setting = null, $subset = false ) {

		$options_page = apply_filters( 'fusion_builder_options_url', admin_url( 'admin.php?page=fusion-element-options' ) );
		$option_anchor = '#' . $setting;
		$language = '&lang=' . Fusion_Multilingual::get_active_language();
		return $options_page . $language . $option_anchor;
	}

	/**
	 * Gets the color scheme names as an array.
	 *
	 * @since	5.0.0
	 * @param   array $standard_schemes array to which we need to add custom color schemes.
	 * @return	array of color scheme names.
	 */
	public function get_custom_color_schemes( $standard_schemes = array() ) {

		$custom_color_schemes = self::$custom_color_schemes;
		if ( is_array( $custom_color_schemes ) ) {
			foreach ( $custom_color_schemes as $key => $color_scheme ) {
				$standard_schemes[ 'scheme-' . $key ] = $color_scheme['name'];
			}
		}
		return $standard_schemes;
	}

	/**
	 * Gets a value from specific custom color scheme.
	 *
	 * @since	5.0.0
	 * @param   integer $scheme_id   key of custom color scheme to check.
	 * @param   string  $option_name option name to find value for.
	 * @return	array of color scheme names.
	 */
	public function get_custom_color( $scheme_id, $option_name = false ) {

		$custom_color_schemes = self::$custom_color_schemes;
		if ( $option_name ) {
			return ( isset( $custom_color_schemes[ $scheme_id ] ) ) ? $custom_color_schemes[ $scheme_id ]['values'][ $option_name ] : '';
		} else {
			return ( isset( $custom_color_schemes[ $scheme_id ] ) ) ? $custom_color_schemes[ $scheme_id ]['values'] : '';
		}
	}

	/**
	 * Sets the $lang property for this object.
	 * Languages are prefixed with a '_'
	 *
	 * If we're not currently performing a migration
	 * it also checks if the options for the current language are set.
	 * If they are not, then we will copy the options from the main language.
	 */
	public static function multilingual_options() {

		// Set the self::$lang.
		if ( ! class_exists( 'Fusion_Multilingual' ) ) {
			include_once 'class-fusion-multilingual.php';
		}
		$active_language = Fusion_Multilingual::get_active_language();

		if ( ! in_array( $active_language, array( '', 'en', 'all' ), true ) ) {
			self::$lang = '_' . $active_language;
		}
		// Make sure the options are copied if needed.
		if ( ! in_array( self::$lang, array( '', 'en', 'all' ), true ) && ! self::$lang_applied ) {
			// Set the $option_name property.
			self::$option_name = self::get_option_name();
			// Get the options without using a language (defaults).
			$original_options = get_option( self::$original_option_name, array() );
			// Get options with a language.
			$options = get_option( self::$original_option_name . self::$lang, array() );
			// If we're not currently performing a migration and the options are not set
			// then we must copy the default options to the new language.
			if ( ! self::$is_updating && ! empty( $original_options ) && empty( $options ) ) {
				update_option( self::$original_option_name . self::$lang, get_option( self::$original_option_name ) );
			}
			// Modify the option_name to include the language.
			self::$option_name  = self::$original_option_name . self::$lang;

			// Set $lang_applied to true. Makes sure we don't do the above more than once.
			self::$lang_applied = true;
		}
	}

	/**
	 * Get the protected $option_name.
	 * If empty returns the original_option_name.
	 *
	 * @return string
	 */
	public static function get_option_name() {
		if ( empty( self::$option_name ) ) {
			return self::$original_option_name;
		}
		return self::$option_name;
	}

	/**
	 * Get the protected $original_option_name.
	 *
	 * @return string
	 */
	public static function get_original_option_name() {
		return self::$original_option_name;
	}

	/**
	 * Change the protected $option_name.
	 *
	 * @param  false|string $option_name The option name to use.
	 */
	public static function set_option_name( $option_name = false ) {
		if ( false !== $option_name && ! empty( $option_name ) ) {
			self::$option_name = $option_name;
		}
	}

	/**
	 * Change the protected $language_is_all property.
	 *
	 * @static
	 * @access public
	 * @param bool $is_all Whether we're on the "all" language option or not.
	 * @return null|void
	 */
	public static function set_language_is_all( $is_all ) {
		if ( true === $is_all ) {
			self::$language_is_all = true;
			return;
		}
		self::$language_is_all = false;
	}

	/**
	 * Get the protected $language_is_all property.
	 *
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function get_language_is_all() {
		return self::$language_is_all;
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
