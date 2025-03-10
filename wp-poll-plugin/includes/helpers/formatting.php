<?php
/**
 * Formatting helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core utility functions
require_once plugin_dir_path(dirname(__FILE__)) . 'core/utils/formatting.php';

/**
 * Format date
 */
function pollify_format_date($date_string) {
    // Include the core date formatting function if not already included
    if (!function_exists('pollify_format_datetime')) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'core/utils/date-formatting.php';
    }
    
    // Call the core function
    return pollify_format_datetime($date_string);
}

/**
 * Format poll options for display
 */
function pollify_format_poll_options($options, $poll_id = 0, $poll_type = '') {
    $formatted_options = array();
    
    // If poll ID is provided, get additional data
    if ($poll_id > 0) {
        // Get votes for each option
        $votes = get_post_meta($poll_id, '_poll_option_votes', true);
        if (!is_array($votes)) {
            $votes = array_fill(0, count($options), 0);
        }
        
        // Get total votes
        $total_votes = array_sum($votes);
        
        // For image-based polls, get image URLs
        $images = array();
        if ($poll_type === 'image-based' || $poll_type === '') {
            $poll_type = $poll_type ?: pollify_get_poll_type($poll_id);
            
            if ($poll_type === 'image-based') {
                $image_ids = get_post_meta($poll_id, '_poll_option_images', true);
                
                if (is_array($image_ids)) {
                    foreach ($image_ids as $key => $image_id) {
                        $images[$key] = wp_get_attachment_url($image_id);
                    }
                }
            }
        }
        
        // For quiz polls, get correct answers
        $correct_answers = array();
        if ($poll_type === 'quiz' || $poll_type === '') {
            $poll_type = $poll_type ?: pollify_get_poll_type($poll_id);
            
            if ($poll_type === 'quiz') {
                $correct_answers = get_post_meta($poll_id, '_poll_correct_answers', true);
                if (!is_array($correct_answers)) {
                    $correct_answers = array();
                }
            }
        }
        
        // Format each option
        foreach ($options as $key => $option) {
            $option_data = array(
                'id' => $key,
                'text' => $option,
                'votes' => isset($votes[$key]) ? $votes[$key] : 0,
                'percentage' => $total_votes > 0 ? round(($votes[$key] / $total_votes) * 100, 1) : 0
            );
            
            // Add image URL if available
            if (isset($images[$key])) {
                $option_data['image_url'] = $images[$key];
            }
            
            // Add correct answer flag if applicable
            if ($poll_type === 'quiz') {
                $option_data['is_correct'] = in_array($key, $correct_answers);
            }
            
            $formatted_options[] = $option_data;
        }
    } else {
        // Simple format for options without poll data
        foreach ($options as $key => $option) {
            $formatted_options[] = array(
                'id' => $key,
                'text' => $option
            );
        }
    }
    
    return $formatted_options;
}

/**
 * Generate short excerpt from text
 */
function pollify_get_excerpt($text, $length = 55) {
    $text = wp_strip_all_tags($text);
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}
