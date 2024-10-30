<?php
/**
 * Un-installation file.
 *
 * @package MoJWT
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'mo_jwt_host_name' );
delete_option( 'mo_jwt_admin_email' );
delete_option( 'mo_jwt_admin_phone' );
delete_option( 'mo_verify_customer' );
delete_option( 'mo_jwt_admin_customer_key' );
delete_option( 'mo_jwt_admin_api_key' );
delete_option( 'mo_jwt_customer_token' );
delete_option( 'mo_jwt_new_customer' );
delete_option( 'message' );
delete_option( 'mo_jwt_new_registration' );
delete_option( 'mo_jwt_registration_status' );

