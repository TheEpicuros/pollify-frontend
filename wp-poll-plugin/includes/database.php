
<?php
/**
 * Database setup and functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create the necessary database tables
 */
function pollify_create_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Polls votes table
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        poll_id bigint(20) unsigned NOT NULL,
        option_id varchar(255) NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        user_ip varchar(100) NOT NULL,
        voted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY poll_id (poll_id),
        KEY user_ip (user_ip),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // Poll ratings table
    $ratings_table = $wpdb->prefix . 'pollify_ratings';
    
    $sql_ratings = "CREATE TABLE $ratings_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        poll_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        user_ip varchar(100) NOT NULL,
        rating tinyint(1) NOT NULL,
        rated_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY poll_id (poll_id),
        KEY user_id (user_id),
        UNIQUE KEY poll_user (poll_id, user_id)
    ) $charset_collate;";
    
    // Poll comments table
    $comments_table = $wpdb->prefix . 'pollify_comments';
    
    $sql_comments = "CREATE TABLE $comments_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        poll_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        user_name varchar(100) NOT NULL,
        comment_text text NOT NULL,
        comment_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY poll_id (poll_id),
        KEY user_id (user_id)
    ) $charset_collate;";
    
    // User activity tracking table
    $activity_table = $wpdb->prefix . 'pollify_user_activity';
    
    $sql_activity = "CREATE TABLE $activity_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        activity_type varchar(50) NOT NULL,
        poll_id bigint(20) unsigned DEFAULT NULL,
        points int(11) DEFAULT 0,
        activity_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY activity_type (activity_type)
    ) $charset_collate;";
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    dbDelta($sql_ratings);
    dbDelta($sql_comments);
    dbDelta($sql_activity);
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
