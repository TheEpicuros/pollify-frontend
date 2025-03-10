
<?php
/**
 * Plugin activation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin activation hook callback
 */
function pollify_activate_plugin() {
    // Create database tables
    require_once POLLIFY_PLUGIN_DIR . 'includes/database.php';
    pollify_create_tables();
    
    // Register custom post type
    pollify_register_post_types();
    
    // Set default options
    pollify_set_default_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Log activation
    error_log('Pollify plugin activated successfully');
}

/**
 * Set default options
 */
function pollify_set_default_options() {
    // Check if options already exist
    if (get_option('pollify_settings')) {
        return;
    }
    
    // Default settings
    $default_settings = array(
        'allow_guests' => true,
        'results_display' => 'bar',
        'show_results_before_vote' => false,
        'enable_comments' => true,
        'enable_ratings' => true,
        'enable_social_sharing' => true,
        'poll_archive_page' => 0,
        'polls_per_page' => 10,
        'loading_animation' => true
    );
    
    // Save default settings
    update_option('pollify_settings', $default_settings);
    update_option('pollify_delete_data_on_uninstall', false);
}
