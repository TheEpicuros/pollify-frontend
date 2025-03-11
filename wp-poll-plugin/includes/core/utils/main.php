
<?php
/**
 * Core utilities - Main file that includes all modularized utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function existence utility first
require_once plugin_dir_path(__FILE__) . 'function-exists.php';
require_once plugin_dir_path(__FILE__) . 'function-registry.php';

// Include utility modules
require_once plugin_dir_path(__FILE__) . 'array-handling.php';
require_once plugin_dir_path(__FILE__) . 'formatting.php';
require_once plugin_dir_path(__FILE__) . 'logging.php';
require_once plugin_dir_path(__FILE__) . 'permissions.php';
require_once plugin_dir_path(__FILE__) . 'capabilities.php';
require_once plugin_dir_path(__FILE__) . 'poll-data.php';
require_once plugin_dir_path(__FILE__) . 'transients.php';
require_once plugin_dir_path(__FILE__) . 'url-handling.php';
require_once plugin_dir_path(__FILE__) . 'user-interactions.php';
require_once plugin_dir_path(__FILE__) . 'date-formatting.php';
require_once plugin_dir_path(__FILE__) . 'validation.php';
require_once plugin_dir_path(__FILE__) . 'sanitization.php';
require_once plugin_dir_path(__FILE__) . 'ip-handling.php';
require_once plugin_dir_path(__FILE__) . 'string-handling.php';
require_once plugin_dir_path(__FILE__) . 'file-handling.php';
require_once plugin_dir_path(__FILE__) . 'security.php';

/**
 * Initialize function registry early
 */
function pollify_init_function_registry() {
    // This ensures the function registry is available early
    // for all plugin components to use
    if (!isset($GLOBALS['pollify_function_registry'])) {
        $GLOBALS['pollify_function_registry'] = array();
    }
}
add_action('plugins_loaded', 'pollify_init_function_registry', 1);

/**
 * Check for function conflicts
 */
function pollify_check_function_conflicts() {
    if (WP_DEBUG && isset($GLOBALS['pollify_function_registry'])) {
        foreach ($GLOBALS['pollify_function_registry'] as $function => $path) {
            if (function_exists($function)) {
                $defined_in = (new ReflectionFunction($function))->getFileName();
                if ($defined_in !== $path) {
                    error_log(sprintf(
                        'Pollify function conflict: %s is registered in %s but defined in %s',
                        $function,
                        $path,
                        $defined_in
                    ));
                }
            }
        }
    }
}
add_action('init', 'pollify_check_function_conflicts', 999);

/**
 * Generate function conflict report
 * 
 * Only available in debug mode for administrators
 * 
 * @return array|null Function conflict report or null if not in debug mode
 */
function pollify_generate_function_report() {
    if (!WP_DEBUG || !current_user_can('manage_options')) {
        return null;
    }
    
    $report = array(
        'total_functions' => 0,
        'conflicts' => array(),
        'registered' => array()
    );
    
    if (isset($GLOBALS['pollify_function_registry'])) {
        $report['total_functions'] = count($GLOBALS['pollify_function_registry']);
        $report['registered'] = $GLOBALS['pollify_function_registry'];
        
        foreach ($GLOBALS['pollify_function_registry'] as $function => $path) {
            if (function_exists($function)) {
                $defined_in = (new ReflectionFunction($function))->getFileName();
                if ($defined_in !== $path) {
                    $report['conflicts'][$function] = array(
                        'registered_in' => $path,
                        'defined_in' => $defined_in
                    );
                }
            }
        }
    }
    
    return $report;
}
