<?php
/**
 * Admin dashboard for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include dashboard components
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard/statistics.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard/recent-polls.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard/popular-polls.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard/getting-started.php';

/**
 * Output the admin dashboard content
 */
pollify_render_dashboard_content();

/**
 * Render the admin dashboard content
 */
function pollify_render_dashboard_content() {
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
            <?php pollify_render_stats_widget($stats); ?>
            
            <!-- Recent Polls -->
            <?php pollify_render_recent_polls_widget(); ?>
            
            <!-- Popular Polls -->
            <?php pollify_render_popular_polls_widget(); ?>
            
            <!-- Getting Started -->
            <?php pollify_render_help_widget(); ?>
        </div>
    </div>
    
    <?php pollify_dashboard_styles(); ?>
    <?php
}

/**
 * Output dashboard styles
 */
function pollify_dashboard_styles() {
    ?>
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
