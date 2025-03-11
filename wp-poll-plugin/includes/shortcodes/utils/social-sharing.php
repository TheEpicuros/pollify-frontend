
<?php
/**
 * Social sharing utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

/**
 * Wrapper for the canonical implementation of social sharing HTML
 */
function pollify_get_social_sharing_buttons($poll_id) {
    // Require the canonical implementation
    if (!function_exists('pollify_get_social_sharing_html')) {
        pollify_require_function('pollify_get_social_sharing_html');
        
        // Fallback if canonical function cannot be loaded
        if (!function_exists('pollify_get_social_sharing_html')) {
            $post = get_post($poll_id);
            $permalink = get_permalink($poll_id);
            $title = get_the_title($poll_id);
            
            ob_start();
            ?>
            <div class="pollify-social-sharing">
                <h4><?php _e('Share this poll:', 'pollify'); ?></h4>
                <div class="pollify-social-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" class="pollify-social-button" target="_blank">Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" class="pollify-social-button" target="_blank">Twitter</a>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
    }
    
    return pollify_get_social_sharing_html($poll_id);
}
