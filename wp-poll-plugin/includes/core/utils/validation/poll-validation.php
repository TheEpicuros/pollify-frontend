
<?php
/**
 * Poll-specific validation utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function existence utilities
require_once plugin_dir_path(dirname(__FILE__)) . 'function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Validate that a poll exists and is publishable
 */
if (pollify_can_define_function('pollify_validate_poll_exists')) {
    pollify_declare_function('pollify_validate_poll_exists', function($poll_id) {
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
    }, $current_file);
}

// Override function to check if poll has ended - using pollify_require_function to import from canonical source
if (pollify_can_define_function('pollify_has_poll_ended')) {
    // Instead of defining it, use the registry to find and require the canonical source
    pollify_require_function('pollify_has_poll_ended');
}

// Function to check if a post is a valid poll
if (pollify_can_define_function('pollify_is_valid_poll')) {
    pollify_declare_function('pollify_is_valid_poll', function($poll_id) {
        $post = get_post($poll_id);
        return $post && $post->post_type === 'poll';
    }, $current_file);
}

/**
 * Validate poll option
 * 
 * @param string $option_id Option ID to validate
 * @param int $poll_id Poll ID to check against
 * @return bool True if valid, false otherwise
 */
if (pollify_can_define_function('pollify_validate_poll_option')) {
    pollify_declare_function('pollify_validate_poll_option', function($option_id, $poll_id) {
        $options = get_post_meta($poll_id, '_poll_options', true);
        
        if (!is_array($options)) {
            return false;
        }
        
        return array_key_exists($option_id, $options);
    }, $current_file);
}

/**
 * Validate poll type
 * 
 * @param string $poll_type Poll type to validate
 * @return bool True if valid, false otherwise
 */
if (pollify_can_define_function('pollify_is_valid_poll_type')) {
    pollify_declare_function('pollify_is_valid_poll_type', function($poll_type) {
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
    }, $current_file);
}
