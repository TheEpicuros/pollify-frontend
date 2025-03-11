
<?php
/**
 * Display settings validation functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include core validation functions
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/validation.php';
require_once plugin_dir_path(dirname(dirname(dirname(__FILE__)))) . 'core/utils/function-exists.php';

// Define the current file path for function registration
$current_file = __FILE__;

/**
 * Override poll settings with shortcode attributes
 */
if (pollify_can_define_function('pollify_get_display_settings')) {
    function pollify_get_display_settings($poll_settings, $atts) {
        return [
            'show_results' => $atts['show_results'] !== null ? ($atts['show_results'] === 'yes') : $poll_settings['always_show_results'],
            'show_social' => $atts['show_social'] === 'yes',
            'show_ratings' => $atts['show_ratings'] === 'yes',
            'show_comments' => $atts['show_comments'] !== null ? ($atts['show_comments'] === 'yes') : $poll_settings['allow_comments'],
            'results_display' => $atts['display'] ? $atts['display'] : $poll_settings['results_display'],
            'width' => !empty($atts['width']) ? ' style="width:' . esc_attr($atts['width']) . ';"' : '',
            'align' => ' pollify-align-' . esc_attr($atts['align']),
        ];
    }
    pollify_register_function_path('pollify_get_display_settings', $current_file);
}
