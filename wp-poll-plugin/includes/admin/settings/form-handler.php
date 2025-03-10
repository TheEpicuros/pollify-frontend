
<?php
/**
 * Admin settings form handler
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Process the settings form submission
 */
function pollify_process_settings_form() {
    // Only process if form was submitted
    if (!isset($_POST['pollify_settings_submit'])) {
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['pollify_settings_nonce']) || !wp_verify_nonce($_POST['pollify_settings_nonce'], 'pollify_save_settings')) {
        // Show error message
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Security check failed. Please try again.', 'pollify') . '</p></div>';
        return;
    }
    
    // Get and sanitize form data
    $settings = array(
        'allow_guests' => isset($_POST['allow_guests']) ? true : false,
        'results_display' => sanitize_text_field($_POST['results_display']),
        'show_results_before_vote' => isset($_POST['show_results_before_vote']) ? true : false,
        'enable_comments' => isset($_POST['enable_comments']) ? true : false,
        'enable_ratings' => isset($_POST['enable_ratings']) ? true : false,
        'enable_social_sharing' => isset($_POST['enable_social_sharing']) ? true : false,
        'poll_archive_page' => absint($_POST['poll_archive_page']),
        'polls_per_page' => absint($_POST['polls_per_page']),
        'loading_animation' => isset($_POST['loading_animation']) ? true : false
    );
    
    // Save settings
    update_option('pollify_settings', $settings);
    
    // Save uninstall option
    $delete_data = isset($_POST['delete_data_on_uninstall']) ? true : false;
    update_option('pollify_delete_data_on_uninstall', $delete_data);
    
    // Show success message
    echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'pollify') . '</p></div>';
}
