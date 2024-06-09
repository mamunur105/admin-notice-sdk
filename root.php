<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Admin Notice SDK
 * Plugin URI:        https://wordpress.org/plugins/admin-notice-sdk
 * Description:       Admin Notice SDK
 * Version:           0.0.1
 * Author:            Tiny Solutions
 * Author URI:        https://www.wptinysolutions.com/
 * Text Domain:       tiny_sdk
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * @package TinySolutions\mlt
 */

// Do not allow directly accessing this file.
use TinySolutions\SDK\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Define media edit Constant.
 */
define( 'SDK_VERSION', '0.0.1' );

define( 'SDK_FILE', __FILE__ );

define( 'SDK_BASENAME', plugin_basename( SDK_FILE ) );

define( 'SDK_URL', plugins_url( '', SDK_FILE ) );

define( 'SDK_ABSPATH', dirname( SDK_FILE ) );

define( 'SDK_PATH', plugin_dir_path( __FILE__ ) );

/**
 * App Init.
 */

require_once SDK_PATH . 'vendor/autoload.php';

/**
 * @return Plugin
 */
function tiny_sdk() {
	return TinySolutions\SDK\Plugin::instance();
}
add_action( 'plugins_loaded', 'tiny_sdk' );
