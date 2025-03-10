
<?php
/**
 * Poll taxonomies
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register poll taxonomies
 */
function pollify_register_taxonomies() {
    // Register poll categories
    register_taxonomy(
        'poll_type',
        'poll',
        array(
            'label' => __('Poll Types', 'pollify'),
            'rewrite' => array('slug' => 'poll-type'),
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        )
    );
    
    // Create default poll types if they don't exist
    pollify_create_default_poll_types();
}
add_action('init', 'pollify_register_taxonomies');

/**
 * Create default poll types
 */
function pollify_create_default_poll_types() {
    $poll_types = array(
        'binary' => __('Binary Choice', 'pollify'),
        'multiple-choice' => __('Multiple Choice', 'pollify'),
        'check-all' => __('Check All That Apply', 'pollify'),
        'ranked-choice' => __('Ranked Choice', 'pollify'),
        'rating-scale' => __('Rating Scale', 'pollify'),
        'open-ended' => __('Open-Ended', 'pollify'),
        'image-based' => __('Image-Based', 'pollify'),
        'quiz' => __('Quiz', 'pollify'),
        'opinion' => __('Opinion', 'pollify'),
        'straw' => __('Straw Poll', 'pollify'),
        'interactive' => __('Interactive', 'pollify'),
        'referendum' => __('Referendum', 'pollify')
    );
    
    foreach ($poll_types as $slug => $name) {
        if (!term_exists($slug, 'poll_type')) {
            wp_insert_term($name, 'poll_type', array('slug' => $slug));
        }
    }
}
