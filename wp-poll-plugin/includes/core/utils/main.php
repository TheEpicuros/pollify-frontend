
<?php
/**
 * Main utility functions file
 * 
 * Includes all utility modules.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include all utility modules
require_once plugin_dir_path(__FILE__) . 'array-handling.php';
require_once plugin_dir_path(__FILE__) . 'permissions.php';
require_once plugin_dir_path(__FILE__) . 'formatting.php';
require_once plugin_dir_path(__FILE__) . 'logging.php';
require_once plugin_dir_path(__FILE__) . 'transients.php';
require_once plugin_dir_path(__FILE__) . 'poll-data.php';
require_once plugin_dir_path(__FILE__) . 'user-interactions.php';
require_once plugin_dir_path(__FILE__) . 'url-handling.php';
