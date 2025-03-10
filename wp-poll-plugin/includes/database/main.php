
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

// Check if needed files exist before including them
$required_files = array(
    'votes.php',
    'comments.php',
    'ratings.php',
    'user-activity.php',
    'poll-data.php',
    'poll-status.php'
);

foreach ($required_files as $file) {
    $file_path = plugin_dir_path(__FILE__) . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log('Pollify Error: Required file not found: ' . $file_path);
    }
}

/**
 * Initialize database tables and perform any necessary upgrades
 */
function pollify_init_database() {
    try {
        // Get current database version
        $db_version = get_option('pollify_db_version', '0');
        
        // Check if we need to run database setup
        if (version_compare($db_version, POLLIFY_VERSION, '<')) {
            if (function_exists('pollify_create_tables')) {
                pollify_create_tables();
                update_option('pollify_db_version', POLLIFY_VERSION);
            } else {
                error_log('Pollify Error: pollify_create_tables function not available during database initialization');
            }
        }
    } catch (Exception $e) {
        error_log('Pollify Database Initialization Error: ' . $e->getMessage());
    }
}
add_action('plugins_loaded', 'pollify_init_database', 5);
