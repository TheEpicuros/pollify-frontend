
<?php
/**
 * Dashboard help/getting started widget
 *
 * @package Pollify
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the help/getting started widget
 */
function pollify_render_help_widget() {
    ?>
    <div class="pollify-widget pollify-help-widget">
        <h2><?php _e('Getting Started', 'pollify'); ?></h2>
        
        <div class="pollify-help-content">
            <p><?php _e('Here\'s how to get started with Pollify:', 'pollify'); ?></p>
            
            <ol>
                <li><?php _e('Create a new poll by clicking "Add New Poll"', 'pollify'); ?></li>
                <li><?php _e('Add your poll question and options', 'pollify'); ?></li>
                <li><?php _e('Publish your poll', 'pollify'); ?></li>
                <li><?php _e('Use the shortcode to display your poll on any page or post', 'pollify'); ?></li>
            </ol>
            
            <p><strong><?php _e('Available Shortcodes:', 'pollify'); ?></strong></p>
            
            <ul>
                <li><code>[pollify id="123"]</code> - <?php _e('Display a specific poll', 'pollify'); ?></li>
                <li><code>[pollify_create]</code> - <?php _e('Display a poll creation form', 'pollify'); ?></li>
                <li><code>[pollify_browse]</code> - <?php _e('Display a list of polls', 'pollify'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}
