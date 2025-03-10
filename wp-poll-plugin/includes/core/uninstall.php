
<?php
/**
 * Plugin uninstall functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin uninstall hook callback
 */
function pollify_uninstall_plugin() {
    // Only run if WP_UNINSTALL_PLUGIN is defined
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }
    
    // Get options
    $delete_data = get_option('pollify_delete_data_on_uninstall', false);
    
    // If set to delete data, remove database tables and options
    if ($delete_data) {
        global $wpdb;
        
        // Delete database tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_votes");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_ratings");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_comments");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}pollify_user_activity");
        
        // Delete all poll posts
        $polls = get_posts(array(
            'post_type' => 'poll',
            'numberposts' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($polls as $poll) {
            wp_delete_post($poll->ID, true);
        }
        
        // Delete options
        delete_option('pollify_settings');
        delete_option('pollify_delete_data_on_uninstall');
    }
    
    // Log uninstall
    error_log('Pollify plugin uninstalled');
}
