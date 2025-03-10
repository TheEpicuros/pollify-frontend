
<?php
/**
 * Plugin constants
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Database version - used for upgrades
if (!defined('POLLIFY_DB_VERSION')) {
    define('POLLIFY_DB_VERSION', '1.0.0');
}

// Capability for managing polls
if (!defined('POLLIFY_ADMIN_CAPABILITY')) {
    define('POLLIFY_ADMIN_CAPABILITY', 'manage_options');
}

// Poll status constants
define('POLLIFY_STATUS_ACTIVE', 'active');
define('POLLIFY_STATUS_ENDED', 'ended');
define('POLLIFY_STATUS_SCHEDULED', 'scheduled');

// Activity types for gamification
define('POLLIFY_ACTIVITY_VOTE', 'vote');
define('POLLIFY_ACTIVITY_CREATE_POLL', 'create_poll');
define('POLLIFY_ACTIVITY_COMMENT', 'comment');
define('POLLIFY_ACTIVITY_RATE', 'rate');

// Points for different activities
define('POLLIFY_POINTS_VOTE', 5);
define('POLLIFY_POINTS_CREATE_POLL', 20);
define('POLLIFY_POINTS_COMMENT', 10);
define('POLLIFY_POINTS_RATE', 2);

// Poll result display types
define('POLLIFY_DISPLAY_BAR', 'bar');
define('POLLIFY_DISPLAY_PIE', 'pie');
define('POLLIFY_DISPLAY_DONUT', 'donut');
define('POLLIFY_DISPLAY_TEXT', 'text');

// Default settings
define('POLLIFY_DEFAULT_RESULTS_DISPLAY', POLLIFY_DISPLAY_BAR);
define('POLLIFY_DEFAULT_ALLOW_COMMENTS', true);
define('POLLIFY_DEFAULT_SHOW_RESULTS', false);
