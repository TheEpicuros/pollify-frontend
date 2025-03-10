
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
    if (!current_user_can('create_polls')) {
        wp_send_json_error(array('message' => 'You do not have permission to create polls.'));
    }
    
    // Get form data
    $title = isset($_POST['poll_title']) ? sanitize_text_field($_POST['poll_title']) : '';
    $description = isset($_POST['poll_description']) ? sanitize_textarea_field($_POST['poll_description']) : '';
    $options = isset($_POST['poll_options']) && is_array($_POST['poll_options']) ? array_map('sanitize_text_field', $_POST['poll_options']) : array();
    $poll_type = isset($_POST['poll_type']) ? sanitize_text_field($_POST['poll_type']) : 'multiple-choice';
    $end_date = isset($_POST['poll_end_date']) ? sanitize_text_field($_POST['poll_end_date']) : '';
    $show_results = isset($_POST['poll_show_results']) ? (bool)$_POST['poll_show_results'] : false;
    $results_display = isset($_POST['poll_results_display']) ? sanitize_text_field($_POST['poll_results_display']) : 'bar';
    $allow_comments = isset($_POST['poll_allow_comments']) ? (bool)$_POST['poll_allow_comments'] : true;
    
    // Admin settings (will be ignored for non-admin users)
    $is_featured = isset($_POST['poll_featured']) ? (bool)$_POST['poll_featured'] : false;
    $is_private = isset($_POST['poll_private']) ? (bool)$_POST['poll_private'] : false;
    $require_login = isset($_POST['poll_require_login']) ? (bool)$_POST['poll_require_login'] : false;
    $max_votes = isset($_POST['poll_max_votes']) ? absint($_POST['poll_max_votes']) : 0;
    
    // For image-based polls
    $option_images = isset($_POST['poll_option_images']) && is_array($_POST['poll_option_images']) ? $_POST['poll_option_images'] : array();
    
    // For quiz polls
    $correct_answers = isset($_POST['poll_correct_answers']) && is_array($_POST['poll_correct_answers']) ? array_map('sanitize_text_field', $_POST['poll_correct_answers']) : array();
    
    // Validate data
    if (empty($title)) {
        wp_send_json_error(array('message' => 'Poll question is required.'));
    }
    
    // Remove empty options
    $options = array_filter($options);
    
    // Different validation based on poll type
    if ($poll_type !== 'open-ended' && count($options) < 2) {
        wp_send_json_error(array('message' => 'At least two poll options are required.'));
    }
    
    // Check if user can create specific poll types
    if (($poll_type === 'quiz' || $poll_type === 'ranked-choice') && !current_user_can('edit_others_polls')) {
        wp_send_json_error(array('message' => 'You do not have permission to create this type of poll.'));
    }
    
    if ($poll_type === 'image-based' && !current_user_can('upload_files')) {
        wp_send_json_error(array('message' => 'You do not have permission to create image polls.'));
    }
    
    // Set post status based on user permissions
    $post_status = current_user_can('publish_polls') ? 'publish' : 'pending';
    
    // Create the poll post
    $poll_data = array(
        'post_title' => $title,
        'post_content' => $description,
        'post_status' => $post_status,
        'post_type' => 'poll',
        'post_author' => get_current_user_id()
    );
    
    $poll_id = wp_insert_post($poll_data);
    
    if (is_wp_error($poll_id)) {
        wp_send_json_error(array('message' => 'Failed to create poll: ' . $poll_id->get_error_message()));
    }
    
    // Save poll options
    update_post_meta($poll_id, '_poll_options', $options);
    
    // Set the poll type
    wp_set_object_terms($poll_id, $poll_type, 'poll_type');
    update_post_meta($poll_id, '_poll_type', $poll_type);
    
    // Save poll settings
    if (!empty($end_date)) {
        update_post_meta($poll_id, '_poll_end_date', $end_date);
    }
    
    update_post_meta($poll_id, '_poll_show_results', $show_results ? '1' : '0');
    update_post_meta($poll_id, '_poll_results_display', $results_display);
    update_post_meta($poll_id, '_poll_allow_comments', $allow_comments ? '1' : '0');
    
    // Save admin settings if user has permission
    if (current_user_can('manage_poll_settings')) {
        update_post_meta($poll_id, '_poll_featured', $is_featured ? '1' : '0');
        update_post_meta($poll_id, '_poll_is_private', $is_private ? '1' : '0');
        update_post_meta($poll_id, '_poll_require_login', $require_login ? '1' : '0');
        
        if ($max_votes > 0) {
            update_post_meta($poll_id, '_poll_max_votes', $max_votes);
        }
    }
    
    // Save type-specific data
    if ($poll_type === 'image-based' && !empty($option_images)) {
        update_post_meta($poll_id, '_poll_option_images', $option_images);
    } elseif ($poll_type === 'quiz' && !empty($correct_answers)) {
        update_post_meta($poll_id, '_poll_correct_answers', $correct_answers);
    } elseif ($poll_type === 'rating-scale') {
        $min_rating = isset($_POST['poll_min_rating']) ? intval($_POST['poll_min_rating']) : 1;
        $max_rating = isset($_POST['poll_max_rating']) ? intval($_POST['poll_max_rating']) : 5;
        update_post_meta($poll_id, '_poll_rating_min', $min_rating);
        update_post_meta($poll_id, '_poll_rating_max', $max_rating);
    }
    
    // Generate shortcode for this poll
    $shortcode = '[pollify id="' . $poll_id . '"]';
    
    // Send email notification to admin if enabled
    $settings = get_option('pollify_settings', array());
    if (isset($settings['admin_notification_new_poll']) && $settings['admin_notification_new_poll']) {
        $to = isset($settings['admin_notification_email']) ? $settings['admin_notification_email'] : get_option('admin_email');
        $subject = sprintf(__('[%s] New Poll Created: %s', 'pollify'), get_bloginfo('name'), $title);
        $message = sprintf(
            __('A new poll has been created on %s.\n\nTitle: %s\nAuthor: %s\nStatus: %s\n\nView: %s\nEdit: %s', 'pollify'),
            get_bloginfo('name'),
            $title,
            wp_get_current_user()->display_name,
            $post_status === 'publish' ? __('Published', 'pollify') : __('Pending Review', 'pollify'),
            get_permalink($poll_id),
            admin_url('post.php?post=' . $poll_id . '&action=edit')
        );
        
        wp_mail($to, $subject, $message);
    }
    
    wp_send_json_success(array(
        'message' => $post_status === 'publish' 
            ? __('Poll created successfully.', 'pollify')
            : __('Poll submitted for review.', 'pollify'),
        'pollId' => $poll_id,
        'pollUrl' => get_permalink($poll_id),
        'shortcode' => $shortcode,
        'status' => $post_status
    ));
}
add_action('wp_ajax_pollify_create_poll', 'pollify_ajax_create_poll');
