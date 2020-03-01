<?php
/**
 * The Gravity Forms tags merger class.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      5.1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * The Gravity Forms tags merger class.
 */
class Avada_Gravity_Forms_Tags_Merger {

	/**
	 * The Gravity Form entry name.
	 *
	 * @static
	 * @access public
	 * @since 5.1
	 * @var string
	 */
	public static $_entry = null;

	/**
	 * The object.
	 *
	 * @static
	 * @access private
	 * @since 5.1
	 * @var Object
	 */
	private static $instance = false;

	/**
	 * The class constructor.
	 *
	 * @access public
	 * @param array $args Array of bool auto_append_eid and encrypt_eid.
	 */
	public function __construct( $args ) {

		if ( ! class_exists( 'GFForms' ) ) {
			return;
		}

		$this->_args = wp_parse_args( $args, array(
			'auto_append_eid' => true, // Boolean or array of form IDs.
			'encrypt_eid'     => true,
		));

		add_filter( 'the_content', array( $this, 'replace_merge_tags' ), 1 );
		add_filter( 'gform_replace_merge_tags', array( $this, 'replace_encrypt_entry_id_merge_tag' ), 10, 3 );

		if ( ! empty( $this->_args['auto_append_eid'] ) ) {
			add_filter( 'gform_confirmation', array( $this, 'append_eid_parameter' ), 20, 3 );
		}
	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @static
	 * @access public
	 * @since 5.1
	 * @param array $args Array of bool auto_append_eid and encrypt_eid.
	 * @return object $instance Instance of this class.
	 */
	public static function get_instance( $args = array() ) {

		if ( null == self::$instance ) {
			self::$instance = new self( $args );
		}
		return self::$instance;

	}

	/**
	 * Replaces the merged tags.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $post_content Content of the post.
	 * @return string $post_content Content of the post with variables replaced.
	 */
	public function replace_merge_tags( $post_content ) {

		$entry = $this->get_entry();

		if ( ! $entry ) {
			return $post_content;
		}

		$form = GFFormsModel::get_form_meta( $entry['form_id'] );

		$post_content = $this->replace_field_label_merge_tags( $post_content, $form );
		$post_content = GFCommon::replace_variables( $post_content, $form, $entry, false, false, false );

		return $post_content;
	}

	/**
	 * Replaces field labels in text.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $text Content of the post.
	 * @param string $form Content of the form.
	 * @return string $text Updated content of the post.
	 */
	public function replace_field_label_merge_tags( $text, $form ) {

		preg_match_all( '/{([^:]+?)}/', $text, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return $text;
		}

		foreach ( $matches as $match ) {

			list( $search, $field_label ) = $match;

			foreach ( $form['fields'] as $field ) {

				$full_input_id       = false;
				$matches_admin_label = rgar( $field, 'adminLabel' ) == $field_label;
				$matches_field_label = false;

				if ( is_array( $field['inputs'] ) ) {

					foreach ( $field['inputs'] as $input ) {
						if ( GFFormsModel::get_label( $field, $input['id'] ) == $field_label ) {
							$matches_field_label = true;
							$input_id            = $input['id'];
							break;
						}
					}
				} else {

					$matches_field_label = GFFormsModel::get_label( $field ) == $field_label;
					$input_id            = $field['id'];

				}

				if ( ! $matches_admin_label && ! $matches_field_label ) {
					continue;
				}

				$replace = sprintf( '{%s:%s}', $field_label, (string) $input_id );
				$text    = str_replace( $search, $replace, $text );

				break;
			}
		} // End foreach().

		return $text;
	}

	/**
	 * Replaces encrypted entry id.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $text  Content of the post.
	 * @param string $form  Content of the form.
	 * @param object $entry Form entry object.
	 * @return string $text  Entry content.
	 */
	public function replace_encrypt_entry_id_merge_tag( $text, $form, $entry ) {

		if ( false === strpos( $text, '{encrypted_entry_id}' ) ) {
			return $text;
		}

		// $entry is not always a "full" entry.
		$entry_id = rgar( $entry, 'id' );
		if ( $entry_id ) {
			$entry_id = $this->prepare_eid( $entry['id'], true );
		}

		return str_replace( '{encrypted_entry_id}', $entry_id, $text );
	}

	/**
	 * Appends eid parameter if enabled.
	 *
	 * @access public
	 * @since 5.1
	 * @param string|array $confirmation Message or content.
	 * @param string       $form         Content of the form.
	 * @param object       $entry        Form entry object.
	 * @return string|array $confirmation Confirmation content.
	 */
	public function append_eid_parameter( $confirmation, $form, $entry ) {

		$is_ajax_redirect = is_string( $confirmation ) && strpos( $confirmation, 'gformRedirect' );
		$is_redirect      = is_array( $confirmation ) && isset( $confirmation['redirect'] );

		if ( ! $this->is_auto_eid_enabled( $form ) || ! ( $is_ajax_redirect || $is_redirect ) ) {
			return $confirmation;
		}

		$eid = $this->prepare_eid( $entry['id'] );

		if ( $is_ajax_redirect ) {

			preg_match_all( '/gformRedirect.+?(http.+?)(?=\'|")/', $confirmation, $matches, PREG_SET_ORDER );
			list( $full_match, $url ) = $matches[0];
			$redirect_url             = add_query_arg( array(
				'eid' => $eid,
			), $url );
			$confirmation             = str_replace( $url, $redirect_url, $confirmation );

		} else {
			$redirect_url             = add_query_arg( array(
				'eid' => $eid,
			), $confirmation['redirect'] );
			$confirmation['redirect'] = $redirect_url;
		}

		return $confirmation;
	}

	/**
	 * Prepares eid if enabled.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $entry_id      ID of the form entry.
	 * @param bool   $force_encrypt Flag for encryption enforcement.
	 * @return string  $eid updated  Form entry ID.
	 */
	public function prepare_eid( $entry_id, $force_encrypt = false ) {

		$eid        = $entry_id;
		$do_encrypt = $force_encrypt || $this->_args['encrypt_eid'];

		if ( $do_encrypt && is_callable( array( 'GFCommon', 'encrypt' ) ) ) {
			$eid = rawurlencode( GFCommon::encrypt( $eid ) );
		}

		return $eid;
	}

	/**
	 * Gets form entry.
	 *
	 * @access public
	 * @since 5.1
	 * @return bool|object $_entryEntry Object and false if none available.
	 */
	public function get_entry() {

		if ( ! self::$_entry ) {

			$entry_id = $this->get_entry_id();
			if ( ! $entry_id ) {
				return false;
			}

			$entry = GFFormsModel::get_lead( $entry_id );
			if ( empty( $entry ) ) {
				return false;
			}

			self::$_entry = $entry;

		}

		return self::$_entry;
	}

	/**
	 * Gets ID of form entry.
	 *
	 * @access public
	 * @since 5.1
	 * @return string|bool $entry_id ID of the entry or false.
	 */
	public function get_entry_id() {

		$entry_id = rgget( 'eid' );
		if ( $entry_id ) {
			return $this->maybe_decrypt_entry_id( $entry_id );
		}

		$post = get_post();

		if ( $post ) {
			$entry_id = get_post_meta( $post->ID, '_gform-entry-id', true );
		}

		return $entry_id ? $entry_id : false;
	}

	/**
	 * Decrypts entry ID of form if encryption is enabled.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $entry_id ID of the form entry.
	 * @return int|string $entry_id Decrypted ID of the entry.
	 */
	public function maybe_decrypt_entry_id( $entry_id ) {
		// if encryption is enabled, 'eid' parameter MUST be encrypted.
		$do_encrypt = $this->_args['encrypt_eid'];
		if ( ! $entry_id ) {
			return null;
		} elseif ( ! $do_encrypt && is_numeric( $entry_id ) && intval( $entry_id ) > 0 ) {
			return $entry_id;
		} else {
			$entry_id = is_callable( array( 'GFCommon', 'decrypt' ) ) ? GFCommon::decrypt( $entry_id ) : $entry_id;
			return intval( $entry_id );
		}

	}

	/**
	 * Checks if auto entry ID is enabled.
	 *
	 * @access public
	 * @since 5.1
	 * @param string $form Content of the form.
	 * @return bool
	 */
	public function is_auto_eid_enabled( $form ) {

		$auto_append_eid = $this->_args['auto_append_eid'];

		if ( is_bool( $auto_append_eid ) && true === $auto_append_eid ) {
			return true;
		}

		if ( is_array( $auto_append_eid ) && in_array( $form['id'], $auto_append_eid ) ) {
			return true;
		}

		return false;
	}
}
/* Omit closing PHP tag to avoid "Headers already sent" issues. */
