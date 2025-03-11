
<?php
/**
 * Dashboard statistics widget
 *
 * @package Pollify
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
 * Render the statistics widget
 *
 * @param array $stats Array of plugin statistics
 */
function pollify_render_stats_widget($stats) {
    ?>
    <div class="pollify-widget pollify-stats-widget">
        <h2><?php _e('Statistics', 'pollify'); ?></h2>
        
        <div class="pollify-stats-grid">
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon dashicons dashicons-chart-bar"></div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['total_polls']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Total Polls', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon dashicons dashicons-backup"></div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['active_polls']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Active Polls', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon dashicons dashicons-chart-line"></div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['total_votes']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Total Votes', 'pollify'); ?></div>
                </div>
            </div>
            
            <div class="pollify-stat-card">
                <div class="pollify-stat-icon dashicons dashicons-groups"></div>
                <div class="pollify-stat-content">
                    <div class="pollify-stat-value"><?php echo number_format_i18n($stats['total_voters']); ?></div>
                    <div class="pollify-stat-label"><?php _e('Unique Voters', 'pollify'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Get plugin statistics
 * Register this as the canonical implementation
 */
if (pollify_can_define_function('pollify_get_stats')) {
    function pollify_get_stats() {
        global $wpdb;
        
        // Get total polls
        $total_polls = wp_count_posts('poll');
        $total_polls_count = $total_polls->publish + $total_polls->future + $total_polls->draft + $total_polls->pending + $total_polls->private;
        
        // Get active polls
        $active_polls_count = $total_polls->publish;
        
        // Get total votes
        $votes_table = $wpdb->prefix . 'pollify_votes';
        $total_votes = $wpdb->get_var("SELECT COUNT(*) FROM $votes_table");
        
        // Get unique voters
        $total_voters = $wpdb->get_var("SELECT COUNT(DISTINCT user_ip) FROM $votes_table");
        
        return array(
            'total_polls' => $total_polls_count,
            'active_polls' => $active_polls_count,
            'total_votes' => $total_votes ? $total_votes : 0,
            'total_voters' => $total_voters ? $total_voters : 0
        );
    }
    pollify_register_function_path('pollify_get_stats', $current_file);
}
