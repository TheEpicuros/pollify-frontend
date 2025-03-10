
<?php
/**
 * Transient handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clean up expired transients
 */
function pollify_cleanup_transients() {
    global $wpdb;
    
    $sql = "DELETE FROM $wpdb->options 
            WHERE option_name LIKE '%_transient_pollify_%' 
            AND option_name NOT LIKE '%_transient_timeout_pollify_%'";
    
    $wpdb->query($sql);
}
