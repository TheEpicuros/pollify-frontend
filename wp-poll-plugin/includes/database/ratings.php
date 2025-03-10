
<?php
/**
 * Database functions for poll ratings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Rate a poll (thumbs up/down)
 */
function pollify_rate_poll($poll_id, $rating, $user_id, $user_ip) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    // Check if user has already rated
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE poll_id = %d AND (user_ip = %s OR user_id = %d)",
        $poll_id, $user_ip, $user_id
    ));
    
    if ($existing) {
        // Update existing rating
        $updated = $wpdb->update(
            $table_name,
            array(
                'rating' => $rating,
                'rated_at' => current_time('mysql')
            ),
            array('id' => $existing),
            array('%d', '%s'),
            array('%d')
        );
        
        return $updated !== false;
    } else {
        // Insert new rating
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'poll_id' => $poll_id,
                'user_id' => $user_id,
                'user_ip' => $user_ip,
                'rating' => $rating,
                'rated_at' => current_time('mysql')
            ),
            array('%d', '%d', '%s', '%d', '%s')
        );
        
        if ($inserted && $user_id > 0) {
            // Record activity for gamification
            pollify_record_user_activity($user_id, 'rate', $poll_id, 2);
        }
        
        return $inserted !== false;
    }
}

/**
 * Get poll ratings count
 */
function pollify_get_poll_ratings($poll_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    $upvotes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND rating = 1",
        $poll_id
    ));
    
    $downvotes = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND rating = 0",
        $poll_id
    ));
    
    return array(
        'upvotes' => (int) $upvotes,
        'downvotes' => (int) $downvotes
    );
}
