
<?php
/**
 * Poll meta admin notices
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display admin notices for poll errors
 */
function pollify_admin_notices() {
    if (isset($_GET['pollify_error']) && $_GET['pollify_error'] === 'options') {
        ?>
        <div class="error">
            <p><?php _e('A poll must have at least two options. Please add more options.', 'pollify'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'pollify_admin_notices');
