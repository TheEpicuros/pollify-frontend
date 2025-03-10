
<?php
/**
 * Utility functions for poll shortcodes
 * 
 * This is a lightweight file that includes all the utility modules
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include utility modules
require_once plugin_dir_path(__FILE__) . 'utils/ip-detection.php';
require_once plugin_dir_path(__FILE__) . 'utils/poll-validation.php';
require_once plugin_dir_path(__FILE__) . 'utils/display-helpers.php';
require_once plugin_dir_path(__FILE__) . 'utils/social-sharing.php';
require_once plugin_dir_path(__FILE__) . 'utils/rating-system.php';
require_once plugin_dir_path(__FILE__) . 'utils/comments-system.php';
require_once plugin_dir_path(__FILE__) . 'utils/results-renderer.php';
require_once plugin_dir_path(__FILE__) . 'utils/special-poll-renderers.php';
