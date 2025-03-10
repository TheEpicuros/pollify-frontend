
<?php
/**
 * Poll creation shortcode main file [pollify_create]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the required component files
require_once plugin_dir_path(__FILE__) . 'permission-check.php';
require_once plugin_dir_path(__FILE__) . 'type-selector.php';
require_once plugin_dir_path(__FILE__) . 'form-renderer.php';

/**
 * Poll create shortcode [pollify_create]
 */
function pollify_create_shortcode($atts) {
    $atts = shortcode_atts(array(
        'types' => '', // comma-separated list of poll types to allow
        'redirect' => '', // URL to redirect after creation
    ), $atts, 'pollify_create');
    
    // Check user permissions and get available poll types
    $permission_check = pollify_create_check_permissions();
    if (!$permission_check['can_create']) {
        return $permission_check['message'];
    }
    
    // Get available poll types and filter them if specified
    $available_types = pollify_create_get_poll_types();
    $filtered_types = pollify_create_filter_poll_types($available_types, $atts['types']);
    
    // If no valid types remain, show an error
    if (empty($filtered_types)) {
        return '<div class="pollify-error">' . __('No valid poll types specified.', 'pollify') . '</div>';
    }
    
    // Start output buffering to capture HTML
    ob_start();
    
    // Render the poll creation interface
    echo '<div id="pollify-create-poll" class="pollify-create-poll">';
    echo '<div class="pollify-create-poll-container">';
    
    echo '<h2>' . __('Create a New Poll', 'pollify') . '</h2>';
    
    // Render the poll type selector grid
    pollify_render_poll_type_selector($filtered_types);
    
    // Render the poll creation form (initially hidden)
    pollify_render_poll_creation_form($atts);
    
    echo '</div>'; // Close .pollify-create-poll-container
    echo '</div>'; // Close #pollify-create-poll
    
    // Return the buffered HTML
    return ob_get_clean();
}
