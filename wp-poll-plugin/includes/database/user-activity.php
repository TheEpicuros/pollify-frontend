
<?php
/**
 * Database functions for user activity and gamification
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Record user activity for gamification
 */
function pollify_record_user_activity($user_id, $activity_type, $poll_id = null, $points = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    return $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'activity_type' => $activity_type,
            'poll_id' => $poll_id,
            'points' => $points,
            'activity_date' => current_time('mysql')
        ),
        array('%d', '%s', '%d', '%d', '%s')
    );
}

/**
 * Get user activity stats
 */
function pollify_get_user_activity_stats($user_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_user_activity';
    
    $total_points = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(points) FROM $table_name WHERE user_id = %d",
        $user_id
    ));
    
    $vote_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND activity_type = 'vote'",
        $user_id
    ));
    
    $comment_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND activity_type = 'comment'",
        $user_id
    ));
    
    $rating_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND activity_type = 'rate'",
        $user_id
    ));
    
    return array(
        'total_points' => (int) $total_points,
        'vote_count' => (int) $vote_count,
        'comment_count' => (int) $comment_count,
        'rating_count' => (int) $rating_count
    );
}
