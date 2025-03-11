
<?php
/**
 * Chart rendering functions for analytics page
 *
 * @package Pollify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render analytics JavaScript for charts
 *
 * @param array $voting_trends Voting trends data.
 * @param array $daily_activity Daily activity data.
 * @param array $stats Demographics statistics data.
 */
function pollify_render_analytics_scripts( $voting_trends, $daily_activity, $stats ) {
	?>
	<script>
	jQuery(document).ready(function($) {
		// Initialize charts if Chart.js is loaded.
		if ( typeof Chart !== 'undefined' ) {
			// Voting trends chart.
			var trendsCtx = document.getElementById('pollify-voting-trends-chart').getContext('2d');
			var trendsData = <?php echo wp_json_encode( $voting_trends ); ?>;
			
			new Chart(trendsCtx, {
				type: 'line',
				data: {
					labels: trendsData.labels,
					datasets: [{
						label: '<?php esc_html_e( 'Votes', 'pollify' ); ?>',
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
			
			// Daily activity chart.
			var activityCtx = document.getElementById('pollify-daily-activity-chart').getContext('2d');
			var activityData = <?php echo wp_json_encode( $daily_activity ); ?>;
			
			new Chart(activityCtx, {
				type: 'bar',
				data: {
					labels: activityData.labels,
					datasets: [{
						label: '<?php esc_html_e( 'Votes', 'pollify' ); ?>',
						data: activityData.values,
						backgroundColor: '#8B5CF6'
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false
				}
			});
			
			// User demographics chart.
			var demographicsCtx = document.getElementById('pollify-demographics-chart').getContext('2d');
			
			new Chart(demographicsCtx, {
				type: 'doughnut',
				data: {
					labels: ['<?php esc_html_e( 'Logged In', 'pollify' ); ?>', '<?php esc_html_e( 'Guest', 'pollify' ); ?>'],
					datasets: [{
						data: [<?php echo esc_html( $stats['logged_in_voters'] ); ?>, <?php echo esc_html( $stats['guest_voters'] ); ?>],
						backgroundColor: ['#8B5CF6', '#D946EF']
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false
				}
			});
		}
		
		// Export CSV functionality.
		$('#pollify-export-csv').on('click', function() {
			var period = $('#pollify-time-period').val();
			var pollId = $('#pollify-poll-select').val();
			
			window.location.href = ajaxurl + '?action=pollify_export_analytics&period=' + period + '&poll_id=' + pollId + '&_wpnonce=' + '<?php echo esc_attr( wp_create_nonce( 'pollify_export_analytics' ) ); ?>';
		});
		
		// Print report functionality.
		$('#pollify-print-report').on('click', function() {
			window.print();
		});
	});
	</script>
	<?php
}

/**
 * Render analytics CSS styles
 */
function pollify_render_analytics_styles() {
	?>
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
