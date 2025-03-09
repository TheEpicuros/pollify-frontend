
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
        user_ip varchar(100) NOT NULL,
        voted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY poll_id (poll_id),
        KEY user_ip (user_ip)
    ) $charset_collate;";
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * Check if a user has already voted on a poll
 */
function pollify_has_user_voted($poll_id, $user_ip) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND user_ip = %s",
        $poll_id,
        $user_ip
    ));
    
    return $result > 0;
}

/**
 * Record a vote for a poll option
 */
function pollify_record_vote($poll_id, $option_id, $user_ip) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'poll_id' => $poll_id,
            'option_id' => $option_id,
            'user_ip' => $user_ip,
            'voted_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s')
    );
    
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
