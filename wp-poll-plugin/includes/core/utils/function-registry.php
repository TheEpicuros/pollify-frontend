
<?php
/**
 * Function registry utility
 * 
 * This file provides a centralized registry for all plugin functions
 * to prevent function redeclaration errors across multiple files.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Initialize the function registry if it doesn't exist
if (!isset($GLOBALS['pollify_function_registry'])) {
    $GLOBALS['pollify_function_registry'] = array();
}

// Only define these functions if they don't already exist
if (!function_exists('pollify_register_function')) {
    /**
     * Register a function in the global registry
     * 
     * @param string $function_name The name of the function
     * @param string $file_path The canonical file path where the function is defined
     * @return bool Whether the function was registered
     */
    function pollify_register_function($function_name, $file_path) {
        if (isset($GLOBALS['pollify_function_registry'][$function_name])) {
            // Function already registered
            return false;
        }
        
        $GLOBALS['pollify_function_registry'][$function_name] = $file_path;
        return true;
    }
}

if (!function_exists('pollify_is_function_registered')) {
    /**
     * Check if a function is registered in the registry
     * 
     * @param string $function_name The name of the function
     * @return bool Whether the function is registered
     */
    function pollify_is_function_registered($function_name) {
        return isset($GLOBALS['pollify_function_registry'][$function_name]);
    }
}

if (!function_exists('pollify_get_function_path')) {
    /**
     * Get the canonical file path for a registered function
     * 
     * @param string $function_name The name of the function
     * @return string|null The canonical file path or null if not registered
     */
    function pollify_get_function_path($function_name) {
        if (!isset($GLOBALS['pollify_function_registry'][$function_name])) {
            return null;
        }
        
        return $GLOBALS['pollify_function_registry'][$function_name];
    }
}

if (!function_exists('pollify_define_function')) {
    /**
     * Safely define a function if it doesn't already exist
     * 
     * @param string $function_name The name of the function
     * @param callable $function_definition The function implementation
     * @param string $file_path The file path where the function is being defined
     * @return bool Whether the function was defined
     */
    function pollify_define_function($function_name, $function_definition, $file_path) {
        // Check if function already exists
        if (function_exists($function_name)) {
            // Log a warning about attempted redefinition
            $registered_path = pollify_get_function_path($function_name);
            if ($registered_path && $registered_path !== $file_path) {
                $message = sprintf(
                    'Warning: Attempted to redefine function %s in %s but it is already defined in %s',
                    $function_name,
                    $file_path,
                    $registered_path
                );
                if (function_exists('error_log')) {
                    error_log($message);
                }
            }
            return false;
        }
        
        // Register the function
        pollify_register_function($function_name, $file_path);
        
        // Define the function
        $args = array();
        $function_string = 'function ' . $function_name . '(';
        $param_count = 0;
        
        // Add parameters to the function definition
        $reflection = new ReflectionFunction($function_definition);
        $parameters = $reflection->getParameters();
        
        foreach ($parameters as $i => $param) {
            if ($i > 0) {
                $function_string .= ', ';
            }
            
            $function_string .= '$' . $param->getName();
            
            if ($param->isOptional()) {
                $default = $param->getDefaultValue();
                if (is_string($default)) {
                    $function_string .= " = '" . addslashes($default) . "'";
                } elseif (is_array($default)) {
                    $function_string .= " = array()";
                } elseif (is_bool($default)) {
                    $function_string .= " = " . ($default ? 'true' : 'false');
                } elseif (is_null($default)) {
                    $function_string .= " = null";
                } elseif (is_int($default) || is_float($default)) {
                    $function_string .= " = " . $default;
                }
            }
            
            $param_count++;
        }
        
        $function_string .= ') {
            $args = func_get_args();
            return call_user_func_array($function_definition, $args);
        }';
        
        // Use eval to define the function dynamically
        eval($function_string);
        
        return true;
    }
}

if (!function_exists('pollify_should_define_function')) {
    /**
     * Check if a function should be defined or imported
     * 
     * @param string $function_name The name of the function
     * @param string $current_file The current file path
     * @return bool Whether the function should be defined in the current file
     */
    function pollify_should_define_function($function_name, $current_file) {
        if (function_exists($function_name)) {
            return false;
        }
        
        $canonical_path = pollify_get_function_path($function_name);
        
        if ($canonical_path && $canonical_path !== $current_file) {
            // Function is registered in another file, so require that file
            require_once $canonical_path;
            return false;
        }
        
        return true;
    }
}
