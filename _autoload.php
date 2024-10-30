<?php // phpcs:ignore File name cannot be changed
/**
 * Main autoload file.
 *
 * @package MoJWT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MJ_DIR', plugin_dir_path( __FILE__ ) );
define( 'MJ_URL', plugin_dir_url( __FILE__ ) );
define( 'MJ_PLUGIN_VERSION', '2.8.0' );
define( 'MJ_VERSION', 'mo_jwt_login_standard' );

mo_jwt_include_file( MJ_DIR . '/classes/common' );
mo_jwt_include_file( MJ_DIR . '/classes/Free' );

if ( 'mo_jwt_login_premium' === MJ_VERSION ) {
	mo_jwt_include_file( MJ_DIR . '/classes/Premium' );
}

/**
 * Traverse all sub-directories for files.
 *
 * Get all files in a directory.
 *
 * @param string $folder Folder to Traverse.
 * @param Array  $results Array of files to append to.
 * @return Array $results Array of files found.
 **/
function mo_jwt_get_dir_contents( $folder, &$results = array() ) {
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder, RecursiveDirectoryIterator::KEY_AS_PATHNAME ), RecursiveIteratorIterator::CHILD_FIRST ) as $file => $info ) {
		if ( $info->isFile() && $info->isReadable() ) {
			$results[ $file ] = realpath( $info->getPathname() );
		}
	}
	return $results;
}

/**
 * Order all php files.
 *
 * Get all php files to require() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return Array Array of php files to require.
 **/
function mo_jwt_get_sorted_files( $folder ) {
	$filepaths  = mo_jwt_get_dir_contents( $folder );
	$interfaces = array();
	$classes    = array();

	foreach ( $filepaths as $file => $filepath ) {
		if ( strpos( $filepath, '.php' ) !== false ) {
			if ( strpos( $filepath, 'Interface' ) !== false ) {
				$interfaces[ $file ] = $filepath;
			} else {
				$classes[ $file ] = $filepath;
			}
		}
	}

	return array(
		'interfaces' => $interfaces,
		'classes'    => $classes,
	);
}

/**
 * Wrapper for require_all().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param string $folder Folder to Traverse.
 * @return void
 **/
function mo_jwt_include_file( $folder ) {
	if ( ! is_dir( $folder ) ) {
		return;
	}
	$folder   = mo_jwt_sane_dir_path( $folder );
	$realpath = realpath( $folder );
	if ( false !== $realpath && ! is_dir( $folder ) ) {
		return;
	}
	$sorted_elements = mo_jwt_get_sorted_files( $folder );
	mo_jwt_require_all( $sorted_elements['interfaces'] );
	mo_jwt_require_all( $sorted_elements['classes'] );
}

/**
 * All files given as input are passed to require_once().
 *
 * Wrapper to call require_all() in perfect order.
 *
 * @param Array $filepaths array of files to require.
 * @return void
 **/
function mo_jwt_require_all( $filepaths ) {
	foreach ( $filepaths as $file => $filepath ) {
		require_once $filepath;
	}
}

/**
 * Validate file paths
 *
 * File names passed are validated to be as required
 *
 * @param string $filename filepath to validate.
 * @return bool validity of file.
 **/
function mo_jwt_is_valid_file( $filename ) {
	return '' !== $filename && '.' !== $filename && '..' !== $filename;
}



/**
 * Get Version number
 */
function mo_jwt_get_version_number() {
	$file_data      = get_file_data( MJ_DIR . '/miniorange-jwt-login-settings.php', array( 'Version' ), 'plugin' );
	$plugin_version = isset( $file_data[0] ) ? $file_data[0] : '';
	return $plugin_version;
}

/**
 * Function to sanitize dir paths.
 *
 * @param string $folder Dir Path to sanitize.
 *
 * @return string sane path.
 */
function mo_jwt_sane_dir_path( $folder ) {
	return str_replace( '/', DIRECTORY_SEPARATOR, $folder );
}

/**
 * Function to load all methods.
 *
 * @param array $all_methods Name of all methods.
 *
 * @return void
 */
function mo_jwt_load_all_methods( $all_methods ) {
	foreach ( $all_methods as $method ) {
		new $method();
	}
}
