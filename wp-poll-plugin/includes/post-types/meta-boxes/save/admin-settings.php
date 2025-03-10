
<?php
/**
 * Save poll admin settings meta
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save the poll admin settings
 */
function pollify_save_admin_settings($post_id) {
    // Featured poll
    update_post_meta($post_id, '_poll_featured', isset($_POST['_poll_featured']) ? '1' : '0');
    
    // Private poll
    update_post_meta($post_id, '_poll_is_private', isset($_POST['_poll_is_private']) ? '1' : '0');
    
    // Require login
    update_post_meta($post_id, '_poll_require_login', isset($_POST['_poll_require_login']) ? '1' : '0');
    
    // Maximum votes
    if (isset($_POST['_poll_max_votes'])) {
        $max_votes = absint($_POST['_poll_max_votes']);
        update_post_meta($post_id, '_poll_max_votes', $max_votes);
    }
    
    // IP restriction
    update_post_meta($post_id, '_poll_ip_restriction', isset($_POST['_poll_ip_restriction']) ? '1' : '0');
    
    // Vote frequency
    if (isset($_POST['_poll_vote_frequency'])) {
        $vote_frequency = sanitize_text_field($_POST['_poll_vote_frequency']);
        update_post_meta($post_id, '_poll_vote_frequency', $vote_frequency);
    }
    
    // Comment moderation
    update_post_meta($post_id, '_poll_moderation', isset($_POST['_poll_moderation']) ? '1' : '0');
    
    // Auto-close poll
    update_post_meta($post_id, '_poll_auto_close', isset($_POST['_poll_auto_close']) ? '1' : '0');
    
    // Vote threshold
    if (isset($_POST['_poll_vote_threshold'])) {
        $vote_threshold = absint($_POST['_poll_vote_threshold']);
        update_post_meta($post_id, '_poll_vote_threshold', $vote_threshold);
    }
    
    // Notification email
    if (isset($_POST['_poll_notification_email'])) {
        $email = sanitize_email($_POST['_poll_notification_email']);
        update_post_meta($post_id, '_poll_notification_email', $email);
    }
    
    // Custom CSS
    if (isset($_POST['_poll_custom_css'])) {
        $custom_css = wp_strip_all_tags($_POST['_poll_custom_css']);
        update_post_meta($post_id, '_poll_custom_css', $custom_css);
    }
}
