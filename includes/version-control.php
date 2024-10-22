<?php

function mvc_git_init() {
    // Initialize Git repository in WordPress directory.
    $output = shell_exec('cd ' . ABSPATH . ' && git init');
    return $output;
}

function mvc_track_changes() {
    // Add, commit, and push changes to Git.
    shell_exec('cd ' . ABSPATH . ' && git add .');
    shell_exec('cd ' . ABSPATH . ' && git commit -m "Automated commit from Multisite Version Control plugin"');
    shell_exec('cd ' . ABSPATH . ' && git push origin main');
}

// Hook to WordPress update actions.
add_action('upgrader_process_complete', 'mvc_track_changes', 10, 2);

?>
