
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

// GitHub integration test comment - safe to remove

// Define plugin constants
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POLLIFY_PLUGIN_FILE', __FILE__);
define('POLLIFY_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include function registry utilities first to prevent duplicate functions
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils/function-exists.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils/function-registry.php';

// Include database functions next to ensure they're available during activation
require_once POLLIFY_PLUGIN_DIR . 'includes/database/main.php';

// Include core files and activation/deactivation functions
require_once POLLIFY_PLUGIN_DIR . 'includes/core/activation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/deactivation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/setup.php';

// Plugin activation and deactivation hooks
register_activation_hook(__FILE__, 'pollify_activate_plugin');
register_deactivation_hook(__FILE__, 'pollify_deactivate_plugin');

// Include admin files only in admin area
if (is_admin()) {
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-menu.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-functions.php';  // Added admin functions
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings/main.php';
}

// Include front-end files
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/api/rest-api.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

// Add plugin action links
add_filter('plugin_action_links_' . POLLIFY_PLUGIN_BASENAME, 'pollify_plugin_add_settings_link');
