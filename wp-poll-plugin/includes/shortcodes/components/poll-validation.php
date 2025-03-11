
<?php
/**
 * Poll validation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core validation functions
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get poll settings
 */
if (pollify_can_define_function('pollify_get_poll_settings')) {
    function pollify_get_poll_settings($poll_id) {
        return [
            'poll_type' => pollify_get_poll_type($poll_id),
            'poll_end_date' => get_post_meta($poll_id, '_poll_end_date', true),
            'always_show_results' => get_post_meta($poll_id, '_poll_show_results', true) === '1',
            'results_display' => get_post_meta($poll_id, '_poll_results_display', true) ?: 'bar',
            'allow_comments' => get_post_meta($poll_id, '_poll_allow_comments', true) === '1',
        ];
    }
    pollify_register_function_path('pollify_get_poll_settings', $current_file);
}

/**
 * Override poll settings with shortcode attributes
 */
if (pollify_can_define_function('pollify_get_display_settings')) {
    function pollify_get_display_settings($poll_settings, $atts) {
        return [
            'show_results' => $atts['show_results'] !== null ? ($atts['show_results'] === 'yes') : $poll_settings['always_show_results'],
            'show_social' => $atts['show_social'] === 'yes',
            'show_ratings' => $atts['show_ratings'] === 'yes',
            'show_comments' => $atts['show_comments'] !== null ? ($atts['show_comments'] === 'yes') : $poll_settings['allow_comments'],
            'results_display' => $atts['display'] ? $atts['display'] : $poll_settings['results_display'],
            'width' => !empty($atts['width']) ? ' style="width:' . esc_attr($atts['width']) . ';"' : '',
            'align' => ' pollify-align-' . esc_attr($atts['align']),
        ];
    }
    pollify_register_function_path('pollify_get_display_settings', $current_file);
}

/**
 * Get voting status for current user and poll
 */
if (pollify_can_define_function('pollify_get_voting_status')) {
    function pollify_get_voting_status($poll_id) {
        // Get vote counts
        $vote_counts = pollify_get_vote_counts($poll_id);
        $total_votes = array_sum($vote_counts);
        
        // Check if user has already voted
        $user_ip = pollify_get_user_ip();
        $user_id = get_current_user_id();
        $has_voted = pollify_has_user_voted($poll_id, $user_ip, $user_id);
        $user_vote = $has_voted ? pollify_get_user_vote($poll_id, $user_ip, $user_id) : null;
        
        // Check if poll has ended
        $has_ended = pollify_has_poll_ended($poll_id);
        
        return [
            'vote_counts' => $vote_counts,
            'total_votes' => $total_votes,
            'has_voted' => $has_voted,
            'user_vote' => $user_vote,
            'has_ended' => $has_ended
        ];
    }
    pollify_register_function_path('pollify_get_voting_status', $current_file);
}
