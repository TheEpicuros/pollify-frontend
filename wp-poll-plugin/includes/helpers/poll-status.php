
<?php
/**
 * Poll status helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core utilities
require_once plugin_dir_path(dirname(__FILE__)) . 'core/utils/function-exists.php';

/**
 * Wrapper function to maintain compatibility with the canonical function 
 * in validation/poll-validation.php
 * 
 * @param int $poll_id Poll ID
 * @return bool Whether the poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    // Use the function registry system to get the canonical function
    $canonical_file = plugin_dir_path(dirname(__FILE__)) . 'core/utils/validation/poll-validation.php';
    
    // Include the validation file containing the canonical function
    if (file_exists($canonical_file)) {
        require_once $canonical_file;
    }
    
    // Check if function exists from the canonical source
    if (function_exists('pollify_has_poll_ended')) {
        return pollify_has_poll_ended($poll_id);
    }
    
    // Fallback implementation if canonical function is not available
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false;
    }
    
    $now = current_time('mysql');
    
    return strtotime($end_date) < strtotime($now);
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
    
    // Include the core database function
    if (!function_exists('pollify_can_user_vote_db')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/poll-status.php';
    }
    
    return pollify_can_user_vote_db($poll_id);
}
