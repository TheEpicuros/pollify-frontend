
<?php
/**
 * Poll rating helper functions
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
 * Get HTML for poll rating - registered as the canonical function
 * 
 * @param int $poll_id Poll ID
 * @return string HTML for poll rating
 */
if (pollify_can_define_function('pollify_get_rating_html')) {
    pollify_declare_function('pollify_get_rating_html', function($poll_id) {
        $ratings = pollify_get_poll_ratings($poll_id);
        $user_ip = pollify_get_user_ip();
        $user_id = get_current_user_id();
        
        ob_start();
        ?>
        <div class="pollify-poll-rating" data-poll-id="<?php echo $poll_id; ?>">
            <div class="pollify-rating-question">
                <?php _e('Was this poll helpful?', 'pollify'); ?>
            </div>
            
            <div class="pollify-rating-buttons">
                <button type="button" class="pollify-rating-button pollify-rating-up" data-rating="1" aria-label="<?php _e('Thumbs up', 'pollify'); ?>">
                    <span class="dashicons dashicons-thumbs-up"></span>
                    <span class="pollify-rating-count"><?php echo (int) $ratings['upvotes']; ?></span>
                </button>
                
                <button type="button" class="pollify-rating-button pollify-rating-down" data-rating="0" aria-label="<?php _e('Thumbs down', 'pollify'); ?>">
                    <span class="dashicons dashicons-thumbs-down"></span>
                    <span class="pollify-rating-count"><?php echo (int) $ratings['downvotes']; ?></span>
                </button>
            </div>
            
            <div class="pollify-rating-message" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }, $current_file);
}
