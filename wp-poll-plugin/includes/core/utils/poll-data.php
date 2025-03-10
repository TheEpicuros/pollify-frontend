
<?php
/**
 * Poll data retrieval utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if poll is valid
 */
function pollify_is_valid_poll($post_id) {
    if (!$post_id) {
        return false;
    }
    
    $post = get_post($post_id);
    
    return $post && $post->post_type === 'poll';
}

/**
 * Get polls by popularity
 */
function pollify_get_popular_polls($limit = 5) {
    global $wpdb;
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.ID, p.post_title, COUNT(v.id) as vote_count
             FROM {$wpdb->posts} p
             LEFT JOIN {$votes_table} v ON p.ID = v.poll_id
             WHERE p.post_type = 'poll' AND p.post_status = 'publish'
             GROUP BY p.ID
             ORDER BY vote_count DESC
             LIMIT %d",
            $limit
        )
    );
    
    return $results;
}

/**
 * Get polls by user
 */
function pollify_get_user_polls($user_id, $limit = 5) {
    return get_posts(array(
        'post_type' => 'poll',
        'author' => $user_id,
        'posts_per_page' => $limit,
        'post_status' => 'publish',
    ));
}
