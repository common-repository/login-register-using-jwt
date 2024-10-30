<?php
/**
 * App
 *
 * JWT Common Settings.
 *
 * @category   Common, Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

use MoJWT\MJUtils;
use MoJWT\Customer;



/**
 * Class for JWT Settings.
 *
 * @category Common, Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Settings {

	/**
	 * JWT Plugin Configuration
	 *
	 * @var Array $config
	 * */
	public $config;

	/**
	 * JWT Attr Configuration
	 *
	 * @var Array $attr_config
	 * */
	public $attr_config;

	/**
	 * JWT utils
	 *
	 * @var \MoJWT\mj_utils $util
	 * */
	public $util;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $mj_util;
		$this->util = $mj_util;
		add_action( 'admin_init', array( $this, 'miniorange_jwt_save_settings' ) );
		add_action( 'admin_init', array( $this, 'mo_jwt_upgrade' ) );
		$this->config      = $this->util->get_plugin_config( 'mo_jwt_config_settings' );
		$this->attr_config = $this->util->get_plugin_config( 'mo_jwt_attr_settings' );
	}

	/**
	 * Saves Settings.
	 *
	 * @return void
	 */
	public function miniorange_jwt_save_settings() {
		global $mj_util, $mo_jwt_license_subscription_namespace;
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'POST' && current_user_can( 'administrator' ) ) {
			if ( isset( $_POST['mo_jwt_config_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_config_nonce'] ) ), 'mo_jwt_config_settings' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_config_settings' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
					return;
				}
				$post = $mj_util->mo_array_sanitize( $_POST );
				$this->save_configurations( $post );
			}

			if ( isset( $_POST['mo_jwt_mapping_section_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_mapping_section_nonce'] ) ), 'mo_jwt_mapping_section' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_mapping_section' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				if ( null !== $mo_jwt_license_subscription_namespace && true === $mo_jwt_license_subscription_namespace::is_license_expired()['STATUS'] ) {
					return;
				}
				$post = $mj_util->mo_array_sanitize( $_POST );
				$this->save_attr_mapping_configurations( $post );
			}

			if ( isset( $_POST['mo_jwt_change_miniorange_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_change_miniorange_nonce'] ) ), 'mo_jwt_change_miniorange' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_change_miniorange' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				isset( $_POST['update_license'] ) && null !== $mo_jwt_license_subscription_namespace ? $mo_jwt_license_subscription_namespace::refresh_license_expiry() : mo_jwt_deactivate();
				return;
			}

			if ( isset( $_POST['mo_jwt_register_customer_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_register_customer_nonce'] ) ), 'mo_jwt_register_customer' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_register_customer' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				// register the admin to miniOrange
				// validation and sanitization.
				$email            = '';
				$phone            = '';
				$password         = '';
				$fname            = '';
				$lname            = '';
				$company          = '';
				$confirm_password = '';
				if ( $this->util->mo_jwt_check_empty_or_null( $_POST['email'] ) || $this->util->mo_jwt_check_empty_or_null( $_POST['password'] ) || $this->util->mo_jwt_check_empty_or_null( $_POST['confirmPassword'] ) ) { // phpcs:ignore
					$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'All the fields are required. Please enter valid entries.' );
					$this->util->mo_jwt_show_error_message();
					return;
				}
				if ( ! isset( $_POST['confirmPassword'] ) || ! isset( $_POST['password'] ) || strlen( $_POST['password'] ) < 8 || strlen( $_POST['confirmPassword'] ) < 8 ) { // phpcs:ignore -- WordPress.Security.ValidatedSanitizedInput.InputNotSanitized miniOrange password has special characters, hence sanitization causes issues.
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Choose a password with minimum length 8.' );
						$this->util->mo_jwt_show_error_message(); // phpcs:ignore
						return;
				} else {
						$email            = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
						$phone            = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
						$password         = isset( $_POST['password'] ) ? stripslashes( $_POST['password'] ) : ''; // phpcs:ignore -- WordPress.Security.ValidatedSanitizedInput.InputNotSanitized miniOrange password has special characters, hence sanitization causes issues.
						$fname            = isset( $_POST['fname'] ) ? sanitize_text_field( wp_unslash( $_POST['fname'] ) ) : '';
						$lname            = isset( $_POST['lname'] ) ? sanitize_text_field( wp_unslash( $_POST['lname'] ) ) : '';
						$company          = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
						$confirm_password = isset( $_POST['confirmPassword'] ) ? stripslashes( $_POST['confirmPassword'] ) : ''; // phpcs:ignore -- WordPress.Security.ValidatedSanitizedInput.InputNotSanitized miniOrange password has special characters, hence sanitization causes issues.
				}

					$this->util->mo_jwt_update_option( 'mo_jwt_admin_email', $email );
					$this->util->mo_jwt_update_option( 'mo_jwt_admin_phone', $phone );
					$this->util->mo_jwt_update_option( 'mo_jwt_admin_fname', $fname );
					$this->util->mo_jwt_update_option( 'mo_jwt_admin_lname', $lname );
					$this->util->mo_jwt_update_option( 'mo_jwt_admin_company', $company );

				if ( $this->util->mo_jwt_is_curl_installed() === 0 ) {
						return $this->util->mo_jwt_show_curl_error();
				}

				if ( strcmp( $password, $confirm_password ) === 0 ) {
						$customer = new Customer( $password );
						$email    = $this->util->mo_jwt_get_option( 'mo_jwt_admin_email' );
						$content  = json_decode( $customer->check_customer(), true );
					if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) === 0 ) {
							$this->create_customer();
					} else {
							$this->mo_jwt_get_current_customer();
					}
				} else {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Passwords do not match.' );
						$this->util->mo_jwt_update_option( 'mo_jwt_verify_customer', '' );
						$this->util->mo_jwt_show_error_message();
				}
			}
			if ( isset( $_POST['mo_jwt_verify_customer_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_verify_customer_nonce'] ) ), 'mo_jwt_verify_customer' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_verify_customer' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
					// register the admin to miniOrange.
				if ( $this->util->mo_jwt_is_curl_installed() === 0 ) {
						return $this->util->mo_jwt_show_curl_error();
				}
					// validation and sanitization.
					$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password = isset( $_POST['password'] ) ? stripslashes( $_POST['password'] ) : ''; // phpcs:ignore -- WordPress.Security.ValidatedSanitizedInput.InputNotSanitized miniOrange password has special characters, hence sanitization causes issues.
				if ( $this->util->mo_jwt_check_empty_or_null( $email ) || $this->util->mo_jwt_check_empty_or_null( $password ) ) {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'All the fields are required. Please enter valid entries.' );
						$this->util->mo_jwt_show_error_message();
						return;
				}

					$this->util->mo_jwt_update_option( 'mo_jwt_admin_email', $email );
					$customer     = new Customer( $password );
					$content      = $customer->get_customer_key();
					$customer_key = json_decode( $content, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
						$this->util->mo_jwt_update_option( 'mo_jwt_admin_customer_key', $customer_key['id'] );
						$this->util->mo_jwt_update_option( 'mo_jwt_admin_api_key', $customer_key['apiKey'] );
						$this->util->mo_jwt_update_option( 'mo_jwt_customer_token', $customer_key['token'] );
					if ( isset( $customer_key['phone'] ) ) {
							$this->util->mo_jwt_update_option( 'mo_jwt_admin_phone', $customer_key['phone'] );
					}
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Customer retrieved successfully' );
						$this->util->mo_jwt_delete_option( 'mo_jwt_verify_customer' );
						$site_url = site_url() . '/wp-admin/admin.php?page=mo_jwt_settings&tab=account';
						$this->util->mo_jwt_show_success_message();
						wp_safe_redirect( $site_url );
						die();
				} else {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Invalid username or password. Please try again.' );
						$this->util->mo_jwt_show_error_message();
				}
			}
			if ( isset( $_POST['mo_jwt_change_email_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_change_email_nonce'] ) ), 'mo_jwt_change_email' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_change_email' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
					// Adding back button.
					$this->util->mo_jwt_update_option( 'mo_jwt_verify_customer', '' );
					$this->util->mo_jwt_update_option( 'mo_jwt_registration_status', '' );
					$this->util->mo_jwt_update_option( 'mo_jwt_new_registration', 'true' );
			}
			if ( isset( $_POST['mo_jwt_contact_us_query_option_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_contact_us_query_option_nonce'] ) ), 'mo_jwt_contact_us_query_option' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_contact_us_query_option' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {
				if ( $this->util->mo_jwt_is_curl_installed() === 0 ) {
						return $this->util->mo_jwt_show_curl_error();
				}
					// Contact Us query.
					$email    = isset( $_POST['mo_jwt_contact_us_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_jwt_contact_us_email'] ) ) : '';
					$phone    = isset( $_POST['mo_jwt_contact_us_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_jwt_contact_us_phone'] ) ) : '';
					$query    = isset( $_POST['mo_jwt_contact_us_query'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_jwt_contact_us_query'] ) ) : '';
					$customer = new Customer();
				if ( $this->util->mo_jwt_check_empty_or_null( $email ) || $this->util->mo_jwt_check_empty_or_null( $query ) ) {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Please fill up Email and Query fields to submit your query.' );
						$this->util->mo_jwt_show_error_message();
				} else {
						$send_config = false;
						$submited    = $customer->submit_contact_us( $email, $phone, $query, $send_config );
					if ( false === $submited ) {
							$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Your query could not be submitted. Please try again.' );
							$this->util->mo_jwt_show_error_message();
					} else {
							$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Thanks for getting in touch! We shall get back to you shortly.' );
							$this->util->mo_jwt_show_success_message();
					}
				}
			}
			if ( isset( $_POST['mo_jwt_demo_request_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_jwt_demo_request_nonce'] ) ), 'mo_jwt_demo_request' ) && isset( $_POST[ \MoJWTConstants::OPTION ] ) && 'mo_jwt_demo_request' === sanitize_text_field( wp_unslash( $_POST[ \MoJWTConstants::OPTION ] ) ) ) {

				$post = $mj_util->mo_array_sanitize( $_POST );

				if ( $mj_util->mo_jwt_is_curl_installed() === 0 ) {
					return $mj_util->mo_jwt_show_curl_error();
				}

				$email = isset( $post['mo_jwt_demo_email'] ) ? sanitize_email( wp_unslash( $post['mo_jwt_demo_email'] ) ) : '';
				$query = isset( $post['mo_jwt_demo_usecase'] ) ? sanitize_text_field( wp_unslash( $post['mo_jwt_demo_usecase'] ) ) : '';

				$jwt_opterations_selected = '';
				$jwt_operations           = array(
					'mo_jwt_demo_login_with_jwt'    => 'Login User with JWT',
					'mo_jwt_demo_register_with_jwt' => 'Register User with JWT',
					'mo_jwt_demo_delete_with_jwt'   => 'Delete User with JWT',
				);
				foreach ( $jwt_operations as $key => $value ) {
					if ( isset( $_POST[ $key ] ) && sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) === 'on' ) {
						$jwt_opterations_selected .= $value . ', ';
					}
				}

				$jwt_opterations_selected = rtrim( $jwt_opterations_selected, ', ' );

				$query .= '<br><b> JWT Operations: </b>' . $jwt_opterations_selected;

				if ( $mj_util->mo_jwt_check_empty_or_null( $email ) || $mj_util->mo_jwt_check_empty_or_null( $query ) ) {
					$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Please fill up Usecase and Email fields to submit your query.' );
					$this->util->mo_jwt_show_error_message();
				} else {
					$url = 'https://demo.miniorange.com/wordpress-oauth/';

					$headers  = array(
						'Content-Type' => 'application/x-www-form-urlencoded',
						'charset'      => 'UTF - 8',
					);
					$args     = array(
						'method'      => 'POST',
						'body'        => array(
							'option' => 'mo_auto_create_demosite',
							'mo_auto_create_demosite_email' => $email,
							'mo_auto_create_demosite_usecase' => $query,
							'mo_auto_create_demosite_demo_plan' => 'login-register-using-jwt@21.5.0',
							'mo_auto_create_demosite_plugin_name' => 'login-register-using-jwt',
						),
						'timeout'     => '20',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking'    => true,
						'headers'     => $headers,
					);
					$response = wp_remote_post( $url, $args );

					if ( is_wp_error( $response ) ) {
						$error_message = $response->get_error_message();
						echo 'Something went wrong: ' . esc_html( $error_message );
						exit();
					}

					$output = wp_remote_retrieve_body( $response );
					$output = json_decode( $output );
					if ( is_null( $output ) ) {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Something went wrong! contact to your administrator' );
						$this->util->mo_jwt_show_error_message();
					}
					if ( 'SUCCESS' === $output->status ) {

						if ( isset( $output->demo_credentials ) ) {

							$demo_credentials = array();

							$site_url           = esc_url_raw( $output->demo_credentials->site_url );
							$email              = sanitize_email( $output->demo_credentials->email );
							$temporary_password = $output->demo_credentials->temporary_password;
							$password_link      = esc_url_raw( $output->demo_credentials->password_link );

							$sanitized_demo_credentials = array(
								'site_url'           => $site_url,
								'email'              => $email,
								'temporary_password' => $temporary_password,
								'password_link'      => $password_link,
								'validity'           => gmdate( 'd F, Y', strtotime( '+10 day' ) ),
							);

							$this->util->mo_jwt_update_option( 'mo_jwt_demo_creds', $sanitized_demo_credentials );
							$output->message = 'Your trial has been generated successfully. Please use the below credentials to access the trial.';
						}

						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, sanitize_text_field( $output->message ) );
						$this->util->mo_jwt_show_success_message();
					} else {
						$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, sanitize_text_field( $output->message ) );
						$this->util->mo_jwt_show_error_message();
					}
				}
			}
		}
		$post = $mj_util->mo_array_sanitize( $_POST );

		do_action( 'do_main_jwt_settings_internal_action', $post );
	}


	/**
	 * Get current customer account.
	 *
	 * @return void
	 */
	public function mo_jwt_get_current_customer() {
		$customer     = new Customer();
		$content      = $customer->get_customer_key();
		$customer_key = json_decode( $content, true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			$this->util->mo_jwt_update_option( 'mo_jwt_admin_customer_key', $customer_key['id'] );
			$this->util->mo_jwt_update_option( 'mo_jwt_admin_api_key', $customer_key['apiKey'] );
			$this->util->mo_jwt_update_option( 'mo_jwt_customer_token', $customer_key['token'] );
			$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Customer retrieved successfully' );
			$this->util->mo_jwt_delete_option( 'mo_jwt_verify_customer' );
			$this->util->mo_jwt_delete_option( 'mo_jwt_new_registration' );
			$this->util->mo_jwt_show_success_message();
		} else {
			$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'You already have an account with miniOrange. Please enter a valid password.' );
			$this->util->mo_jwt_update_option( 'mo_jwt_verify_customer', 'true' );
			$this->util->mo_jwt_show_error_message();

		}
	}

	/**
	 * Create customer from API wrapper.
	 */
	public function create_customer() {
		global $mj_util;
		$customer     = new Customer();
		$customer_key = json_decode( $customer->create_customer(), true );
		if ( strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) === 0 ) {
			$this->mo_jwt_get_current_customer();
			$this->util->mo_jwt_delete_option( 'mo_jwt_new_customer' );
		} elseif ( strcasecmp( $customer_key['status'], 'SUCCESS' ) === 0 ) {
			$this->util->mo_jwt_update_option( 'mo_jwt_admin_customer_key', $customer_key['id'] );
			$this->util->mo_jwt_update_option( 'mo_jwt_admin_api_key', $customer_key['apiKey'] );
			$this->util->mo_jwt_update_option( 'mo_jwt_customer_token', $customer_key['token'] );
			$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Registered successfully.' );
			$this->util->mo_jwt_update_option( 'mo_jwt_registration_status', 'mo_jwt_REGISTRATION_COMPLETE' );
			$this->util->mo_jwt_update_option( 'mo_jwt_new_customer', 1 );
			$this->util->mo_jwt_delete_option( 'mo_jwt_verify_customer' );
			$this->util->mo_jwt_delete_option( 'mo_jwt_new_registration' );
			$this->util->mo_jwt_show_success_message();
		} else {
			$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Something went wrong please try again later. You can also reach out to us via Contact Us form and report this issue.' );
			$this->util->mo_jwt_show_error_message();
		}
	}

	/**
	 * Save config of attr mapping.
	 *
	 * @param mixed $post Configuration of attr mapping.
	 *
	 * @return void
	 */
	public function save_attr_mapping_configurations( $post ) {

		$attr_config = $this->util->get_plugin_config( 'mo_jwt_attr_settings' ) ? $this->util->get_plugin_config( 'mo_jwt_attr_settings' ) : array();
		$default     = array();

		$default['username']    = isset( $post['mo_jwt_username_attr'] ) ? sanitize_text_field( wp_unslash( $post['mo_jwt_username_attr'] ) ) : 'username';
		$attr_config['default'] = $default;

		$this->util->update_plugin_config( $attr_config, 'mo_jwt_attr_settings' );

		$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Settings are saved successfully.' );
		$this->util->mo_jwt_show_success_message();
	}

	/**
	 * Saves Configuration.
	 *
	 * @param mixed $post Values of config.
	 *
	 * @return void
	 */
	public function save_configurations( $post ) {

		if ( isset( $post['mo_jwt_method'] ) ) {

			$this->util->mo_jwt_set_transient( 'mo_jwt', sanitize_text_field( $post['mo_jwt_method'] ), 3600 );
			$this->config['jwtlogin']['mo_jwt_redirection']        = isset( $post['mo_jwt_redirection'] ) ? sanitize_text_field( wp_unslash( $post['mo_jwt_redirection'] ) ) : 'home_redirect';
			$this->config['jwtlogin']['mo_jwt_get_token_from_url'] = isset( $post['mo_jwt_get_token_from_url'] ) ? sanitize_text_field( wp_unslash( $post['mo_jwt_get_token_from_url'] ) ) : 'on';

			$this->util->update_plugin_config( $this->config, 'mo_jwt_config_settings' );

			$this->util->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, 'Settings are saved successfully.' );
			$this->util->mo_jwt_show_success_message();
		}
	}

	/**
	 * Upgrade hook.
	 *
	 * @return void
	 */
	public function mo_jwt_upgrade() {
		$mj_util        = new MJUtils();
		$plugin_version = $mj_util->mo_jwt_get_option( 'mo_jwt_plugin_version' );
		if ( ! $plugin_version || ( MJ_PLUGIN_VERSION !== $plugin_version ) ) {
			$mj_util->upgrade_plugin();
		}
		$mj_util->mo_jwt_update_option( 'mo_jwt_plugin_version', MJ_PLUGIN_VERSION );
	}

}

