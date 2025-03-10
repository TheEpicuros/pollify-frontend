
<?php
/**
 * AJAX handler for creating a poll
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for creating a poll
 */
function pollify_ajax_create_poll() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pollify-nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'You must be logged in to create a poll.'));
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        wp_send_json_error(array('message' => 'You do not have permission to create polls.'));
    }
    
    // Get form data
    $title = isset($_POST['poll_title']) ? sanitize_text_field($_POST['poll_title']) : '';
    $description = isset($_POST['poll_description']) ? sanitize_textarea_field($_POST['poll_description']) : '';
    $options = isset($_POST['poll_options']) && is_array($_POST['poll_options']) ? array_map('sanitize_text_field', $_POST['poll_options']) : array();
    
    // Validate data
    if (empty($title)) {
        wp_send_json_error(array('message' => 'Poll question is required.'));
    }
    
    // Remove empty options
    $options = array_filter($options);
    
    if (count($options) < 2) {
        wp_send_json_error(array('message' => 'At least two poll options are required.'));
    }
    
    // Create the poll post
    $poll_data = array(
        'post_title' => $title,
        'post_content' => $description,
        'post_status' => 'publish',
        'post_type' => 'poll',
        'post_author' => get_current_user_id()
    );
    
    $poll_id = wp_insert_post($poll_data);
    
    if (is_wp_error($poll_id)) {
        wp_send_json_error(array('message' => 'Failed to create poll: ' . $poll_id->get_error_message()));
    }
    
    // Save poll options
    update_post_meta($poll_id, '_poll_options', $options);
    
    wp_send_json_success(array(
        'message' => 'Poll created successfully.',
        'pollId' => $poll_id,
        'pollUrl' => get_permalink($poll_id)
    ));
}
add_action('wp_ajax_pollify_create_poll', 'pollify_ajax_create_poll');

