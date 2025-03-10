
<?php
/**
 * General settings tab for Pollify admin settings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the general settings tab
 * 
 * @param array $settings Current plugin settings
 */
function pollify_render_general_settings($settings) {
    ?>
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
    <?php
}
