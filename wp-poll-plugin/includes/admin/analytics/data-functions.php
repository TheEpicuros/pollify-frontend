
<?php
/**
 * Data fetching functions for analytics
 *
 * @package Pollify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get voting trends data
 *
 * @param string $period The time period to get data for.
 * @return array Data with labels and values.
 */
function pollify_get_voting_trends( $period = 'all' ) {
	global $wpdb;
	$votes_table = $wpdb->prefix . 'pollify_votes';
	
	// Initialize data arrays.
	$labels = array();
	$values = array();
	
	// Set date conditions based on period.
	$date_condition = '';
	switch ( $period ) {
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
			$date_condition = "1=1"; // All time.
	}
	
	if ( 'today' === $period || 'yesterday' === $period ) {
		// Get hourly data for today or yesterday.
		for ( $i = 0; $i < 24; $i++ ) {
			$hour = sprintf( "%02d", $i );
			$labels[] = $hour . ':00';
			
			$query = $wpdb->prepare(
				"SELECT COUNT(*) FROM $votes_table 
				WHERE $date_condition AND HOUR(vote_date) = %d",
				$i
			);
			
			$count = $wpdb->get_var( $query );
			$values[] = $count ? intval( $count ) : 0;
		}
	} elseif ( 'week' === $period ) {
		// Get data for last 7 days.
		for ( $i = 6; $i >= 0; $i-- ) {
			$date = date( 'Y-m-d', strtotime( "-$i days" ) );
			$labels[] = date( 'D', strtotime( $date ) );
			
			$query = $wpdb->prepare(
				"SELECT COUNT(*) FROM $votes_table 
				WHERE DATE(vote_date) = %s",
				$date
			);
			
			$count = $wpdb->get_var( $query );
			$values[] = $count ? intval( $count ) : 0;
		}
	} elseif ( 'month' === $period ) {
		// Get data for last 30 days (by week).
		for ( $i = 0; $i < 4; $i++ ) {
			$start_day = 30 - ( $i * 7 ) - 6;
			$end_day = 30 - ( $i * 7 );
			
			if ( $start_day < 0 ) {
				$start_day = 0;
			}
			
			$start_date = date( 'Y-m-d', strtotime( "-$end_day days" ) );
			$end_date = date( 'Y-m-d', strtotime( "-$start_day days" ) );
			
			$labels[] = date( 'M d', strtotime( $start_date ) ) . ' - ' . date( 'M d', strtotime( $end_date ) );
			
			$query = $wpdb->prepare(
				"SELECT COUNT(*) FROM $votes_table 
				WHERE DATE(vote_date) BETWEEN %s AND %s",
				$start_date,
				$end_date
			);
			
			$count = $wpdb->get_var( $query );
			$values[] = $count ? intval( $count ) : 0;
		}
		
		// Reverse the arrays to display in chronological order.
		$labels = array_reverse( $labels );
		$values = array_reverse( $values );
	} else {
		// Get data for all time (by month).
		$query = "SELECT 
					DATE_FORMAT(vote_date, '%Y-%m') as month, 
					COUNT(*) as count 
				  FROM $votes_table 
				  GROUP BY month 
				  ORDER BY month ASC
				  LIMIT 12";
		
		$results = $wpdb->get_results( $query );
		
		foreach ( $results as $row ) {
			$month_year = date( 'M Y', strtotime( $row->month . '-01' ) );
			$labels[] = $month_year;
			$values[] = intval( $row->count );
		}
	}
	
	return array(
		'labels' => $labels,
		'values' => $values,
	);
}

/**
 * Get daily activity data
 *
 * @param string $period The time period to get data for.
 * @return array Data with labels and values.
 */
function pollify_get_daily_activity( $period = 'all' ) {
	global $wpdb;
	$votes_table = $wpdb->prefix . 'pollify_votes';
	
	// Initialize data arrays.
	$labels = array();
	$values = array();
	
	// Set the time range based on period.
	$date_condition = '';
	$group_by = '';
	$limit = '';
	
	switch ( $period ) {
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
			$date_condition = "1=1"; // All time.
			$group_by = "DATE_FORMAT(vote_date, '%Y-%m')";
			$order_by = "month";
			$limit = "LIMIT 12";
	}
	
	if ( 'today' === $period || 'yesterday' === $period ) {
		// Get hourly data.
		$query = "SELECT 
					HOUR(vote_date) as hour, 
					COUNT(*) as count 
				  FROM $votes_table 
				  WHERE $date_condition 
				  GROUP BY $group_by 
				  ORDER BY hour ASC";
		
		$results = $wpdb->get_results( $query );
		
		// Fill in all hours of the day.
		$hourly_data = array_fill( 0, 24, 0 );
		
		foreach ( $results as $row ) {
			$hourly_data[ $row->hour ] = intval( $row->count );
		}
		
		for ( $i = 0; $i < 24; $i++ ) {
			$hour = sprintf( "%02d", $i );
			$labels[] = $hour . ':00';
			$values[] = $hourly_data[ $i ];
		}
	} elseif ( 'week' === $period || 'month' === $period ) {
		// Get daily data.
		$query = "SELECT 
					DATE(vote_date) as day, 
					COUNT(*) as count 
				  FROM $votes_table 
				  WHERE $date_condition 
				  GROUP BY $group_by 
				  ORDER BY day ASC
				  $limit";
		
		$results = $wpdb->get_results( $query );
		
		foreach ( $results as $row ) {
			$labels[] = date( 'M d', strtotime( $row->day ) );
			$values[] = intval( $row->count );
		}
	} else {
		// Get monthly data.
		$query = "SELECT 
					DATE_FORMAT(vote_date, '%Y-%m') as month, 
					COUNT(*) as count 
				  FROM $votes_table 
				  GROUP BY month 
				  ORDER BY month ASC
				  $limit";
		
		$results = $wpdb->get_results( $query );
		
		foreach ( $results as $row ) {
			$month_year = date( 'M Y', strtotime( $row->month . '-01' ) );
			$labels[] = $month_year;
			$values[] = intval( $row->count );
		}
	}
	
	return array(
		'labels' => $labels,
		'values' => $values,
	);
}

/**
 * Get popular polls
 *
 * @param int $limit Number of polls to retrieve.
 * @return array Popular polls data.
 */
function pollify_get_popular_polls( $limit = 5 ) {
	global $wpdb;
	
	$votes_table = $wpdb->prefix . 'pollify_votes';
	$query = $wpdb->prepare(
		"SELECT p.ID, p.post_title, COUNT(v.id) as vote_count
		FROM {$wpdb->posts} p
		LEFT JOIN $votes_table v ON p.ID = v.poll_id
		WHERE p.post_type = 'poll' AND p.post_status IN ('publish', 'future', 'private')
		GROUP BY p.ID
		ORDER BY vote_count DESC
		LIMIT %d",
		$limit
	);
	
	return $wpdb->get_results( $query );
}
