
<?php
/**
 * Plugin Name: Pollify - React Polling System
 * Plugin URI: https://example.com/pollify
 * Description: A modern polling system with React frontend
 * Version: 1.0.0
 * Author: Lovable
 * Author URI: https://lovable.ai
 * Text Domain: pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POLLIFY_ADMIN_URL', admin_url('admin.php?page=pollify'));

// Include required files
require_once POLLIFY_PLUGIN_DIR . 'includes/database.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

// Activation hook
register_activation_hook(__FILE__, 'pollify_activate_plugin');

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

// Deactivation hook
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');

function pollify_deactivate_plugin() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Log deactivation
    error_log('Pollify plugin deactivated');
}

// Uninstall hook - clean up data if plugin is deleted
register_uninstall_hook(__FILE__, 'pollify_uninstall_plugin');

function pollify_uninstall_plugin() {
    // Only run if WP_UNINSTALL_PLUGIN is defined
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }
    
    // Get options
    $delete_data = get_option('pollify_delete_data_on_uninstall', false);
    
    // If set to delete data, remove database tables and options
    if ($delete_data) {
        global $wpdb;
        
        // Delete database tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_votes");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_ratings");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_comments");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_user_activity");
        
        // Delete all poll posts
        $polls = get_posts(array(
            'post_type' => 'poll',
            'numberposts' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($polls as $poll) {
            wp_delete_post($poll->ID, true);
        }
        
        // Delete options
        delete_option('pollify_settings');
        delete_option('pollify_delete_data_on_uninstall');
    }
    
    // Log uninstall
    error_log('Pollify plugin uninstalled');
}

// Set default options
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

// Admin menu
add_action('admin_menu', 'pollify_admin_menu');

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

// Admin dashboard page
function pollify_admin_page() {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
}

// Enqueue scripts and styles
function pollify_enqueue_scripts() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');
    
    // Enqueue plugin CSS
    wp_enqueue_style(
        'pollify-styles', 
        POLLIFY_PLUGIN_URL . 'assets/css/pollify.css', 
        array(), 
        POLLIFY_VERSION
    );
    
    // Enqueue plugin JS
    wp_enqueue_script(
        'pollify-script', 
        POLLIFY_PLUGIN_URL . 'assets/js/pollify.js', 
        array('jquery'), 
        POLLIFY_VERSION, 
        true
    );
    
    // Get settings
    $settings = get_option('pollify_settings', array());
    
    // Pass WordPress data to JS
    wp_localize_script('pollify-script', 'pollifyData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pollify-nonce'),
        'siteUrl' => get_site_url(),
        'features' => array(
            'animatedProgress' => isset($settings['loading_animation']) ? (bool) $settings['loading_animation'] : true,
            'allowGuests' => isset($settings['allow_guests']) ? (bool) $settings['allow_guests'] : true,
            'showResultsBeforeVote' => isset($settings['show_results_before_vote']) ? (bool) $settings['show_results_before_vote'] : false,
            'enableComments' => isset($settings['enable_comments']) ? (bool) $settings['enable_comments'] : true,
            'enableRatings' => isset($settings['enable_ratings']) ? (bool) $settings['enable_ratings'] : true,
            'enableSocialSharing' => isset($settings['enable_social_sharing']) ? (bool) $settings['enable_social_sharing'] : true
        ),
        'error' => array(
            'generic' => __('An error occurred. Please try again.', 'pollify'),
            'ajaxFailed' => __('Failed to communicate with the server.', 'pollify'),
            'alreadyVoted' => __('You have already voted on this poll.', 'pollify'),
            'invalidOption' => __('Please select a valid option.', 'pollify'),
            'notLoggedIn' => __('You must be logged in to perform this action.', 'pollify')
        )
    ));
}
add_action('wp_enqueue_scripts', 'pollify_enqueue_scripts');

// Admin scripts and styles
function pollify_admin_enqueue_scripts($hook) {
    // Only load on our plugin pages
    if (strpos($hook, 'pollify') === false && get_post_type() !== 'poll') {
        return;
    }
    
    // Enqueue admin CSS
    wp_enqueue_style(
        'pollify-admin-styles', 
        POLLIFY_PLUGIN_URL . 'assets/css/pollify-admin.css', 
        array(), 
        POLLIFY_VERSION
    );
    
    // Enqueue admin JS
    wp_enqueue_script(
        'pollify-admin-script', 
        POLLIFY_PLUGIN_URL . 'assets/js/pollify-admin.js', 
        array('jquery', 'wp-color-picker'), 
        POLLIFY_VERSION, 
        true
    );
    
    // Add color picker
    wp_enqueue_style('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'pollify_admin_enqueue_scripts');

// Add settings link on plugin page
function pollify_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=pollify-settings') . '">' . __('Settings', 'pollify') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'pollify_add_settings_link');

// Error logging for debugging
function pollify_log($message, $level = 'info') {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

// Register REST API endpoints
add_action('rest_api_init', 'pollify_register_rest_routes');

function pollify_register_rest_routes() {
    register_rest_route('pollify/v1', '/polls', array(
        'methods' => 'GET',
        'callback' => 'pollify_api_get_polls',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('pollify/v1', '/polls/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'pollify_api_get_poll',
        'permission_callback' => '__return_true'
    ));
    
    register_rest_route('pollify/v1', '/vote', array(
        'methods' => 'POST',
        'callback' => 'pollify_api_vote',
        'permission_callback' => '__return_true'
    ));
}

// REST API handlers
function pollify_api_get_polls($request) {
    // REST API implementation for getting polls
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => 10,
        'post_status' => 'publish'
    );
    
    $polls = get_posts($args);
    $formatted_polls = array();
    
    foreach ($polls as $poll) {
        $formatted_polls[] = pollify_format_poll_for_api($poll);
    }
    
    return rest_ensure_response($formatted_polls);
}

function pollify_api_get_poll($request) {
    $poll_id = $request['id'];
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return new WP_Error('not_found', 'Poll not found', array('status' => 404));
    }
    
    $formatted_poll = pollify_format_poll_for_api($poll);
    return rest_ensure_response($formatted_poll);
}

function pollify_api_vote($request) {
    $poll_id = isset($request['poll_id']) ? absint($request['poll_id']) : 0;
    $option_id = isset($request['option_id']) ? sanitize_text_field($request['option_id']) : '';
    
    if (!$poll_id || !$option_id) {
        return new WP_Error('missing_data', 'Missing poll or option', array('status' => 400));
    }
    
    // Check if poll exists
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return new WP_Error('not_found', 'Poll not found', array('status' => 404));
    }
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || !isset($options[$option_id])) {
        return new WP_Error('invalid_option', 'Invalid poll option', array('status' => 400));
    }
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    
    if (pollify_has_user_voted($poll_id, $user_ip)) {
        return new WP_Error('already_voted', 'You have already voted on this poll', array('status' => 403));
    }
    
    // Record the vote
    $success = pollify_record_vote($poll_id, $option_id, $user_ip);
    
    if (!$success) {
        return new WP_Error('vote_failed', 'Failed to record vote', array('status' => 500));
    }
    
    // Get updated vote counts
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    // Prepare results data
    $results = array();
    
    foreach ($options as $opt_id => $opt_text) {
        $vote_count = isset($vote_counts[$opt_id]) ? $vote_counts[$opt_id] : 0;
        $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
        
        $results[] = array(
            'id' => $opt_id,
            'text' => $opt_text,
            'votes' => $vote_count,
            'percentage' => $percentage
        );
    }
    
    return rest_ensure_response(array(
        'message' => 'Vote recorded successfully',
        'results' => $results,
        'totalVotes' => $total_votes
    ));
}

// Helper function to format poll data for API
function pollify_format_poll_for_api($poll) {
    $poll_id = $poll->ID;
    $options = get_post_meta($poll_id, '_poll_options', true);
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    $formatted_options = array();
    
    foreach ($options as $option_id => $option_text) {
        $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
        
        $formatted_options[] = array(
            'id' => $option_id,
            'text' => $option_text,
            'votes' => $vote_count
        );
    }
    
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    $status = empty($end_date) || strtotime($end_date) > current_time('timestamp') ? 'active' : 'closed';
    
    return array(
        'id' => $poll_id,
        'title' => get_the_title($poll),
        'description' => $poll->post_content,
        'options' => $formatted_options,
        'createdAt' => get_the_date('c', $poll),
        'createdBy' => get_the_author_meta('display_name', $poll->post_author),
        'status' => $status,
        'totalVotes' => $total_votes
    );
}
