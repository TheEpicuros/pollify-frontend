
<?php
/**
 * Plugin Name: Pollify - Interactive Polls
 * Plugin URI: https://example.com/pollify
 * Description: A fully-featured, modern polling system for WordPress with real-time results, multiple poll types, and gamification.
 * Version: 1.0.0
 * Author: Pollify Team
 * Author URI: https://example.com
 * Text Domain: pollify
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include plugin information and constants
require_once plugin_dir_path(__FILE__) . 'includes/core/plugin-info.php';

// Include core files
require_once POLLIFY_PLUGIN_DIR . 'includes/core/constants.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/setup.php';

// Plugin activation, deactivation, and uninstall
register_activation_hook(__FILE__, 'pollify_activate_plugin');
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');
require_once POLLIFY_PLUGIN_DIR . 'includes/core/activation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/deactivation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/uninstall.php';

// Database setup and access
require_once POLLIFY_PLUGIN_DIR . 'includes/database/main.php';

// Include admin files
if (is_admin()) {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-functions.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-menu.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/analytics.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/user-activity.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/help.php';
    
    // Admin notices and other admin-specific functionality
    add_action('admin_enqueue_scripts', 'pollify_admin_scripts');
}

// Include front-end files
require_once POLLIFY_PLUGIN_DIR . 'includes/assets/enqueue.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';

// Include AJAX handlers
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax/export-analytics.php';

// Include REST API endpoints
require_once POLLIFY_PLUGIN_DIR . 'includes/api/rest-api.php';

// Helper functions
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

// Add plugin action links
add_filter('plugin_action_links_' . POLLIFY_PLUGIN_BASENAME, 'pollify_add_settings_link');
