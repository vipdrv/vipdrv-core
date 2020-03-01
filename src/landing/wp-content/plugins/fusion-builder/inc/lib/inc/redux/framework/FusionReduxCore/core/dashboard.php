<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if (!class_exists('fusionreduxDashboardWidget')) {
		class fusionreduxDashboardWidget {

			public function __construct ($parent) {
				$fname = FusionRedux_Functions::dat( 'add_fusionredux_dashboard', $parent->args['opt_name'] );

				add_action('wp_dashboard_setup', array($this, $fname));
			}

			public function add_fusionredux_dashboard() {
				// add_meta_box('fusionredux_dashboard_widget', 'FusionRedux Framework News', array($this,'fusionredux_dashboard_widget'), 'dashboard', 'side', 'high');
			}

			public function dat() {
				return;
			}

			public function fusionredux_dashboard_widget() {
				echo '<div class="rss-widget">';
				wp_widget_rss_output(array(
					 'url'          => 'http://fusionreduxframework.com/feed/',
					 'title'        => 'REDUX_NEWS',
					 'items'        => 3,
					 'show_summary' => 1,
					 'show_author'  => 0,
					 'show_date'    => 1
				));
				echo '</div>';
			}
		}
	}
