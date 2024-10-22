<?php

// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options and other data.
delete_option('mvc_settings');
?>
