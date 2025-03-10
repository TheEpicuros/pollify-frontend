
<?php
/**
 * Poll results rendering utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate poll results HTML based on display type
 */
function pollify_get_results_html($poll_id, $options, $vote_counts, $total_votes, $display_type = 'bar', $user_vote = null, $poll_type = 'multiple-choice') {
    ob_start();
    
    // Calculate percentages
    $percentages = array();
    foreach ($options as $option_id => $option_text) {
        $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
        $percentages[$option_id] = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
    }
    
    // Sort options by votes (descending)
    arsort($vote_counts);
    $sorted_option_ids = array_keys($vote_counts);
    
    // If no votes, display in original order
    if (empty($sorted_option_ids)) {
        $sorted_option_ids = array_keys($options);
    }
    
    // Check if this is an image-based poll
    $option_images = array();
    if ($poll_type === 'image-based') {
        $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    }
    
    // Determine chart colors
    $colors = array('#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6', '#f43f5e', '#84cc16');
    
    // Special handling for different poll types
    switch ($poll_type) {
        case 'open-ended':
            echo pollify_render_open_ended_results($poll_id, $options, $vote_counts);
            break;
            
        case 'ranked-choice':
            echo pollify_render_ranked_choice_results($poll_id, $options, $vote_counts);
            break;
            
        case 'rating-scale':
            // For rating scale, we'll show the average rating prominently
            echo pollify_render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
            break;
            
        default:
            // For other poll types, use the standard display types
            switch ($display_type) {
                case 'pie':
                case 'donut':
                    // Output data for chart.js
                    $chart_data = array(
                        'labels' => array(),
                        'datasets' => array(
                            array(
                                'data' => array(),
                                'backgroundColor' => array(),
                            )
                        )
                    );
                    
                    foreach ($sorted_option_ids as $i => $option_id) {
                        if (isset($options[$option_id])) {
                            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                            $chart_data['labels'][] = $options[$option_id];
                            $chart_data['datasets'][0]['data'][] = $vote_count;
                            $chart_data['datasets'][0]['backgroundColor'][] = $colors[$i % count($colors)];
                        }
                    }
                    
                    $chart_id = 'pollify-chart-' . $poll_id;
                    ?>
                    <div class="pollify-poll-results pollify-poll-results-chart">
                        <canvas id="<?php echo esc_attr($chart_id); ?>" width="400" height="300"></canvas>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var ctx = document.getElementById('<?php echo esc_js($chart_id); ?>').getContext('2d');
                            var chart = new Chart(ctx, {
                                type: '<?php echo esc_js($display_type); ?>',
                                data: <?php echo json_encode($chart_data); ?>,
                                options: {
                                    responsive: true,
                                    <?php if ($display_type === 'donut') : ?>
                                    cutout: '50%',
                                    <?php endif; ?>
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        }
                                    }
                                }
                            });
                        });
                        </script>
                        
                        <div class="pollify-poll-total">
                            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
                        </div>
                    </div>
                    <?php
                    break;
                    
                case 'text':
                    ?>
                    <div class="pollify-poll-results pollify-poll-results-text">
                        <ol class="pollify-poll-results-list">
                            <?php foreach ($sorted_option_ids as $option_id) : ?>
                                <?php if (isset($options[$option_id])) : ?>
                                    <?php 
                                    $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                                    $percentage = isset($percentages[$option_id]) ? $percentages[$option_id] : 0;
                                    $user_selected = isset($user_vote->option_id) && $user_vote->option_id == $option_id;
                                    ?>
                                    <li class="pollify-poll-result<?php echo $user_selected ? ' pollify-user-voted' : ''; ?>">
                                        <div class="pollify-poll-option-text">
                                            <?php if ($poll_type === 'image-based' && isset($option_images[$option_id])) : ?>
                                                <div class="pollify-result-image">
                                                    <img src="<?php echo esc_url(wp_get_attachment_image_url($option_images[$option_id], 'thumbnail')); ?>" alt="<?php echo esc_attr($options[$option_id]); ?>">
                                                </div>
                                            <?php endif; ?>
                                            <span class="pollify-option-text-value">
                                                <?php echo esc_html($options[$option_id]); ?>
                                                <?php if ($user_selected) : ?>
                                                    <span class="pollify-your-vote"><?php _e('(your vote)', 'pollify'); ?></span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="pollify-poll-option-count">
                                                <?php echo $percentage; ?>% (<?php echo number_format_i18n($vote_count); ?>)
                                            </span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                        
                        <div class="pollify-poll-total">
                            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
                        </div>
                    </div>
                    <?php
                    break;
                    
                case 'bar':
                default:
                    ?>
                    <div class="pollify-poll-results">
                        <?php foreach ($sorted_option_ids as $option_id) : ?>
                            <?php if (isset($options[$option_id])) : ?>
                                <?php 
                                $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                                $percentage = isset($percentages[$option_id]) ? $percentages[$option_id] : 0;
                                $user_selected = isset($user_vote->option_id) && $user_vote->option_id == $option_id;
                                ?>
                                <div class="pollify-poll-result<?php echo $user_selected ? ' pollify-user-voted' : ''; ?>">
                                    <div class="pollify-poll-option-text">
                                        <?php if ($poll_type === 'image-based' && isset($option_images[$option_id])) : ?>
                                            <div class="pollify-result-image">
                                                <img src="<?php echo esc_url(wp_get_attachment_image_url($option_images[$option_id], 'thumbnail')); ?>" alt="<?php echo esc_attr($options[$option_id]); ?>">
                                            </div>
                                        <?php endif; ?>
                                        <span class="pollify-option-text-value">
                                            <?php echo esc_html($options[$option_id]); ?>
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="pollify-poll-total">
                            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
                        </div>
                    </div>
                    <?php
                    break;
            }
    }
    
    return ob_get_clean();
}
