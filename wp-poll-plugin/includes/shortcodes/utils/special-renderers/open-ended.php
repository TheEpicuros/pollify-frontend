
<?php
/**
 * Open-ended poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle rendering of open-ended poll results
 */
class Pollify_OpenEnded_Renderer {
    
    /**
     * Render open-ended poll results
     *
     * @param int $poll_id The poll ID
     * @param array $options The poll options
     * @param array $vote_counts The vote counts
     * @return string The HTML for the open-ended poll results
     */
    public static function render_open_ended_results($poll_id, $options, $vote_counts) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pollify_votes';
        
        // For open-ended polls, we need to get all the responses
        $responses = $wpdb->get_results($wpdb->prepare(
            "SELECT user_response, voted_at, user_id 
            FROM $table_name 
            WHERE poll_id = %d 
            ORDER BY voted_at DESC",
            $poll_id
        ));
        
        ob_start();
        ?>
        <div class="pollify-open-ended-results">
            <h3 class="pollify-results-title"><?php _e('Responses', 'pollify'); ?> (<?php echo count($responses); ?>)</h3>
            
            <?php if (empty($responses)) : ?>
                <p class="pollify-no-responses"><?php _e('No responses yet. Be the first to respond!', 'pollify'); ?></p>
            <?php else : ?>
                <div class="pollify-responses-list">
                    <?php foreach ($responses as $response) : 
                        $user_info = '';
                        if (!empty($response->user_id)) {
                            $user = get_userdata($response->user_id);
                            if ($user) {
                                $user_info = $user->display_name;
                            }
                        }
                    ?>
                    <div class="pollify-response-item">
                        <div class="pollify-response-content">
                            <?php echo wpautop(esc_html($response->user_response)); ?>
                        </div>
                        <div class="pollify-response-meta">
                            <?php if ($user_info) : ?>
                                <span class="pollify-response-user"><?php echo esc_html($user_info); ?></span>
                            <?php endif; ?>
                            <span class="pollify-response-date">
                                <?php echo human_time_diff(strtotime($response->voted_at), current_time('timestamp')); ?> <?php _e('ago', 'pollify'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Alias for backward compatibility - using a different method name
    public static function render_results() {
        // Get arguments
        $args = func_get_args();
        return call_user_func_array([self::class, 'render_open_ended_results'], $args);
    }
}
