
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
    pollify_create_tables();
    
    // Register post types
    pollify_register_post_types();
    
    // Register taxonomies
    pollify_register_taxonomies();
    
    // Create default poll types
    pollify_create_default_poll_types();
    
    // Setup user role capabilities
    pollify_setup_capabilities();
    
    // Create default options
    pollify_create_default_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Create default poll types
 */
function pollify_create_default_poll_types() {
    $default_types = array(
        'multiple-choice' => 'Multiple Choice',
        'check-all' => 'Multiple Answers',
        'binary' => 'Yes/No Question',
        'rating-scale' => 'Rating Scale',
        'image-based' => 'Image Poll',
        'quiz' => 'Quiz',
        'open-ended' => 'Open-Ended',
        'ranked-choice' => 'Ranked Choice'
    );
    
    foreach ($default_types as $slug => $name) {
        if (!term_exists($slug, 'poll_type')) {
            wp_insert_term($name, 'poll_type', array('slug' => $slug));
        }
    }
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
