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
                    <?php echo pollify_get_results_html(
                        $poll_id, 
                        $options, 
                        $voting_status['vote_counts'], 
                        $voting_status['total_votes'], 
                        $display_settings['results_display'], 
                        $voting_status['user_vote'],
                        $poll_settings['poll_type']
                    ); ?>
                    
                    <?php echo pollify_render_user_vote_info($voting_status['user_vote']); ?>
                    
                    <?php if ($poll_settings['poll_type'] === 'quiz' && $voting_status['has_voted']) : ?>
                        <?php echo pollify_render_quiz_results($poll_id, $options, $voting_status['user_vote']); ?>
                    <?php endif; ?>
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

/**
 * Render quiz results with correct answers
 */
function pollify_render_quiz_results($poll_id, $options, $user_vote) {
    // Get correct answers for this quiz
    $correct_options = get_post_meta($poll_id, '_poll_correct_options', true);
    
    if (!is_array($correct_options) || empty($correct_options)) {
        return '';
    }
    
    $user_option_id = isset($user_vote->option_id) ? $user_vote->option_id : '';
    $is_correct = in_array($user_option_id, $correct_options);
    
    ob_start();
    ?>
    <div class="pollify-quiz-results">
        <div class="pollify-quiz-result <?php echo $is_correct ? 'pollify-quiz-correct' : 'pollify-quiz-incorrect'; ?>">
            <?php if ($is_correct) : ?>
                <div class="pollify-quiz-correct-message">
                    <span class="pollify-quiz-icon pollify-quiz-correct-icon">✓</span>
                    <p><?php _e('Correct!', 'pollify'); ?></p>
                </div>
            <?php else : ?>
                <div class="pollify-quiz-incorrect-message">
                    <span class="pollify-quiz-icon pollify-quiz-incorrect-icon">✗</span>
                    <p><?php _e('Incorrect!', 'pollify'); ?></p>
                    
                    <div class="pollify-quiz-correct-answer">
                        <p><?php _e('The correct answer is:', 'pollify'); ?></p>
                        <ul>
                        <?php 
                        foreach ($correct_options as $correct_id) {
                            if (isset($options[$correct_id])) {
                                echo '<li>' . esc_html($options[$correct_id]) . '</li>';
                            }
                        }
                        ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get social sharing HTML
 */
function pollify_get_social_sharing_html($poll_id) {
    $permalink = get_permalink($poll_id);
    $title = get_the_title($poll_id);
    
    ob_start();
    ?>
    <div class="pollify-social-sharing">
        <span class="pollify-share-text"><?php _e('Share this poll:', 'pollify'); ?></span>
        <div class="pollify-share-buttons">
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($permalink); ?>&text=<?php echo urlencode($title); ?>" target="_blank" class="pollify-share-button pollify-twitter-share">
                <span class="pollify-share-icon">X</span>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($permalink); ?>" target="_blank" class="pollify-share-button pollify-facebook-share">
                <span class="pollify-share-icon">f</span>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($permalink); ?>&title=<?php echo urlencode($title); ?>" target="_blank" class="pollify-share-button pollify-linkedin-share">
                <span class="pollify-share-icon">in</span>
            </a>
            <button class="pollify-share-button pollify-copy-link" data-poll-url="<?php echo esc_url($permalink); ?>">
                <span class="pollify-share-icon">🔗</span>
            </button>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.pollify-copy-link');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-poll-url');
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<span class="pollify-share-icon">✓</span>';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Get poll rating HTML
 */
function pollify_get_rating_html($poll_id) {
    $user_id = get_current_user_id();
    $has_rated = $user_id ? pollify_has_user_rated($poll_id, $user_id) : false;
    $rating = pollify_get_poll_rating($poll_id);
    
    ob_start();
    ?>
    <div class="pollify-poll-rating" data-poll-id="<?php echo esc_attr($poll_id); ?>">
        <div class="pollify-rating-header">
            <span class="pollify-rating-title"><?php _e('Rate this poll:', 'pollify'); ?></span>
            <?php if ($rating['count'] > 0) : ?>
            <span class="pollify-rating-average">
                <span class="pollify-rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php if ($i <= round($rating['average'])) : ?>
                            <span class="pollify-star pollify-star-filled">★</span>
                        <?php else : ?>
                            <span class="pollify-star">☆</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
                <span class="pollify-rating-text">
                    <?php printf(__('%1$s/5 (%2$s ratings)', 'pollify'), number_format($rating['average'], 1), $rating['count']); ?>
                </span>
            </span>
            <?php endif; ?>
        </div>
        
        <?php if (!$has_rated) : ?>
        <div class="pollify-rating-form">
            <div class="pollify-rating-stars-input">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                <label for="pollify-rate-<?php echo esc_attr($poll_id); ?>-<?php echo $i; ?>" class="pollify-star-label">
                    <input type="radio" name="pollify_rating" id="pollify-rate-<?php echo esc_attr($poll_id); ?>-<?php echo $i; ?>" value="<?php echo $i; ?>" 
                           class="pollify-star-input" data-poll-id="<?php echo esc_attr($poll_id); ?>">
                    <span class="pollify-star">☆</span>
                </label>
                <?php endfor; ?>
            </div>
            <button type="button" class="pollify-submit-rating" disabled><?php _e('Submit Rating', 'pollify'); ?></button>
        </div>
        <div class="pollify-rating-message" style="display: none;"></div>
        <?php else : ?>
        <div class="pollify-rating-message">
            <?php _e('You have already rated this poll.', 'pollify'); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get comments HTML
 */
function pollify_get_comments_html($poll_id) {
    ob_start();
    ?>
    <div class="pollify-comments-section">
        <h3 class="pollify-comments-title"><?php _e('Comments', 'pollify'); ?></h3>
        
        <?php if (comments_open($poll_id)) : ?>
            <?php
            // Get comments for this poll
            $comments = get_comments(array(
                'post_id' => $poll_id,
                'status' => 'approve',
                'order' => 'ASC',
            ));
            
            if (!empty($comments)) :
            ?>
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
}

/**
 * Helper function to check if user has rated a poll
 */
function pollify_has_user_rated($poll_id, $user_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    return (bool) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE poll_id = %d AND user_id = %d",
        $poll_id, $user_id
    ));
}

/**
 * Helper function to get poll rating
 */
function pollify_get_poll_rating($poll_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'pollify_ratings';
    
    $results = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) as count, AVG(rating) as average FROM $table_name WHERE poll_id = %d",
        $poll_id
    ));
    
    return array(
        'count' => $results->count ? (int) $results->count : 0,
        'average' => $results->average ? (float) $results->average : 0,
    );
}

/**
 * Format a date for display
 */
function pollify_format_date($date_string) {
    $timestamp = strtotime($date_string);
    return date_i18n(get_option('date_format'), $timestamp);
}
