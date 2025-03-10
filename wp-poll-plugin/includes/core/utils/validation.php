
<?php
/**
 * Input validation utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validate and sanitize poll ID
 * 
 * @param mixed $poll_id The poll ID to validate
 * @return int|bool Sanitized poll ID or false if invalid
 */
function pollify_validate_poll_id($poll_id) {
    $poll_id = absint($poll_id);
    
    if ($poll_id <= 0) {
        return false;
    }
    
    // Check if post exists and is a poll
    if (!pollify_is_valid_poll($poll_id)) {
        return false;
    }
    
    return $poll_id;
}

/**
 * Validate email address
 * 
 * @param string $email Email address to validate
 * @return bool True if valid, false otherwise
 */
function pollify_is_valid_email($email) {
    return is_email($email);
}

/**
 * Validate a date string
 * 
 * @param string $date Date string to validate (YYYY-MM-DD format)
 * @return bool True if valid, false otherwise
 */
function pollify_is_valid_date($date) {
    if (empty($date)) {
        return false;
    }
    
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
