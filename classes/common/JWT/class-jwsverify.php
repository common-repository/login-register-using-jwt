<?php //phpcs:ignoreFile
/**
 * Utils
 *
 * JWS Verifier.
 *
 * @category   Core, Helper
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

/**
 * Class to Handle JWT Signature Operations
 *
 * @category Core, Helpers
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class JWSVerify {

	/**
	 * Signing Algo Config.
	 *
	 * @var array
	 */
	public $algo;

	/**
	 * Constructor
	 *
	 * @param string $alg Algorithm to prepare for.
	 *
	 * @return mixed
	 */
	public function __construct( $alg = '' ) {
		if ( empty( $alg ) ) {
			return;
		}
		$alg = explode( 'S', $alg );
		if ( ! is_array( $alg ) || 2 !== count( $alg ) ) {
			return new \WP_Error( 'invalid_signature', __( 'The Signature seems to be invalid or unsupported.' ) );
		}
		if ( 'H' === $alg[0] ) {
			$this->algo['alg'] = 'HSA';
		} elseif ( 'R' === $alg[0] ) {
			$this->algo['alg'] = 'RSA';
		} else {
			return new \WP_Error( 'invalid_signature', __( 'The signature algorithm seems to be unsupported or invalid.' ) );
		}
		$this->algo['sha'] = $alg[1];
	}

	/**
	 * Internal function to verify HSA Signs.
	 *
	 * @param string $payload_to_verify Payload to compute sign from.
	 * @param string $secret            Secret Key.
	 * @param string $sign              Sign to verify.
	 *
	 * @return mixed
	 */
	private function validate_hmac( $payload_to_verify = '', $secret = '', $sign = '' ) {
		if ( empty( $payload_to_verify ) || empty( $sign ) ) {
			return false;
		}

		$sha      = $this->algo['sha'];
		$sha      = 'sha' . $sha;
		$new_sign = \hash_hmac( $sha, $payload_to_verify, $secret, true );
		return hash_equals( $new_sign, $sign );
	}

	/**
	 * Internal function to verify RSA Signs.
	 *
	 * @param string $payload_to_verify Payload to compute sign from.
	 * @param string $raw_cert          Secret Key.
	 * @param string $sign              Sign to verify.
	 *
	 * @return mixed
	 */
	private function validate_rsa( $payload_to_verify = '', $raw_cert = '', $sign = '' ) {
		if ( empty( $payload_to_verify ) || empty( $sign ) ) {
			return false;
		}
		$sha        = $this->algo['sha'];
		$public_key = '';
		$parts      = explode( '-----', $raw_cert );
		if ( preg_match( '/\r\n|\r|\n/', $parts[2] ) ) {
			$public_key = $raw_cert;
		} else {
			$encoding = '-----' . $parts[1] . "-----\n";
			$offset   = 0;
			while ( $segment = substr( $parts[2], $offset, 64 ) ) {
				$encoding .= $segment . "\n";
				$offset   += 64;
			}
			$encoding  .= '-----' . $parts[3] . "-----\n";
			$public_key = $encoding;
		}
		$verified = false;
		switch ( $sha ) {
			case '256':
				$verified = openssl_verify( $payload_to_verify, $sign, $public_key, OPENSSL_ALGO_SHA256 );
				break;
			case '384':
				$verified = openssl_verify( $payload_to_verify, $sign, $public_key, OPENSSL_ALGO_SHA384 );
				break;
			case '512':
				$verified = openssl_verify( $payload_to_verify, $sign, $public_key, OPENSSL_ALGO_SHA512 );
				break;
			default:
				$verified = false;
				break;
		}
		return $verified;
	}

	/**
	 * Internal function to verify Signs.
	 *
	 * @param string $payload_to_verify Payload to compute sign from.
	 * @param string $secret            Raw Certificate or Secret Key.
	 * @param string $sign              Sign to verify.
	 * @param bool   $verify_expiry     Sign to verify.
	 * 
	 * @return mixed
	 */
	public function verify( $payload_to_verify = '', $secret = '', $sign = '', $verify_expiry = true ) {

		if ( empty( $payload_to_verify ) || empty( $sign ) ) {
			return false;
		}

		$alg = $this->algo['alg'];
		switch ( $alg ) {
			case 'HSA':
				return $this->validate_hmac( $payload_to_verify, $secret, $sign );
			case 'RSA':
				return @$this->validate_rsa( $payload_to_verify, $secret, $sign );
			default:
				return false;
		}
	}
}

