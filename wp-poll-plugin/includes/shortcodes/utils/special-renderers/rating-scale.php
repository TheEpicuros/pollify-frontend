
<?php
/**
 * Rating scale poll specialized renderer
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
 * Rating scale renderer class
 */
class Pollify_RatingScale_Renderer {
    /**
     * Render rating scale poll results
     */
    public static function render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes) {
        // Get ratings data
        global $wpdb;
        $votes_table = $wpdb->prefix . 'pollify_votes';
        
        // Get scale settings
        $min_value = get_post_meta($poll_id, '_poll_rating_min', true) ?: 1;
        $max_value = get_post_meta($poll_id, '_poll_rating_max', true) ?: 5;
        $step = get_post_meta($poll_id, '_poll_rating_step', true) ?: 1;
        
        // Calculate overall average rating
        $avg_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(vote_meta) FROM $votes_table WHERE poll_id = %d AND vote_meta IS NOT NULL",
            $poll_id
        ));
        $avg_rating = $avg_rating ? round(floatval($avg_rating), 2) : 0;
        
        // Get distribution of ratings
        $ratings_dist = array();
        for ($i = $min_value; $i <= $max_value; $i += $step) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $votes_table 
                WHERE poll_id = %d AND vote_meta = %f",
                $poll_id, $i
            ));
            $ratings_dist[$i] = (int) $count;
        }
        
        ob_start();
        ?>
        <div class="pollify-rating-scale-results">
            <div class="pollify-rating-scale-header">
                <?php _e('Rating Scale Results', 'pollify'); ?>
            </div>
            
            <div class="pollify-rating-scale-average">
                <div class="pollify-rating-scale-average-label">
                    <?php _e('Average Rating:', 'pollify'); ?>
                </div>
                <div class="pollify-rating-scale-average-value">
                    <?php echo number_format($avg_rating, 2); ?>
                </div>
                <div class="pollify-rating-scale-average-outof">
                    <?php printf(__('out of %s', 'pollify'), $max_value); ?>
                </div>
            </div>
            
            <div class="pollify-rating-scale-distribution">
                <div class="pollify-rating-scale-distribution-label">
                    <?php _e('Rating Distribution:', 'pollify'); ?>
                </div>
                
                <div class="pollify-rating-scale-distribution-chart">
                    <?php
                    foreach ($ratings_dist as $rating => $count) {
                        $percentage = $total_votes > 0 ? round(($count / $total_votes) * 100) : 0;
                        ?>
                        <div class="pollify-rating-scale-distribution-item">
                            <div class="pollify-rating-scale-distribution-rating">
                                <?php echo $rating; ?>
                            </div>
                            <div class="pollify-rating-scale-distribution-bar-container">
                                <div class="pollify-rating-scale-distribution-bar" style="width: <?php echo $percentage; ?>%"></div>
                                <div class="pollify-rating-scale-distribution-percentage">
                                    <?php echo $percentage; ?>%
                                </div>
                            </div>
                            <div class="pollify-rating-scale-distribution-count">
                                <?php echo number_format_i18n($count); ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

/**
 * Register canonical function for rating scale poll results rendering
 */
if (pollify_can_define_function('pollify_render_rating_scale_results')) {
    pollify_declare_function('pollify_render_rating_scale_results', function($poll_id, $options, $vote_counts, $total_votes) {
        return Pollify_RatingScale_Renderer::render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
    }, $current_file);
}
