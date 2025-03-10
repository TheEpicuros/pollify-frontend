
<?php
/**
 * Poll status helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Wrapper function to maintain compatibility with the canonical function 
 * in database/poll-status.php
 * 
 * @param int $poll_id Poll ID
 * @return bool Whether the poll has ended
 */
function pollify_has_poll_ended($poll_id) {
    // Include the core utility function if not already included
    if (!function_exists('pollify_has_poll_ended_db')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/poll-status.php';
    }
    
    return pollify_has_poll_ended_db($poll_id);
}
