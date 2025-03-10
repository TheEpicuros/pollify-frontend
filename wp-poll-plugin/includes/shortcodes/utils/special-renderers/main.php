
<?php
/**
 * Special poll type renderers main file
 * 
 * This file includes all specialized poll type renderers.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include all specialized renderers
require_once plugin_dir_path(__FILE__) . 'open-ended.php';
require_once plugin_dir_path(__FILE__) . 'ranked-choice.php';
require_once plugin_dir_path(__FILE__) . 'rating-scale.php';
