<?php
/**
 * Core
 *
 * JWT Loader.
 *
 * @category   Common, Core, UI
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Base;

use MoJWT\Base\InstanceHelper;
use MoJWT\MJUtils;
/**
 * Class to save Load and Render REST API UI
 *
 * @category Common, Core
 * @package  MoJWT\Standard
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Loader {

	/**
	 * Instance Helper
	 *
	 * @var \MoJWT\Base\InstanceHelper $instance_helper
	 * */
	private $instance_helper;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_script' ) );

		$this->instance_helper = new InstanceHelper();
		$this->instance_helper->get_license_library_instance();
	}

	/**
	 * Function to enqueue CSS
	 */
	public function plugin_settings_style() {
		wp_enqueue_style( 'mo_jwt_admin_settings_style', MJ_URL . 'resources/css/style_settings.min.css', array(), $ver = null, $in_footer = false );
		wp_enqueue_style( 'mo_jwt_admin_settings_phone_style', MJ_URL . 'resources/css/phone.min.css', array(), $ver    = null, $in_footer = false );
		if ( isset( $_GET['tab'] ) && 'license' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			wp_enqueue_style( 'mo-jwt_license', MJ_URL . 'resources/css/bootstrap/bootstrap.min.css', array(), $ver = null, $in_footer = false );
		}
	}

	/**
	 * Function to enqueue JS
	 */
	public function plugin_settings_script() {
		wp_enqueue_script( 'mo_jwt_admin_settings_script', MJ_URL . 'resources/js/settings.min.js', array(), $ver = null, $in_footer = false );
		wp_enqueue_script( 'mo_jwt_admin_settings_phone_script', MJ_URL . 'resources/js/phone.min.js', array(), $ver = null, $in_footer = false );
	}

	/**
	 * Function to load appropriate view
	 *
	 * @param string $currenttab Tab to load and render view for.
	 *
	 * @return void
	 */
	public function load_current_tab( $currenttab ) {
		global $mj_util;

		$to_load  = 0 === $mj_util->get_versi();
		$accounts = $this->instance_helper->get_accounts_instance();
		if ( class_exists( 'MoJWT\Premium\MJUtils' ) && ! $to_load && ! $mj_util->mo_jwt_is_clv() ) {
			if ( ! $mj_util->mo_jwt_customer_registered() ) {
				$accounts->verify_password_ui();
			} elseif ( class_exists( 'MoJWT\Premium\MJUtils' ) && ! $mj_util->mo_jwt_is_clv() && $mj_util->check_versi( 1 ) ) {
				$accounts->mo_jwt_lp();
			}
		} else {
			if ( 'account' === $currenttab ) {
				$accounts->register();
			} elseif ( 'config' === $currenttab || '' === $currenttab ) {
				$this->instance_helper->get_config_instance()->render_ui();
			} elseif ( 'trial' === $currenttab || '' === $currenttab ) {
				$this->instance_helper->get_trial_instance()->render_ui();
			} elseif ( 'license' === $currenttab ) {
				$licensing = $this->instance_helper->get_licensing_instance();
				$licensing->show_licensing_page();
			}
		}
	}
}
