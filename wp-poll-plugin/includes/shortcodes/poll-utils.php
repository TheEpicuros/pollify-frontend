
<?php
/**
 * Utility functions for poll shortcodes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get user IP address
 */
function pollify_get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return sanitize_text_field($ip);
}

/**
 * Generate poll results HTML based on display type
 */
function pollify_get_results_html($poll_id, $options, $vote_counts, $total_votes, $display_type = 'bar', $user_vote = null) {
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
    $poll_type = pollify_get_poll_type($poll_id);
    $option_images = array();
    if ($poll_type === 'image-based') {
        $option_images = get_post_meta($poll_id, '_poll_option_images', true);
    }
    
    // Determine chart colors
    $colors = array('#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6', '#f43f5e', '#84cc16');
    
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
                <ul class="pollify-poll-text-results">
                    <?php foreach ($sorted_option_ids as $option_id) : 
                        if (isset($options[$option_id])) :
                            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                            $percentage = $percentages[$option_id];
                            $is_user_vote = ($user_vote && $user_vote->option_id == $option_id);
                    ?>
                    <li class="pollify-poll-text-result <?php echo $is_user_vote ? 'pollify-user-vote' : ''; ?>">
                        <?php echo esc_html($options[$option_id]); ?>: 
                        <strong><?php echo $vote_count; ?></strong> 
                        (<?php echo $percentage; ?>%)
                        <?php if ($is_user_vote) : ?>
                        <span class="pollify-your-vote"><?php _e('Your vote', 'pollify'); ?></span>
                        <?php endif; ?>
                    </li>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </ul>
                
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
                <?php foreach ($sorted_option_ids as $option_id) : 
                    if (isset($options[$option_id])) :
                        $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                        $percentage = $percentages[$option_id];
                        $is_user_vote = ($user_vote && $user_vote->option_id == $option_id);
                ?>
                <div class="pollify-poll-result <?php echo $is_user_vote ? 'pollify-user-vote' : ''; ?>">
                    <div class="pollify-poll-option-text">
                        <?php if ($poll_type === 'image-based' && !empty($option_images[$option_id])) : 
                            $image_url = wp_get_attachment_image_url($option_images[$option_id], 'thumbnail');
                            if ($image_url) :
                        ?>
                        <div class="pollify-poll-option-image">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($options[$option_id]); ?>">
                        </div>
                        <?php endif; endif; ?>
                        
                        <span class="pollify-poll-option-label">
                            <?php echo esc_html($options[$option_id]); ?>
                        </span>
                        
                        <span class="pollify-poll-option-count">
                            <?php echo $vote_count; ?> <?php echo _n('vote', 'votes', $vote_count, 'pollify'); ?> (<?php echo $percentage; ?>%)
                            
                            <?php if ($is_user_vote) : ?>
                            <span class="pollify-your-vote"><?php _e('Your vote', 'pollify'); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="pollify-poll-option-bar">
                        <div class="pollify-poll-option-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
                
                <div class="pollify-poll-total">
                    <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
                </div>
            </div>
            <?php
            break;
    }
    
    return ob_get_clean();
}

/**
 * Generate social sharing buttons HTML
 */
