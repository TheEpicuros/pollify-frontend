
<?php
/**
 * Custom post types - Main file that includes all modularized post type functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include modularized files
require_once plugin_dir_path(__FILE__) . 'post-types/register.php';
require_once plugin_dir_path(__FILE__) . 'post-types/taxonomies.php';
require_once plugin_dir_path(__FILE__) . 'post-types/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'post-types/save-meta.php';
require_once plugin_dir_path(__FILE__) . 'post-types/helpers.php';
