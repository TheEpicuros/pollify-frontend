
<?php
/**
 * Database functions for poll comments
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add a comment to a poll
 */
function pollify_add_comment($poll_id, $user_id, $user_name, $comment_text) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_comments';
    
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'poll_id' => $poll_id,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'comment_text' => $comment_text,
            'comment_date' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s')
    );
    
    if ($inserted && $user_id > 0) {
        // Record activity for gamification
        pollify_record_user_activity($user_id, 'comment', $poll_id, 3);
    }
    
    return $inserted !== false ? $wpdb->insert_id : false;
}

/**
 * Get comments for a poll
 */
function pollify_get_poll_comments($poll_id, $limit = 10, $offset = 0) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_comments';
    
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE poll_id = %d ORDER BY comment_date DESC LIMIT %d OFFSET %d",
        $poll_id, $limit, $offset
    ));
}
