
<?php
/**
 * Plugin utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings link on plugin page
 */
function pollify_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=pollify-settings') . '">' . __('Settings', 'pollify') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

/**
 * Error logging for debugging
 */
function pollify_log($message, $level = 'info') {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}
