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
    }

    private function setup_hooks() {
        // Hook into the plugin update process to create a backup
        add_action( 'pre_set_site_transient_update_plugins', [ $this, 'backup_plugin_files' ] );
    }

    public function backup_plugin_files( $transient ) {
        // Create backup directory if it doesn't exist
        if ( ! file_exists( $this->backup_dir ) ) {
            wp_mkdir_p( $this->backup_dir );
        }

        // Create a backup zip file of the current plugin version
        $plugin_dir = plugin_dir_path( __DIR__ );
        $backup_file = $this->backup_dir . 'mvc-backup-' . date( 'Y-m-d-H-i-s' ) . '.zip';

        // Use the ZipArchive class to create the zip backup
        $zip = new \ZipArchive();
        if ( $zip->open( $backup_file, \ZipArchive::CREATE ) === true ) {
            $this->add_files_to_zip( $plugin_dir, $zip );
            $zip->close();
        }

        return $transient;
    }

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
}

// Initialize the backup manager
new Backup_Manager();
