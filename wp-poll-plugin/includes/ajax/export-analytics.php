
<?php
/**
 * Export analytics data as CSV
 *
 * @package Pollify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register the AJAX handler.
add_action( 'wp_ajax_pollify_export_analytics', 'pollify_export_analytics_callback' );

/**
 * Handle exporting analytics data to CSV
 */
function pollify_export_analytics_callback() {
	// Verify nonce.
	check_ajax_referer( 'pollify_export_analytics', '_wpnonce' );
	
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to export analytics data.', 'pollify' ) );
	}
	
	// Get parameters.
	$period = isset( $_GET['period'] ) ? sanitize_text_field( wp_unslash( $_GET['period'] ) ) : 'all';
	$poll_id = isset( $_GET['poll_id'] ) ? intval( $_GET['poll_id'] ) : 0;
	
	// Load data functions.
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/analytics/data-functions.php';
	
	// Load stats function.
	if ( ! function_exists( 'pollify_get_stats' ) ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/dashboard/statistics.php';
	}
	
	// Get data.
	$voting_trends = pollify_get_voting_trends( $period );
	$daily_activity = pollify_get_daily_activity( $period );
	$popular_polls = pollify_get_popular_polls( 10 );
	$stats = pollify_get_stats();
	
	// Generate filename.
	$date = current_time( 'Y-m-d' );
	$filename = "pollify-analytics-{$period}-{$date}.csv";
	
	// Set headers for CSV download.
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	
	// Create output stream.
	$output = fopen( 'php://output', 'w' );
	
	// Add CSV headers and data.
	fputcsv( $output, array( 'Pollify Analytics Export', $date ) );
	fputcsv( $output, array( 'Period', $period ) );
	fputcsv( $output, array() );
	
	// Stats summary.
	fputcsv( $output, array( 'Summary Statistics' ) );
	fputcsv( $output, array( 'Metric', 'Value' ) );
	fputcsv( $output, array( 'Total Votes', $stats['total_votes'] ) );
	fputcsv( $output, array( 'Unique Voters', $stats['total_voters'] ) );
	fputcsv( $output, array( 'Average Votes Per Poll', $stats['votes_per_poll'] ) );
	fputcsv( $output, array( 'Most Active Time', $stats['most_active_time'] ) );
	fputcsv( $output, array( 'Logged In Voters', $stats['logged_in_voters'] ) );
	fputcsv( $output, array( 'Guest Voters', $stats['guest_voters'] ) );
	fputcsv( $output, array() );
	
	// Voting trends.
	fputcsv( $output, array( 'Voting Trends' ) );
	fputcsv( $output, array( 'Time Period', 'Vote Count' ) );
	foreach ( $voting_trends['labels'] as $index => $label ) {
		fputcsv( $output, array( $label, $voting_trends['values'][$index] ) );
	}
	fputcsv( $output, array() );
	
	// Popular polls.
	fputcsv( $output, array( 'Top Polls' ) );
	fputcsv( $output, array( 'Poll ID', 'Poll Title', 'Vote Count' ) );
	foreach ( $popular_polls as $poll ) {
		fputcsv( $output, array( $poll->ID, $poll->post_title, $poll->vote_count ) );
	}
	
	// Close the output stream.
	fclose( $output );
	exit;
}
