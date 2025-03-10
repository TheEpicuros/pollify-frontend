
<?php
/**
 * Ranked-choice poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle rendering of ranked-choice poll results
 */
class Pollify_RankedChoice_Renderer {
    
    /**
     * Render ranked-choice poll results
     *
     * @param int $poll_id The poll ID
     * @param array $options The poll options
     * @param array $vote_counts The vote counts
     * @return string The HTML for the ranked-choice poll results
     */
    public static function render_results($poll_id, $options, $vote_counts) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pollify_votes';
        
        // For ranked-choice polls, we need to aggregate the rankings
        $rankings = array();
        $total_votes = 0;
        
        // Initialize rankings array
        foreach ($options as $option_id => $option_text) {
            $rankings[$option_id] = array(
                'text' => $option_text,
                'total_points' => 0,
                'first_choice_count' => 0,
                'positions' => array()
            );
            
            // Initialize positions array (1st place, 2nd place, etc.)
            for ($i = 1; $i <= count($options); $i++) {
                $rankings[$option_id]['positions'][$i] = 0;
            }
        }
        
        // Get all ranked votes from the database
        $votes = $wpdb->get_results($wpdb->prepare(
            "SELECT ranked_options, user_id 
            FROM $table_name 
            WHERE poll_id = %d",
            $poll_id
        ));
        
        // Process each vote
        foreach ($votes as $vote) {
            $total_votes++;
            $ranked_options = maybe_unserialize($vote->ranked_options);
            
            if (is_array($ranked_options)) {
                // Calculate points for each option (reverse of position - higher for better rank)
                $max_points = count($options);
                
                foreach ($ranked_options as $position => $option_id) {
                    if (isset($rankings[$option_id])) {
                        // Position is 0-indexed in the array, but we want it 1-indexed for display
                        $display_position = $position + 1;
                        
                        // Add points (reverse scoring - first place gets max points)
                        $points = $max_points - $position;
                        $rankings[$option_id]['total_points'] += $points;
                        
                        // Increment the position counter
                        $rankings[$option_id]['positions'][$display_position]++;
                        
                        // Count first-choice votes
                        if ($position === 0) {
                            $rankings[$option_id]['first_choice_count']++;
                        }
                    }
                }
            }
        }
        
        // Sort rankings by total points
        uasort($rankings, function($a, $b) {
            return $b['total_points'] <=> $a['total_points'];
        });
        
        ob_start();
        ?>
        <div class="pollify-ranked-choice-results">
            <h3 class="pollify-results-title"><?php _e('Results', 'pollify'); ?> (<?php echo $total_votes; ?> <?php echo _n('vote', 'votes', $total_votes, 'pollify'); ?>)</h3>
            
            <?php if ($total_votes === 0) : ?>
                <p class="pollify-no-votes"><?php _e('No votes yet. Be the first to vote!', 'pollify'); ?></p>
            <?php else : ?>
                <div class="pollify-rankings">
                    <div class="pollify-rankings-header">
                        <div class="pollify-rank"><?php _e('Rank', 'pollify'); ?></div>
                        <div class="pollify-option"><?php _e('Option', 'pollify'); ?></div>
                        <div class="pollify-points"><?php _e('Points', 'pollify'); ?></div>
                        <div class="pollify-first-choice"><?php _e('1st Choice', 'pollify'); ?></div>
                    </div>
                    
                    <?php 
                    $rank = 1;
                    foreach ($rankings as $option_id => $ranking) : 
                        $first_choice_percent = $total_votes > 0 ? round(($ranking['first_choice_count'] / $total_votes) * 100) : 0;
                    ?>
                    <div class="pollify-ranking-item">
                        <div class="pollify-rank"><?php echo $rank; ?></div>
                        <div class="pollify-option"><?php echo esc_html($ranking['text']); ?></div>
                        <div class="pollify-points"><?php echo $ranking['total_points']; ?></div>
                        <div class="pollify-first-choice">
                            <?php echo $ranking['first_choice_count']; ?> 
                            <span class="pollify-percent">(<?php echo $first_choice_percent; ?>%)</span>
                        </div>
                    </div>
                    <?php 
                        $rank++;
                    endforeach; 
                    ?>
                </div>
                
                <div class="pollify-position-breakdown">
                    <h4><?php _e('Position Breakdown', 'pollify'); ?></h4>
                    <table class="pollify-position-table">
                        <thead>
                            <tr>
                                <th><?php _e('Option', 'pollify'); ?></th>
                                <?php for ($i = 1; $i <= count($options); $i++) : ?>
                                <th><?php echo $i; ?><?php echo self::get_ordinal_suffix($i); ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankings as $option_id => $ranking) : ?>
                            <tr>
                                <td><?php echo esc_html($ranking['text']); ?></td>
                                <?php for ($i = 1; $i <= count($options); $i++) : ?>
                                <td><?php echo $ranking['positions'][$i]; ?></td>
                                <?php endfor; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get ordinal suffix for a number (1st, 2nd, 3rd, etc.)
     *
     * @param int $n The number
     * @return string The ordinal suffix
     */
    private static function get_ordinal_suffix($n) {
        if ($n % 100 >= 11 && $n % 100 <= 13) {
            return 'th';
        }
        
        switch ($n % 10) {
            case 1:  return 'st';
            case 2:  return 'nd';
            case 3:  return 'rd';
            default: return 'th';
        }
    }
}
