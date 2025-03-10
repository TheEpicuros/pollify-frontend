
<?php
/**
 * Display helper utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Format date for display
 */
function pollify_format_date($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    $date = date_create($date_string);
    return date_format($date, get_option('date_format') . ' ' . get_option('time_format'));
}

/**
 * Get poll status (active, ended, scheduled)
 */
function pollify_get_poll_status($poll_id) {
    $post = get_post($poll_id);
    
    if ($post->post_status === 'future') {
        return 'scheduled';
    }
    
    if (pollify_has_poll_ended($poll_id)) {
        return 'ended';
    }
    
    return 'active';
}

/**
 * Get poll type display name
 */
function pollify_get_poll_type_name($poll_id) {
    $poll_type = pollify_get_poll_type($poll_id);
    $term = get_term_by('slug', $poll_type, 'poll_type');
    
    return $term ? $term->name : __('Standard Poll', 'pollify');
}
