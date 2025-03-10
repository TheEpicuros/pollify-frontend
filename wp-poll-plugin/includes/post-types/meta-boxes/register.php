
<?php
/**
 * Poll meta boxes registration
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add meta boxes to the poll post type
 */
function pollify_add_meta_boxes() {
    add_meta_box(
        'pollify_poll_options',
        __('Poll Options', 'pollify'),
        'pollify_poll_options_callback',
        'poll',
        'normal',
        'high'
    );
    
    add_meta_box(
        'pollify_poll_settings',
        __('Poll Settings', 'pollify'),
        'pollify_poll_settings_callback',
        'poll',
        'side',
        'default'
    );
}

// Register the meta box callbacks with the save_post_poll action
add_action('save_post_poll', 'pollify_save_poll_meta', 10, 2);
