
<?php
/**
 * REST API endpoints
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register REST API endpoints
add_action('rest_api_init', 'pollify_register_rest_routes');

/**
 * Register REST API routes
 */
function pollify_register_rest_routes() {
    register_rest_route('pollify/v1', '/polls', array(
        'methods' => 'GET',
        'callback' => 'pollify_api_get_polls',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('pollify/v1', '/polls/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'pollify_api_get_poll',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('pollify/v1', '/vote', array(
        'methods' => 'POST',
        'callback' => 'pollify_api_vote',
        'permission_callback' => '__return_true'
    ));
}

/**
 * REST API handler for getting polls
 */
function pollify_api_get_polls($request) {
    // REST API implementation for getting polls
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => 10,
        'post_status' => 'publish'
    );
    
    $polls = get_posts($args);
    $formatted_polls = array();
    
    foreach ($polls as $poll) {
        $formatted_polls[] = pollify_format_poll_for_api($poll);
    }
    
    return rest_ensure_response($formatted_polls);
}

/**
 * REST API handler for getting a single poll
 */
function pollify_api_get_poll($request) {
    $poll_id = $request['id'];
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return new WP_Error('not_found', 'Poll not found', array('status' => 404));
    }
    
    $formatted_poll = pollify_format_poll_for_api($poll);
    return rest_ensure_response($formatted_poll);
}

/**
 * REST API handler for voting on a poll
 */
function pollify_api_vote($request) {
    $poll_id = isset($request['poll_id']) ? absint($request['poll_id']) : 0;
    $option_id = isset($request['option_id']) ? sanitize_text_field($request['option_id']) : '';
    
    if (!$poll_id || !$option_id) {
        return new WP_Error('missing_data', 'Missing poll or option', array('status' => 400));
    }
    
    // Check if poll exists
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return new WP_Error('not_found', 'Poll not found', array('status' => 404));
    }
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || !isset($options[$option_id])) {
        return new WP_Error('invalid_option', 'Invalid poll option', array('status' => 400));
    }
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    
    if (pollify_has_user_voted($poll_id, $user_ip)) {
        return new WP_Error('already_voted', 'You have already voted on this poll', array('status' => 403));
    }
    
    // Record the vote
    $success = pollify_record_vote($poll_id, $option_id, $user_ip);
    
    if (!$success) {
        return new WP_Error('vote_failed', 'Failed to record vote', array('status' => 500));
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
    
    return rest_ensure_response(array(
        'message' => 'Vote recorded successfully',
        'results' => $results,
        'totalVotes' => $total_votes
    ));
}

/**
 * Helper function to format poll data for API
 */
function pollify_format_poll_for_api($poll) {
    $poll_id = $poll->ID;
    $options = get_post_meta($poll_id, '_poll_options', true);
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    $formatted_options = array();
    
    foreach ($options as $option_id => $option_text) {
        $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
        
        $formatted_options[] = array(
            'id' => $option_id,
            'text' => $option_text,
            'votes' => $vote_count
        );
    }
    
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    $status = empty($end_date) || strtotime($end_date) > current_time('timestamp') ? 'active' : 'closed';
    
    return array(
        'id' => $poll_id,
        'title' => get_the_title($poll),
        'description' => $poll->post_content,
        'options' => $formatted_options,
        'createdAt' => get_the_date('c', $poll),
        'createdBy' => get_the_author_meta('display_name', $poll->post_author),
        'status' => $status,
        'totalVotes' => $total_votes
    );
}
