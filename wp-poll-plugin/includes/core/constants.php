
<?php
/**
 * Plugin constants
 * 
 * Note: Main constants are defined in the main plugin file
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Database table names
define('POLLIFY_VOTES_TABLE', 'pollify_votes');
define('POLLIFY_RATINGS_TABLE', 'pollify_ratings');
define('POLLIFY_COMMENTS_TABLE', 'pollify_comments');
define('POLLIFY_USER_ACTIVITY_TABLE', 'pollify_user_activity');

// Additional constants can be defined here if needed in the future
