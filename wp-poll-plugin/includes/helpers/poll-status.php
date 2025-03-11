<?php
/**
 * Poll status helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Check if poll has ended - registered as the canonical function
 *
 * @param int $poll_id Poll ID
 * @return bool Whether the poll has ended
 */
if (pollify_can_define_function('pollify_has_poll_ended')) {
    pollify_declare_function('pollify_has_poll_ended', function($poll_id) {
        $end_date = get_post_meta($poll_id, '_poll_end_date', true);
        
        if (empty($end_date)) {
            return false;
        }
        
        $current_time = current_time('timestamp');
        $end_timestamp = strtotime($end_date);
        
        return $end_timestamp < $current_time;
    }, $current_file);
}

/**
 * Check if poll has started
 *
 * @param int $poll_id Poll ID
 * @return bool Whether the poll has started
 */
function pollify_has_poll_started($poll_id) {
    $start_date = get_post_meta($poll_id, '_poll_start_date', true);
    
    if (empty($start_date)) {
        return true; // No start date means it has started
    }
    
    $current_time = current_time('timestamp');
    $start_timestamp = strtotime($start_date);
    
    return $start_timestamp <= $current_time;
}

/**
 * Get poll duration in seconds
 *
 * @param int $poll_id Poll ID
 * @return int|false Duration in seconds or false if no end date
 */
function pollify_get_poll_duration($poll_id) {
    $start_date = get_post_meta($poll_id, '_poll_start_date', true);
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($start_date) || empty($end_date)) {
        return false;
    }
    
    $start_timestamp = strtotime($start_date);
    $end_timestamp = strtotime($end_date);
    
    return $end_timestamp - $start_timestamp;
}

/**
 * Get poll remaining time in seconds
 *
 * @param int $poll_id Poll ID
 * @return int|false Remaining time in seconds or false if poll has ended or no end date
 */
function pollify_get_poll_remaining_time($poll_id) {
    $end_date = get_post_meta($poll_id, '_poll_end_date', true);
    
    if (empty($end_date)) {
        return false;
    }
    
    $current_time = current_time('timestamp');
    $end_timestamp = strtotime($end_date);
    
    if ($end_timestamp < $current_time) {
        return 0; // Poll has ended
    }
    
    return $end_timestamp - $current_time;
}
