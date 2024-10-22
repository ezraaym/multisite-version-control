<?php
/**
 * Plugin Name: Multisite Version Control
 * Description: A plugin for version control across WordPress multisite networks, with backup and update management.
 * Version: 1.0
 * Author: Your Name
 * Network: true
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('MVC_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include core files.
include_once MVC_PLUGIN_DIR . 'includes/version-control.php';
include_once MVC_PLUGIN_DIR . 'includes/database-tracker.php';
include_once MVC_PLUGIN_DIR . 'includes/backup-manager.php';
include_once MVC_PLUGIN_DIR . 'includes/auto-updater.php';
include_once MVC_PLUGIN_DIR . 'includes/helpers.php';

// Activate/Deactivate hooks.
register_activation_hook(__FILE__, 'mvc_activate_plugin');
register_deactivation_hook(__FILE__, 'mvc_deactivate_plugin');

function mvc_activate_plugin() {
    // Code to run on plugin activation.
    mvc_setup_initial_config();
}

function mvc_deactivate_plugin() {
    // Code to run on plugin deactivation.
    mvc_cleanup_plugin();
}

// Admin Menu Setup.
add_action('network_admin_menu', 'mvc_add_admin_menu');
function mvc_add_admin_menu() {
    add_menu_page(
        'Multisite Version Control',
        'Version Control',
        'manage_network',
        'mvc-settings',
        'mvc_render_admin_settings_page',
        'dashicons-shield',
        99
    );
}

// Load assets.
function mvc_enqueue_admin_assets() {
    wp_enqueue_style('mvc-admin-css', plugins_url('assets/css/admin.css', __FILE__));
    wp_enqueue_script('mvc-admin-js', plugins_url('assets/js/admin.js', __FILE__));
}
add_action('admin_enqueue_scripts', 'mvc_enqueue_admin_assets');

?>
