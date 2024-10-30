<?php
/**
 * MiniOrange enables user to log in through JWT.
 *  Copyright (C) 2015  miniOrange
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * @package     miniOrange JWT
 * @license     http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 */

/**
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/

namespace MoJWT;

/**
 * Accounts
 *
 * JWT Account Settings.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */
class Customer {

	/**
	 * Customer Email
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Customer Phone
	 *
	 * @var string
	 */
	public $phone;

	/**
	 * Default customer key
	 *
	 * @var string
	 */
	private $default_customer_key = '16555';

	/**
	 * Default API key
	 *
	 * @var string
	 */
	private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

	/**
	 * Host Name
	 *
	 * @var string
	 */
	private $host_name = '';

	/**
	 * Host key
	 *
	 * @var string
	 */
	private $host_key = '';


	/**
	 * Constructor
	 *
	 * @param bool $password user miniOrange password.
	 */
	public function __construct( $password = false ) {
		global $mj_util;
		$this->host_name = $mj_util->mo_jwt_get_option( 'mo_jwt_host_name' ) ? $mj_util->mo_jwt_get_option( 'mo_jwt_host_name' ) : 'https://login.xecurify.com';
		$this->email     = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_email' );
		$this->phone     = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_phone' );
		$this->host_key  = $password;
	}

