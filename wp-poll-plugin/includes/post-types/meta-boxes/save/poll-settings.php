
<?php
/**
 * Save poll settings meta
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save the poll type and related settings
 */
function pollify_save_poll_type_settings($post_id) {
    // Save poll type
    if (isset($_POST['_poll_type'])) {
        $poll_type = sanitize_text_field($_POST['_poll_type']);
        
        // Set the poll type taxonomy
        wp_set_object_terms($post_id, $poll_type, 'poll_type');
        
        // For image-based polls, save the image IDs
        if ($poll_type === 'image-based' && isset($_POST['poll_option_images'])) {
            $images = array_map('absint', $_POST['poll_option_images']);
            update_post_meta($post_id, '_poll_option_images', $images);
        }
        
        // For interactive polls, save the settings
        if ($poll_type === 'interactive' && isset($_POST['_poll_interactive_settings'])) {
            $interactive_settings = array();
            
            // Sanitize interaction type
            if (isset($_POST['_poll_interactive_settings']['interaction_type'])) {
                $interactive_settings['interaction_type'] = sanitize_text_field($_POST['_poll_interactive_settings']['interaction_type']);
            }
            
            // Sanitize slider settings
            if (isset($_POST['_poll_interactive_settings']['min'])) {
                $interactive_settings['min'] = intval($_POST['_poll_interactive_settings']['min']);
            }
            
            if (isset($_POST['_poll_interactive_settings']['max'])) {
                $interactive_settings['max'] = intval($_POST['_poll_interactive_settings']['max']);
            }
            
            if (isset($_POST['_poll_interactive_settings']['step'])) {
                $interactive_settings['step'] = intval($_POST['_poll_interactive_settings']['step']);
            }
            
            if (isset($_POST['_poll_interactive_settings']['default'])) {
                $interactive_settings['default'] = intval($_POST['_poll_interactive_settings']['default']);
            }
            
            // Sanitize budget settings
            if (isset($_POST['_poll_interactive_settings']['total_budget'])) {
                $interactive_settings['total_budget'] = intval($_POST['_poll_interactive_settings']['total_budget']);
            }
            
            if (isset($_POST['_poll_interactive_settings']['min_allocation'])) {
                $interactive_settings['min_allocation'] = intval($_POST['_poll_interactive_settings']['min_allocation']);
            }
            
            if (isset($_POST['_poll_interactive_settings']['max_allocation'])) {
                $interactive_settings['max_allocation'] = intval($_POST['_poll_interactive_settings']['max_allocation']);
            }
            
            // Sanitize map settings
            if (isset($_POST['_poll_interactive_settings']['map_type'])) {
                $interactive_settings['map_type'] = sanitize_text_field($_POST['_poll_interactive_settings']['map_type']);
            }
            
            update_post_meta($post_id, '_poll_interactive_settings', $interactive_settings);
        }
        
        // For multi-stage polls, save the stages
        if ($poll_type === 'multi-stage' && isset($_POST['poll_stages'])) {
            $stages = array_map('sanitize_text_field', $_POST['poll_stages']);
            $stages = array_filter($stages); // Remove empty stages
            update_post_meta($post_id, '_poll_stages', $stages);
            
            // Current stage (default to 0 if not set)
            $current_stage = isset($_POST['_poll_current_stage']) ? absint($_POST['_poll_current_stage']) : 0;
            update_post_meta($post_id, '_poll_current_stage', $current_stage);
        }
    }
}

/**
 * Save general poll settings
 */
function pollify_save_general_settings($post_id) {
    // Save other poll settings
    if (isset($_POST['_poll_end_date'])) {
        update_post_meta($post_id, '_poll_end_date', sanitize_text_field($_POST['_poll_end_date']));
    }
    
    update_post_meta($post_id, '_poll_show_results', isset($_POST['_poll_show_results']) ? '1' : '0');
    
    if (isset($_POST['_poll_results_display'])) {
        update_post_meta($post_id, '_poll_results_display', sanitize_text_field($_POST['_poll_results_display']));
    }
    
    update_post_meta($post_id, '_poll_allow_comments', isset($_POST['_poll_allow_comments']) ? '1' : '0');
    
    if (isset($_POST['_poll_allowed_roles'])) {
        $allowed_roles = array_map('sanitize_text_field', $_POST['_poll_allowed_roles']);
        update_post_meta($post_id, '_poll_allowed_roles', $allowed_roles);
    }
}
