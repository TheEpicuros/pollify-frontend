
<?php
/**
 * Poll validation utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core validation utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

/**
 * Check if a poll is valid for voting
 */
function pollify_validate_poll_for_voting($poll_id) {
    // Check if poll exists and is published
    $poll_validation = pollify_validate_poll_exists($poll_id);
    
    if (!$poll_validation['valid']) {
        return $poll_validation;
    }
    
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return array(
            'valid' => false,
            'message' => __('This poll has ended.', 'pollify')
        );
    }
    
    // Check user permissions
    if (!function_exists('pollify_can_user_vote')) {
        require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'post-types/helpers.php';
    }
    
    if (!pollify_can_user_vote($poll_id)) {
        return array(
            'valid' => false,
            'message' => __('You do not have permission to vote on this poll.', 'pollify')
        );
    }
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    $user_id = get_current_user_id();
    
    if (pollify_has_user_voted($poll_id, $user_ip, $user_id)) {
        return array(
            'valid' => false,
            'message' => __('You have already voted on this poll.', 'pollify')
        );
    }
    
    return array(
        'valid' => true,
        'message' => ''
    );
}