	/**
	 * Function to register customer.
	 */
	public function create_customer() {
		global $mj_util;
		$url          = $this->host_name . '/moas/rest/customer/add';
		$password     = $this->host_key;
		$first_name   = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_fname' );
		$last_name    = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_lname' );
		$company      = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_company' );
		$fields       = array(
			'companyName'          => $company,
			'areaOfInterest'       => 'WP JWT Login',
			'firstname'            => $first_name,
			'lastname'             => $last_name,
			\MoJWTConstants::EMAIL => $this->email,
			'phone'                => $this->phone,
			'password'             => $password,
		);
		$field_string = wp_json_encode( $fields );
		return $this->send_request(
			array(),
			false,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to retrieve customer key from API.
	 */
	public function get_customer_key() {
		global $mj_util;
		$url          = $this->host_name . '/moas/rest/customer/key';
		$email        = $this->email;
		$password     = $this->host_key;
		$fields       = array(
			\MoJWTConstants::EMAIL => $email,
			'password'             => $password,
		);
		$field_string = wp_json_encode( $fields );
		return $this->send_request(
			array(),
			false,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to submit contact us form.
	 *
	 * @param string $email Email of the admin.
	 * @param string $phone Phone of the admin.
	 * @param string $query Query of the admin.
	 * @param bool   $send_config Sends config.
	 */
	public function submit_contact_us( $email, $phone, $query, $send_config = true ) {
		global $current_user;
		global $mj_util;
		$user                   = wp_get_current_user();
		$customer_key           = $this->default_customer_key;
		$api_key                = $this->default_api_key;
		$current_time_in_millis = time();
		$url                    = $this->host_name . '/moas/api/notify/send';
		$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
		$hash_value             = hash( 'sha512', $string_to_hash );
		$from_email             = empty( $email ) ? $user->user_email : $email;
		$version                = ( \ucwords( \strtolower( $mj_util->get_versi_str() ) ) !== 'Free' ) ? ( \ucwords( \strtolower( $mj_util->get_versi_str() ) ) . ' - ' . \mo_jwt_get_version_number() ) : ( ' - ' . \mo_jwt_get_version_number() );
		$subject                = 'Query: WordPress JWT Login & Register ' . $version . ' Plugin';
		$query                  = '[WordPress JWT Login & Register ' . $version . '] ' . $query;

		$server                   = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
		$content                  = '<div >Hello, <br><br>First Name :' . $current_user->user_firstname . '<br><br>Last  Name :' . $current_user->user_lastname . '   <br><br>Company :<a href="' . $server . '" target="_blank" >' . $server . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Query :' . $query . '</div>';
		$fields                   = array(
			'customerKey'          => $customer_key,
			'sendEmail'            => true,
			\MoJWTConstants::EMAIL => array(
				'customerKey' => $customer_key,
				'fromEmail'   => $from_email,
				'bccEmail'    => 'info@xecurify.com',
				'fromName'    => 'miniOrange',
				'toEmail'     => 'apisupport@xecurify.com',
				'toName'      => 'apisupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content,
			),
		);
		$field_string             = wp_json_encode( $fields, JSON_UNESCAPED_SLASHES );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		return $this->send_request(
			$headers,
			true,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to send OTP.
	 *
	 * @param string $email         Self Explanatory.
	 * @param string $phone         Self Explanatory.
	 * @param bool   $send_to_email Self Explanatory.
	 * @param bool   $send_to_phone Self Explanatory.
	 */
	public function send_otp_token( $email = '', $phone = '', $send_to_email = true, $send_to_phone = false ) {
		global $mj_util;
		$url          = $this->host_name . '/moas/api/auth/challenge';
		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;
		$username     = $this->email;
		$phone        = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_phone' );

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = self::get_timestamp();

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash = $customer_key . $current_time_in_millis . $api_key;
		$hash_value     = hash( 'sha512', $string_to_hash );

		$customer_key_header  = 'Customer-Key: ' . $customer_key;
		$timestamp_header     = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header = 'Authorization: ' . $hash_value;

		if ( $send_to_email ) {
			$fields = array(
				'customerKey'          => $customer_key,
				\MoJWTConstants::EMAIL => $username,
				'authType'             => 'EMAIL',
			);
		} else {
			$fields = array(
				'customerKey' => $customer_key,
				'phone'       => $phone,
				'authType'    => 'SMS',
			);
		}
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		return $this->send_request(
			$headers,
			true,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to get timestamp from API.
	 */
	public function get_timestamp() {
		global $mj_util;
		$url = $this->host_name . '/moas/rest/mobile/get-timestamp';
		return $this->send_request(
			array(),
			false,
			'',
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to validate OTP.
	 *
	 * @param string $transaction_id OTP TxID.
	 * @param string $otp_token      OTP Entered.
	 *
	 * @return array
	 */
	public function validate_otp_token( $transaction_id, $otp_token ) {
		global $mj_util;
		$url          = $this->host_name . '/moas/api/auth/validate';
		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;
		$username     = $this->email;

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = self::get_timestamp();

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash       = $customer_key . $current_time_in_millis . $api_key;
		$hash_value           = hash( 'sha512', $string_to_hash );
		$customer_key_header  = 'Customer-Key: ' . $customer_key;
		$timestamp_header     = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header = 'Authorization: ' . $hash_value;
		$field_string         = '';
		// check for otp over sms/email.
		$fields       = array(
			'txId'  => $transaction_id,
			'token' => $otp_token,
		);
		$field_string = wp_json_encode( $fields );

		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		return $this->send_request(
			$headers,
			true,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to check if customer registering already exists.
	 */
	public function check_customer() {
		global $mj_util;
		$url          = $this->host_name . '/moas/rest/customer/check-if-exists';
		$email        = $this->email;
		$fields       = array(
			\MoJWTConstants::EMAIL => $email,
		);
		$field_string = wp_json_encode( $fields );
		return $this->send_request(
			array(),
			false,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Function to send Feedback.
	 *
	 * @param string $email   Self Explanatory.
	 * @param string $phone   Self Explanatory.
	 * @param string $message Self Explanatory.
	 */
	public function mo_jwt_send_email_alert( $email, $phone, $message ) {
		global $mj_util;
		if ( ! $this->check_internet_connection() ) {
			return;
		}
		$url = $this->host_name . '/moas/api/notify/send';
		global $user;
		$customer_key = $this->default_customer_key;
		$api_key      = $this->default_api_key;
		$user         = wp_get_current_user();

		$current_time_in_millis = self::get_timestamp();
		$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
		$hash_value             = hash( 'sha512', $string_to_hash );
		$from_email             = empty( $email ) ? $user->user_email : $email;
		$subject                = 'WordPress JWT Login & Register Plugin by miniOrange';
		$site_url               = site_url();
		$version                = ( \ucwords( \strtolower( $mj_util->get_versi_str() ) ) !== 'Free' ) ? ( \ucwords( \strtolower( $mj_util->get_versi_str() ) ) . ' - ' . \mo_jwt_get_version_number() ) : ( ' - ' . \mo_jwt_get_version_number() );

		$query   = '[ WP JWT Login & Register ' . $version . ' ] : ' . $message;
		$server  = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
		$content = '<div >Hello, <br><br>First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . $server . '" target="_blank" >' . $server . '</a><br><br>Phone Number :' . $phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>Query :' . $query . '</div>';

		$fields                   = array(
			'customerKey'          => $customer_key,
			'sendEmail'            => true,
			\MoJWTConstants::EMAIL => array(
				'customerKey' => $customer_key,
				'fromEmail'   => $from_email,
				'bccEmail'    => 'info@xecurify.com',
				'fromName'    => 'miniOrange',
				'toEmail'     => 'apisupport@xecurify.com',
				'toName'      => 'apisupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content,
			),
		);
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		return $this->send_request(
			$headers,
			true,
			$field_string,
			array(),
			false,
			$url
		);
	}


	/**
	 * Handle Forgot password with API.
	 *
	 * @param string $email Email of the customer.
	 *
	 * @return array
	 */
	public function mo_jwt_forgot_password( $email ) {
		global $mj_util;
		$url = $this->host_name . '/moas/rest/customer/password-reset';
		/* The customer Key provided to you */
		$customer_key = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_customer_key' );

		/* The customer API Key provided to you */
		$api_key = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_api_key' );

		/* Current time in milliseconds since midnight, January 1, 1970 UTC. */
		$current_time_in_millis = self::get_timestamp();

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash       = $customer_key . $current_time_in_millis . $api_key;
		$hash_value           = hash( 'sha512', $string_to_hash );
		$customer_key_header  = 'Customer-Key: ' . $customer_key;
		$timestamp_header     = 'Timestamp: ' . number_format( $current_time_in_millis, 0, '', '' );
		$authorization_header = 'Authorization: ' . $hash_value;
		$field_string         = '';
		// *check for otp over sms/email
		$fields                   = array(
			\MoJWTConstants::EMAIL => $email,
		);
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		return $this->send_request(
			$headers,
			true,
			$field_string,
			array(),
			false,
			$url
		);
	}

	/**
	 * Self-Explanatory.
	 */
	public function check_internet_connection() {
		return (bool) @fsockopen( 'login.xecurify.com', 443, $iErrno, $sErrStr, 5 ); // phpcs:ignore --WordPress.WP.AlternativeFunctions.file_system_read_fsockopen using default PHP function to check internet connection.
	}

	/**
	 * Function to actually send requests
	 *
	 * @param array  $additional_headers Additional headers to send with default headers.
	 * @param bool   $override_headers   self explanatory.
	 * @param string $field_string       Field String.
	 * @param array  $additional_args    Additional args to send with default headers.
	 * @param bool   $override_args      self explanatory.
	 * @param string $url                URL to send request to.
	 */
	private function send_request( $additional_headers = false, $override_headers = false, $field_string = '', $additional_args = false, $override_args = false, $url = '' ) {
		$headers  = array(
			'Content-Type'  => 'application/json',
			'charset'       => 'UTF - 8',
			'Authorization' => 'Basic',
		);
		$headers  = ( $override_headers && $additional_headers ) ? $additional_headers : array_unique( array_merge( $headers, $additional_headers ) );
		$args     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'sslverify'   => true,
		);
		$args     = ( $override_args ) ? $additional_args : array_unique( array_merge( $args, $additional_args ), SORT_REGULAR );
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo esc_html( "Something went wrong: $error_message" );
			exit();
		}

		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Deactivation Hook
	 */
	public function manage_deactivate_cache() {
		global $mj_util;
		$lk = $mj_util->mo_jwt_get_option( 'mo_jwt_lk' );
		if ( ! $mj_util->mo_jwt_is_customer_registered() || false === $lk || empty( $lk ) ) {
			return;
		}
		$url          = $this->host_name . '/moas/api/backupcode/updatestatus';
		$customer_key = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_customer_key' );
		$api_key      = $mj_util->mo_jwt_get_option( 'mo_jwt_admin_api_key' );
		$code         = $mj_util->mojwtdecrypt( $lk );

		$current_time_in_millis = round( microtime( true ) * 1000 );
		$current_time_in_millis = number_format( $current_time_in_millis, 0, '', '' );

		/* Creating the Hash using SHA-512 algorithm */
		$string_to_hash           = $customer_key . $current_time_in_millis . $api_key;
		$hash_value               = hash( 'sha512', $string_to_hash );
		$customer_key_header      = 'Customer-Key: ' . $customer_key;
		$timestamp_header         = 'Timestamp: ' . $current_time_in_millis;
		$authorization_header     = 'Authorization: ' . $hash_value;
		$fields                   = '';
		$fields                   = array(
			'code'             => $code,
			'customerKey'      => $customer_key,
			'additionalFields' => array(
				'field1' => site_url(),
			),
		);
		$field_string             = wp_json_encode( $fields );
		$headers                  = array( 'Content-Type' => 'application/json' );
		$headers['Customer-Key']  = $customer_key;
		$headers['Timestamp']     = $current_time_in_millis;
		$headers['Authorization'] = $hash_value;
		$args                     = array(
			'method'      => 'POST',
			'body'        => $field_string,
			'timeout'     => '15',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,

		);
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo esc_html( 'Something went wrong: ' . $error_message );
			exit();
		}

		return wp_remote_retrieve_body( $response );
	}

}
