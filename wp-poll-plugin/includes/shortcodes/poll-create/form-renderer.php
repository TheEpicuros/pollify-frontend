
<?php
/**
 * Poll creation form renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll creation form
 * 
 * @param array $atts Shortcode attributes
 */
function pollify_render_poll_creation_form($atts) {
    ?>
    <form id="pollify-create-poll-form" class="pollify-create-poll-form" style="display: none;">
        <input type="hidden" id="poll_type" name="poll_type" value="">
        <input type="hidden" id="redirect_url" name="redirect_url" value="<?php echo esc_attr($atts['redirect']); ?>">
        
        <div class="pollify-form-group">
            <label for="poll-title"><?php _e('Poll Question', 'pollify'); ?></label>
            <input type="text" id="poll-title" name="poll_title" required>
        </div>
        
        <div class="pollify-form-group">
            <label for="poll-description"><?php _e('Description (optional)', 'pollify'); ?></label>
            <textarea id="poll-description" name="poll_description"></textarea>
        </div>
        
        <div class="pollify-form-group pollify-poll-settings">
            <h4><?php _e('Poll Settings', 'pollify'); ?></h4>
            
            <?php pollify_render_poll_settings(); ?>
        </div>
        
        <div class="pollify-form-group">
            <label><?php _e('Options', 'pollify'); ?></label>
            <div id="poll-options-container">
                <div class="pollify-poll-option-input">
                    <input type="text" name="poll_options[]" required>
                </div>
                <div class="pollify-poll-option-input">
                    <input type="text" name="poll_options[]" required>
                </div>
            </div>
            <button type="button" id="add-poll-option-btn"><?php _e('Add Option', 'pollify'); ?></button>
        </div>
        
        <div id="image-options-container" style="display: none;">
            <div class="pollify-form-group">
                <label><?php _e('Option Images', 'pollify'); ?></label>
                <p class="description"><?php _e('Select images for each poll option. The order matches the options above.', 'pollify'); ?></p>
                <div id="poll-image-options-container">
                    <!-- Image selectors will be added here dynamically -->
                </div>
            </div>
        </div>
        
        <div class="pollify-form-submit">
            <button type="submit"><?php _e('Create Poll', 'pollify'); ?></button>
            <button type="button" id="back-to-poll-types" class="pollify-button-secondary"><?php _e('Back to Poll Types', 'pollify'); ?></button>
        </div>
        
        <div class="pollify-create-poll-message" style="display: none;"></div>
    </form>
    <?php
}

/**
 * Render poll settings fields
 */
function pollify_render_poll_settings() {
    ?>
    <div class="pollify-setting-group">
        <label for="poll-end-date"><?php _e('End Date (optional):', 'pollify'); ?></label>
        <input type="datetime-local" id="poll-end-date" name="poll_end_date">
        <span class="description"><?php _e('Leave empty for no end date', 'pollify'); ?></span>
    </div>
    
    <div class="pollify-setting-group">
        <label>
            <input type="checkbox" name="poll_show_results" value="1">
            <?php _e('Always show results, even before voting', 'pollify'); ?>
        </label>
    </div>
    
    <div class="pollify-setting-group">
        <label for="poll-results-display"><?php _e('Results Display:', 'pollify'); ?></label>
        <select name="poll_results_display" id="poll-results-display">
            <option value="bar"><?php _e('Bar Chart', 'pollify'); ?></option>
            <option value="pie"><?php _e('Pie Chart', 'pollify'); ?></option>
            <option value="donut"><?php _e('Donut Chart', 'pollify'); ?></option>
            <option value="text"><?php _e('Text Only', 'pollify'); ?></option>
        </select>
    </div>
    
    <div class="pollify-setting-group">
        <label>
            <input type="checkbox" name="poll_allow_comments" value="1" checked>
            <?php _e('Allow comments', 'pollify'); ?>
        </label>
    </div>
    <?php
}
