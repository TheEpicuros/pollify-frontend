
<?php
/**
 * Display components main file
 * 
 * This file includes all component helper modules.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include component modules
require_once plugin_dir_path(__FILE__) . 'social-sharing.php';
require_once plugin_dir_path(__FILE__) . 'poll-rating.php';
require_once plugin_dir_path(__FILE__) . 'poll-comments.php';
require_once plugin_dir_path(__FILE__) . 'poll-results.php';
