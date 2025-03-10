
<?php
/**
 * Admin dashboard for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the admin dashboard
 */
function pollify_admin_page() {
    // Get statistics
    $stats = pollify_get_stats();
    ?>
    <div class="wrap pollify-admin-dashboard">
        <h1><?php _e('Pollify Dashboard', 'pollify'); ?></h1>
        
        <div class="pollify-dashboard-header">
            <div class="pollify-version">
                <span><?php _e('Version', 'pollify'); ?>: <?php echo POLLIFY_VERSION; ?></span>
            </div>
            
            <div class="pollify-actions">
                <a href="<?php echo admin_url('post-new.php?post_type=poll'); ?>" class="button button-primary">
                    <?php _e('Create New Poll', 'pollify'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=pollify-settings'); ?>" class="button">
                    <?php _e('Settings', 'pollify'); ?>
                </a>
            </div>
        </div>
        
        <div class="pollify-dashboard-widgets">
            <!-- Statistics -->
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
            
            <!-- Recent Polls -->
            <div class="pollify-widget pollify-recent-polls-widget">
                <h2><?php _e('Recent Polls', 'pollify'); ?></h2>
                
                <?php
                $recent_polls = get_posts(array(
                    'post_type' => 'poll',
                    'posts_per_page' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_polls) {
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr>';
                    echo '<th>' . __('Title', 'pollify') . '</th>';
                    echo '<th>' . __('Author', 'pollify') . '</th>';
                    echo '<th>' . __('Votes', 'pollify') . '</th>';
                    echo '<th>' . __('Date', 'pollify') . '</th>';
                    echo '</tr></thead><tbody>';
                    
                    foreach ($recent_polls as $poll) {
                        $vote_counts = pollify_get_vote_counts($poll->ID);
                        $total_votes = array_sum($vote_counts);
                        $author = get_the_author_meta('display_name', $poll->post_author);
                        $date = get_the_date('M j, Y', $poll);
                        
                        echo '<tr>';
                        echo '<td><a href="' . get_edit_post_link($poll->ID) . '">' . esc_html($poll->post_title) . '</a></td>';
                        echo '<td>' . esc_html($author) . '</td>';
                        echo '<td>' . number_format_i18n($total_votes) . '</td>';
                        echo '<td>' . esc_html($date) . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<p>' . __('No polls found.', 'pollify') . '</p>';
                }
                ?>
                
                <p class="pollify-view-all">
                    <a href="<?php echo admin_url('edit.php?post_type=poll'); ?>">
                        <?php _e('View All Polls', 'pollify'); ?> →
                    </a>
                </p>
            </div>
            
            <!-- Popular Polls -->
            <div class="pollify-widget pollify-popular-polls-widget">
                <h2><?php _e('Popular Polls', 'pollify'); ?></h2>
                
                <?php
                $popular_polls = pollify_get_popular_polls(5);
                
                if ($popular_polls) {
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr>';
                    echo '<th>' . __('Title', 'pollify') . '</th>';
                    echo '<th>' . __('Votes', 'pollify') . '</th>';
                    echo '<th>' . __('Author', 'pollify') . '</th>';
                    echo '</tr></thead><tbody>';
                    
                    foreach ($popular_polls as $poll) {
                        $author = get_the_author_meta('display_name', $poll->post_author);
                        
                        echo '<tr>';
                        echo '<td><a href="' . get_edit_post_link($poll->ID) . '">' . esc_html($poll->post_title) . '</a></td>';
                        echo '<td>' . number_format_i18n($poll->vote_count) . '</td>';
                        echo '<td>' . esc_html($author) . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<p>' . __('No polls with votes yet.', 'pollify') . '</p>';
                }
                ?>
            </div>
            
            <!-- Getting Started -->
            <div class="pollify-widget pollify-help-widget">
                <h2><?php _e('Getting Started', 'pollify'); ?></h2>
                
                <div class="pollify-help-content">
                    <p><?php _e('Here\'s how to get started with Pollify:', 'pollify'); ?></p>
                    
                    <ol>
                        <li><?php _e('Create a new poll by clicking "Add New Poll"', 'pollify'); ?></li>
                        <li><?php _e('Add your poll question and options', 'pollify'); ?></li>
                        <li><?php _e('Publish your poll', 'pollify'); ?></li>
                        <li><?php _e('Use the shortcode to display your poll on any page or post', 'pollify'); ?></li>
                    </ol>
                    
                    <p><strong><?php _e('Available Shortcodes:', 'pollify'); ?></strong></p>
                    
                    <ul>
                        <li><code>[pollify id="123"]</code> - <?php _e('Display a specific poll', 'pollify'); ?></li>
                        <li><code>[pollify_create]</code> - <?php _e('Display a poll creation form', 'pollify'); ?></li>
                        <li><code>[pollify_browse]</code> - <?php _e('Display a list of polls', 'pollify'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .pollify-admin-dashboard {
        max-width: 1200px;
    }
    
    .pollify-dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .pollify-version {
        color: #666;
        font-style: italic;
    }
    
    .pollify-dashboard-widgets {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .pollify-widget {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    
    .pollify-widget h2 {
        border-bottom: 1px solid #eee;
        padding: 12px 15px;
        margin: 0;
        font-size: 14px;
    }
    
    .pollify-stats-widget {
        grid-column: span 2;
    }
    
    .pollify-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        padding: 15px;
        gap: 15px;
    }
    
    .pollify-stat-card {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f9f9f9;
        border-radius: 4px;
    }
    
    .pollify-stat-icon {
        font-size: 28px;
        margin-right: 15px;
        color: #0073aa;
    }
    
    .pollify-stat-value {
        font-size: 24px;
        font-weight: 600;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .pollify-stat-label {
        color: #666;
        font-size: 13px;
    }
    
    .pollify-recent-polls-widget,
    .pollify-popular-polls-widget,
    .pollify-help-widget {
        grid-column: span 1;
    }
    
    .pollify-recent-polls-widget table,
    .pollify-popular-polls-widget table {
        margin: 0;
    }
    
    .pollify-view-all {
        padding: 10px 15px;
        margin: 0;
        border-top: 1px solid #eee;
        text-align: right;
    }
    
    .pollify-help-content {
        padding: 15px;
    }
    
    .pollify-help-content ul,
    .pollify-help-content ol {
        margin-left: 20px;
    }
    
    @media screen and (max-width: 782px) {
        .pollify-dashboard-widgets {
            grid-template-columns: 1fr;
        }
        
        .pollify-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .pollify-stats-widget,
        .pollify-recent-polls-widget,
        .pollify-popular-polls-widget,
        .pollify-help-widget {
            grid-column: span 1;
        }
    }
    </style>
    <?php
}

/**
 * Get plugin statistics
 */
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

/**
 * Get popular polls
 */
function pollify_get_popular_polls($limit = 5) {
    global $wpdb;
    
    $votes_table = $wpdb->prefix . 'pollify_votes';
    
    $query = "SELECT p.*, COUNT(v.id) AS vote_count
              FROM {$wpdb->posts} p
              LEFT JOIN $votes_table v ON p.ID = v.poll_id
              WHERE p.post_type = 'poll' AND p.post_status = 'publish'
              GROUP BY p.ID
              ORDER BY vote_count DESC
              LIMIT %d";
    
    $popular_polls = $wpdb->get_results($wpdb->prepare($query, $limit));
    
    return $popular_polls;
}
