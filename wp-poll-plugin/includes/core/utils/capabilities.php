
<?php
/**
 * Poll capabilities and permission management
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set up poll capabilities for user roles
 * Called during plugin activation
 */
function pollify_setup_capabilities() {
    // Add capabilities to administrators
    $admin_role = get_role('administrator');
    
    if ($admin_role) {
        // Poll management capabilities
        $admin_role->add_cap('create_polls');
        $admin_role->add_cap('edit_polls');
        $admin_role->add_cap('edit_others_polls');
        $admin_role->add_cap('delete_polls');
        $admin_role->add_cap('delete_others_polls');
        $admin_role->add_cap('publish_polls');
        $admin_role->add_cap('read_private_polls');
        
        // Admin capabilities
        $admin_role->add_cap('manage_poll_settings');
        $admin_role->add_cap('view_poll_analytics');
        $admin_role->add_cap('moderate_poll_comments');
    }
    
    // Add capabilities to editors
    $editor_role = get_role('editor');
    
    if ($editor_role) {
        // Poll management capabilities
        $editor_role->add_cap('create_polls');
        $editor_role->add_cap('edit_polls');
        $editor_role->add_cap('edit_others_polls');
        $editor_role->add_cap('delete_polls');
        $editor_role->add_cap('publish_polls');
        $editor_role->add_cap('read_private_polls');
        
        // Limited admin capabilities
        $editor_role->add_cap('view_poll_analytics');
        $editor_role->add_cap('moderate_poll_comments');
    }
    
    // Add capabilities to authors
    $author_role = get_role('author');
    
    if ($author_role) {
        // Basic poll capabilities
        $author_role->add_cap('create_polls');
        $author_role->add_cap('edit_polls');
        $author_role->add_cap('delete_polls');
        $author_role->add_cap('publish_polls');
    }
    
    // Add capabilities to contributors
    $contributor_role = get_role('contributor');
    
    if ($contributor_role) {
        // Very limited poll capabilities
        $contributor_role->add_cap('create_polls');
        $contributor_role->add_cap('edit_polls');
        $contributor_role->add_cap('delete_polls');
    }
}

/**
 * Remove poll capabilities from user roles
 * Called during plugin deactivation
 */
function pollify_remove_capabilities() {
    $roles = array('administrator', 'editor', 'author', 'contributor');
    
    $capabilities = array(
        'create_polls',
        'edit_polls',
        'edit_others_polls',
        'delete_polls',
        'delete_others_polls',
        'publish_polls',
        'read_private_polls',
        'manage_poll_settings',
        'view_poll_analytics',
        'moderate_poll_comments'
    );
    
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        
        if (!$role) {
            continue;
        }
        
        foreach ($capabilities as $cap) {
            $role->remove_cap($cap);
        }
    }
}

/**
 * Check if current user can manage poll settings
 * 
 * @return bool Whether the current user can manage poll settings
 */
function pollify_current_user_can_manage_settings() {
    return current_user_can('manage_poll_settings');
}

/**
 * Check if current user can create polls
 * 
 * @return bool Whether the current user can create polls
 */
function pollify_current_user_can_create_polls() {
    return current_user_can('create_polls');
}

/**
 * Check if current user can edit a specific poll
 * 
 * @param int $poll_id The poll ID
 * @return bool Whether the current user can edit the poll
 */
function pollify_current_user_can_edit_poll($poll_id) {
    $post = get_post($poll_id);
    
    if (!$post) {
        return false;
    }
    
    // Administrators and editors can edit any poll
    if (current_user_can('edit_others_polls')) {
        return true;
    }
    
    // Authors and contributors can only edit their own polls
    if (current_user_can('edit_polls') && $post->post_author == get_current_user_id()) {
        return true;
    }
    
    return false;
}

/**
 * Check if current user can delete a specific poll
 * 
 * @param int $poll_id The poll ID
 * @return bool Whether the current user can delete the poll
 */
function pollify_current_user_can_delete_poll($poll_id) {
    $post = get_post($poll_id);
    
    if (!$post) {
        return false;
    }
    
    // Administrators and editors can delete any poll
    if (current_user_can('delete_others_polls')) {
        return true;
    }
    
    // Authors and contributors can only delete their own polls
    if (current_user_can('delete_polls') && $post->post_author == get_current_user_id()) {
        return true;
    }
    
    return false;
}
