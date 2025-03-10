
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
    // Include the core date formatting function if not already included
    if (!function_exists('pollify_format_date')) {
        require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils/date-formatting.php';
    }
    
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

