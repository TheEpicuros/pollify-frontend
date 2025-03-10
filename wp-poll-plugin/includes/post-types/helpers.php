<?php
/**
 * Poll helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if a poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false; // No end date, poll is active
    }
    
    $current_time = current_time('timestamp');
    $end_timestamp = strtotime($end_date);
    
    return $current_time > $end_timestamp;
}

/**
 * Check if user can vote on a poll
 */
function pollify_can_user_vote($poll_id) {
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return false;
    }
    
    // Check user role restrictions
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

