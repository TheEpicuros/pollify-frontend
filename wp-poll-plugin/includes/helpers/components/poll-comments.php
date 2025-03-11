
<?php
/**
 * Poll comments helper functions
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
 * Get HTML for poll comments - registered as the canonical function
 * 
 * @param int $poll_id Poll ID
 * @return string HTML for poll comments
 */
if (pollify_can_define_function('pollify_get_comments_html')) {
    pollify_declare_function('pollify_get_comments_html', function($poll_id) {
        $comments = pollify_get_poll_comments($poll_id, 5);
        $allow_comments = get_post_meta($poll_id, '_poll_allow_comments', true) === '1';
        
        ob_start();
        ?>
        <div class="pollify-poll-comments" data-poll-id="<?php echo esc_attr($poll_id); ?>">
            <h3 class="pollify-comments-title">
                <?php _e('Comments', 'pollify'); ?>
                <span class="pollify-comments-count">(<?php echo count($comments); ?>)</span>
            </h3>
            
            <?php if ($allow_comments && is_user_logged_in()): ?>
            <div class="pollify-comment-form">
                <div class="pollify-comment-form-content">
                    <textarea name="pollify_comment" placeholder="<?php _e('Add your comment...', 'pollify'); ?>" rows="3"></textarea>
                </div>
                
                <div class="pollify-comment-form-footer">
                    <button type="button" class="pollify-submit-comment" data-nonce="<?php echo wp_create_nonce('pollify-add-comment'); ?>">
                        <?php _e('Submit Comment', 'pollify'); ?>
                    </button>
                </div>
            </div>
            <?php elseif (!$allow_comments): ?>
            <div class="pollify-comments-disabled">
                <p><?php _e('Comments are disabled for this poll.', 'pollify'); ?></p>
            </div>
            <?php else: ?>
            <div class="pollify-comment-login-required">
                <p>
                    <?php 
                    printf(
                        __('You must be <a href="%s">logged in</a> to leave a comment.', 'pollify'),
                        wp_login_url(get_permalink($poll_id))
                    ); 
                    ?>
                </p>
            </div>
            <?php endif; ?>
            
            <div class="pollify-comments-list">
                <?php if (empty($comments)): ?>
                <div class="pollify-no-comments">
                    <p><?php _e('No comments yet. Be the first to comment!', 'pollify'); ?></p>
                </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
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
                    
                    <?php if (count($comments) >= 5): ?>
                    <div class="pollify-load-more-comments">
                        <button type="button" class="pollify-load-more-button" data-offset="5" data-nonce="<?php echo wp_create_nonce('pollify-load-comments'); ?>">
                            <?php _e('Load More Comments', 'pollify'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }, $current_file);
}
