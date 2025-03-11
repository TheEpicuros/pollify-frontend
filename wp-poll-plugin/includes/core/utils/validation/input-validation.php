
<?php
/**
 * Input validation utility functions
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
 * Validate and sanitize poll ID
 * 
 * @param mixed $poll_id The poll ID to validate
 * @return int|bool Sanitized poll ID or false if invalid
 */
if (pollify_can_define_function('pollify_validate_poll_id')) {
    function pollify_validate_poll_id($poll_id) {
        $poll_id = absint($poll_id);
        
        if ($poll_id <= 0) {
            return false;
        }
        
        // Check if post exists and is a poll
        if (!function_exists('pollify_is_valid_poll')) {
            require_once plugin_dir_path(__FILE__) . 'poll-validation.php';
        }
        
        if (!pollify_is_valid_poll($poll_id)) {
            return false;
        }
        
        return $poll_id;
    }
    pollify_register_function_path('pollify_validate_poll_id', $current_file);
}

/**
 * Validate interactive poll inputs
 * 
 * @param array $input_data Input data to validate
 * @param int $poll_id Poll ID to check against
 * @return array|bool Sanitized input data or false if invalid
 */
if (pollify_can_define_function('pollify_validate_interactive_input')) {
    function pollify_validate_interactive_input($input_data, $poll_id) {
        if (!is_array($input_data) || empty($input_data)) {
            return false;
        }
        
        if (!function_exists('pollify_get_poll_type')) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'post-types/helpers.php';
        }
        
        $poll_type = pollify_get_poll_type($poll_id);
        if ($poll_type !== 'interactive') {
            return false;
        }
        
        $sanitized_data = array();
        
        foreach ($input_data as $key => $value) {
            // Sanitize keys and values
            $sanitized_key = sanitize_text_field($key);
            
            if (is_array($value)) {
                $sanitized_value = array_map('sanitize_text_field', $value);
            } else {
                $sanitized_value = sanitize_text_field($value);
            }
            
            $sanitized_data[$sanitized_key] = $sanitized_value;
        }
        
        return $sanitized_data;
    }
    pollify_register_function_path('pollify_validate_interactive_input', $current_file);
}
