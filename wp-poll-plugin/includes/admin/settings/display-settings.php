
<?php
/**
 * Display settings tab for Pollify admin settings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the display settings tab
 * 
 * @param array $settings Current plugin settings
 */
function pollify_render_display_settings($settings) {
    ?>
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
    <?php
}
