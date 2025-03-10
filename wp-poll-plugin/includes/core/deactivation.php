
<?php
/**
 * Plugin deactivation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin deactivation hook callback
 */
function pollify_deactivate_plugin() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Log deactivation
    error_log('Pollify plugin deactivated');
}
