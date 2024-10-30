<?php
/**
 * App
 *
 * JWT Flow Handler.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

use MoJWT\Base\InstanceHelper;
use MoJWT\Methods\JWTLogin;
use MoJWT\Methods\JWTCreate;
use MoJWT\Methods\JWTRegister;
use MoJWT\Methods\JWTDelete;

/**
 * App
 *
 * JWT Login Handler.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */
class JWTFlowHandler {


	/**
	 * Current method slug.
	 *
	 * @var string
	 */
	private $method_slug;
	/**
	 * Current config of all methods.
	 *
	 * @var array
	 */
	private $current_config;
	/**
	 * Current method config.
	 *
	 * @var array
	 */
	private $method_config;
	/**
	 * Custom token name.
	 *
	 * @var string
	 */
	private $jwt_token_name;

	/**
	 * Constructor
	 */
	public function __construct() {

		global $mj_util;
		add_action( 'init', array( $this, 'mo_jwt_initalize_flow' ), 1, 1 );
		add_action( 'rest_api_init', array( $this, 'mo_jwt_initialize_rest_flow' ), 10, 1 );
	}

	/**
	 * Function to fetch JWT from URL
	 */
	public function mo_jwt_initalize_flow() {
		global $mj_util;
		$this->method_slug    = 'jwtlogin';
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config  = ( $this->current_config && $this->current_config[ $this->method_slug ] ) ? ( $this->current_config[ $this->method_slug ] ) : false;
		$this->jwt_token_name = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token';

		$current_config                     = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$login_config                       = $current_config && isset( $current_config['jwtlogin'] ) ? $current_config['jwtlogin'] : false;
		$mo_jwt_enable_auth_on_all_requests = $login_config && isset( $login_config['mo_jwt_enable_auth_on_all_requests'] ) ? $login_config['mo_jwt_enable_auth_on_all_requests'] : false;
		$mo_jwt_enable_audit_logs           = $login_config && isset( $login_config['mo_jwt_enable_audit_logs'] ) ? $login_config['mo_jwt_enable_audit_logs'] : 'off';
		$get_jwt_from_url                   = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_url'] ) ? $this->method_config['mo_jwt_get_token_from_url'] : 'on';
		$get_jwt_from_cookie                = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_cookie'] ) ? $this->method_config['mo_jwt_get_token_from_cookie'] : false;
		$get_jwt_from_header                = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_header'] ) ? $this->method_config['mo_jwt_get_token_from_header'] : false;

		$this->mo_jwt_token_extraction( $mo_jwt_enable_audit_logs, $get_jwt_from_url, $get_jwt_from_cookie, $get_jwt_from_header );

	}

