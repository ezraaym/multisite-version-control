<?php

function mvc_log_database_change($query) {
    // Log the database query.
    $log_file = MVC_PLUGIN_DIR . 'logs/database.log';
    file_put_contents($log_file, $query . PHP_EOL, FILE_APPEND);
}

function mvc_track_db_changes($query) {
    // Track any changes made to the database.
    if (strpos(strtolower($query), 'update') !== false || strpos(strtolower($query), 'insert') !== false || strpos(strtolower($query), 'delete') !== false) {
        mvc_log_database_change($query);
    }
}

// Hook into database queries.
add_filter('query', 'mvc_track_db_changes');

?>
