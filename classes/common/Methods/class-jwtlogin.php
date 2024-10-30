<?php
/**
 * Settings Controller
 *
 * JWT Login Settings controller
 *
 * @category   JWT Login
 * @package    MoJWT\Method
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Methods;

use MoJWT\JWTUtils;
use MoJWT\JWTHandler;
/**
 * Class to API Key Settings Controller.
 *
 * @category Api Key Auth
 * @package  MoJWT\Method\ApiKeyAuth
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWTLogin {

	/**
	 * Method slug
	 *
	 * @var string
	 */
	private $method_slug;

	/**
	 * JWT token
	 *
	 * @var string
	 */
	private $jwt_token;

	/**
	 * Allow jwt login
	 *
	 * @var string
	 */
	private $jwt_allow_login_with;

	/**
	 * Redirection of jwt login
	 *
	 * @var string
	 */
	private $jwt_redirection;

	/**
	 * Method configuration
	 *
	 * @var array
	 */
	private $method_config;

	/**
	 * Current method configuration
	 *
	 * @var array
	 */
	private $current_config;

	/**
	 * Client secret
	 *
	 * @var string
	 */
	private $client_secret;

	/**
	 * Jwt algo
	 *
	 * @var string
	 */
	private $jwt_algo;

	/**
	 * Method for token validation
	 *
	 * @var string
	 */
	private $token_validation_method;

	/**
	 * Url from jwt
	 *
	 * @var string
	 */
	private $get_jwt_from_url;

	/**
	 * Cookie from jwt
	 *
	 * @var string
	 */
	private $get_jwt_from_cookie;

	/**
	 * Header from jwt
	 *
	 * @var string
	 */
	private $get_jwt_from_header;

	/**
	 * Attribute from jwt
	 *
	 * @var string
	 */
	private $get_jwt_attr;

	/**
	 * Custom JWT token name.
	 *
	 * @var string
	 */
	private $jwt_token_name;


	/**
	 * Constructor
	 *
	 * @param bool $token Set token false.
	 *
	 * @return void
	 */
	public function __construct( $token = false ) {

		global $mj_util;

		$this->method_slug             = 'jwtlogin';
		$this->current_config          = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config           = $this->current_config ? ( $this->current_config[ $this->method_slug ] ) : false;
		$this->jwt_allow_login_with    = 'username';
		$this->jwt_redirection         = $this->method_config ? $this->method_config['mo_jwt_redirection'] : false;
		$this->jwt_token               = $token ? $token : false;
		$this->jwt_algo                = $this->current_config ? $this->current_config['jwtcreate']['mo_jwt_sign_algo'] : 'HS256';
		$this->client_secret           = $mj_util->mo_jwt_get_option( 'mo_jwt_client_secret' ) ? $mj_util->mo_jwt_get_option( 'mo_jwt_client_secret' ) : false;
		$this->token_validation_method = isset( $this->method_config['mo_jwt_token_validation_method'] ) ? $this->method_config['mo_jwt_token_validation_method'] : 'signing_key';
		$this->get_jwt_from_url        = $this->current_config ? $this->method_config['mo_jwt_get_token_from_url'] : 'on';
		$this->get_jwt_from_cookie     = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_cookie'] ) ? $this->method_config['mo_jwt_get_token_from_cookie'] : false;
		$this->get_jwt_from_header     = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_header'] ) ? $this->method_config['mo_jwt_get_token_from_header'] : false;
		$this->get_jwt_attr            = $mj_util->get_plugin_config( 'mo_jwt_attr_settings' ) ? $mj_util->get_plugin_config( 'mo_jwt_attr_settings' ) : false;
		$this->jwt_token_name          = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token'; // token name.
	}

	/**
	 * Perform jwt login
	 *
	 * @param mixed $jwt_token JWT token.
	 *
	 * @return mixed
	 */
	public function perform_jwt_login( $jwt_token ) {
		global $mj_util;

		$jwt = '';

		$this->jwt_token = $jwt_token;

		if ( 'oauth_oidc' !== $this->token_validation_method ) {
			$token = \explode( '.', $this->jwt_token );

			if ( count( $token ) !== 3 ) {
				$mj_util->send_error_response_on_url( 'invalid_jwt' );
			}
			$jwt = new JWTUtils( $this->jwt_token );

			$jwt_algo   = $this->jwt_algo;
			$jwt_secret = $this->client_secret;

		}
		$response = false;
		$user     = false;

		if ( $mj_util->check_versi( 1 ) ) {
			$jwt                    = new JWTUtils( $this->jwt_token );
			$instance_helper        = new \MoJWT\Base\InstanceHelper();
			$login_handler_instance = $instance_helper->jwt_login_handler( $this->jwt_token );
			$login_handler_instance->perform_jwt_login( $jwt );
		}

		if ( $this->get_jwt_from_url ) {

			if ( 'on' !== $this->get_jwt_from_cookie ) {

				$jwt = new JWTUtils( $this->jwt_token );

				$jwt_algo   = $this->jwt_algo;
				$jwt_secret = $this->client_secret;

				if ( $jwt->check_algo( $jwt_algo ) ) {
					if ( $jwt->verify( $jwt_secret ) ) {

						$jwt_claims = $jwt->get_decoded_payload();

						if ( $this->get_jwt_attr ) {

							$user_attr = $this->get_jwt_attr['default']['username'];

							if ( array_key_exists( $user_attr, $jwt_claims ) ) {
								$user = get_user_by( 'login', $jwt_claims[ $user_attr ] );

								if ( ! $user ) {
									get_user_by( 'email', $jwt_claims[ $user_attr ] );
								}

								$userid = '';

								if ( ! $user ) {
									$password = $mj_util->gen_rand_str( 10 );
									$userid   = wp_create_user( $jwt_claims[ $user_attr ], $password );

									if ( is_wp_error( $userid ) ) {
										$mj_util->show_error_message_on_screen( 'Error logging you in. Please try again or contact to your administrator.' );
									}
								}

								if ( '' === $userid ) {
									$userid = $user->ID;
								}
								wp_set_current_user( $userid );
								wp_set_auth_cookie( $userid, false );

								if ( 'home_redirect' === $this->jwt_redirection ) {
									wp_safe_redirect( home_url() );
									exit();
								} else {
									wp_safe_redirect( $mj_util->get_current_url_without_jwt() ); // To remove the JWT from URL after redirection.
									exit();
								}
							} else {
								$mj_util->show_error_message_on_screen( 'Please Configure the Attribute Mapping.' );
							}
						} else {
							$mj_util->show_error_message_on_screen( 'Please Configure the Attribute Mapping.' );
						}
					} else {
						$mj_util->send_error_response_on_url( 'JWT Signature is invalid' );
					}
				} else {
					if ( is_user_logged_in() ) {
						$actual_link = $mj_util->get_current_url();
						if ( strpos( $actual_link, $this->jwt_token_name ) !== false ) {
							$actual_link = preg_replace( '/([?&])mo_jwt_token=[^&]+(&|$)/', '$1', $actual_link );
							if ( substr( $actual_link, -1 ) === '?' || substr( $actual_link, -1 ) === '&' ) {
								$char        = substr( $actual_link, -1 );
								$actual_link = str_replace( $char, '', $actual_link );
							}
						}
						wp_safe_redirect( $actual_link );
						exit;
					}
					$mj_util->send_error_response_on_url( 'Incorrect JWT Format' );
				}
				return $response;
			}
		}
	}

}
