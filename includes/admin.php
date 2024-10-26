<?php

function mvc_add_network_admin_menu() {
    add_menu_page(
        'Multisite Version Control',
        'Version Control',
        'manage_network',
        'mvc-settings',
        'mvc_render_admin_settings_page',
        'dashicons-shield',
        99
    );
}

function mvc_render_admin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Multisite Version Control Settings</h1>
        <p>Manage the settings for version control on your WordPress Multisite network.</p>
    </div>
    <?php
}

// Hook into the network admin menu.
add_action( 'network_admin_menu', 'mvc_add_network_admin_menu' );
