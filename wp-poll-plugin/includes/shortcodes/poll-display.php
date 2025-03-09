
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

