
<?php
/**
 * Date formatting utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Format a date for display
 * 
 * @param string $date_string The date string to format
 * @return string Formatted date according to WordPress settings
 */
function pollify_format_date($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    $timestamp = strtotime($date_string);
    return date_i18n(get_option('date_format'), $timestamp);
}

/**
 * Format a date with time for display
 * 
 * @param string $date_string The date string to format
 * @return string Formatted date and time according to WordPress settings
 */
function pollify_format_datetime($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    $timestamp = strtotime($date_string);
    return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
}

/**
 * Get relative time (time ago) from a timestamp
 * 
 * @param string $timestamp The timestamp to format
 * @return string Formatted relative time (e.g., "5 minutes ago")
 */
function pollify_get_time_ago($timestamp) {
    return pollify_time_ago($timestamp);
}
