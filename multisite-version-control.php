<?php
/**
 * Plugin Name: Multisite Version Control
 * Description: A plugin for version control across WordPress multisite networks, with backup and update management.
 * Version: 1.2
 * Author: aym
 * Author URI: https://www.aymscores.com
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

// Admin Menu Setup in the Network Admin.
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

// Render the admin settings page.
function mvc_render_admin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Multisite Version Control Settings</h1>
        <p>Manage the settings for version control on your WordPress Multisite network.</p>
    </div>
    <?php
}

// Enqueue admin assets.
function mvc_enqueue_admin_assets($hook_suffix) {
    // Load CSS and JS only on the plugin settings page.
    if ($hook_suffix === 'settings_page_mvc-settings') {
        wp_enqueue_style('mvc-admin-css', plugins_url('assets/css/admin.css', __FILE__));
        wp_enqueue_script('mvc-admin-js', plugins_url('assets/js/admin.js', __FILE__));
    }
}
add_action('admin_enqueue_scripts', 'mvc_enqueue_admin_assets');

// Include the Plugin Update Checker library.
if (file_exists(MVC_PLUGIN_DIR . 'includes/plugin-update-checker-5.5/plugin-update-checker.php')) {
    require MVC_PLUGIN_DIR . 'includes/plugin-update-checker-5.5/plugin-update-checker.php';

    // Initialize the update checker.
    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\Puc_v4_Factory::buildUpdateChecker(
        'https://github.com/ezraaym/multisite-version-control.git', // Replace this URL with your GitHub repo URL.
        __FILE__,
        'multisite-version-control'
    );

    // Use the "releases" endpoint to fetch GitHub releases.
    $updateChecker->setBranch('main'); // Set the branch you are using.
    $updateChecker->getVcsApi()->enableReleaseAssets();
}
// Include the Plugin Update Checker library.
require MVC_PLUGIN_DIR . 'includes/plugin-update-checker-5.5/plugin-update-checker.php';

// Initialize the update checker.
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/ezraaym/multisite-version-control.git', // Your GitHub repository URL.
    __FILE__, // Path to the main plugin file.
    'multisite-version-control' // Unique plugin slug.
);

// Set the release branch (e.g., 'main').
$updateChecker->setBranch('main');

// Enable fetching release assets from GitHub.
$updateChecker->getVcsApi()->enableReleaseAssets();

// (Optional) Enable debugging to see more information.
$updateChecker->setDebugMode(true);

// Include the Plugin Update Checker library.
require MVC_PLUGIN_DIR . 'includes/plugin-update-checker-5.5/plugin-update-checker.php';

// Initialize the update checker.
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/ezraaym/multisite-version-control/', // Your GitHub repository URL
    __FILE__, // Path to the main plugin file
    'multisite-version-control' // Unique plugin slug
);

// Set the release branch (e.g., 'main').
$updateChecker->setBranch('main');

// Enable fetching release assets from GitHub.
$updateChecker->getVcsApi()->enableReleaseAssets();

// Enable debug mode to see more information.
$updateChecker->setDebugMode(true);

