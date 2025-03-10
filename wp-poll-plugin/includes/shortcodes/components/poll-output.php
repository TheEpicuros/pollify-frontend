
<?php
/**
 * Poll output rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate the complete poll output
 */
function pollify_generate_poll_output($poll_id, $poll, $options, $poll_settings, $display_settings, $voting_status, $display_results) {
    // Load Chart.js for pie/donut charts if needed
    if (($display_results && ($display_settings['results_display'] === 'pie' || $display_settings['results_display'] === 'donut'))) {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.8.0', true);
    }
    
    ob_start();
    ?>
    <div id="pollify-poll-<?php echo $poll_id; ?>" class="pollify-poll pollify-poll-type-<?php echo esc_attr($poll_settings['poll_type']); ?><?php echo $display_settings['align']; ?>"<?php echo $display_settings['width']; ?> data-poll-id="<?php echo $poll_id; ?>">
        <div class="pollify-poll-container">
            <?php echo pollify_render_poll_header($poll_id, $poll, $voting_status['total_votes'], $poll_settings['poll_end_date'], $voting_status['has_ended']); ?>
            
            <?php if ($poll->post_content) : ?>
            <div class="pollify-poll-description">
                <?php echo wpautop($poll->post_content); ?>
            </div>
            <?php endif; ?>
            
            <div class="pollify-poll-options">
                <?php if ($display_results) : ?>
                    <!-- Results view -->
                    <?php echo pollify_get_results_html($poll_id, $options, $voting_status['vote_counts'], $voting_status['total_votes'], $display_settings['results_display'], $voting_status['user_vote']); ?>
                    
                    <?php echo pollify_render_user_vote_info($voting_status['user_vote']); ?>
                <?php else : ?>
                    <!-- Voting form -->
                    <?php echo pollify_render_poll_form($poll_id, $poll, $options, $poll_settings['poll_type'], $display_settings['show_results'] && !$voting_status['has_voted']); ?>
                <?php endif; ?>
            </div>
            
            <?php if ($display_results && $display_settings['show_social']): ?>
            <div class="pollify-poll-footer">
                <?php echo pollify_get_social_sharing_html($poll_id); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($display_results && $display_settings['show_ratings']): ?>
            <div class="pollify-poll-rating-section">
                <?php echo pollify_get_rating_html($poll_id); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($display_results && $display_settings['show_comments']): ?>
            <div class="pollify-poll-comments-section">
                <?php echo pollify_get_comments_html($poll_id); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
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
