
<?php
/**
 * Admin settings page for Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the settings page
 */
function pollify_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save settings if form was submitted
    if (isset($_POST['pollify_settings_submit'])) {
        // Verify nonce
        if (isset($_POST['pollify_settings_nonce']) && wp_verify_nonce($_POST['pollify_settings_nonce'], 'pollify_save_settings')) {
            // Get and sanitize form data
            $settings = array(
                'allow_guests' => isset($_POST['allow_guests']) ? true : false,
                'results_display' => sanitize_text_field($_POST['results_display']),
                'show_results_before_vote' => isset($_POST['show_results_before_vote']) ? true : false,
                'enable_comments' => isset($_POST['enable_comments']) ? true : false,
                'enable_ratings' => isset($_POST['enable_ratings']) ? true : false,
                'enable_social_sharing' => isset($_POST['enable_social_sharing']) ? true : false,
                'poll_archive_page' => absint($_POST['poll_archive_page']),
                'polls_per_page' => absint($_POST['polls_per_page']),
                'loading_animation' => isset($_POST['loading_animation']) ? true : false
            );
            
            // Save settings
            update_option('pollify_settings', $settings);
            
            // Save uninstall option
            $delete_data = isset($_POST['delete_data_on_uninstall']) ? true : false;
            update_option('pollify_delete_data_on_uninstall', $delete_data);
            
            // Show success message
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'pollify') . '</p></div>';
        } else {
            // Show error message
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Security check failed. Please try again.', 'pollify') . '</p></div>';
        }
    }
    
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
                    <!-- General Settings -->
                    <div id="general" class="pollify-settings-section active">
                        <h2><?php _e('General Settings', 'pollify'); ?></h2>
                        
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php _e('Poll Archive Page', 'pollify'); ?></th>
                                <td>
                                    <?php
                                    wp_dropdown_pages(array(
                                        'name' => 'poll_archive_page',
                                        'show_option_none' => __('Select a page', 'pollify'),
                                        'option_none_value' => '0',
                                        'selected' => $settings['poll_archive_page']
                                    ));
                                    ?>
                                    <p class="description"><?php _e('Select a page to display the poll archive. The [pollify_browse] shortcode will be automatically added to this page.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Polls Per Page', 'pollify'); ?></th>
                                <td>
                                    <input type="number" name="polls_per_page" min="1" max="100" value="<?php echo esc_attr($settings['polls_per_page']); ?>">
                                    <p class="description"><?php _e('Number of polls to display per page in the poll archive.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Social Sharing', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_social_sharing" <?php checked($settings['enable_social_sharing']); ?>>
                                        <?php _e('Enable social sharing buttons on polls', 'pollify'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Loading Animation', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="loading_animation" <?php checked($settings['loading_animation']); ?>>
                                        <?php _e('Enable loading animations for better user experience', 'pollify'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Display Settings -->
                    <div id="display" class="pollify-settings-section">
                        <h2><?php _e('Display Settings', 'pollify'); ?></h2>
                        
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php _e('Results Display', 'pollify'); ?></th>
                                <td>
                                    <select name="results_display">
                                        <option value="bar" <?php selected($settings['results_display'], 'bar'); ?>><?php _e('Bar Chart', 'pollify'); ?></option>
                                        <option value="pie" <?php selected($settings['results_display'], 'pie'); ?>><?php _e('Pie Chart', 'pollify'); ?></option>
                                        <option value="donut" <?php selected($settings['results_display'], 'donut'); ?>><?php _e('Donut Chart', 'pollify'); ?></option>
                                        <option value="text" <?php selected($settings['results_display'], 'text'); ?>><?php _e('Text Only', 'pollify'); ?></option>
                                    </select>
                                    <p class="description"><?php _e('Default display style for poll results.', 'pollify'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Comments', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_comments" <?php checked($settings['enable_comments']); ?>>
                                        <?php _e('Enable comments on polls', 'pollify'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Ratings', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_ratings" <?php checked($settings['enable_ratings']); ?>>
                                        <?php _e('Enable ratings on polls (thumbs up/down)', 'pollify'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Voting Settings -->
                    <div id="voting" class="pollify-settings-section">
                        <h2><?php _e('Voting Settings', 'pollify'); ?></h2>
                        
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php _e('Guest Voting', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="allow_guests" <?php checked($settings['allow_guests']); ?>>
                                        <?php _e('Allow non-logged-in users to vote', 'pollify'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Results Visibility', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="show_results_before_vote" <?php checked($settings['show_results_before_vote']); ?>>
                                        <?php _e('Show results before voting', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('If enabled, users can see poll results before casting their vote.', 'pollify'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Advanced Settings -->
                    <div id="advanced" class="pollify-settings-section">
                        <h2><?php _e('Advanced Settings', 'pollify'); ?></h2>
                        
                        <table class="form-table" role="presentation">
                            <tr>
                                <th scope="row"><?php _e('Uninstall Options', 'pollify'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="delete_data_on_uninstall" <?php checked($delete_data); ?>>
                                        <?php _e('Delete all plugin data when uninstalling', 'pollify'); ?>
                                    </label>
                                    <p class="description"><?php _e('If checked, all polls, votes, and settings will be permanently deleted when the plugin is uninstalled.', 'pollify'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
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
