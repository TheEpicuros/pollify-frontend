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
            'description' => __('Different types of polls with varying formats and options.', 'pollify'),
            'labels' => array(
                'name' => __('Poll Types', 'pollify'),
                'singular_name' => __('Poll Type', 'pollify'),
                'search_items' => __('Search Poll Types', 'pollify'),
                'all_items' => __('All Poll Types', 'pollify'),
                'parent_item' => __('Parent Poll Type', 'pollify'),
                'parent_item_colon' => __('Parent Poll Type:', 'pollify'),
                'edit_item' => __('Edit Poll Type', 'pollify'),
                'update_item' => __('Update Poll Type', 'pollify'),
                'add_new_item' => __('Add New Poll Type', 'pollify'),
                'new_item_name' => __('New Poll Type Name', 'pollify'),
                'menu_name' => __('Poll Types', 'pollify'),
            ),
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
        'binary' => array(
            'name' => __('Binary Choice', 'pollify'),
            'description' => __('Simple yes/no or either/or questions.', 'pollify')
        ),
        'multiple-choice' => array(
            'name' => __('Multiple Choice', 'pollify'),
            'description' => __('Select one option from multiple choices.', 'pollify')
        ),
        'check-all' => array(
            'name' => __('Check All That Apply', 'pollify'),
            'description' => __('Select multiple options that apply.', 'pollify')
        ),
        'ranked-choice' => array(
            'name' => __('Ranked Choice', 'pollify'),
            'description' => __('Rank options in order of preference.', 'pollify')
        ),
        'rating-scale' => array(
            'name' => __('Rating Scale', 'pollify'),
            'description' => __('Rate on a scale (1-5, 1-10, etc).', 'pollify')
        ),
        'open-ended' => array(
            'name' => __('Open-Ended', 'pollify'),
            'description' => __('Allow voters to provide text responses.', 'pollify')
        ),
        'image-based' => array(
            'name' => __('Image-Based', 'pollify'),
            'description' => __('Use images as answer options.', 'pollify')
        ),
        'quiz' => array(
            'name' => __('Quiz', 'pollify'), 
            'description' => __('Test knowledge with right/wrong answers.', 'pollify')
        ),
        'opinion' => array(
            'name' => __('Opinion', 'pollify'),
            'description' => __('Gauge sentiment on specific issues.', 'pollify')
        ),
        'straw' => array(
            'name' => __('Straw Poll', 'pollify'),
            'description' => __('Quick, informal sentiment polls.', 'pollify')
        ),
        'interactive' => array(
            'name' => __('Interactive', 'pollify'),
            'description' => __('Real-time polls with live results.', 'pollify')
        ),
        'referendum' => array(
            'name' => __('Referendum', 'pollify'),
            'description' => __('Formal votes on specific measures.', 'pollify')
        )
    );
    
    foreach ($poll_types as $slug => $data) {
        if (!term_exists($slug, 'poll_type')) {
            wp_insert_term(
                $data['name'], 
                'poll_type', 
                array(
                    'slug' => $slug,
                    'description' => $data['description']
                )
            );
        } else {
            // Update existing term description if it exists
            $term = get_term_by('slug', $slug, 'poll_type');
            if ($term && $term->description !== $data['description']) {
                wp_update_term(
                    $term->term_id,
                    'poll_type',
                    array('description' => $data['description'])
                );
            }
        }
    }
}

/**
 * Get all poll types for dropdown or similar
 * 
 * @return array Associative array of poll types
 */
function pollify_get_poll_type_options() {
    $terms = get_terms(array(
        'taxonomy' => 'poll_type',
        'hide_empty' => false,
    ));
    
    $poll_types = array();
    
    foreach ($terms as $term) {
        $poll_types[$term->slug] = $term->name;
    }
    
    return $poll_types;
}

/**
 * Get poll type for a specific poll
 * 
 * @param int $poll_id Poll ID
 * @return string Poll type slug
 */
function pollify_get_poll_type($poll_id) {
    $terms = get_the_terms($poll_id, 'poll_type');
    
    if (!empty($terms) && !is_wp_error($terms)) {
        return $terms[0]->slug;
    }
    
    return 'multiple-choice'; // Default type
}

/**
 * Get poll type name for a specific poll
 * 
 * @param int $poll_id Poll ID
 * @return string Poll type name
 */
function pollify_get_poll_type_name($poll_id) {
    $terms = get_the_terms($poll_id, 'poll_type');
    
    if (!empty($terms) && !is_wp_error($terms)) {
        return $terms[0]->name;
    }
    
    return __('Multiple Choice', 'pollify'); // Default type name
}

/**
 * Get poll type description
 * 
 * @param string $type_slug Poll type slug
 * @return string Poll type description
 */
function pollify_get_poll_type_description($type_slug) {
    $term = get_term_by('slug', $type_slug, 'poll_type');
    
    if ($term && !is_wp_error($term)) {
        return $term->description;
    }
    
    return '';
}

