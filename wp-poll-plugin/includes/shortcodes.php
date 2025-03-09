
<?php
/**
 * Shortcodes for the Pollify plugin
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
    ), $atts, 'pollify');
    
    $poll_id = absint($atts['id']);
    
    if (!$poll_id) {
        return '<div class="pollify-error">Poll ID is required.</div>';
    }
    
    $poll = get_post($poll_id);
    
    if (!$poll || $poll->post_type !== 'poll') {
        return '<div class="pollify-error">Poll not found.</div>';
    }
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || count($options) < 2) {
        return '<div class="pollify-error">This poll has no options.</div>';
    }
    
    // Get vote counts
    $vote_counts = pollify_get_vote_counts($poll_id);
    
    // Check if user has already voted
    $user_ip = pollify_get_user_ip();
    $has_voted = pollify_has_user_voted($poll_id, $user_ip);
    
    ob_start();
    ?>
    <div id="pollify-poll-<?php echo $poll_id; ?>" class="pollify-poll" data-poll-id="<?php echo $poll_id; ?>">
        <div class="pollify-poll-container">
            <h3 class="pollify-poll-title"><?php echo esc_html($poll->post_title); ?></h3>
            
            <?php if ($poll->post_content) : ?>
            <div class="pollify-poll-description">
                <?php echo wpautop($poll->post_content); ?>
            </div>
            <?php endif; ?>
            
            <div class="pollify-poll-options">
                <?php if ($has_voted) : ?>
                    <!-- Results view -->
                    <div class="pollify-poll-results">
                        <?php 
                        $total_votes = 0;
                        foreach ($options as $option_id => $option_text) {
                            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                            $total_votes += $vote_count;
                        }
                        
                        foreach ($options as $option_id => $option_text) :
                            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                            $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
                        ?>
                        <div class="pollify-poll-result">
                            <div class="pollify-poll-option-text">
                                <?php echo esc_html($option_text); ?>
                                <span class="pollify-poll-option-count">
                                    <?php echo $vote_count; ?> votes (<?php echo $percentage; ?>%)
                                </span>
                            </div>
                            <div class="pollify-poll-option-bar">
                                <div class="pollify-poll-option-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="pollify-poll-total">
                            Total votes: <?php echo $total_votes; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Voting form -->
                    <form class="pollify-poll-form" data-poll-id="<?php echo $poll_id; ?>">
                        <?php foreach ($options as $option_id => $option_text) : ?>
                        <div class="pollify-poll-option">
                            <label>
                                <input type="radio" name="poll_option" value="<?php echo $option_id; ?>">
                                <?php echo esc_html($option_text); ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="pollify-poll-submit">
                            <button type="submit" class="pollify-poll-vote-button">Vote</button>
                        </div>
                        
                        <div class="pollify-poll-message" style="display: none;"></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pollify', 'pollify_poll_shortcode');

/**
 * Poll create shortcode [pollify_create]
 */
function pollify_create_shortcode($atts) {
    // Only logged in users can create polls
    if (!is_user_logged_in()) {
        return '<div class="pollify-error">You must be logged in to create a poll.</div>';
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        return '<div class="pollify-error">You do not have permission to create polls.</div>';
    }
    
    ob_start();
    ?>
    <div id="pollify-create-poll" class="pollify-create-poll">
        <div class="pollify-create-poll-container">
            <h2>Create a New Poll</h2>
            
            <form id="pollify-create-poll-form" class="pollify-create-poll-form">
                <div class="pollify-form-group">
                    <label for="poll-title">Poll Question</label>
                    <input type="text" id="poll-title" name="poll_title" required>
                </div>
                
                <div class="pollify-form-group">
                    <label for="poll-description">Description (optional)</label>
                    <textarea id="poll-description" name="poll_description"></textarea>
                </div>
                
                <div class="pollify-form-group">
                    <label>Options</label>
                    <div id="poll-options-container">
                        <div class="pollify-poll-option-input">
                            <input type="text" name="poll_options[]" required>
                        </div>
                        <div class="pollify-poll-option-input">
                            <input type="text" name="poll_options[]" required>
                        </div>
                    </div>
                    <button type="button" id="add-poll-option-btn">Add Option</button>
                </div>
                
                <div class="pollify-form-submit">
                    <button type="submit">Create Poll</button>
                </div>
                
                <div class="pollify-create-poll-message" style="display: none;"></div>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pollify_create', 'pollify_create_shortcode');

/**
 * Poll browse shortcode [pollify_browse]
 */
function pollify_browse_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 10,
    ), $atts, 'pollify_browse');
    
    $limit = absint($atts['limit']);
    
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $polls = get_posts($args);
    
    if (empty($polls)) {
        return '<div class="pollify-info">No polls found.</div>';
    }
    
    ob_start();
    ?>
    <div class="pollify-polls-list">
        <?php foreach ($polls as $poll) : 
            // Get vote counts
            $vote_counts = pollify_get_vote_counts($poll->ID);
            $total_votes = array_sum($vote_counts);
        ?>
        <div class="pollify-poll-card">
            <h3 class="pollify-poll-title">
                <a href="<?php echo get_permalink($poll->ID); ?>"><?php echo esc_html($poll->post_title); ?></a>
            </h3>
            
            <?php if ($poll->post_excerpt) : ?>
            <div class="pollify-poll-excerpt">
                <?php echo wp_trim_words($poll->post_content, 20); ?>
            </div>
            <?php endif; ?>
            
            <div class="pollify-poll-meta">
                <span class="pollify-poll-votes"><?php echo $total_votes; ?> votes</span>
                <span class="pollify-poll-date"><?php echo get_the_date('', $poll->ID); ?></span>
            </div>
            
            <div class="pollify-poll-actions">
                <a href="<?php echo get_permalink($poll->ID); ?>" class="pollify-poll-link">View Poll</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pollify_browse', 'pollify_browse_shortcode');

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
