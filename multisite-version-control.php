<?php
/**
 * Plugin Name: Multisite Version Control
 * Description: A plugin for version control across WordPress multisite networks, with backup and update management.
 * Version: 1.6.5
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
define( 'MVC_PLUGIN_FILE', __FILE__ );
define( 'MVC_VERSION', '1.6.5' );
define( 'MVC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MVC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MVC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'MVC_PLUGIN_SLUG', basename( __FILE__, '.php' ) );

// Include the autoloader and core files.
require_once MVC_PLUGIN_DIR . 'includes/autoload/autoload.php';
include_once MVC_PLUGIN_DIR . 'includes/backup/backup-manager.php';
include_once MVC_PLUGIN_DIR . 'includes/version-history/version-history.php';
include_once MVC_PLUGIN_DIR . 'includes/database/database-tracker.php';

// Initialize the Version History class.
if ( class_exists( '\MVC\Version\Version_History' ) ) {
    new \MVC\Version\Version_History();
} else {
    error_log( '[MVC] Failed to load Version_History class.' );
}

// Initialize the Database Tracker class.
if ( class_exists( '\MVC\Database\Database_Tracker' ) ) {
    new \MVC\Database\Database_Tracker();
} else {
    error_log( '[MVC] Failed to load Database_Tracker class.' );
}

// Initialize the Backup Manager class.
if ( class_exists( '\MVC\Backup\Backup_Manager' ) ) {
    new \MVC\Backup\Backup_Manager();
} else {
    error_log( '[MVC] Failed to load Backup_Manager class.' );
}

// Include admin page setup.
require_once MVC_PLUGIN_DIR . 'includes/admin.php';

// Add a settings page to the network admin menu.
add_action( 'network_admin_menu', 'mvc_add_backup_settings_page' );
function mvc_add_backup_settings_page() {
    add_menu_page(
        'MVC Backup Settings',
        'Backup Settings',
        'manage_network',
        'mvc-backup-settings',
        'mvc_render_backup_settings_page',
        'dashicons-database',
        99
    );
}

// Render the backup settings page.
function mvc_render_backup_settings_page() {
    $backup_destination = get_option( 'mvc_backup_destination', 'local' );
    $ftp_details = get_option( 'mvc_ftp_details', [] );

    ?>
    <div class="wrap">
        <h1>MVC Backup Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'mvc_backup_options' );
            do_settings_sections( 'mvc_backup_settings' );
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Backup Destination</th>
                    <td>
                        <select name="mvc_backup_destination" id="mvc_backup_destination">
                            <option value="local" <?php selected( $backup_destination, 'local' ); ?>>Local Storage</option>
                            <option value="ftp" <?php selected( $backup_destination, 'ftp' ); ?>>FTP Server</option>
                            <option value="s3" <?php selected( $backup_destination, 's3' ); ?>>AWS S3</option>
                        </select>
                    </td>
                </tr>
                <tr class="ftp-details">
                    <th scope="row">FTP Details</th>
                    <td>
                        <input type="text" name="mvc_ftp_details[host]" placeholder="FTP Host" value="<?php echo esc_attr( $ftp_details['host'] ?? '' ); ?>">
                        <input type="text" name="mvc_ftp_details[user]" placeholder="FTP User" value="<?php echo esc_attr( $ftp_details['user'] ?? '' ); ?>">
                        <input type="password" name="mvc_ftp_details[pass]" placeholder="FTP Password" value="<?php echo esc_attr( $ftp_details['pass'] ?? '' ); ?>">
                    </td>
                </tr>
            </table>
            <input type="submit" class="button-primary" value="Save Settings">
        </form>
    </div>
    <?php
}

// Register settings.
add_action( 'admin_init', 'mvc_register_backup_settings' );
function mvc_register_backup_settings() {
    register_setting( 'mvc_backup_options', 'mvc_backup_destination' );
    register_setting( 'mvc_backup_options', 'mvc_ftp_details' );
}

// Include updater.
require_once MVC_PLUGIN_DIR . 'includes/updater/updater.php';

// Initialize the updater.
add_action( 'plugins_loaded', 'mvc_initialize_updater' );
function mvc_initialize_updater() {
    if ( class_exists( '\MVC\Updater\Updater' ) ) {
        new \MVC\Updater\Updater();
    }
}

// Activation and Deactivation Hooks.
register_activation_hook( __FILE__, 'mvc_activate_plugin' );
register_deactivation_hook( __FILE__, 'mvc_deactivate_plugin' );
function mvc_activate_plugin() {
    // Placeholder for activation code.
}
function mvc_deactivate_plugin() {
    // Placeholder for deactivation code.
}

// Load additional hooks and actions.
add_action( 'init', 'mvc_load_hooks' );
function mvc_load_hooks() {
    // Placeholder for additional hooks.
}
