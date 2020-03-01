<?php
/**
 * This class contains static functions
 * that contain collections of data.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * A collection of data.
 *
 * @since 1.0.0
 */
class Fusion_Data {

	/**
	 * Returns an array of all the social icons included in the core fusion font.
	 *
	 * @static
	 * @access public
	 * @param  bool $custom If we want a custom icon entry.
	 * @param  bool $colors If we want to get the colors.
	 * @return  array
	 */
	public static function fusion_social_icons( $custom = true, $colors = false ) {

		$networks = array(
			'blogger'    => array(
				'label' => 'Blogger',
				'color' => '#f57d00',
			),
			'deviantart' => array(
				'label' => 'Deviantart',
				'color' => '#4dc47d',
			),
			'digg'       => array(
				'label' => 'Digg',
				'color' => '#000000',
			),
			'dribbble'   => array(
				'label' => 'Dribbble',
				'color' => '#ea4c89',
			),
			'dropbox'    => array(
				'label' => 'Dropbox',
				'color' => '#007ee5',
			),
			'facebook'   => array(
				'label' => 'Facebook',
				'color' => '#3b5998',
			),
			'flickr'     => array(
				'label' => 'Flickr',
				'color' => '#0063dc',
			),
			'forrst'     => array(
				'label' => 'Forrst',
				'color' => '#5b9a68',
			),
			'gplus'      => array(
				'label' => 'Google+',
				'color' => '#dc4e41',
			),
			'instagram'  => array(
				'label' => 'Instagram',
				'color' => '#3f729b',
			),
			'linkedin'   => array(
				'label' => 'LinkedIn',
				'color' => '#0077b5',
			),
			'myspace'    => array(
				'label' => 'Myspace',
				'color' => '#000000',
			),
			'paypal'     => array(
				'label' => 'Paypal',
				'color' => '#003087',
			),
			'pinterest'  => array(
				'label' => 'Pinterest',
				'color' => '#bd081c',
			),
			'reddit'     => array(
				'label' => 'Reddit',
				'color' => '#ff4500',
			),
			'rss'        => array(
				'label' => 'RSS',
				'color' => '#f26522',
			),
			'skype'      => array(
				'label' => 'Skype',
				'color' => '#00aff0',
			),
			'soundcloud' => array(
				'label' => 'Soundcloud',
				'color' => '#ff8800',
			),
			'spotify'    => array(
				'label' => 'Spotify',
				'color' => '#2ebd59',
			),
			'tumblr'     => array(
				'label' => 'Tumblr',
				'color' => '#35465c',
			),
			'twitter'    => array(
				'label' => 'Twitter',
				'color' => '#55acee',
			),
			'vimeo'      => array(
				'label' => 'Vimeo',
				'color' => '#1ab7ea',
			),
			'vk'         => array(
				'label' => 'VK',
				'color' => '#45668e',
			),
			'xing'       => array(
				'label' => 'Xing',
				'color' => '#026466',
			),
			'yahoo'      => array(
				'label' => 'Yahoo',
				'color' => '#410093',
			),
			'yelp'       => array(
				'label' => 'Yelp',
				'color' => '#af0606',
			),
			'youtube'    => array(
				'label' => 'Youtube',
				'color' => '#cd201f',
			),
			'email'      => array(
				'label' => esc_html__( 'Email Address', 'fusion-builder' ),
				'color' => '#000000',
			),
		);

		// Add a "custom" entry.
		if ( $custom ) {
			$networks['custom'] = array(
				'label' => esc_attr__( 'Custom', 'fusion-builder' ),
				'color' => '',
			);
		}

		if ( ! $colors ) {
			$simple_networks = array();
			foreach ( $networks as $network_id => $network_args ) {
				$simple_networks[ $network_id ] = $network_args['label'];
			}
			$networks = $simple_networks;
		}

		return $networks;

	}

	/**
	 * Returns an array of old names for font-awesome icons
	 * and their new destinations on font-awesome.
	 *
	 * @static
	 * @access public
	 */
	public static function old_icons() {

		$icons = array(
			'arrow'                  => 'angle-right',
			'asterik'                => 'asterisk',
			'cross'                  => 'times',
			'ban-circle'             => 'ban',
			'bar-chart'              => 'bar-chart-o',
			'beaker'                 => 'flask',
			'bell'                   => 'bell-o',
			'bell-alt'               => 'bell',
			'bitbucket-sign'         => 'bitbucket-square',
			'bookmark-empty'         => 'bookmark-o',
			'building'               => 'building-o',
			'calendar-empty'         => 'calendar-o',
			'check-empty'            => 'square-o',
			'check-minus'            => 'minus-square-o',
			'check-sign'             => 'check-square',
			'check'                  => 'check-square-o',
			'chevron-sign-down'      => 'chevron-circle-down',
			'chevron-sign-left'      => 'chevron-circle-left',
			'chevron-sign-right'     => 'chevron-circle-right',
			'chevron-sign-up'        => 'chevron-circle-up',
			'circle-arrow-down'      => 'arrow-circle-down',
			'circle-arrow-left'      => 'arrow-circle-left',
			'circle-arrow-right'     => 'arrow-circle-right',
			'circle-arrow-up'        => 'arrow-circle-up',
			'circle-blank'           => 'circle-o',
			'cny'                    => 'rub',
			'collapse-alt'           => 'minus-square-o',
			'collapse-top'           => 'caret-square-o-up',
			'collapse'               => 'caret-square-o-down',
			'comment-alt'            => 'comment-o',
			'comments-alt'           => 'comments-o',
			'copy'                   => 'files-o',
			'cut'                    => 'scissors',
			'dashboard'              => 'tachometer',
			'double-angle-down'      => 'angle-double-down',
			'double-angle-left'      => 'angle-double-left',
			'double-angle-right'     => 'angle-double-right',
			'double-angle-up'        => 'angle-double-up',
			'download'               => 'arrow-circle-o-down',
			'download-alt'           => 'download',
			'edit-sign'              => 'pencil-square',
			'edit'                   => 'pencil-square-o',
			'ellipsis-horizontal'    => 'ellipsis-h',
			'ellipsis-vertical'      => 'ellipsis-v',
			'envelope-alt'           => 'envelope-o',
			'exclamation-sign'       => 'exclamation-circle',
			'expand-alt'             => 'plus-square-o',
			'expand'                 => 'caret-square-o-right',
			'external-link-sign'     => 'external-link-square',
			'eye-close'              => 'eye-slash',
			'eye-open'               => 'eye',
			'facebook-sign'          => 'facebook-square',
			'facetime-video'         => 'video-camera',
			'file-alt'               => 'file-o',
			'file-text-alt'          => 'file-text-o',
			'flag-alt'               => 'flag-o',
			'folder-close-alt'       => 'folder-o',
			'folder-close'           => 'folder',
			'folder-open-alt'        => 'folder-open-o',
			'food'                   => 'cutlery',
			'frown'                  => 'frown-o',
			'fullscreen'             => 'arrows-alt',
			'github-sign'            => 'github-square',
			'google-plus-sign'       => 'google-plus-square',
			'group'                  => 'users',
			'h-sign'                 => 'h-square',
			'hand-down'              => 'hand-o-down',
			'hand-left'              => 'hand-o-left',
			'hand-right'             => 'hand-o-right',
			'hand-up'                => 'hand-o-up',
			'hdd'                    => 'hdd-o',
			'heart-empty'            => 'heart-o',
			'hospital'               => 'hospital-o',
			'indent-left'            => 'outdent',
			'indent-right'           => 'indent',
			'info-sign'              => 'info-circle',
			'keyboard'               => 'keyboard-o',
			'legal'                  => 'gavel',
			'lemon'                  => 'lemon-o',
			'lightbulb'              => 'lightbulb-o',
			'linkedin-sign'          => 'linkedin-square',
			'meh'                    => 'meh-o',
			'microphone-off'         => 'microphone-slash',
			'minus-sign-alt'         => 'minus-square',
			'minus-sign'             => 'minus-circle',
			'mobile-phone'           => 'mobile',
			'moon'                   => 'moon-o',
			'move'                   => 'arrows',
			'off'                    => 'power-off',
			'ok-circle'              => 'check-circle-o',
			'ok-sign'                => 'check-circle',
			'ok'                     => 'check',
			'paper-clip'             => 'paperclip',
			'paste'                  => 'clipboard',
			'phone-sign'             => 'phone-square',
			'picture'                => 'picture-o',
			'pinterest-sign'         => 'pinterest-square',
			'play-circle'            => 'play-circle-o',
			'play-sign'              => 'play-circle',
			'plus-sign-alt'          => 'plus-square',
			'plus-sign'              => 'plus-circle',
			'pushpin'                => 'thumb-tack',
			'question-sign'          => 'question-circle',
			'remove-circle'          => 'times-circle-o',
			'remove-sign'            => 'times-circle',
			'remove'                 => 'times',
			'reorder'                => 'bars',
			'resize-full'            => 'expand',
			'resize-horizontal'      => 'arrows-h',
			'resize-small'           => 'compress',
			'resize-vertical'        => 'arrows-v',
			'rss-sign'               => 'rss-square',
			'save'                   => 'floppy-o',
			'screenshot'             => 'crosshairs',
			'share-alt'              => 'share',
			'share-sign'             => 'share-square',
			'share'                  => 'share-square-o',
			'sign-blank'             => 'square',
			'signin'                 => 'sign-in',
			'signout'                => 'sign-out',
			'smile'                  => 'smile-o',
			'sort-by-alphabet-alt'   => 'sort-alpha-desc',
			'sort-by-alphabet'       => 'sort-alpha-asc',
			'sort-by-attributes-alt' => 'sort-amount-desc',
			'sort-by-attributes'     => 'sort-amount-asc',
			'sort-by-order-alt'      => 'sort-numeric-desc',
			'sort-by-order'          => 'sort-numeric-asc',
			'sort-down'              => 'sort-asc',
			'sort-up'                => 'sort-desc',
			'stackexchange'          => 'stack-overflow',
			'star-empty'             => 'star-o',
			'star-half-empty'        => 'star-half-o',
			'sun'                    => 'sun-o',
			'thumbs-down-alt'        => 'thumbs-o-down',
			'thumbs-up-alt'          => 'thumbs-o-up',
			'time'                   => 'clock-o',
			'trash'                  => 'trash-o',
			'tumblr-sign'            => 'tumblr-square',
			'twitter-sign'           => 'twitter-square',
			'unlink'                 => 'chain-broken',
			'upload'                 => 'arrow-circle-o-up',
			'upload-alt'             => 'upload',
			'warning-sign'           => 'exclamation-triangle',
			'xing-sign'              => 'xing-square',
			'youtube-sign'           => 'youtube-square',
			'zoom-in'                => 'search-plus',
			'zoom-out'               => 'search-minus',
		);

		return $icons;

	}

