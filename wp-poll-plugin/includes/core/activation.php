
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
    // Create custom database tables
    if (function_exists('pollify_create_tables')) {
        pollify_create_tables();
    }
    
    // Create default options
    pollify_create_default_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
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
