<?php
/**
 * Settings Controller
 *
 * JWT Login Settings controller
 *
 * @category   JWT Create
 * @package    MoJWT\Method
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Methods;

use MoJWT\JWTUtils;
use MoJWT\JWTHandler;
/**
 * For jwt response
 *
 * @category JWT Create
 * @package  MoJWT\Method\JWTCreate
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWTCreate {

	/**
	 * Method slug
	 *
	 * @var string
	 */
	private $method_slug;

	/**
	 * Client Secret
	 *
	 * @var string
	 */
	private $client_secret;

	/**
	 * Current jwt config
	 *
	 * @var array
	 */
	private $current_config;


	/**
	 * Jwt config
	 *
	 * @var array
	 */
	private $method_config;

	/**
	 * Sign algo
	 *
	 * @var string
	 */
	private $sign_algo;

	/**
	 * Token expiry
	 *
	 * @var string
	 */
	private $token_expiry;

	/**
	 * Flag to check if token is invalidated.
	 *
	 * @var string
	 */
	private $jwt_invalidate;

	/**
	 * Custom JWT token name.
	 *
	 * @var string
	 */
	private $jwt_token_name;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {
		global $mj_util;
		$this->method_slug    = 'jwtcreate';
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config  = $this->current_config ? ( $this->current_config['jwtcreate'] ) : false;
		$this->sign_algo      = $this->method_config ? $this->method_config['mo_jwt_sign_algo'] : 'HS256';
		$this->client_secret  = $this->method_config ? $this->method_config['mo_jwt_secret'] : false;
		$this->token_expiry   = $this->method_config ? $this->method_config['mo_jwt_token_expiry'] : false;
		$this->jwt_invalidate = $this->method_config && isset( $this->method_config['mo_jwt_invalidate'] ) ? $this->method_config['mo_jwt_invalidate'] : false;
		$this->jwt_token_name = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token'; // token name.
		$mj_util->mo_jwt_update_option( 'mo_jwt_client_secret', $this->client_secret );
	}

	/**
	 * Create jwt response
	 *
	 * @param mixed $request Request to create jwt response.
	 *
	 * @return array
	 */
	public function create_jwt_response( $request ) {
		global $mj_util;

		if ( isset( $request['username'] ) && isset( $request['password'] ) ) {
			$username      = sanitize_text_field( $request['username'] );
			$password      = sanitize_text_field( $request['password'] );
			$client_secret = $this->client_secret;
			$sign_algo     = $this->sign_algo;
			$token_expiry  = $this->token_expiry;

			if ( false === $client_secret || '' === $client_secret ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'BAD_REQUEST',
					'code'              => '401',
					'error_description' => 'Sorry, client secret is required to make a request. Contact to your administrator.',
				);
				$mj_util->send_json_response( $response );
			}

			$user = get_user_by( 'login', $username );

			if ( ! $user ) {
				$user = get_user_by( 'email', $username );
			}

			if ( $user ) {
				wp_set_current_user( $user->ID );
				$valid_pass = wp_check_password( $password, $user->user_pass, $user->ID );
			}

			if ( isset( $valid_pass ) && $valid_pass ) {
				$token_data       = '';
				$jwt_user         = array(
					'sub'      => $user->ID,
					'username' => $user->user_login,
					'email'    => $user->user_email,
				);
				$jwt              = new JWTUtils();
				$response         = $jwt->create_jwt_token( $jwt_user, $client_secret, $sign_algo, $token_expiry, $client_secret );
				$response['code'] = 200;

				// Invalidate existing JWT.
				if ( $this->jwt_invalidate ) {
					update_user_meta( $user->ID, $this->jwt_token_name, $response['jwt_token'] );
				}

				return $response;
			} else {
				$response = array(
					'status'            => 'error',
					'error'             => 'INVALID_CREDENTIALS',
					'code'              => '400',
					'error_description' => 'Invalid username or password.',
				);
				$mj_util->send_json_response( $response );
			}
		} else {
			$response = array(
				'status'            => 'error',
				'error'             => 'FORBIDDEN',
				'code'              => '403',
				'error_description' => 'Username and password are required.',
			);
			$mj_util->send_json_response( $response );
		}
	}

}
