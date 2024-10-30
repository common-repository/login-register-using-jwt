<?php
/**
 * Settings Controller
 *
 * JWT Login Settings controller
 *
 * @category   JWT Delete
 * @package    MoJWT\Method
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Methods;

use MoJWT\JWTUtils;
use MoJWT\JWTHandler;

require_once ABSPATH . 'wp-admin/includes/user.php';

/**
 * Delete JWT
 *
 * @category JWT Delete
 * @package  MoJWT\Method\JWTRegister
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWTDelete {

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
	 * Jwt algo
	 *
	 * @var string
	 */
	private $jwt_algo;

	/**
	 * API Key for JWT User Registeration.
	 *
	 * @var string $api_key
	 */
	private $api_key;

	/**
	 * Check existing JWT token of user.
	 *
	 * @var string $jwt_invalidate
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
		$this->method_slug    = 'jwtdelete';
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config  = $this->current_config ? ( $this->current_config['jwtcreate'] ) : false;
		$this->sign_algo      = $this->method_config ? $this->method_config['mo_jwt_sign_algo'] : 'HS256';
		$this->client_secret  = $this->method_config ? $this->method_config['mo_jwt_dec_secret'] : false;
		$this->default_role   = $this->current_config ? $this->current_config['jwtregister']['mo_jwt_register_role'] : 'subscriber';
		$this->allow_role     = $this->current_config ? $this->current_config['jwtregister']['mo_jwt_allow_role'] : false;
		$this->api_key        = isset( $this->current_config['jwtregister']['mo_jwt_api_key'] ) ? $this->current_config['jwtregister']['mo_jwt_api_key'] : '';
		$this->jwt_algo       = $this->current_config ? $this->current_config['jwtcreate']['mo_jwt_sign_algo'] : 'HS256';
		$this->jwt_invalidate = $this->method_config && isset( $this->method_config['mo_jwt_invalidate'] ) ? $this->method_config['mo_jwt_invalidate'] : false;
		$this->jwt_token_name = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token'; // token name.
	}
	/**
	 * Delete jwt response
	 *
	 * @param mixed $request Request to delete jwt response.
	 *
	 * @return void
	 */
	public function delete_user_with_jwt( $request ) {

		global $mj_util;

		if ( isset( $request['jwt-token'] ) ) {

			global $mj_util;

			$jwt_token = sanitize_text_field( $request['jwt-token'] );

			$token = \explode( '.', $jwt_token );

			if ( count( $token ) !== 3 ) {
				$response = array(
					'status'            => 'error',
					'error'             => 'BAD_REQUEST',
					'error_description' => 'JWT token passed is incorrect.',
					'code'              => '401',
				);
				$mj_util->send_json_response( $response );
			}

			$jwt = new JWTUtils( $jwt_token );

			$jwt_algo   = $this->jwt_algo;
			$jwt_secret = $this->client_secret;

			$response = false;
			$user     = false;
			if ( $jwt->check_algo( $jwt_algo ) ) {
				if ( $jwt->verify( $jwt_secret ) ) {

					$jwt_claims = $jwt->get_decoded_payload();

					$user = get_user_by( 'login', $jwt_claims['username'] );
					if ( ! $user ) {
						$user = get_user_by( 'email', $jwt_claims['email'] );
					}

					if ( $user ) {
						if ( $this->jwt_invalidate ) {
							$token_invalidate = get_user_meta( $user->ID, $this->jwt_token_name ); // TODO: mo_jwt_token name to be made generic.
							if ( ! empty( $token_invalidate ) && $jwt_token !== $token_invalidate[0] ) {
								$response = array(
									'status'      => 'error',
									'description' => 'Authentication token has been invalidated',
									'code'        => '401',
								);
								$mj_util->send_json_response( $response );
							}
						}

						$delete_status = \wp_delete_user( $user->ID );
						$response      = array(
							'status'      => 'success',
							'description' => 'The user is deleted successfully.',
							'code'        => '200',
						);
						$mj_util->send_json_response( $response );
					} else {
						$response = array(
							'status'      => 'error',
							'error'       => 'USER_NOT_EXIST',
							'description' => 'The user does not exists.',
							'code'        => '401',
						);
						$mj_util->send_json_response( $response );
					}
				} else {
					$response = array(
						'status'      => 'error',
						'error'       => 'INVALID_JWT',
						'description' => 'The JWT passed is invalid.',
						'code'        => '401',
					);
						$mj_util->send_json_response( $response );
				}
			} else {
				$response = array(
					'status'      => 'error',
					'error'       => 'INVALID_JWT',
					'description' => 'The JWT passed is invalid.',
					'code'        => '401',
				);
					$mj_util->send_json_response( $response );
			}
		} else {
			$response = array(
				'status'            => 'error',
				'error'             => 'BAD_REQUEST',
				'error_description' => 'Please pass the JWT token.',
				'code'              => '401',
			);
			$mj_util->send_json_response( $response );
		}
	}

}
