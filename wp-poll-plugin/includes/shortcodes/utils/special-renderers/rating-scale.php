
<?php
/**
 * Rating-scale poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle rendering of rating-scale poll results
 */
class Pollify_RatingScale_Renderer {
    
    /**
     * Render rating-scale poll results
     *
     * @param int $poll_id The poll ID
     * @param array $options The poll options
     * @param array $vote_counts The vote counts
     * @param int $total_votes The total number of votes
     * @return string The HTML for the rating-scale poll results
     */
    public static function render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes) {
        // Calculate average rating
        $total_rating = 0;
        $total_ratings = 0;
        
        foreach ($options as $option_id => $option_text) {
            $count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $rating_value = intval($option_id); // Assuming option_id is the numeric rating value
            $total_rating += $rating_value * $count;
            $total_ratings += $count;
        }
        
        $average_rating = $total_ratings > 0 ? round($total_rating / $total_ratings, 1) : 0;
        
        // Get the distribution of ratings
        $rating_distribution = array();
        foreach ($options as $option_id => $option_text) {
            $count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $percentage = $total_votes > 0 ? round(($count / $total_votes) * 100) : 0;
            
            $rating_distribution[$option_id] = array(
                'value' => $option_text,
                'count' => $count,
                'percentage' => $percentage
            );
        }
        
        // Sort by option_id (which should be the rating value)
        ksort($rating_distribution);
        
        ob_start();
        ?>
        <div class="pollify-rating-scale-results">
            <div class="pollify-average-rating">
                <h3 class="pollify-results-title"><?php _e('Average Rating', 'pollify'); ?></h3>
                <div class="pollify-rating-value"><?php echo $average_rating; ?>/<?php echo count($options); ?></div>
                <div class="pollify-rating-stars">
                    <?php 
                    $max_rating = count($options);
                    for ($i = 1; $i <= $max_rating; $i++) {
                        $star_class = $i <= round($average_rating) ? 'pollify-star-filled' : 'pollify-star-empty';
                        echo '<span class="pollify-star ' . $star_class . '">★</span>';
                    }
                    ?>
                </div>
                <div class="pollify-total-ratings">
                    <?php printf(_n('%s rating', '%s ratings', $total_ratings, 'pollify'), number_format_i18n($total_ratings)); ?>
                </div>
            </div>
            
            <div class="pollify-rating-distribution">
                <h4><?php _e('Rating Distribution', 'pollify'); ?></h4>
                
                <?php foreach ($rating_distribution as $rating => $data) : ?>
                <div class="pollify-rating-bar">
                    <div class="pollify-rating-label"><?php echo esc_html($data['value']); ?></div>
                    <div class="pollify-rating-bar-container">
                        <div class="pollify-rating-bar-fill" style="width: <?php echo esc_attr($data['percentage']); ?>%"></div>
                    </div>
                    <div class="pollify-rating-count">
                        <?php echo $data['count']; ?> 
                        <span class="pollify-rating-percent">(<?php echo $data['percentage']; ?>%)</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Alias for backward compatibility
    public static function render_results($poll_id, $options, $vote_counts, $total_votes) {
        return self::render_rating_scale_results($poll_id, $options, $vote_counts, $total_votes);
    }
}