function pollify_get_social_sharing_html($poll_id) {
    $post = get_post($poll_id);
    $permalink = get_permalink($poll_id);
    $title = get_the_title($poll_id);
    $excerpt = has_excerpt($poll_id) ? get_the_excerpt($poll_id) : wp_trim_words($post->post_content, 20);
    
    // Get featured image
    $thumbnail_url = get_the_post_thumbnail_url($poll_id, 'large');
    
    ob_start();
    ?>
    <div class="pollify-social-sharing">
        <h4><?php _e('Share this poll:', 'pollify'); ?></h4>
        
        <div class="pollify-social-buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" class="pollify-social-button pollify-facebook" target="_blank" rel="noopener">
                <span class="dashicons dashicons-facebook"></span>
                <span class="pollify-social-text"><?php _e('Facebook', 'pollify'); ?></span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" class="pollify-social-button pollify-twitter" target="_blank" rel="noopener">
                <span class="dashicons dashicons-twitter"></span>
                <span class="pollify-social-text"><?php _e('Twitter', 'pollify'); ?></span>
            </a>
            
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($permalink); ?>&title=<?php echo urlencode($title); ?>&summary=<?php echo urlencode($excerpt); ?>" class="pollify-social-button pollify-linkedin" target="_blank" rel="noopener">
                <span class="dashicons dashicons-linkedin"></span>
                <span class="pollify-social-text"><?php _e('LinkedIn', 'pollify'); ?></span>
            </a>
            
            <a href="mailto:?subject=<?php echo urlencode($title); ?>&body=<?php echo urlencode($excerpt . "\n\n" . $permalink); ?>" class="pollify-social-button pollify-email">
                <span class="dashicons dashicons-email"></span>
                <span class="pollify-social-text"><?php _e('Email', 'pollify'); ?></span>
            </a>
        </div>
    </div>
    
    <!-- Open Graph Meta Tags for better social sharing -->
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($excerpt); ?>">
    <meta property="og:url" content="<?php echo esc_url($permalink); ?>">
    <meta property="og:type" content="article">
    <?php if ($thumbnail_url) : ?>
    <meta property="og:image" content="<?php echo esc_url($thumbnail_url); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($excerpt); ?>">
    <?php if ($thumbnail_url) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($thumbnail_url); ?>">
    <?php endif; ?>
    <?php
    
    return ob_get_clean();
}

/**
 * Get rating UI and data for a poll
 */
