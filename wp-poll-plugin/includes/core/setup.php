
<?php
/**
 * Plugin initialization and setup
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

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

// Plugin activation, deactivation hooks are registered in the main file
