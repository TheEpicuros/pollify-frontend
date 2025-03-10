
<?php
/**
 * Admin-specific functionality
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue admin scripts and styles
 */
function pollify_admin_scripts($hook) {
    $admin_pages = array(
        'toplevel_page_pollify',
        'pollify_page_pollify-analytics',
        'pollify_page_pollify-user-activity',
        'pollify_page_pollify-settings',
        'pollify_page_pollify-help'
    );
    
    // Only load scripts on Pollify admin pages
    if (!in_array($hook, $admin_pages) && !in_array(get_current_screen()->post_type, array('poll'))) {
        return;
    }
    
    // Enqueue Chart.js for analytics
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
    
    // Enqueue admin CSS
    wp_enqueue_style('pollify-admin', POLLIFY_PLUGIN_URL . 'assets/css/admin.css', array(), POLLIFY_VERSION);
    
    // Enqueue admin JS
    wp_enqueue_script('pollify-admin', POLLIFY_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), POLLIFY_VERSION, true);
    
    // Add data for the JS
    wp_localize_script('pollify-admin', 'pollify', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pollify_admin_nonce'),
        'strings' => array(
            'confirm_delete' => __('Are you sure you want to delete this poll? This action cannot be undone.', 'pollify'),
            'confirm_reset' => __('Are you sure you want to reset all votes for this poll? This action cannot be undone.', 'pollify'),
        )
    ));
}

/**
 * Add plugin action links
 *
 * @param array $links Plugin action links
 * @return array Modified action links
 */
function pollify_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=pollify-settings') . '">' . __('Settings', 'pollify') . '</a>';
    $docs_link = '<a href="' . admin_url('admin.php?page=pollify-help') . '">' . __('Help', 'pollify') . '</a>';
    
    array_unshift($links, $settings_link, $docs_link);
    
    return $links;
}
