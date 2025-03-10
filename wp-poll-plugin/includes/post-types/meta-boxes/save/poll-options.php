
<?php
/**
 * Save poll options meta
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save the poll options
 */
function pollify_save_poll_options($post_id) {
    // Sanitize and save the poll options
    if (isset($_POST['poll_options'])) {
        $options = array_map('sanitize_text_field', $_POST['poll_options']);
        $options = array_filter($options); // Remove empty options
        
        if (count($options) < 2) {
            // Add error message
            add_filter('redirect_post_location', function($location) {
                return add_query_arg('pollify_error', 'options', $location);
            });
        } else {
            update_post_meta($post_id, '_poll_options', $options);
        }
    }
}
