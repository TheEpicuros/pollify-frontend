
<?php
/**
 * Poll creation shortcode [pollify_create]
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Poll create shortcode [pollify_create]
 */
function pollify_create_shortcode($atts) {
    $atts = shortcode_atts(array(
        'types' => '', // comma-separated list of poll types to allow
        'redirect' => '', // URL to redirect after creation
    ), $atts, 'pollify_create');
    
    // Only logged in users can create polls
    if (!is_user_logged_in()) {
        return '<div class="pollify-error">' . __('You must be logged in to create a poll.', 'pollify') . '</div>';
    }
    
    // Check if user has permission to create polls
    if (!current_user_can('publish_posts')) {
        return '<div class="pollify-error">' . __('You do not have permission to create polls.', 'pollify') . '</div>';
    }
    
    // Get available poll types
    $available_types = array();
    $terms = get_terms(array(
        'taxonomy' => 'poll_type',
        'hide_empty' => false,
    ));
    
    foreach ($terms as $term) {
        $available_types[$term->slug] = $term->name;
    }
    
    // Filter poll types if specified in shortcode
    $filtered_types = $available_types;
    if (!empty($atts['types'])) {
        $allowed_types = explode(',', $atts['types']);
        $filtered_types = array();
        
        foreach ($allowed_types as $type) {
            $type = trim($type);
            if (isset($available_types[$type])) {
                $filtered_types[$type] = $available_types[$type];
            }
        }
    }
    
    // If no valid types remain, show an error
    if (empty($filtered_types)) {
        return '<div class="pollify-error">' . __('No valid poll types specified.', 'pollify') . '</div>';
    }
    
    ob_start();
    ?>
    <div id="pollify-create-poll" class="pollify-create-poll">
        <div class="pollify-create-poll-container">
            <h2><?php _e('Create a New Poll', 'pollify'); ?></h2>
            
            <div class="pollify-poll-types-grid">
                <?php foreach ($filtered_types as $type_slug => $type_name) : 
                    // Get an icon/image for each poll type
                    $icon_class = 'dashicons-chart-bar';
                    
                    switch ($type_slug) {
                        case 'binary':
                            $icon_class = 'dashicons-yes-no';
                            break;
                        case 'multiple-choice':
                            $icon_class = 'dashicons-list-view';
                            break;
                        case 'check-all':
                            $icon_class = 'dashicons-yes';
                            break;
                        case 'ranked-choice':
                            $icon_class = 'dashicons-sort';
                            break;
                        case 'rating-scale':
                            $icon_class = 'dashicons-star-filled';
                            break;
                        case 'open-ended':
                            $icon_class = 'dashicons-editor-textcolor';
                            break;
                        case 'image-based':
                            $icon_class = 'dashicons-format-image';
                            break;
                        case 'quiz':
                            $icon_class = 'dashicons-clipboard';
                            break;
                        case 'opinion':
                            $icon_class = 'dashicons-testimonial';
                            break;
                        case 'straw':
                            $icon_class = 'dashicons-chart-line';
                            break;
                        case 'interactive':
                            $icon_class = 'dashicons-admin-site-alt3';
                            break;
                        case 'referendum':
                            $icon_class = 'dashicons-tickets-alt';
                            break;
                    }
                ?>
                <div class="pollify-poll-type-card" data-poll-type="<?php echo esc_attr($type_slug); ?>">
                    <div class="pollify-poll-type-icon">
                        <span class="dashicons <?php echo esc_attr($icon_class); ?>"></span>
                    </div>
                    <h3 class="pollify-poll-type-title"><?php echo esc_html($type_name); ?></h3>
                    <p class="pollify-poll-type-description">
                        <?php 
                        // Show a short description based on poll type
                        switch ($type_slug) {
                            case 'binary':
                                _e('Simple yes/no or either/or questions.', 'pollify');
                                break;
                            case 'multiple-choice':
                                _e('Select one option from multiple choices.', 'pollify');
                                break;
                            case 'check-all':
                                _e('Select multiple options that apply.', 'pollify');
                                break;
                            case 'ranked-choice':
                                _e('Rank options in order of preference.', 'pollify');
                                break;
                            case 'rating-scale':
                                _e('Rate on a scale (1-5, 1-10, etc).', 'pollify');
                                break;
                            case 'open-ended':
                                _e('Allow voters to provide text responses.', 'pollify');
                                break;
                            case 'image-based':
                                _e('Use images as answer options.', 'pollify');
                                break;
                            case 'quiz':
                                _e('Test knowledge with right/wrong answers.', 'pollify');
                                break;
                            case 'opinion':
                                _e('Gauge sentiment on specific issues.', 'pollify');
                                break;
                            case 'straw':
                                _e('Quick, informal sentiment polls.', 'pollify');
                                break;
                            case 'interactive':
                                _e('Real-time polls with live results.', 'pollify');
                                break;
                            case 'referendum':
                                _e('Formal votes on specific measures.', 'pollify');
                                break;
                            default:
                                _e('Standard poll type.', 'pollify');
                        }
                        ?>
                    </p>
                    <button type="button" class="pollify-select-poll-type">
                        <?php _e('Select', 'pollify'); ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            
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
        </div>
    </div>
    <?php
    return ob_get_clean();
}
