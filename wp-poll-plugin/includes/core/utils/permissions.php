
<?php
/**
 * Permission checking utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if current user can manage polls
 */
function pollify_current_user_can_manage() {
    return current_user_can(POLLIFY_ADMIN_CAPABILITY);
}
