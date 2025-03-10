
<?php
/**
 * Core utilities - Main file that includes all modularized utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include utility modules
require_once plugin_dir_path(__FILE__) . 'array-handling.php';
require_once plugin_dir_path(__FILE__) . 'formatting.php';
require_once plugin_dir_path(__FILE__) . 'logging.php';
require_once plugin_dir_path(__FILE__) . 'permissions.php';
require_once plugin_dir_path(__FILE__) . 'capabilities.php';
require_once plugin_dir_path(__FILE__) . 'poll-data.php';
require_once plugin_dir_path(__FILE__) . 'transients.php';
require_once plugin_dir_path(__FILE__) . 'url-handling.php';
require_once plugin_dir_path(__FILE__) . 'user-interactions.php';
require_once plugin_dir_path(__FILE__) . 'date-formatting.php';
require_once plugin_dir_path(__FILE__) . 'validation.php';
require_once plugin_dir_path(__FILE__) . 'sanitization.php';
require_once plugin_dir_path(__FILE__) . 'ip-handling.php';
require_once plugin_dir_path(__FILE__) . 'string-handling.php';
require_once plugin_dir_path(__FILE__) . 'file-handling.php';
require_once plugin_dir_path(__FILE__) . 'security.php';
