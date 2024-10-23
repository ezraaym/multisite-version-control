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

function mvc_log_to_file($message) {
    $logFile = MVC_PLUGIN_DIR . 'logs/database.log';
    
    // Create the log directory if it doesn't exist.
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    // Add a timestamp to the log message.
    $formattedMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    
    // Append the message to the log file.
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

// Example usage in your database tracker functions:
mvc_log_to_file('Database operation started.');

?>
