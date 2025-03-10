
<?php
/**
 * Plugin Name: Pollify - React Polling System
 * Plugin URI: https://example.com/pollify
 * Description: A modern polling system with React frontend
 * Version: 1.0.0
 * Author: Lovable
 * Author URI: https://lovable.ai
 * Text Domain: pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POLLIFY_ADMIN_URL', admin_url('admin.php?page=pollify'));

// Include core files
require_once POLLIFY_PLUGIN_DIR . 'includes/core/constants.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/activation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/deactivation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/uninstall.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils.php';

// Include features
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-menu.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/assets/enqueue.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/api/rest-api.php';

// Include existing files
require_once POLLIFY_PLUGIN_DIR . 'includes/database.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

// Register hooks
register_activation_hook(__FILE__, 'pollify_activate_plugin');
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');
register_uninstall_hook(__FILE__, 'pollify_uninstall_plugin');

// Add plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'pollify_add_settings_link');
