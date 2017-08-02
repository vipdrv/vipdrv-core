<?php

	if ( !defined ( 'ABSPATH' ) ) {
		exit;
	}

	if (!class_exists('fusionreduxCoreRequired')){
		class fusionreduxCoreRequired {
			public $parent      = null;

			public function __construct ($parent) {
				$this->parent = $parent;
				FusionRedux_Functions::$_parent = $parent;


				/**
				 * action 'fusionredux/page/{opt_name}/'
				 */
				do_action( "fusionredux/page/{$parent->args['opt_name']}/" );

			}


		}
	}