	/**
	 * All font-awesome icons
	 * the array is copied from https://github.com/Smartik89/SMK-Font-Awesome-PHP-JSON
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function fa_icons() {
		$fa_array = array(
			'facebook-square' => 'Facebook square',
			'flickr' => 'Flickr',
			'rss-square' => 'Rss square',
			'twitter-square' => 'Twitter square',
			'vimeo-square' => 'Vimeo square',
			'youtube-square' => 'Youtube square',
			'instagram' => 'Instagram',
			'pinterest-square' => 'Pinterest square',
			'tumblr-square' => 'Tumblr square',
			'google-plus-square' => 'Google plus square',
			'dribbble' => 'Dribbble',
			'digg' => 'Digg',
			'linkedin-square' => 'Linkedin square',
			'skype' => 'Skype',
			'deviantart' => 'Deviantart',
			'yahoo' => 'Yahoo',
			'reddit-square' => 'Reddit square',
			'paypal' => 'Paypal',
			'dropbox' => 'Dropbox',
			'soundcloud' => 'Soundcloud',
			'vk' => 'Vk',
			'envelope-square' => 'Envelope square',
			'none' => '———————————————————————',

			'500px' => '500px',
			'adjust' => 'Adjust',
			'adn' => 'Adn',
			'align-center' => 'Align center',
			'align-justify' => 'Align justify',
			'align-left' => 'Align left',
			'align-right' => 'Align right',
			'amazon' => 'Amazon',
			'ambulance' => 'Ambulance',
			'anchor' => 'Anchor',
			'android' => 'Android',
			'angellist' => 'Angellist',
			'angle-double-down' => 'Angle double down',
			'angle-double-left' => 'Angle double left',
			'angle-double-right' => 'Angle double right',
			'angle-double-up' => 'Angle double up',
			'angle-down' => 'Angle down',
			'angle-left' => 'Angle left',
			'angle-right' => 'Angle right',
			'angle-up' => 'Angle up',
			'apple' => 'Apple',
			'archive' => 'Archive',
			'area-chart' => 'Area chart',
			'arrow-circle-down' => 'Arrow circle down',
			'arrow-circle-left' => 'Arrow circle left',
			'arrow-circle-o-down' => 'Arrow circle o down',
			'arrow-circle-o-left' => 'Arrow circle o left',
			'arrow-circle-o-right' => 'Arrow circle o right',
			'arrow-circle-o-up' => 'Arrow circle o up',
			'arrow-circle-right' => 'Arrow circle right',
			'arrow-circle-up' => 'Arrow circle up',
			'arrow-down' => 'Arrow down',
			'arrow-left' => 'Arrow left',
			'arrow-right' => 'Arrow right',
			'arrow-up' => 'Arrow up',
			'arrows' => 'Arrows',
			'arrows-alt' => 'Arrows alt',
			'arrows-h' => 'Arrows h',
			'arrows-v' => 'Arrows v',
			'asterisk' => 'Asterisk',
			'at' => 'At',
			'backward' => 'Backward',
			'balance-scale' => 'Balance scale',
			'ban' => 'Ban',
			'bar-chart' => 'Bar chart',
			'barcode' => 'Barcode',
			'bars' => 'Bars',
			'battery-empty' => 'Battery empty',
			'battery-full' => 'Battery full',
			'battery-half' => 'Battery half',
			'battery-quarter' => 'Battery quarter',
			'battery-three-quarters' => 'Battery three quarters',
			'bed' => 'Bed',
			'beer' => 'Beer',
			'behance' => 'Behance',
			'behance-square' => 'Behance square',
			'bell' => 'Bell',
			'bell-o' => 'Bell o',
			'bell-slash' => 'Bell slash',
			'bell-slash-o' => 'Bell slash o',
			'bicycle' => 'Bicycle',
			'binoculars' => 'Binoculars',
			'birthday-cake' => 'Birthday cake',
			'bitbucket' => 'Bitbucket',
			'bitbucket-square' => 'Bitbucket square',
			'black-tie' => 'Black tie',
			'bold' => 'Bold',
			'bolt' => 'Bolt',
			'bomb' => 'Bomb',
			'book' => 'Book',
			'bookmark' => 'Bookmark',
			'bookmark-o' => 'Bookmark o',
			'briefcase' => 'Briefcase',
			'btc' => 'Btc',
			'bug' => 'Bug',
			'building' => 'Building',
			'building-o' => 'Building o',
			'bullhorn' => 'Bullhorn',
			'bullseye' => 'Bullseye',
			'bus' => 'Bus',
			'buysellads' => 'Buysellads',
			'calculator' => 'Calculator',
			'calendar' => 'Calendar',
			'calendar-check-o' => 'Calendar check o',
			'calendar-minus-o' => 'Calendar minus o',
			'calendar-o' => 'Calendar o',
			'calendar-plus-o' => 'Calendar plus o',
			'calendar-times-o' => 'Calendar times o',
			'camera' => 'Camera',
			'camera-retro' => 'Camera retro',
			'car' => 'Car',
			'caret-down' => 'Caret down',
			'caret-left' => 'Caret left',
			'caret-right' => 'Caret right',
			'caret-square-o-down' => 'Caret square o down',
			'caret-square-o-left' => 'Caret square o left',
			'caret-square-o-right' => 'Caret square o right',
			'caret-square-o-up' => 'Caret square o up',
			'caret-up' => 'Caret up',
			'cart-arrow-down' => 'Cart arrow down',
			'cart-plus' => 'Cart plus',
			'cc' => 'Cc',
			'cc-amex' => 'Cc amex',
			'cc-diners-club' => 'Cc diners club',
			'cc-discover' => 'Cc discover',
			'cc-jcb' => 'Cc jcb',
			'cc-mastercard' => 'Cc mastercard',
			'cc-paypal' => 'Cc paypal',
			'cc-stripe' => 'Cc stripe',
			'cc-visa' => 'Cc visa',
			'certificate' => 'Certificate',
			'chain-broken' => 'Chain broken',
			'check' => 'Check',
			'check-circle' => 'Check circle',
			'check-circle-o' => 'Check circle o',
			'check-square' => 'Check square',
			'check-square-o' => 'Check square o',
			'chevron-circle-down' => 'Chevron circle down',
			'chevron-circle-left' => 'Chevron circle left',
			'chevron-circle-right' => 'Chevron circle right',
			'chevron-circle-up' => 'Chevron circle up',
			'chevron-down' => 'Chevron down',
			'chevron-left' => 'Chevron left',
			'chevron-right' => 'Chevron right',
			'chevron-up' => 'Chevron up',
			'child' => 'Child',
			'chrome' => 'Chrome',
			'circle' => 'Circle',
			'circle-o' => 'Circle o',
			'circle-o-notch' => 'Circle o notch',
			'circle-thin' => 'Circle thin',
			'clipboard' => 'Clipboard',
			'clock-o' => 'Clock o',
			'clone' => 'Clone',
			'cloud' => 'Cloud',
			'cloud-download' => 'Cloud download',
			'cloud-upload' => 'Cloud upload',
			'code' => 'Code',
			'code-fork' => 'Code fork',
			'codepen' => 'Codepen',
			'coffee' => 'Coffee',
			'cog' => 'Cog',
			'cogs' => 'Cogs',
			'columns' => 'Columns',
			'comment' => 'Comment',
			'comment-o' => 'Comment o',
			'commenting' => 'Commenting',
			'commenting-o' => 'Commenting o',
			'comments' => 'Comments',
			'comments-o' => 'Comments o',
			'compass' => 'Compass',
			'compress' => 'Compress',
			'connectdevelop' => 'Connectdevelop',
			'contao' => 'Contao',
			'copyright' => 'Copyright',
			'creative-commons' => 'Creative commons',
			'credit-card' => 'Credit card',
			'crop' => 'Crop',
			'crosshairs' => 'Crosshairs',
			'css3' => 'Css3',
			'cube' => 'Cube',
			'cubes' => 'Cubes',
			'cutlery' => 'Cutlery',
			'dashcube' => 'Dashcube',
			'database' => 'Database',
			'delicious' => 'Delicious',
			'desktop' => 'Desktop',
			// 'deviantart' => 'Deviantart',
			'diamond' => 'Diamond',
			// 'digg' => 'Digg',
			'dot-circle-o' => 'Dot circle o',
			'download' => 'Download',
			// 'dribbble' => 'Dribbble',
			// 'dropbox' => 'Dropbox',
			'drupal' => 'Drupal',
			'eject' => 'Eject',
			'ellipsis-h' => 'Ellipsis h',
			'ellipsis-v' => 'Ellipsis v',
			'empire' => 'Empire',
			'envelope' => 'Envelope',
			'envelope-o' => 'Envelope o',
			// 'envelope-square' => 'Envelope square',
			'eraser' => 'Eraser',
			'eur' => 'Eur',
			'exchange' => 'Exchange',
			'exclamation' => 'Exclamation',
			'exclamation-circle' => 'Exclamation circle',
			'exclamation-triangle' => 'Exclamation triangle',
			'expand' => 'Expand',
			'expeditedssl' => 'Expeditedssl',
			'external-link' => 'External link',
			'external-link-square' => 'External link square',
			'eye' => 'Eye',
			'eye-slash' => 'Eye slash',
			'eyedropper' => 'Eyedropper',
			'facebook' => 'Facebook',
			'facebook-official' => 'Facebook official',
			'facebook-square' => 'Facebook square',
			'fast-backward' => 'Fast backward',
			'fast-forward' => 'Fast forward',
			'fax' => 'Fax',
			'female' => 'Female',
			'fighter-jet' => 'Fighter jet',
			'file' => 'File',
			'file-archive-o' => 'File archive o',
			'file-audio-o' => 'File audio o',
			'file-code-o' => 'File code o',
			'file-excel-o' => 'File excel o',
			'file-image-o' => 'File image o',
			'file-o' => 'File o',
			'file-pdf-o' => 'File pdf o',
			'file-powerpoint-o' => 'File powerpoint o',
			'file-text' => 'File text',
			'file-text-o' => 'File text o',
			'file-video-o' => 'File video o',
			'file-word-o' => 'File word o',
			'files-o' => 'Files o',
			'film' => 'Film',
			'filter' => 'Filter',
			'fire' => 'Fire',
			'fire-extinguisher' => 'Fire extinguisher',
			'firefox' => 'Firefox',
			'flag' => 'Flag',
			'flag-checkered' => 'Flag checkered',
			'flag-o' => 'Flag o',
			'flask' => 'Flask',
			// 'flickr' => 'Flickr',
			'floppy-o' => 'Floppy o',
			'folder' => 'Folder',
			'folder-o' => 'Folder o',
			'folder-open' => 'Folder open',
			'folder-open-o' => 'Folder open o',
			'font' => 'Font',
			'fonticons' => 'Fonticons',
			'forumbee' => 'Forumbee',
			'forward' => 'Forward',
			'foursquare' => 'Foursquare',
			'frown-o' => 'Frown o',
			'futbol-o' => 'Futbol o',
			'gamepad' => 'Gamepad',
			'gavel' => 'Gavel',
			'gbp' => 'Gbp',
			'genderless' => 'Genderless',
			'get-pocket' => 'Get pocket',
			'gg' => 'Gg',
			'gg-circle' => 'Gg circle',
			'gift' => 'Gift',
			'git' => 'Git',
			'git-square' => 'Git square',
			'github' => 'Github',
			'github-alt' => 'Github alt',
			'github-square' => 'Github square',
			'glass' => 'Glass',
			'globe' => 'Globe',
			'google' => 'Google',
			'google-plus' => 'Google plus',
			// 'google-plus-square' => 'Google plus square',
			'google-wallet' => 'Google wallet',
			'graduation-cap' => 'Graduation cap',
			'gratipay' => 'Gratipay',
			'h-square' => 'H square',
			'hacker-news' => 'Hacker news',
			'hand-lizard-o' => 'Hand lizard o',
			'hand-o-down' => 'Hand o down',
			'hand-o-left' => 'Hand o left',
			'hand-o-right' => 'Hand o right',
			'hand-o-up' => 'Hand o up',
			'hand-paper-o' => 'Hand paper o',
			'hand-peace-o' => 'Hand peace o',
			'hand-pointer-o' => 'Hand pointer o',
			'hand-rock-o' => 'Hand rock o',
			'hand-scissors-o' => 'Hand scissors o',
			'hand-spock-o' => 'Hand spock o',
			'hdd-o' => 'Hdd o',
			'header' => 'Header',
			'headphones' => 'Headphones',
			'heart' => 'Heart',
			'heart-o' => 'Heart o',
			'heartbeat' => 'Heartbeat',
			'history' => 'History',
			'home' => 'Home',
			'hospital-o' => 'Hospital o',
			'hourglass' => 'Hourglass',
			'hourglass-end' => 'Hourglass end',
			'hourglass-half' => 'Hourglass half',
			'hourglass-o' => 'Hourglass o',
			'hourglass-start' => 'Hourglass start',
			'houzz' => 'Houzz',
			'html5' => 'Html5',
			'i-cursor' => 'I cursor',
			'ils' => 'Ils',
			'inbox' => 'Inbox',
			'indent' => 'Indent',
			'industry' => 'Industry',
			'info' => 'Info',
			'info-circle' => 'Info circle',
			'inr' => 'Inr',
			// 'instagram' => 'Instagram',
			'internet-explorer' => 'Internet explorer',
			'ioxhost' => 'Ioxhost',
			'italic' => 'Italic',
			'joomla' => 'Joomla',
			'jpy' => 'Jpy',
			'jsfiddle' => 'Jsfiddle',
			'key' => 'Key',
			'keyboard-o' => 'Keyboard o',
			'krw' => 'Krw',
			'language' => 'Language',
			'laptop' => 'Laptop',
			'lastfm' => 'Lastfm',
			'lastfm-square' => 'Lastfm square',
			'leaf' => 'Leaf',
			'leanpub' => 'Leanpub',
			'lemon-o' => 'Lemon o',
			'level-down' => 'Level down',
			'level-up' => 'Level up',
			'life-ring' => 'Life ring',
			'lightbulb-o' => 'Lightbulb o',
			'line-chart' => 'Line chart',
			'link' => 'Link',
			'linkedin' => 'Linkedin',
			// 'linkedin-square' => 'Linkedin square',
			'linux' => 'Linux',
			'list' => 'List',
			'list-alt' => 'List alt',
			'list-ol' => 'List ol',
			'list-ul' => 'List ul',
			'location-arrow' => 'Location arrow',
			'lock' => 'Lock',
			'long-arrow-down' => 'Long arrow down',
			'long-arrow-left' => 'Long arrow left',
			'long-arrow-right' => 'Long arrow right',
			'long-arrow-up' => 'Long arrow up',
			'magic' => 'Magic',
			'magnet' => 'Magnet',
			'male' => 'Male',
			'map' => 'Map',
			'map-marker' => 'Map marker',
			'map-o' => 'Map o',
			'map-pin' => 'Map pin',
			'map-signs' => 'Map signs',
			'mars' => 'Mars',
			'mars-double' => 'Mars double',
			'mars-stroke' => 'Mars stroke',
			'mars-stroke-h' => 'Mars stroke h',
			'mars-stroke-v' => 'Mars stroke v',
			'maxcdn' => 'Maxcdn',
			'meanpath' => 'Meanpath',
			'medium' => 'Medium',
			'medkit' => 'Medkit',
			'meh-o' => 'Meh o',
			'mercury' => 'Mercury',
			'microphone' => 'Microphone',
			'microphone-slash' => 'Microphone slash',
			'minus' => 'Minus',
			'minus-circle' => 'Minus circle',
			'minus-square' => 'Minus square',
			'minus-square-o' => 'Minus square o',
			'mobile' => 'Mobile',
			'money' => 'Money',
			'moon-o' => 'Moon o',
			'motorcycle' => 'Motorcycle',
			'mouse-pointer' => 'Mouse pointer',
			'music' => 'Music',
			'neuter' => 'Neuter',
			'newspaper-o' => 'Newspaper o',
			'object-group' => 'Object group',
			'object-ungroup' => 'Object ungroup',
			'odnoklassniki' => 'Odnoklassniki',
			'odnoklassniki-square' => 'Odnoklassniki square',
			'opencart' => 'Opencart',
			'openid' => 'Openid',
			'opera' => 'Opera',
			'optin-monster' => 'Optin monster',
			'outdent' => 'Outdent',
			'pagelines' => 'Pagelines',
			'paint-brush' => 'Paint brush',
			'paper-plane' => 'Paper plane',
			'paper-plane-o' => 'Paper plane o',
			'paperclip' => 'Paperclip',
			'paragraph' => 'Paragraph',
			'pause' => 'Pause',
			'paw' => 'Paw',
			// 'paypal' => 'Paypal',
			'pencil' => 'Pencil',
			'pencil-square' => 'Pencil square',
			'pencil-square-o' => 'Pencil square o',
			'phone' => 'Phone',
			'phone-square' => 'Phone square',
			'picture-o' => 'Picture o',
			'pie-chart' => 'Pie chart',
			'pied-piper' => 'Pied piper',
			'pied-piper-alt' => 'Pied piper alt',
			'pinterest' => 'Pinterest',
			'pinterest-p' => 'Pinterest p',
			// 'pinterest-square' => 'Pinterest square',
			'plane' => 'Plane',
			'play' => 'Play',
			'play-circle' => 'Play circle',
			'play-circle-o' => 'Play circle o',
			'plug' => 'Plug',
			'plus' => 'Plus',
			'plus-circle' => 'Plus circle',
			'plus-square' => 'Plus square',
			'plus-square-o' => 'Plus square o',
			'power-off' => 'Power off',
			'print' => 'Print',
			'puzzle-piece' => 'Puzzle piece',
			'qq' => 'Qq',
			'qrcode' => 'Qrcode',
			'question' => 'Question',
			'question-circle' => 'Question circle',
			'quote-left' => 'Quote left',
			'quote-right' => 'Quote right',
			'random' => 'Random',
			'rebel' => 'Rebel',
			'recycle' => 'Recycle',
			'reddit' => 'Reddit',
			// 'reddit-square' => 'Reddit square',
			'refresh' => 'Refresh',
			'registered' => 'Registered',
			'renren' => 'Renren',
			'repeat' => 'Repeat',
			'reply' => 'Reply',
			'reply-all' => 'Reply all',
			'retweet' => 'Retweet',
			'road' => 'Road',
			'rocket' => 'Rocket',
			'rss' => 'Rss',
			// 'rss-square' => 'Rss square',
			'rub' => 'Rub',
			'safari' => 'Safari',
			'scissors' => 'Scissors',
			'search' => 'Search',
			'search-minus' => 'Search minus',
			'search-plus' => 'Search plus',
			'sellsy' => 'Sellsy',
			'server' => 'Server',
			'share' => 'Share',
			'share-alt' => 'Share alt',
			'share-alt-square' => 'Share alt square',
			'share-square' => 'Share square',
			'share-square-o' => 'Share square o',
			'shield' => 'Shield',
			'ship' => 'Ship',
			'shirtsinbulk' => 'Shirtsinbulk',
			'shopping-cart' => 'Shopping cart',
			'sign-in' => 'Sign in',
			'sign-out' => 'Sign out',
			'signal' => 'Signal',
			'simplybuilt' => 'Simplybuilt',
			'sitemap' => 'Sitemap',
			'skyatlas' => 'Skyatlas',
			// 'skype' => 'Skype',
			'slack' => 'Slack',
			'sliders' => 'Sliders',
			'slideshare' => 'Slideshare',
			'smile-o' => 'Smile o',
			'sort' => 'Sort',
			'sort-alpha-asc' => 'Sort alpha asc',
			'sort-alpha-desc' => 'Sort alpha desc',
			'sort-amount-asc' => 'Sort amount asc',
			'sort-amount-desc' => 'Sort amount desc',
			'sort-asc' => 'Sort asc',
			'sort-desc' => 'Sort desc',
			'sort-numeric-asc' => 'Sort numeric asc',
			'sort-numeric-desc' => 'Sort numeric desc',
			// 'soundcloud' => 'Soundcloud',
			'space-shuttle' => 'Space shuttle',
			'spinner' => 'Spinner',
			'spoon' => 'Spoon',
			'spotify' => 'Spotify',
			'square' => 'Square',
			'square-o' => 'Square o',
			'stack-exchange' => 'Stack exchange',
			'stack-overflow' => 'Stack overflow',
			'star' => 'Star',
			'star-half' => 'Star half',
			'star-half-o' => 'Star half o',
			'star-o' => 'Star o',
			'steam' => 'Steam',
			'steam-square' => 'Steam square',
			'step-backward' => 'Step backward',
			'step-forward' => 'Step forward',
			'stethoscope' => 'Stethoscope',
			'sticky-note' => 'Sticky note',
			'sticky-note-o' => 'Sticky note o',
			'stop' => 'Stop',
			'street-view' => 'Street view',
			'strikethrough' => 'Strikethrough',
			'stumbleupon' => 'Stumbleupon',
			'stumbleupon-circle' => 'Stumbleupon circle',
			'subscript' => 'Subscript',
			'subway' => 'Subway',
			'suitcase' => 'Suitcase',
			'sun-o' => 'Sun o',
			'superscript' => 'Superscript',
			'table' => 'Table',
			'tablet' => 'Tablet',
			'tachometer' => 'Tachometer',
			'tag' => 'Tag',
			'tags' => 'Tags',
			'tasks' => 'Tasks',
			'taxi' => 'Taxi',
			'television' => 'Television',
			'tencent-weibo' => 'Tencent weibo',
			'terminal' => 'Terminal',
			'text-height' => 'Text height',
			'text-width' => 'Text width',
			'th' => 'Th',
			'th-large' => 'Th large',
			'th-list' => 'Th list',
			'thumb-tack' => 'Thumb tack',
			'thumbs-down' => 'Thumbs down',
			'thumbs-o-down' => 'Thumbs o down',
			'thumbs-o-up' => 'Thumbs o up',
			'thumbs-up' => 'Thumbs up',
			'ticket' => 'Ticket',
			'times' => 'Times',
			'times-circle' => 'Times circle',
			'times-circle-o' => 'Times circle o',
			'tint' => 'Tint',
			'toggle-off' => 'Toggle off',
			'toggle-on' => 'Toggle on',
			'trademark' => 'Trademark',
			'train' => 'Train',
			'transgender' => 'Transgender',
			'transgender-alt' => 'Transgender alt',
			'trash' => 'Trash',
			'trash-o' => 'Trash o',
			'tree' => 'Tree',
			'trello' => 'Trello',
			'tripadvisor' => 'Tripadvisor',
			'trophy' => 'Trophy',
			'truck' => 'Truck',
			'try' => 'Try',
			'tty' => 'Tty',
			'tumblr' => 'Tumblr',
			// 'tumblr-square' => 'Tumblr square',
			'twitch' => 'Twitch',
			'twitter' => 'Twitter',
			// 'twitter-square' => 'Twitter square',
			'umbrella' => 'Umbrella',
			'underline' => 'Underline',
			'undo' => 'Undo',
			'university' => 'University',
			'unlock' => 'Unlock',
			'unlock-alt' => 'Unlock alt',
			'upload' => 'Upload',
			'usd' => 'Usd',
			'user' => 'User',
			'user-md' => 'User md',
			'user-plus' => 'User plus',
			'user-secret' => 'User secret',
			'user-times' => 'User times',
			'users' => 'Users',
			'venus' => 'Venus',
			'venus-double' => 'Venus double',
			'venus-mars' => 'Venus mars',
			'viacoin' => 'Viacoin',
			'video-camera' => 'Video camera',
			'vimeo' => 'Vimeo',
			// 'vimeo-square' => 'Vimeo square',
			'vine' => 'Vine',
			// 'vk' => 'Vk',
			'volume-down' => 'Volume down',
			'volume-off' => 'Volume off',
			'volume-up' => 'Volume up',
			'weibo' => 'Weibo',
			'weixin' => 'Weixin',
			'whatsapp' => 'Whatsapp',
			'wheelchair' => 'Wheelchair',
			'wifi' => 'Wifi',
			'wikipedia-w' => 'Wikipedia w',
			'windows' => 'Windows',
			'wordpress' => 'Wordpress',
			'wrench' => 'Wrench',
			'xing' => 'Xing',
			'xing-square' => 'Xing square',
			'y-combinator' => 'Y combinator',
			'yahoo' => 'Yahoo',
			'yelp' => 'Yelp',
			'youtube' => 'Youtube',
			'youtube-play' => 'Youtube play',
			// 'youtube-square' => 'Youtube square',
			// Add a "custom" entry
			'custom'                 => 'Custom',
		);

		return $fa_array;

	}

	/**
	 * Get an array of all standard fonts.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function standard_fonts() {

		$standard_fonts = array(
			'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
			"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif" => "'Bookman Old Style', serif",
			"'Comic Sans MS', cursive" => "'Comic Sans MS', cursive",
			'Courier, monospace' => 'Courier, monospace',
			'Garamond, serif' => 'Garamond, serif',
			'Georgia, serif' => 'Georgia, serif',
			'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
			"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif" => "'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif" => "'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
			"'Times New Roman', Times, serif" => "'Times New Roman', Times, serif",
			"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
			'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
		);

		return $standard_fonts;

	}

	/**
	 * Get an array of all font-weights.
	 *
	 * @static
	 * @access public
	 * @return array
	 */
	public static function font_weights() {

		$font_weights = array(
			'100' => esc_attr__( 'Thin (100)', 'fusion-builder' ),
			'200' => esc_attr__( 'Extra Light (200)', 'fusion-builder' ),
			'300' => esc_attr__( 'Light (300)', 'fusion-builder' ),
			'400' => esc_attr__( 'Normal (400)', 'fusion-builder' ),
			'500' => esc_attr__( 'Medium (500)', 'fusion-builder' ),
			'600' => esc_attr__( 'Semi Bold (600)', 'fusion-builder' ),
			'700' => esc_attr__( 'Bold (700)', 'fusion-builder' ),
			'800' => esc_attr__( 'Bolder (800)', 'fusion-builder' ),
			'900' => esc_attr__( 'Extra Bold (900)', 'fusion-builder' ),
		);

		return $font_weights;

	}

