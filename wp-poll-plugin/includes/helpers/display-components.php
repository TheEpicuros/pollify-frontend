
<?php
/**
 * Display component helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get HTML for social sharing
 */
function pollify_get_social_sharing_html($poll_id) {
    $poll_url = get_permalink($poll_id);
    $poll_title = get_the_title($poll_id);
    $encoded_url = urlencode($poll_url);
    $encoded_title = urlencode($poll_title);
    
    ob_start();
    ?>
    <div class="pollify-social-sharing">
        <span class="pollify-share-label"><?php _e('Share:', 'pollify'); ?></span>
        
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" class="pollify-share-facebook" aria-label="<?php _e('Share on Facebook', 'pollify'); ?>">
            <span class="dashicons dashicons-facebook"></span>
        </a>
        
        <a href="https://twitter.com/intent/tweet?url=<?php echo $encoded_url; ?>&text=<?php echo $encoded_title; ?>" target="_blank" class="pollify-share-twitter" aria-label="<?php _e('Share on Twitter', 'pollify'); ?>">
            <span class="dashicons dashicons-twitter"></span>
        </a>
        
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $encoded_url; ?>&title=<?php echo $encoded_title; ?>" target="_blank" class="pollify-share-linkedin" aria-label="<?php _e('Share on LinkedIn', 'pollify'); ?>">
            <span class="dashicons dashicons-linkedin"></span>
        </a>
        
        <a href="mailto:?subject=<?php echo $encoded_title; ?>&body=<?php echo __('Check out this poll:', 'pollify') . ' ' . $encoded_url; ?>" class="pollify-share-email" aria-label="<?php _e('Share via Email', 'pollify'); ?>">
            <span class="dashicons dashicons-email"></span>
        </a>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get HTML for poll rating
 */
function pollify_get_rating_html($poll_id) {
    $ratings = pollify_get_poll_ratings($poll_id);
    $user_ip = pollify_get_user_ip();
    $user_id = get_current_user_id();
    
    ob_start();
    ?>
    <div class="pollify-poll-rating" data-poll-id="<?php echo $poll_id; ?>">
        <div class="pollify-rating-question">
            <?php _e('Was this poll helpful?', 'pollify'); ?>
        </div>
        
        <div class="pollify-rating-buttons">
            <button type="button" class="pollify-rating-button pollify-rating-up" data-rating="1" aria-label="<?php _e('Thumbs up', 'pollify'); ?>">
                <span class="dashicons dashicons-thumbs-up"></span>
                <span class="pollify-rating-count"><?php echo (int) $ratings['upvotes']; ?></span>
            </button>
            
            <button type="button" class="pollify-rating-button pollify-rating-down" data-rating="0" aria-label="<?php _e('Thumbs down', 'pollify'); ?>">
                <span class="dashicons dashicons-thumbs-down"></span>
                <span class="pollify-rating-count"><?php echo (int) $ratings['downvotes']; ?></span>
            </button>
        </div>
        
        <div class="pollify-rating-message" style="display: none;"></div>
    </div>
    <?php
    return ob_get_clean();
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

/**
 * Get HTML for poll results
 */
function pollify_get_results_html($poll_id, $options, $vote_counts, $total_votes, $display_type = 'bar', $user_vote = null) {
    ob_start();
    
    $selected_option = $user_vote ? $user_vote->option_id : null;
    
    if ($display_type === 'pie' || $display_type === 'donut') {
        // Prepare data for pie/donut chart
        $chart_data = array(
            'labels' => array(),
            'data' => array(),
            'colors' => array()
        );
        
        $color_palette = array(
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#6f42c1', '#5a5c69', '#858796', '#2e59d9', '#17a673'
        );
        
        $i = 0;
        foreach ($options as $option_id => $option_text) {
            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
            
            $chart_data['labels'][] = $option_text;
            $chart_data['data'][] = $percentage;
            $chart_data['colors'][] = $color_palette[$i % count($color_palette)];
            
            $i++;
        }
        
        $chart_id = 'pollify-chart-' . $poll_id;
        $chart_type = $display_type === 'pie' ? 'pie' : 'doughnut';
        ?>
        <div class="pollify-results-chart-container">
            <canvas id="<?php echo $chart_id; ?>"></canvas>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: '<?php echo $chart_type; ?>',
                    data: {
                        labels: <?php echo json_encode($chart_data['labels']); ?>,
                        datasets: [{
                            data: <?php echo json_encode($chart_data['data']); ?>,
                            backgroundColor: <?php echo json_encode($chart_data['colors']); ?>,
                            borderColor: '#ffffff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var label = data.labels[tooltipItem.index] || '';
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    return label + ': ' + value + '%';
                                }
                            }
                        }
                    }
                });
            });
            </script>
        </div>
        <?php
    } else {
        // Bar chart or text display
        ?>
        <div class="pollify-results-list">
            <?php foreach ($options as $option_id => $option_text) : 
                $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
                $is_selected = $selected_option === $option_id;
            ?>
            <div class="pollify-result-item<?php echo $is_selected ? ' pollify-voted' : ''; ?>">
                <div class="pollify-result-text">
                    <?php echo esc_html($option_text); ?>
                    <?php if ($is_selected) : ?>
                    <span class="pollify-your-vote"><?php _e('Your vote', 'pollify'); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="pollify-result-data">
                    <div class="pollify-result-count">
                        <?php echo number_format_i18n($vote_count); ?> 
                        <span class="pollify-vote-text">
                            <?php echo _n('vote', 'votes', $vote_count, 'pollify'); ?>
                        </span>
                    </div>
                    
                    <div class="pollify-result-percentage">
                        <?php echo $percentage; ?>%
                    </div>
                </div>
                
                <?php if ($display_type !== 'text') : ?>
                <div class="pollify-result-bar-container">
                    <div class="pollify-result-bar<?php echo $is_selected ? ' pollify-voted-bar' : ''; ?>" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            
            <div class="pollify-results-total">
                <?php 
                printf(
                    _n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'),
                    number_format_i18n($total_votes)
                ); 
                ?>
            </div>
        </div>
        <?php
    }
    
    return ob_get_clean();
}
