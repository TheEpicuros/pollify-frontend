
<?php
/**
 * Function existence checker utility
 * 
 * This file provides utilities to safely declare functions
 * across multiple files without redeclaration errors.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the function registry
require_once plugin_dir_path(__FILE__) . 'function-registry.php';

/**
 * Safely declare a function if it doesn't exist already
 * 
 * @param string $function_name The name of the function
 * @param callable $function_definition The function implementation
 * @param string $file_path The file path where the function is being defined
 * @return bool Whether the function was declared
 */
function pollify_safe_declare_function($function_name, $function_definition, $file_path = null) {
    if ($file_path === null) {
        // Try to determine the file path automatically
        $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $file_path = isset($debug_backtrace[0]['file']) ? $debug_backtrace[0]['file'] : __FILE__;
    }
    
    return pollify_define_function($function_name, $function_definition, $file_path);
}

/**
 * Register a canonical function path
 * 
 * @param string $function_name The name of the function
 * @param string $file_path The canonical file path where the function is defined
 * @return bool Whether the function was registered
 */
function pollify_register_function_path($function_name, $file_path) {
    return pollify_register_function($function_name, $file_path);
}

/**
 * Require the canonical file for a function
 * 
 * @param string $function_name The name of the function
 * @return bool Whether the file was required
 */
function pollify_require_function($function_name) {
    $file_path = pollify_get_function_path($function_name);
    
    if ($file_path && file_exists($file_path)) {
        require_once $file_path;
        return true;
    }
    
    return false;
}

/**
 * Check if a function should be defined in current file
 * 
 * @param string $function_name The name of the function to check
 * @return bool True if function should be defined, false otherwise
 */
function pollify_can_define_function($function_name) {
    $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $current_file = isset($debug_backtrace[0]['file']) ? $debug_backtrace[0]['file'] : __FILE__;
    
    return pollify_should_define_function($function_name, $current_file);
}
