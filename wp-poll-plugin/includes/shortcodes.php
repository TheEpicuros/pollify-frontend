
<?php
/**
 * Shortcodes for the Pollify plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include shortcode utility files
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/poll-validation.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/display-helpers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/ip-detection.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/results-renderer.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/social-sharing.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/comments-system.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/rating-system.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/utils/special-poll-renderers.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-utils.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-display.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-create.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-browse.php';

// Include core shortcode functionality
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/core.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/gutenberg-blocks.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/editor-integration.php';

// Register shortcodes
add_shortcode('pollify', 'pollify_poll_shortcode');
add_shortcode('pollify_create', 'pollify_create_shortcode');
add_shortcode('pollify_browse', 'pollify_browse_shortcode');

// Register Gutenberg blocks
add_action('init', 'pollify_register_gutenberg_blocks');

// Add TinyMCE buttons
add_action('admin_init', 'pollify_add_mce_button');
add_action('admin_footer', 'pollify_mce_button_data');

// Add help tabs
add_action('admin_head', 'pollify_add_help_tab');

/**
 * Generate and get poll shortcode
 *
 * @param int $poll_id The poll ID
 * @param array $args Optional attributes for the shortcode
 * @return string The generated shortcode
 */
function pollify_get_poll_shortcode($poll_id, $args = array()) {
    $shortcode = '[pollify id="' . $poll_id . '"';
    
    foreach ($args as $key => $value) {
        $shortcode .= ' ' . $key . '="' . esc_attr($value) . '"';
    }
    
    $shortcode .= ']';
    
    return $shortcode;
}

/**
 * Copy shortcode to clipboard JS
 */
function pollify_shortcode_clipboard_js() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.pollify-copy-shortcode').on('click', function(e) {
            e.preventDefault();
            
            var shortcode = $(this).data('shortcode');
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(shortcode).select();
            document.execCommand('copy');
            tempInput.remove();
            
            // Show success message
            var $button = $(this);
            var originalText = $button.text();
            $button.text('Copied!');
            setTimeout(function() {
                $button.text(originalText);
            }, 2000);
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'pollify_shortcode_clipboard_js');
add_action('admin_footer', 'pollify_shortcode_clipboard_js');
