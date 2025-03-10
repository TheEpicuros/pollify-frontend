
<?php
/**
 * Ranked choice poll type renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render ranked choice results
 */
function pollify_render_ranked_choice_results($poll_id, $options, $vote_counts) {
    global $wpdb;
    
    // Get ranked choices
    $table_name = $wpdb->prefix . 'pollify_ranked_votes';
    
    $rankings = $wpdb->get_results($wpdb->prepare(
        "SELECT option_id, rank, COUNT(*) as count FROM $table_name WHERE poll_id = %d GROUP BY option_id, rank ORDER BY rank",
        $poll_id
    ));
    
    // Organize data by rank
    $ranks = array();
    $max_count = 0;
    
    foreach ($rankings as $ranking) {
        if (!isset($ranks[$ranking->rank])) {
            $ranks[$ranking->rank] = array();
        }
        $ranks[$ranking->rank][$ranking->option_id] = $ranking->count;
        $max_count = max($max_count, $ranking->count);
    }
    
    ob_start();
    ?>
    <div class="pollify-ranked-choice-results">
        <h3 class="pollify-rankings-title"><?php _e('Rankings', 'pollify'); ?></h3>
        
        <?php if (empty($ranks)) : ?>
            <p class="pollify-no-rankings"><?php _e('No rankings yet.', 'pollify'); ?></p>
        <?php else : ?>
            <div class="pollify-rankings-table-container">
                <table class="pollify-rankings-table">
                    <thead>
                        <tr>
                            <th class="pollify-rank-header"><?php _e('Rank', 'pollify'); ?></th>
                            <?php foreach ($options as $option_id => $option_text) : ?>
                                <th class="pollify-option-header"><?php echo esc_html($option_text); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($rank = 1; $rank <= count($options); $rank++) : ?>
                            <tr>
                                <td class="pollify-rank-cell"><?php echo $rank; ?></td>
                                <?php foreach ($options as $option_id => $option_text) : ?>
                                    <td class="pollify-count-cell">
                                        <?php if (isset($ranks[$rank][$option_id])) : ?>
                                            <div class="pollify-count-bar-container">
                                                <div class="pollify-count-bar" style="width: <?php echo ($ranks[$rank][$option_id] / $max_count) * 100; ?>%"></div>
                                                <span class="pollify-count-value"><?php echo $ranks[$rank][$option_id]; ?></span>
                                            </div>
                                        <?php else : ?>
                                            <span class="pollify-count-zero">0</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="pollify-poll-total">
            <?php 
            $total_voters = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE poll_id = %d",
                $poll_id
            ));
            echo sprintf(_n('Total: %s voter', 'Total: %s voters', $total_voters, 'pollify'), $total_voters); 
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
