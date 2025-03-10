
<?php
/**
 * Poll display shortcode [pollify id="123"]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include poll display components
require_once plugin_dir_path(__FILE__) . 'components/poll-validation.php';
require_once plugin_dir_path(__FILE__) . 'components/poll-form.php';
require_once plugin_dir_path(__FILE__) . 'components/poll-output.php';

/**
 * Poll shortcode [pollify id="123"]
 */
function pollify_poll_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'show_results' => null,
        'show_social' => 'yes',
        'show_ratings' => 'yes',
        'show_comments' => null,
        'display' => null, // bar, pie, donut, text
        'width' => '',
        'align' => 'center', // left, center, right
    ), $atts, 'pollify');
    
    $poll_id = absint($atts['id']);
    
    // Validate poll and check permissions
    $poll_validation = pollify_validate_poll_exists($poll_id);
    if (!$poll_validation['valid']) {
        return $poll_validation['message'];
    }
    
    $poll = $poll_validation['poll'];
    
    // Get poll options
    $options = get_post_meta($poll_id, '_poll_options', true);
    
    if (!is_array($options) || count($options) < 2) {
        return '<div class="pollify-error">' . __('This poll has no options.', 'pollify') . '</div>';
    }
    
    // Get poll settings
    $poll_settings = pollify_get_poll_settings($poll_id);
    
    // Override settings from shortcode attributes if provided
    $display_settings = pollify_get_display_settings($poll_settings, $atts);
    
    // Check voting status
    $voting_status = pollify_get_voting_status($poll_id);
    
    // Determine if we should show results
    $display_results = $display_settings['show_results'] || $voting_status['has_voted'] || $voting_status['has_ended'];
    
    return pollify_generate_poll_output(
        $poll_id, 
        $poll, 
        $options, 
        $poll_settings, 
        $display_settings, 
        $voting_status, 
        $display_results
    );
}
