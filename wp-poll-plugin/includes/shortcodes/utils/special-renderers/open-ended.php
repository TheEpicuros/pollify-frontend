
<?php
/**
 * Open-ended poll type renderer
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
