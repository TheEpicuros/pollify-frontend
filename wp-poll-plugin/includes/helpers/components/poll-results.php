
<?php
/**
 * Poll results helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Get HTML for poll results - registered as the canonical function
 *
 * @param int $poll_id Poll ID
 * @param array $options Poll options array
 * @param array $vote_counts Vote counts array
 * @param int $total_votes Total number of votes
 * @param string $display_type Display type (bar, pie, donut, text)
 * @param object|null $user_vote User's vote data
 * @param string $poll_type Type of poll
 * @return string HTML for poll results
 */
if (pollify_can_define_function('pollify_get_results_html')) {
    pollify_declare_function('pollify_get_results_html', function($poll_id, $options, $vote_counts, $total_votes, $display_type = 'bar', $user_vote = null, $poll_type = 'multiple-choice') {
        ob_start();
        
        $selected_option = $user_vote ? $user_vote->option_id : null;
        
        if ($display_type === 'pie' || $display_type === 'donut') {
            // Prepare data for pie/donut chart
            $chart_data = array(
                'labels' => array(),
                'data' => array(),
                'colors' => array()
            );
            
            $color_palette = array(
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#6f42c1', '#5a5c69', '#858796', '#2e59d9', '#17a673'
            );
            
            $i = 0;
            foreach ($options as $option_id => $option_text) {
                $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
                
                $chart_data['labels'][] = $option_text;
                $chart_data['data'][] = $percentage;
                $chart_data['colors'][] = $color_palette[$i % count($color_palette)];
                
                $i++;
            }
            
            $chart_id = 'pollify-chart-' . $poll_id;
            $chart_type = $display_type === 'pie' ? 'pie' : 'doughnut';
            ?>
            <div class="pollify-results-chart-container">
                <canvas id="<?php echo $chart_id; ?>"></canvas>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('<?php echo $chart_id; ?>').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: '<?php echo $chart_type; ?>',
                        data: {
                            labels: <?php echo json_encode($chart_data['labels']); ?>,
                            datasets: [{
                                data: <?php echo json_encode($chart_data['data']); ?>,
                                backgroundColor: <?php echo json_encode($chart_data['colors']); ?>,
                                borderColor: '#ffffff',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            },
                            tooltips: {
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        var label = data.labels[tooltipItem.index] || '';
                                        var value = data.datasets[0].data[tooltipItem.index];
                                        return label + ': ' + value + '%';
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
            </div>
            <?php
        } else {
            // Bar chart or text display
            ?>
            <div class="pollify-results-list">
                <?php foreach ($options as $option_id => $option_text) : 
                    $vote_count = isset($vote_counts[$option_id]) ? $vote_counts[$option_id] : 0;
                    $percentage = $total_votes > 0 ? round(($vote_count / $total_votes) * 100) : 0;
                    $is_selected = $selected_option === $option_id;
                ?>
                <div class="pollify-result-item<?php echo $is_selected ? ' pollify-voted' : ''; ?>">
                    <div class="pollify-result-text">
                        <?php echo esc_html($option_text); ?>
                        <?php if ($is_selected) : ?>
                        <span class="pollify-your-vote"><?php _e('Your vote', 'pollify'); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pollify-result-data">
                        <div class="pollify-result-count">
                            <?php echo number_format_i18n($vote_count); ?> 
                            <span class="pollify-vote-text">
                                <?php echo _n('vote', 'votes', $vote_count, 'pollify'); ?>
                            </span>
                        </div>
                        
                        <div class="pollify-result-percentage">
                            <?php echo $percentage; ?>%
                        </div>
                    </div>
                    
                    <?php if ($display_type !== 'text') : ?>
                    <div class="pollify-result-bar-container">
                        <div class="pollify-result-bar<?php echo $is_selected ? ' pollify-voted-bar' : ''; ?>" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                
                <div class="pollify-results-total">
                    <?php 
                    printf(
                        _n('Total: %s vote', 'Total: %s votes', $total_votes, 'pollify'),
                        number_format_i18n($total_votes)
                    ); 
                    ?>
                </div>
            </div>
            <?php
        }
        
        return ob_get_clean();
    }, $current_file);
}
