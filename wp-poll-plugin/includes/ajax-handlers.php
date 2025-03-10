<?php
/**
 * AJAX handlers for the Pollify plugin
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

/**
 * AJAX handler for creating a poll
 */
function pollify_ajax_create_poll() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pollify-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'You must be logged in to create a poll.'));
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        wp_send_json_error(array('message' => 'You do not have permission to create polls.'));
    }
    
    // Get form data
    $title = isset($_POST['poll_title']) ? sanitize_text_field($_POST['poll_title']) : '';
    $description = isset($_POST['poll_description']) ? sanitize_textarea_field($_POST['poll_description']) : '';
    $options = isset($_POST['poll_options']) && is_array($_POST['poll_options']) ? array_map('sanitize_text_field', $_POST['poll_options']) : array();
    
    // Validate data
    if (empty($title)) {
        wp_send_json_error(array('message' => 'Poll question is required.'));
    }
    
    // Remove empty options
    $options = array_filter($options);
    
    if (count($options) < 2) {
        wp_send_json_error(array('message' => 'At least two poll options are required.'));
    }
    
    // Create the poll post
    $poll_data = array(
        'post_title' => $title,
        'post_content' => $description,
        'post_status' => 'publish',
        'post_type' => 'poll',
        'post_author' => get_current_user_id()
    );
    
    $poll_id = wp_insert_post($poll_data);
    
    if (is_wp_error($poll_id)) {
        wp_send_json_error(array('message' => 'Failed to create poll: ' . $poll_id->get_error_message()));
    }
    
    // Save poll options
    update_post_meta($poll_id, '_poll_options', $options);
    
    wp_send_json_success(array(
        'message' => 'Poll created successfully.',
        'pollId' => $poll_id,
        'pollUrl' => get_permalink($poll_id)
    ));
}
add_action('wp_ajax_pollify_create_poll', 'pollify_ajax_create_poll');

/**
 * AJAX handler for getting user activity stats
 */
