
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
    $ip_restriction = get_post_meta($post->ID, '_poll_ip_restriction', true);
    $vote_frequency = get_post_meta($post->ID, '_poll_vote_frequency', true);
    $moderation = get_post_meta($post->ID, '_poll_moderation', true);
    $auto_close = get_post_meta($post->ID, '_poll_auto_close', true);
    $vote_threshold = get_post_meta($post->ID, '_poll_vote_threshold', true);
    
    ?>
    <div class="pollify-admin-settings">
        <div class="pollify-admin-settings-section">
            <h3><?php _e('Basic Settings', 'pollify'); ?></h3>
            
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
        </div>
        
        <div class="pollify-admin-settings-section">
            <h3><?php _e('Access & Permissions', 'pollify'); ?></h3>
            
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
                <label for="_poll_ip_restriction">
                    <input 
                        type="checkbox" 
                        id="_poll_ip_restriction" 
                        name="_poll_ip_restriction" 
                        value="1" 
                        <?php checked($ip_restriction, '1'); ?>
                    >
                    <?php _e('Enable IP restriction', 'pollify'); ?>
                </label>
                <span class="description"><?php _e('Prevent multiple votes from the same IP address', 'pollify'); ?></span>
            </p>
            
            <p>
                <label for="_poll_vote_frequency"><?php _e('Vote frequency restriction:', 'pollify'); ?></label>
                <select id="_poll_vote_frequency" name="_poll_vote_frequency">
                    <option value="" <?php selected($vote_frequency, ''); ?>><?php _e('No restriction', 'pollify'); ?></option>
                    <option value="once" <?php selected($vote_frequency, 'once'); ?>><?php _e('Once per user', 'pollify'); ?></option>
                    <option value="daily" <?php selected($vote_frequency, 'daily'); ?>><?php _e('Once per day', 'pollify'); ?></option>
                    <option value="weekly" <?php selected($vote_frequency, 'weekly'); ?>><?php _e('Once per week', 'pollify'); ?></option>
                    <option value="monthly" <?php selected($vote_frequency, 'monthly'); ?>><?php _e('Once per month', 'pollify'); ?></option>
                </select>
            </p>
        </div>
        
        <div class="pollify-admin-settings-section">
            <h3><?php _e('Moderation & Automation', 'pollify'); ?></h3>
            
            <p>
                <label for="_poll_moderation">
                    <input 
                        type="checkbox" 
                        id="_poll_moderation" 
                        name="_poll_moderation" 
                        value="1" 
                        <?php checked($moderation, '1'); ?>
                    >
                    <?php _e('Enable comment moderation', 'pollify'); ?>
                </label>
                <span class="description"><?php _e('All comments must be approved before appearing', 'pollify'); ?></span>
            </p>
            
            <p>
                <label for="_poll_auto_close">
                    <input 
                        type="checkbox" 
                        id="_poll_auto_close" 
                        name="_poll_auto_close" 
                        value="1" 
                        <?php checked($auto_close, '1'); ?>
                    >
                    <?php _e('Automatically close poll when vote threshold is reached', 'pollify'); ?>
                </label>
            </p>
            
            <p>
                <label for="_poll_vote_threshold"><?php _e('Vote threshold:', 'pollify'); ?></label>
                <input 
                    type="number" 
                    id="_poll_vote_threshold" 
                    name="_poll_vote_threshold" 
                    value="<?php echo esc_attr($vote_threshold); ?>" 
                    class="small-text"
                    min="0"
                >
                <span class="description"><?php _e('Number of votes to trigger auto-close (if enabled)', 'pollify'); ?></span>
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
        </div>
        
        <div class="pollify-admin-settings-section">
            <h3><?php _e('Advanced', 'pollify'); ?></h3>
            
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
    </div>
    
    <style>
    .pollify-admin-settings-section {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .pollify-admin-settings-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .pollify-admin-settings-section h3 {
        margin-top: 5px;
        margin-bottom: 10px;
    }
    </style>
    <?php
}
