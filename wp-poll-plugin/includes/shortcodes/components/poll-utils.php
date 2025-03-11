
<?php
/**
 * Poll utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function existence utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Format a date specifically for poll display
 */
if (pollify_can_define_function('pollify_format_poll_date')) {
    function pollify_format_poll_date($date_string) {
        $timestamp = strtotime($date_string);
        return date_i18n(get_option('date_format'), $timestamp);
    }
    pollify_register_function_path('pollify_format_poll_date', $current_file);
}
