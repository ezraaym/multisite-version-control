<div class="wrap">
    <h1><?php esc_html_e('Multisite Version Control Settings', 'mvc'); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('mvc_settings_group'); ?>
        <?php do_settings_sections('mvc-settings'); ?>
        <?php submit_button(); ?>
    </form>
</div>
