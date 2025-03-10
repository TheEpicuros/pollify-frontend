
<?php
/**
 * IP detection utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get user IP address
 * 
 * This is a wrapper around the core utility function to maintain compatibility
 * @see wp-poll-plugin/includes/core/utils/formatting.php
 */
function pollify_get_user_ip() {
    // Include the core utility function if not already included
    if (!function_exists('pollify_get_user_ip')) {
        require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/formatting.php';
    }
    
    // Call the core function
    return pollify_get_user_ip();
}
