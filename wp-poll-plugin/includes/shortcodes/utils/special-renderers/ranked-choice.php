
<?php
/**
 * Ranked choice poll specialized renderer
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
 * Ranked choice renderer class
 */
class Pollify_RankedChoice_Renderer {
    /**
     * Render ranked choice poll results
     */
    public static function render_ranked_choice_results($poll_id, $options, $vote_counts) {
        // Get ranked data
        global $wpdb;
        $votes_table = $wpdb->prefix . 'pollify_votes';
        
        $rankings = array();
        foreach ($options as $option_id => $option_text) {
            // Get average ranking for this option
            $avg_rank = $wpdb->get_var($wpdb->prepare(
                "SELECT AVG(vote_meta) 
                FROM $votes_table 
                WHERE poll_id = %d AND option_id = %d",
                $poll_id, $option_id
            ));
            
            $rankings[$option_id] = array(
                'text' => $option_text,
                'avg_rank' => $avg_rank ? round(floatval($avg_rank), 2) : 0,
                'votes' => isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0
            );
        }
        
        // Sort by average ranking (lower is better)
        uasort($rankings, function($a, $b) {
            if ($a['avg_rank'] == $b['avg_rank']) {
                return 0;
            }
            return ($a['avg_rank'] < $b['avg_rank']) ? -1 : 1;
        });
        
        ob_start();
        ?>
        <div class="pollify-ranked-choice-results">
            <div class="pollify-ranked-choice-header">
                <?php _e('Ranked Choice Results', 'pollify'); ?>
            </div>
            
            <div class="pollify-ranked-choice-list">
                <?php 
                $rank = 1;
                foreach ($rankings as $option_id => $data) {
                    if ($data['votes'] > 0) {
                        ?>
                        <div class="pollify-ranked-choice-item">
                            <div class="pollify-ranked-choice-rank"><?php echo $rank; ?></div>
                            <div class="pollify-ranked-choice-text"><?php echo esc_html($data['text']); ?></div>
                            <div class="pollify-ranked-choice-avg">
                                <?php 
                                printf(
                                    __('Average Rank: %s', 'pollify'),
                                    number_format($data['avg_rank'], 2)
                                ); 
                                ?>
                            </div>
                            <div class="pollify-ranked-choice-votes">
                                <?php 
                                printf(
                                    _n('%s vote', '%s votes', $data['votes'], 'pollify'),
                                    number_format_i18n($data['votes'])
                                ); 
                                ?>
                            </div>
                        </div>
                        <?php
                        $rank++;
                    }
                }
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Register canonical function for ranked choice poll results rendering
 */
if (pollify_can_define_function('pollify_render_ranked_choice_results')) {
    pollify_declare_function('pollify_render_ranked_choice_results', function($poll_id, $options, $vote_counts) {
        return Pollify_RankedChoice_Renderer::render_ranked_choice_results($poll_id, $options, $vote_counts);
    }, $current_file);
}
