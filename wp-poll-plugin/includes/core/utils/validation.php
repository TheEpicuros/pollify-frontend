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

/**
 * Validate poll option
 * 
 * @param string $option_id Option ID to validate
 * @param int $poll_id Poll ID to check against
 * @return bool True if valid, false otherwise
 */
function pollify_validate_poll_option($option_id, $poll_id) {
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options)) {
        return false;
    }
    
    return array_key_exists($option_id, $options);
}

/**
 * Validate interactive poll inputs
 * 
 * @param array $input_data Input data to validate
 * @param int $poll_id Poll ID to check against
 * @return array|bool Sanitized input data or false if invalid
 */
function pollify_validate_interactive_input($input_data, $poll_id) {
    if (!is_array($input_data) || empty($input_data)) {
        return false;
    }
    
    $poll_type = pollify_get_poll_type($poll_id);
    if ($poll_type !== 'interactive') {
        return false;
    }
    
    $sanitized_data = array();
    
    foreach ($input_data as $key => $value) {
        // Sanitize keys and values
        $sanitized_key = sanitize_text_field($key);
        
        if (is_array($value)) {
            $sanitized_value = array_map('sanitize_text_field', $value);
        } else {
            $sanitized_value = sanitize_text_field($value);
        }
        
        $sanitized_data[$sanitized_key] = $sanitized_value;
    }
    
    return $sanitized_data;
}

/**
 * Validate poll type
 * 
 * @param string $poll_type Poll type to validate
 * @return bool True if valid, false otherwise
 */
function pollify_is_valid_poll_type($poll_type) {
    $valid_types = array(
        'multiple-choice',
        'binary',
        'check-all',
        'ranked-choice',
        'rating-scale',
        'open-ended',
        'image-based',
        'quiz',
        'opinion',
        'straw',
        'interactive',
        'referendum'
    );
    
    return in_array($poll_type, $valid_types);
}

if (!function_exists('pollify_validate_poll_exists')) {
    /**
     * Validate that a poll exists and is publishable
     */
    function pollify_validate_poll_exists($poll_id) {
        if (!$poll_id) {
            return [
                'valid' => false,
                'message' => '<div class="pollify-error">' . __('Poll ID is required.', 'pollify') . '</div>'
            ];
        }
        
        $poll = get_post($poll_id);
        
        if (!$poll || $poll->post_type !== 'poll') {
            return [
                'valid' => false,
                'message' => '<div class="pollify-error">' . __('Poll not found.', 'pollify') . '</div>'
            ];
        }
        
        // Check if poll is published
        if ($poll->post_status !== 'publish' && !current_user_can('edit_post', $poll_id)) {
            return [
                'valid' => false,
                'message' => '<div class="pollify-error">' . __('This poll is not published.', 'pollify') . '</div>'
            ];
        }
        
        return [
            'valid' => true,
            'poll' => $poll,
            'message' => ''
        ];
    }
}
