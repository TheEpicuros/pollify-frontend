
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

// Include required files
require_once POLLIFY_PLUGIN_DIR . 'includes/database.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';

// Activation hook
register_activation_hook(__FILE__, 'pollify_activate_plugin');

function pollify_activate_plugin() {
    // Create database tables
    require_once POLLIFY_PLUGIN_DIR . 'includes/database.php';
    pollify_create_tables();
    
    // Register custom post type
    pollify_register_post_types();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');

function pollify_deactivate_plugin() {
    // Flush rewrite rules
    flush_rewrite_rules();
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
    
    // Pass WordPress data to JS
    wp_localize_script('pollify-script', 'pollifyData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pollify-nonce'),
        'siteUrl' => get_site_url(),
        'features' => array(
            'animatedProgress' => true
        )
    ));
}
add_action('wp_enqueue_scripts', 'pollify_enqueue_scripts');
