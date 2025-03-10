
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

            <p><strong><?php _e('Available Poll Types:', 'pollify'); ?></strong></p>
            
            <ul class="pollify-poll-types-list">
                <?php
                $poll_types = array(
                    'binary' => __('Binary Choice - Simple yes/no questions', 'pollify'),
                    'multiple-choice' => __('Multiple Choice - Select one from several options', 'pollify'),
                    'check-all' => __('Multiple Answers - Select multiple options', 'pollify'),
                    'image-based' => __('Image Based - Visual polls with image options', 'pollify'),
                    'rating-scale' => __('Rating Scale - Rate on a numerical scale', 'pollify')
                );
                
                foreach ($poll_types as $type => $description) {
                    echo '<li><strong>' . esc_html($type) . '</strong> - ' . esc_html($description) . '</li>';
                }
                ?>
            </ul>
            
            <p><a href="<?php echo admin_url('edit.php?post_type=poll&page=pollify-help'); ?>" class="button button-secondary"><?php _e('View Full Documentation', 'pollify'); ?></a></p>
        </div>
    </div>
    <?php
}
