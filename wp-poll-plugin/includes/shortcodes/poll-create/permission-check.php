
<?php
/**
 * Permission check for poll creation
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if the current user has permission to create polls
 * 
 * @return array Status array with 'can_create' boolean and 'message' string
 */
function pollify_create_check_permissions() {
    // Only logged in users can create polls
    if (!is_user_logged_in()) {
        return array(
            'can_create' => false,
            'message' => '<div class="pollify-error">' . __('You must be logged in to create a poll.', 'pollify') . '</div>'
        );
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        return array(
            'can_create' => false,
            'message' => '<div class="pollify-error">' . __('You do not have permission to create polls.', 'pollify') . '</div>'
        );
    }
    
    return array(
        'can_create' => true,
        'message' => ''
    );
}

/**
 * Get available poll types
 * 
 * @return array Array of available poll types
 */
function pollify_create_get_poll_types() {
    $available_types = array();
    $terms = get_terms(array(
        'taxonomy' => 'poll_type',
        'hide_empty' => false,
    ));
    
    foreach ($terms as $term) {
        $available_types[$term->slug] = $term->name;
    }
    
    return $available_types;
}

/**
 * Filter poll types based on shortcode attributes
 * 
 * @param array $available_types All available poll types
 * @param string $types_attr Comma-separated list of allowed types
 * @return array Filtered poll types
 */
function pollify_create_filter_poll_types($available_types, $types_attr) {
    if (empty($types_attr)) {
        return $available_types;
    }
    
    $allowed_types = explode(',', $types_attr);
    $filtered_types = array();
    
    foreach ($allowed_types as $type) {
        $type = trim($type);
        if (isset($available_types[$type])) {
            $filtered_types[$type] = $available_types[$type];
        }
    }
    
    return $filtered_types;
}
