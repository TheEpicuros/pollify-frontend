
<?php
/**
 * Database tables setup
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
    
    // Enable error reporting
    $wpdb->show_errors();
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Polls votes table
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
    
    $sql_ratings = "CREATE TABLE IF NOT EXISTS $ratings_table (
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
    
    $sql_comments = "CREATE TABLE IF NOT EXISTS $comments_table (
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
    
    $sql_activity = "CREATE TABLE IF NOT EXISTS $activity_table (
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
    
    // Use dbDelta to create or update tables
    if (!function_exists('dbDelta')) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }
    
    // Create tables one by one and check for errors
    $result = dbDelta($sql);
    if ($wpdb->last_error) {
        error_log('Pollify Error creating votes table: ' . $wpdb->last_error);
    }
    
    $result = dbDelta($sql_ratings);
    if ($wpdb->last_error) {
        error_log('Pollify Error creating ratings table: ' . $wpdb->last_error);
    }
    
    $result = dbDelta($sql_comments);
    if ($wpdb->last_error) {
        error_log('Pollify Error creating comments table: ' . $wpdb->last_error);
    }
    
    $result = dbDelta($sql_activity);
    if ($wpdb->last_error) {
        error_log('Pollify Error creating activity table: ' . $wpdb->last_error);
    }
}
