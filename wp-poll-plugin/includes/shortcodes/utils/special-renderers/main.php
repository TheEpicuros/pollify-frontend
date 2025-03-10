
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

// Include all specialized renderers
require_once plugin_dir_path(__FILE__) . 'open-ended.php';
require_once plugin_dir_path(__FILE__) . 'ranked-choice.php';
require_once plugin_dir_path(__FILE__) . 'rating-scale.php';
require_once plugin_dir_path(__FILE__) . 'multi-stage.php';

// Note: The functions below are compatibility wrappers for backwards compatibility
// They call the class-based methods from the respective files

/**
 * Compatibility function to ensure older code still works
 * This will be removed in a future version
 */
function pollify_render_special_open_ended_results($poll_id, $options, $vote_counts) {
    return Pollify_OpenEnded_Renderer::render_results($poll_id, $options, $vote_counts);
}

/**
 * Compatibility function to ensure older code still works
 * This will be removed in a future version
 */
function pollify_render_special_ranked_choice_results($poll_id, $options, $vote_counts) {
    return Pollify_RankedChoice_Renderer::render_results($poll_id, $options, $vote_counts);
}

/**
 * Compatibility function to ensure older code still works
 * This will be removed in a future version
 */
function pollify_render_special_rating_scale_results($poll_id, $options, $vote_counts, $total_votes) {
    return Pollify_RatingScale_Renderer::render_results($poll_id, $options, $vote_counts, $total_votes);
}

/**
 * Compatibility function for multi-stage polls
 */
function pollify_render_special_multi_stage_results($poll_id, $options, $vote_counts, $total_votes) {
    return Pollify_MultiStage_Renderer::render_results($poll_id, $options, $vote_counts, $total_votes);
}