function pollify_get_rating_html($poll_id) {
    $ratings = pollify_get_poll_ratings($poll_id);
    $upvotes = $ratings['upvotes'];
    $downvotes = $ratings['downvotes'];
    
    // Check if user has already rated
    $user_id = get_current_user_id();
    $user_ip = pollify_get_user_ip();
    
    $user_has_rated = false;
    $user_rating = null;
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    $existing_rating = $wpdb->get_var($wpdb->prepare(
        "SELECT rating FROM $table_name WHERE poll_id = %d AND (user_ip = %s" . ($user_id ? " OR user_id = %d" : "") . ")",
        array_merge(array($poll_id, $user_ip), $user_id ? array($user_id) : array())
    ));
    
    if ($existing_rating !== null) {
        $user_has_rated = true;
        $user_rating = (int) $existing_rating;
    }
    
    ob_start();
    ?>
    <div class="pollify-poll-rating" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <h4><?php _e('Rate this poll:', 'pollify'); ?></h4>
        
        <div class="pollify-rating-buttons">
            <button 
                type="button" 
                class="pollify-rating-up <?php echo $user_rating === 1 ? 'pollify-user-rated' : ''; ?>" 
                data-rating="1" 
                data-nonce="<?php echo wp_create_nonce('pollify-rate-poll'); ?>"
                <?php echo $user_has_rated && $user_rating !== 1 ? 'disabled' : ''; ?>
            >
                <span class="dashicons dashicons-thumbs-up"></span>
                <span class="pollify-rating-count"><?php echo number_format_i18n($upvotes); ?></span>
            </button>
            
            <button 
                type="button" 
                class="pollify-rating-down <?php echo $user_rating === 0 ? 'pollify-user-rated' : ''; ?>" 
                data-rating="0" 
                data-nonce="<?php echo wp_create_nonce('pollify-rate-poll'); ?>"
                <?php echo $user_has_rated && $user_rating !== 0 ? 'disabled' : ''; ?>
            >
                <span class="dashicons dashicons-thumbs-down"></span>
                <span class="pollify-rating-count"><?php echo number_format_i18n($downvotes); ?></span>
            </button>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Get comments HTML for a poll
 */
function pollify_get_comments_html($poll_id) {
    $comments = pollify_get_poll_comments($poll_id, 10);
    $allow_comments = get_post_meta($poll_id, '_poll_allow_comments', true) === '1';
    
    ob_start();
    ?>
    <div class="pollify-poll-comments" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <h4><?php _e('Comments', 'pollify'); ?></h4>
        
        <?php if ($allow_comments && is_user_logged_in()) : ?>
            <div class="pollify-comment-form">
                <form class="pollify-add-comment-form" data-poll-id="<?php echo esc_attr($poll_id); ?>">
                    <div class="pollify-comment-input">
                        <textarea 
                            name="comment_text" 
                            placeholder="<?php esc_attr_e('Add your comment...', 'pollify'); ?>" 
                            rows="3" 
                            required 
                        ></textarea>
                    </div>
                    <div class="pollify-comment-submit">
                        <button 
                            type="submit" 
                            class="pollify-submit-comment" 
                            data-nonce="<?php echo wp_create_nonce('pollify-add-comment'); ?>"
                        >
                            <?php _e('Post Comment', 'pollify'); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif (!$allow_comments) : ?>
            <p class="pollify-comments-disabled"><?php _e('Comments are disabled for this poll.', 'pollify'); ?></p>
        <?php else : ?>
            <p class="pollify-login-to-comment"><?php _e('Please log in to comment on this poll.', 'pollify'); ?></p>
        <?php endif; ?>
        
        <div class="pollify-comments-list">
            <?php if (empty($comments)) : ?>
                <p class="pollify-no-comments"><?php _e('No comments yet. Be the first to comment!', 'pollify'); ?></p>
            <?php else : ?>
                <?php foreach ($comments as $comment) : ?>
                    <div class="pollify-comment">
                        <div class="pollify-comment-header">
                            <span class="pollify-comment-author"><?php echo esc_html($comment->user_name); ?></span>
                            <span class="pollify-comment-date"><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($comment->comment_date)); ?></span>
                        </div>
                        <div class="pollify-comment-body">
                            <?php echo wpautop(esc_html($comment->comment_text)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (count($comments) >= 10) : ?>
                    <div class="pollify-load-more-comments">
                        <button 
                            type="button" 
                            class="pollify-load-more" 
                            data-poll-id="<?php echo esc_attr($poll_id); ?>" 
                            data-offset="10" 
                            data-nonce="<?php echo wp_create_nonce('pollify-load-comments'); ?>"
                        >
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
 * Check if a poll is valid for voting
 */
function pollify_validate_poll($poll_id) {
    // Check if poll exists and is published
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll' || $poll->post_status !== 'publish') {
        return array(
            'valid' => false,
            'message' => __('Poll not found or not published.', 'pollify')
        );
    }
    
    // Check if poll has ended
    if (pollify_has_poll_ended($poll_id)) {
        return array(
            'valid' => false,
            'message' => __('This poll has ended.', 'pollify')
        );
    }
    
    // Check user permissions
    if (!pollify_can_user_vote($poll_id)) {
        return array(
            'valid' => false,
            'message' => __('You do not have permission to vote on this poll.', 'pollify')
        );
    }
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    $user_id = get_current_user_id();
    
    if (pollify_has_user_voted($poll_id, $user_ip, $user_id)) {
        return array(
            'valid' => false,
            'message' => __('You have already voted on this poll.', 'pollify')
        );
    }
    
    return array(
        'valid' => true,
        'message' => ''
    );
}

/**
 * Get poll type display name
 */
function pollify_get_poll_type_name($poll_id) {
    $poll_type = pollify_get_poll_type($poll_id);
    $term = get_term_by('slug', $poll_type, 'poll_type');
    
    return $term ? $term->name : __('Standard Poll', 'pollify');
}

/**
 * Format date for display
 */
function pollify_format_date($date_string) {
    if (empty($date_string)) {
        return '';
    }
    
    $date = date_create($date_string);
    return date_format($date, get_option('date_format') . ' ' . get_option('time_format'));
}

/**
 * Get poll status (active, ended, scheduled)
 */
function pollify_get_poll_status($poll_id) {
    $post = get_post($poll_id);
    
    if ($post->post_status === 'future') {
        return 'scheduled';
    }
    
    if (pollify_has_poll_ended($poll_id)) {
        return 'ended';
    }
    
    return 'active';
}
