
<?php
/**
 * Open-ended poll specialized renderer
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
 * Open-ended renderer class
 */
class Pollify_OpenEnded_Renderer {
    /**
     * Render open-ended poll results
     */
    public static function render_open_ended_results($poll_id, $options, $vote_counts) {
        // Get open-ended responses
        global $wpdb;
        $votes_table = $wpdb->prefix . 'pollify_votes';
        
        $responses = $wpdb->get_results($wpdb->prepare(
            "SELECT vote_value, COUNT(*) as response_count 
            FROM $votes_table 
            WHERE poll_id = %d AND vote_value IS NOT NULL 
            GROUP BY vote_value 
            ORDER BY response_count DESC 
            LIMIT 100",
            $poll_id
        ));
        
        ob_start();
        ?>
        <div class="pollify-open-ended-results">
            <div class="pollify-open-ended-header">
                <?php _e('Open-Ended Poll Responses', 'pollify'); ?>
            </div>
            
            <?php if (empty($responses)) : ?>
            <div class="pollify-open-ended-empty">
                <?php _e('No responses yet.', 'pollify'); ?>
            </div>
            <?php else : ?>
            <div class="pollify-open-ended-responses">
                <?php 
                foreach ($responses as $response) {
                    ?>
                    <div class="pollify-open-ended-response">
                        <div class="pollify-open-ended-text">
                            <?php echo wpautop(esc_html($response->vote_value)); ?>
                        </div>
                        <div class="pollify-open-ended-count">
                            <?php 
                            printf(
                                _n('%s person responded this way', '%s people responded this way', $response->response_count, 'pollify'),
                                number_format_i18n($response->response_count)
                            ); 
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Register canonical function for open-ended poll results rendering
 */
if (pollify_can_define_function('pollify_render_open_ended_results')) {
    pollify_declare_function('pollify_render_open_ended_results', function($poll_id, $options, $vote_counts) {
        return Pollify_OpenEnded_Renderer::render_open_ended_results($poll_id, $options, $vote_counts);
    }, $current_file);
}
