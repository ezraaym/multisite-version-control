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

    <div class="wrap">
        <h1>MVC Backup Settings</h1>
        <!-- Backup Now Button -->
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="mvc_trigger_backup">
            <?php wp_nonce_field( 'mvc_backup_now', 'mvc_backup_nonce' ); ?>
            <button type="submit" class="button-secondary">Backup Now</button>
        </form>
    </div>
    <?php
}

// Hook into the network admin menu.
add_action( 'network_admin_menu', 'mvc_add_network_admin_menu' );
