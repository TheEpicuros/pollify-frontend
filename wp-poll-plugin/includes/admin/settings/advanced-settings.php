
<?php
/**
 * Advanced settings tab for Pollify admin settings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the advanced settings tab
 * 
 * @param bool $delete_data Current setting for data deletion on uninstall
 */
function pollify_render_advanced_settings($delete_data) {
    ?>
    <div id="advanced" class="pollify-settings-section">
        <h2><?php _e('Advanced Settings', 'pollify'); ?></h2>
        
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php _e('Uninstall Options', 'pollify'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="delete_data_on_uninstall" <?php checked($delete_data); ?>>
                        <?php _e('Delete all plugin data when uninstalling', 'pollify'); ?>
                    </label>
                    <p class="description"><?php _e('If checked, all polls, votes, and settings will be permanently deleted when the plugin is uninstalled.', 'pollify'); ?></p>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
