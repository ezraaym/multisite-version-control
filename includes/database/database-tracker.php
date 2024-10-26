<?php

namespace MVC\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Database_Tracker {

    public function __construct() {
        $this->setup_hooks();
    }

    private function setup_hooks() {
        register_activation_hook( MVC_PLUGIN_FILE, [ $this, 'track_database_version' ] );
    }

    public function track_database_version() {
        $db_version = get_option( 'mvc_database_version', '1.0' );
        $new_db_version = '1.1'; // Update as per your changes

        // If database version is lower, run upgrade script
        if ( version_compare( $db_version, $new_db_version, '<' ) ) {
            $this->upgrade_database( $db_version, $new_db_version );
            update_option( 'mvc_database_version', $new_db_version );
        }
    }

    private function upgrade_database( $old_version, $new_version ) {
        global $wpdb;

        if ( version_compare( $old_version, '1.1', '<' ) ) {
            // Example of adding a new column to a custom table
            $table_name = $wpdb->prefix . 'mvc_custom_table';
            $wpdb->query( "ALTER TABLE $table_name ADD COLUMN new_column VARCHAR(255) DEFAULT ''" );
        }
    }
}

// Initialize the database tracker
new Database_Tracker();
