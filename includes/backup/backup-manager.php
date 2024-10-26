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
        // Hook to create a backup before plugin update
        add_action( 'pre_set_site_transient_update_plugins', [ $this, 'backup_plugin_files' ] );
    }

    public function backup_plugin_files( $transient ) {
        // Create backup directory if it doesn't exist
        if ( ! file_exists( $this->backup_dir ) ) {
            if ( ! wp_mkdir_p( $this->backup_dir ) ) {
                error_log( '[MVC Backup] Failed to create backup directory: ' . $this->backup_dir );
                return $transient;
            }
        }

        // Create a backup zip file of the current plugin version
        $plugin_dir = plugin_dir_path( __DIR__ );
        $backup_file = $this->backup_dir . 'mvc-backup-' . date( 'Y-m-d-H-i-s' ) . '.zip';

        // Use the ZipArchive class to create the zip backup
        $zip = new \ZipArchive();
        if ( $zip->open( $backup_file, \ZipArchive::CREATE ) === true ) {
            $this->add_files_to_zip( $plugin_dir, $zip );
            $zip->close();
            error_log( '[MVC Backup] Backup created: ' . $backup_file );
        } else {
            error_log( '[MVC Backup] Failed to create backup zip file: ' . $backup_file );
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

    // Handle local backups
    public function backup_to_local() {
        $backup_file = $this->backup_dir . 'local-backup-' . date( 'Y-m-d-H-i-s' ) . '.zip';

        // Check if directory exists, if not, create it
        if ( ! file_exists( $this->backup_dir ) ) {
            if ( ! wp_mkdir_p( $this->backup_dir ) ) {
                error_log( '[MVC Backup] Failed to create local backup directory: ' . $this->backup_dir );
                return false;
            }
        }

        // Use the ZipArchive class to create the zip backup
        $zip = new \ZipArchive();
        if ( $zip->open( $backup_file, \ZipArchive::CREATE ) === true ) {
            $this->add_files_to_zip( plugin_dir_path( __DIR__ ), $zip );
            $zip->close();
            error_log( '[MVC Backup] Local backup created: ' . $backup_file );
            return true;
        } else {
            error_log( '[MVC Backup] Failed to create local backup zip file: ' . $backup_file );
            return false;
        }
    }

    // Handle FTP backups
    public function backup_to_ftp( $ftp_details ) {
        // Create local backup first
        $backup_file = $this->backup_dir . 'ftp-backup-' . date( 'Y-m-d-H-i-s' ) . '.zip';
        if ( ! $this->backup_to_local() ) {
            return; // Stop if local backup fails
        }

        // Connect to FTP server
        $ftp_conn = ftp_connect( $ftp_details['host'] );
        if ( ! $ftp_conn ) {
            error_log( '[MVC Backup] Failed to connect to FTP server.' );
            return;
        }

        // Login to FTP server
        $login = ftp_login( $ftp_conn, $ftp_details['user'], $ftp_details['pass'] );
        if ( ! $login ) {
            error_log( '[MVC Backup] FTP login failed for user: ' . $ftp_details['user'] );
            ftp_close( $ftp_conn );
            return;
        }

        // Upload the backup file to FTP server
        if ( ! ftp_put( $ftp_conn, 'remote-backup.zip', $backup_file, FTP_BINARY ) ) {
            error_log( '[MVC Backup] Failed to upload backup file to FTP server.' );
        } else {
            error_log( '[MVC Backup] Backup uploaded to FTP server.' );
        }

        // Close FTP connection
        ftp_close( $ftp_conn );
    }

    // Handle S3 backups (Placeholder for future implementation)
    public function backup_to_s3() {
        error_log( '[MVC Backup] S3 backup functionality not implemented yet.' );
        // Placeholder: Add S3 backup logic here
    }
}

// Initialize the backup manager
new Backup_Manager();
