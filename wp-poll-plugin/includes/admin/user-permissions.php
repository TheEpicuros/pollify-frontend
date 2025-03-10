
<?php
/**
 * User permissions management page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the user permissions page
 */
function pollify_user_permissions_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Process form submission
    if (isset($_POST['pollify_save_permissions'])) {
        // Verify nonce
        if (isset($_POST['pollify_permissions_nonce']) && wp_verify_nonce($_POST['pollify_permissions_nonce'], 'pollify_save_permissions')) {
            
            // Update role permissions
            $roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
            $capabilities = array(
                'create_polls' => __('Create Polls', 'pollify'),
                'edit_polls' => __('Edit Own Polls', 'pollify'),
                'edit_others_polls' => __('Edit Others\' Polls', 'pollify'),
                'delete_polls' => __('Delete Own Polls', 'pollify'),
                'delete_others_polls' => __('Delete Others\' Polls', 'pollify'),
                'publish_polls' => __('Publish Polls', 'pollify'),
                'read_private_polls' => __('View Private Polls', 'pollify'),
                'view_poll_analytics' => __('View Poll Analytics', 'pollify'),
                'moderate_poll_comments' => __('Moderate Poll Comments', 'pollify'),
                'manage_poll_settings' => __('Manage Poll Settings', 'pollify')
            );
            
            foreach ($roles as $role_name) {
                $role = get_role($role_name);
                
                if (!$role) {
                    continue;
                }
                
                foreach ($capabilities as $cap => $label) {
                    $cap_key = $role_name . '_' . $cap;
                    
                    if (isset($_POST[$cap_key])) {
                        $role->add_cap($cap);
                    } else {
                        // Don't remove critical capabilities from administrators
                        if ($role_name !== 'administrator' || !in_array($cap, array('publish_polls', 'manage_poll_settings'))) {
                            $role->remove_cap($cap);
                        }
                    }
                }
            }
            
            // Save custom settings
            $custom_settings = array(
                'allow_guest_creation' => isset($_POST['allow_guest_creation']) ? 1 : 0,
                'guest_vote_without_login' => isset($_POST['guest_vote_without_login']) ? 1 : 0,
                'daily_poll_limit' => isset($_POST['daily_poll_limit']) ? absint($_POST['daily_poll_limit']) : 0,
                'require_moderation' => isset($_POST['require_moderation']) ? 1 : 0,
                'enable_poll_reports' => isset($_POST['enable_poll_reports']) ? 1 : 0
            );
            
            // Get existing settings and merge
            $current_settings = get_option('pollify_settings', array());
            $updated_settings = array_merge($current_settings, $custom_settings);
            update_option('pollify_settings', $updated_settings);
            
            // Show success message
            echo '<div class="notice notice-success is-dismissible"><p>' . __('User permissions saved successfully.', 'pollify') . '</p></div>';
        } else {
            // Show error message
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Security check failed. Please try again.', 'pollify') . '</p></div>';
        }
    }
    
    // Get current settings
    $settings = get_option('pollify_settings', array());
    $allow_guest_creation = isset($settings['allow_guest_creation']) ? $settings['allow_guest_creation'] : 0;
    $guest_vote_without_login = isset($settings['guest_vote_without_login']) ? $settings['guest_vote_without_login'] : 0;
    $daily_poll_limit = isset($settings['daily_poll_limit']) ? $settings['daily_poll_limit'] : 0;
    $require_moderation = isset($settings['require_moderation']) ? $settings['require_moderation'] : 0;
    $enable_poll_reports = isset($settings['enable_poll_reports']) ? $settings['enable_poll_reports'] : 0;
    
    // Define the roles and capabilities to display
    $roles = array(
        'administrator' => __('Administrator', 'pollify'),
        'editor' => __('Editor', 'pollify'),
        'author' => __('Author', 'pollify'),
        'contributor' => __('Contributor', 'pollify'),
        'subscriber' => __('Subscriber', 'pollify')
    );
    
    $capabilities = array(
        'create_polls' => __('Create Polls', 'pollify'),
        'edit_polls' => __('Edit Own Polls', 'pollify'),
        'edit_others_polls' => __('Edit Others\' Polls', 'pollify'),
        'delete_polls' => __('Delete Own Polls', 'pollify'),
        'delete_others_polls' => __('Delete Others\' Polls', 'pollify'),
        'publish_polls' => __('Publish Polls', 'pollify'),
        'read_private_polls' => __('View Private Polls', 'pollify'),
        'view_poll_analytics' => __('View Poll Analytics', 'pollify'),
        'moderate_poll_comments' => __('Moderate Poll Comments', 'pollify'),
        'manage_poll_settings' => __('Manage Poll Settings', 'pollify')
    );
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('pollify_save_permissions', 'pollify_permissions_nonce'); ?>
            
            <div class="pollify-settings-tabs">
                <div class="pollify-settings-nav">
                    <a href="#role-permissions" class="active"><?php _e('Role Permissions', 'pollify'); ?></a>
                    <a href="#general-settings"><?php _e('General Settings', 'pollify'); ?></a>
                </div>
                
                <div class="pollify-settings-content">
                    <!-- Role Permissions -->
                    <div id="role-permissions" class="pollify-settings-section active">
                        <h2><?php _e('Role Permissions', 'pollify'); ?></h2>
                        <p><?php _e('Control which user roles have access to various poll features.', 'pollify'); ?></p>
                        
                        <table class="form-table widefat pollify-permissions-table">
                            <thead>
                                <tr>
                                    <th><?php _e('Capability', 'pollify'); ?></th>
                                    <?php foreach ($roles as $role_name => $role_label): ?>
                                        <th><?php echo esc_html($role_label); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($capabilities as $cap => $label): ?>
                                    <tr>
                                        <td><?php echo esc_html($label); ?></td>
                                        <?php foreach ($roles as $role_name => $role_label): 
                                            $role = get_role($role_name);
                                            $has_cap = $role && $role->has_cap($cap);
                                            $disabled = ($role_name === 'administrator' && in_array($cap, array('publish_polls', 'manage_poll_settings')));
                                        ?>
                                            <td>
                                                <input 
                                                    type="checkbox" 
                                                    name="<?php echo esc_attr($role_name . '_' . $cap); ?>" 
                                                    value="1" 
                                                    <?php checked($has_cap); ?> 
                                                    <?php disabled($disabled); ?>
                                                >
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- General Settings -->
                    <div id="general-settings" class="pollify-settings-section">
                        <h2><?php _e('General Permission Settings', 'pollify'); ?></h2>
                        
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php _e('Guest Creation', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="allow_guest_creation" <?php checked($allow_guest_creation); ?>>
                                        <?php _e('Allow non-logged-in users to create polls', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('If enabled, anonymous users can create polls. Not recommended for public sites.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Guest Voting', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="guest_vote_without_login" <?php checked($guest_vote_without_login); ?>>
                                        <?php _e('Allow non-logged-in users to vote on polls', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('This is a global setting. Individual polls can still require login regardless of this setting.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Daily Poll Limit', 'pollify'); ?></th>
                                <td>
                                    <input type="number" name="daily_poll_limit" min="0" value="<?php echo esc_attr($daily_poll_limit); ?>">
                                    <p class="description"><?php _e('Maximum number of polls a user can create per day (0 = unlimited). Does not apply to administrators.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Content Moderation', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="require_moderation" <?php checked($require_moderation); ?>>
                                        <?php _e('Require moderation for user-created polls', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('If enabled, polls created by non-administrators will be set to draft status for review.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Poll Reporting', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_poll_reports" <?php checked($enable_poll_reports); ?>>
                                        <?php _e('Enable users to report inappropriate polls', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('Allows users to flag polls that violate site rules or contain inappropriate content.', 'pollify'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <p class="submit">
                <input type="submit" name="pollify_save_permissions" class="button button-primary" value="<?php _e('Save Permissions', 'pollify'); ?>">
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.pollify-settings-nav a').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and sections
            $('.pollify-settings-nav a').removeClass('active');
            $('.pollify-settings-section').removeClass('active');
            
            // Add active class to clicked tab and corresponding section
            $(this).addClass('active');
            $($(this).attr('href')).addClass('active');
        });
    });
    </script>
    
    <style>
    .pollify-settings-tabs {
        margin-top: 20px;
    }
    
    .pollify-settings-nav {
        display: flex;
        border-bottom: 1px solid #ccc;
        margin-bottom: 20px;
    }
    
    .pollify-settings-nav a {
        padding: 10px 15px;
        text-decoration: none;
        border: 1px solid transparent;
        border-bottom: none;
        margin-bottom: -1px;
        font-weight: 500;
    }
    
    .pollify-settings-nav a.active {
        border-color: #ccc;
        border-bottom-color: #f0f0f1;
        background: #f0f0f1;
    }
    
    .pollify-settings-section {
        display: none;
    }
    
    .pollify-settings-section.active {
        display: block;
    }
    
    .pollify-permissions-table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }
    
    .pollify-permissions-table th,
    .pollify-permissions-table td {
        text-align: center;
        padding: 8px;
    }
    
    .pollify-permissions-table th:first-child,
    .pollify-permissions-table td:first-child {
        text-align: left;
        width: 30%;
    }
    
    .pollify-permissions-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    </style>
    <?php
}
