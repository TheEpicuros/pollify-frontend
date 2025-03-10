
<?php
/**
 * Chart.js results renderer (pie/donut)
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render chart.js results (pie/donut)
 */
function pollify_render_chart_js_results($poll_id, $options, $vote_counts, $total_votes, $display_type = 'pie') {
    ob_start();

    // Chart colors
    $colors = array('#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6', '#f43f5e', '#84cc16');
    
    // Sort options by votes (descending)
    arsort($vote_counts);
    $sorted_option_ids = array_keys($vote_counts);
    
    // If no votes, display in original order
    if (empty($sorted_option_ids)) {
        $sorted_option_ids = array_keys($options);
    }
    
    // Output data for chart.js
    $chart_data = array(
        'labels' => array(),
        'datasets' => array(
            array(
                'data' => array(),
                'backgroundColor' => array(),
            )
        )
    );
    
    foreach ($sorted_option_ids as $i => $option_id) {
        if (isset($options[$option_id])) {
            $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
            $chart_data['labels'][] = $options[$option_id];
            $chart_data['datasets'][0]['data'][] = $vote_count;
            $chart_data['datasets'][0]['backgroundColor'][] = $colors[$i % count($colors)];
        }
    }
    
    $chart_id = 'pollify-chart-' . $poll_id;
    ?>
    <div class="pollify-poll-results pollify-poll-results-chart">
        <canvas id="<?php echo esc_attr($chart_id); ?>" width="400" height="300"></canvas>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('<?php echo esc_js($chart_id); ?>').getContext('2d');
            var chart = new Chart(ctx, {
                type: '<?php echo esc_js($display_type); ?>',
                data: <?php echo json_encode($chart_data); ?>,
                options: {
                    responsive: true,
                    <?php if ($display_type === 'donut') : ?>
                    cutout: '50%',
                    <?php endif; ?>
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
        </script>
        
        <div class="pollify-poll-total">
            <?php echo sprintf(_n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'), number_format_i18n($total_votes)); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
