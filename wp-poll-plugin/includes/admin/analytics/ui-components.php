
<?php
/**
 * UI components for analytics page
 *
 * @package Pollify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the analytics page header
 */
function pollify_render_analytics_header() {
	?>
	<div class="wrap pollify-admin-analytics">
		<h1><?php esc_html_e( 'Pollify Analytics', 'pollify' ); ?></h1>
	<?php
}

/**
 * Render the analytics controls
 *
 * @param string $time_period Current time period filter.
 * @param int    $poll_id     Current poll ID filter.
 */
function pollify_render_analytics_controls( $time_period, $poll_id ) {
	?>
	<div class="pollify-analytics-controls">
		<div class="pollify-filter-controls">
			<form method="get" action="">
				<input type="hidden" name="page" value="pollify-analytics">
				
				<select name="period" id="pollify-time-period">
					<option value="all" <?php selected( $time_period, 'all' ); ?>><?php esc_html_e( 'All Time', 'pollify' ); ?></option>
					<option value="today" <?php selected( $time_period, 'today' ); ?>><?php esc_html_e( 'Today', 'pollify' ); ?></option>
					<option value="yesterday" <?php selected( $time_period, 'yesterday' ); ?>><?php esc_html_e( 'Yesterday', 'pollify' ); ?></option>
					<option value="week" <?php selected( $time_period, 'week' ); ?>><?php esc_html_e( 'Last 7 Days', 'pollify' ); ?></option>
					<option value="month" <?php selected( $time_period, 'month' ); ?>><?php esc_html_e( 'Last 30 Days', 'pollify' ); ?></option>
				</select>
				
				<select name="poll_id" id="pollify-poll-select">
					<option value="0"><?php esc_html_e( 'All Polls', 'pollify' ); ?></option>
					<?php
					$polls = get_posts(
						array(
							'post_type'      => 'poll',
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					);
					
					foreach ( $polls as $poll ) {
						echo '<option value="' . esc_attr( $poll->ID ) . '" ' . selected( $poll_id, $poll->ID, false ) . '>' . esc_html( $poll->post_title ) . '</option>';
					}
					?>
				</select>
				
				<button type="submit" class="button"><?php esc_html_e( 'Apply', 'pollify' ); ?></button>
			</form>
		</div>
		
		<div class="pollify-export-controls">
			<button id="pollify-export-csv" class="button">
				<span class="dashicons dashicons-media-spreadsheet"></span>
				<?php esc_html_e( 'Export CSV', 'pollify' ); ?>
			</button>
			
			<button id="pollify-print-report" class="button">
				<span class="dashicons dashicons-printer"></span>
				<?php esc_html_e( 'Print Report', 'pollify' ); ?>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Render the analytics summary section
 *
 * @param array $stats Analytics statistics data.
 */
function pollify_render_analytics_summary( $stats ) {
	?>
	<div class="pollify-analytics-summary">
		<div class="pollify-stat-card">
			<div class="pollify-stat-icon">
				<span class="dashicons dashicons-chart-bar"></span>
			</div>
			<div class="pollify-stat-content">
				<div class="pollify-stat-value"><?php echo esc_html( number_format_i18n( $stats['total_votes'] ) ); ?></div>
				<div class="pollify-stat-label"><?php esc_html_e( 'Total Votes', 'pollify' ); ?></div>
			</div>
		</div>
		
		<div class="pollify-stat-card">
			<div class="pollify-stat-icon">
				<span class="dashicons dashicons-groups"></span>
			</div>
			<div class="pollify-stat-content">
				<div class="pollify-stat-value"><?php echo esc_html( number_format_i18n( $stats['total_voters'] ) ); ?></div>
				<div class="pollify-stat-label"><?php esc_html_e( 'Unique Voters', 'pollify' ); ?></div>
			</div>
		</div>
		
		<div class="pollify-stat-card">
			<div class="pollify-stat-icon">
				<span class="dashicons dashicons-chart-line"></span>
			</div>
			<div class="pollify-stat-content">
				<div class="pollify-stat-value"><?php echo esc_html( round( $stats['votes_per_poll'], 1 ) ); ?></div>
				<div class="pollify-stat-label"><?php esc_html_e( 'Avg. Votes Per Poll', 'pollify' ); ?></div>
			</div>
		</div>
		
		<div class="pollify-stat-card">
			<div class="pollify-stat-icon">
				<span class="dashicons dashicons-clock"></span>
			</div>
			<div class="pollify-stat-content">
				<div class="pollify-stat-value"><?php echo esc_html( $stats['most_active_time'] ); ?></div>
				<div class="pollify-stat-label"><?php esc_html_e( 'Most Active Time', 'pollify' ); ?></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render the voting trends widget
 *
 * @param array $voting_trends Voting trends data.
 */
function pollify_render_voting_trends_widget( $voting_trends ) {
	?>
	<div class="pollify-analytics-widget">
		<h2><?php esc_html_e( 'Voting Trends', 'pollify' ); ?></h2>
		<div class="pollify-chart-container">
			<canvas id="pollify-voting-trends-chart"></canvas>
		</div>
	</div>
	<?php
}

/**
 * Render the top polls widget
 *
 * @param array $popular_polls Popular polls data.
 */
function pollify_render_top_polls_widget( $popular_polls ) {
	?>
	<div class="pollify-analytics-widget">
		<h2><?php esc_html_e( 'Top Polls', 'pollify' ); ?></h2>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Poll', 'pollify' ); ?></th>
					<th><?php esc_html_e( 'Votes', 'pollify' ); ?></th>
					<th><?php esc_html_e( 'Created', 'pollify' ); ?></th>
					<th><?php esc_html_e( 'Status', 'pollify' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if ( $popular_polls ) {
					foreach ( $popular_polls as $poll ) {
						$date = get_the_date( 'M j, Y', $poll->ID );
						$status = get_post_status( $poll->ID );
						$status_class = 'publish' === $status ? 'pollify-status-active' : 'pollify-status-inactive';
						
						echo '<tr>';
						echo '<td><a href="' . esc_url( get_edit_post_link( $poll->ID ) ) . '">' . esc_html( $poll->post_title ) . '</a></td>';
						echo '<td>' . esc_html( number_format_i18n( $poll->vote_count ) ) . '</td>';
						echo '<td>' . esc_html( $date ) . '</td>';
						echo '<td><span class="pollify-status ' . esc_attr( $status_class ) . '">' . esc_html( ucfirst( $status ) ) . '</span></td>';
						echo '</tr>';
					}
				} else {
					echo '<tr><td colspan="4">' . esc_html__( 'No polls found.', 'pollify' ) . '</td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Render the daily activity widget
 *
 * @param array $daily_activity Daily activity data.
 */
function pollify_render_daily_activity_widget( $daily_activity ) {
	?>
	<div class="pollify-analytics-widget">
		<h2><?php esc_html_e( 'Daily Activity', 'pollify' ); ?></h2>
		<div class="pollify-chart-container">
			<canvas id="pollify-daily-activity-chart"></canvas>
		</div>
	</div>
	<?php
}

/**
 * Render the demographics widget
 *
 * @param array $stats Demographics statistics data.
 */
function pollify_render_demographics_widget( $stats ) {
	?>
	<div class="pollify-analytics-widget">
		<h2><?php esc_html_e( 'User Demographics', 'pollify' ); ?></h2>
		<div class="pollify-chart-container">
			<canvas id="pollify-demographics-chart"></canvas>
		</div>
	</div>
	<?php
}
