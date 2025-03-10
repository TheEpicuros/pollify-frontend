
<?php
/**
 * Poll header rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll header
 */
function pollify_render_poll_header($poll_id, $poll, $total_votes, $poll_end_date, $has_ended) {
    ob_start();
    ?>
    <div class="pollify-poll-header">
        <h3 class="pollify-poll-title"><?php echo esc_html($poll->post_title); ?></h3>
        
        <div class="pollify-poll-meta">
            <span class="pollify-poll-type"><?php echo esc_html(pollify_get_poll_type_name($poll_id)); ?></span>
            
            <?php if ($total_votes > 0) : ?>
            <span class="pollify-poll-votes">
                <?php echo sprintf(_n('%s vote', '%s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
            </span>
            <?php endif; ?>
            
            <?php if ($has_ended) : ?>
            <span class="pollify-poll-ended"><?php _e('Ended', 'pollify'); ?></span>
            <?php elseif (!empty($poll_end_date)) : ?>
            <span class="pollify-poll-ends">
                <?php 
                    printf(
                        __('Ends: %s', 'pollify'), 
                        pollify_format_date($poll_end_date)
                    ); 
                ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get poll type name from slug
 */
function pollify_get_poll_type_name($poll_id) {
    $poll_type = pollify_get_poll_type($poll_id);
    
    $poll_type_names = array(
        'binary' => __('Yes/No', 'pollify'),
        'multiple-choice' => __('Multiple Choice', 'pollify'),
        'check-all' => __('Multiple Answers', 'pollify'),
        'ranked-choice' => __('Ranked Choice', 'pollify'),
        'rating-scale' => __('Rating Scale', 'pollify'),
        'open-ended' => __('Open Response', 'pollify'),
        'image-based' => __('Image Poll', 'pollify'),
        'quiz' => __('Quiz', 'pollify'),
        'opinion' => __('Opinion Poll', 'pollify'),
        'straw' => __('Straw Poll', 'pollify'),
        'interactive' => __('Interactive Poll', 'pollify'),
        'referendum' => __('Referendum', 'pollify')
    );
    
    return isset($poll_type_names[$poll_type]) ? $poll_type_names[$poll_type] : __('Poll', 'pollify');
}
