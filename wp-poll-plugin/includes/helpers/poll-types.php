
<?php
/**
 * Poll types helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get poll type display name - registered as the canonical function
 *
 * @param int $poll_id Poll ID
 * @return string Poll type name
 */
if (pollify_can_define_function('pollify_get_poll_type_name')) {
    function pollify_get_poll_type_name($poll_id) {
        // Include the core utility function if not already included
        if (!function_exists('pollify_get_poll_type_name_from_taxonomy')) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'post-types/taxonomies.php';
        }
        
        return pollify_get_poll_type_name_from_taxonomy($poll_id);
    }
    pollify_register_function_path('pollify_get_poll_type_name', $current_file);
}

/**
 * Get poll type description - registered as the canonical function
 * 
 * @param string $poll_type Poll type slug
 * @return string Poll type description
 */
if (pollify_can_define_function('pollify_get_poll_type_description')) {
    function pollify_get_poll_type_description($poll_type) {
        $term = get_term_by('slug', $poll_type, 'poll_type');
        
        if ($term && !is_wp_error($term)) {
            return $term->description;
        }
        
        return __('Standard poll with multiple options.', 'pollify');
    }
    pollify_register_function_path('pollify_get_poll_type_description', $current_file);
}
