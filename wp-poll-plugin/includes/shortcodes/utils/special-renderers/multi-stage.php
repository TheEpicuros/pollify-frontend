
<?php
/**
 * Multi-stage poll specialized renderer
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
 * Multi-stage renderer class
 */
class Pollify_MultiStage_Renderer {
    /**
     * Render multi-stage poll results
     */
    public static function render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes) {
        // Implementation for multi-stage poll results rendering
        ob_start();
        ?>
        <div class="pollify-multi-stage-results">
            <div class="pollify-multi-stage-header">
                <?php _e('Multi-Stage Poll Results', 'pollify'); ?>
            </div>
            
            <div class="pollify-multi-stage-chart">
                <?php 
                // Implementation for multi-stage results visualization
                foreach ($options as $option_id => $option_text) {
                    $votes = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                    $percentage = $total_votes > 0 ? round(($votes / $total_votes) * 100) : 0;
                    ?>
                    <div class="pollify-multi-stage-option">
                        <div class="pollify-multi-stage-label"><?php echo esc_html($option_text); ?></div>
                        <div class="pollify-multi-stage-bar-container">
                            <div class="pollify-multi-stage-bar" style="width: <?php echo $percentage; ?>%"></div>
                            <div class="pollify-multi-stage-percentage"><?php echo $percentage; ?>%</div>
                        </div>
                        <div class="pollify-multi-stage-votes">
                            <?php echo number_format_i18n($votes); ?> <?php echo _n('vote', 'votes', $votes, 'pollify'); ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Register canonical function for multi-stage poll results rendering
 */
if (pollify_can_define_function('pollify_render_multi_stage_results')) {
    pollify_declare_function('pollify_render_multi_stage_results', function($poll_id, $options, $vote_counts, $total_votes) {
        return Pollify_MultiStage_Renderer::render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes);
    }, $current_file);
}
