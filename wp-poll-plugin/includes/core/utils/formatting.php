<?php
/**
 * Formatting utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get user IP address
 */
function pollify_get_user_ip() {
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
 * Generate a unique ID for tracking votes
 */
function pollify_generate_vote_id() {
    return md5(uniqid(rand(), true));
}

/**
 * Format number with suffix (K, M, etc)
 */
function pollify_format_number($number) {
    $number = (int) $number;
    
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    }
    
    if ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    
    return number_format_i18n($number);
}

/**
 * Format relative time
 */
function pollify_time_ago($timestamp) {
    $time_diff = time() - strtotime($timestamp);
    
    if ($time_diff < 60) {
        return __('just now', 'pollify');
    }
    
    if ($time_diff < 3600) {
        $mins = round($time_diff / 60);
        return sprintf(_n('%s minute ago', '%s minutes ago', $mins, 'pollify'), $mins);
    }
    
    if ($time_diff < 86400) {
        $hours = round($time_diff / 3600);
        return sprintf(_n('%s hour ago', '%s hours ago', $hours, 'pollify'), $hours);
    }
    
    if ($time_diff < 604800) {
        $days = round($time_diff / 86400);
        return sprintf(_n('%s day ago', '%s days ago', $days, 'pollify'), $days);
    }
    
    if ($time_diff < 2592000) {
        $weeks = round($time_diff / 604800);
        return sprintf(_n('%s week ago', '%s weeks ago', $weeks, 'pollify'), $weeks);
    }
    
    if ($time_diff < 31536000) {
        $months = round($time_diff / 2592000);
        return sprintf(_n('%s month ago', '%s months ago', $months, 'pollify'), $months);
    }
    
    $years = round($time_diff / 31536000);
    return sprintf(_n('%s year ago', '%s years ago', $years, 'pollify'), $years);
}
