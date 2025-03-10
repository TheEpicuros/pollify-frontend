
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

// Include core shortcode functionality
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/core.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/gutenberg-blocks.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/editor-integration.php';

// Register shortcodes
add_shortcode('pollify', 'pollify_poll_shortcode');
add_shortcode('pollify_create', 'pollify_create_shortcode');
add_shortcode('pollify_browse', 'pollify_browse_shortcode');

// Register Gutenberg blocks
add_action('init', 'pollify_register_gutenberg_blocks');

// Add TinyMCE buttons
add_action('admin_init', 'pollify_add_mce_button');
add_action('admin_footer', 'pollify_mce_button_data');

// Add help tabs
add_action('admin_head', 'pollify_add_help_tab');
