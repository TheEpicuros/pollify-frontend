
<?php
/**
 * Poll types helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * This is a wrapper function to maintain compatibility and redirect to the 
 * canonical function in taxonomies.php
 *
 * @param int $poll_id Poll ID
 * @return string Poll type name
 */
function pollify_get_poll_type_name($poll_id) {
    // Include the core utility function if not already included
    if (!function_exists('pollify_get_poll_type_name_from_taxonomy')) {
        require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'post-types/taxonomies.php';
    }
    
    return pollify_get_poll_type_name_from_taxonomy($poll_id);
}
