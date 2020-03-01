<?php

	class FusionRedux_Full_Package implements themecheck {
		protected $error = array();

		function check( $php_files, $css_files, $other_files ) {

			$ret = true;

			$check = FusionRedux_ThemeCheck::get_instance();
			$fusionredux = $check::get_fusionredux_details( $php_files );

			if ( $fusionredux ) {

				$blacklist = array(
					'.tx'                    => __( 'FusionRedux localization utilities', 'themecheck', 'Avada' ),
					'bin'                    => __( 'FusionRedux Resting Diles', 'themecheck', 'Avada' ),
					'codestyles'             => __( 'FusionRedux Code Styles', 'themecheck', 'Avada' ),
					'tests'                  => __( 'FusionRedux Unit Testing', 'themecheck', 'Avada' ),
					'class.fusionredux-plugin.php' => __( 'FusionRedux Plugin File', 'themecheck', 'Avada' ),
					'bootstrap_tests.php'    => __( 'FusionRedux Boostrap Tests', 'themecheck', 'Avada' ),
					'.travis.yml'            => __( 'CI Testing FIle', 'themecheck', 'Avada' ),
					'phpunit.xml'            => __( 'PHP Unit Testing', 'themecheck', 'Avada' ),
				);

				$errors = array();

				foreach ( $blacklist as $file => $reason ) {
					checkcount();
					if ( file_exists( $fusionredux['parent_dir'] . $file ) ) {
						$errors[ $fusionredux['parent_dir'] . $file ] = $reason;
					}
				}

				if ( ! empty( $errors ) ) {
					$error = '<span class="tc-lead tc-required">REQUIRED</span> ' . __( 'It appears that you have embedded the full FusionRedux package inside your theme. You need only embed the <strong>FusionReduxCore</strong> folder. Embedding anything else will get your rejected from theme submission. Suspected FusionRedux package file(s):', 'Avada' );
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
