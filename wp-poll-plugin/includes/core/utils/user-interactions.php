
<?php
/**
 * User interaction utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if AJAX request
 */
function pollify_is_ajax() {
    return defined('DOING_AJAX') && DOING_AJAX;
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
