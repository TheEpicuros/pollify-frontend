
<?php
/**
 * Voting status validation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core validation functions
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get voting status for a poll
 * 
 * This centralizes all the voting status checks in one place to ensure
 * consistent behavior across all shortcodes and functions.
 */
if (pollify_can_define_function('pollify_get_voting_status')) {
    function pollify_get_voting_status($poll_id) {
        $user_ip = pollify_get_user_ip();
        $user_id = get_current_user_id();
        
        // Check if user has already voted
        $has_voted = pollify_has_user_voted($poll_id, $user_ip, $user_id);
        $user_vote = $has_voted ? pollify_get_user_vote($poll_id, $user_ip, $user_id) : null;
        
        // Check if poll has ended
        $has_ended = pollify_has_poll_ended($poll_id);
        
        // Get vote counts
        $vote_counts = pollify_get_vote_counts($poll_id);
        $total_votes = array_sum($vote_counts);
        
        return [
            'has_voted' => $has_voted,
            'can_vote' => !$has_voted && !$has_ended && pollify_user_can_vote($poll_id),
            'has_ended' => $has_ended,
            'user_vote' => $user_vote,
            'vote_counts' => $vote_counts,
            'total_votes' => $total_votes,
        ];
    }
    pollify_register_function_path('pollify_get_voting_status', $current_file);
}
