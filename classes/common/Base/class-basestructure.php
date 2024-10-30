<?php
/**
 * Plugin UI Base Structure
 *
 * JWT Login Config guides.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Base;

use MoJWT\Support;
require_once 'class-loader.php';

/**
 * Class to render Basic Structure of plugin UI.
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class BaseStructure {

	/**
	 * Loader instance
	 *
	 * @var MoJWT\Base\Loader
	 */
	private $loader;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		$this->loader = new Loader();
	}

	/**
	 * Function to add Plugin to menu list.
	 */
	public function admin_menu() {
		$page = add_menu_page( 'JWT Login Settings ' . __( 'Configure JWT', 'mo_jwt_settings' ), 'miniOrange JWT Login', 'administrator', 'mo_jwt_settings', array( $this, 'menu_options' ), MJ_URL . 'resources/images/miniorange.png' );
	}

	/**
	 * Render Skeleton.
	 */
	public function menu_options() {
		global $mj_util;
		$mj_util->mo_jwt_update_option( 'mo_jwt_host_name', 'https://login.xecurify.com' );
		$currenttab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ): ''; // phpcs:ignore -- WordPress.Security.NonceVerification.Recommended Ignoring nonce verification because we are fetching data from URL and not on form submission.
		?>
		<div id="mo_jwt_authentication_settings">
			<div id='mrablock' class='mjwt-overlay dashboard'></div>
			<div class="miniorange_container">
				<?php
					$this->content_navbar( $currenttab );
				?>
				<table style="width:100%;">
					<tr>
						<td style="vertical-align:top;width:70%;">
							<?php
								$this->loader->load_current_tab( $currenttab );
							?>
						</td>
						<td style="vertical-align:top;padding-left:0.1%;">
						<?php
							$support = new Support();
							$support->support();
						?>
						</td>
					</tr>
				</table>
			</div>

		</div>
		<?php
	}

	/**
	 * Function to render tabs.
	 *
	 * @param string $currenttab Current active tab.
	 */
	public function content_navbar( $currenttab ) {
		global $mj_util;
		?>
		<div class="wrap">
			<div class="header-warp">
				<h1 style="font-weight: 700">miniOrange JWT Login (Single Sign On) &nbsp;
					<a class="add-new-h2" href="https://wordpress.org/support/plugin/login-register-using-jwt/" target="_blank" rel="noopener">WordPress Forum</a>
				</h1>
				<div><img style="float:left;" src="<?php echo esc_url( MJ_URL . '/resources/images/logo.png' ); ?>"></div>
		</div>
		<div id="tab">
		<h2 class="nav-tab-wrapper">
			<a id="tab-config" class="nav-tab <?php echo ( 'config' === $currenttab || '' === $currenttab ) ? 'mo-jwt-nav-tab-active' : ''; ?>" href="admin.php?page=mo_jwt_settings&tab=config">Configure JWT Settings</a>
			<?php if ( 'mo_jwt_login_standard' === MJ_VERSION ) : ?>
				<a id="auto_trial_button_id" class="nav-tab <?php echo ( 'trial' === $currenttab ) ? 'mo-jwt-nav-tab-active' : ''; ?>" href="admin.php?page=mo_jwt_settings&tab=trial">Premium Trial</a>
			<?php endif ?>
			<a id="acc_setup_button_id" class="nav-tab <?php echo ( 'account' === $currenttab ) ? 'mo-jwt-nav-tab-active' : ''; ?>" href="admin.php?page=mo_jwt_settings&tab=account">Account Setup</a>
			<a id="license_button_id" class="nav-tab <?php echo ( 'license' === $currenttab ) ? 'mo-jwt-nav-tab-active' : ''; ?>" href="admin.php?page=mo_jwt_settings&tab=license">Premium Plans</a>
		</h2>
		<div class="mo_jwt_configuration_btn">
			<button class="mo_jwt_setup_guide">
				<a href="https://www.youtube.com/playlist?list=PL2vweZ-PcNpevdcrVhs_dQ3qOxc0102wI" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url( plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/resources/images/icons/youtube.png' ); ?>" height="25px" width="25px"> Use Case Videos
				</a>
			</button>
			<button class="mo_jwt_setup_guide">
				<a href="https://plugins.miniorange.com/wordpress-single-sign-on-using-jwt-token" target="_blank">
					<img src="<?php echo esc_url( plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/resources/images/icons/user-guide.png' ); ?>" height="25px" width="25px"> Setup Guide
				</a>
			</button>
			<button class="mo_jwt_setup_guide">
				<a href="https://plugins.miniorange.com/wordpress-login-using-jwt-single-sign-on-sso" target="_blank">
					<img src="<?php echo esc_url( plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/resources/images/icons/document.png' ); ?>" alt=" Image" height="25px" width="25px"> Learn More
				</a>
			</button>
		</div>
		</div>
		<?php
	}
}
