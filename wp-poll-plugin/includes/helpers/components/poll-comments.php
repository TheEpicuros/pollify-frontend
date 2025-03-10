
<?php
/**
 * Poll comments helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get HTML for poll comments
 */
function pollify_get_comments_html($poll_id) {
    $comments = pollify_get_poll_comments($poll_id, 5);
    
    ob_start();
    ?>
    <div class="pollify-poll-comments" data-poll-id="<?php echo $poll_id; ?>">
        <h3 class="pollify-comments-title">
            <?php _e('Comments', 'pollify'); ?>
            <span class="pollify-comments-count">(<?php echo count($comments); ?>)</span>
        </h3>
        
        <?php if (is_user_logged_in()): ?>
        <div class="pollify-comment-form">
            <div class="pollify-comment-form-content">
                <textarea name="pollify_comment" placeholder="<?php _e('Add your comment...', 'pollify'); ?>" rows="3"></textarea>
            </div>
            
            <div class="pollify-comment-form-footer">
                <button type="button" class="pollify-submit-comment">
                    <?php _e('Submit Comment', 'pollify'); ?>
                </button>
            </div>
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
                        <span class="pollify-comment-date"><?php echo pollify_format_date($comment->comment_date); ?></span>
                    </div>
                    
                    <div class="pollify-comment-body">
                        <?php echo wpautop(esc_html($comment->comment_text)); ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($comments) === 5): ?>
                <div class="pollify-load-more-comments">
                    <button type="button" class="pollify-load-more-button" data-offset="5">
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
