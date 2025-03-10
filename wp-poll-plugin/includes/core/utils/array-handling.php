
<?php
/**
 * Array handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize array of values
 */
function pollify_sanitize_array($array, $sanitize_function = 'sanitize_text_field') {
    if (!is_array($array)) {
        return array();
    }
    
    return array_map($sanitize_function, $array);
}
