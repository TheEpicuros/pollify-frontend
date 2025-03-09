
<?php
/**
 * Poll display shortcode [pollify id="123"]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Poll shortcode [pollify id="123"]
 */
function pollify_poll_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'show_results' => null,
        'show_social' => 'yes',
        'show_ratings' => 'yes',
        'show_comments' => null,
        'display' => null, // bar, pie, donut, text
        'width' => '',
        'align' => 'center', // left, center, right
    ), $atts, 'pollify');
    
    $poll_id = absint($atts['id']);
    
    if (!$poll_id) {
        return '<div class="pollify-error">' . __('Poll ID is required.', 'pollify') . '</div>';
    }
    
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return '<div class="pollify-error">' . __('Poll not found.', 'pollify') . '</div>';
    }
    
    // Check if poll is published
    if ($poll->post_status !== 'publish' && !current_user_can('edit_post', $poll_id)) {
        return '<div class="pollify-error">' . __('This poll is not published.', 'pollify') . '</div>';
    }
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || count($options) < 2) {
        return '<div class="pollify-error">' . __('This poll has no options.', 'pollify') . '</div>';
    }
    
    // Get poll settings
    $poll_type = pollify_get_poll_type($poll_id);
    $poll_end_date = get_post_meta($poll_id, '_poll_end_date', true);
    $always_show_results = get_post_meta($poll_id, '_poll_show_results', true) === '1';
    $results_display = get_post_meta($poll_id, '_poll_results_display', true) ?: 'bar';
    $allow_comments = get_post_meta($poll_id, '_poll_allow_comments', true) === '1';
    
    // Override settings from shortcode attributes if provided
    $show_results = $atts['show_results'] !== null ? ($atts['show_results'] === 'yes') : $always_show_results;
    $show_social = $atts['show_social'] === 'yes';
    $show_ratings = $atts['show_ratings'] === 'yes';
    $show_comments = $atts['show_comments'] !== null ? ($atts['show_comments'] === 'yes') : $allow_comments;
    
    if ($atts['display']) {
        $results_display = $atts['display'];
    }
    
    // Get vote counts
    $vote_counts = pollify_get_vote_counts($poll_id);
    $total_votes = array_sum($vote_counts);
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    $user_id = get_current_user_id();
    $has_voted = pollify_has_user_voted($poll_id, $user_ip, $user_id);
    $user_vote = $has_voted ? pollify_get_user_vote($poll_id, $user_ip, $user_id) : null;
    
    // Check if poll has ended
    $has_ended = pollify_has_poll_ended($poll_id);
    
    // Determine if we should show results
    $display_results = $show_results || $has_voted || $has_ended;
    
    // Set width style if provided
    $width_style = '';
    if (!empty($atts['width'])) {
        $width_style = ' style="width:' . esc_attr($atts['width']) . ';"';
    }
    
    // Set alignment class
    $align_class = ' pollify-align-' . esc_attr($atts['align']);
    
    ob_start();
    
    // Load Chart.js for pie/donut charts if needed
    if (($display_results && ($results_display === 'pie' || $results_display === 'donut'))) {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.8.0', true);
    }
    ?>
    <div id="pollify-poll-<?php echo $poll_id; ?>" class="pollify-poll pollify-poll-type-<?php echo esc_attr($poll_type); ?><?php echo $align_class; ?>"<?php echo $width_style; ?> data-poll-id="<?php echo $poll_id; ?>">
        <div class="pollify-poll-container">
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
            
            <?php if ($poll->post_content) : ?>
            <div class="pollify-poll-description">
                <?php echo wpautop($poll->post_content); ?>
            </div>
            <?php endif; ?>
            
            <div class="pollify-poll-options">
                <?php if ($display_results) : ?>
                    <!-- Results view -->
                    <?php echo pollify_get_results_html($poll_id, $options, $vote_counts, $total_votes, $results_display, $user_vote); ?>
                    
                    <?php if ($user_vote) : ?>
                    <div class="pollify-user-vote-info">
                        <?php 
                            printf(
                                __('You voted on %s', 'pollify'), 
                                pollify_format_date($user_vote->voted_at)
                            ); 
                        ?>
                    </div>
                    <?php endif; ?>
                <?php else : ?>
                    <!-- Voting form -->
                    <form class="pollify-poll-form" data-poll-id="<?php echo $poll_id; ?>">
                        <?php 
                            // Check if this is an image-based poll
                            $option_images = array();
                            if ($poll_type === 'image-based') {
                                $option_images = get_post_meta($poll_id, '_poll_option_images', true);
                            }
                            
                            // Different input types based on poll type
                            $input_type = 'radio';
                            if ($poll_type === 'check-all') {
                                $input_type = 'checkbox';
                            }
                        ?>
                        
                        <?php if ($poll_type === 'ranked-choice'): ?>
                        <div class="pollify-ranked-choices">
                            <p class="pollify-instruction"><?php _e('Drag to rank your choices in order of preference:', 'pollify'); ?></p>
                            <ul class="pollify-sortable-options">
                                <?php foreach ($options as $option_id => $option_text) : ?>
                                <li class="pollify-sortable-option" data-option-id="<?php echo esc_attr($option_id); ?>">
                                    <span class="pollify-drag-handle dashicons dashicons-menu"></span>
                                    <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <input type="hidden" name="ranked_options" value="">
                        </div>
                        
                        <?php elseif ($poll_type === 'rating-scale'): ?>
                        <div class="pollify-rating-scale">
                            <div class="pollify-rating-question"><?php echo esc_html($poll->post_title); ?></div>
                            <div class="pollify-rating-options">
                                <?php 
                                // Rating scale typically from 1-5 or 1-10
                                $scale_max = count($options);
                                for ($i = 1; $i <= $scale_max; $i++) : 
                                    $option_id = array_keys($options)[$i-1];
                                ?>
                                <label class="pollify-rating-option">
                                    <input type="radio" name="poll_option" value="<?php echo esc_attr($option_id); ?>">
                                    <span class="pollify-rating-value"><?php echo $i; ?></span>
                                    <span class="pollify-rating-label"><?php echo esc_html($options[$option_id]); ?></span>
                                </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <div class="pollify-poll-options-list <?php echo $poll_type === 'image-based' ? 'pollify-image-options' : ''; ?>">
                            <?php foreach ($options as $option_id => $option_text) : ?>
                            <div class="pollify-poll-option">
                                <label>
                                    <input type="<?php echo $input_type; ?>" name="poll_option<?php echo $input_type === 'checkbox' ? '[]' : ''; ?>" value="<?php echo esc_attr($option_id); ?>">
                                    
                                    <?php if ($poll_type === 'image-based' && !empty($option_images[$option_id])) : 
                                        $image_url = wp_get_attachment_image_url($option_images[$option_id], 'medium');
                                        if ($image_url) :
                                    ?>
                                    <div class="pollify-option-image">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($option_text); ?>">
                                    </div>
                                    <?php endif; endif; ?>
                                    
                                    <span class="pollify-option-text"><?php echo esc_html($option_text); ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pollify-poll-submit">
                            <button type="submit" class="pollify-poll-vote-button">
                                <?php _e('Vote', 'pollify'); ?>
                            </button>
                            
                            <?php if ($show_results && !$has_voted): ?>
                            <button type="button" class="pollify-view-results-button">
                                <?php _e('View Results', 'pollify'); ?>
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pollify-poll-message" style="display: none;"></div>
                    </form>
                <?php endif; ?>
            </div>
            
            <?php if ($display_results && $show_social): ?>
            <div class="pollify-poll-footer">
                <?php echo pollify_get_social_sharing_html($poll_id); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($display_results && $show_ratings): ?>
            <div class="pollify-poll-rating-section">
                <?php echo pollify_get_rating_html($poll_id); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($display_results && $show_comments): ?>
            <div class="pollify-poll-comments-section">
                <?php echo pollify_get_comments_html($poll_id); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
