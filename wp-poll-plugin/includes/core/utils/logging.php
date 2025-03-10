
<?php
/**
 * Logging utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log debug information
 */
function pollify_log($message, $level = 'debug') {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    if (is_array($message) || is_object($message)) {
        error_log('[POLLIFY-' . strtoupper($level) . '] ' . print_r($message, true));
    } else {
        error_log('[POLLIFY-' . strtoupper($level) . '] ' . $message);
    }
}
