
<?php
/**
 * Poll comments system utility functions
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
 * Get comments HTML for a poll - deprecated, use canonical function instead
 */
function pollify_get_comments_system_html($poll_id) {
    // Require the canonical function if it exists
    if (!function_exists('pollify_get_comments_html')) {
        pollify_require_function('pollify_get_comments_html');
    }
    
    // Call the canonical function
    if (function_exists('pollify_get_comments_html')) {
        return pollify_get_comments_html($poll_id);
    }
    
    // Fallback implementation
    $comments = pollify_get_poll_comments($poll_id, 10);
    $allow_comments = get_post_meta($poll_id, '_poll_allow_comments', true) === '1';
    
    ob_start();
    ?>
    <div class="pollify-poll-comments" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <h4><?php _e('Comments', 'pollify'); ?></h4>
        
        <?php if ($allow_comments && is_user_logged_in()) : ?>
            <div class="pollify-comment-form">
                <form class="pollify-add-comment-form" data-poll-id="<?php echo esc_attr($poll_id); ?>">
                    <div class="pollify-comment-input">
                        <textarea 
                            name="comment_text" 
                            placeholder="<?php esc_attr_e('Add your comment...', 'pollify'); ?>" 
                            rows="3" 
                            required 
                        ></textarea>
                    </div>
                    <div class="pollify-comment-submit">
                        <button 
                            type="submit" 
                            class="pollify-submit-comment" 
                            data-nonce="<?php echo wp_create_nonce('pollify-add-comment'); ?>"
                        >
                            <?php _e('Post Comment', 'pollify'); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif (!$allow_comments) : ?>
            <p class="pollify-comments-disabled"><?php _e('Comments are disabled for this poll.', 'pollify'); ?></p>
        <?php else : ?>
            <p class="pollify-login-to-comment"><?php _e('Please log in to comment on this poll.', 'pollify'); ?></p>
        <?php endif; ?>
        
        <div class="pollify-comments-list">
            <?php if (empty($comments)) : ?>
                <p class="pollify-no-comments"><?php _e('No comments yet. Be the first to comment!', 'pollify'); ?></p>
            <?php else : ?>
                <?php foreach ($comments as $comment) : ?>
                    <div class="pollify-comment">
                        <div class="pollify-comment-header">
                            <span class="pollify-comment-author"><?php echo esc_html($comment->user_name); ?></span>
                            <span class="pollify-comment-date"><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($comment->comment_date)); ?></span>
                        </div>
                        <div class="pollify-comment-body">
                            <?php echo wpautop(esc_html($comment->comment_text)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($comments) >= 10) : ?>
                    <div class="pollify-load-more-comments">
                        <button 
                            type="button" 
                            class="pollify-load-more" 
                            data-poll-id="<?php echo esc_attr($poll_id); ?>" 
                            data-offset="10" 
                            data-nonce="<?php echo wp_create_nonce('pollify-load-comments'); ?>"
                        >
                            <?php _e('Load More Comments', 'pollify'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
