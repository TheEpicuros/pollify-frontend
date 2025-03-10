
<?php
/**
 * Admin menu registration
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register admin menu
add_action('admin_menu', 'pollify_admin_menu');

/**
 * Register the admin menu items
 */
function pollify_admin_menu() {
    add_menu_page(
        __('Pollify', 'pollify'),
        __('Pollify', 'pollify'),
        'manage_options',
        'pollify',
        'pollify_admin_page',
        'dashicons-chart-bar',
        30
    );
    
    // Dashboard page
    add_submenu_page(
        'pollify',
        __('Dashboard', 'pollify'),
        __('Dashboard', 'pollify'),
        'manage_options',
        'pollify',
        'pollify_admin_page'
    );
    
    // Add polls management pages
    add_submenu_page(
        'pollify',
        __('All Polls', 'pollify'),
        __('All Polls', 'pollify'),
        'manage_options',
        'edit.php?post_type=poll'
    );
    
    add_submenu_page(
        'pollify',
        __('Add New Poll', 'pollify'),
        __('Add New Poll', 'pollify'),
        'manage_options',
        'post-new.php?post_type=poll'
    );
    
    // Add analytics page
    add_submenu_page(
        'pollify',
        __('Analytics', 'pollify'),
        __('Analytics', 'pollify'),
        'manage_options',
        'pollify-analytics',
        'pollify_analytics_page'
    );
    
    // Add user activity page
    add_submenu_page(
        'pollify',
        __('User Activity', 'pollify'),
        __('User Activity', 'pollify'),
        'manage_options',
        'pollify-user-activity',
        'pollify_user_activity_page'
    );
    
    // Add settings page
    add_submenu_page(
        'pollify',
        __('Settings', 'pollify'),
        __('Settings', 'pollify'),
        'manage_options',
        'pollify-settings',
        'pollify_settings_page'
    );
    
    // Add help page
    add_submenu_page(
        'pollify',
        __('Help & Documentation', 'pollify'),
        __('Help & Docs', 'pollify'),
        'manage_options',
        'pollify-help',
        'pollify_help_page'
    );
}

/**
 * Admin dashboard page callback
 */
function pollify_admin_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
}

/**
 * Analytics page callback
 */
function pollify_analytics_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/analytics.php';
}

/**
 * User activity page callback
 */
function pollify_user_activity_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/user-activity.php';
}

/**
 * Help page callback
 */
function pollify_help_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/help.php';
}
