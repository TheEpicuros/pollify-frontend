
<?php
/**
 * AJAX handlers for the Pollify plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include modular AJAX handler files
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax/vote.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax/create-poll.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax/user-stats.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/ajax/helpers.php';

