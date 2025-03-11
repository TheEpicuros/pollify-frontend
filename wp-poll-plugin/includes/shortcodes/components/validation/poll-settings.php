
<?php
/**
 * Poll settings validation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core validation functions
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get poll settings
 */
if (pollify_can_define_function('pollify_get_poll_settings')) {
    function pollify_get_poll_settings($poll_id) {
        return [
            'poll_type' => pollify_get_poll_type($poll_id),
            'poll_end_date' => get_post_meta($poll_id, '_poll_end_date', true),
            'always_show_results' => get_post_meta($poll_id, '_poll_show_results', true) === '1',
            'results_display' => get_post_meta($poll_id, '_poll_results_display', true) ?: 'bar',
            'allow_comments' => get_post_meta($poll_id, '_poll_allow_comments', true) === '1',
        ];
    }
    pollify_register_function_path('pollify_get_poll_settings', $current_file);
}
