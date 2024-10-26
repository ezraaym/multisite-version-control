<?php

namespace MVC\Version;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Version_History {

    private $log_file;

    public function __construct() {
        $this->log_file = WP_CONTENT_DIR . '/mvc-version-history.log';
        $this->setup_hooks();
    }

    private function setup_hooks() {
        // Hook into plugin activation to log the current version
        register_activation_hook( MVC_PLUGIN_FILE, [ $this, 'log_version' ] );
    }

    public function log_version() {
        $version = get_option( 'mvc_plugin_version', '1.0' );
        $new_version = MVC_VERSION;

        // If version has changed, log it
        if ( version_compare( $version, $new_version, '<' ) ) {
            $log_entry = date( 'Y-m-d H:i:s' ) . " - Updated from $version to $new_version" . PHP_EOL;
            file_put_contents( $this->log_file, $log_entry, FILE_APPEND );
            update_option( 'mvc_plugin_version', $new_version );
        }
    }

    public function rollback_version( $target_version ) {
        // Restore the backup corresponding to the target version
        $backup_file = WP_CONTENT_DIR . "/mvc-backups/mvc-backup-{$target_version}.zip";
        if ( file_exists( $backup_file ) ) {
            $this->restore_backup( $backup_file );
            update_option( 'mvc_plugin_version', $target_version );
        }
    }

    private function restore_backup( $backup_file ) {
        $zip = new \ZipArchive();
        if ( $zip->open( $backup_file ) === true ) {
            $zip->extractTo( plugin_dir_path( __DIR__ ) );
            $zip->close();
        }
    }
}

// Initialize version history tracking
new Version_History();
