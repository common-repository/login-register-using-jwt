<?php
/**
 * Utils
 *
 * JWT Utils.
 *
 * @category   Core, Helper
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

use MoJWT\JWSVerify;
use MoJWT\Crypt_RSA;

/**
 * Class to Handle JWT Operations
 *
 * @category Core, Helpers
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWTUtils {

	const HEADER  = 'HEADER';
	const PAYLOAD = 'PAYLOAD';
	const SIGN    = 'SIGN';

	/**
	 * Concerned JWT.
	 *
	 * @var string JWT to utilize.
	 */
	private $jwt;

	/**
	 * Decoded JWT.
	 *
	 * @var array Decoded JWT.
	 */
	private $decoded_jwt;


	/**
	 * Constructor.
	 *
	 * @param string $jwt JWT to initialize.
	 */
	public function __construct( $jwt = '' ) {
		$jwt = \explode( '.', $jwt );

		if ( 3 !== count( $jwt ) ) {
			$jwt_error = new \WP_Error();
			$jwt_error->add( 'invalid_token', __( 'Invalid JWT format' ) );
			return $jwt_error;
		}
		$this->jwt         = $jwt;
		$header            = $this->get_jwt_claim( '', self::HEADER );
		$payload           = $this->get_jwt_claim( '', self::PAYLOAD );
		$this->decoded_jwt = array(
			'header'  => $header,
			'payload' => $payload,
		);
	}

	/**
	 * Function to get decoded JWT Header as Array.
	 *
	 * @param string $claim Header claim to return.
	 * @param string $part  Part of JWT.
	 *
	 * @return string
	 */
	private function get_jwt_claim( $claim = '', $part = '' ) {
		$target = '';
		if ( empty( $this->jwt ) ) {
			return false;
		}
		switch ( $part ) {
			case self::HEADER:
				$target = $this->jwt[0];
				break;
			case self::PAYLOAD:
				$target = $this->jwt[1];
				break;
			case self::SIGN:
				return $this->jwt[2];
		}
		$target = json_decode( base64_decode( $target ), true ); // phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode It is required to decode the JWT token.
		if ( ! $target || empty( $target ) ) {
			return null;
		}
		return empty( $claim ) ? $target : ( isset( $target[ $claim ] ) ? $target[ $claim ] : null );
	}


	/**
	 * Wrapper for JWSVerify class
	 *
	 * @param string $allowed_algo Algorithm Allowed by the application.
	 *
	 * @return bool
	 */
	public function check_algo( $allowed_algo = '' ) {
		$algo = $this->get_jwt_claim( 'alg', self::HEADER );
		if ( is_string( $algo ) ) {
			$algo = explode( 'S', $algo );
		} else {
			return false;
		}
		if ( ! isset( $algo[0] ) ) {
			return false;
		}
		switch ( $algo[0] ) {
			case 'H':
				return ( 'HS256' === $allowed_algo );
			case 'R':
				return ( 'RS256' === $allowed_algo );
			default:
				return false;
		}
	}
	/**
	 * Wrapper for JWSVerify class
	 *
	 * @param string $secret Client Secret or Certificate.
	 *
	 * @param bool   $check_expiry Check the expiry.
	 *
	 * @return bool
	 */
	public function verify( $secret = '', $check_expiry = true ) {

		if ( empty( $secret ) ) {
			return false;
		}

		// exp claim check.
		$expiry = $this->get_jwt_claim( 'exp', self::PAYLOAD );

		if ( $check_expiry ) {
			if ( is_null( $expiry ) || time() > $expiry ) {

				return false;
			}
		}
		// Sign Check.
		$sign_verify       = new JWSVerify( $this->get_jwt_claim( 'alg', self::HEADER ) );
		$payload_to_verify = $this->get_header() . '.' . $this->get_payload();

		return $sign_verify->verify(
			\mb_convert_encoding( $payload_to_verify, 'ISO-8859-1', 'UTF-8' ),
			$secret,
			base64_decode( strtr( $this->get_jwt_claim( false, self::SIGN ), '-_', '+/' ) ) // phpcs:ignore --WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode It is required to decode the JWT token.
		);
	}

	/**
	 * Getter for decoded header
	 *
	 * @return array
	 */
	public function get_decoded_header() {
		return $this->decoded_jwt['header'];
	}

	/**
	 * Getter for decoded payload
	 *
	 * @return array
	 */
	public function get_decoded_payload() {
		return $this->decoded_jwt['payload'];
	}

	/**
	 * Getter for header
	 *
	 * @return string
	 */
	public function get_header() {
		return $this->jwt[0];
	}

	/**
	 * Getter for payload
	 *
	 * @return string
	 */
	public function get_payload() {
		return $this->jwt[1];
	}
	/**
	 * Create jwt response
	 *
	 * @param mixed  $jwt_user    User doing jwt login.
	 * @param mixed  $jwt_secret  Jwt secret.
	 * @param mixed  $jwt_algo    Jwt algo.
	 * @param mixed  $expiry      Expiry date.
	 * @param string $private_key Private key for jwt.
	 *
	 * @return array
	 */
	public function create_jwt_token( $jwt_user, $jwt_secret, $jwt_algo, $expiry, $private_key = '' ) {
		global $mj_util;

		$expiry_in_sec = 60 * intval( $expiry );

		$iat = time();
		$exp = time() + $expiry_in_sec;

		$header = wp_json_encode(
			array(
				'alg' => $jwt_algo,
				'typ' => 'JWT',
			)
		);

		$payload = wp_json_encode(
			array_merge(
				$jwt_user,
				array(
					'iat' => $iat,
					'exp' => $exp,
				)
			)
		);

		$base64_encoded_header  = $mj_util->base64url_encode( $header );
		$base64_encoded_payload = $mj_util->base64url_encode( $payload );

		$jwt = '';

		// Signature Creation.
		if ( 'HS256' === $jwt_algo ) {
			// Create Signature Hash.
			$signature = hash_hmac( 'sha256', $base64_encoded_header . '.' . $base64_encoded_payload, $jwt_secret, true );

			// Encode Signature to Base64Url String.
			$base64_encoded_signature = $mj_util->base64url_encode( $signature );

			// Create JWT.
			$jwt = $base64_encoded_header . '.' . $base64_encoded_payload . '.' . $base64_encoded_signature;

		} elseif ( 'RS256' === $jwt_algo ) {

			// Signing Input.
			$signing_input = $base64_encoded_header . '.' . $base64_encoded_payload;

			// Create Signature.
			openssl_sign( $signing_input, $signature, $private_key, OPENSSL_ALGO_SHA256 );

			// Encode Signature to Base64Url String.
			$base64_encoded_signature = $mj_util->base64url_encode( $signature );

			// Create JWT.
			$jwt = $signing_input . '.' . $base64_encoded_signature;
		}

		$jwt_response = array(
			'token_type' => 'Bearer',
			'iat'        => $iat,
			'expires_in' => $exp,
			'jwt_token'  => $jwt,
		);

		return $jwt_response;
	}

	/**
	 * Wrapper for JWSVerify class.
	 *
	 * @param string $jwks_uri JWKS URI.
	 * @param string $algo     Signing Algo.
	 *
	 * @return bool
	 */
	public function verify_from_jwks( $jwks_uri = '', $algo = 'RS256' ) {
		global $mj_util;

		$jwks_resposnse = wp_remote_get( $jwks_uri );
		if ( is_wp_error( $jwks_resposnse ) ) {
			return false;
		}

		$jwks_resposnse = json_decode( $jwks_resposnse['body'], true );

		$verified = false;
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return $verified;
		}

		if ( ! isset( $jwks_resposnse['keys'] ) ) {
			return $verified;
		}

		foreach ( $jwks_resposnse['keys'] as $key => $value ) {
			if ( ! isset( $value['kty'] ) || 'RSA' !== $value['kty'] || ! isset( $value['e'] ) || ! isset( $value['n'] ) ) {
				continue;
			}

			$verified = $verified || $this->verify(
				$this->jwks_to_pem(
					array(
						'n' => new Math_BigInteger( $mj_util->base64url_decode( $value['n'] ), 256 ),
						'e' => new Math_BigInteger( $mj_util->base64url_decode( $value['e'] ), 256 ),
					)
				)
			);

			if ( true === $verified ) {
				break;
			}
		}

		return $verified;
	}

	/**
	 * Wrapper for Converting JWKS to pem.
	 *
	 * @param array $jwks JWKS format.
	 *
	 * @return string
	 */
	private function jwks_to_pem( $jwks = array() ) {
		$rsa = new Crypt_RSA();
		$rsa->loadKey( $jwks );
		return $rsa->getPublicKey();
	}

}

