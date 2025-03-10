
<?php
/**
 * Database functions for poll status and permissions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if user can vote on a poll
 */
function pollify_can_user_vote($poll_id) {
    $allowed_roles = get_post_meta($poll_id, '_poll_allowed_roles', true);
    
    // If no specific roles are set, allow all
    if (empty($allowed_roles)) {
        return true;
    }
    
    // Check if guest voting is allowed
    if (in_array('guest', $allowed_roles) && !is_user_logged_in()) {
        return true;
    }
    
    // If user is logged in, check their role
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed_roles)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Check if poll has ended - renamed to avoid conflicts
 */
function pollify_has_poll_ended_db($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false;
    }
    
    $now = current_time('mysql');
    
    return strtotime($end_date) < strtotime($now);
}
