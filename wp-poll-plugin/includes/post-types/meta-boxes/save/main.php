
<?php
/**
 * Poll meta save functions - Main file
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include modular save-meta files
require_once plugin_dir_path(__FILE__) . 'poll-options.php';
require_once plugin_dir_path(__FILE__) . 'poll-settings.php';
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'notices.php';

/**
 * Save the poll meta data
 */
function pollify_save_poll_meta($post_id, $post) {
    // Check if our nonce is set
    if (!isset($_POST['pollify_poll_meta_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['pollify_poll_meta_nonce'], 'pollify_save_poll_meta')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save poll options
    pollify_save_poll_options($post_id);
    
    // Save poll type and related settings
    pollify_save_poll_type_settings($post_id);
    
    // Save general poll settings
    pollify_save_general_settings($post_id);
    
    // Save admin settings if the user has permission
    if (current_user_can('manage_poll_settings')) {
        pollify_save_admin_settings($post_id);
    }
}
