
<?php
/**
 * Poll status helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Wrapper function to maintain compatibility with the canonical function 
 * in database/poll-status.php
 * 
 * @param int $poll_id Poll ID
 * @return bool Whether the poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    // Include the core utility function if not already included
    if (!function_exists('pollify_has_poll_ended_db')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/poll-status.php';
    }
    
    return pollify_has_poll_ended_db($poll_id);
}

/**
 * Unified function to check if a user can vote on a poll
 * This centralizes the vote permission checking logic
 * 
 * @param int $poll_id Poll ID
 * @return bool Whether the user can vote
 */
function pollify_user_can_vote($poll_id) {
    // First check if the poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return false;
    }
    
    // Include the core functionality from the post-types helper
    if (!function_exists('pollify_can_user_vote')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'post-types/helpers.php';
    }
    
    return pollify_can_user_vote($poll_id);
}
