
<?php
/**
 * Multi-stage poll type renderer
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
 * Class to handle rendering of multi-stage poll results
 */
class Pollify_MultiStage_Renderer {
    
    /**
     * Render multi-stage poll results
     *
     * @param int $poll_id The poll ID
     * @param array $options The poll options
     * @param array $vote_counts The vote counts
     * @param int $total_votes Total number of votes
     * @return string The HTML for the multi-stage poll results
     */
    public static function render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes) {
        // Get stage information from poll meta
        $stages = get_post_meta($poll_id, '_poll_stages', true);
        if (!is_array($stages) || empty($stages)) {
            $stages = array('Stage 1'); // Default if no stages are defined
        }
        
        $current_stage = get_post_meta($poll_id, '_poll_current_stage', true);
        if (empty($current_stage)) {
            $current_stage = 0; // Default to first stage
        }
        
        ob_start();
        ?>
        <div class="pollify-multi-stage-results">
            <div class="pollify-stage-indicator">
                <h3><?php _e('Current Stage:', 'pollify'); ?> <?php echo esc_html($stages[$current_stage]); ?></h3>
                <div class="pollify-stage-progress">
                    <?php foreach ($stages as $index => $stage_name): ?>
                        <span class="pollify-stage <?php echo ($index == $current_stage) ? 'active' : (($index < $current_stage) ? 'completed' : ''); ?>">
                            <?php echo esc_html($stage_name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="pollify-results-container">
                <?php if (empty($vote_counts)): ?>
                    <p class="pollify-no-votes"><?php _e('No votes have been cast for this stage yet.', 'pollify'); ?></p>
                <?php else: ?>
                    <h4><?php printf(__('Results for %s', 'pollify'), esc_html($stages[$current_stage])); ?></h4>
                    <div class="pollify-vote-results">
                        <?php foreach ($options as $key => $option): 
                            $count = isset($vote_counts[$key]) ? $vote_counts[$key] : 0;
                            $percentage = $total_votes > 0 ? round(($count / $total_votes) * 100) : 0;
                        ?>
                            <div class="pollify-result-item">
                                <div class="pollify-result-option"><?php echo esc_html($option); ?></div>
                                <div class="pollify-result-bar-container">
                                    <div class="pollify-result-bar" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                </div>
                                <div class="pollify-result-count">
                                    <?php echo esc_html($count); ?> 
                                    <span class="pollify-result-percentage">(<?php echo esc_html($percentage); ?>%)</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Register the renderer functions with the function registry
     */
    public static function register_functions() {
        $current_file = __FILE__;
        
        if (pollify_can_define_function('pollify_render_multi_stage_results')) {
            pollify_declare_function('pollify_render_multi_stage_results', function($poll_id, $options, $vote_counts, $total_votes) {
                return self::render_multi_stage_results($poll_id, $options, $vote_counts, $total_votes);
            }, $current_file);
        }
    }
}

// Register the renderer functions
Pollify_MultiStage_Renderer::register_functions();

// Do not define the pollify_render_special_multi_stage_results function directly here
// It will be defined in the special-renderers/main.php file using the registry system
