
<?php
/**
 * Rating scale poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render rating scale results
 */
function pollify_render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes) {
    // Calculate average rating
    $total_rating = 0;
    $total_votes_counted = 0;
    
    foreach ($options as $option_id => $option_text) {
        $rating_value = intval($option_text);
        if ($rating_value > 0) {
            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $total_rating += $rating_value * $vote_count;
            $total_votes_counted += $vote_count;
        }
    }
    
    $average_rating = $total_votes_counted > 0 ? round($total_rating / $total_votes_counted, 1) : 0;
    
    ob_start();
    ?>
    <div class="pollify-rating-scale-results">
        <div class="pollify-average-rating">
            <div class="pollify-average-rating-value"><?php echo $average_rating; ?></div>
            <div class="pollify-average-rating-label"><?php _e('Average Rating', 'pollify'); ?></div>
        </div>
        
        <div class="pollify-rating-distribution">
            <?php foreach ($options as $option_id => $option_text) : ?>
                <?php 
                $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
                ?>
                <div class="pollify-rating-bar-container">
                    <div class="pollify-rating-label"><?php echo esc_html($option_text); ?></div>
                    <div class="pollify-rating-bar">
                        <div class="pollify-rating-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                    <div class="pollify-rating-count"><?php echo $vote_count; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pollify-poll-total">
            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
