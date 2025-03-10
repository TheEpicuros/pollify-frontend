
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

/**
 * Safely declare a function if it doesn't exist already
 * 
 * @param string $function_name The name of the function
 * @param callable $function_definition The function implementation
 * @return bool Whether the function was declared
 */
function pollify_safe_declare_function($function_name, $function_definition) {
    if (function_exists($function_name)) {
        return false;
    }
    
    // Use eval to dynamically define the function
    $function_string = 'function ' . $function_name . '() {
        $args = func_get_args();
        return call_user_func_array($function_definition, $args);
    }';
    
    eval($function_string);
    return true;
}

/**
 * Register a canonical function path
 * 
 * @param string $function_name The name of the function
 * @param string $file_path The canonical file path where the function is defined
 */
function pollify_register_function($function_name, $file_path) {
    global $pollify_function_registry;
    
    if (!isset($pollify_function_registry)) {
        $pollify_function_registry = array();
    }
    
    $pollify_function_registry[$function_name] = $file_path;
}

/**
 * Get the canonical file path for a function
 * 
 * @param string $function_name The name of the function
 * @return string|null The canonical file path or null if not registered
 */
function pollify_get_function_path($function_name) {
    global $pollify_function_registry;
    
    if (!isset($pollify_function_registry) || !isset($pollify_function_registry[$function_name])) {
        return null;
    }
    
    return $pollify_function_registry[$function_name];
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
