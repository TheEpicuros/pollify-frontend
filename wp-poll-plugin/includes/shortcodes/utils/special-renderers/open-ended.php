
<?php
/**
 * Open-ended poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

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
    
    // Function is now registered properly with the function registry system
    public static function register_functions() {
        $current_file = __FILE__;
        
        if (pollify_can_define_function('pollify_render_open_ended_results')) {
            pollify_declare_function('pollify_render_open_ended_results', function($poll_id, $options, $vote_counts) {
                return self::render_open_ended_results($poll_id, $options, $vote_counts);
            }, $current_file);
        }
    }
}

// Register functions with the function registry
Pollify_OpenEnded_Renderer::register_functions();
