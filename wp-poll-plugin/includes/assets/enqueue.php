
<?php
/**
 * Enqueue scripts and styles
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Frontend scripts and styles
add_action('wp_enqueue_scripts', 'pollify_enqueue_scripts');

// Admin scripts and styles
add_action('admin_enqueue_scripts', 'pollify_admin_enqueue_scripts');

/**
 * Enqueue scripts and styles for the frontend
 */
function pollify_enqueue_scripts() {
    // Enqueue jQuery
    wp_enqueue_script('jquery');
    
    // Enqueue all CSS files
    $css_files = array(
        'pollify-base' => 'base.css',
        'pollify-options' => 'options.css',
        'pollify-results' => 'results.css',
        'pollify-card-list' => 'card-list.css',
        'pollify-create-form' => 'create-form.css',
    );
    
    foreach ($css_files as $handle => $filename) {
        wp_enqueue_style(
            $handle,
            POLLIFY_PLUGIN_URL . 'assets/css/' . $filename,
            array(),
            POLLIFY_VERSION
        );
    }
    
    // Main CSS file (for backward compatibility)
    wp_enqueue_style(
        'pollify-styles', 
        POLLIFY_PLUGIN_URL . 'assets/css/pollify.css', 
        array_keys($css_files), 
        POLLIFY_VERSION
    );
    
    // Enqueue plugin JS
    wp_enqueue_script(
        'pollify-script', 
        POLLIFY_PLUGIN_URL . 'assets/js/pollify.js', 
        array('jquery'), 
        POLLIFY_VERSION, 
        true
    );
    
    // Get settings
    $settings = get_option('pollify_settings', array());
    
    // Pass WordPress data to JS
    wp_localize_script('pollify-script', 'pollifyData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('pollify-nonce'),
        'siteUrl' => get_site_url(),
        'features' => array(
            'animatedProgress' => isset($settings['loading_animation']) ? (bool) $settings['loading_animation'] : true,
            'allowGuests' => isset($settings['allow_guests']) ? (bool) $settings['allow_guests'] : true,
            'showResultsBeforeVote' => isset($settings['show_results_before_vote']) ? (bool) $settings['show_results_before_vote'] : false,
            'enableComments' => isset($settings['enable_comments']) ? (bool) $settings['enable_comments'] : true,
            'enableRatings' => isset($settings['enable_ratings']) ? (bool) $settings['enable_ratings'] : true,
            'enableSocialSharing' => isset($settings['enable_social_sharing']) ? (bool) $settings['enable_social_sharing'] : true
        ),
        'error' => array(
            'generic' => __('An error occurred. Please try again.', 'pollify'),
            'ajaxFailed' => __('Failed to communicate with the server.', 'pollify'),
            'alreadyVoted' => __('You have already voted on this poll.', 'pollify'),
            'invalidOption' => __('Please select a valid option.', 'pollify'),
            'notLoggedIn' => __('You must be logged in to perform this action.', 'pollify')
        )
    ));
}

/**
 * Enqueue scripts and styles for the admin area
 */
function pollify_admin_enqueue_scripts($hook) {
    // Only load on our plugin pages
    if (strpos($hook, 'pollify') === false && get_post_type() !== 'poll') {
        return;
    }
    
    // Enqueue admin CSS
    wp_enqueue_style(
        'pollify-admin-styles', 
        POLLIFY_PLUGIN_URL . 'assets/css/pollify-admin.css', 
        array(), 
        POLLIFY_VERSION
    );
    
    // Enqueue admin JS
    wp_enqueue_script(
        'pollify-admin-script', 
        POLLIFY_PLUGIN_URL . 'assets/js/pollify-admin.js', 
        array('jquery', 'wp-color-picker'), 
        POLLIFY_VERSION, 
        true
    );
    
    // Add color picker
    wp_enqueue_style('wp-color-picker');
}
