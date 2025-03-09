
<?php
/**
 * Shortcodes for the Pollify plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include shortcode files
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-utils.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-display.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-create.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-browse.php';

// Register shortcodes
add_shortcode('pollify', 'pollify_poll_shortcode');
add_shortcode('pollify_create', 'pollify_create_shortcode');
add_shortcode('pollify_browse', 'pollify_browse_shortcode');

