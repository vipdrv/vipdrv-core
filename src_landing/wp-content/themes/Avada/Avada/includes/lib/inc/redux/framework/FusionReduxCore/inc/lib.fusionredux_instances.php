<?php

	/**
	 * FusionReduxFrameworkInstances Functions
	 *
	 * @package     FusionRedux_Framework
	 * @subpackage  Core
	 */
	if ( ! function_exists( 'get_fusionredux_instance' ) ) {

		/**
		 * Retreive an instance of FusionReduxFramework
		 *
		 * @param  string $opt_name the defined opt_name as passed in $args
		 *
		 * @return object                FusionReduxFramework
		 */
		function get_fusionredux_instance( $opt_name ) {
			return FusionReduxFrameworkInstances::get_instance( $opt_name );
		}
	}

	if ( ! function_exists( 'get_all_fusionredux_instances' ) ) {

		/**
		 * Retreive all instances of FusionReduxFramework
		 * as an associative array.
		 *
		 * @return array        format ['opt_name' => $FusionReduxFramework]
		 */
		function get_all_fusionredux_instances() {
			return FusionReduxFrameworkInstances::get_all_instances();
		}
	}
