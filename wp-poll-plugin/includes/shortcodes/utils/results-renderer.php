
<?php
/**
 * Poll results rendering utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include specialized renderers
require_once plugin_dir_path(__FILE__) . 'results/bar-chart.php';
require_once plugin_dir_path(__FILE__) . 'results/text-list.php';
require_once plugin_dir_path(__FILE__) . 'results/chart-js.php';

/**
 * Generate poll results HTML based on display type
 */
function pollify_get_results_html($poll_id, $options, $vote_counts, $total_votes, $display_type = 'bar', $user_vote = null, $poll_type = 'multiple-choice') {
    // Calculate percentages
    $percentages = array();
    foreach ($options as $option_id => $option_text) {
        $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
        $percentages[$option_id] = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
    }
    
    // Check if this is an image-based poll
    $option_images = array();
    if ($poll_type === 'image-based') {
        $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    }
    
    // Special handling for different poll types
    switch ($poll_type) {
        case 'open-ended':
            return pollify_render_open_ended_results($poll_id, $options, $vote_counts);
            
        case 'ranked-choice':
            return pollify_render_ranked_choice_results($poll_id, $options, $vote_counts);
            
        case 'rating-scale':
            // For rating scale, we'll show the average rating prominently
            return pollify_render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
            
        default:
            // For other poll types, use the standard display types
            switch ($display_type) {
                case 'pie':
                case 'donut':
                    return pollify_render_chart_js_results($poll_id, $options, $vote_counts, $total_votes, $display_type);
                    
                case 'text':
                    return pollify_render_text_list_results($options, $vote_counts, $total_votes, $percentages, $user_vote, $poll_type, $option_images);
                    
                case 'bar':
                default:
                    return pollify_render_bar_chart_results($options, $vote_counts, $total_votes, $percentages, $user_vote, $poll_type, $option_images);
            }
    }
}
