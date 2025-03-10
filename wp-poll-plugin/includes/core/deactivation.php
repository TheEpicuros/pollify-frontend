
<?php
/**
 * Plugin deactivation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Deactivation hook
 */
function pollify_deactivate_plugin() {
    // Clean up transients
    pollify_delete_all_transients();
    
    // Remove capabilities from roles
    pollify_remove_capabilities();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Delete all plugin transients
 */
function pollify_delete_all_transients() {
    global $wpdb;
    
    // Delete transients with the pollify_ prefix
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_pollify_%'");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_pollify_%'");
}
