<?php

	if ( ! class_exists( 'FusionRedux_Validation_css' ) ) {
		class FusionRedux_Validation_css {

			/**
			 * Field Constructor.
			 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
			 *
			 * @since FusionReduxFramework 1.0.0
			 */
			function __construct( $parent, $field, $value, $current ) {

				$this->parent  = $parent;
				$this->field   = $field;
				$this->value   = $value;
				$this->current = $current;

				$this->validate();
			}

			//function

			/**
			 * Field Render Function.
			 * Takes the vars and validates them
			 *
			 * @since FusionReduxFramework 3.0.0
			 */
			function validate() {

				$data = $this->value;

				$data = wp_filter_nohtml_kses( $data );
				$data = str_replace( '&gt;', '>', $data );
				$data = stripslashes($data);

				if ($data != $this->value) {
					$this->field['msg'] = __( 'Unsafe strings were found in your CSS and have been filtered out.', 'Avada' );
					$this->warning = $this->field;
				}

				$this->value = $data;

			} //function
		} //class
	}
