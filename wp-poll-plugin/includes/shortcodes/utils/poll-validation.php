
<?php
/**
 * Poll validation utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if a poll is valid for voting
 */
function pollify_validate_poll($poll_id) {
    // Check if poll exists and is published
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll' || $poll->post_status !== 'publish') {
        return array(
            'valid' => false,
            'message' => __('Poll not found or not published.', 'pollify')
        );
    }
    
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return array(
            'valid' => false,
            'message' => __('This poll has ended.', 'pollify')
        );
    }
    
    // Check user permissions
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

