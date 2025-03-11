
<?php
/**
 * Special poll type renderers main file
 * 
 * This file includes all specialized poll type renderers.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

// Include all specialized renderers
require_once plugin_dir_path(__FILE__) . 'open-ended.php';
require_once plugin_dir_path(__FILE__) . 'ranked-choice.php';
require_once plugin_dir_path(__FILE__) . 'rating-scale.php';
require_once plugin_dir_path(__FILE__) . 'multi-stage.php';

/**
 * Compatibility function for special open-ended poll results
 */
if (pollify_can_define_function('pollify_render_special_open_ended_results')) {
    pollify_declare_function('pollify_render_special_open_ended_results', function($poll_id, $options, $vote_counts) {
        // Forward to the canonical function if it exists
        if (function_exists('pollify_render_open_ended_results')) {
            return pollify_render_open_ended_results($poll_id, $options, $vote_counts);
        }
        // Fallback to direct class call
        return Pollify_OpenEnded_Renderer::render_open_ended_results($poll_id, $options, $vote_counts);
    }, $current_file);
}

/**
 * Compatibility function for special ranked-choice poll results
 */
if (pollify_can_define_function('pollify_render_special_ranked_choice_results')) {
    pollify_declare_function('pollify_render_special_ranked_choice_results', function($poll_id, $options, $vote_counts) {
        // Forward to the canonical function if it exists
        if (function_exists('pollify_render_ranked_choice_results')) {
            return pollify_render_ranked_choice_results($poll_id, $options, $vote_counts);
        }
        // Fallback to direct class call
        return Pollify_RankedChoice_Renderer::render_ranked_choice_results($poll_id, $options, $vote_counts);
    }, $current_file);
}

/**
 * Compatibility function for special rating-scale poll results
 */
if (pollify_can_define_function('pollify_render_special_rating_scale_results')) {
    pollify_declare_function('pollify_render_special_rating_scale_results', function($poll_id, $options, $vote_counts, $total_votes) {
        // Forward to the canonical function if it exists
        if (function_exists('pollify_render_rating_scale_results')) {
            return pollify_render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
        }
        // Fallback to direct class call
        return Pollify_RatingScale_Renderer::render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
    }, $current_file);
}

/**
 * Compatibility function for special multi-stage poll results
 */
if (pollify_can_define_function('pollify_render_special_multi_stage_results')) {
    pollify_declare_function('pollify_render_special_multi_stage_results', function($poll_id, $options, $vote_counts, $total_votes) {
        // Forward to the canonical function if it exists
        if (function_exists('pollify_render_multi_stage_results')) {
            return pollify_render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes);
        }
        // Fallback to direct class call
        return Pollify_MultiStage_Renderer::render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes);
    }, $current_file);
}
