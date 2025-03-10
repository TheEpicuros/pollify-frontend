<?php
/**
 * Shortcodes for the Pollify plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include shortcode files
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-utils.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-display.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-create.php';
require_once POLLIFY_PLUGIN_DIR . 'includes/shortcodes/poll-browse.php';

// Register shortcodes
add_shortcode('pollify', 'pollify_poll_shortcode');
add_shortcode('pollify_create', 'pollify_create_shortcode');
add_shortcode('pollify_browse', 'pollify_browse_shortcode');

/**
 * Register shortcode in Gutenberg blocks
 */
function pollify_register_gutenberg_blocks() {
    // Skip if Gutenberg is not available
    if (!function_exists('register_block_type')) {
        return;
    }
    
    // Register script
    wp_register_script(
        'pollify-blocks',
        POLLIFY_PLUGIN_URL . 'assets/js/blocks.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
        POLLIFY_VERSION
    );
    
    // Register poll display block
    register_block_type('pollify/poll', array(
        'editor_script' => 'pollify-blocks',
        'attributes' => array(
            'pollId' => array(
                'type' => 'string',
                'default' => ''
            ),
            'showResults' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'showSocial' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showRatings' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showComments' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'display' => array(
                'type' => 'string',
                'default' => 'bar'
            ),
            'width' => array(
                'type' => 'string',
                'default' => ''
            ),
            'align' => array(
                'type' => 'string',
                'default' => 'center'
            )
        ),
        'render_callback' => 'pollify_render_poll_block'
    ));
    
    // Register poll browse block
    register_block_type('pollify/browse', array(
        'editor_script' => 'pollify-blocks',
        'attributes' => array(
            'perPage' => array(
                'type' => 'number',
                'default' => 10
            ),
            'orderBy' => array(
                'type' => 'string',
                'default' => 'date'
            ),
            'order' => array(
                'type' => 'string',
                'default' => 'desc'
            ),
            'showFilters' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'filterBy' => array(
                'type' => 'array',
                'default' => []
            ),
            'pollType' => array(
                'type' => 'string',
                'default' => ''
            )
        ),
        'render_callback' => 'pollify_render_browse_block'
    ));
    
    // Register poll create block
    register_block_type('pollify/create', array(
        'editor_script' => 'pollify-blocks',
        'attributes' => array(
            'types' => array(
                'type' => 'string',
                'default' => ''
            ),
            'redirect' => array(
                'type' => 'string',
                'default' => ''
            ),
            'defaultType' => array(
                'type' => 'string',
                'default' => 'multiple-choice'
            )
        ),
        'render_callback' => 'pollify_render_create_block'
    ));
    
    // Pass poll data to blocks
    wp_localize_script('pollify-blocks', 'pollifyBlocks', array(
        'polls' => pollify_get_polls_for_blocks(),
        'pollTypes' => pollify_get_poll_types()
    ));
}
add_action('init', 'pollify_register_gutenberg_blocks');

/**
 * Render poll block
 */
