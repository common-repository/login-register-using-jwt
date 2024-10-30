<?php
/**
 * Utils
 *
 * JWT Login Utility class.
 *
 * @category   Core
 * @package    MoRestAPI
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

/**
 * Class containing all utility and helper functions.
 *
 * @category Core, Utils
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class MJUtils {

	const STANDARD = 0;

	/**
	 * Flag to check if multisite.
	 *
	 * @var boolean
	 */
	protected $is_multisite;

	/**
	 * Constructor
	 */
	public function __construct() {
		remove_action( 'admin_notices', array( $this, 'mo_jwt_success_message' ) );
		remove_action( 'admin_notices', array( $this, 'mo_jwt_error_message' ) );
		$this->is_multisite = false;
		add_action( 'mo_clear_plug_cache', array( $this, 'manage_deactivate_cache' ) );
	}

	/**
	 * Manage cache on deactivation
	 *
	 * @return void
	 */
	public function manage_deactivate_cache() {
		$customer = new \MoJWT\Customer();
		$customer->manage_deactivate_cache();
	}


	/**
	 * Function to display success message
	 */
	public function mo_jwt_success_message() {
		$class   = 'updated';
		$message = $this->mo_jwt_get_option( \MoJWTConstants::PANEL_MESSAGE_OPTION );
		echo "<div class='" . esc_attr( $class ) . "'> <p>" . esc_attr( $message ) . '</p></div>';
	}

	/**
	 * Function to display error message
	 */
	public function mo_jwt_error_message() {
		$class   = 'error';
		$message = $this->mo_jwt_get_option( \MoJWTConstants::PANEL_MESSAGE_OPTION );
		echo "<div class='" . esc_attr( $class ) . "'> <p>" . esc_attr( $message ) . '</p></div>';
	}

	/**
	 * Function to hook success message function
	 */
	public function mo_jwt_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_jwt_error_message' ) );
		add_action( 'admin_notices', array( $this, 'mo_jwt_success_message' ) );
	}

	/**
	 * Function to check if a string is JSON
	 *
	 * @param string $string string to be verified.
	 */
	public function is_json( $string ) {
		return ( null === json_decode( $string ) ) ? false : true;
	}

	/**
	 * Function to send response
	 *
	 * @param mixed $response Json response.
	 *
	 * @return void
	 */
	public function send_json_response( $response ) {
		$code = isset( $response['code'] ) ? $response['code'] : 302;
		wp_send_json( $response, $code );
	}

	/**
	 * Function to hook error message function
	 */
	public function mo_jwt_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_jwt_success_message' ) );
		add_action( 'admin_notices', array( $this, 'mo_jwt_error_message' ) );
	}

	/**
	 * Is the customer registered?
	 */
	public function mo_jwt_is_customer_registered() {
		$email        = $this->mo_jwt_get_option( 'mo_jwt_admin_email' );
		$customer_key = $this->mo_jwt_get_option( 'mo_jwt_admin_customer_key' );
		if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Get Version of plugin.
	 *
	 * @return string
	 */
	public function get_versi_str() {
		$version_string = 'FREE';
		return $version_string;
	}

	/**
	 * Function to get the Config Object from DB
	 *
	 * @param mixed $option Value of option to be returned.
	 *
	 * @return string
	 */
	public function get_plugin_config( $option ) {
		$config = $this->mo_jwt_get_option( $option );
		return ( ! $config || empty( $config ) ) ? array() : $config;
	}

	/**
	 * Function to Update the Config Object into DB
	 *
	 * @param mixed $config Config to be saved.
	 * @param mixed $option Value of config.
	 *
	 * @return void
	 */
	public function update_plugin_config( $config, $option ) {
		$this->mo_jwt_update_option( $option, $config );
	}

	/**
	 * Function to encrypt a string.
	 *
	 * @param string $str String to be encrypted.
	 */
	public function mojwtencrypt( $str ) {
		$pass = $this->mo_jwt_get_option( 'mo_jwt_customer_token' );
		if ( ! $pass ) {
			return 'false';
		}
		$pass = str_split( str_pad( '', strlen( $str ), $pass, STR_PAD_RIGHT ) );
		$stra = str_split( $str );
		foreach ( $stra as $k => $v ) {
			$tmp        = ord( $v ) + ord( $pass[ $k ] );
			$stra[ $k ] = chr( $tmp > 255 ? ( $tmp - 256 ) : $tmp );
		}
		return base64_encode( join( '', $stra ) ); //phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode It is required to encode the JWT token.
	}

	/**
	 * Function to decrypt a sting.
	 *
	 * @param string $str String to be decrypted.
	 */
	public function mojwtdecrypt( $str ) {
		$str  = base64_decode( $str ); // phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode It is required to decode the JWT token.
		$pass = $this->mo_jwt_get_option( 'mo_jwt_customer_token' );
		if ( ! $pass ) {
			return 'false';
		}
		$pass = str_split( str_pad( '', strlen( $str ), $pass, STR_PAD_RIGHT ) );
		$stra = str_split( $str );
		foreach ( $stra as $k => $v ) {
			$tmp        = ord( $v ) - ord( $pass[ $k ] );
			$stra[ $k ] = chr( $tmp < 0 ? ( $tmp + 256 ) : $tmp );
		}
		return join( '', $stra );
	}

	/**
	 * Function to display error messages[Function to be removed for free version].
	 *
	 * @param mixed $message Error message to be displayed.
	 *
	 * @return void
	 */
	public function show_error_message_on_screen( $message ) {
		wp_die( esc_html( $message ) );
		exit;
	}

	/**
	 * Sends url with error.
	 *
	 * @param mixed $value Error value.
	 *
	 * @return void
	 */
	public function send_error_response_on_url( $value ) {
		$method_slug    = 'jwtlogin';
		$current_config = $this->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $this->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$method_config  = ( $current_config && $current_config[ $method_slug ] ) ? ( $current_config[ $method_slug ] ) : false;
		$jwt_token_name = $method_config && isset( $method_config['mo_jwt_token_name'] ) ? $method_config['mo_jwt_token_name'] : 'mo_jwt_token';

		$current_url = $this->get_current_url();
		if ( isset( $_GET[ $jwt_token_name ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
			$error_string = $jwt_token_name . '=' . sanitize_text_field( wp_unslash( $_GET[ $jwt_token_name ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce verification because we are fetching data from URL and not on form submission.
		}
		if ( strpos( $current_url, $error_string ) !== false ) {

			if ( '&' === $current_url[ ( strpos( $current_url, $error_string ) ) - 1 ] ) {
				$error_string = '&' . $error_string;
			}
			$current_url = str_replace( $error_string, '', $current_url );
		}

		$current_url = strpos( $current_url, '?' ) ? ( $current_url . '&mo_jwt_error=' . $value ) : ( $current_url . '?mo_jwt_error=' . $value );
		wp_safe_redirect( $current_url );
		exit();
	}

	/**
	 * Function to check if given value is null or empty.
	 *
	 * @param mixed $value Thing to check.
	 */
	public function mo_jwt_check_empty_or_null( $value ) {
		if ( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Is cURL installed and enabled?
	 */
	public function mo_jwt_is_curl_installed() {
		if ( in_array( 'curl', get_loaded_extensions(), true ) ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Is cURL installed and enabled?
	 */
	public function mo_jwt_show_curl_error() {
		if ( $this->mo_jwt_is_curl_installed() === 0 ) {
			$this->mo_jwt_update_option( \MoJWTConstants::PANEL_MESSAGE_OPTION, '<a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please enable it to continue.' );
			$this->mo_jwt_show_error_message();
			return;
		}
	}



	/**
	 * Get WP options.
	 *
	 * @param string $key Option to retrieve.
	 * @param string $default Option to retrieve default value.
	 * @return mixed
	 * */
	public function mo_jwt_get_option( $key, $default = false ) {
		$value = ( is_multisite() && $this->is_multisite ) ? get_site_option( $key, $default ) : get_option( $key, $default );
		if ( ! $value || $default === $value ) {
			return $default;
		}
		return $value;
	}

	/**
	 * Update WP options.
	 *
	 * @param string $key   Option to Update.
	 * @param mixed  $value Value to set.
	 * @return bool
	 * */
	public function mo_jwt_update_option( $key, $value ) {
		return ( is_multisite() && $this->is_multisite ) ? update_site_option( $key, $value ) : update_option( $key, $value );
	}

	/**
	 * Delete WP options.
	 *
	 * @param string $key Option to delete.
	 * @return mixed
	 * */
	public function mo_jwt_delete_option( $key ) {
		return ( is_multisite() && $this->is_multisite ) ? delete_site_option( $key ) : delete_option( $key );
	}


	/**
	 * Check correct version number
	 *
	 * @param int $lvl Plugin Level.
	 *
	 * @return bool
	 * */
	public function check_versi( $lvl ) {
		return ( $this->get_versi() >= $lvl );
	}

	/**
	 * Get correct Plugin Level.
	 *
	 * @return int
	 * */
	public function get_versi() {
		return self::STANDARD;
	}


	/**
	 * Premium icon for plugin.
	 *
	 * @return [type]
	 */
	public function get_label_icon() {
		return MJ_VERSION === 'mo_jwt_login_standard' ? '<img src="' . MJ_URL . '/resources/images/icons/prem.png" alt="miniOrange Premium Plans Logo" style="height:16px;width:16px;margin-bottom:-3px;">' : '';
	}

	/**
	 * Premium string for plugin.
	 *
	 * @return [type]
	 */
	public function get_label_string() {
		return MJ_VERSION === 'mo_jwt_login_standard' ? '<span style="color:red;font-weight=bold;">[PREMIUM]</span>' : '';
	}

	/**
	 * Generate Random string
	 *
	 * @param string $length Length of String to generate.
	 *
	 * @return mixed
	 * */
	public function gen_rand_str( $length = 10 ) {
		$characters        = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ random_int( 0, $characters_length - 1 ) ];
		}
		return $random_string;
	}

	/**
	 * Function to get current page URL.
	 */
	public function get_current_url_without_jwt() {
		$site_url       = $this->get_current_url();
		$parse_site_url = $this->parse_url( $site_url );
		$query_params   = $this->parse_url( $site_url )['query'];
		if ( ! empty( $query_params['mo_jwt_token'] ) ) {
			unset( $query_params['mo_jwt_token'] );
		}
		$parse_site_url['query']  = $query_params;
		$generate_url_without_jwt = $this->generate_url( $parse_site_url );
		return $generate_url_without_jwt;
	}

	/**
	 * Function to be called for array sanitization.
	 *
	 * @param mixed $array Array to be sanitized.
	 *
	 * @return array
	 */
	public function mo_array_sanitize( $array ) {

		$result_array = array_map( 'filter_var', $array );

		return $result_array;
	}

	/**
	 * URL Parser
	 *
	 * @param string $url URL to parse.
	 *
	 * @return mixed
	 */
	public function parse_url( $url ) {
		$retval          = array();
		$parts           = explode( '?', $url );
		$retval['host']  = $parts[0];
		$retval['query'] = isset( $parts[1] ) && '' !== $parts[1] ? $parts[1] : '';
		if ( empty( $retval['query'] ) || '' === $retval['query'] ) {
			return $retval;
		}
		$query_params = array();
		foreach ( explode( '&', $retval['query'] ) as $single_pair ) {
			$parts = explode( '=', $single_pair );
			if ( is_array( $parts ) && count( $parts ) === 2 ) {
				$query_params[ str_replace( 'amp;', '', $parts[0] ) ] = $parts[1];
			}
			if ( is_array( $parts ) && 'state' === $parts[0] ) {
				$parts                 = explode( 'state=', $single_pair );
				$query_params['state'] = $parts[1];
			}
		}
		$retval['query'] = is_array( $query_params ) && ! empty( $query_params ) ? $query_params : array();
		return $retval;
	}

	/**
	 * Generate URL from parsed URL
	 *
	 * @param string $url_obj URL to parse.
	 *
	 * @return string
	 */
	public function generate_url( $url_obj ) {
		if ( ! is_array( $url_obj ) || empty( $url_obj ) ) {
			return '';
		}
		if ( ! isset( $url_obj['host'] ) ) {
			return '';
		}
		$url          = $url_obj['host'];
		$query_string = '';
		$i            = 0;
		foreach ( $url_obj['query'] as $param => $value ) {
			if ( 0 !== $i ) {
				$query_string .= '&';
			}
			$query_string .= "$param=$value";
			++$i;
		}
		if ( empty( $query_string ) ) {
			return $url;
		}
		return $url . '?' . $query_string;
	}

	/**
	 * Is the customer registered?
	 */
	public function mo_jwt_customer_registered() {
		$email        = $this->mo_jwt_get_option( 'mo_jwt_admin_email' );
		$customer_key = $this->mo_jwt_get_option( 'mo_jwt_admin_customer_key' );
		if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Function to get current page URL.
	 */
	public function get_current_url() {
		if ( isset( $_SERVER['REQUEST_URI'] ) || isset( $_SERVER['HTTP_HOST'] ) ) {
			return ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
	}

	/**
	 * Activation hook.
	 */
	public function activate_plugin() {
		// This random string is used as the JWT Signing and Decryption Key.
		$get_rand_str = $this->gen_rand_str( 32 );
		// This random string is used as the API Key for mo-jwt-register endpoint.
		$get_api_key = $this->gen_rand_str( 32 );
		if ( ! $this->mo_jwt_get_option( 'mo_jwt_config_settings' ) || empty( $this->mo_jwt_get_option( 'mo_jwt_config_settings' ) ) ) {
			$mo_jwt_config_settings = array(
				'jwtlogin'    => array(
					'mo_jwt_token_validation_method' => 'signing_key',
					'mo_jwt_redirection'             => 'home_redirect',
					'mo_jwt_get_token_from_url'      => 'on',
				),
				'jwtcreate'   => array(
					'mo_jwt_sign_algo'    => 'HS256',
					'mo_jwt_secret'       => $get_rand_str,
					'mo_jwt_dec_secret'   => $get_rand_str,
					'mo_jwt_token_expiry' => 60,
				),
				'jwtregister' => array(
					'mo_jwt_register_role' => 'subscriber',
					'mo_jwt_allow_role'    => '0',
					'mo_jwt_api_key'       => $get_api_key,
				),
				'jwtdelete'   => array(),
			);
			$this->mo_jwt_update_option( 'mo_jwt_config_settings', $mo_jwt_config_settings );
		}
		if ( ! $this->mo_jwt_get_option( 'mo_jwt_attr_settings' ) || empty( $this->mo_jwt_get_option( 'mo_jwt_attr_settings' ) ) ) {
			$mo_jwt_attr_settings = array(
				'default' => array(
					'username' => 'username',
				),
			);
			$this->mo_jwt_update_option( 'mo_jwt_attr_settings', $mo_jwt_attr_settings );
		}
	}

	/**
	 * Upgrades the plugin config on update.
	 *
	 * This function is triggered after a plugin update and performs necessary actions.
	 *
	 * @return void
	 */
	public function upgrade_plugin() {
		if ( $this->mo_jwt_get_option( 'mo_jwt_config_settings' ) || ! empty( $this->mo_jwt_get_option( 'mo_jwt_config_settings' ) ) ) {
			$get_api_key            = $this->gen_rand_str( 32 );
			$mo_jwt_config_settings = $this->mo_jwt_get_option( 'mo_jwt_config_settings' );
			$mo_jwt_config_settings['jwtregister']['mo_jwt_api_key'] = $get_api_key;
			$this->mo_jwt_update_option( 'mo_jwt_config_settings', $mo_jwt_config_settings );
		}
	}

	/**
	 * Set transients.
	 *
	 * @param mixed $key Option to be set.
	 * @param mixed $value Value of option to be set.
	 * @param mixed $time Time for setting value.
	 *
	 * @return mixed
	 */
	public function mo_jwt_set_transient( $key, $value, $time ) {
		return set_transient( $key, $value, $time );
	}

	/**
	 * Get WP Transient.
	 *
	 * @param string $key   Option to get.
	 * @return bool
	 * */
	public function mo_jwt_get_transient( $key ) {
		return get_transient( $key );
	}

	/**
	 * Delete WP Transient.
	 *
	 * @param string $key   Option to get.
	 * @return bool
	 * */
	public function mo_jwt_delete_transient( $key ) {
		return delete_transient( $key );
	}

	/**
	 * Deactivation hook.
	 */
	public function deactivate_plugin() {
		$this->mo_jwt_delete_option( 'mo_jwt_host_name' );
		$this->mo_jwt_delete_option( 'mo_jwt_new_registration' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_email' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_phone' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_fname' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_lname' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_company' );
		$this->mo_jwt_delete_option( \MoJWTConstants::PANEL_MESSAGE_OPTION );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_customer_key' );
		$this->mo_jwt_delete_option( 'mo_jwt_admin_api_key' );
		$this->mo_jwt_delete_option( 'mo_jwt_new_customer' );
		$this->mo_jwt_delete_option( 'mo_jwt_registration_status' );
		$this->mo_jwt_delete_option( 'mo_jwt_customer_token' );
		$this->mo_jwt_delete_option( 'mo_jwt_lk' );
		$this->mo_jwt_delete_option( 'mo_jwt_lv' );
	}

	/**
	 * Base64 Url encode
	 *
	 * @param string $data to encode.
	 */
	public function base64url_encode( $data ) {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' ); //phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode It is required to encode the URL.
	}

	/**
	 * Base64 Url decode
	 *
	 * @param string $data to decode.
	 */
	public function base64url_decode( $data ) {
		return base64_decode( str_pad( strtr( $data, '-_', '+/' ), strlen( $data ) % 4, '=', STR_PAD_RIGHT ) ); // phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode It is required to decode the URL.
	}


}
