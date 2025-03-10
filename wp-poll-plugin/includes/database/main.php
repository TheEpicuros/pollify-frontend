
<?php
/**
 * Main database functionality file
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include database setup
require_once POLLIFY_PLUGIN_DIR . 'includes/database/setup.php';

// Include specific database modules
require_once POLLIFY_PLUGIN_DIR . 'includes/database/votes.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/database/ratings.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/database/comments.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/database/user-activity.php';
