<?php
/**
 * App
 *
 * JWT Settings Controller.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Free;

use MoJWT\Settings;
use MoJWT\Customer;

/**
 * Class for Free JWT Settings
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class FreeSettings {

	/**
	 * JWT Common Settings
	 *
	 * @var \MoJWT\Settings $common_settings
	 * */
	private $common_settings;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {
		$this->common_settings = new Settings();
		global $mj_util;
		if ( ! $mj_util->get_versi( 1 ) ) {
			add_action( 'admin_init', array( $this, 'mo_jwt_free_settings' ) );
			add_action( 'admin_footer', array( $this, 'mo_jwt_feedback_request' ) );
		}
	}

	/**
	 * Function to Save All Sorts of settings
	 *
	 * @return void
	 **/
	public function mo_jwt_free_settings() {
		global $mj_util;
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'POST' && current_user_can( 'administrator' ) ) {
			if ( isset( $_POST['mo_jwt_feedback_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_feedback_nonce'] ) ), 'mo_jwt_feedback' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_feedback' === sanitize_text_field( sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) ) {
				if ( isset( $_POST['miniorange_jwt_feedback_skip'] ) && 'Skip' === sanitize_text_field( sanitize_text_field( wp_unslash( $_POST['miniorange_jwt_feedback_skip'] ) ) ) ) {
					deactivate_plugins( MJ_DIR . 'miniorange-jwt-login-settings.php' );
					$mj_util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Plugin Deactivated Successfully.' );
					$mj_util->mo_jwt_show_success_message();
				} else {
					$user                      = wp_get_current_user();
					$message                   = 'Plugin Deactivated:';
					$deactivate_reason         = isset( $_POST['mo_jwt_deactivate_reason_radio'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_jwt_deactivate_reason_radio'] ) ) : false;
					$deactivate_reason_message = isset( $_POST['mo_jwt_query_feedback'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_jwt_query_feedback'] ) ) : false;
					if ( ! $deactivate_reason ) {
						$mj_util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Please Select one of the reasons ,if your reason is not mentioned please select Other Reasons' );
						$mj_util->mo_jwt_show_error_message();
					}
					$message .= $deactivate_reason;
					if ( isset( $deactivate_reason_message ) ) {
						$message .= ':' . $deactivate_reason_message;
					}
					$email = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_email' );
					if ( '' === $email ) {
						$email = $user->user_email;
					}
					$phone = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_phone' );
					// only reason.
					$feedback_reasons = new Customer();
					$submited         = json_decode( $feedback_reasons->mo_jwt_send_email_alert( $email, $phone, $message ), true );
					deactivate_plugins( MJ_DIR . 'miniorange-jwt-login-settings.php' );
					$mj_util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Thank you for the feedback.' );
					$mj_util->mo_jwt_show_success_message();
				}
			}
			if ( isset( $_POST['mo_jwt_skip_feedback_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_skip_feedback_nonce'] ) ), 'mo_jwt_skip_feedback' ) && isset( $_POST['option'] ) && 'mo_jwt_skip_feedback' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				deactivate_plugins( MJ_DIR . 'miniorange-jwt-login-settings.php' );
				$mj_util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Plugin Deactivated Successfully.' );
				$mj_util->mo_jwt_show_success_message();
			}
		}
	}

	/**
	 * Feedback form
	 */
	public function mo_jwt_feedback_request() {
		$feedback = new \MoJWT\Free\Feedback();
		$feedback->show_form();
	}
}
