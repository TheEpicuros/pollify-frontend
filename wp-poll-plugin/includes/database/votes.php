
<?php
/**
 * Database functions for poll votes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if a user has already voted on a poll
 */
function pollify_has_user_voted($poll_id, $user_ip, $user_id = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $query = "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND (user_ip = %s";
    $params = array($poll_id, $user_ip);
    
    if ($user_id > 0) {
        $query .= " OR user_id = %d";
        $params[] = $user_id;
    }
    
    $query .= ")";
    
    $result = $wpdb->get_var($wpdb->prepare($query, $params));
    
    return $result > 0;
}

/**
 * Record a vote for a poll option
 */
function pollify_record_vote($poll_id, $option_id, $user_ip, $user_id = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $data = array(
        'poll_id' => $poll_id,
        'option_id' => $option_id,
        'user_ip' => $user_ip,
        'voted_at' => current_time('mysql')
    );
    
    $formats = array('%d', '%s', '%s', '%s');
    
    if ($user_id > 0) {
        $data['user_id'] = $user_id;
        $formats[] = '%d';
        
        // Record activity for gamification
        pollify_record_user_activity($user_id, 'vote', $poll_id, 5);
    }
    
    $inserted = $wpdb->insert($table_name, $data, $formats);
    
    return $inserted !== false;
}

/**
 * Get vote counts for a poll
 */
function pollify_get_vote_counts($poll_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT option_id, COUNT(*) as count FROM $table_name WHERE poll_id = %d GROUP BY option_id",
        $poll_id
    ), ARRAY_A);
    
    $vote_counts = array();
    
    foreach ($results as $result) {
        $vote_counts[$result['option_id']] = (int) $result['count'];
    }
    
    return $vote_counts;
}

/**
 * Get user vote details for a poll
 */
function pollify_get_user_vote($poll_id, $user_ip, $user_id = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $query = "SELECT option_id, voted_at FROM $table_name WHERE poll_id = %d AND (user_ip = %s";
    $params = array($poll_id, $user_ip);
    
    if ($user_id > 0) {
        $query .= " OR user_id = %d";
        $params[] = $user_id;
    }
    
    $query .= ") ORDER BY voted_at DESC LIMIT 1";
    
    return $wpdb->get_row($wpdb->prepare($query, $params));
}

/**
 * Get total votes for a poll
 */
function pollify_get_total_votes($poll_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d",
        $poll_id
    ));
}
