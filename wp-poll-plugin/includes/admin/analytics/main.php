
<?php
/**
 * Admin analytics page main controller
 *
 * @package Pollify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include required analytics components.
require_once plugin_dir_path( __FILE__ ) . 'data-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'chart-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'ui-components.php';

/**
 * Render the analytics page
 */
function pollify_analytics_page() {
	// Load the canonical stats function.
	if ( ! function_exists( 'pollify_get_stats' ) ) {
		pollify_require_function( 'pollify_get_stats' );
	}
	
	// Get statistics.
	$stats = pollify_get_stats();
	$time_period = isset( $_GET['period'] ) ? sanitize_text_field( wp_unslash( $_GET['period'] ) ) : 'all';
	$poll_id = isset( $_GET['poll_id'] ) ? intval( $_GET['poll_id'] ) : 0;
	
	// Get data for charts and tables.
	$popular_polls = pollify_get_popular_polls( 10 );
	$voting_trends = pollify_get_voting_trends( $time_period );
	$daily_activity = pollify_get_daily_activity( $time_period );
	
	// Render the UI components.
	pollify_render_analytics_header();
	pollify_render_analytics_controls( $time_period, $poll_id );
	pollify_render_analytics_summary( $stats );
	
	echo '<div class="pollify-analytics-grid">';
	
	// Render chart sections.
	pollify_render_voting_trends_widget( $voting_trends );
	pollify_render_top_polls_widget( $popular_polls );
	pollify_render_daily_activity_widget( $daily_activity );
	pollify_render_demographics_widget( $stats );
	
	echo '</div>';
	
	// Render scripts and styles.
	pollify_render_analytics_scripts( $voting_trends, $daily_activity, $stats );
	pollify_render_analytics_styles();
}

// Import the canonical stats function instead of redefining it.
if ( ! function_exists( 'pollify_get_stats' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'dashboard/statistics.php';
}
