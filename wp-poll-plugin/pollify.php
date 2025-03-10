
<?php
/**
 * Plugin Name: Pollify - Interactive Polls
 * Plugin URI: https://example.com/pollify
 * Description: A fully-featured, modern polling system for WordPress with real-time results, multiple poll types, and gamification.
 * Version: 1.0.0
 * Author: Pollify Team
 * Author URI: https://example.com
 * Text Domain: pollify
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POLLIFY_PLUGIN_FILE', __FILE__);
define('POLLIFY_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include core files
require_once POLLIFY_PLUGIN_DIR . 'includes/core/constants.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils.php';

// Plugin activation, deactivation, and uninstall
register_activation_hook(__FILE__, 'pollify_activate_plugin');
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');
require_once POLLIFY_PLUGIN_DIR . 'includes/core/activation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/deactivation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/uninstall.php';

// Database setup and access
require_once POLLIFY_PLUGIN_DIR . 'includes/database/main.php';

// Include admin files
if (is_admin()) {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-menu.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/analytics.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/user-activity.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/help.php';
    
    // Admin notices and other admin-specific functionality
    add_action('admin_enqueue_scripts', 'pollify_admin_scripts');
}

// Include front-end files
require_once POLLIFY_PLUGIN_DIR . 'includes/assets/enqueue.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';

// Include AJAX handlers
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';

// Include REST API endpoints
require_once POLLIFY_PLUGIN_DIR . 'includes/api/rest-api.php';

// Helper functions
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

/**
 * Initialize the plugin
 */
function pollify_init() {
    // Load plugin textdomain
    load_plugin_textdomain('pollify', false, dirname(POLLIFY_PLUGIN_BASENAME) . '/languages');
    
    // Additional initialization
    do_action('pollify_init');
}
add_action('plugins_loaded', 'pollify_init');

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
add_filter('plugin_action_links_' . POLLIFY_PLUGIN_BASENAME, 'pollify_add_settings_link');

/**
 * Create poll export Ajax handler
 */
add_action('wp_ajax_pollify_export_analytics', 'pollify_export_analytics_callback');
function pollify_export_analytics_callback() {
    // Check nonce
    check_admin_referer('pollify_export_analytics');
    
    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to export poll data.', 'pollify'));
    }
    
    // Get filter parameters
    $period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'all';
    $poll_id = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;
    
    // Get data
    global $wpdb;
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    // Build query
    $where_clauses = array();
    
    if ($poll_id > 0) {
        $where_clauses[] = $wpdb->prepare("poll_id = %d", $poll_id);
    }
    
    switch ($period) {
        case 'today':
            $where_clauses[] = "DATE(vote_date) = CURDATE()";
            break;
        case 'yesterday':
            $where_clauses[] = "DATE(vote_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $where_clauses[] = "vote_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_clauses[] = "vote_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
    
    $where_clause = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    $query = "SELECT v.*, p.post_title as poll_title 
              FROM $votes_table v
              LEFT JOIN {$wpdb->posts} p ON v.poll_id = p.ID
              $where_clause
              ORDER BY vote_date DESC";
    
    $votes = $wpdb->get_results($query);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pollify-export-' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV header row
    fputcsv($output, array(
        __('Poll ID', 'pollify'),
        __('Poll Title', 'pollify'),
        __('Option ID', 'pollify'),
        __('User ID', 'pollify'),
        __('IP Address', 'pollify'),
        __('Vote Date', 'pollify')
    ));
    
    // Add data rows
    foreach ($votes as $vote) {
        fputcsv($output, array(
            $vote->poll_id,
            $vote->poll_title,
            $vote->option_id,
            $vote->user_id > 0 ? $vote->user_id : __('Guest', 'pollify'),
            $vote->user_ip,
            $vote->vote_date
        ));
    }
    
    fclose($output);
    exit;
}
