<?php
/**
 * Handler for contact pages.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      3.8
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle contact pages.
 */
class Avada_Contact {

	/**
	 * The recaptcha class instance
	 *
	 * @access public
	 * @var bool|object
	 */
	public $re_captcha = false;

	/**
	 * Do we have an error? (bool)
	 *
	 * @access public
	 * @var bool
	 */
	public $has_error = false;

	/**
	 * Contact name
	 *
	 * @access public
	 * @var string
	 */
	public $name = '';

	/**
	 * Subject
	 *
	 * @access public
	 * @var string
	 */
	public $subject = '';

	/**
	 * Email address
	 *
	 * @access public
	 * @var string
	 */
	public $email = '';

	/**
	 * The message
	 *
	 * @access public
	 * @var string
	 */
	public $message = '';

	/**
	 * Has the email been sent?
	 *
	 * @access public
	 * @var bool
	 */
	public $email_sent = false;

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		$this->init_recaptcha();
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['submit'] ) ) {
			$this->process_name();
			$this->process_subject();
			$this->process_email();
			$this->process_message();
			$this->process_recaptcha();

			if ( ! $this->has_error ) {
				$this->send_email();
			}
		}
	}

	/**
	 * Setup ReCaptcha.
	 *
	 * @access private
	 */
	private function init_recaptcha() {
		$options = get_option( Avada::get_option_name() );
		if ( $options['recaptcha_public'] && $options['recaptcha_private'] && ! function_exists( 'recaptcha_get_html' ) ) {
			if ( version_compare( PHP_VERSION, '5.3' ) >= 0 && ! class_exists( 'ReCaptcha' ) ) {
				require_once Avada::$template_dir_path . '/includes/recaptcha/src/autoload.php';
				// We use a wrapper class to avoid fatal errors due to syntax differences on PHP 5.2.
				require_once Avada::$template_dir_path . '/includes/recaptcha/class-avada-recaptcha.php';
				// Instantiate ReCaptcha object.
				$re_captcha_wrapper = new Avada_ReCaptcha( $options['recaptcha_private'] );
				$this->re_captcha  = $re_captcha_wrapper->recaptcha;
			}
		}
	}

	/**
	 * Check to make sure that the name field is not empty.
	 *
	 * @access private
	 */
	private function process_name() {
		// @codingStandardsIgnoreLine
		$post_contact_name = ( isset( $_POST['contact_name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
		if ( '' == $post_contact_name || esc_html__( 'Name (required)', 'Avada' ) == $post_contact_name ) {
			$this->has_error = true;
		} else {
			$this->name = $post_contact_name;
		}
	}

	/**
	 * Subject field is not required.
	 *
	 * @access private
	 */
	private function process_subject() {
		// @codingStandardsIgnoreLine
		$post_url      = ( isset( $_POST['url'] ) ) ? sanitize_text_field( wp_unslash( $_POST['url'] ) ) : '';
		$this->subject = ( function_exists( 'stripslashes' ) ) ? stripslashes( $post_url ) : $post_url;
	}

	/**
	 * Check to make sure sure that a valid email address is submitted.
	 *
	 * @access private
	 */
	private function process_email() {
		// @codingStandardsIgnoreLine
		$email = ( isset( $_POST['email'] ) ) ? trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ) : '';
		$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
		if ( '' == $email || esc_html__( 'Email (required)', 'Avada' ) == $email ) {
			$this->has_error = true;
		} elseif ( 0 === preg_match( $pattern, $email ) ) {
			$this->has_error = true;
		} else {
			$this->email = trim( $email );
		}
	}

	/**
	 * Check to make sure a message was entered.
	 *
	 * @access private
	 */
	private function process_message() {
		// @codingStandardsIgnoreLine
		$message = ( isset( $_POST['msg'] ) ) ? esc_textarea( wp_unslash( $_POST['msg'] ) ) : '';
		if ( '' == $message || esc_html__( 'Message', 'Avada' ) == $message ) {
			$this->has_error = true;
		} else {
			$this->message = ( function_exists( 'stripslashes' ) ) ? stripslashes( $message ) : $message;
		}
	}

	/**
	 * Check recaptcha.
	 *
	 * @access private
	 */
	private function process_recaptcha() {
		if ( $this->re_captcha ) {
			$re_captcha_response = null;
			// Was there a reCAPTCHA response? @codingStandardsIgnoreLine
			$post_recaptcha_response = ( isset( $_POST['g-recaptcha-response'] ) ) ? trim( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
			// @codingStandardsIgnoreLine
			$server_remote_addr      = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? trim( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			if ( $post_recaptcha_response && ! empty( $post_recaptcha_response ) ) {
				$re_captcha_response = $this->re_captcha->verify( $post_recaptcha_response, $server_remote_addr );
			}
			// Check the reCaptcha response.
			if ( null == $re_captcha_response || ! $re_captcha_response->isSuccess() ) {
				$this->has_error = true;
			}
		}
	}

	/**
	 * Send the email.
	 *
	 * @access private
	 */
	private function send_email() {
		$options = get_option( Avada::get_option_name() );
		$name    = wp_filter_kses( $this->name );
		$email   = wp_filter_kses( $this->email );
		$subject = wp_filter_kses( $this->subject );
		$message = wp_filter_kses( $this->message );

		if ( function_exists( 'stripslashes' ) ) {
			$subject = stripslashes( $subject );
			$message = stripslashes( $message );
		}

		$email_to = $options['email_address'];
		$body  = esc_html__( 'Name:', 'Avada' ) . " $name \n\n";
		$body .= esc_html__( 'Email:', 'Avada' ) . " $email \n\n";
		$body .= esc_html__( 'Subject:', 'Avada' ) . " $subject \n\n";
		$body .= esc_html__( 'Comments:', 'Avada' ) . "\n $message";

		$headers = 'Reply-To: ' . $name . ' <' . $email . '>' . "\r\n";

		wp_mail( $email_to, $subject, $body, $headers );

		$this->email_sent = true;

		if ( true == $this->email_sent ) {
			$_POST['contact_name'] = '';
			$_POST['email']        = '';
			$_POST['url']          = '';
			$_POST['msg']          = '';
		}
	}
}
