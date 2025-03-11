
<?php
/**
 * Format validation utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function existence utilities
require_once plugin_dir_path(dirname(__FILE__)) . 'function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Validate email address
 * 
 * @param string $email Email address to validate
 * @return bool True if valid, false otherwise
 */
if (pollify_can_define_function('pollify_is_valid_email')) {
    function pollify_is_valid_email($email) {
        return is_email($email);
    }
    pollify_register_function_path('pollify_is_valid_email', $current_file);
}

/**
 * Validate a date string
 * 
 * @param string $date Date string to validate (YYYY-MM-DD format)
 * @return bool True if valid, false otherwise
 */
if (pollify_can_define_function('pollify_is_valid_date')) {
    function pollify_is_valid_date($date) {
        if (empty($date)) {
            return false;
        }
        
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    pollify_register_function_path('pollify_is_valid_date', $current_file);
}
