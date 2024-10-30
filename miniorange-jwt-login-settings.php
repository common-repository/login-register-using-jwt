<?php //phpcs:ignore -- Cannot change the main file name.
/**
 * Plugin Name: WP Login & Register using JWT
 * Plugin URI: http://miniorange.com
 * Description: WordPress Login and register using JWT plugin allows the functionality to auto login, auto register into WordPress using the JWT token.
 * Version: 2.8.0
 * Author: miniOrange
 * Author URI: https://www.miniorange.com
 * License: MIT/Expat
 *
 * @package MoJWT
 */  //phpcs:ignore -- Cannot change the plugin name.

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require '_autoload.php';
use MoJWT\Base\BaseStructure;
use MoJWT\Base\InstanceHelper;
use MoJWT\JWTFlowHandler;

global $mj_util;
$instance_helper             = new InstanceHelper();
$base                        = new BaseStructure();
$mj_util                     = $instance_helper->get_utils_instance();
$settings                    = $instance_helper->get_settings_instance();
$jwt_flow_handler            = $instance_helper->jwt_flow_handler();
$config_settings_all_methods = $instance_helper->get_all_method_instances();
mo_jwt_load_all_methods( $config_settings_all_methods );

/**
 * Activate hook.
 *
 * @return void
 */
function mo_jwt_activate() {
	global $mj_util;
	// Add default option structure.
	$mj_util->activate_plugin();
}
register_activation_hook( __FILE__, 'mo_jwt_activate' );

/**
 * Deactivate hook.
 *
 * @return void
 */
function mo_jwt_deactivate() {
	global $mj_util;
	// delete all stored key-value pairs.
	do_action( 'mo_clear_plug_cache' );
	$mj_util->deactivate_plugin();
}
register_deactivation_hook( __FILE__, 'mo_jwt_deactivate' );
