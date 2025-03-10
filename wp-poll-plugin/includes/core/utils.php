
<?php
/**
 * Utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize array of values
 */
function pollify_sanitize_array($array, $sanitize_function = 'sanitize_text_field') {
    if (!is_array($array)) {
        return array();
    }
    
    return array_map($sanitize_function, $array);
}

/**
 * Check if current user can manage polls
 */
function pollify_current_user_can_manage() {
    return current_user_can(POLLIFY_ADMIN_CAPABILITY);
}

/**
 * Generate a unique ID for tracking votes
 */
function pollify_generate_vote_id() {
    return md5(uniqid(rand(), true));
}

/**
 * Format number with suffix (K, M, etc)
 */
function pollify_format_number($number) {
    $number = (int) $number;
    
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    }
    
    if ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    
    return number_format_i18n($number);
}

/**
 * Get current page URL
 */
function pollify_get_current_url() {
    global $wp;
    return home_url(add_query_arg(array(), $wp->request));
}

/**
 * Check if AJAX request
 */
function pollify_is_ajax() {
    return defined('DOING_AJAX') && DOING_AJAX;
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
 * Format relative time
 */
function pollify_time_ago($timestamp) {
    $time_diff = time() - strtotime($timestamp);
    
    if ($time_diff < 60) {
        return __('just now', 'pollify');
    }
    
    if ($time_diff < 3600) {
        $mins = round($time_diff / 60);
        return sprintf(_n('%s minute ago', '%s minutes ago', $mins, 'pollify'), $mins);
    }
    
    if ($time_diff < 86400) {
        $hours = round($time_diff / 3600);
        return sprintf(_n('%s hour ago', '%s hours ago', $hours, 'pollify'), $hours);
    }
    
    if ($time_diff < 604800) {
        $days = round($time_diff / 86400);
        return sprintf(_n('%s day ago', '%s days ago', $days, 'pollify'), $days);
    }
    
    if ($time_diff < 2592000) {
        $weeks = round($time_diff / 604800);
        return sprintf(_n('%s week ago', '%s weeks ago', $weeks, 'pollify'), $weeks);
    }
    
    if ($time_diff < 31536000) {
        $months = round($time_diff / 2592000);
        return sprintf(_n('%s month ago', '%s months ago', $months, 'pollify'), $months);
    }
    
    $years = round($time_diff / 31536000);
    return sprintf(_n('%s year ago', '%s years ago', $years, 'pollify'), $years);
}

/**
 * Log debug information
 */
function pollify_log($message, $level = 'debug') {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    if (is_array($message) || is_object($message)) {
        error_log('[POLLIFY-' . strtoupper($level) . '] ' . print_r($message, true));
    } else {
        error_log('[POLLIFY-' . strtoupper($level) . '] ' . $message);
    }
}

/**
 * Clean up expired transients
 */
function pollify_cleanup_transients() {
    global $wpdb;
    
    $sql = "DELETE FROM $wpdb->options 
            WHERE option_name LIKE '%_transient_pollify_%' 
            AND option_name NOT LIKE '%_transient_timeout_pollify_%'";
    
    $wpdb->query($sql);
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

/**
 * Check if user can see poll results
 */
function pollify_can_user_see_results($poll_id) {
    // Check if always show results is enabled
    $always_show = get_post_meta($poll_id, '_poll_show_results', true) === '1';
    
    if ($always_show) {
        return true;
    }
    
    // Check if user has voted
    $user_ip = pollify_get_user_ip();
    $user_id = get_current_user_id();
    
    return pollify_has_user_voted($poll_id, $user_ip, $user_id);
}
