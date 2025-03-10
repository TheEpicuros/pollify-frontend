
<?php
/**
 * Gutenberg blocks integration
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

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
