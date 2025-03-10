
<?php
/**
 * Database management - Main file that includes all modularized database functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include setup file
require_once plugin_dir_path(__FILE__) . 'setup.php';

// Include database modules
require_once plugin_dir_path(__FILE__) . 'votes.php';
require_once plugin_dir_path(__FILE__) . 'comments.php';
require_once plugin_dir_path(__FILE__) . 'ratings.php';
require_once plugin_dir_path(__FILE__) . 'user-activity.php';

/**
 * Initialize database tables and perform any necessary upgrades
 */
function pollify_init_database() {
    // Get current database version
    $db_version = get_option('pollify_db_version', '0');
    
    // Check if we need to run database setup
    if (version_compare($db_version, POLLIFY_VERSION, '<')) {
        pollify_create_tables();
        update_option('pollify_db_version', POLLIFY_VERSION);
    }
}
add_action('plugins_loaded', 'pollify_init_database', 5);

/**
 * Get poll data from database
 */
function pollify_get_poll_data($poll_id) {
    $options = get_post_meta($poll_id, '_poll_options', true);
    if (!is_array($options)) {
        $options = array();
    }
    
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    $poll_type = pollify_get_poll_type($poll_id);
    
    $settings = array(
        'show_results' => get_post_meta($poll_id, '_poll_show_results', true) === '1',
        'results_display' => get_post_meta($poll_id, '_poll_results_display', true) ?: 'bar',
        'allow_comments' => get_post_meta($poll_id, '_poll_allow_comments', true) === '1',
        'end_date' => get_post_meta($poll_id, '_poll_end_date', true),
    );
    
    return array(
        'id' => $poll_id,
        'title' => get_the_title($poll_id),
        'description' => get_the_excerpt($poll_id),
        'options' => $options,
        'vote_counts' => $vote_counts,
        'total_votes' => $total_votes,
        'type' => $poll_type,
        'settings' => $settings,
        'author' => get_post_field('post_author', $poll_id),
        'created' => get_post_field('post_date', $poll_id),
        'status' => get_post_status($poll_id),
    );
}

/**
 * Get poll vote counts
 */
function pollify_get_vote_counts($poll_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT option_id, COUNT(*) as vote_count 
        FROM $table_name 
        WHERE poll_id = %d 
        GROUP BY option_id",
        $poll_id
    ));
    
    $counts = array();
    
    if ($results) {
        foreach ($results as $row) {
            $counts[$row->option_id] = (int) $row->vote_count;
        }
    }
    
    return $counts;
}

/**
 * Get poll type
 */
function pollify_get_poll_type($poll_id) {
    $terms = get_the_terms($poll_id, 'poll_type');
    
    if (!$terms || is_wp_error($terms)) {
        return 'multiple-choice'; // Default type
    }
    
    return $terms[0]->slug;
}

/**
 * Check if user has voted on a poll
 */
function pollify_has_user_voted($poll_id, $user_ip, $user_id = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND user_ip = %s",
        $poll_id, $user_ip
    );
    
    if ($user_id) {
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND (user_ip = %s OR user_id = %d)",
            $poll_id, $user_ip, $user_id
        );
    }
    
    $result = $wpdb->get_var($query);
    
    return (int) $result > 0;
}

/**
 * Get user's vote on a poll
 */
function pollify_get_user_vote($poll_id, $user_ip, $user_id = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pollify_votes';
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE poll_id = %d AND user_ip = %s ORDER BY voted_at DESC LIMIT 1",
        $poll_id, $user_ip
    );
    
    if ($user_id) {
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE poll_id = %d AND (user_ip = %s OR user_id = %d) ORDER BY voted_at DESC LIMIT 1",
            $poll_id, $user_ip, $user_id
        );
    }
    
    return $wpdb->get_row($query);
}

/**
 * Check if user can vote on a poll
 */
function pollify_can_user_vote($poll_id) {
    $allowed_roles = get_post_meta($poll_id, '_poll_allowed_roles', true);
    
    // If no specific roles are set, allow all
    if (empty($allowed_roles)) {
        return true;
    }
    
    // Check if guest voting is allowed
    if (in_array('guest', $allowed_roles) && !is_user_logged_in()) {
        return true;
    }
    
    // If user is logged in, check their role
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        
        foreach ($user->roles as $role) {
            if (in_array($role, $allowed_roles)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Check if poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false;
    }
    
    $now = current_time('mysql');
    
    return strtotime($end_date) < strtotime($now);
}
