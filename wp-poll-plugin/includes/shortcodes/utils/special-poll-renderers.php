
<?php
/**
 * Special poll type results renderers
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render open-ended results
 */
function pollify_render_open_ended_results($poll_id, $options, $vote_counts) {
    global $wpdb;
    
    // Get open-ended responses
    $table_name = $wpdb->prefix . 'pollify_open_responses';
    
    $responses = $wpdb->get_results($wpdb->prepare(
        "SELECT response, DATE_FORMAT(voted_at, '%%M %%d, %%Y') as formatted_date FROM $table_name WHERE poll_id = %d ORDER BY voted_at DESC LIMIT 50",
        $poll_id
    ));
    
    ob_start();
    ?>
    <div class="pollify-open-ended-results">
        <h3 class="pollify-responses-title"><?php _e('Responses', 'pollify'); ?></h3>
        
        <?php if (empty($responses)) : ?>
            <p class="pollify-no-responses"><?php _e('No responses yet.', 'pollify'); ?></p>
        <?php else : ?>
            <div class="pollify-responses-list">
                <?php foreach ($responses as $response) : ?>
                    <div class="pollify-response-item">
                        <div class="pollify-response-content"><?php echo wpautop(esc_html($response->response)); ?></div>
                        <div class="pollify-response-date"><?php echo esc_html($response->formatted_date); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="pollify-poll-total">
            <?php echo sprintf(_n('Total: %s response', 'Total: %s responses', count($responses), 'pollify'), count($responses)); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render ranked choice results
 */
function pollify_render_ranked_choice_results($poll_id, $options, $vote_counts) {
    global $wpdb;
    
    // Get ranked choices
    $table_name = $wpdb->prefix . 'pollify_ranked_votes';
    
    $rankings = $wpdb->get_results($wpdb->prepare(
        "SELECT option_id, rank, COUNT(*) as count FROM $table_name WHERE poll_id = %d GROUP BY option_id, rank ORDER BY rank",
        $poll_id
    ));
    
    // Organize data by rank
    $ranks = array();
    $max_count = 0;
    
    foreach ($rankings as $ranking) {
        if (!isset($ranks[$ranking->rank])) {
            $ranks[$ranking->rank] = array();
        }
        $ranks[$ranking->rank][$ranking->option_id] = $ranking->count;
        $max_count = max($max_count, $ranking->count);
    }
    
    ob_start();
    ?>
    <div class="pollify-ranked-choice-results">
        <h3 class="pollify-rankings-title"><?php _e('Rankings', 'pollify'); ?></h3>
        
        <?php if (empty($ranks)) : ?>
            <p class="pollify-no-rankings"><?php _e('No rankings yet.', 'pollify'); ?></p>
        <?php else : ?>
            <div class="pollify-rankings-table-container">
                <table class="pollify-rankings-table">
                    <thead>
                        <tr>
                            <th class="pollify-rank-header"><?php _e('Rank', 'pollify'); ?></th>
                            <?php foreach ($options as $option_id => $option_text) : ?>
                                <th class="pollify-option-header"><?php echo esc_html($option_text); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($rank = 1; $rank <= count($options); $rank++) : ?>
                            <tr>
                                <td class="pollify-rank-cell"><?php echo $rank; ?></td>
                                <?php foreach ($options as $option_id => $option_text) : ?>
                                    <td class="pollify-count-cell">
                                        <?php if (isset($ranks[$rank][$option_id])) : ?>
                                            <div class="pollify-count-bar-container">
                                                <div class="pollify-count-bar" style="width: <?php echo ($ranks[$rank][$option_id] / $max_count) * 100; ?>%"></div>
                                                <span class="pollify-count-value"><?php echo $ranks[$rank][$option_id]; ?></span>
                                            </div>
                                        <?php else : ?>
                                            <span class="pollify-count-zero">0</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="pollify-poll-total">
            <?php 
            $total_voters = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE poll_id = %d",
                $poll_id
            ));
            echo sprintf(_n('Total: %s voter', 'Total: %s voters', $total_voters, 'pollify'), $total_voters); 
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
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
