
<?php
/**
 * Plugin activation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activation hook
 */
function pollify_activate_plugin() {
    global $wpdb;
    
    // Enable error reporting during activation for debugging
    $old_error_reporting = error_reporting(E_ALL);
    $old_display_errors = ini_get('display_errors');
    ini_set('display_errors', 1);
    
    try {
        // Create custom database tables
        if (function_exists('pollify_create_tables')) {
            pollify_create_tables();
        } else {
            error_log('Pollify Error: pollify_create_tables function not found during activation');
        }
        
        // Create default options
        pollify_create_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Log successful activation
        error_log('Pollify plugin activated successfully');
    } catch (Exception $e) {
        // Log any errors during activation
        error_log('Pollify Activation Error: ' . $e->getMessage());
    }
    
    // Restore original error reporting settings
    error_reporting($old_error_reporting);
    ini_set('display_errors', $old_display_errors);
}

/**
 * Create default plugin options
 */
function pollify_create_default_options() {
    $default_options = array(
        'loading_animation' => 1,
        'allow_guests' => 1,
        'show_results_before_vote' => 0,
        'enable_comments' => 1,
        'enable_ratings' => 1,
        'enable_social_sharing' => 1,
        'admin_notification_new_poll' => 1,
        'admin_notification_email' => get_option('admin_email')
    );
    
    if (!get_option('pollify_settings')) {
        add_option('pollify_settings', $default_options);
    }
}
