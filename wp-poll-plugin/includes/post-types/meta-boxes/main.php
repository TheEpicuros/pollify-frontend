
<?php
/**
 * Poll meta boxes - Main file that includes all modularized meta box functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include modularized meta box files
require_once plugin_dir_path(__FILE__) . 'register.php';
require_once plugin_dir_path(__FILE__) . 'poll-options.php';
require_once plugin_dir_path(__FILE__) . 'poll-settings.php';
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'save/main.php';

// Register the meta boxes
add_action('add_meta_boxes', 'pollify_add_meta_boxes');
