
<?php
/**
 * Poll data utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get poll by ID
 */
function pollify_get_poll($poll_id) {
    return get_post($poll_id);
}

/**
 * Get poll type
 */
function pollify_get_poll_type($poll_id) {
    $poll_type = get_post_meta($poll_id, '_poll_type', true);
    
    if (empty($poll_type)) {
        return 'standard';
    }
    
    return $poll_type;
}

/**
 * Check if user can vote on poll
 * 
 * This is a utility wrapper for the helper function
 */
function pollify_check_user_can_vote($poll_id) {
    // Include the helper function if not already included
    if (!function_exists('pollify_user_can_vote')) {
        require_once POLLIFY_PLUGIN_DIR . 'includes/helpers/poll-status.php';
    }
    
    return pollify_user_can_vote($poll_id);
}
