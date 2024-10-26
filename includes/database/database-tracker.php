namespace MVC\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Database_Tracker {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mvc_custom_table';
        $this->setup_hooks();
    }

    private function setup_hooks() {
        register_activation_hook( MVC_PLUGIN_FILE, [ $this, 'create_custom_table' ] );
        add_action( 'plugins_loaded', [ $this, 'track_database_version' ] );
    }

    public function create_custom_table() {
        global $wpdb;

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) !== $this->table_name ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE {$this->table_name} (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                column_name varchar(255) DEFAULT '' NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }

    public function track_database_version() {
        $db_version = get_option( 'mvc_database_version', '1.0' );
        $new_db_version = '1.1';

        if ( version_compare( $db_version, $new_db_version, '<' ) ) {
            $this->upgrade_database();
            update_option( 'mvc_database_version', $new_db_version );
        }
    }

    private function upgrade_database() {
        global $wpdb;

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) === $this->table_name ) {
            $wpdb->query( "ALTER TABLE {$this->table_name} ADD COLUMN new_column VARCHAR(255) DEFAULT ''" );
        } else {
            error_log( '[MVC Database Tracker] Table does not exist, skipping upgrade.' );
        }
    }
}

// Initialize the database tracker
new Database_Tracker();
