
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
    
    add_submenu_page(
        'pollify',
        __('Settings', 'pollify'),
        __('Settings', 'pollify'),
        'manage_options',
        'pollify-settings',
        'pollify_settings_page'
    );
    
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
}

/**
 * Admin dashboard page callback
 */
function pollify_admin_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
}
