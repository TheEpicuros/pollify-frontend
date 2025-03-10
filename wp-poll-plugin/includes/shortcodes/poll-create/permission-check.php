
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
    // Get global settings
    $settings = get_option('pollify_settings', array());
    $guest_creation = isset($settings['allow_guest_creation']) ? $settings['allow_guest_creation'] : false;
    
    // Allow guests to create polls if enabled in settings
    if (!is_user_logged_in() && !$guest_creation) {
        return array(
            'can_create' => false,
            'message' => '<div class="pollify-error">' . __('You must be logged in to create a poll.', 'pollify') . '</div>'
        );
    }
    
    // For logged-in users, check specific capability
    if (is_user_logged_in() && !current_user_can('create_polls')) {
        return array(
            'can_create' => false,
            'message' => '<div class="pollify-error">' . __('You do not have permission to create polls.', 'pollify') . '</div>'
        );
    }
    
    // Check if daily limit has been reached for this user
    if (is_user_logged_in() && !current_user_can('manage_options')) {
        $daily_limit = isset($settings['daily_poll_limit']) ? intval($settings['daily_poll_limit']) : 0;
        
        if ($daily_limit > 0) {
            $user_id = get_current_user_id();
            $created_today = pollify_count_user_polls_today($user_id);
            
            if ($created_today >= $daily_limit) {
                return array(
                    'can_create' => false,
                    'message' => '<div class="pollify-error">' . sprintf(__('You have reached your daily limit of %d polls.', 'pollify'), $daily_limit) . '</div>'
                );
            }
        }
    }
    
    return array(
        'can_create' => true,
        'message' => ''
    );
}

/**
 * Count how many polls a user has created today
 * 
 * @param int $user_id User ID
 * @return int Number of polls created today
 */
function pollify_count_user_polls_today($user_id) {
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    $args = array(
        'post_type' => 'poll',
        'author' => $user_id,
        'date_query' => array(
            array(
                'after' => $today,
                'before' => $tomorrow,
                'inclusive' => true,
            ),
        ),
        'posts_per_page' => -1,
        'fields' => 'ids',
    );
    
    $query = new WP_Query($args);
    return $query->post_count;
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
 * Filter poll types based on shortcode attributes and user permissions
 * 
 * @param array $available_types All available poll types
 * @param string $types_attr Comma-separated list of allowed types
 * @return array Filtered poll types
 */
function pollify_create_filter_poll_types($available_types, $types_attr) {
    if (empty($types_attr)) {
        // If user is not an admin or editor, limit complex poll types
        if (!current_user_can('edit_others_polls')) {
            // Remove advanced poll types for regular users
            unset($available_types['quiz']);
            unset($available_types['ranked-choice']);
            unset($available_types['multi-stage']);
            
            // Only show image polls for users with upload permissions
            if (!current_user_can('upload_files')) {
                unset($available_types['image-based']);
            }
        }
        
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
