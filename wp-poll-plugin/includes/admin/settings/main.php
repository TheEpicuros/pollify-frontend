
<?php
/**
 * Admin settings page main file for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include settings components
require_once plugin_dir_path(__FILE__) . 'general-settings.php';
require_once plugin_dir_path(__FILE__) . 'display-settings.php';
require_once plugin_dir_path(__FILE__) . 'voting-settings.php';
require_once plugin_dir_path(__FILE__) . 'advanced-settings.php';
require_once plugin_dir_path(__FILE__) . 'form-handler.php';

/**
 * Render the settings page
 */
function pollify_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Process form submission if needed
    pollify_process_settings_form();
    
    // Get current settings
    $settings = get_option('pollify_settings', array());
    $delete_data = get_option('pollify_delete_data_on_uninstall', false);
    
    // Default values
    $settings = wp_parse_args($settings, array(
        'allow_guests' => true,
        'results_display' => 'bar',
        'show_results_before_vote' => false,
        'enable_comments' => true,
        'enable_ratings' => true,
        'enable_social_sharing' => true,
        'poll_archive_page' => 0,
        'polls_per_page' => 10,
        'loading_animation' => true
    ));
    
    // Render the settings form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('pollify_save_settings', 'pollify_settings_nonce'); ?>
            
            <div class="pollify-settings-tabs">
                <div class="pollify-settings-nav">
                    <a href="#general" class="active"><?php _e('General', 'pollify'); ?></a>
                    <a href="#display"><?php _e('Display', 'pollify'); ?></a>
                    <a href="#voting"><?php _e('Voting', 'pollify'); ?></a>
                    <a href="#advanced"><?php _e('Advanced', 'pollify'); ?></a>
                </div>
                
                <div class="pollify-settings-content">
                    <!-- General Settings Tab -->
                    <?php pollify_render_general_settings($settings); ?>
                    
                    <!-- Display Settings Tab -->
                    <?php pollify_render_display_settings($settings); ?>
                    
                    <!-- Voting Settings Tab -->
                    <?php pollify_render_voting_settings($settings); ?>
                    
                    <!-- Advanced Settings Tab -->
                    <?php pollify_render_advanced_settings($delete_data); ?>
                </div>
            </div>
            
            <p class="submit">
                <input type="submit" name="pollify_settings_submit" class="button button-primary" value="<?php _e('Save Settings', 'pollify'); ?>">
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
        
        // Set first tab active by default
        $('.pollify-settings-section:first').addClass('active');
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
    </style>
    <?php
}
