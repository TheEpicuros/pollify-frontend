
<?php
/**
 * Poll output rendering functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include component files
require_once plugin_dir_path(__FILE__) . 'header-renderer.php';
require_once plugin_dir_path(__FILE__) . 'quiz-renderer.php';
require_once plugin_dir_path(__FILE__) . 'social-sharing-renderer.php';
require_once plugin_dir_path(__FILE__) . 'rating-renderer.php';
require_once plugin_dir_path(__FILE__) . 'comments-renderer.php';
require_once plugin_dir_path(__FILE__) . 'poll-utils.php';

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
