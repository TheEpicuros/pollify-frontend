
<?php
/**
 * Plugin information and constants
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin information constants
 */
define('POLLIFY_VERSION', '1.0.0');
define('POLLIFY_PLUGIN_DIR', plugin_dir_path(dirname(dirname(__FILE__))));
define('POLLIFY_PLUGIN_URL', plugin_dir_url(dirname(dirname(__FILE__))));
define('POLLIFY_PLUGIN_FILE', dirname(dirname(dirname(__FILE__))) . '/pollify.php');
define('POLLIFY_PLUGIN_BASENAME', plugin_basename(POLLIFY_PLUGIN_FILE));
