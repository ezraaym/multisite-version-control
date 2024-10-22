<div class="wrap">
    <h2><?php esc_html_e('Select Backup Service', 'mvc'); ?></h2>
    <form method="post">
        <label for="backup_service"><?php esc_html_e('Backup Service:', 'mvc'); ?></label>
        <select name="backup_service" id="backup_service">
            <option value="HiDrive">HiDrive</option>
            <option value="Google Drive">Google Drive</option>
        </select>
        <input type="submit" value="<?php esc_attr_e('Save', 'mvc'); ?>" />
    </form>
</div>
