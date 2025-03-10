
<?php
/**
 * Core shortcode functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get polls for Gutenberg blocks
 */
function pollify_get_polls_for_blocks() {
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => 100,
        'post_status' => 'publish'
    );
    
    $polls = get_posts($args);
    $formatted_polls = array();
    
    foreach ($polls as $poll) {
        // Get poll type
        $poll_type = pollify_get_poll_type($poll->ID);
        
        $formatted_polls[] = array(
            'id' => $poll->ID,
            'title' => get_the_title($poll),
            'type' => $poll_type,
            'votes' => pollify_get_total_votes($poll->ID)
        );
    }
    
    return $formatted_polls;
}

/**
 * Get poll types
 */
function pollify_get_poll_types() {
    $terms = get_terms(array(
        'taxonomy' => 'poll_type',
        'hide_empty' => false,
    ));
    
    $poll_types = array();
    
    foreach ($terms as $term) {
        $poll_types[$term->slug] = array(
            'name' => $term->name,
            'description' => $term->description
        );
    }
    
    // Fallback if no terms found
    if (empty($poll_types)) {
        return array(
            'binary' => array(
                'name' => __('Yes/No', 'pollify'),
                'description' => __('Simple yes/no questions', 'pollify')
            ),
            'multiple-choice' => array(
                'name' => __('Multiple Choice', 'pollify'),
                'description' => __('Select one from multiple options', 'pollify')
            ),
            'check-all' => array(
                'name' => __('Multiple Answers', 'pollify'), 
                'description' => __('Select multiple options', 'pollify')
            ),
            'image-based' => array(
                'name' => __('Image Based', 'pollify'),
                'description' => __('Visual polls with images', 'pollify')
            ),
            'rating-scale' => array(
                'name' => __('Rating Scale', 'pollify'),
                'description' => __('Rate on a scale', 'pollify')
            )
        );
    }
    
    return $poll_types;
}
