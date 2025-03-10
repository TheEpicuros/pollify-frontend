
<?php
/**
 * Admin-only poll settings meta box
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the admin settings meta box
 */
function pollify_admin_settings_callback($post) {
    // Get current values
    $featured = get_post_meta($post->ID, '_poll_featured', true);
    $is_private = get_post_meta($post->ID, '_poll_is_private', true);
    $require_login = get_post_meta($post->ID, '_poll_require_login', true);
    $notification_email = get_post_meta($post->ID, '_poll_notification_email', true);
    $max_votes = get_post_meta($post->ID, '_poll_max_votes', true);
    $custom_css = get_post_meta($post->ID, '_poll_custom_css', true);
    
    ?>
    <div class="pollify-admin-settings">
        <p>
            <label for="_poll_featured">
                <input 
                    type="checkbox" 
                    id="_poll_featured" 
                    name="_poll_featured" 
                    value="1" 
                    <?php checked($featured, '1'); ?>
                >
                <?php _e('Feature this poll', 'pollify'); ?>
            </label>
            <span class="description"><?php _e('Featured polls will be highlighted in listings', 'pollify'); ?></span>
        </p>
        
        <p>
            <label for="_poll_is_private">
                <input 
                    type="checkbox" 
                    id="_poll_is_private" 
                    name="_poll_is_private" 
                    value="1" 
                    <?php checked($is_private, '1'); ?>
                >
                <?php _e('Private poll', 'pollify'); ?>
            </label>
            <span class="description"><?php _e('Only accessible via direct link', 'pollify'); ?></span>
        </p>
        
        <p>
            <label for="_poll_require_login">
                <input 
                    type="checkbox" 
                    id="_poll_require_login" 
                    name="_poll_require_login" 
                    value="1" 
                    <?php checked($require_login, '1'); ?>
                >
                <?php _e('Require login to vote', 'pollify'); ?>
            </label>
        </p>
        
        <p>
            <label for="_poll_max_votes"><?php _e('Maximum votes allowed:', 'pollify'); ?></label>
            <input 
                type="number" 
                id="_poll_max_votes" 
                name="_poll_max_votes" 
                value="<?php echo esc_attr($max_votes); ?>" 
                class="small-text"
                min="0"
            >
            <span class="description"><?php _e('Leave empty for unlimited votes', 'pollify'); ?></span>
        </p>
        
        <p>
            <label for="_poll_notification_email"><?php _e('Notification email:', 'pollify'); ?></label>
            <input 
                type="email" 
                id="_poll_notification_email" 
                name="_poll_notification_email" 
                value="<?php echo esc_attr($notification_email); ?>" 
                class="regular-text"
            >
            <span class="description"><?php _e('Email to notify when this poll receives votes', 'pollify'); ?></span>
        </p>
        
        <p>
            <label for="_poll_custom_css"><?php _e('Custom CSS:', 'pollify'); ?></label>
            <textarea 
                id="_poll_custom_css" 
                name="_poll_custom_css" 
                class="large-text code" 
                rows="5"
            ><?php echo esc_textarea($custom_css); ?></textarea>
            <span class="description"><?php _e('Custom CSS for this poll only', 'pollify'); ?></span>
        </p>
    </div>
    <?php
}
