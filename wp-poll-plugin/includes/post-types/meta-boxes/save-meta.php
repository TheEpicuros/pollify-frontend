
<?php
/**
 * Poll meta save functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save the poll meta data
 */
function pollify_save_poll_meta($post_id, $post) {
    // Check if our nonce is set
    if (!isset($_POST['pollify_poll_meta_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['pollify_poll_meta_nonce'], 'pollify_save_poll_meta')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
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
    
    // Save poll type
    if (isset($_POST['_poll_type'])) {
        $poll_type = sanitize_text_field($_POST['_poll_type']);
        
        // Set the poll type taxonomy
        wp_set_object_terms($post_id, $poll_type, 'poll_type');
        
        // For image-based polls, save the image IDs
        if ($poll_type === 'image-based' && isset($_POST['poll_option_images'])) {
            $images = array_map('absint', $_POST['poll_option_images']);
            update_post_meta($post_id, '_poll_option_images', $images);
        }
        
        // For multi-stage polls, save the stages
        if ($poll_type === 'multi-stage' && isset($_POST['poll_stages'])) {
            $stages = array_map('sanitize_text_field', $_POST['poll_stages']);
            $stages = array_filter($stages); // Remove empty stages
            update_post_meta($post_id, '_poll_stages', $stages);
            
            // Current stage (default to 0 if not set)
            $current_stage = isset($_POST['_poll_current_stage']) ? absint($_POST['_poll_current_stage']) : 0;
            update_post_meta($post_id, '_poll_current_stage', $current_stage);
        }
    }
    
    // Save other poll settings
    if (isset($_POST['_poll_end_date'])) {
        update_post_meta($post_id, '_poll_end_date', sanitize_text_field($_POST['_poll_end_date']));
    }
    
    update_post_meta($post_id, '_poll_show_results', isset($_POST['_poll_show_results']) ? '1' : '0');
    
    if (isset($_POST['_poll_results_display'])) {
        update_post_meta($post_id, '_poll_results_display', sanitize_text_field($_POST['_poll_results_display']));
    }
    
    update_post_meta($post_id, '_poll_allow_comments', isset($_POST['_poll_allow_comments']) ? '1' : '0');
    
    if (isset($_POST['_poll_allowed_roles'])) {
        $allowed_roles = array_map('sanitize_text_field', $_POST['_poll_allowed_roles']);
        update_post_meta($post_id, '_poll_allowed_roles', $allowed_roles);
    }
    
    // Save admin settings if the user has permission
    if (current_user_can('manage_poll_settings')) {
        // Featured poll
        update_post_meta($post_id, '_poll_featured', isset($_POST['_poll_featured']) ? '1' : '0');
        
        // Private poll
        update_post_meta($post_id, '_poll_is_private', isset($_POST['_poll_is_private']) ? '1' : '0');
        
        // Require login
        update_post_meta($post_id, '_poll_require_login', isset($_POST['_poll_require_login']) ? '1' : '0');
        
        // Maximum votes
        if (isset($_POST['_poll_max_votes'])) {
            $max_votes = absint($_POST['_poll_max_votes']);
            update_post_meta($post_id, '_poll_max_votes', $max_votes);
        }
        
        // IP restriction
        update_post_meta($post_id, '_poll_ip_restriction', isset($_POST['_poll_ip_restriction']) ? '1' : '0');
        
        // Vote frequency
        if (isset($_POST['_poll_vote_frequency'])) {
            $vote_frequency = sanitize_text_field($_POST['_poll_vote_frequency']);
            update_post_meta($post_id, '_poll_vote_frequency', $vote_frequency);
        }
        
        // Comment moderation
        update_post_meta($post_id, '_poll_moderation', isset($_POST['_poll_moderation']) ? '1' : '0');
        
        // Auto-close poll
        update_post_meta($post_id, '_poll_auto_close', isset($_POST['_poll_auto_close']) ? '1' : '0');
        
        // Vote threshold
        if (isset($_POST['_poll_vote_threshold'])) {
            $vote_threshold = absint($_POST['_poll_vote_threshold']);
            update_post_meta($post_id, '_poll_vote_threshold', $vote_threshold);
        }
        
        // Notification email
        if (isset($_POST['_poll_notification_email'])) {
            $email = sanitize_email($_POST['_poll_notification_email']);
            update_post_meta($post_id, '_poll_notification_email', $email);
        }
        
        // Custom CSS
        if (isset($_POST['_poll_custom_css'])) {
            $custom_css = wp_strip_all_tags($_POST['_poll_custom_css']);
            update_post_meta($post_id, '_poll_custom_css', $custom_css);
        }
    }
}

/**
 * Display admin notices for poll errors
 */
function pollify_admin_notices() {
    if (isset($_GET['pollify_error']) && $_GET['pollify_error'] === 'options') {
        ?>
        <div class="error">
            <p><?php _e('A poll must have at least two options. Please add more options.', 'pollify'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'pollify_admin_notices');
