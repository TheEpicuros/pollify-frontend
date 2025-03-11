
<?php
/**
 * Poll validation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include validation modules
require_once plugin_dir_path(__FILE__) . 'validation/poll-settings.php';
require_once plugin_dir_path(__FILE__) . 'validation/display-settings.php';
require_once plugin_dir_path(__FILE__) . 'validation/voting-status.php';

// Include core validation functions as they may be needed in other files
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'core/utils/function-exists.php';
