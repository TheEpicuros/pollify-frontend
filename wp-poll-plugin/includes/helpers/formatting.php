
<?php
/**
 * Formatting helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get user IP address
 */
function pollify_get_user_ip() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return sanitize_text_field($ip);
}

/**
 * Format date
 */
function pollify_format_date($date_string) {
    $timestamp = strtotime($date_string);
    return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp);
}
