
<?php
/**
 * Export analytics AJAX handler
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create poll export Ajax handler
 */
add_action('wp_ajax_pollify_export_analytics', 'pollify_export_analytics_callback');
function pollify_export_analytics_callback() {
    // Check nonce
    check_admin_referer('pollify_export_analytics');
    
    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to export poll data.', 'pollify'));
    }
    
    // Get filter parameters
    $period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'all';
    $poll_id = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;
    
    // Get data
    global $wpdb;
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    // Build query
    $where_clauses = array();
    
    if ($poll_id > 0) {
        $where_clauses[] = $wpdb->prepare("poll_id = %d", $poll_id);
    }
    
    switch ($period) {
        case 'today':
            $where_clauses[] = "DATE(vote_date) = CURDATE()";
            break;
        case 'yesterday':
            $where_clauses[] = "DATE(vote_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $where_clauses[] = "vote_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_clauses[] = "vote_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
    
    $where_clause = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    
    $query = "SELECT v.*, p.post_title as poll_title 
              FROM $votes_table v
              LEFT JOIN {$wpdb->posts} p ON v.poll_id = p.ID
              $where_clause
              ORDER BY vote_date DESC";
    
    $votes = $wpdb->get_results($query);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pollify-export-' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV header row
    fputcsv($output, array(
        __('Poll ID', 'pollify'),
        __('Poll Title', 'pollify'),
        __('Option ID', 'pollify'),
        __('User ID', 'pollify'),
        __('IP Address', 'pollify'),
        __('Vote Date', 'pollify')
    ));
    
    // Add data rows
    foreach ($votes as $vote) {
        fputcsv($output, array(
            $vote->poll_id,
            $vote->poll_title,
            $vote->option_id,
            $vote->user_id > 0 ? $vote->user_id : __('Guest', 'pollify'),
            $vote->user_ip,
            $vote->vote_date
        ));
    }
    
    fclose($output);
    exit;
}
