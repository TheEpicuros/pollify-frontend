
<?php
/**
 * Admin settings page for Pollify
 * 
 * This file is a wrapper that includes the modularized settings components
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the main settings file
require_once plugin_dir_path(__FILE__) . 'settings/main.php';

// Function pollify_settings_page() is now defined in settings/main.php
