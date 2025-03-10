
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

// Define plugin constants
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('POLLIFY_PLUGIN_FILE', __FILE__);
define('POLLIFY_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include core files
require_once POLLIFY_PLUGIN_DIR . 'includes/core/constants.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/core/utils.php';

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
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/admin-menu.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/dashboard.php';
    require_once POLLIFY_PLUGIN_DIR . 'includes/admin/settings.php';
}

// Include front-end files
require_once POLLIFY_PLUGIN_DIR . 'includes/assets/enqueue.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/post-types.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes.php';

// Include AJAX handlers
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax-handlers.php';

// Include REST API endpoints
require_once POLLIFY_PLUGIN_DIR . 'includes/api/rest-api.php';

// Helper functions
require_once POLLIFY_PLUGIN_DIR . 'includes/helpers.php';

/**
 * Initialize the plugin
 */
function pollify_init() {
    // Load plugin textdomain
    load_plugin_textdomain('pollify', false, dirname(POLLIFY_PLUGIN_BASENAME) . '/languages');
    
    // Additional initialization
    do_action('pollify_init');
}
add_action('plugins_loaded', 'pollify_init');
