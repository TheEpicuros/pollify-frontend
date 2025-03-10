
<?php
/**
 * Poll helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if user can vote on a poll
 * 
 * NOTE: For new code, use pollify_user_can_vote() function from helpers/poll-status.php instead.
 * This function is maintained for backward compatibility.
 * 
 * @deprecated Use pollify_user_can_vote() from helpers/poll-status.php instead
 * @param int $poll_id Poll ID
 * @return bool Whether the user can vote
 */
function pollify_can_user_vote($poll_id) {
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return false;
    }
    
    // Include the core database function if not already included
    if (!function_exists('pollify_can_user_vote_db')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/poll-status.php';
    }
    
    // This implementation adds additional checks on top of the database function
    $allowed_roles = get_post_meta($poll_id, '_poll_allowed_roles', true);
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = array('all');
    }
    
    // If 'all' is allowed, everyone can vote
    if (in_array('all', $allowed_roles)) {
        return true;
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return false;
    }
    
    // Get current user's roles
    $user = wp_get_current_user();
    $user_roles = $user->roles;
    
    // Check if user has any of the allowed roles
    foreach ($user_roles as $role) {
        if (in_array($role, $allowed_roles)) {
            return true;
        }
    }
    
    return false;
}
