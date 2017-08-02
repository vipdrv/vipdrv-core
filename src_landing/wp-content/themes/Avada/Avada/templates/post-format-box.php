<?php
/**
 * Post-format template.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php
switch ( get_post_format() ) {
	case 'gallery':
		$format_class = 'images';
		break;
	case 'link':
		$format_class = 'link';
		break;
	case 'image':
		$format_class = 'image';
		break;
	case 'quote':
		$format_class = 'quotes-left';
		break;
	case 'video':
		$format_class = 'film';
		break;
	case 'audio':
		$format_class = 'headphones';
		break;
	case 'chat':
		$format_class = 'bubbles';
		break;
	default:
		$format_class = 'pen';
		break;
}
?>
<div class="fusion-format-box">
	<i class="fusion-icon-<?php echo esc_attr( $format_class ); ?>"></i>
</div>
