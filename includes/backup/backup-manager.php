<?php

namespace MVC\Backup;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Backup_Manager {

    private $backup_dir;

    public function __construct() {
        $this->backup_dir = WP_CONTENT_DIR . '/mvc-backups/';
        $this->setup_hooks();
        $this->create_backup_directory();
    }

    private function setup_hooks() {
        // Hook to trigger manual backup via admin action
        add_action( 'admin_post_mvc_trigger_backup', [ $this, 'handle_backup_request' ] );
    }

    private function create_backup_directory() {
        if ( ! file_exists( $this->backup_dir ) ) {
            wp_mkdir_p( $this->backup_dir );
        }
    }

    // Create a local backup of the plugin files
    public function backup_to_local() {
        $backup_file = $this->backup_dir . 'local-backup-' . date( 'Y-m-d-H-i-s' ) . '.zip';
        
        $zip = new \ZipArchive();
        if ( $zip->open( $backup_file, \ZipArchive::CREATE ) === true ) {
            $plugin_dir = plugin_dir_path( __DIR__ );
            $this->add_files_to_zip( $plugin_dir, $zip );
            $zip->close();
            error_log( '[MVC Backup] Local backup created: ' . $backup_file );
            return $backup_file; // Return the backup file path
        } else {
            error_log( '[MVC Backup] Failed to create local backup zip file: ' . $backup_file );
            return false;
        }
    }

    // Function to handle backup request from admin
    public function handle_backup_request() {
        // Check nonce
        if ( ! isset( $_POST['mvc_backup_nonce'] ) || ! wp_verify_nonce( $_POST['mvc_backup_nonce'], 'mvc_backup_now' ) ) {
            $this->add_admin_notice( 'Invalid request.', 'error' );
            wp_redirect( add_query_arg( [ 'page' => 'mvc-backup-settings' ], admin_url( 'network/admin.php' ) ) );
            exit;
        }

        // Perform the backup
        if ( $this->backup_to_local() ) {
            $this->add_admin_notice( 'Backup successfully created!', 'success' );
        } else {
            $this->add_admin_notice( 'Failed to create backup!', 'error' );
        }

        // Redirect back to the settings page
        wp_redirect( add_query_arg( [ 'page' => 'mvc-backup-settings' ], admin_url( 'network/admin.php' ) ) );
        exit;
    }

    // Function to add files to zip archive
    private function add_files_to_zip( $dir, $zip, $relative_path = '' ) {
        $files = scandir( $dir );
        foreach ( $files as $file ) {
            if ( $file === '.' || $file === '..' ) {
                continue;
            }

            $file_path = $dir . DIRECTORY_SEPARATOR . $file;
            $zip_path = $relative_path . $file;

            if ( is_dir( $file_path ) ) {
                $this->add_files_to_zip( $file_path, $zip, $zip_path . '/' );
            } else {
                $zip->addFile( $file_path, $zip_path );
            }
        }
    }

    // Function to add admin notice based on backup result
    public function add_admin_notice( $message, $type = 'success' ) {
        add_action( 'admin_notices', function() use ( $message, $type ) {
            echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible">';
            echo '<p>' . esc_html( $message ) . '</p>';
            echo '</div>';
        });
    }
}

// Initialize the backup manager
new Backup_Manager();