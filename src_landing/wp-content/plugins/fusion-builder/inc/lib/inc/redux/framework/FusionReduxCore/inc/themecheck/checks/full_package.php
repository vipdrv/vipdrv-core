<?php

	class FusionRedux_Full_Package implements themecheck {
		protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

			$ret = true;

			$check = FusionRedux_ThemeCheck::get_instance();
			$fusionredux = $check::get_fusionredux_details( $php_files );

			if ( $fusionredux ) {

				$blacklist = array(
					'.tx'                    => __( 'FusionRedux localization utilities', 'themecheck', 'fusion-builder' ),
					'bin'                    => __( 'FusionRedux Resting Diles', 'themecheck', 'fusion-builder' ),
					'codestyles'             => __( 'FusionRedux Code Styles', 'themecheck', 'fusion-builder' ),
					'tests'                  => __( 'FusionRedux Unit Testing', 'themecheck', 'fusion-builder' ),
					'class.fusionredux-plugin.php' => __( 'FusionRedux Plugin File', 'themecheck', 'fusion-builder' ),
					'bootstrap_tests.php'    => __( 'FusionRedux Boostrap Tests', 'themecheck', 'fusion-builder' ),
					'.travis.yml'            => __( 'CI Testing FIle', 'themecheck', 'fusion-builder' ),
					'phpunit.xml'            => __( 'PHP Unit Testing', 'themecheck', 'fusion-builder' ),
				);

				$errors = array();

				foreach ( $blacklist as $file => $reason ) {
					checkcount();
					if ( file_exists( $fusionredux['parent_dir'] . $file ) ) {
						$errors[ $fusionredux['parent_dir'] . $file ] = $reason;
					}
				}

				if ( ! empty( $errors ) ) {
					$error = '<span class="tc-lead tc-required">REQUIRED</span> ' . __( 'It appears that you have embedded the full FusionRedux package inside your theme. You need only embed the <strong>FusionReduxCore</strong> folder. Embedding anything else will get your rejected from theme submission. Suspected FusionRedux package file(s):', 'fusion-builder' );
					$error .= '<ol>';
					foreach ( $errors as $key => $e ) {
						$error .= '<li><strong>' . $e . '</strong>: ' . $key . '</li>';
					}
					$error .= '</ol>';
					$this->error[] = '<div class="fusionredux-error">' . $error . '</div>';
					$ret           = false;
				}
			}

			return $ret;
		}

		function getError() {
			return $this->error;
		}
	}

	$themechecks[] = new FusionRedux_Full_Package();
