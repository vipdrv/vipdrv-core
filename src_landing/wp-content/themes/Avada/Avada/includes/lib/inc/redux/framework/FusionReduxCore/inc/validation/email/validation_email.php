<?php

	if ( ! class_exists( 'FusionRedux_Validation_email' ) ) {
		class FusionRedux_Validation_email {

			/**
			 * Field Constructor.
			 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
			 *
			 * @since FusionReduxFramework 1.0.0
			 */
			function __construct( $parent, $field, $value, $current ) {

				$this->parent       = $parent;
				$this->field        = $field;
				$this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : __( 'You must provide a valid email for this option.', 'Avada' );
				$this->value        = $value;
				$this->current      = $current;

				$this->validate();
			} //function

			/**
			 * Field Render Function.
			 * Takes the vars and outputs the HTML for the field in the settings
			 *
			 * @since FusionReduxFramework 1.0.0
			 */
			function validate() {

				if ( ! is_email( $this->value ) ) {
					$this->value = ( isset( $this->current ) ) ? $this->current : '';
					$this->error = $this->field;
				}
			} //function
		} //class
	}
