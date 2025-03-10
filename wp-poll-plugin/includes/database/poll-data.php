<?php
<?php
/**
 * Database functions for retrieving poll data
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get poll data from database
 */
function pollify_get_poll_data($poll_id) {
    $options = get_post_meta($poll_id, '_poll_options', true);
    if (!is_array($options)) {
        $options = array();
    }
    
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    $settings = array(
        'show_results' => get_post_meta($poll_id, '_poll_show_results', true) === '1',
        'results_display' => get_post_meta($poll_id, '_poll_results_display', true) ?: 'bar',
        'allow_comments' => get_post_meta($poll_id, '_poll_allow_comments', true) === '1',
        'end_date' => get_post_meta($poll_id, '_poll_end_date', true),
    );
    
    return array(
        'id' => $poll_id,
        'title' => get_the_title($poll_id),
        'description' => get_the_excerpt($poll_id),
        'options' => $options,
        'vote_counts' => $vote_counts,
        'total_votes' => $total_votes,
        'type' => pollify_get_poll_type($poll_id),
        'settings' => $settings,
        'author' => get_post_field('post_author', $poll_id),
        'created' => get_post_field('post_date', $poll_id),
        'status' => get_post_status($poll_id),
    );
}
