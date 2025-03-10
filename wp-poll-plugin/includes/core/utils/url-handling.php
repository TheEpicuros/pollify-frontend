
<?php
/**
 * URL handling utility functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get current page URL
 */
function pollify_get_current_url() {
    global $wp;
    return home_url(add_query_arg(array(), $wp->request));
}