	/**
	 * Get an array of all available font subsets for the Google Fonts API.
	 *
	 * @static
	 * @access  public
	 * @return  array
	 */
	public static function font_subsets() {
		return array(
			'greek-ext',
			'greek',
			'cyrillic-ext',
			'cyrillic',
			'latin-ext',
			'latin',
			'vietnamese',
			'arabic',
			'gujarati',
			'devanagari',
			'bengali',
			'hebrew',
			'khmer',
			'tamil',
			'telugu',
			'thai',
		);
	}

	/**
	 * Returns an array of colors to be used in color presets.
	 *
	 * @static
	 * @access public
	 * @since 1.0.0
	 * @param string $context The preset we want.
	 * @return array
	 */
	public static function color_theme( $context ) {
		$options = get_option( Avada::get_option_name(), array() );

		$light = array();
		$light['header_bg_color'] = '#ffffff';
		$light['header_border_color'] = '#e5e5e5';
		$light['content_bg_color'] = '#ffffff';
		$light['slidingbar_bg_color'] = '#363839';
		$light['header_sticky_bg_color'] = '#ffffff';
		$light['footer_bg_color'] = '#363839';
		$light['footer_border_color'] = '#e9eaee';
		$light['copyright_border_color'] = '#4B4C4D';
		$light['testimonial_bg_color'] = '#f6f3f3';
		$light['testimonial_text_color'] = '#747474';
		$light['sep_color'] = '#e0dede';
		$light['slidingbar_divider_color'] = '#505152';
		$light['footer_divider_color'] = '#505152';
		$light['form_bg_color'] = '#ffffff';
		$light['form_text_color'] = '#aaa9a9';
		$light['form_border_color'] = '#d2d2d2';
		$light['tagline_font_color'] = '#747474';
		$light['page_title_color'] = '#333333';
		$light['h1_typography'] = isset( $options['h1_typography'] ) ? $options['h1_typography'] : array();
		$light['h1_typography']['color'] = '#333333';
		$light['h2_typography'] = isset( $options['h2_typography'] ) ? $options['h2_typography'] : array();
		$light['h2_typography']['color'] = '#333333';
		$light['h3_typography'] = isset( $options['h3_typography'] ) ? $options['h3_typography'] : array();
		$light['h3_typography']['color'] = '#333333';
		$light['h4_typography'] = isset( $options['h4_typography'] ) ? $options['h4_typography'] : array();
		$light['h4_typography']['color'] = '#333333';
		$light['h5_typography'] = isset( $options['h5_typography'] ) ? $options['h5_typography'] : array();
		$light['h5_typography']['color'] = '#333333';
		$light['h6_typography'] = isset( $options['h6_typography'] ) ? $options['h6_typography'] : array();
		$light['h6_typography']['color'] = '#333333';
		$light['body_typography'] = isset( $options['body_typography'] ) ? $options['body_typography'] : array();
		$light['body_typography']['color'] = '#747474';
		$light['link_color'] = '#333333';
		$light['menu_h45_bg_color'] = '#FFFFFF';
		$light['menu_first_color'] = '#333333';
		$light['menu_sub_bg_color'] = '#f2efef';
		$light['menu_sub_color'] = '#333333';
		$light['menu_bg_hover_color'] = '#f8f8f8';
		$light['menu_sub_sep_color'] = '#dcdadb';
		$light['snav_color'] = '#ffffff';
		$light['header_social_links_icon_color'] = '#ffffff';
		$light['header_top_first_border_color'] = '#e5e5e5';
		$light['header_top_sub_bg_color'] = '#ffffff';
		$light['header_top_menu_sub_color'] = '#747474';
		$light['header_top_menu_bg_hover_color'] = '#fafafa';
		$light['header_top_menu_sub_hover_color'] = '#333333';
		$light['header_top_menu_sub_sep_color'] = '#e5e5e5';
		$light['sidebar_bg_color'] = '#ffffff';
		$light['page_title_bg_color'] = '#F6F6F6';
		$light['page_title_border_color'] = '#d2d3d4';
		$light['breadcrumbs_text_color'] = '#333333';
		$light['sidebar_heading_color'] = '#333333';
		$light['accordian_inactive_color'] = '#333333';
		$light['counter_filled_color'] = '#a0ce4e';
		$light['counter_unfilled_color'] = '#f6f6f6';
		$light['dates_box_color'] = '#eef0f2';
		$light['carousel_nav_color'] = '#999999';
		$light['carousel_hover_color'] = '#808080';
		$light['content_box_bg_color'] = 'transparent';
		$light['title_border_color'] = '#e0dede';
		$light['icon_circle_color'] = '#333333';
		$light['icon_border_color'] = '#333333';
		$light['icon_color'] = '#ffffff';
		$light['imgframe_border_color'] = '#f6f6f6';
		$light['imgframe_style_color'] = '#000000';
		$light['sep_pricing_box_heading_color'] = '#333333';
		$light['full_boxed_pricing_box_heading_color'] = '#333333';
		$light['pricing_bg_color'] = '#ffffff';
		$light['pricing_border_color'] = '#f8f8f8';
		$light['pricing_divider_color'] = '#ededed';
		$light['social_bg_color'] = '#f6f6f6';
		$light['tabs_bg_color'] = '#ffffff';
		$light['tabs_inactive_color'] = '#f1f2f2';
		$light['tagline_bg'] = '#f6f6f6';
		$light['tagline_border_color'] = '#f6f6f6';
		$light['timeline_bg_color'] = 'transparent';
		$light['timeline_color'] = '#ebeaea';
		$light['woo_cart_bg_color'] = '#fafafa';
		$light['qty_bg_color'] = '#fbfaf9';
		$light['qty_bg_hover_color'] = '#ffffff';
		$light['bbp_forum_header_bg'] = '#ebeaea';
		$light['bbp_forum_border_color'] = '#ebeaea';
		$light['checklist_icons_color'] = '#ffffff';
		$light['flip_boxes_front_bg'] = '#f6f6f6';
		$light['flip_boxes_front_heading'] = '#333333';
		$light['flip_boxes_front_text'] = '#747474';
		$light['full_width_bg_color'] = '#ffffff';
		$light['full_width_border_color'] = '#eae9e9';
		$light['modal_bg_color'] = '#f6f6f6';
		$light['modal_border_color'] = '#ebebeb';
		$light['person_border_color'] = '#f6f6f6';
		$light['popover_heading_bg_color'] = '#f6f6f6';
		$light['popover_content_bg_color'] = '#ffffff';
		$light['popover_border_color'] = '#ebebeb';
		$light['popover_text_color'] = '#747474';
		$light['progressbar_unfilled_color'] = '#f6f6f6';
		$light['section_sep_bg'] = '#f6f6f6';
		$light['section_sep_border_color'] = '#f6f6f6';
		$light['sharing_box_tagline_text_color'] = '#333333';
		$light['header_social_links_icon_color'] = '#bebdbd';
		$light['header_social_links_box_color'] = '#e8e8e8';
		$light['bg_color'] = '#d7d6d6';
		$light['mobile_menu_background_color'] = '#f9f9f9';
		$light['mobile_menu_border_color'] = '#dadada';
		$light['mobile_menu_hover_color'] = '#f6f6f6';
		$light['social_links_icon_color'] = '#bebdbd';
		$light['social_links_box_color'] = '#e8e8e8';
		$light['sharing_social_links_icon_color'] = '#bebdbd';
		$light['sharing_social_links_box_color'] = '#e8e8e8';
		$light['load_more_posts_button_bg_color'] = '#ebeaea';
		$light['ec_bar_bg_color'] = '#efeded';
		$light['flyout_menu_icon_color'] = '#333333';
		$light['flyout_menu_background_color'] = 'rgba(255,255,255,0.95)';
		$light['ec_sidebar_bg_color'] = '#f6f6f6';
		$light['ec_sidebar_link_color'] = '#333333';

		$dark = array();
		$dark['header_bg_color'] = '#29292a';
		$dark['header_border_color'] = '#3e3e3e';
		$dark['header_top_bg_color'] = '#29292a';
		$dark['content_bg_color'] = '#29292a';
		$dark['slidingbar_bg_color'] = '#363839';
		$dark['header_sticky_bg_color'] = '#29292a';
		$dark['slidingbar_border_color'] = '#484747';
		$dark['footer_bg_color'] = '#2d2d2d';
		$dark['footer_border_color'] = '#403f3f';
		$dark['copyright_border_color'] = '#4B4C4D';
		$dark['testimonial_bg_color'] = '#3e3e3e';
		$dark['testimonial_text_color'] = '#aaa9a9';
		$dark['sep_color'] = '#3e3e3e';
		$dark['slidingbar_divider_color'] = '#505152';
		$dark['footer_divider_color'] = '#505152';
		$dark['form_bg_color'] = '#3e3e3e';
		$dark['form_text_color'] = '#cccccc';
		$dark['form_border_color'] = '#212122';
		$dark['tagline_font_color'] = '#ffffff';
		$dark['page_title_color'] = '#ffffff';
		$dark['h1_typography'] = isset( $options['h1_typography'] ) ? $options['h1_typography'] : array();
		$dark['h1_typography']['color'] = '#ffffff';
		$dark['h2_typography'] = isset( $options['h2_typography'] ) ? $options['h2_typography'] : array();
		$dark['h2_typography']['color'] = '#ffffff';
		$dark['h3_typography'] = isset( $options['h3_typography'] ) ? $options['h3_typography'] : array();
		$dark['h3_typography']['color'] = '#ffffff';
		$dark['h4_typography'] = isset( $options['h4_typography'] ) ? $options['h4_typography'] : array();
		$dark['h4_typography']['color'] = '#ffffff';
		$dark['h5_typography'] = isset( $options['h5_typography'] ) ? $options['h5_typography'] : array();
		$dark['h5_typography']['color'] = '#ffffff';
		$dark['h6_typography'] = isset( $options['h6_typography'] ) ? $options['h6_typography'] : array();
		$dark['h6_typography']['color'] = '#ffffff';
		$dark['body_typography'] = isset( $options['body_typography'] ) ? $options['body_typography'] : array();
		$dark['body_typography']['color'] = '#aaa9a9';
		$dark['link_color'] = '#ffffff';
		$dark['menu_h45_bg_color'] = '#29292A';
		$dark['menu_first_color'] = '#ffffff';
		$dark['menu_sub_bg_color'] = '#3e3e3e';
		$dark['menu_sub_color'] = '#d6d6d6';
		$dark['menu_bg_hover_color'] = '#383838';
		$dark['menu_sub_sep_color'] = '#313030';
		$dark['snav_color'] = '#747474';
		$dark['header_social_links_icon_color'] = '#747474';
		$dark['header_top_first_border_color'] = '#3e3e3e';
		$dark['header_top_sub_bg_color'] = '#29292a';
		$dark['header_top_menu_sub_color'] = '#d6d6d6';
		$dark['header_top_menu_bg_hover_color'] = '#333333';
		$dark['header_top_menu_sub_hover_color'] = '#d6d6d6';
		$dark['header_top_menu_sub_sep_color'] = '#3e3e3e';
		$dark['sidebar_bg_color'] = '#29292a';
		$dark['page_title_bg_color'] = '#353535';
		$dark['page_title_border_color'] = '#464646';
		$dark['breadcrumbs_text_color'] = '#ffffff';
		$dark['sidebar_heading_color'] = '#ffffff';
		$dark['accordian_inactive_color'] = '#3e3e3e';
		$dark['counter_filled_color'] = '#a0ce4e';
		$dark['counter_unfilled_color'] = '#3e3e3e';
		$dark['dates_box_color'] = '#3e3e3e';
		$dark['carousel_nav_color'] = '#3a3a3a';
		$dark['carousel_hover_color'] = '#333333';
		$dark['content_box_bg_color'] = 'transparent';
		$dark['title_border_color'] = '#3e3e3e';
		$dark['icon_circle_color'] = '#3e3e3e';
		$dark['icon_border_color'] = '#3e3e3e';
		$dark['icon_color'] = '#ffffff';
		$dark['imgframe_border_color'] = '#494848';
		$dark['imgframe_style_color'] = '#000000';
		$dark['sep_pricing_box_heading_color'] = '#ffffff';
		$dark['full_boxed_pricing_box_heading_color'] = '#AAA9A9';
		$dark['pricing_bg_color'] = '#3e3e3e';
		$dark['pricing_border_color'] = '#353535';
		$dark['pricing_divider_color'] = '#29292a';
		$dark['social_bg_color'] = '#3e3e3e';
		$dark['tabs_bg_color'] = '#3e3e3e';
		$dark['tabs_inactive_color'] = '#313132';
		$dark['tagline_bg'] = '#3e3e3e';
		$dark['tagline_border_color'] = '#3e3e3e';
		$dark['timeline_bg_color'] = 'transparent';
		$dark['timeline_color'] = '#3e3e3e';
		$dark['woo_cart_bg_color'] = '#333333';
		$dark['qty_bg_color'] = '#29292a';
		$dark['qty_bg_hover_color'] = '#383838';
		$dark['bbp_forum_header_bg'] = '#383838';
		$dark['bbp_forum_border_color'] = '#212121';
		$dark['checklist_icons_color'] = '#ffffff';
		$dark['flip_boxes_front_bg'] = '#3e3e3e';
		$dark['flip_boxes_front_heading'] = '#ffffff';
		$dark['flip_boxes_front_text'] = '#aaa9a9';
		$dark['full_width_bg_color'] = '#242424';
		$dark['full_width_border_color'] = '#3e3e3e';
		$dark['modal_bg_color'] = '#29292a';
		$dark['modal_border_color'] = '#242424';
		$dark['person_border_color'] = '#494848';
		$dark['popover_heading_bg_color'] = '#29292a';
		$dark['popover_content_bg_color'] = '#3e3e3e';
		$dark['popover_border_color'] = '#242424';
		$dark['popover_text_color'] = '#ffffff';
		$dark['progressbar_unfilled_color'] = '#3e3e3e';
		$dark['section_sep_bg'] = '#3e3e3e';
		$dark['section_sep_border_color'] = '#3e3e3e';
		$dark['sharing_box_tagline_text_color'] = '#ffffff';
		$dark['header_social_links_icon_color'] = '#545455';
		$dark['header_social_links_box_color'] = '#383838';
		$dark['bg_color'] = '#1e1e1e';
		$dark['mobile_menu_background_color'] = '#3e3e3e';
		$dark['mobile_menu_border_color'] = '#212122';
		$dark['mobile_menu_hover_color'] = '#383737';
		$dark['social_links_icon_color'] = '#3e3e3e';
		$dark['social_links_box_color'] = '#383838';
		$dark['sharing_social_links_icon_color'] = '#919191';
		$dark['sharing_social_links_box_color'] = '#4b4e4f';
		$dark['load_more_posts_button_bg_color'] = '#3e3e3e';
		$dark['ec_bar_bg_color'] = '#353535';
		$dark['flyout_menu_icon_color'] = '#ffffff';
		$dark['flyout_menu_background_color'] = 'rgba(0,0,0,0.85)';
		$dark['ec_sidebar_bg_color'] = '#f6f6f6';
		$dark['ec_sidebar_link_color'] = '#ffffff';

		$green = array();
		$green['primary_color'] = '#a0ce4e';
		$green['pricing_box_color'] = '#92C563';
		$green['image_gradient_top_color'] = '#D1E990';
		$green['image_gradient_bottom_color'] = '#AAD75B';
		$green['button_gradient_top_color'] = '#D1E990';
		$green['button_gradient_bottom_color'] = '#AAD75B';
		$green['button_gradient_top_color_hover'] = '#AAD75B';
		$green['button_gradient_bottom_color_hover'] = '#D1E990';
		$green['button_accent_color'] = '#6e9a1f';
		$green['button_accent_hover_color'] = '#638e1a';
		$green['button_bevel_color'] = '#54770f';
		$green['checklist_circle_color'] = '#a0ce4e';
		$green['counter_box_color'] = '#a0ce4e';
		$green['countdown_background_color'] = '#a0ce4e';
		$green['dropcap_color'] = '#a0ce4e';
		$green['flip_boxes_back_bg'] = '#a0ce4e';
		$green['progressbar_filled_color'] = '#a0ce4e';
		$green['counter_filled_color'] = '#a0ce4e';
		$green['ec_sidebar_widget_bg_color'] = '#a0ce4e';
		$green['menu_hover_first_color'] = '#a0ce4e';
		$green['header_top_bg_color'] = '#a0ce4e';
		$green['content_box_hover_animation_accent_color'] = '#a0ce4e';
		$green['map_overlay_color'] = '#a0ce4e';
		$green['flyout_menu_icon_hover_color'] = '#a0ce4e';

		$darkgreen = array();
		$darkgreen['primary_color'] = '#9db668';
		$darkgreen['pricing_box_color'] = '#a5c462';
		$darkgreen['image_gradient_top_color'] = '#cce890';
		$darkgreen['image_gradient_bottom_color'] = '#afd65a';
		$darkgreen['button_gradient_top_color'] = '#cce890';
		$darkgreen['button_gradient_bottom_color'] = '#AAD75B';
		$darkgreen['button_gradient_top_color_hover'] = '#AAD75B';
		$darkgreen['button_gradient_bottom_color_hover'] = '#cce890';
		$darkgreen['button_accent_color'] = '#577810';
		$darkgreen['button_accent_hover_color'] = '#577810';
		$darkgreen['button_bevel_color'] = '#577810';
		$darkgreen['checklist_circle_color'] = '#9db668';
		$darkgreen['counter_box_color'] = '#9db668';
		$darkgreen['countdown_background_color'] = '#9db668';
		$darkgreen['dropcap_color'] = '#9db668';
		$darkgreen['flip_boxes_back_bg'] = '#9db668';
		$darkgreen['progressbar_filled_color'] = '#9db668';
		$darkgreen['counter_filled_color'] = '#9db668';
		$darkgreen['ec_sidebar_widget_bg_color'] = '#9db668';
		$darkgreen['menu_hover_first_color'] = '#9db668';
		$darkgreen['header_top_bg_color'] = '#9db668';
		$darkgreen['content_box_hover_animation_accent_color'] = '#9db668';
		$darkgreen['map_overlay_color'] = '#9db668';
		$darkgreen['flyout_menu_icon_hover_color'] = '#9db668';

		$orange = array();
		$orange['primary_color'] = '#e9a825';
		$orange['pricing_box_color'] = '#c4a362';
		$orange['image_gradient_top_color'] = '#e8cb90';
		$orange['image_gradient_bottom_color'] = '#d6ad5a';
		$orange['button_gradient_top_color'] = '#e8cb90';
		$orange['button_gradient_bottom_color'] = '#d6ad5a';
		$orange['button_gradient_top_color_hover'] = '#d6ad5a';
		$orange['button_gradient_bottom_color_hover'] = '#e8cb90';
		$orange['button_accent_color'] = '#785510';
		$orange['button_accent_hover_color'] = '#785510';
		$orange['button_bevel_color'] = '#785510';
		$orange['checklist_circle_color'] = '#e9a825';
		$orange['counter_box_color'] = '#e9a825';
		$orange['countdown_background_color'] = '#e9a825';
		$orange['dropcap_color'] = '#e9a825';
		$orange['flip_boxes_back_bg'] = '#e9a825';
		$orange['progressbar_filled_color'] = '#e9a825';
		$orange['counter_filled_color'] = '#e9a825';
		$orange['ec_sidebar_widget_bg_color'] = '#e9a825';
		$orange['menu_hover_first_color'] = '#e9a825';
		$orange['header_top_bg_color'] = '#e9a825';
		$orange['content_box_hover_animation_accent_color'] = '#e9a825';
		$orange['map_overlay_color'] = '#e9a825';
		$orange['flyout_menu_icon_hover_color'] = '#e9a825';

		$lightblue = array();
		$lightblue['primary_color'] = '#67b7e1';
		$lightblue['pricing_box_color'] = '#62a2c4';
		$lightblue['image_gradient_top_color'] = '#90c9e8';
		$lightblue['image_gradient_bottom_color'] = '#5aabd6';
		$lightblue['button_gradient_top_color'] = '#90c9e8';
		$lightblue['button_gradient_bottom_color'] = '#5aabd6';
		$lightblue['button_gradient_top_color_hover'] = '#5aabd6';
		$lightblue['button_gradient_bottom_color_hover'] = '#90c9e8';
		$lightblue['button_accent_color'] = '#105378';
		$lightblue['button_accent_hover_color'] = '#105378';
		$lightblue['button_bevel_color'] = '#105378';
		$lightblue['checklist_circle_color'] = '#67b7e1';
		$lightblue['counter_box_color'] = '#67b7e1';
		$lightblue['countdown_background_color'] = '#67b7e1';
		$lightblue['dropcap_color'] = '#67b7e1';
		$lightblue['flip_boxes_back_bg'] = '#67b7e1';
		$lightblue['progressbar_filled_color'] = '#67b7e1';
		$lightblue['counter_filled_color'] = '#67b7e1';
		$lightblue['ec_sidebar_widget_bg_color'] = '#67b7e1';
		$lightblue['menu_hover_first_color'] = '#67b7e1';
		$lightblue['header_top_bg_color'] = '#67b7e1';
		$lightblue['content_box_hover_animation_accent_color'] = '#67b7e1';
		$lightblue['map_overlay_color'] = '#67b7e1';
		$lightblue['flyout_menu_icon_hover_color'] = '#67b7e1';

		$lightred = array();
		$lightred['primary_color'] = '#f05858';
		$lightred['pricing_box_color'] = '#c46262';
		$lightred['image_gradient_top_color'] = '#e89090';
		$lightred['image_gradient_bottom_color'] = '#d65a5a';
		$lightred['button_gradient_top_color'] = '#e89090';
		$lightred['button_gradient_bottom_color'] = '#d65a5a';
		$lightred['button_gradient_top_color_hover'] = '#d65a5a';
		$lightred['button_gradient_bottom_color_hover'] = '#e89090';
		$lightred['button_accent_color'] = '#781010';
		$lightred['button_accent_hover_color'] = '#781010';
		$lightred['button_bevel_color'] = '#781010';
		$lightred['checklist_circle_color'] = '#f05858';
		$lightred['counter_box_color'] = '#f05858';
		$lightred['countdown_background_color'] = '#f05858';
		$lightred['dropcap_color'] = '#f05858';
		$lightred['flip_boxes_back_bg'] = '#f05858';
		$lightred['progressbar_filled_color'] = '#f05858';
		$lightred['counter_filled_color'] = '#f05858';
		$lightred['ec_sidebar_widget_bg_color'] = '#f05858';
		$lightred['menu_hover_first_color'] = '#f05858';
		$lightred['header_top_bg_color'] = '#f05858';
		$lightred['content_box_hover_animation_accent_color'] = '#f05858';
		$lightred['map_overlay_color'] = '#f05858';
		$lightred['flyout_menu_icon_hover_color'] = '#f05858';

		$pink = array();
		$pink['primary_color'] = '#e67fb9';
		$pink['pricing_box_color'] = '#c46299';
		$pink['image_gradient_top_color'] = '#e890c2';
		$pink['image_gradient_bottom_color'] = '#d65aa0';
		$pink['button_gradient_top_color'] = '#e890c2';
		$pink['button_gradient_bottom_color'] = '#d65aa0';
		$pink['button_gradient_top_color_hover'] = '#d65aa0';
		$pink['button_gradient_bottom_color_hover'] = '#e890c2';
		$pink['button_accent_color'] = '#78104b';
		$pink['button_accent_hover_color'] = '#78104b';
		$pink['button_bevel_color'] = '#78104b';
		$pink['checklist_circle_color'] = '#e67fb9';
		$pink['counter_box_color'] = '#e67fb9';
		$pink['countdown_background_color'] = '#e67fb9';
		$pink['dropcap_color'] = '#e67fb9';
		$pink['flip_boxes_back_bg'] = '#e67fb9';
		$pink['progressbar_filled_color'] = '#e67fb9';
		$pink['counter_filled_color'] = '#e67fb9';
		$pink['ec_sidebar_widget_bg_color'] = '#e67fb9';
		$pink['menu_hover_first_color'] = '#e67fb9';
		$pink['header_top_bg_color'] = '#e67fb9';
		$pink['content_box_hover_animation_accent_color'] = '#e67fb9';
		$pink['map_overlay_color'] = '#e67fb9';
		$pink['flyout_menu_icon_hover_color'] = '#e67fb9';

		$lightgrey = array();
		$lightgrey['primary_color'] = '#9e9e9e';
		$lightgrey['pricing_box_color'] = '#c4c4c4';
		$lightgrey['image_gradient_top_color'] = '#e8e8e8';
		$lightgrey['image_gradient_bottom_color'] = '#d6d6d6';
		$lightgrey['button_gradient_top_color'] = '#e8e8e8';
		$lightgrey['button_gradient_bottom_color'] = '#d6d6d6';
		$lightgrey['button_gradient_top_color_hover'] = '#d6d6d6';
		$lightgrey['button_gradient_bottom_color_hover'] = '#e8e8e8';
		$lightgrey['button_accent_color'] = '#787878';
		$lightgrey['button_accent_hover_color'] = '#787878';
		$lightgrey['button_bevel_color'] = '#787878';
		$lightgrey['checklist_circle_color'] = '#9e9e9e';
		$lightgrey['counter_box_color'] = '#9e9e9e';
		$lightgrey['countdown_background_color'] = '#9e9e9e';
		$lightgrey['dropcap_color'] = '#9e9e9e';
		$lightgrey['flip_boxes_back_bg'] = '#9e9e9e';
		$lightgrey['progressbar_filled_color'] = '#9e9e9e';
		$lightgrey['counter_filled_color'] = '#9e9e9e';
		$lightgrey['ec_sidebar_widget_bg_color'] = '#9e9e9e';
		$lightgrey['menu_hover_first_color'] = '#9e9e9e';
		$lightgrey['header_top_bg_color'] = '#9e9e9e';
		$lightgrey['content_box_hover_animation_accent_color'] = '#9e9e9e';
		$lightgrey['map_overlay_color'] = '#9e9e9e';
		$lightgrey['flyout_menu_icon_hover_color'] = '#9e9e9e';

		$brown = array();
		$brown['primary_color'] = '#ab8b65';
		$brown['pricing_box_color'] = '#c49862';
		$brown['image_gradient_top_color'] = '#e8c090';
		$brown['image_gradient_bottom_color'] = '#d69e5a';
		$brown['button_gradient_top_color'] = '#e8c090';
		$brown['button_gradient_bottom_color'] = '#d69e5a';
		$brown['button_gradient_top_color_hover'] = '#d69e5a';
		$brown['button_gradient_bottom_color_hover'] = '#e8c090';
		$brown['button_accent_color'] = '#784910';
		$brown['button_accent_hover_color'] = '#784910';
		$brown['button_bevel_color'] = '#784910';
		$brown['checklist_circle_color'] = '#ab8b65';
		$brown['counter_box_color'] = '#ab8b65';
		$brown['countdown_background_color'] = '#ab8b65';
		$brown['dropcap_color'] = '#ab8b65';
		$brown['flip_boxes_back_bg'] = '#ab8b65';
		$brown['progressbar_filled_color'] = '#ab8b65';
		$brown['ec_sidebar_widget_bg_color'] = '#ab8b65';
		$brown['menu_hover_first_color'] = '#ab8b65';
		$brown['header_top_bg_color'] = '#ab8b65';
		$brown['content_box_hover_animation_accent_color'] = '#ab8b65';
		$brown['map_overlay_color'] = '#ab8b65';
		$brown['flyout_menu_icon_hover_color'] = '#ab8b65';

		$red = array();
		$red['primary_color'] = '#e10707';
		$red['pricing_box_color'] = '#c40606';
		$red['image_gradient_top_color'] = '#e80707';
		$red['image_gradient_bottom_color'] = '#d60707';
		$red['button_gradient_top_color'] = '#e80707';
		$red['button_gradient_bottom_color'] = '#d60707';
		$red['button_gradient_top_color_hover'] = '#d60707';
		$red['button_gradient_bottom_color_hover'] = '#e80707';
		$red['button_accent_color'] = '#780404';
		$red['button_accent_hover_color'] = '#780404';
		$red['button_bevel_color'] = '#780404';
		$red['checklist_circle_color'] = '#e10707';
		$red['counter_box_color'] = '#e10707';
		$red['countdown_background_color'] = '#e10707';
		$red['dropcap_color'] = '#e10707';
		$red['flip_boxes_back_bg'] = '#e10707';
		$red['progressbar_filled_color'] = '#e10707';
		$red['counter_filled_color'] = '#e10707';
		$red['ec_sidebar_widget_bg_color'] = '#e10707';
		$red['menu_hover_first_color'] = '#e10707';
		$red['header_top_bg_color'] = '#e10707';
		$red['content_box_hover_animation_accent_color'] = '#e10707';
		$red['map_overlay_color'] = '#e10707';
		$red['flyout_menu_icon_hover_color'] = '#e10707';

		$blue = array();
		$blue['primary_color'] = '#1a80b6';
		$blue['pricing_box_color'] = '#62a2c4';
		$blue['image_gradient_top_color'] = '#90c9e8';
		$blue['image_gradient_bottom_color'] = '#5aabd6';
		$blue['button_gradient_top_color'] = '#90c9e8';
		$blue['button_gradient_bottom_color'] = '#5aabd6';
		$blue['button_gradient_top_color_hover'] = '#5aabd6';
		$blue['button_gradient_bottom_color_hover'] = '#90c9e8';
		$blue['button_accent_color'] = '#105378';
		$blue['button_accent_hover_color'] = '#105378';
		$blue['button_bevel_color'] = '#105378';
		$blue['checklist_circle_color'] = '#1a80b6';
		$blue['counter_box_color'] = '#1a80b6';
		$blue['countdown_background_color'] = '#1a80b6';
		$blue['dropcap_color'] = '#1a80b6';
		$blue['flip_boxes_back_bg'] = '#1a80b6';
		$blue['progressbar_filled_color'] = '#1a80b6';
		$blue['counter_filled_color'] = '#1a80b6';
		$blue['ec_sidebar_widget_bg_color'] = '#1a80b6';
		$blue['menu_hover_first_color'] = '#1a80b6';
		$blue['header_top_bg_color'] = '#1a80b6';
		$blue['content_box_hover_animation_accent_color'] = '#1a80b6';
		$blue['map_overlay_color'] = '#1a80b6';
		$blue['flyout_menu_icon_hover_color'] = '#1a80b6';

		if ( isset( $$context ) ) {
			return $$context;
		}
		return array();
	}
}

/* Omit closing PHP tag to avoid 'Headers already sent' issues. */
