
<?php
/**
 * Social sharing helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get HTML for social sharing - registered as the canonical function
 * 
 * @param int $poll_id Poll ID
 * @return string HTML for social sharing buttons
 */
if (pollify_can_define_function('pollify_get_social_sharing_html')) {
    pollify_declare_function('pollify_get_social_sharing_html', function($poll_id) {
        $poll_url = get_permalink($poll_id);
        $poll_title = get_the_title($poll_id);
        $encoded_url = urlencode($poll_url);
        $encoded_title = urlencode($poll_title);
        
        ob_start();
        ?>
        <div class="pollify-social-sharing">
            <span class="pollify-share-label"><?php _e('Share:', 'pollify'); ?></span>
            
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" class="pollify-share-facebook" aria-label="<?php _e('Share on Facebook', 'pollify'); ?>">
                <span class="dashicons dashicons-facebook"></span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo $encoded_url; ?>&text=<?php echo $encoded_title; ?>" target="_blank" class="pollify-share-twitter" aria-label="<?php _e('Share on Twitter', 'pollify'); ?>">
                <span class="dashicons dashicons-twitter"></span>
            </a>
            
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $encoded_url; ?>&title=<?php echo $encoded_title; ?>" target="_blank" class="pollify-share-linkedin" aria-label="<?php _e('Share on LinkedIn', 'pollify'); ?>">
                <span class="dashicons dashicons-linkedin"></span>
            </a>
            
            <a href="mailto:?subject=<?php echo $encoded_title; ?>&body=<?php echo __('Check out this poll:', 'pollify') . ' ' . $encoded_url; ?>" class="pollify-share-email" aria-label="<?php _e('Share via Email', 'pollify'); ?>">
                <span class="dashicons dashicons-email"></span>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }, $current_file);
}