function pollify_render_poll_block($attributes) {
    $shortcode_atts = array(
        'id' => isset($attributes['pollId']) ? $attributes['pollId'] : 0,
        'show_results' => isset($attributes['showResults']) ? ($attributes['showResults'] ? 'yes' : 'no') : null,
        'show_social' => isset($attributes['showSocial']) ? ($attributes['showSocial'] ? 'yes' : 'no') : 'yes',
        'show_ratings' => isset($attributes['showRatings']) ? ($attributes['showRatings'] ? 'yes' : 'no') : 'yes',
        'show_comments' => isset($attributes['showComments']) ? ($attributes['showComments'] ? 'yes' : 'no') : null,
        'display' => isset($attributes['display']) ? $attributes['display'] : null,
        'width' => isset($attributes['width']) ? $attributes['width'] : '',
        'align' => isset($attributes['align']) ? $attributes['align'] : 'center'
    );
    
    $shortcode_string = '';
    foreach ($shortcode_atts as $key => $value) {
        if ($value !== null && $value !== '') {
            $shortcode_string .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
    }
    
    return do_shortcode('[pollify' . $shortcode_string . ']');
}

/**
 * Render browse block
 */
function pollify_render_browse_block($attributes) {
    $shortcode_atts = array(
        'per_page' => isset($attributes['perPage']) ? $attributes['perPage'] : 10,
        'orderby' => isset($attributes['orderBy']) ? $attributes['orderBy'] : 'date',
        'order' => isset($attributes['order']) ? $attributes['order'] : 'desc',
        'show_filters' => isset($attributes['showFilters']) ? ($attributes['showFilters'] ? 'yes' : 'no') : 'yes',
        'filter_by' => isset($attributes['filterBy']) ? implode(',', $attributes['filterBy']) : '',
        'poll_type' => isset($attributes['pollType']) ? $attributes['pollType'] : ''
    );
    
    $shortcode_string = '';
    foreach ($shortcode_atts as $key => $value) {
        if ($value !== null && $value !== '') {
            $shortcode_string .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
    }
    
    return do_shortcode('[pollify_browse' . $shortcode_string . ']');
}

/**
 * Render create block
 */
function pollify_render_create_block($attributes) {
    $shortcode_atts = array(
        'types' => isset($attributes['types']) ? $attributes['types'] : '',
        'redirect' => isset($attributes['redirect']) ? $attributes['redirect'] : ''
    );
    
    $shortcode_string = '';
    foreach ($shortcode_atts as $key => $value) {
        if ($value !== null && $value !== '') {
            $shortcode_string .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
    }
    
    return do_shortcode('[pollify_create' . $shortcode_string . ']');
}

/**
 * Get polls for Gutenberg blocks
 */
function pollify_get_polls_for_blocks() {
    $args = array(
        'post_type' => 'poll',
        'posts_per_page' => 100,
        'post_status' => 'publish'
    );
    
    $polls = get_posts($args);
    $formatted_polls = array();
    
    foreach ($polls as $poll) {
        // Get poll type
        $poll_type = pollify_get_poll_type($poll->ID);
        
        $formatted_polls[] = array(
            'id' => $poll->ID,
            'title' => get_the_title($poll),
            'type' => $poll_type,
            'votes' => pollify_get_total_votes($poll->ID)
        );
    }
    
    return $formatted_polls;
}

/**
 * Get poll types
 */
function pollify_get_poll_types() {
    $terms = get_terms(array(
        'taxonomy' => 'poll_type',
        'hide_empty' => false,
    ));
    
    $poll_types = array();
    
    foreach ($terms as $term) {
        $poll_types[$term->slug] = array(
            'name' => $term->name,
            'description' => $term->description
        );
    }
    
    // Fallback if no terms found
    if (empty($poll_types)) {
        return array(
            'binary' => array(
                'name' => __('Yes/No', 'pollify'),
                'description' => __('Simple yes/no questions', 'pollify')
            ),
            'multiple-choice' => array(
                'name' => __('Multiple Choice', 'pollify'),
                'description' => __('Select one from multiple options', 'pollify')
            ),
            'check-all' => array(
                'name' => __('Multiple Answers', 'pollify'), 
                'description' => __('Select multiple options', 'pollify')
            ),
            'image-based' => array(
                'name' => __('Image Based', 'pollify'),
                'description' => __('Visual polls with images', 'pollify')
            ),
            'rating-scale' => array(
                'name' => __('Rating Scale', 'pollify'),
                'description' => __('Rate on a scale', 'pollify')
            )
        );
    }
    
    return $poll_types;
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
add_action('admin_init', 'pollify_add_mce_button');

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
add_action('admin_footer', 'pollify_mce_button_data');

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
add_action('admin_head', 'pollify_add_help_tab');
