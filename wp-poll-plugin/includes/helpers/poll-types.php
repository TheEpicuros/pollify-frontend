
<?php
/**
 * Poll types helper functions
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
 * Get poll type display name - registered as the canonical function
 *
 * @param int $poll_id Poll ID
 * @return string Poll type name
 */
if (pollify_can_define_function('pollify_get_poll_type_name')) {
    pollify_declare_function('pollify_get_poll_type_name', function($poll_id) {
        // Include the core utility function if not already included
        if (!function_exists('pollify_get_poll_type_name_from_taxonomy')) {
            require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'post-types/taxonomies.php';
        }
        
        return pollify_get_poll_type_name_from_taxonomy($poll_id);
    }, $current_file);
}

/**
 * Get poll type description - registered as the canonical function
 * 
 * @param string $poll_type Poll type slug
 * @return string Poll type description
 */
if (pollify_can_define_function('pollify_get_poll_type_description')) {
    pollify_declare_function('pollify_get_poll_type_description', function($poll_type) {
        switch ($poll_type) {
            case 'binary':
                return __('Simple yes/no or either/or questions.', 'pollify');
            case 'multiple-choice':
                return __('Select one option from multiple choices.', 'pollify');
            case 'check-all':
                return __('Select multiple options that apply.', 'pollify');
            case 'ranked-choice':
                return __('Rank options in order of preference.', 'pollify');
            case 'rating-scale':
                return __('Rate on a scale (1-5, 1-10, etc).', 'pollify');
            case 'open-ended':
                return __('Allow voters to provide text responses.', 'pollify');
            case 'image-based':
                return __('Use images as answer options.', 'pollify');
            case 'quiz':
                return __('Test knowledge with right/wrong answers.', 'pollify');
            case 'opinion':
                return __('Gauge sentiment on specific issues.', 'pollify');
            case 'straw':
                return __('Quick, informal sentiment polls.', 'pollify');
            case 'interactive':
                return __('Real-time polls with live results.', 'pollify');
            case 'referendum':
                return __('Formal votes on specific measures.', 'pollify');
            default:
                $term = get_term_by('slug', $poll_type, 'poll_type');
                if ($term && !is_wp_error($term) && !empty($term->description)) {
                    return $term->description;
                }
                return __('Standard poll with multiple options.', 'pollify');
        }
    }, $current_file);
}
