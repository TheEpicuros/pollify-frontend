
<?php
/**
 * Bar chart results renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render bar chart results
 */
function pollify_render_bar_chart_results($options, $vote_counts, $total_votes, $percentages, $user_vote = null, $poll_type = 'multiple-choice', $option_images = array()) {
    ob_start();
    ?>
    <div class="pollify-poll-results">
        <?php foreach ($options as $option_id => $option_text) : ?>
            <?php 
            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $percentage = isset($percentages[$option_id]) ? $percentages[$option_id] : 0;
            $user_selected = isset($user_vote->option_id) && $user_vote->option_id == $option_id;
            ?>
            <div class="pollify-poll-result<?php echo $user_selected ? ' pollify-user-voted' : ''; ?>">
                <div class="pollify-poll-option-text">
                    <?php if ($poll_type === 'image-based' && isset($option_images[$option_id])) : ?>
                        <div class="pollify-result-image">
                            <img src="<?php echo esc_url(wp_get_attachment_image_url($option_images[$option_id], 'thumbnail')); ?>" alt="<?php echo esc_attr($option_text); ?>">
                        </div>
                    <?php endif; ?>
                    <span class="pollify-option-text-value">
                        <?php echo esc_html($option_text); ?>
                        <?php if ($user_selected) : ?>
                            <span class="pollify-your-vote"><?php _e('(your vote)', 'pollify'); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="pollify-poll-option-count">
                        <?php echo $percentage; ?>%
                    </span>
                </div>
                <div class="pollify-poll-option-bar">
                    <div class="pollify-poll-option-bar-fill <?php echo $user_selected ? 'pollify-progress-animated' : ''; ?>" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <div class="pollify-poll-option-vote-count">
                    <?php echo number_format_i18n($vote_count); ?> <?php echo _n('vote', 'votes', $vote_count, 'pollify'); ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="pollify-poll-total">
            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