	/**
	 * Function to extract token.
	 *
	 * @param mixed $mo_jwt_enable_audit_logs Audit log flag.
	 * @param mixed $get_jwt_from_url         JWT token from URL switch.
	 * @param mixed $get_jwt_from_cookie      JWT token from cookie switch.
	 * @param mixed $get_jwt_from_header      JWT token from header switch.
	 *
	 * @return void
	 */
	public function mo_jwt_token_extraction( $mo_jwt_enable_audit_logs, $get_jwt_from_url, $get_jwt_from_cookie, $get_jwt_from_header ) {

		if ( 'off' === $get_jwt_from_url && 'off' === $get_jwt_from_cookie && 'off' === $get_jwt_from_header ) {
			return;
		}

		if ( 'on' === $get_jwt_from_url && isset( $_GET[ $this->jwt_token_name ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			$mo_jwt_token = sanitize_text_field( wp_unslash( $_GET[ $this->jwt_token_name ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			if ( ! is_user_logged_in() ) {
				$this->mo_jwt_login_exc( $mo_jwt_token );
			} else {
				$userid = get_current_user_id();
				if ( ! empty( $mo_jwt_enable_audit_logs ) && 'on' === $mo_jwt_enable_audit_logs ) {
					$server_val = isset( $_SERVER['HTTP_SEC_CH_UA'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_SEC_CH_UA'] ) ) : '';
					do_action( 'mo_jwt_sso_audit', $userid, '', $server_val, $mo_jwt_token );
				}
			}
		} elseif ( 'on' === $get_jwt_from_cookie && isset( $_COOKIE[ $this->jwt_token_name ] ) ) {
			$mo_jwt_token = sanitize_text_field( wp_unslash( $_COOKIE[ $this->jwt_token_name ] ) );
			if ( ! is_user_logged_in() ) {
				$this->mo_jwt_login_exc( $mo_jwt_token );
			} else {
				$userid = get_current_user_id();
				if ( ! empty( $mo_jwt_enable_audit_logs ) && 'on' === $mo_jwt_enable_audit_logs ) {
					$server_val = isset( $_SERVER['HTTP_SEC_CH_UA'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_SEC_CH_UA'] ) ) : '';
					do_action( 'mo_jwt_sso_audit', $userid, '', $server_val, $mo_jwt_token );
				}
				if ( isset( $_COOKIE[ $this->jwt_token_name ] ) ) {
					setcookie( $this->jwt_token_name, 'invalid', time() - 60, $path = '/' );
				}
			}
		} elseif ( 'on' === $get_jwt_from_header ) {
			$headers = $this->mo_jwt_fetch_headers();
			if ( isset( $headers[ $this->jwt_token_name ] ) || isset( $headers[ strtoupper( $this->jwt_token_name ) ] ) ) {
				$mo_jwt_token = isset( $headers[ $this->jwt_token_name ] ) ? $headers[ $this->jwt_token_name ] : $headers[ strtoupper( $this->jwt_token_name ) ];
				if ( ! is_user_logged_in() ) {
					$this->mo_jwt_login_exc( $mo_jwt_token );
				} else {
					$userid = get_current_user_id();
					if ( ! empty( $mo_jwt_enable_audit_logs ) && 'on' === $mo_jwt_enable_audit_logs ) {
						$server_val = isset( $_SERVER['HTTP_SEC_CH_UA'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_SEC_CH_UA'] ) ) : '';
						do_action( 'mo_jwt_sso_audit', $userid, '', $server_val, $mo_jwt_token );
					}
				}
			}
		}

	}
	/**
	 * Execute jwt login
	 *
	 * @param mixed $token Token for jwt login.
	 *
	 * @return void
	 */
	public function mo_jwt_login_exc( $token ) {
		$mo_jwt_login = new JWTLogin( $token );
		$mo_jwt_login->perform_jwt_login( $token );
	}

	/**
	 * Fetch Request Headers
	 */
	public function mo_jwt_fetch_headers() {
		$headers = array();
		global $mj_util;
		$server = $mj_util->mo_array_sanitize( $_SERVER );
		foreach ( $server as $name => $value ) {
			if ( substr( $name, 0, 5 ) === 'HTTP_' ) {
				$headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) ) ] = $value;
			}
		}

		$headers = array_change_key_case( $headers, CASE_UPPER );

		return $headers;

	}

	/**
	 * REST API Flow initiate for JWT Flow
	 */
	public function mo_jwt_initialize_rest_flow() {

		global $mj_util, $mo_jwt_license_subscription_namespace;

		// Handle Token Response.
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			if ( strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/api/v1/mo-jwt-register' ) !== false ) {
				if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
					$mj_util->mo_jwt_send_exp_response();
				}
				$json = file_get_contents( 'php://input' );

				$json = json_decode( $json, true );

				if ( json_last_error() !== JSON_ERROR_NONE ) {
                    $json = $_POST; // phpcs:ignore -- WordPress.Security.NonceVerification.Recommended Ignoring nonce verification because we are fetching data from URL and not on form submission.
				}
				$mo_jwt_register = new JWTRegister();
				$response        = $mo_jwt_register->create_user_with_jwt( $json );

				$mj_util->send_json_response( $response );
			} elseif ( strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/api/v1/mo-jwt-delete' ) !== false ) {
				if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
					$mj_util->mo_jwt_send_exp_response();
				}
				$json = file_get_contents( 'php://input' );

				$json = json_decode( $json, true );

				if ( json_last_error() !== JSON_ERROR_NONE ) {
                    $json = $_POST; // phpcs:ignore -- Ignoring nonce verification because we are fetching data from URL and not on form
				}
				$mo_jwt_register = new JWTDelete();
				$response        = $mo_jwt_register->delete_user_with_jwt( $json );

				$mj_util->send_json_response( $response );
			} elseif ( strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '/api/v1/mo-jwt' ) !== false ) {
				if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
					$mj_util->mo_jwt_send_exp_response();
				}

				$json = file_get_contents( 'php://input' );

				$json = json_decode( $json, true );

				if ( json_last_error() !== JSON_ERROR_NONE ) {
                    $json = $_POST; // phpcs:ignore -- Ignoring nonce verification because we are fetching data from URL and not on form
				}
				$mo_jwt_create = new JWTCreate();
				$response      = $mo_jwt_create->create_jwt_response( $json );

				$mj_util->send_json_response( $response );

			}
		}

	}

}
