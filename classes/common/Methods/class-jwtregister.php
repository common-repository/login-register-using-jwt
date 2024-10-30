<?php
/**
 * Settings Controller
 *
 * JWT Login Settings controller
 *
 * @category   JWT Register
 * @package    MoJWT\Method
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Methods;

use MoJWT\JWTUtils;
use MoJWT\JWTHandler;
/**
 * JWT Register
 *
 * @category JWT Register
 * @package  MoJWT\Method\JWTRegister
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWTRegister {

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
	 * Allowed role
	 *
	 * @var string
	 */
	private $allow_role;

	/**
	 * Default role
	 *
	 * @var string
	 */
	private $default_role;

	/**
	 * API Key for JWT User Registeration.
	 *
	 * @var string $api_key
	 */
	private $api_key;

	/**
	 * Check if JWT token need to be invalidated
	 *
	 * @var string $jwt_invalidate
	 */
	private $jwt_invalidate;

	/**
	 * JWT token name stored in meta key
	 *
	 * @var string $jwt_token_name
	 */
	private $jwt_token_name;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {
		global $mj_util;
		$this->method_slug    = 'jwtregister';
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config  = $this->current_config ? ( $this->current_config['jwtcreate'] ) : false;
		$this->sign_algo      = $this->method_config ? $this->method_config['mo_jwt_sign_algo'] : 'HS256';
		$this->client_secret  = $this->method_config ? $this->method_config['mo_jwt_secret'] : false;
		$this->default_role   = $this->current_config && $mj_util->get_versi() ? $this->current_config['jwtregister']['mo_jwt_register_role'] : 'subscriber';
		$this->allow_role     = $this->current_config && $mj_util->get_versi() ? $this->current_config['jwtregister']['mo_jwt_allow_role'] : false;
		$this->api_key        = isset( $this->current_config['jwtregister']['mo_jwt_api_key'] ) ? $this->current_config['jwtregister']['mo_jwt_api_key'] : '';
		$this->jwt_invalidate = $this->method_config && isset( $this->method_config['mo_jwt_invalidate'] ) ? $this->method_config['mo_jwt_invalidate'] : false;
		$this->jwt_token_name = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token'; // token name.

		add_action( 'wp_ajax_mo_jwt_generate_new_api_key', array( $this, 'mo_jwt_generate_new_api_key' ) );
	}

	/**
	 * Function to generate API key for endpoint authentication.
	 */
	public function mo_jwt_generate_new_api_key() {
		global $mj_util, $mo_jwt_license_subscription_namespace;
		if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
			$api_key  = $this->current_config['jwtregister']['mo_jwt_api_key'];
			$response = array(
				'status'      => 'success',
				'new_api_key' => $api_key,
			);
			wp_send_json( $response );
		}
		$new_api_key = $mj_util->gen_rand_str( 32 );
		$this->current_config['jwtregister']['mo_jwt_api_key'] = $new_api_key;
		$mj_util->update_plugin_config( $this->current_config, 'mo_jwt_config_settings' );

		$response = array(
			'status'      => 'success',
			'new_api_key' => $new_api_key,
		);
		wp_send_json( $response );
	}

	/**
	 * Create user with jwt
	 *
	 * @param mixed $request Request to create user with jwt.
	 *
	 * @return string
	 */
	public function create_user_with_jwt( $request ) {

		global $mj_util;

		if ( isset( $request['username'] ) && isset( $request['apikey'] ) ) {

			$api_key        = $request['apikey'];
			$config         = $mj_util->get_plugin_config( 'mo_jwt_config_settings' );
			$plugin_api_key = $config['jwtregister']['mo_jwt_api_key'];

			if ( ! ( $api_key === $plugin_api_key ) ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'UNAUTHORIZED',
					'code'              => '401',
					'error_description' => 'Invalid API Key.',
				);
				$mj_util->send_json_response( $response );
			}

			if ( false === $this->client_secret || '' === $this->client_secret ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'BAD_REQUEST',
					'code'              => '401',
					'error_description' => 'Sorry, client secret is required to make a request. Contact to your administrator.',
				);
				$mj_util->send_json_response( $response );
			}

			$username = sanitize_text_field( $request['username'] );
			$password = isset( $request['password'] ) ? sanitize_text_field( $request['password'] ) : $mj_util->gen_rand_str( 10 );

			if ( empty( $password ) ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'BAD_REQUEST',
					'code'              => '401',
					'error_description' => 'User password cannot be empty.',
				);
				$mj_util->send_json_response( $response );
			}

			$user = wp_create_user( $username, $password );

			if ( is_wp_error( $user ) ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'BAD_REQUEST',
					'code'              => '401',
					'error_description' => $user->get_error_message(),
				);
				$mj_util->send_json_response( $response );
			}

			$allow_default_role = 0;

			if ( $this->allow_role ) {
				$user_role = isset( $request['role'] ) ? sanitize_text_field( $request['role'] ) : false;
				$user_role = str_replace( ' ', '_', $user_role );
				$user_role = strtolower( $user_role );
				if ( $user_role ) {
					$all_roles = array_keys( wp_roles()->roles );
					if ( ! in_array( $user_role, $all_roles, true ) ) {
						$wp_user = new \WP_User( $user );
						$wp_user->set_role( $this->default_role );
						$response = array(
							'status'            => 'error',
							'error'             => 'BAD_REQUEST',
							'code'              => '401',
							'error_description' => 'The role passed in the request does not exists in the WordPress. Pass a correct role.',
							'error_details'     => 'User created with default roles',
						);
						$mj_util->send_json_response( $response );
					}

					$wp_user = new \WP_User( $user );
					$wp_user->set_role( $user_role );
				} else {
					$allow_default_role = 1;
				}
			}
			if ( $allow_default_role ) {
				$wp_user = new \WP_User( $user );
				$wp_user->set_role( $this->default_role );
			}

			$client_secret = $this->client_secret;
			$sign_algo     = $this->sign_algo;

			$user = get_user_by( 'login', $username );

			$token_data = '';
			$jwt_user   = array(
				'sub'      => $user->ID,
				'username' => $user->user_login,
				'email'    => $user->user_email,
			);
			$jwt        = new JWTUtils();
			$response   = $jwt->create_jwt_token( $jwt_user, $client_secret, $sign_algo, 60, $client_secret );
			if ( $this->jwt_invalidate ) {
				update_user_meta( $user->ID, $this->jwt_token_name, $response['jwt_token'] );
			}
			$response['code'] = 200;
			return $response;

		} elseif ( ! isset( $request['username'] ) ) {
			$response = array(
				'status'            => 'error',
				'error'             => 'FORBIDDEN',
				'code'              => '403',
				'error_description' => 'Username is required.',
			);
			$mj_util->send_json_response( $response );
		} else {
			$response = array(
				'status'            => 'error',
				'error'             => 'FORBIDDEN',
				'code'              => '403',
				'error_description' => 'API Key is required.',
			);
			$mj_util->send_json_response( $response );
		}
	}

}
