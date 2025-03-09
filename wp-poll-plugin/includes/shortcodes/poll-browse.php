
<?php
/**
 * Poll browse shortcode [pollify_browse]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

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

