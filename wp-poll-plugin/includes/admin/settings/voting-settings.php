
<?php
/**
 * Voting settings tab for Pollify admin settings
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the voting settings tab
 * 
 * @param array $settings Current plugin settings
 */
function pollify_render_voting_settings($settings) {
    ?>
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
    <?php
}
