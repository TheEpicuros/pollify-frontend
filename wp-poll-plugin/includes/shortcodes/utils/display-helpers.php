
<?php
/**
 * Display helper utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

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
 * Get poll type display name - this is a wrapper for the canonical function
 */
function pollify_get_poll_type_name($poll_id) {
    // Include the canonical implementation
    if (!function_exists('pollify_get_poll_type_name_from_taxonomy')) {
        require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'helpers/poll-types.php';
    }
    
    // Ensure the function exists before calling it
    if (function_exists('pollify_get_poll_type_name')) {
        return pollify_get_poll_type_name($poll_id);
    }
    
    // Fallback implementation if canonical function is unavailable
    $poll_type = pollify_get_poll_type($poll_id);
    $term = get_term_by('slug', $poll_type, 'poll_type');
    
    return $term ? $term->name : __('Standard Poll', 'pollify');
}
