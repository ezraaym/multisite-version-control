<?php
/**
 * Plugin Name: Multisite Version Control
 * Description: A plugin for version control across WordPress multisite networks, with backup and update management.
 * Version: 1.4
 * Author: aym
 * Author URI: https://www.aymscores.com
 * Plugin URI: https://www.aymscores.com
 * Network: true
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'MVC_VERSION', '1.0' );
define( 'MVC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MVC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MVC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'MVC_PLUGIN_SLUG', basename( __FILE__, '.php' ) );

// Include the autoloader.
require_once MVC_PLUGIN_DIR . 'includes/autoload.php';

// Include admin page setup.
require_once MVC_PLUGIN_DIR . 'includes/admin.php';

// Include updater.
require_once MVC_PLUGIN_DIR . 'includes/updater/updater.php';

// Initialize the updater.
function mvc_initialize_updater() {
    if ( class_exists( 'MVC\Updater\Updater' ) ) {
        new \MVC\Updater\Updater();
    }
}
add_action( 'plugins_loaded', 'mvc_initialize_updater' );

// Activation and Deactivation Hooks.
register_activation_hook( __FILE__, 'mvc_activate_plugin' );
register_deactivation_hook( __FILE__, 'mvc_deactivate_plugin' );

/**
 * Plugin activation function.
 */
function mvc_activate_plugin() {
    // Placeholder for activation code.
}

/**
 * Plugin deactivation function.
 */
function mvc_deactivate_plugin() {
    // Placeholder for deactivation code.
}

// Load additional hooks and actions.
function mvc_load_hooks() {
    // Placeholder for additional hooks.
}
add_action( 'init', 'mvc_load_hooks' );
