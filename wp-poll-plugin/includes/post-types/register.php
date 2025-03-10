
<?php
/**
 * Poll custom post type registration
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the poll custom post type
 */
function pollify_register_post_types() {
    $labels = array(
        'name'               => _x('Polls', 'post type general name', 'pollify'),
        'singular_name'      => _x('Poll', 'post type singular name', 'pollify'),
        'menu_name'          => _x('Polls', 'admin menu', 'pollify'),
        'name_admin_bar'     => _x('Poll', 'add new on admin bar', 'pollify'),
        'add_new'            => _x('Add New', 'poll', 'pollify'),
        'add_new_item'       => __('Add New Poll', 'pollify'),
        'new_item'           => __('New Poll', 'pollify'),
        'edit_item'          => __('Edit Poll', 'pollify'),
        'view_item'          => __('View Poll', 'pollify'),
        'all_items'          => __('All Polls', 'pollify'),
        'search_items'       => __('Search Polls', 'pollify'),
        'parent_item_colon'  => __('Parent Polls:', 'pollify'),
        'not_found'          => __('No polls found.', 'pollify'),
        'not_found_in_trash' => __('No polls found in Trash.', 'pollify')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Poll posts for the Pollify plugin', 'pollify'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'poll'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-chart-bar',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type('poll', $args);
    
    // Register meta boxes for poll options
    add_action('add_meta_boxes', 'pollify_add_meta_boxes');
    add_action('save_post_poll', 'pollify_save_poll_meta', 10, 2);
}
add_action('init', 'pollify_register_post_types');
