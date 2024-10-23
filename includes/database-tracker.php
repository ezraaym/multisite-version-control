<?php

function mvc_log_database_change($query) {
    // Log the database query.
    $log_file = MVC_PLUGIN_DIR . 'logs/database.log';

    // Create the log directory if it doesn't exist.
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    // Append the query to the log file.
    file_put_contents($log_file, $query . PHP_EOL, FILE_APPEND);
}

function mvc_track_db_changes($query) {
    // Track any changes made to the database.
    $lower_query = strtolower($query);
    if (strpos($lower_query, 'update') !== false || strpos($lower_query, 'insert') !== false || strpos($lower_query, 'delete') !== false) {
        mvc_log_database_change($query);
    }
}

// Hook into database queries.
add_filter('query', 'mvc_track_db_changes');
?>
