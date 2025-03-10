
<?php
/**
 * Poll type selector grid rendering
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the poll type selector grid
 * 
 * @param array $poll_types Array of poll types to display
 */
function pollify_render_poll_type_selector($poll_types) {
    ?>
    <div class="pollify-poll-types-grid">
        <?php foreach ($poll_types as $type_slug => $type_name) : 
            // Get an icon/image for each poll type
            $icon_class = pollify_get_poll_type_icon($type_slug);
        ?>
        <div class="pollify-poll-type-card" data-poll-type="<?php echo esc_attr($type_slug); ?>">
            <div class="pollify-poll-type-icon">
                <span class="dashicons <?php echo esc_attr($icon_class); ?>"></span>
            </div>
            <h3 class="pollify-poll-type-title"><?php echo esc_html($type_name); ?></h3>
            <p class="pollify-poll-type-description">
                <?php echo pollify_get_poll_type_description($type_slug); ?>
            </p>
            <button type="button" class="pollify-select-poll-type">
                <?php _e('Select', 'pollify'); ?>
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Get appropriate icon class for poll type
 * 
 * @param string $type_slug The poll type slug
 * @return string Icon class
 */
function pollify_get_poll_type_icon($type_slug) {
    switch ($type_slug) {
        case 'binary':
            return 'dashicons-yes-no';
        case 'multiple-choice':
            return 'dashicons-list-view';
        case 'check-all':
            return 'dashicons-yes';
        case 'ranked-choice':
            return 'dashicons-sort';
        case 'rating-scale':
            return 'dashicons-star-filled';
        case 'open-ended':
            return 'dashicons-editor-textcolor';
        case 'image-based':
            return 'dashicons-format-image';
        case 'quiz':
            return 'dashicons-clipboard';
        case 'opinion':
            return 'dashicons-testimonial';
        case 'straw':
            return 'dashicons-chart-line';
        case 'interactive':
            return 'dashicons-admin-site-alt3';
        case 'referendum':
            return 'dashicons-tickets-alt';
        default:
            return 'dashicons-chart-bar';
    }
}

/**
 * Get description for poll type
 * 
 * @param string $type_slug The poll type slug
 * @return string Description
 */
function pollify_get_poll_type_description($type_slug) {
    switch ($type_slug) {
        case 'binary':
            return __('Simple yes/no or either/or questions.', 'pollify');
        case 'multiple-choice':
            return __('Select one option from multiple choices.', 'pollify');
        case 'check-all':
            return __('Select multiple options that apply.', 'pollify');
        case 'ranked-choice':
            return __('Rank options in order of preference.', 'pollify');
        case 'rating-scale':
            return __('Rate on a scale (1-5, 1-10, etc).', 'pollify');
        case 'open-ended':
            return __('Allow voters to provide text responses.', 'pollify');
        case 'image-based':
            return __('Use images as answer options.', 'pollify');
        case 'quiz':
            return __('Test knowledge with right/wrong answers.', 'pollify');
        case 'opinion':
            return __('Gauge sentiment on specific issues.', 'pollify');
        case 'straw':
            return __('Quick, informal sentiment polls.', 'pollify');
        case 'interactive':
            return __('Real-time polls with live results.', 'pollify');
        case 'referendum':
            return __('Formal votes on specific measures.', 'pollify');
        default:
            return __('Standard poll type.', 'pollify');
    }
}
