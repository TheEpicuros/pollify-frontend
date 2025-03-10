
<?php
/**
 * Poll utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Format a date for display
 */
function pollify_format_date($date_string) {
    $timestamp = strtotime($date_string);
    return date_i18n(get_option('date_format'), $timestamp);
}
