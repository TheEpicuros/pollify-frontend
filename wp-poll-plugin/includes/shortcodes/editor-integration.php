
<?php
/**
 * TinyMCE and editor integration for shortcodes
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add TinyMCE button for shortcodes
 */
function pollify_add_mce_button() {
    // Check user permissions
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    
    // Check if WYSIWYG is enabled
    if ('true' == get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'pollify_add_mce_plugin');
        add_filter('mce_buttons', 'pollify_register_mce_button');
    }
}

/**
 * Register new button in the editor
 */
function pollify_register_mce_button($buttons) {
    array_push($buttons, 'pollify_shortcodes');
    return $buttons;
}

/**
 * Add the plugin JS for the editor button
 */
function pollify_add_mce_plugin($plugin_array) {
    $plugin_array['pollify_shortcodes'] = POLLIFY_PLUGIN_URL . 'assets/js/mce-button.js';
    return $plugin_array;
}

/**
 * Add data for TinyMCE button
 */
function pollify_mce_button_data() {
    ?>
    <script type="text/javascript">
    var pollifyShortcodeData = {
        polls: <?php echo json_encode(pollify_get_polls_for_blocks()); ?>,
        pollTypes: <?php echo json_encode(pollify_get_poll_types()); ?>,
        l10n: {
            title: '<?php _e('Insert Pollify Shortcode', 'pollify'); ?>',
            pollTitle: '<?php _e('Select Poll', 'pollify'); ?>',
            browseTitle: '<?php _e('Browse Polls', 'pollify'); ?>',
            createTitle: '<?php _e('Create Poll Form', 'pollify'); ?>',
            insert: '<?php _e('Insert', 'pollify'); ?>',
            cancel: '<?php _e('Cancel', 'pollify'); ?>'
        }
    };
    </script>
    <?php
}

/**
 * Add shortcode documentation to help tab
 */
function pollify_add_help_tab() {
    $screen = get_current_screen();
    
    // Only add to post and page edit screens
    if (!in_array($screen->id, array('post', 'page'))) {
        return;
    }
    
    $screen->add_help_tab(array(
        'id' => 'pollify_shortcodes_help',
        'title' => __('Pollify Shortcodes', 'pollify'),
        'content' => '<h2>' . __('Pollify Shortcodes', 'pollify') . '</h2>' .
            '<p>' . __('You can use the following shortcodes to display polls on your site:', 'pollify') . '</p>' .
            '<ul>' .
            '<li><code>[pollify id="123"]</code> - ' . __('Display a specific poll', 'pollify') . '</li>' .
            '<li><code>[pollify_create]</code> - ' . __('Display a poll creation form', 'pollify') . '</li>' .
            '<li><code>[pollify_browse]</code> - ' . __('Display a list of polls', 'pollify') . '</li>' .
            '</ul>' .
            '<h3>' . __('Poll Display Options', 'pollify') . '</h3>' .
            '<ul>' .
            '<li><code>show_results</code> - ' . __('Show results before voting (yes/no)', 'pollify') . '</li>' .
            '<li><code>show_social</code> - ' . __('Show social sharing buttons (yes/no)', 'pollify') . '</li>' .
            '<li><code>show_ratings</code> - ' . __('Show poll rating buttons (yes/no)', 'pollify') . '</li>' .
            '<li><code>show_comments</code> - ' . __('Show comments section (yes/no)', 'pollify') . '</li>' .
            '<li><code>display</code> - ' . __('Results display type (bar, pie, donut, text)', 'pollify') . '</li>' .
            '<li><code>width</code> - ' . __('Poll width (e.g., 100%, 500px)', 'pollify') . '</li>' .
            '<li><code>align</code> - ' . __('Poll alignment (left, center, right)', 'pollify') . '</li>' .
            '</ul>' .
            '<h3>' . __('Poll Types', 'pollify') . '</h3>' .
            '<p>' . __('When creating a poll, you can choose from these poll types:', 'pollify') . '</p>' .
            '<ul>' .
            '<li><strong>' . __('Binary Choice', 'pollify') . '</strong> - ' . __('Simple yes/no or either/or questions', 'pollify') . '</li>' .
            '<li><strong>' . __('Multiple Choice', 'pollify') . '</strong> - ' . __('Select one option from multiple choices', 'pollify') . '</li>' .
            '<li><strong>' . __('Multiple Answers', 'pollify') . '</strong> - ' . __('Select multiple options that apply', 'pollify') . '</li>' .
            '<li><strong>' . __('Image Based', 'pollify') . '</strong> - ' . __('Use images as answer options', 'pollify') . '</li>' .
            '<li><strong>' . __('Rating Scale', 'pollify') . '</strong> - ' . __('Rate on a numerical scale', 'pollify') . '</li>' .
            '</ul>'
    ));
}