function pollify_ajax_get_user_stats() {
    // Check nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'pollify-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Get user ID
    $user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
    
    // If no user ID provided, try to get current user
    if (!$user_id && is_user_logged_in()) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        wp_send_json_error(array('message' => 'User not found.'));
    }
    
    // Get user data
    $user = get_userdata($user_id);
    
    if (!$user) {
        wp_send_json_error(array('message' => 'User not found.'));
    }
    
    // Get user activity stats
    $activity_stats = pollify_get_user_activity_stats($user_id);
    
    // Get polls created by user
    $args = array(
        'post_type' => 'poll',
        'author' => $user_id,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    
    $polls = get_posts($args);
    $poll_count = count($polls);
    
    // Calculate level and progress
    $points = $activity_stats['total_points'];
    $current_level = 1;
    $next_level_points = 100;
    
    if ($points >= 100) {
        $current_level = 2;
        $next_level_points = 200;
    }
    
    if ($points >= 200) {
        $current_level = 3;
        $next_level_points = 350;
    }
    
    if ($points >= 350) {
        $current_level = 4;
        $next_level_points = 500;
    }
    
    if ($points >= 500) {
        $current_level = 5;
        $next_level_points = 750;
    }
    
    if ($points >= 750) {
        $current_level = 6;
        $next_level_points = 1000;
    }
    
    // Calculate progress percentage to next level
    $level_min = $next_level_points - ($next_level_points - $points < 100 ? 100 : $next_level_points - $points);
    $progress_percentage = min(100, max(0, (($points - $level_min) / ($next_level_points - $level_min)) * 100));
    
    // Get user achievements
    $achievements = array();
    
    // First vote achievement
    $has_voted = $activity_stats['vote_count'] > 0;
    $achievements[] = array(
        'id' => 'first_vote',
        'title' => __('First Vote', 'pollify'),
        'description' => __('Cast your first vote on a poll', 'pollify'),
        'icon' => 'vote',
        'unlocked' => $has_voted,
        'date_unlocked' => $has_voted ? pollify_get_first_activity_date($user_id, 'vote') : null,
    );
    
    // Community voice achievement
    $is_community_voice = $activity_stats['comment_count'] >= 10;
    $achievements[] = array(
        'id' => 'community_voice',
        'title' => __('Community Voice', 'pollify'),
        'description' => __('Leave 10 comments on polls', 'pollify'),
        'icon' => 'message',
        'unlocked' => $is_community_voice,
        'date_unlocked' => $is_community_voice ? pollify_get_achievement_date($user_id, 'comment', 10) : null,
    );
    
    // Poll creator achievement
    $is_poll_creator = $poll_count > 0;
    $achievements[] = array(
        'id' => 'poll_creator',
        'title' => __('Poll Creator', 'pollify'),
        'description' => __('Create your first poll', 'pollify'),
        'icon' => 'chart',
        'unlocked' => $is_poll_creator,
        'date_unlocked' => $is_poll_creator ? get_the_date('Y-m-d', $polls[0]->ID) : null,
    );
    
    // Popular poll achievement
    $has_popular_poll = false;
    $popular_poll_date = null;
    
    foreach ($polls as $poll) {
        $vote_count = pollify_get_total_votes($poll->ID);
        
        if ($vote_count >= 50) {
            $has_popular_poll = true;
            $popular_poll_date = get_the_date('Y-m-d', $poll->ID);
            break;
        }
    }
    
    $achievements[] = array(
        'id' => 'popular_poll',
        'title' => __('Popular Opinion', 'pollify'),
        'description' => __('Create a poll with 50+ votes', 'pollify'),
        'icon' => 'star',
        'unlocked' => $has_popular_poll,
        'date_unlocked' => $popular_poll_date,
    );
    
    // Poll expert achievement
    $is_poll_expert = $points >= 500;
    $achievements[] = array(
        'id' => 'poll_expert',
        'title' => __('Poll Expert', 'pollify'),
        'description' => __('Earn 500 points in the system', 'pollify'),
        'icon' => 'award',
        'unlocked' => $is_poll_expert,
        'date_unlocked' => $is_poll_expert ? pollify_get_achievement_date($user_id, null, 500) : null,
    );
    
    // Prepare response data
    $response_data = array(
        'user_id' => $user_id,
        'user_name' => $user->display_name,
        'total_points' => $activity_stats['total_points'],
        'vote_count' => $activity_stats['vote_count'],
        'comment_count' => $activity_stats['comment_count'],
        'rating_count' => $activity_stats['rating_count'],
        'poll_count' => $poll_count,
        'current_level' => $current_level,
        'next_level_points' => $next_level_points,
        'progress_percentage' => round($progress_percentage),
        'achievements' => $achievements,
    );
    
    wp_send_json_success($response_data);
}
add_action('wp_ajax_pollify_get_user_stats', 'pollify_ajax_get_user_stats');
add_action('wp_ajax_nopriv_pollify_get_user_stats', 'pollify_ajax_get_user_stats');

/**
 * Helper function to get the date of the first activity of a specific type
 */
function pollify_get_first_activity_date($user_id, $activity_type = null) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    $query = "SELECT activity_date FROM $table_name WHERE user_id = %d";
    $params = array($user_id);
    
    if ($activity_type) {
        $query .= " AND activity_type = %s";
        $params[] = $activity_type;
    }
    
    $query .= " ORDER BY activity_date ASC LIMIT 1";
    
    $date = $wpdb->get_var($wpdb->prepare($query, $params));
    
    return $date ? date('Y-m-d', strtotime($date)) : null;
}

/**
 * Helper function to get the date when an achievement was unlocked
 */
function pollify_get_achievement_date($user_id, $activity_type = null, $threshold = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    if ($activity_type && $threshold > 0) {
        // For count-based achievements (like 10 comments)
        $query = "SELECT a1.activity_date 
                  FROM $table_name a1
                  JOIN (
                      SELECT activity_date
                      FROM $table_name
                      WHERE user_id = %d AND activity_type = %s
                      ORDER BY activity_date ASC
                      LIMIT %d
                  ) a2 ON a1.activity_date = a2.activity_date
                  ORDER BY a1.activity_date DESC
                  LIMIT 1";
        
        $date = $wpdb->get_var($wpdb->prepare($query, $user_id, $activity_type, $threshold));
    } elseif ($threshold > 0) {
        // For point-based achievements (like 500 points)
        $query = "SELECT activity_date 
                  FROM $table_name
                  WHERE user_id = %d
                  GROUP BY activity_date
                  HAVING SUM(points) >= %d
                  ORDER BY activity_date ASC
                  LIMIT 1";
        
        $date = $wpdb->get_var($wpdb->prepare($query, $user_id, $threshold));
    } else {
        return null;
    }
    
    return $date ? date('Y-m-d', strtotime($date)) : null;
}
