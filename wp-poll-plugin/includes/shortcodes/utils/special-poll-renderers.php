
<?php
/**
 * Special poll type results renderers
 * 
 * This file is kept for backward compatibility.
 * It includes the new specialized renderer modules.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the specialized renderers main file which loads all renderers
require_once plugin_dir_path(__FILE__) . 'special-renderers/main.php';
