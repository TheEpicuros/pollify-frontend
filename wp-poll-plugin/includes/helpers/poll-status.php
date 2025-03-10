
<?php
/**
 * Poll status helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if a poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false;
    }
    
    $end_timestamp = strtotime($end_date);
    $current_timestamp = current_time('timestamp');
    
    return $end_timestamp < $current_timestamp;
}
