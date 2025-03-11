
<?php
/**
 * Poll comments rendering functions
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
 * Get comments HTML - deprecated, use canonical function instead
 * This function now forwards to the canonical implementation
 */
if (pollify_can_define_function('pollify_get_comments_html_renderer')) {
    pollify_declare_function('pollify_get_comments_html_renderer', function($poll_id) {
        // Require the canonical function if it exists
        if (!function_exists('pollify_get_comments_html')) {
            pollify_require_function('pollify_get_comments_html');
        }
        
        // Call the canonical function
        if (function_exists('pollify_get_comments_html')) {
            return pollify_get_comments_html($poll_id);
        }
        
        // Fallback implementation if canonical function not available
        $comments = get_comments(array(
            'post_id' => $poll_id,
            'status' => 'approve',
            'order' => 'ASC',
        ));
        
        ob_start();
        ?>
        <div class="pollify-comments-section">
            <h3 class="pollify-comments-title"><?php _e('Comments', 'pollify'); ?></h3>
            
            <?php if (comments_open($poll_id)) : ?>
                <?php if (!empty($comments)) : ?>
                    <div class="pollify-comments-list">
                        <?php
                        wp_list_comments(array(
                            'style' => 'div',
                            'short_ping' => true,
                            'avatar_size' => 40,
                        ), $comments);
                        ?>
                    </div>
                <?php else : ?>
                    <p class="pollify-no-comments"><?php _e('No comments yet. Be the first to comment!', 'pollify'); ?></p>
                <?php endif; ?>
                
                <?php if (is_user_logged_in()) : ?>
                    <div class="pollify-comment-form">
                        <h4><?php _e('Leave a comment', 'pollify'); ?></h4>
                        <?php
                        comment_form(array(
                            'title_reply' => '',
                            'label_submit' => __('Submit Comment', 'pollify'),
                            'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="4" aria-required="true" placeholder="' . esc_attr__('Your comment...', 'pollify') . '"></textarea></p>',
                        ), $poll_id);
                        ?>
                    </div>
                <?php else : ?>
                    <p class="pollify-login-to-comment">
                        <?php 
                        printf(
                            __('Please <a href="%s">log in</a> to leave a comment.', 'pollify'),
                            esc_url(wp_login_url(get_permalink($poll_id)))
                        ); 
                        ?>
                    </p>
                <?php endif; ?>
            <?php else : ?>
                <p class="pollify-comments-closed"><?php _e('Comments are closed for this poll.', 'pollify'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }, $current_file);
}
