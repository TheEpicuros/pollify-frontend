<?php
/**
 * Admin analytics page for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include function registry utilities
require_once plugin_dir_path(dirname(__FILE__)) . 'core/utils/function-exists.php';

/**
 * Render the analytics page
 */
function pollify_analytics_page() {
    // Load the canonical stats function
    if (!function_exists('pollify_get_stats')) {
        pollify_require_function('pollify_get_stats');
    }
    
    // Get statistics
    $stats = pollify_get_stats();
    $time_period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'all';
    $poll_id = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;
    
    // Get popular polls
    $popular_polls = pollify_get_popular_polls(10);
    
    // Get voting trends
    $voting_trends = pollify_get_voting_trends($time_period);
    
    // Get activity by day
    $daily_activity = pollify_get_daily_activity($time_period);
    
    // Render the page
    ?>
    <div class="wrap pollify-admin-analytics">
        <h1><?php _e('Pollify Analytics', 'pollify'); ?></h1>
        
        <div class="pollify-analytics-controls">
            <div class="pollify-filter-controls">
                <form method="get" action="">
                    <input type="hidden" name="page" value="pollify-analytics">
                    
                    <select name="period" id="pollify-time-period">
                        <option value="all" <?php selected($time_period, 'all'); ?>><?php _e('All Time', 'pollify'); ?></option>
                        <option value="today" <?php selected($time_period, 'today'); ?>><?php _e('Today', 'pollify'); ?></option>
                        <option value="yesterday" <?php selected($time_period, 'yesterday'); ?>><?php _e('Yesterday', 'pollify'); ?></option>
                        <option value="week" <?php selected($time_period, 'week'); ?>><?php _e('Last 7 Days', 'pollify'); ?></option>
                        <option value="month" <?php selected($time_period, 'month'); ?>><?php _e('Last 30 Days', 'pollify'); ?></option>
                    </select>
                    
                    <select name="poll_id" id="pollify-poll-select">
                        <option value="0"><?php _e('All Polls', 'pollify'); ?></option>
                        <?php
                        $polls = get_posts(array(
                            'post_type' => 'poll',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ));
                        
                        foreach ($polls as $poll) {
                            echo '<option value="' . esc_attr($poll->ID) . '" ' . selected($poll_id, $poll->ID, false) . '>' . esc_html($poll->post_title) . '</option>';
                        }
                        ?>
                    </select>
                    
                    <button type="submit" class="button"><?php _e('Apply', 'pollify'); ?></button>
                </form>
            </div>
            
            <div class="pollify-export-controls">
                <button id="pollify-export-csv" class="button">
                    <span class="dashicons dashicons-media-spreadsheet"></span>
                    <?php _e('Export CSV', 'pollify'); ?>
                </button>
                
                <button id="pollify-print-report" class="button">
                    <span class="dashicons dashicons-printer"></span>
                    <?php _e('Print Report', 'pollify'); ?>
                </button>
            </div>
        </div>
        
        <div class="pollify-analytics-summary">
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon">
                    <span class="dashicons dashicons-chart-bar"></span>
                </div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['total_votes']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Total Votes', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['total_voters']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Unique Voters', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo round($stats['votes_per_poll'], 1); ?></div>
                    <div class="pollify-stat-label"><?php _e('Avg. Votes Per Poll', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo $stats['most_active_time']; ?></div>
                    <div class="pollify-stat-label"><?php _e('Most Active Time', 'pollify'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="pollify-analytics-grid">
            <!-- Voting Trends -->
            <div class="pollify-analytics-widget">
                <h2><?php _e('Voting Trends', 'pollify'); ?></h2>
                <div class="pollify-chart-container">
                    <canvas id="pollify-voting-trends-chart"></canvas>
                </div>
            </div>
            
            <!-- Top Polls -->
            <div class="pollify-analytics-widget">
                <h2><?php _e('Top Polls', 'pollify'); ?></h2>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Poll', 'pollify'); ?></th>
                            <th><?php _e('Votes', 'pollify'); ?></th>
                            <th><?php _e('Created', 'pollify'); ?></th>
                            <th><?php _e('Status', 'pollify'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($popular_polls) {
                            foreach ($popular_polls as $poll) {
                                $date = get_the_date('M j, Y', $poll->ID);
                                $status = get_post_status($poll->ID);
                                $status_class = $status === 'publish' ? 'pollify-status-active' : 'pollify-status-inactive';
                                
                                echo '<tr>';
                                echo '<td><a href="' . esc_url(get_edit_post_link($poll->ID)) . '">' . esc_html($poll->post_title) . '</a></td>';
                                echo '<td>' . number_format_i18n($poll->vote_count) . '</td>';
                                echo '<td>' . esc_html($date) . '</td>';
                                echo '<td><span class="pollify-status ' . esc_attr($status_class) . '">' . esc_html(ucfirst($status)) . '</span></td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4">' . __('No polls found.', 'pollify') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Daily Activity -->
            <div class="pollify-analytics-widget">
                <h2><?php _e('Daily Activity', 'pollify'); ?></h2>
                <div class="pollify-chart-container">
                    <canvas id="pollify-daily-activity-chart"></canvas>
                </div>
            </div>
            
            <!-- User Demographics -->
            <div class="pollify-analytics-widget">
                <h2><?php _e('User Demographics', 'pollify'); ?></h2>
                <div class="pollify-chart-container">
                    <canvas id="pollify-demographics-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Initialize charts if Chart.js is loaded
        if (typeof Chart !== 'undefined') {
            // Voting trends chart
            var trendsCtx = document.getElementById('pollify-voting-trends-chart').getContext('2d');
            var trendsData = <?php echo json_encode($voting_trends); ?>;
            
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trendsData.labels,
                    datasets: [{
                        label: '<?php _e('Votes', 'pollify'); ?>',
                        data: trendsData.values,
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // Daily activity chart
            var activityCtx = document.getElementById('pollify-daily-activity-chart').getContext('2d');
            var activityData = <?php echo json_encode($daily_activity); ?>;
            
            new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: activityData.labels,
                    datasets: [{
                        label: '<?php _e('Votes', 'pollify'); ?>',
                        data: activityData.values,
                        backgroundColor: '#8B5CF6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // User demographics chart
            var demographicsCtx = document.getElementById('pollify-demographics-chart').getContext('2d');
            
            new Chart(demographicsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['<?php _e('Logged In', 'pollify'); ?>', '<?php _e('Guest', 'pollify'); ?>'],
                    datasets: [{
                        data: [<?php echo $stats['logged_in_voters']; ?>, <?php echo $stats['guest_voters']; ?>],
                        backgroundColor: ['#8B5CF6', '#D946EF']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Export CSV functionality
        $('#pollify-export-csv').on('click', function() {
            var period = $('#pollify-time-period').val();
            var pollId = $('#pollify-poll-select').val();
            
            window.location.href = ajaxurl + '?action=pollify_export_analytics&period=' + period + '&poll_id=' + pollId + '&_wpnonce=' + '<?php echo wp_create_nonce('pollify_export_analytics'); ?>';
        });
        
        // Print report functionality
        $('#pollify-print-report').on('click', function() {
            window.print();
        });
    });
    </script>
    
    <style>
    .pollify-admin-analytics {
        max-width: 1200px;
    }
    
    .pollify-analytics-controls {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
    }
    
    .pollify-analytics-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .pollify-stat-card {
        display: flex;
        align-items: center;
        padding: 20px;
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
    }
    
    .pollify-stat-icon {
        margin-right: 15px;
    }
    
    .pollify-stat-icon .dashicons {
        font-size: 30px;
        width: 30px;
        height: 30px;
        color: #8B5CF6;
    }
    
    .pollify-stat-value {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .pollify-stat-label {
        color: #666;
    }
    
    .pollify-analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .pollify-analytics-widget {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    
    .pollify-analytics-widget h2 {
        border-bottom: 1px solid #eee;
        padding: 12px 15px;
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }
    
    .pollify-chart-container {
        height: 300px;
        padding: 15px;
    }
    
    .pollify-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
    }
    
    .pollify-status-active {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .pollify-status-inactive {
        background: #f8d7da;
        color: #842029;
    }
    
    @media print {
        .pollify-analytics-controls, #adminmenuwrap, #adminmenuback, #wpadminbar, #wpfooter {
            display: none !important;
        }
        
        #wpcontent, #wpbody-content {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
        
        .pollify-analytics-grid {
            display: block;
        }
        
        .pollify-analytics-widget {
            break-inside: avoid;
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
    }
    
    @media screen and (max-width: 782px) {
        .pollify-analytics-summary,
        .pollify-analytics-grid {
            grid-template-columns: 1fr;
        }
        
        .pollify-analytics-controls {
            flex-direction: column;
        }
        
        .pollify-export-controls {
            margin-top: 15px;
        }
    }
    </style>
    <?php
}

/**
 * Get voting trends data
 */
function pollify_get_voting_trends($period = 'all') {
    global $wpdb;
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    // Initialize data arrays
    $labels = array();
    $values = array();
    
    // Set date conditions based on period
    $date_condition = '';
    switch ($period) {
        case 'today':
            $date_condition = "DATE(vote_date) = CURDATE()";
            break;
        case 'yesterday':
            $date_condition = "DATE(vote_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $date_condition = "vote_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $date_condition = "vote_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        default:
            $date_condition = "1=1"; // All time
    }
    
    if ($period === 'today' || $period === 'yesterday') {
        // Get hourly data for today or yesterday
        for ($i = 0; $i < 24; $i++) {
            $hour = sprintf("%02d", $i);
            $labels[] = $hour . ':00';
            
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $votes_table 
                WHERE $date_condition AND HOUR(vote_date) = %d",
                $i
            );
            
            $count = $wpdb->get_var($query);
            $values[] = $count ? $count : 0;
        }
    } elseif ($period === 'week') {
        // Get data for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('D', strtotime($date));
            
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $votes_table 
                WHERE DATE(vote_date) = %s",
                $date
            );
            
            $count = $wpdb->get_var($query);
            $values[] = $count ? $count : 0;
        }
    } elseif ($period === 'month') {
        // Get data for last 30 days (by week)
        for ($i = 0; $i < 4; $i++) {
            $start_day = 30 - ($i * 7) - 6;
            $end_day = 30 - ($i * 7);
            
            if ($start_day < 0) $start_day = 0;
            
            $start_date = date('Y-m-d', strtotime("-$end_day days"));
            $end_date = date('Y-m-d', strtotime("-$start_day days"));
            
            $labels[] = date('M d', strtotime($start_date)) . ' - ' . date('M d', strtotime($end_date));
            
            $query = $wpdb->prepare(
                "SELECT COUNT(*) FROM $votes_table 
                WHERE DATE(vote_date) BETWEEN %s AND %s",
                $start_date, $end_date
            );
            
            $count = $wpdb->get_var($query);
            $values[] = $count ? $count : 0;
        }
        
        // Reverse the arrays to display in chronological order
        $labels = array_reverse($labels);
        $values = array_reverse($values);
    } else {
        // Get data for all time (by month)
        $query = "SELECT 
                    DATE_FORMAT(vote_date, '%Y-%m') as month, 
                    COUNT(*) as count 
                  FROM $votes_table 
                  GROUP BY month 
                  ORDER BY month ASC
                  LIMIT 12";
        
        $results = $wpdb->get_results($query);
        
        foreach ($results as $row) {
            $month_year = date('M Y', strtotime($row->month . '-01'));
            $labels[] = $month_year;
            $values[] = $row->count;
        }
    }
    
    return array(
        'labels' => $labels,
        'values' => $values
    );
}

/**
 * Get daily activity data
 */
function pollify_get_daily_activity($period = 'all') {
    global $wpdb;
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    // Initialize data arrays
    $labels = array();
    $values = array();
    
    // Set the time range based on period
    $date_condition = '';
    $group_by = '';
    $limit = '';
    
    switch ($period) {
        case 'today':
        case 'yesterday':
            $date = $period === 'today' ? 'CURDATE()' : 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            $date_condition = "DATE(vote_date) = $date";
            $group_by = "HOUR(vote_date)";
            $order_by = "hour";
            break;
        case 'week':
            $date_condition = "vote_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $group_by = "DATE(vote_date)";
            $order_by = "day";
            break;
        case 'month':
            $date_condition = "vote_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $group_by = "DATE(vote_date)";
            $order_by = "day";
            $limit = "LIMIT 30";
            break;
        default:
            $date_condition = "1=1"; // All time
            $group_by = "DATE_FORMAT(vote_date, '%Y-%m')";
            $order_by = "month";
            $limit = "LIMIT 12";
    }
    
    if ($period === 'today' || $period === 'yesterday') {
        // Get hourly data
        $query = "SELECT 
                    HOUR(vote_date) as hour, 
                    COUNT(*) as count 
                  FROM $votes_table 
                  WHERE $date_condition 
                  GROUP BY $group_by 
                  ORDER BY hour ASC";
        
        $results = $wpdb->get_results($query);
        
        // Fill in all hours of the day
        $hourly_data = array_fill(0, 24, 0);
        
        foreach ($results as $row) {
            $hourly_data[$row->hour] = $row->count;
        }
        
        for ($i = 0; $i < 24; $i++) {
            $hour = sprintf("%02d", $i);
            $labels[] = $hour . ':00';
            $values[] = $hourly_data[$i];
        }
    } elseif ($period === 'week' || $period === 'month') {
        // Get daily data
        $query = "SELECT 
                    DATE(vote_date) as day, 
                    COUNT(*) as count 
                  FROM $votes_table 
                  WHERE $date_condition 
                  GROUP BY $group_by 
                  ORDER BY day ASC
                  $limit";
        
        $results = $wpdb->get_results($query);
        
        foreach ($results as $row) {
            $labels[] = date('M d', strtotime($row->day));
            $values[] = $row->count;
        }
    } else {
        // Get monthly data
        $query = "SELECT 
                    DATE_FORMAT(vote_date, '%Y-%m') as month, 
                    COUNT(*) as count 
                  FROM $votes_table 
                  GROUP BY month 
                  ORDER BY month ASC
                  $limit";
        
        $results = $wpdb->get_results($query);
        
        foreach ($results as $row) {
            $month_year = date('M Y', strtotime($row->month . '-01'));
            $labels[] = $month_year;
            $values[] = $row->count;
        }
    }
    
    return array(
        'labels' => $labels,
        'values' => $values
    );
}

/**
 * Get popular polls
 */
function pollify_get_popular_polls($limit = 5) {
    global $wpdb;
    
    $votes_table = $wpdb->prefix . 'pollify_votes';
    $query = $wpdb->prepare("
        SELECT p.ID, p.post_title, COUNT(v.id) as vote_count
        FROM {$wpdb->posts} p
        LEFT JOIN $votes_table v ON p.ID = v.poll_id
        WHERE p.post_type = 'poll' AND p.post_status IN ('publish', 'future', 'private')
        GROUP BY p.ID
        ORDER BY vote_count DESC
        LIMIT %d
    ", $limit);
    
    return $wpdb->get_results($query);
}

// Import the canonical stats function instead of redefining it
if (!function_exists('pollify_get_stats')) {
    require_once plugin_dir_path(__FILE__) . 'dashboard/statistics.php';
}

