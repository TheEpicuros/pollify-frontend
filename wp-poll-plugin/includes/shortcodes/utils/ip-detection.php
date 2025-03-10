<?php
/**
 * IP detection utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core utility functions
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/formatting.php';

/**
 * Check if a user has already voted on a poll based on IP
 *
 * @param int $poll_id The poll ID
 * @return bool True if user has voted, false otherwise
 */
function pollify_has_user_voted_by_ip($poll_id) {
    // Get user IP using the core utility function
    $user_ip = pollify_get_user_ip();
    
    // Get vote records for this poll
    $votes = get_post_meta($poll_id, '_poll_votes_ip', true);
    
    if (!is_array($votes)) {
        $votes = array();
    }
    
    // Check if the user's IP exists in the vote records
    return in_array($user_ip, $votes);
}

/**
 * Record user vote by IP
 * 
 * @param int $poll_id The poll ID
 * @return bool True if recorded successfully, false otherwise
 */
function pollify_record_vote_by_ip($poll_id) {
    // Get user IP using the core utility function
    $user_ip = pollify_get_user_ip();
    
    // Get existing vote records
    $votes = get_post_meta($poll_id, '_poll_votes_ip', true);
    
    if (!is_array($votes)) {
        $votes = array();
    }
    
    // Add the user's IP to the vote records if not already present
    if (!in_array($user_ip, $votes)) {
        $votes[] = $user_ip;
        update_post_meta($poll_id, '_poll_votes_ip', $votes);
        return true;
    }
    
    return false;
}
