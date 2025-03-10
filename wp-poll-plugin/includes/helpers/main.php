
<?php
/**
 * Main helper functions file
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include all helper modules
require_once plugin_dir_path(__FILE__) . 'formatting.php';
require_once plugin_dir_path(__FILE__) . 'poll-status.php';
require_once plugin_dir_path(__FILE__) . 'poll-types.php';
require_once plugin_dir_path(__FILE__) . 'display-components.php';
