<?php
/**
 * Maintenance page.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       http://theme-fusion.com
 * @package    Avada
 * @subpackage Core
 * @since      4.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Maintenance page.
 */
class Avada_Maintenance {

	/**
	 * Determines if we should activate the maintenance mode or not.
	 *
	 * @access private
	 * @var bool
	 */
	private $maintenance = false;

	/**
	 * The message that will be displayed to all non-admins.
	 * This will be displayed on the frontend instead of the normal site.
	 *
	 * @access private
	 * @var string
	 */
	private $users_warning = '';

	/**
	 * Same as $users_warning but for admins.
	 *
	 * @access private
	 * @var string
	 */
	private $admin_warning = '';

	/**
	 * Constructor.
	 *
	 * @access public
	 * @param bool   $maintenance     Maintenance on/off.
	 * @param string $users_warning The warning to show to users.
	 * @param string $admin_warning The warning to show to admins.
	 */
	public function __construct( $maintenance = false, $users_warning = '', $admin_warning = '' ) {

		// No need to do anything if we're not in maintenance mode.
		if ( true !== $maintenance ) {
			return;
		}

		// Only continue if we're on the frontend.
		if ( is_admin() ) {
			return;
		}

		$this->maintenance   = $maintenance;
		$this->users_warning = $users_warning;
		$this->admin_warning = $admin_warning;

		if ( is_admin() || ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) ) {
			return;
		}

		$this->maintenance_page();

	}

	/**
	 * Displays the maintenance page.
	 *
	 * @access public
	 */
	public function maintenance_page() {
		?>
		<div class="wrapper" style="width:800px;max-width:95%;background:#f7f7f7;border:1px solid #f2f2f2;border-radius:3px;margin:auto;margin-top:200px;">
			<div class="inner" style="padding:2rem;font-size:1.2rem;color:#333;">
				<?php if ( current_user_can( 'install_plugins' ) ) : // Current user is an admin. ?>
					<p><?php echo $this->admin_warning; // WPCS: XSS ok. ?></p>
				<?php else : ?>
					<p><?php echo $this->users_warning; // WPCS: XSS ok. ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		exit;

	}
}
