
<?php
/**
 * AJAX handler for voting on a poll
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for voting on a poll
 */
function pollify_ajax_vote() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pollify-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Get poll ID and option
    $poll_id = isset($_POST['poll_id']) ? absint($_POST['poll_id']) : 0;
    $option_id = isset($_POST['option_id']) ? sanitize_text_field($_POST['option_id']) : '';
    
    if (!$poll_id || !$option_id) {
        wp_send_json_error(array('message' => 'Missing poll or option.'));
    }
    
    // Check if the poll exists
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        wp_send_json_error(array('message' => 'Poll not found.'));
    }
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || !isset($options[$option_id])) {
        wp_send_json_error(array('message' => 'Invalid poll option.'));
    }
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    
    if (pollify_has_user_voted($poll_id, $user_ip)) {
        wp_send_json_error(array('message' => 'You have already voted on this poll.'));
    }
    
    // Record the vote
    $success = pollify_record_vote($poll_id, $option_id, $user_ip);
    
    if (!$success) {
        wp_send_json_error(array('message' => 'Failed to record vote. Please try again.'));
    }
    
    // Get updated vote counts
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    // Prepare results data
    $results = array();
    
    foreach ($options as $opt_id => $opt_text) {
        $vote_count = isset($vote_counts[$opt_id]) ? $vote_counts[$opt_id] : 0;
        $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
        
        $results[] = array(
            'id' => $opt_id,
            'text' => $opt_text,
            'votes' => $vote_count,
            'percentage' => $percentage
        );
    }
    
    wp_send_json_success(array(
        'message' => 'Vote recorded successfully.',
        'results' => $results,
        'totalVotes' => $total_votes
    ));
}
add_action('wp_ajax_pollify_vote', 'pollify_ajax_vote');
add_action('wp_ajax_nopriv_pollify_vote', 'pollify_ajax_vote');

