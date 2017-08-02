<?php

if ( fusion_is_element_enabled( 'fusion_login' ) ||
	fusion_is_element_enabled( 'fusion_register' ) ||
	fusion_is_element_enabled( 'fusion_lost_password' ) ) {

	if ( ! class_exists( 'FusionSC_Login' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @package fusion-builder
		 * @since 1.0
		 */
		class FusionSC_Login extends Fusion_Element {

			/**
			 * Element counter, used for CSS.
			 *
			 * @since 1.0
			 * @var int $args
			 */
			private $login_counter = 0;

			/**
			 * Parameters from the shortcode.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array $args
			 */
			protected $args;

			/**
			 * Whether the nonces script has already been added to the footer or not.
			 *
			 * @static
			 * @access private
			 * @since 2.0.0
			 * @var bool
			 */
			private static $nonce_added_to_footer = false;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				/* add_action( 'login_init', array( $this, 'login_init' ) ); */
				add_action( 'lostpassword_post', array( $this, 'lost_password_redirect' ) );
				add_filter( 'login_redirect', array( $this, 'login_redirect' ), 10, 3 );
				add_filter( 'registration_errors', array( $this, 'registration_error_redirect' ), 10, 3 );

				add_filter( 'fusion_attr_login-shortcode', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_login-shortcode-form', array( $this, 'form_attr' ) );
				add_filter( 'fusion_attr_login-shortcode-button', array( $this, 'button_attr' ) );

				add_shortcode( 'fusion_login', array( $this, 'render_login' ) );
				add_shortcode( 'fusion_register', array( $this, 'render_register' ) );
				add_shortcode( 'fusion_lost_password', array( $this, 'render_lost_password' ) );

				add_action( 'wp_ajax_fusion_login_nonce', array( $this, 'ajax_get_login_nonce_field' ) );
				add_action( 'wp_ajax_nopriv_fusion_login_nonce', array( $this, 'ajax_get_login_nonce_field' ) );
				add_action( 'wp_footer', array( $this, 'print_login_nonce_script' ) );
			}

			/**
			 * Add default values to shortcode parameters.
			 *
			 * @since 1.0
			 *
			 * @param  array $args       Shortcode paramters.
			 * @return array                Shortcode paramters with default values where necesarry.
			 */
			public function default_shortcode_parameter( $args ) {

				global $fusion_settings;

				$defaults = FusionBuilder::set_shortcode_defaults(
					array(
						'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
						'class'                 => '',
						'id'                    => '',
						'button_fullwidth'      => $fusion_settings->get( 'button_span' ),
						'caption'               => '',
						'caption_color'         => '',
						'form_background_color' => $fusion_settings->get( 'user_login_form_background_color' ),
						'heading'               => '',
						'heading_color'         => '',
						'link_color'            => '',
						'lost_password_link'    => '',
						'redirection_link'      => '',
						'register_link'         => '',
						'text_align'            => $fusion_settings->get( 'user_login_text_align' ),

						'disable_form'          => '', // Only for demo usage.
					), $args
				);

				$defaults['main_container'] = ( $defaults['disable_form'] ) ? 'div' : 'form';

				return $defaults;
			}

			/**
			 * Render the login shortcode.
			 *
			 * @since 1.0
			 *
			 * @param  array  $args       Shortcode paramters.
			 * @param  string $content    Content between shortcode.
			 * @return string               HTML output.
			 */
			public function render_login( $args, $content = '' ) {

				$defaults = $this->default_shortcode_parameter( $args );

				$defaults['action'] = 'login';

				extract( $defaults );

				$this->args = $defaults;

				$styles = $this->get_style_tag();

				$html = '<div ' . FusionBuilder::attributes( 'login-shortcode' ) . '>' . $styles;

				if ( ! is_user_logged_in() ) {
					$user_login = ( isset( $_GET['log'] ) ) ?  $_GET['log'] : '';

					$html .= '<h3 class="fusion-login-heading">' . $heading . '</h3>';
					$html .= '<div class="fusion-login-caption">' . $caption . '</div>';

					$html .= '<' . $main_container . ' ' . FusionBuilder::attributes( 'login-shortcode-form' ) . '>';

					// Get the success/error notices.
					$html .= $this->render_notices( $action );

					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="user_login">' . esc_attr__( 'Username', 'fusion-builder' ) . '</label>';
					$html .= '<input type="text" name="log" placeholder="' . esc_attr__( 'Username', 'fusion-builder' ) . '" value="' . esc_attr( $user_login ) . '" size="20" class="fusion-login-username input-text" id="user_login" />';
					$html .= '</div>';

					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="user_pass">' . esc_attr__( 'Password', 'fusion-builder' ) . '</label>';
					$html .= '<input type="password" name="pwd" placeholder="' . esc_attr__( 'Password', 'fusion-builder' ) . '" value="" size="20" class="fusion-login-password input-text" id="user_pass" />';
					$html .= '</div>';

					$html .= '<div class="fusion-login-submit-wrapperr">';
					$html .= '<button ' . FusionBuilder::attributes( 'login-shortcode-button' ) . '>' . esc_attr__( 'Log in', 'fusion-builder' ) . '</button>';

					// Set the query string for successful password reset.
					if ( ! $redirection_link ) {
						$redirection_link = $this->get_redirection_link();
					}
					$html .= $this->render_hidden_login_inputs( $redirection_link );

					$html .= '</div>';

					$html .= '<div class="fusion-login-links">';
					if ( '' !== $lost_password_link ) {
						$html .= '<a class="fusion-login-lost-passowrd" target="_self" href="' . $lost_password_link . '">' . esc_attr__( 'Lost password?', 'fusion-builder' ) . '</a>';
					}
					if ( '' !== $register_link ) {
						$html .= '<a class="fusion-login-register" target="_self" href="' . $register_link . '">' . esc_attr__( 'Register', 'fusion-builder' ) . '</a>';
					}
					$html .= '</div>';

					$html .= '</' . $main_container . '>';
				} else {
					$user = get_user_by( 'id', get_current_user_id() );

					$html .= '<div class="fusion-login-caption">' . sprintf( esc_attr__( 'Welcome %s', 'fusion-builder' ), ucwords( $user->display_name ) ) . '</div>';
					$html .= '<div class="fusion-login-avatar">' . get_avatar( $user->ID, apply_filters( 'fusion_login_box_avatar_size', 50 ) ) . '</div>';
					$html .= '<ul class="fusion-login-loggedin-links">';
					$html .= '<li><a href="' . get_dashboard_url() . '">' . esc_attr__( 'Dashboard', 'fusion-builder' ) . '</a></li>';
					$html .= '<li><a href="' . get_edit_user_link( $user->ID ) . '">' . esc_attr__( 'Profile', 'fusion-builder' ) . '</a></li>';
					$html .= '<li><a href="' . wp_logout_url( get_permalink() ) . '">' . esc_attr__( 'Logout', 'fusion-builder' ) . '</a></li>';
					$html .= '</ul>';

				}

				$html .= '</div>';

				return $html;
			}

			/**
			 * Render the register shortcode.
			 *
			 * @since 1.8.0
			 *
			 * @param  array  $args       Shortcode paramters.
			 * @param  string $content    Content between shortcode.
			 * @return string               HTML output.
			 */
			public function render_register( $args, $content = '' ) {
				$defaults = $this->default_shortcode_parameter( $args );

				$defaults['action'] = 'register';

				extract( $defaults );

				$this->args = $defaults;

				$styles = $this->get_style_tag();

				$html = '';

				if ( ! is_user_logged_in() ) {
					$html .= '<div ' . FusionBuilder::attributes( 'login-shortcode' ) . '>' . $styles;
					$html .= '<h3 class="fusion-login-heading">' . $heading . '</h3>';
					$html .= '<div class="fusion-login-caption">' . $caption . '</div>';

					$html .= '<' . $main_container . ' ' . FusionBuilder::attributes( 'login-shortcode-form' ) . '>';

					// Get the success/error notices.
					$html .= $this->render_notices( $action );

					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="user_login">' . esc_attr__( 'Username', 'fusion-builder' ) . '</label>';
					$html .= '<input type="text" name="user_login" placeholder="' . esc_attr__( 'Username', 'fusion-builder' ) . '" value="" size="20" class="fusion-login-username input-text" id="user_login" />';
					$html .= '</div>';

					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="user_pass">' . esc_attr__( 'Email', 'fusion-builder' ) . '</label>';
					$html .= '<input type="text" name="user_email" placeholder="' . esc_attr__( 'Email', 'fusion-builder' ) . '" value="" size="20" class="fusion-login-email input-text" id="user_email" />';
					$html .= '</div>';

					/* Only added as honeypot for spambots. */
					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="confirm_email">Please leave this field empty</label>';
					$html .= '<input class="fusion-hidden-content" type="text" name="confirm_email" id="confirm_email" value="">';
					$html .= '</div>';

					$html .= '<p class="fusion-login-input-wrapper">' . esc_attr__( 'Registration confirmation will be e-mailed to you.', 'fusion-builder' ) . '</p>';

					$html .= '<div class="fusion-login-submit-wrapperr">';
					$html .= '<button ' . FusionBuilder::attributes( 'login-shortcode-button' ) . '>' . esc_attr__( 'Register', 'fusion-builder' ) . '</button>';

					// Set the query string for successful password reset.
					if ( ! $redirection_link ) {
						$redirection_link = $this->get_redirection_link();
					}
					$html .= $this->render_hidden_login_inputs( $redirection_link,  array( 'action' => 'register', 'success' => '1' ) );

					$html .= '</div>';

					$html .= '</' . $main_container . '>';
					$html .= '</div>';
				} else {
					$html .= do_shortcode( '[fusion_alert type="general" border_size="1px" box_shadow="yes"]' . esc_attr__( 'You are already signed up.', 'fusion-builder' ) . '[/fusion_alert]' );
				}

				return $html;
			}

			/**
			 * Render the lost password shortcode.
			 *
			 * @since 1.8.0
			 *
			 * @param  array  $args       Shortcode paramters.
			 * @param  string $content    Content between shortcode.
			 * @return string               HTML output.
			 */
			public function render_lost_password( $args, $content = '' ) {

				$defaults = $this->default_shortcode_parameter( $args );

				$defaults['action'] = 'lostpassword';

				extract( $defaults );

				$this->args = $defaults;

				$styles = $this->get_style_tag();

				$html = '';

				if ( ! is_user_logged_in() ) {

					$html .= '<div ' . FusionBuilder::attributes( 'login-shortcode' ) . '>' . $styles;
					$html .= '<h3 class="fusion-login-heading">' . $heading . '</h3>';
					$html .= '<div class="fusion-login-caption">' . $caption . '</div>';

					$html .= '<' . $main_container . ' ' . FusionBuilder::attributes( 'login-shortcode-form' ) . '>';

					// Get the success/error notices.
					$html .= $this->render_notices( $action );

					$html .= '<p class="fusion-login-input-wrapper">' . esc_attr__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'fusion-builder' ) . '</p>';

					$html .= '<div class="fusion-login-input-wrapper">';
					$html .= '<label class="fusion-hidden-content" for="user_login">' . esc_attr__( 'Username or Email', 'fusion-builder' ) . '</label>';
					$html .= '<input type="text" name="user_login" placeholder="' . esc_attr__( 'Username or Email', 'fusion-builder' ) . '" value="" size="20" class="fusion-login-username input-text" id="user_login" />';
					$html .= '</div>';

					$html .= '<div class="fusion-login-submit-wrapperr">';
					$html .= '<button ' . FusionBuilder::attributes( 'login-shortcode-button' ) . '>' . esc_attr__( 'Reset Password', 'fusion-builder' ) . '</button>';

					// Set the query string for successful password reset.
					if ( ! $redirection_link ) {
						$redirection_link = $this->get_redirection_link();
					}
					$html .= $this->render_hidden_login_inputs( $redirection_link, array( 'action' => 'lostpassword', 'success' => '1' ) );

					$html .= '</div>';
					$html .= '</' . $main_container . '>';
					$html .= '</div>';

				} else {
					$html .= do_shortcode( '[fusion_alert type="general" border_size="1px" box_shadow="yes"]' . esc_attr__( 'You are already signed in.', 'fusion-builder' ) . '[/fusion_alert]' );
				}

				return $html;
			}

			/**
			 * Render the needed hidden login inputs.
			 *
			 * @access public
			 * @since 1.0
			 * @param  string $redirection_link A redirection link.
			 * @param  array  $query_args       The query arguments.
			 * @return string
			 */
			public function render_hidden_login_inputs( $redirection_link = '', $query_args = array() ) {
				$html = '';
				if ( ! $this->args['disable_form'] ) {

					$html .= '<input type="hidden" name="user-cookie" value="1" />';

					// If no redirection link is given, get ones.
					if ( empty( $redirection_link ) ) {
						$redirection_link = wp_get_referer();
						if ( isset( $_SERVER['REQUEST_URI'] ) ) {
							$redirection_link = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						}

						// Redirection and source input.
						$redirection_link = remove_query_arg( 'loggedout', $redirection_link );
					}

					if ( ! empty( $query_args ) ) {
						$redirection_link = add_query_arg( $query_args, $redirection_link );
					}

					$html .= '<input type="hidden" name="redirect_to" value="' . esc_url( $redirection_link ) . '" />';
					$html .= '<input type="hidden" name="fusion_login_box" value="true" />';

				}
				// Prevent hijacking of the form.
				$html .= '<span class="fusion-login-nonce" style="display:none;"></span>';

				return $html;

			}

			/**
			 * Generates nonce field, used in AJAX request.
			 *
			 * @access public
			 */
			public function ajax_get_login_nonce_field() {
				wp_nonce_field( 'fusion-login', '_wpnonce', false, true );
				die();
			}

			/**
			 * Prints nonce AJAX script.
			 *
			 * @access public
			 */
			public function print_login_nonce_script() {

				// If we've already added the script to the footer
				// there's no need to proceed any further.
				if ( self::$nonce_added_to_footer ) {
					return;
				}

				// Set self::$nonce_added_to_footer to true to avoid adding it multiple times.
				self::$nonce_added_to_footer = true;
				?>
				<script type="text/javascript">
				jQuery( document ).ready( function() {
					var ajaxurl = '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>';
					if ( 0 < jQuery( '.fusion-login-nonce' ).length ) {
						jQuery.get( ajaxurl, { 'action': 'fusion_login_nonce' }, function( response ) {
							jQuery( '.fusion-login-nonce' ).html( response );
						});
					}
				});
				</script>
				<?php
			}

			/**
			 * Deals with the different requests.
			 *
			 * @since 1.8.0
			 */
			public function login_init() {
				check_admin_referer( 'fusion-login' );
				$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';

				$action = 'reauth';
				if ( isset( $_POST['wp-submit'] ) ) {
					$action = 'post-data';
				}

				$redirect_link = $this->get_redirection_link();

				// Redirect to change password form.
				if ( 'resetpass' === $action ) {
					wp_redirect( add_query_arg( array( 'action' => 'resetpass' ), $redirect_link ) );
					exit;
				}

				if (
					'post-data' === $action || // Don't mess with POST requests.
					'reauth' === $action    || // Need to reauthorize.
					'logout' === $action       // User is logging out.
				) {
					return;
				}

				wp_redirect( $redirect_link );
				exit;
			}

			/**
			 * Constructs a redirection link, either from the $redirect_to variable or from the referer.
			 *
			 * @access public
			 * @since 1.0
			 * @param bool $error Whether we have an error or not.
			 * @return string The redirection link.
			 */
			public function get_redirection_link( $error = false ) {
				$redirection_link = '';

				if ( $error && isset( $_REQUEST['_wp_http_referer'] ) ) {
					$redirection_link = $_REQUEST['_wp_http_referer'];
				} elseif ( isset( $_REQUEST['redirect_to'] ) ) {
					$redirection_link = $_REQUEST['redirect_to'];
				} elseif ( isset( $_SERVER ) && isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['HTTP_REFERER'] ) {
					$referer_array = wp_parse_url( $_SERVER['HTTP_REFERER'] );
					$referer = '//' . $referer_array['host'] . $referer_array['path'];

					// If there's a valid referrer, and it's not the default log-in screen.
					if ( ! empty( $referer ) && ! strstr( $referer, 'wp-login' ) && ! strstr( $referer, 'wp-admin' ) ) {
						$redirection_link = $referer;
					}
				}

				return $redirection_link;
			}

			/**
			 * Redirects after the login, both on success and error.
			 *
			 * @since 1.8.0
			 *
			 * @param string           $redirect_to           The redirect destination URL.
			 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
			 * @param WP_User|WP_Error $user        WP_User object if login was successful, WP_Error object otherwise.
			 * @return string The redirection link.
			 */
			public function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
				// Make sure we come from the login box.
				if ( isset( $_POST['fusion_login_box'] ) ) {
					check_admin_referer( 'fusion-login' );
					// If we have no errors, remove the action query arg.
					if ( ! isset( $user->errors ) ) {
						return $redirect_to;
					}

					// Redirect to the page with the login box with error code.
					wp_redirect( add_query_arg( array( 'action' => 'login', 'success' => '0' ), $this->get_redirection_link( true ) ) );
					exit;
				}
				return $redirect_to;
			}

			/**
			 * Redirects after the login, both on success and error.
			 *
			 * @since 1.8.0
			 *
			 * @param WP_Error $errors              A WP_Error object containing any errors encountered during registration.
			 * @param string   $sanitized_user_login  User's username after it has been sanitized.
			 * @param string   $user_email            User's email.
			 * @return void|WP_Error                Error object.
			 */
			public function registration_error_redirect( $errors, $sanitized_user_login, $user_email ) {
				// Make sure we come from the login box.
				if ( isset( $_POST['fusion_login_box'] ) ) {
					check_admin_referer( 'fusion-login' );
					$redirection_link = $this->get_redirection_link();

					// Redirect spammers directly to success page.
					if ( ! isset( $_POST['confirm_email'] ) || '' !== $_POST['confirm_email'] ) {
						wp_redirect( add_query_arg( array( 'action' => 'register', 'success' => '1' ), $redirection_link ) );
						exit;
					}

					// Error - prepare query strings for front end notice output.
					if ( ! empty( $errors->errors ) ) {
						$redirection_link = $this->get_redirection_link( true );
						$redirection_link = add_query_arg( array( 'action' => 'register', 'success' => '0' ), $redirection_link );

						// Empty username.
						if ( isset( $errors->errors['empty_username'] ) ) {
							$redirection_link = add_query_arg( array( 'empty_username' => '1' ), $redirection_link );
						}
						// Empty email.
						if ( isset( $errors->errors['empty_email'] ) ) {
							$redirection_link = add_query_arg( array( 'empty_email' => '1' ), $redirection_link );
						}
						// Username exists.
						if ( isset( $errors->errors['username_exists'] ) ) {
							$redirection_link = add_query_arg( array( 'username_exists' => '1' ), $redirection_link );
						}
						// Email exists.
						if ( isset( $errors->errors['email_exists'] ) ) {
							$redirection_link = add_query_arg( array( 'email_exists' => '1' ), $redirection_link );
						}

						wp_redirect( $redirection_link );
						exit;
					}
				}

				return $errors;
			}

			/**
			 * Redirects on lost password submission error..
			 *
			 * @since 1.8.0
			 *
			 * @return void
			 */
			public function lost_password_redirect() {
				// Make sure we come from the login box.
				if ( isset( $_POST['fusion_login_box'] ) ) {
					check_admin_referer( 'fusion-login' );
					$redirection_link = add_query_arg( array( 'action' => 'lostpassword', 'success' => '0' ), $this->get_redirection_link( true ) );
					$user_data = '';

					// Error - empty input.
					if ( empty( $_POST['user_login'] ) ) {
						$redirection_link = add_query_arg( array( 'empty_login' => '1' ), $redirection_link );
						// Check email.
					} elseif ( strpos( $_POST['user_login'], '@' ) ) {
						$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
						// Error - invalid email.
						if ( empty( $user_data ) ) {
							$redirection_link = add_query_arg( array( 'unregistered_mail' => '1' ), $redirection_link );
						}
					} else {
						// Check username.
						$login = trim( $_POST['user_login'] );
						$user_data = get_user_by( 'login', $login );

						// Error - invalid username.
						if ( empty( $user_data ) ) {
							$redirection_link = add_query_arg( array( 'unregisred_user' => '1' ), $redirection_link );
						}
					}

					// Redirect on error.
					if ( empty( $user_data ) ) {
						wp_redirect( $redirection_link );
						exit;
					}
				}
			}

			/**
			 * Renders the response messages after form submission.
			 *
			 * @since 1.8.0
			 *
			 * @param string $context The context of the calling form.
			 * @return string
			 */
			public function render_notices( $context = '' ) {

				// Make sure we have some query string returned; if not we had a successful login.
				if ( isset( $_GET['action'] ) && $_GET['action'] == $context ) {
					// Login - there is only an error message and it is always the same.
					if ( 'login' == $_GET['action'] && isset( $_GET['success'] ) && '0' == $_GET['success'] ) {
						$notice_type = 'error';
						$notices     = esc_attr__( 'Login failed, please try again.', 'fusion-builder' );
						// Registration.
					} elseif ( 'register' == $_GET['action'] ) {
						// Success.
						if ( isset( $_GET['success'] ) && '1' == $_GET['success'] ) {
							$notice_type = 'success';
							$notices     = esc_attr__( 'Registration complete. Please check your e-mail.', 'fusion-builder' );
							// Error.
						} else {
							$notice_type = 'error';
							$notices = '';

							// Empty username.
							if ( isset( $_GET['empty_username'] ) ) {
								$notices .= esc_attr__( 'Please enter a username.', 'fusion-builder' ) . '<br />';
							}
							// Empty email.
							if ( isset( $_GET['empty_email'] ) ) {
								$notices .= esc_attr__( 'Please type your e-mail address.', 'fusion-builder' ) . '<br />';
							}
							// Username exists.
							if ( isset( $_GET['username_exists'] ) ) {
								$notices .= esc_attr__( 'This username is already registered. Please choose another one.', 'fusion-builder' ) . '<br />';
							}
							// Email exists.
							if ( isset( $_GET['email_exists'] ) ) {
								$notices .= esc_attr__( 'This email is already registered, please choose another one.', 'fusion-builder' ) . '<br />';
							}

							// Generic Error.
							if ( ! $notices ) {
								$notices .= esc_attr__( 'Something went wrong during registration. Please try again.', 'fusion-builder' );
								// Delete the last line break.
							} else {
								$notices = substr( $notices, 0, strlen( $notices ) - 6 );
							}
						}
					} elseif ( 'lostpassword' == $_GET['action'] ) {
						// Lost password.
						if ( isset( $_GET['success'] ) && '1' == $_GET['success'] ) {
							// Success.
							$notice_type = 'success';
							$notices     = esc_attr__( 'Check your e-mail for the confirmation link.', 'fusion-builder' );
						} else {
							// Error.
							$notice_type = 'error';
							$notices     = '';

							// Empty login.
							if ( isset( $_GET['empty_login'] ) ) {
								$notices .= esc_attr__( 'Enter a username or e-mail address.', 'fusion-builder' ) . '<br />';
							}

							// Empty login.
							if ( isset( $_GET['unregisred_user'] ) ) {
								$notices .= esc_attr__( 'Invalid username.', 'fusion-builder' ) . '<br />';
							}

							// Empty login.
							if ( isset( $_GET['unregistered_mail'] ) ) {
								$notices .= esc_attr__( 'There is no user registered with that email address.', 'fusion-builder' ) . '<br />';
							}

							// Generic Error.
							if ( ! $notices ) {
								$notices .= esc_attr__( 'Invalid username or e-mail.', 'fusion-builder' );
								// Delete the last line break.
							} else {
								$notices = substr( $notices, 0, strlen( $notices ) - 6 );
							}
						}
					}

					return do_shortcode( '[fusion_alert type="' . $notice_type . '" border_size="1px" box_shadow="yes"]' . $notices . '[/fusion_alert]' );
				}
				return '';
			}

			/**
			 * Constructs the scoped style tag for the login box.
			 *
			 * @since 1.8.0
			 *
			 * @return string The scoped styles.
			 */
			public function get_style_tag() {
				$this->login_counter++;

				$styles = '';

				if ( $this->args['heading_color'] ) {
					$styles .= '.fusion-login-box-' . $this->login_counter . ' .fusion-login-heading{color:' . $this->args['heading_color'] . ';}';
				}

				if ( $this->args['caption_color'] ) {
					$styles .= '.fusion-login-box-' . $this->login_counter . ' .fusion-login-caption{color:' . $this->args['caption_color'] . ';}';
				}

				if ( $this->args['link_color'] ) {
					$styles .= '.fusion-login-box-' . $this->login_counter . ' a{color:' . $this->args['link_color'] . ';}';
				}

				if ( $styles ) {
					$styles = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
				}

				return $styles;
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], array(
					'class' => 'fusion-login-box fusion-login-box-' . $this->login_counter . ' fusion-login-box-' . $this->args['action'] . ' fusion-login-align-' . $this->args['text_align'],
				) );

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;

			}

			/**
			 * Attributes function for the form container.
			 *
			 * @since 1.0
			 *
			 * @return array The attributes.
			 */
			public function form_attr() {

				$attr = array(
					'class' => 'fusion-login-form',
				);

				if ( $this->args['form_background_color'] ) {
					$attr['style'] = 'background-color:' . $this->args['form_background_color'] . ';';
				}

				if ( $this->args['disable_form'] ) {
					return $attr;
				}

				$attr['name']   = $this->args['action'] . 'form';
				$attr['id']     = $this->args['action'] . 'form';
				$attr['method'] = 'post';

				if ( 'login' == $this->args['action'] ) {
					$attr['action'] = site_url( 'wp-login.php', 'login_post' );
				} else {
					$attr['action'] = site_url( add_query_arg( array( 'action' => $this->args['action'] ), 'wp-login.php' ), 'login_post' );
				}

				return $attr;

			}

			/**
			 * Attribues function for the button.
			 *
			 * @since 1.0
			 *
			 * @return array The attributes.
			 */
			public function button_attr() {

				global $fusion_settings;

				$button_size = strtolower( $fusion_settings->get( 'button_size', false, 'medium' ) );

				$attr = array(
					'class' => 'fusion-login-button fusion-button button-default button-' . $button_size,
				);

				if ( 'yes' != $this->args['button_fullwidth'] ) {
					$attr['class'] .= ' fusion-login-button-no-fullwidth';
				}

				$attr['type'] = 'submit';
				$attr['name'] = 'wp-submit';

				return $attr;

			}

			/**
			 * Builds the dynamic styling.
			 *
			 * @access public
			 * @since 1.1
			 * @return array
			 */
			public function add_styling() {

				global $fusion_library, $fusion_settings, $dynamic_css_helpers;

				$main_elements = apply_filters( 'fusion_builder_element_classes', array( '.fusion-login-box' ), '.fusion-login-box' );

				$elements = $dynamic_css_helpers->map_selector( $main_elements, ' a:hover' );
				$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['color'] = $fusion_library->sanitize->color( $fusion_settings->get( 'primary_color' ) );

				if ( 'yes' == $fusion_settings->get( 'button_span' ) && class_exists( 'WooCommerce' ) ) {
					$elements = $dynamic_css_helpers->map_selector( $main_elements, '.fusion-login-box-submit' );
					$css['global'][ $dynamic_css_helpers->implode( $elements ) ]['float'] = 'none';
				}

				return $css;

			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections User Login settings.
			 */
			public function add_options() {

				return array(
					'user_shortcode_section' => array(
						'label'       => esc_html__( 'User Login Element', 'fusion-builder' ),
						'id'          => 'user_login_shortcode_section',
						'description' => '',
						'type'        => 'accordion',
						'fields'      => array(
							'user_login_text_align' => array(
								'label'       => esc_html__( 'User Login Text Align', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the alignment of all user login content. "Text Flow" follows the default text align of the site. "Center" will center all elements.', 'fusion-builder' ),
								'id'          => 'user_login_text_align',
								'default'     => 'center',
								'type'        => 'radio-buttonset',
								'choices'     => array(
									'textflow' => esc_html__( 'Text Flow', 'fusion-builder' ),
									'center'   => esc_html__( 'Center', 'fusion-builder' ),
								),
							),
							'user_login_form_background_color' => array(
								'label'       => esc_html__( 'User Login Form Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the color of the form background.', 'fusion-builder' ),
								'id'          => 'user_login_form_background_color',
								'default'     => '#f6f6f6',
								'type'        => 'color-alpha',
							),
						),
					),
				);
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {

				Fusion_Dynamic_JS::enqueue_script( 'fusion-button' );
			}
		}
	}

	new FusionSC_Login();

}

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_login() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'        => esc_attr__( 'User Login', 'fusion-builder' ),
		'description' => esc_attr__( 'Enter some content for this block', 'fusion-builder' ),
		'shortcode'   => 'fusion_login',
		'icon'        => 'fusiona-calendar-check-o',
		'params'      => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Align', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the alignment of all content parts. "Text Flow" follows the default text align of the site. "Center" will center all elements.', 'fusion-builder' ),
				'param_name'  => 'text_align',
				'value'       => array(
					''         => esc_attr__( 'Default', 'fusion-builder' ),
					'textflow' => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'center'   => esc_attr__( 'Center', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading text.', 'fusion-builder' ),
				'param_name'  => 'heading',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Heading Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading color.', 'fusion-builder' ),
				'param_name'  => 'heading_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'heading',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Caption', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption text.', 'fusion-builder' ),
				'param_name'  => 'caption',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Caption Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption color.', 'fusion-builder' ),
				'param_name'  => 'caption_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'caption',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Span', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have the button span the full width.', 'fusion-builder' ),
				'param_name'  => 'button_fullwidth',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Form Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the form wrapping box.', 'fusion-builder' ),
				'param_name'  => 'form_background_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'user_login_form_background_color' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Link Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a link color.', 'fusion-builder' ),
				'param_name'  => 'link_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'link_color' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Redirection Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url to which a user should redirected after form submission. Leave empty to use the same page.', 'fusion-builder' ),
				'param_name'  => 'redirection_link',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Register Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url the "Register" link should open.', 'fusion-builder' ),
				'param_name'  => 'register_link',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Lost Password Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url the "Lost Password" link should open.', 'fusion-builder' ),
				'param_name'  => 'lost_password_link',
				'value'       => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_login' );

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_lost_password() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'      => esc_attr__( 'User Lost Password', 'fusion-builder' ),
		'shortcode' => 'fusion_lost_password',
		'icon'      => 'fusiona-calendar-check-o',
		'params'    => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Align', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the alignment of all content parts. "Text Flow" follows the default text align of the site. "Center" will center all elements.', 'fusion-builder' ),
				'param_name'  => 'text_align',
				'value'       => array(
					''         => esc_attr__( 'Default', 'fusion-builder' ),
					'textflow' => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'center'   => esc_attr__( 'Center', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading text.', 'fusion-builder' ),
				'param_name'  => 'heading',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Heading Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading color.', 'fusion-builder' ),
				'param_name'  => 'heading_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'heading',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Caption', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption text.', 'fusion-builder' ),
				'param_name'  => 'caption',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Caption Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption color.', 'fusion-builder' ),
				'param_name'  => 'caption_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'caption',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Span', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have the button span the full width.', 'fusion-builder' ),
				'param_name'  => 'button_fullwidth',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Form Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the form wrapping box.', 'fusion-builder' ),
				'param_name'  => 'form_background_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'user_login_form_background_color' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Link Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a link color.', 'fusion-builder' ),
				'param_name'  => 'link_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'link_color' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Redirection Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url to which a user should redirected after form submission. Leave empty to use the same page.', 'fusion-builder' ),
				'param_name'  => 'redirection_link',
				'value'       => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_lost_password' );

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_register() {

	global $fusion_settings;

	fusion_builder_map( array(
		'name'      => esc_attr__( 'User Register', 'fusion-builder' ),
		'shortcode' => 'fusion_register',
		'icon'      => 'fusiona-calendar-check-o',
		'params'    => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Align', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the alignment of all content parts. "Text Flow" follows the default text align of the site. "Center" will center all elements.', 'fusion-builder' ),
				'param_name'  => 'text_align',
				'value'       => array(
					''         => esc_attr__( 'Default', 'fusion-builder' ),
					'textflow' => esc_attr__( 'Text Flow', 'fusion-builder' ),
					'center'   => esc_attr__( 'Center', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading text.', 'fusion-builder' ),
				'param_name'  => 'heading',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Heading Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading color.', 'fusion-builder' ),
				'param_name'  => 'heading_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'heading',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Caption', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption text.', 'fusion-builder' ),
				'param_name'  => 'caption',
				'value'       => esc_attr__( 'Your Content Goes Here', 'fusion-builder' ),
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Caption Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a caption color.', 'fusion-builder' ),
				'param_name'  => 'caption_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'caption',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Span', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have the button span the full width.', 'fusion-builder' ),
				'param_name'  => 'button_fullwidth',
				'value'       => array(
					''    => esc_attr__( 'Default', 'fusion-builder' ),
					'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
					'no'  => esc_attr__( 'No', 'fusion-builder' ),
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Form Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the form wrapping box.', 'fusion-builder' ),
				'param_name'  => 'form_background_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'user_login_form_background_color' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Link Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a link color.', 'fusion-builder' ),
				'param_name'  => 'link_color',
				'value'       => '',
				'default'     => $fusion_settings->get( 'link_color' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Redirection Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url to which a user should redirected after form submission. Leave empty to use the same page.', 'fusion-builder' ),
				'param_name'  => 'redirection_link',
				'value'       => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_register' );
