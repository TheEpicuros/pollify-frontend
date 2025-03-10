
<?php
/**
 * Database management - Main file that includes all modularized database functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include setup file
require_once plugin_dir_path(__FILE__) . 'setup.php';

// Include database modules
require_once plugin_dir_path(__FILE__) . 'votes.php';
require_once plugin_dir_path(__FILE__) . 'comments.php';
require_once plugin_dir_path(__FILE__) . 'ratings.php';
require_once plugin_dir_path(__FILE__) . 'user-activity.php';
require_once plugin_dir_path(__FILE__) . 'poll-data.php';
require_once plugin_dir_path(__FILE__) . 'poll-status.php';

/**
 * Initialize database tables and perform any necessary upgrades
 */
function pollify_init_database() {
    // Get current database version
    $db_version = get_option('pollify_db_version', '0');
    
    // Check if we need to run database setup
    if (version_compare($db_version, POLLIFY_VERSION, '<')) {
        pollify_create_tables();
        update_option('pollify_db_version', POLLIFY_VERSION);
    }
}
add_action('plugins_loaded', 'pollify_init_database', 5);
