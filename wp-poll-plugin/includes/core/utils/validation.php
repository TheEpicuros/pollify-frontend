<?php
/**
 * Input validation utility functions
 * 
 * This is a lightweight file that includes all validation modules.
 * Each validation utility is separated into its own file for better organization and maintainability.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function existence utilities
require_once plugin_dir_path(__FILE__) . 'function-exists.php';

// Include all validation modules
require_once plugin_dir_path(__FILE__) . 'validation/poll-validation.php';
require_once plugin_dir_path(__FILE__) . 'validation/input-validation.php';
require_once plugin_dir_path(__FILE__) . 'validation/format-validation.php';

// Define the current file path for function registration
$current_file = __FILE__;

// Additional validation functions can be added here if needed
// If the file grows again, consider extracting them to a new validation module
